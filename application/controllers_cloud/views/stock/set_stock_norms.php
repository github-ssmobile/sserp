<?php include __DIR__.'../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Setup Placement Norms </h3></center>

<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>

<style>
    
    .btn-outline-info {
    color: #17a2b8  !important;
    background-color: transparent  !important;
    background-image: none !important;    
    margin: 0 !important;
    /*box-shadow: none !important;*/
    border: 1px solid #17a2b8 !important;
    
    padding: 5px 10px !important;
    text-transform: initial  !important;
     } 
    
</style>
<script>
    
$(document).ready(function(){
   
           
            $('#idbrand').change(function () {

                var product_category = $('#product_category').val();
                var branch = $('#branch').val();                
                var category = 0;
//                if ($('#category').val()) {
//                    category = $('#category').val();
//                }
                var brand = 0;
                if ($('#idbrand').val()) {
                    brand = $('#idbrand').val();
                }
                 var days = $('#days').val(); 
                if (branch && product_category && brand) {
                    $.ajax({
                        url: "<?php echo base_url() ?>Stock/ajax_get_model_stock_norms",
                        method: "POST",
                        data: {category: category, brand: brand, product_category: product_category,branch: branch,days:days},
                        success: function (data)
                        {
                            $('#model_price_data').html(data);
                            $(".stock-norms").show();
                        }
                    });
                } else {
                    alert("Please select Branch/Product category/Brand first!!");
                }
            });


        $(document).on("click", ".stock-norms", function(event) {   
            event.preventDefault();
            
            var serialized = $('.stock-form').serialize();
             $.ajax({
                        url: "<?php echo base_url() ?>Stock/ajax_save_model_stock_norms",
                        method: "POST",
                        data: serialized,
                        dataType:'json',
                        success: function (data)
                        {
                            
                            if(data.data === 'success'){
                                alert("Placement norms submitted successfully!!");
                                location.reload();	
                            }else if(data.data === "fail"){
                                alert("Fail to submit stock norms!! ")
                            }else{
                                alert("Select at least one model !! ")
                            }
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
.fixedelement1 {
  background-color: #fbf7c0;
  position: sticky;
  top: 30px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
</style>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 700px;">
    <div id="purchase" style="padding: 20px 10px;">
        <div class="col-md-2">
            <input type="text" name="days" value="30" id="days" class="form-control" placeholder="Last Sale Days" pattern="[0-9]{3}" />
        </div>
        <div class="col-md-2">
            <select class="chosen-select form-control" name="branch" id="branch" required="">
                            <option value="">Select Branch</option>
                            <?php foreach ($branch_data as $branch) { ?>
                                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                            <?php } ?>
                        </select>
        </div>
        <div class="col-md-3">
            <select class="chosen-select form-control" name="product_category" id="product_category" required="">
                            <option value="">Select Product Category</option>
                            <?php foreach ($product_category as $type) { ?>
                                <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
                            <?php } ?>
                        </select>
        </div>
        
        <div class="col-md-2">
            <select class="chosen-select form-control" name="idbrand" id="idbrand" required="">
                <option value="">Select Brand</option>
                <option value="0">All</option>
                <?php foreach ($brand_data as $brand) { ?>
                    <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="clearfix"></div><hr>
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
        <div class="col-md-2">         
        </div>
        <div class="clearfix"></div>
        <br>
        <form class="stock-form">
            <div>
        <table id="model_price_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        
        </table>
                </div>
            <button type="button" id="submit-all" style="display:none"  class="stock-norms pull-right btn btn-info waves-effect"> Save </button>
        </form>
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>