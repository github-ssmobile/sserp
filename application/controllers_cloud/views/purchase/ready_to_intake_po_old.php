<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Purchase Order</h3></center></div><div class="clearfix"></div><hr>
<div id="purchase" style="padding: 10px; margin: 0;">
    <div class="col-md-5">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-4">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div>
    <div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto; padding: 0">
        <table id="branch_data" class="table table-condensed table-full-width table-bordered table-hover">
            <thead class="bg-info">
                <th>Sr</th>
                <th>PO ID</th>
                <th>Date</th>
                <th>Warehouse</th>
                <th>Vendor</th>
                <th>Scan</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($purchase_order as $po){ if($po->status != 3){ ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td>
                        <?php echo $po->financial_year.'-'.$po->id_purchase_order ?>
                        <input type="hidden" name="id_po_order" value="<?php echo $po->id_purchase_order ?>" />
                    </td>
                    <td><?php echo $po->entry_time ?></td>
                    <td><?php echo $po->branch_name ?></td>
                    <td><?php echo $po->vendor_name ?></td>
                    <td><a href="<?php echo base_url('Purchase/purchase_inward/'.$po->id_purchase_order.'/'.$po->idvendor) ?>" class="btn btn-sm btn-link waves-effect waves-ripple"><i class="fa fa-barcode fa-lg"></i></a></td>
                </tr>
                <?php $i++; }} ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>