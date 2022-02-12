<?php include __DIR__ . '../../header.php'; ?>
<style>
    .blink {
        animation: blinker 1s linear infinite;
    }
    @keyframes blinker {
        10% {
            opacity: 0;
        }
    }
</style>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-clipboard-text fa-lg"></span> Cash Closer</h3></center></div>
<div class="col-md-1"><a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a></div><div class="clearfix"></div><hr>
 <!--level 1 = admin, 2 = idbranch, 3 = user_has_branch-->
<table class="table table-striped table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Sr</th>
        <th>Reference Id</th>
        <th>Date</th>
        <th>Closure Amount</th>
        <th>Remark</th>
        <th>Status</th>
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; $total_amt=0; foreach($cash_closure_data as $cash_closure){ ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $cash_closure->idcombine ?></td>
            <td><?php echo $cash_closure->entry_time ?></td>
            <td><?php $total_amt += $cash_closure->closure_cash; echo $cash_closure->closure_cash ?></td>
            <td><?php echo $cash_closure->remark ?></td>
            <td><?php if($cash_closure->status == 0){ echo "Pending"; }else{ echo "Deposited"; } ?></td>
            <!--<td><a class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>-->
        </tr>
        <?php $i++; } ?>
    </tbody>
    <thead>
        <th colspan="2"></th>
        <th>Total</th>
        <th><?php echo $total_amt; ?></th>
        <th></th>
        <th></th>
    </thead>
</table>
<?php include __DIR__ . '../../footer.php'; ?>