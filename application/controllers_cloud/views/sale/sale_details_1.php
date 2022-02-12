<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-cart-outline fa-lg"></span> Sale Details </h3></center></div><div class="clearfix"></div>
<?php foreach ($sale_data as $sale){ ?>
<div style="font-family: K2D; font-size: 15px;">
    <div class="panel panel-info">
        <?php if($sale->corporate_sale == 1){ ?></center>
        <div class="panel-heading">
            <center><?php echo "Corporate Sale";?></center>
        </div>
        <?php } ?>
        <div class="panel-body" style="min-height: 600px">
            <div class="col-md-4 pull-right">
                <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_sale ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Entry time:</span> &nbsp; &nbsp; <?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->inv_no ?>
            </div><div class="clearfix"></div>To,
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo date('d/m/Y', strtotime($sale->date)); ?>
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
            <div class="col-md-12">
                <span class="text-muted">Remark: </span>
                <?php echo $sale->remark ?>
            </div>
            <div class="clearfix"></div>
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
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
                            if($sale->gst_type == 0){
                                $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                $taxable = $product->total_amount / $cal;
                                $taxable_total += $taxable;
                                $cgst = $product->total_amount - $taxable;
                                $cgstamt = $cgst / 2;
                                $cgstamt_total += $cgstamt;
                            }else{
                                $cali = ($product->igst_per + 100) / 100;
                                $taxable = $product->total_amount / $cali;
                                $taxable_total += $taxable;
                                $igst = $product->total_amount - $taxable;
                                $igstamt_total += $igstamt;
                            }
                            ?>
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
                                <td><?php echo number_format($cgstamt,2).'<p class="pull-right" style="font-size:11px">('.$product->sgst_per.'%)</p>' ?></td>
                        <?php }else{ ?>
                        <td><?php echo $product->igst_per ?></td>
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
                        <td><?php echo number_format($cgstamt_total,2) ?></td>
                        <td><?php echo number_format($cgstamt_total,2) ?></td>
                        <td>0</td>
                        <td><?php echo $sale->final_total ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table><br>
            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Details</span>
            <div class="thumbnail" style="overflow: auto; padding: 2px">
                <table class="table table-condensed table-bordered table-hover" style="font-size: 14px; margin-bottom: 0">
                    <tbody>
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
                    </tbody>
                </table>
            </div>
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
                        <?php } ?>
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