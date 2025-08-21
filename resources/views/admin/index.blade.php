@extends('layouts.main')
@section('title', 'Painel do Admin')
@section('content')
    <h2>Painel do Admin</h2>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <h3>Opções de Gerenciamento</h3>
    <div>
        <button onclick="toggleSection('users-section')">Usuarios</button>
        <button onclick="toggleSection('products-section')">Produtos</button>
        <button onclick="toggleSection('categories-section')">Categorias</button>
    </div>

    {{-- user section --}}
    <div id="users-section" style="display: block;">
        <h3>Usuarios</h3>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if ($user->address)
                                {{ $user->address->street }}, {{ $user->address->number }}, {{ $user->address->city }}-{{ $user->address->state }}
                            @else
                                No Address
                            @endif
                        </td>
                        <td>
                            {{-- user section futuro maybe? --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Products Section --}}
    <div id="products-section" style="display: none;">
        <h3>Produtos</h3>
        <div>
            <a href="{{ route('admin.products.create') }}"><button>Novo Produto</button></a>
            <a href="{{ route('admin.products.index') }}"><button>Listar Produtos</button></a>
        </div>
    </div>

    {{-- Categories Section --}}
    <div id="categories-section" style="display: none;">
        <h3>Categorias</h3>
        <div>
            <a href="{{ route('admin.categories.create') }}"><button>Nova Categoria</button></a>
            <a href="{{ route('admin.categories.index') }}"><button>Listar Categorias</button></a>
        </div>
    </div>

    <script>
        function toggleSection(sectionId) {
            const sections = ['users-section', 'products-section', 'categories-section'];
            sections.forEach(id => {
                document.getElementById(id).style.display = (id === sectionId) ? 'block' : 'none';
            });
        }
    </script>

@endsection