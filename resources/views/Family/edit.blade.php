@extends('layouts.form')

@section('content')

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
                <th colspan="2">Personal Info</th>
            </thead>
            <tbody>                
                <tr>
                    <td style="width:50%;">Nama Keluarga</td>
                    <td style="width:50%;">
                        <input type="text" id="family_name" name="family_name" maxlength="100" value="{{$family->family_name}}" required>
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