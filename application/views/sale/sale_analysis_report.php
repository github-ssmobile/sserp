<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
            var days = $('#days').val();
            var idpcat = $('#idpcat').val();
            var allpcat = $('#allpcat').val();
            var idbrand = $('#idbrand').val();
            var allbrand = $('#allbrand').val();
            if(days != '' && idpcat != '' && idbrand != ''){
               $.ajax({
                    url:"<?php echo base_url() ?>Sale/ajax_get_day_sale_analysis_report",
                    method:"POST",
                    data:{days: days, idpcat: idpcat, idbrand: idbrand, allpcat : allpcat, allbrand : allbrand},
                    success:function(data)
                    {
                        $('#sale_data').html(data);
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
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="fa fa-cubes fa-lg"></span> Sale Analysis report</h3></center></div><div class="clearfix"></div><hr><br>
<div class="col-md-1"><b>Days</b></div>
<div class="col-md-1">
    <input type="text" class="form-control input-sm" name="days" id="days" value="30">
</div>
<div class="col-md-2"><b>Product Category</b></div>
<div class="col-md-2">
    <select class="form-control chosen-select" name="idpcat" id="idpcat">
        <option value="0">All</option>
        <?php foreach($product_category as $pcat){ ?>
            <option value="<?php echo $pcat->id_product_category ?>"><?php echo $pcat->product_category_name ?></option>
        <?php $allpcat[] = $pcat->id_product_category; } ?>
    </select>
     <input type="hidden" name="allpcat" id="allpcat" value="<?php echo implode($allpcat,',') ?>">
</div>
<div class="col-md-1"><b>brand</b></div>
<div class="col-md-2">
    <select class="form-control chosen-select" name="idbrand" id="idbrand">
        <option value="0">All</option>
        <?php foreach($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
        <?php $allbrand[] = $brand->id_brand; } ?>
    </select>
    <input type="hidden" name="allbrand" id="allbrand" value="<?php echo implode($allbrand,',') ?>">
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
    <button type="post" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('sale_analysis_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
</div><div class="clearfix"></div><br>
<div id="sale_data" style="overflow-x: auto;height: 680px">
    
</div>
<?php include __DIR__.'../../footer.php'; ?>