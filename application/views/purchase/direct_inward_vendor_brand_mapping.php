<?php include __DIR__ . '../../header.php'; 
if($this->session->userdata('direct_inward') == 0 && $this->session->userdata('role_type') == 2){ ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Direct Inward</center></div><div class="clearfix"></div><hr>
<center><h3><i class="mdi mdi-alert"></i> Your branch is not allowed for direct purchase inward. </h3>
    <img src="<?php echo base_url('assets/images/highAlertIcon.gif') ?>" />
</center>
<?php }else{ ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Direct Inward</center></div>
<div class="col-md-1"><a class="arrow-down waves-effect btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay"></a></div><div class="clearfix"></div><hr>
<script>
    $(document).ready(function () {
        $('#idgodown').change(function(){
            var godown_name = $('#idgodown option:selected').text();
            $('#godown_name').val(godown_name);
        });
        $('#idvendor').change(function(){
            var idvendor = $(this).val();
            $.ajax({
                url: "<?php echo base_url() ?>Purchase/ajax_get_vendor_has_brands_for_direct_inward",
                method: "POST",
                data:{idvendor : idvendor},
                success: function (data)
                {
                    $('#model_block').html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
    });
</script>
<form id="pay">
    <?php // $po_id = 'PO/' . $y . '-' . $y2 . '/' . $_SESSION['idbranch'] . '/' . strtoupper(substr($_SESSION['branch_name'], 0, 4) . substr($_SESSION['branch_name'], -1)); ?>
    <div class="col-md-10 col-md-offset-1 thumbnail" style="padding: 10px; margin-bottom: 40px; border-radius: 10px;">
        <div class="col-md-8 col-md-offset-2">
            <center><h4 style="margin: 0"><i class="pe pe-7s-news-paper fa-lg"></i> Generate Direct Inward Request</h4></center>
        </div>
        <div class="col-md-2">
            <small id="po_id">DI/<?php // echo $y.'-'.$y2.'/'.$branch_data->branch_code.'/'; ?></small>
        </div><div class="clearfix"></div><hr>
        <input type="hidden" name="financial_year" id="financial_year" value="DI/<?php echo $y.'-'.$y2.'/'.$_SESSION['branch_code'].'/'; ?>" />
        <input type="hidden" name="iduser" value="<?php  echo $_SESSION['id_users'] ?>" />
        <div class="col-md-5">
            <div class="col-md-3 text-muted">Date</div>
            <div class="col-md-9"><?php echo $date ?>
                <input type="hidden" name="date" value="<?php echo $now ?>" />
                <input type="hidden" name="godown_name" id="godown_name" />
                <input type="hidden" name="status" value="0" />
                <input type="hidden" name="idwarehouse" id="idwarehouse" value="<?php echo $_SESSION['idbranch'] ?>" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-3 text-muted">Godown</div>
            <div class="col-md-9">
                <select class="form-control input-sm" required="" name="idgodown" id="idgodown">
                    <option value="">Select Godown</option>
                    <?php foreach ($godown_data as $godown){ ?>
                        <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
        </div>
        <div class="col-md-7">
            <div class="col-md-3 text-muted">Vendor</div>
            <div class="col-md-9">
                <select class="chosen-select form-control input-sm" required="" name="idvendor" id="idvendor" style="width: 100%">
                    <option value="">Select Vendor</option>
                    <?php foreach ($vendor_data as $vendor) { ?>
                        <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
            <div class="col-md-3 text-muted">Remark</div>
            <div class="col-md-9"><input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark" /></div>
        </div><div class="clearfix"></div>
        <div id="model_block"></div><div class="clearfix"></div><br>
        <div class="panel" id="model_table" style="display: none; margin-top: 10px; padding: 0">
            <table class="table table-condensed table-striped table-responsive">
                <thead>
                <th>Model Id</th>
                <th>Model</th>
                <th>Godown</th>
                <th>Warehouse Qty</th>
                <th class="col-md-2">Qty</th>
                <th>Remove</th>
                </thead>
                <tbody id="selected_model"></tbody>
            </table>
        </div><hr>
        <a class="btn btn-warning waves-effect simple-tooltip gradient1" data-toggle="collapse" data-target="#pay">Close</a>
        <button type="submit" formmethod="POST" formaction="<?php echo base_url('Purchase/save_purchase_direct_inward') ?>" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
        <div class="clearfix"></div>
    </div>
</form><div class="clearfix"></div>
<div class="panel" style="padding: 0; margin: 0;overflow: auto">
    <div id="purchase" style="padding: 10px; margin: 0">
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-5">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <?php // die('<pre>' . print_r($model_data, 1) . '</pre>');?>
        <!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>-->
        <div class="clearfix"></div>
        <div class="" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0">
                <thead>
                    <th>Sr</th>
                    <th>PO ID</th>
                    <th>Date</th>
                    <th>Warehouse</th>
                    <th>Vendor</th>
                    <th>Status</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($purchase_direct_inward as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->financial_year.'-'.$po->id_purchase_direct_inward ?></td>
                        <td><?php echo $po->date ?></td>
                        <td><?php echo $po->branch_name ?></td>
                        <td><?php echo $po->vendor_name ?></td>
                        <?php if($po->status == 0){ ?>
                        <td><?php echo 'Pending'; ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/purchase_direct_inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                        <?php }elseif($po->status == 1){ ?>
                        <td><?php echo 'Approved'; ?></td>
                        <td><a href="<?php echo base_url('Purchase/purchase_direct_inward/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-link waves-effect waves-ripple"><i class="fa fa-barcode fa-lg"></i></a></td>
                        <?php }elseif($po->status == 2){ ?>
                        <td><?php echo 'Rejected'; ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/purchase_direct_inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                        <?php }elseif($po->status == 3){ ?>
                        <td><?php echo 'Inwarded'; ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/purchase_direct_inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                        <?php } ?>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } include __DIR__ . '../../footer.php'; ?>