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
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <button type="button" onclick="history.back()">Voltar</button>
@endsection