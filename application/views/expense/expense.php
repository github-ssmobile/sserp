<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
//       $('#idexpensehead').change(function (){
//            var idexpense = $('#idexpensehead').val();
//            $.ajax({
//                type: "POST",
//                url: "<?php echo base_url('Expense/ajax_get_expensehead_byid'); ?>",
//                data: {idexpense: idexpense},
//                success: function(data){
//                    if(data == 1){
//                        $('.proceed_approval').show();
//                        $('.submit_btn').hide();
//                        $('#status').val(data);
//                    }else{
//                        $('.proceed_approval').hide();
//                        $('.submit_btn').show();
//                        $('#status').val(data);
//                    }
//                }
//            }); 
//       });
       
       $('.submit_btn').click(function (){
//            var branch_bal = parseInt($('#branch_bal').val());
            var branch_bal = parseInt($('#avalbal').val());
            var amount = parseInt($('#examount').val());
            var idwallettype = $('#idwallettype').val();
            var idexpensehead = $('#idexpensehead').val();
            var available_cash = $('#available_cash').val();
            if(idwallettype == ''){
                alert("Select Expese Wallet Type");
                return false;
            }
            if(idexpensehead == ''){
                alert("Select Expese Head");
                return false;
            }
            if(available_cash > 0){
                if(branch_bal > 0 ){
                    if(branch_bal == 0 || branch_bal == ''){
                        alert("Wallet Limit Exceeded");
                        return false;
                    }
                    if(branch_bal < amount){
                        alert(" Expense Amount is Should Not Greater Than " +branch_bal + " Rupees");
                        return false;
                    }
                    if(amount == 0 || amount < 0){
                        alert(" Expense Amount is Should Not Less Than 0 ");
                        return false;
                    }
                    if(amount > available_cash){
                        alert("Expense Amount is Should Not Greater Than Branch Available Cash  ");
                        return false;
                    }
                }
                else{
                    alert("Wallet Limit Exceeded");
                    return false;
                }
            }
            else{
                alert("Branch Available Balance is 0");
                return false;
            }
       });
       
       $('.btndelete').click(function (){
            var ce = $(this);
            var idexpense = $(ce).closest('td').find('#idexpense').val();
            if(confirm("Do You Want To Cancel Expense?")){
                $.ajax({
                   type: "POST",
                   url: "<?php echo base_url('Expense/delete_expense'); ?>",
                   data: {idexpense: idexpense},
                   success: function(data){
                      alert("Expense Deleted Suceessfully!");
                      window.location.reload();
                   }
                }); 
            }else{
                 return false();
            }
        });
        
        $('#idwallettype').change(function (){
            var idwallet = $('#idwallettype').val();
            if(idwallet != ''){
                $.ajax({
                   type: "POST",
                   url: "<?php echo base_url('Expense/ajax_load_expense_header_byidwallet'); ?>",
                   data: {idwallet: idwallet},
                   success: function(data){
                       $('#expensehead').html(data);
                   }
                }); 
            }else{
                alert("Select Wallet Type");
                return false;
            }
            
        });
        $('#idexpensehead').change(function (){
            var idhead = $('#idexpensehead').val();
            if(idhead != ''){
                $.ajax({
                   type: "POST",
                   url: "<?php echo base_url('Expense/ajax_load_expense_subheader_byidhead'); ?>",
                   data: {idhead: idhead},
                   success: function(data){
                       $('#idexpenssubehead').html(data);
                   }
                }); 
            }else{
                alert("Select Wallet Type");
                return false;
            }
            
        });
        
   });
   
   $(document).ready(function(){
      $('.btnprint').dblclick(function(e){
        e.preventDefault();
        alert("Don Not Double Click On Button ");
      });   
    });
</script>
<style>
.blink_me {
  animation: blinker 2s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<div class="col-md-8"><center><h3 style="margin: 10px 0"><span class="mdi mdi-bank fa-lg"></span> Expense </h3></center></div>
<div class="col-md-2"><h3><?php  //foreach ($expense_data as $esp){ $total_exp = $total_exp + $esp->approve_expense_amount;} $avl_bal = $branch_aval_bal->aval_balance - $total_exp; echo $avl_bal; ?> </h3></div>
<div class="col-md-1">
    <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
</div><div class="clearfix"></div><hr>
<?php $avl_bal=0;
if($branch_aval_bal){
    foreach ($branch_aval_bal as $bavl){ 
        if($bavl->idwallet_type != 3) { ?>
            <a style="cursor: pointer" href="<?php echo base_url()?>Expense/salary_incentive_expense/<?php echo $bavl->idwallet_type ?>">
                <div class="col-md-2 hovereffect" style="border: 1px solid #004085;height:100px;margin-right: 10px;padding: 5px;border-radius: 18px;border-left: 7px solid #3e9cd1; " >
                    <h4><center><?php echo $bavl->wallet_type; ?></center></h4><hr style="border-color: #bae5f5">
                        <h4><center>
                       <?php $total_exp =0; foreach($expense_data as $exp){ 
                            if($exp->id_wallet_type == $bavl->idwallet_type){
                              $total_exp = $exp->exp_amount;
                            }
                        }
                        $avl_bal = $bavl->aval_balance - $total_exp;
                        echo $avl_bal; ?> <span class="fa fa-rupee"></span></center></h4>
                </div>
            </a>
        <?php } else{?>
            <a style="cursor: pointer" href="<?php echo base_url()?>Expense/add_branch_expense/<?php echo $bavl->idwallet_type ?>">
                <div class="col-md-2 hovereffect" style="border: 1px solid #004085;height:100px;margin-right: 10px;padding: 5px;border-radius: 18px;border-left: 7px solid #3e9cd1; " >
                    <h4><center><?php echo $bavl->wallet_type; ?></center></h4><hr style="border-color: #bae5f5">
                        <h4><center>
                       <?php $total_exp =0; foreach($expense_data as $exp){ 
                            if($exp->id_wallet_type == $bavl->idwallet_type){
                              $total_exp = $exp->exp_amount;
                            }
                        }
                        $avl_bal = $bavl->aval_balance - $total_exp;
                        echo $avl_bal; ?> <span class="fa fa-rupee"></span></center></h4>
                </div>
            </a>
    <?php } }
}else{ ?>
     <div class="col-md-2 hovereffect" style="border: 1px solid #004085;height:100px;margin-right: 10px;padding: 5px;border-radius: 18px;border-left: 7px solid #3e9cd1; " >
    <h4><center>Wallet Balance</center></h4><hr style="border-color: #bae5f5">
        <h4><center>
        <?php echo $avl_bal; ?> <span class="fa fa-rupee"></span></center></h4>
    </div>
<?php } ?>
<div class="clearfix"></div><br>

<?php $avaiable=0;$todays_sale=0;$todays_salesreturn=0;$credit_rec=0;$pending_closure_cash=0;$total_expense=0;$total_short_deposit=0;$total_daybook=0;$tcash_deposit=0;
    if($total_daybook_cash->sum_cash){
        $total_daybook = $total_daybook_cash->sum_cash;
    }
    foreach ($todays_cash as $cash){
        if($cash->entry_type == 1){ // Sale
            $todays_sale = $cash->todays_cash;
        }elseif($cash->entry_type == 2 || $cash->entry_type == 3){ // 2 = Sales Return - Cash // 3 = Sales Return - Replace, upgrade
            $todays_salesreturn = $cash->todays_cash;
        }elseif($cash->entry_type == 4){ // Credit, Buyback receive
            $credit_rec = $cash->todays_cash;
        }elseif($cash->entry_type == 5){ // Expense
            $total_expense = $cash->todays_cash;
        }elseif($cash->entry_type == 6){ // Deposit to HO
            $tcash_deposit = $cash->todays_cash;
        }
    }
    $available = $total_daybook + $todays_sale + $todays_salesreturn + $credit_rec + $total_expense + $tcash_deposit;
?>
<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <?php if($_SESSION['expense_allowed'] == 1){ ?> 
        <?php $var_closer = 1;
        if(count($todays_cash_closure) > 0){
            $var_closer = 2;
        }elseif(count($sale_last_entry_byidbranch)){
            if($sale_last_entry_byidbranch[0]->sum_cash == 0){
                $var_closer = 1;
            }else{
                if(count($cash_closure_last_entry) == 0){
                    $var_closer = 0;
                }else{
                    if($sale_last_entry_byidbranch[0]->date > $cash_closure_last_entry[0]->date){
                        $var_closer = 0;
                    }elseif($sale_last_entry_byidbranch[0]->date == $cash_closure_last_entry[0]->date){
                        if($sale_last_entry_byidbranch[0]->sum_cash <= $cash_closure_last_entry[0]->closure_cash){
                            $var_closer = 1;
                        }else{
                            $var_closer = 0;
                        }
                    }
                }
            }
        }
        if($var_closer == 2){
            echo '<center><h3>You submitted todays cash closure</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                .'<h3>You do not able to add todays expenses after cash closure.</h3>'
                .'</center><hr>';
        }elseif($var_closer == 1){ ?>
        <?php echo form_open_multipart('Expense/save_branch_expense', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Expense </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/expense.png" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <div class="col-md-4"><b>Image</b></div>
                 <div class="col-md-8">
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
                <div class="col-md-4"><b>Date</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" readonly=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Wallet Type</b></div>
                <div class="col-md-8">
                    <select class="form-control" name="idwallettype" id="idwallettype" required="">
                        <option value="">Select Wallet Type</option>
                        <?php foreach ($wallet_type as $wallet){ ?>
                        <option value="<?php echo $wallet->id_wallet_type?>"><?php echo $wallet->wallet_type;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Expense Header</b></div>
                <div class="col-md-8">
                    <div id="expensehead">
                        <select class="form-control" name="idexpensehead" id="idexpensehead" required="">
                            <option value="">Select Expense Header</option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Expense Sub-Header</b></div>
                <div class="col-md-8">
                    <div id="idexpenssubehead">
                        <select class="form-control" name="idexpenssubehead" id="idexpenssubehead" >
                             <option value="">Select Expense Subhead</option>
                        </select>
                        <input type="hidden"  id="status" name="status" value="0">
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Amount</b></div>
                <div class="col-md-8"><input type="number" class="form-control" id="examount" name="amount" required=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Remark</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="remark"></div>
                <div class="clearfix"></div><br>
            </div>
            <div class="clearfix"></div><hr>
         
            <!--<input type="hidden" id="branch_bal" name="branch_bal" value="<?php echo $avl_bal; ?>">-->
            <input type="hidden" id="available_cash" name="available_cash" value="<?php echo $available; ?>">
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_branch_expense">Submit</button>
            <!--<button type="submit" class="btn btn-info pull-right proceed_approval" style="display: none" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_branch_expense_proceed_for_approval">Proceed to Approval</button>-->
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div><hr>
        <?php echo form_close(); ?>
        <?php 
        }else{ 
            echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                .'<h3>You must have to submit cash closure first.</h3>'
                .'</center>';
        } 
        ?>
        <?php } else{ ?>
        <div class="blink_me" style="color: red"><center><h4>Expense Facility Blocked For This Branch !..</h4></center></div>
        <?php } ?>
        
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
        <div class="col-md-2 pull-right">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('user_petty_cash');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class=""  style="padding: 0; overflow: auto;">
             <table id="user_petty_cash" class="table table-bordered table-condensed" >
                <thead style="background-color: #99ccff;border: #6699ff">
                    <th><b>Sr.</b></th>
                    <th><b>Date</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Wallet type</b></th>
                    <th><b>Expense Head</b></th>
                    <th><b>Expense Subhead</b></th>
                    <th><b>Image</b></th>
                    <th><b>Amount</b></th>
                    <th><b>Remark</b></th>
                    <th><b>status</b></th>
                    <!--<th><b>Action</b></th>-->
                    <th><b>Print</b></th>
                    <!--<th><b>Action</b></th>-->
<!--                    <th><b>Status</b></th>
                    <th><b>Action</b></th>-->
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total=0; foreach ($allexpense_data as $expense){ 
//                        if($expense->status == 1 &&  ($expense->approved_status == 0 ||  $expense->approved_status == 1 ) ) {?>
                    <tr <?php if($expense->approved_status == 2){?>style="background-color: #f7c2c2;"<?php } ?> >
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($expense->entry_date)); ?></td>
                        <td><?php echo $expense->branch_name; ?></td>
                        <td><?php echo $expense->wallet_type; ?></td>
                        <td><?php echo $expense->expense_type; ?></td>
                        <td><?php echo $expense->expense_subheader; ?></td>
                        <td><?php if($expense->expense_image != NULL){ ?> <a href="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" target="_blank"><img style="height: 50px;" src="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" ></a><?php } ?></td>
                        <td><?php echo $expense->expense_amount; $total = $total + $expense->expense_amount; ?></td>
                        <td><?php echo $expense->expense_remark; ?></td>
                        <td><?php if($expense->approved_status == 1 || $expense->approved_status == 3){ echo 'Approved'; }elseif ($expense->approved_status == 0){echo 'Pending For Approval'; }elseif($expense->approved_status == 2){ echo 'Rejected'; } elseif($expense->approved_status == 4){ echo 'Expense Cancelled'; } ?></td>
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
<!--                        <td>
                            <input type="hidden" name="idexpense" id="idexpense" value="<?php echo $expense->id_expense; ?>">
                           <?php //if(!$todays_cash_closure ){ ?> 
                            <a class="btn btn-floating btn-small btn-warning btndelete" ><span class="fa fa-trash-o"></span></a>
                                <?php // } ?>
                        </td>-->
                       <td><?php if($expense->approved_status != 2){ ?> <a class="btn btn-floating btn-small btn-info btnprint" href="<?php echo base_url()?>Expense/print_expense/<?php echo $expense->id_expense?>/1"><span class="fa fa-print"></span></a> <?php } ?></td>
                    </tr>
                    <?php // }
                    } ?>
                    <tr>
                        <td></td>
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