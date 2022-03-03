<?php include __DIR__ . '../../header.php'; ?>
<style>
    .blink {
        animation: blinker 1s linear infinite;
    }
    @keyframes blinker {
        10% {
            opacity: 0;
        }
    }
</style>
<script>
    $(document).ready(function (){
       $('.btndelete').click(function (){
          if(!confirm("Do You Want To Delete Cash Deposite")){
              return false;
          } 
       }); 
    });
</script>
<?php $pending_closure_cash=0;$idcombine=0;$id_cash_closure=0;
//    if($total_daybook_cash->sum_cash){
//        $total_daybook = $total_daybook_cash->sum_cash;
//    }
//    if($todays_daybooksum->todays_sale_cash){
//        $todays_sale = $todays_daybooksum->todays_sale_cash;
//    }
//    if($todays_salesreturn_sum->todays_sale_cash){
//        $todays_salesreturn = $todays_salesreturn_sum->todays_sale_cash;
//    }
//    if($credit_received_amount->todays_sale_cash){
//        $credit_rec = $credit_received_amount->todays_sale_cash;
//    }
    if(count($sum_cash_closure)){
        $pending_closure_cash = $sum_cash_closure[0]->pending_closure_cash;
        $idcombine = $sum_cash_closure[0]->idcombine;
        $id_cash_closure = $sum_cash_closure[0]->id_cash_closure;
        $closure_date_cash =   $sum_cash_closure[0]->closure_date;
    }else{
        $closure_date_cash = "";
    }
//    $available = $total_daybook + $todays_sale + $todays_salesreturn + $credit_rec + $pending_closure_cash;
?>
<script>
    $(document).ready(function(){
        $('#total_amount').keyup(function(){
            var pending_closure_cash = $('#pending_closure_cash').val();
            var minus = pending_closure_cash - $(this).val();
            $('#remain_amount').val(minus);
            $('#remain_amount_lb').html(minus);
        });
        $('#deposit_btn').click(function(){
//            var total_amount = $('#total_amount').val();
//            if(total_amount <= 0){
//                swal('😠 Alert','Deposit amount should be greater than 0!!','warning');
//                return false;
//            }
            if(!confirm('Are you sure? Do you want to submit')){
                return false;
            };
        });
    });
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-clipboard-text fa-lg"></span> Cash Deposit</h3></center></div>
<div class="col-md-1"><a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a></div><div class="clearfix"></div><hr>
 <!--level 1 = admin, 2 = idbranch, 3 = user_has_branch--> 
<form id="pay">
    <div class="col-md-4 col-md-offset-1"><br><br><br><br>
        <center><h4 class="blink" style="color: #ff0000; font-family: Kurale"><b>Stay upto date...</b></h4></center>
        <!--<center><h4 class="blink" style="color: #ff0000; font-family: Kurale"><b>Deposit Cash after Closure...</b></h4></center>-->
        <div class="neucard shadow-inset border-light p-2">
            <div class="shadow-soft border-light rounded">
                <div class="thumbnail" style="padding: 0; margin: 0; border-radius: 0; border: none;">
                    <img src="<?php echo base_url() ?>assets/images/Cashdeposit.jpeg" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
    <?php $var_closer = 1;
    if(count($sale_last_entry_byidbranch)){
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
    if($var_closer){ ?>
        <div style="background-image: linear-gradient(to right top, #051937, #153c64, #216393, #288ec3, #2cbcf2);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px;">
            <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                <center><i class="pe pe-7s-cash fa-lg"></i> Cash Deposit Form </center>
            </div>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail" style="font-size: 15px;">
            <div class="col-md-4">Total Closure Cash</div>
            <div class="col-md-8">
                <?php echo moneyFormatIndia($pending_closure_cash) ?> <i class="mdi mdi-currency-inr fa-lg"></i>
                <input type="hidden" name="pending_closure_cash" id="pending_closure_cash" value="<?php echo $pending_closure_cash ?>" />
            </div><div class="clearfix"></div><br>
            <!--<div class="col-md-4">Amount in words</div>-->
            <!--<div class="col-md-8"><?php // echo getIndianCurrency($pending_closure_cash) ?></div><div class="clearfix"></div><br>-->
            <div class="col-md-4">Deposit Amount</div>
            <div class="col-md-8"><input type="number" class="form-control" name="total_amount" id="total_amount" placeholder="Enter Deposit Amount" max="<?php echo $pending_closure_cash ?>" min="1" required="" /></div><div class="clearfix"></div><br>
            <div class="col-md-4">Remaining Amount</div>
            <div class="col-md-8">
                <span name="remain_amount_lb" id="remain_amount_lb">0</span> <i class="mdi mdi-currency-inr fa-lg"></i>
                <input type="hidden" name="remain_amount" id="remain_amount" /></div><div class="clearfix"></div><br>
            <div class="col-md-4">Select Bank</div>
            <div class="col-md-8">
                <select class="form-control" required="" name="idbank">
                    <option value="">Select Bank</option>
                    <?php foreach ($bank_data as $bank){ ?>
                    <option value="<?php echo $bank->id_bank ?>"><?php echo $bank->bank_name.' '.$bank->bank_branch.' '.$bank->bank_ifsc ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
            <div class="col-md-4">Enter Remark</div>
            <div class="col-md-8">
                <textarea class="form-control input-sm" name="remark" placeholder="Enter Remark" required=""></textarea>
            </div><div class="clearfix"></div><br>
            <div class="col-md-4">Upload Image</div>
            <div class="col-md-8">
                <div class="thumbnail" id="image-preview" style="min-height: 200px">
                    <label for="image-upload" id="image-label">Upload Image</label>
                    <input type="file" name="userfile" id="file" onchange="loadFilee(event)" required="">
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
            <div class="col-md-4">Deposit date</div>
            <div class="col-md-8">
                <?php echo date('d-m-Y h:i a') ?>
                <input type="hidden" name="date" value="<?php echo date('Y-m-d H:i:s') ?>" />
            </div>
            
            <div class="clearfix"></div><hr>
            <input type="hidden" name="cash_closure_date" value="<?php echo $closure_date_cash ?>" />
            <input type="hidden" name="refid" value="<?php echo $idcombine ?>" />
            <input type="hidden" name="id_cash_closure" value="<?php echo $id_cash_closure ?>" />
            <input type="hidden" name="idbranch" value="<?php echo $this->session->userdata('idbranch') ?>" />
            <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" formmethod="POST" id="deposit_btn" formaction="<?php echo base_url('Payment/save_cash_deposit') ?>" class="pull-right btn btn-info waves-effect" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Save</button>
            <div class="clearfix"></div>
        </div>
    <?php }else{ ?>
        <div class="col-md-6 col-md-offset-3 thumbnail" style="border: none"><center><img src="<?php echo base_url() ?>assets/images/premature-closure.jpg" style="width: 100%"/></center></div>
        <div class="clearfix"></div>
        <center><h4 class="blink" style="color: #ff0000; font-family: Kurale"><b>First Fill Yesterday's Cash Closure.</b></h4></center>
    <?php } ?>
    </div><div class="clearfix"></div>
</form>
<table class="table table-striped table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Date</th>
        <th>Closure Amount</th>
        <th>Deposit Amount</th>
        <th>Difference Amount</th>
        <th>Bank</th>
        <th>Image</th>
        <th>Remark</th>
        <!--<th>Status</th>-->
        <!--<th>Action</th>-->
    </thead>
    <tbody class="cash_closure_entries">
        <?php foreach($deposit_to_bank_data as $deposit){ ?>
        <tr>
            <td><?php echo $deposit->entry_time ?></td>
            <td><?php echo $deposit->total_closure_cash ?></td>
            <td><?php echo $deposit->deposit_cash ?></td>
            <td><?php echo $deposit->remaining_after_deposit ?></td>
            <td><?php echo $deposit->bank_name ?></td>
            <td><?php if( $deposit->deposite_image) { ?><a target="_blank" href="<?php echo base_url()?><?php echo $deposit->deposite_image?>"><img src="<?php echo base_url()?><?php echo $deposit->deposite_image?>" height="30px"></a><?php } ?></td>
            <td><?php echo $deposit->remark ?></td>
            <!--<td><?php // if($deposit->reconciliation_status == 0){ echo "Pending"; }else{ echo "Deposited"; } ?></td>-->
            <!--<td><a href="<?php echo base_url()?>Payment/delete_cash_deposite/<?php echo $deposit->id_cash_deposite_to_bank ?>" class="btn btn-warning btn-floating btndelete"><i class="fa fa-trash"></a></td>-->
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include __DIR__ . '../../footer.php'; ?>