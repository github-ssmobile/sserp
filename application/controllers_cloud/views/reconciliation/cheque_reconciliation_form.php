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
        $("#sidebar").addClass("active");
        $(document).on("click", ".cheque_reconciliation_btn", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var idreconciliation=ce.val();
            var amount=$(parentDiv).find('.amount').val();
            var received_amt=$(parentDiv).find('.received_amt').val();
            var idbank=$(parentDiv).find('.idbank').val();
            var utr=$(parentDiv).find('.utr').val();
            var received_date=$(parentDiv).find('.received_date').val();
            var idsale_payment=$(parentDiv).find('.idsale_payment').val();
            var iduser=$(parentDiv).find('.iduser').val();
            if(received_amt === '' ||  idbank === '' || utr === '' || received_date === ''){
                swal('😠 All fields are mandatory', 'Fill required fields', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Reconciliation/receive_cheque_reconciliation') ?>",
                    method:"POST",
                    data:{idreconciliation:idreconciliation,received_amt:received_amt,idbank:idbank,utr:utr,received_date:received_date,amount:amount,iduser:iduser,idsale_payment:idsale_payment},
                    success:function(data){
                        if(data == 1){
                            swal('🙂 Payment received', 'Reconciliation done', 'success');
                            $(parentDiv).remove();
                        }else{
                            swal('😠 Failed to receive Payment', 'Entry not added! Please try Again!', 'warning');
                        }
                    }
                });
            }
        });
        $(document).on("click", ".cheque_bounce_btn", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var idreconciliation=ce.val();
            var amount= +$(parentDiv).find('.amount').val();
            var received_amt= +$(parentDiv).find('.received_amt').val();
            var idbank= $(parentDiv).find('.idbank').val();
            var utr=$(parentDiv).find('.utr').val();
            var received_date=$(parentDiv).find('.received_date').val();
            var iduser=$(parentDiv).find('.iduser').val();
            var inv_no=$(parentDiv).find('.inv_no').val();
            var idcustomer=$(parentDiv).find('.idcustomer').val();
            var corporate_sale=$(parentDiv).find('.corporate_sale').val();
            var date=$(parentDiv).find('.date').val();
            var idbranch=$(parentDiv).find('.branch').val();
            var idsale=$(parentDiv).find('.idsale').val();
            var transaction_id=$(parentDiv).find('.transaction_id').val();
            var customer_bank_name=$(parentDiv).find('.customer_bank_name').val();
            var idsale_payment=$(parentDiv).find('.idsale_payment').val();
            var bounce_charges= +$(parentDiv).find('.bounce_charges').val();
            if(idbank==='' || utr==='' || received_date==='' || bounce_charges=== ''){
                swal('😠 All fields are mandatory', 'Fill required fields', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Reconciliation/receive_cheque_bounce_reconciliation') ?>",
                    method:"POST",
                    dataType: "json",
                    data:{idreconciliation:idreconciliation,received_amt:received_amt,idbank:idbank,utr:utr,received_date:received_date,
                            amount:amount,iduser:iduser,inv_no:inv_no,idcustomer:idcustomer,corporate_sale:corporate_sale,date:date,
                            idsale:idsale,idbranch:idbranch,customer_bank_name:customer_bank_name,transaction_id:transaction_id,idsale_payment:idsale_payment,bounce_charges:bounce_charges},
                    success:function(data){
                        if(data.result == 'Success'){
//                            $(parentDiv).remove();
                            swal('🙂 Cheque bounce submitted', 'Entry added in branch credit with cheque bounce charges', 'success');
                            $(parentDiv).remove();
                        }else{
                            swal('🙂 Failed to Submit Cheque bounce', 'Entry not added! Please try Again!', 'warning');
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
        $('#idbranch, #datefrom, #dateto').change(function(){
            var payment_head = $('#payment_head').val();
            var payment_mode = $('#payment_mode').val();
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_cheque_reconciliation_form",
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
<center><h3 style="margin-top: 0"><span class="mdi mdi-cash fa-lg"></span> Cheque Reconcilation</h3></center><div class="clearfix"></div><hr>
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
<div class="col-md-3">
    Select Branch
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php } ?>
    </select>
</div>
<input type="hidden" name="payment_mode" id="payment_mode" value="2">
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<div class="clearfix"></div><br>
<?php if( $save = $this->session->flashdata('save_data')): ?>
<div class="alert alert-dismissible alert-success" id="alert-dismiss">
    <?= $save ?>
</div>
<?php endif; ?>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>