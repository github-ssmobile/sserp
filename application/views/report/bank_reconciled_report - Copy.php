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
        var payment_mode = $('#payment_mode').val();
        var modes = $('#modes').val();
        var idbank = $('#idbank').val();
        if(datefrom == '' && dateto == ''){
            swal('Date Selection Required', 'Select date range', 'warning');
            return false;
        }
        $.ajax({
            url:"<?php echo base_url() ?>Reconciliation/ajax_bank_reconciled_report",
            method:"POST",
            data:{idbranch: idbranch, datefrom: datefrom, dateto: dateto, branches: branches,modes:modes,idpayment_mode : payment_mode, idbank:idbank},
            success:function(data)
            {
                $(".daybook").html(data);
            }
        });
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-cash fa-lg"></span> Bank Reconciled Report</h3></center><div class="clearfix"></div><hr>
<!--<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Date</div>-->
<div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-3">
    <select data-placeholder="Select Payment Mode" name="payment_mode" id="payment_mode" class="chosen-select" required="" style="width: 100%">
        <option value="">Payment Modes</option>
        <option value="">All Payment Modes</option>
        <?php foreach ($payment_mode as $mode){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php $modes[] = $mode->id_paymentmode; } ?>
    </select>
</div>
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
<!--<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Branch</div>-->
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
<!--<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Payment Modes</div>-->
<div class="col-md-2">
    <select data-placeholder="Select Payment Mode" name="idbank" id="idbank" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Bank</option>
        <option value="">All Banks</option>
        <?php foreach ($active_bank as $bank){ ?>
        <option value="<?php echo $bank->id_bank; ?>"><?php echo $bank->bank_name.' '.$bank->bank_branch; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-1">
    <input type="hidden" name="modes" id="modes" value="<?php echo implode($modes,',') ?>">
    <input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
    <button class="btn btn-primary btn-sm" id="filter_btn"><i class="fa fa-filter"></i> Filter</button>
</div>
<div class="alert_msg"></div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; font-size: 13px; overflow: auto; padding: 0">
    <table class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>