<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div>
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div>
            <label for="street">Street</label>
            <input type="text" name="street" id="street" value="{{ old('street', $user->address ? $user->address->street : '') }}" required>
        </div>
        <div>
            <label for="number">Number</label>
            <input type="text" name="number" id="number" value="{{ old('number', $user->address ? $user->address->number : '') }}" required>
        </div>
        <div>
            <label for="complement">Complement</label>
            <input type="text" name="complement" id="complement" value="{{ old('complement', $user->address ? $user->address->complement : '') }}">
        </div>
        <div>
            <label for="neighborhood">Neighborhood</label>
            <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $user->address ? $user->address->neighborhood : '') }}" required>
        </div>
        <div>
            <label for="zip_code">Zip Code</label>
            <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $user->address ? $user->address->zip_code : '') }}" required>
        </div>
        <div>
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="{{ old('city', $user->address ? $user->address->city : '') }}" required>
        </div>
        <div>
            <label for="state">State</label>
            <input type="text" name="state" id="state" value="{{ old('state', $user->address ? $user->address->state : '') }}" maxlength="2" required>
        </div>
        <div>
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>Client</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <button type="submit">Update User</button>
    </form>
</body>
</html>