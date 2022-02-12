<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        $("#sidebar").addClass("active");
        $(document).on("click", ".service_stock", function(event) {
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#idbranch').val();
            var iduser = +$('#iduser').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_branch_received_by_ho",
                method:"POST",
                data:{brand: brand, idbranch: branch, product_category: product_category,iduser:iduser},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                     $(".chosen-select").chosen({search_contains: true});
                }
            });           
        });
//        
//        $(document).on("click", ".repaired_ajax", function(event) {
//            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
//            var rep_block = '<center><h4>Repaired Service State</h4></center><hr>\n\
//                    <div class="col-md-2">Remark</div>\n\
//                    <div class="col-md-10"><input type="text" class="form-control repaire_remark" placeholder="Enter Repaired Remark" /></div>\n\
//                    <div class="clearfix"></div><br>\n\
//                    <div class="col-md-4 pull-right">\n\
//                        <button class="btn btn-warning repaired_btn" id="repaired_btn" value="1" >Repaired</button>\n\
//                    </div><div class="clearfix"></div>';
//            parent_div.html(rep_block);
//        });
//        $(document).on("click", ".rejected_ajax", function(event) {
//            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
//            var rej_block = '<center><h4>Rejected Service State</h4></center><hr>\n\
//                    <div class="col-md-2">Remark</div>\n\
//                    <div class="col-md-10"><input type="text" class="form-control repaire_remark" placeholder="Enter Rejected Remark" /></div>\n\
//                    <div class="clearfix"></div><br>\n\
//                    <div class="col-md-4 pull-right">\n\
//                        <button class="btn btn-danger rejected_btn" value="2">Rejected</button>\n\
//                    </div><div class="clearfix"></div>';
//            parent_div.html(rej_block);
//        });
//        $(document).on("click", ".doa_letter_ajax", function(event) {
//            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
//            var doa_let_block = '<center><h4>DOA Letter Service State</h4></center><hr>\n\
//                    <div class="col-md-2">DOA ID</div>\n\
//                    <div class="col-md-4"><input type="text" class="form-control input-sm doa_id" placeholder="Enter DOA ID" /></div>\n\
//                    <div class="col-md-2">DOA Date</div>\n\
//                    <div class="col-md-4"><input type="text" data-provide="datepicker" onfocus="blur()" class="form-control input-sm doa_date" placeholder="Enter DOA Date" /></div>\n\
//                    <div class="clearfix"></div><br>\n\
//                    <div class="col-md-3">Upload File</div>\n\
//                    <div class="col-md-5"><input type="file" class="form-control input-sm doa_letter_file" /></div>\n\
//                    <div class="col-md-4 pull-right">\n\
//                        <button class="btn btn-info doa_letter_btn" value="3" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">DOA Letter</button>\n\
//                    </div><div class="clearfix"></div>';
//            parent_div.html(doa_let_block);
//        });
//        
//        $(document).on("click", ".doa_handset_ajax", function(event) {
//            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
//            $.ajax({
//                url: "<?php echo base_url() ?>Service/force_doa_handset_entry_form",
//                method: "POST",
//                data:{},
//                success: function (data)
//                {
//                    parent_div.html(data);
//                    $(".chosen-select").chosen({search_contains: true});
//                }
//            });
//        });
//        $(document).on('change', '#newidbrand', function () {
//            if ($('#newidbrand').val()) {
//                var product_category = 0;
//                var brand = +$('#newidbrand').val();
//                $.ajax({
//                    url: "<?php echo base_url() ?>Stock_allocation/ajax_variants_by_brand",
//                    method: "POST",
//                    data: {brand: brand, product_category: product_category},
//                    success: function (data)
//                    {
//                        $(".model_block").html(data);                        
//                        $(".chosen-select").chosen({search_contains: true});
//                    }
//                });
//            }
//        });
//        // repaire
//        $(document).on('click', '.repaired_btn', function(e) {
//            var action_form = $(this).closest('div').parent('div').parent('.action_form');
//            var warranty_status = $(this).val();
//            var repaire_remark = $(this).closest('div').parent('div').find('.repaire_remark').val();
//            var imei = action_form.find('.imei_no').val();
//            var idservice = action_form.find('.idservice').val();
//            var idwarehouse = action_form.find('.idwarehouse').val();
//            var idvariant = action_form.find('.idvariant').val();
//            var counter_faulty = action_form.find('.counter_faulty').val();
//            var iduser = +$('#iduser').val();
//            $.ajax({
//                url: "<?php echo base_url() ?>Service/service_process_by_excecutive_repaire",
//                method: "POST",
//                data:{imei:imei,idservice:idservice,idwarehouse:idwarehouse,idvariant:idvariant,iduser:iduser,repaire_remark:repaire_remark,warranty_status:warranty_status,counter_faulty:counter_faulty},
//                dataType: 'json',
//                success: function (data)
//                {
//                    if(data.result === 'Success'){
//                        swal("Product status changed", "Status - Repaired", "success");
//                        $(".modal-backdrop.in").hide();
//                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
//                    }else{
//                        swal("Failed to change status!", "Retry again", "warning");
//                        return false;
//                    }
//                }
//            });
//        });
//        $(document).on('click', '.rejected_btn', function(e) {
//            var action_form = $(this).closest('div').parent('div').parent('.action_form');
//            var warranty_status = $(this).val();
//            var repaire_remark = $(this).closest('div').parent('div').find('.repaire_remark').val();
//            var imei = action_form.find('.imei_no').val();
//            var idservice = action_form.find('.idservice').val();
//            var idwarehouse = action_form.find('.idwarehouse').val();
//            var idvariant = action_form.find('.idvariant').val();
//            var counter_faulty = action_form.find('.counter_faulty').val();
//            var iduser = +$('#iduser').val();
//            $.ajax({
//                url: "<?php echo base_url() ?>Service/service_process_by_excecutive_reject",
//                method: "POST",
//                data:{imei:imei,idservice:idservice,idwarehouse:idwarehouse,idvariant:idvariant,iduser:iduser,repaire_remark:repaire_remark,warranty_status:warranty_status,counter_faulty:counter_faulty},
//                dataType: 'json',
//                success: function (data)
//                {
//                    if(data.result === 'Success'){
//                        swal("Product status changed", "Status - Rejected", "success");
//                        $(".modal-backdrop.in").hide();
//                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
//                    }else{
//                        swal("Failed to change status!", "Retry again", "warning");
//                        return false;
//                    }
//                }
//            });
//        });
//        $(document).on('click', '.doa_handset_btn', function(e) {
//            var action_form = $(this).closest('div').parent('div').parent('.action_form');
//            var warranty_status = $(this).val();
//            var repaire_remark = $(this).closest('div').parent('div').find('.repaire_remark').val();
//            var new_enter_imei = $(this).closest('div').parent('div').find('.new_enter_imei').val();
//            var model = $(this).closest('div').parent('div').find('.model').val();
//            var newidbrand = $(this).closest('div').parent('div').find('.newidbrand').val();
//            
//            var imei = action_form.find('.imei_no').val();
//            var idservice = action_form.find('.idservice').val();
//            var idwarehouse = action_form.find('.idwarehouse').val();
//            var idvariant = action_form.find('.idvariant').val();
//            var counter_faulty = action_form.find('.counter_faulty').val();
//            var iduser = +$('#iduser').val();
//            $.ajax({
//                url: "<?php echo base_url() ?>Service/service_process_by_excecutive_doa_handset_btn",
//                method: "POST",
//                data:{imei:imei,idservice:idservice,idwarehouse:idwarehouse,idvariant:idvariant,iduser:iduser,repaire_remark:repaire_remark,warranty_status:warranty_status,counter_faulty:counter_faulty,
//                       new_enter_imei:new_enter_imei,model:model,newidbrand:newidbrand},
//                dataType: 'json',
//                success: function (data)
//                {
//                    if(data.result === 'Success'){
//                        swal("Product status changed", "Status - DOA Handset Submitted", "success");
//                        $(".modal-backdrop.in").hide();
//                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
//                    }else{
//                        swal("Failed to change status!", "Retry again", "warning");
//                        return false;
//                    }
//                }
//            });
//        });
//        
//        $(document).on("click", ".doa_letter_btn", function() {
//            var action_form = $(this).closest('div').parent('div').parent('.action_form');
//            var warranty_status = $(this).val();
//            var doa_id = $(this).closest('div').parent('div').find('.doa_id').val();
//            var doa_date = $(this).closest('div').parent('div').find('.doa_date').val();
//            var doa_letter_file = $(this).closest('div').parent('div').find('.doa_letter_file').val();
//            var imei = action_form.find('.imei_no').val();
//            var idservice = action_form.find('.idservice').val();
//            var idwarehouse = action_form.find('.idwarehouse').val();
//            var idvariant = action_form.find('.idvariant').val();
//            var counter_faulty = action_form.find('.counter_faulty').val();
//            var iduser = +$('#iduser').val();
//            var doa_letter_file = $(this).closest('div').parent('div').find('.doa_letter_file').prop("files")[0];   // Getting the properties of file from file field
//            
//            var form_data = new FormData();           
//            form_data.append("doa_letter_file", doa_letter_file);
//            form_data.append("iduser", iduser);         
//            form_data.append("counter_faulty", counter_faulty);         
//            form_data.append("idvariant", idvariant);         
//            form_data.append("idwarehouse", idwarehouse);         
//            form_data.append("idservice", idservice);         
//            form_data.append("imei", imei);
//            form_data.append("doa_date", doa_date);
//            form_data.append("doa_id", doa_id);     
//            form_data.append("warranty_status", warranty_status);
//                
//            $.ajax({
//                url: "<?php echo base_url() ?>Service/service_process_by_excecutive_doa_letter_btn",
////                    dataType: 'script',
//                dataType: 'json',
//                cache: false,
//                contentType: false,
//                processData: false,
//                data: form_data,                         // Setting the data attribute of ajax with file_data
//                type: 'post',
//                success: function (data)
//                {
//                    if(data.result === 'Success'){
//                        swal("Product status changed", "Status - DOA Letter Submitted", "success");
//                        $(".modal-backdrop.in").hide();
//                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
//                    }else{
//                        swal("Failed to change status!", "Retry again", "warning");
//                        return false;
//                    }
//                }
//           });
//        });
//        
//        
//        $(document).on('keydown', 'input[id=new_enter_imei]', function(e) {
//            var keyCode = e.keyCode || e.which; 
//            if (keyCode === 13 && $(this).val() !== '') {
//                var selmodel = $(this).closest('div').parent('div').find('#model');
//                var selbrand = $(this).closest('div').parent('div').find('#newidbrand');
//                if(selbrand.val() && selmodel.val()){
//                    var imei = $(this).val();
//                        $.ajax({
//                            url: "<?php echo base_url() ?>Service/ajax_verify_imei_presence",
//                            method: "POST",
//                            data:{imei : imei},
//                             dataType:'json',
//                            success: function (data)
//                            {
//                                if(data.data === 'fail'){
//                                    swal("Product already present in ERP!", "Re-verify IMEI and try again", "warning");
//                                    return false;
//                                }else if(data.data === 'success'){
//                                    $('input[id=new_enter_imei]').prop('name',"new_enter_imei");
//                                    $('input[id=new_enter_imei]').prop('readonly',"readonly");
//                                    $(".new_product").show();
//                                    return false;
//                                }
//                            }
//                        });
//                }else{
//                    swal("Select Brand and Model First!", "Select model", "warning");
//                    return false;
//                }
//            }
//        });
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
<div class="col-md-10"><center><h3><span class="mdi mdi-exit-to-app fa-lg"></span> Branch Received Stock</h3></center></div><div class="clearfix"></div><hr>
    <?php  if(count($branch_data)==1){ ?>
          <input type="hidden" value="<?php echo $branch_data[0]->id_branch; ?>" name="idbranch" id="idbranch">        
    <?php }else{ ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch" required="">
            <option value="">Select Branch</option>   
            <option value="0">All</option>         
            <?php foreach($branch_data as $branch){ ?>                
                <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="">Product Category</option>
            <option value="0">All</option>
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="">Select Brand</option>
            <option value="0">All</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <?php if($this->session->userdata('idrole') == 39){ ?>
    <input type="hidden" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    <?php }else{ ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" id="iduser" name="iduser">
            <option value="0">Select Excecutive</option>
            <option value="0">All</option>
            <?php foreach ($service_excecutive as $excecutive){ ?>
            <option value="<?php echo $excecutive->id_users ?>"><?php echo $excecutive->user_name ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
    <div class="col-md-2" style="text-align: center;">
        <button type="button"  class="service_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Filter</button>
    </div>
    <div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto;padding: 0">
         <br> 
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
        <div class="col-md-6">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div style="height: 650px;">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
            </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>