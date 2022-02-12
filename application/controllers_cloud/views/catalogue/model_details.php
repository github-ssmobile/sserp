<?php include __DIR__.'../../header.php'; ?>


<div class="col-md-9">
        <center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Model</h3></center>
    </div>
<div class="col-md-2 pull-right">
    <a  href="<?php echo base_url('Catalogue/add_model') ?>" class="btn btn-outline-info waves-effect" style="padding: 5px 13px !important;"><i class="fa fa-plus fa-2x" style="margin-right: 10px;"></i>Create Model</a>        
    </div><div class="clearfix"></div>

<style>
.btn-outline-info {
    color: #17a2b8  !important;
    background-color: transparent  !important;
    background-image: none !important;    
    margin: 0 !important;
    border: 1px solid #17a2b8 !important;
    line-height: 21px !important;
    padding: 5px 5px !important;
    text-transform: initial  !important;
}
    
#image-preview {
    width: auto;
    height: auto;
    position: relative;
    overflow: hidden;
    background-color: #ffffff;
    color: #ecf0f1;
}
#image-preview input {
    line-height: 200px;
    font-size: 200px;
    position: absolute;
    height: auto;
    width: auto;
    opacity: 0;
    z-index: 10;
}
#image-preview label {
    position: absolute;
    z-index: 5;
    opacity: 0.3;
    cursor: pointer;
    background-color: #000;
    width: 150px;
    height: 32px; 
    font-size: 17px;
    font-family: Kurale;
    padding: 5px;
    text-transform: capitalize;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    text-align: center;
}
</style>
<script>            
    $(document).ready(function(){
        
          $('#product_category').change(function () {
                var product_category = $('#product_category').val();
                var type_name = $('#product_category option:selected').text();
                $("#product_category_name").val(type_name);

                $.ajax({
                    url: "<?php echo base_url() ?>Catalogue/ajax_get_category_by_product_category",
                    method: "POST",
                    data: {product_category: product_category},
                    success: function (data)
                    {
                        $("#category").html(data);
                        $("#category").trigger("chosen:updated");

                    }
                });
            });

            $('#category,#idbrand,#product_category').change(function () {

                var product_category = $('#product_category').val();
                var category = 0;
                if ($('#category').val()) {
                    category = $('#category').val();
                }
                var brand = 0;
                if ($('#idbrand').val()) {
                    brand = $('#idbrand').val();
                }
                if (product_category) {
                    $.ajax({
                        url: "<?php echo base_url() ?>Catalogue/ajax_get_model_byPCB/1",
                        method: "POST",
                        data: {category: category, brand: brand, product_category: product_category,view:'table'},
                        success: function (data)
                        {
                            $('#model_data').html(data);
                        }
                    });
                } else {
                    alert("Please select product category first!!");
                }
            });
    
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        
        <div class="col-md-3">
            <select class="chosen-select form-control" name="product_category" id="product_category" required="">
                            <option value="">Select Product Category</option>
                            <?php foreach ($product_category as $type) { ?>
                                <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
                            <?php } ?>
                        </select>
        </div>
        <div class="col-md-3">
            <select class="chosen-select  form-control" id="category" >
                <option id="">Select Category</option>
               
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        
        <div class="clearfix"></div>
        <table id="model_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px;">
            <thead>
                <th>Sr</th>
                <th>Product Category</th>
                <th>Category</th>
                <th>Brand</th>                
                <th>Model </th>
                <th>Status</th>
                <th>View</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($model_data as $model){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $model->product_category_name; ?></td>
                    <td><?php echo $model->category_name; ?></td>
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->full_name; ?></td>
                    
                    <td><?php if($model->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="thumbnail btn-link waves-effect" href="<?php echo base_url('Catalogue/edit_model/'.$model->idmodel) ?>"  style="margin: 0" >
                            <span class="mdi mdi-pen text-primary fa-lg"></span>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>