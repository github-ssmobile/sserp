<?php include __DIR__ . '../../header.php'; ?>
<script src="<?php echo site_url('assets/js/autocomplete-jquery-ui.js') ?>"  type="text/javascript"></script>
<?= link_tag("assets/css/autocomplete-jquery-ui.css") ?>
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
//                $('#gst_no').val('');
//                $('#cust_state').val('');
//                $('#cust_idstate').val('');
//                $('#cust_pincode').val('');
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
//                                $('#gst_no').val(customer.customer_gst);
//                                $('#cust_state').val(customer.customer_state);
//                                $('#cust_pincode').val(customer.customer_pincode);
//                                $('#cust_idstate').val(customer.idstate);
                                $('#address').val(customer.customer_address);
                                $('#cust_oldcontact').val(cust_mobile);
                            });
                        }else{
                            swal("Customer not found!", "You have to create new customer", "warning");
                            $('#idcustomer').val('');
                            $('#cust_fname').val('');
                            $('#cust_lname').val('');
//                            $('#gst_no').val('');
//                            $('#cust_state').val('');
//                            $('#cust_idstate').val('');
//                            $('#cust_pincode').val('');
                            $('#cust_oldcontact').val('');
                            $('#address').val('');
                            $('#customer_contact').val(cust_mobile);
                            $('#customer_form').modal('show');
                        }
                    }
                });
            }
        });
        $(document).on("click", "#submit_btn", function (event) {
            if(+$('#cust_mobile').val() != +$('#cust_oldcontact').val()){
                swal('Verify customer contact!','First verify contact by pressing enter key on customer contact');
                return false;
            }
            if(!confirm('Do you want to submit')){return false;}
        });
        
        $('.btnreport').click(function (){
            var fromdate = $('#from_rdate').val();
            var todate = $('#to_rdate').val();
            var idbranch = $('#branchid').val();
            var allbranches = $('#allbranches').val();
            $.ajax({
                url:"<?php echo base_url() ?>Payment/ajax_get_cash_payment_receive_data",
                method:"POST",
                data:{fromdate: fromdate, todate: todate, idbranch: idbranch, allbranches: allbranches},
                success:function(data)
                {
                    $('#cashpayment_data').html(data);
                }
            });
        });
    }); 
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-cash-multiple fa-lg"></span> Old Cash Receipt</h3></center></div>
<div class="clearfix"></div><hr>
 <!--level 1 = admin, 2 = idbranch, 3 = user_has_branch-->
<?php if($this->session->userdata('level') == 2){ 
if($var_closer){ ?>
<div class="col-md-6 col-sm-12 col-md-offset-3"><br>
    <div style="background-image: linear-gradient(to right top, #051937, #153c64, #216393, #288ec3, #2cbcf2);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px;">
        <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
            <center><i class="pe pe-7s-cash fa-lg"></i> Cash Receive Form </center>
        </div>
    </div><div class="clearfix"></div><br>
    <form class="" style="font-size: 14px;">
        <div class="thumbnail">
            <div class="col-md-3">Date</div>
            <div class="col-md-9">
                <?php echo date('d-m-Y') ?>
                <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>" />
                <input type="hidden" name="idbranch" value="<?php echo $this->session->userdata('idbranch') ?>" />
                <input type="hidden" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                <input type="hidden" name="receive_type" value="0"/>
            </div><div class="clearfix"></div><br>
            <div class="col-md-3">Cash</div>
            <div class="col-md-9">
                <input type="number" class="form-control" name="amount" placeholder="Cash Amount" autocomplete="off" required="" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-3">Invoice No</div>
            <div class="col-md-9">
                <input type="text" class="form-control" name="inv_no" placeholder="Invoice Number" required="" />
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
            <div class="col-md-3">Remark</div>
            <div class="col-md-9">
                <textarea class="form-control" rows="3" name="remark" placeholder="Enter Remark, Reason, payment resource" required=""></textarea>
            </div><div class="clearfix"></div><br>
            <button type="submit" id="submit_btn" formmethod="POST" formaction="<?php echo base_url('Payment/save_cash_payment_receive') ?>" class="pull-right btn btn-info waves-effect" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Save</button>
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
<table class="table table-striped table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Sr</th>
        <th>Receipt Id</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Invoice No</th>
        <th>Cash Amount</th>
        <th>Entry by</th>
        <th>Entry Time</th>
        <th>Remark</th>
        <th>Print</th>
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; $total_amt=0; foreach($cash_payment_data as $cash_payment){ ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td>CashRec/<?php echo $cash_payment->id_cash_payment_receive ?></td>
            <td><?php echo $cash_payment->date ?></td>
            <td><?php echo $cash_payment->cust_fname.' '.$cash_payment->cust_lname ?></td>
            <td><?php echo $cash_payment->cust_contact ?></td>
            <td><?php echo $cash_payment->inv_no ?></td>
            <td><?php echo $cash_payment->amount ?></td>
            <td><?php echo $cash_payment->user_name ?></td>
            <td><?php echo $cash_payment->entry_time ?></td>
            <td><?php echo $cash_payment->remark ?></td>
            <td><a href="<?php echo base_url()?>Payment/cash_payment_received_receipt/<?php echo $cash_payment->id_cash_payment_receive ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
        <?php $i++; } ?>
    </tbody>
</table>
<?php } else{ ?>

<div class="col-md-1">Date</div>
<div class="col-md-2">
    <input type="date" class="form-control" name="from_rdate" id="from_rdate">
</div>
<div class="col-md-2">
    <input type="date" class="form-control" name="to_rdate" id="to_rdate">
</div>
<div class="col-md-1">Branch</div>
<div class="col-md-2    ">
    <select name="branchid" id="branchid" class="form-control chosen-select">
        <option value="0">All Branch</option>
        <?php foreach ($branch_data as $bdata){ ?>
            <option value="<?php echo $bdata->id_branch?>"><?php echo $bdata->branch_name;?></option>
        <?php $branches[] = $bdata->id_branch; } ?>
    </select>
    <input type="hidden" name="allbranches" id="allbranches" value="<?php echo implode($branches,',') ?>">
</div>
<div class="col-md-1"><button class="btn btn-primary btnreport">Filter</button></div>
<div class="clearfix"></div><hr>
<div class="col-md-4 col-sm-4 col-xs-4 ">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm" >
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
    </div>
</div>
<div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
    <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('cash_payment_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
</div> 
<div class="clearfix"></div><br>
<div id="cashpayment_data"></div>
<?php } ?>

<?php if($this->session->userdata('level') == 2){  
require_once 'customer_master.php'; }?>
<?php include __DIR__ . '../../footer.php'; ?>