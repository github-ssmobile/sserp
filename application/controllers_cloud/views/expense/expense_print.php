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
<?php if($expense_data){?>
    <div style="font-family: K2D;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="font-size: 14px; padding: 0px 15px; background-color: #fff; line-height: 23px">
                <div class="col-md-4 col-xs-4" style="padding: 0;">
                    <img class="hovereffect" height="75" src="<?php echo base_url() ?><?php echo $expense_data->company_logo?>" alt="SS Mobile"/>
                </div>
                <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px; ">
                    <h3 style="color: #000; font-family: K2D; font-size: 23px; margin: 0"><?php echo $expense_data->company_name?></h3>
                    <?php echo $expense_data->company_address?>
                </div>
                
                <div class="clearfix" style="border-bottom: 1px solid #cbcbcb "></div>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> Expense Receipt</h4></center>
                <div class="col-md-7 col-xs-7">
                        Receipt No.: &nbsp;<b><?php echo sprintf('%07d', $expense_data->id_expense) ?></b>
                    </div>
                    <div class="pull-right">
                        Date: &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($expense_data->entry_time)) ?>
                    </div><div class="clearfix"></div>
                <div style="border: 1px solid #f00c0c; padding: 10px; line-height: 30px">
                    
                    <div class="col-md-2 col-sm-2 col-xs-3">Branch</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $expense_data->branch_name.' - '.$expense_data->branch_contact ?></span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Address</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $expense_data->branch_address?></span>
                    </div>
                     <div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Created By</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: uppercase" ><?php echo $expense_data->user_name?></span>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Expense Type</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <?php echo $expense_data->expense_type; ?>
                    </div><div class="clearfix"></div>
                    <div class="col-md-2 col-sm-2 col-xs-3">Amount In Words</div>
                    <div class="col-md-10 col-sm-10 col-xs-9" style="border-bottom: 1px dashed #f00c0c">
                        <span style="text-transform: capitalize" ><?php echo getIndianCurrency($expense_data->approve_expense_amount); ?></span>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-1 col-xs-1 col-xs-1 col-md-offset-1 col-xs-offset-1 col-sm-offset-1" style="border: 1px solid #f00c0c;">
                        <center><h4><i class="fa fa-rupee"></i></h4></center>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3" style="border: 1px solid #f00c0c;">
                        <h4><?php echo $expense_data->approve_expense_amount; ?> /-</h4>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right"><br>
                        <center>Authorized Signatory</center>
                    </div><div class="clearfix"></div>
                </div>
                <center><i>This is computer generated receipt.</i></center>
            </div>
        </div>
        <?php if($idredirect == 1){?>
        <a class="btn btn-primary fix" style=" position: fixed;bottom: 80px;right: 20px; " href="<?php echo base_url()?>Expense/expense">Back To Expense</a>
        <?php }?>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
</body>
</html>