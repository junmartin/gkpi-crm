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
        padding:3px 3px;
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
    .badge-value-success{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(rgb(111, 220, 81),rgb(96, 201, 108));
    }    
    .badge-value-warning{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(rgb(236, 207, 76),rgb(245, 179, 25));
    }    
    .badge-value-danger{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(rgb(227, 70, 30), #B40273);
    }    
</style>

<?php

    // FILTER

    foreach ($asset_type as $type){
        $chk_type[$type['id']] = (!empty($param['type_'.$type['id']])) ? "checked" : "";
    }

?>

<h3>Asset</h3>
<form name="myForm" id="myForm" method="GET">
<table border="1">
    <th style="text-align:left; width:50%;"><b>Filter</b> <a href="javascript:void(0)" onclick="uncheck_all();">[clear filter]</a></th>
    <th style="text-align:left; width:25%;"><b>Sorting</b></th>
    <tbody>
        <tr>
            <td style="vertical-align:top">
                Tipe:
                <?php foreach ($asset_type as $type) { ?>
                    <input type="checkbox" class="refresh" name="<?php echo "type_".$type['id'];?>" id="<?php echo "type_".$type['id'];?>" value="1" <?php echo $chk_type[$type['id']];?>>
                    <label for="<?php echo "type_".$type['id'];?>"><?php echo $type['name'];?></label>
                <?php }?>
            </td>
        </tr>
    </tbody>
</table>
</form>
<br>
<a href="{{ route('asset.create')}}">[+ Add New]</a>
<table border="1">
    <thead>
        <tr>
            <th>Asset</th>
            <th>Merk</th>
            <th>Model</th>
            <th>Tipe</th>
            <th>Tanggal Perolehan</th>
            <th>Serial No.</th>
            <th>Spec</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        @foreach($assets as $ast)
            <tr>
                <!-- <td style="vertical-align:top">{{$ast->asset_type->name}}</td> -->
                <td style="vertical-align:top">
                    <table style="border:0px;">
                        <tr style="height:20px;">
                            <td style="vertical-align:top;">
                                <a href="{{ route('asset.edit', $ast->id)}}" style="text-decoration:none;">
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$ast->asset_type->name}}</span><span class="badge-value-primary">{{$ast->name}}</span>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align:top;">{{$ast->merk}}</td>
                <td style="vertical-align:top;">{{$ast->model}}</td>
                <td style="vertical-align:top;">{{$ast->tipe}}</td>
                <td style="vertical-align:top">
                    <!-- {{$ast->acquired_date}} -->
                    <?php if(!empty($ast->acquired_date)){ ?>
                        <?php 
                            $acquired = new DateTime($ast->acquired_date);
                            $tdy = new DateTime();
                            $asset_age = $tdy->diff($acquired)->y;
                            if($asset_age <= 1) {
                                $badge = 'success';
                            } elseif ($asset_age <= 3) {
                                $badge = 'warning';
                            } else {
                                $badge = 'danger';
                            }
                        ?>
                        <span class="badge" style="vertical-align:top; margin-top:5px;">
                            <span class="badge-key">{{$ast->acquired_date}}</span><span class="badge-value-<?php echo $badge;?>"><?php echo $asset_age." tahun";?></span>
                        </span>
                    <?php } else { echo ""; }?>
                </td>
                <td style="vertical-align:top">{{$ast->serial_number}}</td>
                <td style="vertical-align:top">{{$ast->spec}}</td>
                <td style="text-align:center;">
                    <a href="{{ route('asset.edit', $ast->id)}}">[ Edit ]</a>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

<script>

    const refresh_trigger = document.getElementsByClassName('refresh');
    
    for (let index = 0; index < refresh_trigger.length; index++) {
        const element = refresh_trigger[index];
        
        refresh_trigger[index].addEventListener('change', (event) => {
            document.forms[0].submit();
        });
    }

</script>


@endsection