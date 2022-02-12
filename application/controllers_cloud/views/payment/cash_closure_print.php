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
<?php if($cash_closure){?>
    <div style="font-family: K2D;">
        <div id="printTable" class="print_invoice"><br>
            <div class="container-fluid" style="font-size: 14px; padding: 0px 15px; background-color: #fff; line-height: 23px">
                <div class="col-md-4 col-xs-4" style="padding: 0;">
                    <img class="hovereffect" height="75" src="<?php echo base_url() ?><?php echo $cash_closure->company_logo?>" alt="SS Mobile"/>
                </div>
                <div class="col-md-8 col-xs-8 text-center" style="padding: 0; padding-top: 15px; padding-left: 10px; ">
                    <h3 style="color: #000; font-family: K2D; font-size: 23px; margin: 0"><?php echo $cash_closure->company_name?></h3>
                    <?php echo $cash_closure->company_address?>
                </div>
                <div class="clearfix" style="border-bottom: 1px solid #cbcbcb "></div>
                <center><h4 style="color: #000;font-family: K2D; margin: 5px"><i class="pe pe-7s-news-paper"></i> Cash Closure Denomination</h4></center>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-2 col-xs-2 "><b>Branch</b></div>
                    <div class="col-md-4 col-xs-4 "><?php echo $cash_closure->branch_name ?></div>
                    <div class="col-md-2 col-xs-2"><b> Date</b></div>
                    <div class="col-md-4 col-xs-4 "><?php echo date('Y-m-d h:i:sa', strtotime($cash_closure->entry_time)); ?></div>
                    <div class="clearfix"></div>
                    <!--<div class="col-md-3 col-xs-3"><b>Closure Amount</b></div>-->
                    <!--<div class="col-md-3 col-xs-3 "><?php echo $cash_closure->closure_cash ?></div>-->
                    <div class="col-md-2 col-xs-2"><b> Remark</b></div>
                    <div class="col-md-9 col-xs-9 "><?php echo $cash_closure->remark ?></div>
                </div>
                <div class="col-md-1"></div>
                <div class="clearfix"></div><br>
                <div class="col-md-1"></div>
                <div class="col-md-10 thumbnail" style="padding: 0">
                    <table class="table table-condensed" style="margin-bottom: 0">
                        <thead>
                            <th>Denomination</th>
                            <th></th>
                            <th>Qty</th>
                            <th></th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            <?php foreach ($closure_denomination as $cdenomination){ ?>
                            <tr>
                                <td><?php echo $cdenomination->denomination; ?></td>
                                <td>x</td>
                                <td><?php echo $cdenomination->qty; ?></td>
                                <td>=</td>
                                <td><?php echo $cdenomination->cash; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <thead>
                            <th></th>
                            <th></th>
                            <th>Total Amount</th>
                            <th></th>
                            <th><?php echo moneyFormatIndia($cash_closure->closure_cash) ?></th>
                        </thead>
                    </table>
                </div>
                <div class="clearfix"></div>
                <center><i>This is computer generated receipt.</i></center>
            </div>
        </div>
    </div>
<?php } ?>
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap-select.js"></script>
</body>
</html>