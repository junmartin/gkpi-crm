@extends('layouts.form')

@section('content')

<style>
    /* Container for the cards */
    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        max-height: 300px; /* Adjust the height as needed */
        overflow-y: auto; /* Enable vertical scrolling */
        padding: 10px; /* Add padding for aesthetics */
        border: 1px solid #ccc; /* Optional: for better visibility */
        border-radius: 8px; /* Optional: rounded corners */
        background-color: #fff; /* Optional: background color */
    }

    /* Hidden checkbox */
    .card-container input[type="checkbox"] {
        display: none;
    }

    /* Card styling */
    .card {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 135px;
        height: 80px;
        border: 2px solid #ccc;
        border-radius: 4px;
        background-color: #f9f9f9;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s, border-color 0.3s;
        /* font-size: 14px; */
        /* font-weight: bold; */
    }

    /* Card styling when selected */
    .card-container input[type="checkbox"]:checked + .card {
        background-color: #4CAF50;
        color: #fff;
        border-color: #388E3C;
    }

</style>

<h3>Update Attendance</h3>

<form action="{{ route('sermon.update',$sermon->id); }}" method="POST" enctype="multipart/form-data">
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


    
<?php 
    // echo "<br>Jemaat:".$attds[0]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[1]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[2]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[3]['jemaat_id'];

    // echo "<br>Jemaat:".$jemaats[0]['name'];
    
    $arr_jemaat = [];
    foreach ($jemaats as $x => $jem){
        $arr_jemaat[$jem['id']] = $jem;
    }

    
    $attd_jemaat = [];
    foreach ($attds as $x => $attd){
        $attd_jemaat[$attd['jemaat_id']] = 1;
    }

?>

<div style="width:100%; display:table;">
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            
            <thead>
                <th colspan="2">Sermon Info</th>
            </thead>
            <tbody> 
                <tr>
                    <td style="width:50%;">Date</td>
                    <td style="width:50%;">
                        <input type="date" id="sermon_date" name="sermon_date" value="{{$sermon->sermon_date}}" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tipe Ibadah</td>
                    <td style="width:50%;">
                        <!-- <input type="text" id="ibadah_id" name="ibadah_id" value="{{$sermon->ibadah_id}}"> -->
                        <select name="ibadah_id" id="ibadah_id">
                            <?php foreach ($ibadah as $i=>$ibd) { 
                                $chk = ($ibd['id'] === $sermon->ibadah_id) ? "selected" : "";
                                ?>
                                
                                <option value="{{$ibd['id']}}" {{$chk}}> {{$ibd['ibadah_name']}} </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Kebaktian</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_name" name="ibadah_name" maxlength="100" value="{{$sermon->ibadah_name}}" required>
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Attendance</th>
            </thead>
            <tbody> 
                <tr>
                    <td colspan="2">
                        <div class="card-container">

                            <?php foreach ($arr_jemaat as $j=>$jem) { ?>
                                <?php
                                    $pass_photo = ($jem['pass_photo'] != "") ? $jem['pass_photo'] : "jemaat/file/no-image.jpg";
                                    $kelamin = ($jem['jenis_kelamin'] == "0") ? "L" : "P";
                                    $badge_color = ($jem['jenis_kelamin'] == "0") ? "primary" : "danger";
                                ?>
                                <!-- <br><input type="checkbox" name="jemaat[]" id="<?php echo $j;?>" value="<?php echo $j;?>" <?php echo (!empty($attd_jemaat[$j])) ? "checked" : "";?> > <label for="<?php echo $j;?>"> <?php echo $jem['name'];?> </label> -->
                                
                                <label>
                                    <input type="checkbox" name="jemaat[]" id="<?php echo $j;?>" value="<?php echo $j;?>" <?php echo (!empty($attd_jemaat[$j])) ? "checked" : "";?> >
                                    <!-- <div class="card"><?php echo $jem['name'];?></div> -->

                                    <div class="card">
                                        <table style="border:0px;">
                                            <tr style="height:20px;">
                                                <td style="vertical-align:top; width:45px;">
                                                    <img src="{{ asset($pass_photo) }}" width="40px" />
                                                </td>
                                                <td>
                                                    {{$jem['name']}}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </span>
</div>

<div style="text-align:right;">
    <input type="reset" value="Reset" >
    <input type="submit" value="Submit" >
</div>
</form>

@endsection