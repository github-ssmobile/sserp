<?php include __DIR__.'../../header.php'; ?>
<?php foreach ($sale_data as $sale){ ?>
<div class="col-md-10">
    <center>
        <h3 style="margin-top: 15px">
            <i class="mdi mdi-keyboard-return fa-lg"></i> Sales Return 
        </h3>
    </center>
</div><div class="clearfix"></div>
    <div style="font-family: K2D; font-size: 15px;">
        <div class="panel panel-info" style="min-height: 800px">
            <div class="col-md-4 col-sm-6 pull-right">
                <span class="text-muted">Return Id:</span> &nbsp; &nbsp; &nbsp; <?php echo $sale->id_salesreturn ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 col-sm-6 pull-right">
                <span class="text-muted">Entry time:</span> &nbsp; &nbsp; <?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 col-sm-6 pull-right">
                <span class="text-muted">Return Inv:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->sales_return_invid ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 col-sm-6 pull-right">
                <span class="text-muted">Return Date:</span> &nbsp; <?php echo $sale->date ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4 col-sm-6 pull-right">
                <span class="text-muted">Sale Invoice:</span> &nbsp; <?php echo $sale->inv_no ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4">To,<br>
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_fname.' '.$sale->customer_lname ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4">
                <span class="text-muted">Address</span>: <?php echo $sale->customer_address ?>
            </div><div class="clearfix"></div>
            <div class="col-md-4">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_contact ?>
            </div><div class="clearfix"></div>
            <?php if($sale->customer_gst !=''){ ?>
            <div class="col-md-4">
                <span class="text-muted">GSTIN</span>: <?php echo $sale->customer_gst ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-7">
                <span class="text-muted">Promoter</span>: <?php echo $sale->user_name ?>
            </div><div class="clearfix"></div>
            <center>
                <?php if($sale->sales_return_type==1){ ?>
                    <h3 style="color: #ff9999;margin-top: 0px"><i class="mdi mdi-cash-multiple"></i> Cash Return
                        <!--<img height="100" src="<?php // echo base_url('assets/images/cndn.png') ?>" />-->
                    </h3>
                <?php } else if($sale->sales_return_type==2){ ?>
                    <h3 style="color: #ff9999;margin-top: 0px"><i class="mdi mdi-cellphone-android"></i> Product Return Replace
                        <!--<img height="100" src="<?php // echo base_url('assets/images/cndn.png') ?>" />-->
                    </h3>
                <?php } else if($sale->sales_return_type==3){ ?>
                    <h3 style="color: #ff9999;margin-top: 0px"><i class="mdi mdi-cellphone-android"></i> DOA Return
                        <!--<img height="100" src="<?php // echo base_url('assets/images/cndn.png') ?>" />-->
                    </h3>
                <?php } ?>
            </center>
            <div class="col-md-12">
                <div class="thumbnail" style="padding: 0">
                    <table id="model_data" class="table table-bordered table-condensed table-hover" style="margin-bottom: 0">
                        <thead class="bg-info">
                            <th class="col-md-4">Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Basic</th>
                            <th>Dis </th>
                            <th>Taxable <i class="fa fa-rupee"></i></th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Amount</th>
                            <th class="col-md-1">IMEI/SRNO</th>
                            <?php if($sale->sales_return_type==3){ ?>
                            <th class="col-md-1">New IMEI/SRNO</th>
                            <?php } ?>
                        </thead>
                        <tbody>
                            <?php $taxable_total = 0; $cgstamt_total = 0; foreach ($sale_product as $product) { 
                                    $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                    $taxable = $product->total_amount / $cal;
                                    $taxable_total += $taxable;
                                    $cgst = $product->total_amount - $taxable;
                                    $cgstamt = $cgst / 2;
                                    $cgstamt_total += $cgstamt;
                                ?>
                            <tr>
                                <td><?php echo $product->product_name ?></td>
                                <td><?php echo $product->sku_type ?></td>
                                <td><?php echo $product->qty ?></td>
                                <td><?php echo $product->price ?></td>
                                <td><?php echo $product->basic ?></td>
                                <td><?php echo $product->discount_amt ?></td>
                                <td><?php echo number_format($taxable,2) ?></td>
                                <td><?php echo number_format($cgstamt,2).'<p class="pull-right" style="font-size:11px">('.$product->cgst_per.'%)</p>' ?></td>
                                        <td><?php echo number_format($cgstamt,2).'<p class="pull-right" style="font-size:11px">('.$product->sgst_per.'%)</p>' ?></td>
                                <td><?php // echo $product->igst_amt.'(0)' ?>0</td>
                                <td><?php echo $product->total_amount ?></td>
                                <td><?php echo $product->imei_no ?></td>
                                <?php if($sale->sales_return_type==3){ ?>
                                <td><?php echo $product->new_imei_against_doa ?></td>
                                <?php } ?>
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
                                <?php if($sale->sales_return_type==3){ ?>
                                <td></td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><div class="clearfix"></div><br>
            <div class="col-md-6 col-md-offset-3" style="padding: 0">
                <div class="thumbnail">
                    <table class="table table-striped table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin-bottom: 0">
                        <tbody>
                            <?php if($sale->sales_return_type!=3){ ?>
                            <tr>
                                <td>Return Cash</td>
                                <td><?php echo $sale->final_total ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td>Approved by</td>
                                <td colspan="3"><?php echo $sale->sales_return_approved_by ?></td>
                            </tr>
                            <tr>
                                <td>Reason</td>
                                <td colspan="3"><?php echo $sale->sales_return_reason ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><div class="clearfix"></div>
        </div>
    </div>
<?php } include __DIR__ . '../../footer.php'; ?>