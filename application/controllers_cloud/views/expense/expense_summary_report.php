<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
            var idbranch = $('#idbranch').val();
            var from = $('#from').val();
            var to = $('#to').val();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_get_expense_summary_report_data'); ?>",
                data: {idbranch: idbranch, from: from, to: to},
                success: function(data){
                   $('#expense_data').html(data);
                }
            }); 
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-history fa-lg"></span> Expense Summary Report </h3></center></div>
<div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
         <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php }else { ?>
            <div class="col-md-1"><b>Branch</b></div>
            <div class="col-md-3">
                <select class="form-control chosen-select" name="idbranch" id="idbranch">
                    <option value="0">All Branch</option>
                    <?php foreach($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php } ?>
                </select>
            </div>
         <?php  }  ?>
        <div class="col-md-1"><b>From</b></div>
        <div class="col-md-2">
            <input type="text" class="form-control" data-provide="datepicker" name="from" id="from" autocomplete="off">
        </div>
        <div class="col-md-1"><b>To</b></div>
        <div class="col-md-2">
            <input type="text" class="form-control" data-provide="datepicker" name="to" id="to" autocomplete="off">
        </div>
        <div class="col-md-2"><button class="btn btn-primary" id="btnsubmit">Submit</button></div>
        <div class="clearfix"></div><br>
        <div class="col-md-4  " >
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-1 col-md-offset-6">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('expense_summary_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class=""  style="padding: 0; overflow: auto;">
            <div id="expense_data">
                
            </div>
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>