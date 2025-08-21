@extends('layouts.main')
@section('title', isset($category) ? 'Editar Categoria' : 'Criar Categoria')
@section('content')
    <h2>{{ isset($category) ? 'Editar Categoria' : 'Criar Categoria' }}</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif

        <div>
            <label for="name">Nome:</label>
            <input type="text" name="name" id="name" value="{{ old('name', isset($category) ? $category->name : '') }}" required>
        </div>

        <button type="submit">{{ isset($category) ? 'Editar Categoria' : 'Criar Categoria' }}</button>
        <button type="button" onclick="history.back()">Voltar</button>
    </form>
@endsection