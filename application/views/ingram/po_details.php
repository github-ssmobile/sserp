<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Purchase Order Details </h3></center></div><div class="clearfix"></div>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 15px; margin: 0; overflow: auto; font-size: 15px;">
    <div class="col-md-6">
        <div class="col-md-3 text-muted">PO Date</div>
        <div class="col-md-9" style="margin-bottom:5px;"><?php echo date('d-m-Y',  strtotime($purchase_order->date)) ?></div>
        <div class="col-md-3 text-muted">PO ID</div>
        <div class="col-md-9" style="margin-bottom:5px;"><?php echo $purchase_order->financial_year.'-'.$purchase_order->id_vendor_po ?></div><div class="clearfix"></div>
        <div class="col-md-3 text-muted">Warehouse</div>
        <div class="col-md-9" style="margin-bottom:5px;"><?php echo $purchase_order->branch_name ?></div>
        <div class="col-md-3 text-muted">Ingram PO No.</div>
        <div class="col-md-9"><b><?php echo $purchase_order->ingram_order_number ?></b></div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div><br>
    <div class="thumbnail" style="padding: 0">
        <table id="branch_data" class="table table-condensed table-striped table-bordered" style="margin-bottom: 0">
            <thead style="background-color: #99ccff">
                <th class="col-md-1">Id</th>
                <th class="col-md-4">Product</th>                
                <th class="col-md-1">Ordered Qty</th>
                <th class="col-md-1">Confirmed Qty</th>                
            </thead>
            <tbody class="data_1">
                <?php foreach ($purchase_order_product as $product){ ?>
                <tr>
                    <td><?php echo $product->id_vendor_po_product ?></td>
                    <td><?php echo $product->full_name ?></td>
                    <td><?php echo $product->ordered_qty ?></td>
                    <td><?php echo $product->confirmed_qty ?></td>                    
                    
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div><hr>
    
    <center><b></b><p style="font-size: 22px; color: blue"><?php if($purchase_order->status == 1 && $purchase_order->ingram_order_status==1){ echo 'Order Placed'; }elseif($purchase_order->status == 1 && $purchase_order->ingram_order_status==2){ echo 'Inwared to APOB'; }elseif($purchase_order->status == 2 || $purchase_order->status == 3){ echo 'Order Rejected'; }?></p></b></center>
</div>
<?php include __DIR__ . '../../footer.php'; ?>