<?php include __DIR__ . '../../header.php'; ?>
<script>
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
    var products = [];
    var j=0;
    $(document).ready(function(){
        $('#selsupplier').change(function(){
            var supplier = $(this).val();
            var supplier_name = $('#selsupplier option:selected').text();
            if (confirm('You selected supplier: '+ supplier_name +' Are you sure?')) {
                $('#display_supplier').val(supplier_name);
                <?php foreach ($vendor_data as $suppliers){ ?>
                    if('<?php echo $suppliers->id_vendor ?>' === supplier){
                        $('#state').val('<?php echo $suppliers->state ?>');
                    }
                <?php } ?>
                $('#selsupplier').attr("readonly", true);
            }else{
                $('#state').val('');
                return false;
            }
        });
        $('#model').change(function(){
        
            if(!$("input[name='gstradio']").is(":checked")){
                alert('Select GST/Non-GST');
                return false;
            }
            if($('#state').val() === ''){
                alert('Select Supplier');
                return false;
            }else{
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
                    data:{id : id,gstradio:gstradio},
                    success:function(data)
                    {
                        $("#product").css("display", "block");
                        $("#product_data").append(data);
                        ++j;
                        $('#count').val(j);
                        $("#selsupplier_block").hide();
                        $("#display_supplier_block").show();
                        $(".gst-block").hide();
                        $(".gst-text-block").html("Selected type :- <b>"+gstradio_text+"</b>")
                        window.setTimeout(function() {
                            $(".fadeout_nongst").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove(); 
                            });
                        }, 3000);
                    }
                });
            }
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
        var id = $(this).text();
        if (confirm('Are you sure? You want to remove product: '+id)) {
            var str  = '#m'+id;
            products = jQuery.grep(products, function(value) { return value !== id; });
            $('#modelid').val(products);
            $('input[id=qty]').keyup();
            --j;
            $('#count').val(j);
            var imeie=$('#mn'+id).find(".scanned").text();
            var imeis = imeie.split(',');
            var i=0;
            for(i=0;i<(imeis.length-1);i++){
                barcodes = jQuery.grep(barcodes, function(value) {
                    return value !== imeis[i];
                }); 
            }
            $('#imeiscanned').val(barcodes);
            $(str).remove();
            $('#mn'+id).remove();
            $("#qty" ).trigger( "keyup" );
        }
    });
    var barcodes = []; var qty1=1; var $_blockDelete = false;
    $(document).on('keydown', 'input[id=barcode]', function(e) {
        var keyCode = e.keyCode || e.which; 
        if (keyCode === 13 && $(this).val() !== '') {
            var ce = $(this);
            if($(ce).closest('td').parent('tr').prev('tr').find(".skutype").val() != '4'){
            var skulength = +$(ce).closest('td').parent('tr').prev('tr').find(".skulenght").val();
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
                $(ce).closest('td').parent('tr').find(".scanned1").append('<input type="text" class="btn btn-sm btn-warning imeino" name="imeino[]" id="imeino" readonly="" style="margin:2px" value="'+$(this).val()+'" "/>');
                    /*.on('click','input',function(){
                    var barid = $(this).val();
                    var TextSearch = $(ce).closest('td').parent('tr').find(".scanned").val();
//                  var rconfirm = confirm('Are you sure? You want to remove imei/srno: '+ barid);
                    scanned = TextSearch.replace(barid+',', '');
                    $(ce).closest('td').parent('tr').find(".scanned").val(scanned);
                    barcodes = jQuery.grep(barcodes, function(value) { return value !== barid; });
                    $('#imeiscanned').val(barcodes);
                    $(ce).closest('td').parent('tr').find(".barcode").removeAttr('readonly', true);
                    $(this).remove();
//                    qty1 = +$(ce).closest('td').parent('tr').find(".qty1").val() + 1;
//                    $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                    });*/
                
                $(ce).closest('td').parent('tr').prev('tr').find(".qty").attr('readonly', true);
                $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                $(this).val('');
                if(qty1 === 0){
                    $(this).prop('readonly', true);
                    //$(ce).closest('td').parent('tr').prev('tr').find(".qty").removeAttr('readonly');
                }
            }
            else{
                alert('duplicate IMEI/SRNO scanned');
                return false;
            }
            }
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
            var org_qty = $(ce).closest('td').parent('tr').prev('tr').find(".qty").val();
            if(qty>0){
              $(ce).closest('td').parent('tr').find(".barcode").removeAttr("readonly");
            }
            if(qty==org_qty){
              $(ce).closest('td').parent('tr').prev('tr').find(".qty").removeAttr("readonly");
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
        if ($(this).val()) {
            var ce = $(this);
            qty = isNaN($(ce).closest('td').parent('tr').find(".qty").val()) ? 0 : $(ce).closest('td').parent('tr').find(".qty").val();
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
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Direct Inward Form </h3></center></div><div class="clearfix"></div>
<div class="thumbnail"><br>
    <form>
    <div class="">
    <input type="hidden" class="form-control input-sm" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <div class="col-md-1"> Date</div>
    <div class="col-md-2">
        <input type="text" class="form-control input-sm" name="date" value="<?php echo $now ?>" readonly="" />
        <input type="hidden" class="form-control input-sm" id="count" />
    </div>
    <div class="col-md-1"> Invoice No</div>
    <div class="col-md-2">
        <input type="text" class="form-control input-sm" name="supplier_inv" placeholder="Supplier Invoice No" required=""/>
    </div>
    <div class="col-md-1"> Supplier</div>
    <div class="col-md-3" id="selsupplier_block">
        <select name="idvendor" id="selsupplier" class="form-control input-sm selectpicker" required="" data-live-search="true" data-live-search-placeholder="Search">
            <option value="">Select Supplier</option>
            <?php foreach ($vendor_data as $suppliers){ ?>
            <option value="<?php echo $suppliers->id_vendor ?>"><?php echo $suppliers->vendor_name ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-3" id="display_supplier_block" style="display: none">
        <input id="display_supplier" class="form-control input-sm" readonly=""/>
    </div>
    <div class="col-md-2">
        <input type="text" id="state" name="state" class="form-control input-sm" placeholder="Supplier State" required="" onfocus="blur()"/>
    </div>
    <div class="clearfix"></div><br>
    <div class="col-md-1">Supplier Invoice Date</div>
    <div class="col-md-2">
        <input type="text" class="form-control input-sm datepick" name="inv_date" placeholder="Supplier Invoice Date" autocomplete="off" onfocus="blur()"  value="<?php echo $now ?>" required="" />
    </div>
    <div class="col-md-1"> Warehouse</div>
    <div class="col-md-2"><?php echo $_SESSION['branch_name'] ?><input type="hidden" name="idwarehouse" value="<?php echo $_SESSION['idbranch'] ?>" /></div>
    <div class="col-md-1"> Remark</div>
    <div class="col-md-5">
        <textarea class="form-control input-sm" name="remark" placeholder="Enter remark" ></textarea>
    </div>
    <div class="clearfix"></div>
    </div><div class="clearfix"></div><hr>
    <div class="col-md-7 col-xs-12 col-sm-12">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <select class="chosen-select form-control" name="model" id="model" required="">
                <option value="">Select Product</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name . ' ' . $variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-3 gst-block">
        <div class="col-md-6">
            <input class="form-check-input" type="radio" name="gstradio" id="gst" value="1">
            <label class="form-check-label" for="gst">
              GST
            </label>
        </div>
        <div class="col-md-6">
            <input class="form-check-input" type="radio" name="gstradio" id="nongst" value="0">
            <label class="form-check-label" for="nongst">
              Non GST
            </label>
        </div>
    </div>
    <div class="col-md-2 pull-right gst-text-block"></div>
    <div class="clearfix"></div><br>
    <input type="hidden" id="modelid" name="modelid" class="form-control" />
    <input type="hidden" id="imeiscanned" name="imeiscanned" class="form-control" />
    <div class="" id="product" style="overflow: auto; display: none">
        <table id="inward_table" class="table table-bordered table-condensed table-full-width table-hover" style="font-size: 14px">
            <thead class="bg-info">
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
            <tbody id="product_data" style="border: 1px solid #C8D4D4">
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
                <tr class="bg-info">
                    <td colspan="9"></td>
                    <td colspan="3">
                        <input type="hidden" id="total_tax" name="total_tax" value="0"/>
                        Total Tax: <span id="total_tax_label">0</span>
                    </td>
                    <td colspan="1">After GST Discount</td>
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
        </table>
        <input type="hidden" name="check_qty" id="check_qty" class="check_qty" value="0" />
        <button type="submit" class="btn btn-primary pull-right btn-sub gradient2" formmethod="POST" formaction="<?php echo base_url() ?>Inward/save_inward">Submit</button>
    </div><div class="clearfix"></div><br>
    </form>
</div>
<?php include __DIR__ . '../../footer.php'; 