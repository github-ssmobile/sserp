<?php include __DIR__.'../../header.php'; ?>


<div class="col-md-9">
        <center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Model</h3></center>
    </div>
<div class="col-md-2 pull-right">
    <a  href="<?php echo base_url('Catalogue/add_model') ?>" class="btn btn-outline-info waves-effect" style="padding: 5px 13px !important;"><i class="fa fa-plus fa-2x" style="margin-right: 10px;"></i>Create Model</a>        
    </div><div class="clearfix"></div>

    <style>
        .item-div:hover .link {color:#2874f0;}
        .link { color: #212121; }   
        .price-ol {;display: inline-block;font-size: 16px;font-weight: 500;color: #212121;}
        .price-mrp {margin-left: 10px;font-size: 14px;color: #878787;text-decoration: line-through;}
        .price-dis {margin-left: 10px;font-size: 13px;color: #388e3c;font-weight: 500;}
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
                        url: "<?php echo base_url() ?>Catalogue/ajax_get_model_byPCB/0",
                        method: "POST",
                        data: {category: category, brand: brand, product_category: product_category},
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
        
        
        <div id="model_data" class="" style="font-size: 13px;margin-top: 20px">
           
            <?php $i=1; foreach ($model_data as $model){ ?>
            <div class="col-md-2 col-sm-4 btn waves-effect" title="<?php echo $model->full_name; ?>" style="padding: 2px !important;height: 295px !important;">
                    <div class="item-div">
                        <a target="_blank" href="<?php echo base_url('Catalogue/edit_model/'.$model->id_model) ?>">
                        <div class="image-url">
                            <div class="thumbnail" id="iimage-preview" style="max-height: 200px;min-height: 180px;border: 0px solid #fff  !important;margin-bottom: 5px !important;">                                                
                                <?php $path=''; if($model->variant_image_path==null){
                                    $path=base_url() . $model->model_image_path;
                                }else{ 
                                    $path=base_url() . $model->variant_image_path;
                               } ?>
                                <img class="img-view" src="<?php echo $path ?>"  id="userfileimage" />                                                
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="" style="margin: 10px !important; font-size: 14px;line-height: 1.4;white-space: normal;text-transform: none;">
                            <div style="height: 38px;">
                                <a class="link" title="<?php echo $model->full_name; ?>"  ><?php echo $model->full_name; ?></a>
                            </div>
                            <div class="clearfix"></div>
                            <div style="margin-top: 10px;">
                                <span class="price-ol">₹<?php echo $model->online_price; ?></span>
                                <span class="price-mrp">₹<?php echo $model->mrp; ?></span>                                
                                <span class="price-dis">
                                    <?php if ($model->mrp > 0) {
                                        echo round(((($model->mrp - $model->online_price) / $model->mrp) * 100));
                                    } else{ 
                                        echo '0';} ?>% off
                                    </span>        
                            </div>   
                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>     
                         </a>
                    </div>                
                </div>
             <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>
