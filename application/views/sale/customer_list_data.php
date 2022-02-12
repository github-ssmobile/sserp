<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('#btnfilter').click(function (){
            var from = $('#datefrom').val();
            var to = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var idzone = $('#idzone').val();
            var zones = $('#zones').val();
            if((idbranch !='' && idzone !='') || (idbranch =='' && idzone =='')){ 
                alert("Select Any One From Zone and Branch");
                return false;
            }else{
                if(from !='' && to !=''){
                    if(idbranch != ''){
                        $.ajax({
                            url:"<?php echo base_url() ?>Sale/ajax_get_customer_list_byidbranch_date",
                            method:"POST",
                            data:{from: from, to: to, idbranch: idbranch, branches: branches},
                            success:function(data)
                            {
                                $("#cust_data").html(data);
                            }
                        });
                    }else{
                        $.ajax({
                            url:"<?php echo base_url() ?>Sale/ajax_get_customer_list_byidzone_date",
                            method:"POST",
                            data:{from: from, to: to, idzone: idzone, zones: zones},
                            success:function(data)
                            {
                                $("#cust_data").html(data);
                            }
                        });
                    }
                    
                }else{
                    alert("Select Date Range");
                    return false;
                }
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
</style>
<div class="col-md-10"><center><h3><span class="fa fa-handshake-o fa-lg"></span> Customer Data</center></div><div class="clearfix"></div><hr>
<div class="" >
    <div style="padding: 5px;">
        <div class="col-md-2"><b>From </b><input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" required=""></div>
        <div class="col-md-2"><b>To</b> <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date" required=""></div>
        <?php if($this->session->userdata('level') == 2) { ?>
            <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']; ?>">
            <input type="hidden" name="idzone" id="idzone" value="">
        <?php } else { ?> 
                
            <?php if($this->session->userdata('level') == 3){ ?>
            <div class="col-md-2">
                <b>Branch</b>
                <select  name="idbranch" id="idbranch" class="chosen-select"  style="width: 100%">
                    <option value="">Select Branches</option>
                    <option value="0">All Branches</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
            <input type="hidden" name="idzone" id="idzone" value="">
        <?php }else { ?>
            <div class="col-md-2">
            <b>Zone</b>
            <select name="idzone" id="idzone" class="chosen-select" style="width: 100%">
                <option value="">Select Zone</option>
                <option value="0">All Branches</option>
                <?php foreach ($zone_data as $zone){ ?>
                <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
                <?php $zones[] = $zone->id_zone; } ?>
            </select>
            <input type="hidden" name="zones" id="zones" value="<?php echo implode($zones,',') ?>">
        </div>
       <div class="col-md-1"> <b>OR </b></div>
       <div class="col-md-2">
                <b>Branch</b>
                <select  name="idbranch" id="idbranch" class="chosen-select"  style="width: 100%">
                    <option value="">Select Branches</option>
                    <option value="0">All Branches</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
        <?php }
        
                    } ?>
            <div class="col-md-2"><button class="btn btn-primary" id="btnfilter" >Filter</button></div>
        <div class="clearfix"></div><br>
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2 pull-right">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('customer_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div id="cust_data" style="overflow-x: auto;height: 650px;">
            
        </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>