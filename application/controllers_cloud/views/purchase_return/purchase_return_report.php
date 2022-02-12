<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnreport').click(function (){
            var from = $('#datefrom').val();
            var to = $('#dateto').val();
            var idvendor = $('#idvendor').val();
            if(from !='' && to !='' && idvendor !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Purchase_return/ajax_get_purchase_return_report",
                    method:"POST",
                    data:{from : from, to : to, idvendor: idvendor},
                    success:function(data)
                    {
                        $("#branch_data").hide();
                        $("#returndata").html(data);
                    }
                });
            }
            else{
                alert("Select Filter");
                return false;
            }
        }); 
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-repeat fa-lg"></span> Purchase Return Report</center></div><div class="clearfix"></div><hr>
<div class="" style="padding: 0; overflow: auto">
    <div id="purchase" style="padding: 5px;">
        <div class="col-md-4">
             <div class="input-group">
                <div class="input-group-btn">
                    <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
                </div>
                <div class="input-group-btn">
                    <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-control input-sm" name="idvendor" id="idvendor">
                <option value="0">All Vendor</option>
                <?php foreach($vendor_data as $vdata){ ?>
                <option value="<?php echo $vdata->id_vendor ?>"><?php echo $vdata->vendor_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-primary btnreport">Search</button></div>
        <div class="clearfix"></div><br>
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
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <div id="returndata">
                
            </div>
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover " style="margin-bottom: 0; font-size: 13px">
                <thead class="bg-info">
                    <th>Sr</th>
                    <th>Return ID</th>
                    <th>Date Time</th>
                    <th>Warehouse</th>
                    <th>Vendor</th>
                    <th>GSTIN</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($purchase_return as $pr){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $pr->financial_year.$pr->id_purchasereturn ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($pr->entry_time)) ?></td>
                        <td><?php echo $pr->branch_name ?></td>
                        <td><?php echo $pr->vendor_name ?></td>
                        <td><?php echo $pr->vendor_gst ?></td>
                        <td><center><a target="_blank" href="<?php echo base_url('Purchase_return/purchase_return_details/'.$pr->id_purchasereturn) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>