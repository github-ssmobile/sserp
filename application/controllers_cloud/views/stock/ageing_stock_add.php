<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        
        $(document).on('change', '#brand', function() {          
            var brand = +$('#brand').val();    
            var idgodown = +$('#idgodown').val();
                if(!idgodown){
                    alert("Please select godown!!!");
                    return false;
                }
            var product_category=0;
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
            var idgodown = +$('#idgodown').val();         
            var brand = +$('#brand').val();         
            var days = +$('#days').val();    
            $.ajax({
                url:"<?php echo base_url() ?>Stock/ajax_get_ageing_branch_stock_by_variant",
                method:"POST",
                data:{variant : model,idgodown : idgodown, days: days, brand: brand},
                success:function(data)
                {
                    $("#showdata").html(data);      
                    sortTable();
                }
            });
        });
        
        
        
    });
    function sortTable(){
	var rows = $('#stock_data tbody  tr').get();
	rows.sort(function(a, b) {
            var A = +$(a).children('td').eq(8).text().toUpperCase();
            var B = +$(b).children('td').eq(8).text().toUpperCase();
            if(A < B) {
              return -1;
            }
            if(A > B) {
              return 1;
            }
            return 0;
	});
	$.each(rows, function(index, row) {
	    $('#stock_data').children('tbody').append(row);
        });
    }
</script>
<style>
.fixheader {
    background-color: #fbf7c0;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 999;
}
.fixheader1 {
    background-color: #fbf7c0;
    position: sticky;
    top: 28px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 999;
}
/*.fixonleft {
    position: fixed;
    bottom: 120px;
    right: 80px; 
}
.fixonright {
    position: fixed;
    bottom: 120px;
    left: 250px; 
}*/
</style>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span>Ageing Stock Add</h3>
    </center>
</div>
<div class="clearfix"></div>
<div class="col-md-2"></div>
    <div class="col-md-1" style="margin-top: 7px;">
        <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
        <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    </div>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="idgodown" id="idgodown" required="">
            <?php foreach ($active_godown as $godown){ ?>
            <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2 col-sm-3" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="brand" id="brand" required="">
            <option value="">Select Brand</option>            
            <?php foreach ($brand_data as $brand){ ?>
            <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-md-2 " id="model_list" style="padding: 5px">
        <select class="chosen-select form-control input-sm" name="model" id="model">
            <option value="0">Select Model</option>
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
        <form>
            <div id="showdata" style="overflow-x: auto;height: 700px;">
                
            </div>
        </form>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>