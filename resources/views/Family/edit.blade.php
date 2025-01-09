@extends('layouts.form')

@section('content')

<style>
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

<h3>Update Family</h3>

<form action="{{ route('family.update',$family->id) }}" method="POST" enctype="multipart/form-data">
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
            
            <thead>
                <th colspan="2">Family</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;text-align:right;">Nama Keluarga</td>
                    <td style="width:50%;">
                        <input type="text" id="family_name" name="family_name" maxlength="100" value="{{$family->family_name}}" required>
                    </td>
                </tr>
            </tbody>

            <thead>
                <th colspan="2">Family Member</th>
            </thead>
            <tbody> 
                <?php foreach ($family->people as $people){

                    $pass_photo = ($people['pass_photo'] != "") ? $people['pass_photo'] : "jemaat/file/no-image.jpg";
                    $kelamin = ($people['jenis_kelamin'] == "0") ? "L" : "P";
                    $badge_color = ($people['jenis_kelamin'] == "0") ? "primary" : "danger";
                    $today = new DateTime();
                    $birth = new DateTime($people['birth_date']);
                    $age = $today->diff($birth)->y;                
                ?>
                
                <tr>
                    <td style="width:50%;text-align:right;">
                        {{$people['pivot']['role']}}
                    </td>
                    <td style="width:50%;">
                        <table style="border:0px;">
                            <tr style="height:20px;">
                                <td rowspan="2" style="vertical-align:top; width:45px;">
                                    <img src="{{ asset($pass_photo) }}" width="40px" />
                                </td>
                                <td>
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$kelamin}}</span><span class="badge-value-{{$badge_color}}">{{$people['name']}}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr style="height:20px;">
                                <td style="vertical-align:top;">{{$age." tahun"}}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- <td style="width:50%;">
                        {{$people['name']}}
                    </td>
                    <td style="width:50%;">
                        {{$people['pivot']['role']}}
                    </td> -->
                </tr>

                <?php } ?>
            </tbody>
        </table>
    </span>
</div>

<?php 
// echo '<pre>';
// var_dump($family->people[0]['name']);
// var_dump($family->people[0]['pivot']['role']);
// // var_dump($family->people);
// echo '</pre>';
?>

<div style="text-align:right;">
    <input type="reset" value="Reset" >
    <input type="submit" value="Submit" >
</div>
</form>

@endsection