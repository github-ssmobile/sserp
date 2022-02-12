<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('.btnsubmit').click(function(){
            var from = $('#from').val();
            var to = $('#to').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var idstatus = $('#idstatus').val();
            
            if(from != '' && to != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Report/ajax_get_token_cancellation_report",
                    method:"POST",
                    data:{from : from, to: to, idbranch: idbranch, branches: branches, idstatus: idstatus},
                    success:function(data)
                    {
                        $("#tokendata").html(data);
                    }
                });
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
    table {
    border-spacing: 0;
    border-collapse: separate;
}
</style>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-file-document fa-lg"></span>Token Cancellation Report</h3></center></div><div class="clearfix"></div><br>
<div>
    <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required value="<?php echo date('Y-m-d')?>"></div>
    <div class="col-md-2"><input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required value="<?php echo date('Y-m-d')?>"> </div>
    <div class="col-md-3">
        <select data-placeholder="Select Status" name="idstatus" id="idstatus" class="form-control" required="" style="width: 100%">
            <option value="all">All Status</option>
            <option value="0">Pending</option>
            <option value="1">Completed</option>
            <option value="2">Cancelled</option>
        </select>
    </div>    
    <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
    <?php } else { ?>
        <div class="col-md-2">
            <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
                <option value="0">All Branches</option>
                <?php foreach ($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php $branches[] = $branch->id_branch; } ?>
            </select>
        </div>
        <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
    <?php } ?>
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
    <div class="col-md-1 col-sm-1 col-xs-1 pull-right"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('token_data_report');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div>
    <div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <div id="tokendata" style="overflow-x: auto;height: 750px">
        
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>