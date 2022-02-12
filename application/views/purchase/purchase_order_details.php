<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Purchase Order Details </h3></center></div><div class="clearfix"></div>
<div class="thumbnail" style="padding: 15px; margin: 0; min-height: 800px; overflow: auto; font-size: 15px;">
    <div class="col-md-6">
        <div class="col-md-3 text-muted">PO Date</div>
        <div class="col-md-9"><?php echo date('d-m-Y h:i:s a',  strtotime($purchase_order->entry_time)) ?></div>
        <div class="col-md-3 text-muted">PO ID</div>
        <div class="col-md-9"><?php echo $purchase_order->financial_year.$purchase_order->id_purchase_order ?></div><div class="clearfix"></div>
        <div class="col-md-3 text-muted">Warehouse</div>
        <div class="col-md-9"><?php echo $purchase_order->branch_name ?></div><div class="clearfix"></div>
    </div>
    <div class="col-md-6">
        <div class="col-md-2 text-muted">Vendor</div>
        <div class="col-md-10"><?php echo $purchase_order->vendor_name ?></div>
        <div class="col-md-2 text-muted">Contact</div>
        <div class="col-md-10"><?php echo $purchase_order->vendor_contact ?></div><div class="clearfix"></div>
        <div class="col-md-2 text-muted">State</div>
        <div class="col-md-10"><?php echo $purchase_order->state ?></div><div class="clearfix"></div>
        <div class="col-md-2 text-muted">GSTIN</div>
        <div class="col-md-10"><?php echo $purchase_order->vendor_gst ?></div><div class="clearfix"></div>
    </div><div class="clearfix"></div><br>
    <div class="thumbnail" style="padding: 0">
        <table id="branch_data" class="table table-condensed table-striped table-bordered" style="margin-bottom: 0">
            <thead>
                <th class="col-md-1">Id</th>
                <th class="col-md-4">Product</th>
                <th class="col-md-1">Qty</th>
            </thead>
            <tbody class="data_1">
                <?php foreach ($purchase_order_product as $product){ ?>
                <tr>
                    <td><?php echo $product->id_variant ?></td>
                    <td><?php echo $product->full_name ?></td>
                    <td><?php echo $product->qty ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if($purchase_order->required_approval==1 && $purchase_order->status==0 && $_SESSION['idrole'] == 10){ ?>
    <form method="POST" action="<?php echo base_url('Purchase/approve_po') ?>">
        <input type="hidden" name="id_po_order" value="<?php echo $purchase_order->id_purchase_order ?>" />
        <button type="submit" value="2" name="status" class="btn btn-warning gradient1 waves-effect waves-light">Reject</button>
        <button type="submit" value="1" name="status" class="btn pull-right btn-primary gradient2 waves-effect waves-light">Approve</button>
    </form>
    <?php }else{ ?>
    <a class="btn btn-large btn-floating pull-right" style="position: fixed; bottom: 50px; right: 50px" href="<?php echo base_url('Purchase/purchase_order_details_print/'.$purchase_order->id_purchase_order) ?>"><i class="fa fa-print"></i></a>
    <?php } ?>
</div>
<?php include __DIR__ . '../../footer.php'; ?>