@extends('layouts.main')
@section('title', 'Home Sitezudo')
@section('content')
    <div class="container">
        <!-- Banner Placeholder -->
        <div class="banner">
            <img src="https://via.placeholder.com/1200x300" alt="Banner Placeholder" class="banner-image">
        </div>

        <!-- Lista de Categorias -->
        <h2>Categorias</h2>
        <div class="category-list">
            @foreach ($categories as $category)
                <a href="#" class="category-item">{{ $category->name }}</a>
            @endforeach
        </div>

        <!-- Grid de Produtos -->
        <h2>Produtos</h2>
        <div class="product-grid" id="product-grid">
            @foreach ($products as $product)
                <a href="{{ route('products.show', $product->id) }}" class="product-item">
                    @php
                        $mainImage = $product->images->where('is_main', true)->first();
                    @endphp
                    @if ($mainImage && Storage::disk('public')->exists($mainImage->path))
                        <img src="{{ asset('storage/' . $mainImage->path) }}" alt="{{ $product->name }}" class="product-image">
                    @else
                        <img src="https://via.placeholder.com/150" alt="No Image" class="product-image">
                    @endif
                    <h3>{{ $product->name }}</h3>
                    <p>R${{ number_format($product->price, 2) }}</p>
                </a>
            @endforeach
        </div>

        <!-- Botão Ver Mais -->
        <div class="load-more">
            <button id="load-more-btn">Ver Mais</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more-btn');
            let offset = 36; // Começa após os 36 produtos iniciais

            loadMoreBtn.addEventListener('click', function() {
                fetch(`/load-more-products?offset=${offset}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.products.length > 0) {
                            const productGrid = document.getElementById('product-grid');
                            data.products.forEach(product => {
                                const productItem = document.createElement('a');
                                productItem.href = `/products/${product.id}`;
                                productItem.className = 'product-item';
                                productItem.innerHTML = `
                                    <img src="${product.main_image ? '/storage/' + product.main_image : 'https://via.placeholder.com/150'}" alt="${product.name}" class="product-image">
                                    <h3>${product.name}</h3>
                                    <p>R$${parseFloat(product.price).toFixed(2)}</p>
                                `;
                                productGrid.appendChild(productItem);
                            });
                            offset += 36; // Incrementa o offset para o próximo lote
                        } else {
                            loadMoreBtn.disabled = true;
                            loadMoreBtn.textContent = 'Não há mais produtos';
                        }
                    })
                    .catch(error => console.error('Erro ao carregar mais produtos:', error));
            });
        });
    </script>

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .banner {
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .banner-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .category-item {
            padding: 10px 20px;
            background-color: #f0f0f0;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .product-item {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        .product-image {
            width: 100%;
            max-width: 150px;
            height: auto;
        }
        .load-more {
            text-align: center;
            margin-bottom: 20px;
        }
        #load-more-btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #load-more-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
@endsection