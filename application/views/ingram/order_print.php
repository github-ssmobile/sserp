<?php include __DIR__.'../../sale/header_invoice.php'; ?>
<style>
@page {
    size: A4;
    margin-left: 30px;
    margin-right: 30px;
    padding: 20px;
    display:inline-block; 
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
                    <center><h4  style="color: #000;font-family: K2D; margin: 0">Advanced Booking Receipt</h4></center>
                    
                    Booking Ref No.: &nbsp;<b><?php echo $sale->token_uid ?></b>
                    <input type="hidden" value="<?php echo $sale->id_sale_token ?>" id="inv_id">
                    <input type="hidden" value="<?php echo $sale->idbranch ?>" id="idbranch">
                    <input type="hidden" value="<?php echo $sale->customer_contact ?>" id="customer_contact">
                    <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->date)) ?>" id="inv_date">
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y h:i A', strtotime($sale->date)) ?>
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
                        &nbsp; <span style="text-transform: uppercase" ><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></span><br>
                        &nbsp; Address: <span style="text-transform: capitalize" ><?php echo $sale->customer_address.', '.$sale->customer_state.'-'.$sale->customer_pincode; ?></span><br>
                        &nbsp; Mobile: <?php echo $sale->customer_contact;
                        if ($sale->customer_gst) { ?><br>
                        &nbsp; GST No.: <?php echo $sale->customer_gst;
                        } $gst_type = $sale->gst_type; 
                         ?>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #999999">
                    <table id="model_data" class="table table-bordered table-condensed table-hover" style="margin: 0;">
                        <thead class="text-center">
                        <th><center>SN</center></th>
                        <th class="col-md-5 col-xs-5"><center>Product</center></th>
                        
                        <th>HSN</th>
                        <th><center>Qty</center></th>                        
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
                                   $tqty += $product->qty;
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->product_name; ?></td>                                        
                                        <td><?php echo $product->hsn ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo $total_amount ?></td>
                                    </tr>                                   
                                    <?php } ?>
                                    <tr>
                                        <td colspan="3"><span class="pull-right">Gross Total &nbsp; &nbsp; </span></td>
                                        <td><?php echo $tqty ?></td>                                        
                                        <td><?php echo moneyFormatIndia($ttotal_amount); ?></td>
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
                            
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right">
                        <br><br><br><br><br><br>
                        <center>Authorized Signatory</center>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
<script>
//   if ($('#do_id').val()) {
//       window.getPDF();
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