@extends('layouts.form')

@section('content')

<h3>Record Attendance</h3>

<form action="{{ route('attendance.store') }}" method="POST" enctype="multipart/form-data">
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
            <thead>
                <th colspan="2">Sermon Info</th>
            </thead>
            <tbody> 
                <tr>
                    <td style="width:50%;">Date</td>
                    <td style="width:50%;">
                        <input type="date" id="sermon_date" name="sermon_date" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Tipe Ibadah</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_id" name="ibadah_id" value="1">
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Kebaktian</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_name" name="ibadah_name" maxlength="100" required>
                    </td>
                </tr>
            </tbody>
            <thead>
                <th colspan="2">Kehadiran</th>
            </thead>
            <tbody> 
                <tr>
                    <td colspan="2">
                        <br><input type="checkbox" name="jemaat[]" value="1"> Jun
                        <br><input type="checkbox" name="jemaat[]" value="2"> Anne
                        <br><input type="checkbox" name="jemaat[]" value="3"> Jo
                        <br><input type="checkbox" name="jemaat[]" value="4"> Jill
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