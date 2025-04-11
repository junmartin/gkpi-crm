@extends('layouts.form')

@section('content')
    <h2>Assign Role to {{ $user->name }}</h2>

    <form method="POST" action="{{ route('users.assignRole', $user) }}">
        @csrf
        <label for="name">Name</label>
        <input type="text" name="name" value="">

        <label for="email">Email</label>
        <input type="text" name="email" value="">

        <label for="email">Password</label>
        <input type="password" name="email" value="">

        <label for="email">Confirm Password</label>
        <input type="password" name="email" value="">

        <label for="role">Select Role:</label>
        <select name="role" id="role">
            @foreach($roles as $role)
                <option value="{{ $role->name }}" @if($user->hasRole($role->name)) selected @endif>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        <button type="submit">Assign Role</button>
    </form>

    <a href="{{ route('users.index') }}">Back</a>
@endsection
