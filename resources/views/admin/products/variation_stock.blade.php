@extends('layouts.main')
@section('title', 'Editar Estoque da Variação')
@section('content')
    <h2>Editar Estoque para Variação de {{ $variation->product->name }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <div class="variation-details" style="margin-bottom: 20px;">
        <h3>Variação: {{ $variation->name }} - {{ $variation->value }}</h3>
        @if ($variation->image && Storage::disk('public')->exists($variation->image->path))
            <img src="{{ asset('storage/' . $variation->image->path) }}" width="100" alt="{{ $variation->name }} Image">
        @else
            <p>Sem Imagem</p>
        @endif
        <p>Preço Adicional: {{ $variation->additional_price }}</p>
        <p>Estoque Atual: {{ $variation->stock_quantity }}</p>
    </div>

    <form action="{{ route('admin.products.variations.updateStock', [$variation->product_id, $variation->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="stock_quantity">Novo Estoque:</label>
            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ $variation->stock_quantity }}" min="0" required style="width: 100px;">
        </div>

        <button type="submit">Atualizar Estoque</button>
        <button type="button" onclick="history.back()">Voltar</button>
    </form>
@endsection