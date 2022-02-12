<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-cart-outline fa-lg"></span> Sale Details </h3></center></div><div class="clearfix"></div>
<?php $sale=$sale_data[0]; ?>
<div style="font-family: K2D; font-size: 15px;">
    <div class="panel panel-info">
       
        <div class="panel-body" style="min-height: 600px">
            <div class="col-md-4 pull-right">
                <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_old_sale ?>
            </div><div class="clearfix"></div>           
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->invoice_no ?>
            </div><div class="clearfix"></div>To,
            <div class="col-md-4 pull-right">
                <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo date('d/m/Y', strtotime($sale->invoice_date)); ?>
            </div><div class="clearfix"></div>
            <div class="col-md-6">
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_name ?>
            </div><div class="clearfix"></div>          
            <div class="col-md-6">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_mobile ?>
            </div><div class="clearfix"></div>
            <?php if($sale->customer_gst_no !=''){ ?>
            <div class="col-md-6">
                <span class="text-muted">GST</span>: <?php echo $sale->customer_gst_no ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-6">
                <span class="text-muted">Sales Promoter</span>: <?php echo $sale->promoter_name; ?>
            </div><div class="clearfix"></div>
            <div class="clearfix"></div>
            <br><br>
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px; margin-bottom: 0">
                <thead class="bg-info">
                        <th><center>SN</center></th>
                        <th class="col-md-5 col-xs-5"><center>Product</center></th>
                        <th>HSN</th>
                        <th><center>Qty</center></th>                       
                        <th><center>Basic</center></th>                                                
                        <?php $gst_type = $sale->igst; if ($gst_type != 0) { ?>
                            <th><center>IGST</center></th>
                        <?php } else { ?>
                            <th><center>CGST</center></th>
                            <th><center>SGST</center></th>
                        <?php } ?>
                        <th><center>MOP</center></th>
                        <th><center>Discount</center></th>
                        <th><center>Sold Amount</center></th>
                        </thead>
                        <tbody>
                        <?php $i = 1;
                            $tqty = 0;
                            $trate = 0;
//                            $tdiscount = 0;
                            $tcgst = 0;
                            $tigst = 0;
                            $ttaxable = 0;
                            $ttotal_amount = 0;
                           
                                foreach ($sale_data as $product) {
                                    $total_amount=$product->settlement_amount;
                                    $ttotal_amount += $total_amount;
                                    $cal = ($product->gst_rate + 100) / 100;
                                    $taxable = $total_amount / $cal;
                                    $igstamt = $total_amount - $taxable;
                                    $tigst += $igstamt;
                                    $tqty += 1;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / 1;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; ?>[
                                            <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                                                <?php echo $sale->serial_no;  ?>
                                            <?php }else{ ?>
                                                <?php echo $sale->imei_1_no; ?>
                                            <?php } ?>
                                                ]
                                        </td>
                                        <td><?php echo $product->hsn_code ?></td>
                                        <td>1</td>                                        
                                        <td><?php echo $product->base_price ?></td>
                                         <?php $gst_type = $sale->igst; if ($gst_type != 0) { ?>
                                            <td><?php echo $product->igst . '<span class="pull-right" style="font-size:11px">(' . $product->gst_rate . '%)</span>' ?></td>                                        
                                        <?php } else { ?>
                                            <td><?php echo $product->sgst . '<span class="pull-right" style="font-size:11px">(' . ($product->gst_rate/2) . '%)</span>' ?></td>                                        
                                            <td><?php echo $product->cgst . '<span class="pull-right" style="font-size:11px">(' . ($product->gst_rate/2) . '%)</span>' ?></td>                                                                                    
                                        <?php } ?>
                                        
                                        <td><?php echo $product->total_amount_per_qty ?></td>
                                        <td><?php echo $product->hidden_discount; ?></td>
                                        <td><?php echo $product->settlement_amount ?></td>
                                    </tr>
                                  
                                    <?php } ?>
                                    <tr class="bg-info">
                                          <?php $gst_type = $sale->igst; if ($gst_type != 0) { ?>
                                        <td colspan="8"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>   
                                          <?php } else { ?>
                                        <td colspan="9"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>   
                                          <?php } ?>
                                        <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                    </tr>
                                    
                        </tbody>
                    </table>
            <br>
            <span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="fa fa-rupee"></i> Payment Details</span>
             <br>
             <br>
            <div class="thumbnail" style="overflow: auto; padding: 2px">
                <table class="table table-condensed table-bordered table-hover" style="font-size: 14px; margin-bottom: 0">
                    <tbody>
                        <?php 
                        $buyback_ledger_amount=0;
                        $cash_amount=0;
                        $credit_amount=0;
                        $paytm_amount=0;
                        $cheque_amount=0;
                        $bajaj_finserv=0;
                        $bharat_pe=0;
                        $hdb_finance=0;
                        $hdfc_finance=0;
                        $home_credit=0;
                        $icici_finance=0;
                        $idfc_finance=0;
                        $paytm=0;
                        $phone_pe=0;
                        $pine_labs=0;
                        $samsung_sure=0;
                        foreach ($sale_data as $product) {                             
                            $buyback_ledger_amount=$buyback_ledger_amount+$product->buyback_ledger_amount;
                            $cash_amount=$cash_amount+$product->cash_amount;
                            $credit_amount=$credit_amount+$product->credit_amount;
                            $paytm_amount=$paytm_amount+$product->paytm_amount;
                            $cheque_amount=$cheque_amount+$product->cheque_amount;
                            $bajaj_finserv=$bajaj_finserv+$product->bajaj_finserv;
                            $bharat_pe=$bharat_pe+$product->bharat_pe;
                            $hdb_finance=$hdb_finance+$product->hdb_finance;
                            $hdfc_finance=$hdfc_finance+$product->hdfc_finance;
                            $home_credit=$home_credit+$product->home_credit;
                            $icici_finance=$icici_finance+$product->icici_finance;
                            $idfc_finance=$idfc_finance+$product->idfc_finance;
                            $paytm=$paytm+$product->paytm;
                            $phone_pe=$phone_pe+$product->phone_pe;
                            $pine_labs=$pine_labs+$product->pine_labs;
                            $samsung_sure=$samsung_sure+$product->samsung_sure;
                           } ?>
                        
                       <thead class="bg-info">
                            <th><span class="text-muted">Mode of Payment</span></th>
                            <th>Amount</th>
                            <th>TransactionId/RRN/CHE.No</th>
                        </thead>
                            
                        
                        <?php if($cash_amount>0){ ?>
                        <tr>
                            <td>CASH</td>
                            <td> <?php echo $cash_amount  ?></td>
                            <td></td>
                        </tr>
                        <?php } ?>
                        <?php if($buyback_ledger_amount>0){ ?>
                            <tr>
                            <td>BUYBACK</td>
                             <td> <?php echo $buyback_ledger_amount ?></td>
                            <td><?php echo $sale->buyback_item ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($credit_amount>0){ ?>
                            <tr>
                            <td>CREDIT</td>
                             <td> <?php echo $credit_amount ?></td>
                            <td></td>
                        </tr>
                        <?php } ?>
                        <?php if($cheque_amount>0){ ?>
                            <tr>
                            <td>CHEQUE</td>
                             <td> <?php echo $cheque_amount ?></td>
                            <td><?php echo $sale->cheque_no ?></td>
                        </tr>
                        <?php } ?>
                         <?php if($bharat_pe>0){ ?>
                            <tr>
                            <td>BHARAT PE</td>
                             <td> <?php echo $bharat_pe ?></td>
                            <td><?php echo $sale->bharat_pe_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($phone_pe>0){ ?>
                            <tr>
                            <td>PHONE PE</td>
                             <td> <?php echo $phone_pe ?></td>
                            <td><?php echo $sale->phone_pe_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($paytm_amount>0){ ?>
                            <tr>
                            <td>PAYTM</td>
                             <td> <?php echo $paytm_amount ?></td>
                            <td><?php echo $sale->paytm_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($pine_labs>0){ ?>
                            <tr>
                            <td>SWIPE</td>
                             <td> <?php echo $pine_labs ?></td>
                            <td><?php echo $sale->pine_labs_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($bajaj_finserv>0){ ?>
                            <tr>
                            <td>BAJAJ FINSERV</td>
                            <td> <?php echo $bajaj_finserv ?></td>
                            <td><?php echo $sale->bajaj_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($hdb_finance>0){ ?>
                            <tr>
                            <td>HDB FINANCE/td>
                             <td> <?php echo $hdb_finance ?></td>
                            <td><?php echo $sale->hdb_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($hdfc_finance>0){ ?>
                            <tr>
                            <td>HDFC FINANCE</td>
                            <td> <?php echo $hdfc_finance ?></td>
                            <td><?php echo $sale->hdfc_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($home_credit>0){ ?>
                            <tr>
                            <td> HOME CREDIT</td>
                            <td> <?php echo $home_credit ?></td>
                            <td><?php echo $sale->home_credit_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($icici_finance>0){ ?>
                            <tr>
                            <td>ICICI FINANCE</td>
                            <td> <?php echo $icici_finance  ?></td>
                            <td><?php echo $sale->icici_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($idfc_finance>0){ ?>
                            <tr>
                            <td>IDFC FINANCE</td>
                            <td> <?php echo $idfc_finance ?></td>
                            <td><?php echo $sale->idfc_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($samsung_sure>0){ ?>
                            <tr>
                            <td>SAMSUNG SURE</td>
                             <td> <?php echo $samsung_sure ?></td>
                            <td><?php echo $sale->samsung_sure_transaction_id ?></td>
                        </tr>
                        <?php } ?>
                        
                     
                    </tbody>
                </table>
            </div>
           <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php  include __DIR__.'../../footer.php'; ?>