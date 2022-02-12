<?php include __DIR__.'../../header.php'; ?>
<style>
.modes_block:hover{
    background-color: #f4f4f4;
}
.blink {
    animation: blinker 1s linear infinite;
}
@keyframes blinker {
    10% {
        opacity: 0;
    }
}
</style>
<script src="<?php echo site_url('assets/js/autocomplete-jquery-ui.js') ?>"  type="text/javascript"></script>
<?= link_tag("assets/css/autocomplete-jquery-ui.css") ?>
<script>
//window.onload=function() { setTimeout(function(){ $('#myDiv').remove(); }, 2500); };
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $('#cust_mobile').autocomplete({
        source: '<?php echo base_url('Sale/customer_contact_autocomplete') ?>',
    });
    // Get Customer details from contact number
    $(document).on('keyup', 'input[id=cust_mobile]', function(e) {
        var keyCode = e.keyCode || e.which; 
        var cust_mobile = $("#cust_mobile").val();
        if(keyCode === 13 && cust_mobile.length != 10){
            swal("Incorrect mobile number!", "Check mobile number digits", "warning");
            $('#idcustomer').val('');
            $('#cust_fname').val('');
            $('#cust_lname').val('');
            $('#gst_no').val('');
            $('#cust_state').val('');
            $('#cust_idstate').val('');
            $('#cust_pincode').val('');
            $('#cust_latitude').val('');
            $('#cust_longitude').val('');
            $('#address').val('');
        }else if (cust_mobile.length === 10 && keyCode === 13) {
            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_get_customer_bycontact",
                method:"POST",
                dataType: 'json',
                data:{cust_mobile : cust_mobile},
                success:function(data)
                {
                    if(data.result == 'Success'){
                        $(data.contact_list).each(function (index, customer) {
                            if(customer.customer_gst == '' || customer.customer_gst == null){
                                swal("Customer GSTIN Should not empty!", "Corporate sale required Customers GSTIN", "warning");
                                $('#idcustomer').val('');
                                $('#cust_fname').val('');
                                $('#cust_lname').val('');
                                $('#gst_no').val('');
                                $('#cust_state').val('');
                                $('#cust_idstate').val('');
                                $('#cust_pincode').val('');
                                $('#cust_latitude').val('');
                                $('#cust_longitude').val('');
                                $('#address').val('');
                            }else{
                                var customer_details = "Name: "+customer.customer_fname+" "+customer.customer_lname;
                                customer_details += ", Mobile: "+cust_mobile;
                                swal("Customer Added!", "Customer: "+customer_details, "success");
                                $('#idcustomer').val(customer.id_customer);
                                $('#cust_fname').val(customer.customer_fname);
                                $('#cust_lname').val(customer.customer_lname);
                                $('#gst_no').val(customer.customer_gst);
                                $('#cust_state').val(customer.customer_state);
                                $('#cust_pincode').val(customer.customer_pincode);
                                $('#cust_idstate').val(customer.idstate);
                                $('#cust_latitude').val(customer.customer_latitude);
                                $('#cust_longitude').val(customer.customer_longitude);
                                $('#address').val(customer.customer_address);
                            }
                        });
                    }else{
                        swal("Customer not found!", "You have to create new customer", "warning");
                        $('#idcustomer').val('');
                        $('#cust_fname').val('');
                        $('#cust_lname').val('');
                        $('#gst_no').val('');
                        $('#cust_state').val('');
                        $('#cust_idstate').val('');
                        $('#cust_pincode').val('');
                        $('#cust_latitude').val('');
                        $('#cust_longitude').val('');
                        $('#address').val('');
                    }
                }
            });
        }
    });
    // product select
    var products = [], count=0;
    $('#skuvariant').change(function(){
        var skuvariant = $(this).val();
        var idbranch = $('#idbranch').val();
        var idgodown = $('#idgodown').val();
        var is_dcprint = $('#dcprint').val();
        if(skuvariant != ''){
            if (products.includes(skuvariant) === false){
                $.ajax({
                    url: "<?php echo base_url() ?>Sale/ajax_get_corporate_imei_details",
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
                url: "<?php echo base_url() ?>Sale/ajax_get_corporate_imei_details",
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
//                        swal({
//                            title: "Want to Add Product?",
//                            text: "IMEI/ SRNO "+ imei,
//                            type: "warning",
//                            showCancelButton: true,
//                            confirmButtonColor: '#4BC97F',
//                            confirmButtonText: 'Yes, Add it!',
//                            closeOnConfirm: true,
//                        },
//                        function(){
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
                            count++;
//                        });
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
    
    // Quantity, Discount
    $(document).on('keyup', 'input[id=qty],input[id=discount_amt],input[id=price]', function() {
        var discount_amt=0, total=0, price=0, qty=0, price=0, basic=0,total_mrp=0;
        var ce = $(this).closest('td').parent('tr');
        qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
        price = (isNaN(+ce.find(".price").val())) ? 0 : +ce.find(".price").val();
        discount_amt = (isNaN(+ce.find(".discount_amt").val())) ? 0 : +ce.find(".discount_amt").val();
        
        basic = price * qty;
        total = basic - +discount_amt; 
        
        var mop = (isNaN(+ce.find(".mop").val())) ? 0 : +ce.find(".mop").val();
        var corporate_sale_price = (isNaN(+ce.find(".landing").val())) ? 0 : +ce.find(".corporate_sale_price").val();
        var mrp = (isNaN(+ce.find(".mrp").val())) ? 0 : +ce.find(".mrp").val();
        var total_corporate_sale_price = qty * corporate_sale_price;
        total_mrp = qty * mrp;
        basic = price * qty;
        total = basic - discount_amt; 
        if(total_corporate_sale_price > total){
            swal("Not allowed to give price less than wholesale!", total_corporate_sale_price+" होलसेल किंमतीपेक्षा कमी किंमत देऊ शकत नाही","warning");
            ce.find(".price").val(mop);
            ce.find(".discount_amt").val('0');
            basic = mop * qty;
            total = basic - 0; 
        }
        
        ce.find(".basic").val(basic);
        ce.find(".spbasic").html(basic);
        ce.find(".total_amt").val(total);
        ce.find(".sptotal_amt").html(total);
        var total_basic_sum=0,sum_total_gross_amt=0,sum_discount_amt=0;
        $('tr').each(function () {
            // basic cal
            $(this).find('.basic').each(function () {
                var total_basic = $(this).val();
                if (!isNaN(total_basic) && total_basic.length !== 0) {
                    total_basic_sum += parseFloat(total_basic);
                }
            });
            $('#gross_total', this).val(total_basic_sum.toFixed(2));
            $('#spgross_total', this).html(total_basic_sum.toFixed(2));
            // gross total cal
            $(this).find('.total_amt').each(function () {
                var total_gross_amt = $(this).val();
                if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                    sum_total_gross_amt += parseFloat(total_gross_amt);
                }
            });
            $('#final_total', this).val(sum_total_gross_amt.toFixed(2));
            $('#spfinal_total', this).html(sum_total_gross_amt.toFixed(2));
            // discount_amt total cal
            $(this).find('.discount_amt').each(function () {
                var discount_amt1 = $(this).val();
                if (!isNaN(discount_amt1) && discount_amt1.length !== 0) {
                    sum_discount_amt += parseFloat(discount_amt1);
                }
            });
            $('#final_discount', this).val(sum_discount_amt.toFixed(2));
            $('#spfinal_discount', this).html(sum_discount_amt.toFixed(2));
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
                url: "<?php echo base_url() ?>Sale/ajax_get_payment_mode_data_byidhead",
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
            url: "<?php echo base_url() ?>Sale/ajax_get_payment_mode_data_byidhead",
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
        if($('#gst_no').val() == ''){
            swal("Customer GSTIN Should not empty!", "Corporate sale required Customers GSTIN", "warning");
            return false;
        }
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
                return true;
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
});
</script>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Generate Invoice</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
    <?php // if(count($todays_cash_closure) > 0){ ?>
        <!--<center><h4 class="blink" style="color: #ff0000; font-family: Kurale"><b>Your Branch submitted Today's Cash Closure. Below invoice will save under <?php echo date('d-m-Y', strtotime("+1 days")); ?> date</b></h4></center><div class="clearfix"></div><br>-->
        <!--<input type="hidden" name="cash_closure" value="1" />-->
    <?php // $var_closer = 1;
//    if(count($sale_last_entry_byidbranch)){
//        if($sale_last_entry_byidbranch[0]->sum_cash == 0){
//            $var_closer = 1;
//        }else{
//            if(count($cash_closure_last_entry) == 0){
//                $var_closer = 0;
//            }else{
//                if($last_date_entry[0]->date == $cash_closure_last_entry[0]->date){
//                    $var_closer = 1;
//                }elseif($last_date_entry[0]->date > $cash_closure_last_entry[0]->date){
//                    $var_closer = 0;
//                }
//            }
//        }
//    }
//    echo $sale_last_entry_byidbranch[0]->sum_cash.' '.$cash_closure_last_entry[0]->closure_cash;
    if($var_closer){ ?>
    <form>
    <div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">
        <div style="background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -30px">
            <div class="col-md-12 col-lg-12" style="padding: 5px">
                <div class="" style="font-size: 17px; padding: 3px; margin: 0px; color: #fff">
                    <center><i class="fa fa-clipboard"></i> Corporate Sale Form </center>
                </div>
            </div>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div>
        <div><h5 style="color:#1b6caa;font-family: Kurale;">Customer Details</h5></div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Customer Contact</small>
                <input type="hidden" id="cust_latitude" name="cust_latitude" />
                <input type="hidden" id="cust_longitude" name="cust_longitude" />
                <input type="hidden" id="modelid" name="madelid"/>
                <input type="hidden" id="imeiscanned" name="imeiscanned" />
                <input type="hidden" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                <input list="text" maxlength="10" class="form-control input-sm" name="cust_mobile" id="cust_mobile" required="" placeholder="Customer Mobile No" pattern="[6789][0-9]{9}" />
                <!--<datalist id="customers"></datalist>-->
                <input type="hidden" name="idcustomer" id="idcustomer" value=""/>
                <input type="hidden" name="bfl_store_id" id="bfl_store_id" value="<?php echo $invoice_no->bfl_store_id ?>"/>
                <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/>
                <input type="hidden" name="idstate" id="idstate" value="<?php echo $invoice_no->idstate ?>"/>
                <input type="hidden" class="form-control input-sm" name="address" id="address" placeholder="Address" />
            </div>
        </div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Customer First Name</small>
                <input type="text" class="form-control input-sm" name="cust_fname" id="cust_fname" required="" placeholder="Customer First Name" onfocus="blur()" />
            </div>
        </div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Customer Last Name</small>
                <input type="text" class="form-control input-sm" name="cust_lname" id="cust_lname" required="" placeholder="Customer Last Name" onfocus="blur()" />
            </div>
        </div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Customer GSTIN</small>
                <input type="text" class="form-control input-sm" name="gst_no" id="gst_no" pattern="^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[a-z1-9A-Z]{1}[zZ]{1}[a-z0-9A-Z]{1}$" placeholder="Customer GST Number" onfocus="blur()"/>
            </div>
        </div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Customer State</small>
                <input type="text" class="form-control input-sm" id="cust_state" placeholder="Customer State" onfocus="blur()"/>
                <input type="hidden" name="cust_idstate" id="cust_idstate" />
                <input type="hidden" name="cust_pincode" id="cust_pincode" />
            </div>
        </div>
        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <small class="text-muted">Sales Promoter</small>
<!--                <select class="form-control input-sm" name="idsalesperson" required="">
                    <option value="">Select Sales Promoter</option>
                    <?php // foreach ($active_users_byrole as $user) { ?>
                        <option value="<?php // echo $user->id_users ?>"><?php // echo $user->user_name ?></option>
                    <?php // } ?>
                </select>-->
                <select class="form-control input-sm" name="idsalesperson" required="">
                    <option value="0">Corporate Sales Promoter</option>
                </select>
            </div>
        </div><div class="clearfix"></div><hr>
        <!--<h6 style="color:#1b6caa;font-family: Kurale;">Product Details</h6>-->        
        <div><h5 style="color:#1b6caa;font-family: Kurale;">Product Details</h5></div>
        <!--<div class="col-md-1">Scan IMEI</div>-->
        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <input type="text" class="form-control" placeholder="Scan IMEI/SRNO/Barcode" id="enter_imei"/>
            </div>
        </div>
        <div class="col-md-2 col-sm-2" style="padding: 0 5px">
            <h5 class="text-muted" style="padding: 5px 0">Default Godown - New
                <input type="hidden" id="idgodown" value="1"/>
<!--                <select class="form-control input-sm" id="idgodown" required="">
                    <?php // foreach ($active_godown as $godown){ ?>
                    <option value="<?php // echo $godown->id_godown ?>"><?php // echo $godown->godown_name ?></option>
                    <?php // } ?>
                </select>-->
            </h5>
        </div>
        <div class="col-md-6 col-sm-6" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <select class="chosen-select form-control input-sm" name="skuvariant" id="skuvariant">
                    <option value="">Select Quantity Based Product</option>
                    <?php foreach ($model_variant as $variant) { ?>
                        <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div><div class="clearfix"></div>
        <center id="img_scanner" style="margin-top: 10px">
            <img src="<?php echo base_url() ?>assets/images/scanner.gif" style="max-width: 100%" />
            <!--<h4 style="color:#1b6caa;">Scan IMEI/ SRNO or Select Product</h4>-->
            <h4 style="color:#1b6caa;font-family: Kurale;">Scan IMEI/ SRNO or Select Product</h4>
        </center>
        <div id="product" style="display: none;">
            <div class="thumbnail" id="product" style="overflow: auto;margin-top: 10px; padding: 0">
                <table id="inward_table" class="table table-bordered table-condensed table-hover" style="font-size: 13px; margin-bottom: 0">
                    <!--<thead class="" style="background-image: linear-gradient(to right, #81fdff, #78f3ff, #76e8ff, #7adcff, #83d0ff, #83d0ff, #83d0ff, #83d0ff, #7adcff, #76e8ff, #78f3ff, #81fdff); font-size: 14px">-->
                    <thead style="color: #fff; background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);">
                        <td>Product</td>
                        <td>IMEI/SRNO</td>
                        <td>Avail</td>
                        <td>MRP</td>
                        <td>MOP</td>
                        <td>Price</td>
                        <td style="width: 100px">Qty</td>
                        <td>Basic</td>
                        <td style="width: 100px">Discount</td>
                        <td>Tax</td>
                        <td>Total</td>
                        <td>Remove</td>
                    </thead>
                    <tbody id="product_data">
                    </tbody>
                    <thead id="product_data1">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td>
                                <input type="hidden" name="gross_total" id="gross_total"/>
                                <span id="spgross_total">0</span>
                            </td>
                            <td>
                                <input type="hidden" name="final_discount" id="final_discount" class="form-control input-sm final_discount" placeholder="Total Discount" value="0" readonly=""/>
                                <span id="spfinal_discount">0</span>
                            </td>
                            <td></td>
                            <td colspan="2">
                                <input type="hidden" name="final_total" id="final_total"/>
                                <span id="spfinal_total">0</span>
                            </td>
                        </tr>
                    </thead>
                </table>
            </div><div class="clearfix"></div><hr>
            <h5 style="color:#1b6caa;font-family: Kurale;">Mode of payment</h5>
            <?php foreach ($payment_head as $head) { ?>
                <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                    <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode<?php echo $head->payment_head ?>" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                        <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="<?php echo $head->id_paymenthead ?>" selected_head="<?php echo $head->payment_head ?>" />
                        <!--<input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>"  data-toggle="collapse" data-target="#modes<?php echo $head->payment_head ?>" type="checkbox" />-->
                        <label for="paymentmode<?php echo $head->payment_head ?>" class="label-primary" style="margin-bottom: 10px"></label> 
                        <span><?php echo $head->payment_head ?></span>
                    </label>
                </div>
            <?php } ?><div class="clearfix"></div>
            <div class="payment_modes" style="font-size: 12px"></div>
            <div id="bfl_form"></div><hr>
            <div class="col-md-2 col-sm-3 col-xs-4">
                <a class="btn btn-warning gradient1" href="<?php echo base_url('Sale'); ?>">Cancel</a>
            </div>
            <div class="col-md-5 col-md-offset-3 col-sm-9 col-xs-8">
                <input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark"/>
            </div>
            <div class="col-md-2 col-sm-3 col-xs-4">
                <button type="submit" id="invoice_submit" class="btn btn-primary btn-sub gradient2" formmethod="POST" formaction="<?php echo site_url('Sale/save_corporate_sale') ?>">Submit</button>
            </div><div class="clearfix"></div>
        </div>
    </div><div class="clearfix"></div>
    </form>
    <?php }else{ 
        echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
            .'</center>';
    } ?>
</div><div class="clearfix"></div>
<?php require_once 'corporate_sale_master.php'; ?>
<?php include __DIR__.'../../footer.php'; ?>