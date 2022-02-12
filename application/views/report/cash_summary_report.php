<?php include __DIR__ . '../../header.php'; ?>
<style>
    .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        z-index: 9;
    }
    .fixedelement_bottom{
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        background-color: #c5f4dd;
        font-size: 13px;
        z-index: 9;
    }
    .grdark{
        background-color: #ade7ca;
    }
</style>
<script>
    $(document).ready(function(){
        $('#datefrom, #dateto, #idbranch').change(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if(datefrom !== '' && dateto !== '' && idbranch !== ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Report/ajax_cash_sumamry_report",
                    method:"POST",
                    data:{datefrom : datefrom, dateto : dateto, idbranch: idbranch, branches:branches},
                    success:function(data)
                    {
                        $(".cash_ledger_report").html(data);
                    }
                });
            }
        });
    });
</script>
<div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1"><center><h3 style="margin: 0"><span class="mdi mdi-cash-multiple fa-lg"></span> Cash Summary Report</h3></center></div><div class="clearfix"></div><hr>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Select Date</div>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
        </div>
    </div>
</div>
<?php if($_SESSION['level'] == 2){ ?>
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>">
<?php }else{ ?>
    <div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">
        <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
            <option value="">Select Branch</option>
            <option value="0">All Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php $branches[] = $branch->id_branch; } ?>
        </select>
    </div>
    <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
<?php } ?>
<div class="col-md-3 col-sm-3 col-xs-3 ">
    <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
</div>
<div class="col-md-1 col-sm-1 col-xs-1"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('cash_ledger_report<?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div><div class="clearfix"></div>
<div class="thumbnail" style="height: 650px; font-size: 13px; overflow: auto; margin-top: 5px; padding: 0;">
    <table id="cash_ledger_report<?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-bordered table-responsive cash_ledger_report" style="margin: 0"></table>   
</div>
<?php include __DIR__ . '../../footer.php'; ?>