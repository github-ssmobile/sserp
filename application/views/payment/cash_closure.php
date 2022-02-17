<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        // disable mousewheel on a input number field when in focus
        // (to prevent Cromium browsers change the value when scrolling)
        $('form').on('focus', 'input[type=number]', function (e) {
          $(this).on('wheel.disableScroll', function (e) {
            e.preventDefault();
          });
        });
        $('form').on('blur', 'input[type=number]', function (e) {
          $(this).off('wheel.disableScroll');
        });
       $('.btndelete').click(function (){
          if(!confirm("Do You Want To Delete Cash Closure")){
              return false;
          } 
       });
    }); 
</script>
<style>
    .blink {
        animation: blinker 1s linear infinite;
    }
    @keyframes blinker {
        5% {
            opacity: 0;
        }
    }
</style>
<?php $avaiable=0;$todays_sale=0;$todays_salesreturn=0;$credit_rec=0;$pending_closure_cash=0;$total_expense=0;$total_short_deposit=0;$total_daybook=0;
        $tcash_deposit=0;$tcash_receive=0;$tcash_refund=0;$tcash_accessories=0;
//$total_deposit=0;
    if($total_daybook_cash->sum_cash){
        $total_daybook = $total_daybook_cash->sum_cash;
    }
    foreach ($todays_cash as $cash){
        if($cash->entry_type == 1){ // Sale
            $todays_sale = $cash->todays_cash;
        }elseif($cash->entry_type == 2 || $cash->entry_type == 3){ // 2 = Sales Return - Cash // 3 = Sales Return - Replace, upgrade
            $todays_salesreturn += $cash->todays_cash;
        }elseif($cash->entry_type == 4){ // Credit, Buyback receive
            $credit_rec = $cash->todays_cash;
        }elseif($cash->entry_type == 5){ // Expense
            $total_expense = $cash->todays_cash;
        }elseif($cash->entry_type == 6){ // Deposit to HO
            $tcash_deposit = $cash->todays_cash;
        }elseif($cash->entry_type == 8){ // Deposit to HO
            $tcash_receive = $cash->todays_cash;
        }elseif($cash->entry_type == 9){ // cash receive
            $tcash_refund = $cash->todays_cash;
        }elseif($cash->entry_type == 10){ // accessories cash deposite
            $tcash_accessories = $cash->todays_cash;
        }
//        elseif($cash->entry_type == 6){ // Todays cash deposit
//            $total_deposit = $cash->todays_cash;
//        }
    }
//    if($sum_cash_closure->pending_closure_cash){
//        $pending_closure_cash = -$sum_cash_closure->pending_closure_cash;
//    }
//    if($todays_short_deposit_sum->short_deposit){
//        $total_short_deposit = -$todays_short_deposit_sum->short_deposit;
//    }
//    if($todays_cash_deposit->sum_deposit_cash){
//        $tcash_deposit = -$todays_cash_deposit->sum_deposit_cash;
//    }
    $available = $total_daybook + $todays_sale + $todays_salesreturn + $credit_rec + $total_expense + $tcash_deposit + $tcash_receive + $tcash_refund + $tcash_accessories;
?>
<script>
    $(document).ready(function(){
        $('.qty').keyup(function(){
            var ce = $(this);
            var parrent = $(ce).closest('td').parent('tr');   
            var denom = parrent.find(".denom").val();   
            var first_val = denom.replace(/ .*/,'');
            var cal = +first_val * +ce.val();
            parrent.find(".amount").val(cal);
            parrent.find(".amountsp").html(cal);
            var total_amount_sum=0;
            $('tr').each(function () {
                $(this).find('.amount').each(function () {
                    var total_amount = $(this).val();
                    if (!isNaN(total_amount) && total_amount.length !== 0) {
                        total_amount_sum += parseFloat(total_amount);
                    }
                });
                $('#total_amount', this).val(total_amount_sum);
                $('#total_amount_lb', this).html(total_amount_sum);
            });
//            var remaining_total = $('#available_total').val() - total_amount_sum;
//            $('#remaining_total').val(remaining_total);
        });
        $('#closure_btn').click(function(){
            var total_amount = $('#total_amount').val();
            if(total_amount <= 0){
                swal('😠 Alert','Not allowed to enter lest than 1 quantity!!','warning');
                return false;
            }
            if(total_amount != $('#available_total').val()){
                swal('😠 Alert','Available amount and entered amount should be equal!!','warning');
                return false;
            }
            if(!confirm('Are you sure? Do you want to submit')){
                return false;
            };
        });
    });
    
    function get_accessories_daybookamount_cnt(){
            
        var idbranch = $('#idbranch').val();
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){
            dd='0'+dd
        } 
        if(mm<10){
            mm='0'+mm
        } 
        var today = yyyy+'-'+mm+'-'+dd;
       // alert(today);return false;
        
        $.ajax({
        url:"<?php echo base_url() ?>Accessories_deposite/get_accessories_daybookamount_cnt",
        method:"POST",
        data:{idbranch: idbranch,today:today},
        success:function(data)
        {
            if(data == 1){
                alert("Please Deposite Accessories Cash");
                return false;
            }else{
                console.log('accessories amount is 0.')
            }
        }
       }); 
          
      }
    
    
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-clipboard-text fa-lg"></span> Cash Closer</h3></center></div>
<div class="col-md-1"><a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a></div><div class="clearfix"></div><hr>
 <!--level 1 = admin, 2 = idbranch, 3 = user_has_branch-->
 <?php // } 
    $var_closer = 1;
    $datetime = date('Y-m-d H:i:s');
    $spdatetime = date('Y-m-d h:i:s a');
    $heading = "Today's Cash Closure ";
//    echo $daybook_cash_sum[0]->sum_cash;
//    if($daybook_cash_sum[0]->sum_cash == 0){
//        $var_closer = 1;
//    }else
    if(count($sale_last_entry_byidbranch)){
        if($sale_last_entry_byidbranch[0]->sum_cash == 0){
            $var_closer = 1;
        }else{
            if(count($cash_closure_last_entry) == 0){
                $var_closer = 0;
                $datetime = date('Y-m-d 23:50:00',strtotime("-1 days"));
                $heading = "Yesterday's Cash Closure ";
                $spdatetime = date('d-m-Y 11:50:00',strtotime("-1 days")).'pm';
            }else{
                if($sale_last_entry_byidbranch[0]->date > $cash_closure_last_entry[0]->date){
                    $var_closer = 0;
                    $datetime = date('Y-m-d 23:50:00',strtotime("-1 days"));
                    $heading = "Yesterday's Cash Closure ";
                    $spdatetime = date('d-m-Y 11:50:00',strtotime("-1 days")).'pm';
                }elseif($sale_last_entry_byidbranch[0]->date == $cash_closure_last_entry[0]->date){
                    if($sale_last_entry_byidbranch[0]->sum_cash <= $cash_closure_last_entry[0]->closure_cash){
                        $var_closer = 1;
                    }else{
//                        echo $sale_last_entry_byidbranch[0]->sum_cash.' '.$cash_closure_last_entry[0]->closure_cash;
                        $var_closer = 0;
                        $datetime = date('Y-m-d 23:50:00',strtotime("-1 days"));
                        $heading = "Yesterday's Cash Closure ";
                        $spdatetime = date('d-m-Y 11:50:00',strtotime("-1 days")).'pm';
                    }
                }
            }
        }
    }
    ?>
<form class="collapse" id="pay" method="POST" action="<?php echo base_url('Payment/save_cash_closure') ?>" >
    <div class="col-md-4 col-sm-6" style="padding: 0">
        <div style="margin-top: 100px;">
        <center><h4 style="color: #ff0000; font-family: Kurale"><b>Daily fill Cash Closure...</b></h4></center>
        <div class="neucard shadow-inset border-light p-2">
            <div class="shadow-soft border-light rounded">
                <div class="thumbnail" style="padding: 0; margin: 0; border-radius: 0; border: none;">
                    <img src="<?php echo base_url() ?>assets/images/cash_closure.jpg" />
                    <!--<img src="https://images.all-free-download.com/images/graphicthumb/3d_cash_register_elements_vector_530412.jpg" />-->
                </div>
            </div>
        </div>
        <center><h4 style="color: #ff0000; font-family: Kurale"><b>Stay upto date...</b></h4></center>
        <!--<center><span class="blink" style="color: #ff0000"><i class="mdi mdi-cash fa-lg"></i> <b>Daily fill Cash Closure</b> and stay upto date.</span></center><br>-->
        </div>
    </div>
    <div class="col-md-8 col-sm-12"><br>
        <div style="background-image: linear-gradient(to right top, #051937, #153c64, #216393, #288ec3, #2cbcf2);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px;">
            <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                <center><i class="pe pe-7s-cash fa-lg"></i> Cash Closure Form </center>
            </div>
        </div><div class="clearfix"></div>
        <?php if(count($todays_cash_closure) > 0){ ?>
        <div class="col-md-6 col-md-offset-3 thumbnail" style="border: none"><center><img src="<?php echo base_url() ?>assets/images/premature-closure.jpg" style="width: 100%"/></center></div>
        <div class="clearfix"></div>
        <center><h4 class="blink" style="color: #ff0000; font-family: Kurale"><b>Daily Cash Closure Limit is over.</b></h4></center>
        <?php }else{ ?>
        <center><h4 class="blink" style="font-family: Kurale; color: #153c64"><?php echo $heading.$spdatetime ?></h4></center>
        <div class="" style="font-size: 14px;">
            <div class="col-md-6" style="padding: 3px">
                <div class="thumbnail">
                    <table class="table table-striped table-condensed" style="margin-bottom: 0">
                        <thead>
                            <th class="col-md-4">Denomination</th>
                            <th class="col-md-4">Quantity</th>
                            <th class="col-md-4">Amount</th>
                        </thead>
                        <tbody>
                            <?php $i = 0; foreach ($denomination as $denom) { ?>
                            <tr style="font-size: 13px">
                                <td>
                                    <?php echo $denom->denomination ?>
                                    <input type="hidden" class="denom" name="denom[]" value="<?php echo $denom->denomination ?>" />
                                </td>
                                <td><input type="number" class="form-control input-sm qty" name="qty[]" placeholder="Qty" autocomplete="off" /></td>
                                <td>
                                    <span class="amountsp">0</span>
                                    <input type="hidden" class="form-control input-sm amount" name="amount[]" placeholder="Amount" readonly=""/>
                                </td>
                            </tr>
                            <?php $i++; } ?>
                            <tr>
                                <th colspan="2">Total</th>
                                <th><input type="text" id="total_amount" name="total_amount" class="form-control input-sm" value="0" placeholder="Total Amount" readonly=""/></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="count" value="<?php echo $i ?>"/>
            </div>
            <div class="col-md-6" style="padding: 3px">
                <div class="thumbnail">
                    <table class="table table-striped" style="margin: 0">
                        <thead>
                            <th>Today's</th>
                            <th>Amount</th>
                        <thead>
                        <tbody>
                            <tr>
                                <td><i class="mdi mdi-plus-circle-outline fa-lg"></i> Opening cash</td>
                                <td><?php echo $total_daybook ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-plus-circle-outline fa-lg"></i> Sale(In cash)</td>
                                <td><?php echo $todays_sale ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-plus-circle-outline fa-lg"></i> Credit,Buyback Received</td>
                                <td><?php echo $credit_rec ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-plus-circle-outline fa-lg"></i> Cash Payment Received</td>
                                <td><?php echo $tcash_receive ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-minus-circle-outline fa-lg"></i> Sales return</td>
                                <td><?php echo $todays_salesreturn ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-minus-circle-outline fa-lg"></i> Expense</td>
                                <td><?php echo $total_expense ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-minus-circle-outline fa-lg"></i> Advanced Cash - Refund</td>
                                <td><?php echo $tcash_refund ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-minus-circle-outline fa-lg"></i> Cash Deposit</td>
                                <td><?php echo $tcash_deposit ?></td>
                            </tr>
                            <tr>
                                <td><i class="mdi mdi-plus-circle-outline fa-lg"></i> Accessories Cash</td>
                                <td><?php echo $tcash_accessories ?></td>
                            </tr>
                            <tr>
                                <td>Available</td>
                                <td>
                                    <?php echo $available ?>
                                    <input type="hidden" name="available_total" id="available_total" value="<?php echo $available ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>Entered Amount</td>
                                <td><b style="font-size: 16px"><div id="total_amount_lb">0</div></b></td>
                            </tr>
                        </tbody>
                    </table>
<!--                                <span class="col-md-3 col-sm-3">Bank</span>
                    <div class="col-md-9 col-sm-9">
                        <select class="form-control input-sm" required="" name="idbank">
                            <option value="">Select Bank</option>
                            <?php foreach ($bank_data as $bank){ ?>
                            <option value="<?php echo $bank->id_bank ?>"><?php echo $bank->bank_name.' '.$bank->bank_branch.' '.$bank->bank_ifsc ?></option>
                            <?php } ?>
                        </select>
                    </div><div class="clearfix"></div><br>-->
                    <textarea class="form-control input-sm" rows="3" name="remark" placeholder="Enter Remark" required=""></textarea>
                    <div class="clearfix"></div><br>
                    <?php // if($var_closer){ ?>
                    <span class="col-md-12">
                        <span class="pull-left">Closure Date</span>
                        <div class="pull-right">
                            <?php echo $spdatetime ?>
                            <!--<input type="hidden" name="remaining_total" id="remaining_total" value="0" />-->
                            <input type="hidden" name="date" value="<?php echo $datetime ?>" />
                        </div><div class="clearfix"></div>
                    </span>
                    <?php // }else{ ?>
<!--                    <span class="col-md-12">
                        <span class="pull-left">Closure Date</span>
                        <div class="pull-right">
                            <?php 
//                            $datetime=date('Y-m-d 23:50:00',strtotime("-1 days"));
//                            echo date('d-m-Y 11:50 p',strtotime("-1 days")); ?>
                            <input type="hidden" name="date" value="<?php // echo $datetime ?>" />
                        </div><div class="clearfix"></div>
                    </span>-->
                    <?php // } ?>
                    <div class="clearfix"></div><hr>
                    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $this->session->userdata('idbranch') ?>" />
                    <!--<input type="hidden" id="var_closer" name="var_closer" value="<?php echo $var_closer ?>" />-->
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="button"  id="closure_btn"  onclick="get_accessories_daybookamount_cnt()" class="pull-right btn btn-info waves-effect" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Save</button>
                    <div class="clearfix"></div>
                </div>
            </div><div class="clearfix"></div>
        </div>
        <?php } ?>
    </div><div class="clearfix"></div><br>
</form>
<table class="table table-striped table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Sr</th>
        <th>Reference Id</th>
        <th>Date</th>
        <th>Closure Amount</th>
        <th>Remark</th>
        <th>Print</th>
        <!--<th>Action</th>-->
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; $total_amt=0; foreach($cash_closure_data as $cash_closure){ ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $cash_closure->idcombine ?></td>
            <td><?php echo $cash_closure->entry_time ?></td>
            <td><?php $total_amt += $cash_closure->closure_cash; echo $cash_closure->closure_cash ?></td>
            <td><?php echo $cash_closure->remark ?></td>
            <td><a href="<?php echo base_url()?>Payment/cash_closure_print/<?php echo $cash_closure->id_cash_closure?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
            <!--<td><?php // if($cash_closure->status == 0){ echo "Pending"; }else{ echo "Deposited"; } ?></td>-->
            <!--<td><?php // if($cash_closure->status == 0){ ?> <a href="<?php echo base_url()?>Payment/delete_cash_closure/<?php echo $cash_closure->id_cash_closure?>" class="btn btn-warning btn-floating waves-effect btndelete"><i class="fa fa-trash-o"></i></a> <?php // } ?></td>-->
        </tr>
        <?php $i++; } ?>
    </tbody>
    <thead>
        <th colspan="2"></th>
        <th>Total</th>
        <th><?php echo $total_amt; ?></th>
        <th></th>
        <th></th>
    </thead>
</table>
<?php include __DIR__ . '../../footer.php'; ?>