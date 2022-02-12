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
                url: "<?php echo base_url('Reconciliation/ajax_bank_reconciliation') ?>",
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.result == "Success"){
                        var row = '<tr><td>'+$('#date').val()+'</td>\n\
                                   <td>'+$("#idpayment_mode option:selected").text()+'</td>\n\
                                   <td>'+$("#idbank option:selected").text()+'</td>\n\
                                   <td>'+$('#trans_id').val()+'</td>\n\
                                   <td>'+$('#amount').val()+'</td></tr>';
                        $('#first_row').prepend(row);
                        $('.alert_msg').show();
                        $('.alert_msg').text('Last reconciliation Tranx Id '+$('#trans_id').val());
                        $('#trans_id').val('');
                        $('#amount').val('');
                    }else{
                        swal('Alert Failed!', 'Bank reconciliation failed to submit!!', 'warning');
                    }
                }
            });
        }
    });
});
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-bank fa-lg"></span> Bank Reconcilation</h3></center><div class="clearfix"></div><hr>
<form id="bank_recon_form">
    <div class="col-md-3">
        Select Payment Modes
        <select data-placeholder="Select Payment Mode" name="idpayment_mode" id="idpayment_mode" class="chosen-select" required="" style="width: 100%">
            <?php foreach ($payment_mode as $mode){ ?>
            <option value="<?php echo $mode->id_paymentmode; ?>"><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-3"> Select Bank
        <select name="idbank" id="idbank" class="chosen-select required" style="width: 100%" required="">
            <?php foreach ($bank_data as $bank){ ?>
            <option value="<?php echo $bank->id_bank; ?>"><?php echo $bank->bank_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2"> Select Date
        <input type="text" name="date" id="date" data-provide="datepicker" class="form-control required" placeholder="Select Date" required="" onfocus="blur()" />
    </div>
    <div class="col-md-2"> Enter Transaction Id
        <input type="text" name="trans_id" id="trans_id" class="form-control required" placeholder="Enter Transaction Id" required="" />
    </div>
    <div class="col-md-2"> Enter Amount
        <input type="number" name="amount" id="amount" class="form-control required" placeholder="Enter Amount" step="0.001" required="" min="0.1" />
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
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-4">
    <div id="count_1" class="text-info"></div>
</div>
<div class="col-md-2">
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('reconciliation_report <?php echo date('d-m-Y h:i a') ?>');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div>
<div class="clearfix"></div><br>

<div class="thumbnail" style="height: 500px; overflow: auto; padding: 0">
    <table id="reconciliation_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0">
        <thead class="fixedelement">
            <th>Date</th>
            <th>Mode</th>
            <th>bank</th>
            <th>Transaction Id</th>
            <th>Amount</th>
        </thead>
        <tbody id="first_row">
        </tbody>
        <tbody class="data_1">
            <?php foreach ($bank_recon as $recon){ ?>
            <tr>
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->payment_mode. ' '.$recon->payment_head ?></td>
                <td><?php echo $recon->bank_name.' '.$recon->bank_branch ?></td>
                <td><?php echo $recon->transaction_id ?></td>
                <td><?php echo $recon->amount ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>