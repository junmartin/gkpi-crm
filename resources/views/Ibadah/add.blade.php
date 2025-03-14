@extends('layouts.form')

@section('content')

<h3>Create Ibadah</h3>

<form action="{{ route('ibadah.store') }}" method="POST" enctype="multipart/form-data">
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
                <th colspan="2">Info Ibadah</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;">Nama Ibadah</td>
                    <td style="width:50%;">
                        <input type="text" id="ibadah_name" name="ibadah_name" maxlength="100" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Catatan</td>
                    <td style="width:50%;">
                        <input type="text" id="remark" name="remark" maxlength="100" required>
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