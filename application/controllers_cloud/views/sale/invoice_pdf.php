<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>ERP</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <?= link_tag("assets/css/bootstrap.css") ?>    
    <?= link_tag("assets/css/font-awesome.min.css") ?>        
    <?= link_tag("assets/css/style.css") ?>        
    <?= link_tag("assets/css/k2d.css") ?>
        
    <script src="<?php echo base_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    
    <script src="<?php echo base_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    
    
    <style>.btn{ margin: 0; }</style>
</head>
<body style="
      font-family: 'Nunito Sans', sans-serif;
      /*font-family: 'Roboto', sans-serif;*/
    /*font-size: 17px;*/
    font-weight: 400;">
   
<?php include __DIR__.'../../extras.php'; ?>

<style>
@page {
    size: A4;
    margin-left: 10px;
    margin-right: 10px;
    padding: 10px;
    display:inline-block; 
}
@media print {
    html, body {
        width: 220mm;
        height: 297mm;
    }
}
</style>

<?php foreach ($sale_data as $sale) { ?>
    <div style="font-family: K2D; font-size: 13px;">
        <div id="printTable" class=" container-fluid print_invoice">
            <div class="" style="padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                    <div class="col-md-4 col-xs-4" style="padding: 0;">
                        <img class="hovereffect" height="85" src="<?php echo base_url()?><?php echo $sale->company_logo?>" alt="SS Mobile"/>
                    </div>
                    <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px;line-height: 20px">
                        <h3 style="color: #000; font-family: K2D; font-size: 20px; margin: 0;"><?php echo $sale->company_name?></h3>
                        <h5 style="font-size: 10px; margin-top: 5px;"><?php echo $sale->company_address?></h5>
                    </div>
                </div>
                <div class="row justify-content-end" style="padding: 3px 10px;">
                    <center><h4  style="color: #000;font-family: K2D; margin: 0">TAX INVOICE</h4></center>
                    Invoice No.: &nbsp;<b><?php echo $sale->inv_no ?></b>
                    <input type="hidden" value="<?php echo $sale->inv_no ?>" id="inv_no">
                    <input type="hidden" value="<?php echo $sale->id_sale ?>" id="inv_id">
                    <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->invoice_date)) ?>" id="inv_date">
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y h:i A', strtotime($sale->invoice_date)) ?>
                    </div><div class="clearfix"></div>
                </div>
                <div class="row" style="border: 1px solid #999999;">
                    <div class="col-md-6 col-xs-6">
                        <b>Branch: &nbsp; <?php echo $sale->branch_name ?></b><br>
                        <b>Address: </b><?php echo $sale->branch_address; ?><br>
                        <b>GST No:</b> <?php echo $sale->branch_gstno; ?><br>
                        <b>Contact:</b> <?php echo $sale->branch_contact; ?><br>
                        <b>Sales Promoter:</b> <?php echo $sale->user_name; ?>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <b> Buyer,</b><br>
                        &nbsp; <span style="text-transform: uppercase" ><?php echo $sale->sale_customer_fname.' '.$sale->sale_customer_lname ?></span><br>
                        &nbsp; Address: <span style="text-transform: capitalize" ><?php echo $sale->customer_address.', '.$sale->customer_state.'-'.$sale->customer_pincode; ?></span><br>
                        &nbsp; Mobile: <?php echo $sale->customer_contact;
                        if ($sale->customer_gst != '') { ?><br>
                        &nbsp; GST No.: <?php echo $sale->customer_gst;
                        } $gst_type = $sale->gst_type; 
                        if(count($financer_of_idsale) > 0){
                            echo '<br><b>Finance Provider: </b>'.$financer_of_idsale[0]->payment_mode.' Finance ['.$financer_of_idsale[0]->transaction_id.']';
                        } ?>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #999999">
                    <table id="model_data" class="table table-bordered table-condensed table-hover" style="margin: 0;">
                        <tr class="text-center">
                        <th><center>SN</center></th>
                        <th class="col-md-4 col-xs-4"><center>Product</center></th>
                        <th><center>IMEI</center></th>
                        <th>HSN</th>
                        <th><center>Qty</center></th>
                        <th><center>Rate</center></th>
                        
                        <th><center>Taxable</center></th>
                        <?php if ($gst_type == 1) { ?>
                            <th><center>IGST</center></th>
                        <?php } else { ?>
                            <th><center>CGST</center></th>
                            <th><center>SGST</center></th>
                        <?php } ?>
                        <th><center>Amount</center></th>
                        </tr>
                        <tbody style="font-size: 12px;">
                        <?php $i = 1;
                            $tqty = 0;
                           $trate = 0;
                            $tcgst = 0;
                            $tigst = 0;
                            $ttaxable = 0;
                            $ttotal_amount = 0;
                            $is_mop = 0;
                            $discount_amt=0;
                            if ($gst_type == 1) { // igst
                                foreach ($sale_product as $product) {
                                    $total_amount=0;
                                    if($product->is_mop){
                                        $is_mop += 1;
                                        if($product->total_amount > $product->mop){
                                            $total_amount=$product->total_amount;
                                        }else{
                                            if($product->idskutype == 4){
                                                $total_amount=$product->mop * $product->qty;
                                            }else{
                                                $total_amount=$product->mop;
                                            }
                                            $discount_amt += $product->mop - $product->total_amount;
                                        }
                                    }else{
                                        $total_amount=$product->total_amount;
                                        $is_mop += 0;
                                    }
                                    $ttotal_amount += $total_amount;
                                    $cal = ($product->igst_per + 100) / 100;
                                    $taxable = $total_amount / $cal;
                                    $igstamt = $total_amount - $taxable;
                                    $tigst += $igstamt;
                                    $tqty += $product->qty;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / $product->qty;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name;
                                    if($product->sales_return_type == 3){ echo '<br>DOA Return'; }
                                    elseif($product->idsale_product_for_doa != NULL){ echo '<br>DOA Replace'; } ?></td>
                                        <td><?php echo $product->imei_no ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo number_format($rate,2) ?></td>
                                        
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <td><?php echo number_format($igstamt, 2) ?> &nbsp; <?php echo "(".$product->igst_per . '%)' ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                    
                                    <?php } ?>
                                    <tr>
                                        <td colspan="4"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                        <td><?php echo $tqty ?></td>
                                        <td><?php echo number_format($trate,2) ?></td>
                                    
                                        <td><?php echo number_format($ttaxable, 2) ?></td>
                                        <td><?php echo number_format($tigst, 2) ?></td>
                                        <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                    </tr>
                                    <?php // cgst
                                        } else {
                                        foreach ($sale_product as $product) {
                                            $total_amount=0;
                                            if($product->is_mop){
                                                $is_mop += 1;
                                                if($product->total_amount > $product->mop){
                                                    $total_amount=$product->total_amount;
                                                }else{
                                                    if($product->idskutype == 4){
                                                        $total_amount=$product->mop * $product->qty;
                                                    }else{
                                                        $total_amount=$product->mop;
                                                    }
                                                    $discount_amt += $product->mop - $product->total_amount;
                                                }
                                            }else{
                                                $is_mop += 0;
                                                $total_amount=$product->total_amount;
                                            }
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
                                        <td><?php echo $product->product_name;
                                    if($product->sales_return_type == 3){ echo ' DOA Return'; }
                                    elseif($product->idsale_product_for_doa != NULL){ echo ' DOA Replace'; }?></td>
                                        <td><?php echo $product->imei_no ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo number_format($rate,2)  ?></td>                                        
                                        <td><?php echo number_format($taxable, 2) ?></td>                                        
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->cgst_per . '%)</span>' ?></td>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->sgst_per . '%)</span>' ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                    <td><?php echo $tqty ?></td>
                                    <td><?php echo number_format($trate,2) ?></td>
                                    <td><?php echo number_format($ttaxable, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo number_format($tcgst, 2) ?></td>
                                    <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
                                </tr>
                            <?php } ?>
                            <tr style="border: 1px solid #999999">
                                <td colspan="10" style="border: 1px solid #999999;font-size: 14px;">
                                    <b>Declaration:</b> We declare that this invoice shows actual price of the goods described & that all particulars are true & correct.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <?php foreach ($sale_payment as $payment) {
                        if ($payment->idpayment_mode == 400 && $sale->bfl_upload == 0) { ?>
                            <input type="hidden" value="<?php echo $payment->transaction_id ?>" id="do_id">
                        <?php }
                    } ?>

                <div class="row" style="border: 1px solid #999999">
                    <div class="col-md-9 col-xs-9 col-xs-9" style="border-right: 1px solid #c3c3c3; padding: auto 5px"> 
                        <div style="line-height: 20px; color: #666666;">
                            <i class="fa fa-circle" style="font-size: 10px; opacity: 0.4;"></i> &nbsp;  <span style="color: #000;">Terms & Conditions</span><br>
                            <div style="font-size: 12px">
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
            </div>
            <div class="col-md-10">
                <center><i>Subject to Kolhapur<?php // echo $sale->branch_district ?> jurisdiction</i></center>
            </div>
            <div class="clearfix"></div>
            <?php if($sale->customer_gst != '' && $discount_amt > 0 && $is_mop > 0){ ?><hr>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> CREDIT NOTE</h4></center>
                <div class="col-md-10">
                    <b>Branch: &nbsp; <?php echo $sale->branch_name ?></b><br>
                    <b>Address: </b> <?php echo $sale->branch_address; ?><br>
                    <b>Contact:</b> <?php echo $sale->branch_contact; ?> &nbsp; &nbsp;
                    <b>GSTIN:</b> <?php echo $sale->branch_gstno; ?>
                </div><div class="clearfix"></div>
                
                <div class="col-md-7 col-xs-7">
                    <b>CN No.: &nbsp;<?php echo sprintf('%07d', $sale->id_sale) ?></b>
                </div>
                <div class="pull-right">
                    <b>Date:</b> &nbsp;<?php echo date('d-M-Y h:i A', strtotime($sale->invoice_date)) ?>
                </div><div class="clearfix"></div><br>                
                
                <div style="border: 1px solid #999999;  line-height: 20px">
                    <div class="col-md-8">
                        <div class="col-md-6"> 
                            Customer: &nbsp;                  
                            <span style="text-transform: uppercase" ><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></span>
                        </div>
                        <div class="col-md-4">
                            Contact No.:   &nbsp;                          
                            <?php echo $sale->customer_contact; ?>
                        </div><div class="clearfix"></div>
                    </div>                    
                    <div class="col-md-8">
                        <div class="col-md-10">
                            Address:     &nbsp;                    
                            <?php echo $sale->customer_address.', '.$sale->customer_pincode; ?>
                        </div><div class="clearfix"></div>                        
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            GSTIN: &nbsp;                      
                            <span style="text-transform: capitalize"><?php echo $sale->customer_gst; ?></span>
                        </div><div class="clearfix"></div>
                    </div>
                    <?php if ($gst_type == 1) { 
                        $cal = ($product->igst_per + 100) / 100;
                        $taxable = $discount_amt / $cal;
                        $igstamt = $discount_amt - $taxable; ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Discount</th>
                            <th>IGST</th>
                            <th>Total</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td><?php echo number_format($taxable,2) ?></td>
                                <td><?php echo number_format($igstamt,2) ?></td>
                                <td><?php echo $discount_amt ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } elseif ($gst_type == 0) {
                        $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                        $taxable = $discount_amt / $cal;
                        $cgst = $discount_amt - $taxable;
                        $cgstamt = $cgst / 2;?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Discount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>Total</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td><?php echo number_format($taxable,2) ?></td>
                                <td><?php echo number_format($cgstamt,2) ?></td>
                                <td><?php echo number_format($cgstamt,2) ?></td>
                                <td><?php echo $discount_amt ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } ?>                    
                    
                </div>
            <?php } ?>
            <center><i>This is computer generated invoice no signature required.</i></center>
        </div>
    </div>
<?php } ?>

</body>
</html>