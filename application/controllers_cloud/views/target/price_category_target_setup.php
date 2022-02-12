<?php include __DIR__.'../../header.php'; ?>

<script>
    $(document).ready(function (){
        $('#sidebar').addClass("active");
        $('#btnreport').click(function (){
            var idbranch = $('#idbranch') .val();
            var monthyear = $('#monthyear') .val();
            var idpcat = $('#idpcat') .val();
            
            if(idbranch != '' && monthyear != '' && idpcat != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Target/ajax_get_price_category_setup",
                    method:"POST",
                    data:{idbranch: idbranch, monthyear: monthyear, idpcat: idpcat},
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
        
//        $('#monthyear').change(function (){
//            var month = $('#monthyear').val();
//            $.ajax({
//                url:"<?php echo base_url() ?>Target/get_target_slab_for_setup_bymonth",
//                method:"POST",
//                data:{month: month},
//                success:function(data)
//                {
//                    $('#slab_data').html(data);
//                }
//            });
//        });
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
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Price Category Target Setup </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div style="padding: 20px 10px; margin: 0">
         <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
         <?php } else { ?>
            <div class="col-md-1"><b>Branch</b></div>
            <div class="col-md-2">
                <select name="idbranch" class="form-control chosen-select" id="idbranch">
                    <option value="">Select Branch</option>
                    <?php foreach ($branch_data as $branch){  ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name;?></option>
                    <?php  $branches[] = $branch->id_branch; } ?>
                </select>
                <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
            </div>
        <?php } ?>
        <div class="col-md-1"><b>Product Category</b></div>
        <div class="col-md-2">
            <select name="idpcat" class="form-control chosen-select" id="idpcat">
                <option value="">Select Product Category</option>
                <?php foreach ($product_category as $pcat){  ?>
                    <option value="<?php echo $pcat->id_product_category; ?>"><?php echo $pcat->product_category_name;?></option>
                <?php  } ?>
            </select>
        </div>
        <div class="col-md-1"><b>Month</b></div>
        <div class="col-md-2">
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('price_category_target_setup');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="target_data"> </div>
        <br>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>