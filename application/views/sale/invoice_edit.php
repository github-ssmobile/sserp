<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on('keydown', 'input[id=invno]', function(e) {
        var invno = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_search_invoice_for_edit_config",
                method:"POST",
                data:{invno : invno,level: level},
                success:function(data)
                {
                    $("#invoice_data").html(data);
                    $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
    $(document).on('click', 'button[id=reverse_value]', function(e) {
        var invno = $('#invno').val();
        var level = $('#level').val();
        
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_search_invoice_for_edit_config",
            method:"POST",
            data:{invno : invno,level: level},
            success:function(data)
            {
                $("#invoice_data").html(data);
                $(".chosen-select").chosen({search_contains: true});
            }
        });
    });
    $(document).on("click", "#customer_edit_btn", function (event) {
        var idcustomer = $('#idcustomer').val();
        var customer_fname = $('#customer_fname').val();
        var customer_lname = $('#customer_lname').val();
        var customer_address = $('#customer_address').val();
        var customer_gst = $('#customer_gst').val();
        var idsale = $('#idsale').val();
        var pincode = $('#pincode').val();
        var idstate = $('#idstate').val();
        var config_edit = $('#config_edit').val();
        var customer_idstate = $('#customer_idstate').val();
        var branch_idstate = $('#branch_idstate').val();
        var state_name = $("#idstate option:selected").text();
//        var old_idstate = $('#old_idstate').val();
        var gst_type = $('#gst_type').val();
        if(idstate == '' || customer_fname == '' || customer_lname == '' || customer_address == '' || pincode == ''){
            swal('Alert!', 'Required mandatory fields','warning');
            return false;
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Sale/edit_sale_customer",
                method:"POST",
                dataType:"json",
                data:{idcustomer:idcustomer,customer_fname:customer_fname,customer_lname:customer_lname,customer_address:customer_address,
                    customer_gst:customer_gst,idsale:idsale,gst_type:gst_type,pincode:pincode,idstate:idstate,state_name:state_name,
                    branch_idstate:branch_idstate,customer_idstate:customer_idstate,config_edit:config_edit},
                success:function(data)
                {
                    if(data.result == 'success'){
                        swal("Customer updated", "Customer details updated in sale", "success");
                        $('#reverse_value').trigger('click');
                    }else{
                        swal("Failed to edit customer!", "😠 Try again", "warning");
                    }
                }
            });
        }
    });
    $(document).on('click', '#verify_get_customer', function(e) {
        var cust_mobile = $("#cust_mobile").val();
        if(cust_mobile.length != 10){
            swal("Incorrect mobile number!", "Check mobile number digits", "warning");
            $('#cust_fname').val('');
            $('#cust_lname').val('');
            $('#gst_no').val('');
            $('#cust_state').val('');
            $('#cust_idstate').val('');
            $('#cust_pincode').val('');
            $('#address').val('');
            $('#spcust_fname').html('');
            $('#spcust_lname').html('');
            $('#spgst_no').html('');
            $('#spcust_pincode').html('');
            $('#customer_state').val('');
            $('#spcustomer_state').html('');
            $('#spcustomer_contact').html('');
            $('#spaddress').html('');
        }else if (cust_mobile.length === 10) {
            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_get_customer_bycontact",
                method:"POST",
                dataType: 'json',
                data:{cust_mobile : cust_mobile},
                success:function(data)
                {
                    if(data.result == 'Success'){
                        $(data.contact_list).each(function (index, customer) {
                            var customer_details = "Name: "+customer.customer_fname+" "+customer.customer_lname;
                            customer_details += ", Mobile: "+cust_mobile;
                            swal("Customer Added!", "Customer "+customer_details, "success");
                            $('#nidcustomer').val(customer.id_customer);
                            $('#cust_fname').val(customer.customer_fname);
                            $('#cust_lname').val(customer.customer_lname);
                            $('#address').val(customer.customer_address);
                            $('#gst_no').val(customer.customer_gst);
                            $('#cust_pincode').val(customer.customer_pincode);
                            $('#cust_idstate').val(customer.idstate);
                            $('#customer_contact').val(cust_mobile);
                            $('#spcust_fname').html(customer.customer_fname);
                            $('#spcust_lname').html(customer.customer_lname);
                            $('#spgst_no').html(customer.customer_gst);
                            $('#spcust_pincode').html(customer.customer_pincode);
                            $('#spaddress').html(customer.customer_address);
                            $('#spcustomer_contact').html(cust_mobile);
                            $('#spcustomer_state').html(customer.customer_state);
                            $('#customer_state').val(customer.customer_state);
                            $('#customer_block').show();
                            $('#empty_block').hide();
                        });
                    }else{
                        swal("Customer not found!", "You have to create new customer", "warning");
                        $('#nidcustomer').val('');
                        $('#cust_fname').val('');
                        $('#cust_lname').val('');
                        $('#gst_no').val('');
                        $('#cust_idstate').val('');
                        $('#cust_pincode').val('');
                        $('#address').val('');
                        $('#customer_contact').val('');
                        $('#spcust_fname').html('');
                        $('#spcust_lname').html('');
                        $('#spgst_no').html('');
                        $('#spcust_pincode').html('');
                        $('#spaddress').html('');
                        $('#spcustomer_contact').html('');
                        $('#customer_state').val('');
                        $('#spcustomer_state').html('');
                    }
                }
            });
        }
    });
    
    $(document).on("click", "#add_selected_customer", function (event) {
        var nidcustomer = $('#nidcustomer').val();
        var cust_fname = $('#cust_fname').val();
        var cust_lname = $('#cust_lname').val();
        var address = $('#address').val();
        var gst_no = $('#gst_no').val();
        var cust_pincode = $('#cust_pincode').val();
        var cust_idstate = $('#cust_idstate').val();
        var customer_contact = $('#customer_contact').val();
        var customer_state = $('#customer_state').val();
//        var customer_idstate = $('#customer_idstate').val();
        var idsale = $('#idsale').val();
        if(nidcustomer == ''){
            swal("Try again", "Enter mobile and get ccusstomer  details", "warning");
            return false;
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Sale/change_sale_customer",
                method:"POST",
                dataType:"json",
                data:{idcustomer:nidcustomer,customer_fname:cust_fname,customer_lname:cust_lname,customer_address:address,customer_gst:gst_no,idsale:idsale,cust_pincode:cust_pincode,cust_idstate:cust_idstate,customer_contact:customer_contact},
                success:function(data)
                {
                    if(data.result == 'success'){
                        swal("Customer updated", "Customer details updated in sale", "success");
                        $('#idcustomer').val(nidcustomer);
                        $('#customer_fname').val(cust_fname);
                        $('#customer_lname').val(cust_lname);
                        $('#customer_address').val(address);
                        $('#pincode').val(cust_pincode);
                        $('#customer_gst').val(gst_no);
                        $('#idstate').val(cust_idstate);
                        $("#idstate option:selected").text(customer_state);
                        $('#spcust_contact').html(customer_contact);
                        $('#customer_selection_form').modal('hide');
                    }else{
                        swal("Failed to edit customer!", "😠 Try again", "warning");
                    }
                }
            });
        }
    });
    var sale_payments = [], idedit_salepayment = [];
    $(document).on('click', 'a[id=oldremove_payment]', function() {
        var parrent = $(this).closest('td').parent('tr');
        var idsale_payment = $(this).closest('td').find('.idsale_payment').val();
        var payment_mode = $(this).closest('td').find('.payment_mode').val();
        var payment_amount = $(this).closest('td').find('.payment_amount').val();
        swal({
                title: "Want to Remove Sale Payment?",
                text: "Payment mode: "+payment_mode+" Amount: "+payment_amount,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#E84848',
                confirmButtonText: 'Yes, Remove it!',
                closeOnConfirm: false,
            },
            function(){
                swal("Removed!", "Payment mode: "+payment_mode+" Amount: "+payment_amount+" Payment removed!", "success");
                var idremoved_sale_payments = parrent.find('.idremoved_sale_payments');
                idremoved_sale_payments.val(idsale_payment);
                var idedited_sale_payments = parrent.find('.idedited_sale_payments');
                idedited_sale_payments.val(0);
                parrent.hide();
                $('.edit_amount').keyup();
        });
    });
    $(document).on("click", "a[id=remove_payment]", function (event) {
        if (confirm('Are you sure? You want to remove payment mode')){
            $(this).closest('div').parent('.modes_block').remove();
            var total_received_sum=0;
            $('.modes_block').each(function () {
                $(this).find('.amount').each(function () {
                    var total_received = +$(this).val();
                    if (!isNaN(total_received) && total_received.length !== 0) {
                        total_received_sum += parseFloat(total_received);
                    }
                });
            });
            var ovre = +$('#enfinal_total').html() + total_received_sum;
            $('#entered_amout').html(ovre);
            var avail = $('#invoice_amount').html() - ovre;
            $('#remaining_amount').html(avail);
        }
    });
    $(document).on('change', 'select[id=select_payment_type]', function() {
        if($(this).val() != ''){
            if($('#remaining_amount').html() <= 0){
                swal("Amount alert", "Not allowed due to Entered remaining amount is less or equal to 0!", "warning");
            }else{
                var idpayment_mode = $(this).val();
                var paymenthead = $('option:selected', this).attr('paymenthead');
                var headname = $('option:selected', this).attr('headname');
                var modename = $('option:selected', this).attr('modename');
                var credit_type = $('option:selected', this).attr('credit_type');
                $.ajax({
                    url:"<?php echo base_url() ?>Sale/ajax_get_payment_mode_attributes_byidhead",
                    method:"POST",
                    data:{idpayment_mode:idpayment_mode,paymenthead:paymenthead,headname:headname,modename:modename,credit_type:credit_type},
                    success:function(data)
                    {
                        $('.payment_block').append(data);
                    }
                });
            }
        }
    });
    $(document).on('change', 'input[id=new_imei_no]', function() {
        var new_imei_no = $(this).val();
        var ce = $(this).closest('td').parent('tr');
        var idvariant = +ce.find(".idvariant").val();
        var idgodown = +ce.find(".idgodown").val();
        var idbranch = +$("#idbranch").val();
        var old_imei_no = +ce.find(".old_imei_no").val();
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_check_valid_imei",
            method:"POST",
            dataType:"json",
            data:{new_imei_no:new_imei_no,idvariant:idvariant,idgodown:idgodown,idbranch:idbranch},
            success:function(data)
            {
                if(data == 'Success'){
                    swal("Entered imei is matched!", "", 'success');
                    return true;
                }else{
                    swal("Entered imei is not matched!", "😠 With model or not in branch or godown stock!!", 'warning');
                    ce.find(".new_imei_no").val(old_imei_no);
                    return false;
                }
            }
        });
    });
    $(document).on('keyup', 'input[id=qty],input[id=discount_amt],input[id=price],input[id=activation_code],input[id=insurance_imei_no],input[id=new_imei_no]', function() {
        var discount_amt=0,total=0,basic=0,price_diff=0,total_priceoff=0,total_basic_sum=0,total_dicount_sum=0,sum_total_gross_amt=0,insurance_imei_no=0,activation_code=0;
        var ce = $(this).closest('td').parent('tr');
        var idsaleproduct = +ce.find(".idsaleproduct").val();
        ce.find(".edited_idsaleproduct").val(idsaleproduct);
        var price = (isNaN(+ce.find(".price").val())) ? 0 : +ce.find(".price").val();
        var qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
//        var activation_code = (isNaN(+ce.find(".activation_code").val())) ? 0 : +ce.find(".activation_code").val();
//        var insurance_imei_no = (isNaN(+ce.find(".insurance_imei_no").val())) ? 0 : +ce.find(".insurance_imei_no").val();
        discount_amt = (isNaN(+ce.find(".discount_amt").val())) ? 0 : +ce.find(".discount_amt").val();

        price_diff = $(ce).closest('td').parent('tr').find(".price_diff").val();
        total_priceoff = qty * price_diff;
        $(ce).closest('td').parent('tr').find(".discount_amt").prop('max', total_priceoff);
        if(discount_amt > total_priceoff ){
            alert('Not allowed to give discount greater than'+total_priceoff);
            return false;
        }
        
        basic = price * qty;
        total = basic - +discount_amt; 
        
        ce.find(".basic").val(basic);
        ce.find(".spbasic").html(basic);
        ce.find(".total_amt").val(total);
        ce.find(".sptotal_amt").html(total);
        
        $('.product_row').each(function () {
            $(this).find('.basic').each(function () {
                var total_basic = $(this).val();
                if (!isNaN(total_basic) && total_basic.length !== 0) {
                    total_basic_sum += parseFloat(total_basic);
                }
            });
            $(this).find('.freez_basic').each(function () {
                var total_basic = $(this).val();
                if (!isNaN(total_basic) && total_basic.length !== 0) {
                    total_basic_sum += parseFloat(total_basic);
                }
            });
            $(this).find('.discount_amt').each(function () {
                var total_discount = $(this).val();
                if (!isNaN(total_discount) && total_discount.length !== 0) {
                    total_dicount_sum += parseFloat(total_discount);
                }
            });
            $(this).find('.freez_discount_amt').each(function () {
                var total_discount = $(this).val();
                if (!isNaN(total_discount) && total_discount.length !== 0) {
                    total_dicount_sum += parseFloat(total_discount);
                }
            });
            $(this).find('.total_amt').each(function () {
                var total_gross_amt = $(this).val();
                if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                    sum_total_gross_amt += parseFloat(total_gross_amt);
                }
            });
            $(this).find('.freez_total_amt').each(function () {
                var total_gross_amt = $(this).val();
                if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                    sum_total_gross_amt += parseFloat(total_gross_amt);
                }
            });
        });
        
        $('#basic_total').val(total_basic_sum.toFixed(2));
        $('#spbasic_total').html(total_basic_sum.toFixed(2));
        $('#discount_total').val(total_dicount_sum.toFixed(2));
        $('#spdiscount_total').html(total_dicount_sum.toFixed(2));
        $('#final_total').val(sum_total_gross_amt.toFixed(2));
        $('#spfinal_total').html(sum_total_gross_amt.toFixed(2));
        
        $('#invoice_amount').html(sum_total_gross_amt.toFixed(2));
        var avail = sum_total_gross_amt - $('#entered_amout').html();
        $('#remaining_amount').html(avail);
    });
    $(document).on("keyup", ".edit_amount, .transaction_id, .attr_value", function (event) {
        var parrent = $(this).closest('td').parent('tr');
        var idsale_payment = +parrent.find('.idsale_payment').val();
        var idedited_sale_payments = parrent.find('.idedited_sale_payments');
//        alert(idsale_payment);
        idedited_sale_payments.val(idsale_payment);
        var total_received_sum=0,overall_total=0;
        $('.modes_block').each(function () {
            if($(this).find('.idremoved_sale_payments').val() == 0){
                $(this).find('.edit_amount').each(function () {
                    var total_received = +$(this).val();
                    if (!isNaN(total_received) && total_received.length !== 0) {
                        total_received_sum += parseFloat(total_received);
                    }
                });
            }
            $(this).find('.amount').each(function () {
                var total_received = +$(this).val();
                if (!isNaN(total_received) && total_received.length !== 0) {
                    overall_total += parseFloat(total_received);
                }
            });
        });
        var ovre = total_received_sum + overall_total;
        $('#enfinal_total').html(total_received_sum);
        $('#entered_amout').html(ovre);
        var avail = $('#invoice_amount').html() - ovre;
        $('#remaining_amount').html(avail);
    });
    $(document).on("keyup", ".amount", function (event) {
        var overall_total=0;
        $('.modes_block').each(function () {
            $(this).find('.amount').each(function () {
                var total_received = +$(this).val();
                if (!isNaN(total_received) && total_received.length !== 0) {
                    overall_total += parseFloat(total_received);
                }
            });
        });
        var ovre = +$('#enfinal_total').html() + overall_total;
        $('#entered_amout').html(ovre);
        var avail = $('#invoice_amount').html() - ovre;
        $('#remaining_amount').html(avail);
    });
    $(document).on("click", "#correction_submit", function (event) {
        var total_amts = +$('#entered_amout').html();
        var final_total = +$('#spfinal_total').html();
        var remaining = +$('#remaining_amount').html();
        if(total_amts>final_total){
            swal("Entered payment amount is greater!", "😠 Payment amount is greater than invoice amount!! "+remaining);
            return false;
        }else if(total_amts<final_total){
            swal("Entered payment amount is less!", "😠 Payment amount is less than invoice amount!! " +remaining);
            return false;
        }
        if(!confirm('Do you want to submit?')){
            return false;
        }
    });
});
</script>
<center><h3 style="margin: 0"><span class="mdi mdi-margin fa-lg"></span> Invoice Edit</h3></center><div class="clearfix"></div><hr>
<div class="col-md-1 col-sm-2">Invoice No</div>
<div class="col-md-4 col-sm-7">
    <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
</div><div class="clearfix"></div>
<input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
<input type="hidden" class="form-control input-sm" name="edited_by" value="<?php echo $this->session->userdata('id_users') ?>"/><br>
<div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
<style>
.btn-outline {
    background-color: transparent;
    color: inherit;
    transition: all .5s;
    border-radius: 2px;
    text-transform: capitalize;
}
.btn-primary.btn-outline {
    border: 1px solid #428bca;
    color: #428bca;
}
.btn-success.btn-outline {
    border: 1px solid #1fa337;
    color: #1fa337;
}
.btn-info.btn-outline {
    border: 1px solid #5bc0de;
    color: #5bc0de;
}
.btn-warning.btn-outline {
    border: 1px solid #ff8a1c;
    color: #ff8a1c;
}
.btn-danger.btn-outline {
    border: 1px solid #d9534f;
    color: #d9534f;
}
.btn-primary.btn-outline:hover,
.btn-success.btn-outline:hover,
.btn-info.btn-outline:hover,
.btn-warning.btn-outline:hover,
.btn-danger.btn-outline:hover {
    color: #fff;
}
</style>
<?php include __DIR__ . '../../footer.php'; ?>