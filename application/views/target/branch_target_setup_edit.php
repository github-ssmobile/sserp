<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idzone = $('#idzone') .val();
            var monthyear = $('#monthyear') .val();
            var lastmonthyear = $('#lastmonthyear') .val();
            
            if(idzone != '' && monthyear != '' && lastmonthyear != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Target/ajax_edit_get_branch_target_setup_data",
                    method:"POST",
                    data:{idzone: idzone, monthyear: monthyear, lastmonthyear: lastmonthyear},
                    success:function(data)
                    {
                        $('#report_data').html(data);
                    }
                });
            }else{
                alert("Select Data Properly ! ");
                return false;
            }
        });
    });
    
    $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });
    });
</script>
<style>
    .fixheader {
        /*background-color: #fbf7c0;*/
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixheader1 {
        position: sticky;
        top: 30px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  .fixleft1{
    position: sticky;
    left:45px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #c6e6f5;

  }
  .fixleft2{
    position: sticky;
    left:150px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  .textcenter{
      text-align: center;
  }
  
  .table{
      border-collapse: separate;
      border-spacing: 0;
  }
  .borderleft{
      border-left: 1px solid #999999;
  }
</style>
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Branch Target Setup Edit</h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-1"><b>Zone</b></div>
        <div class="col-md-2">
            <select name="idzone" class="form-control" id="idzone">
                <option value="">Select Zone</option>
                <?php foreach ($zone_data as $zone){  ?>
                <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name;?></option>
                <?php  } ?>
            </select>
        </div>
        <div class="col-md-1"><b>Month</b></div>
        <div class="col-md-2">
            <input type="text" class="form-control monthpick" readonly="" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
            <!--            <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">-->
        </div>
        <div class="col-md-2"><b>Last Month</b></div>
        <div class="col-md-2">
            <input type="text" class="form-control monthpick" placeholder="Select Month" id="lastmonthyear" name="lastmonthyear"  value="<?php echo date('Y-m');?>">
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary" id="btnreport">Filter</button>
        </div>
       <div class="clearfix"></div><hr>
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_target_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="report_data" style="height: 800px;overflow-x: auto;padding: 0">
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>