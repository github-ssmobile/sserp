<?php include __DIR__.'../../header.php'; ?>

<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-arrow-right-bold fa-lg"></span>NLC Price Update</h3></center></div>
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
    var mrp = $(parentDiv).find('input[name="mrp"]').val()
    fd.append("mrp", mrp);
    var mop = $(parentDiv).find('input[name="mop"]').val()
    fd.append("mop", mop);       
    var nlc_price = $(parentDiv).find('input[name="nlc_price"]').val()
    fd.append("nlc_price", nlc_price);
    var dp_price = $(parentDiv).find('input[name="dp_price"]').val()
    fd.append("dp_price", dp_price);
    var scheme_price = $(parentDiv).find('input[name="scheme_price"]').val()
    fd.append("scheme_price", scheme_price);
    var scheme_price = $(parentDiv).find('input[name="scheme_price"]').val()
    fd.append("sale_kitty", scheme_price);
    var sale_kitty = $(parentDiv).find('input[name="sale_kitty"]').val()
    fd.append("sale_kitty", sale_kitty);
    
    fd.append("is_ajax", "yes");
    if(!confirm("Do You Want To Update Price to All Color Variants ")){
        jQuery.ajax({
                url: "<?php echo base_url('Pricing/update_nlc_price') ?>",
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                method: 'POST',
                success: function (data, textStatus, jqXHR) {
//                    alert(data.result);
                    if (data.result == 'yes') {
                        $(parentDiv).css("background", "#e6ffc0");
                        alert("Price updated successfully!");
                        $(parentDiv).find('.mrp').html(mrp);
                        $(parentDiv).find('.mop').html(mop);
                        $(parentDiv).find('.nlc_price').html(nlc_price);
                        $(parentDiv).find('.dp_price').html(dp_price);
                        $(parentDiv).find('.scheme_price').html(scheme_price);
                        $(parentDiv).find('.sale_kitty').html(sale_kitty);
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
                url: "<?php echo base_url('Pricing/update_nlc_price_toall') ?>",
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                method: 'POST',
                success: function (data, textStatus, jqXHR) {
                    if (data.result == 'yes') {
                        $(parentDiv).css("background", "#e6ffc0");
                        alert("Price updated successfully!");
                        $(parentDiv).find('.mrp').html(mrp);
                        $(parentDiv).find('.mop').html(mop);
                        $(parentDiv).find('.nlc_price').html(nlc_price);
                        $(parentDiv).find('.dp_price').html(dp_price);
                         $(parentDiv).find('.scheme_price').html(scheme_price);
                        $(parentDiv).find('.sale_kitty').html(sale_kitty);
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
    
    $('.salekitty').focusout(function (){
        var parentdiv  = $(this).closest('tr')
        var kitty = +$(this).val();
        var dp = +$(parentdiv).find('.dpprice').val();
        var sch = +$(parentdiv).find('.schemeprice').val();
        var nlc = 0;
        if(dp > 0){
           var nlc1 = dp - sch;
           nlc = nlc1 + kitty;
           $(parentdiv).find('.nlcprice').val(nlc);
        }
        else{
            alert("DP Price is 0 ");
            return false;
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
                url: "<?php echo base_url() ?>Pricing/ajax_get_model_bycategory_nlc/1",
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
                <img src="<?php echo base_url()?>assets/images/opening_stock.jpg" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <?php echo form_open_multipart('Stock/upload_opening_stock_excel', array('id' => 'pay')) ?>     
                <center><h4>NLC Price Upload</h4></center>
                    <div class="clearfix"></div><br>
                    <div class="col-md-3"><b>Upload File </b></div>
                     <div class="col-md-9">
                         <input type="file" name="uploadfile" id="uploadfile" required="">
                    </div>
                    <div class="clearfix"></div><br>
                <div class="clearfix"></div><hr>
                <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Pricing/save_price_bulk_price">Submit</button>
                <div class="clearfix"></div><br>
                <?php echo form_close(); ?>
                <div class="clearfix"></div>
            </div>
        </div>
</div>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('model_price_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div>
        <table id="model_price_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
        <thead>
            <th>Sr</th>
            <th style="display: none">Idcategory</th>            
            <th style="display: none">Idproductcategory</th>            
            <th style="display: none">Idmoel</th>     
            <th style="display: none">Idbrand</th>     
            <th style="display: none">Idvariant</th>  
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th>            
            <th>MRP</th>
            <th>MOP/Customer</th>
            <th>Dp Price</th>
            <th>Scheme Amount</th>
            <th>Sale Kitty</th>
            <th>NLC Price</th>
            <th>Landing</th>
            <th>Action</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                 <td style="display: none"><?php echo $model->idcategory; ?></td>
                <td style="display: none"><?php echo $model->idproductcategory; ?></td>
                <td style="display: none"><?php echo $model->idmodel; ?></td>
                <td style="display: none"><?php echo $model->idbrand ; ?></td>
                <td style="display: none"><?php echo $model->id_variant; ?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                
                
                <form class="model_price_submit_form">
                    <td><div class="myDiv1" style="display: none"><input type="text" name="mrp" class="mrp form-control input-sm" value="<?php echo $model->mrp; ?>" /></div><div class="mrp myDiv2"><?php echo $model->mrp; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="mop" class="form-control input-sm" value="<?php echo $model->mop; ?>" /></div><div class="mop myDiv2"><?php echo $model->mop; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="dp_price" class="form-control input-sm dpprice" value="<?php echo $model->dp_price; ?>" /></div><div class="dp_price myDiv2"><?php echo $model->dp_price; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="scheme_price" class="form-control input-sm schemeprice"  value="<?php echo $model->scheme_amount ?>"></div><div class="scheme_price myDiv2"><?php echo $model->scheme_amount ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="sale_kitty" class="form-control input-sm salekitty"  value="<?php echo $model->sale_kitty ?>"></div><div class="sale_kitty myDiv2"><?php echo $model->sale_kitty ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" /><input type="text" name="nlc_price" class="form-control input-sm nlcprice" readonly="" value="<?php echo $model->nlc_price; ?>" /></div><div class="nlc_price myDiv2"><?php echo $model->nlc_price; ?></div></td>
                    <td><?php echo $model->landing; ?></td>
                    <td><div class="myDiv1" style="display: none">
                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div><div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div></td>
                </form>
            </tr>
            
            <?php $i++; } ?>
        </tbody>
        </table><div class="clearfix"></div>
        
        <div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>