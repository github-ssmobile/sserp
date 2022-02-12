<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#datefrom, #dateto, #idcompany').change(function(){
        
        var idcompany = $('#idcompany').val();        
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
                url:"<?php echo base_url() ?>Report/ajax_inter_state_purchase",
                method:"POST",
                data:{idcompany: idcompany,datefrom: datefrom, dateto: dateto},
                success:function(data)
                {
                    $(".interState_report").html(data);
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="	mdi mdi-login fa-lg"></span> <?php echo $title ;?></h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<div class="fixedelement"><br>    
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
<div class="col-md-2  col-xs-6 col-sm-4">
    <select class="chosen-select form-control input-sm" name="idcompany" id="idcompany">
        <option value="">Select Company</option>
        <?php foreach ($comapny_data as $company) { ?>
                <option value="<?php echo $company->company_id; ?>"><?php echo $company->company_name; ?></option>
        <?php } ?>
    </select>        
</div>
<div class="col-md-2 col-sm-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('interState_report<?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Excel</button>
</div><div class="clearfix"></div><br>
</div>
<div class="thumbnail" style="overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed table-hover interState_report" id="interState_report<?php echo date('d-m-Y h:i a') ?>" style="font-size: 13px">
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>Mandate </th> 
                <th>Date</th>
                <th>Vendor Invoice No</th>
                <th>Invoice No</th>
                <th>Seller Company</th>
                <th>Seller GST No</th>
                <th>Buyer Company</th>
                <th>Godown</th>
                <th>Brand</th>
                <th>Model</th>
                <th>HSN</th>
                <th>IMEI</th>
                <th>Base Price</th>
                <th>Qty</th>
                <th>Taxable</th>
                <th>IGST Rate (%)</th>	                
                <th>IGST</th>	                
                <th>Total Amount</th>
                <th>Branch From</th>
                <th>Branch  To</th>
            </thead>
            <tbody>
                <?php foreach ($inter_state_data as $data){ ?>
                <tr>
                    <?php if($data->transaction_type=='Transfer'){ ?>                        
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $data->idoutward_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'T'.$data->idoutward_transfer ?></b></a></td>
                    <?php }else{ ?>
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/<?php echo $data->idoutward_transfer ?>/0" style="margin: 0 8px;padding: 5px !important;width: 60%;"><b style="color: #0e10aa !important;"><?php echo 'O'.$data->idoutward_transfer ?></b></a></td>                        
                    <?php } ?>
                    <td><?php echo $data->date ?></td>     
                    <td><?php echo $data->sales_invoice?></td>
                    <td><?php echo $data->purchase_invoice?></td>
                    <td><?php echo $data->company_from ?></td>
                    <td><?php echo $data->gst_no_from ?></td>
                    <td><?php echo $data->company_to ?></td>
                    <td><?php echo $data->godown_name ?></td>
                    <td><?php echo $data->brand_name ?></td>
                    <td><?php echo $data->full_name ?></td>
                    <td><?php echo $data->hsn ?></td>
                    <td><?php echo $data->imei_no ?></td>
                    <?php
                        $total_amount = $data->price*($data->qty);
                        $cal = ($data->igst_per + 100) / 100;
                        $taxable = $total_amount / $cal;
                        $igstamt = $total_amount - $taxable;
                        $rate = $taxable / $data->qty;
                    ?>
                    <td><?php echo round($rate,2) ?></td>
                    <td><?php echo $data->qty ?></td>
                    <td><?php echo round($taxable, 2) ?></td>
                    <td><?php echo $data->igst_per ?></td>
                    <td><?php echo round($igstamt, 2) ?></td>
                    <td><?php echo $total_amount ?></td>
                    <td><?php echo $data->branch_from?></td>
                    <td><?php echo $data->branch_to ?></td>
                                   
                </tr>
                <?php } ?>
            </tbody> 
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>