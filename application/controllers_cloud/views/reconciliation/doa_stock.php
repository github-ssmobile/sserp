<?php include __DIR__.'../../header.php'; ?>
<script>
var products = [];
$(document).ready(function(){
    $(document).on("change", "#brand", function(event) {
        var brand = +$('#brand').val();
        var idwarehouse = +$('#idwarehouse').val();
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_get_doa_stock",
            method:"POST",
            data:{brand: brand,idwarehouse:idwarehouse},
            success:function(data)
            {
                products = [];
                $(".export").show();
                $("#doa_data").html(data);
                $("#send_to_vendor_form_open").show();
            }
        });           
    });
    $(document).on("click", ".sel_product", function(event) {
        var id = $(this).val();
        if($(this).prop("checked") === true){
            if (products.includes(id) === false){
                products.push(id);
            }else{
                alert('duplicate product selected');
                return false;
            }
        }else if($(this).prop("checked") === false){
            products = jQuery.grep(products, function(value) { return value !== id; });
            if(products.length == 0){
                $("#doa_send_to_vendor_form").html('');
            }
        }
        $('#idservices').val(products);
    });
    $(document).on("click", "#send_to_vendor_form_open", function(event) {
        if(products.length == 0){
            swal('Select Product', 'Select Service Product for - Send to Branch', 'warning');
            return false;
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_open_service_send_to_vendor_form",
                method:"POST",
                success:function(data)
                {
                    $("#doa_send_to_vendor_form").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });         
        }
    });
    $(document).on("click", "#save_doa_send_to_vendor", function(event) {
        if($("#idvendor").val() == ''){ alert('Select Vendor'); return false; }
        if(!confirm('Do you want to submit')){ return false; }
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
  z-index: 999;
}
</style>
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> DOA Stock List</h3></center></div>
<div class="clearfix"></div><hr>
<div class="col-md-2 col-sm-3">
    <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
        <option value="">Select Brand</option>
        <option value="0">All</option>
        <?php foreach ($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
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
<div class="col-md-5"><div id="count_1" class="text-info"></div></div>
<div class="col-md-1">
    <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('doa_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="col-md-2 pull-right">
    <a class="btn btn-primary btn-sm" id="send_to_vendor_form_open" style="margin-top: 6px;line-height: unset;display: none"><span class="mdi mdi-share fa-lg"></span> Send to Vendor</a>
</div><div class="clearfix"></div><br>
<form>
    <input type="hidden" name="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <input type="hidden" name="idwarehouse" id="idwarehouse" value="<?php echo $this->session->userdata('idbranch') ?>"/>
    <div id="doa_send_to_vendor_form"></div>
    <div class="thumbnail" style="overflow: auto;padding: 0">
        <div style="height: 650px;">
            <table id="doa_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px"></table>
        </div>
    </div>
</form>
<?php include __DIR__.'../../footer.php'; ?>