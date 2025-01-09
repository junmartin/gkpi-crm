@extends('layouts.form')

@section('content')

<h3>Ibadah</h3>

<a href="{{ route('ibadah.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Catatan</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($ibadahs as $i)
            <tr>
                <td>{{$i->ibadah_name}}</td>
                <td>{{$i->remark}}</td>
                <td style="text-align:center;">
                    <a href="{{ route('ibadah.edit', $i->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection