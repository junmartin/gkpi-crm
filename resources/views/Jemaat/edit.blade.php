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


<h3>Edit Jemaat</h3>


<form action="{{ route('jemaat.update',$jemaat->id) }}" method="POST" enctype="multipart/form-data">
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
                        <input type="text" id="name" name="name" maxlength="100" required value="{{$jemaat->name}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Nama Panggilan</td>
                    <td style="width:50%;">
                        <input type="text" id="nick_name" name="nick_name" maxlength="100" value="{{$jemaat->nick_name}}">
                    </td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <?php 
                    $chk_male = ''; $chk_female = '';
                    $chk_male = ($jemaat['jenis_kelamin'] == 0 ) ? "checked" : "";
                    $chk_female = ($jemaat['jenis_kelamin'] == 1 ) ? "checked" : "";
                    ?>
                    <td>
                        <input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="0" {{$chk_male}}> Laki-laki<br>
                        <input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="1" {{$chk_female}}> Perempuan
                    </td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>
                        <input type="text" id="birth_place" name="birth_place" maxlength="100" value="{{$jemaat->birth_place}}">
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>
                        <input type="date" id="birth_date" name="birth_date" maxlength="100" value="{{$jemaat->birth_date}}">
                    </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>
                        <textarea rows="4" cols="50" style="width:100%" id="address" name="address">{{$jemaat->address}}</textarea>
                    </td>
                </tr>
                <tr>
                    <td>No HP</td>
                    <td>
                        <input type="text" id="mobile_no" name="mobile_no" maxlength="100" value="{{$jemaat->mobile_no}}">
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        <input type="text" id="email" name="email" maxlength="100" value="{{$jemaat->email}}">
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
                        <!-- <ul id="file_list" style="list-style: none; padding: 0;"></ul>
                        <ol>
                            <li>
                                <a href="{{ Storage::url($jemaat->pass_photo) }}" target="_blank">
                                {{ basename($jemaat->pass_photo) }}<br>
                                </a>
                            </li>
                        </ol> -->
                        <?php 
                            $jemaat_photo = ($jemaat->pass_photo !="") ? $jemaat->pass_photo : "storage/jemaat/file/no-image.jpg";
                        ?>
                        <img src="{{ asset($jemaat_photo) }}" width="50px" />
                    </td>
                </tr>
            
            </tbody>

        </table>
    </span>
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            <thead>
                <th colspan="2">Emergency Contact</th>
            </thead>
            <tbody>
                <tr>
                    <td>Name <small><em>(Jangan yang sekeluarga / serumah)</em></small></td>
                    <td>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" maxlength="100" value="{{$jemaat->emergency_contact_name}}">
                    </td>
                </tr>
                <tr>
                    <td>Mobile</td>
                    <td>
                        <input type="text" id="emergency_contact_mobile" name="emergency_contact_mobile" maxlength="100" value="{{$jemaat->emergency_contact_mobile}}">
                    </td>
                </tr>
                <tr>
                    <td>Relationship</td>
                    <td>
                        <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" maxlength="100" value="{{$jemaat->emergency_contact_relation}}">
                    </td>
                </tr>
                
            </tbody>
            <thead>
                <th colspan="2">Marital Info</th>
            </thead>
            <tbody>
                <tr>
                    <?php 
                        $marital = [];
                        $marital['TK'] = ($jemaat->marital_status == 'TK') ? 'selected' : '';
                        $marital['K'] = ($jemaat->marital_status == 'K') ? 'selected' : '';
                        $marital['M'] = ($jemaat->marital_status == 'M') ? 'selected' : '';
                        $marital['CM'] = ($jemaat->marital_status == 'CM') ? 'selected' : '';
                        $marital['CH'] = ($jemaat->marital_status == 'CH') ? 'selected' : '';
                    ?>

                    <td>Status Perkawinan</td>
                    <td>
                        <select name="marital_status">
                            <option value="TK" <?php echo $marital['TK'];?>> Tidak Kawin</option>
                            <option value="K" <?php echo $marital['K'];?>> Kawin</option>
                            <option value="M" <?php echo $marital['M'];?>> Meninggal</option>
                            <option value="CM" <?php echo $marital['CM'];?>> Cerai/Mati</option>
                            <option value="CH" <?php echo $marital['CH'];?>> Cerai/Hidup</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Pernikahan</td>
                    <td>
                        <input type="date" id="marriage_date" name="marriage_date" maxlength="100" value="{{$jemaat->marriage_date}}">
                    </td>
                </tr>
                <tr>
                    <td>Nama Pasangan</td>
                    <td>
                        <input type="text" id="spouse_name" name="spouse_name" maxlength="100" value="{{$jemaat->spouse_name}}">
                    </td>
                </tr>
            </tbody>
            <!-- <tr><td colspan="2" style="border:0px;">&nbsp;</td></tr> -->
            <thead>
                <th colspan="2">Family</th>
            </thead>
            <tbody>
                <tr>
                    <td>Assign to Family</td>
                    <td>
                        <!-- <a href="{{route('assign_family',$jemaat->id)}}"> Assign Family </a> -->

                        <select name="family_id" id="family_id">
                        <option value=""> N/A
                        @foreach($family as $fam)
                            <?php $sel = ($fam['id'] == $jemaat->family_id) ? "selected" : "";?>
                            <option value="{{$fam->id}}" {{$sel}}> {{$fam->family_name}}
                        @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Role</td>
                    <td>
                        <select name="role" id="role">
                            <option value="N/A"> N/A
                            <?php if($jemaat['jenis_kelamin'] == 0) {?>
                                <option value="Suami" <?php echo ($jemaat->role == "Suami") ? "selected" :"";?>> Suami
                            <?php } ?>

                            <?php if($jemaat['jenis_kelamin'] == 1) {?>
                                <option value="Istri" <?php echo ($jemaat->role == "Istri") ? "selected" :"";?>> Istri
                            <?php } ?>

                            <option value="Anak" <?php echo ($jemaat->role == "Anak") ? "selected" :"";?>> Anak
                        </select>
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
                    <?php 
                        $member['permanen'] = ($jemaat->member_type == 'permanen') ? 'selected' : '';
                        $member['simpatisan'] = ($jemaat->member_type == 'simpatisan') ? 'selected' : '';

                    ?>
                    <td colspan="2">
                        <select id="member_type" name="member_type" required>
                            <option value="simpatisan" <?php echo $member['simpatisan'];?> >Simpatisan</option>
                            <option value="permanen" <?php echo $member['permanen'];?> >Jemaat Tetap</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Status Baptis</td>
                    <?php
                        $baptis = [];
                        $baptis['Baptis_Dewasa'] = ($jemaat->baptise_status == 'Baptis_Dewasa') ? "selected" : '';
                        $baptis['Baptis_Anak'] = ($jemaat->baptise_status == 'Baptis_Anak') ? "selected" : '';
                        $baptis['Sidi'] = ($jemaat->baptise_status == 'Sidi') ? "selected" : '';
                        $baptis['Atestasi'] = ($jemaat->baptise_status == 'Atestasi') ? "selected" : '';
                        $baptis['Belum_Baptis'] = ($jemaat->baptise_status == 'Belum_Baptis') ? "selected" : '';
                        $baptis['Baptis_Gereja_Lain'] = ($jemaat->baptise_status == 'Baptis_Gereja_Lain') ? "selected" : '';
                    ?>
                    <td colspan="2">
                        <select id="baptise_status" name="baptise_status" required>
                            <option value="Belum_Baptis" <?php echo $baptis['Belum_Baptis'];?>> Belum Baptis</option>
                            <option value="Baptis_Dewasa" <?php echo $baptis['Baptis_Dewasa'];?>> Baptis Dewasa</option>
                            <option value="Baptis_Anak" <?php echo $baptis['Baptis_Anak'];?>> Baptis Anak</option>
                            <option value="Baptis_Gereja_Lain" <?php echo $baptis['Baptis_Gereja_Lain'];?>> Baptis Gereja Lain</option>
                            <option value="Sidi" <?php echo $baptis['Sidi'];?>> Sidi</option>
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
        <table border="1" width="100%">
            <thead>
                <th colspan="2">Sermon Attendance (Last 10)</th>
            </thead>
            <tbody>
                @foreach($sermons as $sermon)
                    <tr>
                        <td>{{ $sermon->sermon_date }} - {{ $sermon->ibadah_name }}</td>
                        <td>
                            <input type="checkbox" name="sermons[]" value="{{ $sermon->id }}" {{ in_array($sermon->id, $sermon_attendances) ? 'checked' : '' }}>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </span>
</div>
<div style="text-align:right;">
    <!-- <input type="reset" value="Reset" > -->
    <input type="submit" value="Submit" >
</div>
</form>

@endsection

@section('script')
    <script src="{{ asset('assets/js/edit_jemaat.js') }}?v={{ time() }}"></script>
@endsection