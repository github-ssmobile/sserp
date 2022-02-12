<?php include __DIR__.'../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Price Control</h3></center>
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
                var emi = $(parentDiv).find('input[name="emi"]').val()
                fd.append("emi", emi);    
                var is_online = 0;
                if ($(parentDiv).find('input[name="is_online"]').is(":checked")) {
                    is_online = 1;
                }
                fd.append("is_online", is_online);
                var online_price = $(parentDiv).find('input[name="online_price"]').val()
                fd.append("online_price", online_price);
                fd.append("is_ajax", "yes");

                jQuery.ajax({
                    url: "<?php echo base_url('Pricing/update_online_price') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data, textStatus, jqXHR) {

                        if (data == 'yes') {
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("Price updated successfully!");
                            $(parentDiv).find('.emi').html(emi);
                            $(parentDiv).find('.online_price').html(online_price);
                            $(parentDiv).find('.is_online').html(((is_online == 1) ? 'Yes' : 'No'));
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 1500)
                        } else {
                            $(parentDiv).css("background", "#fdb4b4");
                            alert("Fail to update rice!");
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
                        url: "<?php echo base_url() ?>Pricing/ajax_get_model_bycategory/0",
                        method: "POST",
                        data: {category: category, brand: brand, product_category: product_category},
                        success: function (data)
                        {
                            $('#model_price_data').html(data);
                        }
                    });
                } else {
                    alert("Please select product category first!!");
                }
            });


           

        });
        
         $(document).on("keyup", "#cash", function(event) {       
                var parentDiv = $(this).closest('tr');                
                $(parentDiv).find('#cash_per').val($(this).val());
                $(parentDiv).find('#igst').val($(this).val() * 2);
            });
            
            $(document).on("keyup", "#cash_per", function(event) {       
                var parentDiv = $(this).closest('tr');                
                $(parentDiv).find('#sgst').val($(this).val());
                $(parentDiv).find('#igst').val($(this).val() * 2);
            });
            
        $(document).on("keyup", "#cgst", function(event) {  
           var parentDiv = $(this).closest('tr');
           $(parentDiv).find('#sgst').val($(this).val());
           $(parentDiv).find('#igst').val($(this).val() * 2);

       });
       
        $(document).on("click", ".hide-btn", function(event) {             
                var parentDiv = $(this).closest('tr');
                $(parentDiv).find(".myDiv1").css("display", "block");
                $(parentDiv).find(".myDiv2").css("display", "none");
            });
        
</script>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 700px;">
    <div id="purchase" style="padding: 20px 10px;">
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_price_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div>
        <table id="model_price_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        <thead>
            <th>Sr</th>
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th> 
             <th>MRP</th>
            <th>MOP</th>
            <th>isOnline</th>
            <th>Online Price</th>
            <th>Best EMI</th>
            <th>Action</th>
            
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->mrp; ?></td>
                <td><?php echo $model->mop; ?></td>
                
                <form class="model_price_submit_form">                    
                                  
                    <td>
                        <div class="myDiv1" style="display: none">
                            <div class="material-switch">
                                <?php $checked="";                            
                                if($model->is_online==1){
                                    $checked="checked";
                                }?>
                                <!--<input type='hidden' value='0' name='is_online'>-->
                                <input id="active_is_online<?php echo $model->id_variant ?>" name="is_online"  type="checkbox" <?php echo $checked ?> />
                                <label for="active_is_online<?php echo $model->id_variant ?>" class="label-primary"></label>
                            </div>
                        </div>
                        <div class="is_online myDiv2"><?php echo (($model->is_online==1)?'Yes':'No'); ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none">
                            <input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" />                                                     
                            <input type="text" name="online_price" class="form-control input-sm" value="<?php echo $model->online_price; ?>" /></div>
                        <div class="online_price myDiv2"><?php echo $model->online_price; ?></div>
                    </td>                   
                   
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="emi" class="form-control input-sm" value="<?php echo $model->best_emi_price; ?>" /></div>
                        <div class="emi myDiv2"><?php echo $model->best_emi_price; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div>
                        <div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div>
                    </td>
                </form>
                
            </tr>
            
            <?php $i++; } ?>
        </tbody>
        </table><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>