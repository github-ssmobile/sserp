<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
           var invoice = $('#invoice').val();
           var imei = $('#imei').val();
           var mobile = $('#mobile').val();          
           if(invoice != '' || imei != '' || mobile !='' ){
               $.ajax({
                    url:"<?php echo base_url() ?>Old_erp/ajax_search_invoice",
                    method:"POST",
                    data:{invoice: invoice, imei: imei, mobile: mobile},
                    success:function(data)
                    {
                        $('#sale_data').html(data);
                    }
                });
           }else{
               alert("Please enter valid data");
               return false;
           }
       }); 
    });
</script>
<style>

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-magnify fa-lg"></span> Sale report</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-2">
    <b>Invoice Number</b>
    <input type="text" class="form-control" id="invoice" name="invoice" required="" placeholder="Enter Invoice Number">
</div>
<div class="col-md-2">
    <b>IMEI</b>
    <input type="text" class="form-control" id="imei" name="imei" required="" placeholder="Enter IMEI number">
</div>
<div class="col-md-2">
    <b>Customer Mobile</b>
    <input type="text" class="form-control" id="mobile" name="mobile" required="" placeholder="Enter Customer Mobile">
</div>


<div class="col-md-2">
    <br>
    <button class="btn btn-primary" id="btnsubmit">Search</button>
</div>
<div class="clearfix"></div><hr>
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
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('sale_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div><div class="clearfix"></div><br>
<div id="sale_data">
    
</div>
<?php include __DIR__.'../../footer.php'; ?>