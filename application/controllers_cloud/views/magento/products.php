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
                var cgst = $(parentDiv).find('input[name="cgst"]').val()
                fd.append("cgst", cgst);
                var sgst = $(parentDiv).find('input[name="sgst"]').val()
                fd.append("sgst", sgst);
                var igst = $(parentDiv).find('input[name="igst"]').val()
                fd.append("igst", igst);
                var emi = $(parentDiv).find('input[name="emi"]').val()
                fd.append("emi", emi);
                fd.append("is_ajax", "yes");

                jQuery.ajax({
                    url: "<?php echo base_url('Pricing/update_price') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (data, textStatus, jqXHR) {

                        if (data == 'yes') {
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("Price updated successfully!");
                            $(parentDiv).find('.igst').html(igst);
                            $(parentDiv).find('.sgst').html(sgst);
                            $(parentDiv).find('.cgst').html(cgst);
                            $(parentDiv).find('.online_price').html(online_price);
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
                    url: "<?php echo base_url() ?>Magento/ajax_get_category_by_product_category",
                    method: "POST",
                    data: {product_category: product_category},
                    success: function (data)
                    {
                        $("#category").html(data);
                        $("#category").trigger("chosen:updated");

                    }
                });
            });

            $('#category').change(function () {

                var product_category = $('#product_category').val();
                  var category = $('#category').val();
               
                if (product_category && category) {
                    $.ajax({
                        url: "<?php echo base_url() ?>Magento/ajax_get_product_by_category",
                        method: "POST",
                        data: {category: category},
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
                                <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                            <?php } ?>
                        </select>
        </div>
        <div class="col-md-3">
            <select class="chosen-select  form-control" id="category" >
                <option id="">Select Category</option>
               
            </select>
        </div>
        
        <div class="col-md-3">
           
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
       
        </table><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>