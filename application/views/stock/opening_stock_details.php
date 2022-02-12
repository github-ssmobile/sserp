<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
<div class="col-md-1">
    <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
</div><div class="clearfix"></div><hr>
<div class="" style="padding: 0; margin: 0; min-height: 650px;">
    <div class="col-md-3 col-sm-3 col-xs-3 ">
        <input id="myInput" type="text" class="form-control input-sm" placeholder="Search..">
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
        <button class="btn btn-primary btn-sm pull-right " onclick="javascript:xport.toCSV('opening_stock_data');" style="margin: 0"><span class="fa fa-file-excel-o"></span> Export To Excel</button>
    </div>
    <div class="clearfix"></div><br>
    <?php if($opening_data){ ?>
    <table class="table table-bordered" id="opening_stock_data">
        <thead style="background-color: #ffffcc">
           <th>Sr.</th>
           <th>Branch</th>
           <th>Date</th>
           <th>Imei</th>
           <th>Godown</th>
           <th>Product Category</th>
           <th>Brand</th>
           <th>Variant Name</th>
           <th>Old Model Name</th>
       </thead>
       <tbody id="myTable">
           <?php $i=1; foreach ($opening_data as $opening){ ?>
           <tr>
               <td><?php echo $i++; ?></td>
               <td><?php echo $opening->branch_name; ?></td>
               <td><?php echo $opening->date; ?></td>
               <td><?php echo $opening->imei_no; ?></td>
               <td><?php echo $opening->godown_name; ?></td>
               <td><?php echo $opening->product_category_name; ?></td>
               <td><?php echo $opening->brand_name; ?></td>
               <td><?php echo $opening->full_name; ?></td>
               <td><?php echo $opening->product_name; ?></td>
           </tr>
           <?php } ?>
       </tbody>
    </table>
    <?php } ?>
</div>
<?php include __DIR__.'../../footer.php'; ?>