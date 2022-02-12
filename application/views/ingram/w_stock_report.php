<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".quantity_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#branch').val();
            var iswarehouse=1; 
             var idgodown = +$('#idgodown').val();
            if(!idgodown && !brand && !product_category && !branch){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_quantity_stock",
                method:"POST",
                data:{ brand: brand, branch: branch, product_category: product_category,idgodown:idgodown,iswarehouse:iswarehouse},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
        
        $(document).on("click", ".imei_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#branch').val();
            var iswarehouse=1;
             var idgodown = +$('#idgodown').val();
            if(!idgodown && !brand && !product_category && !branch){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_imei_stock",
                method:"POST",
                data:{ brand: brand, branch: branch, product_category: product_category,idgodown:idgodown,iswarehouse:iswarehouse},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
         $(document).on("click", ".intra_imei_stock", function(event) {   
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#branch').val();
            var iswarehouse = 1;
            var idgodown = +$('#idgodown').val();
            if(!idgodown && !brand && !product_category && !branch){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_imei_stock_intransit",
                method:"POST",
                data:{ brand: brand, branch: branch, product_category: product_category,idgodown:idgodown,iswarehouse:iswarehouse},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
    });
</script>
<style>
      table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
 
}
.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
</style>
<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Stock Report</h3>
    </center>
</div>
<div class="clearfix"></div>
<input type="hidden" value="<?php echo $idgodown ?>" name="idgodown" id="idgodown"> 
<input type="hidden" value="<?php echo $_SESSION['idbranch']; ?>" name="branch" id="branch">
<input type="hidden" value="23" name="brand" id="brand">

    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="">Product Category</option>
            <option value="0">All</option>
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>    
             <div class="clearfix"></div><br>
            <div class="col-md-6"></div>
        <div class="col-md-2" style="text-align: center;">
            <button type="button"  class="quantity_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Quantity</button>
        </div>
        <div class="col-md-2" >
            <button type="button"  class="imei_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">IMEI Stock</button>
        </div>
        
    <div class="clearfix"></div>
    <br>
    <div class="thumbnail" style="overflow: auto;padding: 0">
         <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-6">
            <div id="count_1" class="text-info"></div>
        </div>
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div style="height: 650px;">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
            </table>
        </div>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>