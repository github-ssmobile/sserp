<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.submit_btn').click(function (){
            var idgodown = $('#idgodown') .val();
            var idbranch = $('#idbranch') .val();
            if(idgodown != '' && idbranch != ''){
                if(!confirm('Do You Want To Upload File')) {
                    return false;
                }
            }else{
                alert("Select Godown And Branch");
            }
        });
        $('#btnreport').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var branchid = $('#branch').val();
            var branches = $('#branches').val();
            if(from !='' && to !='' && branchid !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Stock/ajax_opening_stock_data",
                    method:"POST",
                    data:{from: from, to: to, branchid: branchid, branches: branches},
                    success:function(data)
                    {
                        $('#report_data').html(data);
                    }
                });
           }else{
               alert("Select Date Range");
               return false;
           }
        });
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
    <div class="col-md-1">
        <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
    </div><div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
        <div class="clearfix"></div><br>
        <div class="col-md-2">
            <input type="text" placeholder="Select From date" class="form-control" name="from" id="from" data-provide="datepicker">
        </div>
        <div class="col-md-2">
            <input type="text" placeholder="Select To date" class="form-control" name="to" id="to" data-provide="datepicker">
        </div>
        <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <input type="hidden" id="branch" name="branch" value="<?php echo $_SESSION['idbranch']?>">
        <?php } else { ?>
            <div class="col-md-2">
                <select class="form-control" name="branch" id="branch">
                    <option value="0">All Branch</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch?>"><?php echo $branch->branch_name;?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
        <?php } ?>
        <div class="col-md-1"><button type="submit" id="btnreport" class="btn btn-info btn-sm">Filter</button></div>
         <div class="col-md-3 col-sm-3 col-xs-3 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-primary btn-sm pull-right " onclick="javascript:xport.toCSV('opening_stock_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div>
        <div class="clearfix"></div><br>
        
        <div id="report_data"></div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>