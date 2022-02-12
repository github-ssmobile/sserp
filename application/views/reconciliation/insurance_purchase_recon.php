<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
}
.alert_msg{
    width: 350px;
    background-color: #fff;
    border: 1px solid #00cccc;
    font-family: Kurale;
    font-size: 16px;
    text-align: center;
    opacity: 0.9;
    border-radius: 5px;
    position: fixed;
    bottom: 2%;
    left: 20%;
    padding: 10px;
    display: none;
    animation: blinker 1s linear infinite;
    z-index: 999;
}
@keyframes blinker {
  30% {
    opacity: 0;
  }
}
</style>
<script>
$(document).ready(function(){
    $(document).on("change", "#idvariant", function(event) {
        jQuery.ajax({
            url: "<?php echo base_url('Reconciliation/ajax_get_insurance_pending_purchase_recon') ?>",
            type: 'POST',
            data:{idvariant : $(this).val()},
            success: function(data) {
                $('.data_1').html(data);
            }
        });
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-lumx fa-lg"></span> Insurance Purcahse Reconcilation</h3></center><div class="clearfix"></div><hr>
<form enctype="multipart/form-data">
    <div class="col-md-3" style="padding: 5px"> Product
        <select class="form-control input-sm" name="idvariant" id="idvariant" required="">
            <option value="">Select Product</option>
            <?php foreach ($model_variant as $variant) { ?>
                <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-3" style="padding: 5px">CSV File columns/ format
        <table class="table table-bordered table-condensed text-center" style="font-family: Kurale">
            <tr>
                <td>Activation Code</td>
                <td>IMEI/SRNO</td>
                <td>Amount</td>
            </tr>
        </table>
    </div>
    <div class="col-md-3" style="padding: 5px"> Upload Reconciliation File(csv file)
        <input type="file" name="fileupload" id="fileupload" class="form-control input-sm" placeholder="Enter Transaction Id" required=""/>
    </div>
    <div class="col-md-1" style="padding: 5px"><br>
        <button type="submit" class="btn btn-primary btn-sm" formmethod="POST" formaction="<?php echo base_url('Reconciliation/submit_insurance_purchase_recon') ?>">Submit</button>
    </div>
    <input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
</form>
<div class="alert_msg"></div>
<div class="clearfix"></div><br>
<div class="col-md-5">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
    </div>
</div>
<div class="col-md-4">
    <div id="count_1" class="text-info"></div>
</div>
<div class="col-md-2">
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('pending_recon <?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; overflow: auto; padding: 0">
    <table id="pending_recon <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-bordered table-hover daybook">
        <thead class="fixedelement">
            <th>Date</th>
            <th>Invoice</th>
            <th>Product</th>
            <th>Activation code</th>
            <th>IMEI/SRNO</th>
            <th>Invoice Amount</th>
            <th>Sale Recon Amount</th>
            <th>Short Receive</th>
            <th>Sale Recon by</th>
            <th>Sale Recon Date</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($insurance_pending_recon as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td><?php echo $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->total_amount - $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->user_name ?></td>
                <td><?php echo $recon->sale_recon_date ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>