<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #9a9b97;

     /*background-image: linear-gradient(to right top, #e1f0ff, #edeffd, #f6eef8, #f9f0f4, #f8f2f2);*/
    /*background-image: linear-gradient(to right top, #090537, #51094f, #950051, #cc2b3e, #eb6712);*/
    /*background-image: linear-gradient(to right top, #46cfb0, #49d4ab, #4ed8a5, #56dc9f, #60e097);*/
    color: #fff;
    z-index: 9;
}
.grdark{
    background-color: #ade7ca;
    /*background-image: linear-gradient(to right top, #090537, #51094f, #950051, #cc2b3e, #eb6712);*/
    /*background-image: linear-gradient(to right top, #46cfb0, #49d4ab, #4ed8a5, #56dc9f, #60e097);*/
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
.bfl_form {cursor: pointer;}
</style>
<script>
    $(document).ready(function(){
        $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
        $("#sidebar").addClass("active");
        $(document).on("click", ".payment_reconciliation_btn", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');
            var idreconciliation=ce.val();
            var amount=$(parentDiv).find('.amount').val();
            var commission_amt=$(parentDiv).find('.commission_amt').val();
            var commission_per=$(parentDiv).find('.commission_per').val();
            var received_amt=$(parentDiv).find('.received_amt').val();
            var short_receive=$(parentDiv).find('.short_receive').val();
            var idbank=$(parentDiv).find('.idbank').val();
            var utr=$(parentDiv).find('.utr').val();
            var received_date=$(parentDiv).find('.received_date').val();
            var iduser=$(parentDiv).find('.iduser').val();
            if(received_amt==='' || commission_amt==='' || commission_per==='' || idbank==='' || utr==='' || received_date==='' || short_receive ===''){
                swal('😠 All fields are mandatory', 'Fill required fields', 'warning');
                return false;
            }
            if(parseFloat(received_amt) > parseFloat(amount)){
                swal('😠 Alert', 'Received amount should not be greater than expected amount', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Reconciliation/receive_payment_reconciliation') ?>",
                    method:"POST",
                    dataType:"json",
                    data:{idreconciliation:idreconciliation,received_amt:received_amt,commission_amt:commission_amt,commission_per:commission_per,idbank:idbank,utr:utr,received_date:received_date,amount:amount,iduser:iduser,short_receive:short_receive},
                    success:function(data){
                        if(data.result == 'Success'){
                            $(parentDiv).remove();
                            $('#filter_1').select();
                            $('.alert_msg').show();
                            $('.alert_msg').text('🙂 Payment received... Reconciliation done');
                            $('.alert_msg').fadeOut(20000);
                        }else{
                            swal('😠 Alert !','Entry not submitted, Try again...','warning');
                        }
                    }
                });
            }
        });
    
    $(document).on("keyup", ".commission_amt, .received_amt", function(event) {
        var ce = $(this);
        var parentDiv=$(ce).closest('td').parent('tr');
        var amount=$(parentDiv).find('.amount').val();
//        var received_amt=$(parentDiv).find('.received_amt').val();
//        var commission=$(parentDiv).find('.commission_amt').val();
        var received_amt = (isNaN(+$(parentDiv).find('.received_amt').val())) ? 0 : +$(parentDiv).find('.received_amt').val();
        var commission = (isNaN(+$(parentDiv).find('.commission_amt').val())) ? 0 : +$(parentDiv).find('.commission_amt').val();
        var commission_per=$(parentDiv).find('.commission_per');
        var commission_per_lb=$(parentDiv).find('.commission_per_lb');
        if(received_amt === '' || received_amt === '0'){
            $(parentDiv).find('.commission_amt').val('0');
            commission_per.val('0');
            commission_per_lb.html(0+'%');
            $(parentDiv).find('.short_receive').val(amount);
            $(parentDiv).find('.short_receive_lb').html(amount);
            return false;
        }else{
            var per = (commission * 100)/received_amt;
            commission_per.val(per.toFixed(2));
            commission_per_lb.html(per.toFixed(2)+'%');
            var short = amount - (received_amt + commission);
            $(parentDiv).find('.short_receive').val(short);
            $(parentDiv).find('.short_receive_lb').html(short);
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
 function get_bflform(idsale){
    var id_sale = idsale;
    $('#modal_body_table').html('');  
    $.ajax({
                url:"<?php echo base_url() ?>Reconciliation/ajax_bfl_data_form",
                method:"POST",
                dataType: 'json',
                data:{id_sale:id_sale},
                success:function(data)
                {   
                    if(data != null){
                    var bfl_form = '<div class="col-md-10 col-md-offset-1" style="font-size: 14px">'
                                +'<div class="thumbnail" style="overflow: auto; padding: 0">'
                                    +'<table class="table table-bordered table-condensed table-striped" style="margin: 0">'
                                        +'<tbody>'
                                            +'<tr>'
                                                +'<td>DO ID:</td>'
                                                +'<td>'+ data.do_id +'</td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Customer Name</td>';
                                              if(data.customer_name == null){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.customer_name +'</td>';    
                                                }
                                              bfl_form+='</tr>'
                                            +'<tr>'
                                                +'<td>Mobile No</td>';
                                              if(data.mobile == null){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.mobile +'</td>';    
                                                }  
                                              bfl_form+='</tr>'
                                            +'<tr>'
                                                +'<td>Brand</td>';
                                                if(data.bfl_brand == 'null'){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.bfl_brand +'</td>';    
                                                }
                                              bfl_form+='</tr>'
                                            +'<tr>'
                                               +'<td>Model</td>';
                                               if(data.bfl_model == 'null'){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.bfl_model +'</td>';    
                                                }                                              
                                            bfl_form+='</tr>'
                                            +'<tr>'
                                                +'<td>IMEI/ SRNO</td>';
                                              if(data.bfl_srno == 'null'){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.bfl_srno +'</td>';    
                                                }  
                                            bfl_form+='</tr>'
                                            +'<tr>'
                                                +'<td>Scheme Code (GT/AE)</td>';
                                              if(data.scheme_code == 'null'){
                                              bfl_form+='<td>--</td>';    
                                                }else{
                                              bfl_form+='<td>'+ data.scheme_code + '</td>';    
                                                }  
                                            bfl_form+='</tr>'
                                            +'<tr>'
                                                +'<td>MOP</td>'
                                                +'<td>'+ data.mop +' <i class="fa fa-rupee"></i></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>DownPayment</td>'
                                                +'<td>'+ data.downpayment +' <i class="fa fa-rupee"></i></td>'
                                            +'</tr>'
//                                            +'<tr>'
//                                                +'<td>Net Disbursement</td>'
//                                                +'<td>'+ data.loan +' <i class="fa fa-rupee"></i><input type="hidden" name="bfl_netdisbursement" value="'+ data.loan +'" /></td>'
//                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Loan</td>'
                                                +'<td>'+ data.loan +' <i class="fa fa-rupee"></i></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Finance ID</td>'
                                                +'<td>'+ data.do_id +'</td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>EMI Amount</td>'
                                                +'<td>'+ data.emi_amount +' <i class="fa fa-rupee"></i></td>'
                                            +'</tr>'
                                            +'<tr>'
                                                +'<td>Tenure</td>'
                                                +'<td>'+ data.tenure +'</td>'
                                            +'</tr>'
                                        +'</tbody>'
                                    +'</table>'
                                +'</div>'
                        +'</div><div class="clearfix"></div>';
                    $('#modal_val').modal('show');
                    $('#modal_body_table').append(bfl_form);
                  }else{
                    $('#modal_val').modal('show');
                    $('#modal_body_table').append('<span style="color:red;font-size: 20px;">No Data Found...!</span>');
                  } 
                }
            });
    }
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Payment Reconcilation</h3></center><div class="clearfix"></div><hr>
<div class="col-md-3 col-sm-3 col-xs-6" style="padding: 2px">
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
<div class="col-md-2" style="padding: 0">
    Select Branch
    <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Branches</option>
        <option value="">All Branches</option>
        <?php foreach ($branch_data as $branch){ ?>
        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-3" style="padding: 0">
    Select Payment Mode
    <select data-placeholder="Select Payment Mode" name="payment_mode" id="payment_mode" class="chosen-select" required="" style="width: 100%">
        <option value="">Select Payment Modes</option>
        <option value="">All Payment Modes</option>
        <?php foreach ($payment_mode as $mode){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php } ?>
    </select>
</div>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<div class="col-md-3" style="padding: 0">Search
    <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
</div>
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('reconciliation_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<?php if( $save = $this->session->flashdata('save_data')): ?>
<div class="alert alert-dismissible alert-success" id="alert-dismiss">
    <?= $save ?>
</div>
<?php endif; ?>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table id="reconciliation_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<div class="alert_msg"></div>
<div id="modal_val" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="width: 700px;">
        <div class="modal-content">
          <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button> 
                <center>
                <h4 class="modal-title"><span class="modal_title">Bajaj Finance Information</span></h4>
                </center>
            </div>
        <div class="modal-body info">
            <div id="modal_body_table"></div>
            
        </div>
       
      </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>