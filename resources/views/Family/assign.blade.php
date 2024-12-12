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
<h3>Assign Jemaat to Family</h3>

<?php
// var_dump($family);
$kelamin = ($jemaat->jenis_kelamin == "0") ? "Laki-laki" : "Perempuan";
$badge_color = ($jemaat->jenis_kelamin == "0") ? "primary" : "danger";

$birthDate = new DateTime($jemaat->birth_date);
$today = new DateTime(); // Gets the current date
$age = $today->diff($birthDate)->y; // Calculate the difference in years

$pass_photo = ($jemaat->pass_photo != "") ? $jemaat->pass_photo : "jemaat/file/no-image.jpg";

?>
<form action="{{route('assign_family.submit', ['jemaat_id' => $jemaat->id])}}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- @method('PUT') -->

    <input type="hidden" name="jemaat_id" value="{{$jemaat->id}}">

<div style="width:100%; display:table;">
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            <thead>
                <th colspan="2">Personal Info</th>
            </thead>
            <tbody>                
                <tr>
                    <td>
                        <table style="border:0px;">
                            <tr style="height:20px;">
                                <td rowspan="2" style="vertical-align:top; width:45px;">
                                    <img src="{{ asset($pass_photo) }}" width="40px" />
                                </td>
                                <td>
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$kelamin}}</span><span class="badge-value-{{$badge_color}}">{{$jemaat->name}}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr style="height:20px;">
                                <td style="vertical-align:top;">{{$age." tahun"}}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align:top;">
                        Sebagai: <select name="role" id="role">
                            <option> Suami </option>
                            <option> Istri </option>
                            <option> Anak </option>
                        </select>
                        <br><br>
                        <div style="text-align:center;">
                            <input type="submit" value="Save" >
                        </div>
                    </td>
                </tr>
                
            </tbody>
            

        </table>
    </span>
    <span style="float:left; width:33%;">
        <table border="1" width="100%">
            <thead>
                <th colspan="2">Families</th>
            </thead>
            <tbody>                
                @foreach($family as $fam)
                <tr>
                    <td>
                        <input type="radio" name="families" id="{{$fam->id}}" value="{{$fam->id}}"> <label for="{{$fam->id}}">{{$fam->family_name}}</label>
                    </td>
                </tr>
                @endforeach
                
            </tbody>
            
        </table>
    </span>
</div>

</form>

@endsection
