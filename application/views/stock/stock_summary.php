<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".aa_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();            
             var days = +$('#days').val();    
             var idgodown = +$('#idgodown').val();
            var report = +$(this).val();
            
            if(!idgodown && !brand && !product_category){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_stock_summary",
                method:"POST",
                data:{ reporttype:report,brand: brand,  idproductcategory: product_category,idgodown:idgodown,days:days},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
        
       
        
    });
</script>
<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Stock Analysis</h3>
    </center>
</div>
<div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px;">    
<div class="col-md-1" style="margin-top: 12px;">Days Sale</div>
    <div class="col-md-1" style="margin-top: 7px;">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>
    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idgodown" id="idgodown" required="">            
            <?php foreach ($active_godown as $godown){ ?>
            <option value="<?php echo $godown->id_godown; ?>"><?php echo $godown->godown_name; ?></option>
            <?php } ?>
        </select>
    </div>       
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="0">Product Category</option>            
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="0">Select Brand</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
<div class="clearfix"></div>
</div>
<div class="fixedelement hovereffect1" style="padding: 5px;">
    <div class="col-md-5"></div>
    <div class="col-md-2" style="text-align: center;">
        <button type="button" value="branch_summary" class="aa_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Branch Summary</button>
    </div>    
    <div class="col-md-3" style="text-align: center;">
        <button type="button" value="branch_category_summary" class="aa_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Branch Category Summary</button>
    </div>    
    <div class="col-md-2" style="text-align: center;">
        <button type="button" value="zone_summary" class="aa_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Zone Summary</button>
    </div>    
    <div class="clearfix"></div><br>        
</div>

        
    <br>
    <div class="thumbnail" style="overflow: auto">
         <div class="col-md-4">
            
        </div>
        <div class="col-md-6">
            <div id="count_1" class="text-info"></div>
        </div>
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
        </table>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>