<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('.btnsubmit').click(function(){
            var idzone = $('#idzone').val();
            var allzones = $('#allzones').val();
            var idproductcat = $('#idproductcat').val();
            var allpcats = $('#allpcats').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var from = $('#from').val();
            var to = $('#to').val();
            if(idzone != ''){
                    $.ajax({
                        url:"<?php echo base_url() ?>Report/ajax_get_zone_price_category_data_byzone",
                        method:"POST",
                        data:{idzone : idzone, idproductcat: idproductcat, allzones: allzones, allpcats: allpcats, from: from, to: to},
                        success:function(data)
                        {
                            $("#price_data").html(data);
                        }
                    });
                }
            else{
                alert("Select Zone ");
                return false;
            }
           
            
        });
    });
</script>
<style>
.fixheader {
        /*background-color: #fbf7c0;*/
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
.fixheader1 {
        /*background-color: #fbf7c0;*/
        position: sticky;
        top: 29px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
</style>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-file-document fa-lg"></span>Zone Price Category Report</h3></center></div><div class="clearfix"></div><br>
<div>
    <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required value="<?php echo date('Y-m-d')?>"></div>
    <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required value="<?php echo date('Y-m-d')?>"> </div>
    <div class="col-md-2">
        <select class="chosen-select form-control" name="idproductcat" id="idproductcat" required="">
            <option value="0">All Product Category</option>
            <?php foreach ($product_category as $pcat) { ?>
                <option value="<?php echo $pcat->id_product_category; ?>"><?php echo $pcat->product_category_name; ?></option>
            <?php $pcats[] = $pcat->id_product_category; } ?>
        </select>
        <input type="hidden" name="allpcats" id="allpcats" value="<?php echo implode($pcats,',') ?>">
    </div>
    <div class="col-md-2">
        <select class="chosen-select form-control" name="idzone" id="idzone" required="">
            <option value="">Select Zone</option>
            <option value="0">All Zone</option>
            <?php foreach ($zone_data as $zone) { ?>
                <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
            <?php $zones[] = $zone->id_zone; } ?>
        </select>
        <input type="hidden" name="allzones" id="allzones" value="<?php echo implode($zones,',') ?>">
    </div>
    <?php //if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <!--<input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">-->
    <?php //} else { ?>
<!--        <div class="col-md-2">
            <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
                <option value="">Select Branch</option>
                <option value="0">All Branches</option>
                <?php foreach ($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php $branches[] = $branch->id_branch; } ?>
            </select>
        </div>
        <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">-->
    <?php //} ?>
    <div class="col-md-1">
        <button type="submit" class="btn btn-info btnsubmit ">Filter</button>
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
    <div class="col-md-1 col-sm-1 col-xs-1 pull-right"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('zone_price_category_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div>
    <div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <div id="price_data">
        
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>