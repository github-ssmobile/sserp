<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
$(document).ready(function(){
    $(document).on("change", "#idvendor, #brand", function(event) {
        var idvendor = +$('#idvendor').val();
        var idbrand = +$('#brand').val();
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_pending_doa_inward",
            method:"POST",
            data:{idvendor:idvendor,idbrand:idbrand},
            success:function(data)
            {
                $(".export").show();
                $("#doa_pending_inward_data").html(data);
            }
        });           
    });
    $(document).on("click", ".inward_new_handset", function(event) {
        var parentDiv = $(this).closest('td');
        var iddoainward = $(this).val();
        var idservice = $(parentDiv).find('.idservice').val();
        var imei_no = $(parentDiv).find('.imei_no').val();
        var idvariant = $(parentDiv).find('.idvariant').val();
        var idbrand = $(parentDiv).find('.idbrand').val();
        var idwarehouse = $(parentDiv).find('.idwarehouse').val();
        var idmodel = $(parentDiv).find('.idmodel').val();
        var idproductcategory = $(parentDiv).find('.idproductcategory').val();
        var idcategory = $(parentDiv).find('.idcategory').val();
        var idsku_type = $(parentDiv).find('.idsku_type').val();
        var product_name = $(parentDiv).find('.product_name').val();
        var idvendor = $(parentDiv).find('.idvendor').val();
        var entry_by = $('#entry_by').val();
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/save_inward_new_handset",
            method:"POST",
            dataType: 'json',
            data:{iddoainward:iddoainward,idservice:idservice,imei_no:imei_no,idvariant:idvariant,idbrand:idbrand,idsku_type:idsku_type,idvendor:idvendor,
                idwarehouse:idwarehouse,entry_by:entry_by,idmodel:idmodel,idproductcategory:idproductcategory,idcategory:idcategory,product_name:product_name},
            success:function(data)
            {
                if(data.result == 'Success'){
                    $(parentDiv).parent('tr').remove();
                    swal('Received Successfully!', 'Handset Inwarded in stock!!', 'success');
                }else{
                    alert('Failed to inward..Try again!!');
                }
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
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> Pending DOA Inward</h3></center></div>
<div class="clearfix"></div><hr>
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
<div class="col-md-4">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-2 pull-right">
    <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('doa_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div>
<div class="col-md-4 pull-right"><div id="count_1" class="text-info"></div></div>
<div class="clearfix"></div><br>
<input type="hidden" name="entry_by" id="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
<div class="thumbnail" style="overflow: auto;padding: 0">
    <div style="height: 450px;">
        <table id="doa_pending_inward_data" class="table table-condensed table-bordered table-hover" style="font-size: 13px"></table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>