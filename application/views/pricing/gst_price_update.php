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
            var salesman = $(parentDiv).find('input[name="salesman"]').val();
            fd.append("salesman", salesman);
            var mrp = $(parentDiv).find('input[name="mrp"]').val()
            fd.append("mrp", mrp);
            var mop = $(parentDiv).find('input[name="mop"]').val()
            fd.append("mop", mop);       
            var landing = $(parentDiv).find('input[name="landing"]').val()
            fd.append("landing", landing);
            var is_mop=0;
            if ($(parentDiv).find('input[name="is_mop"]').is(":checked")) {
                is_mop = 1;
            }
            fd.append("is_mop", is_mop);
            var is_online = 0;
            if ($(parentDiv).find('input[name="is_online"]').is(":checked")) {
                is_online = 1;
            }
            fd.append("is_online", is_online);
            var online_price = $(parentDiv).find('input[name="online_price"]').val()
            fd.append("online_price", online_price);
            var wholesale_price = $(parentDiv).find('input[name="wholesale_price"]').val()
            fd.append("wholesale_price", wholesale_price);
            var cgst = $(parentDiv).find('input[name="cgst"]').val()
            fd.append("cgst", cgst);
            var sgst = $(parentDiv).find('input[name="sgst"]').val()
            fd.append("sgst", sgst);
            var igst = $(parentDiv).find('input[name="igst"]').val()
            fd.append("igst", igst);
            var emi = $(parentDiv).find('input[name="emi"]').val()
            fd.append("emi", emi);
            fd.append("is_ajax", "yes");
            if(!confirm("Do You Want To Update Price to All Color Variants ")){
                jQuery.ajax({
                    url: "<?php echo base_url('Pricing/update_gst_price') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    method: 'POST',
                    success: function (data, textStatus, jqXHR) {
                        if (data.result == 'yes') {
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("Price updated successfully!");
                            $(parentDiv).find('.igst').html(igst);
                            $(parentDiv).find('.sgst').html(sgst);
                            $(parentDiv).find('.cgst').html(cgst);
                            $(parentDiv).find('.online_price').html(online_price);
                            $(parentDiv).find('.wholesale_price').html(wholesale_price);
                            $(parentDiv).find('.landing').html(landing);
                            $(parentDiv).find('.mrp').html(mrp);
                            $(parentDiv).find('.mop').html(mop);
                            $(parentDiv).find('.emi').html(emi);
                            $(parentDiv).find('.salesman').html(salesman);
                            $(parentDiv).find('.is_mop').html(((is_mop == 1) ? 'Yes' : 'No'));
                            $(parentDiv).find('.is_online').html(((is_online == 1) ? 'Yes' : 'No'));
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 1500)
                        } else {
                            $(parentDiv).css("background", "#fdb4b4");
                            alert("Fail to update Price!");
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 1500)
                        }
                        $(parentDiv).find(".myDiv2").css("display", "block");
                        $(parentDiv).find(".myDiv1").css("display", "none");
                    }
                });
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Pricing/update_gst_price_to_allvariants') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    method: 'POST',
                    success: function (data, textStatus, jqXHR) {
                        if (data.result == 'yes') {
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("Price updated successfully!");
                            $(parentDiv).find('.igst').html(igst);
                            $(parentDiv).find('.sgst').html(sgst);
                            $(parentDiv).find('.cgst').html(cgst);
                            $(parentDiv).find('.online_price').html(online_price);
                            $(parentDiv).find('.wholesale_price').html(wholesale_price);
                            $(parentDiv).find('.landing').html(landing);
                            $(parentDiv).find('.mrp').html(mrp);
                            $(parentDiv).find('.mop').html(mop);
                            $(parentDiv).find('.emi').html(emi);
                            $(parentDiv).find('.salesman').html(salesman);
                            $(parentDiv).find('.is_mop').html(((is_mop == 1) ? 'Yes' : 'No'));
                            $(parentDiv).find('.is_online').html(((is_online == 1) ? 'Yes' : 'No'));
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 1500)
                        } else {
                            $(parentDiv).css("background", "#fdb4b4");
                            alert("Fail to update Price!");
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 1500)
                        }
                        $(parentDiv).find(".myDiv2").css("display", "block");
                        $(parentDiv).find(".myDiv1").css("display", "none");
                    }
                });
            }
        });

        $('#product_category').change(function () {
            var product_category = $('#product_category').val();
            var type_name = $('#product_category option:selected').text();
            $("#product_category_name").val(type_name);
        });
        $('#category,#idbrand,#product_category').change(function () {

            var product_category = $('#product_category').val();
            var category = 0;
            var brand = 0;
            if ($('#idbrand').val()) {
                brand = $('#idbrand').val();
            }
            if (product_category) {
                $.ajax({
                    url: "<?php echo base_url() ?>Pricing/ajax_get_model_bycategory_gst/1",
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_price_export');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div>
        <div>
            <table id="model_price_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover"></table>
        </div>
        <div class="clearfix"></div><br>
       
        <div style="display: none">
            <table id="model_price_export" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Product Type</th>            
                    <th>Brand</th>
                    <th>Model</th>            
                    <th>MRP</th>
                    <th>MOP/Customer</th>
                    <th>Salesman</th>
                    <th>Landing</th>
                    <th>isMOP</th>
                    <th>isOnline</th>
                    <th>Online Price</th>
                    <th>Wholesale price</th>
                    <th>Best EMI</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($model_data as $model){ ?>
                    <tr>
                        <td><?php echo $i;?></td>
                        <td><?php echo $model->product_category_name; ?></td>                
                        <td><?php echo $model->brand_name; ?></td>
                        <td><?php echo $model->full_name; ?></td>
                        <form class="model_price_submit_form">
                            <td><?php echo $model->mrp; ?></td>
                            <td><?php echo $model->mop; ?></td>
                            <td><?php echo $model->salesman_price; ?></td>
                            <td><?php echo $model->landing; ?></td>                    
                            <td><?php echo (($model->is_mop==1)?'Yes':'No'); ?></td>                    
                            <td><?php echo (($model->is_online==1)?'Yes':'No'); ?></td>
                            <td><?php echo $model->online_price; ?></td> 
                            <td><?php echo $model->corporate_sale_price; ?></td> 
                            <td><?php echo $model->best_emi_price; ?></td> 
                            <td><?php echo $model->cgst; ?></td>
                            <td><?php echo $model->sgst; ?></td>
                            <td><?php echo $model->igst; ?></td>
                        </form>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>