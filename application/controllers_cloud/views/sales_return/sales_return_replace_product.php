<?php include __DIR__ . '../../header.php'; ?>
<!--// link_tag('assets/css/bootstrap-select.min.css')-->
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on('keydown', 'input[id=invno]', function(e) {
        var invno = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            $("#replacement_form").html('');
            var branch = $('#idbranch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Sales_return/search_sales_product_return_invoice_byinvno",
                method:"POST",
                data:{invno : invno, branch: branch, level: level},
                success:function(data)
                {
                    $("#invoice_data").html(data);
                }
            });
        }
    });
    $(document).on('change', '.chk_return, .selected_qty', function() {
        var ce = $(this);
        var skutype = $(ce).closest('td').parent('tr').find(".skutype").val();
        var total_qty = +$(ce).closest('td').parent('tr').find(".row_qty").val();
        var rettotal_amt = $(ce).closest('td').parent('tr').find(".rettotal_amt").val();
        var retdiscount_amt = $(ce).closest('td').parent('tr').find(".retdiscount_amt").val();
        var ret_basic = $(ce).closest('td').parent('tr').find(".ret_basic").val();
        var taxable = $(ce).closest('td').parent('tr').find(".taxable").val();
        var cgst_amt = $(ce).closest('td').parent('tr').find(".cgst_amt").val();
        var sgst_amt = $(ce).closest('td').parent('tr').find(".sgst_amt").val();
        var igst_amt = $(ce).closest('td').parent('tr').find(".igst_amt").val();
        var tax = $(ce).closest('td').parent('tr').find(".tax").val();
        var selected_qty = $(ce).closest('td').parent('tr').find(".selected_qty");
        var chk_return = $(ce).closest('td').parent('tr').find(".chk_return");
        if(chk_return.prop("checked") == true){
            if(parseInt(selected_qty.val()) > parseInt(selected_qty.attr('max'))){
                swal("Quantity not available!", "Please enter value not more than "+selected_qty.attr('max'), "warning");
                selected_qty.val(selected_qty.attr('max'));
            }
            if(selected_qty.val() == 0){ selected_qty.val(1); }
            if(skutype == '4'){
                selected_qty.removeAttr('readonly');
                selected_qty.prop('min', 1);
                var selected_qty_val = selected_qty.val();
                var selected_row_total = (rettotal_amt / total_qty) * (selected_qty_val);
                var selected_row_discount = (retdiscount_amt / total_qty) * (selected_qty_val);
                var selected_row_basic = (ret_basic / total_qty) * (selected_qty_val);
                var selected_row_taxable = (taxable / total_qty) * (selected_qty_val);
                var selected_row_cgst_amt = (cgst_amt / total_qty) * (selected_qty_val);
                var selected_row_sgst_amt = (sgst_amt / total_qty) * (selected_qty_val);
                var selected_row_igst_amt = (igst_amt / total_qty) * (selected_qty_val);
                var selected_row_tax = (tax / total_qty) * (selected_qty_val);
            }else{
                var selected_row_total = rettotal_amt;
                var selected_row_discount = retdiscount_amt;
                var selected_row_basic = ret_basic;
                var selected_row_taxable = taxable;
                var selected_row_cgst_amt = cgst_amt;
                var selected_row_sgst_amt = sgst_amt;
                var selected_row_igst_amt = igst_amt;
                var selected_row_tax = tax;
            }
            $(ce).closest('td').parent('tr').css("background-color", "#E0FFFE");
        }else if(chk_return.prop("checked") == false){
            selected_qty.val(0);
            if(skutype == '4'){
                selected_qty.attr('readonly', true);
            }
            var selected_row_total = 0;
            var selected_row_discount = 0;
            var selected_row_basic = 0;
            var selected_row_taxable = 0;
            var selected_row_cgst_amt = 0;
            var selected_row_sgst_amt = 0;
            var selected_row_igst_amt = 0;
            var selected_row_tax = 0;
            $(ce).closest('td').parent('tr').css("background-color", "#FFFFFF");
        }
        $(this).closest('td').parent('tr').find(".selected_row_total").val(selected_row_total);
        $(this).closest('td').parent('tr').find(".selected_row_discount").val(selected_row_discount);
        $(this).closest('td').parent('tr').find(".selected_row_basic").val(selected_row_basic);
        $(this).closest('td').parent('tr').find(".selected_row_taxable").val(selected_row_taxable);
        $(this).closest('td').parent('tr').find(".selected_row_cgst_amt").val(selected_row_cgst_amt);
        $(this).closest('td').parent('tr').find(".selected_row_sgst_amt").val(selected_row_sgst_amt);
        $(this).closest('td').parent('tr').find(".selected_row_igst_amt").val(selected_row_igst_amt);
        $(this).closest('td').parent('tr').find(".selected_row_tax").val(selected_row_tax);
        var total_selected_sum = 0, total_selected_dis_sum=0, total_selected_bas_sum=0;
        $('.tr_row').each(function () {
            $(this).find('.selected_row_total').each(function () {
                var total_selected = $(this).val();
                if (!isNaN(total_selected) && total_selected.length !== 0) {
                    total_selected_sum += parseFloat(total_selected);
                }
            });
            $(this).find('.selected_row_discount').each(function () {
                var total_selected_dis = $(this).val();
                if (!isNaN(total_selected_dis) && total_selected_dis.length !== 0) {
                    total_selected_dis_sum += parseFloat(total_selected_dis);
                }
            });
            $(this).find('.selected_row_basic').each(function () {
                var selected_total_bas = $(this).val();
                if (!isNaN(selected_total_bas) && selected_total_bas.length !== 0) {
                    total_selected_bas_sum += parseFloat(selected_total_bas);
                }
            });
        });
        $('#selected_total_amountlb').html(total_selected_sum);
        $('#selected_total_amount').val(total_selected_sum);
        $('#selected_total_discount').val(total_selected_dis_sum);
        $('#selected_total_basic').val(total_selected_bas_sum);
    });
    
    $(document).on('click', '#confirm_return', function() {
        var total_selected_sum = $('#selected_total_amount').val();
        if(total_selected_sum == 0){
            swal("Select product for return!", "Select atleast one product", "warning");
            return false;
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Sales_return/product_replacement_form",
                method:"POST",
                data:{total_selected_sum: total_selected_sum},
                success:function(data)
                {
                    $("#replacement_form").html(data);
                    $("#confirm_return_block").html('<button type="reset" class="btn btn-danger" id="reset_btn">Cancel</button>');
                    $('.tr_row').each(function () {
                        $(this).find('.chk_return').each(function () {
                            $(this).hide();
                        });
                        $(this).find('.selected_qty').each(function () {
                            $(this).attr('readonly', true);
                        });
                        $(this).find('.chk_return').each(function () {
                            if($(this).prop("checked") == true){
                                $(this).closest('td').find('.seleted_lbl').show();
                                
                                var saleproduct_id = $(this).closest('td').parent('.tr_row').find('.saleproduct_id');
                                saleproduct_id.attr('name', saleproduct_id.attr('id'));
                                
                                var imei_no = $(this).closest('td').parent('.tr_row').find('.imei_no');
                                imei_no.attr('name', imei_no.attr('id'));
                                
                                var ret_idmodel = $(this).closest('td').parent('.tr_row').find('.ret_idmodel');
                                ret_idmodel.attr('name', ret_idmodel.attr('id'));
                                
                                var ret_product_name = $(this).closest('td').parent('.tr_row').find('.ret_product_name');
                                ret_product_name.attr('name', ret_product_name.attr('id'));
                                
                                var idtype = $(this).closest('td').parent('.tr_row').find('.idtype');
                                idtype.attr('name', idtype.attr('id'));
                                
                                var idcategory = $(this).closest('td').parent('.tr_row').find('.idcategory');
                                idcategory.attr('name', idcategory.attr('id'));
                                
                                var idbrand = $(this).closest('td').parent('.tr_row').find('.idbrand');
                                idbrand.attr('name', idbrand.attr('id'));
                                
                                var is_gst = $(this).closest('td').parent('.tr_row').find('.is_gst');
                                is_gst.attr('name', is_gst.attr('id'));
                                
                                var retidvendor = $(this).closest('td').parent('.tr_row').find('.retidvendor');
                                retidvendor.attr('name', retidvendor.attr('id'));
                                
                                var idgodown = $(this).closest('td').parent('.tr_row').find('.idgodown');
                                idgodown.attr('name', idgodown.attr('id'));
                                
                                var idvariant = $(this).closest('td').parent('.tr_row').find('.idvariant');
                                idvariant.attr('name', idvariant.attr('id'));
                                
                                var skutype = $(this).closest('td').parent('.tr_row').find('.skutype');
                                skutype.attr('name', skutype.attr('id'));
                                
                                var row_qty = $(this).closest('td').parent('.tr_row').find('.row_qty');
                                row_qty.attr('name', row_qty.attr('id'));
                                
                                var sale_return_qty = $(this).closest('td').parent('.tr_row').find('.sale_return_qty');
                                sale_return_qty.attr('name', sale_return_qty.attr('id'));
                                
                                var avail_qty = $(this).closest('td').parent('.tr_row').find('.avail_qty');
                                avail_qty.attr('name', avail_qty.attr('id'));
                                
                                var price = $(this).closest('td').parent('.tr_row').find('.price');
                                price.attr('name', price.attr('id'));
                                
                                var ret_basic = $(this).closest('td').parent('.tr_row').find('.ret_basic');
                                ret_basic.attr('name', ret_basic.attr('id'));
                                
                                var retdiscount_amt = $(this).closest('td').parent('.tr_row').find('.retdiscount_amt');
                                retdiscount_amt.attr('name', retdiscount_amt.attr('id'));
                                
                                var taxable = $(this).closest('td').parent('.tr_row').find('.taxable');
                                taxable.attr('name', taxable.attr('id'));
                                
                                var cgst_amt = $(this).closest('td').parent('.tr_row').find('.cgst_amt');
                                cgst_amt.attr('name', cgst_amt.attr('id'));
                                
                                var cgst = $(this).closest('td').parent('.tr_row').find('.cgst');
                                cgst.attr('name', cgst.attr('id'));
                                
                                var sgst_amt = $(this).closest('td').parent('.tr_row').find('.sgst_amt');
                                sgst_amt.attr('name', sgst_amt.attr('id'));
                                
                                var sgst = $(this).closest('td').parent('.tr_row').find('.sgst');
                                sgst.attr('name', sgst.attr('id'));
                                
                                var igst_amt = $(this).closest('td').parent('.tr_row').find('.igst_amt');
                                igst_amt.attr('name', igst_amt.attr('id'));
                                
                                var igst = $(this).closest('td').parent('.tr_row').find('.igst');
                                igst.attr('name', igst.attr('id'));
                                
                                var tax = $(this).closest('td').parent('.tr_row').find('.tax');
                                tax.attr('name', tax.attr('id'));
                                
                                var rettotal_amt = $(this).closest('td').parent('.tr_row').find('.rettotal_amt');
                                rettotal_amt.attr('name', rettotal_amt.attr('id'));
                                
                                var selected_qty = $(this).closest('td').parent('.tr_row').find('.selected_qty');
                                selected_qty.attr('name', selected_qty.attr('id'));
                                
                                var selected_row_total = $(this).closest('td').parent('.tr_row').find('.selected_row_total');
                                selected_row_total.attr('name', selected_row_total.attr('id'));
                                
                                var selected_row_discount = $(this).closest('td').parent('.tr_row').find('.selected_row_discount');
                                selected_row_discount.attr('name', selected_row_discount.attr('id'));
                                
                                var selected_row_basic = $(this).closest('td').parent('.tr_row').find('.selected_row_basic');
                                selected_row_basic.attr('name', selected_row_basic.attr('id'));
                                
                                var selected_row_taxable = $(this).closest('td').parent('.tr_row').find('.selected_row_taxable');
                                selected_row_taxable.attr('name', selected_row_taxable.attr('id'));
                                
                                var selected_row_cgst_amt = $(this).closest('td').parent('.tr_row').find('.selected_row_cgst_amt');
                                selected_row_cgst_amt.attr('name', selected_row_cgst_amt.attr('id'));
                                
                                var selected_row_sgst_amt = $(this).closest('td').parent('.tr_row').find('.selected_row_sgst_amt');
                                selected_row_sgst_amt.attr('name', selected_row_sgst_amt.attr('id'));
                                
                                var selected_row_igst_amt = $(this).closest('td').parent('.tr_row').find('.selected_row_igst_amt');
                                selected_row_igst_amt.attr('name', selected_row_igst_amt.attr('id'));
                                
                                var selected_row_tax = $(this).closest('td').parent('.tr_row').find('.selected_row_tax');
                                selected_row_tax.attr('name', selected_row_tax.attr('id'));
                                
                            }else if($(this).prop("checked") == false){
                                $(this).closest('td').find('.seleted_lbl').hide();
                            }
                        });
                    });
                    Tipped.create('.simple-tooltip');
                    swal("Product selected for return!", "Amount of rupees "+$('#selected_total_amount').val()+" added in cash for replace, upgrade product", "success");
                }
            });
        }
    });
    
    $("button[type='reset']").closest('form').on('reset', function(event) {
        alert('hi');
//        javascript:void(0)
        setTimeout(function() {
            $('.tr_row').each(function () {
                $(this).css("background-color", "#FFFFFF");
                $(this).find('.chk_return').each(function () {
                    $(this).show();
                });
                $(this).find('.seleted_lbl').each(function () {
                    $(this).hide();
                });
            });
            $("#replacement_form").html('');
            swal("Cancelled return process!", "Reset selection successfully", "success");
        }, 1);
    });
});
</script>
<style>
.modes_block:hover{
    background-color: #f4f4f4;
}
</style>
<script src="<?php echo site_url('assets/js/autocomplete-jquery-ui.js') ?>"  type="text/javascript"></script>
<?= link_tag("assets/css/autocomplete-jquery-ui.css") ?>

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
<div class="col-md-10"><center><h3 style="margin-top: 15px"><span class="mdi mdi-keyboard-return fa-lg"></span> Sales Return - Product Replace/Upgrade</h3></center></div><div class="clearfix"></div><hr>
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
    if($var_closer){ ?>
<form>
    <div class="col-md-1 col-sm-2">Invoice No</div>
    <div class="col-md-4 col-sm-7">
        <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
    </div><div class="clearfix"></div>
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/>
    <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
    <input type="hidden" class="form-control input-sm" name="sales_return_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
</form>
<style>
#floatingButton {
    position: fixed;
    display: block;
    width: 60px;
    height: 60px;
    text-align: center;
    box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
    background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);
    color: #fff;
    line-height: 60px;
    border-radius: 50% 50%;
    bottom: 50px;
    right: 30px;
}
</style>
<?php }else{ 
        echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
            .'</center>';
    } ?>
<?php include __DIR__ . '../../footer.php'; ?>