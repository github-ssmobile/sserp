<?php include __DIR__ . '../../header.php'; ?>
<script>
    window.onload=function() { $("#sidebar").addClass("active"); };
    var scanned=[],idvariant=[];var variants = [];
    $(document).ready(function () {
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
        $('.scan_imei').change(function(){
            var parent = $($(this)).closest('td').parent('tr');
            var imei = $(this).val();
            if (scanned.includes(imei) === false){
                scanned.push(imei);
                parent.find(".scanned").val(scanned);
            }else{
                alert('duplicate product selected');
                return false;
            }
            $(this).val('');
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
                var scanned;
                $('#imeiscanned').val(barcodes);
                //$('#scanned').val(barcodes);
                e.preventDefault();
                var ce = $(this);
                $(ce).closest('td').parent('tr').find(".scanned").append(''+$(this).val()+',');
//                qty1 = $(ce).closest('td').parent('tr').find(".qty1").val();
                qty1 = +$(ce).closest('td').parent('tr').find(".qty1").val() - 1;
                $(ce).closest('td').parent('tr').find(".scanned1").append('<input type="text" class="btn btn-sm btn-warning imeino" name="imeino[]" id="imeino" readonly="" style="margin:2px 0" value="'+$(this).val()+'" "/>');
                $(ce).closest('td').parent('tr').find(".qty2").attr('readonly', true);
                $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                $(this).val('');
                    if(qty1 === 0){
                        $(this).prop('readonly', true);
                    }
                }
                else{
                    alert('duplicate IMEI/SRNO scanned');
                    return false;
                }
            }
        }
    });
    $(document).on('change', 'input[id=qty2]', function() {
        var ce = $(this);
        var skutype = $(ce).closest('td').parent('tr').find(".skutype").val();
        var qty1 = $(ce).closest('td').parent('tr').find(".qty1");
        var qty = $(this).val();
        qty1.val(qty);
        if(qty > 0){
            if(skutype !== '4'){
            }else{
                qty1.val(0);
            }
        }else{
            alert('Quantity not accepted');
            return false;
        }
    });
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
            var org_qty = $(ce).closest('td').parent('tr').find(".qty2").val();
            if(qty>0){
              $(ce).closest('td').parent('tr').find(".barcode").removeAttr("readonly");
            }
            if(qty==org_qty){
              $(ce).closest('td').parent('tr').find(".qty2").removeAttr("readonly");
            }
        }
    });
    
    $(document).on('click', '.remove_btn', function() {
        var parent = $($(this)).closest('td').parent('tr');
        var idvariant = parent.find(".idvariant").val();
        $(parent).remove();
        variants = jQuery.grep(variants, function(value) { return value !== idvariant; });
    });
    
    $(document).ready(function () {
        $('#idmodelvariant').change(function(){
            var idmodel = $(this).val();
            if (variants.includes(idmodel) === false){
                variants.push(idmodel);
                $.ajax({
                    url: "<?php echo base_url() ?>Purchase/ajax_get_product_byid",
                    method: "POST",
                    data:{id : idmodel, variants: variants},
                    success: function (data)
                    {
                        $('#model_table').show();
                        $('#selected_model').append(data);
//                        $('#variant_array').val(variants);
                    }
                });
            }else{
                alert('duplicate product selected');
                return false;
            }
        });
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
        } else{
            alert("Please scan all IMEIs");
            return false;
        }
    });
    $(document).on('keyup', 'input[id=qty2], input[id=price], input[id=discount_per], input[id=discount_amt], input[id=chrgs_amt], input[id=price], input[id=total_discount]', function() {
        var qty=0,price=0,discount_amt=0,chrgs_amt=0,discount_per=0,total=0,cgst=0,sgst=0,igst=0,cgst_amt=0,sgst_amt=0,igst_amt=0,tax=0,basic=0,taxable=0;
        if ($(this).val()) {
            var ce = $(this);
            qty = isNaN($(ce).closest('td').parent('tr').find(".qty2").val()) ? 0 : $(ce).closest('td').parent('tr').find(".qty2").val();
            price = isNaN($(ce).closest('td').parent('tr').find(".price").val()) ? 0 : $(ce).closest('td').parent('tr').find(".price").val();
            basic = isNaN($(ce).closest('td').parent('tr').find(".basic").val()) ? 0 : $(ce).closest('td').parent('tr').find(".basic").val();
            chrgs_amt = isNaN($(ce).closest('td').parent('tr').find(".chrgs_amt").val()) ? 0 : $(ce).closest('td').parent('tr').find(".chrgs_amt").val();
            discount_per = isNaN($(ce).closest('td').parent('tr').find(".discount_per").val()) ? 0 : $(ce).closest('td').parent('tr').find(".discount_per").val();
            discount_amt = isNaN($(ce).closest('td').parent('tr').find(".discount_amt").val()) ? 0 : $(ce).closest('td').parent('tr').find(".discount_amt").val();
            
            if($('#state').val() === 'Maharashtra'){
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
            $(ce).closest('td').parent('tr').find(".spcgst_amt").html(+cgst_amt+'('+cgst+'%)');
            $(ce).closest('td').parent('tr').find(".sgst_amt").val(sgst_amt);
            $(ce).closest('td').parent('tr').find(".spsgst_amt").html(sgst_amt+'('+sgst+'%)');
            $(ce).closest('td').parent('tr').find(".igst_amt").val(igst_amt);
            $(ce).closest('td').parent('tr').find(".spigst_amt").html(igst_amt+'('+igst+'%)');
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
            <div class="col-md-9"><?php echo date('d-m-Y',  strtotime($purchase_order->date)) ?></div>
            <div class="col-md-3 text-muted">PO ID</div>
            <div class="col-md-9"><?php echo $purchase_order->financial_year.'-'.$purchase_order->id_purchase_order ?></div><div class="clearfix"></div>
            <div class="col-md-3 text-muted">Warehouse</div>
            <div class="col-md-9"><?php echo $purchase_order->branch_name ?></div><div class="clearfix"></div>
            <div class="col-md-3 text-muted">Warehouse State</div>
            <div class="col-md-9">
                <?php echo $purchase_order->branch_state_name ?>
            </div><div class="clearfix"></div>
        </div>
        <div class="col-md-6">
            <div class="col-md-2 text-muted">Vendor</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_name ?></div>
            <div class="col-md-2 text-muted">Contact</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_contact ?></div><div class="clearfix"></div>
            <div class="col-md-2 text-muted">State</div>
            <div class="col-md-10"><?php echo $purchase_order->state ?></div><div class="clearfix"></div>
            <div class="col-md-2 text-muted">GSTIN</div>
            <div class="col-md-10"><?php echo $purchase_order->vendor_gst ?></div><div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
        <div class="col-md-5 gst-block">
            <div class="col-md-6">
                <input class="form-check-input" type="radio" name="gstradio" id="gst" value="1">
                <label class="form-check-label" for="gst">GST Invoice</label>
            </div>
            <div class="col-md-6">
                <input class="form-check-input" type="radio" name="gstradio" id="nongst" value="0">
                <label class="form-check-label" for="nongst">Non GST Invoice</label>
            </div>
        </div><div class="clearfix"></div>
        <table id="branch_data" class="table table-condensed table-hover table-bordered">
            <thead class="bg-info">
                <th>Id</th>
                <th class="col-md-4">Product</th>
                <th class="col-md-1">Qty</th>
                <th class="col-md-1">Rate</th>
                <th class="col-md-1">Basic</th>
                <th class="col-md-1">Charges</th>
                <th class="col-md-1">Discount</th>
                <th class="col-md-1">Taxable</th>
                <th class="col-md-1">CGST</th>
                <th class="col-md-1">SGST</th>
                <th class="col-md-1">IGST</th>
                <th class="col-md-1">Amount</th>
                <th class="col-md-1">Scan</th>
                <th class="col-md-4">Scanned IMEI</th>
                <th class="col-md-1"><center>Remove</center></th>
            </thead>
            <tbody id="selected_model" class="data_1">
                <?php foreach ($purchase_order_product as $product){ ?>
                <tr>
                    <td><?php echo $product->id_variant ?></td>
                    <td><?php echo $product->full_name ?></td>
                    <td>
                        <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $product->idsku_type ?>" />
                        <input type="hidden" id="skulenght" class="form-control skulenght" name="skulenght[]" value="<?php echo $product->sku_lenght ?>" />
                        <input type="text" id="qty2" name="qty2[]" class="form-control input-sm qty2" value="<?php echo $product->qty ?>"/>
                        <input type="hidden" id="qty1" name="qty1[]" class="form-control input-sm qty1" value="<?php echo $product->qty ?>" style="margin: 0; width: 100px" />
                    </td>
                    
                    <td class="col-md-1">
                    <input type="text" id="price" name="price[]" class="form-control input-sm price" required="" placeholder="Price" min="1"/>
                    <input type="hidden" id="price_percent" name="price_percent[]" class="price_percent"/>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="0" min="0"/>
                    <span class="input-sm spbasic" id="spbasic" name="spbasic[]">0</span>
                </td>
                <td class="col-md-1">                    
                    <input type="hidden" id="chrgs_amt" name="chrgs_amt[]" class="form-control chrgs_amt input-sm" readonly="" placeholder="Amount" value="0"/>
                    <span class="input-sm spchrgs_amt" id="spchrgs_amt" name="spchrgs_amt[]">0</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="discount_per" name="discount_per[]" class="form-control discount_per input-sm" placeholder="Percentage" value="0" />
                    <input type="text" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0"/>
                </td>
                <td>
                    <input type="hidden" id="taxable" name="taxable[]" class="taxable" placeholder="Taxable" readonly="" value="0"/>
                    <span class="input-sm sptaxable" id="sptaxable" name="sptaxable[]">0</span>
                </td>
               <?php if(1){ ?>
                <td class="col-md-1">
                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $product->cgst; ?>" readonly=""/>
                    <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="form-control input-sm cgst_amt" value="" placeholder="CGST <?php echo $product->cgst; ?>%" readonly=""/>
                    <span class="input-sm spcgst_amt" id="spcgst_amt" name="spcgst_amt[]"><?php echo $product->cgst; ?>%</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $product->sgst; ?>" readonly=""/>
                    <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="form-control input-sm sgst_amt" value="" placeholder="SGST <?php echo $product->sgst; ?>%" readonly=""/>
                    <span class="input-sm spsgst_amt" id="spsgst_amt" name="spsgst_amt[]"><?php echo $product->sgst; ?>%</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $product->igst; ?>" readonly=""/>
                    <input type="hidden" id="igst_amt" name="igst_amt[]" class="form-control input-sm igst_amt" value="" placeholder="IGST <?php echo $product->igst; ?>%" readonly=""/>
                    <span class="input-sm spigst_amt" id="spigst_amt" name="spigst_amt[]"><?php echo $product->igst; ?>%</span>
                    <input type="hidden" class="form-control input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                </td>
            <?php }else{ ?>
                <td class="col-md-1">
                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="0" readonly=""/>
                    <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="form-control input-sm cgst_amt" value="" placeholder="CGST 0%" readonly=""/>
                    <span class="input-sm spcgst_amt" id="spcgst_amt" name="spcgst_amt[]">0%</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="0" readonly=""/>
                    <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="form-control input-sm sgst_amt" value="" placeholder="SGST 0%" readonly=""/>
                    <span class="input-sm spsgst_amt" id="spsgst_amt" name="spsgst_amt[]">0%</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="0" readonly=""/>
                    <input type="hidden" id="igst_amt" name="igst_amt[]" class="form-control input-sm igst_amt" value="" placeholder="IGST 0%" readonly=""/>
                    <span class="input-sm spigst_amt" id="spigst_amt" name="spigst_amt[]">0%</span>
                    <input type="hidden" class="form-control input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                </td>
            <?php } ?>
                <td>
                    <input type="hidden" class="total" id="total" name="total[]" placeholder="Total Amount" value="0"/>
                    <span class="input-sm sptotal" id="sptotal" name="sptotal[]">0</span>
                </td>
<!--                    
                    <td><input type="number" required="" id="price" class="form-control input-sm price" name="price" placeholder="Rate" style="width: 100px"/></td>
                    <td>0</td>
                    <td>0</td>
                    <td><input type="number" required="" class="form-control input-sm" placeholder="Discount" style="width: 100px"/></td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>-->
                    <td><?php if($product->idsku_type != 4){ ?>
                        <input type="text" id="barcode" name="barcode[]" class="form-control input-sm barcode"  value="" placeholder="Scan IMEI" style="margin: 0;width: 100px"/>
                        <?php } ?></td>
                    <td>
                        <textarea class="scanned" id="scanned" name="scanned[]" style="display: none"></textarea>
                        <div class="scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                    </td>
                    <td><a class="btn btn-link remove" name="remove[]" id="remove" style="color: #cc0033"><?php echo $model->id_model; ?></button></td>
                    <td><center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove_btn"><i class="fa fa-times fa-lg"></i></a></center></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot id="product_data1">
                <tr>
                    <td colspan="3"></td>
                    <td>Total
                        <input type="hidden" id="total_item_rate"/>
                    </td>
                    <td>
                        <input type="hidden" id="total_basic_amt" name="total_basic_amt"  value="0"/>
                        <span id="total_basic_amt_label">0</span>
                    </td>
                    <td><input type="text" class="form-control input-sm" id="total_charges" name="total_charges" value="0" /></td>
                    <td>
                        <input class="form-control input-sm" type="text" id="total_discount" name="total_discount" readonly=""  value="0"/>
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
                <tr class="bg-info">
                    <td colspan="5"></td>
                    <td colspan="2">
                        <input type="hidden" id="total_tax" name="total_tax" value="0"/>
                        Total Tax: <span id="total_tax_label">0</span>
                    </td>
                    <td colspan="2">After GST Discount</td>
                    <td colspan="2">
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
        <div class="col-md-1">Add New</div>
        <div class="col-md-6">
            <select class="chosen-select form-control" name="idmodelvariant" id="idmodelvariant" required="">
                <option value="">Select Model</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="clearfix"></div><br>
        <div class="pull-right col-md-6" style="height: 250px">
            <div class="col-md-2">Remark</div>
            <div class="col-md-8">
                <input type="text" class="form-control input-sm" placeholder="Enter Remark" required="">
            </div>
            <div class="col-md-2">
               <button class="btn btn-primary gradient2 waves-effect waves-light btn-sub">Submit</button>
            </div>
        </div>
    </form>
</div>
<?php include __DIR__ . '../../footer.php'; ?>