<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        
        
        $('#status, #from, #to').change(function(){
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();        
        $.ajax({
            url: "<?php echo base_url() ?>Ingram_Api/ajax_get_purchase_order_report",
            method: "POST",
            data:{status: status, from: from, to: to},
            success: function (data)
            {
                $('.purchase_order_report').html(data);
            }
        });
    });
        
    }); 
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-image-filter-tilt-shift fa-lg"></span> Purchase Order Report </h3></center></div>
<div class="clearfix"></div><hr>

<div class="col-md-3">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Status
                    </a>
                </div>
                <select class="chosen-select form-control input-sm" id="status">
                    <option value="">Select status</option>
                    <option value="1">Order Placed - Pending for inward</option>
                    <option value="4">Inwareded</option>                                                                                
                    <option value="2,3">Rejected</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="from" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date from">
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="to" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date to">
        </div>
        
        <div class="col-md-1">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('purchase_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div><div class="clearfix"></div><br>
<table class="table table-condensed table-bordered" id="purchase_data">
    <thead>
        <th>Sr</th>        
        <th>Date</th>
        <th>SS OrderNumber</th>
        <th>Ingram OrderNumber</th>
        <th>Product</th>
        <th>Ordered Qty</th>     
        <th>Quantity</th>                     
        <th>Status</th>       
        <th>Info</th>
    </thead>
    <tbody id="purchase_order_report" class="data_1 purchase_order_report">
                    <?php if(count($purchase_order)==0){?>
                <tr>
                    <td colspan="9" style="background: #ffffff;">                 
                        <center><img src="<?php echo base_url('assets/images/no-data-found.png') ?>" style="width: 50%" /></center>                    
                    </td>   
                        </tr>
                    <?php }else{ ?>
                    
                    <?php $i=1; foreach ($purchase_order as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->date ?></td>     
                        <td><?php echo $po->financial_year.'-'.$po->id_vendor_po ?></td>
                        <td><?php echo $po->ingram_order_number ?></td>                        
                        <td><?php echo $po->sku ?></td>
                        <td><?php echo $po->oqty?></td>
                        <td><?php echo $po->qty?></td>
                                                             
                        <td><?php 
                            if($po->status==1 && $po->ingram_order_status==1){
                                echo "Pending For Inward";
                            }elseif($po->status==1 && $po->ingram_order_status==2){
                                echo "Inwared";
                            }elseif($po->status==2 || $po->status==3){
                                echo "Rejected";
                            }
                        ?></td>
                        <td><a href="<?php echo base_url()?>Ingram_Api/po_details/<?php echo $po->id_vendor_po ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-info-circle"></i></a></td>
                    </tr>
                    <?php $i++; } ?>                   
                
                 <?php } ?>
                    </tbody>
</table>
<?php include __DIR__ . '../../footer.php'; ?>