<?php include __DIR__.'../../header.php'; ?>
<center><h3><span class="mdi mdi-currency-usd fa-lg"></span> Payment Mode</h3></center>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px; background-color: #fff">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">
            <div class="col-md-5">
                <div class="thumbnail" style="border: none"><br>
                    <img src="<?php echo base_url() ?>assets/images/mass-payment.png" style="width: 100%" />
                </div>
            </div>
            <div class="col-md-6"><br>
                <div class="thumbnail">
                    <center><h4><span class="mdi mdi-flattr"></span> Add Payment Mode</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Payment Head</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="idpaymenthead">
                            <?php foreach ($payment_head_data as $payment_head) { if($payment_head->head_show == 1){ ?>
                                <option value="<?php echo $payment_head->id_paymenthead ?>"><?php echo $payment_head->payment_head ?></option>
                            <?php }} ?>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Payment Mode</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="payment_mode" placeholder="Enter Payment Mode" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Has Devices</label>
                    <div class="col-md-7">
                        <div class="material-switch" style="margin-top: -5px">
                            <input id="hasdevices" name="hasdevices" type="checkbox"/> 
                            <label for="hasdevices" class="label-primary"></label> 
                        </div>
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_payment_mode') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div><hr>
        </form>
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
        <div class="col-md-4">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('payment_mode_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
        <div class="clearfix"></div>
        <table id="payment_mode_data" class="table table-condensed table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Payment Head</th>
                <th>Payment Mode</th>
                <th>Attributes</th>
                <th>Status</th>
                <th><center>Add device</center></th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($payment_mode_data as $payment_mode){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $payment_mode->payment_head; ?></td>
                    <td><?php echo $payment_mode->payment_mode; ?></td>
                    <td><?php foreach ($payment_head_has_attributes as $has_attributes){ if($has_attributes->idpayment_head == $payment_mode->id_paymenthead){ echo '<div class="thumbnail" style="margin-bottom:2px; padding:5px 10px">'.$has_attributes->attribute_name.'</div>'; }} ?></td>
                    <td><?php if($payment_mode->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <center>
                        <?php if($payment_mode->has_devices == 1){ ?>
                            <a class="thumbnail btn-sm btn-link waves-effect" href="<?php echo base_url('Master/payment_mode_has_devices/'.$payment_mode->id_paymentmode.'/'.$payment_mode->payment_mode.'/'.$payment_mode->id_paymenthead) ?>" style="margin: 0" >
                                <span class="mdi mdi-credit-card-multiple text-primary fa-lg"></span>
                            </a>
                        </center>
                        <?php } ?>
                    </td>
                    <td>
                        <a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form>
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-cash" style="font-size: 28px"></span> Edit Payment Mode</h4></center><hr>
                                        <label class="col-md-3  col-md-offset-1">Payment Head</label>
                                        <div class="col-md-7">
                                            <select class="select form-control" name="idpaymenthead">
                                                <option value="<?php echo $payment_mode->id_paymenthead ?>"><?php echo $payment_mode->payment_head ?></option>
                                                <?php foreach ($payment_head_data as $payment_head) { if($payment_head->head_show == 1){ ?>
                                                    <option value="<?php echo $payment_head->id_paymenthead ?>"><?php echo $payment_head->payment_head ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Payment Mode</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $payment_mode->payment_mode; ?>" name="payment_mode" placeholder="Enter Payment Mode" required=""/>
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Has Devices</label>
                                        <div class="col-md-7">
                                            <div class="material-switch">  
                                                <?php $checked="";
                                                    if($payment_mode->has_devices==1){
                                                        $checked="checked";
                                                    } ?> 
                                                <input id="<?php echo $i; ?>hasdevices" name="hasdevices" type="checkbox" <?php echo $checked; ?> /> 
                                                <label for="<?php echo $i; ?>hasdevices" class="label-primary"></label> 
                                            </div>
                                        </div><div class="clearfix"></div><br>
<!--                                        <label class="col-md-3 col-md-offset-1">Tranx Id Type</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php // echo $payment_mode->tranxid_type; ?>" name="tranxid_type" placeholder="Enter Transaction Id Type" required=""/>
                                        </div><div class="clearfix"></div><br>-->
                                        <label class="col-md-3 col-md-offset-1">Status</label>
                                        <div class="col-md-7">
                                            <select class="select form-control" name="status">
                                                <option value="<?php echo $payment_mode->active ?>"><?php if($payment_mode->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div><div class="clearfix"></div><br>
                                        <div class="clearfix"></div>
                                    </div>
                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" value="<?php echo $payment_mode->id_paymentmode ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_payment_mode') ?>" class="btn btn-info pull-right waves-effect"> Save</button><div class="clearfix"></div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>