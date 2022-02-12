<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>

<script>
  $(document).ready(function(){
      $(document).on("click", ".receive_service_case", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idservice=$(this).val();
            var remark=$(parentDiv).find('.remark').val();
            var counter_faulty=$(parentDiv).find('.counter_faulty').val();
            var warranty_state=$(parentDiv).find('input[name=warranty_state]:checked').val();
            var imei_no=$(parentDiv).find('.imei_no').val();
            var idvariant=$(parentDiv).find('.idvariant').val();
            var iduser=$('#iduser').val();
            var idbranch=$('#idbranch').val();
            if(remark != ''){
                if (confirm('Do you want to Receive??')) {
                   jQuery.ajax({
                       url: "<?php echo base_url('Service/ajax_receive_service_case') ?>",
                       method:"POST",
                       data:{idservice:idservice,remark:remark,warranty_state:warranty_state,counter_faulty:counter_faulty,idvariant:idvariant,imei_no:imei_no,iduser:iduser,idbranch:idbranch},
                       success:function(data){
                           $(parentDiv).remove();
                           swal('🙂 Service handset received and closed', 'Case Closed', 'success');
                       }
                   });
               }
           }else{
                alert('Select status and enter remark');
           }
        });
        $(document).on("change", "#type", function(event) {                      
            var type = +$('#type').val();
            var branch = +$('#idbranch').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_pending_service_stock_report",
                method:"POST",
                data:{ type:type,idbranch: branch},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
         $(document).on("click", ".check_doa", function(event) {  
         alert("dsfg");
            el=$(this).parent();
            $(el).find('.doa_with').show();
            $(el).find('.close_with').hide();
        });
         $(document).on("click", ".check_close", function(event) {                      
            el=$(this).parent();
            $(el).find('.doa_with').hide();
            $(el).find('.close_with').show();
        });
        
        $(document).on("click", ".btn_receive_case", function(event) {            
            var ce = $(this);
            var idservice = $(this).val();
            var parentDiv=$(ce).closest('td').parent('tr');            
//            var idservice=$(parentDiv).find('.details').show();
            $(parentDiv).find('.details').html('<div class="col-md-6" style="padding: 2px">\n\
                                <label class="form-check-label thumbnail" for="repaired'+idservice+'" style="padding: 5px; margin: 0px; font-weight: 100">\n\
                                        &nbsp; <input type="radio" class="warranty_state" name="warranty_state" id="repaired'+idservice+'" value="1" />&nbsp; Repaired</label></div>\n\
                                    <div class="col-md-6" style="padding: 2px">\n\
                                    <label class="form-check-label thumbnail" for="rejected'+idservice+'" style="padding: 5px; margin: 0px; font-weight: 100">\n\
                                        &nbsp; <input type="radio" class="warranty_state" name="warranty_state" id="rejected'+idservice+'" value="2" />\n\
                                        &nbsp; Rejected\n\
                                    </label>\n\
                                </div>\n\
                                <div class="col-md-12" style="padding: 2px">\n\
                                    <input type="text" class="form-control input-sm remark" id="remark" name="remark" placeholder="Enter remark"/>\n\
                                </div><div class="clearfix"></div>\n\
                                <div class="pull-right" style="padding: 2px">\n\
                                    <button value="'+idservice+'" idservice="'+idservice+'" class="btn btn-primary btn-sm gradient2 receive_service_case" ><span class="fa fa-send-o"></span> Receive </button>\n\
                                </div><div class="clearfix"></div>');
            $(parentDiv).find('.btn_make_doa').hide();
            $(this).hide();
        });
        
        
        $(document).on("click", ".doa_letter_ajax", function(event) {
            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
            var doa_let_block = '<center><h4>DOA Letter Service State</h4></center><hr>\n\
                    <div class="col-md-2">DOA ID</div>\n\
                    <div class="col-md-4"><input type="text" class="form-control input-sm doa_id" placeholder="Enter DOA ID" /></div>\n\
                    <div class="col-md-2">DOA Date</div>\n\
                    <div class="col-md-4"><input type="text" data-provide="datepicker" onfocus="blur()" class="form-control input-sm doa_date" placeholder="Enter DOA Date" /></div>\n\
                    <div class="clearfix"></div><br>\n\
                    <div class="col-md-3">Upload File</div>\n\
                    <div class="col-md-5"><input type="file" class="form-control input-sm doa_letter_file" /></div>\n\
                    <div class="col-md-4 pull-right">\n\
                        <button class="btn btn-info doa_letter_btn" value="3" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">DOA Letter</button>\n\
                    </div><div class="clearfix"></div>';
            parent_div.html(doa_let_block);
        });
        
        $(document).on("click", ".doa_handset_ajax", function(event) {
            var parent_div = $(this).closest('label').parent('div').parent('.action_form').find('.service_state_form');
            $.ajax({
                url: "<?php echo base_url() ?>Service/force_doa_handset_entry_form",
                method: "POST",
                data:{},
                success: function (data)
                {
                    parent_div.html(data);
                    $(".chosen-select").chosen({search_contains: true});
                }
            });
        });
        $(document).on('change', '#newidbrand', function () {
            if ($('#newidbrand').val()) {
                var product_category = 0;
                var brand = +$('#newidbrand').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/ajax_variants_by_brand",
                    method: "POST",
                    data: {brand: brand, product_category: product_category},
                    success: function (data)
                    {
                        $(".model_block").html(data);                        
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        });
        $(document).on('click', '.doa_handset_btn', function(e) {
            var action_form = $(this).closest('div').parent('div').parent('.action_form');
            var warranty_status = $(this).val();
            var repaire_remark = $(this).closest('div').parent('div').find('.repaire_remark').val();
            var new_enter_imei = $(this).closest('div').parent('div').find('input[name=new_enter_imei]').val();
            var model = $(this).closest('div').parent('div').find('.model').val();
            var newidbrand = $(this).closest('div').parent('div').find('.newidbrand').val();
            
            var imei = action_form.find('.imei_no').val();
            var idservice = action_form.find('.idservice').val();
            var idbranch = action_form.find('.idbranch').val();
            var idvariant = action_form.find('.idvariant').val();
            var counter_faulty = action_form.find('.counter_faulty').val();
            var iduser = +$('#iduser').val();            
            if(new_enter_imei){
            $.ajax({
                url: "<?php echo base_url() ?>Service/service_send_to_local_counter_faulty_doa_handset_btn",
                method: "POST",
                data:{imei:imei,idservice:idservice,idbranch:idbranch,idvariant:idvariant,iduser:iduser,repaire_remark:repaire_remark,warranty_status:warranty_status,counter_faulty:counter_faulty,
                       new_enter_imei:new_enter_imei,model:model,newidbrand:newidbrand},
                dataType: 'json',
                success: function (data)
                {
                    if(data.result === 'Success'){
                        swal("Product status changed", "Status - DOA Handset Submitted", "success");
                        $(".modal-backdrop.in").hide();
                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
                    }else{
                        swal("Failed to change status!", "Retry again", "warning");
                        return false;
                    }
                }
            });
            }else{
                swal("Please enter Handset Details!", "Retry again", "warning");
            }
        });
        
        $(document).on("click", ".doa_letter_btn", function() {
            var action_form = $(this).closest('div').parent('div').parent('.action_form');
            var warranty_status = $(this).val();
            var doa_id = $(this).closest('div').parent('div').find('.doa_id').val();
            var doa_date = $(this).closest('div').parent('div').find('.doa_date').val();
            var doa_letter_file = $(this).closest('div').parent('div').find('.doa_letter_file').val();
            var imei = action_form.find('.imei_no').val();
            var idservice = action_form.find('.idservice').val();
            var idbranch = action_form.find('.idbranch').val();
            var idvariant = action_form.find('.idvariant').val();
            var counter_faulty = action_form.find('.counter_faulty').val();
            var iduser = +$('#iduser').val();
            var doa_letter_file = $(this).closest('div').parent('div').find('.doa_letter_file').prop("files")[0];   // Getting the properties of file from file field
            
            var form_data = new FormData();           
            form_data.append("doa_letter_file", doa_letter_file);
            form_data.append("iduser", iduser);         
            form_data.append("counter_faulty", counter_faulty);         
            form_data.append("idvariant", idvariant);         
            form_data.append("idbranch", idbranch);         
            form_data.append("idservice", idservice);         
            form_data.append("imei", imei);
            form_data.append("doa_date", doa_date);
            form_data.append("doa_id", doa_id);     
            form_data.append("warranty_status", warranty_status);
                
            $.ajax({
                url: "<?php echo base_url() ?>Service/service_send_to_local_counter_faulty_doa_letter_btn",
//                    dataType: 'script',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         // Setting the data attribute of ajax with file_data
                type: 'post',
                success: function (data)
                {
                    if(data.result === 'Success'){
                        swal("Product status changed", "Status - DOA Letter Submitted", "success");
                        $(".modal-backdrop.in").hide();
                        action_form.parent('div').parent('div').parent('div').parent('div').parent('td').parent('tr').remove();
                    }else{
                        swal("Failed to change status!", "Retry again", "warning");
                        return false;
                    }
                }
           });
        });
        
        
        $(document).on('keydown', 'input[id=new_enter_imei]', function(e) {
            var keyCode = e.keyCode || e.which; 
            if (keyCode === 13 && $(this).val() !== '') {
                var selmodel = $(this).closest('div').parent('div').find('#model');
                var selbrand = $(this).closest('div').parent('div').find('#newidbrand');
                if(selbrand.val() && selmodel.val()){
                    var imei = $(this).val();
                        $.ajax({
                            url: "<?php echo base_url() ?>Service/ajax_verify_imei_presence",
                            method: "POST",
                            data:{imei : imei},
                             dataType:'json',
                            success: function (data)
                            {
                                if(data.data === 'fail'){
                                    swal("Product already present in ERP!", "Re-verify IMEI and try again", "warning");
                                    return false;
                                }else if(data.data === 'success'){
                                    $('input[id=new_enter_imei]').prop('name',"new_enter_imei");
                                    $('input[id=new_enter_imei]').prop('readonly',"readonly");
                                    $(".new_product").show();
                                    return false;
                                }
                            }
                        });
                }else{
                    swal("Select Brand and Model First!", "Select model", "warning");
                    return false;
                }
            }
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
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> Pending Service Stock</h3></center></div>
<div class="clearfix"></div><hr>
 <input type="hidden" value="<?php echo $_SESSION['idbranch']; ?>" name="idbranch" id="idbranch">   
    <div class="col-md-1">Pending AT</div>
    <div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" id="type" name="type">            
            <option value="0">All</option>
            <option value="1">Local Care</option>
            <option value="2">HO</option>
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
    <div class="col-md-4">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary btn-sm gradient2 export pull-right" onclick="javascript:xport.toCSV('service_data');" style="margin-top: 6px;line-height: unset; "><span class="fa fa-file-excel-o"></span> Export</button>
    </div><div class="clearfix"></div><br>
    <input type="hidden" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    <div class="thumbnail" style="overflow: auto;padding: 0">
        <div style="height: 650px;" id="stock_data">
            <table id="service_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">           
              <?php if(count($service_stock)){ ?>
                <thead class="fixedelementtop">                    
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>                        
                        <td><?php echo $stock->id_service; ?>
                            <input type="hidden" name="idservice" class="idservice" id="idservice" value="<?php echo $stock->id_service ?>">
                        </td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>    
<!--                        <td>
                        </td>-->
                        <?php if($stock->process_status == 2){ ?>
                        <td style="width:250px">
                            <?php if($stock->counter_faulty){ ?>
                            <a class="btn btn-sm btn-warning waves-effect white-text" href="" data-toggle="modal" data-target="#edit<?php echo $stock->id_service ?>">DOA</a>
                            <div class="modal fade" id="edit<?php echo $stock->id_service ?>">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <center><h4>Servicse Process - Generate Service State </h4></center><hr>
                                            <?php if($stock->counter_faulty){ ?>
                                            <center class="red-text"><i class="mdi mdi-flip-to-back fa-lg"></i> <?php echo 'Counter Faulty Product'; ?></center><hr>
                                            <?php }else{ ?>
                                            <center class="red-text"><i class="mdi mdi-flip-to-back fa-lg"></i> <?php echo 'Sold Product'; ?></center><hr>
                                            <?php } ?>
                                            <div class="action_form" style="line-height: 25px">
                                                <div class="col-md-3" style="font-weight: bold">Case ID: <?php echo $stock->id_service ?></div>
                                                <div class="col-md-9"><?php echo $stock->full_name ?> - <?php echo $stock->imei ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Branch</div>
                                                <div class="col-md-9"><?php echo $stock->branch_name; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Problem</div>
                                                <div class="col-md-9"><?php echo $stock->problem; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Remark</div>
                                                <div class="col-md-9"><?php echo $stock->remark; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Customer</div>
                                                <div class="col-md-9"><?php echo $stock->customer_name.'-'.$stock->mob_number ?></div><div class="clearfix"></div>
                                                <div class="clearfix"></div><hr>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="doa_letter<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="doa_letter_ajax" name="state<?php echo $stock->id_service ?>" id="doa_letter<?php echo $stock->id_service ?>" onclick="return doa_letter_data<?php echo $stock->id_service ?>()" />
                                                        &nbsp; DOA Letter
                                                    </label>
                                                </div>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="doa_handset<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="doa_handset_ajax" name="state<?php echo $stock->id_service ?>" id="doa_handset<?php echo $stock->id_service ?>" onclick="return doa_handset_data<?php echo $stock->id_service ?>()" />
                                                        &nbsp; Replacement Handset                                                
                                                </div><div class="clearfix"></div><br>
                                                <div class="service_state_form thumbnail"><center>Select Action</center></div>
                                                <input type="hidden" class="counter_faulty" name="counter_faulty" value="<?php echo $stock->counter_faulty ?>" />
                                                <input type="hidden" class="imei_no" name="imei_no" value="<?php echo $stock->imei ?>" />
                                                <input type="hidden" class="idbranch" name="idbranch" value="<?php echo $stock->idbranch ?>" />
                                                <input type="hidden" class="idservice" name="idservice" value="<?php echo $stock->id_service ?>" />
                                                <input type="hidden" class="idvariant" name="idvariant" value="<?php echo $stock->idvariant ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <a class="btn btn-warning btn-sm btn_make_doa" href="<?php echo base_url('Service/make_doa/'.$stock->id_service) ?>">DOA</a>
                        <?php } ?>
                            
                            <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 btn_receive_case col-md-7 pull-right" style="text-transform: capitalize" value="<?php echo $stock->id_service ?>"><i class="fa fa-send-o"></i> Receive and Close </button>
                            <!--<a href="<?php //  echo base_url('Service/receive_service_case/'.$stock->id_service) ?>" class="btn btn-primary gradient2 btn-sm" style="margin-top: 6px;line-height: unset;"><center>Receive and Close</center></a>-->
                            <div class="details"></div>
                            <input type="hidden" class="counter_faulty" value="<?php echo $stock->counter_faulty ?>" />
                            <input type="hidden" class="imei_no" value="<?php echo $stock->imei ?>" />
                            <input type="hidden" class="idvariant" value="<?php echo $stock->idvariant ?>" />
<!--                                <div class="col-md-6" style="padding: 2px">
                                    <label class="form-check-label thumbnail" for="repaired<?php echo $stock->id_service ?>" style="padding: 5px; margin: 0px; font-weight: 100">
                                        &nbsp; <input type="radio" class="warranty_state" name="warranty_state" id="repaired<?php echo $stock->id_service ?>" value="1" />
                                         Repaired
                                    </label>
                                </div>
                                <div class="col-md-6" style="padding: 2px">
                                    <label class="form-check-label thumbnail" for="rejected<?php echo $stock->id_service ?>" style="padding: 5px; margin: 0px; font-weight: 100">
                                        &nbsp; <input type="radio" class="warranty_state" name="warranty_state" id="rejected<?php echo $stock->id_service ?>" value="2" />
                                        Rejected
                                    </label>
                                </div>
                                <div class="col-md-12" style="padding: 2px">
                                    <input type="text" class="form-control input-sm remark" id="remark" name="remark" placeholder="Enter remark"/>
                                </div><div class="clearfix"></div>
                                <div class="pull-right" style="padding: 2px">
                                    <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 receive_service_case" ><span class="fa fa-send-o"></span> Receive </button>
                                </div><div class="clearfix"></div>-->
                            <!--</div>-->
                        </td>
                        <?php }else{ ?>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php } ?>
                        </td>
                        <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
       
        <?php } ?>
             </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>