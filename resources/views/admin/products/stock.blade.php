@extends('layouts.main')
@section('title', 'Manage Stock')
@section('content')
    <h2>Manage Stock for {{ $product->name }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <div class="product-main-image" style="margin-bottom: 20px;">
        <h3>{{ $product->name }}</h3>
        @php
            $mainImage = $product->images->where('is_main', true)->first();
        @endphp
        @if ($mainImage && Storage::disk('public')->exists($mainImage->path))
            <img src="{{ asset('storage/' . $mainImage->path) }}" width="100" alt="{{ $product->name }} Main Image">
        @else
            <p>No Main Image</p>
        @endif
    </div>

    <form action="{{ route('admin.products.updateStock', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h3>Variations</h3>
        @if ($product->variations->isEmpty())
            <p>No variations available.</p>
        @else
            @foreach ($product->variations as $index => $variation)
                <div class="variation" style="display: flex; align-items: left; margin-bottom: 15px;">

                    <div style="margin-right: 10px;">
                        @if ($variation->image && Storage::disk('public')->exists($variation->image->path))
                            <img src="{{ asset('storage/' . $variation->image->path) }}" width="50" alt="{{ $variation->name }} Image">
                        @else
                            <span>No Image</span>
                        @endif
                    </div>
                    
                    <div>
                        <label>{{ $variation->name }}: {{ $variation->value }}</label>
                        <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                    </div>
                    
                    <div>
                        <input type="number" name="variations[{{ $index }}][stock_quantity]" value="{{ $variation->stock_quantity }}" min="0" required style="width: 100px;">
                    </div>
                </div>
            @endforeach
        @endif

        <button type="submit">Atualizar Estoque</button>
        <button type="button" onclick="history.back()">Voltar</button>
    </form>
@endsection