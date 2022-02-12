<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $('.btngenerate').click(function (){ 
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();            
            var idgodown = +$('#idgodown').val();
            var idbranch = +$('#idbranch').val();
            var allbranch = $('#allbranch').val();
            var allbrand = $('#allbrand').val();
            var allpcat = $('#allpcat').val();
             
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_ageing_model_data",
                method:"POST",
                data:{ idbranch:idbranch,brand: brand, idproductcategory: product_category,allbranch: allbranch,allbrand: allbrand,allpcat: allpcat },
                success:function(data)
                {
                    $(".export").show();
                    $("#ageing_data").html(data);
                }
            });           
        });
    });
</script>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center>        
            <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Ageing Model Report </h3>                   
    </center>
</div>
<div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idgodown" id="idgodown" required="">            
            <?php foreach ($active_godown as $godown){ ?>
            <option value="<?php echo $godown->id_godown; ?>"><?php echo $godown->godown_name; ?></option>
            <?php } ?>
        </select>
    </div>    
    <?php        
        if(count($branch_data)==1){ ?>
            <input type="hidden" value="<?php echo $branch_data[0]->id_branch ?>" name="idbranch" id="idbranch">               
        <?php }else{ ?>
        <div class="col-md-2 col-sm-3" style="padding: 5px">
            <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch" required="">              
                <option value="0">All Branch</option>
                <?php foreach ($branch_data as $type){ ?>
                <option value="<?php echo $type->id_branch; ?>"><?php echo $type->branch_name; ?></option>
                <?php $allbranch[] = $type->id_branch; } ?>
            </select>
            <input type="hidden" name="allbranch" id="allbranch" value="<?php echo implode($allbranch,',') ?>">
        </div>               
    <?php }    ?>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="product_category" id="product_category" required="">
            <option value="0">All Product Category</option>            
            <?php foreach ($product_category as $type){ ?>
            <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
            <?php $allpcat[] = $type->id_product_category; } ?>
        </select>
        <input type="hidden" name="allpcat" id="allpcat" value="<?php echo implode($allpcat,',') ?>">
    </div>    
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="0">All Brands</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php $allbrand[] = $brand->id_brand; } ?>
        </select>
        <input type="hidden" name="allbrand" id="allbrand" value="<?php echo implode($allbrand,',') ?>">
    </div>
    <div class="col-md-2" style="text-align: center;">
        <button type="button"  class=" btn btn-primary gradient2 btngenerate" style="margin-top: 6px;line-height: unset;">Generate</button>
    </div>
    <div class="clearfix"></div>
    </div>
    <br>
    <div class="thumbnail" style="overflow: auto">
        <div class="col-md-4 col-sm-4 col-xs-4 ">
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
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('Ageing_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div id="ageing_data">
            
        </div>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>