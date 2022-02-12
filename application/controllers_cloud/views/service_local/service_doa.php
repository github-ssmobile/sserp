<?php include __DIR__ . '../../header.php'; ?>

<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    var products = [], checked;
    $(document).on('keydown', 'input[id=invno]', function(e) {
        var invno = $(this).val();
        var imei = 0
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/search_invoice_byimei",
                method:"POST",
                data:{imei:imei,invno : invno, branch: branch, level: level},
                success:function(data)
                {
                    products = [];
                    checked = 0;
                    $("#invoice_data").html(data);                   
                     $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
    $(document).on('keydown', 'input[id=imei]', function(e) {
        var imei = $(this).val();
        var invno=0;
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/search_invoice_byimei",
                method:"POST",
                data:{invno:invno, imei : imei, branch: branch, level: level},
                success:function(data)
                {
                    products = [];
                    checked = 0;
                    $("#invoice_data").html(data);
                     $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
     $(document).on('click', '#btn_letter', function () {      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=1;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/product_replacement_form",
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
     $(document).on('click', '#btn_force_doa', function () {      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=3;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/product_replacement_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                        $(".doa_type").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});
                        $("#doa_return_type").val("3");
                    }
                });
            
        });
        
     $(document).on('click', '#btn_new_handset', function () {    
     
                var html='<div class="doatype"><div class="col-md-12">'+
                            '<center><h5 style="margin-top: 0"> What Customer wants ??</h5></center><br>'+
                            '</div><div class="clearfix"></div><div class="col-md-4"></div><div class="col-md-2">'+
                            '<button type="button" title="Wants to upgrade to new hansdet" class="btn  btn-primary waves-effect waves-light handset_upgrade" id="handset_upgrade" ><span class="mdi mdi-cellphone-android fa-lg"></span> Upgrade </button>'+
                            '</div>'+
                            '<div class="col-md-2">'+
                            '<button type="button" title="Wants same handset received from care center" class="btn  btn-primary waves-effect waves-light handset_n_upgrade" id="handset_n_upgrade" ><span class="mdi mdi-cellphone-android fa-lg"></span> No Upgrade </button>'+
                            '</div><div class="clearfix"></div><br></div>';
                
                $(".dynamic_form_new_handset").html(html);                        
                $(".dynamic_form_new_handset").fadeIn();
                 $(".doa_type").fadeOut();
               
                
        });
        $(document).on('click', '#handset_upgrade', function () {    
      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=2;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/product_replacement_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                        $(".doatype").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});
                        $("#doa_return_type").val("2");
                        $(".new_product").hide();
                    }
                });
            
        });
     $(document).on('click', '#handset_n_upgrade', function () {    
      
                var total_selected_sum = $('#selected_total_amount').val();
                var doa_return_type=2;
                $.ajax({
                    url: "<?php echo base_url() ?>Service/product_noupgrade_form",
                    method: "POST",
                     data:{total_selected_sum:total_selected_sum,doa_return_type:doa_return_type},
                    success: function (data)
                    {
                        $(".dynamic_form").html(data);                        
                        $(".dynamic_form").fadeIn();
                        $(".doatype").fadeOut();
                        $(".chosen-select").chosen({search_contains: true});   
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
    var products = [], count=0;
    $(document).on('change', '#skuvariant', function(e) {
        var skuvariant = $(this).val();
        var idbranch = $('#idbranch').val();
        var idgodown = $('#idgodown').val();
        var is_dcprint = $('#dcprint').val();
        if(skuvariant != ''){
            if (products.includes(skuvariant) === false){
                $.ajax({
                    url: "<?php echo base_url() ?>Sales_return/ajax_get_imei_details",
                    method: "POST",
                    data:{skuvariant : skuvariant,idbranch: idbranch, idgodown: idgodown, is_dcprint: is_dcprint},
                    success: function (data)
                    {
                        if(data == 0){
                            swal("Product not found in branch!", "Check actual location of product");
                            return false;
                        }else if(data == 1){  // 'if dc_product'
                            swal("Not allowed to select product!", "Selected product having 'Invoice type'");
                            return false;
                        }else if(data == 2){ // 'if invoice_product';
                            swal("Not allowed to select product!", "Selected product having 'Delivery challan type'");
                            return false;
                        }else{
                            products.push(skuvariant);
                            $("#product").show();
                            $("#product_data").append(data);
                            $('#img_scanner').hide();
                            $('#modelid').val(products);
                            $('#gross_total').val(price);
                            $('#spgross_total').html(price);
                            $('#final_total').val(price);
                            $('#spfinal_total').html(price);
                            count++;
                            $('input[id=qty]').keyup();
                            $('.doa').val($('.doa_id').val());
                        }
                    }
                });
            }else{
                swal("Duplicate product selected!", "Product already in selected list","warning");
                return false;
            }
        }
    });
    // IMEI enter
    var imeis = [], price = 0;
    $(document).on('keydown', 'input[id=enter_imei]', function(e) {
        var keyCode = e.keyCode || e.which; 
        if (keyCode === 13 && $(this).val() !== '') {
//    $('#enter_imei').change(function(){
        var imei = $(this).val();
        var idbranch = $('#idbranch').val();
        var is_dcprint = $('#dcprint').val();
//        if(imei != ''){
            if (imeis.includes(imei) === false){
            $.ajax({
                url: "<?php echo base_url() ?>Sales_return/ajax_get_imei_details",
                method: "POST",
                data:{imei : imei,idbranch: idbranch, is_dcprint: is_dcprint},
                success: function (data)
                {
                    if(data == 0){
                        swal("Product not found in branch!", "Check actual location of product", "warning");
                        return false;
                    }else if(data == 1){  // 'dc_product'
                        swal("Not allowed to select product!", "Selected product having 'Invoice type'", "warning");
                        return false;
                    }else if(data == 2){ // 'invoice_product';
                        swal("Not allowed to select product!", "Selected product having 'Delivery challan type'", "warning");
                        return false;
                    }else if(data == 3){ // Other that New Godown not accepted
                        swal("Product not found in New Godown!", "Change godown type to New Godown", "warning");
                        return false;
                    }else{
                        imeis.push(imei);
                        $('#enter_imei').val('');
                        $("#product").show();
                        $('#img_scanner').hide();
                        $('#imeiscanned').val(imeis);
                        $("#product_data").append(data);
                        $('#gross_total').val(price);
                        $('#spgross_total').html(price);
                        $('#final_total').val(price);
                        $('#spfinal_total').html(price);
                        $('input[id=qty]').keyup();
                        $('.doa').val($('.doa_id').val());
                        count++;
                    }
                }
            });
            }else{
                swal("Duplicate product selected!", "Product already in selected list","warning");
                return false;
            }
//        }
        }
    });
    
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
    
    // Quantity, Discount
    $(document).on('keyup', 'input[id=qty],input[id=discount_amt],input[id=price]', function() {
        var discount_amt=0, total=0, price=0, qty=0, price=0, basic=0;
        var ce = $(this).closest('td').parent('tr');
        qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
        price = (isNaN(+ce.find(".price").val())) ? 0 : +ce.find(".price").val();
        discount_amt = (isNaN(+ce.find(".discount_amt").val())) ? 0 : +ce.find(".discount_amt").val();
        
        basic = price * qty;
        total = basic - +discount_amt; 
        
        ce.find(".basic").val(basic);
        ce.find(".spbasic").html(basic);
        ce.find(".total_amt").val(total);
        ce.find(".sptotal_amt").html(total);
        var total_basic_sum=0,sum_total_gross_amt=0,sum_discount_amt=0;
        $('.product_tr').each(function () {
            // basic cal
            $(this).find('.basic').each(function () {
                var total_basic = $(this).val();
                if (!isNaN(total_basic) && total_basic.length !== 0) {
                    total_basic_sum += parseFloat(total_basic);
                }
            });
            $('#gross_total').val(total_basic_sum.toFixed(2));
            $('#spgross_total').html(total_basic_sum.toFixed(2));
            // gross total cal
            $(this).find('.total_amt').each(function () {
                var total_gross_amt = $(this).val();
                if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                    sum_total_gross_amt += parseFloat(total_gross_amt);
                }
            });
            $('#final_total').val(sum_total_gross_amt.toFixed(2));
            $('#spfinal_total').html(sum_total_gross_amt.toFixed(2));
            // discount_amt total cal
            $(this).find('.discount_amt').each(function () {
                var discount_amt1 = $(this).val();
                if (!isNaN(discount_amt1) && discount_amt1.length !== 0) {
                    sum_discount_amt += parseFloat(discount_amt1);
                }
            });
            $('#final_discount').val(sum_discount_amt.toFixed(2));
            $('#spfinal_discount').html(sum_discount_amt.toFixed(2));
        });
    });
    
    // Remove product row
    $(document).on('click', 'a[id=remove]', function() {
        var parrent = $(this).closest('td').parent('tr');
        var product_name = parrent.find('.product_name').val();
        var rem_imei = parrent.find('.imei').val();
        var idvariant = parrent.find('.idvariant').val();
        
        var basic = +parrent.find('.basic').val();
        var discount_amt = +parrent.find('.discount_amt').val();
        var total_amt = +parrent.find('.total_amt').val();
        
        var tprice=0,tdiscount_amt=0,ttotal_amt=0;
        swal({
                title: "Want to Remove Product?",
                text: product_name,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#E84848',
                confirmButtonText: 'Yes, Remove it!',
                closeOnConfirm: false,
            },
            function(){
                swal("Removed!", product_name+" Product removed!", "success");
                imeis = jQuery.grep(imeis, function(value) { return value !== rem_imei; }); 
                $('#imeiscanned').val(imeis);
                products = jQuery.grep(products, function(value) { return value !== idvariant; });
                $('#modelid').val(products);

                tprice = (isNaN(basic)) ? 0 : basic;
                tdiscount_amt = (isNaN(discount_amt)) ? 0 : discount_amt;
                ttotal_amt = (isNaN(total_amt)) ? 0 : total_amt;

                var minus_tprice = $('#gross_total').val() - tprice;
                var minus_tdiscount_amt = $('#final_discount').val() - tdiscount_amt;
                var minus_ttotal_amt = $('#total_amt').val() - ttotal_amt;

                $('#gross_total').val(minus_tprice);
                $('#spgross_total').html(minus_tprice);

                $('#final_discount').val(minus_tdiscount_amt);
                $('#spfinal_discount').html(minus_tdiscount_amt);

                $('#final_total').val(minus_ttotal_amt);
                $('#spfinal_total').html(minus_ttotal_amt);

                parrent.remove();
                $('input[id=qty]').keyup();
                count--;
        });
//        if (confirm('Are you sure? You want to remove product: '+product_name)){
//        }
        if(count == 0){
            $("#product").hide();
            $('#img_scanner').show();
        }
    });
    $(document).on("change", ".paymenthead", function (event) {
        var paymenthead = $(this).val();
        var headname = $(this).attr('selected_head');
        if($(this).prop("checked") == true){
            $.ajax({
                url: "<?php echo base_url() ?>Sales_return/ajax_get_payment_mode_data_byidhead",
                method: "POST",
                data:{paymenthead : paymenthead, headname: headname},
                success: function (data)
                {
                    $('.payment_modes').append(data);
                }
            });
        }else{
            if (confirm('Are you sure? You want to remove: '+headname)){
                $('.modes_blockc'+paymenthead).remove();
            }else{
                $(this).prop("checked", true);
            }
        }
    });
    $(document).on("click", "a[id=add_more_payment]", function (event) {
        var parrent = $(this).closest('div').parent('.modes_block');
        var paymenthead = parrent.find('.idpaymenthead').val();
        var headname = parrent.find('.headname').val();
        $.ajax({
            url: "<?php echo base_url() ?>Sales_return/ajax_get_payment_mode_data_byidhead",
            method: "POST",
            data:{paymenthead : paymenthead, headname: headname},
            success: function (data)
            {
                $('.payment_modes').append(data);
            }
        });
        $(this).closest('div').html('<center>Remove<br><a class="btn btn-danger btn-floating waves-effect remove_payment" id="remove_payment" style=""><i class="fa fa-minus"></i></a></center>');
        $(this).remove();
    });
    $(document).on("click", "a[id=remove_payment]", function (event) {
        if (confirm('Are you sure? You want to remove payment mode')){
            $(this).closest('div').parent('.modes_block').remove();
        }
    });
    $(document).on("click", "#invoice_submit", function (event) {
//         event.preventDefault();
        if(count == 0){
            swal("Product not added!", "Scan IMEI/ SRNO or Select product");
            return false;
        }else{
            var final_total= +$("input[name='final_total']").val();
            var total_amts=0;
            var amts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
            var i=0;
            var total = count_arr(amts);
            for(i=0;i<total;i++){
                total_amts += parseFloat(amts[i]);
            }
            var remaining = total_amts - final_total;
            var remaining1 = final_total - total_amts;
            if(total_amts>final_total){
                swal("Entered payment amount is greater!", "😠 Payment amount is greater than invoice amount!! "+remaining);
                return false;
            }else if(total_amts<final_total){
                swal("Entered payment amount is less!", "😠 Payment amount is less than invoice amount!! " +remaining1);
//                alert("😠 Payment amount is less than invoice amount!! You entered Less Amount " +remaining1);
                return false;
            }else{       
                if(isset)
                    if (confirm('Do you want to proceed with DOA Return?!!')) {
                        return true;
                    }else{
                        return false;
                    }
                }
            function count_arr(array){ var c = 0; for(i in array) if(array[i] != undefined) c++; return c;}
        }
    });
    // BFL integration
    $(document).on("change", ".tranxid, .payment_type", function (event) {
        var parent = $(this).closest('div').parent('.modes_block');
        var payment_mode = parent.find('.payment_type').val();
        var sfid = parent.find('.tranxid').val();
        var bfl_store_id = $('#bfl_store_id').val();
        if(payment_mode === '4' && sfid !== ''){
            $('#bfl_form').show();
            $.ajax({
                url:"<?php echo base_url('Sale/bfl_test') ?>",
                method:"POST",
                data:{payment_mode : payment_mode, sfid: sfid, bfl_store_id: bfl_store_id},
                dataType: 'json',
                success:function(data)
                {
                    if(data.ResponseMessage == 'success'){
                        var downscheme = data.DoDetails.CustomerDownPayment / data.DoDetails.TotalEMI;
                        var loanscheme = data.DoDetails.NetLoanAmount / data.DoDetails.TotalEMI;
                        var bfl_form = '<div class="col-md-10 col-md-offset-1" style="font-size: 14px">'
                            +'<div class="thumbnail" style="padding: 10px; margin: 10px 0">'
                                +'<center><h4>Bajaj Finance Limited</h4><u>Delivery Order</u></center>'
                                +'Dear, <b>IPalace <?php echo $_SESSION['branch_name'] ?></b> <span class="pull-right">Date: <b><?php echo date('d/m/Y h:i:s A'); ?></b></span><br>'
                                +'<p> &nbsp; &nbsp; &nbsp; &nbsp; We are pleased to inform you that the loan application of Mr/Miss/Mrs. <b>'+ data.DoDetails.CustomerName +'</b></p><hr>'
                                +'<div class="thumbnail" style="overflow: auto; padding: 0">'
                                    +'<table class="table table-bordered table-condensed table-striped" style="margin: 0">'
                                        +'<tbody>'
                                            +'<tr>'
                                                +'<td>DO ID:</td>'
                                                +'<td>'+ data.DoDetails.DONumber +'<input type="hidden" name="bfl_do_id" value="'+ data.DoDetails.DONumber +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Customer Name</td>'
                                                +'<td>'+ data.DoDetails.CustomerName +'<input type="hidden" name="bfl_customer" value="'+ data.DoDetails.CustomerName +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Mobile No</td>'
                                                +'<td>'+ data.DoDetails.CustomerPhoneNo +'<input type="hidden" name="bfl_mobile" value="'+ data.DoDetails.CustomerPhoneNo +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Brand</td>'
                                                +'<td>'+ data.DoDetails.ManufacturerName +'<input type="hidden" name="bfl_brand" value="'+ data.DoDetails.ManufacturerName +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Model</td>'
                                               +'<td>'+ data.DoDetails.ModelNo +'<input type="hidden" name="bfl_model" value="'+ data.DoDetails.ModelNo +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>IMEI/ SRNO</td>'
                                                +'<td>'+ data.DoDetails.SerialNo +'<input type="hidden" name="bfl_srno" value="'+ data.DoDetails.SerialNo +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Scheme Code (GT/AE)</td>'
                                                +'<td>'+ data.DoDetails.SchemeId+ ' ('+loanscheme+'/'+downscheme+')' +'<input type="hidden" name="scheme_code" value="'+ data.DoDetails.SchemeId +'" /><input type="hidden" name="scheme" value="('+loanscheme+'/'+downscheme+')" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>MOP</td>'
                                                +'<td>'+ data.DoDetails.InvoiceAmount +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_mop" value="'+ data.DoDetails.InvoiceAmount +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>DownPayment</td>'
                                                +'<td>'+ data.DoDetails.CustomerDownPayment +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_downpayment" value="'+ data.DoDetails.CustomerDownPayment +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Loan</td>'
                                                +'<td>'+ data.DoDetails.NetLoanAmount +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_loan" value="'+ data.DoDetails.NetLoanAmount +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Finance ID</td>'
                                                +'<td>'+ data.DoDetails.DONumber +'</td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>EMI Amount</td>'
                                                +'<td>'+ data.DoDetails.TotalEMI +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_emi_amount" value="'+ data.DoDetails.TotalEMI +'" /></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Tenure</td>'
                                                +'<td>'+ data.DoDetails.Tenure +'<input type="hidden" name="bfl_tenure" value="'+ data.DoDetails.Tenure +'" /></td>'
                                            +'</tr>'
                                        +'</tbody>'
                                    +'</table>'
                                +'</div>'
                                +'<div class="col-md-2 col-sm-3 col-xs-4">BFL Remark</div>'
                                +'<div class="col-md-10 col-sm-9 col-xs-8"><input type="text" class="form-control input-sm" name="bfl_remark" placeholder="Enter Remark of finance" /></div><div class="clearfix"></div>'
                            +'</div>'
                        +'</div><div class="clearfix"></div>';
                        parent.find('.amount').val(data.DoDetails.NetLoanAmount);
                        $('#bfl_form').html(bfl_form);

                        $('#mobile').val(data.DoDetails.CustomerPhoneNo);
                        $('#customer').val(data.DoDetails.CustomerName);
                        $('#gst_no').val(data.DoDetails.customerGSTIN);
                        $('#address').val(data.DoDetails.AddressLine1+', '+data.DoDetails.AddressLine2+', '+data.DoDetails.AddressLine3+', '+data.DoDetails.Area+' ,'+data.DoDetails.CITY);
                        $('#id_customer').val('0');
                        $("#cust_state option:selected").text(data.DoDetails.STATE);
                        $('#cust_state').css('pointer-events','none');

                        $('#customer').attr('readonly', true);
                        $('#gst_no').attr('readonly', true);
                        $('#address').attr('readonly', true);
                        $('#mobile').attr('readonly', true);
                    }else{
                        var bfl_form = '<div class="alert alert-danger" id="alert-dismiss"><center><h4 style="padding: 0; margin: 0">No Data found in SFDC for dealId: '+sfid+'</h4></center></div>';
                        $('#bfl_form').html(bfl_form);
                        parent.find('.tranxid').val('');
                        parent.find('.amount').val('0');
                        parent.find('.amount').attr('readonly',false);
                        setTimeout(function() {
                            $('#bfl_form').hide();
                            $('#bfl_form').html('');
                        }, 5000);
                        $('#customer').val('');
                        $('#gst_no').val('');
                        $('#mobile').val('');
                        $('#address').val('');
                        $('#id_customer').val('0');
                        $("#customer").removeAttr('readonly');
                        $("#gst_no").removeAttr('readonly');
                        $("#address").removeAttr('readonly');
                        $("#mobile").removeAttr('readonly');
                        $('#cust_state').css('pointer-events','');
                    }
                }
            });
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
            <form class="inv_form">
        <center><h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> DOA RETURN </h3></center><br>
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
            <div class="col-md-12">
                <div class="col-md-4 pull-right">
                    <div class="thumbnail text-center" style="padding: 0px">
                        <h4 style="padding: 0px 10px">Products Amount: <spna id="selected_total_amountlb"><?php echo $service_data[0]->sold_amount; ?></spna> <i class="fa fa-rupee"></i></h4>
                        <input type="hidden" id="selected_total_amount" name="selected_total_amount" value="<?php echo $service_data[0]->sold_amount; ?>">                
                    </div><div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="doa_type">
                <div class="col-md-12">
                    <center><h5 style="margin-top: 0"> Select DOA Type - based on what we have revceived from care centor</h5></center><br>
                </div><div class="clearfix"></div>
                <div class="col-md-3"></div>
                <div class="col-md-2">
                    <button type="button" title="Received DOA letter" class="btn  btn-primary waves-effect waves-light btn_letter" id="btn_letter" ><span class="mdi mdi-cellphone-android fa-lg"></span> DOA Letter </button>
                </div>
                <div class="col-md-2">
                    <button type="button" title="Received new handset" class="btn  btn-primary waves-effect waves-light btn_new_handset" id="btn_new_handset" ><span class="mdi mdi-cellphone-android fa-lg"></span> New Handset </button>
                </div>
                <div class="col-md-2">
                    <button type="button" title="Nothing received yet- Force DOA" class="btn  btn-primary waves-effect waves-light btn_force_doa" id="btn_force_doa" ><span class="mdi mdi-cellphone-android fa-lg"></span> Force DOA </button>
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
                
                
                <?php if($service_data[0]->erp_type==0){
                    
                    ?>
                    <input type="hidden" name="price<?php echo $service_data[0]->id_service; ?>" id="price" value="<?php echo $sale->total_amount_per_qty ?>" />                            
                    <input type="hidden" name="cust_fname" value="<?php echo $service_data[0]->customer_name ?>" />
                    <input type="hidden" name="cust_lname" value="" />
                    <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst_no ?>" />  
                    <input type="hidden" id="selected_total_discount" name="selected_total_discount" value="0" />
                    <input type="hidden" id="selected_total_basic" name="selected_total_basic" value="<?php echo $service_data[0]->sold_amount; ?>" />
                    <?php $cgst=($sale->gst_rate/2);
                            $tax = $sale->cgst + $sale->cgst + $sale->igst; 
                            ?>
                    <input type="hidden" name="cgst_per" id="cgst_per" value="<?php echo $cgst ?>" />
                    <input type="hidden" name="sgst_per" id="sgst_per" value="<?php echo $cgst ?>" />
                    <input type="hidden" name="igst_per" id="igst_per" value="<?php echo $sale->gst_rate;?>" />
                    <input type="hidden" name="taxable_amt" id="taxable_amt" value="<?php echo $sale->base_price;?>" />
                    <input type="hidden" name="cgst_amt" id="cgst_amt" value="<?php echo $sale->cgst;?>" />
                    <input type="hidden" name="sgst_amt" id="sgst_amt" value="<?php echo $sale->sgst;?>" />
                    <input type="hidden" name="igst_amt" id="igst_amt" value="<?php echo $sale->igst;?>" />     
                    <input type="hidden" name="tax" id="tax" value="<?php echo $tax; ?>" />
                    <input type="hidden" id="is_gst" value="" />
                    <input type="hidden" id="idvendor" value="0" />
                    <input type="hidden" id="old_landing" name="old_landing" value="<?php echo $sale->manager_price;?>" />
                        
                <?php }elseif($service_data[0]->erp_type==1){  
                        $taxable_total=0;
                             if($sale->gst_type){
                                // igst
                                $cal = ($sale->igst_per + 100) / 100;
                                $taxable = $sale->total_amount / $cal;
                                $taxable_total += $taxable;
                                $igstamt = $sale->total_amount - $taxable;
                                $cgstamt=0;
                               
                            }else{
                                $cal = ($sale->cgst_per + $sale->sgst_per + 100) / 100;
                                $taxable = $sale->total_amount / $cal;
                                $taxable_total += $taxable;
                                $cgst = $sale->total_amount - $taxable;
                                $cgstamt = $cgst / 2;
                                $igstamt=0;
                                
                            }                    
                            $tax = $cgstamt + $cgstamt + $igstamt; 
                     ?>    
                    <input type="hidden" id="is_gst" value="<?php echo $sale->is_gst ?>" />
                    <input type="hidden" id="idvendor" value="<?php echo $sale->idvendor ?>" />
                    <input type="hidden" name="price<?php echo $service_data[0]->id_service; ?>" id="price" value="<?php echo $sale->price ?>" />    
                    <input type="hidden" name="tax" id="tax" value="<?php echo $tax; ?>" />
                    <input type="hidden" name="cgst_per" id="cgst_per" value="<?php echo $sale->cgst_per; ?>" />
                    <input type="hidden" name="sgst_per" id="sgst_per" value="<?php echo $sale->sgst_per; ?>" />
                    <input type="hidden" name="igst_per" id="igst_per" value="<?php echo $sale->igst_per; ?>" />
                    <input type="hidden" name="taxable_amt" id="taxable_amt" value="<?php echo $taxable_total ;?>" />
                    <input type="hidden" name="cgst_amt" id="cgst_amt" value="<?php echo $cgstamt ;?>" />
                    <input type="hidden" name="sgst_amt" id="sgst_amt" value="<?php echo $cgstamt ;?>" />
                    <input type="hidden" name="igst_amt" id="igst_amt" value="<?php echo $igstamt ;?>" /> 
                    
                    <input type="hidden" name="cust_fname" value="<?php echo $sale->customer_fname ?>" />
                    <input type="hidden" name="cust_lname" value="<?php echo $sale->customer_lname ?>" />                    
                    <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst ?>" />    
                    <input type="hidden" id="selected_total_discount" name="selected_total_discount" value="<?php echo $sale->discount_amt ?>" />
                    <input type="hidden" id="selected_total_basic" name="selected_total_basic" value="<?php echo $sale->basic ?>" />                    
                    <input type="hidden" id="old_landing" name="old_landing" value="<?php echo $sale->landing;?>" />
                <?php } ?>
            
            <div class="dynamic_form_new_handset" style="display: none;">            
               
            </div>        
            <div class="dynamic_form" style="display: none;">            
               
            </div>
             </form>
        </div>
   
    </div>
    
        
<?php   include __DIR__ . '../../footer.php'; ?>
