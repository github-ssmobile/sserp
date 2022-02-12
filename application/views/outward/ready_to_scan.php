<?php include __DIR__.'../../header.php'; ?>
<center>
    <h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Stock Mandates</h3>
</center><hr>
<div class="clearfix"></div>
<br>
<div class="thumbnail" id="stock_allocation_data">
    <table id="allocation_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
        <thead class="fixedelement" style="text-align: center;position: none !important;">                        
        <th>Mandate Number</th>
        <th>Branch Name</th>
        <th>Date</th>        
        <th>Products</th>
        <th>Total Quantity</th>
        <th>Allocation Type</th>        
        <th>Action</th>
        </thead>   
            <tbody class="data_1 textalign">
            <?php $i = 1;
            foreach ($stock_allocation as $data) {                                         
                    ?>                
                    <tr>  
                        <td class="textalign" style="color: #0e10aa !important;"><?php echo $data->id_stock_allocation; ?> </td>
                        <td><?php echo $data->branch_name; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($data->date)) ?></td>
                        <td><?php echo $data->sum_product ?></td>
                        <td><?php echo $data->sum_qty ?></td>
                        <?php  $allocation_type=''; if($data->allocation_type == 0){ $allocation_type='Branch'; }else if($data->allocation_type == 1){ $allocation_type='Model'; }else{ $allocation_type='Route'; } ?>
                        <td><?php echo $allocation_type; ?></td>     
                        <td class="textalign"><center>
                            <a target="" class="thumbnail gradient2 textalign" href="<?php echo base_url('Outward/stock_outward/'.$data->id_stock_allocation) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%; color: #fff"><i class=" fa lg mdi mdi-barcode-scan" style="margin-right: 5px;"></i>   Scan  </a></center>
                            
                        </td>
                    </tr>
                <?php $i++;
            } ?>
            </tbody>  
            </table> 
</div>
<?php include __DIR__.'../../footer.php'; ?>