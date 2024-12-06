@extends('layouts.form')

@section('content')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
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
</style>

<?php 
    // Initialize defaults
    $chk_appraisal = $chk_archived = $chk_listed = $chk_sold = "";
    $chk_to_buy = $chk_to_rent = "";
    $chk_type = [];
    $chk_city = [];

    // Check if there are parameters in the request
    // $chk_appraisal = (!empty($param['status_appraisal'])) ? 'checked' : '';
    // $chk_archived = (!empty($param['status_archived'])) ? 'checked' : '';
    // $chk_listed = (!empty($param['status_listed'])) ? 'checked' : '';
    // $chk_sold = (!empty($param['status_sold'])) ? 'checked' : '';
    
    // $chk_for_sell = (!empty($param['for_sell'])) ? 'checked' : '';
    // $chk_for_rent = (!empty($param['for_rent'])) ? 'checked' : '';
    
    // foreach ($proptypes as $proptype) {
    //     // $chk_type[$proptype['name']] = in_array($proptype['name'], $paramType) ? "checked" : "";
    //     $chk_type[$proptype['name']] = (!empty($param['type_'.$proptype['name']])) ? "checked" : "";
    // }
    
    // foreach ($distinct_city as $city) {
    //     $chk_city[str_replace(' ', '_', $city)] = (!empty($param['city_'.str_replace(' ', '_', $city)])) ? "checked" : "";
    // }
    
        
?>



<h3>Listing</h3>
<form name="myForm" id="myForm" method="GET">
<table border="1">
    <th style="text-align:left; width:50%;"><b>Filter</b> <a href="javascript:void(0)" onclick="uncheck_all();">[clear filter]</a></th>
    <th style="text-align:left; width:25%;"><b>Sorting</b></th>
    <th style="text-align:left; width:25%;"><b>Column</b></th>
    <tbody>
<?php
// Get the status, for, type, and city parameters from the URL
$statusParams = explode('|', $_GET['status'] ?? '');
$forParams = explode('|', $_GET['for'] ?? '');
$typeParams = explode('|', $_GET['type'] ?? '');
$cityParams = explode('|', $_GET['city'] ?? '');
?>

        <tr>
            <td style="vertical-align: top;">
                Status:
                <input type="checkbox" class="refresh" name="status_appraisal" value="1" id="appraisal" <?php //echo $chk_appraisal;?>>
                <label for="appraisal">Appraisal</label>

                <input type="checkbox" class="refresh" name="status_archived" value="1" id="archived" <?php //echo $chk_archived;?>>
                <label for="archived">Archived</label>

                <input type="checkbox" class="refresh" name="status_listed" value="1" id="listed" <?php //echo $chk_listed;?>>
                <label for="listed">Listed</label>

                <input type="checkbox" class="refresh" name="status_sold" value="1" id="sold" <?php //echo $chk_sold;?>>
                <label for="sold">Sold</label>
                <br>

                For:
                <input type="checkbox" class="refresh" name="for_sell" value="1" id="to_buy" <?php //echo $chk_for_sell;?>>
                <label for="to_buy">Sell</label>

                <input type="checkbox" class="refresh" name="for_rent" value="1" id="to_rent" <?php //echo $chk_for_rent;?>>
                <label for="to_rent">Rent</label>
                <br>

            </td>
            <td style="vertical-align: top;">
                Sorting
            </td>
            <td style="vertical-align: top;">
                <!-- <input type="checkbox" class="refresh" name="show_add" value="1" id="show_add" <?php //echo (in_array('show_add',$column_show)) ? 'checked' : '';?>>
                <label for="show_add">Address</label><br> -->

                <!-- <input type="checkbox" class="refresh" name="show_spec" value="1" id="show_spec" <?php //echo (in_array('show_spec',$column_show)) ? 'checked' : '';?>>
                <label for="show_spec">Specification</label><br> -->

                <input type="checkbox" class="refresh" name="show_owner" value="1" id="show_owner" <?php //echo (in_array('show_owner',$column_show)) ? 'checked' : '';?>>
                <label for="show_owner">Owner</label>
            </td>
        </tr>
    </tbody>
</table>

</form>

<a href="{{ route('jemaat.create')}}">[+ Add New]</a>
<table border="1">
    <colgroup>
        <!-- Property & Location -->
        <col width="8%" style="visibility:visible"> 

        <!-- For -->
        <col width="5%" style="visibility:visible">

        <!-- Address -->
        <col width="10%" style="visibility:visible">

        <!-- Detail -->
        <col width="10%" style="visibility:visible">

        <!-- Status -->
        <col width="5%" style="visibility:visible">

        <!-- Price -->
        <col width="5%" style="visibility:visible">
        
        <!-- <col width="7%" style="visibility:visible"> -->
        
        <col width="27%" id="col_spec" style="visibility:visible">
    
        <col width="5%" id="col_vendor" style="visibility:visible">
        
        <col width="4%" style="visibility:visible">
        <col width="4%" style="visibility:visible">
    </colgroup>
    <thead>
        <tr>
            <!-- <th>Listing Date</th> -->
            <th>Property & Location</th>            
            <th>For</th>
            <th>Address</th>
            <th>Details</th>
            <?php //if(in_array('show_add',$column_show)){?>
                <th>Address</th>
            <?php //} ?>
            <th>Status</th>
            
            <th>Price</th>
            <!-- <th>Space</th> -->
            <?php //if(in_array('show_spec',$column_show)){?>
                <th>Specification</th>
            <?php //} ?>
            <?php //if(in_array('show_owner',$column_show)){?>
                <th>Owner</th>
            <?php //} ?>
            <th>Progress</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <!-- Example Data Row -->

        @foreach($jemaats as $listing)

            <tr style="vertical-align: top;" >
                <!-- <td>{{$listing->listing_date}}</td> -->
                <td>             
                    <span class="top-left">
                    <a href="{{ route('listing.edit', $listing->id)}}">
                    <span class="badge">
                        <span class="badge-key">{{ucfirst($listing->propertyType->name)}}</span><span class="badge-value">{{$listing->city}}</span>
                    </span>
                    </a>
                    </span><br>
                    <span class="top-left" style="color:gray; float-right; vertical-align:bottom;"><em><small>Since:{{$listing->listing_date}}</small></em></span>
                </td>
                
                <td style="text-align:center;">
                    <img src="https://img.shields.io/badge/Perempuan-pink">                    
                </td>
                <td style="font-size:small; line-height:1;">
                    {{$listing->area}},&nbsp;&#128205;<em><a href="#">[Location]</a></em>
                    <br>{{$listing->address}}
                    <br><b>{{$listing->subdistrict}}, {{$listing->district}}</b>
                    
                </td>
                <td style="font-size:small; line-height:1; justify-content: space-between;">
                    <img src="https://img.shields.io/badge/Key-On Hand-olive">
                    <img src="https://img.shields.io/badge/Banner-Put On-green">    
                    <br>
                    <div style="flex:1;">
                    <?php if(!empty($listing_attr[$listing->id])){ ?>
                        <?php 
                            foreach($listing_attr[$listing->id] as $list_attr){                                    
                                echo $prop_attr[$list_attr['proptype_attr_id']]['key'];
                                echo ": ";
                                if($prop_attr[$list_attr['proptype_attr_id']]['input_type'] == 'boolean'){
                                    if($list_attr['value'] == 1){
                                        $value = "Yes";
                                    }elseif($list_attr['value'] == 0){
                                        $value = "No";
                                    }
                                }else{
                                    $value = $list_attr['value'];
                                }
                                echo "<b>".$value."</b>";
                                echo "<br>";
                            }
                        ?>
                    <?php } ?>
                    </div>
                    
                </td>
                <?php //if(in_array('show_add',$column_show)){?>
                    <td>{{$listing->address}}<br><b>{{$listing->subdistrict}}, {{$listing->district}}</b></td>
                <?php //} ?>

                <!-- Status -->
                <?php 
                    $status = $listing->listing_status;
                    $badge_color = $color[$listing->listing_status];
                ?>
                <td style="text-align:left;">
                    <!-- <img src="https://img.shields.io/badge/Status-{{ucfirst($listing->listing_status)}}-{{$color[$listing->listing_status]}}"> -->
                </td>
                
                <td style="text-align:right;   padding-right: 10px;">
                    <!-- <img src="https://img.shields.io/badge/Price-{{$price}}-blue" alt="Price"> -->
                    <!-- <br><img src="https://img.shields.io/badge/Highest Bid-Rp2.5M-orange"> -->

                </td>
                <!-- <td style="text-align:right;">{{$listing->land_space}}</td> -->

                <?php //if(in_array('show_spec',$column_show)){?>                    
                    <td style="font-size:small; line-height:1;">{!!nl2br(str_replace(" ", " &nbsp;", $listing->specification))!!}</td>
                <?php //} ?>
                <?php //if(in_array('show_owner',$column_show)){?>
                    <td>John Doe</td>
                <?php //} ?>
                <td style="font-size:small; line-height:1;">
                    <a href="#"><img src="https://img.shields.io/badge/Leads-12-blue" alt="Leads"></a>
                    <br>
                    <a href="#"><img src="https://img.shields.io/badge/Inquiries-10-green" alt="Inquiries"></a>
                    <!-- <br>
                    <img src="https://img.shields.io/badge/Days%20Listed-45-yellow" alt="Days Listed"> -->
                    

                </td>
                <td style="text-align:center;">
                    <a href="{{ route('listing.edit', $listing->id)}}">View</a>
                    <br><br><a href="{{ route('listing.edit', $listing->id)}}">Edit</a>
                    <br><br><a href="{{ route('listing.edit', $listing->id)}}">Share</a>
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
    
    function uncheck_all() {
        var checkboxes = document.getElementsByTagName('input');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
        }
        document.forms[0].submit();
    }
</script>


@endsection