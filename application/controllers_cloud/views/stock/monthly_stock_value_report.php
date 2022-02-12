<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
            var monthyear = $('#monthyear').val();
            if(monthyear != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Stock/ajax_get_monthly_stock_value_report",
                    method:"POST",
                    data:{monthyear: monthyear},
                    success:function(data)
                    {
                        $('#stock_data').html(data);
                    }
                });
            }else{
               alert("Please Select Date Range");
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
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-magnify fa-lg"></span>Stock Value Report - Monthly Summary</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-1">Month</div>
<div class="col-md-2">
    <input type="text" class="form-control monthpick" placeholder="Select Month"  id="monthyear" name="monthyear" >
</div>

<div class="col-md-2">
    <button class="btn btn-primary" id="btnsubmit">Filter</button>
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
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('stock_value_report_monthly_summary');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div>
<div class="clearfix"></div><br>
<div id="stock_data" style="overflow-x: auto"> 
    
</div>
<?php include __DIR__.'../../footer.php'; ?>