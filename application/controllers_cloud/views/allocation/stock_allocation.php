<?php include __DIR__.'../../header.php'; ?>

<script>
window.onload=function() {
    $("#sidebar").addClass("active");
}
$(document).ready(function(){
    
    $('#type').change(function(){
        var type = +$('#type').val();
        var days = +$('#days').val();
        var idgodown = +$('#idgodown').val();
        if(!idgodown){
            alert("Please select warehouse type!");
            return false;
        }
        $.ajax({
            url:"<?php echo base_url() ?>Stock_allocation/ajax_model_stock_allocation",
            method:"POST",
            data:{category : 0, type: type,days:days,idgodown:idgodown},
            success:function(data)
            {
                $("#stock_data").html(data);
            }
        });
        $.ajax({
            url:"<?php echo base_url() ?>Master/ajax_get_category_by_type",
            method:"POST",
            data:{type : type},
            success:function(data)
            {
                $("#category").html(data);
            }
        });
        $('#search_label').hide();
    });

    $(document).on('change', '#brand', function() {  
        var product_category = +$('#product_category').val();
        var brand = +$('#brand').val();                
        $.ajax({
            url:"<?php echo base_url() ?>Stock_allocation/ajax_variants_by_brand",
            method:"POST",
            data:{brand : brand,product_category: product_category},
            success:function(data)
            {
                $("#model_list").html(data);
                $(".chosen-select").chosen({ search_contains: true });
            }
        });
    });
    
    $(document).on('change', '#model', function() {  
        var model = +$('#model').val();    
        var days = +$('#days').val();  
         var idgodown = +$('#idgodown').val();
        $.ajax({
            url:"<?php echo base_url() ?>Stock_allocation/ajax_model_variants_allocation_data",
            method:"POST",
             data:{variant : model,days : days,idgodown:idgodown},
            success:function(data)
            {
//                      $("#model_data").html(data);  
                        $(".search_label").hide();
//                      $("#variant_data").html(data);   
                        $('.allocationform').show();
                        var splitted = data.split("|"); // RESULT
                        $("#variant_data").html(splitted[0]);                      
                        $('#variant_data .top_row').html(splitted[1]); 
            }
        });
    });
    
    $(document).on('click', '.select-variant', function() {   
            
        var model = $(this).parent().find('input[name="idmodel"]').val();    
        var idcategory = $(this).parent().find('input[name="idcategory"]').val(); 
        var idbrand = $(this).parent().find('input[name="idbrand"]').val(); 
        var variant = $(this).parent().find('input[name="idvariant"]').val(); 
        var idskutype = $(this).parent().find('input[name="idskutype"]').val(); 
        var idproductcategory = $(this).parent().find('input[name="idproductcategory"]').val();  
        var days = +$('#days').val();
        var idgodown = +$('#idgodown').val();
        if(idgodown){
            if(confirm("Do you want to allocate stock for this model!")){
                $.ajax({
                    url:"<?php echo base_url() ?>Stock_allocation/ajax_model_variants_allocation_data",
                    method:"POST",
                    data:{model : model,days : days,variant : variant,idgodown : idgodown,idproductcategory:idproductcategory,idbrand:idbrand},
                    success:function(data)
                    {   
//                        $("#variant_data").html(data); 
                        $(".search_label").hide();
//                      $("#variant_data").html(data);   
                        $('.allocationform').show();
                        var splitted = data.split("|"); // RESULT
                        $("#variant_data").html(splitted[0]);                      
                        $('#variant_data .top_row').html(splitted[1]); 
                    }
                });
            }        
        }else{
            alert("Select godown first!!");
        }
    });
    
    
     $(document).on("click", ".allocationform", function(event) {   
            event.preventDefault();
            if (confirm('Do you want to Submit the allocation!!')) {
            var serialized = $('.allocation_form').serialize();
             $.ajax({
                        url: "<?php echo base_url() ?>Stock_allocation/save_stock_allocation",
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
    .fixheader {
        background-color: #e1f0ff;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
    .fixheader1 {
        background-color: #e1f0ff;
        position: sticky;
        top: 68px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
    .fixheader2 {
        background-color: #e1f0ff;
        position: sticky;
        top: 117px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
	.fixleft3{
  position: sticky;
  left:200px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);  
}
    
.fixleft{
  position: sticky;
  left:0px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
.fixleft1{
  position: sticky;
  left:47px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
.fixleft2{
  position: sticky;
  left:133px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  
}
   
</style>
    
    <div class="col-md-11">
        <center>
            <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Model Allocation</h3>
        </center>
    </div><div class="clearfix"></div>
    <div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">
    <div class="col-md-1" style="padding: 6px 0px 0px 21px;">Days Sale</div>
    <div class="col-md-1" style="padding: 0px 16px 0px 0px;">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>
    <div class="col-md-2">
        <select class=" chosen-select form-control input-sm" name="idgodown" id="idgodown">
            <!--<option value="">Warehouse</option>-->
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
    
    <div class="col-md-2 " id="model_list">
    <select class="chosen-select form-control input-sm" name="model" id="model">
        <option value="0">Select Model</option>
    </select>
    </div>    
    <div class="clearfix"></div>
    </div>
    
    <form class="allocation_form">
        <input type="hidden" name="allocation_type" value="1" />
        <div class="thumbnail" id="search_block" style="margin-bottom: 0; padding: 0;">
            <center><div class="search_label"><h3>Select From Above Filter</h3></div></center>
              <div class="col-md-1 col-sm-2 pull-right">
                <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('variant_data');" style="margin-top: 6px;line-height: unset; "><span class="fa fa-file-excel-o"></span> Export</button>
             </div>
            <div class="clearfix"></div><br>
            <center><table id="model_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;"></table></center><hr>        
            <center><table id="variant_data" class="table-condensed table-bordered table-responsive table-hover" style="font-size: 13px;"></table></center>        
        </div>
        <br><br>    
        <div class="col-md-2 pull-right">
            <button type="button" class="allocationform btn btn-primary" style=" display: none; margin: 0; right: 30px; bottom: 20px">Submit</button>
        </div>
    </form>
    

<?php include __DIR__.'../../footer.php'; ?>