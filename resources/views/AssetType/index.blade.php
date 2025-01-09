@extends('layouts.form')

@section('content')

<h3>Asset Type</h3>

<a href="{{ route('asset_type.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Tipe Asset</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($asset_type as $ast)
            <tr>
                <td style="vertical-align:top">{{$ast->name}}</td>
                <td style="text-align:center;">
                    <a href="{{ route('asset_type.edit', $ast->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection