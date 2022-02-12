<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $('#inward_filter').click(function(){
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var idbranch = $('#idbranch').val();
        var status = $('#status').val();
        if((datefrom != '' && dateto == '') || (datefrom == '' && dateto != '')){
            return false;
        }
        $.ajax({
            url:"<?php echo base_url() ?>Purchase/ajax_get_purchase_direct_inward_data_byidbranch",
            method:"POST",
            data:{status : status, idbranch: idbranch, datefrom: datefrom, dateto: dateto},
            success:function(data)
            {
                $("#direct_inward_data").html(data);
            }
        });
    });
});
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Direct Inward</center></div><div class="clearfix"></div><hr>
<div class="panel" style="padding: 0; margin: 0;overflow: auto; min-height: 500px">
    <div id="purchase" style="padding: 10px; margin: 0">
        <div class="col-md-3 col-sm-3 col-xs-6" style="padding: 2px">
            Date Range
            <div class="input-group">
                <div class="input-group-btn">
                    <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
                </div>
                <div class="input-group-btn">
                    <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
                </div>
            </div>
        </div>
        <div class="col-md-2" style="padding: 0">
            Select Branch
            <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
                <option value="">Select Branches</option>
                <option value="">All Branches</option>
                <?php foreach ($branch_data_list as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2" style="padding: 0 5px">
            Status
            <select class="form-control input-sm" name="status" id="status" style="width: 100%">
                <option value="">Select Status</option>
                <option value="">All Status</option>
                <option value="0">Pending</option>
                <option value="1">Approved</option>
                <option value="3">Inwarded</option>
                <option value="2">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            Search
            <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
        </div>
        <div class="col-md-1"><br>
            <button class="btn btn-primary btn-sm" id="inward_filter"><span class="fa fa-filter"></span> Filter</button>
        </div>
        <div class="col-md-1"><br>
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('direct_inward_data');"><span class="fa fa-file-excel-o"></span> Excel</button>
        </div>
        <?php // die('<pre>' . print_r($model_data, 1) . '</pre>');?>
        <!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>-->
        <div class="clearfix"></div>
        <div class="direct_inward_block" style="padding: 0; margin-top: 10px">
            <table id="direct_inward_data" class="table table-condensed table-bordered table-striped table-hover" style="margin-bottom: 0">
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
                        <td><?php echo $po->financial_year.$po->id_purchase_direct_inward ?></td>
                        <td><?php echo $po->date ?></td>
                        <td><?php echo $po->branch_name ?></td>
                        <td><?php echo $po->vendor_name ?></td>
                        <?php if($po->status == 3){ ?>
                        <td>Inwarded</td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                        <?php }else{ ?>
                        <td><?php if($po->status == 0){ echo 'Pending'; }elseif($po->status == 1){ echo 'Approved'; }elseif($po->status == 2){ echo 'Rejected'; } ?></td>
                        <td><a href="<?php echo base_url('Purchase/purchase_direct_inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                        <?php } ?>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>