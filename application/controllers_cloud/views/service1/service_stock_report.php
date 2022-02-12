<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".service_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#idbranch').val();
            var status = +$('#status').val();
           
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_service_stock_report",
                method:"POST",
                data:{ status:status,brand: brand, idbranch: branch, product_category: product_category},
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
        <h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Service Stock</h3>
    </center>
</div>
<div class="clearfix"></div>
 <div class="col-md-1"></div>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" id="status" name="status">
            <option value="">Select Status</option>
            <option value="0">All</option>
            <option value="1">Branch Inward</option>
            <option value="2">Sent To Care</option>
            <option value="11">Closed</option>
            <!--<option value="3">Force DOA</option>-->            
        </select>
    </div>

      <?php  if(count($branch_data)==1){ ?>
            <input type="hidden" value="<?php echo $branch_data[0]->id_branch; ?>" name="idbranch" id="idbranch">        
     <?php }else{ ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch" required="">
            <option value="">Select Branch</option>   
            <option value="0">All</option>         
            <?php foreach($branch_data as $branch){ ?>                
                <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="">Product Category</option>
            <option value="0">All</option>
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="">Select Brand</option>
            <option value="0">All</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
        <div class="col-md-2" style="text-align: center;">
            <button type="button"  class="service_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Generate</button>
        </div>
        
        
    <div class="clearfix"></div>
    <br>
    
    <div class="thumbnail" style="overflow: auto;padding: 0">
         <br> 
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
        <br>
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