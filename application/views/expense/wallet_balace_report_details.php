<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('.btndelete').click(function (){
           var parenttr = $(this).closest('td').parent('tr');
           var idempsalary = $(this).closest('td').find('#idempsalary').val();
           if(confirm("Do You Want To Delete Salary ? ")){
                $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_delete_emp_salary'); ?>",
                data: {idempsalary: idempsalary},
                success: function(data){
                    if(data == '0' || data == 0){
                        parenttr.remove();
                    }else{
                        alert("Failed to Delete");
                        return false;
                    }
                }
            }); 
           }else{
               return false;
           }
            
       }); 
       $('.btnupdate').click(function (){
            var parenttr = $(this).closest('td').parent('tr');
            var idempsalary = parenttr.find('#idempsalary').val();
            var idbranch = parenttr.find('#branchid').val();
            var branch_name = parenttr.find('#branchid :selected').text();
            var oldbranch = parenttr.find('#oldbranch').val();
            var idstatus = parenttr.find('#status').val();
            var date = parenttr.find('#date').val();
            var amount = parenttr.find('#amount').val();
            var idwallet = parenttr.find('#idwallet').val();
            if(idstatus == 3){
                if(idbranch == ''){
                    alert("Select Branch");
                    return false;
                }else{
                    if(confirm("Do You Want To Update Entry ? ")){
                        $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('Expense/ajax_update_emp_salary'); ?>",
                        data: {idempsalary: idempsalary, idbranch: idbranch, oldbranch: oldbranch, idstatus: idstatus, branch_name: branch_name, date: date, amount: amount, idwallet: idwallet},
                        success: function(data){
                            if(data == 0 || data == '0'){
                                window.location.reload();
                            }else{
                                alert("Failed To Update Employee");
                                return false;
                            }
                        }
                    }); 
                   }else{
                       return false;
                   }
                }
            }else{
                if(confirm("Do You Want To Update Entry ? ")){
                    $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Expense/ajax_update_emp_salary'); ?>",
                    data: {idempsalary: idempsalary, idbranch: idbranch, oldbranch: oldbranch, idstatus: idstatus, branch_name: branch_name, date: date, amount: amount, idwallet: idwallet},
                    success: function(data){
                        if(data == 0 || data == '0'){
                            window.location.reload();
                        }else{
                            alert("Failed To Update Employee");
                            return false;
                        }
                    }
                }); 
               }else{
                   return false;
               }
            }
            
        }); 
        
        $('.status').change(function (){
            var parenttr = $(this).closest('td').parent('tr');
            var idstatus = $(this).closest('td').find('.status').val();
            if(idstatus == 3 || idstatus == '3'){
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Expense/ajax_get_branch_data'); ?>",
                    data: {},
                    success: function(data){
                        parenttr.find('.showbranch').html(data);
                        $(".chosen-select").chosen({ search_contains: true });
                    }
                }); 
            }else{
                parenttr.find('.showbranch').empty();
            }
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
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-file-chart fa-lg"></span> Wallet Report </h3></center></div>
<div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('wallet_balance_report');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class=""  style="padding: 0; overflow: auto;height: 800px">
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #95bdfb" class="fixheader">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Wallet Type</th>
                    <th>Branch</th>
                    <th>Emp Id</th>
                    <th>Emp Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>Delete</th>
                </thead>
                <tbody>
                    <?php $sr=1; $total =0; foreach ($empslary_data as $emps){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $emps->date; ?></td>
                        <td><?php echo $emps->wallet_type?></td>
                        <td><?php echo $emps->branch_name?></td>
                        <td><?php echo $emps->empid;?></td>
                        <td><?php echo $emps->emp_name;?></td>
                        <td><?php echo $emps->amount; $total = $total+$emps->amount; ?></td>
                        <td><b><?php if($emps->status == 1){ echo 'Paid'; }else{ ?></b>
                            <select class="form-control input-sm status" name="status" id="status">
                                <option value="<?php echo $emps->status ?>"><?php if($emps->status == 0){ echo 'Pending'; } elseif ($emps->status == 1){ echo 'Paid'; }elseif($emps->status == 2){ echo 'On Hold'; }elseif($emps->status == 3){ echo 'Transfer'; } ?></option>
                                <option value="0">Pending</option>
                                <option value="2">On Hold</option>
                                <option value="3">Transfer</option>
                            </select> 
                            <?php } ?>
                            <input type="hidden" id="oldbranch" name="oldbranch" value="<?php echo $emps->idbranch; ?>">
                            <input type="hidden" id="date" name="date" value="<?php echo $emps->date; ?>">
                            <input type="hidden" id="amount" name="amount" value="<?php echo $emps->amount; ?>">
                            <input type="hidden" id="idwallet" name="idwallet" value="<?php echo $emps->idwallet; ?>">
                            <div class="showbranch" style="margin-top: 10px"></div>
                        </td>
                        <td><?php if($emps->status != 1){?><a class="btn btn-primary btn-sm btnupdate">Submit</a><?php } ?></td>
                        <td><?php if($emps->status != 1){?><input type="hidden" name="idempsalary" id="idempsalary" value="<?php echo $emps->idemployee_salary; ?>"><a class="btn btn-floating btn-warning btndelete"><span class="fa fa-trash"></span></a><?php } ?></td>
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
                    </tr>
                </tbody>
            </table>
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>