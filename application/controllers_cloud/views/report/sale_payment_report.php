<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('#payment_head').click(function(){
            $('#payment_mode').val('');
        });
        $('#payment_head').change(function(){
            var payment_head = $(this).val();
            if(payment_head != ''){
                var idbranch = $('#idbranch').val();
                var payment_mode = $('#payment_mode').val();
                var head_name = $("#payment_head option:selected").text();
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
                    url:"<?php echo base_url() ?>Report/ajax_get_sale_payment",
                    method:"POST",
                    data:{idpayment_head : payment_head, idbranch: idbranch, idpayment_mode: payment_mode, head_name: head_name},
                    success:function(data)
                    {
                        $(".receivables").html(data);
                    }
                });
            }
        });
        $('#payment_mode, #idbranch').change(function(){
            var payment_head = $('#payment_head').val();
            var payment_mode = $('#payment_mode').val();
            var head_name = $("#payment_head option:selected").text();
            var idbranch = $('#idbranch').val();
            $.ajax({
                url:"<?php echo base_url() ?>Report/ajax_get_sale_payment",
                method:"POST",
                data:{idpayment_mode : payment_mode, idbranch: idbranch, idpayment_head: payment_head, head_name: head_name},
                success:function(data)
                {
                    $(".receivables").html(data);
                }
            });
        });
    });
</script>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-currency-inr fa-lg"></span> Sale Payment Report</h3></center></div><div class="clearfix"></div><br>
<div>
    <div class="col-md-3 col-sm-3 col-xs-9" style="padding: 2px">Payment Head
        <select class="form-control input-sm" name="payment_head" id="payment_head">
            <option value="">Select Payment Head</option>
            <?php foreach ($payment_head as $head){ ?>
            <option value="<?php echo $head->id_paymenthead; ?>"><?php echo $head->payment_head; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-9" style="padding: 2px">Payment Mode
        <select class="form-control input-sm" name="payment_mode" id="payment_mode">
            <option value="">Select Payment Mode</option>
        </select>
    </div>
    <?php if($_SESSION['level'] != 1){ ?>
        <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>">
    <?php }else{ ?>
        <div class="col-md-3 col-sm-3 col-xs-9" style="padding: 2px">Select Branch
            <select class="form-control input-sm" name="idbranch" id="idbranch">
                <option value="">Select Branch</option>
                <?php foreach ($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('sale_payment <?php echo date('d-m-Y h_i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div>
    <div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <table id="sale_payment <?php echo date('d-m-Y h_i a') ?>" class="table table-condensed table-bordered white table-hover receivables" style="margin-bottom: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>