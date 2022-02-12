<?php include __DIR__.'../../header.php'; ?>

<script>
    $(document).ready(function (){
        $('#sidebar').addClass("active");
        $('#btnreport').click(function (){
            var idbranch = $('#idbranch') .val();
            var branches = $('#branches') .val();
            var monthyear = $('#monthyear') .val();
            var target_slabs = $('#target_slabs') .val();
            var allslabs = $('#allslabs') .val();
            var idpcat = $('#idpcat').val();
            
             var idzone = $('#idzone') .val();
            var allzone = $('#allzone') .val();
//            alert(target_slabs);
            
             if(idbranch == '' && idzone == ''){
                alert("Select Any One From Branch and Zone");
                return false;
            }else{
                
                if(idbranch != '' && monthyear != '' && target_slabs != '' && idpcat != ''){
                   $.ajax({
                        url:"<?php echo base_url() ?>Target/ajax_get_promotor_target_setup_report_byidbranch",
                        method:"POST",
                        data:{idbranch: idbranch, monthyear: monthyear, branches: branches, target_slabs: target_slabs, idpcat: idpcat, allslabs : allslabs},
                        success:function(data)
                        {
                            $('#target_data').html(data);
                        }
                    });
                }
                if(idzone != '' && monthyear != '' && target_slabs != '' && idpcat != ''){
                   $.ajax({
                        url:"<?php echo base_url() ?>Target/ajax_get_promotor_target_setup_report_byidzone",
                        method:"POST",
                        data:{idzone: idzone, monthyear: monthyear, allzone: allzone, target_slabs: target_slabs, idpcat: idpcat, allslabs : allslabs},
                        success:function(data)
                        {
                            $('#target_data').html(data);
                        }
                    });
                }
            }
        });
        
          $('#monthyear').change(function (){
            var month = $('#monthyear').val();
            $.ajax({
                url:"<?php echo base_url() ?>Target/get_target_slab_data_bymonth",
                method:"POST",
                data:{month: month},
                success:function(data)
                {
                    $('#slab_data').html(data);
                }
            });
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
    left:70px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #c6e6f5;

  }
  .fixleft2{
    position: sticky;
    left:150px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  
 table {
    border-collapse: separate;
    border-spacing: 0;
    
}
/*
.table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
    border: 1px solid #666666;
       
}*/
</style>
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Promotor Target Setup Report</h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div style="padding: 20px 10px; margin: 0">
        
        <div class="col-md-2"><b>Product Category</b>
            <select class="form-control" id="idpcat" name="idpcat">
                <option value="">Select Product category</option>
                <?php foreach ($product_cat_data as $pcat){ ?>
                    <option value="<?php echo $pcat->id_product_category?>"><?php echo $pcat->product_category_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2"><b>Month</b>
            <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear"  required="">
        </div>
        <div class="col-md-2"><b>Target Slabs</b>
            <div id="slab_data">
                <select class="form-control input-sm" name="target_slab" id="target_slabs">
                    <option value="">Select Slabs</option>
                </select>
            </div>
        </div>
          <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
            <input type="hidden" id="idzone" name="idzone" value="">
         <?php } else { ?>
            <div class="col-md-2"><b>Branch</b>
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
        <?php if($this->session->userdata('level') != 3 && $this->session->userdata('level') != 2){?>
            <div class="col-md-1"><b>OR</b></div>
            <div class="col-md-2">
                <b>Zone</b>
                <select name="idzone" class="form-control chosen-select" id="idzone">
                    <option value="">Select Zone</option>
                    <!--<option value="all">Overall Zone</option>-->
                    <option value="0">All Zones</option>
                    <?php foreach ($zone_data as $zone){  ?>
                        <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name;?></option>
                    <?php  $zones[] = $zone->id_zone; } ?>
                </select>
                <input type="hidden" name="allzone" id="allzone" value="<?php echo implode($zones,',') ?>">
            </div>
        <?php } else{ ?>
            <input type="hidden" id="idzone" name="idzone" value="">
        <?php } ?>   
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('promotor_target_setup_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="target_data"> </div>
        <br>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>