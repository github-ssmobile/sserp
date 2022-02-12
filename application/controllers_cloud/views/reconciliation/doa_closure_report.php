<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
$(document).ready(function(){
    $(document).on("change", "#idvendor, #brand, #datefrom, #dateto", function(event) {
        var idvendor = +$('#idvendor').val();
        var idbrand = +$('#brand').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        if((datefrom == '' && dateto != '') || (datefrom != '' && dateto == '')){
            return false;
        }
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_doa_closure_data",
            method:"POST",
            data:{idvendor:idvendor,idbrand:idbrand,datefrom:datefrom,dateto:dateto},
            success:function(data)
            {
                $(".export").show();
                $("#doa_closure_data").html(data);
            }
        });           
    });
});
</script>
<style>
table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
}
.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}
</style>
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> DOA Closure Report</h3></center></div>
<div class="clearfix"></div><hr>
<div class="col-md-3">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-3">
    <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
        <option value="">Select Brand</option>
        <option value="0">All Brand</option>
        <?php foreach ($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-3">
    <select class="chosen-select form-control input-sm" name="idvendor" id="idvendor" required="" >
        <option value="">Select Vendor</option>
        <option value="0">All Vendor</option>
        <?php foreach ($vendor_data as $vendor) { ?>
            <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-3">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('doa_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="col-md-3 pull-right"><div id="count_1" class="text-info"></div></div>
<div class="clearfix"></div><br>
<input type="hidden" name="entry_by" id="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
<div id="doa_send_to_vendor_form"></div>
<div class="thumbnail" style="overflow: auto;padding: 0">
    <div style="height: 450px;">
        <table id="doa_closure_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px"></table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>