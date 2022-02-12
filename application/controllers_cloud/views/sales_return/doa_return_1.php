<?php include __DIR__ . '../../header.php'; ?>
<!--// link_tag('assets/css/bootstrap-select.min.css')-->
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    var checked = 0;
    $(document).on('keydown', 'input[id=invno]', function(e) {
        var invno = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Sales_return/search_sales_doa_return_invoice_byinvno",
                method:"POST",
                data:{invno : invno, branch: branch, level: level},
                success:function(data)
                {
                    $("#invoice_data").html(data);
                }
            });
        }
    });
    
    $(document).on('change', 'input[id=chk_return]', function() {
        checked = checked + 1;
    });
    $(document).on("click", "#btn_product_return", function (event) {
        if(checked == 0){
            swal("Select any product!", "😠 Select product for return", "warning");
            return false;
        }
    });
});
</script>
<center><h3 style="margin: 0"><span class="mdi mdi-keyboard-return fa-lg"></span> Sales Return - DOA Return</h3></center><div class="clearfix"></div><hr>
<form>
    <div class="col-md-1 col-sm-2">Invoice No</div>
    <div class="col-md-4 col-sm-7">
        <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
    </div><div class="clearfix"></div>
    <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
    <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
    <input type="hidden" class="form-control input-sm" name="sales_return_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
</form>
<?php include __DIR__ . '../../footer.php'; ?>