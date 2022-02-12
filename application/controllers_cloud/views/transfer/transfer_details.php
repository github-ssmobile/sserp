<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> Stock Request Details </h3></center>
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
                <?php if($transfer->status < 3){ ?>
            <thead class="bg-info">
                <th>SrNo</th>
                <th class="col-md-7">Product</th>
                <th class="col-md-1">Godown</th>
                <th>Qty</th>
            </thead>
            <tbody>
                <?php 
                $i=1;
                foreach ($transfer_product as $product) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $product->full_name; ?></td>
                    <td><?php echo $product->godown_name; ?></td>
                    <td><?php echo $product->qty ?></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
            <?php }else { ?>           
                
                
                <thead class="bg-info">
                   <th style="width: 2%;">Sr</th>
                    <th style="width: 25%;" class="col-md-5">Product</th>
                    <th style="width: 10%;" class="col-md-1">Godown</th>
                    <th style="width: 5%;">Qty</th>
                    <th style="width: 58%;" class="col-md-5">IMEI/SRNO</th>                     
                </thead>
                <tbody>
                    <?php $i=1; foreach ($transfer_product as $product) { 
                        $array = explode(',', $product->imei_no); ?>
                    <tr>
                        <td><?php echo $i++; ?></td>                        
                        <td><?php echo $product->full_name; ?></td>
                        <td><?php echo $product->godown_name; ?></td>
                        <td><?php 
                            if($transfer->status==0 || $transfer->status==2){
                                echo $product->qty;    
                            }else{
                                echo $product->approved_qty;
                            }
                        ?>
                        </td>
                        <td><?php $j=0;$im=""; foreach($array as $imei){
                            if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; }
                            $j++;                                            
                        }
                        echo $im;                                                
                        //echo $product->imei_no ?></td>
                    </tr>
            <?php }  ?>
                </tbody>
            <?php }  ?>
            </table>
        </div>
    </div>
    <?php          
         if($transfer->status >= 4){ ?>

        <div style="position: fixed; right: 30px; top: 80px;"><a target="_blank" class="thumbnail textalign" href="<?php echo base_url('Transfer/transfer_dc/'.$transfer->id_transfer) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-info " style="color: blue"></i></a></div>
        <!--<a class="btn hovereffect1" href="<?php // echo base_url('Transfer/transfer_dc/'.$transfer->id_transfer) ?>" target="_blank" style="position: fixed; right: 30px; top: 80px; border-radius: 50px; border: 1px solid green;"><i class="pe pe-7s-note2" style="font-size: 30px"></i></a>-->
        <div class="thumbnail">
            <center><h4 style="margin-bottom: 0"><i class="mdi mdi-truck"></i> Shipment Details</h4></center>
            <div class="clearfix"></div><hr>
            <div class="col-md-2 text-muted">Dispatch Date</div>
            <div class="col-md-2"><?php echo $transfer->dispatch_date ?></div>
            <div class="col-md-2 text-muted">Dispatch Type</div>
            <div class="col-md-1"><?php echo $transfer->dispatch_type ?></div>
            <div class="col-md-2 text-muted">Courier/ Transport Name</div>
            <div class="col-md-3"><?php echo $transfer->courier_name ?></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2 text-muted">POP/LR Number</div>
            <div class="col-md-2"><?php echo $transfer->po_lr_no ?></div>
            <div class="col-md-2 text-muted">No of Boxes</div>
            <div class="col-md-1"><?php echo $transfer->no_of_boxes ?></div>
            <div class="col-md-2 text-muted">Remark</div>
            <div class="col-md-3"><?php echo $transfer->shipment_remark ?></div><div class="clearfix"></div><hr>
            <?php if($_SESSION['idbranch'] == $transfer->idbranch && $transfer->status == 4){ ?>
                <div class="col-md-1 pull-right">
                    <input type="hidden" name="shipment_entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    <input type="hidden" name="dc" value="<?php echo $transfer->id_transfer ?>"/>
                    <input type="hidden" name="shipment_receive_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                    <input type="hidden" name="idbranch" value="<?php echo $transfer->idbranch ?>"/>
                    <input type="hidden" name="count" value="<?php echo $i-1 ?>" />
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Transfer/receive_shipment_stock_from_branch') ?>" class="btn btn-primary">Receive</button>
                </div>
                <div class="col-md-4 pull-right"><input type="text" class="form-control input-sm" name="shipment_received_remark" placeholder="Enter Shipment Received Remark"/></div>
                <div class="col-md-1 pull-right">Remark</div>
                <div class="clearfix"></div>
            <?php } ?>
        </div>        
<?php } ?>
        <div style="position: fixed; right: 30px; bottom: 70px;"><a  href="<?php echo base_url('Transfer/transfer_dc/'.$transfer->id_transfer) ?>" class="btn btn-floating btn-large waves-effect waves-light gradient2 print-a"><i class="pe pe-7s-print" style="font-size: 30px"></i></a></div>
<?php }  include __DIR__ . '../../footer.php'; ?>
