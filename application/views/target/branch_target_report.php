<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var idbranch = $('#idbranch') .val();
            var branches = $('#branches') .val();
            var monthyear = $('#monthyear') .val();
            
            if(idbranch != '' && monthyear != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Target/ajax_get_branch_target_byidbranch",
                    method:"POST",
                    data:{idbranch: idbranch, monthyear: monthyear, branches: branches},
                    success:function(data)
                    {
                        $('#target_data').html(data);
                    }
                });
            }else{
                alert("Select Data Properly ! ");
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
</style>
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Branch Target Report </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
   
    <div  style="padding: 20px 10px; margin: 0">
         <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
         <?php } else{ ?>
        <div class="col-md-1"><b>Branch</b></div>
        <div class="col-md-2">
            <select name="idbranch" class="form-control chosen-select" id="idbranch">
                <option value="">Select Branch</option>
                <option value="0">All Branch</option>
                <?php foreach ($branch_data as $branch){  ?>
                    <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name;?></option>
                <?php  $branches[] = $branch->id_branch; } ?>
            </select>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
        </div>
         <?php } ?>
        <div class="col-md-1"><b>Month</b></div>
        <div class="col-md-2">
            <!--<input type="text" class="form-control" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">-->
            <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_target_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="target_data">
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>