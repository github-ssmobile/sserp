<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.submit_btn').click(function (){
            var idbranch = $('#idbranch') .val();
            var idpayment_mode = $('#idpayment_mode') .val();
            if(idbranch == '' || idpayment_mode == ''){
                alert("Select Branch and payment mode");
                return false;
            }else{
                if(!confirm('Do You Want To Upload File')) {
                    return false;
                }
            }
        });
        $('#idpayment_mode').change(function (){
            var payment_head = $('option:selected', this).attr('payment_head');
            $('#idpayment_head') .val(payment_head);
        });
        $('#idbranch').change(function (){
            var branch_code = $('option:selected', this).attr('branch_code');
            $('#branch_code') .val(branch_code);
        });
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-upload"></span> Upload Sale by Payment Mode</h3></center></div><div class="clearfix"></div><hr>
<div class="col-md-10 thumbnail col-md-offset-1" style="border-radius: 8px">
    <?php
    $invdate = '2/9/2020';
    $invdate1 = '2-9-2020';
    if (strpos($invdate, '/') !== false) {
        $output1 = explode('/', $invdate);
    }
    if (strpos($invdate1, '-') !== false) {
        $output2 = explode('-', $invdate1);
    }
//    echo print_r($output1).' <hr>'.print_r($output2);
//    $date = $output1[2].'-'.$output1[1].'-'.$output1[0];
//    die($date);
    ?>
    <div class="col-md-5 thumbnail" style="padding: 10px;border: none; padding: 0">
        <img src="<?php echo base_url()?>assets/images/rocket_sceince.gif" style="height: auto;" />
    </div>
    <div class="col-md-7" style="padding: 10px;">
        <?php echo form_open_multipart() ?>     
        <center><h4>Select and upload CSV File</h4></center><hr><br>
        <div class="col-md-3"><b>Branch</b></div>
        <div class="col-md-9">
            <select class="form-control chosen-select" name="idbranch" id="idbranch" required="">
                <option value="">Select Branch</option>
                <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch?>" branch_code="<?php echo $branch->branch_code ?>"><?php echo $branch->branch_name;?></option>
                <?php } ?>
            </select>
        </div><div class="clearfix"></div><br>
        <div class="col-md-3"><b>Payment Modes</b></div>
        <div class="col-md-9">
            <select data-placeholder="Select Payment Mode" name="idpayment_mode" id="idpayment_mode" class="chosen-select" required="" style="width: 100%">
                <option value="">Select Payment Modes</option>
                <?php foreach ($payment_mode as $mode){ ?>
                <option value="<?php echo $mode->id_paymentmode; ?>" payment_head="<?php echo $mode->idpaymenthead ?>" ><?php echo $mode->payment_mode.' '.$mode->payment_head; ?></option>
                <?php } ?>
            </select>
        </div><div class="clearfix"></div><br>
        <div class="col-md-3"><b>Upload csv </b></div>
        <div class="col-md-9">
            <input type="hidden" class="form-control" name="idpayment_head" id="idpayment_head">
            <input type="hidden" class="form-control" name="branch_code" id="branch_code">
            <input type="file" class="form-control" name="uploadfile" id="uploadfile" required="">
        </div><div class="clearfix"></div><br><hr>
        <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Old_erp/submit_upload_old_sale_by_paymentmode">Upload</button>
        <div class="clearfix"></div>
        <?php echo form_close(); ?>
    </div>
    <div class="clearfix"></div>
</div>
<?php include __DIR__.'../../footer.php'; ?>