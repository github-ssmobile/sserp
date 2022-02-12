<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Ingram Purchase Order</center></div>
<div class="clearfix"></div><hr>
<script>
$(document).ready(function () {
    $('#status, #from, #to').change(function(){
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        $.ajax({
            url: "<?php echo base_url() ?>Ingram_Api/ajax_get_purchase_order_data",
            method: "POST",
            data:{status: status, from: from, to: to},
            success: function (data)
            {
                $('#po_report').html(data);
            }
        });
    });
    
});
</script>
<div class="" style="padding: 0; margin: 0;overflow: auto">
    <div id="purchase" style="padding: 10px; margin: 0">
        <div class="clearfix"></div>
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px">
                <thead>
                    <th>Sr</th>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Product</th>                    
                    <th>Qty</th>
                    <th>Action</th>
                </thead>
                <tbody id="po_report" class="data_1">
                    <?php $i=1; foreach ($purchase_order as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->id_sale_token ?></td>
                        <td><?php echo $po->date ?></td>
                        <td><?php echo $po->branch_name ?></td>
                        <td><?php echo $po->full_name." - ".$po->sku ?></td>                        
                        <td><?php echo $po->qty ?></td>                        
                        <td><?php echo 'Pending'; ?></td>
                        <td><center><a target="_blank" href="<?php echo base_url('Ingram_Api/process_order/'.$po->id_sale_token) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>