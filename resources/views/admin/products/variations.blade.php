@extends('layouts.main')
@section('title', 'Variations for ' . $product->name)
@section('content')
    <h2>Product: {{ $product->name }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <p>Description: {{ $product->description }}</p>
    <p>Price: {{ $product->price }}</p>
    <p>Category: {{ $product->category ? $product->category->name : 'No Category' }}</p>
    <p>Status: {{ $product->status }}</p>

    <h3>Main Image</h3>
    @php
        $mainImage = $product->images->where('is_main', true)->first();
    @endphp
    @if ($mainImage && Storage::disk('public')->exists($mainImage->path))
        <img src="{{ asset('storage/' . $mainImage->path) }}" width="100" alt="{{ $product->name }} Main Image">
    @else
        <p>No Main Image</p>
    @endif

    <h3>Additional Images</h3>
    @if ($product->images->where('is_main', false)->isEmpty())
        <p>No additional images available.</p>
    @else
        @foreach ($product->images->where('is_main', false) as $image)
            @if (Storage::disk('public')->exists($image->path))
                <img src="{{ asset('storage/' . $image->path) }}" width="100" alt="{{ $product->name }} Additional Image">
            @endif
        @endforeach
    @endif

    <h3>Variations</h3>
    @if ($product->variations->isEmpty())
        <p>No variations available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Additional Price</th>
                    <th>Total Price</th>
                    <th>Stock Quantity</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->variations as $variation)
                    <tr>
                        <td>
                            @if ($variation->image && Storage::disk('public')->exists($variation->image->path))
                                <img src="{{ asset('storage/' . $variation->image->path) }}" width="50" alt="{{ $variation->name }} Image">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $variation->name }}</td>
                        <td>{{ $variation->value }}</td>
                        <td>{{ $variation->additional_price }}</td>
                        <td>{{ number_format($product->price + $variation->additional_price, 2) }}</td>
                        <td>{{ $variation->stock_quantity }}</td>
                        <td>
                            <a href="{{ route('admin.products.variations.stock', [$product->id, $variation->id]) }}"><button>Editar Estoque</button></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <button type="button" onclick="history.back()">Voltar</button>
@endsection