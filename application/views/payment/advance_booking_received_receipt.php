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
<?php if($payment_received_data){?>
    <div style="font-family: K2D;">
        <div id="printTable" class="print_invoice"><br>
            <!--<div class="container-fluid" style="font-size: 16px; padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">-->
            <div class="container-fluid" style="font-size: 14px; padding: 0px 15px; background-color: #fff; line-height: 23px">
                <div class="col-md-4 col-xs-4" style="padding: 0;">
                    <img class="hovereffect" height="85" src="<?php echo base_url()?><?php echo $payment_received_data->company_logo?>" alt="SS Mobile"/>
                </div>
                <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px;">
                    <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0"><?php echo $payment_received_data->company_name?></h3>
                    <?php echo $payment_received_data->company_address?>
                </div>
                <div class="clearfix" style="border-bottom: 1px solid #cbcbcb"></div>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> Payment Receipt</h4></center>
                <div class="col-md-12 col-xs-12">
                    <b>Branch: &nbsp; <?php echo $payment_received_data->branch_name ?></b><br>
                    <b>Address: </b> <?php echo $payment_received_data->branch_address; ?><br>
                    <b>Contact:</b> <?php echo $payment_received_data->branch_contact; ?> &nbsp; &nbsp;
                    <b>GSTIN:</b> <?php echo $payment_received_data->branch_gstno; ?>
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($payment_received_data->entry_time)) ?>
                    </div><div class="clearfix"></div>
                </div><div class="clearfix"></div>
                <div style="border: 1px solid #f00c0c; padding: 10px; line-height: 30px">
                    <div class="col-md-7 col-xs-7">
                        Receipt No.: &nbsp;<b>AdvPay/<?php echo $payment_received_data->branch_code ?>/<?php echo sprintf('%05d', $payment_received_data->id_advance_payment_receive ) ?></b>
                    </div>
                    <div class="pull-right">
                        Sales Promoter: &nbsp;<?php echo $payment_received_data->user_name; ?>
                    </div><div class="clearfix"></div>
<!--                    <div class="col-md-2 col-sm-2 col-xs-3">Invoice No.:</div>
                    <div class="col-md-5 col-sm-5 col-xs-5" style="border-bottom: 1px dashed #f00c0c">
                        <span><?php // echo $payment_received_data->inv_no ?></span>
                    </div>-->
                    <div class="col-md-2 col-sm-3 col-xs-3">Product:</div>
                    <div class="col-md-10 col-sm-9 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $payment_received_data->mfull_name; ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Customer:</div>
                    <div class="col-md-5 col-sm-5 col-xs-5" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $payment_received_data->cust_fname.' '.$payment_received_data->cust_lname ?></span>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2">Contact No.:</div>
                    <div class="col-md-3 col-sm-3 col-xs-2" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $payment_received_data->cust_contact; ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Address:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $payment_received_data->cust_address; ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Payment Mode:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $payment_received_data->payment_mode.' '.$payment_received_data->payment_head;
                        if($payment_received_data->tranxid_type != NULL){ echo ' ['.$payment_received_data->transaction_id.']'; } ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Amount In Words:</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: capitalize" ><?php echo getIndianCurrency($payment_received_data->amount); ?></span>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-1 col-xs-1 col-xs-1 col-md-offset-1 col-xs-offset-1 col-sm-offset-1" style="border: 1px solid #f00c0c;">
                        <center><h4><i class="fa fa-rupee"></i></h4></center>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3" style="border: 1px solid #f00c0c;">
                        <h4><?php echo $payment_received_data->amount; ?></h4>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right"><br>
                        <center>Authorized Signatory</center>
                    </div><div class="clearfix"></div>
                </div>
                <center><i>This is computer generated receipt.</i></center>
            </div>
        </div>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
</body>
</html>