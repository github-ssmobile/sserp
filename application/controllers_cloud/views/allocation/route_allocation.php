<?php include __DIR__.'../../header.php'; ?>

<script>
    
$(document).ready(function(){    
    $(document).on('change', '#brand', function() {
        var brand = $('#brand option:selected').text();
        if (confirm('Do you want to Allocate for '+brand+'!!')) {
            if ($('#brand').val()) {
                var product_category = +$('#product_category').val();
                if (!$('#idroute').val()) {
                    alert("Select Route First!");
                    return false;
                }
                var idroute = +$('#idroute').val();
                var idgodown = +$('#idgodown').val();
                var brand = +$('#brand').val();
                var days = +$('#days').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/ajax_route_allocation_data",
                    method: "POST",
                    data: {brand: brand, product_category: product_category, idgodown: idgodown, idroute: idroute, days: days},
                    success: function (data)
                    {
                        $("#variant_data").html(data);
                        $('.allocationform').show();
                        $('.search_label').hide();
                    }
                });
            }
        }
    });
    $(document).on("click", ".allocationform", function(event) {   
        event.preventDefault();
            if (confirm('Do you want to Submit the allocation!!')) {
                var serialized = $('.allocation_form').serialize();
                $.ajax({
                        url: "<?php echo base_url() ?>Stock_allocation/save_route_allocation",
                        method: "POST",
                        data: serialized,
                        dataType:'json',
                        success: function (data)
                        {                            
                            if(data.data === 'success'){
                                alert("Allocation submitted successfully!!");
                                location.reload();	
                            }else if(data.data === "fail"){
                                alert("Fail to save allocation!! ")
                            }else{
                                alert("Select at least one model !! ")
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
.fixedelement1 {
  background-color: #fbf7c0;
  position: sticky;
  top: 20px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
.fix{
    background-color: #fbf7c0;
  position: sticky;
  top: 51px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
.fixleft{
  position: sticky;
  left:0px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
.fixleft1{
  position: sticky;
  left:80px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
.fixleft2{
  position: sticky;
  left:140px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
.fixleft3{
  position: sticky;
  left:210px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
</style>
    <div class="col-md-11">
        <center>
            <h3 style="margin-top: 0"><span class="mdi mdi-road-variant fa-lg"></span> Route Allocation</h3>
        </center>
    </div><div class="clearfix"></div>
    <div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">
    <div class="col-md-1" style="padding: 6px 0px 0px 21px;">Days Sale</div>
    <div class="col-md-1" style="padding: 0px 16px 0px 0px;">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>
    <div class="col-md-2">
        <select class=" chosen-select form-control input-sm" name="idroute" id="idroute">
            <option value="">Select Route</option>
            <?php foreach ($route_data as $route){ ?>
            <option value="<?php echo $route->id_route ?>"><?php echo $route->route_name ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class=" chosen-select form-control input-sm" name="idgodown" id="idgodown">            
            <?php foreach ($active_godown as $godown){ ?>
            <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category">
            <option value="0">Product Category</option>
            <?php foreach ($product_category as $category){ ?>
            <option value="<?php echo $category->id_product_category; ?>"><?php echo $category->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2">
        <select class="chosen-select form-control input-sm" name="brand" id="brand">
            <option value="0">Select Brand</option>
             <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>           
    <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>    
    <form class="allocation_form">
        <input type="hidden" name="allocation_type" value="2" />
    <div class="thumbnail" id="search_block" style="margin-bottom: 0; padding: 0;">
        <center><div class="search_label"><h3>Select From Above Filter</h3></div></center><br>
        <center><table id="model_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;"></table></center>        
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
    <div class="clearfix"></div><br>
    <!--<div style="overflow-x:auto;height: ">-->
            <table id="variant_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;"></table>
    <!--</div>-->
            
    </div>
    <br><br>    
    <div class="col-md-2 pull-right">
        <button type="button" class="allocationform btn btn-primary" style=" display: none; margin: 0; right: 30px; bottom: 20px">Submit</button>
    </div>
    </form>
    

<?php include __DIR__.'../../footer.php'; ?>