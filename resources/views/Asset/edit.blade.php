@extends('layouts.form')

@section('content')

<style>
    .gallery {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .gallery img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 5px;
    }

    /* Modal Styling */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90%;
        position: relative;
        display: flex;
        align-items: center;
    }

    .modal img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 5px;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    .nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 30px;
        color: white;
        background: none;
        border: none;
        cursor: pointer;
        padding: 10px;
    }

    .prev {
        left: 10px;
    }

    .next {
        right: 10px;
    }
</style>

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
                        <!-- <ul id="file_list" style="list-style: none; padding: 0;"></ul>
                        <ol> -->
                            <div class="gallery">
                            @foreach($asset_photos as $p => $photo)

                            <?php 
                                $asset_photo = ($photo->asset_photo !="") ? $photo->asset_photo : "storage/jemaat/file/no-image.jpg";
                            ?>
                            
                            <img src="{{ asset($asset_photo) }}" width="50px" onclick="openModal({{$p}});"/>
                            @endforeach
                            </div>
                        <!-- </ol> -->
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

<div class="modal" id="imageModal">
    <div class="modal-content">
        <button class="nav-button prev" onclick="prevImage()">&#10094;</button>
        <img id="modalImage" src="" alt="Preview">
        <button class="nav-button next" onclick="nextImage()">&#10095;</button>
        <span class="close" onclick="closeModal()">&times;</span>
    </div>
</div>


<div style="text-align:right;">
    <input type="submit" value="Submit" >
</div>
</form>

@endsection

@section('script')
<script src="{{ asset('assets/js/edit_assets.js') }}?v={{time()}}"></script>

<script>
    let currentIndex = 0;
    const images = document.querySelectorAll('.gallery img');
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    function openModal(index) {
        currentIndex = index;
        modalImage.src = images[currentIndex].src;
        modal.style.display = "flex";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        modalImage.src = images[currentIndex].src;
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        modalImage.src = images[currentIndex].src;
    }

    document.addEventListener('keydown', (event) => {
        if (modal.style.display === "flex") {
            if (event.key === "ArrowRight") {
                nextImage();
            } else if (event.key === "ArrowLeft") {
                prevImage();
            } else if (event.key === "Escape") {
                closeModal();
            }
        }
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
</script>

@endsection