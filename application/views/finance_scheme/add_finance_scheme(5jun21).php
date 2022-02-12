<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        $(document).on('change', '#schemetypes', function() {          
            var idhead = +$('#schemetypes').val();    
            
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_get_payment_mode_byidhead_edit",
                method:"POST",
                data:{idhead : idhead},
                success:function(data)
                {
                    $("#paymentmode").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
        
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
                    $(".finance_mop").val(mop);
                }
            });
        });
        
         $(document).on('change', '.down_emi,.finance_emi,.total_emi', function() {     
            var parentdiv = $(this).closest('div').parent('div');
            var total_emi = +parentdiv.find('.total_emi').val();  
            var mop = +$('.finance_mop').val(); 
            var fin_sch_type = $('.finance_scheme_type').val(); 
            var fin_pro = $('#idpaymentmode').val(); 
            var down_emi = (isNaN(+parentdiv.find(".down_emi").val())) ? 0 : +parentdiv.find(".down_emi").val();
            if(fin_sch_type !='' && fin_pro != '' && mop != ''){
            
                if( down_emi >= total_emi){
                    var em=0;
                    alert("Downpayment Emi Should be Less Than "+total_emi);
                    +parentdiv.find(".down_emi").val(em);
                    down_emi = 0;
                }

                var femi = total_emi - down_emi;
                parentdiv.find('.finance_emi').val(femi);     
                var finance_emi = +parentdiv.find('.finance_emi').val();    
                var sche_code = '';
                var emi = 0;
                var fin_emi=0;
                var down = 0;

                 var proce_fee = +parentdiv.find('.processng_fee').val();    
                var interest_rate = (isNaN(+parentdiv.find(".finance_interest_rate").val())) ? 0 : +parentdiv.find(".finance_interest_rate").val();
                 var interest_amount = 0;
                if(interest_rate > 0 ){
                    interest_amount = (mop * interest_rate)/100;
                }

                if(total_emi != '' && finance_emi !='' && down_emi >='0' && mop !=''){
                    var tot = finance_emi + down_emi;
                    if(tot == total_emi){
                        sche_code = total_emi+'/'+down_emi;
                        emi =  mop/total_emi;
                        fin_emi = emi*finance_emi;
                        down = emi * down_emi;

                        parentdiv.find('.scheme_code').val(sche_code);
                        parentdiv.find('.emi_amount').val(Math.round(emi));
                        parentdiv.find('.finance_emi_amount').val(Math.round(fin_emi));
                        parentdiv.find('.down_emi_amount').val(Math.round(down));
                        var final = down + interest_amount + proce_fee;
                        parentdiv.find('.final_down_emi_amount').val(final);
                    }else{
                        alert('sum of Finance EMI and Downpayment EMI is equal to ' +total_emi);
                        return false;
                    }
                }else{
                    alert("Select Model Data");
                    return false;
                }
            }else{
                alert("Select Data properly!");
                +parentdiv.find('.total_emi').val('0');  
                return false;
                
            }
        });
        $(document).on('change', '.finance_scheme_type', function() {          
            var type = $('.finance_scheme_type').val(); 
            if(type == '0'){
                $('.finance_interest').hide();
            }
            if(type == '1'){
                $('.finance_interest').show();
            }
       });
       //interest rate cal
        $(document).on('change', '.finance_interest_rate,.processng_fee', function() {   
            var mop = +$('.finance_mop').val();    
            var parentdiv = $(this).closest('div').parent('div');
            var interest_rate = (isNaN(+parentdiv.find(".finance_interest_rate").val())) ? 0 : +parentdiv.find(".finance_interest_rate").val();
            var fee = (isNaN(+parentdiv.find(".processng_fee").val())) ? 0 : +parentdiv.find(".processng_fee").val();
            var down_emi_amount = +parentdiv.find('.down_emi_amount').val(); 
            var interest_amount = 0;
            if(interest_rate > 0 ){
                interest_amount = (mop * interest_rate)/100;
            }
            var final = down_emi_amount + interest_amount + fee;
           parentdiv.find('.final_down_emi_amount').val(final);
       });
       
       $(document).on('click', '.add_finance_scheme', function() {
            var myparent = '<div><div class="col-md-2 pull-right">\n\
                            <a class="btn btn-floating btn-danger btn-sm pull-right waves-effect waves-light rem_finance_scheme" ><i class="fa fa-minus" style="padding-right: 20px;margin-top: -5px;"></i></a>\n\
                        </div><div class="clearfix"></div> ';
            myparent += $(".append_finance").find('div').html();
            myparent += '</div>';
                
            $(".append_finance").append(myparent);
        });
        $(document).on('click', '.rem_finance_scheme', function() {
            $(this).closest('div').parent('div').remove();
        });
        
        
        //*************** SWAP *******************
        
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
        
         $(document).on('change', '#idvariant', function() {          
            var idvariant = +$('#idvariant').val();    
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
            if(type == '1'){
                $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                 $('.rate_interest').hide();
                 $('.mop_interest').hide();
                 $('.cashback_type').hide();
                $('.cashback1').hide();
                
            }
            if(type == '2'){
                 $('.total_emi1').show();
                $('.finance_emi1').show();
                $('.down_emi1').show();
                $('.emi_amount1').show();
                $('.rate_interest').show();
                $('.mop_interest').show();
                  $('.cashback_type').hide();
                $('.cashback1').hide();
            }
            if(type == '3'){
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
            if(type ==''){
                $('.cashback_type').hide();
                $('.cashback1').hide();
                $('.total_emi1').hide();
                $('.finance_emi1').hide();
                $('.down_emi1').hide();
                $('.emi_amount1').hide();
                $('.rate_interest').hide();
                $('.mop_interest').hide();
            }
        });
        
        $(document).on('change', '.total_emi_1', function() {   
//             alert("hi");
            var mop = +$('.finance_mop1').val();    
            var parentdiv = $(this).closest('div').parent('div').parent('div');
            var total_emi = +parentdiv.find('.total_emi_1').val();    
            var emi = 0;
            parentdiv.find('.finance_emi_1').val(total_emi);
            if(mop <= 0){
                alert("Select model data");
                total_emi = 0;
                parentdiv.find('.total_emi_1').val(total_emi);    
                parentdiv.find('.finance_emi_1').val(total_emi);
//                 return false;
            }
                if(total_emi != '' && mop != '' ){
                    emi = mop/total_emi;
                    parentdiv.find('.emi_amount_1').val(Math.round(emi));
                    parentdiv.find('.finance_emi_amount_1').val(Math.round(emi));
                }else{
                    alert("Fill EMI Details");
                    return false;
                }
            
        });
        
        $(document).on('change', '.rate_interest_1', function() {   
            var mop = +$('.finance_mop1').val();    
            var parentdiv = $(this).closest('div').parent('div').parent('div');
            var intrest = +parentdiv.find('.rate_interest_1').val();    
            var total_emi = +parentdiv.find('.total_emi_1').val();  
            var fina_emi = 0;
            if(intrest > 0){
                var interest_amount = (mop/100)*intrest;
                var mopintrest = mop + interest_amount;
                parentdiv.find('.mop_interest_1').val(Math.round(mopintrest));
                fina_emi = (mopintrest/total_emi);
                parentdiv.find('.emi_amount_1').val(Math.round(fina_emi));
                parentdiv.find('.finance_emi_amount_1').val(Math.round(fina_emi));
                
            }else{
                parentdiv.find('.mop_interest_1').val(mop);
            }
           
        });
        
        //Add More Swipe block
         $(document).on('click', '.add_swipe_scheme', function() {
            var myparent = '<div> <div class="col-md-2 pull-right">\n\
                            <a class="btn btn-floating btn-danger btn-sm pull-right waves-effect waves-light rem_swipe_scheme" ><i class="fa fa-minus" style="padding-right: 20px;margin-top: -5px;"></i></a>\n\
                        </div><div class="clearfix"></div>' ;
            myparent += $(".append_swipe").find('div').html();
            myparent += ' </div>';
                
            $(".append_swipe").append(myparent);
        });
        $(document).on('click', '.rem_swipe_scheme', function() {
            $(this).closest('div').parent('div').remove();
        });
        
        
        
        //********** REPORT ***************
          $(document).on('change', '#brandid', function() {          
            var brand = +$('#brandid').val();    
            
            var product_category=0;
            $.ajax({
                url:"<?php echo base_url() ?>Finance_scheme/ajax_modellist_by_brand",
                method:"POST",
                data:{brand : brand,product_category: product_category},
                success:function(data)
                {
                    $("#modellist").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
        
         $('#btnreport').click(function (){
            var from = $('#from_date').val();
//            var to = $('#to_date').val();
            var type = $('#schemetypes').val();    
            var idmode = $('.idpmodel').val();   
            var brand = $('#brandid').val();    
            var idvariant = $('#idvariant').val();    
            if(from != '' && brand != '' && idvariant != '' && type != '' && idmode != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Finance_scheme/ajax_get_finance_scheme_byfilter",
                    method:"POST",
                    data:{from : from, brand: brand, idvariant: idvariant, type: type, idmode: idmode},
                    success:function(data)
                    {
                        $('#financeschemedata').html(data);
                    }
                });
            }else{
                alert("Select Filter Data Properly");
                return false;
            }
        });
        $('.btndelete').click(function (){
           var idfinance = $(this).closest('td').find('#ídfinance').val();
           var entry_time = $(this).closest('td').find('#entry_time').val();
           var all = 0;
            if(!confirm("Do You Want To Delete All Color Variants Entry Of This Model ? ")){
//                all = 0;
//                $.ajax({
//                    url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
//                    method:"POST",
//                    data:{idfinance : idfinance,all: all, entry_time: entry_time},
//                    success:function(data)
//                    {
//                        if(data == '1' || data == 1 ){
//                            alert("Finance Scheme Deleted successfully");
//                            window.location.reload();
//                        }else{
//                            alert("Failed To delete");
//                            return false;
//                        }
//                        
//                    }
//                });
                    return false;
            }else{
                all = 1;
                 $.ajax({
                    url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
                    method:"POST",
                    data:{idfinance : idfinance,all: all, entry_time: entry_time},
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
        
        
    });
    
</script>
<style>
.fixheader {
    background-color: #fbf7c0;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 9;
}
.fixheader1 {
    background-color: #fbf7c0;
    position: sticky;
    top: 28px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 9;
}

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
    <div class="" id="pay" style="border: 1px solid #cccccc;border-radius: 8px;padding: 10px;">
    
    <div class="col-md-2"><b>Scheme</b></div>
    <div class="col-md-2" style="padding: 5px">
        <select class="chosen-select form-control input-sm" id="finance_scheme" name="finance_scheme" required="">
            <option value="">Select Finance Scheme </option>
            <option value="4">Finance</option>
            <option value="3">Swipe</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div id="finance_block" style="display: none">
        <form>
            <div class="col-md-2"><b>From</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required">
            </div>
            <div class="col-md-2"><b>To</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required">
            </div>
            <div class="clearfix"></div>
            <div class="col-md-2"><b>Brand</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="idbrand" id="brand" required="">
                    <option value="">Select Brand</option>            
                    <?php foreach ($brand_data as $brand){ ?>
                    <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2"><b>Model</b></div>
            <div class="col-md-2 " id="model_list" style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="idmodel" id="model">
                    <option value="0">Select Model</option>
                </select>
            </div>
            <div class="col-md-2"><b>Finance MOP</b></div>
            <div class="col-md-2 " style="padding: 5px">
                <input type="text" name="finance_mop" id="finance_mop" class="form-control finance_mop" required="">
            </div>
            <div class="col-md-2"><b>Finance Provider</b></div>
            <div class="col-md-2 " id="finance_provider" style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="idpaymentmode" id="idpaymentmode">
                    <option value="0">Select Finance Provider</option>
                </select>
            </div>
            <div class="col-md-2"><b>Finance Scheme Type</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="chosen-select form-control input-sm finance_scheme_type" id="finance_scheme_type" name="finance_scheme_type" required="">
                    <option value="">Select Finance Scheme Type </option>
                    <option value="0">Non Vanilla</option>
                    <option value="1">Vanilla</option>
                </select>
            </div>
            <div class="clearfix"></div><hr>
            <div class='append_finance'>
                <div>
                    <div class="col-md-2"><b>Total EMI</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="total_emi[]" id="total_emi" class="form-control total_emi" required="">
                    </div>
                    <div class="col-md-2"><b>Downpayment EMI</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="down_emi[]" id="down_emi" class="form-control down_emi" value="0" required="">
                    </div>
                     <div class="col-md-2"><b>Finance EMI</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="finance_emi[]" id="finance_emi" class="form-control finance_emi" required="">
                    </div>
                    <div class="col-md-2"><b>Scheme Code</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="scheme_code[]" id="scheme_code" class="form-control scheme_code" required="">
                    </div>
                    <div class="col-md-2"><b>EMI Amount</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="emi_amount[]" id="emi_amount " class="form-control emi_amount">
                    </div>
                    <div class="col-md-2"><b>Finance EMI Amount</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="finance_emi_amount[]" id="finance_emi_amount" class="form-control finance_emi_amount">
                    </div>
                    <div class="col-md-2"><b>Downpayment EMI Amount</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="down_emi_amount[]" id="down_emi_amount" class="form-control down_emi_amount">
                    </div>
                    <div class="col-md-2 finance_interest"><b>Rate Of Interest</b></div>
                    <div class="col-md-2 finance_interest"  style="padding: 5px">
                        <input type="text" name="finance_interest_rate[]" id="finance_interest_rate" class="form-control finance_interest_rate" value="0">
                    </div>
                    <div class="col-md-2"><b>Processing Fee</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="processng_fee[]" id="processng_fee" class="form-control processng_fee" value="0">
                    </div>
                    <div class="col-md-2"><b>Final Downpayment Amount</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="final_down_emi_amount[]" id="final_down_emi_amount" class="form-control final_down_emi_amount">
                    </div>
                    <div class="col-md-2"><b>Cashback</b></div>
                    <div class="col-md-2 "  style="padding: 5px">
                        <input type="text" name="cashback[]" id="cashback" class="form-control cashback" value="0">
                    </div><div class="clearfix"></div><hr>
                </div>
            </div>
            
            <div class="col-md-2 pull-right">
                <a class="btn btn-floating btn-primary btn-sm pull-right waves-effect waves-light add_finance_scheme" ><i class="fa fa-plus" style="padding-right: 20px;margin-top: -5px;"></i></a>
            </div><div class="clearfix"></div><hr>
            <input type="hidden" name="scheme_type" id="scheme_type">
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Finance_scheme/save_finance_scheme">Submit</button>
        </form>
    </div>
    <div class="clearfix"></div>
    
    
    <!--//********************* SWIPE BLOCK ****************************//-->
    
    <div id="swipe_block" style="display: none">
        <form>
            <div class="col-md-2"><b>Scheme Type</b></div>
            <div class="col-md-2 " style="padding: 5px">
                 <select class="chosen-select form-control input-sm" name="swipe_scheme" id="swipe_scheme">
                    <option value="">Select swipe scheme</option>
                    <option value="1">Brand Scheme</option>
                    <option value="2">Bank Scheme</option>
                    <option value="3">Cashback Scheme</option>
                </select>
            </div>
            <div class="col-md-2"><b>From</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required">
            </div>
            <div class="col-md-2"><b>To</b></div>
            <div class="col-md-2" style="padding: 5px">
                <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required">
            </div>
            <div class="clearfix"></div>
            <div class="col-md-2"><b>Brand</b></div>
            <div class="col-md-2" style="padding: 5px">
                <select class="chosen-select form-control input-sm " name="idbrand" id="idbrand" required="">
                    <option value="">Select Brand</option>            
                    <?php foreach ($brand_data as $brand){ ?>
                    <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2"><b>Model</b></div>
            <div class="col-md-2 " id="model_list1" style="padding: 5px">
                <select class="chosen-select form-control input-sm " name="idmodel" id="idmodel">
                    <option value="">Select Model</option>
                </select>
            </div>
            <div class="col-md-2"><b>Finance MOP</b></div>
            <div class="col-md-2 " style="padding: 5px">
                <input type="text" name="finance_mop" id="finance_mop1" class="form-control finance_mop1" required="">
            </div>
            <div class="col-md-2"><b>Finance Provider</b></div>
            <div class="col-md-2" id="finance_provider1" style="padding: 5px">
                <select class="chosen-select form-control input-sm idpaymentmode" name="idpaymentmode" id="idpaymentmode">
                    <option value="">Select Finance Provider</option>
                </select>
            </div>
            
            <div class="col-md-2"><b>Type Of Card</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="type_of_card" id="type_of_card">
                    <option value="">Select Card Type</option>
                    <option value="0">Debit Card</option>
                    <option value="1">Credit Card</option>
                </select>
            </div>
           <div class="col-md-2"><b>Bank</b></div>
            <div class="col-md-2 "  style="padding: 5px">
                <select class="chosen-select form-control input-sm" name="bank" id="bank">
                    <option value="">Select Bank</option>
                    <?php foreach ($bank_data as $bank){ ?>
                    <option value="<?php echo $bank->id_bank?>"><?php echo $bank->bank_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div><hr>
            <div class="append_swipe">
                <div>
                    <div class="total_emi1" style="display: none">
                        <div class="col-md-2 total_emi1"><b>Total EMI</b></div>
                        <div class="col-md-2 total_emi1"  style="padding: 5px">
                            <input type="text" name="total_emi[]" id="total_emi1" class="form-control total_emi_1" required="">
                        </div>
                    </div>
                    <div class="down_emi1" style="display: none">
                        <div class="col-md-2"><b>Downpayment EMI</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="down_emi[]" id="down_emi1" class="form-control down_emi_1" value="0" readonly="" >
                        </div>
                    </div>
                    <div class="finance_emi1" style="display: none">
                        <div class="col-md-2"><b>Finance EMI</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="finance_emi[]" id="finance_emi1" class="form-control finance_emi_1" required="">
                        </div>
                    </div>
                    <div class="rate_interest" style="display: none">
                        <div class="col-md-2"><b>Rate Of Interest</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="rate_interest[]" id="rate_interest" class="form-control rate_interest_1">
                        </div>
                    </div>
                    <div class="mop_interest" style="display: none">
                        <div class="col-md-2"><b>MOP with Interest</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="mop_interest[]" id="mop_interest" class="form-control mop_interest_1">
                        </div>
                    </div>
                    <div class="emi_amount1" style="display: none">
                        <div class="col-md-2"><b>EMI Amount</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="emi_amount[]" id="emi_amount1" class="form-control emi_amount_1">
                            <input type="hidden" name="finance_emi_amount[]" id="finance_emi_amount1" class="form-control finance_emi_amount_1">
                        </div>
                    </div>
                    <div class="cashback_type" style="display: none">
                        <div class="col-md-2"><b>Cashback Type</b></div>
                        <div class="col-md-2" style="padding: 5px">
                             <select class="form-control input-sm cashback_type" name="cashback_type[]" id="cashback_type">
                                <option value="">Select cashback type</option>
                                <option value="0">Instante</option>
                                <option value="1">90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="cashback1" style="display: none">
                        <div class="col-md-2"><b>Cashback</b></div>
                        <div class="col-md-2 "  style="padding: 5px">
                            <input type="text" name="cashback[]" id="cashback1" class="form-control cashback_1">
                        </div>
                    </div>
                    <div class="clearfix"></div><hr>
                </div>
            </div>
            <div class="col-md-2 pull-right">
                <a class="btn btn-floating btn-primary btn-sm pull-right waves-effect waves-light add_swipe_scheme" ><i class="fa fa-plus" style="padding-right: 20px;margin-top: -5px;"></i></a>
            </div>
            <div class="clearfix"></div><hr>
            <input type="hidden" name="scheme_type" id="scheme_type1">
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Finance_scheme/save_finance_scheme">Submit</button>
        </form>
    </div>
    <div class="clearfix"></div><br>
</div>
    <div class="clearfix"></div><br>
    
    
    <!--//****************REPORT*********************-->
    <input type="hidden" class="form-control" data-provide="datepicker" id="from_date" value="<?php echo date('Y-m-d'); ?>" name="from_date" required">

    <div class="col-md-2" style="padding: 5px"><b>Brand</b>
        <select class="chosen-select form-control input-sm "  id="brandid" required="">
            <option value="">Select Brand</option>            
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2 " id="modellist" style="padding: 5px"><b>Model</b>
        <select class="chosen-select form-control input-sm " name="idvariant" id="idvariant">
            <option value="">Select Model</option>
        </select>
    </div>
    <div class="col-md-2 " style="padding: 5px"><b>Scheme Type</b>
         <select class="chosen-select form-control input-sm" name="schemetypes" id="schemetypes">
            <option value="0">All scheme type</option>
            <option value="4">Finance Scheme</option>
            <option value="3">Swipe Scheme</option>
        </select>
    </div>
    <div class="col-md-2 " style="padding: 5px"><b>Payment Mode</b>
        <div id="paymentmode">
            <select class="chosen-select form-control input-sm idpmodel" name="idpmodel" id="idpmodel">
                <option value="0">All Finance Provider</option>
            </select>
        </div>
    </div>
    <div class="col-md-2 ">
        <br>
        <button class="btn btn-primary" id="btnreport">Filter</button>
    </div>
    <div class="clearfix"></div><br>
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
    <div class="col-md-1"></div>
    <div class="col-md-1 col-sm-2">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('finance_scheme_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="clearfix"></div><br>
    <div style="overflow-x: auto;height: 600px;">
        <div id="financeschemedata"></div>
    </div>
    
</div>

<?php } include __DIR__.'../../footer.php'; ?>