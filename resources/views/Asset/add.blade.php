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
                        <textarea rows="4" cols="50" style="width:98%" id="spec" name="spec"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </span>
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            
            <thead>
                <th colspan="2">Ownership & Status</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;">Asset Status</td>
                    <?php 
                        $asset_status['new'] = "New";
                        $asset_status['use'] = "In Use";
                        $asset_status['oos'] = "Out of Service";
                        $asset_status['sto'] = "In Storage";
                        $asset_status['dis'] = "Disposed";
                        $asset_status['los'] = "Lost/Stolen";
                        $asset_status['dmg'] = "Damage/Broken";
                    ?>
                    <td style="width:50%;">
                        <select type="text" id="status" name="status" required>
                            @foreach($asset_status as $s => $sts)
                                <option value="{{$s}}">{{$sts}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Location</td>
                    <td style="width:50%;">
                        <select type="text" id="location" name="location" required>
                            <option value="Ruang Ibadah">Ruang Ibadah</option>
                            <option value="Ruang Sekolah Minggu">Ruang Sekolah Minggu</option>
                            <option value="Ruang Tengah">Ruang Tengah</option>
                            <option value="Kantor">Kantor</option>
                            <option value="Pastori">Pastori</option>
                            <option value="Dapur">Dapur</option>
                            <option value="Gudang">Gudang</option>
                            <option value="Cafe">Cafe</option>
                            <option value="Halaman">Halaman</option>
                            <option value="Tempat Lain">Tempat Lain</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">PIC</td>
                    <td style="width:50%;">
                        <input type="text" id="pic" name="pic" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Ownership<br><small>(leave blank if belong to Church)</small></td>
                    <td style="width:50%;">
                        <input type="text" id="ownership" name="ownership" maxlength="100">
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Media</th>
            </thead>
            <tbody>
                <tr>
                    <td>Foto Asset</td>
                    <td>
                        <input type="file" id="asset_photo" name="asset_photo[]" multiple accept=".jpg,.jpeg,.png,.mp4,.avi,.mov">
                        <span id="error_message_media" style="color: red;"></span>
                        <ul id="file_list" style="list-style: none; padding: 0;"></ul>                        
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

@section('script')
<script src="{{ asset('assets/js/add_assets.js') }}?v={{time()}}"></script>
@endsection