@extends('layouts.form')

@section('content')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        /* margin-bottom: 20px; */
        border: 1px solid black;
    }
    tr:hover {
        background-color: #f1f1f1;
    }

    .modal-content tr:hover {
        background-color: transparent;
    }

    td {
        padding:5px 2px;
        /* padding: 10px; */
    }

    .top-left {
      float: left;
    }

    .bottom-right {
      float: right;
      clear: both;
    }

    .badge{
        
        display: inline-block;
        padding: 0; !important
        margin: 0.15rem;
        font-family: Helvetica;
        font-size: 70%;
        font-weight: 500;
        letter-spacing: .05rem;
        line-height: 1;
        color: #555;
        text-align: center;
        /* text-shadow: 1px 1px 1px black; */
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .badge-key{
        border-bottom-left-radius: 0.25rem;
        border-top-left-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#5b5b5b;color:#fff;
        background: linear-gradient(#5b5b5b, #4b4b4b);
    }

    .badge-value{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(#0e80c1, #0273B4);
    }    
    .badge-value-primary{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(#0e80c1, #0273B4);
    }    
    .badge-value-danger{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(#c10e80, #B40273);
    }    

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        width: 70%;
        position: relative;
        z-index: 1;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-content::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('/storage/logo-gereja.png');
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0.3;
        z-index: -1;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<?php 
    // Initialize defaults
    $chk_appraisal = $chk_archived = $chk_listed = $chk_sold = "";
    $chk_lakilaki = $chk_perempuan = "";

    $chk_lansia = $chk_dewasa = $chk_pemuda = $chk_sekolah = "";

    $chk_type = [];
    $chk_city = [];


    // FILTER

    $chk_lakilaki = (!empty($param['lakilaki'])) ? 'checked' : '';
    $chk_perempuan = (!empty($param['perempuan'])) ? 'checked' : '';

    foreach ($distinct_city as $city) {
        $chk_city[str_replace(' ', '_', $city)] = (!empty($param['city_'.str_replace(' ', '_', $city)])) ? "checked" : "";
    }      

    foreach ($distinct_status as $status) {
        $chk_status[str_replace(' ', '_', $status)] = (!empty($param['status_'.str_replace(' ', '_', $status)])) ? "checked" : "";
    }      

    foreach ($distinct_baptise as $baptise) {
        $chk_baptise[str_replace(' ', '_', $baptise)] = (!empty($param['baptise_'.str_replace(' ', '_', $baptise)])) ? "checked" : "";
    }      

    // SORTING
    $chk_sort_age_asc = (!empty($param['sort']) && $param['sort'] == 'age_asc') ? "checked" : "";
    $chk_sort_age_desc = (!empty($param['sort']) && $param['sort'] == 'age_desc') ? "checked" : "";
    
    $chk_sort_alphabet_asc = (!empty($param['sort']) && $param['sort'] == 'name_asc') ? "checked" : "";
    $chk_sort_alphabet_desc = (!empty($param['sort']) && $param['sort'] == 'name_desc') ? "checked" : "";
    
    $chk_sort_input_desc = (!empty($param['sort']) && $param['sort'] == 'input_desc') ? "checked" : "";

    $chk_lansia = (!empty($param['lansia'])) ? 'checked' : '';
    $chk_dewasa = (!empty($param['dewasa'])) ? 'checked' : '';
    $chk_pemuda = (!empty($param['pemuda'])) ? 'checked' : '';
    $chk_sekolah = (!empty($param['sekolah'])) ? 'checked' : '';
    
?>

<h3>Jemaat</h3>
<form name="myForm" id="myForm" method="GET">
<table border="1">
    <th style="text-align:left; width:30%;"><b>Filter</b> <a href="javascript:void(0)" onclick="uncheck_all();">[clear filter]</a></th>
    <th style="text-align:left; width:30%;"><b>Filter</b></th>
    <th style="text-align:left; width:40%;"><b>Sorting</b></th>
    <!-- <th style="text-align:left; width:25%;"><b>Column</b></th> -->
    <tbody>
<?php
// Get the status, for, type, and city parameters from the URL
$statusParams = explode('|', $_GET['status'] ?? '');
$forParams = explode('|', $_GET['for'] ?? '');
$typeParams = explode('|', $_GET['type'] ?? '');
$cityParams = explode('|', $_GET['city'] ?? '');


?>

        <tr>
            <td style="vertical-align: top;">

                Jenis Kelamin:
                <input type="checkbox" class="refresh" name="lakilaki" value="1" id="lakilaki" <?php echo $chk_lakilaki;?>>
                <label for="lakilaki">Laki-laki</label>

                <input type="checkbox" class="refresh" name="perempuan" value="1" id="perempuan" <?php echo $chk_perempuan;?>>
                <label for="perempuan">Perempuan</label>
                <br>

                Status:
                <?php foreach ($distinct_status as $status) { ?>
                    <input type="checkbox" class="refresh" name="<?php echo "status_".str_replace(' ', '_', $status);?>" value="1" 
                        id="<?php echo "status_".str_replace(' ', '_', $status);?>" <?php echo $chk_status[str_replace(' ', '_', $status)];?>>
                    <label for="<?php echo "status_".str_replace(' ', '_', $status);?>"> <?php echo ucfirst($status); ?></label>
                <?php } ?>
                <br>

                Baptis:
                <?php foreach ($distinct_baptise as $baptise) { ?>
                    <input type="checkbox" class="refresh" name="<?php echo "baptise_".str_replace(' ', '_', $baptise);?>" value="1" 
                        id="<?php echo "baptise_".str_replace(' ', '_', $baptise);?>" <?php echo $chk_baptise[str_replace(' ', '_', $baptise)];?>>
                    <label for="<?php echo "baptise_".str_replace(' ', '_', $baptise);?>"> <?php echo ucfirst($baptise); ?></label>
                <?php } ?>
                <br>

                <!-- Tempat Lahir: -->
                <?php //foreach ($distinct_city as $city) { ?>
                    <!-- <input type="checkbox" class="refresh" name="<?php echo "city_".str_replace(' ', '_', $city);?>" value="1" 
                        id="<?php //echo "city_".str_replace(' ', '_', $city);?>" <?php echo $chk_city[str_replace(' ', '_', $city)];?>> -->
                    <!-- <label for="<?php echo "city_".str_replace(' ', '_', $city);?>"> <?php echo ucfirst($city); ?></label> -->
                <?php //} ?>

            </td>
            <td style="vertical-align: top;">
                Kelompok Usia :
                <input type="checkbox" class="refresh" name="lansia" value="1" id="lansia" <?php echo $chk_lansia;?>>
                <label for="lansia">Lansia</label>

                <input type="checkbox" class="refresh" name="dewasa" value="1" id="dewasa" <?php echo $chk_dewasa;?>>
                <label for="dewasa">Dewasa</label>
                
                <br>
                <input type="checkbox" class="refresh" name="pemuda" value="1" id="pemuda" <?php echo $chk_pemuda;?>>
                <label for="pemuda">Pemuda</label>

                <input type="checkbox" class="refresh" name="sekolah" value="1" id="sekolah" <?php echo $chk_sekolah;?>>
                <label for="sekolah">Sekolah Minggu</label>
                <br>

            </td>
            <td style="vertical-align: top;">
                <input type="radio" class="refresh" name="sort" id="sort_age_asc" value="age_asc" <?php echo $chk_sort_age_asc;?>><label for="sort_age_asc">Termuda</label>
                <input type="radio" class="refresh" name="sort" id="sort_age_desc" value="age_desc" <?php echo $chk_sort_age_desc;?>><label for="sort_age_desc">Tertua</label>
                <br>
                <input type="radio" class="refresh" name="sort" id="sort_alphabet_asc" value="name_asc" <?php echo $chk_sort_alphabet_asc;?>><label for="sort_alphabet_asc">A-Z</label>
                <input type="radio" class="refresh" name="sort" id="sort_alphabet_desc" value="name_desc" <?php echo $chk_sort_alphabet_desc;?>><label for="sort_alphabet_desc">Z-A</label>
                <br>
                <input type="radio" class="refresh" name="sort" id="sort_input_desc" value="input_desc" <?php echo $chk_sort_input_desc;?>><label for="sort_input_desc">Last Input</label>
            </td>
            <!-- <td style="vertical-align: top;"> -->
                <!-- <input type="checkbox" class="refresh" name="show_add" value="1" id="show_add" <?php //echo (in_array('show_add',$column_show)) ? 'checked' : '';?>>
                <label for="show_add">Address</label><br> -->

                <!-- <input type="checkbox" class="refresh" name="show_spec" value="1" id="show_spec" <?php //echo (in_array('show_spec',$column_show)) ? 'checked' : '';?>>
                <label for="show_spec">Specification</label><br> -->
<!-- 
                <input type="checkbox" class="refresh" name="show_owner" value="1" id="show_owner" <?php //echo (in_array('show_owner',$column_show)) ? 'checked' : '';?>>
                <label for="show_owner">Owner</label> -->
            <!-- </td> -->
        </tr>
    </tbody>
</table>

</form>
<br>
<a href="{{ route('jemaat.create')}}">[+ Add New]</a>
<table border="1">
    <colgroup>
        <!-- Nama -->
        <col width="15%" style="visibility:visible"> 

        <!-- Tmpt & Tgl Lahir -->
        <col width="10%" style="visibility:visible">

        <!-- Address -->
        <col width="10%" style="visibility:visible">

        <!-- HP -->
        <col width="10%" style="visibility:visible">

        <!-- Status Kawin -->
        <col width="5%" style="visibility:visible">

        <!-- Umur -->
        <!-- <col width="5%" style="visibility:visible"> -->

        <!-- Nama Pasangan -->
        <col width="10%" style="visibility:visible">
    
        <!-- Tgl Kawin -->
        <col width="10%" id="col_spec" style="visibility:visible">
    
        <!-- Gereja Asal -->
        <col width="5%" id="col_vendor" style="visibility:visible">
        
        <!-- Keterangan -->
        <col width="10%" style="visibility:visible">

        <col width="5%" style="visibility:visible">
    </colgroup>
    <thead>
        <tr>
            <!-- <th>Listing Date</th> -->
            <th>Nama</th>            
            <th>Tempat & Tgl Lahir</th>
            <th>Kontak</th>
            <th>Alamat</th>            
            <th>Keluarga</th>
            <!-- <th>Status Perkawinan</th>
            <th>Nama Pasangan</th>
            <th>Tgl Pernikahan</th> -->

            <!-- <th>Gereja Asal</th> -->

            <th>Kontak Darurat</th>
            <!-- <th>Ultah Perkawinan</th> -->
            <th>Keterangan</th>
            <th>Kehadiran</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <!-- Example Data Row -->

        <?php 
            $status_kawin = [];
            $status_kawin['TK'] = "Tidak Kawin";
            $status_kawin['K'] = "Kawin";
            $status_kawin['M'] = "Meninggal";
            $status_kawin['CM'] = "Cerai/Mati";
            $status_kawin['CH'] = "Cerai/Hidup";

            $member['simpatisan']['name'] = 'Simpatisan';   
            $member['simpatisan']['color'] = 'orange';   
            $member['permanen']['name'] = 'Jemaat Tetap';   
            $member['permanen']['color'] = 'green';
        ?>

        @foreach($jemaats as $jem)
            <?php 
                $kelamin = ($jem->jenis_kelamin == "0") ? "L" : "P";
                $badge_color = ($jem->jenis_kelamin == "0") ? "primary" : "danger";
                
                if(!empty($jem->birth_date)){

                    $birthDate = new DateTime($jem->birth_date);
                    $today = new DateTime(); // Gets the current date
                    $age = $today->diff($birthDate)->y." tahun"; // Calculate the difference in years
                }else{
                    $age = "";
                }

                $marriage_date = (!empty($jem->marriage_date)) ? date('d M Y',strtotime($jem->marriage_date)) : "";

                $pass_photo = ($jem->pass_photo != "") ? $jem->pass_photo : "storage/jemaat/file/no-image.jpg";
                
            ?>
            <tr style="vertical-align: top; height:60px;" >
                <td>
                    <table style="border:0px;">
                        <tr style="height:20px;">
                            <td rowspan="2" style="vertical-align:top; width:45px;">
                                <div style="cursor:pointer;" onclick="show_modal('{{$jem->name}}', '{{ asset($pass_photo) }}', '{{$jem->birth_place}}', '{{ date('d-M-Y',strtotime($jem->birth_date)) }}', '{{$jem->mobile_no}}', '{{$jem->email}}', '{{$jem->address}}', '{{$status_kawin[$jem->marital_status]}}', '{{$jem->spouse_name}}', '{{$marriage_date}}', '{{$jem->previous_church}}', '{{$jem->emergency_contact_name}}', '{{$jem->emergency_contact_mobile}}', '{{$jem->emergency_contact_relation}}', '{{$member[$jem->member_type]['name']}}', '{{$jem->baptise_status}}')">
                                    <img src="{{ asset($pass_photo) }}" width="40px" />
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('jemaat.show', $jem->id)}}" style="text-decoration:none;">
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$kelamin}}</span><span class="badge-value-{{$badge_color}}">{{$jem->name}}</span>
                                    </span>
                                </a>
                            </td>
                        </tr>
                        <tr style="height:20px;">
                            <td style="vertical-align:top;">{{$age}}</td>
                        </tr>
                    </table>
                </td>
                
                <td>{{(!empty($jem->birth_place)) ? $jem->birth_place : "(no info)" }}, {{(!empty($jem->birth_date)) ? date('d-M-Y',strtotime($jem->birth_date)) : "(no info)"}}</td>
                <td>{{$jem->mobile_no}}<br>{{$jem->email}}</td>
                <td>{{$jem->address}}</td>
                <!-- <td>{{$status_kawin[$jem->marital_status]}}</td> -->
                <!-- <td>{{$age." tahun"}}</td> -->
                <!-- <td>{{$jem->spouse_name}}</td>
                <td>{{$marriage_date}}</td>
                <td>{{$jem->previous_church}}</td> -->
                
                <td>
                    {{$jem->family->family_name ?? '-'}}
                    <!-- <img src="https://img.shields.io/badge/Baptis-{{$jem->baptise_status}}-blue" alt="Keluarga"> -->
                </td>
                <td>
                    <b>{{$jem->emergency_contact_name}} {{$jem->emergency_contact_mobile}}</b><br>
                    <small>{{$jem->emergency_contact_relation}}</small>
                </td>
                <!-- <td>{{$marriage_date}}</td> -->
                <!-- <td>{{$jem->previous_church}}</td>  -->

                <td style="font-size:small; line-height:1;">
                    <a href="#"><img src="https://img.shields.io/badge/Status-{{ucfirst($member[$jem->member_type]['name'])}}-{{$member[$jem->member_type]['color']}}" alt="Inquiries"></a>
                    <br>
                    <a href="#"><img src="https://img.shields.io/badge/Baptis-{{$jem->baptise_status}}-blue" alt="Leads"></a>
                    <br>
                    <?php echo (!empty($jem->previous_church)) ? "Gereja Asal: ".$jem->previous_church : "";?>
                            
                </td>
                <td>
                    @if(isset($attendance_data[$jem->id]))
                        {{ $attendance_data[$jem->id]['count'] }} / {{ $attendance_data[$jem->id]['total'] }} ({{ $attendance_data[$jem->id]['percentage'] }}%)
                    @else
                        0 / {{ $attendance_data[array_key_first($attendance_data)]['total'] ?? 0 }} (0%)
                    @endif
                </td>
                <td style="text-align:center;">
                    <!-- <a href="{{ route('jemaat.edit', $jem->id)}}">[ View ]</a>
                    <br><br> -->
                    <a href="{{ route('jemaat.edit', $jem->id)}}">[ Edit ]</a>
                </td>
            </tr>
        @endforeach
        
    </tbody>
</table>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2 style="text-align:center;">GKPI Griya Permata</h2>
    <hr>
    <table width="100%">
        <tr>
            <td width="20%" style="vertical-align: top;">
                <img id="modal_img" src="" width="100%"/>
                <div style="background: rgba(239,132,199,0.4);">
                    <em>Status Perkawinan:</em> <br><b><span id="modal_marital_status"></span></b><br><br>
                    <em>Nama Pasangan:</em> <br><b><span id="modal_spouse_name"></span></b><br><br>
                    <em>Tgl Pernikahan:</em> <br><b><span id="modal_marriage_date"></span></b><br><br>
                </div>
            </td>
            <td width="80%" style="vertical-align: top;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="vertical-align:top;">
                            <em>Nama:</em> <br><b><span id="modal_name"></span></b><br><br>
                            <em>Tempat/Tgl Lahir:</em> <br><b><span id="modal_birth_place"></span></b>, <b><span id="modal_birth_date"></span></b><br><br>
                            <em>No. HP:</em> <br><b><span id="modal_mobile_no"></span></b><br><br>
                            <em>Email:</em> <br><b><span id="modal_email"></span></b><br><br>
                            <em>Alamat:</em> <br><b><span id="modal_address"></span></b><br><br>
                        </td>
                        <td width="50%" style="vertical-align:top;">
                            <em>Gereja Asal:</em> <br><b><span id="modal_previous_church"></span></b><br><br>
                            <em>Kontak Darurat:</em> <br><b><span id="modal_emergency_contact_name"></span></b> (<b><span id="modal_emergency_contact_relation"></span></b>) <b><span id="modal_emergency_contact_mobile"></span></b><br><br>
                            <em>Status Keanggotaan:</em> <br><b><span id="modal_member_type"></span></b><br><br>
                            <em>Status Baptis:</em> <br><b><span id="modal_baptise_status"></span></b><br><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
  </div>
</div>

<script>

    const refresh_trigger = document.getElementsByClassName('refresh');
    
    for (let index = 0; index < refresh_trigger.length; index++) {
        const element = refresh_trigger[index];
        
        refresh_trigger[index].addEventListener('change', (event) => {
            document.forms[0].submit();
        });
    }
    
    function uncheck_all() {
        var checkboxes = document.getElementsByTagName('input');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
        }
        document.forms[0].submit();
    }

    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    function show_modal(name, pass_photo, birth_place, birth_date, mobile_no, email, address, marital_status, spouse_name, marriage_date, previous_church, emergency_contact_name, emergency_contact_mobile, emergency_contact_relation, member_type, baptise_status) {
        modal.style.display = "block";
        document.getElementById('modal_img').src = pass_photo;
        document.getElementById('modal_name').innerHTML = name;
        document.getElementById('modal_birth_place').innerHTML = birth_place;
        document.getElementById('modal_birth_date').innerHTML = birth_date;
        document.getElementById('modal_mobile_no').innerHTML = mobile_no;
        document.getElementById('modal_email').innerHTML = email;
        document.getElementById('modal_address').innerHTML = address;
        document.getElementById('modal_marital_status').innerHTML = marital_status;
        document.getElementById('modal_spouse_name').innerHTML = spouse_name;
        document.getElementById('modal_marriage_date').innerHTML = marriage_date;
        document.getElementById('modal_previous_church').innerHTML = previous_church;
        document.getElementById('modal_emergency_contact_name').innerHTML = emergency_contact_name;
        document.getElementById('modal_emergency_contact_mobile').innerHTML = emergency_contact_mobile;
        document.getElementById('modal_emergency_contact_relation').innerHTML = emergency_contact_relation;
        document.getElementById('modal_member_type').innerHTML = member_type;
        document.getElementById('modal_baptise_status').innerHTML = baptise_status;
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>


@endsection