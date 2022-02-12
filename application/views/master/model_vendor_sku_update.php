<?php include __DIR__.'../../header.php'; ?>

<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-arrow-right-bold fa-lg"></span>Model - Vendor SKU Update</h3></center></div>
  <div class="col-md-1">
        <a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>
    </div><div class="clearfix"></div><hr>
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
    
   $(document).on("click", ".save", function(event) {  
       
    event.preventDefault();
     
    var parentDiv = $(this).closest('tr');
    var $form = $(this);
    var fd = new FormData();
    fd.append("ids", $(parentDiv).find('input[name="ids"]').val());
    var sku = $(parentDiv).find('input[name="sku"]').val()
    fd.append("sku", sku);
    var sku_column = $(parentDiv).find('input[name="sku_column"]').val()
    fd.append("sku_column", sku_column);
    fd.append("is_ajax", "yes");
    
           jQuery.ajax({
                url: "<?php echo base_url('Master/save_sku_update') ?>",
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                method: 'POST',
                success: function (data, textStatus, jqXHR) {
                    if (data.result == 'yes') {
                        $(parentDiv).css("background", "#e6ffc0");
                        alert("SKU updated successfully!");
                        $(parentDiv).find('.sku').html(sku);
                        setTimeout(function () {
                            $(parentDiv).css("background", "#fff");
                        }, 1500)
                    } else {
                        $(parentDiv).css("background", "#fdb4b4");
                        alert("Fail to update SKU!");
                        setTimeout(function () {
                            $(parentDiv).css("background", "#fff");
                        }, 1500)
                    }
                    $(parentDiv).find(".myDiv2").css("display", "block");
                    $(parentDiv).find(".myDiv1").css("display", "none");
                }
            });        
    });
    
    $('#product_category').change(function () {
        var product_category = $('#product_category').val();
        var type_name = $('#product_category option:selected').text();
        $("#product_category_name").val(type_name);
    });

    $('#idbrand,#product_category,#vendors_skua').change(function () {
        var product_category = $('#product_category').val();
        var vendors_sku = $('#vendors_skua').val();        
        var category = 0;
        var brand = 0;
        if ($('#idbrand').val()) {
            brand = $('#idbrand').val();
        }
        if (vendors_sku !== "") {
            $.ajax({
                url: "<?php echo base_url() ?>Master/ajax_get_model_bycategory_sku",
                method: "POST",
                data: {category: category, brand: brand, product_category: product_category, vendors_sku:vendors_sku},
                success: function (data)
                {
                    $('#model_sku_data').html(data);
                }
            });
        } else {
            alert("Please select product category first!!");
        }
    });

  });
        
    $(document).on("click", ".hide-btn", function(event) {  
        var parentDiv = $(this).closest('tr');
        $(parentDiv).find(".myDiv1").css("display", "block");
        $(parentDiv).find(".myDiv2").css("display", "none");
    });
        
</script>
<div id="pay" class="collapse">
 <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="mdi mdi-file-excel" style="font-size: 28px"></span> Upload CSV File </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/sku.jpg" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <?php echo form_open_multipart('', array('id' => 'pay')) ?>     
                <center><h4>Bulk Update SKU </h4></center>
                    <div class="clearfix"></div><br>
                    <div class="col-md-3"><b> Vendor SKU </b></div>
                    <div class="col-md-9">
                        <select class=" form-control" name="vendors_sku" id="vendors_sku" required="">
                            <option value="0">Select SKU</option>
                            <?php foreach ($sku_data as $skus){ ?>
                                <option value="<?php echo $skus->column_name ?>"><?php echo $skus->vendor_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-3"><b>Upload File </b></div>
                     <div class="col-md-9">
                         <input type="file" name="uploadfile" id="uploadfile" required="">
                    </div>
                    <div class="clearfix"></div><br>
                <div class="clearfix"></div><hr>
                <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Master/save_bulk_sku_update">Submit</button>
                <div class="clearfix"></div><br>
                <?php echo form_close(); ?>
                <div class="clearfix"></div>
            </div>
        </div>
</div>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 700px;">
    <div id="purchase" style="padding: 20px 10px;">
        
        <div class="col-md-3">
            <select class="chosen-select form-control" name="vendors_skua" id="vendors_skua" required="">
                <option value="">Select SKU</option>
                <?php foreach ($sku_data as $skus){ ?>
                    <option value="<?php echo $skus->column_name ?>"><?php echo $skus->vendor_name ?></option>
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
        <div class="col-md-3">
            <select class="chosen-select form-control" name="idbrand" id="idbrand" required="">
                <option value="">Select Brand</option>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_sku_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div><br>
        <table id="model_sku_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        
        </table><div class="clearfix"></div>
        
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>