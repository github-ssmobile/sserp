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
        
         $('.iddispatchtype').change(function (){
            var iddispatch = $('.iddispatchtype option:selected').text();
            $('#dispatch_type').val(iddispatch);
       });
   
       $('.idtvendors').change(function (){
            var idtransfer = $('.idtvendors option:selected').text();
            $('#courier_name').val(idtransfer);
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
    $(document).on('click', '.btn-sub-', function() {
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
    
    $(document).on("click", ".submit-transfer", function(event) {   
        event.preventDefault();
        
        var idtransfer=$(".idtransfer").val();
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
        
        if($(".iddispatchtype").val() && $(".idtvendors").val() && $('#po_lr_no').val() && $('#no_of_boxes').val() && $('#shipment_remark').val()){
            
        }else{
            alert("Update shipment details");            
            return false;
        }
        
            if (confirm('Do you want to Proceed!!')) {
                var serialized = $('.transfer').serialize();
                $.ajax({
                        url: "<?php echo base_url() ?>Transfer/save_transfer",
                        method: "POST",
                        data: serialized,
                        dataType:'json',
                        success: function (data)
                        {                            
                            if(data.data === 'success'){
                                alert("Data submitted successfully!!");
                                
                                window.location = "<?php echo base_url() ?>Transfer/transfer_details/"+idtransfer;
                            }else if(data.data === "fail"){
                                alert("Fail to submit transfer data!! ")
                            }else{
                                alert("Select at least one model !! ")
                            }
                        }
                    });
            }
        });
        
    

</script>
<form class="transfer">
    <center><h3 style="margin-top: 0"><span class="mdi mdi-barcode-scan fa-lg"></span> Transfer </h3></center>
    <div class="thumbnail" style="padding: 15px 0; margin: 0;font-size: 13px">
        <div class="col-md-10">
            <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                <div class="col-md-6">Mandate No : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $transfer_data[0]->id_transfer ?></div>
                <div class="clearfix"></div><br>                                
                <div class="col-md-6">Branch : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $branch_data[1]->branch_name ?></div>
                <div class="clearfix"></div>                              
            </div>
            <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                <div class="col-md-6" >Request Date : </div>
                <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $transfer_data[0]->date ?></div>
                <div class="clearfix"></div><br>
                <div class="col-md-6">Transfer Date : </div>
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
            <input type="hidden" id="idbranch" class="idbranch" name="idbranch" value="<?php echo $branch_data[1]->id_branch ?>" />
            <input type="hidden" id="idwarehouse" class="idwarehouse" name="idwarehouse" value="<?php echo $branch_data[0]->id_branch ?>" />
            <input type="hidden" id="idcompany_to" class="idcompany_to" name="idcompany_to" value="<?php echo $branch_data[1]->idcompany ?>" />
            <input type="hidden" id="idcompany_from" class="idcompany_from" name="idcompany_from" value="<?php echo $branch_data[0]->idcompany ?>" />
            <input type="hidden" id="idtransfer" class="idtransfer" name="idtransfer" value="<?php echo $transfer_data[0]->id_transfer ?>" />
            <input type="hidden" id="gst_type" class="gst_type" name="gst_type" value="<?php echo $gst_type; ?>" />
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
                    </thead>
                    <tbody id="product_data" style="border: 1px solid #C8D4D4">
                        <?php $i=1; foreach($transfer_product as $product_data){ ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $product_data->full_name ?></td>
                            <td><?php echo $product_data->godown_name ?></td>
                            <td><input type="hidden" id="idproductcategory" class="idproductcategory" name="idproductcategory[]" value="<?php echo $product_data->idproductcategory ?>" />
                            <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $product_data->idcategory ?>" />
                            <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $product_data->idbrand ?>" />
                            <input type="hidden" id="modelid" class="modelid" name="modelid[]" value="<?php echo $product_data->idmodel ?>" />
                            <input type="hidden" id="idvariant" class="idvariant" name="idvariant[]" value="<?php echo $product_data->idvariant ?>" />
                            <input type="hidden" id="id_godown" class="id_godown" name="id_godown[]" value="<?php echo $product_data->idgodown ?>" />
                            <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $product_data->idskutype ?>"/>
                            <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $product_data->full_name; ?>" />
                            <input type="text" name="qty[]" class="form-control input-sm qty" value="<?php echo $product_data->approved_qty  ?>" readonly=""/>
                            <input type="hidden" name="price[]" value="<?php echo $product_data->landing ?>"/>
                            <input type="hidden" name="cgst_per[]" value="<?php echo $product_data->cgst ?>"/>
                            <input type="hidden" name="sgst_per[]" value="<?php echo $product_data->sgst ?>"/>
                            <input type="hidden" name="igst_per[]" value="<?php echo $product_data->igst ?>"/>
                            <input type="hidden" name="id_transfer_product[]" value="<?php echo $product_data->id_transfer_product ?>"/></td>
                            <td>
                                <div class="col-md-9" style="padding: 0; margin: 0">
                                    <input type="text" id="barcode" class="form-control input-sm barcode" value="" placeholder="Scan IMEI" <?php if ($product_data->idskutype== 4) { ?> readonly="" <?php } ?> style="margin: 0"/>
                                </div>
                                <div class="col-md-3" style="padding: 0; margin: 0">
                                    <?php if($product_data->idskutype==4){ ?>
                                    <input type="text" id="qty1" class="form-control input-sm qty1" if value="<?php echo $product_data->approved_qty  ?>" placeholder="Qty1" readonly=""  style="margin: 0"/>
                                    <?php }else{ ?>
                                    <input type="text" id="qty1" class="form-control input-sm qty1" if value="0" placeholder="Qty1" readonly=""  style="margin: 0"/>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="1" placeholder="Scanned IMEI" style="display: none"></textarea>
                                <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                            </td>                            
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
            
                <br><?php 
                 $role_type=$this->session->userdata('role_type'); 
                 if($role_type!=0){
                    ?>
                <div class="thumbnail" >            
                <center><h4 style="margin-bottom: 0"><i class=""></i> Shipment Details</h4></center>
                <div class="clearfix"></div><hr>
                <div class="col-md-2">Dispatch Date</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="dispatch_date" value="<?php echo $now ?>" readonly=""/></div>
                <div class="col-md-2">Dispatch Type</div>
                <div class="col-md-2">
                    <select class="select form-control input-sm iddispatchtype" required="" name="iddispatchtype" >
                        <option value="">Select Type</option>
                        <?php foreach ($dispatch_data as $dispatch){ ?>
                        <option value="<?php echo $dispatch->id_dispatch_type ?>"><?php echo $dispatch->dispatch_type?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="dispatch_type" name="dispatch_type">
                </div>
                <div class="col-md-2">Courier/ Transport Name</div>
                <div class="col-md-2">
                     <select class="select form-control input-sm idtvendors" required="" name="idtvendors" >
                        <option value="">Select Transport Vendor</option>
                        <?php foreach ($transport_vendor as $tvendors){ ?>
                        <option value="<?php echo $tvendors->id_transport_vendor ?>"><?php echo $tvendors->transport_vendor_name?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" class="form-control input-sm" id="courier_name" name="courier_name" placeholder="Enter Courier/ Transport Name"/></div><div class="clearfix"></div><br>
                <div class="col-md-2">POP/LR Number</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm po_lr_no" id="po_lr_no" name="po_lr_no" placeholder="Enter POP/LR Number"/></div>
                <div class="col-md-2">No of Boxes</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm no_of_boxes" id="no_of_boxes" name="no_of_boxes" placeholder="No of Boxes" required=""/></div>
                <div class="col-md-2">Remark</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm shipment_remark" id="shipment_remark" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                <div class="col-md-1 pull-right">
                    <input type="hidden" name="shipment_entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    <input type="hidden" name="id_transfer" value="<?php echo $transfer_data[0]->id_transfer ?>"/>
                    <!--<button type="submit" formmethod="POST" formaction="<?php // echo base_url('Transfer/save_shipment_details') ?>" class="btn btn-primary">Submit</button>-->
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
                <br>
                <?php } ?>
                <input type="hidden" name="count" value="<?php echo $i ?>" />
                <button type="button" class="submit-transfer btn btn-primary pull-right btn-sub">Submit</button>
                <div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div><br>
    </div>
</form>
<?php } include __DIR__.'../../footer.php'; ?>