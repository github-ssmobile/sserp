<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnupdate').click(function (){
            if(confirm("Do You Want To Update Correction Request? ")){
                var parenttr = $(this).closest('td').parent('tr');
                var idcorrectionreq = $(this).closest('td').find('.idcorrectionreq').val();
                var idstatus = $(this).closest('td').parent('tr').find('.status').val();
                var update_remark = $(this).closest('td').parent('tr').find('.update_remark').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Correction/update_correction_request'); ?>",
                    data: {idcorrectionreq: idcorrectionreq, idstatus: idstatus, update_remark: update_remark},
                    success: function(data){
                        if(data == '1' || data == 1){
                            if(idstatus == '2' || idstatus == 2){
                                parenttr.remove();
                            }
                        }
                        else if (data == '0' || data == 0) {
                            alert("Correction Request Not Updated");
                            return false;
                        }
                    }
                    
                }); 
                
            }else{
                return false;
            }
        });
       
   });
   
</script>
<style>
      .fixedelement{
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #c5f4dd;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        z-index: 9;
    }
  .fixleft{
    position: sticky;
    left:0px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    

  }
  .fixleft1{
    position: sticky;
    left:80px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft2{
    position: sticky;
    left:140px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft3{
    position: sticky;
    left:157px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
  .fixleft4{
    position: sticky;
    left:235px;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
     background-color: #fbf7c0;

  }
</style>

<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-checkbox-marked-outline fa-lg"></span> Pending Corrections </h3></center></div>
<div class="col-md-1">
    <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
</div><div class="clearfix"></div><hr>
<div class="clearfix"></div><br>

<div class="" style="padding: 0; margin: 0;">
    <div style="padding: 10px; margin: 0">
        <div style="overflow-x: auto;height: 800px">
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
                <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('branch_pending_correction');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
            </div> 
            <div class="clearfix"></div><br>
            <table class="table table-bordered table-condensed" id="branch_pending_correction">
                <thead style="background-color: #a9c5fc" class="fixedelement">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th class="fixleft" style="background-color: #a9c5fc">Branch</th>
                    <th class="fixleft1" style="background-color: #a9c5fc">System</th>
                    <th>Helpline</th>
                    <th class="fixleft3" style="background-color: #a9c5fc">Correction Type</th>
                    <th class="fixleft4" style="background-color: #a9c5fc">Invoice No</th>
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
                    <th>Created By</th>
                    <th>Status</th>
                    <th>Remark</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach($correction_request_data as $cdata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $cdata->date ?></td>
                        <td class="fixleft" style="background-color: #ebf0fc;"><?php echo $cdata->branch_name ?></td>
                        <td class="fixleft1" style="background-color: #ebf0fc;"><?php echo $cdata->system_name ?></td>
                        <td><?php echo $cdata->helpline_type ?></td>
                        <td class="fixleft3" style="background-color: #ebf0fc;"><?php echo $cdata->correction_type ?></td>
                        <td class="fixleft4" style="background-color: #ebf0fc;"><?php echo $cdata->invoice_no ?></td>
                        <td><?php echo $cdata->transaction_id ?></td>
                        <td><?php echo $cdata->gst_no ?></td>
                        <td><?php echo $cdata->customer_fname.' '.$cdata->customer_lname ?></td>
                        <td><?php echo $cdata->cust_contact ?></td>
                        <td><?php echo $cdata->cust_info ?></td>
                        <td><?php if($cdata->idold_paymentmode != 0){ echo $cdata->oldpaymentmode; } ?></td>
                        <td><?php if($cdata->idnew_paymentmode != 0){ echo $cdata->newpaymentmode; } ?></td>
                        <td><?php echo $cdata->old_amount ?></td>
                        <td><?php echo $cdata->new_amount ?></td>
                        <td><?php echo $cdata->product_name ?></td>
                        <td><?php echo $cdata->new_imei ?></td>
                        <td><?php echo $cdata->oldproduct ?></td>
                        <td><?php echo $cdata->newproduct ?></td>
                        <td><?php echo $cdata->remark ?></td>
                        <td><?php echo $cdata->user_name ?></td>
                        <td><select class="form-control status" name="status" style="width: 120px;" >
                                <option value="<?php echo $cdata->status?>"><?php if($cdata->status == 0){ echo 'Pending';}elseif($cdata->status == 1){ echo 'On Hold';}elseif ($cdata->status == 2){ echo 'Closed'; } ?></option>
                                <?php if($cdata->status != 0){ ?>
                                    <option value="0">Pending</option>
                                <?php } if($cdata->status != 1){ ?>
                                    <option value="1">On Hold</option>
                                <?php }  if($cdata->status != 2){ ?>
                                    <option value="2">Closed</option>
                                <?php } ?>
                            </select> 
                        </td>
                        <td><input type="text" class="form-control update_remark" name="update_remark" value="<?php echo $cdata->updated_remark?>" style="width: 150px"></td>
                       <td>
                           <input type="hidden" class="idcorrectionreq" name="idcorrectionreq" value="<?php echo $cdata->id_correction_request?>">
                            <a class="btn btn-primary btn-sm btnupdate" >Submit</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>