<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-cart-outline fa-lg"></span> Sale Details </h3></center></div><div class="clearfix"></div>
<?php foreach ($sale_data as $sale){ ?>
<div style="font-family: K2D; font-size: 15px;">
    <div class="panel panel-info">
        <?php if($sale->corporate_sale == 1){ ?></center>
        <div class="panel-heading">
            <center><?php echo "Online Sale";?></center>
        </div>
        <?php } ?>
        <div class="panel-body" style="min-height: 600px">
            <div class="col-md-4 pull-right">
                <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_sale ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Entry time:</span> &nbsp; &nbsp; <?php echo date('d/m/Y h:i a', strtotime($sale->invoice_date)) ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->inv_no ?>
            </div><div class="clearfix"></div>To,
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo date('d/m/Y', strtotime($sale->invoice_date)); ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_fname.' '.$sale->customer_lname ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Address</span>: <?php echo $sale->customer_address ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_contact ?>
            </div><div class="clearfix"></div>
            <?php if($sale->customer_gst !=''){ ?>
            <div class="col-md-6">
                <span class="text-muted">GST</span>: <?php echo $sale->customer_gst ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-6">
                <span class="text-muted">Sales Promoter</span>: <?php echo $sale->user_name; ?>
            </div><div class="clearfix"></div>
<!--            <div class="col-md-12">
                <span class="text-muted">Remark: </span>
                <?php // echo $sale->remark ?>
            </div>-->
            <div class="clearfix"></div>
<!--            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
                <thead class="bg-info">
                    <th class="col-md-4">Product</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Basic</th>
                    <th>Dis </th>
                    <th>Taxable <i class="fa fa-rupee"></i></th>
                    <?php if($sale->gst_type == 0){ ?>
                    <th>CGST</th>
                    <th>SGST</th>
                    <?php }else{ ?>
                    <th>IGST</th>
                    <?php } ?>
                    <th>Amount</th>
                    <th>DOA Replace</th>
                </thead>
                <tbody>
                    <?php $taxable_total = 0; $cgstamt_total = 0; foreach ($sale_product as $product) { 
                            $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                            $taxable = $product->total_amount / $cal;
                            $taxable_total += $taxable;
                            $cgst = $product->total_amount - $taxable;
                            $cgstamt = $cgst / 2;
                            $cgstamt_total += $cgstamt; ?>
                    <tr>
                        <td><?php echo $product->product_name ?> [<?php echo $product->imei_no ?>]</td>
                        <td><?php echo $product->hsn ?></td>
                        <td><?php echo $product->qty ?></td>
                        <td><?php echo $product->price ?></td>
                        <td><?php echo $product->basic ?></td>
                        <td><?php echo $product->discount_amt ?></td>
                        <td><?php echo number_format($taxable,2) ?></td>
                        <?php if($sale->gst_type == 0){ ?>
                        <td><?php echo number_format($cgstamt,2).'<p class="pull-right" style="font-size:11px">('.$product->cgst_per.'%)</p>' ?></td>
                        <td><?php echo number_format($cgstamt,2).'<p class="pull-right" style="font-size:11px">('.$product->cgst_per.'%)</p>' ?></td>
                        <?php }else{ ?>
                        <td><?php echo $cgst ?></td>
                        <?php } ?>
                        <td><?php echo $product->total_amount ?></td>
                        <td><?php if($product->sales_return_type==1){ echo 'Cash Return'; }elseif($product->sales_return_type==2){ echo 'Replace,Upgrade Return'; }elseif($product->sales_return_type==3){ echo 'DOA Return ['.$product->doa_imei_no.']'; }else{ echo '-'; } ?></td>
                    </tr>
                    <?php } ?>
                    <tr class="bg-info">
                        <td colspan="3"></td>
                        <td>Total</td>
                        <td><?php echo $sale->basic_total ?></td>
                        <td><?php echo $sale->discount_total ?></td>
                        <td><?php echo number_format($taxable_total,2) ?></td>
                        <?php if($sale->gst_type == 0){ ?>
                        <td><?php echo number_format($cgstamt_total,2) ?></td>
                        <td><?php echo number_format($cgstamt_total,2) ?></td>
                        <?php }else{ ?>
                        <td><?php echo $cgstamt_total ?></td>
                        <?php } ?>
                        <td><?php echo $sale->final_total ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table><br>-->
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
//                                    $total_amount=0;
//                                    if($product->is_mop){
//                                        if($product->mop < $product->total_amount){
//                                            $total_amount=$product->total_amount;
//                                        }else{
//                                            if($product->idskutype == 4){
//                                                $total_amount=$product->mop * $product->qty;
//                                            }else{
//                                                $total_amount=$product->mop;
//                                            }
//                                        }
//                                    }else{
                                        $total_amount=$product->total_amount;
//                                    }
                                    $ttotal_amount += $total_amount;
                                    $cal = ($product->igst_per + 100) / 100;
                                    $taxable = $total_amount / $cal;
                                    $igstamt = $total_amount - $taxable;
                                    $tigst += $igstamt;
                                    $tqty += $product->qty;
//                                    $trate += $product->price;
                                    $tdiscount += $product->discount_amt;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / $product->qty;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name;
                                    if ($product->imei_no != NULL) {
                                        echo ' [' . $product->imei_no . ']';
                                    }elseif($product->ssale_type != 0){
                                        echo ' [' . $product->insurance_imei_no . ']<br>';
                                        echo 'ActCode: ' . $product->activation_code;
                                    } if($product->sales_return_type == 3){ echo '<br>DOA Return'; }
                                    if($product->idsale_product_for_doa != NULL){ echo '<br>DOA Replace'; } ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <!--<td><?php // echo number_format($rate,2) ?></td>-->
                                        <td><?php echo $product->mop ?></td>
                                        <td><?php echo $product->price ?></td>
                                        <td><?php echo $product->basic ?></td>
                                        <td><?php echo $product->discount_amt ?></td>
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <td><?php echo number_format($igstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->igst_per . '%)</span>' ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                    <?php // } for ($i = count($sale_product); $i < 10; $i++) { ?>
                                    <!--<tr>-->
                                        <!--<td style="height: 50px"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>-->
                                    <!--</tr>-->
                                    <?php } ?>
                                    <tr class="bg-info">
                                        <td colspan="7"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                        <!--<td><?php // echo $tqty ?></td>-->
                                        <!--<td><?php // echo number_format($trate,2) ?></td>-->
                                        <td><?php echo $tdiscount ?></td>
                                        <td><?php echo number_format($ttaxable, 2) ?></td>
                                        <td><?php echo number_format($tigst, 2) ?></td>
                                        <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                    </tr>
                                    <?php // cgst
                                        } else {
                                        foreach ($sale_product as $product) {
//                                            $total_amount=0;
//                                            if($product->is_mop){
//                                                if($product->idskutype == 4){
//                                                    $total_amount=$product->mop * $product->qty;
//                                                }else{
//                                                    $total_amount=$product->mop;
//                                                }
//                                            }else{
                                                $total_amount=$product->total_amount;
//                                            }
                                            $ttotal_amount += $total_amount;
                                            $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                            $taxable = $total_amount / $cal;
                                            $cgst = $total_amount - $taxable;
                                            $cgstamt = $cgst / 2;
                                            $tcgst += $cgstamt;
                                            $tqty += $product->qty;
//                                            $tdiscount += $product->discount_amt;
                                            $ttaxable += $taxable;
                                            $rate = $taxable / $product->qty;
                                            $trate += $rate;
                                        ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name;
                                    if ($product->imei_no != NULL) {
                                        echo ' [' . $product->imei_no . ']';
                                    }elseif($product->ssale_type != 0){
                                        echo ' [' . $product->insurance_imei_no . ']<br>';
                                        echo 'Activation Code: ' . $product->activation_code;
                                    } if($product->sales_return_type == 3){ echo ' DOA Return'; }
                                    elseif($product->idsale_product_for_doa != NULL){ echo ' DOA Replace'; }?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <!--<td><?php // echo number_format($rate,2)  ?></td>-->
                                        <td><?php echo $product->mop ?></td>
                                        <td><?php echo $product->price ?></td>
                                        <td><?php echo $product->basic  ?></td>
                                        <td><?php echo $product->discount_amt ?></td>
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <?php // if($gst_type==1){  ?>
                                        <!--<td><?php // echo number_format($igstamt,2).'<span class="pull-right" style="font-size:12px">('.$product->igst_per.'%)</span>'  ?></td>-->
                                        <?php // }else{  ?>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->cgst_per . '%)</span>' ?></td>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->sgst_per . '%)</span>' ?></td>
                                        <?php // }  ?>
                                        <!--<td><?php // echo $product->igst_amt.'(0)'  ?>0</td>-->
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                <?php // } for ($i = count($sale_product); $i < 10; $i++) { ?>
                                    <!--<tr>-->
                                        <!--<td style="height: 50px"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>-->
                                    <!--</tr>-->
                                <?php } ?>
                                <tr class="bg-info">
                                    <td colspan="7"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                    <!--<td><?php // echo $tqty ?></td>-->
                                    <!--<td><?php // echo number_format($trate,2) ?></td>-->
                                    <td><?php echo $sale->discount_total ?></td>
                                    <td><?php echo number_format($ttaxable, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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

                                <?php if($payment->bounce_charges != 0){ ?>
                                <td><span class="text-muted">Bounce Charges</span></td>
                                <td><?php echo $payment->bounce_charges;?></td>

                                <?php } ?>
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
                            
                            <?php if($payment->bounce_charges != 0){ ?>
                            <td><span class="text-muted">Bounce Charges</span></td>
                            <td><?php echo $payment->bounce_charges;?></td>
                            
                            <?php } ?>
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
            <div class="col-md-12 thumbnail">
                <span class="text-muted">Remark: </span>
                <?php echo $sale->remark;
                if($sale->idadvance_payment_receive){ ?>
                <a href="<?php echo base_url()?>Payment/advance_booking_received_receipt/<?php echo $sale->idadvance_payment_receive  ?>" class="btn-sm waves-effect" style="color: #005bc0; font-size: 15px;border: 1px dotted #005bc0">Click to open Advanced booking receipt <i class="mdi mdi-newspaper fa-lg"></i></a>
                <?php } ?>
                
            </div><div class="clearfix"></div>
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
            <div class="col-md-4 warning-color" style="border-left: 10px solid #fff; padding: 5px">
                <div class="hovereffect" style="padding: 10px;">
                    Total Amount<br>
                    <?php  echo $sum; ?>
                </div>
            </div>
            <div class="col-md-4 primary-color" style="border-left: 10px solid #fff; padding: 5px">
                <div class="hovereffect" style="padding: 10px;">
                    Reconciled Amount<br>
                    <?php echo $total_received; ?>
                </div>
            </div>
            <div class="col-md-4 default-color" style="border-left: 10px solid #fff; padding: 5px">
                <div class="hovereffect" style="padding: 10px;">
                    Due Amount<br>
                    <?php echo $sum - $total_received; ?>
                </div>
            </div><div class="clearfix"></div>
        </div>
    </div>
</div>
<?php } include __DIR__.'../../footer.php'; ?>