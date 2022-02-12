<?php include __DIR__ . '../../header.php'; ?>

<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    var products = [], checked;
   
     $(document).on('click', '#btn_letter', function () {      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=1;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/force_doa_clerance_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                        $(".doa_type").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});
                        $("#doa_return_type").val("1");
                    }
                });
            
        });
    
        $(document).on('click', '#btn_new_handset', function () {    
      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=2;
                var idservice = $('input[name=idservice]').val();
                var idbrand = $('input[name=idbrand'+idservice+']').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Service/force_doa_clerance_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type,idbrand:idbrand},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                         $(".doa_type").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});
                        $("#doa_return_type").val("2");
                        $(".new_product").hide();
                    }
                });            
        });
         $(document).on('click', '#btn_cn', function () {      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=4;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/force_doa_clerance_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                        $(".doa_type").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});
                        $("#doa_return_type").val("4");
                    }
                });
            
        });
       

    $(document).on('click', '.cancel_btn', function () {                  
        location.reload();
    });      
        
   $(document).on('change', '#idproblem', function() {
        $('.problem').val($('option:selected',this).text());
    });    
    $(document).on('keyup', '.doa_id', function() {
        var a=$(this).val();
        $('.doa').val(a);
    });
    $(document).on("click", "#btn_inward", function (event) {
        var is_selected = $('#is_selected').val();
        if(is_selected == 0){
            swal("Select any product!", "😠 Select product for Inward", "warning");
            return false;
        }
       
    });
});
</script>

<script>
//window.onload=function() { setTimeout(function(){ $('#myDiv').remove(); }, 2500); };
$(document).ready(function(){
    
    
     $(document).on('keydown', 'input[id=new_enter_imei]', function(e) {
        var keyCode = e.keyCode || e.which; 
        if (keyCode === 13 && $(this).val() !== '') {
            if($('#newidbrand').val() && $('#model').val()){            
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
                        $(".idvariant").html(data);                        
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        });
});
</script>
<div class="clearfix"></div>

<div style="font-family: K2D; font-size: 15px;" class="col-md-10 col-md-offset-1">    
        <div class="thumbnail" style="border-radius: 0; margin-bottom: 0"><br>
          <form enctype="multipart/form-data" class="inv_form">
        <center><h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> Force DOA Closure </h3></center><br>
          <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8">
                <div>CaseID  :- <b style="color: #0e10aa !important;"><?php echo $service_data[0]->id_service ?></b></div>
                <div >Date  :- <b><?php echo date('d-M-Y', strtotime($service_data[0]->entry_time)) ?></b></div><br>                                
                
            </div>
            <div class="col-md-4 col-xs-4">
                <div>Invoice Date :- <?php echo date('d-M-Y', strtotime($service_data[0]->inv_date)) ?></div>
                <div>Invoice No :- <?php echo $service_data[0]->inv_no; ?></div>
            </div>
            <div class="clearfix"></div><hr>            
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">                
                <b>Branch: &nbsp; <?php echo $service_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b>Customer , </b><br>
                <b>Name: &nbsp; <?php echo $service_data[0]->customer_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->mob_number; ?><br>
            </div>  
            <div class="clearfix"></div><hr>
           
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px">
            
            <thead class="bg-info">
                
                <th class="col-md-1">Sr no</th>
                <th class="col-md-7">Product / Description</th>
                              
            </thead>
            <tbody>
                <tr>                    
                    <td> 1 </td>
                    <td><?php echo $service_data[0]->full_name.' - ['.$service_data[0]->imei.']'; ?></td>
                                     
                </tr>                
                <tr>            
                    <td> </td>
                    <td colspan="1">Service Issue :- <?php echo $service_data[0]->problem; ?></td>                         
                </tr>
                <tr>
                    <td> </td>                    
                    <td colspan="1"> Remark :- <?php echo $service_data[0]->remark; ?></td>                    
                </tr>
            </tbody>
            
            </table>
            
            <div class="clearfix"></div>
            <div class="doa_type">
                <div class="col-md-12">
                    <center><h5 style="margin-top: 0"> Select DOA Type - based on what we have revceived from care center</h5></center><br>
                </div><div class="clearfix"></div>
                <div class="col-md-4"></div>
                <div class="col-md-2">
                    <button type="button" title="Received DOA letter" class="btn  btn-primary waves-effect waves-light btn_letter" id="btn_letter" ><span class="mdi mdi-cellphone-android fa-lg"></span> DOA Letter </button>
                </div>
                <div class="col-md-2">
                    <button type="button" title="Received new handset" class="btn  btn-primary waves-effect waves-light btn_new_handset" id="btn_new_handset" ><span class="mdi mdi-cellphone-android fa-lg"></span> New Handset </button>
                </div>
                <div class="col-md-2">
                    <button type="button" title="Received CN at branch" class="btn  btn-primary waves-effect waves-light btn_cn" id="btn_cn" ><span class="mdi mdi-cellphone-android fa-lg"></span> Received Credit Note </button>
                </div>
                
            </div>
            <div class="clearfix"></div>    
                <input type="hidden" name="idservice" value="<?php echo $service_data[0]->id_service; ?>" />            
                <input type="hidden" name="idmodel<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idmodel; ?>" />            
                <input type="hidden" name="idvariant<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idvariant; ?>" />
                <input type="hidden" name="idcategory<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idcategory; ?>" />
                <input type="hidden" name="idbrand<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idbrand; ?>" />                
                <input type="hidden" name="idproductcategory<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idproductcategory; ?>" />
                                
                <input type="hidden" name="erp_type" id="erp_type" value="<?php echo $service_data[0]->erp_type; ?>" />
                <input id="chk_return" type="hidden" class="chk_return" name="chk_return[]" value="<?php echo $service_data[0]->idsale_product ?>" />
                <input type="hidden" name="doa_return_type" id="doa_return_type" value="" />
                <input type="hidden" name="inv_date" value="<?php echo $service_data[0]->inv_date ?>" />
                <input type="hidden" name="inv_no" value="<?php echo $service_data[0]->inv_no ?>" />
                <input type="hidden" name="idbranch" value="<?php echo $service_data[0]->idbranch ?>" />
                <input type="hidden" name="idcustomer" value="<?php echo $service_data[0]->idcustomer ?>" />
                <input type="hidden" name="address" id="address" value="<?php echo $service_data[0]->cust_address ?>" />
                <input type="hidden" name="cust_idstate" id="cust_idstate" value="<?php echo $service_data[0]->cust_idstate ?>" />                
                <input type="hidden" name="mobile" value="<?php echo $service_data[0]->mob_number ?>" />                
                <input type="hidden" name="created_by" value="<?php echo $service_data[0]->idusers ?>" />
                <input type="hidden" name="id_sale" value="<?php echo $service_data[0]->idsale ?>" />
                <input type="hidden" name="cust_pincode" id="cust_pincode" value="" />                
                
                <input type="hidden" name="qty" id="qty" value="1" />
                <input type="hidden" name="ret_product_name" id="ret_product_name" value="<?php echo $service_data[0]->full_name ?>" />
                <input type="hidden" name="imei_no<?php echo $service_data[0]->id_service; ?>" id="imei_no" value="<?php echo $service_data[0]->imei ?>" />
                <input type="hidden" name="skutype<?php echo $service_data[0]->id_service; ?>" id="skutype" value="<?php echo $service_data[0]->idskutype ?>" />
                <input type="hidden" name="sales_return_invid<?php echo $service_data[0]->id_service; ?>" id="sales_return_invid" value="<?php echo $service_data[0]->sales_return_invid ?>" />
                <input type="hidden" name="new_imei_against_doa" id="new_imei_against_doa" value="<?php echo $service_data[0]->new_imei_against_doa ?>" />
                
            
            <div class="dynamic_form_new_handset" style="display: none;">            
               
            </div>        
            <div class="dynamic_form" style="display: none;">            
               
            </div>
             </form>
        </div>
   
    </div>
    
        
<?php   include __DIR__ . '../../footer.php'; ?>
