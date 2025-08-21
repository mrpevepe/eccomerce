@extends('layouts.main')
@section('title', 'Manage Categories')
@section('content')
    <h2>Manage Categories</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.categories.create') }}"><button>Add New Category</button></a>
    <button type="button" onclick="history.back()">Voltar</button>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category->id) }}"><button>Edit</button></a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure? This may affect associated products.')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection