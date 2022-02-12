<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){

       $('.submit_btn').click(function (){
            var closetdiv = $(this).closest('div');
            var parentdiv = $(this).closest('div').parent('div');
            
            var branch_bal = parseInt(closetdiv.find('#avalbal').val());
            var amount = parseInt(closetdiv.find('#examount').val());
            var idwallettype = closetdiv.find('#idwallettype').val();
            var available_cash = closetdiv.find('#available_cash').val();
            var date = closetdiv.find('#date').val();
            var idempsalary = closetdiv.find('#idempsalary').val();
            
            var idexpensehead = parentdiv.find('#idexpensehead').val();
            var idexpenssubehead = parentdiv.find('#idexpenssubehead').val();
            var status = parentdiv.find('#status').val();
            
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
                    else if(branch_bal < amount){
                        alert(" Expense Amount is Should Not Greater Than " +branch_bal + " Rupees");
                        return false;
                    }
                    else if(amount == 0 || amount < 0){
                        alert(" Expense Amount is Should Not Less Than 0 ");
                        return false;
                    }
                    else if(amount > available_cash){
                        alert("Expense Amount is Should Not Greater Than Branch Available Cash  ");
                        return false;
                    }
                    else{
                        $.ajax({
                            type: "POST",
                            url: "<?php echo base_url('Expense/ajax_save_expense'); ?>",
                            data: {branch_bal: branch_bal, amount: amount, idwallettype: idwallettype, available_cash: available_cash, date: date, idexpensehead: idexpensehead, idexpenssubehead: idexpenssubehead, status: status, idempsalary: idempsalary},
                            success: function(data){
                                if(data == 0 || data == '0'){
                                    alert("Expense Save Successfully");
                                    window.location.reload();
                                }else{
                                    if(data == 2 || data == '2'){
                                        alert("Expense Already Submitted! ");
                                        return false;
                                    }else{
                                        alert("Failed To Save Expense! Try Again");
                                        return false;
                                    }
                                }
                            }
                        }); 
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
       
        $('.btnproceed').click(function (){
            var parentdiv = $(this).closest('div').parent('div');
            parentdiv.find('.expensehead').show();
        });
        
        $('.idexpensehead').change(function (){
            var idhead = $(this).val();
            var parentdiv = $(this).closest('div').parent('div');
            if(idhead != ''){
                $.ajax({
                   type: "POST",
                   url: "<?php echo base_url('Expense/ajax_load_expense_subheader_byidhead'); ?>",
                   data: {idhead: idhead},
                   success: function(data){
                       parentdiv.find('#expense_subhead').html(data);
                       parentdiv.find('.btnproceed').hide();
                       parentdiv.find('.submit_btn').show();
                   }
                }); 
            }else{
                alert("Select Expense Head");
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
.card {
  box-shadow: 0 4px 8px 0 rgba(0.2,0.1,0.2,0.2);
  transition: 0.3s;
  width: 32%;
  margin-right: 10px;
  border-radius: 5px;
  border: 1px solid #cccccc;
  padding: 5px;
  margin-bottom: 10px;
}
.card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

</style>
<?php 
    $avl_bal=0;
    if($branch_aval_bal){
        $total_exp =0;
        if($expense_data->exp_amount > 0){
            $total_exp = $expense_data->exp_amount;
        }
        $avl_bal = $branch_aval_bal->aval_balance - $total_exp;
    }
?>
<div class="col-md-8"><center><h3 style="margin: 10px 0"><span class="mdi mdi-wallet fa-lg"></span> <?php echo $wallet_type->wallet_type; ?> </h3></center></div>
<div class="col-md-2"><h3><?php echo $avl_bal;   ?> </h3></div>
<div class="col-md-1"></div><div class="clearfix"></div><hr>

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
        }elseif($var_closer == 1){  ?>
                <?php foreach($emp_salary_data as $emp){ ?>
                    <div class="col-md-4 col-lg-4 col-sm-4 card">
                        <div style="text-align: right;padding: 5px;color: #000000"><b><?php echo $emp->date?></b></div>
                        <div class="clearfix"></div><hr>
                        <div class="col-md-2 col-lg-2 col-sm-2">ID</div>
                        <div class="col-md-1 col-lg-1 col-sm-1">-</div>
                        <div class="col-md-9 col-lg-9 col-sm-9"><b><?php echo $emp->empid;?></b></div>
                        <div class="clearfix"></div>
                        <div class="col-md-2 col-lg-2 col-sm-2">Name</div>
                        <div class="col-md-1 col-lg-1 col-sm-1">-</div>
                        <div class="col-md-9 col-lg-9 col-sm-9"><b><?php echo $emp->emp_name;?></b></div>
                        <div class="clearfix"></div>
                        <div class="col-md-2 col-lg-2 col-sm-2">Amount</div>
                        <div class="col-md-1 col-lg-1 col-sm-1"> - </div>
                        <div class="col-md-9 col-lg-9 col-sm-9"><b><?php echo $emp->amount;?></b></div>
                        <div class="clearfix"></div>
                        <div class="col-md-2 col-lg-2 col-sm-2">Status</div>
                        <div class="col-md-1 col-lg-1 col-sm-1">-</div>
                        <div class="col-md-9 col-lg-9 col-sm-9"><b><?php if($emp->status == 0){ echo 'Pending'; }elseif($emp->status == 1){ echo 'Paid'; }elseif($emp->status == 2){ echo 'On Hold'; }if($emp->status == 3){ echo 'Transfer'; }?></b></div>
                        <div class="clearfix"></div><br>
                        <div class="expensehead" style="width: 90%;padding-left: 20px;display: none;" >
                            <select class="form-control idexpensehead" name="idexpensehead" id="idexpensehead">
                                <option value="">Select Expense Header</option>
                                <?php foreach ($heade_data as $head){ ?>
                                    <option value="<?php echo $head->id_expense_head?>"><?php echo $head->expense_type;?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <div id="expense_subhead" style="width: 90%;padding-left: 20px;">

                        </div>
                        <div class="clearfix"></div><hr>
                        <div>
                            <input type="hidden" id="examount" name="examount" value="<?php echo $emp->amount;?>">
                            <input type="hidden" id="date" name="date" value="<?php echo date('Y-m-d');?>">
                            <!--available petticash amount-->
                            <input type="hidden" id="avalbal" name="avalbal" value="<?php echo $avl_bal;?>">
                            <input type="hidden" id="idwallettype" name="idwallettype" value="<?php echo $idwallet?>">
                            <!--Daybook Available cash-->
                            <input type="hidden" id="available_cash" name="available_cash" value="<?php echo $available; ?>">
                            <input type="hidden" id="idempsalary" name="idempsalary" value="<?php echo $emp->idemployee_salary; ?>">
                            <?php if($emp->status !=1 && $emp->status != 2){?><button class="btn btn-primary btn-sm pull-right btnproceed">Proceed To Pay</button><?php } ?>
                            <button class="btn btn-info btn-sm pull-right submit_btn" style="display: none"><span class="fa fa-money"></span> PAY</button>
                        </div>
                    </div>
                <?php } ?>
         <!--<button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Expense/save_branch_expense">Submit</button>-->
            <div class="clearfix"></div>
        <?php }else{ 
            echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                .'<h3>You must have to submit cash closure first.</h3>'
                .'</center>';
        } 
    } else{ ?>
        <div class="blink_me" style="color: red"><center><h4>Expense Facility Blocked For This Branch !..</h4></center></div>
    <?php } ?>
</div>

<?php include __DIR__.'../../footer.php'; ?>