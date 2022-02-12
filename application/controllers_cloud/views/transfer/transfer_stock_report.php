<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#datefrom, #dateto,#idbranch_other,#idbranch').change(function(){
        
        var idbranch = $('#idbranch').val();  
        var idbranch_other = $('#idbranch_other').val();  
        
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();                
        if(dateto !== '' && datefrom === '')
        {
            alert('Select Date !!');
            return false;
        }else if(dateto === '' && datefrom !== ''){
            return false;
        }
        else{
            $.ajax({
                url:"<?php echo base_url() ?>Transfer/ajax_store_stock_transfer_report",
                method:"POST",
                data:{idbranch: idbranch,datefrom: datefrom, dateto: dateto,idbranch_other:idbranch_other},
                success:function(data)
                {
                    $(".inward_report").html(data);
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-repeat fa-lg"></span> <?php echo $title ;?></h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<div class="fixedelement">
    <div class="clearfix"></div><br>
  

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
     <?php if(count($branch_data_to)>1){ ?>
         <div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch">
            <option value="">Select Branch To</option>
            <?php foreach ($branch_data_to as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <?php }else{ ?>
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']; ?>">
        <?php } ?>
    
 <div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" name="idbranch_other" id="idbranch_other">
            <option value="">Select Branch From</option>
            <?php foreach ($branch_data_from as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>
<div class="col-md-2 col-sm-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('inward_report<?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Excel</button>
</div><div class="clearfix"></div><br>
</div>
<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover inward_report" id="inward_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Mandate </th>                
                <th>Branch From</th>
                <th>Branch  To</th>
                <th>Godown</th>
                <th>Brand</th>
                <th>Model</th>
                <th>IMEI</th>
                <th>Qty</th>
                <th>Request Date</th>
                <th>Dispatch Date</th>
                <th>Received Date</th>                
            </thead>
            <tbody>
                 <?php foreach ($transfer_data as $transfer){ 
                    $string_imei=rtrim($transfer->imei_no,',');                     
                    $imei_array=explode(',', $string_imei);                     
                    foreach ($imei_array as $imei){ 
                    ?>
                <tr>
                    <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $transfer->idtransfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $transfer->idtransfer ?></b></a></td>   
                    <td><?php echo $transfer->branch_from?></td>
                    <td><?php echo $transfer->branch_to ?></td>
                    <td><?php echo $transfer->godown_name ?></td>
                    <td><?php echo $transfer->brand_name ?></td>
                    <td><?php echo $transfer->full_name ?></td>
                    <td><?php echo $imei ?></td>
                    <td><?php echo $transfer->qty ?></td>
                    <td><?php echo $transfer->date ?></td>
                    <td><?php echo $transfer->dispatch_date ?></td>
                    <td><?php echo $transfer->shipment_received_date ?></td>                    
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>    
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>