<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-bank fa-lg"></span> Branch Credit control panel </h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
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
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_credit_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div><br>
        <form>
            <table class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead style="background-color:#99ccff">
                    <th>Sr</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Branch Category</th>
                    <th>Credit / Custody Limit</th>
                    <th>Credit / Custody Days</th>
                </thead>
                <tbody class="data_1">
                    <?php $i = 1;
                    foreach ($branch_data as $bdata){ ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $bdata->branch_name; ?></td>
                            <td><?php echo $bdata->zone_name; ?></td>
                            <td><?php echo $bdata->branch_category_name; ?></td>
                            <td><input type="text" name="credit_limt[]" class="form-control input-sm" value="<?php echo $bdata->credit_limit; ?>"></td>
                            <td><input type="text" name="credit_days[]" class="form-control input-sm" value="<?php echo $bdata->credit_days; ?>"><input type="hidden" name="idbranch[]" class="form-control input-sm" value="<?php echo $bdata->id_branch; ?>"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="clearfix"></div><br>
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Master/update_branch_credit_limit">Submit</button>
        </form>
        <div class="clearfix"></div>
        <div style="display: none">
        <table id="branch_credit_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead style="background-color:#99ccff">
                    <th>Sr</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Branch Category</th>
                    <th>Credit/Custudy Limit</th>
                    <th>Credit/Custudy Days</th>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($branch_data as $bdata){ ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $bdata->branch_name; ?></td>
                            <td><?php echo $bdata->zone_name; ?></td>
                            <td><?php echo $bdata->branch_category_name; ?></td>
                            <td><?php echo $bdata->credit_limit; ?></td>
                            <td><?php echo $bdata->credit_days; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>