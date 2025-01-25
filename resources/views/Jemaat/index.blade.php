@extends('layouts.form')

@section('content')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        border: 1px solid black;
    }
    tr:hover {
        background-color: #f1f1f1;
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
</style>

<?php 
    // Initialize defaults
    $chk_appraisal = $chk_archived = $chk_listed = $chk_sold = "";
    $chk_lakilaki = $chk_perempuan = "";
    $chk_type = [];
    $chk_city = [];


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
?>

<h3>Jemaat</h3>
<form name="myForm" id="myForm" method="GET">
<table border="1">
    <th style="text-align:left; width:50%;"><b>Filter</b> <a href="javascript:void(0)" onclick="uncheck_all();">[clear filter]</a></th>
    <th style="text-align:left; width:25%;"><b>Sorting</b></th>
    <th style="text-align:left; width:25%;"><b>Column</b></th>
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
                Sorting
            </td>
            <td style="vertical-align: top;">
                <!-- <input type="checkbox" class="refresh" name="show_add" value="1" id="show_add" <?php //echo (in_array('show_add',$column_show)) ? 'checked' : '';?>>
                <label for="show_add">Address</label><br> -->

                <!-- <input type="checkbox" class="refresh" name="show_spec" value="1" id="show_spec" <?php //echo (in_array('show_spec',$column_show)) ? 'checked' : '';?>>
                <label for="show_spec">Specification</label><br> -->
<!-- 
                <input type="checkbox" class="refresh" name="show_owner" value="1" id="show_owner" <?php //echo (in_array('show_owner',$column_show)) ? 'checked' : '';?>>
                <label for="show_owner">Owner</label> -->
            </td>
        </tr>
    </tbody>
</table>

</form>

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
            <th>Keterangan</th>
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
                    $age = "N/A";
                }

                $marriage_date = (!empty($jem->marriage_date)) ? date('d M Y',strtotime($jem->marriage_date)) : "";

                $pass_photo = ($jem->pass_photo != "") ? $jem->pass_photo : "storage/jemaat/file/no-image.jpg";
                
            ?>
            <tr style="vertical-align: top; height:60px;" >
                <td>
                    <table style="border:0px;">
                        <tr style="height:20px;">
                            <td rowspan="2" style="vertical-align:top; width:45px;">
                                <a href="{{ route('jemaat.edit', $jem->id)}}" style="text-decoration:none;">
                                    <img src="{{ asset($pass_photo) }}" width="40px" />
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('jemaat.edit', $jem->id)}}" style="text-decoration:none;">
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$kelamin}}</span><span class="badge-value-{{$badge_color}}">{{$jem->name}}</span>
                                        <!-- <span class="badge-key">{{$jem->name}}</span><span class="badge-value-{{$badge_color}}">{{$jem->nick_name}}</span> -->
                                        <!-- <span class="badge-key">{{$age. "thn"}}</span><span class="badge-value-{{$badge_color}}">{{$jem->name}}</span> -->
                                    </span>
                                </a>
                            </td>
                        </tr>
                        <tr style="height:20px;">
                            <td style="vertical-align:top;">{{$age}}</td>
                        </tr>
                    </table>
                </td>
                
                <td>{{$jem->birth_place}}, {{(!empty($jem->birth_date)) ? date('d-M-Y',strtotime($jem->birth_date)) : "N/A"}}</td>
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
                <!-- <td>{{$jem->previous_church}}</td>  -->

                
                <td style="font-size:small; line-height:1;">
                    <a href="#"><img src="https://img.shields.io/badge/Status-Jemaat_Tetap-green" alt="Inquiries"></a>
                    <br>
                    <a href="#"><img src="https://img.shields.io/badge/Baptis-{{$jem->baptise_status}}-blue" alt="Leads"></a>
                    <br>
                    <?php echo (!empty($jem->previous_church)) ? "Gereja Asal: ".$jem->previous_church : "";?>
                            
                </td>
                <td style="text-align:center;">
                    <a href="{{ route('jemaat.edit', $jem->id)}}">[ View ]</a>
                    <br><br><a href="{{ route('jemaat.edit', $jem->id)}}">[ Edit ]</a>
                </td>
            </tr>
        @endforeach
        
    </tbody>
</table>



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
</script>


@endsection