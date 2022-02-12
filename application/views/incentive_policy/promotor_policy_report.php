<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('#btnreport').click(function (){
            var month = $('#monthyear').val();
            var idpcat = $('#idpcat').val();
            var idbranch = $('#idbranch').val();
            var allbranches = $('#allbranches').val();
            var idzone = $('#idzone').val();
            if(idbranch == '' && idzone == ''){
                alert("Select Any One from Branch & Zone");
                return false;
            }else if (idbranch != '' && idzone != '') {
                alert("Select Any One from Branch & Zone");
                return false;
            }else{
                if(month !='' && idpcat !=''){
                    $.ajax({
                        url:"<?php echo base_url() ?>Incentive_policy/ajax_get_promotor_policy_report",
                        method:"POST",
                        data:{ month: month, idpcat: idpcat, idbranch: idbranch, allbranches: allbranches, idzone: idzone},
                        success:function(data)
                        {
                            $('#reportdata').html(data);
                        }
                    });
                }else{
                    alert("Select Data Properly!...");
                    return false();
                }
            }
        });
    });
</script>
<style>
.fixheader {
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 9;
    }
</style>
<div class="col-md-8 col-md-offset-1 col-sm-8 col-sm-offset-2">
    <center><h3><span class="mdi mdi-checkbox-marked-outline fa-lg"></span>Promotor Incentive Report</h3></center>
</div>
<div class="clearfix"></div><hr>
<div class="thumbnail"  style="padding: 0; margin: 0; min-height: 650px;">
    
    <div class="" id="pay" style="padding: 20px 10px; ">
        <div class="col-md-2"><b>Month</b>
            <input type="text" class="form-control monthpick"  placeholder="Select Month" id="monthyear" name="monthyear"  value="<?php echo date('Y-m');?>">
        </div>
        <div class="col-md-3"><b>Product Category</b>
            <select name="idpcat" id="idpcat" class="form-control input-sm">
                <option value="">Select Product Category</option>
                <?php foreach ($product_category_data as $pdata){ ?>
                <option value="<?php echo $pdata->id_product_category ?>"><?php echo $pdata->product_category_name; ?></option>
                <?php  } ?>
            </select>
        </div>
        <?php if($this->session->userdata('level') == 2){ ?>
            <input type="hidden" class="form-control" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php } else{ ?>
            <div class="col-md-3"><b>Branch</b>
                <select name="idbranch" id="idbranch" class="form-control input-sm chosen-select">
                    <option value="">Select Branch</option>
                    <option value="0">All Branch</option>
                    <?php foreach ($branch_data as $bdata){ ?>
                    <option value="<?php echo $bdata->id_branch ?>"><?php echo $bdata->branch_name; ?></option>
                    <?php $branches[] = $bdata->id_branch; } ?>
                </select>
                 <input type="hidden" name="allbranches" id="allbranches" value="<?php echo implode($branches,',') ?>">
            </div>
        <?php } 
        if($this->session->userdata('level') == 1){ ?>
            <div class="col-md-1"><b><span style="color: red">OR</span></b></div>
            <div class="col-md-2"><b>Zone</b>
                <select name="idzone" id="idzone" class="form-control input-sm chosen-select">
                    <option value="">Select Zone</option>
                    <?php foreach ($zone_data as $zdata){ ?>
                    <option value="<?php echo $zdata->id_zone ?>"><?php echo $zdata->zone_name; ?></option>
                    <?php  } ?>
                </select>
            </div>
        <?php }else{?>
            <input type="hidden" class="form-control" name="idzone" id="idzone" value="">
        <?php } ?>
        <div class="col-md-1"><br><button class="btn btn-primary" id="btnreport">Submit</button></div>
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
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('promotor_incentive_policy');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <div id="reportdata"></div>
    </div>
    <div class="clearfix"></div><br>

 </div>

<?php include __DIR__ . '../../footer.php'; ?>