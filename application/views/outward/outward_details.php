<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else {     ?>
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
<?php
 $outward=$outward_data[0]; ?>
    <div class="thumbnail col-md-offset-2" style="padding: 0; background: #fffcf0;margin: 0 20px -1px 20px;"><br>     
        <center><h3 style="margin-top: 0"><span class="mdi mdi-truck fa-lg"></span> Outward Details </h3></center>
            <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                <b>FROM, </b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[0]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b> To,</b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[1]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[1]->branch_contact; ?><br>
                
            </div>  
            <div class="clearfix"></div><hr>
            <div class="col-md-8 col-xs-8">
                <div class="col-md-2">Mandate Number</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"><?php echo $outward->id_stock_allocation ?></b></div><br>
                <div class="col-md-2">DC Number</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"><?php echo $outward->id_outward ?></b></div><br>
                <div class="col-md-2">Remark</div><div class="col-md-6"> :- <?php echo $outward->outward_remark ?></div>                
                <br>
            </div>
            <div class="col-md-4 col-xs-4">
                <div class="col-md-4">Allocation Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y', strtotime($outward->confirm_time)) ?></div><br>
                <div class="col-md-4">Outward Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y', strtotime($outward->scan_time)) ?></div><br>
                <div class="col-md-4">Shipment Date</div><div class="col-md-6"> :- <?php if($outward->shipment_entry_time!="0000-00-00 00:00:00") { echo date('d-M-Y', strtotime($outward->shipment_entry_time)); } ?></div><div class="clearfix"></div>                  
            </div>
            <div class="clearfix"></div><br>
        
        <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin: 0;padding: 30px;">
            <thead class="">
                <th style="width: 2%;">Sr</th>
                <th style="width: 25%;" class="col-md-5">Product</th>
                <th style="width: 10%;" class="col-md-1">Godown</th>
                <th style="width: 5%;">Qty</th>
                <th style="width: 58%;" class="col-md-5">IMEI/SRNO</th>
               
            </thead>
            <tbody>
                <?php $i=1; foreach ($outward_data as $product) { 
                    $array = explode(',', $product->imei);       ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $product->full_name; ?></td>
                    <td><?php echo $product->godown_name; ?></td>
                    <td><?php echo $product->qty ?></td>
                     <td><?php $j=0;$im=""; foreach($array as $imei){
                            if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; }
                            $j++;                                            
                        }
                                        echo $im;                                                
                                        //echo $product->imei ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<div class="thumbnail col-md-offset-2" style="padding: 0; background: #fffcf0;margin: 0 20px -1px 20px;padding: 10px"> <center><b style="color: blue">
     <?php if($outward->status == 0){
           echo "Just Scanned";
            }elseif($outward->status == 1){
                echo "In-Transit";
            }elseif($outward->status == 2){
                 echo "Received";
            }
     ?>
</b></center></div>

   <?php if($outward->status == 0 && $this->session->userdata('role_type')!=2){ ?>        
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
                    <input type="hidden" class="form-control input-sm" id="courier_name" name="courier_name" placeholder="Enter Courier/ Transport Name"/>
                   
                </div><div class="clearfix"></div><br>
                 <div class="col-md-2">Vehicle No</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="vehicle_no" placeholder="Enter Vehicle Number"/></div>
                <div class="col-md-2">POP/LR Number</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="po_lr_no" placeholder="Enter POP/LR Number"/></div>
                <div class="col-md-2">No of Boxes</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="no_of_boxes" placeholder="No of Boxes" required=""/></div>
                <div class="clearfix"></div><br>
                <div class="col-md-2">Remark</div>
                <div class="col-md-2"><input type="text" class="form-control input-sm" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                <div class="col-md-1 pull-right">
                    <input type="hidden" name="shipment_entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    <input type="hidden" name="id_outward" value="<?php echo $outward->id_outward ?>"/>
                    <input type="hidden" name="id_allocation" value="<?php echo $outward->idstock_allocation ?>"/>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Outward/save_shipment_details') ?>" class="btn btn-primary">Submit</button>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    <?php } elseif($outward->status > 0){ ?>
        <!--<div style="position: fixed; right: 30px; top: 80px;"><a class="btn hovereffect1" href="<?php echo base_url('Outward/outward_dc/'.$outward->id_outward) ?>" target="_blank" style="border-radius: 50px; border: 1px solid green;"><i class="pe pe-7s-note2" style="font-size: 30px"></i></a></div>-->       
        <div class="thumbnail" style="background: #fffcf0;margin: 0 20px -1px 20px;">
            <center><h4 style="margin-bottom: 0"><i class="mdi mdi-truck"></i> Shipment Details</h4></center>
            <div class="clearfix"></div><hr>
            <div class="col-md-2 text-muted">Dispatch Date</div>
            <div class="col-md-2"><?php echo $outward->dispatch_date ?></div>
            <div class="col-md-2 text-muted">Dispatch Type</div>
            <div class="col-md-1"><?php echo $outward->dispatch_type ?></div>
            <div class="col-md-2 text-muted">Courier/ Transport Name</div>
            <div class="col-md-3"><?php echo $outward->courier_name ?></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2 text-muted">POP/LR Number</div>
            <div class="col-md-2"><?php echo $outward->po_lr_no ?></div>
            <div class="col-md-2 text-muted">No of Boxes</div>
            <div class="col-md-1"><?php echo $outward->no_of_boxes ?></div>
            <div class="col-md-2 text-muted">Remark</div>
            <div class="col-md-3"><?php echo $outward->shipment_remark ?></div><div class="clearfix"></div><br>
        </div>
<?php }} include __DIR__.'../../footer.php'; ?>