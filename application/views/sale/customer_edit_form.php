<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="fa fa-handshake-o fa-lg"></span> Edit Customer</center></div><div class="clearfix"></div><hr>
<?php foreach ($customer_data as $customer){ ?>
<script>
$(document).ready(function(){
    $(document).on("change", "#idstate", function (event) {
        var state_name = $("#idstate option:selected").text();
        $('#state_name').val(state_name);
    });
});
</script>
<form class="" style="padding: 0; overflow: auto">
    <div id="purchase" style="padding: 5px;">
        <div class="col-md-8 col-md-offset-2 thumbnail">
            <center><h4><div id="spcust_contact">Contact No. <?php echo $customer->customer_contact ?></div></h4></center>
            <div class="clearfix"></div>
            <div class="p-1">
                <span class="col-md-2 text-muted">Customer</span>
                <div class="col-md-10">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <input type="text" class="form-control" id="customer_fname" name="customer_fname" placeholder="Customer First Name" value="<?php echo $customer->customer_fname ?>" required="">
                            <input type="hidden" id="idcustomer" name="idcustomer" value="<?php echo $customer->id_customer ?>">
                        </div>
                        <div class="input-group-btn">
                            <input type="text" class="form-control" id="customer_lname" name="customer_lname" placeholder="Customer First Name" value="<?php echo $customer->customer_lname ?>" required="">
                        </div>
                    </div>
                </div>
            </div><div class="clearfix"></div><br>
<!--            <div class="p-1">
                <span class="col-md-2 text-muted">Address</span>
                <div class="col-md-10">
                    <input type="text" class="form-control" id="customer_address" name="customer_address" value="<?php echo $customer->customer_address ?>" />
                </div>-->
            <!--</div><div class="clearfix"></div>-->
            <div class="p-1">
                <span class="col-md-2 text-muted">Pincode</span>
                <div class="col-md-10">
                    <input type="text" class="form-control" id="pincode" name="pincode" value="<?php echo $customer->customer_pincode ?>" required="" />
                    <input type="hidden" class="form-control" id="state_name" name="state_name" value="<?php echo $customer->customer_state ?>" />
                </div>
            </div><div class="clearfix"></div><br>
            <div class="p-1">
                <span class="col-md-2 text-muted">Address</span>
                <div class="col-md-10">
                    <textarea type="text" class="form-control" id="customer_address" name="customer_address" required=""><?php echo $customer->customer_address ?></textarea>
                </div>
            </div><div class="clearfix"></div><br>
            <div class="p-1">
                <span class="col-md-2 text-muted">State</span>
                <div class="col-md-10">
                    <select name="idstate" id="idstate" style="width: 100%" class="form-control" required="">
                        <option value="">Select State</option>
                        <option value="<?php echo $customer->idstate; ?>" selected="" ><?php echo $customer->customer_state; ?></option>
                        <?php foreach ($state_data as $state) { ?>
                        <option value="<?php echo $state->id_state; ?>" ><?php echo $state->state_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div><div class="clearfix"></div><br>
            <div class="p-1">
                <span class="col-md-2 text-muted">GSTIN</span>
                <div class="col-md-10">
                    <input type="text" class="form-control" name="customer_gst" id="customer_gst" value="<?php echo $customer->customer_gst ?>" placeholder="Enter GSTIN" pattern="^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[a-z1-9A-Z]{1}[zZ]{1}[a-z0-9A-Z]{1}$" />
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div>
            <div class="clearfix"></div>
            <div class="p-1">
                <div class="col-md-12 p-2">
                    <div class="col-md-6">
                        <h4 class=green-text>Customer Edited Count <?php echo $customer->customer_edit_count; ?> Times</h4>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-danger pull-right">Customer Edit Limit is 2</h4>
                    </div>
                    <div class="clearfix"></div><hr>
                    <?php if($customer->customer_edit_count >= 2){ ?>
                        <marquee style="color: #ff0033">Customer Edit Limit is over, You can not edit customer details</marquee><br>
                    <?php } elseif($customer->customer_gst != NULL){ ?>
                    <marquee style="color: #ff0033">GSTIN already present, You can not edit customer details</marquee><br>
                    <?php }else{ ?>
                        <div class="pull-right">
                            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Sale/edit_customer_details') ?>" class="btn btn-primary btn-outline" id="customer_edit_btn"><i class="mdi mdi-account-edit"></i> Edit Customer</button>
                        </div>
                    <?php } ?>
                </div>
            </div><div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</form>
<?php } include __DIR__ . '../../footer.php'; ?>