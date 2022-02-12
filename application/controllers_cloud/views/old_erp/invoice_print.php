<?php include 'header_invoice.php'; ?>
<style>
/*@page {
    margin-left: 30px;
    margin-right: 30px;
    padding: 30px;
    display:inline-block; 
}
@media print {
    html, body {
        width: 220mm;
        height: 297mm;
    }
}*/

@page {
  size: A4;
  margin: 0;
}
@media print {
  html, body {
    width: 210mm;
    height: 297mm;
  }
  /* ... the rest of the rules ... */
}
</style>
<script src="<?php echo site_url(); ?>assets/js/jspdf.min.js"></script>
<script src="<?php echo site_url(); ?>assets/js/html2canvas.js"></script>
<script>
window.print();
//window.setTimeout(function () {
//    $("#alert-dismiss").fadeTo(500, 0).slideUp(500, function () {
//        $(this).remove();
//    });
//}, 5000);
</script>
<?php $sale=$sale_data[0]; ?>
    <div style="font-family: K2D; font-size: 13px;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                    <div class="col-md-4 col-xs-4" style="padding: 0;">
                        <img class="hovereffect" height="85" src="<?php echo base_url()?><?php echo $printhead->company_logo?>" alt="SS Mobile"/>
                    </div>
                    <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px;">
                        <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0"><?php echo $printhead->company_name?></h3>
                        <?php echo $printhead->company_address?>
                    </div>
                </div>
                <div class="row justify-content-end" style="padding: 3px 10px;">
                    <center><h4  style="color: #000;font-family: K2D; margin: 0">TAX INVOICE</h4></center>
                    Invoice No.: &nbsp;<b><?php echo $sale->invoice_no ?></b>
                    <input type="hidden" value="<?php echo $sale->invoice_no ?>" id="inv_no">
                    <input type="hidden" value="<?php echo $sale->id_old_sale ?>" id="inv_id">
                    <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->invoice_date)) ?>" id="inv_date">
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y', strtotime($sale->invoice_date)) ?>
                    </div><div class="clearfix"></div>
                </div>
                <div class="row" style="border: 1px solid #999999;">
                    <div class="col-md-7 col-xs-7">
                        <b>Branch: &nbsp; <?php echo $printhead->branch_name ?></b><br>
                        <b>Address: </b><?php echo $printhead->branch_address; ?><br>
                        <b>GST No:</b> <?php echo $printhead->branch_gstno; ?><br>
                        <b>Contact:</b> <?php echo $printhead->branch_contact; ?><br>
                        <b>Sales Promoter:</b> <?php echo $sale->promoter_name; ?>
                    </div>
                    <div class="col-md-5 col-xs-5">
                        <b> Buyer,</b><br>
                        &nbsp; <span style="text-transform: uppercase" ><?php echo $sale->customer_name ?></span><br>                        
                        &nbsp; Mobile: <?php echo $sale->customer_mobile;
                        if ($sale->customer_gst_no != '') { ?><br>
                        &nbsp; GST No.: <?php echo $sale->customer_gst_no;
                        } $gst_type = $sale->igst; 
                        ?>
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
                        <?php if ($gst_type != 0) { ?>
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
                            if ($gst_type != 0) { // igst
                                foreach ($sale_data as $product) {
                                    $total_amount=$product->total_amount_per_qty;
                                    $ttotal_amount += $total_amount;
                                    $cal = ($product->gst_rate + 100) / 100;
                                    $taxable = $total_amount / $cal;
                                    $igstamt = $total_amount - $taxable;
                                    $tigst += $igstamt;
                                    $tqty += 1;
//                                    $trate += $product->price;
//                                    $tdiscount += $product->discount_amt;
                                    $ttaxable += $taxable;
                                    $rate = $taxable / 1;
                                    $trate += $rate;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; ?></td>
                                        <td>
                                            <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                                                <?php echo $sale->serial_no;  ?>
                                            <?php }else{ ?>
                                                <?php echo $sale->imei_1_no; ?>
                                            <?php } ?>                                                
                                        </td>
                                        <td><?php echo $product->hsn_code ?></td>
                                        <td>1</td>
                                        <td><?php echo number_format($rate,2) ?></td>
                                        <!--<td><?php // echo $product->price ?></td>-->
                                        <!--<td><?php // echo $product->basic ?></td>-->
                                        <!--<td><?php // echo $product->discount_amt ?></td>-->
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <td><?php echo number_format($igstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . $product->gst_rate . '%)</span>' ?></td>
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
                                        foreach ($sale_data as $product) {
                                            $total_amount=$product->total_amount_per_qty;
                                            $ttotal_amount += $total_amount;
                                            $cal = ($product->gst_rate + 100) / 100;
                                            $taxable = $total_amount / $cal;
                                            $cgst = $total_amount - $taxable;
                                            $cgstamt = $cgst / 2;
                                            $tcgst += $cgstamt;
                                            $tqty += 1;
//                                            $tdiscount += $product->discount_amt;
                                            $ttaxable += $taxable;
                                            $rate = $taxable / 1;
                                            $trate += $rate;
                                        ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; ?></td>
                                        <td>
                                            <?php if($sale->imei_1_no=="" || $sale->imei_1_no=="'"){ ?>
                                                <?php echo $sale->serial_no;  ?>
                                            <?php }else{ ?>
                                                    <?php echo $sale->imei_1_no; ?>
                                            <?php } ?>                                                
                                        </td>
                                        <td><?php echo $product->hsn_code ?></td>
                                        <td>1</td>
                                        <td><?php echo number_format($rate,2)  ?></td>
                                        <!--<td><?php // echo $product->price ?></td>-->
                                        <!--<td><?php // echo $product->basic  ?></td>-->
                                        <!--<td><?php // echo $product->discount_amt ?></td>-->
                                        <td><?php echo number_format($taxable, 2) ?></td>
                                        <?php // if($gst_type==1){  ?>
                                        <!--<td><?php // echo number_format($igstamt,2).'<span class="pull-right" style="font-size:12px">('.$product->igst_per.'%)</span>'  ?></td>-->
                                        <?php // }else{  ?>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . ($product->gst_rate/2) . '%)</span>' ?></td>
                                        <td><?php echo number_format($cgstamt, 2) . '<span class="pull-right" style="font-size:11px">(' . ($product->gst_rate/2) . '%)</span>' ?></td>
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
            </div><center><i>Subject to <?php echo $printhead->branch_district ?> jurisdiction</i></center>
            <?php $hidden_discount=0;
                        foreach ($sale_data as $product) {  
                            $hidden_discount=$hidden_discount+$product->hidden_discount;
                        }
             if($sale->customer_gst_no != '' && $hidden_discount > 0){ ?><hr>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> CREDIT NOTE</h4></center>
                <div class="col-md-12 col-xs-12">
                    <b>Branch: &nbsp; <?php echo $printhead->branch_name ?></b><br>
                    <b>Address: </b> <?php echo $printhead->branch_address; ?><br>
                    <b>Contact:</b> <?php echo $printhead->branch_contact; ?> &nbsp; &nbsp;
                    <b>GSTIN:</b> <?php echo $printhead->branch_gstno; ?>
                </div><div class="clearfix"></div>
                <div class="col-md-7 col-xs-7">
                    <b>CN No.: &nbsp;<?php echo  $sale->hidden_discount_credit_note_no ?></b>
                </div>
                <div class="pull-right">
                    <b>Date:</b> &nbsp;<?php echo date('d-M-Y', strtotime($sale->invoice_date)) ?>
                </div><div class="clearfix"></div>
                <div style="border: 1px solid #f00c0c; padding: 5px 10px; line-height: 30px">
                    <div class="col-md-2 col-sm-2 col-xs-3">Customer:</div>
                    <div class="col-md-5 col-sm-5 col-xs-5" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $sale->customer_name ?></span>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2">Contact No.:</div>
                    <div class="col-md-3 col-sm-3 col-xs-2" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $sale->customer_mobile; ?>
                    </div><div class="clearfix"></div>
                   
                    <div class="col-md-2 col-sm-2 col-xs-3">GSTIN:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: capitalize"><?php echo $sale->customer_gst_no; ?></span>
                    </div><div class="clearfix"></div>
                    <?php 
                    
                    if ($gst_type != 0) { 
                        $cal = ($sale->gst_rate + 100) / 100;
                        $taxable = $hidden_discount / $cal;
                        $igstamt = $hidden_discount - $taxable; ?>
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
                                <td><?php echo $hidden_discount ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else {
                        $cal = ($sale->gst_rate + 100) / 100;
                        $taxable = $hidden_discount / $cal;
                        $cgst = $hidden_discount - $taxable;
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
                                <td><?php echo $hidden_discount ?></td>
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
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
<script>
   if ($('#do_id').val()) {
       window.getPDF();
   } else {
//       window.print();
   }

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