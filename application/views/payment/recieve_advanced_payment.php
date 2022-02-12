<?php include __DIR__ . '../../header.php'; ?>
<script src="<?php echo site_url('assets/js/autocomplete-jquery-ui.js') ?>"  type="text/javascript"></script>
<?= link_tag("assets/css/autocomplete-jquery-ui.css") ?>
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
        $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
        $('#cust_mobile').autocomplete({
            source: '<?php echo base_url('Sale/customer_contact_autocomplete') ?>',
            minLength: 5,
        });
    // Get Customer details from contact number
        $(document).on('keyup', 'input[id=cust_mobile]', function(e) {
            var keyCode = e.keyCode || e.which; 
            var cust_mobile = $("#cust_mobile").val();
            if(keyCode === 13 && cust_mobile.length != 10){
                swal("Incorrect mobile number!", "Check mobile number digits", "warning");
                $('#idcustomer').val('');
                $('#cust_fname').val('');
                $('#cust_lname').val('');
                $('#gst_no').val('');
                $('#cust_state').val('');
                $('#cust_idstate').val('');
                $('#cust_pincode').val('');
                $('#cust_oldcontact').val('');
                $('#address').val('');
            }else if (cust_mobile.length === 10 && keyCode === 13) {
                $.ajax({
                    url:"<?php echo base_url() ?>Sale/ajax_get_customer_bycontact",
                    method:"POST",
                    dataType: 'json',
                    data:{cust_mobile : cust_mobile},
                    success:function(data)
                    {
                        if(data.result == 'Success'){
                            $(data.contact_list).each(function (index, customer) {
                                var customer_details = customer.customer_fname+" "+customer.customer_lname;
                                customer_details += ", Mobile: "+cust_mobile;
                                swal("Customer Added!", "Customer: "+customer_details, "success");
                                $('.alert_msg').show();
                                $('.alert_msg').text('Customer Added: '+customer_details);
                                $('.alert_msg').fadeOut(20000);
                                $('#idcustomer').val(customer.id_customer);
                                $('#cust_fname').val(customer.customer_fname);
                                $('#cust_lname').val(customer.customer_lname);
                                $('#gst_no').val(customer.customer_gst);
                                $('#cust_state').val(customer.customer_state);
                                $('#cust_pincode').val(customer.customer_pincode);
                                $('#cust_idstate').val(customer.idstate);
                                $('#address').val(customer.customer_address);
                                $('#cust_oldcontact').val(cust_mobile);
                            });
                        }else{
                            swal("Customer not found!", "You have to create new customer", "warning");
                            $('#idcustomer').val('');
                            $('#cust_fname').val('');
                            $('#cust_lname').val('');
                            $('#gst_no').val('');
                            $('#cust_state').val('');
                            $('#cust_idstate').val('');
                            $('#cust_pincode').val('');
                            $('#cust_oldcontact').val('');
                            $('#address').val('');
                            $('#customer_contact').val(cust_mobile);
                            $('#customer_form').modal('show');
                        }
                    }
                });
            }
        });
        $(document).on("change", ".paymenthead", function (event) {
            var paymenthead = $(this).val();
            var headname = $(".paymenthead option:selected").text();
            if(paymenthead){
                $.ajax({
                    url: "<?php echo base_url() ?>Payment/ajax_get_payment_mode_data_byidhead",
                    method: "POST",
                    data:{paymenthead : paymenthead, headname: headname},
                    success: function (data)
                    {
                        $('.payment_modes').html(data);
                    }
                });
            }else{
                $('.payment_modes').html('');
            }
        });
        $(document).on("click", "#submit_btn", function (event) {
            if(+$('#cust_mobile').val() != +$('#cust_oldcontact').val()){
                confirm('First verify contact number by pressing enter key on customer contact');
                return false;
            }
            if(!$('#idmodelvariant').val()){
                confirm('Select product for booking..');
                return false;
            }
            if(!confirm('Do you want to submit')){return false;}
        });
        $(document).on("click", ".btn_refund", function(event) {            
            var ce = $(this);
            var idrow = $(this).val();
            var parentDiv=$(ce).closest('td').parent('tr');            
//            var idservice=$(parentDiv).find('.details').show();
            $(parentDiv).find('.refund_block').html('<div style="padding: 2px;width: 250px">\n\
                                    <input type="text" class="form-control input-sm refund_remark" id="refund_remark" name="refund_remark" placeholder="Enter refund reason"/>\n\
                                </div>\n\
                                <div class="pull-right" style="padding: 2px">\n\
                                    <button value="'+idrow+'" class="btn btn-danger btn-sm refund_submit" ><span class="fa fa-repeat"></span> Refund</button>\n\
                                </div><div class="clearfix"></div>\n\
                                <span class="blink" style="color:#cc0000;">Note: Refund will deduct cash from your daybook</span>');
            $(this).hide();
        });
        
        $(document).on("click", ".refund_submit", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idrow=$(this).val();
            var refund_remark=$(parentDiv).find('.refund_remark').val();
            var ref_amount=$(parentDiv).find('.ref_amount').val();
//            alert(ref_amount);
            if(refund_remark != ''){
                swal({
                    title: "Do you want to submit refund?",
                    text: "Refund will debited from your daybook cash: "+ref_amount,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#E84848',
                    confirmButtonText: 'Yes, Refund it!',
                    closeOnConfirm: false,
                },
                function(){
                    jQuery.ajax({
                        url: "<?php echo base_url('Payment/ajax_refund_booking_payment') ?>",
                        method:"POST",
                        dataType:"json",
                        data:{idrow:idrow,refund_remark:refund_remark,ref_amount:ref_amount},
                        success:function(data){
                            if(data.result == 'Success'){
//                                alert(data.daybook_cash);
                                swal('🙂 Refund submitted', 'Refund cash to customer done', 'success');
                                $(parentDiv).find('.claim_btn').remove();
                                $(parentDiv).find('.refund_block').html('<span>Refund to customer</span>');
                                $(parentDiv).addClass('recon1_2');
                            }else{
                                swal('Failed to submit Refund', 'You do not have sufficient cash in your daybook, Available cash: '+data.daybook_cash, 'warning');
                                $(parentDiv).find('.btn_refund').show();
                                $(parentDiv).find('.refund_block').html('');
                            }
                        }
                   });
                    swal("Refund done!", ref_amount+" Amount Refund to customer!", "success");
               });
           }else{
                swal('🙂 Refund reason is mandatory', 'Enter refund reason', 'warning');
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
    /*.recon0_0{ recon pending/pending*/
        /*background-color: #ffebee;*/
    /*}*/
/*    .recon01{ recon pending/sale
        background-color: #005bc0;
    }*/
/*    .recon02{ recon pending/refund
        background-color: #ffccff;
    }*/
    .recon1_0{ /*recon done/claim pending*/
        background-color: #fcfdef;
    }
    .recon1_1{ /*recon done/sale*/
        background-color: #eaedff;
    }
    .recon1_2{ /*recon done/claim refund*/
        background-color: #ffebee;
    }
</style>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-image-filter-tilt-shift fa-lg"></span> Advanced Payment Receipt</h3></center></div>
<div class="clearfix"></div><hr>
 <!--level 1 = admin, 2 = idbranch, 3 = user_has_branch-->
<?php if($var_closer){ ?>
<div class="col-md-6 col-sm-12 col-md-offset-3"><br>
    <div style="background-image: linear-gradient(to right top, #051937, #153c64, #216393, #288ec3, #2cbcf2);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px;">
        <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
            <center><i class="pe pe-7s-cash fa-lg"></i> Advanced Booking Form </center>
        </div>
    </div><div class="clearfix"></div><br>
    <form class="" style="font-size: 14px;">
        <div class="thumbnail">
<!--            <div class="col-md-3">Invoice No</div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="inv_no" placeholder="Invoice Number" required="" />
            </div>-->
            <div class="col-md-3">Date</div>
            <div class="col-md-3">
                <?php echo date('d-m-Y') ?>
                <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>" />
                <input type="hidden" name="idbranch" value="<?php echo $this->session->userdata('idbranch') ?>" />
                <input type="hidden" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
            </div>
            <div class="col-md-2">Promoter</div>
            <div class="col-md-4">
                <select class="form-control" name="idsalesperson" required="" id="idsalesperson">
                    <option value="">Select Sales Promoter</option>
                    <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                        <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                    <?php }} ?>
                </select>
            </div><div class="clearfix"></div><br>
            <div class="col-md-3">Customer Mobile</div>
            <div class="col-md-9">
                <input type="hidden" name="idcustomer" id="idcustomer" value=""/>
                <input type="number" class="form-control" name="cust_contact" id="cust_mobile" placeholder="Customer Contact" required="" />
                <input type="hidden" class="form-control" id="cust_oldcontact" placeholder="Customer Contact" required="" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-3">Customer Name</div>
            <div class="col-md-9">
                <div class="input-group">
                    <div class="input-group-btn">
                        <input type="text" class="form-control" name="cust_fname" id="cust_fname" placeholder="Customer First Name" required="" onfocus="blur()" />
                    </div>
                    <div class="input-group-btn">
                        <input type="text" class="form-control" name="cust_lname" id="cust_lname" placeholder="Customer Last Name" required="" onfocus="blur()" />
                    </div>
                </div>
            </div><div class="clearfix"></div><br>
            <div class="col-md-3">Address</div>
            <div class="col-md-9">
                <input type="text" class="form-control" name="cust_address" id="address" placeholder="Customer Address" required="" onfocus="blur()" />
            </div><div class="clearfix"></div><br>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <h5 style="color:#1b6caa;font-family: Kurale;">Select Product</h5>
            </div>
            <div class="col-md-9">
                <select class="chosen-select form-control" name="idmodelvariant" required="" id="idmodelvariant" required="">
                    <option value="">Select Model</option>
                    <?php foreach ($model_variant as $variant) { ?>
                        <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name . ' ' . $variant->full_name; ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
            <div class="col-md-3 col-sm-2 col-xs-6">
                <h5 style="color:#1b6caa;font-family: Kurale;">Mode of payment</h5>
            </div>
            <div class="col-md-4">
                <select class="form-control paymenthead" name="paymenthead" required="">
                    <option value="">Select Payment Type</option>
                    <?php foreach ($payment_head as $head) { ?>
                    <option value="<?php echo $head->id_paymenthead ?>" name="paymenthead" selected_head="<?php echo $head->payment_head ?>"><?php echo $head->payment_head ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">Amount</div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="amount" placeholder="Amount" autocomplete="off" required="" min="1" />
            </div><div class="clearfix"></div><br>
            <div class="payment_modes col-md-9 col-md-offset-3" style="font-size: 12px"></div>
            <div class="col-md-3">Remark</div>
            <div class="col-md-9">
                <textarea class="form-control" name="remark" placeholder="Enter Remark"></textarea>
            </div><div class="clearfix"></div><br>
            <button type="submit" id="submit_btn" formmethod="POST" formaction="<?php echo base_url('Payment/save_advanced_payment_receive') ?>" class="pull-right btn btn-info waves-effect" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Save</button>
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<?php }else{ 
    echo '<center><h3>You did not submitted yesterdays cash closure</h3>'
        . '<a href="'.base_url().'Payment/cash_closure"><h4 style="font-family: Kurale; color: #1e61c7"><i class="mdi mdi-chevron-double-right"></i>Click here to open cash closure form</h4></a>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
        .'</center>';
} ?><div class="clearfix"></div><br>
<table class="table table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Sr</th>
        <th>Date</th>
        <th>Product</th>
        <th>Sales Promoter</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Payment head</th>
        <th>Payment type</th>
        <th>Amount</th>
        <th>Entry by</th>
        <th>Entry Time</th>
        <th>Days Diff</th>
        <th>Remark</th>
        <th>Reconciliation</th>
        <th>Inv No</th>
        <th>Inv Date</th>
        <th>Sale</th>
        <th>Refund</th>
        <th>Print</th>
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; $total_amt=0; foreach($cash_payment_data as $cash_payment){ ?>
        <tr class="recon<?php echo $cash_payment->payment_receive.'_'.$cash_payment->claim ?>">
            <td><?php echo $i; ?></td>
            <td><?php echo date('d-m-Y', strtotime($cash_payment->date)) ?></td>
            <td><?php echo $cash_payment->full_name ?></td>
            <td><?php echo $cash_payment->sales_person ?></td>
            <td><?php echo $cash_payment->cust_fname.' '.$cash_payment->cust_lname ?></td>
            <td><?php echo $cash_payment->cust_contact ?></td>
            <td><?php echo $cash_payment->payment_head; ?></td>
            <td><?php echo $cash_payment->payment_mode; ?></td>
            <td>
                <?php echo $cash_payment->amount ?>
                <input type="hidden" class="ref_amount" value="<?php echo $cash_payment->amount ?>" />
            </td>
            <td><?php echo $cash_payment->user_name ?></td>
            <td><?php echo $cash_payment->entry_time ?></td>
            <td><?php $now = time(); // or your date as well
                $your_date = strtotime($cash_payment->entry_time);
                $datediff = $now - $your_date;
                echo round($datediff / (60 * 60 * 24)); ?></td>
            <td><?php echo $cash_payment->remark ?></td>
            <td><?php if($cash_payment->payment_receive){ echo 'Done'; }else{ echo 'Pending'; }?></td>
            <?php if($cash_payment->claim == 1){ ?>
            <td><a href="<?php echo base_url('Sale/sale_details/'.$cash_payment->idsale) ?>" class="waves-effect" style="color: #005bc0"><?php echo $cash_payment->inv_no ?></a></td>
            <td><?php echo date('d-m-Y', strtotime($cash_payment->inv_date)) ?></td>
            <td>Sale</td>
            <td>-</td>
            <?php }elseif($cash_payment->claim == 0 && $cash_payment->payment_receive){ ?>
            <td>-</td>
            <td>-</td>
            <td><a href="<?php echo base_url()?>Sale/index?idclaim=<?php echo $cash_payment->id_advance_payment_receive ?>" class="btn btn-sm waves-effect white-text claim_btn" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Invoice <i class="fa fa-send-o"></i></a></td>
            <td>
                <button value="<?php echo $cash_payment->id_advance_payment_receive ?>" class="btn btn-danger btn-sm waves-effect btn_refund"><i class="fa fa-reply"></i> Refund</button>
                <div class="refund_block"></div>
            </td>
            <?php // }elseif($cash_payment->claim == 2){ ?>
<!--            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>Refund<br>Remark: <?php echo $cash_payment->refund_remark ?></td>-->
            <?php }else{ ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <?php } ?>
            <td><a href="<?php echo base_url()?>Payment/advance_booking_received_receipt/<?php echo $cash_payment->id_advance_payment_receive ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
        <?php $i++; } ?>
    </tbody>
</table>
<?php require_once 'customer_master.php'; ?>
<?php include __DIR__ . '../../footer.php'; ?>