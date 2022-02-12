<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnreport').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if(from !='' && to !='' && idbranch !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Sales_return/ajax_get_gst_sale_return_report",
                    method:"POST",
                    data:{from: from, to: to, idbranch: idbranch, branches: branches},
                    success:function(data)
                    {   
                        $('#sale_data').html(data);
                    }
                });
            }
        });
    });
</script>
<style>
table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
 
}

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<center><h3 style="margin-top: 0"><span class="mdi mdi-checkbox-multiple-marked-circle  fa-lg"></span>GST Sales Return Report</h3></center><div class="clearfix"></div><hr>
    <div class="col-md-2">
        <b>From</b>
        <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required="" placeholder="Date From">
    </div>
    <div class="col-md-2">
        <b>To</b>
        <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required="" placeholder="Date To">
    </div>
    <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
    <?php } else {
        if($this->session->userdata('role_type') == 1){ ?>
            <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php }else{ ?>
            <div class="col-md-3">
                <b>Branch</b>
                <select class="form-control chosen-select" name="idbranch" id="idbranch">
                    <option value="0">All Branch</option>
                    <?php foreach($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
        <?php } 
    }?>
    <div class="col-md-2">
        <button class="btn btn-info btnreport" style="margin-top: 10px;">Submit</button>
    </div>
    <div class="clearfix"></div><br>
    <div class="col-md-5 col-sm-6 col-xs-6">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-4">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-2 col-sm-4 col-xs-4">
        <button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('gst_sales_return_report');"><span class="fa fa-file-excel-o"></span> Excel</button>
    </div>
    <?php if( $save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
    <?php endif; ?><div class="clearfix"></div>
    <div class="thumbnail" style="height: 700px; overflow: auto; padding: 0; margin-top: 10px; font-size: 13px;">
        <div id="sale_data"></div>
    </div>
<div class="clearfix"></div><br>
<?php include __DIR__ . '../../footer.php'; ?>