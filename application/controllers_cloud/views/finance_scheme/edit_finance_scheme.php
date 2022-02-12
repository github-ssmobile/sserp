<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        
        $(document).on('change', '#brand', function() {          
            var brand = +$('#brand').val();    
            
            var product_category=0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_variants_by_brand",
                method:"POST",
                data:{brand : brand,product_category: product_category},
                success:function(data)
                {
                    $("#model_list").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
       
        $(document).on('change', '#finance_scheme', function() {          
            var idhead = +$('#finance_scheme').val();    
            if(idhead == '4'){
                var type = 0;
                $('#scheme_type').val(type);
                $('#scheme_type1').val(type);
                $('#finance_block').show();
                $('#swipe_block').hide();
            }
            if(idhead == '3'){
                var type = 1;
                $('#scheme_type').val(type);
                $('#scheme_type1').val(type);
                $('#finance_block').hide();
                $('#swipe_block').show();
                
            }
            
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_get_payment_mode_byidhead",
                method:"POST",
                data:{idhead : idhead},
                success:function(data)
                {
                    $("#finance_provider").html(data);
                    $("#finance_provider1").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
        
        $(document).on('change', '#model', function() {          
            var idvariant = +$('#model').val();    
            var mop = 0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_get_model_variant_data_byidvariant",
                method:"POST",
                data:{idvariant : idvariant},
                success:function(data)
                {
                     mop = data;
                    $("#finance_mop").val(mop);
                }
            });
        });
        $(document).on('change', '#down_emi,#finance_emi', function() {          
            var total_emi = +$('#total_emi').val();    
            var down_emi = +$('#down_emi').val();    
            var mop = +$('#finance_mop').val();    
            var femi = total_emi - down_emi;
            $('#finance_emi').val(femi);     
            var finance_emi = +$('#finance_emi').val();    
            var sche_code = '';
            var emi = 0;
            var fin_emi=0;
            var down = 0;
            if(total_emi != '' && finance_emi !='' && down_emi !='' && mop !=''){
                var tot = finance_emi + down_emi;
                if(tot == total_emi){
                    sche_code = total_emi+'/'+down_emi;
                    emi =  mop/total_emi;
                    fin_emi = emi*finance_emi;
                    down = emi * down_emi;
                    
                    $('#scheme_code').val(sche_code);
                    $('#emi_amount').val(Math.round(emi));
                    $('#finance_emi_amount').val(Math.round(fin_emi));
                    $('#down_emi_amount').val(Math.round(down));
                }else{
                    alert('sum of Finance EMI and Downpayment EMI is equal to ' +total_emi);
                    return false;
                }
            }else{
                alert("Fill EMI Details");
                return false;
            }
        });
        
        
        
        //Swipe
         $(document).on('change', '#idbrand', function() {          
            var brand = +$('#idbrand').val();    
            
            var product_category=0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_variants_by_brand_swipe",
                method:"POST",
                data:{brand : brand,product_category: product_category},
                success:function(data)
                {
                    $("#model_list1").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
          $(document).on('change', '#idmodel', function() {          
            var idvariant = +$('#idmodel').val();    
            var mop = 0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_get_model_variant_data_byidvariant",
                method:"POST",
                data:{idvariant : idvariant},
                success:function(data)
                {
                     mop = data;
                    $("#finance_mop1").val(mop);
                }
            });
        });
          $(document).on('change', '#swipe_scheme', function() {          
            var type = $('#swipe_scheme').val(); 
            if(type == '1'){ //Brand
                $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                 $('.cashback_type').hide();
                $('.rate_interest').hide();
                $('.mop_interest').hide();
                $('.cashback1').hide();
                
            }
            if(type == '2'){ //Bank
                 $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                $('.rate_interest').show();
                $('.mop_interest').show();
                  $('.cashback_type').hide();
                $('.cashback1').hide();
            }
            if(type == '3'){ // Cashback
                $('.cashback_type').show();
                $('.cashback1').show();
                $('.total_emi1').hide();
                $('.finance_emi1').hide();
                $('.down_emi1').hide();
                $('.emi_amount1').hide();
                $('.rate_interest').hide();
                $('.mop_interest').hide();
                
                $('#finance_emi1').attr('required', false);
                $('#total_emi1').attr('required', false);
                
            }
        });
        $(document).on('change', '#total_emi1', function() {          
            var total_emi = +$('#total_emi1').val();    
            var mop = +$('#finance_mop1').val();    
            var emi = 0;
            if(total_emi != '' && mop != '' ){
                emi = mop/total_emi;
                $('#emi_amount1').val(emi);
                $('#finance_emi1').val(total_emi);
                $('#finance_emi_amount1').val(emi);
            }else{
                alert("Fill EMI Details");
                return false;
            }
        });
        
        $('.btndelete').click(function (){
           var idfinance = $(this).closest('td').find('#ídfinance').val();
           var all = 0;
            if(!confirm("Do You Want To Delete All Color Variants Entry Of This Model ? ")){
                all = 0;
                $.ajax({
                    url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
                    method:"POST",
                    data:{idfinance : idfinance,all: all},
                    success:function(data)
                    {
                        if(data == '1' || data == 1 ){
                            alert("Finance Scheme Deleted successfully");
                            window.location.reload();
                        }else{
                            alert("Failed To delete");
                            return false;
                        }
                        
                    }
                });
            }else{
                all = 1;
                 $.ajax({
                    url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
                    method:"POST",
                    data:{idfinance : idfinance,all: all},
                    success:function(data)
                    {
                         if(data == '1' || data == 1 ){
                            alert("Finance Scheme Deleted successfully");
                            window.location.reload();
                        }else{
                            alert("Failed To delete");
                            return false;
                        }
                    }
                });
            }
           
        });
        $(document).on('change', '#rate_interest', function() {          
            var intrest = +$('#rate_interest').val();    
            var mop = +$('#finance_mop1').val();    
            var total_emi = +$('#total_emi1').val();    
            var fina_emi = 0;
           
            if(intrest > 0){
                var interest_amount = (mop/100)*intrest;
                var mopintrest = mop + interest_amount;
                $('#mop_interest').val(Math.round(mopintrest));
                fina_emi = (mopintrest/total_emi);
                $('#emi_amount1').val(Math.round(fina_emi));
                $('#finance_emi_amount1').val(Math.round(fina_emi));
                
            }else{
                $('#mop_interest').val(mop);
            }
           
        });
        
        $(document).on('change', '.finance_interest_rate,.processng_fee', function() {   
            var mop = +$('#finance_mop').val();    
         
            var interest_rate = (isNaN(+$(".finance_interest_rate").val())) ? 0 : +$(".finance_interest_rate").val();
            var fee = (isNaN(+$(".processng_fee").val())) ? 0 : +$(".processng_fee").val();
            var down_emi_amount = +$('#down_emi_amount').val(); 
            var interest_amount = 0;
            if(interest_rate > 0 ){
                interest_amount = (mop * interest_rate)/100;
            }
            var final = down_emi_amount + interest_amount + fee;
           $('.final_down_emi_amount').val(final);
       });
       
    });
    
    $(window).on('load', function() {
        var type  = <?php echo $finance_data->scheme_type ?>;
         if(type == '1'){ //Brand
                $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                 $('.cashback_type').hide();
                $('.rate_interest').hide();
                $('.mop_interest').hide();
                $('.cashback1').hide();
                
            }
            if(type == '2'){ //Bank
                 $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                $('.rate_interest').show();
                $('.mop_interest').show();
                  $('.cashback_type').hide();
                $('.cashback1').hide();
            }
            if(type == '3'){ // Cashback
                $('.cashback_type').show();
                $('.cashback1').show();
                $('.total_emi1').hide();
                $('.finance_emi1').hide();
                $('.down_emi1').hide();
                $('.emi_amount1').hide();
                $('.rate_interest').hide();
                $('.mop_interest').hide();
                
                $('#finance_emi1').attr('required', false);
                $('#total_emi1').attr('required', false);
                
            }
   });
        
    
</script>
<style>
.fixheader {
    background-color: #fbf7c0;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 999;
}
.fixheader1 {
    background-color: #fbf7c0;
    position: sticky;
    top: 28px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 999;
}
/*.fixonleft {
    position: fixed;
    bottom: 120px;
    right: 80px; 
}
.fixonright {
    position: fixed;
    bottom: 120px;
    left: 250px; 
}*/

.chosen-container {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    font-size: 13px;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    width: 183px !important;
}
</style>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span>Finance Scheme Portal</h3>
    </center>
</div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="overflow: auto">
    
    <div class="col-md-2"><b>Scheme</b></div>
    <div class="col-md-2" style="padding: 5px">
        <select class="chosen-select form-control input-sm" id="finance_scheme" name="finance_scheme" required="" >
            <?php if($finance_data->scheme == 0) { ?>
                <option value="4">Finance</option>
            <?php }else{ ?>
                <option value="3">Swipe</option>
            <?php } ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <?php if($scheme == 0){  ?>
        <form>
            <div class="col-md-2"><b>From</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" value="<?php echo $finance_data->from_date; ?>" required">
            </div>
            <div class="col-md-2"><b>To</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" value="<?php echo $finance_data->to_date; ?>" required">
            </div>
            <div class="clearfix"></div>
            <div class="col-md-2"><b>Brand</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="form-control input-sm" name="idbrand" id="brand" required="">
                    <option value="<?php echo $finance_data->idbrand; ?>"><?php  echo $finance_data->brand_name; ?></option>
                </select>
            </div>
            <div class="col-md-2"><b>Model</b></div>
            <div class="col-md-2 " id="model_list" style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="idmodel" id="model">
                    <option value="<?php echo $finance_data->idvariant; ?>"><?php  echo $finance_data->full_name; ?></option>
                </select>
            </div>
             <div class="col-md-2"><b>Finance MOP</b></div>
            <div class="col-md-2 " style="padding: 5px">
                <input type="text" name="finance_mop" id="finance_mop" value="<?php echo $finance_data->finance_mop?>" class="form-control" readonly>
            </div>
            <div class="col-md-2"><b>Finance Provider</b></div>
            <div class="col-md-2 " id="finance_provider" style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="idpaymentmode" id="idpaymentmode">
                    <option value="<?php echo $finance_data->idpayment_mode?>"><?php echo $finance_data->payment_mode?></option>
                    <?php foreach ($payment_mode as $pmode){ ?>
                    <option value="<?php echo $pmode->id_paymentmode?>"><?php echo $pmode->payment_mode; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2"><b>Finance Scheme Type</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="chosen-select form-control input-sm finance_scheme_type" id="finance_scheme_type" name="finance_scheme_type" required="">
                      <option value="<?php echo $finance_data->finance_scheme_type?>"><?php if($finance_data->finance_scheme_type == 0){ echo 'Non Vanilla';}else{ echo 'Vanilla';}?></option>
                </select>
            </div>
           
            <div class="col-md-2"><b>Total EMI</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="total_emi" id="total_emi" class="form-control " value="<?php echo $finance_data->total_emi?>" required="">
            </div>
            <div class="col-md-2"><b>Downpayment EMI</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="down_emi" id="down_emi" class="form-control " value="<?php echo $finance_data->emi_downpayment?>" required="">
            </div>
            <div class="col-md-2"><b>Finance EMI</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="finance_emi" id="finance_emi" value="<?php echo $finance_data->emi_finance?>" class="form-control" required="">
            </div>

            <div class="col-md-2"><b>Scheme Code</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="scheme_code" id="scheme_code" class="form-control" value="<?php echo $finance_data->scheme_code?>"  required="">
            </div>
            <div class="col-md-2"><b>EMI Amount</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="emi_amount" id="emi_amount" value="<?php echo $finance_data->emi_amount?>" class="form-control ">
            </div>
            <div class="col-md-2"><b>Finance EMI Amount</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="finance_emi_amount" id="finance_emi_amount" value="<?php echo $finance_data->finance_amount?>" class="form-control ">
            </div>
            <div class="col-md-2"><b>Downpayment EMI Amount</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="down_emi_amount" id="down_emi_amount" value="<?php echo $finance_data->downpayment_amount?>" class="form-control ">
            </div>
             <div class="col-md-2 finance_interest"><b>Rate Of Interest</b></div>
            <div class="col-md-2 finance_interest"  style="padding: 5px">
                <input type="text" name="finance_interest_rate" id="finance_interest_rate" class="form-control finance_interest_rate" value="<?php echo $finance_data->rate_of_interest ?>">
            </div>
            <div class="col-md-2"><b>Processing Fee</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="processng_fee" id="processng_fee" class="form-control processng_fee" value="<?php echo $finance_data->processing_fee ?>">
            </div>
            <div class="col-md-2"><b>Final Downpayment Amount</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="final_down_emi_amount" id="final_down_emi_amount" class="form-control final_down_emi_amount" value="<?php echo $finance_data->final_downpayment_amount ?>">
            </div>
            <div class="col-md-2"><b>Cashback</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <input type="text" name="cashback" id="cashback"  value="<?php echo $finance_data->cashback_amount?>" class="form-control " value="0">
            </div>
            <div class="clearfix"></div><hr>
            <input type="hidden" name="scheme_type" id="scheme_type" value="<?php echo $scheme ?> ">
            <input type="hidden" name="entry_time" id="scheme_type1" value="<?php echo $finance_data->entry_time ?> ">
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Finance_scheme/update_finance_scheme_data">Submit</button>
        </form>
    <?php } ?>
    <div class="clearfix"></div>
     <?php if($scheme == 1){  ?>
        <form>
            <div class="col-md-2"><b>Scheme Type</b></div>
            <div class="col-md-2 " style="padding: 5px">
                 <select class="chosen-select form-control input-sm" name="swipe_scheme" id="swipe_scheme">
                     <option value="<?php echo $finance_data->scheme_type ?>"><?php if($finance_data->scheme_type == 0){ echo 'Finance Scheme';}elseif ($finance_data->scheme_type == 1){ echo 'Brand Scheme'; }elseif ($finance_data->scheme_type == 2){ echo 'Bank Scheme'; }else{ echo 'Cashback Scheme';}?></option>
                    <option value="1">Brand Scheme</option>
                    <option value="2">Bank Scheme</option>
                    <option value="3">Cashback Scheme</option>
                </select>
            </div>
            <div class="col-md-2"><b>From</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" value="<?php echo $finance_data->from_date ?>" id="from" name="from" required">
            </div>
            <div class="col-md-2"><b>To</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" value="<?php echo $finance_data->to_date ?>" required">
            </div>
            <div class="clearfix"></div>
            <div class="col-md-2"><b>Brand</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="chosen-select form-control input-sm " name="idbrand" id="idbrand" required="">
                    <option value="<?php echo $finance_data->idbrand; ?>"><?php echo $finance_data->brand_name; ?></option>
                </select>
            </div>
            <div class="col-md-2"><b>Model</b></div>
            <div class="col-md-2 " id="model_list1" style="padding: 5px">
                <select class="chosen-select form-control input-sm " name="idmodel" id="idmodel">
                    <option value="<?php echo $finance_data->idvariant; ?>"><?php echo $finance_data->full_name; ?></option>
                </select>
            </div>
            <div class="col-md-2"><b>Finance Provider</b></div>
            <div class="col-md-2" id="finance_provider1" style="padding: 5px">
                <select class="chosen-select form-control input-sm idpaymentmode" name="idpaymentmode" id="idpaymentmode">
                    <option value="<?php echo $finance_data->idpayment_mode?>"><?php echo $finance_data->payment_mode?></option>
                    <?php foreach ($payment_mode as $pmode){ ?>
                    <option value="<?php echo $pmode->id_paymentmode?>"><?php echo $pmode->payment_mode; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2"><b>Finance MOP</b></div>
            <div class="col-md-2 " style="padding: 5px">
                <input type="text" name="finance_mop" id="finance_mop1" value="<?php echo $finance_data->finance_mop?>" class="form-control " required="">
            </div>
            
            <div class="col-md-2"><b>Type Of Card</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="type_of_card" id="type_of_card">
                    <option value="<?php echo $finance_data->type_of_card ?>"><?php if($finance_data->type_of_card == 0){ echo 'dc'; } if($finance_data->type_of_card == 1){ echo 'cc'; } ?></option>
                    <option value="0">dc</option>
                    <option value="1">cc</option>
                </select>
            </div>
            
            <div class="col-md-2"><b>Bank</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="bank" id="bank">
                    <option value="<?php echo $finance_data->idbank?>"><?php echo $finance_data->bank_name; ?></option>
                    <?php foreach ($bank_data as $bank){ ?>
                    <option value="<?php echo $bank->id_bank?>"><?php echo $bank->bank_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div>
            <div class="total_emi1">
                <div class="col-md-2"><b>Total EMI</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="total_emi" id="total_emi1" value="<?php echo $finance_data->total_emi; ?>" class="form-control " required="">
                </div>
            </div>
            <div class="finance_emi1" >
                <div class="col-md-2"><b>Finance EMI</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="finance_emi" id="finance_emi1" value="<?php echo $finance_data->emi_finance; ?>" class="form-control" required="">
                </div>
            </div>
            <div class="down_emi1" style="">
                <div class="col-md-2"><b>Downpayment EMI</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="down_emi" id="down_emi1" class="form-control " value="<?php echo $finance_data->emi_downpayment; ?>" value="0" readonly="" >
                </div>
            </div>
            <div class="rate_interest" style="">
                <div class="col-md-2"><b>Rate Of Interest</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="rate_interest" id="rate_interest" value="<?php echo $finance_data->rate_of_interest; ?>" class="form-control ">
                </div>
            </div>
             <div class="mop_interest" style="display: none">
                <div class="col-md-2"><b>MOP with Interest</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="mop_interest" id="mop_interest" value="<?php echo $finance_data->interest_mop?>" class="form-control ">
                </div>
            </div>
            <div class="emi_amount1" style="">
                <div class="col-md-2"><b>EMI Amount</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="emi_amount" id="emi_amount1" value="<?php echo $finance_data->emi_amount ?>" class="form-control ">
                    <input type="hidden" name="finance_emi_amount" id="finance_emi_amount1" value="<?php echo $finance_data->finance_amount ?>" class="form-control ">
                </div>
            </div>
            <div class="cashback_type" style="">
                <div class="col-md-2"><b>Cashback Type</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                     <select class="form-control input-sm" name="cashback_type" id="cashback_type">
                        <option value="<?php echo $finance_data->cashback_type ?>"><?php if($finance_data->cashback_type == 0){ echo 'Instant'; } if($finance_data->cashback_type == 1){ echo '90 Days';}?></option>
                        <option value="0">Instante</option>
                        <option value="1">90 Days</option>
                    </select>
                </div>
            </div>
            <div class="cashback1" style="">
                <div class="col-md-2"><b>Cashback</b></div>
                <div class="col-md-2 "  style="padding: 5px">
                    <input type="text" name="cashback" id="cashback1" value="<?php echo $finance_data->cashback_amount; ?>" class="form-control ">
                </div>
            </div>
               
            <div class="clearfix"></div><hr>
            <input type="hidden" name="scheme_type" id="scheme_type1" value="<?php echo $scheme ?> ">
            <input type="hidden" name="entry_time" id="scheme_type1" value="<?php echo $finance_data->entry_time ?> ">
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Finance_scheme/update_finance_scheme_data">Submit</button>
        </form>
     <?php } ?>
    <div class="clearfix"></div><br>
   
    <div class="clearfix"></div><br>
    
    
</div>
<?php } include __DIR__.'../../footer.php'; ?>