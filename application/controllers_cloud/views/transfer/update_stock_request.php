<?php include __DIR__.'../../header.php';  
if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<script>
$(document).ready(function(){
    
    $(document).on('input', '.a_qty', function () {
         var a_qty = parseInt($(this).val());
         var avai = parseInt($(this).parent().parent().parent().find('.avail').val()); 
         var qty = parseInt($(this).parent().parent().parent().find('.qty').val());          
         if(a_qty>avai){             
             $(this).val('0');
             alert("Sorry, Stock not available!");
             return false;
         }else if(a_qty > qty ){
             $(this).val('0');
             alert("Sorry, Entered quantity greater than requested quantity!");
             return false;
         }
    });
    $(document).on("click", ".submit-transfer", function (event) {
            event.preventDefault();
            var bool=true;
            $(".a_qty").each(function () {
                if($(this).val() == '' || $(this).val() == ""){
                    bool=false;
                    return false;
                }
            });   
            if(bool==true){                
            if (confirm('Do you want to proceed!!')) {
            var serialized = $('.transfer_request').serialize();
            var idtransfer = $('#idtransfer').val()
            $.ajax({
                url: "<?php echo base_url() ?>Transfer/approve_stock_request",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if (data.data === 'success') {
                        alert("Stock request ready to scan!!");
//                        window.location = "<?php echo base_url() ?>Transfer/stock_requests_for_me";                    
                        window.location = "<?php echo base_url() ?>Transfer/stock_trasnfer/"+idtransfer;    
                    } else if (data.data === "fail") {
                        alert("Fail to approve!! ");
                    } else {
                        alert("Something went wronge !!");
                    }
                }
            });
            }
            }else{
                alert("Please enter approved quantity..!");                
                return false;
            }
        });
        $(document).on("click", ".submit-reject", function (event) {
            event.preventDefault();
           var idtransfer = $('#idtransfer').val()
           var remark = $('#remark').val()
            if (confirm('Do you want to Reject!!')) {           
            $.ajax({
                url: "<?php echo base_url() ?>Transfer/reject_stock_request",
                method: "POST",
                data: {idtransfer: idtransfer,  remark: remark},
                dataType: 'json',
                success: function (data)
                {
                    if (data.data === 'success') {
                        alert("Stock request Rejected!!");
                        window.location = "<?php echo base_url() ?>Transfer/stock_requests_for_me";                    
                    } else if (data.data === "fail") {
                        alert("Fail to Reject!! ");
                    } else {
                        alert("Something went wronge !!");
                    }
                }
            });
            }
        });
        
    
});
</script>
<form class="transfer_request">
<center>
    <h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> Stock Request Details </h3>
</center><hr>
<div class="thumbnail" style="padding: 15px 0; margin: 0;font-size: 13px">
        <div class="col-md-10 col-md-offset-1">
             <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                <b> Request From ,</b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[1]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[1]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" >
                
                <b>Request To , </b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[0]->branch_contact; ?><br>
                
            </div>  
            <div class="clearfix"></div><hr>
            <div class="col-md-8 col-xs-8">
                <div class="col-md-3">Transfer Mandate :- <b style="color: #0e10aa !important;"><?php echo $transfer_data[0]->id_transfer ?></b></div><br>                
                <div class="col-md-3">Status :- <b style="color: #0e10aa !important;"> <?php if($transfer_data[0]->status==0){ echo 'Pending'; }elseif($transfer->status==1){ echo 'Approved'; }elseif($transfer->status==2){ echo 'Rejected';}elseif($transfer->status > 2 || $transfer->status < 5 ){ echo 'Transffered';}elseif($transfer->status > 2 || $transfer->status < 5 ){ echo 'Received';} ?></b></div><br>                
                <br>
            </div>
            <div class="col-md-4 col-xs-4">                 
                    <div>Request Date :- <?php echo date('d-M-Y h:i:s', strtotime($transfer_data[0]->entry_time)) ?></div><br>                    
                    
                 
            </div>
            <div class="clearfix"></div><br>
            <div class="clearfix"></div>                                    
            <input type="hidden" id="idbranch" class="idbranch" name="idbranch" value="<?php echo $branch_data[1]->id_branch ?>" />
            <input type="hidden" id="idwarehouse" class="idwarehouse" name="idwarehouse" value="<?php echo $branch_data[0]->id_branch ?>" />
            <input type="hidden" id="idtransfer" class="idtransfer" name="idtransfer" value="<?php echo $transfer_data[0]->id_transfer ?>" />
            <input type="hidden" id="gst_type" class="gst_type" name="gst_type" value="<?php echo $gst_type; ?>" />
        </div>
        <div class="clearfix"></div>
    </div>
<div class="thumbnail" style="font-family: K2D;">
        <div class="col-md-12" id="product" style="overflow: auto;">
            <div class="row">
                <table class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px">
                    <thead class="bg-info">
                        <th class="col-md-1">Srno</th>
                        <th class="col-md-3">Product</th>  
                        <th class="col-md-1">Godown Name</th>  
                        <th class="col-md-1">Requested Qty</th>
                        <th class="col-md-1">Current Qty</th>
                        <th class="col-md-2">Approved Qty</th>                        
                    </thead>
                    <tbody id="product_data" style="border: 1px solid #C8D4D4">
                        <?php $i=1; foreach($transfer_product as $product_data){ ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $product_data->full_name ?></td>
                            <td><?php echo $product_data->godown_name ?></td>
                            <td><input type="hidden" name="id_transfer_product[]" value="<?php echo $product_data->id_transfer_product ?>" />
                                <input type="text"  class="form-control input-sm qty" value="<?php echo $product_data->qty  ?>" readonly=""/>
                            </td>
                            <td>  <input type="hidden"  class="avail" value="<?php echo $product_data->stock_qty ?>" /> <?php echo $product_data->stock_qty ?></td>
                            <td>
                            <div class="col-md-9" style="padding: 0; margin: 0">
                                <input type="text" id="a_qty" name="a_qty[]" class="form-control input-sm a_qty" value="" placeholder="Enter Quantity" style="margin: 0"/>
                            </div>
                            </td>                            
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>                                   
                <div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div><br>
        <div>                        
            <div class="col-md-3">
                <button type="button" class="submit-reject btn btn-danger pull-right btn-sub">Reject</button>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-1">Remark :- </div>
            <div class="col-md-3"><textarea class="form-control input-sm" id="remark" name="remark"  placeholder="Enter remark" ></textarea></div>
            <div class="col-md-2">
                <button type="button" class="submit-transfer btn btn-primary pull-right btn-sub">Proceed</button>
            </div>
            <div class="col-md-1"></div>   
            <div class="clearfix"></div><br>
        </div>
    </div>

</form>
<div id="allocated_stock_data" style="max-height: 500px;overflow: auto"></div><div class="clearfix"></div><br>
<?php if(count($transfer_product)){ ?>

    <!--<input type="hidden" id="qty_changed" name="qty_changed" value="0" />-->

<?php }} include __DIR__.'../../footer.php'; ?>