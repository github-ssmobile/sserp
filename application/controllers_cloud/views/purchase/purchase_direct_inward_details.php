<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Purchase Order Details </h3></center></div><div class="clearfix"></div>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 15px; margin: 0; min-height: 800px; overflow: auto; font-size: 15px;">
    <div class="col-md-6">
        <div class="col-md-3 text-muted">PO Date</div>
        <div class="col-md-9"><?php echo date('d-m-Y',  strtotime($purchase_order->date)) ?></div>
        <div class="col-md-3 text-muted">PO ID</div>
        <div class="col-md-9"><?php echo $purchase_order->financial_year.'-'.$purchase_order->id_purchase_direct_inward ?></div><div class="clearfix"></div>
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
                <!--<th class="col-md-1">Godown</th>-->
                <th class="col-md-1">Qty</th>
            </thead>
            <tbody class="data_1">
                <?php foreach ($purchase_order_product as $product){ ?>
                <tr>
                    <td><?php echo $product->id_variant ?></td>
                    <td><?php echo $product->full_name ?></td>
                    <!--<td><?php // echo $product->godown_name ?></td>-->
                    <td><?php echo $product->qty ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div><hr>
    <?php if($_SESSION['idrole'] == 10 && $purchase_order->status == 0 ){ // purchase manager ?> 
    <form>
        <input type="hidden" name="id_purchase_direct_inward" value="<?php echo $purchase_order->id_purchase_direct_inward ?>" />
        <button type="submit" formaction="<?php echo base_url('Purchase/approve_direct_inward_bymanager') ?>" formmethod="POST" name="status" value="1" class="btn btn-info gradient2 pull-right">Approve</button>
        <button type="submit" formaction="<?php echo base_url('Purchase/approve_direct_inward_bymanager') ?>" formmethod="POST" name="status" value="2" class="btn btn-info gradient1 pull-left">Reject</button>
    </form>
    <?php } ?>
    <center><p style="font-size: 22px"><?php if($purchase_order->status == 0){ echo 'Pending'; }elseif($purchase_order->status == 1){ echo 'Approved'; }elseif($purchase_order->status == 2){ echo 'Rejected'; }elseif($purchase_order->status == 3){ echo 'Inwarded'; }?></p></center>
</div>
<?php include __DIR__ . '../../footer.php'; ?>