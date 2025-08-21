@extends('layouts.main')
@section('title', 'Manage Stock')
@section('content')
    <h2>Manage Stock for {{ $product->name }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.products.updateStock', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h3>Variations</h3>
        @foreach ($product->variations as $index => $variation)
            <div>
                <label>{{ $variation->name }}: {{ $variation->value }}</label>
                <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                <input type="number" name="variations[{{ $index }}][stock_quantity]" value="{{ $variation->stock_quantity }}" min="0" required>
            </div>
        @endforeach

        <button type="submit">Atualizar Estoque</button>
    </form>
@endsection