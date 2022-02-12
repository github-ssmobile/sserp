<?php include __DIR__.'../../header.php'; ?>
<style>
.blink_me {
  animation: blinker 2s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
    <div class="col-md-1">
        <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
    </div><div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
        <div  class="blink_me" style="color: red"><center><h4>Below Opening Stock Not Uploaded. Check Model Variants Before Upload</h4></center></div>
        <table class="table table-bordered table-condensed">
            <thead>
                <th><b>Sr.</b></th>
                <th><b>Product Name</b></th>
                <th><b>Imei</b></th>
                <!--<th><b>Branch</b></th>-->
                <th><b>Godown</b></th>
                <th><b>Date</b></th>
            </thead>
            <tbody>
                <?php $i=1; foreach($remain_upload as $remain){ ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $remain->name; ?></td>
                    <td><?php echo $remain->imei; ?></td>
                    <!--<td><?php echo $remain->branch_name ?></td>-->
                    <td><?php echo $remain->godown_name ?></td>
                    <td><?php echo date('Y-m-d h:i:sa', strtotime($remain->datetime)); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a class="btn btn-info" href="<?php echo base_url()?>Stock/opening_stock">Close</a>
    </div>
<?php include __DIR__.'../../footer.php'; ?>