<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#btnsubmit').click(function (){
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            var from = $('#from').val();
            var to = $('#to').val();
            var idwallet = $('#idwallet').val();
            var allwallet = $('#allwallet').val();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_get_salary_incentive_report'); ?>",
                data: {idbranch: idbranch, from: from, to: to, branches: branches, idwallet: idwallet, allwallet: allwallet},
                success: function(data){
                   $('#salaryincentive_report').html(data);
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-file-chart fa-lg"></span> Salary / Incentive Report </h3></center></div>
<div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="from" name="from" >
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" data-provide="datepicker" id="to" name="to" >
        </div>
        <div class="col-md-2">
            <select class="form-control chosen-select" name="idwallet" id="idwallet">
                <option value="0">All Wallet Type</option>
                <?php foreach($wallet_type as $wallet){ 
                    if($wallet->id_wallet_type != 3){ ?>
                    <option value="<?php echo $wallet->id_wallet_type; ?>"><?php echo $wallet->wallet_type; ?></option>
                <?php $allwallet[] = $wallet->id_wallet_type; }} ?>
            </select>
            <input type="hidden" name="allwallet" id="allwallet" value="<?php echo implode($allwallet,',') ?>">
        </div>
         <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
        <?php }else { ?>
            <div class="col-md-2">
                <select class="form-control chosen-select" name="idbranch" id="idbranch">
                    <option value="0">All Branch</option>
                    <?php foreach($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                    <?php $branches[] = $branch->id_branch; } ?>
                </select>
            </div>
            <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
         <?php }  ?>
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('salary_incentive_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class=""  style="padding: 0; overflow: auto;">
            <div id="salaryincentive_report">
                
            </div>
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>