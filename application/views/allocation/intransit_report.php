<?php include __DIR__.'../../header.php'; if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
$(document).ready(function(){
    $('#status, #idbranch, #datefrom, #dateto').change(function(){
        var status = $('#status').val();
        var idbranch = $('#idbranch').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
            
            if(datefrom != '' && dateto == '' || datefrom == '' && dateto != ''){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock_allocation/ajax_get_stock_allocation_by_status",
                method:"POST",
                data:{status : status, idbranch: idbranch, datefrom: datefrom, dateto: dateto},
                success:function(data)
                {
                    $("#stock_allocation_data").html(data);
                }
            });
        
    });
});
</script>
<center>
    <h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Warehouse Shipment Report</h3>
</center><hr>
<div class="col-md-2">
    <select class="chosen-select form-control input-sm" id="status" name="status">
        <option value="">Select Status</option>        
        <option value="4">In transit</option>
        <option value="5">Received</option>
    </select>
</div>
    <div class="col-md-2">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch">
            <option value="">Select Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>

<div class="col-md-3">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm datepick" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm datepick" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-2 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('allocation_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div><div class="clearfix"></div>
<br>
<div class="thumbnail" id="stock_allocation_data">
    <table id="allocation_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
        <thead class="fixedelement" style="text-align: center;position: none !important;">                        
        <th>Mandate Number</th>
        <th>Branch Name</th>
        <th>Date</th>        
        <th>Products</th>
        <th>Total Quantity</th>
        <th>Allocation Type</th>                          
        <th>Receive Remark</th>
        <th>Info</th>
        <th>Print DC</th>
        </thead>   
            <tbody class="data_1">
            <?php $i = 1;
            foreach ($stock_allocation as $data) {                                         
                    ?>                
                    <tr>  
                        <td class="textalign" style="color: #0e10aa !important;"><?php echo $data->id_stock_allocation; ?> </td>
                        <td><?php echo $data->branch_name; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($data->date)) ?></td>
                        <td><?php echo $data->sum_product ?></td>
                        <td><?php echo $data->sum_qty ?></td>
                        <?php  $allocation_type=''; if($data->allocation_type == 0){ $allocation_type='Branch'; }else if($data->allocation_type == 1){ $allocation_type='Model'; }else{ $allocation_type='Route'; } ?>
                        <td><?php echo $allocation_type; ?></td>     
                        <td><?php echo $data->shipment_received_remark ?></td>
                        <td>
                            <a target="" class="thumbnail textalign" href="<?php echo base_url('Stock_allocation/stock_allocation_details/'.$data->id_stock_allocation) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-info " style="color: blue"></i>
                            
                        </td>
                        <td>
                            <a target="" class="thumbnail textalign" href="<?php echo base_url('Outward/outward_dc/'.$data->id_stock_allocation) ?>/0" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-print " style="color: blue"></i>
                            
                        </td>
                    </tr>
                <?php $i++;
            } ?>
            </tbody>  
            </table> 
</div>
<?php } include __DIR__.'../../footer.php'; ?>