<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Purchase Order</center></div>
<div class="col-md-1"><a target="_blank" href="<?php echo base_url('Purchase/create_purchase_order') ?>" class="btn btn-large btn-info btn-floating waves-effect"><i class="fa fa-plus fa-lg"></i></a></div><div class="clearfix"></div><hr>
<script>
$(document).ready(function () {
    $('#status, #from, #to').change(function(){
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        $.ajax({
            url: "<?php echo base_url() ?>Purchase/ajax_get_purchase_order_data",
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
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Status
                    </a>
                </div>
                <select class="form-control input-sm" id="status">
                    <option value="">Select status</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Rejected</option>
                    <option value="3">Inwarded</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="from" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date from">
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="to" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date to">
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div><div class="clearfix"></div>
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0; font-size: 13px">
                <thead>
                    <th>Sr</th>
                    <th>PO ID</th>
                    <th>Date</th>
                    <th>Warehouse</th>
                    <th>Vendor</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody id="po_report" class="data_1">
                    <?php $i=1; foreach ($purchase_order as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->financial_year.'-'.$po->id_purchase_order ?></td>
                        <td><?php echo $po->date ?></td>
                        <td><?php echo $po->branch_name ?></td>
                        <td><?php echo $po->vendor_name ?></td>
                        <td><?php echo $po->vendor_address ?></td>
                        <td><?php echo 'Pending'; ?></td>
                        <td><center><a target="_blank" href="<?php echo base_url('Purchase/purchase_order_details/'.$po->id_purchase_order) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>