<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<style>
    .fixheader {
        background-color: #fbf7c0;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> Audit Report </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
         <div class="col-md-4 col-sm-4 col-xs-4 ">
            <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
        </div>
        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
            <button class="btn btn-info btn-sm pull-right " onclick="javascript:xport.toCSV('stock_audit_details');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
        </div> 
        <div class="clearfix"></div><br>
        <table class="table table-bordered table-condensed " id="stock_audit_details"> 
            <thead style="background-color: #99ccff" class="fixheader">
                    <th><b>Sr.</b></th>
                    <th><b>Date.</b></th>
                    <th><b>Imei No</b></th>
                    <th><b>Category</b></th>
                    <th><b>Brand</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Product</b></th>
                    <th><b>Qty</b></th>
                    <th><b>Status</b></th>
                    <th><b>Remark</b></th>
                    
                </thead>
                <tbody id="myTable">
                    <?php $i=1; foreach($audit_data as $audit){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $audit->finish_date; ?></td>
                        <td><?php echo $audit->imei_no; ?></td>
                        <td><?php echo $audit->product_category_name; ?></td>
                        <td><?php echo $audit->brand_name; ?></td>
                        <td><?php echo $audit->branch_name; ?></td>
                        <td><?php echo $audit->full_name ?></td>
                        <td><?php echo $audit->qty ?></td>
                        <td><?php echo $audit->status ?></td>
                         <td><?php echo $audit->remark ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                    
            </table>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>