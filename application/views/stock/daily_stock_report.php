<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        
        $('#btnreport').click(function (){
            
            var from = $('#from').val();
            var monthyear = $('#monthyear').val();
            var idpcat = $('#idpcat').val();
            var allpcats = $('#allpcats').val();            
            var idbranch = $('#idbranch').val();
            var allbranches = $('#allbranches').val();
           
                if(from != '' || monthyear !=''){
                   $.ajax({
                        url:"<?php echo base_url() ?>stock/ajax_daily_stock_report_byidbranch",
                        method:"POST",
                        data:{from: from, idpcat: idpcat, idbranch: idbranch, allbranches: allbranches, allpcats: allpcats, monthyear: monthyear },
                        success:function(data)
                        {
                            $('#target_data').html(data);
                        }
                    });
 
            }
        });
        
        $(document).on('change', '#from', function() {
            $('#monthyear').val('');
        });
        $(document).on('change', '#monthyear', function() {
            $('#from').val('');
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
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Stock Summary Report </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
   
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-2">
            <b>Date</b>
            <input type="text" name="from" id="from" data-provide="datepicker" value="<?php echo date('Y-m-d')?>" class="form-control " placeholder="Date" required>
        </div>
        <div class="col-md-2">
            <b>Month</b>
            <input type="text" class="form-control monthpick" placeholder="Select Month" id="monthyear" name="monthyear" value="">
        </div>
         
        <div class="col-md-2">
            <b>Product Category</b>
            <select name="idpcat" class="form-control chosen-select" id="idpcat">
                <option value="0">All Category</option>
                <?php foreach ($product_cat_data as $pcat){  ?>
                    <option value="<?php echo $pcat->id_product_category; ?>"><?php echo $pcat->product_category_name;?></option>
                <?php  $productcat[] = $pcat->id_product_category; } ?>
            </select>
            <input type="hidden" name="allpcats" id="allpcats" value="<?php echo implode($productcat,',') ?>">
        </div>         
        <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
              <input type="hidden" id="idzone" name="idzone" value="">
        <?php } else{ ?>
            <div class="col-md-2">
                <b>Branch</b>
                <select name="idbranch" class="form-control chosen-select" id="idbranch">
                    <option value="">Select Branch</option>
                    <?php if($this->session->userdata('level') == 1){ ?>
                        <option value="all">Group Summary</option>
                    <?php } ?>
                    <option value="0">All Branch</option>
                    <?php foreach ($branch_data as $branch){  ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name;?></option>
                    <?php  $branches[] = $branch->id_branch; } ?>
                </select>
                <input type="hidden" name="allbranches" id="allbranches" value="<?php echo implode($branches,',') ?>">
            </div>
        <?php } ?> 
        <div class="col-md-1">
            <br>
            <button class="btn btn-primary" id="btnreport">Filter</button>
        </div>
       <div class="clearfix"></div><hr>
       <div class="thumbnail" style="overflow: auto;padding: 0">
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('stock_summary_report<?php echo date('d-m-Y');?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <table id="target_data"  class="table table-condensed table-bordered" id="stock_summary_report<?php echo date('d-m-Y');?>">
         </table>
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>