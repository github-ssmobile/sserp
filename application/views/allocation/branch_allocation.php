<?php include __DIR__ . '../../header.php'; ?>
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
  top: 20px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}

</style>
<script>

    $(document).ready(function () {
        $(document).on('change', '#warehouse', function () {
            if ($('#warehouse').val()) {                
                var warehouse = +$('#warehouse').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/ajax_branch_by_warehouse",
                    method: "POST",
                    data: {warehouse: warehouse},
                    success: function (data)
                    {
                        $(".branch_class").html(data);
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        });
        $(document).on('change', '#brand', function () {
            if ($('#brand').val()) {
                var product_category = 0;//+$('#product_category').val();
                var brand = +$('#brand').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/ajax_variants_by_brand",
                    method: "POST",
                    data: {brand: brand, product_category: product_category},
                    success: function (data)
                    {
                        $(".variant").html(data);
                        $('.allocationform').show();
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        });
        
        $(document).on("click", ".delete_row", function (event) {
        
                if (confirm('Do you want to Delete this product!!')) {
                    var variant = $(this).attr("variant"); 
                    var idgodown = $(this).attr("godown"); 
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/remove_variant",
                    method: "POST",                
                    data: {variant: variant,idgodown:idgodown},                
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                            $(element).parent().parent().fadeOut();   
                            $(element).parent().parent().remove();
                        }else if(data.data === "fail"){
                            alert("Fail to remove!! ");
                        }

                    }
                });
            }
        });
        
        $(document).on("click", ".allocationform", function (event) {
            event.preventDefault();
            if (confirm('Do you want to Submit the allocation!!')) {
            var serialized = $('.allocation_form').serialize();
            $.ajax({
                url: "<?php echo base_url() ?>Stock_allocation/save_branch_allocation",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if (data.data === 'success') {
                        alert("Allocation submitted successfully!!");
                        location.reload();
                    } else if (data.data === "fail") {
                        alert("Fail to save allocation!! ")
                    } else {
                        alert("Select at least one model !! ")
                    }
                }
            });
            }
        });
        $(document).on('change', '#model', function () {
            var variant_id = +$('#model').val();
            var days = +$('#days').val();
            var brand = +$('#brand').val();
            var idgodown = +$('#idgodown').val();
            var godown_name = $('#idgodown option:selected').text();
            var branch = +$('#branch').val();
            var warehouse = +$('#warehouse').val();
            if (!idgodown) {
                alert("Please select warehouse type!");
                return false;
            }
            if (!branch) {
                alert("Please select branch!");
                return false;
            }
                    $.ajax({
                        url: "<?php echo base_url() ?>Stock_allocation/ajax_variants_by_id",
                        method: "POST",
                        data: {variant_id: variant_id, days: days, brand: brand, idgodown: idgodown,idbranch:branch,godown_name:godown_name,warehouse:warehouse},
                        success: function (data)
                        {
                            var idexist=data.replace(/\s/g, '');
                            if(idexist==="<exist>"){                                 
                             alert("Model already added!");   
                            }else{
                             $("#variant_data").append(data);
                             $(".search_label").hide();
                            }
                        }
                    });
                });
            $(document).on('change', '#branch', function () {            
                    var branch = $('#branch option:selected').text();
                    var bid=+$(this).val();
                    var b=+$("#idbranch").val();
                    if(b>0){                        
                        alert("You cant chnage branch!!");                       
                        return false;
                    }
                    if (confirm('Do you want to allocate for the branch'+branch+' !!')) {
                    $.ajax({
                        url: "<?php echo base_url() ?>Stock_allocation/ajax_get_branch_allocation_header",
                        method: "POST",
                        data: {branch:branch,idbranch:bid},
                        success: function (data)
                        {
                            $("#variant_data").html("");
                            $("#variant_data").append(data);
                            $("#idbranch").val(bid);
                            $(".search_label").hide();    
                            $('.allocationform').show();
                        }                        
                    });     
                    }
                });                
            });
</script>
<div class="col-md-11">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-home-variant fa-lg"></span> Branch Allocation</h3>
    </center>
</div><div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px"><br>
    <div class="col-md-1 col-xs-3 col-sm-2">Days Sale</div>
    <div class="col-md-1 col-xs-3 col-sm-2">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>    
    <div class="col-md-2  col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="warehouse" id="warehouse">
            <?php foreach ($warehouse as $branch) {
                if($idwarehouse==$branch->id_branch){ ?>
            <option selected="" value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php }else{ ?>                
                    <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php }} ?>
        </select>        
    </div>
    <div class="col-md-2 col-xs-6 col-sm-4 branch_class">
        <select class="chosen-select form-control input-sm" name="branch" id="branch">
            <option value="0">Select Branch</option>
            <?php foreach ($branch_data as $branch) { ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
        <div class="chosen-container chosen-container-single branch_lable" style="display:none">
            
        </div>
    </div>    
    <div class="col-md-2 col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="idgodown"  id="idgodown">            
            <?php foreach ($active_godown as $godown) { ?>
                <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="col-md-2  col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="brand" id="brand">
            <option value="0">Select Brand</option>
            <?php foreach ($brand_data as $brand) { ?>
                <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>              
    <div class="col-md-4"></div>
    <div class="clearfix"></div><br>
    <div class="col-md-4  col-xs-12 col-sm-6 variant">        
        <select class="chosen-select form-control" name="model" id="model" required="">
            <option value="">Select Model</option>                
        </select>        
    </div>    
    <div class="clearfix"></div>   
</div>    
   

<form class="allocation_form">
    <input type="hidden" name="allocation_type" value="0" />
    <input type="hidden" name="idbranch" id="idbranch" value="0" />
    <div class="thumbnail" id="search_block" style="margin-bottom: 0; padding: 0;">                
        <center>
            <table id="variant_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
            </table>
        </center>        
    </div>
    <br><br>    
    <div class="col-md-2 pull-right">
        <button type="button" class="allocationform btn btn-primary" style=" display: none; margin: 0; right: 30px; bottom: 20px">Submit</button>
    </div>
</form>


<?php include __DIR__ . '../../footer.php'; ?>