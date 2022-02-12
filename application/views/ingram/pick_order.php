<?php include __DIR__.'../../header.php';  
if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<style>
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

<script>
    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode === 13) {
            event.preventDefault();
            return false;
          }
        });
    });
    var barcodes = []; var qty1=1;
    $(document).on('keyup', 'input[id=barcode]', function(e) {
        var ce = $(this);
        var val = $(this).val();
        var keyCode = e.keyCode || e.which; 
        if (keyCode === 13 && val !== '') {
            var qty = +$(ce).closest('td').parent('tr').find(".qty").val();
            var scannedqty = +$(ce).closest('td').parent('tr').find(".qty1").val();
            if(qty==scannedqty){
                alert('Quantity limit exceeded!!');
                $(this).val('');
                return false;
            }
            var idvariant = $(ce).closest('td').parent('tr').find(".idvariant").val();
            var idgodown = $(ce).closest('td').parent('tr').find(".id_godown").val();
            var idwarehouse = $(".idwarehouse").val();
//            alert(idgodown);
            $.ajax({
                url:"<?php echo base_url() ?>Ingram_Api/ajax_check_valid_barcode",
                method:"POST",
                data:{val : val, idvariant: idvariant, idbranch: idwarehouse,idgodown:idgodown},
                 dataType:'json',
                success:function(data)
                {
                    if(data.error === false){
                        if (barcodes.includes(val) === false){
                            barcodes.push(val); 
            //              var scanned;
                            $('#imeiscanned').val(barcodes);
                            e.preventDefault();
                            $(ce).closest('td').parent('tr').find(".scanned").append(''+val+',');
//                            qty1 = $(ce).closest('td').parent('tr').find(".qty1").val();
                            qty1 = +$(ce).closest('td').parent('tr').find(".qty1").val() + +1;
                            $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                            $(ce).closest('td').parent('tr').find(".scanned1").append('<input type="text" class="btn btn-sm btn-warning scanned_imei" id="scanned_imei" readonly="" style="margin:2px; width: 160px;background-color: #0e10aa;opacity: 0.8;" value="'+val+'" href="javascript:void(0);"></input>');
                            $('.alert_msg').show();
                            $('.alert_msg').text('IMEI/SRNO Verifyed: '+val);
                            $('.alert_msg').fadeOut(20000);
                        }else{
                            alert('Failed: Duplicate IMEI/SRNO scanned');
                            $(this).val('');
                            return false;
                        }
                    }else{
                        alert('Failed: IMEI/SRNO not verified');
                        $(this).val('');
                        return false;
                    }
                }
            });
            $(this).val('');
        }
    });
    $(document).on('click', '.scanned_imei', function() {
        var barid = $(this).val();
        if (confirm('Are you sure? You want to remove imei/srno: '+ barid)) {
            var ce = $(this);
            var qty = $(ce).closest('td').parent('tr').find(".qty1").val()
            $(ce).closest('td').parent('tr').find(".qty1").val(parseInt(qty)-1);
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
        var qty1=0; var qty=0;
        $('tr').each(function () {
            $(this).find('.qty1').each(function () {
                qty1 += parseFloat($(this).val());                    
            });  
            $(this).find('.qty').each(function () {
                qty += parseFloat($(this).val());                    
            });
        });
        if (parseInt(qty1) != parseInt(qty)){
            alert("Please scan all IMEIs");
            return false;
        }
    });
    
    $(document).on("click", ".submit-outward", function(event) {   
        event.preventDefault();
                var qty1=0; var qty=0;
                $('tr').each(function () {
                    $(this).find('.qty1').each(function () {
                        qty1 += parseFloat($(this).val());                    
                    });  
                    $(this).find('.qty').each(function () {
                        qty += parseFloat($(this).val());                    
                    });
                });
                if($('#awb_no').val()=="" && $('#courier_name').val()==""){
                    alert("Please enter AWB No /  Courier name");
                    return false;
                }else{
                
                if (parseInt(qty1) !== parseInt(qty)){
                    alert("Please scan all IMEIs");
                    return false;
                }else{
                        if (confirm('Do you want to Proceed!!')) {
                            var serialized = $('.outward').serialize();
                            $.ajax({
                                    url: "<?php echo base_url() ?>Ingram_Api/save_picked",
                                    method: "POST",
                                    data: serialized,
                                    dataType:'json',
                                    success: function (data)
                                    {     
                                        
                                        if(data.data === 'success'){
                                            alert("Order Picked successfully!!");                                            
                                            window.location = "<?php echo base_url() ?>Ingram_Api/po_invoice_print/"+data.message;
                                        }else if(data.data === "fail"){
                                            alert("Fail to submit pick the order!! ")
                                        }
                                    }
                                });
                        }
            }
            }
        });
    

</script>
<form class="outward">
   
    <div class="thumbnail" style="padding: 15px 0; margin: 0;font-size: 13px">
         <center><h3 style="margin-top: 0"><span class="mdi mdi-cart-outline fa-lg"></span> Pick and Verify </h3></center><hr>
        <div class="col-md-12">
            <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                <div class="col-md-6">SS Order Number : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $purchase_order[0]->id_sale_token ?></div>
                <div class="clearfix"></div><br>                                
                
            </div>
            
            <div class="clearfix"></div>                                    
            <input type="hidden" id="idbranch" class="idbranch" name="idbranch" value="<?php echo $purchase_order[0]->idbranch ?>" />
            <input type="hidden" id="idwarehouse" class="idwarehouse" name="idwarehouse" value="<?php echo $idwarehouse ?>" />                        
            <input type="hidden" id="id_sale_token" class="id_sale_token" name="id_sale_token" value="<?php echo $purchase_order[0]->id_sale_token ?>" />
            
        </div>
        <div class="clearfix"></div>
    </div>
    <input type="hidden" id="imeiscanned" class="form-control" />
    <div class="thumbnail" style="font-family: K2D;">
        <div class="col-md-12" id="product" style="overflow: auto;">
            <div class="row">
                <table class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px">
                    <thead class="bg-info">                        
                        <th class="col-md-3">Product</th>  
                        <th class="col-md-1">SKU</th>                          
                        <th class="col-md-1">Qty</th>
                        <th class="col-md-2">IMEI/SRNO</th>
                        <th class="col-md-5">Scanned Products</th>
                        <!--<th>Remove</th>-->
                    </thead>
                    <tbody id="product_data" style="border: 1px solid #C8D4D4">
                        <?php $i=1; foreach($purchase_order as $allo_data){ 
                        
                          if($allo_data->qty==0){ ?>                        
                           <tr class="product_data" id="m<?php echo $allo_data->idvariant?>">                                                
                               <td class="col-md-1">
                                    <?php echo $allo_data->full_name; ?>
                                </td>                                
                                <td><?php echo $allo_data->vendor_sku; ?></td>   
                                <td>
                                    <?php echo $allo_data->qty; ?>
                                </td>
                                <td class="col-md-1" colspan="7">
                                    <?php echo $allo_data->p_remark; ?>
                                </td>
                           </tr>
                      <?php  }else{ ?>
                        <tr>
                           
                            <td><?php echo $allo_data->full_name ?></td>    
                            <td><?php echo $allo_data->vendor_sku; ?></td>     
                            <td><input type="hidden" id="idproductcategory" class="idproductcategory" name="idproductcategory[]" value="<?php echo $allo_data->idproductcategory ?>" />
                            <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $allo_data->idcategory ?>" />
                            <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $allo_data->idbrand ?>" />
                            <input type="hidden" id="modelid" class="modelid" name="modelid[]" value="<?php echo $allo_data->idmodel ?>" />
                            <input type="hidden" id="idvariant" class="idvariant" name="idvariant[]" value="<?php echo $allo_data->idvariant ?>" />
                            <input type="hidden" id="id_godown" class="id_godown" name="id_godown[]" value="<?php echo $allo_data->idgodown ?>" />
                            <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $allo_data->idskutype ?>"/>                            
                            <input type="hidden" id="id_saletokenproduct" class="id_saletokenproduct" name="id_saletokenproduct[]" value="<?php echo $allo_data->id_saletokenproduct ?>"/>
                            
                            <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $allo_data->full_name; ?>" />
                            <input type="text" name="qty[]" class="form-control input-sm qty" value="<?php echo $allo_data->qty  ?>" readonly=""/>
                            <input type="hidden" name="price[]" value="<?php echo $allo_data->landing ?>"/>
                            <input type="hidden" name="cgst_per[]" value="<?php echo $allo_data->cgst ?>"/>
                            <input type="hidden" name="sgst_per[]" value="<?php echo $allo_data->sgst ?>"/>
                            <input type="hidden" name="igst_per[]" value="<?php echo $allo_data->igst ?>"/></td>
                            <td>
                                <div class="col-md-9" style="padding: 0; margin: 0">
                                    <input type="text" id="barcode" class="form-control input-sm barcode" value="" placeholder="Scan IMEI" <?php if ($allo_data->idskutype== 4) { ?> readonly="" <?php } ?> style="margin: 0"/>
                                </div>
                                <div class="col-md-3" style="padding: 0; margin: 0">
                                    <?php if($allo_data->idskutype==4){ ?>
                                    <input type="text" id="qty1" class="form-control input-sm qty1" if value="<?php echo $allo_data->qty  ?>" placeholder="Qty1" readonly=""  style="margin: 0"/>
                                    <?php }else{ ?>
                                    <input type="text" id="qty1" class="form-control input-sm qty1" if value="0" placeholder="Qty1" readonly=""  style="margin: 0"/>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="1" placeholder="Scanned IMEI" style="display: none"></textarea>
                                <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                            </td>
                    <?php  }?>
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
                <input type="hidden" name="count" value="<?php echo $i ?>" />
                <button type="button" class="submit-outward btn btn-primary pull-right btn-sub">Pack the Order</button>
                <div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div><br>
    </div>
</form>
<div class="alert_msg"></div>
<?php } include __DIR__.'../../footer.php'; ?>