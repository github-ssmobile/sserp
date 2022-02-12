<?php include 'header_invoice.php'; ?>
<style>
@page {
    size: A4;
    padding: 20px;
    display:inline-block; 
    margin: 10mm 10mm 10mm 10mm;
}
@media print {
    html, body {
        width: 210mm;
        height: 297mm;
    }
}
</style>
<script src="<?php echo site_url(); ?>assets/js/jspdf.min.js"></script>
<script src="<?php echo site_url(); ?>assets/js/html2canvas.js"></script>
<script>
//window.print();
//window.setTimeout(function () {
//    $("#alert-dismiss").fadeTo(500, 0).slideUp(500, function () {
//        $(this).remove();
//    });
//}, 5000);
</script>
<?php foreach ($sale_data as $sale) { ?>
    <div style="font-family: K2D; font-size: 13px;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                    <div class="col-md-4 col-xs-4" style="padding: 0;">
                        <img class="hovereffect" height="85" src="<?php echo base_url()?><?php echo $sale->company_logo?>" alt="SS Mobile"/>
                    </div>
                    <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px;">
                        <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0"><?php echo $sale->company_name?></h3>
                        <?php echo $sale->company_address?>
                    </div>
                </div>
                <div class="row justify-content-end" style="padding: 3px 10px;">
                    <center><h4  style="color: #000;font-family: K2D; margin: 0">TAX INVOICE</h4></center>
                    Invoice No.: &nbsp;<b><?php echo $sale->inv_no ?></b>
                    <input type="hidden" value="<?php echo $sale->inv_no ?>" id="inv_no">
                    <input type="hidden" value="<?php echo $sale->id_sale ?>" id="inv_id">
                    <input type="hidden" value="<?php echo $sale->id_branch ?>" id="idbranch">
                    <input type="hidden" value="<?php echo $sale->customer_contact ?>" id="customer_contact">
                    <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->invoice_date)) ?>" id="inv_date">
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y h:i A', strtotime($sale->invoice_date)) ?>
                    </div><div class="clearfix"></div>
                </div>
                <div class="row" style="border: 1px solid #999999;">
                    <div class="col-md-7 col-xs-7">
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
                        if ($sale->customer_gst) { ?><br>
                        &nbsp; GST No.: <?php echo $sale->customer_gst;
                        } $gst_type = $sale->gst_type; 
                        if(count($financer_of_idsale) > 0){
                            echo '<br><b>Finance Provider: </b>'.$financer_of_idsale[0]->payment_mode.' Finance ['.$financer_of_idsale[0]->transaction_id.']';
                        } ?>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #999999">
                    <table id="model_data" class="table table-bordered table-condensed table-hover" style="margin: 0;">
                        <thead class="text-center">
                        <th><center>SN</center></th>
                        <th class="col-md-5 col-xs-5"><center>Product</center></th>
                        <th><center>IMEI</center></th>
                        <th>HSN</th>
                        <th><center>Qty</center></th>
                        <th><center>Rate</center></th>
                        <!--<th><center>Disc</center></th>-->
                        <th><center>Taxable</center></th>
                        <?php if ($gst_type == 1) { ?>
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
//                            $tdiscount = 0;
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
//                                    $trate += $product->price;
//                                    $tdiscount += $product->discount_amt;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / $product->qty;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; if($product->activation_code){ echo '['.$product->activation_code.']'; }
                                    if($product->sales_return_type == 3){ echo '<br>DOA Return'; }
                                    elseif($product->idsale_product_for_doa != NULL){ echo '<br>DOA Replace'; } ?></td>
                                        <td><?php if($product->insurance_imei_no){ echo '['.$product->insurance_imei_no.']'; }else{ echo $product->imei_no; } ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo number_format($rate,2) ?></td>
                                        <!--<td><?php // echo $product->price ?></td>-->
                                        <!--<td><?php // echo $product->basic ?></td>-->
                                        <!--<td><?php // echo $product->discount_amt ?></td>-->
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <td><?php echo number_format($igstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->igst_per . '%)</span>' ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>
                                    <?php // } for ($i = count($sale_product); $i < 10; $i++) { ?>
                                    <!--<tr>-->
                                        <!--<td style="height: 50px"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>-->
                                    <!--</tr>-->
                                    <?php } ?>
                                    <tr>
                                        <td colspan="4"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                        <td><?php echo $tqty ?></td>
                                        <td><?php echo number_format($trate,2) ?></td>
                                        <!--<td><?php // echo $sale->discount_total ?></td>-->
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
//                                            $tdiscount += $product->discount_amt;
                                            $ttaxable += $taxable;
                                            $rate = $taxable / $product->qty;
                                            $trate += $rate;
                                        ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; if($product->activation_code){ echo '['.$product->activation_code.']'; }
                                    if($product->sales_return_type == 3){ echo ' DOA Return'; }
                                    elseif($product->idsale_product_for_doa != NULL){ echo ' DOA Replace'; }?></td>
                                        <td><?php if($product->insurance_imei_no){ echo '['.$product->insurance_imei_no.']'; }else{ echo $product->imei_no; } ?></td>
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo number_format($rate,2)  ?></td>
                                        <!--<td><?php // echo $product->price ?></td>-->
                                        <!--<td><?php // echo $product->basic  ?></td>-->
                                        <!--<td><?php // echo $product->discount_amt ?></td>-->
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
<!--                <div class="col-md-5 col-xs-5 col-md-offset-5" style="padding: 0">
                    <div class="thumbnail">
                        <table class="table table-striped table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin-bottom: 0">
                            <tbody>
                                    <?php foreach ($sale_payment as $payment) { ?>
                                    <tr>
                                        <td class="text-muted">Mode of Payment</td>
                                        <td><?php echo $payment->payment_mode ?></td>
                                        <td class="text-muted">Amount</td>
                                        <td><?php echo $payment->amount ?></td>
                                        <td class="text-muted"><?php echo $payment->tranxid_type ?></td>
                                        <td><?php echo $payment->transaction_id ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div><div class="clearfix"></div>-->
                <div class="row" style="border: 1px solid #999999">
                    <div class="col-md-9 col-xs-9 col-xs-9" style="border-right: 1px solid #c3c3c3; padding: auto 5px"> 
                        <div style="line-height: 20px; color: #666666;">
                            <i class="fa fa-circle" style="font-size: 10px; opacity: 0.4;"></i> &nbsp;  <span style="color: #000;">Terms & Conditions</span><br>
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
            </div><center><i>Subject to Kolhapur<?php // echo $sale->branch_district ?> jurisdiction</i></center>
            <?php if($sale->customer_gst != '' && $discount_amt > 0 && $is_mop > 0){ ?><hr>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> CREDIT NOTE</h4></center>
                <div class="col-md-12 col-xs-12">
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
                </div><div class="clearfix"></div>
                <div style="border: 1px solid #f00c0c; padding: 5px 10px; line-height: 30px">
                    <div class="col-md-2 col-sm-2 col-xs-3">Customer:</div>
                    <div class="col-md-5 col-sm-5 col-xs-5" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></span>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2">Contact No.:</div>
                    <div class="col-md-3 col-sm-3 col-xs-2" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $sale->customer_contact; ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Address:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $sale->customer_address.', '.$sale->customer_pincode; ?>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-3">GSTIN:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: capitalize"><?php echo $sale->customer_gst; ?></span>
                    </div><div class="clearfix"></div>
                    <?php if ($gst_type == 1) { 
                        $cal = ($product->igst_per + 100) / 100;
                        $taxable = $discount_amt / $cal;
                        $igstamt = $discount_amt - $taxable; ?>
                    <table class="table table-bordered">
                        <thead>
                            <th>Discount</th>
                            <th>IGST</th>
                            <th>Total</th>
                        </thead>
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
                        <thead>
                            <th>Discount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>Total</th>
                        </thead>
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
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right"><br>
                        <center>Authorized Signatory</center>
                    </div><div class="clearfix"></div>
                </div>
            <?php } ?>
            <!--<center><i>This is computer generated invoice.</i></center>-->
        </div>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
<script>
//   if ($('#do_id').val()) {
       window.getPDF();
//   } else {
//       window.print();
//   }

   function getPDF() {
       var HTML_Width = $(".print_invoice").width();
       var HTML_Height = $(".print_invoice").height();
       var top_left_margin = 15;
       var PDF_Width = HTML_Width + (top_left_margin * 2);
       var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
       var canvas_image_width = HTML_Width;
       var canvas_image_height = HTML_Height;

       var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

       html2canvas($(".print_invoice")[0], {allowTaint: true}).then(function (canvas) {
           canvas.getContext('2d');

           console.log(canvas.height + "  " + canvas.width);

           var imgData = canvas.toDataURL("image/jpeg", 1.0);
           var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
           pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);


           for (var i = 1; i <= totalPDFPages; i++) {
               pdf.addPage(PDF_Width, PDF_Height);
               pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
           }
           //pdf.save("pdf_1.pdf");  

           var file = pdf.output('blob');
           var fd = new FormData();     // To carry on your data  
           fd.append('mypdf', file);
           fd.append('inv_id', $('#inv_id').val());
           fd.append('inv_no', $('#inv_no').val());
           fd.append('do_id', $('#do_id').val());
           fd.append('inv_date', $('#inv_date').val());
           fd.append('idbranch', $('#idbranch').val());
           fd.append('customer_contact', $('#customer_contact').val());
           $('.img').hide();
           $.ajax({
               url: "<?php echo base_url() ?>Sale/save_pdf",
               data: fd,
               dataType: 'text',
               processData: false,
               contentType: false,
               type: 'POST',
               success: function (response) {
                   window.print();
               },
               error: function (jqXHR) {

               }
           });

           //window.print();                       
       });
   };
</script>
</body>
</html>