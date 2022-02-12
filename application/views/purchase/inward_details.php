<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-8 col-md-offset-1 text-center">
    <span class="mdi mdi-cart-outline fa-2x"> Purchase Details</span>
</div><div class="clearfix"></div><hr>
<?php foreach ($inward_data as $inward){ ?>
<div style="font-family: K2D; font-size: 16px;">
    <div class="thumbnail" style="border-radius: 0; min-height: 600px;">
        <div class="col-md-6">
            <span class="col-md-3 col-xs-3 text-muted">Inward Id: </span>
            <div class="col-md-9 col-xs-9"><?php echo $inward->financial_year.'/'.$inward->id_inward ?></div><div class="clearfix"></div>
            <span class="col-md-3 col-xs-3 text-muted">Date:</span>
            <div class="col-md-9 col-xs-9"><?php echo $inward->date ?></div><div class="clearfix"></div>
            <span class="col-md-3 text-muted">Remark:</span>
            <div class="col-md-9"><?php echo $inward->remark ?></div><div class="clearfix"></div>
        </div>
        <div class="col-md-6">
            <span class="col-md-4 col-xs-4 text-muted">Supplier Inv No:</span>
            <div class="col-md-8 col-xs-8"><?php echo $inward->supplier_invoice_no ?></div><div class="clearfix"></div>
            <span class="col-md-4 col-xs-4 text-muted">Supplier:</span>
            <div class="col-md-8 col-xs-8"><?php echo $inward->vendor_name ?></div><div class="clearfix"></div>
            <span class="col-md-4 text-muted">Supplier Date:</span>
            <div class="col-md-8"><?php echo $inward->vendor_invoice_date ?></div><div class="clearfix"></div>
            <span class="col-md-4 text-muted">Entry time:</span>
            <div class="col-md-8"><?php echo date('d/m/Y h:i:s A', strtotime($inward->entry_time)); ?></div>
        </div><div class="clearfix"></div><br>
        <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px;">
            <thead class="bg-info">
                <th>Id</th>
                <th class="col-md-3">Product</th>
                <th>Godown</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Basic</th>
                <th>Discount</th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Amount</th>
                <th>IMEI</th>
            </thead>
            <tbody>
                <?php foreach ($inward_product as $product){ ?>
                <tr>
                    <td><?php echo $product->idvariant ?></td>
                    <td><?php echo $product->product_name ?></td>
                    <td><?php echo $product->godown_name ?></td>
                    <td><?php echo $product->sku_type ?></td>
                    <td><?php echo $product->qty ?></td>
                    <td><?php echo $product->price ?></td>
                    <td><?php echo $product->basic ?></td>
                    <td><?php echo $product->discount_amt ?></td>
                    <td><?php echo $product->taxable_amt ?></td>
                    <td><?php echo $product->cgst_amt ?></td>
                    <td><?php echo $product->sgst_amt ?></td>
                    <td><?php echo $product->igst_amt ?></td>
                    <td><?php echo $product->total_amount ?></td>
                    <td><div style="width: 200px; overflow: auto"><?php echo $product->imei_srno ?></div></td>
                </tr>
                <?php } ?>
<!--                <tr class="gradient_thead">
                    <td colspan="5"></td>
                    <td>Total</td>
                    <td><?php // echo $inward->total_basic_amt ?></td>
                    <td><?php // echo $inward->total_discount_amt ?></td>
                    <td><?php // echo $inward->total_taxable_amt ?></td>
                    <td><?php // echo $inward->total_cgst_amt ?></td>
                    <td><?php // echo $inward->total_sgst_amt ?></td>
                    <td><?php // echo $inward->total_igst_amt ?></td>
                    <td><?php // echo $inward->gross_amount ?></td>
                    <td></td>
                </tr>-->
            </tbody>
        </table>
        <div class="col-md-6 col-md-offset-6" style="padding: 0">
            <div class="thumbnail">
                <table class="table table-striped table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin-bottom: 0">
                    <tbody>
                        <tr>
                            <td><span>Total Basic &nbsp; &nbsp; </span></td>
                            <td><?php echo $inward->total_basic_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Freight Charges &nbsp; &nbsp; </span></td>
                            <td> + <?php echo $inward->total_charges_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Total Discount Before GST&nbsp; &nbsp; </span></td>
                            <td> - <?php echo $inward->total_discount_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Total Taxable Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $inward->total_taxable_amt ?></td>
                        </tr>
                        <tr>
                            <td>Total CGST</td>
                            <td>+ <?php echo $inward->total_cgst_amt ?></td>
                        </tr>
                        <tr>
                            <td>Total SGST</td>
                            <td>+ <?php echo $inward->total_sgst_amt ?></td>
                        <tr>
                            <td>Total IGST</td>
                            <td>+ <?php echo $inward->total_igst_amt ?>
                        </tr>
                        <tr>
                            <td><span>Total Tax Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $inward->total_tax ?></td>
                        </tr>
                        <tr>
                            <td><span>Gross Total Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $inward->gross_amount ?></td>
                        </tr>
                        <tr>
                            <td><span>Discount After GST &nbsp; &nbsp; </span></td>
                            <td> - <?php echo $inward->overall_discount ?></td>
                        </tr>
                        <tr>
                            <td><span>Final Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $inward->final_amount ?></td>
                        </tr>
                        <tr>
                            <td><span>TCS Amount &nbsp; &nbsp; </span></td>
                            <td> + <?php echo $inward->tcs_amount ?></td>
                        </tr>
                        <tr>
                            <td><span>Overall Total &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $inward->overall_amount ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><div class="clearfix"></div>
    </div>
</div>
<?php } include __DIR__ . '../../footer.php'; ?>