<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#datefrom, #dateto,#idbranch_to,#date_filter').change(function(){
        
        var idbranch_to = $('#idbranch_to').val();        
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();      
        var date_filter = $('#date_filter').val();      
        if(dateto !== '' && datefrom === '' && date_filter==='')
        {
            alert('Select Date & Filter !!');
            return false;
        }else if(dateto === '' && datefrom !== ''  && date_filter===''){
            return false;
        }
        else{
            $.ajax({
                url:"<?php echo base_url() ?>Outward/ajax_w_outward_stock_report",
                method:"POST",
                data:{idbranch_to: idbranch_to,datefrom: datefrom, dateto: dateto,date_filter:date_filter},
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
<div class="fixedelement">
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']; ?>">
<div class="clearfix"></div><br>

<div class="col-md-3 col-sm-3">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm"  data-provide="datepicker"  placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" name="date_filter" id="date_filter">
            <option value="">Date Filter</option>            
            <option value="date">Outward Date</option>
            <option value="dispatch_date">Dispatch Date</option>

        </select>
    </div>

<div class="col-md-2 col-sm-3">
        <select class="chosen-select form-control input-sm" name="idbranch_to" id="idbranch_to">
            <option value="">All Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
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
                <th>Sr no</th>    
				<th>Mandate </th>    
                <th>Outward Date</th>
				<th>Dispatch Date</th>
                <th>Branch From</th>
                <th>Branch  To</th>
                <th>Godown</th>
                <th>Brand</th>
                <th>Model</th>
                <th>IMEI</th>
                <th>Qty</th>                               
                <th>Received Date</th>                
                <th>Dispatch type</th>                
                <th>Courier name</th>                
                <th>PO/LR no</th>                
                <th>No of Box</th>       
                <th>Status</th>
            </thead>
            <tbody>
                <?php $i=1; foreach ($outward_data as $outward){ ?>
                <tr>
					<td><?php echo $i; ?></td>
                    <td><a target="" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/0/<?php echo $outward->idoutward ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $outward->idoutward ?></b></a></td>
                    <td><?php echo $outward->date ?></td>     
					<td><?php echo $outward->dispatch_date ?></td>                                   
					<td><?php echo $outward->branch_from?></td>
                    <td><?php echo $outward->branch_to ?></td>
                    <td><?php echo $outward->godown_name ?></td>
                    <td><?php echo $outward->brand_name ?></td>
                    <td><?php echo $outward->full_name ?></td>
                    <td><?php echo $outward->imei_no ?></td>
                    <td><?php echo $outward->qty ?></td>                    
                    <td><?php echo $outward->shipment_received_date ?></td>                    
                    <td><?php echo $outward->dispatch_type ?></td>                    
                    <td><?php echo $outward->courier_name ?></td>                    
                    <td><?php echo $outward->po_lr_no ?></td>                    
                    <td><?php echo $outward->no_of_boxes ?></td>  
                    <td><?php if($outward->out_status==0){echo 'Scanned';}elseif($outward->out_status==1){ echo 'Dispatched';}elseif($outward->out_status==2){ echo 'Received';} ?></td>  
                </tr>
                <?php  $i++; } ?>
            </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>