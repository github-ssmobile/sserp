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
                    $("#counter_service_block").hide();
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
                    $("#counter_service_block").hide();
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
        var skutype = +$(ce).closest('td').parent('tr').find(".skutype").val();
        var idgodown = +$(ce).closest('td').parent('tr').find("#idgodown").val();
        sales_return_type = $("#sales_return_type").val();
        
        var ce = $(this);
        var idsaleproduct = ce.val();
        var is_gst = $(ce).closest('td').parent('tr').find("#is_gst").val();
        var idvendor = $(ce).closest('td').parent('tr').find("#idvendor").val();        
        var imei_no = $(ce).closest('td').parent('tr').find(".imei").val();
        var model = $(ce).closest('td').parent('tr').find(".model").val();
        
        
        $("#idsaleproduct").val(idsaleproduct);        
        $("#dididvariant").val(idmodel);        
        $("#dis_gst").val(is_gst);
        $("#didvendor").val(idvendor);
        $("#didgodown").val(idgodown);
        $("#dskutype").val(skutype);
        $("#dimei_no").val(imei_no);
        $("#dprice").val(price);
        $("#dimodel").val(model);
        
        if($(".erp").val()==0){
            var product_name = $(ce).closest('td').parent('tr').find(".product_name").val();
        $.ajax({
                    url: "<?php echo base_url() ?>Service/ajax_variants_by_olderp_model",
                    method: "POST",
                    data: {product_name: product_name},
                    success: function (data)
                    {
                        $(".old_model").html(data);                                                      
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        
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
        }else{
             if(!confirm("Do you want to Inward!!")){
                return false;
        }
        }
       
    });
});
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-android fa-lg"></span> Service Inward </h3></center></div><div class="clearfix"></div><hr>
    <div class="sold_service_block">
        <form>
            <div class="col-md-3">
                <h4 style="margin: 0"><span class="mdi mdi-cellphone-link-off fa-lg"></span> Inward Sold Product</h4>
            </div>
            <div class="col-md-1">Invoice No</div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
            </div>
            <div class="col-md-2">IMEI No</div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="imei" name="imei" placeholder="Search IMEI Number"/>
            </div>
            <div class="clearfix"></div>
            <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
            <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>            
            <div id="invoice_data" style="font-size: 14px; overflow: auto"></div>
        </form>  
    </div>
<style>
h1 {
  font-size: 150%;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 400;
  padding-top: 10px;
}
header {
  /*background-color: #fff;*/
  color: #fff;
  background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
  box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
  border-radius: 5px;
}
header p {
  font-family: 'Allura';
  color: #fff;
  margin-bottom: 0;
  font-size: 32px;
  margin-top: -20px;
}
</style>
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on('keydown', 'input[id=imei_no]', function(e) {
        var imei = $(this).val();
        var invno=0;
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/search_stock_byimei",
                method:"POST",
                data:{imei : imei, branch: branch, level: level},
                success:function(data)
                {
                    $("#invoice_data_cf").html(data);
                    $(".sold_service_block").hide();
                    $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
   $(document).on('change', '#idproblem', function() {
        $('.problem').val($('option:selected',this).text());
    });
    
});
</script>
<div id="counter_service_block">
    <div class="sold_service_block">
        <div class="clearfix"></div><hr>
        <center><h4 style="margin: 0">OR</h4></center><hr>
    </div>
    <div>
        <form>
            <div class="col-md-3">
                <h4 style="margin: 0"><span class="mdi mdi-cellphone-link-off fa-lg"></span> Inward Counter Faulty </h4>
            </div>
            <div class="col-md-1">IMEI No</div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="imei_no" placeholder="Search IMEI Number"/>
            </div>
            <div class="clearfix"></div>
            <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
            <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
            <div class="clearfix"></div><br>
            <div id="invoice_data_cf" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
        </form>
    </div><div class="clearfix"></div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>