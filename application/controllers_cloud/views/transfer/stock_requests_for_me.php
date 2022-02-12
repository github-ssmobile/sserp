<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#status, #idbranch_from, #datefrom, #dateto').change(function(){
        var status = $('#status').val();
        var idbranch = $('#idbranch').val();
        var idbranch_from = $('#idbranch_from').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        if(status === ''){
            alert('Select Status !!');
            return false;
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Transfer/ajax_get_for_me_stock_request_bystatus",
                method:"POST",
                data:{status : status, idbranch: idbranch,idbranch_from:idbranch_from, datefrom: datefrom, dateto: dateto},
                success:function(data)
                {
                    $(".reports").html(data);
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Stock Request Report</h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<div class="fixedelement"><br>    
<div class="col-md-1 col-sm-2"><i class="fa fa-filter fa-lg"></i> Status</div>
<div class="col-md-2 col-sm-3">
    <select class="chosen-select form-control input-sm" id="status" name="status">
        <option value="">Select Status</option>
        <option value="0">Pending</option>
        <option value="1">Approved</option>
        <option value="3">Ready To Dispatch</option>
        <option value="4">In Transit</option>
        <option value="5">Received</option>
        <option value="2">Rejected</option>
    </select>
</div>

    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $idbranch ?>">

    <div class="col-md-1 col-sm-2"><i class="fa fa-sitemap fa-lg"></i> Branch</div>
    <div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" name="idbranch_from" id="idbranch_from">
            <option value="">Select Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>

<div class="col-md-1 col-sm-2"><i class="fa fa-calendar fa-lg"></i> Date</div>
<div class="col-md-3 col-sm-3">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm datepick" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm datepick" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-2 col-sm-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('stock_reports<?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Excel</button>
</div><div class="clearfix"></div><br>
</div>
<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover reports" id="my_stock_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>DC</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Request From</th>
                <th>Total Product</th>
                <th>Branch Remark</th>
                <th>My Remark</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php foreach ($transfer_data as $transfer){ ?>
                <tr>
                    <td><b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></td>
                    <td><?php echo $transfer->date ?></td>
                    <td><?php echo $transfer->branch_from  ?></td>
                    <td><?php echo $transfer->branch_to ?></td>
                    <td><?php echo $transfer->total_product ?></td>
                    <td><?php echo $transfer->transfer_remark ?></td>
                    <td><?php echo $transfer->approved_remark ?></td>
                    <td><a target="" class="thumbnail gradient2 textalign" href="<?php echo site_url() ?>Transfer/update_stock_request/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;color: #fff"><i class="" style="margin-right: 5px;"></i></i> Approve </a></td>
                </tr>
                <?php } ?>
            </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>