@extends('layouts.form')

@section('content')

<h3>Record Attendance</h3>

<form action="{{ route('attendance.adjust_update',['sermon_date'=>$sermon_date,'ibadah_id'=>$ibadah_id]) }}" method="POST" enctype="multipart/form-data">
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


    
<?php 
    // echo "<br>Jemaat:".$attds[0]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[1]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[2]['jemaat_id'];
    // echo "<br>Jemaat:".$attds[3]['jemaat_id'];

    echo "<br>Jemaat:".$jemaats[0]['name'];
    
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
                        <input type="date" id="sermon_date" name="sermon_date" value="{{$attds[0]->sermon_date}}" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tipe Ibadah</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_id" name="ibadah_id" value="{{$attds[0]->ibadah_id}}">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Kebaktian</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_name" name="ibadah_name" maxlength="100" value="{{$attds[0]->ibadah_name}}" required>
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Kehadiran</th>
            </thead>
            <tbody> 
                <tr>
                    <td colspan="2">
                        <?php foreach ($arr_jemaat as $j=>$jem) { ?>
                        <br><input type="checkbox" name="jemaat[]" value="<?php echo $j;?>" <?php echo (!empty($attd_jemaat[$j])) ? "checked" : "";?> > <?php echo $jem['name'];?>
                        <?php } ?>
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