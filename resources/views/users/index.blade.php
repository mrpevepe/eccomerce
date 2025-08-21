@extends('layouts.main')
@section('title', 'Cadastro users Admin')
@section('content')
    <h2>Users</h2>
    
    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif


    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Address</th>
                <th>Actions</th>
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
                            Sem Endereço
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection