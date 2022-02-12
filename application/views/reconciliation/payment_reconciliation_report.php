<?php include __DIR__ . '../../header.php'; ?>
<style>
    .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
        z-index: 9;
    }
  </style>

<script>
    $(document).ready(function(){
        $('#payment_mode, #idbranch, #datefrom, #dateto').change(function(){
            var payment_head = $('#payment_head').val();
            var payment_mode = $('#payment_mode').val();
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var modes = $('#modes').val();
            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_payment_reconciliation_report",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head, datefrom: datefrom, dateto: dateto,branches:branches,modes:modes},
                success:function(data)
                {
                    $(".daybook").html(data);
                }
            });
        });
    });
</script>
<style>

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<center><h3 style="margin-top: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Pending Payment Reconcilation Report</h3></center><div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    Date Range
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
<div class="col-md-3">Branch
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
<div class="col-md-3">
    Select Payment Modes
    <select data-placeholder="Select Payment Mode" name="payment_mode" id="payment_mode" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Payment Modes</option>
        <option value="">All Payment Modes</option>
        <option value="2"><?php echo "Cheque"; ?></option>
        <?php $modes[] = 2; foreach ($payment_mode as $mode){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php $modes[] = $mode->id_paymentmode; } ?>
    </select>
</div>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<input type="hidden" name="modes" id="modes" value="<?php echo implode($modes,',') ?>">
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('reconciliation_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<?php if( $save = $this->session->flashdata('save_data')): ?>
<div class="alert alert-dismissible alert-success" id="alert-dismiss">
    <?= $save ?>
</div>
<?php endif; ?>
<div class="thumbnail" style="font-size: 12px; ">
    <table id="reconciliation_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>