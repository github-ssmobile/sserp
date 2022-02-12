<?php include __DIR__ . '../../header.php'; ?>
<script>
    var products = [];
    window.onload=function() { 
        $("#sidebar").addClass("active");
        <?php foreach ($purchase_order_product as $product){ ?>
        products.push('<?php echo $product->id_variant ?>');
        <?php } ?>
    };
    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode === 13) {
            event.preventDefault();
            return false;
          }
        });
        $('#total_discount').keyup(function(){
            var total_price = $('#total_item_rate').val(), price_percent = 0, total_discount = $('#total_discount').val();
            var total_price_percent = total_price / 100;
            var total_discount = total_discount/100;
            $('tr').each(function () {
                $(this).find('.price').each(function () {
                    var total_basic = $(this).val();
                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                        price_percent = parseFloat(total_basic)/total_price_percent;
                        var ce = $(this);
                        var dis_amount = total_discount * price_percent;
                        $(ce).closest('td').parent('tr').find(".price_percent").val(price_percent);
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
        $('#total_charges').keyup(function(){
            var total_price = $('#total_item_rate').val(), price_percent = 0, total_charges = $('#total_charges').val();
            var total_price_percent = total_price / 100;
            var total_charges = total_charges/100;
            $('tr').each(function () {
                $(this).find('.price').each(function () {
                    var total_basic = $(this).val();
                    if (!isNaN(total_basic) && total_basic.length !== 0) {
                        price_percent = parseFloat(total_basic)/total_price_percent;
                        var ce = $(this);
                        var chrgs_amount = total_charges * price_percent;
                        $(ce).closest('td').parent('tr').find(".price_percent").val(price_percent);
                        $(ce).closest('td').parent('tr').find(".chrgs_amt").val(chrgs_amount.toFixed(2));
                        $(ce).closest('td').parent('tr').find(".spchrgs_amt").text(chrgs_amount.toFixed(2));
                    }
                });
            });
            $(".qty").trigger("keyup");
        });
    });
    $(document).ready(function(){
        $('#model').change(function(){
            var j=0;
            if(!$("input[name='gstradio']").is(":checked")){
                alert('Select GST/Non-GST');
                return false;
            }
            var sel_idgodown = $('#sel_idgodown').val();
            var sel_godown_text = $("#sel_idgodown option:selected").text();
            if(sel_idgodown == ''){
                alert('Select Godawn to Inward');
                return false;
            }
            var gstradio= $("input[name='gstradio']:checked").val();
            var gstradio_text= $("input[name='gstradio']:checked").next('label').text();
            var id;
            id = $(this).val();
            
            if (products.includes(id) === false){
                products.push(id); 
            }
            else{
                alert('duplicate product selected');
                return false;
            }
            $('#modelid').val(products);
            var id = $(this).val();
            $.ajax({
                url:"<?php echo base_url() ?>Purchase/ajax_get_product_byid",
                method:"POST",
                data:{id : id,gstradio:gstradio,sel_idgodown:sel_idgodown, sel_godown_text:sel_godown_text},
                success:function(data)
                {
//                    alert(data);
                    $("#product").css("display", "block");
                    $("#selected_model").append(data);
                    ++j;
                    $('#count').val(j);
                    $("#selsupplier_block").hide();
                    $("#display_supplier_block").show();
                    $(".gst-block").hide();
                    $(".gst-text-block").html("Selected Invoice type :- <b>"+gstradio_text+"</b>")
                    window.setTimeout(function() {
                        $(".fadeout_nongst").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, 3000);
                }
            });
        });
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
    $(document).on('change', 'input[id=qty]', function() {
        var ce = $(this);
        var id = $(ce).closest('td').parent('tr').find(".idmodel").val();
        var skutype = $(ce).closest('td').parent('tr').find(".skutype").val();
        var qty = $(this).val();
        $('#qty1'+id).val(qty);
        $('#qty2'+id).val(qty);
        if(qty > 0){
            if(skutype !== '4'){
                $('#mn'+id).show();
            }else{
                $('#qty1'+id).val(0);
                $('#qty2'+id).val(0);
            }
        }else{
            alert('Quantity not accepted');
            return false;
        }
    });
    $(document).on('click', 'a[id=remove]', function() {
        var j=0;
        var id = $(this).text();
        if (confirm('Are you sure? You want to remove product: '+id)) {
            var ce = $(this);
            var str = ce.closest('td').parent('tr');
            products = jQuery.grep(products, function(value) { return value !== id; });
            $('#modelid').val(products);
//            $('input[id=qty]').keyup();
//            $(".qty").trigger("keyup");
            --j;
            $('#count').val(j);
            var imeie = ce.closest('td').parent('tr').find(".scanned").text();
            var imeis = imeie.split(',');
            var i=0;
            for(i=0;i<(imeis.length-1);i++){
                barcodes = jQuery.grep(barcodes, function(value) {
                    return value !== imeis[i];
                }); 
            }
            $('#imeiscanned').val(barcodes);
            $(str).remove();
//            $('#mn'+id).remove();
            $(".qty").trigger("keyup");
        }
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
            var qty = $(ce).closest('td').parent('tr').find(".qty1").val()
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
            return true;
        }  else{
            alert("Please scan all IMEIs");
            return false;
        }
    });
    
    $(document).on('keyup', 'input[id=qty], input[id=price], input[id=discount_per], input[id=discount_amt], input[id=chrgs_amt], input[id=price], input[id=total_discount]', function() {
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
                $('#total_basic_amt', this).val(total_basic_sum.toFixed(2));
                $('#total_basic_amt_label', this).html(total_basic_sum.toFixed(2));
            // taxable cal
                $(this).find('.taxable').each(function () {
                    var total_taxable = $(this).val();
                    if (!isNaN(total_taxable) && total_taxable.length !== 0) {
                        sum_total_taxable += parseFloat(total_taxable);
                    }
                });
                $('#total_taxable_amt', this).val(sum_total_taxable.toFixed(2));
                $('#total_taxable_amt_label', this).html(sum_total_taxable.toFixed(2));
            // cgst cal
                $(this).find('.cgst_amt').each(function () {
                    var total_cgst_amt = $(this).val();
                    if (!isNaN(total_cgst_amt) && total_cgst_amt.length !== 0) {
                        sum_total_cgst_amt += parseFloat(total_cgst_amt);
                    }
                });
                $('#total_cgst_amt', this).val(sum_total_cgst_amt.toFixed(2));
                $('#total_cgst_amt_label', this).html(sum_total_cgst_amt.toFixed(2));
            // sgst cal
                $(this).find('.sgst_amt').each(function () {
                    var total_sgst_amt = $(this).val();
                    if (!isNaN(total_sgst_amt) && total_sgst_amt.length !== 0) {
                        sum_total_sgst_amt += parseFloat(total_sgst_amt);
                    }
                });
                $('#total_sgst_amt', this).val(sum_total_sgst_amt.toFixed(2));
                $('#total_sgst_amt_label', this).html(sum_total_sgst_amt.toFixed(2));
            // cgst cal
                $(this).find('.igst_amt').each(function () {
                    var total_igst_amt = $(this).val();
                    if (!isNaN(total_igst_amt) && total_igst_amt.length !== 0) {
                        sum_total_igst_amt += parseFloat(total_igst_amt);
                    }
                });
                $('#total_igst_amt', this).val(sum_total_igst_amt.toFixed(2));
                $('#total_igst_amt_label', this).html(sum_total_igst_amt.toFixed(2));
            // gross total cal
                $(this).find('.total').each(function () {
                    var total_gross_amt = $(this).val();
                    if (!isNaN(total_gross_amt) && total_gross_amt.length !== 0) {
                        sum_total_gross_amt += parseFloat(total_gross_amt);
                    }
                });
                $('#gross_total', this).val(sum_total_gross_amt.toFixed(2));
                $('#gross_total_label', this).html(sum_total_gross_amt.toFixed(2));
                $('#final_total', this).val(sum_total_gross_amt.toFixed(2));
                $('#final_total_test', this).val(sum_total_gross_amt.toFixed(2));
                $('#final_total_label', this).html(sum_total_gross_amt.toFixed(2));
            // total discount
                $(this).find('.discount_amt').each(function () {
                    var total_disocunt = $(this).val();
                    if (!isNaN(total_disocunt) && total_disocunt.length !== 0) {
                        sum_total_discount += parseFloat(total_disocunt);
                    }
                });
                $('#total_discount', this).val(sum_total_discount);
//                $('#total_discount_label', this).html(sum_total_discount.toFixed(2));
            // total tax
                $(this).find('.tax').each(function () {
                    var total_tax = $(this).val();
                    if (!isNaN(total_tax) && total_tax.length !== 0) {
                        sum_total_tax += parseFloat(total_tax);
                    }
                });
                $('#total_tax', this).val(sum_total_tax.toFixed(2));
                $('#total_tax_label', this).html(sum_total_tax.toFixed(2));
                // total price
                $(this).find('.price').each(function () {
                    var total_price = $(this).val();
                    if (!isNaN(total_price) && total_price.length !== 0) {
                        sum_total_price += parseFloat(total_price);
                    }
                });
                $('#total_item_rate', this).val(sum_total_price.toFixed(2));
            });
        }
    });
    $(document).on('keyup', '#overall_discount', function() {
        var overall_discount=0,final_total=0,dicount_minus=0;
        overall_discount = $(this).val();
        final_total = $('#final_total_test').val();
        dicount_minus = final_total - overall_discount;
        dicount_minus = dicount_minus.toFixed(2);
        $('#final_total').val(dicount_minus);
        $('#final_total_label').html(dicount_minus);
    });
</script>

<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Purchase Inward </h3></center></div><div class="clearfix"></div>
<div class="thumbnail" style="padding: 15px; margin: 0; overflow: auto; font-size: 13px;">
    <form>
        <div class="col-md-6">
            <div class="col-md-3 text-muted">PO Date</div>
            <div class="col-md-9">
                <?php echo date('d-m-Y',  strtotime($purchase_order->date)) ?>
                <input type="hidden" name="date" value="<?php echo $now ?>" />
                <input type="hidden" name="id_purchase_order" value="<?php echo $purchase_order->id_purchase_order ?>" />
                <input type="hidden" name="idbranch" value="<?php echo $purchase_order->id_branch ?>" />
                <input type="hidden" class="input-sm" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                <input type="hidden" class="input-sm" name="branch_code" value="<?php echo $purchase_order->branch_code ?>"/>
            </div>
            <div class="col-md-3 text-muted">PO ID</div>
            <div class="col-md-9"><?php echo $purchase_order->financial_year.'-'.$purchase_order->id_purchase_order ?></div><div class="clearfix"></div>
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
            <input type="text" class="form-control input-sm datepick" name="inv_date" placeholder="Supplier Invoice Date" autocomplete="off" onfocus="blur()"  value="<?php echo $now ?>" required="" />
        </div>
        <div class="col-md-1">Supplier Invoice No</div>
        <div class="col-md-2">
            <input type="text" class="form-control input-sm" name="supplier_inv" placeholder="Supplier Invoice No" required=""/>
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
        <input type="hidden" id="imeiscanned" name="imeiscanned" class="form-control" />
        <div class="clearfix"></div>
        <?php if($purchase_order->state == $purchase_order->branch_state_name){ ?>
            <input type="hidden" id="state" value="0" />
        <?php }else{ ?>
            <input type="hidden" id="state" value="1" />
        <?php } ?>
        <table id="branch_data" class="table table-condensed table-hover table-bordered">
            <thead class="gradient_thead">
                <th>Id</th>
                <th class="col-md-4">Product</th>
                <th class="col-md-1">Godown</th>
                <th class="col-md-1">Qty</th>
                <th class="col-md-1">Rate</th>
                <th>Basic</th>
                <th>Charges</th>
                <th>
                    <span class="col-md-8" style="padding: 0">Disc</span>
                    <span class="col-md-4" style="padding: 0">
                        <div class="material-switch" style="margin-top: -5px; display: none">
                            <input disabled="" id="discount_switch" name="discount_switch" type="checkbox" checked="" /> 
                            <label for="discount_switch" class="label-primary"></label> 
                        </div>
                    </span>
                </th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Amount</th>
                <th>Scan</th>
                <th>Scanned IMEI</th>
                <th><center>Remove</center></th>
            </thead>
            <tbody id="selected_model" class="data_1">
                <?php // echo '<pre>'.print_r($purchase_order_product,1).'</pre>'; 
               $idmodels = ''; foreach ($purchase_order_product as $product){  if($product->cgst == 0){ ?>
            <tr class="fadeout_nongst"><td colspan="1"><h4 style="color: #cc0033"><i class="mdi mdi-alert"></i> Please Setup GST Rates for <?php echo $product->type.' '.$product->category_name.' '.$product->brand_name.' '.$product->model_name; ?>...</h4></td></tr>
            <?php }else{ ?>
            <tr class="product_data" id="m<?php echo $product->id_variant?>">
                <td><?php echo $product->id_variant; ?></td>
                <td><?php echo $product->full_name; ?></td>
                <td class="col-md-1">
                    <input type="hidden" id="id_purchase_order_product" class="id_purchase_order_product" name="id_purchase_order_product[]" value="<?php echo $product->id_purchase_order_product; ?>" />
                    <!--<input type="hidden" id="idgodown" class="idgodown" name="idgodown[]" value="<?php // echo $product->idgodown; ?>" />-->
                    <?php // echo $product->godown_name; ?>
                    <select class="form-control input-sm" id="idgodown" class="idgodown" name="idgodown[]" required="" style="width: 100px" />
                        <?php foreach ($godown_data as $godown){ ?>
                        <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="idtype" class="idtype" name="idtype[]" value="<?php echo $product->idproductcategory ?>" />
                    <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $product->idcategory ?>" />
                    <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $product->idbrand ?>" />
                    <input type="hidden" id="idmainmodel" class="idmainmodel" name="idmainmodel[]" value="<?php echo $product->idmodel ?>" />
                    <input type="hidden" id="idmodel" class="idmodel" name="idmodel[]" value="<?php echo $product->id_variant ?>" />
                    <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $product->idsku_type ?>" />
                    <input type="hidden" id="skulenght" class="skulenght" name="skulenght[]" value="<?php echo $product->sku_lenght ?>" />
                    <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $product->full_name; ?>" />
                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" min="1" value="<?php echo $product->qty; ?>" style="width: 80px" />
                </td>
                <td class="col-md-1">
                    <input type="text" id="price" name="price[]" class="form-control input-sm price" required="" placeholder="Price" min="1" style="width: 80px"/>
                    <input type="hidden" id="price_percent" name="price_percent[]" class="price_percent"/>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="0" min="0"/>
                    <span class="input-sm spbasic" id="spbasic" name="spbasic[]">0</span>
                </td>
                <td class="col-md-1">                    
                    <input type="hidden" id="chrgs_amt" name="chrgs_amt[]" class="chrgs_amt input-sm" readonly="" placeholder="Amount" value="0"/>
                    <span class="input-sm spchrgs_amt" id="spchrgs_amt" name="spchrgs_amt[]">0</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="discount_per" name="discount_per[]" class="discount_per input-sm" placeholder="Percentage" value="0" />
                    <input type="text" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" readonly="" style="width: 80px" />
                </td>
                <td>
                    <input type="hidden" id="taxable" name="taxable[]" class="taxable" placeholder="Taxable" readonly="" value="0"/>
                    <span class="input-sm sptaxable" id="sptaxable" name="sptaxable[]">0</span>
                </td>
                <td class="gst_cgst">
                    <input type="hidden" id="cgst" name="cgst[]" class="input-sm cgst" value="<?php echo $product->cgst; ?>" readonly=""/>
                    <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="input-sm cgst_amt" value="" placeholder="CGST <?php echo $product->cgst; ?>%" readonly=""/>
                    <span class="input-sm spcgst_amt" id="spcgst_amt" name="spcgst_amt[]"><?php echo $product->cgst; ?>%</span>
                </td>
                <td class="gst_sgst">
                    <input type="hidden" id="sgst" name="sgst[]" class="input-sm sgst" value="<?php echo $product->sgst; ?>" readonly=""/>
                    <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="input-sm sgst_amt" value="" placeholder="SGST <?php echo $product->sgst; ?>%" readonly=""/>
                    <span class="input-sm spsgst_amt" id="spsgst_amt" name="spsgst_amt[]"><?php echo $product->sgst; ?>%</span>
                </td>
                <td class="gst_igst">
                    <input type="hidden" id="igst" name="igst[]" class="input-sm igst" value="<?php echo $product->igst; ?>" readonly=""/>
                    <input type="hidden" id="igst_amt" name="igst_amt[]" class="input-sm igst_amt" value="" placeholder="IGST <?php echo $product->igst; ?>%" readonly=""/>
                    <span class="input-sm spigst_amt" id="spigst_amt" name="spigst_amt[]"><?php echo $product->igst; ?>%</span>
                    <input type="hidden" class="input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                </td>
                <td>
                    <input type="hidden" class="total" id="total" name="total[]" placeholder="Total Amount" value="0"/>
                    <span class="input-sm sptotal" id="sptotal" name="sptotal[]">0</span>
                </td>
                <td>
                    <?php if($product->idsku_type == 4){ ?>
                        <input type="hidden" id="qty1<?php echo $product->id_variant ?>" name="qty1[]" class="input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 100px" value="0" />
                        <input type="hidden" id="qty2<?php echo $product->id_variant ?>" name="qty2[]" class="input-sm qty2" placeholder="Qty1" readonly="" style="margin: 0" value="0"/>
                    <?php }else{ ?>
                        <div id="mn<?php echo $product->id_variant?>" style="width: 200px">
                            <input type="hidden" id="idmodel" class="idmodel" value="<?php echo $product->id_variant ?>" />
                            <div class="col-md-3" style="padding: 0; margin: 0;">
                                <input type="text" id="qty1<?php echo $product->id_variant?>" name="qty1[]" class="form-control input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 70px" value="<?php echo $product->qty; ?>" />
                                <input type="hidden" id="qty2<?php echo $product->id_variant?>" name="qty2[]" class="qty2" value="<?php echo $product->qty; ?>"/>
                            </div>
                            <div class="col-md-9" style="padding: 0; margin: 0;">
                                <input type="text" id="barcode" name="barcode[]" class="form-control input-sm barcode"  value="" placeholder="Scan IMEI" style="margin: 0; width: 150px"/>
                            </div>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="2" placeholder="Scanned IMEI" style="display: none"></textarea>
                    <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                </td>
                <td><center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><?php echo $product->id_variant; ?></a></center></td>
            </tr>
            <?php $idmodels .= $product->id_variant.','; }} ?>
            </tbody>
            <tfoot id="product_data1">
                <tr>
                    <td colspan="4"></td>
                    <td>Total
                        <input type="hidden" id="total_item_rate"/>
                    </td>
                    <td>
                        <input type="hidden" id="total_basic_amt" name="total_basic_amt"  value="0"/>
                        <span id="total_basic_amt_label">0</span>
                    </td>
                    <td><input type="text" class="form-control input-sm" id="total_charges" name="total_charges" value="0" style="width: 80px" /></td>
                    <td>
                        <input class="form-control input-sm" type="text" id="total_discount" name="total_discount" value="0" style="width: 80px" />
                    </td>
                    <td>
                        <input type="hidden" class="total_taxable_amt" name="total_taxable_amt" id="total_taxable_amt" value="0"/>
                        <span type="text"  id="total_taxable_amt_label">0</span>
                    </td>
                    <td>
                        <input type="hidden" name="total_cgst_amt" id="total_cgst_amt" value="0"/>
                        <span type="text" name="total_cgst_amt_label" id="total_cgst_amt_label">0</span>
                    </td>
                    <td>
                        <input type="hidden" name="total_sgst_amt" id="total_sgst_amt" value="0"/>
                        <span type="text" name="total_sgst_amt_label" id="total_sgst_amt_label">0</span>
                    </td>
                    <td>
                        <input type="hidden" name="total_igst_amt" id="total_igst_amt" value="0"/>
                        <span type="text" name="total_igst_amt_label" id="total_igst_amt_label">0</span>
                    </td>
                    <td colspan="4">
                        <input type="hidden" name="gross_total" id="gross_total" class="grand_total" value="0" />
                        <span name="gross_total_label" id="gross_total_label">0</span>
                    </td>
                </tr>
                <tr class="gradient_thead">
                    <td colspan="8"></td>
                    <td colspan="3">
                        <input type="hidden" id="total_tax" name="total_tax" value="0"/>
                        Total Tax: <span id="total_tax_label">0</span>
                    </td>
                    <td colspan="2">After GST Discount</td>
                    <td colspan="1">
                        <input type="text" class="form-control input-sm" name="overall_discount" id="overall_discount" placeholder="Overall Discount in rupees" value="0"/>
                    </td>
                    <td colspan="4">
                        <input type="hidden" name="final_total_test" id="final_total_test" value="0" />
                        <input type="hidden" name="final_total" id="final_total" class="final_total" value="0" />
                        <span id="final_total_label">0</span>
                    </td>
                </tr>
            </tfoot>
        </table><hr>
        <input type="hidden" id="modelid" name="modelid" value="<?php echo rtrim($idmodels,','); ?>" />
        <div class="col-md-1">Godown</div>
        <div class="col-md-2">
            <select class="form-control input-sm" required="" name="sel_idgodown" id="sel_idgodown">
                <option value="">Select Godown</option>
                <?php foreach ($godown_data as $godown){ ?>
                    <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <select class="chosen-select form-control" name="model" id="model">
                <option value="">Select Model</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="pull-right col-md-5">
            <div class="col-md-2">Remark</div>
            <div class="col-md-8">
                <input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark" required="">
                <input type="hidden" name="direct_inward" value="0">
            </div>
            <div class="col-md-2">
                <button formmethod="POST" formaction="<?php echo base_url('Purchase/save_purchase_inward') ?>" class="btn btn-primary gradient2 waves-effect waves-light btn-sub">Submit</button>
            </div>
        </div>
    </form>
</div>
<?php include __DIR__ . '../../footer.php'; ?>