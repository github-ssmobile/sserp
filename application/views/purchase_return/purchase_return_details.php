<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-8 col-md-offset-1 text-center">
    <span class="mdi mdi-repeat fa-2x"> Purchase Return Details</span>
</div><div class="clearfix"></div><hr>
<?php // foreach ($purchase_return_data as $purchase_return){ ?>
<div style="font-family: K2D; font-size: 15px;">
    <div class="thumbnail" style="border-radius: 0; min-height: 600px;">
        <div class="col-md-6">
            <span class="col-md-3 col-xs-3 text-muted">Inward Id: </span>
            <div class="col-md-9 col-xs-9"><?php echo $purchase_return->financial_year.$purchase_return->id_purchasereturn ?></div><div class="clearfix"></div>
            <span class="col-md-3 col-xs-3 text-muted">Date Time:</span>
            <div class="col-md-9 col-xs-9"><?php echo date('d/m/Y h:i:s A', strtotime($purchase_return->entry_time)) ?></div><div class="clearfix"></div>
            <span class="col-md-3 text-muted">Remark:</span>
            <div class="col-md-9"><?php echo $purchase_return->purchase_return_reason ?></div><div class="clearfix"></div>
        </div>
        <div class="col-md-6">
            <span class="col-md-4 col-xs-4 text-muted">Vendor:</span>
            <div class="col-md-8 col-xs-8"><?php echo $purchase_return->vendor_name ?></div><div class="clearfix"></div>
            <span class="col-md-4 text-muted">GSTIN:</span>
            <div class="col-md-8 col-xs-8"><?php echo $purchase_return->vendor_gst ?></div><div class="clearfix"></div>
            <span class="col-md-4 text-muted">Address:</span>
            <div class="col-md-8 col-xs-8"><?php echo $purchase_return->vendor_address ?></div><div class="clearfix"></div>
            <span class="col-md-4 text-muted">State:</span>
            <div class="col-md-8 col-xs-8"><?php echo $purchase_return->state ?></div><div class="clearfix"></div>
        </div><div class="clearfix"></div><br>
        <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px;">
            <thead class="gradient_thead">
                <th>Sr</th>
                <th>Id</th>
                <th class="col-md-3">Product</th>
                <th>IMEI/SRNO</th>
                <th>Godown</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Basic</th>
                <th>Discount</th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Amount</th>
            </thead>
            <tbody>
                <?php $i=1; foreach ($purchase_return_product as $product){ ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $product->idvariant ?></td>
                    <td><?php echo $product->product_name ?></td>
                    <td><?php echo $product->imei_no ?></td>
                    <td><?php echo $product->godown_name ?></td>
                    <td><?php echo $product->qty ?></td>
                    <td><?php echo $product->price ?></td>
                    <td><?php echo $product->basic ?></td>
                    <td><?php echo $product->discount_amt ?></td>
                    <td><?php echo $product->taxable_amt ?></td>
                    <td><?php echo $product->cgst_amt ?></td>
                    <td><?php echo $product->sgst_amt ?></td>
                    <td><?php echo $product->igst_amt ?></td>
                    <td><?php echo $product->total_amount ?></td>
                </tr>
                <?php $i++; } ?>
<!--                <tr class="gradient_thead">
                    <td colspan="5"></td>
                    <td>Total</td>
                    <td><?php // echo $purchase_return->total_basic_amt ?></td>
                    <td><?php // echo $purchase_return->total_discount_amt ?></td>
                    <td><?php // echo $purchase_return->total_taxable_amt ?></td>
                    <td><?php // echo $purchase_return->total_cgst_amt ?></td>
                    <td><?php // echo $purchase_return->total_sgst_amt ?></td>
                    <td><?php // echo $purchase_return->total_igst_amt ?></td>
                    <td><?php // echo $purchase_return->gross_amount ?></td>
                    <td></td>
                </tr>-->
            </tbody>
        </table>
        <div class="col-md-7 col-md-offset-4" style="padding: 0">
            <div class="thumbnail">
                <table class="table table-striped table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin-bottom: 0">
                    <tbody>
                        <tr>
                            <td><span>Total Basic &nbsp; &nbsp; </span></td>
                            <td><?php echo $purchase_return->total_basic_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Freight Charges &nbsp; &nbsp; </span></td>
                            <td> + <?php echo $purchase_return->total_charges_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Total Discount Before GST&nbsp; &nbsp; </span></td>
                            <td> - <?php echo $purchase_return->total_discount_amt ?></td>
                        </tr>
                        <tr>
                            <td><span>Total Taxable Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $purchase_return->total_taxable_amt ?></td>
                        </tr>
                        <tr>
                            <td>Total CGST</td>
                            <td>+ <?php echo $purchase_return->total_cgst_amt ?></td>
                        </tr>
                        <tr>
                            <td>Total SGST</td>
                            <td>+ <?php echo $purchase_return->total_sgst_amt ?></td>
                        <tr>
                            <td>Total IGST</td>
                            <td>+ <?php echo $purchase_return->total_igst_amt ?>
                        </tr>
                        <tr>
                            <td><span>Total Tax Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $purchase_return->total_tax ?></td>
                        </tr>
                        <tr>
                            <td><span>Gross Total Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $purchase_return->gross_amount ?></td>
                        </tr>
                        <tr>
                            <td><span>Discount After GST &nbsp; &nbsp; </span></td>
                            <td> - <?php echo $purchase_return->overall_discount ?></td>
                        </tr>
                        <tr>
                            <td><span>Final Amount &nbsp; &nbsp; </span></td>
                            <td> = <?php echo $purchase_return->final_amount ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>