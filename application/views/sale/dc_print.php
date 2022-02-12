<?php include 'header_invoice.php'; ?>
<style>
@page {
    size: A4;
    margin: 30px;
    padding: 20px;
}
@media print {
    html, body {
        width: 210mm;
        height: 297mm;
    }
}
</style>
<script>
    window.print();
</script>
<?php foreach ($sale_data as $sale) { ?>
    <div style="font-family: K2D; font-size: 13px;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="font-size: 16px; padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                    <div class="col-md-4 col-xs-4" style="padding: 0;">
                        <img class="hovereffect" height="85" src="<?php echo base_url() ?>assets/images/logo.jpg" alt="SS Mobile"/>
                    </div>
                    <div class="col-md-8 col-xs-8 text-center" style="padding: 0; font-size: 14px; padding-top: 15px; padding-left: 10px;">
                        <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0">SS COMMUNICATION & SERVICES PVT LTD</h3>
                        RATIKAMAL COMPLEX, 399 E WARD, SHAHUPURI, KOLHAPUR - 416001
                    </div>
                </div>
                <div class="row justify-content-end" style="padding: 3px 10px;">
                    <center><h4  style="color: #000;font-family: K2D">DELIVERY CHALLAN</h4></center>
                    <div class="col-md-7 col-xs-7">
                        Invoice No.: &nbsp;<b><?php echo $sale->inv_no ?></b>
                        <input type="hidden" value="<?php echo $sale->inv_no ?>" id="inv_no">
                        <input type="hidden" value="<?php echo $sale->id_sale ?>" id="inv_id">
                        <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->entry_time)) ?>" id="inv_date">
                    </div>
                    <div class="col-md-5 col-xs-5">
                        Date: &nbsp;<?php echo date('d-M-Y h:i A', strtotime($sale->entry_time)) ?>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #999999; font-size: 15px;">
                    <div class="col-md-7 col-xs-7">
                        <b>Branch: &nbsp; <?php echo $sale->branch_name ?></b><br>
                        <b>Address: </b> <?php echo $sale->branch_address; ?><br>
                        <b>GST No:</b> <?php echo $sale->branch_gstno; ?><br>
                        <b>Contact:</b> <?php echo $sale->branch_contact; ?><br>
                        <!--<b>Sales Promoter:</b>--> 
                        <?php // echo $sale->full_name; ?>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <b> Buyer,</b><br>
                        &nbsp; <span style="text-transform: uppercase" ><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></span><br>
                        &nbsp; Address: <span style="text-transform: capitalize" ><?php echo $sale->customer_address; ?></span><br>
                        &nbsp; Mobile: <?php echo $sale->customer_contact;
                        if ($sale->customer_gst != '') { ?><br>
                        &nbsp; GST No.: <?php echo $sale->customer_gst;
                        } $gst_type = $sale->gst_type; ?>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #999999">
                    <table id="model_data" class="table table-bordered table-condensed table-hover" style="font-size: 14px; margin: 0;">
                        <thead class="text-center">
                        <th><center>SN</center></th>
                        <th class="col-md-5 col-xs-5"><center>Product</center></th>
                        <th>HSN</th>
                        <th><center>Qty</center></th>
                        <th><center>Rate</center></th>
                        <!--<th><center>Disc</center></th>-->
                        <!--<th><center>Taxable</center></th>-->
                        <th><center>Amount</center></th>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $tqty = 0;
                            $trate = 0;
                            $tcgst = 0;
                            $tigst = 0;
                            $ttaxable = 0;
                            foreach ($sale_product as $product) {
                            $tqty += $product->qty;
                            $rate = $product->total_amount / $product->qty;
                            $trate += $rate;
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $product->product_name;
                            if ($product->imei_no != NULL) {
                                echo ' [' . $product->imei_no . ']';
                            } ?></td>
                                <td><?php echo $product->hsn ?></td>
                                <td><?php echo $product->qty ?></td>
                                <td><?php echo $rate ?></td>
                                <td><?php echo $product->total_amount ?></td>
                            </tr>
                            <?php } for ($i = count($sale_product); $i < 10; $i++) { ?>
                            <tr>
                                <td style="height: 50px"></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                <td><?php echo $tqty ?></td>
                                <td><?php echo number_format($trate,2) ?></td>
                                <td><?php echo moneyFormatIndia($sale->final_total); ?></td>
                            </tr>
                            <tr style="border: 1px solid #999999">
                                <td colspan="10" style="border: 1px solid #999999;font-size: 14px;">
                                    <b>Declaration:</b> We declare that this invoice shows actual price of the goods described & that all particulars are true & correct.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row" style="border: 1px solid #999999">
                    <div class="col-md-9 col-xs-9 col-xs-9" style="border-right: 1px solid #c3c3c3; padding: auto 5px"> 
                        <div style="line-height: 20px; color: #666666;">
                            <i class="fa fa-circle" style="font-size: 10px; opacity: 0.4;"></i> &nbsp;  <span style="color: #000;font-size: 14px;">Terms & Conditions</span><br>
                            <div style="font-size: 13px">
                            - &nbsp; Goods once sold will not be taken back, until and unless approved by the manufacturer.<br>
                            -  &nbsp; SS Communication & Service Pvt Ltd is not responsible for the performance and the warranty of any device sold. Warranty if any, is provided only by the manufacturer and as per the manufacturer’s policy only.<br>
                            -  &nbsp; In spite of the above, any device which is physically damaged, tampered or water logged, will not qualify for any kind of warranty from the manufacturer. <br>
                            -  &nbsp; All warranty periods if any are mentioned in the warranty card of the manufacturer and is applicable from the date of this invoice.<br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right">
                        <br><br><br><br><br><br>
                        <center>Authorized Signatory</center>
                    </div>
                </div>
            </div><center><i>Subject to <?php echo $sale->branch_district ?> jurisdiction</i></center>
        </div>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>