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
<?php 

foreach ($outward_data as $sale) { ?>
    <div style="font-family: K2D; font-size: 13px;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                    <div class="col-md-2 col-xs-2" style="padding: 0;">
                        <img class="hovereffect" height="85" src="<?php echo base_url()?><?php echo $sale->company_logo?>" alt="SS Mobile"/>
                    </div>
                    <div class="col-md-7 col-xs-7 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px;">
                        <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0"><?php echo $sale->company_name?></h3>
                        <?php echo $sale->company_address?>

                    </div>

                    <div class="col-md-3 col-xs-3">
                        <img id='barcode' 
                        src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $einv_data['ewb_signedQRCode'];?>&amp;size=100x100" title="QRCODE" width="250" height="190" />
                    </div>
                    <div class="clearfix"></div><hr>
                    <center><h1 style="color: #000; font-family: K2D; font-size: 14px; margin-left: 100px;width:auto;"><?php echo 'IRN No :'. $einv_data['ewb_irnno'];?></h1></center>
                </div>
                <div class="row justify-content-end" style="padding: 3px 10px;">
                    <center><h4  style="color: #000;font-family: K2D; margin: 0">TAX INVOICE</h4></center>
                    Invoice No.: &nbsp;<b><?php echo $sale->id_outward ?></b>
                    <input type="hidden" value="<?php echo $sale->id_outward ?>" id="id_outward">
                    <input type="hidden" value="<?php echo $sale->id_outward ?>" id="inv_id">
                    <input type="hidden" value="<?php echo $sale->idbranch ?>" id="idbranch">
                    <input type="hidden" value="<?php echo $sale->branch_contact ?>" id="branch_contact">
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
                        &nbsp; <span style="text-transform: uppercase" ><?php echo $sale->buyer_name ?></span><br>
                        &nbsp; Address: <span style="text-transform: capitalize" ><?php echo $sale->buyer_add.', '.$sale->buyer_state.'-'.$sale->buyer_pin; ?></span><br>
                        &nbsp; Mobile: <?php echo $sale->buyer_contact;
                        if ($sale->buyer_gst) { ?><br>
                        &nbsp; GST No.: <?php echo $sale->buyer_gst;
                    }
                    $gst_type = $sale->gst_type; 
                     //$gst_type = 1; 
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
                        
                            if ($gst_type != 1) { // igst
                             $i=1;
                             $t_rate=0; $t_amt=0;$tqty=0;$tt_amt=0;$totala=0;$t_aamt=0;$t_gstamt=0;
                             foreach ($sale_product as $product) {
                                 $price=0;
                                 $array = explode(',', $product->imei); 
                                 $amt=$product->price;
                                 $tax_rate=($product->cgst_per*2);
                                 if($einv_data['bill_type']=='3'){
                                     if($product->is_mop){
                                        $is_mop += 1;
                                        if($product->total_amount > $product->mop){
                                            $amt=$product->total_amount;
                                        }else{
                                            if($product->idskutype == 4){
                                                $amt=$product->mop * $product->qty;
                                            }else{
                                                $amt=$product->mop;
                                            }
                                            $discount_amt += $product->mop - $product->total_amount;
                                        }
                                    }else{
                                        $amt=$product->total_amount;
                                        $is_mop += 0;
                                    }
                                    //  $amt=$product->total_amount;
                                    $amount = $amt ;
                                }else{
                                    if($product->idgodown == 2){
                                $cal = 50/100;  //// landing minus 50% for demo stock -  inter state transfer
                            }else{
                                $cal = 2 /100;   //// landing minus 2% for inter state transfer
                            }
                            $amount = $amt - ($cal*$amt);
                        }
                            //$price = $amt - $taxble;
                        
                        $rate= round(($amount*100)/(100+$tax_rate),2);
                        $t_rate=$t_rate+$rate;
                        $gst_amt = (($rate*$tax_rate) / 100);
                        $basic_rate = ($amount) - $gst_amt;
                        
                        $taxable =  round((($rate)*($product->qty)),2);
                        $tqty=$tqty+$product->qty;
                        $t_aamt=$t_aamt+$product->price;
                        $tt_amt=$tt_amt+$taxable;
                        $gst_amt = round((($taxable*$tax_rate) / 100),2);
                        $t_amt=round($taxable+$gst_amt,2);
                        $totala=$totala=+$t_amt;
                        $t_gstamt=$t_gstamt=+$gst_amt;
                        ?>
                        <tr>
                           <td><?php echo $i++; ?></td>
                           <td><?php echo $product->full_name; ?></td>
                           <td><?php $j=0;$im=""; foreach($array as $imei){
                            if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; } $j++; } echo $im;?></td>
                            <td><?php //echo $hsn_data['hsn'] ?></td>
                            <td><?php echo $product->qty ?></td>
                            <td><?php echo $taxable ?></td>

                            <td><?php echo $taxable ?></td>
                            <td><?php echo number_format(($gst_amt/2), 2) . '<span class="pull-right" style="font-size:11px">(' . ($tax_rate/2) . '%)</span>' ?></td>
                            
                            <td><?php echo number_format(($gst_amt/2), 2) . '<span class="pull-right" style="font-size:11px">(' . ($tax_rate/2) . '%)</span>' ?></td>
                            <td><?php echo number_format($t_amt,2) ?></td>
                            
                        </tr>

                    <?php } ?>
                    <tr>
                        <td colspan="4"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                        <td><?php echo $tqty ?></td>
                        <td><?php echo number_format($t_rate,2) ?></td>
                        <td><?php echo number_format($t_rate, 2) ?></td>
                        <td><?php echo number_format(($t_gstamt/2), 2) ?></td>
                        <td><?php echo number_format(($t_gstamt/2), 2) ?></td>
                        
                        <td><?php echo number_format($totala,2); ?></td>
                    </tr>
                    <?php 
                } else {
                    $i=1;
                    $t_rate=0; $t_amt=0;$tqty=0;$tt_amt=0;$totala=0;$t_aamt=0;$t_gstamt=0;
                    foreach ($sale_product as $product) {
                     $price=0;
                     $array = explode(',', $product->imei); 
                     $amt=$product->price;
                     $tax_rate=($product->cgst_per*2);
                     if($einv_data['bill_type']=='3'){
                      if($product->is_mop){
                        $is_mop += 1;
                        if($product->total_amount > $product->mop){
                            $amt=$product->total_amount;
                        }else{
                            if($product->idskutype == 4){
                                $amt=$product->mop * $product->qty;
                            }else{
                                $amt=$product->mop;
                            }
                            $discount_amt += $product->mop - $product->total_amount;
                        }
                    }else{
                        $amt=$product->total_amount;
                        $is_mop += 0;
                    }
                }else{
                    if($product->idgodown == 2){
                                $cal = 50/100;  //// landing minus 50% for demo stock -  inter state transfer
                            }else{
                                $cal = 2 /100;   //// landing minus 2% for inter state transfer
                            }
                            $amount = $amt - ($cal*$amt);
                        }
                            //$price = $amt - $taxble;
                        
                        $rate= round(($amount*100)/(100+$tax_rate),2);
                        $t_rate=$t_rate+$rate;
                        $gst_amt = (($rate*$tax_rate) / 100);
                        $basic_rate = ($amount) - $gst_amt;
                        
                        $taxable =  round((($rate)*($product->qty)),2);
                        $tqty=$tqty+$product->qty;
                        $t_aamt=$t_aamt+$product->price;
                        $tt_amt=$tt_amt+$taxable;
                        $gst_amt = round((($taxable*$tax_rate) / 100),2);
                        $t_amt=round($taxable+$gst_amt,2);
                        $totala=$totala=+$t_amt;
                        $t_gstamt=$t_gstamt=+$gst_amt;
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $product->full_name; ?></td>
                            <td><?php $j=0;$im=""; foreach($array as $imei){
                                if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; } $j++; } echo $im;?></td>
                                <td><?php //echo $hsn_data['hsn'] ?></td>
                                <td><?php echo $product->qty ?></td>
                                <td><?php echo $taxable ?></td>

                                <td><?php echo $taxable ?></td>
                                

                                <td><?php echo number_format($gst_amt, 2) . '<span class="pull-right" style="font-size:11px">(' . $tax_rate . '%)</span>' ?></td>

                                <td><?php echo number_format($t_amt,2) ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="4"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                            <td><?php echo $tqty ?></td>
                            <td><?php echo number_format($t_rate,2) ?></td>
                            <td><?php echo number_format($t_rate, 2) ?></td>
                            <td><?php echo number_format($t_gstamt, 2) ?></td>
                            
                            <td><?php echo number_format($totala,2); ?></td>
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
    </div><center><i>Subject to Kolhapur<?php // echo $sale->branch_district ?> jurisdiction</i></center>
    <?php 
    if($einv_data['bill_type']=='3'){ 
        if($sale->buyer_gst != '' && $discount_amt > 0 && $is_mop > 0){ ?><hr>
        <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> CREDIT NOTE</h4></center>
        <div class="col-md-12 col-xs-12">
            <b>Branch: &nbsp; <?php echo $sale->branch_name ?></b><br>
            <b>Address: </b> <?php echo $sale->branch_address; ?><br>
            <b>Contact:</b> <?php echo $sale->branch_contact; ?> &nbsp; &nbsp;
            <b>GSTIN:</b> <?php echo $sale->branch_gstno; ?>
        </div><div class="clearfix"></div>
        <div class="col-md-7 col-xs-7">
            <b>CN No.: &nbsp;<?php echo sprintf('%07d', $sale->id_outward) ?></b>
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
                <?php echo $sale->branch_contact; ?>
            </div><div class="clearfix"></div>
            <div class="col-md-2 col-sm-2 col-xs-3">Address:</div>
            <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                <?php echo $sale->buyer_add.', '.$sale->buyer_pin; ?>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-3">GSTIN:</div>
            <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                <span style="text-transform: capitalize"><?php echo $sale->buyer_gst; ?></span>
            </div><div class="clearfix"></div>
            <?php if ($gst_type == 1) { 
                $cal = ($product['igst_per'] + 100) / 100;
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
    <?php } 
}
?>
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
           fd.append('id_outward', $('#id_outward').val());
           fd.append('do_id', $('#do_id').val());
           fd.append('inv_date', $('#inv_date').val());
           fd.append('idbranch', $('#idbranch').val());
           fd.append('branch_contact', $('#branch_contact').val());
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