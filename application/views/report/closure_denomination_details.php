<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1"><center><h3 style="margin: 0"><span class="mdi mdi-clipboard-text fa-lg"></span> Cash Closure Details</h3></center></div><div class="clearfix"></div><hr>

<div class="col-md-4 col-sm-4 col-xs-4 ">
    <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
</div>
<div class="col-md-2 col-sm-2 col-xs-2 pull-right">
    <button class="btn btn-primary btn-sm pull-right" onclick="javascript:xport.toCSV('cash_closer_details<?php echo date('d-m-Y h:i a') ?>');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export</button>
</div>
<div class="clearfix"></div>
<div class="thumbnail" style="height: 700px; font-size: 13px; overflow: auto; margin-top: 5px">
    <table class="table table-bordered table-condensed" id="cash_closer_details<?php echo date('d-m-Y h:i a') ?>">
        <thead>
            <th>Sr.</th>
            <th>Branch</th>
            <th>Denomination</th>
            <th>Qty</th>
            <th>Cash</th>
        </thead>
        <tbody id="myTable">
            <?php $i=1; $total=0;  foreach ($closer_data as $closer){ ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $closer->branch_name; ?></td>
                <td><?php echo $closer->denomination; ?></td>
                <td><?php echo $closer->qty; ?></td>
                <td><?php echo $closer->cash; $total = $total +  $closer->cash; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Total</b></td>
                <td><b><?php echo $total; ?></b></td>
            </tr>
        </tbody>
        
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>