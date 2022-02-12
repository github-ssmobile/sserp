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
    $(document).on("click", "#filter_btn", function(event) {
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var idvariant = $('#idvariant').val();
        if(dateto == '' && datefrom == ''){
            alert("Select date range");
            return false;
        }
        jQuery.ajax({
            url: "<?php echo base_url('Reconciliation/get_insurance_recon_bystatus_date') ?>",
            type: 'POST',
            data:{idvariant : idvariant,datefrom:datefrom,dateto:dateto},
            success: function(data) {
                $('.daybook').html(data);
            }
        });
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-lumx fa-lg"></span> Insurance Reconcilation Report</h3></center><div class="clearfix"></div><hr>
<div class="col-md-1" style="padding: 2px">Date</div>
<div class="col-md-3" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-3" style="padding: 2px"> 
    <select class="form-control input-sm required" name="idvariant" id="idvariant" required="">
        <option value="">Select Product</option>
        <?php foreach ($model_variant as $variant) { ?>
            <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-2 pull-right">
    <button id="filter_btn" class="btn btn-primary btn-sm"><span class="fa fa-filter"></span> Filter</button>
</div>
<div class="clearfix"></div><br>
<div class="col-md-4" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
    </div>
</div>
<div class="col-md-2 pull-right">
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('pending_recon <?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; overflow: auto; padding: 0">
    <table id="pending_recon <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-bordered table-hover daybook">
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>