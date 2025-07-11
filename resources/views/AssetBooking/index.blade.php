@extends('layouts.form')

@section('content')

<style>
    table {
        /* width: 100%; */
        border-collapse: collapse;
        /* margin-bottom: 20px; */
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

<h3>Asset Booking</h3>

<a href="{{ route('assetbooking.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Booking Date</th>
            <th>Jemaat</th>
            <th>Asset</th>
            <th>Status</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($assetBookings as $booking)
            <tr>
                <td style="vertical-align:top">{{$booking->booking_date}}</td>
                <td style="vertical-align:top;">
                    <b>{{$booking->jemaat->name}}</b><br>
                </td>
                <td style="vertical-align:top">
                    <b>{{$booking->asset->name}}</b><br>
                </td>
                <td style="vertical-align:top">{{$booking->status}}</td>
                <td style="text-align:center;">
                    <a href="{{ route('assetbooking.edit', $booking->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection