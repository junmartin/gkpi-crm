@extends('layouts.form')

@section('content')
<h3>Users</h3>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ implode(', ', $user->getRoleNames()->toArray()) }}</td>
            <td><a href="{{ route('users.edit', $user) }}">Edit Role</a></td>
        </tr>
    @endforeach
</table>
@endsection
