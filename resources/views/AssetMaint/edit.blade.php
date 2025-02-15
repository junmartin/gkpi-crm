@extends('layouts.form')

@section('content')

<h3>Update New Asset</h3>

<form action="{{ route('asset_maint.update', $assetMaint->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        <input type="date" id="maint_date" name="maint_date" value="{{$assetMaint->maint_date}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Judul Service</td>
                    <td style="width:50%;">
                        <input type="text" id="maint_title" name="maint_title" maxlength="100"  value="{{$assetMaint->maint_title}}"required>
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
                        <input type="text" id="maint_type" name="maint_type" maxlength="100"  value="{{$assetMaint->maint_type}}"required>
                    </td>
                </tr>
                
                <!-- <tr>
                    <td style="width:50%;">Deskripsi</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="desc" name="desc">{{$assetMaint->desc}}</textarea>
                    </td>
                </tr> -->
                
                <tr>
                    <td style="width:50%;">Tanggal Service Selanjutnya</td>
                    <td style="width:50%;">
                        <input type="date" id="next_maint_date" name="next_maint_date"  value="{{$assetMaint->next_maint_date}}">
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Vendor Maintenance</th>
            </thead>
            <tbody>
                <tr>
                    <td style="width:50%;">Nama Vendor</td>
                    <td style="width:50%;">
                        <input type="text" id="vendor_name" name="vendor_name" maxlength="100" value="{{$assetMaint->vendor_name}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Alamat Vendor</td>
                    <td style="width:50%;">
                        <input type="text" id="vendor_address" name="vendor_address" maxlength="500" value="{{$assetMaint->vendor_address}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Vendor Contact</td>
                    <td style="width:50%;">
                        <input type="text" id="vendor_contact" name="vendor_contact" maxlength="100" value="{{$assetMaint->vendor_contact}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Biaya</td>
                    <td style="width:50%;">
                        <input type="number" id="maint_fee" name="maint_fee" maxlength="100"  value="{{$assetMaint->maint_fee}}">
                    </td>
                </tr>
            </tbody>
        </table>
    </span>

    <span style="float:left; width:33%">
        <table border="1" width="100%">
            
            <thead>
                <th colspan="2">Service Detail</th>
            </thead>
            <tbody>
                <tr>
                    <td style="width:50%;">
                        Masalah<br>
                        <small><em>Kondisi yang menjadi masalah</em></small>
                    </td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:98%" id="masalah" name="masalah">{{$assetMaint->masalah}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">
                        Diagnosa<br>
                        <small><em>Kondisi menurut Vendor Service</em></small>
                    </td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:98%" id="diagnosa" name="diagnosa">{{$assetMaint->diagnosa}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">
                        Tindakan<br>
                        <small><em>Tindakan yang dilakukan untuk mengatasi masalah</em></small>
                    </td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:98%" id="tindakan" name="tindakan">{{$assetMaint->tindakan}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">
                        Hasil<br>
                        <small><em>Hasil akhir setelah tindakan</em></small>
                    </td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:98%" id="hasil" name="hasil">{{$assetMaint->hasil}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Catatan</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="remark" name="remark">{{$assetMaint->remark}}</textarea>
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