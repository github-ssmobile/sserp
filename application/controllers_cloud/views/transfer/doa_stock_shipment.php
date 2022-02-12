<?php include __DIR__ . '../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Branch Stock Shipment</h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<div class="fixedelement"><br>    


<div class="col-md-2 col-sm-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('transfer<?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Excel</button>
</div><div class="clearfix"></div><br>
</div>
<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover my_stock_report" id="my_stock_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>DC</th>
                <th>Date</th>
                <th>Branch From</th>
                <th>Total Product</th>
                <th>Remark</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($transfer_data as $transfer){ ?>
                <tr>
                    <td><b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></td>
                    <td><?php echo $transfer->date ?></td>                    
                    <td><?php echo $transfer->branch_from ?></td>
                    <td><?php echo $transfer->total_product ?></td>
                    <td><?php echo $transfer->transfer_remark ?></td>
                    <td class="textalign">
                        <center>
                            <?php if($transfer->status==4){ ?>
                                <a target="_blank" class="thumbnail gradient2 textalign" href="<?php echo base_url('Transfer/receive_doa_b2b_shipment/'.$transfer->id_transfer) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%; color: #fff"><i class="" style="margin-right: 5px;"></i>   Receive  </a>                            
                            <?php }else { ?>
                                <a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><i class="fa fa-info " style="color: blue"></i></a>
                                 <?php } ?>                   
                        </center>
                    <td>
                </tr>
                <?php } ?>
            </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>