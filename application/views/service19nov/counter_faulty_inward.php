<?php include __DIR__ . '../../header.php'; ?>
<style>
h1 {
  font-size: 150%;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 400;
  padding-top: 10px;
}
header {
  /*background-color: #fff;*/
  color: #fff;
  background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
  box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
  border-radius: 5px;
}
header p {
  font-family: 'Allura';
  color: #fff;
  margin-bottom: 0;
  font-size: 32px;
  margin-top: -20px;
}
</style>
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    var products = [], checked;
    $(document).on('keydown', 'input[id=imei_no]', function(e) {
        var imei = $(this).val();
        var invno=0;
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/search_stock_byimei",
                method:"POST",
                data:{imei : imei, branch: branch, level: level},
                success:function(data)
                {
                    products = [];
                    checked = 0;
                    $("#invoice_data").html(data);
                     $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
   $(document).on('change', '#idproblem', function() {
        $('.problem').val($('option:selected',this).text());
    });
    $(document).on("click", "#btn_inward", function (event) {
        if(!confirm("Do you want to submit entry")){
            return false;
        }
    });
});
</script>
<center><h3 style="margin: 0"><span class="mdi mdi-cellphone-link-off fa-lg"></span> Counter Faulty Inward </h3></center><div class="clearfix"></div><hr>
    <div class="col-md-12">
        <form>
            <div class="col-md-1 col-sm-2">IMEI No</div>
            <div class="col-md-4 col-sm-7">
                <input type="text" class="form-control" id="imei_no" placeholder="Search IMEI Number"/>
            </div>
            <div class="clearfix"></div>
            <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
            <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
            <div class="clearfix"></div><br>
            <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
        </form>
    </div><div class="clearfix"></div>
<?php include __DIR__ . '../../footer.php'; ?>