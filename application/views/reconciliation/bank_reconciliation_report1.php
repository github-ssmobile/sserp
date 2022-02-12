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
<script>
    $(document).ready(function(){
        $(document).on("click", ".payment_reconciliation_btn", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var idreconciliation=ce.val();
            var amount=$(parentDiv).find('.amount').val();
            var commission_amt=$(parentDiv).find('.commission_amt').val();
            var commission_per=$(parentDiv).find('.commission_per').val();
            var received_amt=$(parentDiv).find('.received_amt').val();
            var idbank=$(parentDiv).find('.idbank').val();
            var utr=$(parentDiv).find('.utr').val();
            var received_date=$(parentDiv).find('.received_date').val();
            var iduser=$(parentDiv).find('.iduser').val();
            if(received_amt==='' || commission_amt==='' || commission_per==='' || idbank==='' || utr==='' || received_date===''){
                swal('😠 All fields are mandatory', 'Fill required fields', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Reconciliation/receive_payment_reconciliation') ?>",
                    method:"POST",
                    data:{idreconciliation:idreconciliation,received_amt:received_amt,commission_amt:commission_amt,commission_per:commission_per,idbank:idbank,utr:utr,received_date:received_date,amount:amount,iduser:iduser},
                    success:function(data){
                        $(parentDiv).remove();
                        swal('🙂 Payment received', 'Reconciliation done', 'success');
                    }
                });
            }
        });
    
    $(document).on("keyup", ".commission_amt, .received_amt", function(event) {
        var ce = $(this);
        var parentDiv=$(ce).closest('td').parent('tr');
        var received_amt=$(parentDiv).find('.received_amt').val();
        var commission=$(parentDiv).find('.commission_amt').val();
        var commission_per=$(parentDiv).find('.commission_per');
        var commission_per_lb=$(parentDiv).find('.commission_per_lb');
        if(received_amt === '' || received_amt === '0'){
            $(parentDiv).find('.commission_amt').val('0');
            commission_per.val('0');
            commission_per_lb.html(0+'%');
            return false;
        }else{
            var per = (commission * 100)/received_amt;
            commission_per.val(per.toFixed(2));
            commission_per_lb.html(per.toFixed(2)+'%');
        }
    });
    
//    $(document).ready(function(){
        $('#payment_mode, #idbranch, #datefrom, #dateto').change(function(){
            var payment_head = $('#payment_head').val();
            var payment_mode = $('#payment_mode').val();
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_payment_reconciliation_form",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head, datefrom: datefrom, dateto: dateto},
                success:function(data)
                {
                    $(".daybook").html(data);
                }
            });
        });
    });
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-bank fa-lg"></span> Bank Reconcilation</h3></center><div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">Date Range
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<div class="col-md-3"> Select Branches
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-3">
    Select Payment Modes
    <select data-placeholder="Select Payment Mode" name="payment_mode" id="payment_mode" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Payment Modes</option>
        <option value="">All Payment Modes</option>
        <?php foreach ($payment_mode as $mode){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php } ?>
    </select>
</div>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('reconciliation_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table id="reconciliation_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>