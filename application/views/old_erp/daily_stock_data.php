<?php include __DIR__.'../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#download_stock_data').click(function(){
        var dateformat = $('#date').val().split('-');
        var dat = dateformat[2]+'-'+dateformat[1]+'-'+dateformat[0];
        var address = "<?php echo base_url('assets/stock_sheets/stock_data_opening_') ?>"+dat+".csv";
        $.ajax({
            url:address,
            error: function()
            {
               swal('Alert!', 'stock excel file of '+dat+' does not exists', 'warning');
               return false;
            },
            success: function()
            {
                window.open(address, "_blank","toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400");
            }
        });
    });
     $(document).on("click", "#get_stock_data", function (event) {
         
            var report_date = $('#date').val();
            
            $.ajax({
                url:"<?php echo base_url() ?>Old_erp/get_daily_stock_manual",
                method:"POST",
                data:{report_date : report_date},
                success:function(data)
                {
                 
                    $("#stock_data").html(data);
                }
            });
        });
});
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-upload"></span> Daily Stock Data</h3></center></div><div class="clearfix"></div><hr>
<div class="col-md-3 col-md-offset-3">
    <input type="text" class="form-control input-lg" id="date" data-provide="datepicker" placeholder="Select date" onfocus="blur()" />
</div>
<div class="col-md-4">
   <button id="get_stock_data" class="btn btn-primary">Get Data</button>
    <button id="download_stock_data" class="btn btn-primary">Download</button>
    
</div>
<div id="stock_data">
 
</div>
<?php include __DIR__.'../../footer.php'; ?>