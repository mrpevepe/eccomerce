@extends('layouts.main')
@section('title', isset($product) ? 'Editar Produto' : 'Adicionar Produto')
@section('content')
    <h2>{{ isset($product) ? 'Editar Produto' : 'Adicionar Produto' }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div>
            <label for="name">Nome:</label>
            <input type="text" name="name" id="name" value="{{ old('name', isset($product) ? $product->name : '') }}" required>
        </div>

        <div>
            <label for="description">Descrição:</label>
            <textarea name="description" id="description" required>{{ old('description', isset($product) ? $product->description : '') }}</textarea>
        </div>

        <div>
            <label for="category_id">Categoria:</label>
            <select name="category_id" id="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', isset($product) ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="price">Preço:</label>
            <input type="number" name="price" id="price" step="0.01" value="{{ old('price', isset($product) ? $product->price : '') }}" required>
        </div>

        <div>
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="active" {{ old('status', isset($product) ? $product->status : '') == 'active' ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ old('status', isset($product) ? $product->status : '') == 'inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <h3>Imagem Principal</h3>
        @if(isset($product))
            @php
                $mainImage = $product->images->where('is_main', true)->first();
            @endphp
            @if($mainImage && Storage::disk('public')->exists($mainImage->path))
                <img src="{{ asset('storage/' . $mainImage->path) }}" width="100" alt="Main Image">
                <label>
                    <input type="checkbox" name="delete_images[]" value="{{ $mainImage->id }}"> Delete Main Image
                </label>
            @endif
        @endif
        <div>
            <label for="main_image">Nova Imagem Principal:</label>
            <input type="file" name="main_image" id="main_image" accept="image/*">
            <div class="main_image_preview"></div>
        </div>

        <h3>Imagens Adicionais (Máximo 3)</h3>
        @if(isset($product))
            @foreach($product->images->where('is_main', false) as $image)
                @if(Storage::disk('public')->exists($image->path))
                    <img src="{{ asset('storage/' . $image->path) }}" width="100" alt="Additional Image">
                    <label>
                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Delete Image
                    </label>
                @endif
            @endforeach
        @endif
        <div>
            <label for="additional_images">Novas Imagens Adicionais:</label>
            <input type="file" name="additional_images[]" id="additional_images" accept="image/*" multiple>
            <div class="additional_images_preview"></div>
        </div>

        <h3>Variações</h3>
        <div id="variations">
            @if(isset($product) && $product->variations)
                @foreach ($product->variations as $index => $variation)
                    <div class="variation">
                        <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                        <input type="text" name="variations[{{ $index }}][name]" value="{{ $variation->name }}" required>
                        <input type="text" name="variations[{{ $index }}][value]" value="{{ $variation->value }}" required>
                        <input type="number" name="variations[{{ $index }}][additional_price]" step="0.01" value="{{ $variation->additional_price }}" required>
                        <input type="number" name="variations[{{ $index }}][stock_quantity]" value="{{ $variation->stock_quantity }}" required>
                        <div>
                            <label>Variation Image:</label>
                            @if ($variation->image)
                                <img src="{{ asset('storage/' . $variation->image->path) }}" width="100" alt="Variation Image" class="variation_image_preview_{{ $index }}">
                                <label>
                                    <input type="checkbox" name="delete_variation_images[]" value="{{ $variation->image->id }}"> Delete Image
                                </label>
                            @endif
                            <input type="file" name="variations[{{ $index }}][image]" id="variation_image_{{ $index }}" accept="image/*">
                            <div class="variation_image_preview_{{ $index }}"></div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()">Remove Variation</button>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" onclick="addVariation()">Adicionar Variação</button>

        <button type="submit">Salvar</button>
    </form>

    <style>
        .variation {
            margin-bottom: 20px;
        }
        [class^="variation_image_preview_"] {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
        }
        .additional_images_preview img {
            display: inline-block;
            margin-right: 10px;
            margin-top: 5px;
            vertical-align: middle;
        }
    </style>

    <script>
        function addPreviewListener(inputId, previewClass) {
            const input = document.getElementById(inputId);
            const preview = document.querySelector(`.${previewClass}`);
            if (input && preview) {
                input.addEventListener('change', function(e) {
                    preview.innerHTML = ''; // Limpa preview anterior
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        // todas aditional imagess
                        if (inputId === 'additional_images') {
                            Array.from(files).forEach(file => {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.width = 100;
                                    preview.appendChild(img);
                                };
                                reader.readAsDataURL(file);
                            });
                        } else {
                            // mostra so 1 image
                            const file = files[0];
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.width = 100;
                                preview.appendChild(img);
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            }
        }

        function addVariation() {
            const index = document.querySelectorAll('.variation').length;
            const variationDiv = document.createElement('div');
            variationDiv.className = 'variation';
            variationDiv.innerHTML = `
                <input type="text" name="variations[${index}][name]" placeholder="Variation Name (e.g., Size)" required>
                <input type="text" name="variations[${index}][value]" placeholder="Value (e.g., Medium)" required>
                <input type="number" name="variations[${index}][additional_price]" step="0.01" placeholder="Additional Price" required>
                <input type="number" name="variations[${index}][stock_quantity]" placeholder="Stock Quantity" required>
                <div>
                    <label>Variation Image:</label>
                    <input type="file" name="variations[${index}][image]" id="variation_image_${index}" accept="image/*">
                    <div class="variation_image_preview_${index}"></div>
                </div>
                <button type="button" onclick="this.parentElement.remove()">Remove Variation</button>
            `;
            document.getElementById('variations').appendChild(variationDiv);
            addPreviewListener(`variation_image_${index}`, `variation_image_preview_${index}`);
        }

        // Initialize previews on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Main image preview
            addPreviewListener('main_image', 'main_image_preview');

            // Additional images preview
            addPreviewListener('additional_images', 'additional_images_preview');

            // Variation images preview
            @if(isset($product) && $product->variations)
                @foreach($product->variations as $index => $variation)
                    addPreviewListener('variation_image_{{ $index }}', 'variation_image_preview_{{ $index }}');
                @endforeach
            @endif
        });
    </script>
@endsection