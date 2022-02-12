<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#checkAll').click(function () {    
            $(':checkbox.checkimei').prop('checked', this.checked);    
        });
    });
</script>
<style>
.fix {
    position: fixed;
    bottom: 80px;
    right: 20px; 
}

.fixleft {
    position: fixed;
    bottom: 80px;
    left: 250px; 
}
.fixtop {
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index: 999;
    
}
.blink_me {
  animation: blinker 1s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> Generate Missing Invoice </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0" >
        <?php if(count($missing_data) > 0){ ?>
        <form>
<!--            <div class="col-md-6 col-md-offset-2">
                <div class="blink_me" style="color: red"><center><h4>Inward Missing stock before missing credit bill creation</h4></center></div>
            </div>
            <div class="col-md-1 pull-left">
                <a class="btn btn-warning" href="<?php echo base_url()?>Audit/audit_missing_inward"><span class="fa fa-arrow-right"></span> <b>Missing Inward</b></a>
            </div>    
            <div class="clearfix"></div><br>-->
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #9ed5f0" class="fixtop">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <th>Godown</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>Imei</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <!--<th>Price</th>-->
                    <th>Select all<br>
                        <input type="checkbox" id="checkAll">
                    </th>
                </thead>
                <tbody>
                    <?php $sr=1; foreach ($missing_data as $miss){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $miss->audit_date; ?></td>
                        <td><?php echo $miss->branch_name; ?></td>
                        <td><?php echo $miss->product_category_name; ?></td>
                        <td><?php echo $miss->godown_name; ?></td>
                        <td><?php echo $miss->brand_name; ?></td>
                        <td><?php echo $miss->product_name; ?></td>
                        <td><?php echo $miss->imei_no ?></td>
                        <td><?php echo $miss->qty ?></td>
                        <td><?php echo 'Missing'; ?></td>
                        <!--<td><?php // echo $miss->mop ?></td>-->
                        <td>
                            <?php if($miss->idskutype == 4){ ?>
                                <input type="hidden" name="idvariant[<?php echo $miss->id_stock; ?>]" value="<?php echo $miss->idvariant; ?>">
                                <input type="hidden" name="missqty[<?php echo $miss->id_stock; ?>]" value="<?php echo $miss->qty; ?>">
                            <?php }else{ ?>
                                <input type="hidden" name="missimei[<?php echo $miss->id_stock; ?>]" value="<?php echo $miss->imei_no; ?>">
                            <?php } ?>
                            <input type="hidden" name="idbranch" value="<?php echo $miss->idbranch?>">
                            <input type="hidden" name="idgodown" value="<?php echo $miss->idgodown?>">
                            <input type="hidden" name="idbrand" value="<?php echo $miss->idbrand?>">
                            <input type="hidden" name="idproductcategory" value="<?php echo $miss->idproductcategory?>">
                            <input type="checkbox" class="checkimei" name="checkimei[<?php echo $miss->id_stock; ?>]" id="checkkimei" value="<?php echo $miss->id_stock; ?>">
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button class="btn btn-primary pull-right fix" id="submit_audit" formaction="<?php echo base_url()?>Audit/save_missing_audit_credit" formmethod="POST">Submit</button>
        </form>
        <a class="btn btn-warning pull-left fixleft" href="<?php echo base_url()?>Audit/audit_missing_stock_report">Cancel</a>
        <?php } else{ ?>
            <center><h4>Missing Data Not Found</h4>   
                <a class="btn btn-info pull-left fixleft" href="<?php echo base_url()?>Audit/audit_missing_stock_report"><span class="fa fa-arrow-left"></span> Back To Report</a></center>
        <?php } ?>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>