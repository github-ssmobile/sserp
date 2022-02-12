<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('#idcorrectiontype').change(function (){
            var idcorrectiontype = $('#idcorrectiontype').val();
            var idsystem = $('#idsystem').val();
            var idhelpline = $('#idhelpline').val();
            if(idcorrectiontype != '' && idsystem != '' && idhelpline != ''){
                if(idcorrectiontype == '1'){
                    $('.inv_no').show();
                    $('.payment_mode').show();
                    $('.transaction_id').show();
                    
                    $("#inv_no").prop('required',true);
                    $("#idpaymentmode").prop('required',true);
                    $("#transaction_id").prop('required',true);
                    
                    $('.gst_no').hide();
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    
                    $('#gst_no').prop('required',false);
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                }
                if(idcorrectiontype == '2'){
                    $('.inv_no').show();
                    $('.gst_no').show();
                    
                   $("#inv_no").prop('required',true);
                   $("#gst_no").prop('required',true);
                    
                    $('.payment_mode').hide();
                    $('.transaction_id').hide();
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                   
                    $('#idpaymentmode').prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                    
                }
                if(idcorrectiontype == '3'){
                    $('.inv_no').show();
                    $('.contact').show();
                    $('.idcustomer').show();
                    
                    $('.gst_no').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    $('.payment_mode').hide();
                    $('.transaction_id').hide();
                    
                    $("#inv_no").prop('required',true);
                    $('#contact').prop('required',true);
                    $('#idcustomer').prop('required',true);
                    
                    $('#idpaymentmode').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                }
                if(idcorrectiontype == '4'){
                    $('.inv_no').show();
                    $('.contact').show();
                    $('.idcustomer').show();
                    $('.cust_info').show();
                    
                    $('.gst_no').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    $('.payment_mode').hide();
                    $('.transaction_id').hide();
                   
                    $("#inv_no").prop('required',true);
                    $('#contact').prop('required',true);
                    $('#idcustomer').prop('required',true);
                    $('#cust_info').prop('required',true);
                    
                    $('#idpaymentmode').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                    
                }
                if(idcorrectiontype == '5'){
                    $('.inv_no').show();
                    $('.payment_mode').show();
                    $('.idnewpaymentmode').show();
                    
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.gst_no').hide();
                    $('.cust_info').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    $('.transaction_id').hide();
                    
                    $("#inv_no").prop('required',true);
                    $('#idpaymentmode').prop('required',true);
                    $('#idnewpaymentmode').prop('required',true);
                    
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                }
                if(idcorrectiontype == '6'){
                    $('.inv_no').show();
                    $('.payment_mode').show();
                    $('.oldamount').show();
                    $('.newamount').show();
                    
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.gst_no').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    $('.transaction_id').hide();
                    
                    $("#inv_no").prop('required',true);
                    $('#idpaymentmode').prop('required',true);
                    $('#oldamount').prop('required',true);
                    $('#newamount').prop('required',true);
                    
                    $('#idnewpaymentmode').prop('required',false);
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                }
                if(idcorrectiontype == '7'){
                    $('.inv_no').show();
                    $('.product_name').show();
                    $('.newimei').show();
                    
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.gst_no').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.oldproduct').hide();
                    $('.newproduct').hide();
                    $('.payment_mode').hide();
                    $('.transaction_id').hide();
                    
                    $("#inv_no").prop('required',true);
                    $('#product_name').prop('required',true);
                    $('#newimei').prop('required',true);
                    
                    $('#idpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                    $('#oldproduct').prop('required',false);
                    $('#newproduct').prop('required',false);
                }
                if(idcorrectiontype == '8'){
                    $('.inv_no').show();
                    $('.oldproduct').show();
                    $('.newproduct').show();
                    
                    $('.contact').hide();
                    $('.idcustomer').hide();
                    $('.gst_no').hide();
                    $('.cust_info').hide();
                    $('.idnewpaymentmode').hide();
                    $('.oldamount').hide();
                    $('.newamount').hide();
                    $('.product_name').hide();
                    $('.newimei').hide();
                    $('.transaction_id').hide();
                    
                    $("#inv_no").prop('required',true);
                    $('#oldproduct').prop('required',true);
                    $('#newproduct').prop('required',true);
                    
                    $('#product_name').prop('required',false);
                    $('#newimei').prop('required',false);
                    $('#idpaymentmode').prop('required',false);
                    $('#oldamount').prop('required',false);
                    $('#newamount').prop('required',false);
                    $('#idnewpaymentmode').prop('required',false);
                    $('#contact').prop('required',false);
                    $('#idcustomer').prop('required',false);
                    $('#cust_info').prop('required',false);
                    $("#gst_no").prop('required',false);
                    $('#transaction_id').prop('required',false);
                }
            }else{
                alert("Select Filter Data");
                return false;
            }
        });
        
        $('#contact').focusout(function (){
        
//        $(document).on('keydown', 'input[id=contact]', function(e) {
//            var keyCode = e.keyCode || e.which ; 
//            if (keyCode === 13 ) {
               var contact = $('#contact').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Correction/ajax_get_customer_bycontact'); ?>",
                    data: {contact: contact},
                    success: function(data){
                        
                        $('#customer_add').html(data);
                        $('#idcustomertemp').remove();
                    }
                }); 
//            }
        });
       
   });
   
    $(document).ready(function() {
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              return false;
            }
        });
    });
   
</script>

<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-bank fa-lg"></span> Correction Panel</h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>
<div class="clearfix"></div><br>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
       <?php  echo form_open_multipart('Correction/save_correction_request', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail  col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Correction </h4></center><hr>
            <div class="col-md-4 thumbnail" style="padding: 10px;margin-right: 20px;">
                <img src="<?php echo base_url()?>assets/images/alphatestersanimation2.gif" style="height: auto;width: 400px" />
            </div>
            <div class="col-md-7" style="padding: 10px;">
                <div class="col-md-4"><b>Date</b></div>
                <div class="col-md-8"><input type="text" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" readonly=""></div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Branch</b></div>
                <div class="col-md-8">
                    <?php if($this->session->userdata('level') != 2 ){ ?>
                        <select class="form-control" name="idbranch" id="idbranch">
                            <option value="">Select Branch</option>
                            <?php foreach ($branch_data as $bdata){ ?>
                            <option value="<?php echo $bdata->id_branch ?>"><?php echo $bdata->branch_name; ?></option>
                            <?php } ?>
                        </select>
                    <?php }else{ ?>
                    <select class="form-control" name="idbranch" id="idbranch">
                            <?php foreach ($branch_data as $bdata){ 
                                if($bdata->id_branch == $_SESSION['idbranch']){?>
                                    <option value="<?php echo $bdata->id_branch ?>"><?php echo $bdata->branch_name; ?></option>
                            <?php }  } ?>
                        </select>
                    <?php } ?>
                    </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>System</b></div>
                <div class="col-md-8">
                    <select class="form-control" name="idsystem" id="idsystem" required="">
                        <option value="">Select Correction System</option>
                        <?php foreach ($correction_system as $sys){ ?>
                        <option value="<?php echo $sys->id_correction_system?>"><?php echo $sys->system_name;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Helpline Type</b></div>
                <div class="col-md-8">
                    <select class="form-control" name="idhelpline" id="idhelpline" required="">
                        <option value="">Select Helpline Type</option>
                        <?php foreach ($helpline_type as $htype){ ?>
                        <option value="<?php echo $htype->id_helpline_type?>"><?php echo $htype->helpline_type;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-4"><b>Correction Type</b></div>
                <div class="col-md-8">
                    <select class="form-control" name="idcorrectiontype" id="idcorrectiontype" required="">
                        <option value="">Select Correction Type</option>
                        <?php foreach ($correction_type as $ctype){ ?>
                            <option value="<?php echo $ctype->id_correction_type?>"><?php echo $ctype->correction_type;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div><br>
                <div class="inv_no" style="display: none">
                    <div class="col-md-4"><b>Invoice Number</b></div>
                    <div class="col-md-8"><input type="text" name="inv_no" id="inv_no" class="form-control" placeholder="Enter Full Invoice Number" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="payment_mode" style="display: none">
                    <div class="col-md-4"><b>Payment Mode</b></div>
                    <div class="col-md-8">
                        <select class="form-control" name="idpaymentmode" id="idpaymentmode" required="">
                           <option value="0">Select Payment Mode</option>
                           <?php foreach ($payment_mode as $pmode){ ?>
                               <option value="<?php echo $pmode->id_paymentmode?>"><?php echo $pmode->payment_mode;?></option>
                           <?php } ?>
                       </select>
                    </div>
                    <div class="clearfix"></div><br>
                 </div>
                <div class="transaction_id" style="display: none">
                   <div class="col-md-4"><b>Transaction ID</b></div>
                   <div class="col-md-8"><input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter Transaction ID" required=""></div>
                   <div class="clearfix"></div><br>
                </div>
                <div class="gst_no" style="display: none">
                    <div class="col-md-4"><b>GST NO</b></div>
                    <div class="col-md-8"><input type="text" name="gst_no" id="gst_no" class="form-control" placeholder="Enter GST Number" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="contact" style="display: none">
                    <div class="col-md-4"><b>Contact No</b></div>
                    <div class="col-md-8"><input type="text" name="contact" id="contact" class="form-control" placeholder="Enter Contact Number" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="idcustomer" style="display: none">
                    <div class="col-md-4"><b>Customer</b></div>
                    <div class="col-md-8">
                        <div id="customer_add"></div>
                       <select class="form-control" name="idcustomer" id="idcustomertemp" >
                           <option value="">Select Customer</option>
                       </select>
                    </div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="cust_info" style="display: none">
                    <div class="col-md-4"><b>Customer Info </b></div>
                    <div class="col-md-8">
                        <textarea name="cust_info" id="cust_info" class="form-control" required=""></textarea>
                    </div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="idnewpaymentmode" style="display: none">
                    <div class="col-md-4"><b>New Payment Mode</b></div>
                    <div class="col-md-8">
                        <select class="form-control" name="idnewpaymentmode" id="idnewpaymentmode" required="">
                           <option value="0">Select Payment Mode</option>
                           <?php foreach ($payment_mode as $pmode){ ?>
                               <option value="<?php echo $pmode->id_paymentmode?>"><?php echo $pmode->payment_mode;?></option>
                           <?php } ?>
                       </select>
                    </div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="oldamount" style="display: none">
                    <div class="col-md-4"><b>Old Amount</b></div>
                    <div class="col-md-8"><input type="text" name="oldamount" id="oldamount" class="form-control" placeholder="Enter Old Amount" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="newamount" style="display: none">
                    <div class="col-md-4"><b>New Amount</b></div>
                    <div class="col-md-8"><input type="text" name="newamount" id="newamount" class="form-control" placeholder="Enter New Amount" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="product_name" style="display: none">
                    <div class="col-md-4"><b>Product Name</b></div>
                    <div class="col-md-8"><input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter Product Name" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="newimei" style="display: none">
                    <div class="col-md-4"><b>New IMEI</b></div>
                    <div class="col-md-8"><input type="text" name="newimei" id="newimei" class="form-control" placeholder="Enter New IMEI" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="oldproduct" style="display: none">
                    <div class="col-md-4"><b>Old Product</b></div>
                    <div class="col-md-8"><input type="text" name="oldproduct" id="oldproduct" class="form-control" placeholder="Enter Old Product Name" required=""></div>
                    <div class="clearfix"></div><br>
                </div>
                <div class="newproduct" style="display: none">
                    <div class="col-md-4"><b>New Product</b></div>
                    <div class="col-md-8"><input type="text" name="newproduct" id="newproduct" class="form-control" placeholder="Enter New Product Name" required=""></div>
                   <div class="clearfix"></div><br>
                </div>
                <div class="col-md-4"><b>Remark</b></div>
                <div class="col-md-8"><input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" ></div>
                <div class="clearfix"></div><br>
            </div>
            <div class="clearfix"></div><hr>            
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" class="btn btn-primary pull-right submit_btn" formmethod="POST" formaction="<?php echo base_url()?>Correction/save_correction_request">Submit</button>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div><hr>
        <?php  echo form_close(); ?>
    </div>
    <div style="overflow-x: auto;height: 700px">
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
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
        <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_correction');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div> 
    <div class="clearfix"></div><br>
        <table class="table table-bordered table-condensed" id="branch_correction">
            <thead style="background-color: #a9c5fc">
                <th>Sr.</th>
                <th>Date</th>
                <th>Branch</th>
                <th>System</th>
                <th>Helpline</th>
                <th>Correction Type</th>
                <th>Invoice No</th>
                <th>Transaction Id</th>
                <th>GST No</th>
                <th>Customer</th>
                <th>Customer Contact</th>
                <th>Customer Updates</th>
                <th>Payment Mode</th>
                <th>New Payment Mode</th>
                <th>Amount</th>
                <th>New Amount</th>
                <th>Product Name</th>
                <th>New IMEI</th>
                <th>Old Product</th>
                <th>New Product</th>
                <th>Remark</th>
                <th>Status</th>
                <th>Created By</th>
            </thead>
            <tbody class="data_1">
                <?php $sr=1; foreach($correction_request_data as $cdata){ ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $cdata->date ?></td>
                    <td><?php echo $cdata->branch_name ?></td>
                    <td><?php echo $cdata->system_name ?></td>
                    <td><?php echo $cdata->helpline_type ?></td>
                    <td><?php echo $cdata->correction_type ?></td>
                    <td><?php echo $cdata->invoice_no ?></td>
                    <td><?php echo $cdata->transaction_id ?></td>
                    <td><?php echo $cdata->gst_no ?></td>
                    <td><?php echo $cdata->customer_fname.' '.$cdata->customer_lname ?></td>
                    <td><?php echo $cdata->cust_contact ?></td>
                    <td><?php echo $cdata->cust_info ?></td>
                    <td><?php if($cdata->idold_paymentmode != 0){ echo $cdata->oldpaymentmode; }?></td>
                    <td><?php if($cdata->idnew_paymentmode != 0){ echo $cdata->newpaymentmode; }?></td>
                    <td><?php echo $cdata->old_amount ?></td>
                    <td><?php echo $cdata->new_amount ?></td>
                    <td><?php echo $cdata->product_name ?></td>
                    <td><?php echo $cdata->new_imei ?></td>
                    <td><?php echo $cdata->oldproduct ?></td>
                    <td><?php echo $cdata->newproduct ?></td>
                    <td><?php echo $cdata->remark ?></td>
                    <td><?php if($cdata->status == 0){ echo 'Pending';}elseif($cdata->status == 1){ echo 'On Hold';}elseif ($cdata->status == 2){ echo 'Closed'; } ?></td>
                    <td><?php echo $cdata->user_name ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>