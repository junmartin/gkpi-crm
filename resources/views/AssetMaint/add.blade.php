@extends('layouts.form')

@section('content')

<h3>Create New Asset</h3>

<form action="{{ route('asset_maint.store') }}" method="POST" enctype="multipart/form-data">
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
                <th colspan="2">Asset Maintenance</th>
            </thead>
            <tbody>
                <tr>
                    <td style="width:50%;">Tanggal Service</td>
                    <td style="width:50%;">
                        <input type="date" id="maint_date" name="maint_date">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Judul Service</td>
                    <td style="width:50%;">
                        <input type="text" id="maint_title" name="maint_title" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Asset</td>
                    <td style="width:50%;">
                        <select id="asset_id" name="asset_id" required>
                            @foreach($asset as $ast)
                            <option value="{{$ast->id}}">{{$ast->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td style="width:50%;">Tipe Service</td>
                    <td style="width:50%;">
                        <input type="text" id="maint_type" name="maint_type" maxlength="100" required>
                    </td>
                </tr>
                
                <tr>
                    <td style="width:50%;">Deskripsi</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="desc" name="desc"></textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Biaya</td>
                    <td style="width:50%;">
                        <input type="number" id="maint_fee" name="maint_fee" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tanggal Service Selanjutnya</td>
                    <td style="width:50%;">
                        <input type="date" id="next_maint_date" name="next_maint_date">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Catatan</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="remark" name="remark"></textarea>
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