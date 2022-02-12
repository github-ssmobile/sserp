<?php include __DIR__.'../../header.php'; ?>
<style>
    .greytext{
        color: #dddfeb;
    }
    .greytext:hover{
        transform: scale(1.3);
    }
    .box{
        box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
        border: 1px solid #e3e6f0;
        border-left-color: rgb(227, 230, 240);
        border-left-style: solid;
        border-left-width: 1px;
        border-radius: 8px;
        background: #fff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    <div class="col-md-4"><br>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url($_SESSION['dashboard']) ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('Master/payment_mode_details') ?>">Payment mode</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Role Has Menu</li>
        </ol>
    </div>
    <div class="col-md-4">
        <center><h3><span class="mdi mdi-domain fa-lg"></span> <?php echo $name ?> devices</h3></center>
    </div><div class="clearfix"></div><hr>
    <!--<center><h4><span class="mdi mdi-credit-card fa-lg"></span>  Devices</h4></center>-->
    <div class="thumbnail" style="padding: 0; margin: 0;">
        <table class="table table-striped table-condensed" style="margin-bottom: 0">
            <thead>
                <th>Sr</th>
                <th>Branch</th>
                <th>Device Id</th>
                <th>Status</th>
                <th><center>Submit</center></th>
            </thead>
            <tbody>
                <tr>
                <form>
                    <td>Create</td>
                    <td>
                        <select data-placeholder="Select Branches" name="idbranch" class="chosen-select" required="" style="width: 400px">
                            <option value="">Select Branch</option>
                            <?php foreach ($branch_data as $branch) { ?>
                            <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="device_id" placeholder="New Device Id for <?php echo $name ?>" required="" />
                        <input type="hidden" name="idmode" value="<?php echo $mode ?>" />
                        <input type="hidden" name="mode_name" value="<?php echo $name ?>" />
                        <input type="hidden" name="paymenthead" value="<?php echo $paymenthead ?>" />
                    </td>
                    <td>
                        <select class="select form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </td>
                    <td>
                        <center><button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_payment_mode_has_devices') ?>" class="btn btn-sm btn-info gradient2 waves-effect"><i class="fa fa-save"></i> Save</button></center>
                    </td>
                </form>
                </tr>
                <?php $i=1; if(count($devices_data) > 0){ foreach ($devices_data as $device){ ?>
                <tr>
                <form>
                    <td><?php echo $i ?></td>
                    <td>
                        <select data-placeholder="Select Branches" name="idbranch" class="chosen-select" required="" style="width: 400px">
                            <option value="<?php echo $device->idbranch ?>"><?php echo $device->branch_name; ?></option>
                            <?php foreach ($branch_data as $branch) { ?>
                            <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="device_id" value="<?php echo $device->device_id ?>" required="" />
                        <input type="hidden" name="idmode" value="<?php echo $mode ?>" />
                        <input type="hidden" name="mode_name" value="<?php echo $name ?>" />
                        <input type="hidden" name="idrow" value="<?php echo $device->id_payment_mode_has_devices ?>" />
                        <input type="hidden" name="paymenthead" value="<?php echo $paymenthead ?>" />
                    </td>
                    <td>
                        <?php // if($device->status == 1){ echo 'Active'; } else{ echo 'In Active'; } ?>
                        <select class="select form-control" name="status">
                            <option value="<?php echo $device->status ?>"><?php if($device->status == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </td>
                    <td>
                        <center><button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/edit_payment_mode_has_devices') ?>" class="btn btn-sm btn-info gradient2 waves-effect"><i class="fa fa-edit"></i> Edit</button></center>
                    </td>
                </form>
                </tr>
                <?php $i++; }} ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>