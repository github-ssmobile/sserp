<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function(){
        $("#sidebar").addClass("active");
        $(document).on("click", ".service_stock", function(event) {          
            var product_category = +$('#product_category').val();
            var brand = +$('#brand').val();
            var branch = +$('#idbranch').val();
            var status = +$('#status').val();
            var warranty = +$('#warranty').val();
           
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_service_stock_report",
                method:"POST",
                data:{ status:status,brand: brand, idbranch: branch, product_category: product_category,warranty:warranty},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
    });
</script>
<style>
      table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
 
}
.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
</style>
<div class="col-md-10"><center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> Counter Faulty Pending Stock</h3></center></div>
<div class="clearfix"></div><hr>
    <div class="clearfix"></div><br>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
        </div>
    </div>
    <div class="col-md-6">
        <div id="count_1" class="text-info"></div>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="clearfix"></div><br>
    <div class="thumbnail" style="overflow: auto;padding: 0">
        <div style="height: 650px;">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Status</th>
                    <th>Branch</th>
                    <th>Branch Inward Date</th>
                    <th>Brand</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>
                    <th>Pending Days</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php echo 'Counter Faulty'; ?></td>
                        <td><?php echo '<small class="red-text">Approval Pending</small>'; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->entry_time);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?>
                        </td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                                <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php }else{ ?>
                                <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
            </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>