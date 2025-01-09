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
        max-width: 1440px;
        width: 100%;
        /* padding: 20px; */
        border: 1px solid #ccc;
        /* border-radius: 8px; */
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
    }

    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .form-grid {
            flex-direction: column; /* Stack elements */
        }

        .form-container {
            padding: 15px;
        }

        table {
            margin-bottom: 20px; /* Space between stacked tables */
        }

        td, th {
            font-size: 14px; /* Adjust font size */
        }
    }

    @media (max-width: 480px) {
        .form-container {
            padding: 10px;
        }

        td, th {
            font-size: 12px;
        }

        input, select, textarea {
            font-size: 14px;
        }
    }
</style>

<div class="form-container">
    <div class="form-grid">
        <div>
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
                        <td style="width:50%;">Nama Panggilan</td>
                        <td style="width:50%;">
                            <input type="text" id="nick_name" name="nick_name" maxlength="100" >
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
                        <td>Alamat</td>
                        <td>
                            <textarea rows="4" cols="50" style="width:100%" id="address" name="address" required></textarea>
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
        </div>
        <div>
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
        </div>
        <div>
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
        </div>
    </div>
    <div style="text-align: right;">
        <button type="reset" class="btn-submit">Reset</button>
        <button type="submit" class="btn-submit">Submit</button>
    </div>
</div>





@endsection