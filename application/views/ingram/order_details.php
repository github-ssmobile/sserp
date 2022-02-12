<?php include __DIR__.'../../header.php'; ?>

<?php foreach ($sale_data as $sale){ ?>
<div style="font-family: K2D; font-size: 15px;">   
    <div class="panel panel-info"> <br>
        <div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-cart-outline fa-lg"></span> Ingram Order Details </h3></center></div><div class="clearfix"></div>
      
        <div class="panel-body" style="min-height: 600px">
            <form id="sale_form_submit">
            <div class="col-md-4 pull-right">
                <span class="text-muted">Branch:</span><b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->branch_name ?></b>
            </div><div class="clearfix"></div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Booking token Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_sale_token ?>
            </div><div class="clearfix"></div>            
            <div class="col-md-4 pull-right">
                <span class="text-muted">PO No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->token_uid ?>                
                    <input type="hidden" name="po_number" id="po_number" value="<?php echo $sale->token_uid ?>"/>
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $sale->idbranch ?>"/>
                    <input type="hidden" name="id_sale_token" id="id_sale_token" value="<?php echo $sale->id_sale_token ?>"/>                    
                
            </div><div class="clearfix"></div>To,
            <div class="col-md-4 pull-right">
                <span class="text-muted">Booking Date:</span> &nbsp; <?php echo date('d/m/Y', strtotime($sale->date)); ?>
            </div><div class="clearfix"></div>            
            <div class="col-md-6">
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_fname.' '.$sale->customer_lname ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Address</span>: <?php echo $sale->customer_address ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_contact ?>
            </div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Deliver At</span>: <?php if($sale->deliver_at==0){echo "Branch Address"; }else{ echo "Customer Address"; } ?>
            </div>
            <div class="clearfix"></div>
            <?php if($sale->customer_gst !=''){ ?>
            <div class="col-md-6">
                <span class="text-muted">GST</span>: <?php echo $sale->customer_gst ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-6">
                <span class="text-muted">Sales Promoter</span>: <?php echo $sale->user_name; ?>
            </div><div class="clearfix"></div>
            <div class="clearfix"></div><br>
                   
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
                <thead class="bg-info">
                        <th><center>SN</center></th>
                        <th class="col-md-5 col-xs-5"><center>Product</center></th>
                        <th>HSN</th>
                        <th><center>Qty</center></th>
                        <th><center>MOP</center></th>
                        <th><center>Price</center></th>
                        <th><center>Basic</center></th>
                        <th><center>Discount</center></th>
                        <th><center>Taxable</center></th>
                        <?php $gst_type = $sale->gst_type; if ($gst_type == 1) { ?>
                            <th><center>IGST</center></th>
                        <?php } else { ?>
                            <th><center>CGST</center></th>
                            <th><center>SGST</center></th>
                        <?php } ?>
                        <th><center>Amount</center></th>
                        </thead>
                        <tbody>
                        <?php $i = 1;
                            $tqty = 0;
                            $trate = 0;
                            $tdiscount = 0;
                            $tcgst = 0;
                            $tigst = 0;
                            $ttaxable = 0;
                            $ttotal_amount = 0;
                             if ($gst_type == 1) { // igst
                                foreach ($sale_product as $product) {
                                        $total_amount=$product->total_amount;
                                    $ttotal_amount += $total_amount;
                                    $cal = ($product->igst_per + 100) / 100;
                                    $taxable = $total_amount / $cal;
                                    $igstamt = $total_amount - $taxable;
                                    $tigst += $igstamt;
                                    $tqty += $product->qty;
                                    $tdiscount += $product->discount_amt;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / $product->qty;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name;  ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo $product->mop ?></td>
                                        <td><?php echo $product->price ?></td>
                                        <td><?php echo $product->basic ?></td>
                                        <td><?php echo $product->discount_amt ?></td>
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <td><?php echo number_format($igstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->igst_per . '%)</span>' ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                    <?php  } ?>
                                    <tr class="bg-info">
                                        <td colspan="7"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                        <td><?php echo $tdiscount ?></td>
                                        <td><?php echo number_format($ttaxable, 2) ?></td>
                                        <td><?php echo number_format($tigst, 2) ?></td>
                                        <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                    </tr>
                                    <?php // cgst
                                        } else {
                                        foreach ($sale_product as $product) {
                                            $total_amount=$product->total_amount;
                                            $ttotal_amount += $total_amount;
                                            $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                            $taxable = $total_amount / $cal;
                                            $cgst = $total_amount - $taxable;
                                            $cgstamt = $cgst / 2;
                                            $tcgst += $cgstamt;
                                            $tqty += $product->qty;
                                            $ttaxable += $taxable;
                                            $rate = $taxable / $product->qty;
                                            $trate += $rate;
                                        ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo $product->mop ?></td>
                                        <td><?php echo $product->price ?></td>
                                        <td><?php echo $product->basic  ?></td>
                                        <td><?php echo $product->discount_amt ?></td>
                                        <td><?php echo number_format($taxable, 2) ?></td>                                       
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->cgst_per . '%)</span>' ?></td>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->sgst_per . '%)</span>' ?></td>                                       
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="bg-info">
                                    <td colspan="7"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                    <td><?php echo $sale->discount_total ?></td>
                                    <td><?php echo number_format($ttaxable, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                </tr>
                            <?php } ?>                                 
                        </tbody>
                    </table>
            <br>
            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Details</span>
            <div class="thumbnail" style="overflow: auto; padding: 2px">
                <table class="table table-condensed table-bordered table-hover" style="font-size: 14px; margin-bottom: 0">
                    <tbody>
                        <?php if($sale->idcustomer == 1){ ?>
                            <?php $sum = 0; foreach ($sale_payment as $payment){ ?>
                            <tr>
                                <td><?php echo $payment->inv_no ?></td>
                                <td><span class="text-muted">Mode of Payment</span></td>
                                <td><?php echo $payment->payment_mode.' <small>'.$payment->payment_head.'</small>' ?></td>
                                <!--<td class="text-muted">Amount</td>-->
                                <td><?php echo $payment->amount ?></td>

                                
                                <?php if($payment->tranxid_type != NULL){ ?>
                                <td><span class="text-muted"><?php echo $payment->tranxid_type ?></span></td>
                                <td><?php echo $payment->transaction_id ?></td>
                                <?php } foreach ($payment_head_has_attributes as $has_attributes){
                                    if($has_attributes->idpayment_head == $payment->idpayment_head){ ?>
                                        <td><span class="text-muted"><?php if($payment->idpayment_mode == 17){ echo 'Bank UTR'; }else{ echo $has_attributes->attribute_name; }?></span></td>
                                        <td><?php $clm = $has_attributes->column_name; echo $payment->$clm; ?></td>
                                <?php }} ?>
                            </tr>
                            <?php $sum += $payment->amount; } ?>
                            <tr style="font-weight: bold">
                                <td></td>
                                <td>Total</td>
                                <td><?php echo $sum ?></td>
                            </tr>
                        <?php }else{ ?>
                        <?php $sum = 0; foreach ($sale_payment as $payment){ ?>
                        <tr>
                            <td><span class="text-muted">Mode of Payment</span></td>
                            <td><?php echo $payment->payment_mode.' <small>'.$payment->payment_head.'</small>' ?></td>
                            <!--<td class="text-muted">Amount</td>-->
                            <td><?php echo $payment->amount ?></td>
                            
                            
                            <?php if($payment->tranxid_type != NULL){ ?>
                            <td><span class="text-muted"><?php echo $payment->tranxid_type ?></span></td>
                            <td><?php echo $payment->transaction_id ?></td>
                            <?php } foreach ($payment_head_has_attributes as $has_attributes){
                                if($has_attributes->idpayment_head == $payment->idpayment_head){ ?>
                                    <td><span class="text-muted"><?php if($payment->idpayment_mode == 17){ echo 'Bank UTR'; }else{ echo $has_attributes->attribute_name; }?></span></td>
                                    <td><?php $clm = $has_attributes->column_name; echo $payment->$clm; ?></td>
                            <?php }} ?>
                        </tr>
                        <?php $sum += $payment->amount; } ?>
                        <tr style="font-weight: bold">
                            <td></td>
                            <td>Total</td>
                            <td><?php echo $sum ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
           <div class="clearfix"></div>
           
           <div class="col-md-2 pull-right"></div>
           <div class="clearfix"></div>
        </form>
           
            <h4 style="font-family: Kurale;color: #005bc0"><i class="fa fa-sign-in"></i> Received Payment</h4>
            <div class="thumbnail" style="overflow: auto;padding: 2px">
                <table class="table table-bordered table-condensed table-hover" style="font-size: 14px; margin-bottom: 0">
                    <thead>
                        <th>Mode</th>
                        <th>Amount</th>
                        <th>Txn No</th>
                        <th>Reconciliation</th>
                        <th>Pending</th>
                        <th>Bank</th>
                        <th>UTR</th>
                        <th>Received Date</th>
                    </thead>
                    <tbody>
                        <?php if($sale->idcustomer == 1){ ?>
                        <?php $total_remain=0; $total_received=0; $total_amt = 0;
                            foreach ($sale_reconciliation as $recon){ $remain=0;
                            $remain = $recon->amount - $recon->received_amount;
                            $total_remain += $remain; 
                            $total_amt += $recon->amount;
                            $total_received += $recon->received_amount; ?>
                        <tr>
                            <td><?php echo $recon->payment_mode ?></td>
                            <td><?php echo $recon->amount ?></td>
                            <td><?php echo $recon->transaction_id ?></td>
                            <td><?php echo $recon->received_amount ?></td>
                            <td><?php echo $remain ?></td>
                            <td><?php echo $recon->bank_name ?></td>
                            <td><?php echo $recon->utr_no ?></td>
                            <td><?php if($recon->received_amount > 0){ echo date('d/m/Y H:i:s', strtotime($recon->received_entry_time)); } ?></td>
                            <td><?php echo $recon->inv_no ?></td>
                        </tr>
                        <?php } ?>
                        <?php }else{ ?>
                        <?php $total_remain=0; $total_received=0; $total_amt = 0;
                            foreach ($sale_reconciliation as $recon){ $remain=0;
                            $remain = $recon->amount - $recon->received_amount;
                            $total_remain += $remain; 
                            $total_amt += $recon->amount;
                            $total_received += $recon->received_amount; ?>
                        <tr>
                            <td><?php echo $recon->payment_mode ?></td>
                            <td><?php echo $recon->amount ?></td>
                            <td><?php echo $recon->transaction_id ?></td>
                            <td><?php echo $recon->received_amount ?></td>
                            <td><?php echo $remain ?></td>
                            <td><?php echo $recon->bank_name ?></td>
                            <td><?php echo $recon->utr_no ?></td>
                            <td><?php if($recon->received_amount > 0){ echo date('d/m/Y H:i:s', strtotime($recon->received_entry_time)); } ?></td>
                        </tr>
                        <?php }} ?>
                    </tbody>
                    <thead>
                        <th>Total</th>
                        <th><?php echo $total_amt ?></th>
                        <th></th>
                        <th><?php echo $total_received ?></th>
                        <th><?php echo $total_remain ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                </table>
            </div>
            <div class="col-md-4 " style="border-left: 5px solid #fff; padding: 5px">
                <div class="thumbnail" style="padding: 5px;">
                    Total Amount<br>
                    <?php  echo $sum; ?>
                </div>
            </div>
            <div class="col-md-4 " style="border-left: 5px solid #fff; padding: 5px">
                <div class="thumbnail" style="padding: 5px;">
                    Reconciled Amount<br>
                    <?php echo $total_received; ?>
                </div>
            </div>
            <div class="col-md-4 " style="border-left: 5px solid #fff; padding: 5px">
                <div class="thumbnail" style="padding: 5px;">
                    Due Amount<br>
                    <?php echo $sum - $total_received; ?>
                </div>
            </div><div class="clearfix"></div>
           
            <div id="app" class="thumbnail">
                    <div class="swiper-container">
                        <p class="swiper-control">
                            <button type="button" class="btn btn-default btn-sm prev-slide">Prev</button>
                            <button type="button" class="btn btn-default btn-sm next-slide">Next</button>
                        </p>
                        <div class="swiper-wrapper timeline">
                            <?php if ($sale_data[0]->date != NULL) { ?>
                                <div class="swiper-slide">
                                    <div class="timestamp">
                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->date)) ?><span>
                                                </div>
                                                <div class="status">
                                                    <span>
                                                        <?php echo "Order Created" ?>                           
                                                    </span>
                                                </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($sale_data[0]->approved_date != NULL) { ?>
                                                <div class="swiper-slide">
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->approved_date)) ?><span>
                                                                </div>
                                                                <div class="status">
                                                                    <span>
                                                                        <?php echo "Order Approved and Placed at Ingram" ?>                           
                                                                    </span>
                                                                </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($sale_data[0]->picked_date != NULL) { ?>
                                                                <div class="swiper-slide">
                                                                    <div class="timestamp">
                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->picked_date)) ?><span>
                                                                                </div>
                                                                                <div class="status">
                                                                                    <span>
                                                                                        <?php echo "Picked and Verify" ?>                           
                                                                                    </span>
                                                                                </div>
                                                                                </div>
                                                                            <?php } ?>
                                                                            <?php if ($sale_data[0]->packed_date != NULL) { ?>
                                                                                <div class="swiper-slide">
                                                                                    <div class="timestamp">
                                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->packed_date)) ?><span>
                                                                                                </div>
                                                                                                <div class="status">
                                                                                                    <span>
                                                                                                        <?php echo "Shipment and Dispatched" ?>                           
                                                                                                    </span>
                                                                                                </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                            <?php if ($sale_data[0]->return_date != NULL) { ?>
                                                                                                <div class="swiper-slide">
                                                                                                    <div class="timestamp">
                                                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->return_date)) ?><span>
                                                                                                                </div>
                                                                                                                <div class="status">
                                                                                                                    <span>
                                                                                                                        <?php echo "Return by customer - " . $sale_data[0]->reject_reason ?>                           
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                                </div>
                                                                                                            <?php } ?>
                                                                                                            <?php if ($sale_data[0]->reject_date != NULL) { ?>
                                                                                                                <div class="swiper-slide">
                                                                                                                    <div class="timestamp">
                                                                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->reject_date)) ?><span>
                                                                                                                                </div>
                                                                                                                                <div class="status">
                                                                                                                                    <span>
                                                                                                                                        <?php echo "Order Rejected"; ?>                           
                                                                                                                                    </span>
                                                                                                                                </div>
                                                                                                                                </div>
                                                                                                                            <?php } ?>
                                                                                                                            <?php if ($sale_data[0]->received_date != NULL) { ?>
                                                                                                                                <div class="swiper-slide">
                                                                                                                                    <div class="timestamp">
                                                                                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->received_date)) ?><span>
                                                                                                                                                </div>
                                                                                                                                                <div class="status">
                                                                                                                                                    <span>
                                                                                                                                                        <?php echo "Mark as Received by Branch/Customer by Ingram"; ?>                           
                                                                                                                                                    </span>
                                                                                                                                                </div>
                                                                                                                                                </div>
                                                                                                                                            <?php } ?>
                                                                                                                                            <?php if ($sale_data[0]->received_date_branch != NULL) { ?>
                                                                                                                                                <div class="swiper-slide">
                                                                                                                                                    <div class="timestamp">
                                                                                                                                                        <span class="date"><?php echo date('d-m-Y', strtotime($sale_data[0]->received_date_branch)) ?><span>
                                                                                                                                                                </div>
                                                                                                                                                                <div class="status">
                                                                                                                                                                    <span>
                                                                                                                                                                        <?php echo "Received by Branch" . " - " . $sale_data[0]->receive_branch_remark; ?>                           
                                                                                                                                                                    </span>
                                                                                                                                                                </div>
                                                                                                                                                                </div>
                                                                                                                                                            <?php } ?>

                                                                                                                                                            </div>
                                                                                                                                                            <div class="swiper-pagination"></div>
                                                                                                                                                            </div>
                                                                                                                                                            </div>
            
        </div>
    </div>
</div>
<style>
.timeline {
  font-family: 'K2D';
  margin: 20px 0;
  list-style-type: none;
  display: flex;
  padding: 0;
  text-align: center;
}
.timeline li {
  transition: all 200ms ease-in;
}
.timestamp {
  width: 100%;
  margin-bottom: 20px;
  padding: 0px 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  font-weight: 100;
}
.status {
  padding: 0px 5px;
  display: flex;
  justify-content: center;
  border-top: 4px solid #c30e14;
  position: relative;
  transition: all 200ms ease-in;
}
.status span {
  padding-top: 20px;
}
.status span:before {
  content: "";
  width: 25px;
  height: 25px;
  background-color: #9b0c13;
  /*background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5)*/
  border-radius: 25px;
  border: 4px solid #c30e14;
  position: absolute;
  top: -15px;
  left: calc(50% - 12px);
  transition: all 200ms ease-in;
}
.swiper-control {
  text-align: right;
}
.swiper-container {
  width: 100%;
  height: 200px;
  margin: 10px 0;
  overflow: hidden;
  padding: 0 10px;
}
.swiper-slide {
  width: 200px;
  text-align: center;
  font-size: 14px;
}
.swiper-slide:nth-child(2n) {
  width: 40%;
}
.swiper-slide:nth-child(3n) {
  width: 20%;
}
</style>
<?php } include __DIR__.'../../footer.php'; ?>