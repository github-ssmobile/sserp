<?php include __DIR__ . '../../header.php'; ?>
<style>
    .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #4285f4;
        font-size: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        z-index: 9;
        color: white;
    }
</style>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span>&nbsp;CRM Report</h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
 

<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
<form name="myForm" enctype="multipart/form-data" id="myForm"  method="post" >
<div id="crm_report" style="padding: 20px 10px; margin: 0">
<div class="row" style="margin-right: 0px;margin-left: 0px;padding-bottom: 15px;">
<div class="col-md-1 col-sm-4 col-xs-6" style="padding: 2px;">Day Filter :</div>
<div class="col-md-2">
    <select data-placeholder="Select Day Filter" name="id_day_filter" id="id_day_filter" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Day</option>
<!--        <option value="1">1 Days</option>
        <option value="2">2 Days</option>-->
        <option value="30">Last 30 Days</option>
        <option value="60">Last 60 Days</option>
        <option value="90">Last 90 Days</option>
        <option value="180">Last 180 Days</option>
        <option value="180">Last 365 Days</option>
    </select>
</div>

<div class="col-md-1 col-sm-4 col-xs-6" style="padding: 2px;margin-left:15px"><span style="color:red">OR&nbsp;&nbsp;</span> Date Filter :</div>
<div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px;margin-right: 12px;">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="date_filter1" id="date_filter1" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="date_filter" id="date_filter" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>

<div class="col-md-1 col-sm-4 col-xs-6" style="padding: 2px;margin-right: 12px;"><span style="color:red">OR&nbsp;&nbsp;</span>Birth Day :</div>
<div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="datefrom" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="dateto" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
 <!--level 1 - admin, 2 - idbranch, 3 - user_has_branch-->
</div>   
<div class="row" style="margin-right: 0px;margin-left: 0px;padding-bottom: 15px;">
   
<?php if($this->session->userdata('level') == 2){ ?>     
    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Branch :</div>
<div class="col-md-2">
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Branch</option>
        <option value="All">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php $branches[] = $branch->id_branch; } ?>
    </select>
</div>
<input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
<?php } ?>

<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px"><span style="color:red;padding-right: 13px;padding-left: 15px;">OR</span>Zone :</div>
<div class="col-md-2">
    <select data-placeholder="Select Zones" name="idzone" id="idzone" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Zone</option>
        <option value="All">All Zone</option>
        <?php foreach ($zone_data as $zone){ ?>
        <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
        <?php $zones[] = $zone->id_zone; } ?>
    </select>
</div>
<input type="hidden" name="zones" id="zones" value="<?php echo implode($zones,',') ?>">

<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Brand :</div>
<div class="col-md-2">
    <select data-placeholder="Select Brands" name="idbrand" id="idbrand" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Brand</option>
        <option value="All">All Brands</option>
        <?php foreach ($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
        <?php $brandes[] = $brand->id_brand; } ?>
    </select>
</div>
<input type="hidden" name="brands" id="brands" value="<?php echo implode($brandes,',') ?>"> 

<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Category :</div>
<div class="col-md-2">
    <select data-placeholder="Select Categorys" name="id_category" id="id_category" class="chosen-select" required="" style="width: 100%">
<!--        <option value="-1">Select Category</option>-->
        <option value="All">All Category</option>
        <?php foreach ($product_category as $data){ ?>
        <option value="<?php echo $data->id_product_category; ?>"><?php echo $data->product_category_name; ?></option>
        <?php $category_datas[] = $data->id_product_category; } ?>
    </select>
</div>
<input type="hidden" name="categorys" id="categorys" value="<?php echo implode($category_datas,',') ?>">
    
</div>    
<div class="row" style="margin-right: 0px;margin-left: 0px;padding-bottom: 15px;">

<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Price Category :</div>
<div class="col-md-2">
    <select data-placeholder="Select Price Category" name="id_price_category" id="id_price_category" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Price Category</option>
        <option value="All">All Price Category</option>
        <?php foreach ($price_category_data as $data){ ?>
        <option value="<?php echo $data->id_price_category_lab; ?>"><?php echo $data->lab_name; ?></option>
        <?php $price_category[] = $data->id_price_category_lab; } ?>
    </select>
</div>
<input type="hidden" name="price_categorys" id="price_categorys" value="<?php echo implode($price_category,',') ?>">



<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Pincode :</div>
<div class="col-md-2">
<input type="text" name="pincode" id="pincode" class="form-control input-sm"  placeholder="Enter Pincode">
</div>

<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">City :</div>
<div class="col-md-2">
<input type="text" name="city" id="city" class="form-control input-sm"  placeholder="Enter City">
</div>
 
</div>

<div class="row" style="margin-right: 0px;margin-left: 0px;padding-bottom: 15px;">

    <div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Payment Head :</div>
<div class="col-md-2">
    <select data-placeholder="Select Payment Heads" name="idpayment_head" id="idpayment_head" onchange="getPaymetmode(this.value)" class="chosen-select" required="" style="width: 100%">
        <option value="-1">Select Payment Head</option>
        <option value="All">All Payment head</option>
        <?php foreach ($payment_head as $data){ ?>
        <option value="<?php echo $data->id_paymenthead; ?>"><?php echo $data->payment_head; ?></option>
        <?php $payment_heads[] = $data->id_paymenthead; } ?>
    </select>
</div>
<input type="hidden" name="payment_heads" id="payment_heads" value="<?php echo implode($payment_heads,"','") ?>">       
        
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Payment Mode :</div>
<div class="col-md-2">
    <select data-placeholder="Select Payment Modes" name="idpayment_mode" id="idpayment_mode" class="chosen-select" required="" style="width: 100%">
    </select>
</div>
<input type="hidden" name="payment_modes" id="payment_modes" value="">     
<!--<input type="hidden" name="payment_modes" id="payment_modes" value="<?php echo implode($payment_modes,"','") ?>">     -->
</div>
    
<div class="row" style="margin-right: 0px;margin-left: 0px;">
    <div class="col-md-1"></div>    
    <div class="col-md-2">
        <button type="button" class="btn btn-primary btn-sm"  id="filter_btn"><i class="fa fa-filter"></i> Filter</button>
    </div>
</div>    
</div>
</form>    
    <hr style="border-top: 1px solid #ddd;">
<div class="col-md-5">
    <div class="input-group" id="search_div" style="display: none;" >
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-4">
    <div id="count_1" class="text-info"></div>
</div>    
<div class="col-md-2 col-sm-2 col-xs-2 excel_div" style="float: right;display: none;" ><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('crm_table');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div><div class="clearfix"></div><br>
 <div id="crmreport_div" style="overflow: auto;height: 550px;" ></div>   
</div>

<script>
$(document).ready(function(){
    
$('#filter_btn').click(function(){

        var idpayment_head1 = $('#idpayment_head').val();
        var idpayment_mode1 = $('#idpayment_mode').val();


        if(idpayment_head1 != -1){
            if(idpayment_mode1 == -1){
            alert("Please Select Payment Mode");
            return false;
            }
         }

        var allData = jQuery("#myForm").serialize();
       
        $(".excel_div").show();
        $("#search_div").show();
        $.ajax({
            url:"<?php echo base_url() ?>Customer_loyalty/ajax_get_crm_report_data",
            method:"POST",
            cache: false,
            dataType : 'json',
            data : allData,
            success: function (result) {

            document.getElementById('crmreport_div').innerHTML=result;
//            $("#crm_table").DataTable({
//                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
//                        $('td', nRow).css('background-color', 'white');
//                },
//                "lengthMenu": [[100, 250, 500, 1000, -1], [100, 250, 500, 1000, "All"]]
//            });
        },error: function (msg){
            alert('Please try after sometime!!!');
            }
        });
        
    });
    
    
    $('#id_day_filter').change(function(){
      $("#datefrom").prop("readonly", true); 
      $("#dateto").prop("readonly", true);
      $('#datefrom').removeAttr('data-provide');
      $('#dateto').removeAttr('data-provide');
      
      $("#date_filter1").prop("readonly", true); 
      $("#date_filter").prop("readonly", true);
      $('#date_filter1').removeAttr('data-provide');
      $('#date_filter').removeAttr('data-provide');
    });
    
    $('#date_filter1,#date_filter').change(function(){
      
      $("#id_day_filter").prop('disabled', true).trigger("chosen:updated");
      
      $("#datefrom").prop("readonly", true); 
      $("#dateto").prop("readonly", true);
      $('#datefrom').removeAttr('data-provide');
      $('#dateto').removeAttr('data-provide');
    });
    
    $('#datefrom,#dateto').change(function(){
      
      $("#id_day_filter").prop('disabled', true).trigger("chosen:updated");
      
      $("#date_filter1").prop("readonly", true); 
      $("#date_filter").prop("readonly", true);
      $('#date_filter1').removeAttr('data-provide');
      $('#date_filter').removeAttr('data-provide');
    });
    
    
    $('#idbranch').change(function(){
      $("#idzone").prop('disabled', true).trigger("chosen:updated");
    });
    
    $('#idzone').change(function(){
      $("#idbranch").prop('disabled', true).trigger("chosen:updated");
    });
    
 });
 
 function getPaymetmode(id_payhead){
 
    $.ajax({
            type:'POST',
            url:"<?php echo base_url() ?>Customer_loyalty/ajax_get_paymentmode_idhead",
            cache: false,
            dataType: "text",
            data:{id_payhead:id_payhead},
            success:function(msg){
                var obj=JSON.parse(msg);
                //alert(obj['payment_modes']);
                $('#idpayment_mode').html('');
                $("#idpayment_mode").append(obj['payment_modes']).trigger("chosen:updated");
                $("#payment_modes").val(obj['array_modes']);
            },
            error:function(){

            }
        });
 
 }
 
</script>
<?php include __DIR__ . '../../footer.php'; ?>