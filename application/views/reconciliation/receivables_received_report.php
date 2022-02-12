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
.bfl_form {cursor: pointer;}
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
                url:"<?php echo base_url() ?>Reconciliation/ajax_receivables_received_report",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head, datefrom: datefrom, dateto: dateto,branches:branches,modes:modes,date_filter:date_filter},
                success:function(data)
                {
                    $(".daybook").html(data);
                }
            });
        });
    });
});
function get_bflform_report(idsale){
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
<center><h3 style="margin-top: 0"><span class="mdi mdi-repeat fa-lg"></span> Receivables Received Report</h3></center><div class="clearfix"></div><hr>
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
<div class="col-md-2 col-sm-3">
    Date Filter
        <select class="chosen-select form-control input-sm" name="date_filter" id="date_filter">
            <option value="">Date Filter</option>            
            <option value="date">Invoice Date</option>
            <option value="received_entry_time">Received Date</option>
            <option value="transfer_date">settlement Date</option>

        </select>
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
        <?php foreach ($payment_mode as $mode){ if($mode->id_paymentmode != 1){ ?>
        <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
        <?php $modes[] = $mode->id_paymentmode; }} ?>
    </select>
</div>
<input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
<input type="hidden" name="modes" id="modes" value="<?php echo implode($modes,',') ?>">
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