<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
$(document).ready(function(){
    $(document).on("change", "#idvendor, #brand", function(event) {
        var idvendor = +$('#idvendor').val();
        var idbrand = +$('#brand').val();
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_get_doa_stock_for_recon",
            method:"POST",
            data:{idvendor:idvendor,idbrand:idbrand},
            success:function(data)
            {
                $(".export").show();
                $("#doa_data").html(data);
            }
        });           
    });
    
    $(document).on("click", ".btn_handset", function(event) {            
        var ce = $(this);
        var parentDiv=$(ce).closest('div').parent('.action_form');
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_get_receive_block",
            method:"POST",
            data:{confirm_type:1},
            success:function(data)
            {
                $(parentDiv).find('.new_handset_block').html(data);
                $(".chosen-select").chosen({search_contains: true});
                $(ce).closest('div').hide();
            }
        });      
    });
    
    $(document).on("click", "#save_receive_handset", function(event) {
        var parentDiv = $(this).closest('div').parent('.new_handset_block');
        var imei=$(parentDiv).find('.new_imei').val();
        var verified_imei=$(parentDiv).find('.verified_imei');
        var variant=$(parentDiv).find('.idmodelvariant').val();
        var new_brand=$(parentDiv).find('.new_brand').val();
        var tr_row = $(parentDiv).parent('.action_form').parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr');
        
        if(imei && variant){
            if(verified_imei.val() != imei){
                $.ajax({
                    url: "<?php echo base_url() ?>Service/ajax_verify_imei_presence",
                    method: "POST",
                    data:{imei : imei},
                    dataType:'json',
                    success: function (data)
                    {
                        if(data.data === 'fail'){
                            swal("Product already present in ERP!", "Re-verify IMEI and try again", "warning");
                            verified_imei.val('');
                            return false;
                        }else if(data.data === 'success'){
                            verified_imei.val(imei);
                            swal("IMEI/SRNO Verified", 'Click again on submit button', "success");
                        }
                    }
                });
            }
            var id_doa_stock = $(parentDiv).parent('.action_form').find('.id_doa_stock').val();
            $.ajax({
                url: "<?php echo base_url() ?>Reconciliation/check_if_entry_receive",
                method: "POST",
                dataType: 'json',
                data:{id_doa_stock:id_doa_stock},
                success: function (data)
                {
                    if(data.result === 'No'){
                        if(confirm('Do you want to submit')){
                            var doa_idvariant = $(parentDiv).parent('.action_form').find('.doa_idvariant').val();
                            var doa_imei = $(parentDiv).parent('.action_form').find('.doa_imei').val();
                            var idservice = $(parentDiv).parent('.action_form').find('.idservice').val();
                            var entry_by = $('#entry_by').val();
                            var idwarehouse = $('#idwarehouse').val();
                            $.ajax({
                                url: "<?php echo base_url() ?>Reconciliation/submit_receive_handset_against_letter",
                                method: "POST",
                                dataType: 'json',
                                data:{doa_idvariant:doa_idvariant,doa_imei:doa_imei,id_doa_stock:id_doa_stock,new_imei:imei,new_variant:variant,entry_by:entry_by,idwarehouse:idwarehouse,idservice:idservice,new_brand:new_brand},
                                success: function (data)
                                {
                                    if(data.result === 'Success'){
                                        swal("New Replacement Product Inwarded", "Inwarded successfully...", "success");
                                        $(".modal-backdrop.in").hide();
                                        tr_row.remove();
                                    }else{
                                        swal("Failed to inward product!", "Retry again", "warning");
                                        return false;
                                    }
                                }
                            });
                        }
                    }else{
                        swal("Entry already received!", "Refresh and check again", "warning");
                        return false;
                    }
                }
            });
        }else{
            swal("All fields are required!", "Select Model and Enter IMEI/SRNO", "warning");
            return false;
        }
    });
    $(document).on("click", "#save_receive_cn", function(event) {
        var parentDiv = $(this).closest('div').parent('.action_form');
        var tr_row = $(parentDiv).parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr');
        var id_doa_stock = $(parentDiv).find('.id_doa_stock').val();
        var cn_no = $(parentDiv).find('.cn_no').val();
        var cn_amount = $(parentDiv).find('.cn_amount').val();
        if(cn_no && cn_amount){
            $.ajax({
                url: "<?php echo base_url() ?>Reconciliation/check_if_entry_receive",
                method: "POST",
                dataType: 'json',
                data:{id_doa_stock:id_doa_stock},
                success: function (data)
                {
                    if(data.result === 'No'){
                        if(confirm('Do you want to submit')){
                            var doa_idvariant = $(parentDiv).find('.doa_idvariant').val();
                            var doa_imei = $(parentDiv).find('.doa_imei').val();
                            var idservice = $(parentDiv).find('.idservice').val();
                            var entry_by = $('#entry_by').val();
                            var idwarehouse = $('#idwarehouse').val();
                            $.ajax({
                                url: "<?php echo base_url() ?>Reconciliation/submit_receive_cn_against_letter",
                                method: "POST",
                                dataType: 'json',
                                data:{doa_idvariant:doa_idvariant,doa_imei:doa_imei,id_doa_stock:id_doa_stock,entry_by:entry_by,idwarehouse:idwarehouse,idservice:idservice,cn_no:cn_no,cn_amount:cn_amount},
                                success: function (data)
                                {
                                    if(data.result === 'Success'){
                                        swal("CN Received", "Entry submitted...", "success");
                                        $(".modal-backdrop.in").hide();
                                        tr_row.remove();
                                    }else{
                                        swal("Failed to receive cn!", "Retry again", "warning");
                                        return false;
                                    }
                                }
                            });
                        }
                }else{
                    swal("Entry already received!", "Refresh and check again", "warning");
                    return false;
                }
            }
        });
        }else{
            swal("All fields are required!", "Enter CN and Amount", "warning");
            return false;
        }
    });
//    function recheck_entry(id_doa_stock){
//        var id_doa_stock=id_doa_stock;
//        $.ajax({
//            url: "<?php echo base_url() ?>Reconciliation/check_if_entry_receive",
//            method: "POST",
//            dataType: 'json',
//            data:{id_doa_stock:id_doa_stock},
//            success: function (data)
//            {
//                if(data.result === 'No'){
//                    return 1;
//                }else{
//                    return 0;
//                }
//            }
//        });
//    }
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
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> DOA Pending from Vendor</h3></center></div>
<div class="clearfix"></div><hr>
<div class="col-md-2">
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
<div class="col-md-3"><div id="count_1" class="text-info"></div></div>
<div class="col-md-1">
    <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('doa_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<input type="hidden" name="entry_by" id="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
<div class="thumbnail" style="overflow: auto;padding: 0">
    <div style="height: 650px;">
        <table id="doa_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px"></table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>