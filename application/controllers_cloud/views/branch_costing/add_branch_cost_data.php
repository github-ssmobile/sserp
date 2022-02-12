<?php include __DIR__.'../../header.php'; ?>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idcostheader = $('#idcostheader') .val();
            var monthyear = $('#monthyear') .val();
            
            if(idcostheader != '' && monthyear != '' ){
               $.ajax({
                    url:"<?php echo base_url() ?>Costing/ajax_get_branch_costing_data",
                    method:"POST",
                    data:{idcostheader: idcostheader, monthyear: monthyear},
                    success:function(data)
                    {
                        $('#report_data').html(data);
                    }
                });
            }else{
                alert("Select Branch Costing Header ! ");
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="fa fa-gear  fa-lg"></span> Add Branch Costing Data </h3></center></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>
<div  style="padding: 20px 10px; margin: 0">
    <div class="col-md-1"><b>Month</b></div>
    <div class="col-md-2">
        <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
    </div>
    <div class="col-md-2"><b>Cost Header</b></div>
    <div class="col-md-3">
        <select name="idcostheader" class="form-control" id="idcostheader">
            <option value="">Select Cost Header</option>
            <?php foreach ($costing_headers as $cheader){  ?>
            <option value="<?php echo $cheader->id_cost_header; ?>"><?php echo $cheader->cost_header_name;?></option>
            <?php  } ?>
        </select>
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
        <div id="report_data">
        </div>
    </div>
<div class="clearfix"></div>
   
<?php include __DIR__.'../../footer.php'; ?>