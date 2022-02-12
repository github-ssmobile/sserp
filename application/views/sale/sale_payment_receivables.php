<?php include __DIR__ . '../../header.php'; ?>
<script>
    $("#sidebar").addClass("active");
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on("submit", ".credit_payment_receive_form", function(event) {
        event.preventDefault();
        var $parentDiv = $(this).closest("tr");
        var $form = $(this);
        $form.css('pointer-events','none');
        var fd = new FormData();
        if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
            event.preventDefault();
            alert("Fill Mandatory fields !!");
            return false;
        }else{
            var other_data = $form.serializeArray();
            $.each(other_data, function(key, input) {
                fd.append(input.name, input.value);
            });
            fd.append("is_ajax", "yes");
            jQuery.ajax({
                url: "<?php echo base_url('Reconciliation/save_credit_received') ?>",
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.result == "Success"){
                        location.reload();
//                        window.open("<?php echo base_url('Reconciliation/payment_received_receipt/') ?>"+data.idrecon, "_blank","toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=4000,height=4000");
                    }else{
                        alert('Failed to submit entry');
                    }
                }
            });
            setTimeout(function () {
                $parentDiv.remove();
//                $('#payment_head').trigger('change');
            }, 200);
        }
    });
//    $(document).ready(function(){
    $('#payment_head').click(function(){
        $('#payment_mode').val('');
    });
    $('#payment_head').change(function(){
        $('#load_img').remove();
        var payment_head = $(this).val();
        var idbranch = $('#idbranch').val();
        var payment_mode = $('#payment_mode').val();
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_get_payment_mode_byhead",
            method:"POST",
            data:{payment_head : payment_head},
            success:function(data)
            {
                $("#payment_mode").html(data);
            }
        });
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_get_sale_receivables",
            method:"POST",
            data:{idpayment_head : payment_head, idbranch: idbranch, idpayment_mode: payment_mode},
            success:function(data)
            {
                $(".receivables").html(data);
            }
        });
    });
    $('#payment_mode, #idbranch').change(function(){
        $('#load_img').remove();
        var payment_head = $('#payment_head').val();
        var payment_mode = $('#payment_mode').val();
        var idbranch = $('#idbranch').val();
        $.ajax({
            url:"<?php echo base_url() ?>Sale/ajax_get_sale_receivables",
            method:"POST",
            data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head},
            success:function(data)
            {
                $(".receivables").html(data);
            }
        });
    });
});
</script>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-currency-inr fa-lg"></span> Credit/Custody Receive</h3></center></div><div class="clearfix"></div><hr>
<?php // } 
//    $var_closer = 1;
//    if(count($sale_last_entry_byidbranch)){
//        if($sale_last_entry_byidbranch[0]->sum_cash == 0){
//            $var_closer = 1;
//        }else{
//            if(count($cash_closure_last_entry) == 0){
//                $var_closer = 0;
//            }else{
//                if($last_date_entry[0]->date == $cash_closure_last_entry[0]->date){
//                    $var_closer = 1;
//                }elseif($last_date_entry[0]->date > $cash_closure_last_entry[0]->date){
//                    $var_closer = 0;
//                }
//            }
//        }
//    }
    if($var_closer){ ?>
<div class="p-2">
    <div class="col-md-2 col-sm-4 col-xs-6" style="padding: 2px">Payment Head
        <select class="form-control input-sm" name="payment_head" id="payment_head">
            <option value="">Select Payment Head</option>
            <?php foreach ($payment_head as $head){  if($head->valid_for_creadit_receive == 1){ ?>
            <option value="<?php echo $head->id_paymenthead; ?>"><?php echo $head->payment_head; ?></option>
            <?php }} ?>
        </select>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-6" style="padding: 2px">Payment Mode
        <select class="form-control input-sm" name="payment_mode" id="payment_mode">
            <option value="">Select Payment Mode</option>
        </select>
    </div>
    <?php if($this->session->userdata('role_type')==1 || $_SESSION['level'] != 1){ ?>
        <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>">
    <?php }else{ ?>
    <div class="col-md-2 col-sm-4 col-xs-12" style="padding: 2px">Branch
        <select class="form-control input-sm" name="idbranch" id="idbranch">
            <option value="">Select Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
    <div class="col-md-3">
        Search
        <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
    </div>
        <div class="col-md-2"><br>
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-1 col-sm-2 col-xs-2 pull-right"><br>
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('receivables <?php echo date('d-m-Y h_i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
    </div><div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <!--<center><img id="load_img" src="<?php echo base_url('assets/images/mini-setting-gif.gif') ?>" style="max-width: 100%" /></center>-->
    <table id="receivables <?php echo date('d-m-Y h_i a') ?>" class="table table-condensed table-bordered white table-hover receivables" style="margin-bottom: 0"></table>
</div>
<?php }else{ 
        echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
            .'</center>';
    } ?>
<?php include __DIR__ . '../../footer.php'; ?>