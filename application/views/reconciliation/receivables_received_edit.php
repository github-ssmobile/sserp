<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelementtop{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
    z-index: 9;
}
.grdark{
    background-color: #ade7ca;
}
.alert_msg{
    width: 450px;
    background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
    color: #fff;
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
    z-index: 9999999;
    /*animation: blinker 2s linear infinite;*/
}
</style>
<script>
$(document).ready(function(){
    $("#sidebar").addClass("active");
    $(document).ready(function(){
        $('#payment_mode, #idbranch, #datefrom, #dateto,#date_filter').change(function(){
            var payment_head = $('#payment_head').val();
            var payment_mode = $('#payment_mode').val();
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var modes = $('#modes').val();
            var idrole = $('#idrole').val();
            var date_filter = $('#date_filter').val();      
            if(dateto !== '' && datefrom === '' && date_filter==='')
            {
                alert('Select Date & Filter !!');
                return false;
            }else if(dateto === '' && datefrom !== ''  && date_filter===''){
                alert('Select Date & Filter !!');
                return false;
            }
//            if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
//                return false;
//            }
            $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_receivables_received_edit",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head, datefrom: datefrom, dateto: dateto,branches:branches,modes:modes,date_filter:date_filter,idrole:idrole},
                success:function(data)
                {
                    $(".daybook").html(data);
                }
            });
        });
    });
});
function payment_reconciliation_return(payment_id){
//alert(payment_id);
var return_count = $('#returncount_'+payment_id).val();
var idrole = $('#idrole').val();
var id_salepayment = $('#idsalepayment_'+payment_id).val();
var id_paymenthead = $('#idpaymenthead_'+payment_id).val();
    if (confirm("Are You Want Return Back Payment Reconciliation?")) {
    $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/payment_reconciliation_return",
                method:"POST",
                dataType:"json",
                data:{payment_id:payment_id,return_count:return_count,idrole:idrole,id_salepayment:id_salepayment,id_paymenthead:id_paymenthead},
                success:function(data)
                {
                    if(data.result == 'Success'){
                            $("#reconsilationrow_"+payment_id).remove();
                            $('.alert_msg').show();
                            $('.alert_msg').text('Payment Reconciliation Returned Back Done');
                            $('.alert_msg').fadeOut(20000);
                        }else{
                            swal('Alert !','Entry not submitted, Try again...','warning');
                        }
                }
            });
    }else{
        console.log('Cancel Return Back Payment Reconciliation')
    }    
}
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-repeat fa-lg"></span> Receivables Received Edit</h3></center><div class="clearfix"></div><hr>
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
    <input type="hidden" id="idrole" value="<?php echo $this->session->userdata('idrole') ?>">
</div>
 <?php if($this->session->userdata('idrole') != 27){?>
<div class="col-md-2 col-sm-3">
   
    Date Filter
        <select class="chosen-select form-control input-sm" name="date_filter" id="date_filter">
            <option value="">Date Filter</option>            
            <option value="date">Invoice Date</option>
            <option value="received_entry_time">Received Date</option>
            <option value="transfer_date">settlement Date</option>

        </select>
    
    </div>
<?php } ?>
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
    <?php  
      if(!empty($payment_mode)){
    ?>
    Select Payment Modes
    <select data-placeholder="Select Payment Mode" name="payment_mode" id="payment_mode" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Payment Modes</option>
        <option value="">All Payment Modes</option>
        <?php foreach ($payment_mode as $mode){ if($mode->id_paymentmode != 1){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php $modes[] = $mode->id_paymentmode; }} ?>
    </select>
    <?php  } ?>
</div>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<?php  
  if(!empty($payment_mode)){
?>
<input type="hidden" name="modes" id="modes" value="<?php echo implode($modes,',') ?>">
<?php  } ?>
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('receivables_received_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<?php if( $save = $this->session->flashdata('save_data')): ?>
<div class="alert alert-dismissible alert-success" id="alert-dismiss">
    <?= $save ?>
</div>
<?php endif; ?>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table id="receivables_received_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<div class="alert_msg"></div>
<?php include __DIR__ . '../../footer.php'; ?>