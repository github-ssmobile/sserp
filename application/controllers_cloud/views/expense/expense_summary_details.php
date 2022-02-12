<?php include __DIR__.'../../header.php'; ?>
<style>
    .fixheader {
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 99;
    }
</style>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-history fa-lg"></span> Expense Summary Report </h3></center></div>
<div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('expense_summary');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class=""  style="padding: 0; overflow: auto;">
            <table id="expense_summary" class="table table-bordered table-condensed">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th><b>Sr.</b></th>
                    <th><b>Date</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Wallet Type</b></th>
                    <th><b>Expense Head</b></th>
                    <th><b>Expense SubHead</b></th>
                    <th><b>Amount</b></th>
                    <th><b>Expense Remark</b></th>
                    <th><b>Created By</b></th>
                    <th><b>Status</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; $i=1; foreach ($expense_summary_data as $exp){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $exp->entry_date; ?></td>
                        <td><?php echo $exp->branch_name; ?></td>
                        <td><?php echo $exp->wallet_type; ?></td>
                        <td><?php echo $exp->expense_type; ?></td>
                        <td><?php echo $exp->expense_subheader; ?></td>
                        <td><?php echo $exp->approve_expense_amount; $total = $total+$exp->approve_expense_amount; ?></td>
                        <td><?php echo $exp->expense_remark; ?></td>
                        <td><?php echo $exp->user_name; ?></td>
                        <td><?php if($exp->approved_status == 0){ echo 'Pending For Approval'; } elseif ($exp->approved_status == 1 || $exp->approved_status == 3){ echo 'Approved'; }elseif( $exp->approved_status == 2){ echo 'Rejected'; } { }?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                         <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>