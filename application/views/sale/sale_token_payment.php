<?php 
$en_total=0;
foreach ($sale_token_payment as $token_payment){
    $head = $token_payment->idpayment_head;
    $credit_balance=0;
//    Find Credit balance
        if($head==6){
            $idbranch = $_SESSION['idbranch'];
            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        //***** credit custudy check START *******//            
            $credit_data = $this->General_model->get_branch_credit_data($idbranch);            
            //branch credit limt and credit days
            $credit_limit = $invoice_no->credit_limit;
            //branch tilldate credit amount sum
            $overall_credit = $credit_data->credit_amount;
            $credit_balance=($credit_limit-$overall_credit);            
        }
    $headname = $token_payment->payment_head;
//    $token_payment = $this->General_model->get_payment_head_byid($head); 
//    $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); ?>
    
    <div id="modes_block<?php echo $head ?>" class="modes_block modes_blockc<?php echo $head ?> thumbnail" style="margin-bottom: 5px; padding: 5px;">
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            <span style="font-size: 15px; font-family: Kurale"><?php echo $headname ?></span>
            <br><span style="font-size: 15px; color: #002a80;padding-left: 25px"><?php echo $token_payment->payment_mode ?></span>
            <input type="hidden" class="payment_type" name="payment_type[]" value="<?php echo $token_payment->idpayment_mode ?>" />
        </div>
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            Amount
            <!-- Credit limit condition -->
             <?php if($head==6){ ?>
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" max="<?php echo $credit_balance ?>" placeholder="Amount" value="<?php echo $token_payment->amount ?>" min="1" required="" />                    
                <?php }else{ ?>
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" placeholder="Amount" value="<?php echo $token_payment->amount ?>" min="1" required="" />                      
                <?php } ?>
                
            <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="<?php echo $head ?>" />
            <input type="hidden" class="headname" name="headname[]" value="<?php echo $headname ?>" />
            <input type="hidden" class="credit_type" name="credit_type[]" value="<?php echo $token_payment->credit_type ?>" />
        </div>
        <?php if($token_payment->tranxid_type == NULL){ ?>
        <div class="col-md-2 col-sm-3 hidden">
            <?php echo $token_payment->tranxid_type ?>
            <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $token_payment->tranxid_type ?>" value="<?php echo NULL; ?>" />
        </div>
        <?php }else{ ?>
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            <?php echo $token_payment->tranxid_type ?>
            <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $token_payment->tranxid_type ?>" required="" pattern="[a-zA-Z0-9\-]+" value="<?php echo $token_payment->transaction_id ?>" />
        </div>
        <?php } ?>
        <?php foreach ($payment_attribute as $attribute){ ?>
        <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
            <?php echo $attribute->attribute_name ?>
            <?php $attr_name = $attribute->column_name ?>
            <input type="text" class="form-control input-sm headattr" id="<?php echo $attribute->column_name ?>" name="headattr[<?php echo $head ?>][<?php echo $attribute->id_payment_attribute ?>][]" placeholder="<?php echo $attribute->attribute_name ?>" required="" value="<?php echo $token_payment->$attr_name ?>" />
        </div>
        <?php } if($token_payment->multiple_rows){ ?>
        <div class="col-md-2 col-sm-3 pull-right" style="padding: 0;">
            <center>Add More<br>
                <a class="btn btn-primary btn-floating waves-effect add_more_payment" id="add_more_payment"><i class="fa fa-plus"></i></a>
            </center>
        </div>
        <?php } ?>
        <div class="clearfix"></div>
    </div><div class="clearfix"></div> 
    <script>
        $(document).ready(function(){
            $('#product_model_name').autocomplete({
                source: '<?php echo base_url('Sale/get_product_names_autocomplete') ?>',
            });
        });
    </script>
<?php $en_total += $token_payment->amount; } ?>