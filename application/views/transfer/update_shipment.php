<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('.iddispatchtype').change(function (){
            var iddispatch = $('.iddispatchtype option:selected').text();
            $('#dispatch_type').val(iddispatch);
       });
   
       $('.idtvendors').change(function (){
            var idtransfer = $('.idtvendors option:selected').text();
            $('#courier_name').val(idtransfer);
       });
    });
</script>
<center><h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> Update Shipment </h3></center>
<?php foreach ($transfer_data as $transfer){ ?>
    <div style="font-family: K2D; font-size: 15px;">
        <div class="thumbnail" style="border-radius: 0; margin-bottom: 0"><br>
          <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                <b> Request From ,</b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[1]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[1]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b>Request To , </b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[0]->branch_contact; ?><br>
            </div>  
            <div class="clearfix"></div><hr>
            <div class="col-md-8 col-xs-8">
                <div class="col-md-3">Transfer Mandate</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></div><br>                
                <div class="col-md-3">Status</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"> <?php if($transfer->status==0){ echo 'Pending'; }elseif($transfer->status==1){ echo 'Approved'; }elseif($transfer->status==2){ echo 'Rejected';}elseif($transfer->status > 2 || $transfer->status < 5 ){ echo 'Transffered';}elseif($transfer->status > 2 || $transfer->status < 5 ){ echo 'Received';} ?></b></div><br>
                <div class="col-md-3">Remark</div><div class="col-md-6"> :- <?php echo $transfer->transfer_remark ?></div><br>
            </div>
            <div class="col-md-4 col-xs-4">
                <div class="col-md-4">Request Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y h:i:s', strtotime($transfer->entry_time)) ?></div><br>
                <div class="col-md-4">Dispatch Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y h:i:s', strtotime($transfer->dispatch_date)) ?></div><br>
                <div class="col-md-4">Received Date</div><div class="col-md-6"> :- <?php   if($transfer->shipment_received_date==NULL){ echo ""; }else{  echo date('d-M-Y h:i:s', strtotime($transfer->shipment_received_date)); } ?></div><br>
                
            </div>
            <div class="clearfix"></div><br>
            
           
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px">
                <thead class="bg-info">
                    <th>Sr</th>                 
                    <th class="col-md-6">Product</th>
                    <th>Godown</th>
                    <th>Requested Qty</th>
                    <th>Transferred Qty</th>
                    <th class="col-md-4">IMEI/SRNO</th>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($transfer_product as $product) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>                        
                        <td><?php echo $product->full_name; ?></td>
                        <td><?php echo $product->godown_name; ?></td>
                        <td><?php echo $product->qty ?></td>
                            <td><?php echo $product->approved_qty ?></td>
                        <td><?php echo $product->imei_no ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php       
        if($transfer->status == 3){ 
            if($idbranch==$transfer->transfer_from){
            ?>        
        <div class="thumbnail" style=" margin: 0 20px -1px 20px;">
            <form>
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
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="po_lr_no" placeholder="Enter POP/LR Number"/></div>
                <div class="col-md-2">No of Boxes</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="no_of_boxes" placeholder="No of Boxes" required=""/></div>
                <div class="col-md-2">Remark</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                <div class="col-md-1 pull-right">
                    <input type="hidden" name="shipment_entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    <input type="hidden" name="id_transfer" value="<?php echo $transfer->id_transfer ?>"/>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Transfer/save_shipment_details') ?>" class="btn btn-primary">Submit</button>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
        <?php } }  ?>
<?php }  include __DIR__ . '../../footer.php'; ?>
