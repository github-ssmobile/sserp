<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
    z-index: 9;
}
.bg-green{
    background-color: #d4e4ff;
}
.fixedelement_bottom{
    position: -webkit-sticky;
    position: sticky;
    bottom: 0;
    background-color: #c5f4dd;
    font-size: 14px;
    z-index: 9;
}
</style>
<script>
    $(document).ready(function(){
        $('#filter_btn').click(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var idvariant = $('#idvariant').val();
            if(datefrom == '' || dateto == ''){
                swal('Select Date', 'You must select date range', 'warning');
                return false;
            }else{
                $.ajax({
                    url:"<?php echo base_url() ?>Outward/ajax_transfer_balance_report",
                    method:"POST",
                    data:{datefrom : datefrom, dateto : dateto, idbranch: idbranch,branches: branches,idvariant:idvariant},
                    success:function(data)
                    {
                        $(".transfer_balance").html(data);
                    }
                });
            }
        });
    });
</script>
<div class="col-md-2 col-sm-0 col-xs-0"></div>
<div class="col-md-6 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Daybook Report</h3></center></div>
<div class="col-md-2 col-sm-2 col-xs-2"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('balance_transfer<?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div><div class="clearfix"></div><br>
<div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
 <!--level 1 - admin, 2 - idbranch, 3 - user_has_branch--> 
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Branch</div>
<div class="col-md-3">
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php $branches[] = $branch->id_branch; } ?>
    </select>
</div>
<input type="hidden" name="branches" id="branches" value="<?php echo implode(',',$branches) ?>">
<?php } ?>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Product</div>
<div class="col-md-3">
    <select class="form-control input-sm" name="idvariant" id="idvariant">
        <!--<option value="">Select Product</option>-->
        <?php foreach ($model_variant as $variant) { ?>
            <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-1">
    <button class="btn btn-primary btn-sm" id="filter_btn"><i class="fa fa-filter"></i> Filter</button>
</div>
<div class="clearfix"></div>
<div class="thumbnail" style="height: 550px; font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <table id="balance_transfer<?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-bordered table-hover transfer_balance" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>