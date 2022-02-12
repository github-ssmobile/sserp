<?php include __DIR__ . '../../header.php'; ?>
<style>
    .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
        z-index: 9;
    }
    .grdark{
        background-color: #ade7ca;
    }
</style>
<script>
    $(document).ready(function(){
//    $(document).ready(function(){
        $('.btnsubmit').click(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if(datefrom != '' && dateto != '' && idbranch != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Sale/ajax_invoice_correction_report",
                    method:"POST",
                    data:{idbranch: idbranch, datefrom: datefrom, dateto: dateto, branches: branches},
                    success:function(data)
                    {
                        $(".daybook").html(data);
                    }
                });
            }else{
                alert("Select Filter");
                return false;
            }
        });
    });
</script>
<center><h3 style="margin-top: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Invoice Correction Report </h3></center><div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <!--Date Range-->
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
    <div class="col-md-3">
        <!--Select Branches-->
        <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
            <option value="0">All Branches</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php $branchess[] = $branch->id_branch; } ?>
        </select>
    </div>
    <input type="hidden" name="branches" id="branches" value="<?php echo implode($branchess,',') ?>">
<?php }?>
<div class="col-md-1"><button class="btn btn-info btn-sm btnsubmit" >Filter</button></div>
<div class="col-md-1 pull-right">
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('invoice_correction_report <?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<div class="thumbnail" style="height: 500px; font-size: 12px; overflow: auto; padding: 0">
    <table id="invoice_correction_report <?php echo date('d-m-Y h:i a') ?>" class="table table-condensed table-striped table-full-width table-bordered table-responsive table-hover daybook" style="margin: 0"></table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>