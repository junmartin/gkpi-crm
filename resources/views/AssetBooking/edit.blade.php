@extends('layouts.form')

@section('content')

<h3>Update Asset Booking</h3>

<form action="{{ route('assetbooking.update', $assetBooking->id) }}" method="POST">
    @csrf
    @method('PUT')

    @if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }} error occured</li>
            @endforeach
        </ul>
    </div>
    @endif

<div style="width:100%; display:table;">
    <span style="float:left; width:33%">
        <table border="1" width="100%">
            
            <thead>
                <th colspan="2">Asset Booking Details</th>
            </thead>
            <tbody>
                <tr>
                    <td style="width:50%;">Jemaat</td>
                    <td style="width:50%;">
                        <select id="jemaat_id" name="jemaat_id" required>
                            @foreach($jemaats as $jemaat)
                            <option value="{{$jemaat->id}}" {{ $assetBooking->jemaat_id == $jemaat->id ? 'selected' : '' }}>{{$jemaat->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Asset</td>
                    <td style="width:50%;">
                        <select id="asset_id" name="asset_id" required>
                            @foreach($assets as $asset)
                            <option value="{{$asset->id}}" {{ $assetBooking->asset_id == $asset->id ? 'selected' : '' }}>{{$asset->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Booking Date</td>
                    <td style="width:50%;">
                        <input type="date" id="booking_date" name="booking_date" value="{{$assetBooking->booking_date}}" required>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Status</td>
                    <td style="width:50%;">
                        <select id="status" name="status" required>
                            <option value="pending" {{ $assetBooking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $assetBooking->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $assetBooking->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="completed" {{ $assetBooking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </span>
</div>

<div style="text-align:right;">
    <input type="submit" value="Submit" >
</div>
</form>

@endsection