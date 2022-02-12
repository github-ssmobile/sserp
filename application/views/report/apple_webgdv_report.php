<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('.btnrep').click(function (){
          var fromdate = $('#fromdate').val();
          var todate = $('#todate').val();
            if(fromdate != '' && todate != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Master/ajax_apple_webgdv_report",
                    method:"POST",
                    data:{fromdate : fromdate, todate : todate},
                    success:function(data)
                    {
                        $("#apple_report").html(data);
                    }
                });
            }else{
                alert("Please Select date");
                return false;
            }
       });
    });
</script>
<style>
    .fixheader {
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
</style>
<div align="center" class="text-darken-4 col-md-8 col-md-offset-1 text-center">
    <span class="mdi mdi-file-document fa-2x"> Apple WEB GDV Report</span>
</div>
<div class="col-md-1 pull-right">
  
</div><div class="clearfix"></div><hr>
<!--<h4 style="color: #ff3333"><center>File Allowed To Download Once In Day</center></h4>-->
 <?php if( $save = $this->session->flashdata('alert_dms_data')): ?>
        <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
            <?= $save ?>
        </div>
<?php endif; ?>
    <div class="col-md-1">From</div> 
    <div class="col-md-2">
        <input type="text" name="fromdate" id="fromdate" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" required="">
    </div>
    <div class="col-md-1">To</div> 
    <div class="col-md-2">
        <input type="text" name="todate" id="todate" class="form-control input-sm" data-provide="datepicker" placeholder="To Date" required="">
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary btnrep">Search</button>
    </div>
   <div class="clearfix"></div><br>
    <div class="col-md-4 col-sm-4 col-xs-4 ">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
        <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('apple_webgdv_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div> 
   <div class="clearfix"></div><br>
   <div id="apple_report"></div>
   <div class="clearfix"></div><br>
<?php include __DIR__ . '../../footer.php'; ?>