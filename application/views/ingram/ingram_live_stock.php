<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> Ingram Live Stock</center></div>
<div class="clearfix"></div><hr>
 
<div class="" style="padding: 0; margin: 0;overflow: auto">
    <div id="purchase" style="padding: 10px; margin: 0">                
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <br><div class="col-md-4">
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
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset;"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
            <table id="stock_data" class="table table-condensed table-bordered table-striped table-hover " style="margin-bottom: 0; font-size: 13px">
                <thead style="color: #fff; background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);">
                    <th>Sr</th> 
                    <th>Product</th>
                    <th>Part Number</th>
                    <th>SKU</th>
                    <th>Quantity</th>    
                </thead>
                <tbody id="po_report" class="data_1">
                    <?php if(count($ingram_data)>0){ $sr=1;
                        $i=0; foreach ($ingram_data['model'] as $data){  
                            if($ingram_data['qty'][$i]==0){}else{
                        ?>
                    <tr>
                        <td><?php echo ($sr) ?></td>
                        <td><?php echo $data ?></td>                                               
                        <td><?php echo $ingram_data['part_number'][$i] ?></td>                        
                        <td><?php echo $ingram_data['sku'][$i] ?></td>                        
                        <td><?php echo $ingram_data['qty'][$i] ?></td>                           
                    </tr>
                            <?php $sr++; } $i++;  } ?>                   
                
                 <?php } ?>
                    </tbody>
            </table>
            <p class="pull-left"><?php echo $links; ?></p>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>