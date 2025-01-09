@extends('layouts.form')

@section('content')

<h3>Create New Asset</h3>

<form action="{{ route('asset.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }} eargaer</li>
            @endforeach
        </ul>
    </div>
    @endif

<div style="width:100%; display:table;">
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            
            <thead>
                <th colspan="2">Asset Info</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;">Jenis</td>
                    <td style="width:50%;">
                        <select type="text" id="type_id" name="type_id" required>
                            @foreach($asset_type as $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Nama</td>
                    <td style="width:50%;">
                        <input type="text" id="name" name="name" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tanggal Perolehan</td>
                    <td style="width:50%;">
                        <input type="date" id="acquired_date" name="acquired_date">
                    </td>
                </tr>
                
                <tr>
                    <td style="width:50%;">Merk</td>
                    <td style="width:50%;">
                        <input type="text" id="merk" name="merk" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Model</td>
                    <td style="width:50%;">
                        <input type="text" id="model" name="model" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Serial No.</td>
                    <td style="width:50%;">
                        <input type="text" id="serial_number" name="serial_number" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tipe</td>
                    <td style="width:50%;">
                        <input type="text" id="tipe" name="tipe" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Spesifikasi</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="spec" name="spec"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </span>
</div>

<div style="text-align:right;">
    <input type="submit" value="Submit" >
</div>
</form>

@endsection