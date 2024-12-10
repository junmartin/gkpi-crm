@extends('layouts.form')

@section('content')
<style>
    body {
        /* display: flex; */
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        max-width: 900px;
        width: 100%;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .form-group textarea {
        resize: vertical;
    }

    .btn-submit {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        /* width: 100%; */
        margin-top: 20px;
    }

    .btn-submit:hover {
        background-color: #45a049;
    }
    
    td {
        /* border: 1px solid #ddd; */
        /* padding: 8px; */
        width: 100%; 
        /* Ensures the table cell takes the available width */
    }

    /* input, select {
        width: 150px;
    } */

    textarea {
        width: 100%;
        box-sizing: border-box;
        resize: none;
        height: 100%; /* This ensures it takes the full height of 4 rows */
        overflow: auto;
    }
</style>


<script>
    function confirmReset() {
        return confirm("Are you sure you want to reset all fields?");
    }

    function confirmSubmit() {
        return confirm("Are you sure you want to submit the form?");
    }
</script>


<h3>Create New Jemaat</h3>


<form action="{{ route('jemaat.store') }}" method="POST" enctype="multipart/form-data">
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
            <!-- <colgroup>
                <col width="50%" >
                <col width="50%" >
            </colgroup> -->
            <thead>
                <th colspan="2">Personal Info</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;">Nama</td>
                    <td style="width:50%;">
                        <input type="text" id="name" name="name" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>
                        <input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="0"> Laki-laki<br>
                        <input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="1"> Perempuan
                    </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>
                        <textarea rows="4" cols="50" style="width:100%" id="address" name="address" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>
                        <input type="text" id="birth_place" name="birth_place" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>
                        <input type="date" id="birth_date" name="birth_date" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td>No HP</td>
                    <td>
                        <input type="text" id="mobile_no" name="mobile_no" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <input type="text" id="email" name="email" maxlength="100">
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Media</th>
            </thead>
            <tbody>
                <tr>
                    <td>Pas Foto</td>
                    <td>
                        <input type="file" id="pass_photo" name="pass_photo" accept=".jpg,.jpeg,.png,.mp4,.avi,.mov">
                        <span id="error_message_media" style="color: red;"></span>
                        <ul id="file_list" style="list-style: none; padding: 0;"></ul>
                        
                    </td>
                </tr>
            
            </tbody>

        </table>
    </span>
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            <thead>
                <th colspan="2">Marital Info</th>
            </thead>
            <tbody>
                <tr>
                    <td>Status Perkawinan</td>
                    <td>
                        <select name="marital_status">
                            <option value="TK"> Tidak Kawin</option>
                            <option value="K"> Kawin</option>
                            <option value="M"> Meninggal</option>
                            <option value="CM"> Cerai/Mati</option>
                            <option value="CH"> Cerai/Hidup</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Pernikahan</td>
                    <td>
                        <input type="date" id="marriage_date" name="marriage_date" maxlength="100" >
                    </td>
                </tr>
                <tr>
                    <td>Nama Pasangan</td>
                    <td>
                        <input type="text" id="spouse_name" name="spouse_name" maxlength="100" >
                    </td>
                </tr>
            </tbody>
        </table>        
    </span>

    <script>
        
        toggle_by_class("t_1", true) ;

        document.getElementById('property_type').addEventListener('change', function() {
            toggle_by_class('t_'+this.value, true);
        });

        function toggle_by_class(cls, on) { 
            var lst_x = document.getElementsByClassName('x');
            for(var i = 0; i < lst_x.length; ++i) {
                lst_x[i].style.display = 'none';
            }

            var lst = document.getElementsByClassName(cls);
            for(var i = 0; i < lst.length; ++i) {
                lst[i].style.display = on ? '' : 'none';
            }
        }
    </script>
    <span style="float:left; width:34%">
        <table border="1" width="100%">
            <thead>
                <th colspan="3">Church Info</th>
            </thead>
            <tbody>
                <tr>
                    <td>Status</td>
                    <td colspan="2">
                        <select id="member_type" name="member_type" required>
                            <option value="permanen">Jemaat Tetap</option>
                            <option value="partisipan">Partisipan</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Status Baptis</td>
                    <td colspan="2">
                        <select id="baptise_status" name="baptise_status" required>
                            <option value="Baptis_Dewasa"> Baptis Dewasa</option>
                            <option value="Baptis_Anak"> Baptis Anak</option>
                            <option value="Sidi"> Sidi</option>
                            <option value="Atestasi"> Atestasi</option>
                            <option value="Belum_Baptis"> Belum Baptis</option>
                            <option value="Baptis_Gereja_Lain"> Baptis Gereja Lain</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Gereja Asal</td>
                    <td>
                        <input type="text" id="previous_church" name="previous_church" maxlength="100">
                    </td>
                </tr>
                <tr>
                    <td>Remark</td>
                    <td colspan="2">
                        <textarea rows="4" cols="50" style="width:100%" id="remark" name="remark"></textarea>
                    </td>
                </tr>
        </table>
    </span>
</div>
<div style="text-align:right;">
    <input type="reset" value="Reset" >
    <input type="submit" value="Submit" >
</div>
</form>

@endsection

@section('script')
<script src="{{ asset('assets/js/add_jemaat.js') }}?v={{time()}}"></script>
@endsection