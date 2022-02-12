<?php include __DIR__.'../../header.php';

?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idbranch = $('#idbranch') .val();
            var idzone = $('#idzone') .val();
            var monthyear = $('#monthyear') .val();
            var acc_id = $('#idbranch option:selected').attr('acc_id');
            var acc_idbranch = $('#acc_idbranch').val();
            if(idbranch != '' && idzone != ''){
                alert("Select Any one from branch / Zone");
                return false;
            }else{
                if(monthyear != '' && idbranch != '' && acc_id != ''){
                 
                 $.ajax({
                    url:"http://117.247.86.62:8088/ssweb/index.php/stock/sale/ajax_get_branch_qualified_sale",
                    method:"POST",
                    datatype : "json",
                    data:{monthyear: monthyear, acc_id : acc_id , acc_idbranch: acc_idbranch},
                    success:function(data)
                    {
                       $('#infield').val(data);
                       $('#infield').trigger("change");
                   }
               });
             }
             if(monthyear != '' && idzone != ''){
                 $.ajax({
                    url:"http://117.247.86.62:8088/ssweb/index.php/stock/sale/ajax_get_branch_qualified_sale",
                    method:"POST",
                    datatype : "json",
                    data:{monthyear: monthyear, acc_id : acc_id, acc_idbranch: acc_idbranch},
                    success:function(data)
                    {
                       $('#infield').val(data);
                       $('#infield').trigger("change");
                   }
               });
             }
         }
     });
        $('#infield').change(function (){
            var idbranch = $('#idbranch') .val();
            var monthyear = $('#monthyear') .val();
            var acc_data = $('#infield').val();
            var acc_id = $('#idbranch option:selected').attr('acc_id');
            var idzone = $('#idzone') .val();
            if(idbranch != '' && idzone != ''){
                alert("Select Any One From Branch & Zone");
                return false;
            }
            else{ 
             $.ajax({
                url:"<?php echo base_url()?>Costing/ajax_get_combined_report",
                method:"POST",
                data:{monthyear: monthyear,idbranch: idbranch, acc_data : acc_data,  idzone: idzone},
                success:function(data)
                {
                    $('#report_data').html(data);
                }
            });
         }

     });
        
        $('#idzone').change(function (){
            var idzone = $('#idzone').val();
            if(idzone != ''){
                $.ajax({
                    url:"<?php echo base_url()?>Costing/ajax_get_branch_byidzone",
                    method:"POST",
                    data:{idzone: idzone},
                    success:function(data)
                    {
                        $('#acc_idbranch').val(data);
                    }
                });
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Branch Combined Report </h3></center></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>
<div  style="padding: 20px 10px; margin: 0">
    <input type="hidden" name="infield" id="infield">
    <div class="col-md-2">
        <b>Month</b>
        <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
    </div>
    <div class="col-md-3">
        <b>Branch</b>
        <select name="idbranch" class="form-control" id="idbranch">
            <option value="">Select Branch</option>
            <?php foreach ($branch_data as $bdata){  ?>
                <option value="<?php echo $bdata->id_branch; ?>" acc_id="<?php echo $bdata->acc_branch_id;?>"><?php echo $bdata->branch_name;?></option>
            <?php  } ?>
        </select>
    </div>
    <div class="col-md-3">
        <b>OR Zone</b>
        <select name="idzone" class="form-control" id="idzone">
            <option value="">Select Zone</option>
            <?php foreach ($zone_data as $zone){  ?>
                <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name;?></option>
            <?php  } ?>
        </select>
        <input type="hidden" name="acc_idbranch" id="acc_idbranch">
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
        <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_costing_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div> 
    <div class="clearfix"></div><br>
    <div id="report_data" style="overflow-x: auto;height: 700px">
    </div>
</div>
<div class="clearfix"></div>

<?php include __DIR__.'../../footer.php'; ?>