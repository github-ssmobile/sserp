<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#datefrom, #dateto').change(function(){
        
        var idbranch = $('#idbranch').val();        
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
                url:"<?php echo base_url() ?>Outward/ajax_store_inward_stock_report",
                method:"POST",
                data:{idbranch: idbranch,datefrom: datefrom, dateto: dateto},
                success:function(data)
                {
                    $(".inward_report").html(data);
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> <?php echo $title ;?></h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<div class="fixedelement"><br>    
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']; ?>">
</div>
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
<div class="col-md-2 col-sm-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('inward_report<?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Excel</button>
</div><div class="clearfix"></div><br>

<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover inward_report" id="inward_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Mandate </th> 
                <th>Received Date</th>   
                <th>Branch From</th>
                <th>Branch  To</th>
                <th>Godown</th>
                <th>Brand</th>
                <th>Model</th>
                <th>IMEI</th>
                <th>Qty</th>
                <th>Allocation Date</th>
                <th>Dispatch Date</th>
                <th>Outward Remark</th>
                <th>Dispatch Remark</th>
                <th>Received Remark</th>
                             
            </thead>
            <tbody>
                <?php foreach ($outward_data as $outward){ ?>
                <tr>
                    <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/0/<?php echo $outward->idoutward ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $outward->idoutward ?></b></a></td>
                    <td><?php echo $outward->shipment_received_date ?></td>     
                    <td><?php echo $outward->branch_from?></td>
                    <td><?php echo $outward->branch_to ?></td>
                    <td><?php echo $outward->godown_name ?></td>
                    <td><?php echo $outward->brand_name ?></td>
                    <td><?php echo $outward->full_name ?></td>
                    <td><?php echo $outward->imei_no ?></td>
                    <td><?php echo $outward->qty ?></td>
                    <td><?php echo $outward->date ?></td>
                    <td><?php echo $outward->dispatch_date ?></td>
                    <td><?php echo $outward->outward_remark ?></td>
                    <td><?php echo $outward->shipment_remark ?></td>
                    <td><?php echo $outward->shipment_received_remark ?></td>
                                   
                </tr>
                <?php } ?>
            </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>