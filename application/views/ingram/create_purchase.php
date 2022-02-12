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
.alert_msg{
    width: 450px;
    background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
    color: #fff;
    border: 1px solid #00cccc;
    font-family: Kurale;
    font-size: 16px;
    text-align: center;
    opacity: 0.9;
    border-radius: 5px;
    position: fixed;
    bottom: 2%;
    left: 2%;
    padding: 10px;
    display: none;
	z-index: 9999999;
    /*animation: blinker 2s linear infinite;*/
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
        minLength: 5,
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
            $('#cust_oldcontact').val('');
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
                            var customer_details = customer.customer_fname+" "+customer.customer_lname;
                            customer_details += ", Mobile: "+cust_mobile;
//                            swal("Customer Added!", "Customer: "+customer_details, "success");
                            $('.alert_msg').show();
                            $('.alert_msg').text('Customer Added: '+customer_details);
                            $('.alert_msg').fadeOut(20000);
                            $('#idcustomer').val(customer.id_customer);
                            $('#cust_fname').val(customer.customer_fname);
                            $('#cust_lname').val(customer.customer_lname);
                            $('#gst_no').val(customer.customer_gst);
                            $('#cust_state').val(customer.customer_state);
                            $('#cust_pincode').val(customer.customer_pincode);
                            $('#cust_idstate').val(customer.idstate);
                            $('#cust_latitude').val(customer.customer_latitude);
                            $('#cust_longitude').val(customer.customer_longitude);
                            $('#cust_oldcontact').val(cust_mobile);
                            $('#address').val(customer.customer_address);
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
                        $('#cust_oldcontact').val('');
                        $('#address').val('');
                        $('#customer_contact').val(cust_mobile);
                        $('#customer_form').modal('show');
                    }
                }
            });
        }
    });
    // product select
    var products = [], count=0, imeis = [];
 
    
    
    $('#skuvariant').change(function(){
        var idvariant = $(this).val();
        var idbranch = $('#idbranch').val();
        var idgodown = $('#idgodown').val();        
        var sale_type = $("option:selected", this).attr('sale_type');
        var sku = $("option:selected", this).attr('sku');        
         var model_name = $('#skuvariant option:selected').text();
//        alert(sale_type);
        if(idvariant != ''){
            if (products.includes(idvariant) === false){
                $.ajax({
                    url: "<?php echo base_url() ?>Ingram_Api/ajax_get_product_details",
                    method: "POST",
                    data:{idvariant : idvariant,idbranch: idbranch, idgodown: idgodown, sku: sku,model_name:model_name},
                    success: function (data)
                    {
                        if(data == 0){
                            swal("Product SKU dose not match with Ingram!", "Contact Purhcase Team");
                            return false;
                        }else if(data == 1){  // 'if dc_product'
                            swal("Ingram session has been expired!", "Please try again to add same product");
                            return false;
                        }else if(data == 2){  
                            swal("Quantity is not available at Ingram!", "Please try again after some time");
                            return false;
                        }else{
                            products.push(idvariant);
                            $("#product").show();
                            $("#product_data").append(data);
                            $('#img_scanner').hide();
                            $('#modelid').val(products);
                            $('#gross_total').val(price);
                            $('#spgross_total').html(price);
                            $('#final_total').val(price);
                            $('#spfinal_total').html(price);
                            
//                            $('#remaining_amount').html(price);
//                            var avail = price - $('#entered_amout').html();
//                            $('#remaining_amount1').html(avail);
//                            
                            count++;
                            $('input[id=qty]').change();
                        }
                    }
                });
            }else{
                swal("Duplicate product selected!", "Product already in selected list","warning");
                return false;
            }
        }
    });
//    alert($('#modelid').val());
    // IMEI enter
    var price = 0;
    
    // Quantity, Discount
    $(document).on('change', 'input[id=qty],input[id=discount_amt],input[id=price]', function() {
        
    
        var discount_amt=0,total=0,price=0,qty=0,basic=0,total_mrp=0;
        var ce = $(this).closest('td').parent('tr');
        
        var availqty=+ce.find("#availqty").val();
        qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
        if(qty>availqty){            
            swal("Quantity not available!", "Please Re-enter quantity","warning");
            ce.find(".qty").val('0');
            return false;
        }else{
        
        price = (isNaN(+ce.find(".price").val())) ? 0 : +ce.find(".price").val();
        discount_amt = (isNaN(+ce.find(".discount_amt").val())) ? 0 : +ce.find(".discount_amt").val();
        var mop = (isNaN(+ce.find(".mop").val())) ? 0 : +ce.find(".mop").val();
        var landing = (isNaN(+ce.find(".landing").val())) ? 0 : +ce.find(".landing").val();
        var online_price = (isNaN(+ce.find(".online_price").val())) ? 0 : +ce.find(".online_price").val();
//        var billing_type = $("input[name=billing_type]").val(); 
//        if(billing_type === '0' || billing_type === 0){
//            landing=online_price;
//        }        
        var mrp = (isNaN(+ce.find(".mrp").val())) ? 0 : +ce.find(".mrp").val();
        var is_mop = (isNaN(+ce.find(".is_mop").val())) ? 0 : +ce.find(".is_mop").val();
        var total_landing = qty * landing;
        total_mrp = qty * mrp;
        basic = price * qty;
        total = basic - discount_amt; 
        if(is_mop){
            if(mop > price){
                swal("Not allowed to give price less than MOP!", mop+" MOP किंमतीपेक्षा कमी किंमतत देऊ शकत नाही","warning");
                ce.find(".price").val(mop);
                ce.find(".discount_amt").val('0');
                basic = mop * qty;
                total = basic - 0; 
            }
        }
        if(total_landing > total){
            swal("Not allowed to give price less than landing!", total_landing+" लँडिंग किंमतीपेक्षा कमी किंमत देऊ शकत नाही","warning");
            ce.find(".price").val(mop);
            ce.find(".discount_amt").val('0');
            basic = mop * qty;
            total = basic - 0; 
        }
        if(total_mrp < total){
            swal("Not allowed to give price greater than MRP!", total_mrp+" MRP किंमतीपेक्षा जास्त किंमत देऊ शकत नाही","warning");
            ce.find(".price").val(mop);
            ce.find(".discount_amt").val('0');
            basic = mop * qty;
            total = basic - 0; 
        }
//        
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
            $('#remaining_amount', this).html(sum_total_gross_amt);
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
        
        var avail = sum_total_gross_amt - $('#entered_amout').html();
        var booking = (isNaN(+$("#booking_amout").val())) ? 0 : +$("#booking_amout").val();
        $('#remaining_amount1').html(avail - booking);
    }
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
                
//                $('#remaining_amount').html(minus_ttotal_amt);
//                var avail = minus_ttotal_amt - $('#entered_amout').html();
//                $('#remaining_amount1').html(avail);
                
                parrent.remove();
                $('input[id=qty]').change();
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
                $('.amount').trigger('keyup');
            }else{
                $(this).prop("checked", true);
            }
        }
    });
    
    $(document).on("keyup", ".amount", function (event) {
        var total_received_sum=0;
        $('.modes_block').each(function () {
            $(this).find('.amount').each(function () {
                var total_received = +$(this).val();
                if (!isNaN(total_received) && total_received.length !== 0) {
                    total_received_sum += parseFloat(total_received);
                }
            });
        });
        $('#entered_amout').html(total_received_sum);
        var avail = $('#remaining_amount').html() - total_received_sum;
        var booking = (isNaN(+$("#booking_amout").val())) ? 0 : +$("#booking_amout").val();
        $('#remaining_amount1').html(avail - booking);
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
            var total_received_sum=0;
            $('.modes_block').each(function () {
                $(this).find('.amount').each(function () {
                    var total_received = +$(this).val();
                    if (!isNaN(total_received) && total_received.length !== 0) {
                        total_received_sum += parseFloat(total_received);
                    }
                });
            });
            $('#entered_amout').html(total_received_sum);
            var avail = $('#remaining_amount').html() - total_received_sum;
            var booking = (isNaN(+$("#booking_amout").val())) ? 0 : +$("#booking_amout").val();
            $('#remaining_amount1').html(avail - booking);
        }
    });
    $(document).on("click", "#invoice_submit", function (event) {
        var sum_sale_type = 0, arr_insurance_imei = [];
        $('.skuqty_row').each(function () {
            $(this).find('.sale_type').each(function () {
                var sale_type = $(this).val();
                if (!isNaN(sale_type) && sale_type.length !== 0) {
                    sum_sale_type += parseFloat(sale_type);
                }
            });
            $(this).find('.insurance_imei').each(function () {
                var insurance_imei = $(this).val();
                if (!isNaN(insurance_imei) && insurance_imei.length !== 0) {
                    arr_insurance_imei.push(insurance_imei);
                }
            });
        });
//        alert(imeis.length);
        if(sum_sale_type > 0 && imeis.length > 0){
            var found = 0;
            for (var i = 0; i < arr_insurance_imei.length; i++) {
                if (imeis.indexOf(arr_insurance_imei[i]) > -1) {
                    found = 1;
                    break;
                }
            }
            if(!found){
                swal("IMEI/SRNO Not matched with Insurance IMEI!", "Check entered insurance IMEI");
//                return false;
            }
        }
        if(count == 0){
            swal("Product not added!", "Scan IMEI/ SRNO or Select product");
            return false;
        }if($('#idcustomer').val() == ''){
            swal("Customer not added!", "Add customer or create new customer");
            return false;
        }
        var final_total= +$("input[name='final_total']").val();
        var total_amts=0;
        var amts = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
        var i=0;
        var total = count_arr(amts);
        for(i=0;i<total;i++){
            total_amts += parseFloat(amts[i]);
        }
        var booking = (isNaN($("#booking_amout").val())) ? 0 : +$("#booking_amout").val();
        total_amts += booking;
        var remaining = total_amts - final_total;
        var remaining1 = final_total - total_amts;
        if(total_amts>final_total){
            swal("Entered payment amount is greater!", "😠 Payment amount is greater than invoice amount!!"+remaining);
            return false;
        }if(total_amts<final_total){
            swal("Entered payment amount is less!", "😠 Payment amount is less than invoice amount!!" +remaining1);
            return false;
        }if($('#idsalesperson').val()==''){
            swal("Sales promoter is not selected!", "😠 Select sales promoter!! ","warning");
            return false;
        }if($('input[name=deliver_at]').val()==''){
            swal("Delivery type is not selected!", "😠 Select Delivery place!! ","warning");
            return false;
        }
        var payment_mode = $('#paymenttype4').val();
        var sfid = $('#tranxid4').val();
        if(payment_mode === '4' && sfid !== ''){
            if(sfid != $('#verify_bfl_tranxid4').val()){
                swal("Bajaj DO ID Not verified!", "😠 Click on submit again for verification!!","warning");
                $('#tranxid4').trigger('change');
                return false;
            }
        }
//        var gift_brand = [];
//        $('tr').each(function () {
//            $(this).find('#idbrand').each(function () {
//                if(gift_brand.includes($(this).val()) === false){
//                    gift_brand.push($(this).val());
//                }
//            });
//        });
//        if(gift_brand.length == 1 && gift_brand[0]== 62){
//            swal("😠 Unable to sale only Gift, Add other products", "तुम्ही बिलामध्ये फक्त गिफ्ट विकू शकत नाही!", "warning");
//            return false;
//        }
        if(+$('#cust_mobile').val() != +$('#cust_oldcontact').val()){
            confirm('First verify contact number by pressing enter key on customer contact');
            return false;
        }
        
        if (!confirm('Are you sure? Do you want to submit invoice')){
            return false;
        }
        function count_arr(array){ var c = 0; for(i in array) if(array[i] != undefined) c++; return c;}
    });
    
    $(document).on("change", "input[name=deliver_at]", function (event) {        
        if($(this).val()===1 || $(this).val()==='1'){
//         swal("Full Payment!", "You must accept full payment if you want to deliver at customer address","success");
        }
    });
    
});
</script>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Ingram Order </h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
<?php 
function getToken() {
    $token = sha1(mt_rand());
    if (!isset($_SESSION['tokens'])) {
        $_SESSION['tokens'] = array($token => 1);
    } else {
        $_SESSION['tokens'][$token] = 1;
    }
    return $token;
}
$token = getToken();
$this->session->unset_userdata('idsale_url'); 
if($var_closer==1 && $allow_ingram_purchase==1){
//    echo '<pre>'.print_r($payment_received_data,1).'</pre>';?>
    <form id="sale_form_submit">
        <input type="hidden" name="token" value="<?php echo $token;?>"/>
        <div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">
            <div style="">
                <div class="col-md-12 col-lg-12" style="padding: 5px">
                    <div class="" style="font-size: 17px; padding: 3px; margin: 0px; color: #000">
                        <center><i class="fa fa-clipboard"></i> Order Token Form </center>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
          <hr>
            
<!--            <div class="col-md-6">
                <median style="color:#1b6caa;font-family: Kurale;">Billing Type ? </median>
                <div style="margin-top:5px">
                <div class="form-check col-md-4 thumbnail" style="padding:3px">
                    <input class="form-check-input" type="radio" name="billing_type" value="0" id="atoffline">
                 <label class="form-check-label" style="font-size: 10px;" for="atoffline">
                   Offline
                 </label>
                </div>
               <div class="form-check col-md-4 thumbnail" style="margin-left: 10px;margin-bottom:0;padding:3px;">
                 <input class="form-check-input" type="radio" name="billing_type" value="1" id="atonline">
                 <label class="form-check-label" style="font-size: 10px;" for="atonline">
                   Online
                 </label>
               </div>
               </div>
            </div>-->
            <div class="col-md-6">
                <median style="color:#1b6caa;font-family: Kurale;">Where to deliver ? </median>
                <div style="margin-top:5px">
                <div class="form-check col-md-4 thumbnail" style="padding:3px">
                    <input class="form-check-input"  required="" type="radio" name="deliver_at" value="0"  id="atbranch">
                 <label class="form-check-label" style="font-size: 10px;" for="atbranch">
                   At Branch Address
                 </label>
               </div>
                <div class="form-check col-md-4 thumbnail" style="padding:3px;margin-left: 10px;">
                 <input class="form-check-input"  required="" type="radio" name="deliver_at" value="1" id="atcustomer">
                 <label class="form-check-label" style="font-size: 10px;" for="atcustomer">
                   At Customer Address
                 </label>
               </div>
            </div>
            </div>
            <div class="clearfix"></div><hr>
            <div class="col-md-3"><h5 style="color:#1b6caa;font-family: Kurale;">Customer Details</h5></div>
            <div class="clearfix"></div>
            
            <?php // if($payment_received_data){ ?>
            <!--<h5 style="color:#113c63;font-family: Kurale;"> &nbsp; &nbsp; <u>Advanced Booking By</u></h5>-->
            <?php // } ?>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Customer Contact</small>
                    <input type="hidden" id="cust_latitude" name="cust_latitude" />
                    <input type="hidden" id="cust_longitude" name="cust_longitude" />
                    <input type="hidden" id="modelid" name="madelid"/>
                    <input type="hidden" id="imeiscanned" name="imeiscanned" />
                    <input type="hidden" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    
                    <input type="hidden" id="idsale_token" name="idsale_token" value="" />
                    <input type="hidden" id="booking_amout" value="0" />
                    <input list="text" maxlength="10" class="form-control input-sm" name="cust_mobile" id="cust_mobile" required="" placeholder="Customer Mobile No" pattern="[26789][0-9]{9}"  />
                   
                    <input type="hidden" name="idcustomer" id="idcustomer" value=""/>
                    <input type="hidden" id="cust_oldcontact" placeholder="Customer Contact" required="" value="" />
                    <input type="hidden" name="bfl_store_id" id="bfl_store_id" value="<?php echo $invoice_no->bfl_store_id ?>"/>
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/>
                    <input type="hidden" name="idstate" id="idstate" value="<?php echo $invoice_no->idstate ?>"/>
                    <input type="hidden" class="form-control input-sm" name="address" id="address" placeholder="Address" value="" />
                </div>
            </div>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Customer First Name</small>
                    <input type="text" class="form-control input-sm" name="cust_fname" id="cust_fname" required="" placeholder="Customer First Name" onfocus="blur()" value="" />
                </div>
            </div>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Customer Last Name</small>
                    <input type="text" class="form-control input-sm" name="cust_lname" id="cust_lname" required="" placeholder="Customer Last Name" onfocus="blur()" value="" />
                </div>
            </div>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Customer GSTIN</small>
                    <input type="text" class="form-control input-sm" name="gst_no" id="gst_no" pattern="^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[a-z1-9A-Z]{1}[a-z1-9A-Z]{1}[a-z0-9A-Z]{1}$" placeholder="Customer GST Number" onfocus="blur()" value=""/>
                </div>
            </div>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Customer State</small>
                    <input type="text" class="form-control input-sm" id="cust_state" placeholder="Customer State" onfocus="blur()" value=""/>
                    <input type="hidden" name="cust_idstate" id="cust_idstate" value="" />
                    <input type="hidden" name="cust_pincode" id="cust_pincode" value="" />
                </div>
            </div>
            <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <small class="text-muted">Sales Promoter</small>                   
                        <select class="form-control input-sm" name="idsalesperson" required="" id="idsalesperson">
                            <option value="">Select Sales Promoter</option>
                            <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                                <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                            <?php }} ?>
                        </select>
                   
                </div>
            </div><div class="clearfix"></div><hr>
            
            <div><h5 style="color:#1b6caa;font-family: Kurale;">Product Details
                
            </h5></div>
            
            <div class="col-md-6 col-sm-6" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <select class="chosen-select form-control input-sm" name="skuvariant" id="skuvariant">
                        <option value="">Select Model</option>
                        <?php foreach ($model_variant as $variant) { ?>
                            <option value="<?php echo $variant->id_variant; ?>" sku="<?php echo $variant->$sku_column; ?>" sale_type="<?php echo $variant->sale_type; ?>"><?php echo $variant->full_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div><div class="clearfix"></div>
            <div id="product" style="display: none">
                <div class="thumbnail" id="product" style="overflow: auto;margin-top: 10px; padding: 0">
                    <table id="inward_table" class="table table-bordered table-condensed table-hover" style="font-size: 13px; margin-bottom: 0">
                        <thead style="color: #fff; background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);">
                            <td>Product</td>                            
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
                            <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="<?php echo $head->id_paymenthead ?>" selected_head="<?php echo $head->payment_head ?>"/>                                    
                            <label for="paymentmode<?php echo $head->payment_head ?>" class="label-primary" style="margin-bottom: 10px"></label>
                            <span><?php echo $head->payment_head ?></span>
                        </label>
                    </div>
                <?php } ?><div class="clearfix"></div>
                <div class="payment_modes" style="font-size: 12px">                    
                </div>
                <div id="bfl_form"></div><hr>
                <div class="thumbnail" style="margin-bottom: 5px;padding: 0px;font-size: 14px;">
                    <table class="table table-bordered" style="margin-bottom: 0">
                        <tbody>                            
                            <tr>
                                <td>Total Amount</td>
                                <td><span id="remaining_amount">0</span> <i class="mdi mdi-currency-inr fa-lg"></i></td>
                                <td>Entered Amount</td>
                                <td><span id="entered_amout">0</span> <i class="mdi mdi-currency-inr fa-lg"></i></td>                                
                                <td>Remaining Amount</td>
                                <td><span id="remaining_amount1">0</span> <i class="mdi mdi-currency-inr fa-lg"></i></td>
                            </tr>                            
                        </tbody>
                    </table>
                </div><hr>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <a class="btn btn-warning gradient1" href="<?php echo base_url('Ingram_Api/ingram_purchase'); ?>">Cancel</a>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-5">
                    <!--Entered Amount: <span class="entered">0</span>-->
                </div>
                <div class="col-md-5 col-sm-9 col-xs-8">
                    <input type="text" class="form-control" name="remark" placeholder="Enter Remark"/>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <button type="submit" id="invoice_submit" class="btn btn-primary btn-sub gradient2 pull-right" formmethod="POST" formaction="<?php echo site_url('Ingram_Api/create_ingram_po') ?>">Create PO</button>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div>
        </div>
    </form>
    <div class="alert_msg"></div>
    <?php }else{ 
        if(!$var_closer){        
        echo '<center><h3>You did not submitted yesterdays cash closure</h3>'
        . '<a href="'.base_url().'Payment/cash_closure"><h4 style="font-family: Kurale; color: #1e61c7"><i class="mdi mdi-chevron-double-right"></i>Click here to open cash closure form</h4></a>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
        .'</center>';
        }else{
            echo '<center><h3>Web Billing is not allowed for your branch</h3>'        
            .'<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>Contact your cluster head.</h3>'
        .'</center>';
        }
    } ?>
</div><div class="clearfix"></div>
<?php  require_once __DIR__.'../../sale/sale_master.php'; ?>
<input type="hidden" id="verify_bfl_tranxid4" value="" />
<!--<div class="neucard shadow-inset border-light p-4">
    <div class="shadow-soft neuborder border-light rounded p-4">
        Hi
    </div>
</div>--> 
<script>
$(document).ready(function(){
    // BFL integration    
     $('.floatingButtonWrap').css('display','none');
    $(document).on("change", "#tranxid4, #paymenttype4", function (event) {
        var parent = $(this).closest('div').parent('.modes_block');
        var payment_mode = $('#paymenttype4').val();
        var sfid = $('#tranxid4').val();
        var bfl_store_id = $('#bfl_store_id').val();
        var idbranch = $('#idbranch').val();
        if(payment_mode === '4' && sfid !== ''){
            $('#bfl_form').show();
            $.ajax({
                url:"<?php echo base_url('Sale/bajaj_finance_integration') ?>",
                method:"POST",
                data:{payment_mode : payment_mode, sfid: sfid, bfl_store_id: bfl_store_id},
                dataType: 'json',
                success:function(data)
                {
                    if(data.ResponseMessage == 'success'){
                        $('#verify_bfl_tranxid4').val(sfid);
                        var downscheme = data.DoDetails.CustomerDownPayment / data.DoDetails.TotalEMI;
                        var loanscheme = data.DoDetails.NetLoanAmount / data.DoDetails.TotalEMI;
                        var bfl_form = '<div class="col-md-10 col-md-offset-1" style="font-size: 14px">'
                            +'<div class="thumbnail" style="padding: 10px; margin: 10px 0">'
                                +'<center><h4>Bajaj Finance Limited</h4><u>Delivery Order</u></center>'
                                +'Dear, <b>SS COMMUNICATION <?php echo $_SESSION['branch_name'] ?></b> <span class="pull-right">Date: <b><?php echo date('d/m/Y h:i:s A'); ?></b></span><br>'
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
                                                +'<td>Net Disbursement</td>'
                                                +'<td>'+ data.DoDetails.NetDisbursement +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_netdisbursement" value="'+ data.DoDetails.NetDisbursement +'" /></td>'
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
//                        parent.find('.amount').val(data.DoDetails.NetDisbursement);
//                        parent.find('.amount').attr('readonly','true');
                        $('#scheme_code').val(loanscheme+'/'+downscheme);
                        $('#scheme_code').attr('readonly',true);
                        $('#tranxid4').attr('readonly',true);
                        $('#bfl_form').html(bfl_form);

//                        $('#mobile').val(data.DoDetails.CustomerPhoneNo);
//                        $('#customer').val(data.DoDetails.CustomerName);
//                        $('#gst_no').val(data.DoDetails.customerGSTIN);
//                        $('#address').val(data.DoDetails.AddressLine1+', '+data.DoDetails.AddressLine2+', '+data.DoDetails.AddressLine3+', '+data.DoDetails.Area+' ,'+data.DoDetails.CITY);
//                        $("#cust_state option:selected").text(data.DoDetails.STATE);
//                        $('#cust_state').css('pointer-events','none');

                        var cfname = data.DoDetails.CustomerFirstName;
                        var cmname = '';
                        if(data.DoDetails.CustomerMiddleName == null){
                            cmname = '';
                        }else{
                            cmname = data.DoDetails.CustomerMiddleName+' ';
                        }
                        var clname = cmname+data.DoDetails.CustomerLastName;
                        if(data.DoDetails.customerGSTIN == null){
                            var bfgst = ''
                        }else{
                            var bfgst = data.DoDetails.customerGSTIN;
                        }
//                        alert(cfname+' '+clname);
                        var bfl_cust_data = '<form class="customer_bfl_form_submit">'
                                        +'<input type="hidden" name="iduser" value="<?php echo $_SESSION['id_users'] ?>" />'
                                        +'<input type="hidden" name="idbranch" value="<?php echo $_SESSION['idbranch'] ?>" />'
                                        +'<input type="text" class="form-control input-sm required" placeholder="First Name" name="customer_fname" required="" value="'+cfname+'"/>'
                                        +'<input type="text" class="form-control input-sm required" placeholder="Last Name" name="customer_lname" required="" value="'+clname+'"/>'
                                        +'<input type="text" class="form-control input-sm required" id="customer_contact" placeholder="Customer Contact" name="customer_contact" required="" value="'+data.DoDetails.CustomerPhoneNo+'" />'
                                        +'<input type="email" class="form-control input-sm" placeholder="Customer Email Id" name="email_id" value="'+data.DoDetails.CustomerEmailID+'"/>'
                                        +'<input type="text" class="form-control input-sm" placeholder="Customer GSTIN" name="customer_gst" id="customer_gst" value="'+bfgst+'" />'
                                        +'<input type="text" class="form-control input-sm required" placeholder="Customer Address" name="customer_address" required="" value="'+data.DoDetails.AddressLine1+' '+data.DoDetails.AddressLine2+' '+data.DoDetails.AddressLine3+' '+data.DoDetails.Area+'" />'
                                        +'<input type="text" class="form-control input-sm required" placeholder="Customer Pincode" name="customer_pincode" id="customer_pincode" required="" pattern="^[0-9]{6}$" value="'+data.DoDetails.PinCode+'" />'
                                        +'<input type="text" class="form-control input-sm required" placeholder="Customer City" name="customer_city" id="customer_city" required="" value="'+data.DoDetails.CITY+'" />'
                                        +'<input type="text" class="form-control input-sm required" placeholder="Customer Disctrict" name="customer_district" id="customer_district" required="" value="'+data.DoDetails.CITY+'" />'
                                        +'<input type="text" class="form-control input-sm required" name="customer_state" id="customer_state" required="" value="'+data.DoDetails.STATE+'" />'
                                        +'<a id="bfl_customer_submit" class="btn btn-info pull-right waves-effect gradient2"> Save</a>'
                                    +'</form>';
                        $('#bfl_customer_data').html(bfl_cust_data);
                        $('#bfl_customer_submit').trigger('click');
//                        $('.amount').trigger('keyup');
                        $('.floatingButtonWrap').css('display','none');
                    }else{
                        $('#verify_bfl_tranxid4').val('');
                        var bfl_form = '<div class="alert alert-danger" id="alert-dismiss"><center><h4 style="padding: 0; margin: 0">No Data found in SFDC for dealId: '+sfid+'</h4></center></div>';
                        $('#bfl_form').html(bfl_form);
//                        parent.find('.amount').val('0');
//                        parent.find('.amount').attr('readonly',false);
                        $('#scheme_code').val('');
                        $('#tranxid4').val('');
                        $('#tranxid4').removeAttr('readonly');
                        
                        setTimeout(function() {
                            $('#bfl_form').hide();
                            $('#bfl_form').html('');
                        }, 5000);
                        $('#cust_mobile').removeAttr('readonly');
                        $('.floatingButtonWrap').css('display','block');
                    }
                }
            });
        }
    });
    $(document).on("click", "#bfl_customer_submit", function (event) {
        var $form = $('.customer_bfl_form_submit');
        if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
            event.preventDefault();
            alert("Fill Mandatory fields !!");
            return false;
        }else{
            var serialized = $('.customer_bfl_form_submit').serialize();
            $.ajax({
                url: "<?php echo base_url('Sale/save_bfl_customer') ?>",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if(data.result == 'Success'){
//                            alert(data.state_data.gst_code);
                        $(data.customer_data).each(function (index, customer) {
//                                alert(customer.customer_fname);
                            $('#idcustomer').val(customer.id_customer);
                            $('#cust_mobile').val(customer.customer_contact);
                            $('#cust_oldcontact').val(customer.customer_contact);
                            $('#cust_fname').val(customer.customer_fname);
                            $('#cust_lname').val(customer.customer_lname);
                            $('#gst_no').val(customer.customer_gst);
                            $('#cust_pincode').val(customer.customer_pincode);
                            $('#cust_idstate').val(customer.idstate);
                            $('#cust_state').val(customer.customer_state);
                            $('#address').val(customer.customer_address);
                            $('#cust_mobile').attr('readonly',true);
                        });
                    }else if(data.result == 'Failed'){
                        $('#idcustomer').val('');
                        $('#cust_mobile').val('');
                        $('#cust_oldcontact').val('');
                        $('#cust_fname').val('');
                        $('#cust_lname').val('');
                        $('#gst_no').val('');
                        $('#cust_idstate').val('');
                        $('#cust_pincode').val('');
                        $('#address').val('');
                        $('#cust_mobile').removeAttr('readonly');
                        alert(data.msg);
                        return false;
                    }
                }
            });
        }
    });
});
</script>
<div id="bfl_customer_data" class="hidden"></div>

<?php include __DIR__.'../../footer.php'; ?>