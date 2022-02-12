<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="fa fa-sign-in fa-lg"></span> Service Case Details </h3></center></div><div class="clearfix"></div><hr>
<h4><center>Service - Sold Product</center></h4>
<div class="col-md-10 col-md-offset-1">
    <div class="col-md-8 col-xs-8">
        <div>CaseID  :- <b style="color: #0e10aa !important;"><?php echo $service_data[0]->id_service ?></b></div>
        <div>Date  :- <b><?php echo date('d-M-Y', strtotime($service_data[0]->entry_time)) ?></b></div><br>                                
    </div>
    <div class="col-md-4 col-xs-4">
        <div>Invoice Date :- <?php echo date('d-M-Y', strtotime($service_data[0]->inv_date)) ?></div>
        <div>Invoice No :- <a href="<?php echo base_url('Sale/sale_details/'.$service_data[0]->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $service_data[0]->inv_no ?></a></div>
    </div>
    <div class="clearfix"></div><hr>            
    <div class="col-md-8 col-xs-8">
        <b>Branch: &nbsp; <?php echo $service_data[0]->branch_name ?></b><br>                        
        <b>Contact:</b> <?php echo $service_data[0]->branch_contact; ?><br>
    </div>
    <div class="col-md-4 col-xs-4">
        <b>Customer , </b><br>
        <b>Name: &nbsp; <?php echo $service_data[0]->customer_name ?></b><br>                        
        <b>Contact:</b> <?php echo $service_data[0]->mob_number; ?><br>
    </div><div class="clearfix"></div>
    <div class="thumbnail" style="padding: 0">
        <table id="model_data" class="table table-hover" style="margin-bottom: 0">
            <thead class="bg-info">
                <th colspan="2"><?php echo $service_data[0]->full_name.' - ['.$service_data[0]->imei.']'; ?>
                <div class="col-md-3 pull-right">Sold Amount: <?php echo $service_data[0]->sold_amount ?></div></th>
            </thead>
            <tbody>
                <tr>            
                    <td>Service Issue</td>
                    <td><?php echo $service_data[0]->problem; ?></td>                         
                </tr>
                <tr>
                    <td>Remark</td>                    
                    <td><?php echo $service_data[0]->remark; ?></td>                    
                </tr>
                <tr>
                    <td>Delivery Status</td>
                    <td><?php echo $service_data[0]->delivery_status; ?></td>
                </tr>
                <?php if($service_data[0]->warranty_status != ''){ ?>
                <tr>
                    <?php if($service_data[0]->warranty_status == 1){ ?>
                    <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-flip-to-front fa-lg"></i> Repaired</h4></td>
                    <?php }elseif($service_data[0]->warranty_status == 2){ ?>
                    <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-close-box-outline fa-lg"></i> Rejected</h4></td>
                    <?php }elseif($service_data[0]->warranty_status == 3){ $return_reason = 'DOA Letter, DOA Id-'.$service_data[0]->doa_id.', DOA date-'.$service_data[0]->doa_date; ?>
                    <td><h4 style="color: #cc0099;">DOA Letter</h4></td>
                    <td>
                        <h4 class="col-md-9" style="color: #28538d;">
                            <span class="col-md-5">DOA ID: <?php echo $service_data[0]->doa_id; ?></span>
                            <span class="col-md-7">Date: <?php echo $service_data[0]->doa_date; ?></span>
                        </h4>
                        <a class="col-md-3 waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$service_data[0]->doa_letter_path) ?>" style="color: #1b6caa"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                    </td>
                    <?php }elseif($service_data[0]->warranty_status == 4){ $return_reason = 'DOA Handset, IMEI- '.$service_data[0]->new_imei_against_doa; ?>
                    <td><h4 style="color: #cc0099;">DOA Handset</h4></td><td><h4 style="color: #28538d;">New IMEI - <?php echo $service_data[0]->new_imei_against_doa ?></h4></td>
                    <?php } ?>
                </tr>
                <?php }if($service_data[0]->executive_remark != NULL){ ?>
                    <tr>
                        <td>Executive Remark</td>
                        <td><?php echo $service_data[0]->executive_remark; ?></td>                    
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <form>
        <input type="hidden" name="selected_total_amount" id="selected_total_amount" value="<?php echo $service_data[0]->sold_amount ?>" />
        
        <input type="hidden" name="idwarehouse" value="<?php echo $service_data[0]->idwarehouse ?>" />
        <input type="hidden" id="old_idvariant" name="old_idvariant" value="<?php echo $service_data[0]->idvariant ?>" />
        <input type="hidden" name="erp_type" value="<?php echo $service_data[0]->erp_type ?>" />
        <input type="hidden" id="imei_no" name="imei_no" value="<?php echo $service_data[0]->imei ?>" />
        <input type="hidden" name="old_idmodel" value="<?php echo $service_data[0]->idmodel ?>" />
        <input type="hidden" name="idservice" value="<?php echo $service_data[0]->id_service ?>" />
        <input type="hidden" name="warranty_status" value="<?php echo $service_data[0]->warranty_status ?>" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
        <input type="hidden" name="counter_faulty" value="0" />
        <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $service_data[0]->idbranch ?>" />
        <input type="hidden" name="idsale_product" value="<?php echo $service_data[0]->idsale_product ?>" />
        <input type="hidden" name="idsale" value="<?php echo $service_data[0]->idsale ?>" />
        <input type="hidden" name="inv_no" value="<?php echo $service_data[0]->inv_no ?>" />
        <input type="hidden" name="doa_return_type" value="<?php echo $service_data[0]->warranty_status ?>" />
        <input type="hidden" name="idcustomer" value="<?php echo $service_data[0]->idcustomer ?>" />
        <input type="hidden" name="doa_id" value="<?php echo $service_data[0]->doa_id ?>" />
        <input type="hidden" name="doa_date" value="<?php echo $service_data[0]->doa_date ?>" />
        <input type="hidden" name="cust_idstate" value="<?php echo $service_data[0]->cust_idstate ?>" />
        
        <input type="hidden" name="idmodel<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idmodel; ?>" />            
        <input type="hidden" name="idvariant<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idvariant; ?>" />
        <input type="hidden" name="idcategory<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idcategory; ?>" />
        <input type="hidden" name="idbrand<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idbrand; ?>" />                
        <input type="hidden" name="idproductcategory<?php echo $service_data[0]->id_service; ?>" value="<?php echo $service_data[0]->idproductcategory; ?>" />
        <input type="hidden" name="skutype<?php echo $service_data[0]->id_service; ?>" id="skutype" value="<?php echo $service_data[0]->idskutype ?>" />
        <input type="hidden" name="ret_product_name" id="ret_product_name" value="<?php echo $service_data[0]->full_name ?>" />
<!--        <input type="hidden" name="customer_name" value="<?php // echo $service_data[0]->customer_name ?>" />
        <input type="hidden" name="cust_pincode" value="<?php // echo $service_data[0]->cust_pincode ?>" />
        <input type="hidden" name="mobile" value="<?php // echo $service_data[0]->mob_number ?>" />
        <input type="hidden" name="address" value="<?php // echo $service_data[0]->cust_address ?>" />-->
        <?php if($service_data[0]->process_status == 11){ ?>
            <h4 style="color: #28538d;"><center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Service Case Closed</center></h4>
        <?php }elseif($service_data[0]->process_status == 15 && $service_data[0]->branch_process_enable == 1 && $service_data[0]->idbranch == $this->session->userdata('idbranch')){
            $total_selected_cash = $service_data[0]->sold_amount; ?>
            <h4><center><i class="mdi mdi-arrow-down-bold fa-2x" style="color: #9b0c13"></i></center></h4>
            <script>
                //window.onload=function() { setTimeout(function(){ $('#myDiv').remove(); }, 2500); };
                $(document).ready(function(){
                    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
                    var products = [], count=0;
                    $(document).on('change', '#skuvariant', function(e) {
                        var skuvariant = $(this).val();
                        var idbranch = $('#idbranch').val();
                        var old_idvariant = $('#old_idvariant').val();
                        var idgodown = $('#idgodown').val();                       
                        var is_dcprint = $('#dcprint').val();
                        if(skuvariant != ''){
                            if (products.includes(skuvariant) === false){
                                $.ajax({
                                    url: "<?php echo base_url() ?>Service/ajax_get_imei_details_for_doa_replace",
                                    method: "POST",
                                    data:{old_idvariant:old_idvariant,skuvariant : skuvariant,idbranch: idbranch, idgodown: idgodown, is_dcprint: is_dcprint},
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
                        var old_idvariant = $('#old_idvariant').val();
                        var imei_no = $('#imei_no').val();
                        var is_dcprint = $('#dcprint').val();
                         
                //        if(imei != ''){
                            if (imeis.includes(imei) === false){
                            $.ajax({
                                url: "<?php echo base_url() ?>Service/ajax_get_imei_details_for_doa_replace",
                                method: "POST",
                                data:{imei_no:imei_no,old_idvariant:old_idvariant,imei : imei,idbranch: idbranch, is_dcprint: is_dcprint},
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
                                        $('input[id=price]').trigger('change');
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

                    // Quantity, Discount
                    $(document).on('change', 'input[id=qty],input[id=discount_amt],input[id=price]', function() {
                    
                        var discount_amt=0, total=0, price=0, qty=0, price=0, basic=0,total_mrp=0;
                        var ce = $(this).closest('td').parent('tr');
                        qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
                        price = (isNaN(+ce.find(".price").val())) ? 0 : +ce.find(".price").val();
                        discount_amt = (isNaN(+ce.find(".discount_amt").val())) ? 0 : +ce.find(".discount_amt").val();
var mop = (isNaN(+ce.find(".mop").val())) ? 0 : +ce.find(".mop").val();
        var landing = (isNaN(+ce.find(".landing").val())) ? 0 : +ce.find(".landing").val();
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
                            alert(total_basic_sum);
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
                                if(!confirm('Do you want to submit?')){
                                    return false;
                                }
                            }
                            function count_arr(array){ var c = 0; for(i in array) if(array[i] != undefined) c++; return c;}
                        }
                    });
                });
            </script>
            <div>
                <!--<form>-->
                <!--<div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">-->
                <div class="neucard shadow-inset border-light p-4">
                    <div class="shadow-soft border-light rounded p-4" style="background-color: #fff">
                        <div style="background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -45px">
                            <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                                <center><i class="fa fa-clipboard"></i> Replace/ Upgrade Form Against Service DOA</center>
                            </div>
                        </div><div class="clearfix"></div><br>
                        <!--<input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/><br>-->
                        <input type="hidden" name="bfl_store_id" id="bfl_store_id" value="<?php echo $invoice_no->bfl_store_id ?>"/>
                        <input type="hidden" name="idstate" id="idstate" value="<?php echo $invoice_no->idstate ?>"/>
                        <div class="col-md-2 col-sm-4" style="padding: 0 5px">Approved by</div>
                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <input type="text" class="form-control input-sm" name="sales_return_approved_by" placeholder="Sale Return Approved By" required="" value="Service Co-ordinator" readonly="" />
                        </div>
                        <div class="col-md-2 col-sm-4" style="padding: 0 5px">Return Reason</div>
                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <input type="text" class="form-control input-sm" name="sales_return_reason" placeholder="Enter Reason for product replacement" required="" value="<?php echo $return_reason ?>" readonly="" />
                        </div><div class="clearfix"></div><br>
                        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                            <span>Sales Promoter</span>
                        </div>
                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <select class="form-control input-sm" name="idsalesperson" required="">
                                <option value="">Select Sales Promoter</option>
                                <?php foreach ($active_users_byrole as $user) { ?>
                                    <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-4" style="padding: 5px">
                            Default Godown -> New Godown
                            <input type="hidden" id="idgodown" value="1"/>
                        </div><div class="clearfix"></div><br>
                        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                            <span>Scan IMEI</span>
                        </div>
                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <input type="text" class="form-control input-sm" placeholder="Scan IMEI/SRNO/Barcode" id="enter_imei"/>
                        </div>
                        <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                            <span>Select Product</span>
                        </div>
                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <select class="chosen-select form-control input-sm" name="skuvariant" id="skuvariant">
                                <option value="">Select Quantity Based Product</option>
                                <?php foreach ($model_variant as $variant) { ?>
                                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <center id="img_scanner" style="margin-top: 10px">
                            <img src="<?php echo base_url() ?>assets/images/scanner.gif" style="max-width: 100%" />
                            <!--<h4 style="color:#1b6caa;">Scan IMEI/ SRNO or Select Product</h4>-->
                            <!--<h4 style="color:#1b6caa;font-family: Kurale;">Scan IMEI/ SRNO or Select Product</h4>-->
                        </center>
                        <div id="product" style="display: none;">
                            <div class="thumbnail" id="product" style="overflow: auto;margin-top: 10px; padding: 0">
                                <table id="inward_table" class="table table-bordered table-condensed table-hover" style="font-size: 13px; margin-bottom: 0">
                                    <!--<thead class="" style="background-image: linear-gradient(to right, #81fdff, #78f3ff, #76e8ff, #7adcff, #83d0ff, #83d0ff, #83d0ff, #83d0ff, #7adcff, #76e8ff, #78f3ff, #81fdff); font-size: 14px">-->
                                    <thead style="color: #fff; background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);">
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
                            <?php foreach ($payment_head as $head) { if($head->id_paymenthead == 0){ ?>
                            <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                                <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode1" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                                    <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="1" selected_head="Cash" checked="" disabled />
                                    <label for="paymentmodeCash" class="label-primary" style="margin-bottom: 10px"></label> 
                                    <span><?php echo $head->payment_head; ?></span>
                                </label>
                            </div>
                            <?php }else{ ?>
                            <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                                <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode<?php echo $head->payment_head ?>" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                                    <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="<?php echo $head->id_paymenthead ?>" selected_head="<?php echo $head->payment_head ?>" />
                                    <label for="paymentmode<?php echo $head->payment_head ?>" class="label-primary" style="margin-bottom: 10px"></label> 
                                    <span><?php echo $head->payment_head ?></span>
                                </label>
                            </div>
                            <?php }} ?><div class="clearfix"></div>
                            <div id="modes_block1" class="modes_block modes_blockc1 thumbnail" style="margin-bottom: 5px; padding: 5px;">
                                <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                    <span style="font-size: 15px; font-family: Kurale">DOA</span>
                                    <select class="form-control input-sm payment_type" name="payment_type[]">
                                        <option value="0">DOA Return</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                    Amount
                                    <input type="number" class="form-control input-sm amount" id="amount1" name="amount[]" placeholder="Amount" value="<?php echo $total_selected_cash ?>" readonly="" required="" />
                                    <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="0" />
                                    <input type="hidden" class="headname" name="headname[]" value="DOA" />
                                    <input type="hidden" class="credit_type" name="credit_type[]" value="0" />
                                </div>
                                <div class="col-md-2 col-sm-3 hidden">                            
                                    <input type="text" class="form-control input-sm tranxid" id="tranxid1" name="tranxid[]" value="<?php echo NULL; ?>" />
                                </div><div class="clearfix"></div>
                            </div><div class="clearfix"></div>
                            <div class="payment_modes" style="font-size: 12px"></div>
                            <div id="bfl_form"></div><hr>
                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <a class="btn btn-warning gradient1" href="<?php echo base_url('Service/process_service_details/'.$service_data[0]->id_service); ?>">Cancel</a>
                            </div>
                            <div class="col-md-5 col-md-offset-3 col-sm-9 col-xs-8">
                                <input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark"/>
                            </div>
                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <button type="submit" id="invoice_submit" class="btn btn-success btn-sub" formmethod="POST" formaction="<?php echo site_url('Service/save_product_replace_upgrade_against_doa_return') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Submit</button>
                            </div><div class="clearfix"></div>
                        </div>
                    </div><div class="clearfix"></div>
                    <!--</form>-->
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div>
        <?php } ?>
    </form>
</div><div class="clearfix"></div><br><br><br><br><br>
<?php   include __DIR__ . '../../footer.php'; ?>
