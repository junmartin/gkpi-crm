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

<h3>Asset Maintenance</h3>

<a href="{{ route('asset_maint.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Service</th>
            <th>Asset</th>
            <!-- <th>Service Type</th> -->
            <!-- <th>Service Desc</th> -->
            <th>Vendor</th>
            <th>Biaya (Rp)</th>
            <!-- <th>Tanggal Service Selanjutnya</th> -->
            <th>Catatan</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($maints as $maint)
            <tr>
                <td style="vertical-align:top">{{$maint->maint_date}}</td>
                <td style="vertical-align:top;">
                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">{{$maint->maint_type}}</span><span class="badge-value-primary">{{$maint->maint_title}}</span>
                    </span>
                </td>
                <td style="vertical-align:top">
                    <b>{{$maint->asset->name}}</b><br>
                </td>
                <!-- <td style="vertical-align:top">{{$maint->maint_type}}</td> -->
                <!-- <td style="vertical-align:top">{{$maint->desc}}</td> -->
                <td style="vertical-align:top">
                    <b>{{$maint->vendor_name}}</b><br>
                    <small>{{$maint->vendor_address}}</small><br>
                    {{$maint->vendor_contact}}
                </td>
                <td style="vertical-align:top; text-align:right;"><?php echo number_format($maint->maint_fee);?></td>
                <!-- <td style="vertical-align:top">{{$maint->next_maint_date}}</td> -->
                <td style="vertical-align:top; font-size:smaller">
                    <b>Masalah</b>: {{$maint->masalah}}<br>
                    <b>Diagnosa</b>: {{$maint->diagnosa}}<br>
                    <b>Tindakan</b>: {{$maint->tindakan}}<br>
                    <b>Hasil</b>: {{$maint->hasil}}<br>
                    <b>Catatan</b>: {{$maint->remark}}<br>
                    <?php if(!empty($maint->next_maint_date)){ ?>
                        <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">Next Service</span><span class="badge-value-danger">{{$maint->next_maint_date}}</span>
                    </span>
                    <?php } ?>
                </td>
                <td style="text-align:center;">
                    <a href="{{ route('asset_maint.edit', $maint->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>


@endsection