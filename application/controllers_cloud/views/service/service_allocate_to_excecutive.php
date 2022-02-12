<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".service_stock", function(event) {         
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#idbranch').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_service_allocate_to_excecutive",
                method:"POST",
                data:{brand: brand, idbranch: branch, product_category: product_category},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
        $(document).on("click", ".submit_allocation", function(event) {
            var ce = $(this);
            var idservice=ce.val();
            var parentDiv=$(ce).closest('td');
            var parentDivtr=$(ce).closest('td').parent('tr');
            var excecutive=$(parentDiv).find('.excecutive').val();
            var imei_no=$(parentDiv).find('.imei_no').val();
            var idwarehouse=$(parentDiv).find('.idwarehouse').val();
            var idvariant=$(parentDiv).find('.idvariant').val();
            if(excecutive == ''){
                swal('😠 All fields are mandatory', 'Select Service Excecutive', 'warning');
                return false;
            }else{
                jQuery.ajax({
                    url: "<?php echo base_url('Service/service_allocation_to_excecutive') ?>",
                    method:"POST",
                    dataType:"Json",
                    data:{idservice:idservice,excecutive:excecutive,imei_no:imei_no,idwarehouse:idwarehouse,idvariant:idvariant},
                    success:function(data){
                        if(data.result == 'Success'){
                            $(parentDivtr).remove();
                            swal('🙂 Allocation done', imei_no+' Service product allocated', 'success');
                        }else{
                            swal('😠 Failed to allocate service', 'Entry not updated! Please try Again!', 'warning');
                        }
                    }
                });
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
</style>
<div class="col-md-10"><center><h3><span class="mdi mdi-exit-to-app fa-lg"></span> Allocate to Executive</h3></center></div><div class="clearfix"></div><hr>
    <?php  if(count($branch_data)==1){ ?>
          <input type="hidden" value="<?php echo $branch_data[0]->id_branch; ?>" name="idbranch" id="idbranch">        
    <?php }else{ ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch" required="">
            <option value="">Select Branch</option>   
            <option value="0">All</option>         
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
    <div class="col-md-2" style="text-align: center;">
        <button type="button"  class="service_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Filter</button>
    </div>
    <div class="clearfix"></div><br>
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
    <div class="col-md-1 col-sm-2">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto;padding: 0">
        <div style="height: 650px;">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
            </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>