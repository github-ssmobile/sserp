<?php include __DIR__ . '../../header.php'; ?>
<style>
.fixedelement{
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #c5f4dd;
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
    left: 20%;
    padding: 10px;
    display: none;
    animation: blinker 1s linear infinite;
    z-index: 999;
}
@keyframes blinker {
  30% {
    opacity: 0;
  }
}
</style>
<script>
$(document).ready(function(){
    $(document).on("submit", "#bank_recon_form", function(event) {
        event.preventDefault();
        var $form = $(this);
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
                url: "<?php echo base_url('Reconciliation/ajax_insurance_reconciliation') ?>",
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.result == "Success"){
                        $('.alert_msg').show();
                        $('.alert_msg').text('Reconciliation done, Activation Id '+$('#trans_id').val());
                        $('#trans_id').val('');
                        $('#imei').val('');
                        $('#amount').val('');
                        $('.row_'+data.row_id).remove();
                    }else if(data.result == 'NotFound'){
                        swal('Entry not found!', 'You have entered details are not matched!!', 'warning');
                        return false;
                    }else{
                        swal('Alert Failed!', 'Insurance reconciliation failed to submit!!', 'warning');
                        return false;
                    }
                }
            });
        }
    });
    $(document).on("change", "#idvariant,#idbranch", function(event) {
        var idvariant = $('#idvariant').val();
        var idbranch = $('#idbranch').val();
        var branches = $('#branches').val();
        jQuery.ajax({
            url: "<?php echo base_url('Reconciliation/ajax_get_insurance_pending_reconciliation') ?>",
            type: 'POST',
            data:{idvariant : idvariant, idbranch: idbranch, branches: branches},
            success: function(data) {
                $('.data_1').html(data);
            }
        });
    });
    $(document).on("click", ".insurance_reconciliation_btn", function(event) {
        var ce = $(this);
        var parentDiv=$(ce).closest('td').parent('tr');
        var idrecon=ce.val();
        var received_amt=$(parentDiv).find('.received_amt').val();
        var iduser=$('#iduser').val();
        var actv_code=$(parentDiv).find('.actv_code').val();
        if(received_amt === ''){
            swal('😠 Received Amount are mandatory', 'Fill required fields', 'warning');
            return false;
        }else{
            jQuery.ajax({
                url: "<?php echo base_url('Reconciliation/ajax_insurance_reconciliation_byid') ?>",
                method:"POST",
                dataType: 'json',
                data:{idrecon:idrecon,received_amt:received_amt,iduser:iduser},
                success:function(data){
                    if(data.result == "Success"){
                        $('.alert_msg').show();
                        $('.alert_msg').text('Reconciliation done, Activation Id '+actv_code);
                        $(parentDiv).remove();
                    }else{
                        swal('😠 Failed to receive Payment', 'Entry not added! Please try Again!', 'warning');
                    }
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-lumx fa-lg"></span> Insurance Reconcilation</h3></center><div class="clearfix"></div><hr>
<form id="bank_recon_form">
    <div class="col-md-3"> Product
        <select class="form-control input-sm required" name="idvariant" id="idvariant" required="">
            <option value="">Select Product</option>
            <?php foreach ($model_variant as $variant) { ?>
                <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
            <?php } ?>
        </select>
    </div>
     <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
    <?php } else { ?>
        <div class="col-md-2">Branch
            <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
                <option value="0">All Branches</option>
                <?php foreach ($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php $branches[] = $branch->id_branch; } ?>
            </select>
        </div>
        <input type="hidden" name="branches" id="branches" value="<?php echo implode(',',$branches) ?>">
    <?php } ?>

    <div class="col-md-3"> Activation/Reference Code
        <input type="text" name="trans_id" id="trans_id" class="form-control input-sm required" placeholder="Enter Transaction Id" required="" />
    </div>
    <div class="col-md-2"> Enter IMEI/SRNO
        <input type="text" name="imei" id="imei" class="form-control input-sm required" placeholder="Enter IMEI/SRNO" required="" />
    </div>
    <div class="col-md-2"> Enter Amount
        <input type="number" name="amount" id="amount" class="form-control input-sm required" placeholder="Enter Amount" step="0.001" required="" min="1" />
    </div>
    <input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->userdata('id_users') ?>">
    <input type="submit" value="submit" style="display: none">
</form>
<div class="alert_msg"></div>
<div class="clearfix"></div><br>
<div class="col-md-5">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
    </div>
</div>
<div class="col-md-4">
    <div id="count_1" class="text-info"></div>
</div>
<div class="col-md-2">
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('pending_recon <?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div><br>
<div class="thumbnail" style="height: 700px; overflow: auto; padding: 0">
    <table id="pending_recon <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-bordered table-hover daybook">
        <thead class="fixedelement">
            <th>Date</th>
            <th>Invoice</th>
            <th>Product</th>
            <th>Activation code</th>
            <th>IMEI</th>
            <th>Amount</th>
            <th>Received Amount</th>
            <th>Action</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($insurance_pending_recon as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td>
                    <input type="number" class="form-control input-sm received_amt" placeholder="Enter Amount" step="0.001" min="1" />
                    <input type="hidden" class="actv_code" value="<?php echo $recon->activation_code ?>" />
                </td>
                <td>
                    <button class="btn btn-primary btn-sm insurance_reconciliation_btn" value="<?php echo $recon->id_insurance_reconciliation ?>" style="margin: 0">Receive</button>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>