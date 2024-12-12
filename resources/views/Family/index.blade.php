@extends('layouts.form')

@section('content')

<h3>Family</h3>

<a href="{{ route('family.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Anggota Keluarga</th>
            <th>Catatan</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($families as $fam)
            <tr>
                <td>{{$fam->family_name}}</td>
                <td>-</td>
                <td>-</td>
                <td style="text-align:center;">
                    <a href="{{ route('family.edit', $fam->id)}}">[ View ]</a>
                    <br><br><a href="{{ route('family.edit', $fam->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection