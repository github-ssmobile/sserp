<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var idpcat = $('#idpcat').val();
            var allpcats = $('#allpcats').val();
            
            var idbranch = $('#idbranch').val();
            var allbranches = $('#allbranches').val();
            
            var idzone = $('#idzone') .val();
            var allzone = $('#allzone') .val();
            
            if(idbranch == '' && idzone == ''){
                alert("Select Any One From Branch and Zone");
                return false;
            }else{
                
                if(from != '' && to != '' && idpcat !='' && idbranch != ''){
                   $.ajax({
                        url:"<?php echo base_url() ?>Target/ajax_get_mtd_brand_sale_report_byidbranch",
                        method:"POST",
                        data:{from: from, to: to, idpcat: idpcat, idbranch: idbranch, allbranches: allbranches, allpcats: allpcats},
                        success:function(data)
                        {
                            $('#target_data').html(data);
                            sortTable();
                        }
                    });
                }
                if(from != '' && to != '' && idpcat !='' && idzone != ''){
                   $.ajax({
                        url:"<?php echo base_url() ?>Target/ajax_get_mtd_brand_sale_report_byidzone",
                        method:"POST",
                        data:{from: from, to: to, idpcat: idpcat, idzone: idzone, allzone: allzone, allpcats: allpcats},
                        success:function(data)
                        {
                            $('#target_data').html(data);
                            if(idzone == 'all'){
                                sortTablezoneall();
                            } else{
                                sortTablezone();
                            }
                        }
                    });
                }
            }
        });
    });
    function sortTable(){
	var rows = $('#MTD_Brand_Sale_Report tbody  tr').get();
	rows.sort(function(a, b) {
            var A = +$(a).children('td').eq(6).text().toUpperCase();
            var B = +$(b).children('td').eq(6).text().toUpperCase();
            if(A < B) {
              return 1;
            }
            if(A > B) {
              return -1;
            }
            return 0;
	});
	$.each(rows, function(index, row) {
	    $('#MTD_Brand_Sale_Report').children('tbody').append(row);
        });
    }
    function sortTablezone(){
	var rows = $('#MTD_Brand_Sale_Report tbody  tr').get();
	rows.sort(function(a, b) {
            var A = +$(a).children('td').eq(2).text().toUpperCase();
            var B = +$(b).children('td').eq(2).text().toUpperCase();
            if(A < B) {
              return 1;
            }
            if(A > B) {
              return -1;
            }
            return 0;
	});
	$.each(rows, function(index, row) {
	    $('#MTD_Brand_Sale_Report').children('tbody').append(row);
        });
    }
    function sortTablezoneall(){
	var rows = $('#MTD_Brand_Sale_Report tbody  tr').get();
	rows.sort(function(a, b) {
            var A = +$(a).children('td').eq(1).text().toUpperCase();
            var B = +$(b).children('td').eq(1).text().toUpperCase();
            if(A < B) {
              return 1;
            }
            if(A > B) {
              return -1;
            }
            return 0;
	});
	$.each(rows, function(index, row) {
	    $('#MTD_Brand_Sale_Report').children('tbody').append(row);
        });
    }
</script>
<style>
    .fixheader {
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixheader1 {
        position: sticky;
        top: 30px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
    .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
  .fixleft1{
    position: sticky;
    left:45px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #c6e6f5;

  }
  .fixleft2{
    position: sticky;
    left:150px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    background-color: #c6e6f5;

  }
</style>
<center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> MTD Brand Sale Report </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
   
    <div  style="padding: 20px 10px; margin: 0">
        <div class="col-md-2"><b>From</b>
            <input type="text" name="search" id="from" data-provide="datepicker" class="form-control input-sm " placeholder="From Date" required>
        </div>
        <div class="col-md-2">
            <b>To</b>
            <input type="text" name="search" id="to" data-provide="datepicker" class="form-control input-sm " placeholder="To Date" required>
        </div>
        <div class="col-md-2">
            <b>Product Category</b>
            <select name="idpcat" class="form-control chosen-select" id="idpcat">
                <option value="0">All Category</option>
                <?php foreach ($product_cat_data as $pcat){  ?>
                    <option value="<?php echo $pcat->id_product_category; ?>"><?php echo $pcat->product_category_name;?></option>
                <?php  $productcat[] = $pcat->id_product_category; } ?>
            </select>
            <input type="hidden" name="allpcats" id="allpcats" value="<?php echo implode($productcat,',') ?>">
        </div>
        <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
              <input type="hidden" id="idzone" name="idzone" value="">
        <?php } else{ ?>
            <div class="col-md-2">
                <b>Branch</b>
                <select name="idbranch" class="form-control chosen-select" id="idbranch">
                    <option value="">Select Branch</option>
                    <option value="0">All Branch</option>
                    <?php foreach ($branch_data as $branch){  ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name;?></option>
                    <?php  $branches[] = $branch->id_branch; } ?>
                </select>
                <input type="hidden" name="allbranches" id="allbranches" value="<?php echo implode($branches,',') ?>">
            </div>
        <?php } ?>
        
        <?php if($this->session->userdata('level') != 3 && $this->session->userdata('level') != 2){?>
            <div class="col-md-1"><b>OR</b></div>
            <div class="col-md-2">
                <b>Zone</b>
                <select name="idzone" class="form-control chosen-select" id="idzone">
                    <option value="">Select Zone</option>
                    <option value="all">Overall Zones</option>
                    <option value="0">All Zones</option>
                    <?php foreach ($zone_data as $zone){  ?>
                        <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name;?></option>
                    <?php  $zones[] = $zone->id_zone; } ?>
                </select>
                <input type="hidden" name="allzone" id="allzone" value="<?php echo implode($zones,',') ?>">
            </div>
        <?php } else{ ?>
            <input type="hidden" id="idzone" name="idzone" value="">
        <?php } ?>        
        <div class="col-md-1">
            <br>
            <button class="btn btn-primary" id="btnreport">Filter</button>
        </div>
       <div class="clearfix"></div><hr>
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
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('MTD_Brand_Sale_Report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="target_data" style="overflow-x: auto;height: 680px;">
        </div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>