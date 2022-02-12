<?php include __DIR__ . '../../header.php'; ?>
<style>
    .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
         /*background-image: linear-gradient(to right top, #e1f0ff, #edeffd, #f6eef8, #f9f0f4, #f8f2f2);*/
        /*background-image: linear-gradient(to right top, #090537, #51094f, #950051, #cc2b3e, #eb6712);*/
        /*background-image: linear-gradient(to right top, #46cfb0, #49d4ab, #4ed8a5, #56dc9f, #60e097);*/
        /*color: #000;*/
        z-index: 9;
    }

</style>
<script>
    $(document).ready(function(){

        $('.btnsubmit').click(function(){
            var payment_mode = $('#payment_mode_edit').val();
            var datefrom = $('#datefrom_edit').val();
            var dateto = $('#dateto_edit').val();
            var idbranch = $('#idbranch_edit').val();
            var modes = $('#modes_edit').val();
            var branches = $('#branches_edit').val();
            
            if(payment_mode != '' && datefrom != '' && dateto != '' && idbranch != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Reconciliation/ajax_get_credit_received_edit",
                    method:"POST",
                    data:{payment_mode : payment_mode, idbranch: idbranch, datefrom: datefrom, dateto: dateto, modes: modes, branches: branches},
                    success:function(data)
                    {
                        $(".daybook").html(data);
                    }
                });
            }else{
                alert("Select Filter");
                return false;
            }
        });
    });
    $(document).on("click", ".hide-btn", function(event) {  
        var parentDiv = $(this).closest('tr');
        $(parentDiv).find(".myDiv1").css("display", "block");
        $(parentDiv).find(".myDiv2").css("display", "none");
        $(parentDiv).find(".myDiv3").css("display", "none");
    });
    function submit_edit_credit(idreconl,event){
        event.preventDefault();
        var reconsilation = idreconl;
        var parentDiv = $(this).closest('tr');
        var transactionid = $("#txn_"+reconsilation).val();
        var transactionid_old = $("#txnold_"+reconsilation).val();
        var inv_no = $("#invno_"+reconsilation).val();
        var saleid = $("#saleid_"+reconsilation).val();
        var reconlid = $("#reconlid_"+reconsilation).val();
        var user_id = $(".user_id").val();
       
           if(confirm("Do You Want To Update TxnId ?")){
               if(transactionid != null && transactionid != ""){
                   $("#txnold_"+reconsilation).val(transactionid);
            jQuery.ajax({
                url: "<?php echo base_url('Reconciliation/update_transactionid') ?>",
                data: {reconsilation:reconsilation,transactionid:transactionid,transactionid_old:transactionid_old,inv_no:inv_no,saleid:saleid,reconlid:reconlid,user_id:user_id},
                method: 'POST',
                success: function () {
                    $('tr').find('#div1_'+reconsilation).css("display", "none");
                    $('tr').find('#div2_'+reconsilation).css("display", "block");
                    $('#div2_'+reconsilation).text(transactionid);
                    $('tr').find('#button2_'+reconsilation).css("display", "none");
                    $('tr').find('#button1_'+reconsilation).css("display", "block");
                    $('tr').find('#button3_'+reconsilation).css("display", "block");
                }
            });
            }else{
              alert('Please Enter TxnId');
              return false;
            }
           }else{
                $('tr').find('#div1_'+reconsilation).css("display", "none");
                $('tr').find('#div2_'+reconsilation).css("display", "block");
                $('tr').find('#button2_'+reconsilation).css("display", "none");
                $('tr').find('#button1_'+reconsilation).css("display", "block");
                $('tr').find('#button3_'+reconsilation).css("display", "block");
           }
        
    }
    function submit_delete_credit(idreconl,event){
        event.preventDefault();
        var reconsilation = idreconl;
        if(reconsilation){
        if(confirm("Do You Want To Delete ?")){
            jQuery.ajax({
                url: "<?php echo base_url('Reconciliation/delete_credit_transactionid') ?>",
                data: {reconsilation:reconsilation},
                method: 'POST',
                success: function () {
                    alert('Deleted Successfully.');
                    $("#button3_"+reconsilation).parent().parent().remove();
                }
            });
           }else{
              console.log('Delete Cancelled')
           }
        }   
    }
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Credit/Custody Received Edit </h3></center><div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search_edit" id="datefrom_edit" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search_edit" id="dateto_edit" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<?php if($_SESSION['level'] == 2){ ?>
    <input type="hidden" name="idbranch_edit" id="idbranch_edit" value="<?php echo $_SESSION['idbranch'] ?>">
<?php }else{ ?>
    <div class="col-md-3">
        <!--Select Branches-->
        <select data-placeholder="Select Branches" name="idbranch_edit" id="idbranch_edit" class="chosen-select" required="" style="width: 100%">
            <option value="0">All Branches</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php $branchess[] = $branch->id_branch; } ?>
        </select>
    </div>
    <input type="hidden" name="branches_edit" id="branches_edit" value="<?php echo implode($branchess,',') ?>">
<?php }?>
<div class="col-md-3">
    <!--Select Payment Modes-->
    <select data-placeholder="Select Payment Mode" name="payment_mode_edit" id="payment_mode_edit" class="chosen-select" required="" style="width: 100%">
        <option value="0">All Payment Modes</option>
        <?php foreach ($payment_mode as $mode){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php $modes[] = $mode->id_paymentmode; } ?>
    </select>
</div>
<input type="hidden" name="modes_edit" id="modes_edit" value="<?php echo implode($modes,',') ?>">
<div class="col-md-1"><button class="btn btn-info btn-sm btnsubmit" >Filter</button></div>
<div class="col-md-1 pull-right">
<!--    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('reconciliation_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>-->
</div><div class="clearfix"></div><br>
<?php if( $save = $this->session->flashdata('save_data')): ?>
<div class="alert alert-dismissible alert-success" id="alert-dismiss">
    <?= $save ?>
</div>
<?php endif; ?>
<div class="thumbnail" style="height: 650px; font-size: 12px; overflow: auto; padding: 0">
    <table id="reconciliation_report_edit <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>