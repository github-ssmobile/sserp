<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        
    $(document).on('change', '#sku', function() {  
        var sku = $('#sku').val();    
        var model_name=$("#sku option:selected").text();
        $.ajax({
            url:"<?php echo base_url() ?>Ingram_Api/ajax_get_ingram_stock_by_variant",
            method:"POST",
            data:{sku : sku,model_name:model_name},
            success:function(data)
            {
                $("#stock_data").html(data);                  
            }
        });
    });
        
        
    });
</script>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span>Stock Search</h3>
    </center>
</div>
<div class="clearfix"></div>
<div class="col-md-2"></div>
  
        <div class="col-md-2 " id="model_list" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="sku" id="sku">
            <option value="">Select Model</option>                     
            <?php foreach ($model_data as $model){ ?>
            <option value="<?php echo $model->$sku_column; ?>"><?php echo $model->full_name; ?></option>
            <?php } ?>
        </select>
        </div>
 
            
        <div class="clearfix"></div><br>
        
    <div class="thumbnail" style="overflow: auto">
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
        <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        
        <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
        </table>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>