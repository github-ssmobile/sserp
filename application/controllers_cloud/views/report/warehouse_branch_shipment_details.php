<?php include __DIR__.'../../header.php'; ?>

<style>

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="fa fa-truck  fa-lg"></span> Warehouse To Branch Shipment Report</h3></center></div><div class="clearfix"></div><hr><br>

<div class="col-md-5">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-4">
    <div id="count_1" class="text-info"></div>
</div>
<div class="col-md-2">
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('warehouse_to_branch_shipment');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div><div class="clearfix"></div><br>
<div class="thumbnail" style="border-radius: 0; margin-bottom: 0"><br>
    <div id="sale_data" style="overflow-x: auto;height: 600px">
        <?php if($outward_data){  ?>
         <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
            <b> From ,</b><br>
            <b>Branch: &nbsp; <?php echo $outward_data->branch_from ?></b><br>                        
            <b>Contact:</b> <?php echo $outward_data->branch_contact_from; ?><br>
        </div>
        <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
            <b> To , </b><br>
            <b>Branch: &nbsp; <?php echo $outward_data->branch_name ?></b><br>                        
            <b>Contact:</b> <?php echo $outward_data->branch_contact; ?><br>
        </div>  
        <div class="clearfix"></div><hr>
        <div class="col-md-8 col-xs-8">
            <div class="col-md-3">DC</div><div class="col-md-6"> - &nbsp; <b style="color: #0e10aa !important;"> <?php echo $outward_data->idstock_allocation ?></b></div><br>                
            <div class="col-md-3">Status</div><div class="col-md-6"> - &nbsp; <b style="color: #0e10aa !important;"> <?php if($outward_data->status==1){ echo 'Intransit'; }elseif($outward_data->status==2){ echo 'Received'; }?></b></div><br>
            <div class="col-md-3">dispatch_type</div><div class="col-md-6"> - &nbsp; <?php echo $outward_data->dispatch_type ?></div><br>
        </div>
        <div class="col-md-4 col-xs-4">
            <div class="col-md-4">Dispatch Date</div><div class="col-md-6"> - <?php echo date('d-M-Y ', strtotime($outward_data->dispatch_date)) ?></div><br>
            <div class="col-md-4">Received Date</div><div class="col-md-6"> - <?php   if($outward_data->shipment_received_date==NULL){ echo ""; }else{  echo date('d-M-Y', strtotime($outward_data->shipment_received_date)); } ?></div><br>

        </div>
        <div class="clearfix"></div><br>
        <?php } ?>
        <table class="table table-bordered table-condensed" id="warehouse_to_branch_shipment">
            <thead class="fixedelementtop" style="background-color: #99ccff">
                <th>Branch</th>
                <th>Date</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Imei No</th>
                <th>Qty</th>
                <!--<th>Status</th>-->
            </thead>
            <tbody class="data_1">
                <?php $sr=1; foreach($wh_detals as $wh){ ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $wh->date; ?></td>
                    <td><?php echo $wh->brand_name; ?></td>
                    <td><?php echo $wh->full_name;  ?></td>
                    <td><?php echo $wh->imei_no; ?></td>
                    <td><?php echo $wh->qty; ?></td>
                    <!--<td><?php echo $wh->shipment_status; ?></td>-->
                <?php } ?>
                    </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>