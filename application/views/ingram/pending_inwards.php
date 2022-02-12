<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> <?php echo "Purchase Inward" ; ?></center></div>
<div class="clearfix"></div><hr>
<div class="" style="padding: 0; margin: 0;overflow: auto">
    <div id="purchase" style="padding: 10px; margin: 0">
        <div class="col-md-11"></div>
        <div class="col-md-1">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('order_report');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div><div class="clearfix"></div>
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover " style="margin-bottom: 0; font-size: 13px">
                <thead>
                    <th>Sr</th>
                    <th>Date</th>   
                    <th>SS OrderNumber</th>
                    <th>Ingram OrderNumber</th>                    
                    <th>Product</th>
                    <th>Quantity</th>                     
                    <th>Status</th>                   
                    <th>Action</th>
                    <th>Info</th>
                </thead>
                <tbody id="order_report" class="data_1">
                    <?php if(count($purchase_order)==0){?>
                <tr>
                    <td colspan="9" style="background: #ffffff;">                 
                        <center><img src="<?php echo base_url('assets/images/no-data-found.png') ?>" style="width: 50%" /></center>                    
                    </td>   
                        </tr>
                    <?php }else{ ?>
                    
                    <?php $i=1; foreach ($purchase_order as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->date ?></td>     
                        <td><?php echo $po->financial_year.'-'.$po->id_vendor_po ?></td>
                        <td><?php echo $po->ingram_order_number ?></td>                        
                        <td><?php echo $po->sku ?></td>
                        <td><?php echo $po->qty?></td>
                                                             
                        <td><?php 
                            if($po->status==1 && $po->ingram_order_status==1){
                                echo "Pending For Inward";
                            }elseif($po->status==1 && $po->ingram_order_status==2){
                                echo "Inwared";
                            }elseif($po->status==2 || $po->status==3){
                                echo "Rejected";
                            }
                        ?></td>
                        
                        <td><center>                            
                            <input type="hidden" class="id_sale_token" id="po_number" value="<?php echo $po->financial_year.'-'.$po->id_vendor_po ?>" />
                           <?php if($po->ingram_order_status==1){ ?>
                                <a target="_blank" href="<?php echo base_url('Ingram_Api/ingram_inward/'.$po->id_vendor_po) ?>" class="btn btn-sm btn-link waves-effect waves-ripple"><i class="fa fa-barcode fa-lg"></i></a>
                            <?php } ?>
                        
                            </td>
                            <td><a href="<?php echo base_url()?>Ingram_Api/po_details/<?php echo $po->id_vendor_po ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-info-circle"></i></a></td>
                    
                    </tr>
                    <?php $i++; } }?>                   
                
                    </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>