<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('#datefrom, #dateto, #idbranch').change(function(){
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var idbranch = $('#idbranch').val();
            if(datefrom !== '' && dateto !== '' && idbranch !== ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Report/ajax_cash_closure_report",
                    method:"POST",
                    data:{datefrom : datefrom, dateto : dateto, idbranch: idbranch},
                    success:function(data)
                    {
                        $(".closer_data").html(data);
                    }
                });
            }
        });
    });
</script>
<div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1"><center><h3 style="margin: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Cash Closure Report</h3></center></div><div class="clearfix"></div><hr>
<div class="col-md-1 col-sm-2 col-xs-3" style="padding: 2px">Select Date</div>
<div class="col-md-4 col-sm-4 col-xs-6" style="padding: 2px">
    <div class="input-group">
        <div class="input-group-btn">
            <input type="text" name="search" id="datefrom" data-provide="datepicker" class="form-control input-sm " placeholder="From Date">
        </div>
        <div class="input-group-btn">
            <input type="text" name="search" id="dateto" data-provide="datepicker" class="form-control input-sm " placeholder="To Date">
        </div>
    </div>
</div>
 <!--level 1 - admin, 2 - idbranch, 3 - user_has_branch--> 
<?php if($_SESSION['level'] == 2){ ?>
    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>">
<?php }else{ ?>
    <div class="col-md-3 col-sm-4 col-xs-6" style="padding: 2px">
        <select class="form-control input-sm chosen-select" name="idbranch" id="idbranch">
            <option value="">Select Branch</option>
            <option value="0">All Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>
<?php } ?>
<div class="clearfix"></div><br>
<div class="col-md-4 col-sm-4 col-xs-4 ">
    <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
</div>
<div class="col-md-2 col-sm-2 col-xs-2 pull-right">
    <button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('cash_closure_report<?php echo date('d-m-Y') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div>
<div class="thumbnail" style="height: 500px; font-size: 13px; overflow: auto; margin-top: 5px">
    <div class="closer_data"></div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>