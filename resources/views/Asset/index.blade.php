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
    .badge-value-secondary{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(rgb(90, 190, 233),rgb(90, 191, 249));
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
    .badge-value-inactive{
        border-bottom-right-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.25rem 0.3rem;
        background:#0F80c1;color:#fff;
        background: linear-gradient(rgb(130, 125, 124),rgb(88, 86, 87));
    }    
</style>


<?php 
    $asset_status['new'] = "New"; //success
    $asset_status['use'] = "In Use"; //primary
    $asset_status['oos'] = "Out of Service"; //warning
    $asset_status['sto'] = "In Storage"; //secondary
    $asset_status['dis'] = "Disposed"; //inactive
    $asset_status['los'] = "Lost/Stolen"; //danger
    $asset_status['dmg'] = "Damage/Broken"; //danger

    $asset_color['new'] = 'success';
    $asset_color['use'] = 'primary';
    $asset_color['oos'] = 'warning';
    $asset_color['sto'] = 'secondary';
    $asset_color['dis'] = 'inactive';
    $asset_color['los'] = 'danger';
    $asset_color['dmg'] = 'danger';
?>
<?php
    // FILTER
    foreach ($asset_type as $type){
        $chk_type[$type['id']] = (!empty($param['type_'.$type['id']])) ? "checked" : "";
    }

    foreach ($asset_status as $s => $stat){
        $chk_stat[$s] = (!empty($param['stat_'.$s])) ? "checked" : "";
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
                <br>
                Status:
                <?php foreach ($asset_status as $s => $sts) { ?>
                    <input type="checkbox" class="refresh" name="<?php echo "stat_".$s;?>" id="<?php echo "stat_".$s;?>" value="1" <?php echo $chk_stat[$s];?>>
                    <label for="<?php echo "stat_".$s;?>"><?php echo $sts;?></label>
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
            <th>Owned By</th>
            <th>PIC</th>
            <th>Status/Location</th>
            <!-- <th>Merk</th>
            <th>Model</th>
            <th>Tipe</th>
            <th>Serial No.</th> -->
            <th>Spec</th>
            <th>Tanggal Perolehan</th>
            <th>Maint. #</th>
            <th>Next Maintenance</th>
            <th>Action</th>
        <tr>
    </thead>
    <tbody>
        
        @foreach($assets as $ast)
        <?php //foreach ($assets as $a => $ast) { ?>
        
            <tr>
                
                <td style="vertical-align:top">
                    <table style="border:0px;">
                        <tr style="height:20px;">
                            <td style="vertical-align:top; width:45px;">
                                <?php $asset_photo = ($ast->asset_photo->isNotEmpty()) ? $ast->asset_photo->first()->asset_photo : "storage/jemaat/file/no-image.jpg";?>
                                <img src="{{ asset($asset_photo) }}" width="50px"/>
                                
                            </td>
                            <td style="vertical-align:top;">
                                <a href="{{ route('asset.edit', $ast['id'])}}" style="text-decoration:none;">
                                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                                        <span class="badge-key">{{$ast->asset_type->name}}</span><span class="badge-value-primary">{{$ast['name']}}</span>
                                    </span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align:top;">
                    {{$ast['ownership'] ? $ast['ownership'] : 'GKPI-GP'}}
                </td>
                
                <td style="vertical-align:top;">
                    <?php $show_pic = ($ast['pic']) ? true : false ;?>
                    <?php if($show_pic){?>
                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">PIC</span><span class="badge-value-primary">{{$ast['pic']}}</span>
                    </span> 
                    <?php }?>
                </td>
                <td style="vertical-align:top;">
                    <?php $show_stat = ($ast['status']) ? true : false ;?>
                    <?php if($show_stat){?>
                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">Status</span><span class="badge-value-{{$asset_color[$ast->status]}}">{{$asset_status[$ast->status]}}</span>
                    </span>
                    <?php }?>

                    
                    <?php $show_loc = ($ast['location']) ? true : false ;?>
                    <?php if($show_loc){?>
                    <br>
                    <span class="badge" style="vertical-align:top; margin-top:5px;">
                        <span class="badge-key">Location</span><span class="badge-value-primary">{{$ast['location']}}</span>
                    </span>
                    <?php }?>
                    
                    
                </td>
                <!-- <td style="vertical-align:top;">{{$ast['merk']}}</td>
                <td style="vertical-align:top;">{{$ast['model']}}</td>
                <td style="vertical-align:top;">{{$ast['tipe']}}</td>
                <td style="vertical-align:top">{{$ast['serial_number']}}</td> -->
                <td style="vertical-align:top">                    
                    <?php 
                        echo ($ast['merk']) ? "<small><b>Merk:".$ast['merk']."</b></small><br>" : "";
                        echo ($ast['model']) ? "<small><b>Model:".$ast['model']."</b></small><br>" : "";
                        echo ($ast['tipe']) ? "<small><b>Tipe:".$ast['tipe']."</b></small><br>" : "";
                        echo ($ast['serial_number']) ? "<small><b>S/N:".$ast['serial_number']."</b></small><br>" : "";
                    ?>
                    <?php echo nl2br($ast['spec']);?>
                </td>
                <td style="vertical-align:top">
                    <!-- {{$ast->acquired_date}} -->
                    <?php if(!empty($ast->acquired_date)){ ?>
                        <?php 
                            $acquired = new DateTime($ast['acquired_date']);
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
                            <span class="badge-key">{{$ast['acquired_date']}}</span><span class="badge-value-<?php echo $badge;?>"><?php echo $asset_age." tahun";?></span>
                        </span>
                    <?php } else { echo ""; }?>
                </td>
                
                <!-- <td style="vertical-align:top">{{$ast->maintenance->count()}}</td> -->
                <!-- <td style="vertical-align:top">
                    @if($ast->maintenance->isNotEmpty())
                        {{$ast->maintenance?->first()->maint_date}}
                    @endif
                    
                </td> -->
                <td style="vertical-align:top">
                    @if ($ast->maintenance->count() > 0)
                        <span class="badge" style="vertical-align:top; margin-top:5px;">
                            <span class="badge-key">Maint. count</span><span class="badge-value-primary">{{$ast->maintenance->count()}}</span>
                        </span>
                    @endif
                </td>
                <td style="vertical-align:top">
                    @if ($ast->maintenance->isNotEmpty())
                        <?php $badge = ( $ast->maintenance->first()->maint_date->isFuture()) ? "primary":"danger";?>
                        <span class="badge" style="vertical-align:top; margin-top:5px;">
                            <span class="badge-key"><?php echo date("Y-m-d",strtotime($ast->maintenance->first()->maint_date));?></span><span class="badge-value-<?php echo $badge;?>">{{$ast->maintenance?->first()->maint_date->diffForHumans()}}</span>
                        </span>
                    @endif
                </td>
                <td style="vertical-align:top;text-align:left;">
                    <a href="{{ route('asset.edit', $ast->id)}}">[Edit]</a><br><br>
                    <a href="{{ route('asset.edit_status', $ast->id)}}">[Change Ownership & Status]</a>
                </td>

            </tr>
        <?php //} ?>
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