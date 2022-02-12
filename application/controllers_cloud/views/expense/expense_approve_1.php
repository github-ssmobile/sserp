<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $(document).on('click', 'a[id=approveexpense]', function() {
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            var idexpense = parentdiv.find('#idexpense').val();
            var approved_amount = parentdiv.find('#approved_amount').val();
            var remark = parentdiv.find('#remark').val();
            //alert(idexpense);
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_approve_branch_expense'); ?>",
                data: {idexpense: idexpense, approved_amount: approved_amount, remark: remark},
                success: function(data){
                    alert("Expense Approve Suuceessfully!..")
                    window.location.reload();
                }
            }); 
       }); 
       $(document).on('click', 'a[id=rejectexpense]', function() {
            var ce = $(this);
            var parentdiv = $(ce).closest('td').parent('tr');
            var idexpense = parentdiv.find('#idexpense').val();
            var approved_amount = parentdiv.find('#rejectapproved_amount').val();
            var remark = parentdiv.find('#remark').val();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_reject_branch_expense'); ?>",
                data: {idexpense: idexpense, approved_amount: approved_amount, remark: remark},
                success: function(data){
                    alert("Expense Rejected Suuceessfully!..")
                    window.location.reload();
                }
            }); 
       }); 
    });
</script>
<div class="col-md-8"><center><h3 style="margin: 10px 0"><span class="mdi mdi-currency-usd fa-lg"></span>Approve Expense </h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('user_petty_cash');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail"  style="padding: 0; overflow: auto;">
             <table id="user_petty_cash" class="table table-bordered table-condensed" >
                <thead style="background-color: #99ccff;border: #6699ff">
                    <th><b>Sr.</b></th>
                    <th><b>Date</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Expense Head</b></th>
                    <th><b>Amount</b></th>
                    <th><b>Reason</b></th>
                    <th><b>Approved Amount</b></th>
                    <th><b>Remark</ </th>
                    <th><b>Action</b></th>
                    
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total=0; foreach ($expense_data as $expense){ if($expense->status == 1) {?>
                    <tr>
                        <form>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($expense->entry_date)); ?></td>
                        <td><?php echo $expense->branch_name; ?></td>
                        <td><?php echo $expense->expense_type; ?></td>
                        <td><?php echo $expense->expense_amount; ?></td>
                        <td><?php echo $expense->expense_remark; ?></td>
                        <td>
                            
                            <input type="number" class="form-control input-sm" name="approved_amount" id="approved_amount" value="<?php echo $expense->expense_amount?>"></td>
                            <input type="hidden" class="form-control input-sm" name="rejectapproved_amount" id="rejectapproved_amount" value="<?php echo $expense->expense_amount?>"></td>
                        <td><input type="text" class="form-control input-sm" name="remark" id="remark" ></td>
                        <td>
                            <input type="hidden" id="idexpense" name="idexpense" value="<?php echo $expense->id_expense; ?> ">
                            <a class="btn btn-primary btn-sm" id="approveexpense">Approve</a>
                            <a class="btn btn-warning btn-sm" id="rejectexpense" >Reject</a>
                        </td>
                        
                    </tr>
                    <?php  } } ?>
                </tbody>
            </table>
            
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>