@extends('layouts.main')
@section('title', 'Manage Products')
@section('content')
    <h2>Gerenciar Produtos</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.products.create') }}"><button>Novo Produto</button></a>
    <button type="button" onclick="history.back()">Voltar</button>
    <table>
        <thead>
            <tr>
                <th>Imagem Principal</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>
                        @php
                            $mainImage = $product->images->where('is_main', true)->first();
                        @endphp
                        @if ($mainImage && Storage::disk('public')->exists($mainImage->path))
                            <img src="{{ asset('storage/' . $mainImage->path) }}" width="50" alt="{{ $product->name }}">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ? $product->category->name : 'No Category' }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->status }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}"><button>Editar</button></a>
                        <a href="{{ route('admin.products.stock', $product->id) }}"><button>Estoque</button></a>
                        @if ($product->status === 'active')
                            <form action="{{ route('admin.products.deactivate', $product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure?')">Desativar</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.products.variations', $product->id) }}"><button>Ver Variações</button></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection