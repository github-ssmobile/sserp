<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
//    $(document).on("submit", ".credit_payment_receive_form", function(event) {
//        event.preventDefault();
//        var $parentDiv = $(this).closest("tr");
//        var $form = $(this);
//        $form.css('pointer-events','none');
//        var fd = new FormData();
//        if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
//            event.preventDefault();
//            alert("Fill Mandatory fields !!");
//            return false;
//        }else{
//            var other_data = $form.serializeArray();
//            $.each(other_data, function(key, input) {
//                fd.append(input.name, input.value);
//            });
//            fd.append("is_ajax", "yes");
//            jQuery.ajax({
//                url: "<?php echo base_url('Reconciliation/save_credit_received') ?>",
//                data: fd,
//                processData: false,
//                contentType: false,
//                type: 'POST',
//                dataType: 'json',
//                success: function(data) {
//                    if(data.result == "Success"){
//                        window.open("<?php echo base_url('Reconciliation/payment_received_receipt/') ?>"+data.idrecon, "_blank","toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=4000,height=4000");
//                    }else{
//                        alert('Failed to submit entry');
//                    }
//                }
//            });
//            setTimeout(function () {
//                $parentDiv.fadeOut();
////                $('#payment_head').trigger('change');
//            }, 200);
//        }
//    });
//    $(document).ready(function(){
    $('#payment_head').click(function(){
        $('#payment_mode').val('');
    });
    
    $('#payment_head').change(function(){
        $('#load_img').remove();
        var payment_head = $(this).val();
        var idbranch = $('#idbranch').val();
        var branches = $('#branches').val();
        var payment_mode = $('#payment_mode').val();
        var from = $('#datefrom').val();
        var to = $('#dateto').val();
        
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_get_payment_mode_byhead",
            method:"POST",
            data:{payment_head : payment_head, from: from, to: to},
            success:function(data)
            {
                $("#payment_mode").html(data);
            }
        });
        if((from =='' && to != '') || (from !='' && to == '')){ 
            return false; 
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_get_credit_receivable_report",
                method:"POST",
                data:{idpayment_head : payment_head, idbranch: idbranch, idpayment_mode: payment_mode,branches:branches,from: from,to: to},
                success:function(data)
                {
                    $(".receivables").html(data);
                }
            });
        }
    });
    $('#payment_mode, #idbranch, #datefrom, #dateto').change(function(){
        $('#load_img').remove();
        var payment_head = $('#payment_head').val();
        var payment_mode = $('#payment_mode').val();
        var idbranch = $('#idbranch').val();
        var branches = $('#branches').val();
         var from = $('#datefrom').val();
        var to = $('#dateto').val();
        if((from =='' && to != '') || (from !='' && to == '')){ 
            return false; 
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_get_credit_receivable_report",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head,branches:branches,from: from,to: to},
                success:function(data)
                {
                    $(".receivables").html(data);
                }
            });
        }
    });
});
</script>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-currency-inr fa-lg"></span> Credit Receivable Report</h3></center></div><div class="clearfix"></div><br>
<div class="p-2">
    <div class="col-md-3 col-sm-3 col-xs-6" style="padding: 2px">
        Date 
        <div class="input-group">
            <div class="input-group-btn">
                <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
            </div>
            <div class="input-group-btn">
                <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6" style="padding: 2px">Payment Head
        <select class="form-control input-sm" name="payment_head" id="payment_head">
            <option value="">Select Payment Head</option>
            <option value="">All Payment Heads</option>
            <?php foreach ($payment_head as $head){  if($head->valid_for_creadit_receive == 1){ ?>
            <option value="<?php echo $head->id_paymenthead; ?>"><?php echo $head->payment_head; ?></option>
            <?php }} ?>
        </select>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">Payment Mode
        <select class="form-control input-sm" name="payment_mode" id="payment_mode">
            <option value="">Select Payment Mode</option>
        </select>
    </div>
    <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
    <?php } else { ?>
    <div class="col-md-3 col-sm-4 col-xs-12" style="padding: 2px">Branch
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
    <div class="col-md-1 col-sm-2 col-xs-2 pull-right"><br>
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('receivables <?php echo date('d-m-Y h_i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
    </div><div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <!--<center><img id="load_img" src="<?php echo base_url('assets/images/mini-setting-gif.gif') ?>" style="max-width: 100%" /></center>-->
    <table id="receivables <?php echo date('d-m-Y h_i a') ?>" class="table table-condensed table-bordered white table-hover receivables" style="margin-bottom: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>