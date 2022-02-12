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
        $(document).on("click", ".cash_reconciliation_btn", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var idreconciliation=ce.val();
            var amount=$(parentDiv).find('.amount').val();
            var received_amt=$(parentDiv).find('.received_amt').val();
            var utr=$(parentDiv).find('.utr').val();
            var iduser=$(parentDiv).find('.iduser').val();
            var received_date=$(parentDiv).find('.received_date').val();
            if(received_amt === '' ||  utr === ''){
                swal('😠 All fields are mandatory', 'Enter Amount and UTR Number', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Reconciliation/ajax_cash_reconciliation') ?>",
                    method:"POST",
                    dataType: 'json',
                    data:{idreconciliation:idreconciliation,received_amt:received_amt,utr:utr,amount:amount,iduser:iduser,received_date:received_date},
                    success:function(data){
                        if(data.result == 'Success'){
                            swal('🙂 Payment received', 'Reconciliation done', 'success');
                            $('.alert_msg').text('Last reconciliation Tranx Id '+utr);
                            $(parentDiv).remove();
                        }else{
                            swal('😠 Failed to receive Payment', 'Entry not added! Please try Again!', 'warning');
                        }
                    }
                });
                
            }
        });
        $(document).on("keyup", ".received_amt", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var received_amt=$(parentDiv).find('.received_amt').val();
            var amount=$(parentDiv).find('.amount').val();
            if(received_amt === '' || received_amt === '0'){
                $(parentDiv).find('.short_receive').val(amount);
                $(parentDiv).find('.short_receive_lb').html(amount);
                return false;
            }else{
                var short = amount - received_amt;
                $(parentDiv).find('.short_receive').val(short);
                $(parentDiv).find('.short_receive_lb').html(short);
            }
        });
        $('#filter_btn').click(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_cash_reconciliation_form",
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
<center><h3 style="margin-top: 0"><span class="mdi mdi-cash fa-lg"></span> Cash Reconcilation</h3></center><div class="clearfix"></div><hr>
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
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
<div class="col-md-2">
    <button class="btn btn-primary btn-sm" id="filter_btn"><i class="fa fa-filter"></i> Filter</button>
</div>
<div class="alert_msg"></div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table class="table table-condensed table-striped table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>