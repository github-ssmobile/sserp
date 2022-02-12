<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#idexpensehead').change(function (){
            var idexpense = $('#idexpensehead').val();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('Expense/ajax_get_expensehead_byid'); ?>",
                data: {idexpense: idexpense},
                success: function(data){
                    if(data == 1){
                        $('.proceed_approval').show();
                        $('.submit_btn').hide();
                        $('#status').val(data);
                    }else{
                        $('.proceed_approval').hide();
                        $('.submit_btn').show();
                        $('#status').val(data);
                    }
                }
            }); 
       }); 
       $('.submit_btn').click(function (){
            var branch_bal = parseInt($('#branch_bal').val());
            var amount = parseInt($('#examount').val());
            var idexpensehead = $('#idexpensehead').val();
             
            if(branch_bal == 0 || branch_bal == ''){
                alert("Petti Cash Not Assigned");
                return false;
            }
            if(branch_bal < amount){
                alert(" Expense Amount is Should Not Greater Than Petti cash  ");
                return false;
            }
            if(idexpensehead == ''){
                alert("Select Expese Head");
                return false;
            }
            if(amount == 0 || amount < 0){
                alert(" Expense Amount is Should Not Less Than 0 ");
                return false;
            }
       });
       
    });
</script>
<div class="col-md-8"><center><h3 style="margin: 10px 0"><span class="mdi mdi-bank fa-lg"></span> Expense </h3></center></div>
<div class="col-md-2"><h3><?php echo $branch_aval_bal->aval_balance ?> <span class="fa fa-rupee "></span> </h3></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <?php echo form_open_multipart('Expense/save_branch_expense', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Expense </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/expense.png" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <div class="col-md-2"><b>Image</b></div>
                 <div class="col-md-5">
                    <div class="thumbnail" id="image-preview" style="min-height: 200px">
                        <label for="image-upload" id="image-label">Upload Image</label>
                        <input type="file" name="userfile" id="file" onchange="loadFilee(event)" >
                        <img height="200" id="userfileimage" style="width: 100%; "/>
                    </div>
                    <script>
                        var loadFilee = function (event) {
                            var visitoutput = document.getElementById('userfileimage');
                            visitoutput.src = URL.createObjectURL(event.target.files[0]);
                        };
                    </script>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>Date</b></div>
                <div class="col-md-10"><input type="text" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" readonly=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>Expense Type</b></div>
                <div class="col-md-10">
                    <select class="form-control" name="idexpensehead" id="idexpensehead" required="">
                        <option value="">Select Expense Type</option>
                        <?php foreach ($expense_head as $expense){ ?>
                        <option value="<?php echo $expense->id_expense_head?>"><?php echo $expense->expense_type;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>Amount</b></div>
                <div class="col-md-10"><input type="number" class="form-control" id="examount" name="amount" required=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-2"><b>Remark</b></div>
                <div class="col-md-10"><input type="text" class="form-control" name="remark"></div>
                <div class="clearfix"></div><br>
            </div>
            <div class="clearfix"></div><hr>
            <input type="hidden" id="status" name="status" value="0">
            <input type="hidden" id="branch_bal" name="branch_bal" value="<?php echo $branch_aval_bal->aval_balance; ?>">
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_branch_expense">Submit</button>
            <button type="submit" class="btn btn-info pull-right proceed_approval" style="display: none" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_branch_expense_proceed_for_approval">Proceed to Approval</button>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
        <?php echo form_close(); ?>
        
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
                    <th><b>Image</b></th>
                    <th><b>Amount</b></th>
                    <th><b>Remark</b></th>
                    <th><b>status</b></th>
                    <!--<th><b>Action</b></th>-->
<!--                    <th><b>Status</b></th>
                    <th><b>Action</b></th>-->
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total=0; foreach ($expense_data as $expense){ 
//                        if($expense->status == 1 &&  ($expense->approved_status == 0 ||  $expense->approved_status == 1 ) ) {?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($expense->entry_date)); ?></td>
                        <td><?php echo $expense->branch_name; ?></td>
                        <td><?php echo $expense->expense_type; ?></td>
                        <td><?php if($expense->expense_image){ ?> <a href="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" target="_blank"><img style="height: 50px;" src="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" ></a><?php }?></td>
                        <td><?php echo $expense->expense_amount; ?></td>
                        <td><?php echo $expense->expense_remark; ?></td>
                        <td><?php if($expense->approved_status == 1 || $expense->approved_status == 3){ echo 'Approved'; }elseif ($expense->approved_status == 0){echo 'Pending For Approval'; }elseif($expense->approved_status == 2){ echo 'Rejected'; }  ?></td>
<!--                        <td>
                            <?php if ($expense->status == 1 && $expense->approved_status == 1){ ?>
                                <form>
                                    <input type="hidden" name="amount" value="<?php echo -$expense->approve_expense_amount; ?> ">
                                    <input type="hidden" name="idbranch" value="<?php echo $expense->idbranch; ?> ">
                                    <input type="hidden" name="idexpense" value="<?php echo $expense->id_expense; ?> ">
                                    <button class="btn btn-primary btn-sm saveedit" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_expense_daybook_data">Submit</button>
                                </form>
                           <?php } ?>
                        </td>-->
                    </tr>
                    <?php // }
                    } ?>
                </tbody>
            </table>
            
        </div><div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>