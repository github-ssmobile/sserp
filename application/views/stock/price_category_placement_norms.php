<?php include __DIR__.'../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span>Price Category Placement Norms </h3></center>
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
    $('.btnreport').click(function () {
        var product_category = $('#product_category').val();
        var branch = $('#branch').val();                
        var days = $('#days').val(); 
        if (branch && product_category) {
            $.ajax({
                url: "<?php echo base_url() ?>Stock/ajax_get_price_category_stock_norms",
                method: "POST",
                data: {product_category: product_category,branch: branch,days:days},
                success: function (data)
                {
                    $('#model_price_data').html(data);
                }
            });
        } else {
            alert("Please select Branch/Product category ");
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
        <?php if($this->session->userdata('level') == 2){ ?>
            <input type="hidden" class="form-control" name="branch" id="branch" value="<?php echo $_SESSION['idbranch']; ?>">
        <?php } else { ?>
            <div class="col-md-3">
                <select class="chosen-select form-control" name="branch" id="branch" required="">
                    <option value="">Select Branch</option>
                    <?php foreach ($branch_data as $branch) { ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->zone_name.' - '.$branch->branch_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>
        <div class="col-md-3">
            <select class="chosen-select form-control" name="product_category" id="product_category" required="">
                <option value="">Select Product Category</option>
                <?php foreach ($product_category as $type) { ?>
                    <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-primary btnreport">Search</button></div>
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
        <div id="model_price_data"> </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>