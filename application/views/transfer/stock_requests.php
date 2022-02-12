<?php include __DIR__ . '../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> <?php echo $title ?></h3></center><br>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>

<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover reports" id="my_stock_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>DC</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Request From</th>
                <th>Total Product</th>
                <th>Remark</th>
                <th>Action</th>
            </thead>
            <tbody>                
               <?php  foreach ($transfer_data as $transfer){ ?>
                <tr>
                    <td><b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></td>
                    <td><?php echo $transfer->date ?></td>
                    <td><?php echo $transfer->branch_from  ?></td>
                    <td><?php echo $transfer->branch_to ?></td>
                    <td><?php echo $transfer->total_product ?></td>
                    <td><?php echo $transfer->transfer_remark ?></td>
                    <?php if($transfer->status==3){ ?>
                    <td><a target="" class="thumbnail gradient2 textalign" href="<?php echo site_url() ?>Transfer/update_shipment/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;color: #fff"><i class=" fa lg mdi mdi-truck" style="margin-right: 5px;"></i></i> Update Shipment </a></td>
                    <?php }else{ ?>
                    <td><a target="" class="thumbnail gradient2 textalign" href="<?php echo site_url() ?>Transfer/stock_trasnfer/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;color: #fff"><i class=" fa lg mdi mdi-barcode-scan" style="margin-right: 5px;"></i></i> Scan </a></td>                        
                    <?php } ?>
                </tr>
                <?php } ?>    
            </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>