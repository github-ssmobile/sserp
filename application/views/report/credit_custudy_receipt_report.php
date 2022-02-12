<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('.btnsubmit').click(function(){
            var idbranch = $('#idbranch').val();
            var from = $('#from').val();
            var to = $("#to").val();
            if(from != '' && to != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Report/ajax_get_credit_custudy_receipt_report",
                    method:"POST",
                    data:{idbranch : idbranch, from: from, to: to},
                    success:function(data)
                    {
                        $("#credit_custudy").html(data);
                    }
                });
            }else{
                alert("Select date Range");
                return false;
            }
        });
    });
</script>
<div class="col-md-10 col-sm-10 col-xs-10"><center><h3 style="margin: 0"><span class="mdi mdi-file-document fa-lg"></span>Credit Custody Receipt Report</h3></center></div><div class="clearfix"></div><br>
<div>
     <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php }else { ?>
            <div class="col-md-2">
                <select class="form-control chosen-select" name="idbranch" id="idbranch">
                    <option value="0">All Branch</option>
                    <?php foreach($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php } ?>
                </select>
            </div>
         <?php }  ?>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="from" name="from" >
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="to" name="to" >
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-info btnsubmit ">Submit</button>
        </div>
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
        <div class="col-md-1 col-sm-1 col-xs-1 pull-right"><button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('credit_custudy_receipt');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button></div>
    <div class="clearfix"></div>
</div>
<div class="thumbnail" style="font-size: 13px; overflow: auto; margin-top: 5px; padding: 0">
    <div id="credit_custudy">
        
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>