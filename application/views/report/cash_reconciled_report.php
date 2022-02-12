<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
    z-index: 9;
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
    left: 2%;
    padding: 10px;
    display: none;
    animation: blinker 1s linear infinite;
}
@keyframes blinker {
    30% {
        opacity: 0;
    }
}
</style>
<script>
    $(document).ready(function(){
        $('#filter_btn').click(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Report/ajax_cash_reconciled_report",
                method:"POST",
                data:{idbranch: idbranch, datefrom: datefrom, dateto: dateto, branches: branches},
                success:function(data)
                {
                    $(".daybook").html(data);
                }
            });
        });
    });
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-cash fa-lg"></span> Cash Reconciled Report</h3></center><div class="clearfix"></div><hr>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Date</div>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Branch</div>
<div class="col-md-3">
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php $branches[] = $branch->id_branch; } ?>
    </select>
</div>
<input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
<?php } ?>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<div class="col-md-1">
    <button class="btn btn-primary btn-sm" id="filter_btn"><i class="fa fa-filter"></i> Filter</button>
</div>
<div class="col-md-1 col-sm-2 col-xs-2">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('cash_reconciled_report<?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="alert_msg"></div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; font-size: 13px; overflow: auto; padding: 0">
    <table id="cash_reconciled_report<?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>