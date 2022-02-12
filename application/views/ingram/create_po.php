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
                    url: "<?php echo base_url() ?>Ingram_Api/ajax_get_imei_details",
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
    $(document).on('change', 'input[id=qty]', function() {
        var ce = $(this).closest('td').parent('tr');
        var availqty=+ce.find("#availqty").val();
        qty = (isNaN(+ce.find(".qty").val())) ? 0 : +ce.find(".qty").val();
//        if(qty>availqty){            
//            swal("Quantity not available!", "Please Re-enter quantity","warning");
//            ce.find(".qty").val('0');
//            return false;
//        }
    });
    
    // Remove product row
    $(document).on('click', 'a[id=remove]', function() {
        var parrent = $(this).closest('td').parent('tr');
        var product_name = parrent.find('.product_name').val();
        var idvariant = parrent.find('.idvariant').val();
        
        
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
                
                products = jQuery.grep(products, function(value) { return value !== idvariant; });
                $('#modelid').val(products);

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
    
    $(document).on("click", "#invoice_submit", function (event) {
        var sum_sale_type = 0, arr_insurance_imei = [];
        $('.skuqty_row').each(function () {
            $(this).find('.sale_type').each(function () {
                var sale_type = $(this).val();
                if (!isNaN(sale_type) && sale_type.length !== 0) {
                    sum_sale_type += parseFloat(sale_type);
                }
            });
        });
        if(count == 0){
            swal("Product not added!", "Scan IMEI/ SRNO or Select product");
            return false;
        }        
        
        if (!confirm('Are you sure? Do you want to submit invoice')){
            return false;
        }
        function count_arr(array){ var c = 0; for(i in array) if(array[i] != undefined) c++; return c;}
    });
    
   
    
});
</script>
<?php 

$this->session->unset_userdata('idsale_url'); ?>

<div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">
          
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Purchase Order </h3></center></div><div class="clearfix"></div><hr><br>

    <form id="sale_form_submit">        
        <div class="" >
            <div><h4 style="color:#1b6caa;font-family: Kurale;"> Select Product </h4></div>
            <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                <div style="padding: 5px 0">
                    <select class="chosen-select form-control input-sm" name="skuvariant" id="skuvariant">
                        <option value="">Select Model</option>
                        <?php foreach ($model_variant as $variant) { ?>
                            <option value="<?php echo $variant->id_variant; ?>" sku="<?php echo $variant->$sku_column; ?>" sale_type="<?php echo $variant->sale_type; ?>"><?php echo $variant->full_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div><div class="clearfix"></div><hr>
            <div >
                <div class="thumbnail"  style="overflow: auto;margin-top: 10px; padding: 0">
                    <table id="inward_table" class="table table-bordered table-condensed table-hover" style="font-size: 13px; margin-bottom: 0">
                        <thead style="color: #fff; background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);">
                            <td>Product</td>                            
                            <td>SKU</td>                            
                            <td>Ingram Stock</td>
                            <td>APOB Stock</td>
                            <td>Retail Price</td>
                            <td>Customer Price</td>
                            <td style="width: 100px">Qty</td>
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
                                    <td></td>                                    
                                </tr>
                            </thead>
                        
                    </table>
                </div><div class="clearfix"></div><hr>
                <div id="product" style="display:none">
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <a class="btn btn-warning gradient1" href="<?php echo base_url('Sale'); ?>">Cancel</a>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-5">
                    <!--Entered Amount: <span class="entered">0</span>-->
                </div>
                <div class="col-md-5 col-sm-9 col-xs-8">
                    <input type="text" class="form-control" name="remark" placeholder="Enter Remark"/>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <button type="submit" id="invoice_submit" class="btn btn-primary btn-sub gradient2 pull-right" formmethod="POST" formaction="<?php echo site_url('Ingram_Api/save_ingram_po') ?>">Create PO</button>
                </div><div class="clearfix"></div>
                </div>
            </div><div class="clearfix"></div>
        </div>
    </form>
    <div class="alert_msg"></div>
   
</div><div class="clearfix"></div>


<?php include __DIR__.'../../footer.php'; ?>