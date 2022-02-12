<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){        
        $(document).on("click", ".aa_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();            
//             var days = +$('#days').val();    
            var datefrom = $('#datefrom').val();    
             var dateto = $('#dateto').val();    
             var idgodown = +$('#idgodown').val();
             var idbranch = +$('#idbranch').val();
             if(!$('#idbranch').val()){
                alert("Please select branch!");
                return false; 
             }
            if(!idbranch && !idgodown && !brand && !product_category){
                alert("Please do the proper selection!");
                return false;
            }
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_store_stock_analysis_report",
                method:"POST",
                data:{ idbranch:idbranch,brand: brand,  idproductcategory: product_category,idgodown:idgodown,datefrom:datefrom,dateto:dateto},
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
            <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Store Stock Analysis  </h3>                   
    </center>
</div>
<div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">    
    <div class="col-md-2 col-sm-2 col-xs-4" style="padding: 2px">
        <div class="input-group">
            <div class="input-group-btn">
                <input type="text" name="datefrom" id="datefrom" class="col-md-1 form-control input-sm" data-provide="datepicker" placeholder="From Date">
            </div>
            <div class="input-group-btn">
                <input type="text" name="dateto" id="dateto" class="col-md-1 form-control input-sm" data-provide="datepicker" placeholder="To Date">
            </div>
        </div>
    </div>
    <div class="clearfix"></div><br>
    
<!--<div class="col-md-1" style="margin-top: 12px;">Days Sale</div>-->
    <!--<div class="col-md-1" style="margin-top: 7px;">-->
        <!--<input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />-->
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    <!--</div>-->
    
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
                            <option value="">Select Branch</option>
                            <option value="0">All Branch</option>
                            <?php foreach ($branch_data as $type){ ?>
                            <option value="<?php echo $type->id_branch; ?>"><?php echo $type->branch_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>               
    <?php }    ?>
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
            <option value="0">All Brands</option>
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
        <div class="col-md-2" style="text-align: center;">
            <button type="button"  class="aa_stock btn btn-primary gradient2" style="margin-top: 6px;line-height: unset;">Generate</button>
        </div>
        
    <div class="clearfix"></div>
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