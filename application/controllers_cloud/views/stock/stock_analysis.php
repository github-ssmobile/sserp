<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".aa_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();            
             var days = +$('#days').val();    
             var idgodown = +$('#idgodown').val();
             var warehouse = +$('#warehouse').val();
             
            if(!idgodown && !brand && !product_category){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_stock_analysis_report",
                method:"POST",
                data:{ brand: brand,  idproductcategory: product_category,idgodown:idgodown,days:days,warehouse:warehouse},
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
.fixedelement2 {
  background-color: #fbf7c0;
  position: sticky;
  top: 100px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 99;
}
</style>
<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Stock Analysis Stock</h3>
    </center>
</div>
<div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">    
<div class="col-md-1" style="margin-top: 12px;">Days Sale</div>
    <div class="col-md-1" style="margin-top: 7px;">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>
    <input type="hidden" value="1" name="idgodown" id="idgodown">    
    
    <?php if (count($branch_data)==1){ ?>
        <input type="hidden" value="<?php echo $branch_data[0]->id_branch ?>" name="warehouse" id="warehouse">    
    <?php } else{ ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="warehouse" id="warehouse">
            <?php foreach ($branch_data as $branch) { ?>
                    <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>        
    </div>    
    <?php }?>
    
    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="">Product Category</option>
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="">Select Brand</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
        <div class="col-md-2" style="text-align: center;">
            <button type="button"  class="aa_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Generate</button>
        </div>
        
    <div class="clearfix"></div>
    </div>
    <br>
    <div class="thumbnail" >
         <div class="col-md-4">
            
        </div>
        <div class="col-md-6">
            <div id="count_1" class="text-info"></div>
        </div>
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div style="overflow: auto;height: 700px">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
            </table>
        </div>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>