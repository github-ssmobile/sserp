<?php include __DIR__.'../../header.php';  ?>
<style>
.floatingButtonWrap {
    display: block;
    position: fixed;
    bottom: 25px;
    right: 30px;
    z-index: 999;
}
.floatingButtonInner {
    position: relative;
}
.floatingButton {
    display: block;
    width: 55px;
    height: 55px;
    text-align: center;
    border: 1px solid #003399;
    background-color: #fff;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.3);
    /*background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);*/
    /*background-image: linear-gradient(to right top, #e9d82e, #f2bc00, #f99f00, #fd7e00, #ff5800);*/
    /*background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);*/
    color: #003399;
    line-height: 65px;
    position: absolute;
    border-radius: 50% 50%;
    bottom: 0px;
    right: 0px;
    /*border: 5px solid #176391;*/
    /* opacity: 0.3; */
    opacity: 1;
    transition: all 0.4s;
}
.floatingButton .fas {
    font-size: 25px !important;
}
.floatingButton.open,
.floatingButton:hover,
.floatingButton:focus,
.floatingButton:active {
    /*opacity: 1;*/
    color: #003399;
    box-shadow: 0px 15px 20px rgba(0, 51, 153, 0.4);
    /*transform: translateY(-7px);*/
}
.floatingButton .fas {
    transform: rotate(0deg);
    transition: all 0.4s;
}
.floatingButton.open .fas {
    transform: rotate(270deg);
}
.floatingMenu {
    position: absolute;
    bottom: 30px;
    right: 0px;
    /* width: 200px; */
    display: none;
}
/*.floatingMenu li {
    width: 100%;
    float: right;
    list-style: none;
    text-align: right;
    margin-bottom: 5px;
}*/
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
</style>
<script>
var products = [];
$(document).ready(function(){
    $("#sidebar").addClass("active");
    $(document).on("click", ".service_stock", function(event) {     
        var product_category = +$('#product_category').val();
        var brand = +$('#brand').val();
        var branch = +$('#idbranch').val();
        var status = +$('#status').val();
        var warranty = +$('#warranty').val();
        if(!branch){
            alert('Select branch');
            return false;
        }
        $.ajax({
            url:"<?php echo base_url() ?>Service/ajax_get_service_send_to_branch_list",
            method:"POST",
            data:{ status:status,brand: brand, idbranch: branch, product_category: product_category,warranty:warranty},
            success:function(data)
            {
                $(".export").show();
                $("#stock_data").html(data);
                products = [];
                $("#service_send_to_branch_form").html('');
            }
        });           
    });
    
//    $('#save_service_send_to_branch').on('click',function () {
//        if($('#receiver_branch') == ''){alert('Select receiver branch');return false;}
//        if(!confirm('Do you want to submit')){
//            return false;
//        }
//    });
    
    $(document).on("click", ".sel_product", function(event) {
        var id = $(this).val();
        if($(this).prop("checked") === true){
            if (products.includes(id) === false){
                products.push(id);
            }else{
                alert('duplicate product selected');
                return false;
            }
        }else if($(this).prop("checked") === false){
            products = jQuery.grep(products, function(value) { return value !== id; });
            if(products.length == 0){
                $("#service_send_to_branch_form").html('');
            }
        }
        $('#idservices').val(products);
        $('#selected_branch').val($(this).closest('center').find('.row_branch').val());
        $('#selected_branch_name').val($(this).closest('center').find('.row_branch_name').val());
    });
    $(document).on("click", "#send_to_branch_form_open", function(event) {
        var selected_branch = $('#selected_branch').val();
        var selected_branch_name = $('#selected_branch_name').val();
        if(products.length == 0){
            swal('Select Product', 'Select Service Product for - Send to Branch', 'warning');
            return false;
        }else{
             $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_open_service_send_to_branch_form",
                method:"POST",
                data:{selected_branch:selected_branch,selected_branch_name:selected_branch_name},
                success:function(data)
                {
                    $("#service_send_to_branch_form").html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });         
        }
    });
});
</script>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Service - Send to Branch List</h3></center></div><div class="clearfix"></div><hr>
<input type="hidden" value="16" id="status" name="status">
<?php  if(count($branch_data)==1){ ?>
      <input type="hidden" value="<?php echo $branch_data[0]->id_branch; ?>" name="idbranch" id="idbranch">        
<?php }else{ ?>
<div class="col-md-2 col-sm-3" style="padding: 5px">
    <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch" required="">
        <?php // if($this->session->userdata('idrole') != 36){ ?>
        <option value="0">Select Branch</option>   
        <?php // } ?>
        <?php foreach($branch_data as $branch){ ?>                
            <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
        <?php } ?>
    </select>
</div>
<?php } ?>
<div class="col-md-2 col-sm-3" style="padding: 5px">
    <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
        <option value="">Product Category</option>
        <option value="0">All</option>
        <?php foreach ($product_category as $type){ ?>
        <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
        <?php } ?>
    </select>
</div>    
<div class="col-md-2 col-sm-3" style="padding: 5px">
    <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
        <option value="">Select Brand</option>
        <option value="0">All</option>
        <?php foreach ($brand_data as $brand){ ?>
        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-md-2 col-sm-3" style="padding: 5px">
    <select class="chosen-select form-control input-sm" name="warranty" id="warranty" required="">
        <option value="">Select Status</option>
        <option value="">All</option>
        <option value="0">Pending</option>
        <option value="1">Repaired</option>
        <option value="2">Rejected</option>
        <option value="3">DOA Letter</option>
        <option value="4">DOA Handset</option>
    </select>
</div>
<div class="col-md-1" style="text-align: center;">
    <button type="button"  class="service_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Filter</button>
</div>
<div class="clearfix"></div><br>
<form>
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
    <div class="col-md-5">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="col-md-2">
        <a class="btn btn-primary btn-sm" id="send_to_branch_form_open" style="margin-top: 6px;line-height: unset;"><span class="mdi mdi-share fa-lg"></span> Send to Branch</a>
    </div><div class="clearfix"></div><br>
    <div id="service_send_to_branch_form"></div>
    <input type="hidden" name="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <input type="hidden" name="idwarehouse" value="<?php echo $this->session->userdata('idbranch') ?>"/>
    <input type="hidden" id="selected_branch"  />
    <input type="hidden" id="selected_branch_name"  />
    <input type="hidden" id="idservices" />
    <div class="thumbnail" style="overflow: auto;padding: 0;height: 450px;">
        <table id="stock_data" class="table table-condensed table-bordered table-hover" style="font-size: 13px"></table>
    </div>
</form>
<?php include __DIR__.'../../footer.php'; ?>