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
     $(document).on('change', '#idbrand', function () {
            if ($('#idbrand').val()) {
                var product_category = 0;
                var brand = +$('#idbrand').val();
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
    
    var sales_return_type, return_cash=0, temp_cash=0;
    $(document).on('change', 'input[id=chk_return]', function() {
        var ce = $(this);
        var saleproduct_id = $(ce).closest('td').parent('tr').find(".saleproduct_id").val();
        var idmodel = $(ce).closest('td').parent('tr').find(".idmodel").val();
        var qty = +$(ce).closest('td').parent('tr').find(".selected_qty").val();
        var price = +$(ce).closest('td').parent('tr').find(".price").val();
        sales_return_type = $("#sales_return_type").val();
        if(qty == ''){ qty = 0; }
        if($(this).prop("checked") == true){
            checked = checked + qty;
            return_cash += price;
            if(products.includes(saleproduct_id) === false){
                products.push(saleproduct_id);
            }
            temp_cash += price;
            $("#sales_return_product_id").val(saleproduct_id);
            $("#sales_return_model_id").val(idmodel);
            $("#txt_selected_sale_products").val(products);
        }else if($(this).prop("checked") == false){
            checked = checked - qty;
            return_cash -= price;
            temp_cash -= price;
            products = jQuery.grep(products, function(value) { return value !== saleproduct_id; });
            $("#txt_selected_sale_products").val(products);
        }
        $('#is_selected').val(return_cash);
    });
   
   
   $(document).on('change', '#idproblem', function() {
        $('.problem').val($('option:selected',this).text());
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
<center><h3 style="margin: 0"><span class="mdi mdi-cellphone-android fa-lg"></span> Service Inward </h3></center><div class="clearfix"></div><hr>
    <div class="col-md-10 col-md-offset-1">
        <form>
            <div class="col-md-1 col-sm-2">Invoice No</div>
            <div class="col-md-4 col-sm-7">
                <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
            </div>
            <div class="col-md-1 col-sm-2">IMEI No</div>
            <div class="col-md-4 col-sm-7">
                <input type="text" class="form-control" id="imei" name="imei" placeholder="Search IMEI Number"/>
            </div>
            <div class="clearfix"></div>
            <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
            <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>            
            <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
        </form>  
    </div>
<?php include __DIR__ . '../../footer.php'; ?>