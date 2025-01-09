@extends('layouts.form')

@section('content')

<h3>Family</h3>

<a href="{{ route('family.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Nama Keluarga</th>
            <th>Jumlah Anggota</th>
            <th>Catatan</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($families as $fam)
            <tr>
                <td style="vertical-align:top">{{$fam->family_name}}</td>
                <td style="vertical-align:top">{{$fam->countPeople()}} orang.</td>
                <td>-</td>
                <td style="text-align:center;">
                    <a href="{{ route('family.edit', $fam->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection