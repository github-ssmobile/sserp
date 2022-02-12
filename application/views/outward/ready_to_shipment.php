<?php include __DIR__.'../../header.php'; ?>
<center>
    <h3 style="margin-top: 0"><span class="mdi mdi-truck fa-lg"></span><?php echo $title ;  ?></h3>
</center><hr>
<div class="clearfix"></div>
<br>
<div class="thumbnail" id="stock_allocation_data">
    <table id="allocation_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
        <thead class="fixedelement" style="text-align: center;position: none !important;">                        
        <th>DC Number</th>
        <th>Mandate Number</th>
        <th>Branch Name</th>
        <th>Date</th>        
        <th>Products</th>
        <th>Total Quantity</th>             
        <th>Action</th>
        </thead>   
            <tbody class="data_1 textalign">
            <?php $i = 1;
            foreach ($stock_allocation as $data) {                                         
                    ?>                
                    <tr>  
                        <td class="textalign" style="color: #0e10aa !important;">
                            <a target="" href="<?php echo base_url('Outward/outward_details/'.$data->idstock_allocation.'/0') ?>">  <?php echo $data->id_outward; ?>  </a></center>
                        </td>                        
                        
                        <td class="textalign" style="color: #0e10aa !important;"><?php echo $data->idstock_allocation; ?> </td>
                        <td><?php echo $data->branch_name; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($data->date)) ?></td>
                        <td><?php echo $data->sum_product ?></td>
                        <td><?php echo $data->sum_qty ?></td>                        
                        <td class="textalign"><center>
                            <?php if ($data->status==0){ ?>
                                <a target="" class="thumbnail gradient2 textalign" href="<?php echo base_url('Outward/outward_details/'.$data->idstock_allocation.'/0') ?>" style="margin: 0 8px;padding: 5px !important;width: 50%; color: #fff"><i class="" style="margin-right: 5px;"></i>   Update  </a></center>
                            <?php } elseif($data->status==1){ ?>
                                <a target="" class="thumbnail gradient2 textalign" href="<?php echo base_url('Outward/receive_w_shipment/'.$data->idstock_allocation) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%; color: #fff"><i class="" style="margin-right: 5px;"></i>   Receive  </a></center>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++;
            } ?>
            </tbody>  
            </table> 
</div>
<?php include __DIR__.'../../footer.php'; ?>