<?php include __DIR__.'../../header.php';  
if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
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
            
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_check_valid_barcode",
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
                if (parseInt(qty1) !== parseInt(qty)){
                    alert("Please scan all IMEIs");
                    return false;
                }else{
                        if (confirm('Do you want to Proceed!!')) {
                            var serialized = $('.outward').serialize();
                            $.ajax({
                                    url: "<?php echo base_url() ?>Outward/save_outward",
                                    method: "POST",
                                    data: serialized,
                                    dataType:'json',
                                    success: function (data)
                                    {                            
                                        if(data.data === 'success'){
                                            alert("Data submitted successfully!!");
                                            location.reload();	
                                             window.location = "<?php echo base_url() ?>Stock_allocation/stock_allocation_details/"+data.message;
                                        }else if(data.data === "fail"){
                                            alert("Fail to submit outward data!! ")
                                        }else{
                                            alert("Select at least one model !! ")
                                        }
                                    }
                                });
                        }
            }
        });
    

</script>
<form class="outward">
    <center><h3 style="margin-top: 0"><span class="mdi mdi-barcode-scan fa-lg"></span> Outward </h3></center>
    <div class="thumbnail" style="padding: 15px 0; margin: 0;font-size: 13px">
        <div class="col-md-10">
            <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                <div class="col-md-6">Mandate No : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $stock_allocation[0]->id_stock_allocation ?></div>
                <div class="clearfix"></div><br>                                
                <div class="col-md-6">Branch : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $stock_allocation[0]->branch_name ?></div>
                <div class="clearfix"></div>                              
            </div>
            <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                <div class="col-md-6" >Allocation Date : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $stock_allocation[0]->date ?></div>
                <div class="clearfix"></div><br>
                <div class="col-md-6">Outward Date : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo date('Y-m-d'); ?></div>
                <div class="clearfix"></div><br>
            </div>
            <div class="col-md-4">                
                <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">Remark : </div>
                <div class="col-md-8" style="font-family: Kurale; font-size: 20px;color: #0e10aa !important;text-align: left">
                    <textarea class="form-control input-sm" name="remark"  placeholder="Enter remark" ></textarea>
                </div>
                <div class="clearfix"></div>                              
            </div>
            <div class="clearfix"></div>                                    
            <input type="hidden" id="idbranch" class="idbranch" name="idbranch" value="<?php echo $stock_allocation[0]->idbranch ?>" />
            <input type="hidden" id="idwarehouse" class="idwarehouse" name="idwarehouse" value="<?php echo $stock_allocation[0]->idwarehouse ?>" />
            <input type="hidden" id="idallocation" class="idallocation" name="idallocation" value="<?php echo $stock_allocation[0]->id_stock_allocation ?>" />
            <input type="hidden" id="gst_type" class="gst_type" name="gst_type" value="<?php echo $gst_type; ?>" />
            
            <input type="hidden" id="idcompany_to" class="idcompany_to" name="idcompany_to" value="<?php echo $stock_allocation[0]->idcompany ?>" />
            <input type="hidden" id="idcompany_from" class="idcompany_from" name="idcompany_from" value="<?php echo $w_idcompany ?>" />
        </div>
        <div class="clearfix"></div>
    </div>
    <input type="hidden" id="imeiscanned" class="form-control" />
    <div class="thumbnail" style="font-family: K2D;">
        <div class="col-md-12" id="product" style="overflow: auto;">
            <div class="row">
                <table class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px">
                    <thead class="bg-info">
                        <th>Srno</th>
                        <th class="col-md-3">Product</th>  
                        <th class="col-md-1">Godown Name</th>  
                        <th class="col-md-1">Qty</th>
                        <th class="col-md-2">IMEI/SRNO</th>
                        <th class="col-md-5">Scanned Products</th>
                        <!--<th>Remove</th>-->
                    </thead>
                    <tbody id="product_data" style="border: 1px solid #C8D4D4">
                        <?php $i=1; foreach($stock_allocation as $allo_data){ ?>
                        <tr>
                            <td><?php echo $allo_data->id_stock_allocation_data ?></td>
                            <td><?php echo $allo_data->full_name ?></td>
                            <td><?php echo $allo_data->godown_name ?></td>
                            <td><input type="hidden" id="idproductcategory" class="idproductcategory" name="idproductcategory[]" value="<?php echo $allo_data->idproductcategory ?>" />
                            <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $allo_data->idcategory ?>" />
                            <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $allo_data->idbrand ?>" />
                            <input type="hidden" id="modelid" class="modelid" name="modelid[]" value="<?php echo $allo_data->idmodel ?>" />
                            <input type="hidden" id="idvariant" class="idvariant" name="idvariant[]" value="<?php echo $allo_data->idvariant ?>" />
                            <input type="hidden" id="id_godown" class="id_godown" name="id_godown[]" value="<?php echo $allo_data->idgodown ?>" />
                            <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $allo_data->idskutype ?>"/>
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
<!--                            <td>
                                <input type="hidden" class="form-control input-sm id_stock_allocation_data" value="<?php echo $allo_data->id_stock_allocation_data  ?>" />
                                <a class="btn remove" name="remove[]" id="remove" style="color: #cc0033"><i class="fa fa-trash-o fa-lg"></i></a>
                            </td>-->
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
                <input type="hidden" name="count" value="<?php echo $i ?>" />
                <button type="button" class="submit-outward btn btn-primary pull-right btn-sub">Submit</button>
                <div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div><br>
    </div>
</form>
<?php } include __DIR__.'../../footer.php'; ?>