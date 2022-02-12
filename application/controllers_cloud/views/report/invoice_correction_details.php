<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
    z-index: 9;
}
.grdark{
    background-color: #ade7ca;
}
</style>
<center><h3><span class="mdi mdi-clipboard-text fa-lg"></span> Invoice Correction Report </h3></center><div class="clearfix"></div><hr>
<div class="col-md-4 pull-right">
    <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $invoice_edit_details[0]->idsale ?>
</div><div class="clearfix"></div>           
<div class="col-md-4 pull-right">
    <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $invoice_edit_details[0]->inv_no; ?>
</div><div class="clearfix"></div>
<div class="col-md-4 pull-right">
    <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo date('d/m/Y', strtotime($invoice_edit_details[0]->invoice_date)); ?>
</div><div class="clearfix"></div>
<?php if(count($sale_product_edit) > 0){ ?>
<span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="mdi mdi-package-variant"></i> Sale Product Edit</span>
<div class="thumbnail" style="overflow: auto; padding: 0">
    <table class="table table-striped table-bordered table-responsive table-hover" style="margin: 0">
        <thead>
            <th>Product</th>
            <th>IMEI/SRNO</th>
            <th>Old Qty</th>
            <th>Qty</th>
            <th>Old Price</th>
            <th>Price</th>
            <th>Old Discount</th>
            <th>Discount</th>
            <th>Old Basic</th>
            <th>Basic</th>
            <th>Old Total Amount</th>
            <th>Total Amount</th>
        </thead>
        <tbody>
            <?php foreach($sale_product_edit as $edit){ ?>
            <tr>
                <td><?php echo $edit->product_name ?></td>
                <td><?php echo $edit->imei_no ?></td>
                <td><?php echo $edit->old_qty ?></td>
                <td><?php echo $edit->qty ?></td>
                <td><?php echo $edit->old_price ?></td>
                <td><?php echo $edit->price ?></td>
                <td><?php echo $edit->old_discount_amt ?></td>
                <td><?php echo $edit->discount_amt ?></td>
                <td><?php echo $edit->old_basic ?></td>
                <td><?php echo $edit->basic ?></td>
                <td><?php echo $edit->old_total_amount ?></td>
                <td><?php echo $edit->total_amount ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
<?php if(count($sale_payment_edit) > 0){ // echo '<pre>'. print_r($sale_payment_edit, 1) .'</pre>';  ?>
<span style="font-family: Kurale; font-size: 18px; color: #005bc0"><i class="mdi mdi-currency-inr"></i> Sale Payment Edit</span>
<div class="thumbnail" style="overflow: auto; padding: 0">
    <table class="table table-striped table-bordered table-responsive table-hover" style="margin: 0">
        <thead>
            <th>Entry Type</th>
            <th>Payment Mode</th>
            <th>Wrong Amount</th>
            <th>Amount</th>
            <th>Wrong Tranx Id</th>
            <th>Tranx Id</th>
            <th>Old approved by</th>
            <th>Approved by</th>
            <th>Wrong Buyback Product</th>
            <th>Buyback Product</th>
            <th>Customer bank</th>
            <th>Wrong customer bank</th>
            <th>Old buyback vendor</th>
            <th>buyback vendor</th>
            <th>old swipe card</th>
            <th>swipe card number</th>
            <th>old referral</th>
            <th>referral</th>
            <th>old finance promoter</th>
            <th>finance promoter</th>
            <th>old scheme code</th>
            <th>scheme code</th>
        </thead>
        <tbody>
            <?php foreach($sale_payment_edit as $payment_edit){ ?>
            <tr>
                <td><?php echo $payment_edit->entry_type ?></td>
                <td><?php echo $payment_edit->payment_mode.' '.$payment_edit->payment_head ?></td>
                <td><?php echo $payment_edit->old_amount ?></td>
                <td><?php echo $payment_edit->amount ?></td>
                <td><?php echo $payment_edit->old_transaction_id ?></td>
                <td><?php echo $payment_edit->transaction_id ?></td>
                <td><?php echo $payment_edit->old_approved_by ?></td>
                <td><?php echo $payment_edit->approved_by ?></td>
                <td><?php echo $payment_edit->old_product_model_name ?></td>
                <td><?php echo $payment_edit->product_model_name ?></td>
                <td><?php echo $payment_edit->old_customer_bank_name ?></td>
                <td><?php echo $payment_edit->customer_bank_name ?></td>
                <td><?php echo $payment_edit->old_buyback_vendor_name ?></td>
                <td><?php echo $payment_edit->buyback_vendor_name ?></td>
                <td><?php echo $payment_edit->old_swipe_card_number ?></td>
                <td><?php echo $payment_edit->swipe_card_number ?></td>
                <td><?php echo $payment_edit->old_referral_name ?></td>
                <td><?php echo $payment_edit->referral_name ?></td>
                <td><?php echo $payment_edit->old_finance_promoter_name ?></td>
                <td><?php echo $payment_edit->finance_promoter_name ?></td>
                <td><?php echo $payment_edit->old_scheme_code ?></td>
                <td><?php echo $payment_edit->scheme_code ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>
<?php include __DIR__ . '../../footer.php'; ?>