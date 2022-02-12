<?php include __DIR__ . '../../header.php'; 
if($purchase_order->status == 1){ ?>
<style>
    .blink {
        animation: blinker 1s linear infinite;
    }
    @keyframes blinker {
        5% {
            opacity: 0;
        }
    }
</style>
<script>
    var products = [];
    window.onload=function() { 
        $("#sidebar").addClass("active");
    };
    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode === 13) {
            event.preventDefault();
            return false;
          }
        });
        $('#total_discount').keyup(function(){
            var total_price = $('#total_basic_amt').val(), basic_percent = 0, total_discount = $('#total_discount').val();
            var total_basic_percent = total_price / 100;
            var total_discount = total_discount/100;
            $('tr').each(function () {
                $(this).find('.basic').each(function () {
                    var total_basic = $(this).val();
                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                        basic_percent = parseFloat(total_basic)/total_basic_percent;
                        var ce = $(this);
                        var dis_amount = total_discount * basic_percent;
                        $(ce).closest('td').parent('tr').find(".basic_percent").val(basic_percent);
                        $(ce).closest('td').parent('tr').find(".discount_amt").val(dis_amount.toFixed(2));
                    }
                });
            });
            $(".qty").trigger("keyup");
        });
        $('.gstradio').click(function(){
            if(($(this).val()==1)){
                if (confirm('Are you sure? You selected GST Invoice type product !')) {
                    $(".gst-block").hide();
                    var gstradio_text= $("input[name='gstradio']:checked").next('label').text();
                    $(".gst-text-block").html("Selected Invoice type :- <b>"+gstradio_text+"</b>");
                    
                }else{
                    $(this).val('');
                    return false;
                }
            }else{
                if (confirm('Are you sure? You selected Non-GST Invoice type product !')) {
                    $(".gst-block").hide();
                    var gstradio_text= $("input[name='gstradio']:checked").next('label').text();
                    $(".gst-text-block").html("Selected Invoice type :- <b>"+gstradio_text+"</b>");
                    var gst_cgst = '<input type="hidden" id="cgst" name="cgst[]" class="input-sm cgst" value="0" readonly=""/>'
                                    +'<input type="hidden" id="cgst_amt" name="cgst_amt[]" class="input-sm cgst_amt" value="" placeholder="CGST 0%" readonly=""/>'
                                    +'<span class="input-sm spcgst_amt" id="spcgst_amt" name="spcgst_amt[]">0%</span>';

                    var gst_sgst = '<input type="hidden" id="sgst" name="sgst[]" class="input-sm sgst" value="0" readonly=""/>'
                                    +'<input type="hidden" id="sgst_amt" name="sgst_amt[]" class="input-sm sgst_amt" value="" placeholder="SGST 0%" readonly=""/>'
                                    +'<span class="input-sm spsgst_amt" id="spsgst_amt" name="spsgst_amt[]">0%</span>';
                                
                    var gst_igst =  '<input type="hidden" id="igst" name="igst[]" class="input-sm igst" value="0" readonly=""/>'
                                    +'<input type="hidden" id="igst_amt" name="igst_amt[]" class="input-sm igst_amt" value="" placeholder="IGST 0%" readonly=""/>'
                                    +'<span class="input-sm spigst_amt" id="spigst_amt" name="spigst_amt[]">0%</span>'
                                    +'<input type="hidden" class="input-sm tax" id="tax" name="tax[]" />';
                    $(".gst_cgst").html(gst_cgst);
                    $(".gst_sgst").html(gst_sgst);
                    $(".gst_igst").html(gst_igst);
                }else{
                    $(this).val('');
                    return false;
                }
            }
        });
        $('#total_charges').change(function(){
            var total_price = $('#total_basic_amt').val(), basic_percent = 0, total_charges = $('#total_charges').val();
            var total_basic_percent = total_price / 100;
            var total_charges = total_charges/100;
            $('tr').each(function () {
                $(this).find('.basic').each(function () {
                    var total_basic = $(this).val();
                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                        basic_percent = parseFloat(total_basic)/total_basic_percent;
                        var ce = $(this);
                        var chrgs_amount = total_charges * basic_percent;
                        $(ce).closest('td').parent('tr').find(".basic_percent").val(basic_percent);
                        $(ce).closest('td').parent('tr').find(".chrgs_amt").val(chrgs_amount.toFixed(2));
                        $(ce).closest('td').parent('tr').find(".spchrgs_amt").text(chrgs_amount.toFixed(2));
                    }
                });
            });
            $(".qty").trigger("keyup");
        });
    });
    $(document).ready(function(){
        $('#discount_switch').on('click',function () {
            if($(this).prop("checked") === true){
                //$('.discount_per').prop('readonly', true);
                $('.discount_amt').prop('readonly', true);
                $('#total_discount').removeAttr('readonly', true);
            }
            else if($(this).prop("checked") === false){
                $('.discount_amt').removeAttr('readonly', true);
                $('#total_discount').prop('readonly', true);
                //$('.discount_per').removeAttr('readonly', true);
            }
        });
    });
    var barcodes = []; var qty1=1; var $_blockDelete = false;
    $(document).on('keydown', 'input[id=barcode]', function(e) {
        var keyCode = e.keyCode || e.which; 
        if (keyCode === 13 && $(this).val() !== '') {
            var ce = $(this);
            if($(ce).closest('td').parent('tr').find(".skutype").val() != '4'){
                var skulength = +$(ce).closest('td').parent('tr').find(".skulenght").val();
                if(skulength!='' && $(this).val().length!=skulength){
                    alert('Enter '+skulength+' digit valid IMEI');
                    return false;
                }
                if (barcodes.includes($(this).val()) === false){
                    barcodes.push($(this).val()); 
                    $('#imeiscanned').val(barcodes);
                    e.preventDefault();
                    var ce = $(this);
                    $(ce).closest('td').parent('tr').find(".scanned").append(''+$(this).val()+',');
                    qty1 = +$(ce).closest('td').parent('tr').find(".qty1").val() - 1;
                    $(ce).closest('td').parent('tr').find(".scanned1").append('<input type="text" class="btn btn-sm btn-warning imeino" name="imeino[]" id="imeino" readonly="" style="margin:2px" value="'+$(this).val()+'" "/>');

                    $(ce).closest('td').parent('tr').find(".qty").attr('readonly', true);
                    $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                    $(this).val('');
                    if(qty1 === 0){
                        $(this).prop('readonly', true);
                        //$(ce).closest('td').parent('tr').find(".qty").removeAttr('readonly');
                    }
                }
                else{
                    alert('duplicate IMEI/SRNO scanned');
                    return false;
                }
            }
        }
    });
    var scanned;
    $(document).on('click', '.imeino', function() {
        var barid = $(this).val();
        if (confirm('Are you sure? You want to remove imei/srno: '+ barid)) {
            var ce = $(this);
            var qty = $(ce).closest('td').parent('tr').find(".qty1").val();
            $(ce).closest('td').parent('tr').find(".qty1").val(parseInt(qty)+1);
            barcodes = jQuery.grep(barcodes, function(value) { return value !== barid; });
            $('#imeiscanned').val(barcodes);
           
            var TextSearch = $(ce).closest('td').parent('tr').find(".scanned").val();
            scanned = TextSearch.replace(barid+',', '');
            $(ce).closest('td').parent('tr').find(".scanned").text(scanned);
           
            $(this).fadeOut();
            var qty = $(ce).closest('td').parent('tr').find(".qty1").val();
            var org_qty = $(ce).closest('td').parent('tr').find(".qty").val();
            if(qty>0){
              $(ce).closest('td').parent('tr').find(".barcode").removeAttr("readonly");
            }
            if(qty==org_qty){
              $(ce).closest('td').parent('tr').find(".qty").removeAttr("readonly");
            }
        }
    });
    
    $(document).on('click', '.btn-sub', function() {
        var qty1=0;
        $('tr').each(function () {
            $(this).find('.qty1').each(function () {
                qty1 += parseFloat($(this).val());                    
            });                  
        });
        if (qty1==0){
            if(!confirm('Do you want to submit purchase inward!!!')){
                return false;
            }
        }  else{
            alert("Please scan all IMEIs");
            return false;
        }
    });
    $(document).on('change', '.csvfile', function() {
        var csv_label = $(this).closest('td').find(".csv_label");
        var parrent = $(this).closest('td').parent('tr');
        if($(this).val() != ''){
            csv_label.html('<span class="green-text">File uploaded</span>');
            parrent.find(".barcode").hide();
            parrent.find(".qty1").val(0);
            parrent.find(".clear_selection").show();
            parrent.find(".scanned").val('');
            parrent.find(".scanned1").html('');
        }else{
            csv_label.html('<span class="red-text">Not uploaded</span>');
            var old_qty = parrent.find(".qty").val();
            parrent.find(".qty1").val(old_qty);
            parrent.find(".barcode").show();
            parrent.find(".clear_selection").hide();
            parrent.find(".barcode").removeAttr("readonly");
        }
    });
    $(document).on('click', '.clear_selection', function() {
        var parrent = $(this).closest('td').parent('tr');
        var csvfile = parrent.find(".csvfile");
        if(csvfile.val() != ''){
            if (confirm('Are you sure? You want to clear selected file')) {
                csvfile.val('');
                parrent.find('.csv_label').html('<span class="red-text">Not uploaded</span>');
                parrent.find(".barcode").show();
                var old_qty = parrent.find(".qty").val();
                parrent.find(".qty1").val(old_qty);
                parrent.find(".clear_selection").hide();
                parrent.find(".barcode").removeAttr("readonly");
            }
        }
    });
    $(document).on('keyup', 'input[id=qty], input[id=discount_per], input[id=discount_amt], input[id=chrgs_amt], input[id=price], input[id=total_discount]', function() {
        var qty=0,price=0,discount_amt=0,chrgs_amt=0,discount_per=0,total=0,cgst=0,sgst=0,igst=0,cgst_amt=0,sgst_amt=0,igst_amt=0,tax=0,basic=0,taxable=0;
        if(!$("input[name='gstradio']").is(":checked")){
            alert('Select GST/Non-GST');
            return false;
        }
        if ($(this).val()) {
            var ce = $(this);
            qty = isNaN($(ce).closest('td').parent('tr').find(".qty").val()) ? 0 : $(ce).closest('td').parent('tr').find(".qty").val();
            price = isNaN($(ce).closest('td').parent('tr').find(".price").val()) ? 0 : $(ce).closest('td').parent('tr').find(".price").val();
            basic = isNaN($(ce).closest('td').parent('tr').find(".basic").val()) ? 0 : $(ce).closest('td').parent('tr').find(".basic").val();
            chrgs_amt = isNaN($(ce).closest('td').parent('tr').find(".chrgs_amt").val()) ? 0 : $(ce).closest('td').parent('tr').find(".chrgs_amt").val();
            discount_per = isNaN($(ce).closest('td').parent('tr').find(".discount_per").val()) ? 0 : $(ce).closest('td').parent('tr').find(".discount_per").val();
            discount_amt = isNaN($(ce).closest('td').parent('tr').find(".discount_amt").val()) ? 0 : $(ce).closest('td').parent('tr').find(".discount_amt").val();
            
            if($('#state').val() == 0){
                cgst = $(ce).closest('td').parent('tr').find(".cgst").val();
                sgst = $(ce).closest('td').parent('tr').find(".sgst").val();
                cgst_amt = $(ce).closest('td').parent('tr').find(".cgst_amt").val();
                sgst_amt = $(ce).closest('td').parent('tr').find(".sgst_amt").val();
            }else{
                igst = $(ce).closest('td').parent('tr').find(".igst").val();
                igst_amt = $(ce).closest('td').parent('tr').find(".igst_amt").val();
            }
//          Calculation
            basic = qty * price; 
            basic = basic.toFixed(2);
            taxable = basic - discount_amt;
            taxable = taxable + +chrgs_amt;
            cgst_amt = (taxable * cgst)/100;
            cgst_amt = cgst_amt.toFixed(2);
            sgst_amt = (taxable * sgst)/100;
            sgst_amt = sgst_amt.toFixed(2);
            igst_amt = (taxable * igst)/100;
            igst_amt = igst_amt.toFixed(2);
            tax = +cgst_amt + +sgst_amt + +igst_amt;
            total = taxable + tax;
            // Assign values
            $(ce).closest('td').parent('tr').find(".basic").val(basic);
            $(ce).closest('td').parent('tr').find(".spbasic").html(basic);
            $(ce).closest('td').parent('tr').find(".discount_amt").val(discount_amt);
            $(ce).closest('td').parent('tr').find(".discount_per").val(discount_per);
            $(ce).closest('td').parent('tr').find(".taxable").val(taxable);
            $(ce).closest('td').parent('tr').find(".sptaxable").html(taxable);
            $(ce).closest('td').parent('tr').find(".cgst_amt").val(cgst_amt);
            $(ce).closest('td').parent('tr').find(".spcgst_amt").html(+cgst_amt+'<br>('+cgst+'%)');
            $(ce).closest('td').parent('tr').find(".sgst_amt").val(sgst_amt);
            $(ce).closest('td').parent('tr').find(".spsgst_amt").html(sgst_amt+'<br>('+sgst+'%)');
            $(ce).closest('td').parent('tr').find(".igst_amt").val(igst_amt);
            $(ce).closest('td').parent('tr').find(".spigst_amt").html(igst_amt+'<br>('+igst+'%)');
            $(ce).closest('td').parent('tr').find(".tax").val(tax);
            $(ce).closest('td').parent('tr').find(".total").val(total.toFixed(2));
            $(ce).closest('td').parent('tr').find(".sptotal").html(total.toFixed(2));
            
            var total_basic_sum=0,sum_total_taxable=0,sum_total_cgst_amt=0,sum_total_sgst_amt=0,sum_total_igst_amt=0,sum_total_gross_amt=0, sum_total_discount=0,sum_total_tax=0,sum_total_price=0;
            $('tr').each(function () {
            // basic cal
                $(this).find('.basic').each(function () {
                    var total_basic = $(this).val();
                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                        total_basic_sum += parseFloat(total_basic);
                    }
                });
            // taxable cal
                $(this).find('.taxable').each(function () {
                    var total_taxable = $(this).val();
                    if (!isNaN(total_taxable) && total_taxable.length !== 0) {
                        sum_total_taxable += parseFloat(total_taxable);
                    }
                });
            // cgst cal
                $(this).find('.cgst_amt').each(function () {
                    var total_cgst_amt = $(this).val();
                    if (!isNaN(total_cgst_amt) && total_cgst_amt.length !== 0) {
                        sum_total_cgst_amt += parseFloat(total_cgst_amt);
                    }
                });
            // sgst cal
                $(this).find('.sgst_amt').each(function () {
                    var total_sgst_amt = $(this).val();
                    if (!isNaN(total_sgst_amt) && total_sgst_amt.length !== 0) {
                        sum_total_sgst_amt += parseFloat(total_sgst_amt);
                    }
                });
            // cgst cal
                $(this).find('.igst_amt').each(function () {
                    var total_igst_amt = $(this).val();
                    if (!isNaN(total_igst_amt) && total_igst_amt.length !== 0) {
                        sum_total_igst_amt += parseFloat(total_igst_amt);
                    }
                });
            // gross total cal
                $(this).find('.total').each(function () {
                    var total_gross_amt = $(this).val();
                    if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                        sum_total_gross_amt += parseFloat(total_gross_amt);
                    }
                });
            // total discount
                $(this).find('.discount_amt').each(function () {
                    var total_disocunt = $(this).val();
                    if (!isNaN(total_disocunt) && total_disocunt.length !== 0) {
                        sum_total_discount += parseFloat(total_disocunt);
                    }
                });
//                $('#total_discount_label', this).html(sum_total_discount.toFixed(2));
            // total tax
                $(this).find('.tax').each(function () {
                    var total_tax = $(this).val();
                    if (!isNaN(total_tax) && total_tax.length !== 0) {
                        sum_total_tax += parseFloat(total_tax);
                    }
                });
                // total price
                $(this).find('.price').each(function () {
                    var total_price = $(this).val();
                    if (!isNaN(total_price) && total_price.length !== 0) {
                        sum_total_price += parseFloat(total_price);
                    }
                });
            });
            $('#total_basic_amt').val(total_basic_sum.toFixed(2));
            $('#total_basic_amt_label').html(total_basic_sum.toFixed(2));
            $('#total_taxable_amt').val(sum_total_taxable.toFixed(2));
            $('#total_taxable_amt_label').html(sum_total_taxable.toFixed(2));
            $('#total_cgst_amt').val(sum_total_cgst_amt.toFixed(2));
            $('#total_cgst_amt_label').html(sum_total_cgst_amt.toFixed(2));
            $('#total_sgst_amt').val(sum_total_sgst_amt.toFixed(2));
            $('#total_sgst_amt_label').html(sum_total_sgst_amt.toFixed(2));
            $('#total_igst_amt').val(sum_total_igst_amt.toFixed(2));
            $('#total_igst_amt_label').html(sum_total_igst_amt.toFixed(2));
            $('#gross_total').val(sum_total_gross_amt.toFixed(2));
            $('#gross_total_label').html(sum_total_gross_amt.toFixed(2));
            // discount minus
            var overall_discount = +$('#overall_discount').val();
            var minus_discount = sum_total_gross_amt - overall_discount;
            $('#final_total').val(minus_discount.toFixed(2));
            $('#final_total_test').val(minus_discount.toFixed(2));
            $('#final_total_label').html(minus_discount.toFixed(2));
            $('#total_discount').val(sum_total_discount);
            $('#total_tax').val(sum_total_tax.toFixed(2));
            $('#total_tax_label').html(sum_total_tax.toFixed(2));
            $('#total_item_rate').val(sum_total_price.toFixed(2));
            var overall_amount = +minus_discount + +$('#tcs_amount').val();
            $('#overall_amount').val(overall_amount.toFixed(2));
            $('#overall_amount_label').html(overall_amount.toFixed(2));
        }
    });
    $(document).on('keyup', '#tcs_amount', function() {
        var final_total = +$('#final_total').val();
        var overall_amount = final_total + +$(this).val();
        $('#overall_amount').val(overall_amount.toFixed(2));
        $('#overall_amount_label').html(overall_amount.toFixed(2));
    });
    $(document).on('keyup', '#overall_discount', function() {
        var overall_discount=0,final_total=0,dicount_minus=0,overall_amount=0;
        overall_discount = $(this).val();
        final_total = +$('#gross_total').val();
        dicount_minus = final_total - overall_discount;
        dicount_minus = dicount_minus.toFixed(2);
        $('#final_total').val(dicount_minus);
        $('#final_total_label').html(dicount_minus);
        
        overall_amount = +dicount_minus + +$('#tcs_amount').val();
        $('#overall_amount').val(overall_amount.toFixed(2));
        $('#overall_amount_label').html(overall_amount.toFixed(2));
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Purchase Direct Inward </h3></center></div><div class="clearfix"></div>
<div class="panel panel-info panel-body" style="padding: 15px; margin: 0; font-size: 13px;">
    <form method="post" id="submit_inward_form" enctype="multipart/form-data">
        <div class="col-md-6">
            <div class="col-md-3 text-muted">Date</div>
            <div class="col-md-9">
                <?php echo date('d-m-Y',  strtotime($purchase_order->date)) ?>
                <input type="hidden" name="date" value="<?php echo $now ?>" />
                <input type="hidden" name="id_purchase_direct_inward" value="<?php echo $purchase_order->id_purchase_direct_inward ?>" />
                <input type="hidden" name="idbranch" value="<?php echo $purchase_order->id_branch ?>" />
                <input type="hidden" class="input-sm" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                <input type="hidden" class="input-sm" name="branch_code" value="<?php echo $purchase_order->branch_code ?>"/>
            </div>
            <div class="col-md-3 text-muted">ID</div>
            <div class="col-md-9"><?php echo $purchase_order->financial_year.'-'.$purchase_order->id_purchase_direct_inward ?></div><div class="clearfix"></div>
            <div class="col-md-3 text-muted">Warehouse</div>
            <div class="col-md-9"><?php echo $purchase_order->branch_name ?></div><div class="clearfix"></div>
            <div class="col-md-3 text-muted">Warehouse State</div>
            <div class="col-md-9"><?php echo $purchase_order->branch_state_name ?></div><div class="clearfix"></div>
        </div>
        <div class="col-md-6">
            <div class="col-md-2 text-muted">Vendor</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_name ?>
                <input type="hidden" name="idvendor" value="<?php echo $purchase_order->idvendor ?>" />
            </div>
            <div class="col-md-2 text-muted">Contact</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_contact ?></div><div class="clearfix"></div>
            <div class="col-md-2 text-muted">State</div>
            <div class="col-md-10"><?php echo $purchase_order->state ?>
                <input type="hidden" name="state" value="<?php echo $purchase_order->state ?>" />
            </div><div class="clearfix"></div>
            <div class="col-md-2 text-muted">GSTIN</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_gst ?></div><div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
        <div class="col-md-1">Supplier Invoice Date</div>
        <div class="col-md-2">
            <input type="text" class="form-control input-sm datepick required" name="inv_date" placeholder="Supplier Invoice Date" autocomplete="off" onfocus="blur()"  value="<?php echo $now ?>" required="" />
        </div>
        <div class="col-md-1">Supplier Invoice No</div>
        <div class="col-md-2">
            <input type="text" class="form-control input-sm required" name="supplier_inv" placeholder="Supplier Invoice No" required=""/>
        </div>
        <div class="gst-block col-md-6">
            <div class="col-md-4">Select Invoice Type</div>
            <div class="col-md-8">
                <div class="col-md-6">
                    <input class="form-check-input gstradio" type="radio" name="gstradio" id="gst" value="1">
                    <label class="form-check-label" for="gst">GST Invoice</label>
                </div>
                <div class="col-md-6">
                    <input class="form-check-input gstradio" type="radio" name="gstradio" id="nongst" value="0">
                    <label class="form-check-label" for="nongst">Non GST Invoice</label>
                </div>
            </div>
        </div>
        <div class="col-md-4 pull-right gst-text-block"></div>
        <div class="clearfix"></div>
        <center class="blink red-text"><i class="mdi mdi-alert"></i> 
            <!--Check product quantity before file upload, Convert excel file into CSV file format.<br>-->
            Excel फाईलला CSV फाइल स्वरूपनात रूपांतरित करा आणि फाइल अपलोड करण्यापूर्वी Product Quantity तपासून पहा.</center>
        <div class="clearfix"></div>
        <input type="hidden" id="imeiscanned" name="imeiscanned" class="form-control" />
        <div class="clearfix"></div>
        <?php if($purchase_order->state == $purchase_order->branch_state_name){ ?>
            <input type="hidden" id="state" value="0" />
        <?php }else{ ?>
            <input type="hidden" id="state" value="1" />
        <?php } ?>
            <div class="thumbnail" style="overflow: auto; padding: 0">
                <table id="branch_data" class="table table-condensed table-hover table-bordered" style="margin-bottom: 0">
                    <thead class="bg-info">
                        <th>Id</th>
                        <th class="col-md-4">Product</th>
                        <th class="col-md-1">Godown</th>
                        <th>Qty</th>
                        <th class="col-md-1">MRP</th>
                        <th class="col-md-1">Rate</th>
                        <th>Basic</th>
                        <!--<th>Charges</th>-->
                        <th>
                            <span class="col-md-8" style="padding: 0">Model Disc</span>
                            <span class="col-md-4" style="padding: 0">
                                <div class="material-switch" style="margin-top: -5px;">
                                    <input id="discount_switch" name="discount_switch" type="checkbox" checked="" /> 
                                    <label for="discount_switch" class="label-primary"></label> 
                                </div>
                            </span>
                        </th>
                        <th>Taxable</th>
<!--                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>-->
                        <th>Amount</th>
                        <th>Scan</th>
                        <th>Scanned IMEI</th>
                        <th style="min-width: 150px">Upload File</th>
                        <th>File</th>
                    </thead>
                    <tbody id="selected_model" class="data_1">
                        <?php // echo '<pre>'.print_r($purchase_order_product,1).'</pre>'; 
                       $idmodels = ''; foreach ($purchase_order_product as $product){  if($product->cgst == 0){ ?>
                    <tr class="fadeout_nongst"><td colspan="1"><h4 style="color: #cc0033"><i class="mdi mdi-alert"></i> Please Setup GST Rates for <?php echo $product->full_name; ?>...</h4></td></tr>
                    <?php }else{ ?>
                    <tr class="product_data" id="m<?php echo $product->id_variant?>">
                        <td><?php echo $product->id_variant; ?></td>
                        <td><?php echo $product->full_name; ?></td>
                        <td>
                            <input type="hidden" id="id_purchase_order_product" class="id_purchase_order_product" name="id_purchase_order_product[]" value="<?php echo $product->id_purchase_direct_inward_product; ?>" />
                            <select class="form-control input-sm" id="idgodown" class="idgodown" name="idgodown[]" style="width: 100px" required="" />
                                <option value="">Select Godown</option>
                                <?php foreach ($godown_data as $godown){ if($godown->id_godown == 1 || $godown->id_godown == 2){ ?>
                                <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
                                <?php }} ?>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" id="idtype" class="idtype" name="idtype[]" value="<?php echo $product->idproductcategory ?>" />
                            <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $product->idcategory ?>" />
                            <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $product->idbrand ?>" />
                            <input type="hidden" id="idmainmodel" class="idmainmodel" name="idmainmodel[]" value="<?php echo $product->idmodel ?>" />
                            <input type="hidden" id="idmodel" class="idmodel" name="idmodel[]" value="<?php echo $product->id_variant ?>" />
                            <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $product->idsku_type ?>" />
                            <input type="hidden" id="skulenght" class="skulenght" name="skulenght[]" value="<?php echo $product->sku_lenght ?>" />
                            <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $product->full_name; ?>" />
                            <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty required" placeholder="Qty" required="" min="1" value="<?php echo $product->qty; ?>" />
                            <?php echo $product->qty; ?>
                        </td>
                        <td class="col-md-1">
                            <input type="text" id="mrp" name="mrp[]" class="form-control input-sm mrp" required="" placeholder="MRP" min="1" style="width: 80px"/>
                        </td>
                        <td class="col-md-1">
                            <input type="text" id="price" name="price[]" class="form-control required input-sm price" required="" placeholder="Price" min="1" style="width: 80px"/>
                        </td>
                        <td>
                            <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="0" min="0"/>
                            <span class="input-sm spbasic" id="spbasic" name="spbasic[]">0</span>
                            <input type="hidden" id="basic_percent" name="basic_percent[]" class="basic_percent"/>
        <!--                </td>
                        <td class="col-md-1">                    -->
                            <input type="hidden" id="chrgs_amt" name="chrgs_amt[]" class="chrgs_amt input-sm" readonly="" placeholder="Amount" value="0"/>
                            <span class="hidden spchrgs_amt" id="spchrgs_amt" name="spchrgs_amt[]">0</span>
                        </td>
                        <td class="col-md-1">
                            <input type="hidden" id="discount_per" name="discount_per[]" class="discount_per input-sm" placeholder="Percentage" value="0" />
                            <input type="text" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" readonly="" style="width: 80px" />
                        </td>
                        <td>
                            <input type="hidden" id="taxable" name="taxable[]" class="taxable" placeholder="Taxable" readonly="" value="0"/>
                            <span class="input-sm sptaxable" id="sptaxable" name="sptaxable[]">0</span>
                        </td>
                        <td class="gst_cgst hidden">
                            <input type="hidden" id="cgst" name="cgst[]" class="input-sm cgst" value="<?php echo $product->cgst; ?>" readonly=""/>
                            <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="input-sm cgst_amt" value="" placeholder="CGST <?php echo $product->cgst; ?>%" readonly=""/>
                            <span class="input-sm spcgst_amt" id="spcgst_amt" name="spcgst_amt[]"><?php echo $product->cgst; ?>%</span>
                        </td>
                        <td class="gst_sgst hidden">
                            <input type="hidden" id="sgst" name="sgst[]" class="input-sm sgst" value="<?php echo $product->sgst; ?>" readonly=""/>
                            <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="input-sm sgst_amt" value="" placeholder="SGST <?php echo $product->sgst; ?>%" readonly=""/>
                            <span class="input-sm spsgst_amt" id="spsgst_amt" name="spsgst_amt[]"><?php echo $product->sgst; ?>%</span>
                        </td>
                        <td class="gst_igst hidden">
                            <input type="hidden" id="igst" name="igst[]" class="input-sm igst" value="<?php echo $product->igst; ?>" readonly=""/>
                            <input type="hidden" id="igst_amt" name="igst_amt[]" class="input-sm igst_amt" value="" placeholder="IGST <?php echo $product->igst; ?>%" readonly=""/>
                            <span class="input-sm spigst_amt" id="spigst_amt" name="spigst_amt[]"><?php echo $product->igst; ?>%</span>
                            <input type="hidden" class="input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                        </td>
                        <td>
                            <input type="hidden" class="total" id="total" name="total[]" placeholder="Total Amount" value="0"/>
                            <span class="input-sm sptotal" id="sptotal" name="sptotal[]">0</span>
                        </td>
                        <?php if($product->idsku_type == 4){ ?>
                        <td>
                            <input type="hidden" id="qty1<?php echo $product->id_variant ?>" name="qty1[]" class="input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 100px" value="0" />
                            <input type="hidden" id="qty2<?php echo $product->id_variant ?>" name="qty2[]" class="input-sm qty2" placeholder="Qty1" readonly="" style="margin: 0" value="0"/>
                        </td>
                        <td>
                            <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="2" placeholder="Scanned IMEI" style="display: none"></textarea>
                        </td>
                        <?php }else{ ?>
                        <td>
                            <div id="mn<?php echo $product->id_variant?>" style="width: 200px">
                                <input type="hidden" id="idmodel" class="idmodel" value="<?php echo $product->id_variant ?>" />
                                <div class="col-md-3" style="padding: 0; margin: 0;">
                                    <input type="text" id="qty1<?php echo $product->id_variant?>" name="qty1[]" class="form-control input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 60px" value="<?php echo $product->qty; ?>" />
                                    <input type="hidden" id="qty2<?php echo $product->id_variant?>" name="qty2[]" class="qty2" value="<?php echo $product->qty; ?>"/>
                                </div>
                                <div class="col-md-9" style="padding: 0; margin: 0;">
                                    <input type="text" id="barcode" name="barcode[]" class="form-control input-sm barcode"  value="" placeholder="Scan IMEI" style="margin: 0; width: 150px"/>
                                </div>
                            </div>
                        </td>
                        <td>
                            <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="2" placeholder="Scanned IMEI" style="display: none"></textarea>
                            <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                        </td>
                        <?php } ?>
                        <td>
                            <input type="file" class="form-control input-sm csvfile" id="csvfile" name="csvfile[]" style="min-width: 100px" />
                            <center class="csv_label"><span class="red-text">Not uploaded</span></center>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="red-text waves-effect clear_selection" id="clear_selection" style="text-transform: capitalize; display: none">Clear</a>
                        </td>
                    </tr>
                    <?php $idmodels .= $product->id_variant.','; }} ?>
                    </tbody>
                </table>
            </div>
        <input type="hidden" id="modelid" name="modelid" value="<?php echo rtrim($idmodels,','); ?>" />
        <div class="col-md-offset-5 thumbnail col-md-6" style="padding: 0">
            <table class="table table-striped" style="margin-bottom: 0">
                <tbody>
                    <tr>
                        <td>Total Basic</td>
                        <td>
                            <input type="hidden" id="total_basic_amt" name="total_basic_amt"  value="0"/>
                            &nbsp; <span id="total_basic_amt_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Freight/Other Charges</td>
                        <td>
                           <input type="text" class="form-control input-sm" id="total_charges" name="total_charges" value="0" style="width: 200px" />
                        </td>
                    </tr>
                    <tr>
                        <td>Total Model Discount</td>
                        <td>
                           <input class="form-control input-sm" type="text" id="total_discount" name="total_discount" value="0" style="width: 200px" />
                        </td>
                    </tr>
                    <tr>
                        <td>Taxable</td>
                        <td>
                           <input type="hidden" class="total_taxable_amt" name="total_taxable_amt" id="total_taxable_amt" value="0"/>
                            &nbsp; <span id="total_taxable_amt_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>CSGT</td>
                        <td>
                            <input type="hidden" name="total_cgst_amt" id="total_cgst_amt" value="0"/>
                            &nbsp; <span name="total_cgst_amt_label" id="total_cgst_amt_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>SGST</td>
                        <td>
                            <input type="hidden" name="total_sgst_amt" id="total_sgst_amt" value="0"/>
                            &nbsp; <span name="total_sgst_amt_label" id="total_sgst_amt_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>IGST</td>
                        <td>
                            <input type="hidden" name="total_igst_amt" id="total_igst_amt" value="0"/>
                            &nbsp; <span name="total_igst_amt_label" id="total_igst_amt_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Tax</td>
                        <td>
                            <input type="hidden" id="total_tax" name="total_tax" value="0"/>
                            &nbsp; <span id="total_tax_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Gross Total</td>
                        <td>
                            <input type="hidden" name="gross_total" id="gross_total" class="grand_total" value="0" />
                            &nbsp; <span name="gross_total_label" id="gross_total_label">0</span>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Discount After GST</td>
                        <td><input type="text" class="form-control input-sm" name="overall_discount" id="overall_discount" placeholder="Overall Discount in rupees" value="0" style="width: 200px" /></td>
                    </tr>
                    <tr>
                        <td>Final Amount</td>
                        <td>
                            <input type="hidden" name="final_total_test" id="final_total_test" value="0" />
                            <input type="hidden" name="final_total" id="final_total" class="final_total" value="0" />
                            &nbsp; <span id="final_total_label">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>TCS Amount</td>
                        <td>
                            <input type="number" class="form-control input-sm required" name="tcs_amount" id="tcs_amount" placeholder="Add TCS Amount" value="0" style="width: 200px" required=""/>
                        </td>
                    </tr>
                    <tr>
                        <td>Overall Total</td>
                        <td>
                            <input type="hidden" name="overall_amount" id="overall_amount" class="overall_amount" value="0" />
                            &nbsp; <span id="overall_amount_label">0</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-1 col-md-offset-5">Remark</div>
        <div class="col-md-4">
            <input type="text" class="form-control input-sm required" name="remark" placeholder="Enter Remark" required="">
            <input type="hidden" name="direct_inward" value="1">
        </div>
        <div class="col-md-2">
            <!--<button type="submit" class="btn btn-primary gradient2 waves-effect waves-light btn-sub" id="inward_submit">Submit</button>-->
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Purchase/save_purchase_inward') ?>" class="btn btn-primary gradient2 waves-effect waves-light btn-sub" id="inward_submit">Submit</button>
        </div>
    </form>
</div>
<?php }else{ ?>
    <div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Purchase Inward</center></div><div class="clearfix"></div><hr>
    <center><h3><i class="mdi mdi-alert"></i> You selected wrong PO, This PO is not approved or pending or already inwarded. </h3>
        <img src="<?php echo base_url('assets/images/highAlertIcon.gif') ?>" />
    </center>
<?php } include __DIR__ . '../../footer.php'; ?>