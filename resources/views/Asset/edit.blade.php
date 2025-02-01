@extends('layouts.form')

@section('content')

<h3>Update Asset</h3>

<form action="{{ route('asset.update', $asset->id) }}" method="POST" enctype="multipart/form-data">
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

<div style="width:100%; display:table; overflow-y:auto;">
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
                        <input type="text" id="name" name="name" maxlength="100" value="{{$asset->name}}" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tanggal Perolehan</td>
                    <td style="width:50%;">
                        <input type="date" id="acquired_date" name="acquired_date" value="{{$asset->acquired_date}}">
                    </td>
                </tr>
                
                <tr>
                    <td style="width:50%;">Merk</td>
                    <td style="width:50%;">
                        <input type="text" id="merk" name="merk" value="{{$asset->merk}}" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Model</td>
                    <td style="width:50%;">
                        <input type="text" id="model" name="model" value="{{$asset->model}}" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Serial No.</td>
                    <td style="width:50%;">
                        <input type="text" id="serial_number" name="serial_number" value="{{$asset->serial_number}}"  maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tipe</td>
                    <td style="width:50%;">
                        <input type="text" id="tipe" name="tipe" value="{{$asset->tipe}}" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Spesifikasi</td>
                    <td style="width:50%;">
                        <textarea rows="4" cols="50" style="width:100%" id="spec" name="spec">{{$asset->spec}}</textarea>
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
                        <p style="color:red"><b>Perhatian! Foto Lama Akan Terhapus Jika Mengupload Foto Baru</b></p>
                        <span id="error_message_media" style="color: red;"></span>
                        <ul id="file_list" style="list-style: none; padding: 0;"></ul>
                        <ol>
                            @foreach($asset_photos as $photo)

                            <?php 
                                $asset_photo = ($photo->asset_photo !="") ? $photo->asset_photo : "storage/jemaat/file/no-image.jpg";
                            ?>
                            
                            <img src="{{ asset($asset_photo) }}" width="50px" />
                            @endforeach
                        </ol>
                    </td>
                </tr>
            
            </tbody>
        </table>
    </span>
    <span style="float:left; width:33%; max-height:300px">
        <table border="1" width="100%" style="">
            
            <thead>
                <th>Service / Maintenance Log</th>
            </thead>
            <tbody>                
                <tr>
                    <td height="300" style="vertical-align:top;">
                        <span style=" max-height: 300px; overflow-y:scroll">
                        Recent Services:<br>
                        
                        <ul>
                            @foreach ($maints as $m)
                            <li>
                                <a href="{{route('asset_maint.edit',$m->id)}}">
                                {{$m->maint_date}} - {{$m->maint_title}}
                                </a>
                            </li>
                            @endforeach
                            
                        </ul>
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
<script src="{{ asset('assets/js/edit_assets.js') }}?v={{time()}}"></script>
@endsection