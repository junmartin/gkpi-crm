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

<h3>Kehadiran</h3>

<?php 
$arr_ibadah = [];
foreach ($ibadah as $key => $value) {
    $arr_ibadah[$value['id']] = $value;
}

?>

<a href="{{ route('sermon.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Ibadah</th>
            <th>Jumlah Jemaat</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($sermon as $ser)
            <tr>
                <td style="vertical-align:top">
                    {{$ser->sermon_date}}
                </td>
                <td style="vertical-align:top">
                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">{{$arr_ibadah[$ser->ibadah_id]['ibadah_name']}}</span><span class="badge-value-primary">{{$ser->ibadah_name}}</span>
                    </span>    
                

                </td>
                <!-- <td style="vertical-align:top">attd->total_attendees</td> -->
                <td style="vertical-align:top">{{$ser->attendee_count}}</td>
                
                <td style="text-align:center;">                    
                    <a href="{{route('sermon.edit',$ser->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection