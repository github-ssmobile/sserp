<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>

<script>
  $(document).on("click", ".sent_to_local", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idservice=$(parentDiv).find('.idservice').val();
            var care=$(parentDiv).find('.care').val();
            if(care){
                 if (confirm('Do you want to send??')) {
                    jQuery.ajax({
                        url: "<?php echo base_url('Service/ajax_sent_to_local') ?>",
                        method:"POST",
                        data:{idservice:idservice,care:care},
                        success:function(data){
                            $(parentDiv).remove();
                            swal('🙂 Case sent to Local Care', 'Transfer Done', 'success');
                        }
                    });
                }
            }
        });
        $(document).on("click", ".btn_care", function(event) {            
             var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idservice=$(parentDiv).find('.details').show();
            $(this).hide();
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
<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> My Service Stock</h3>
    </center>
</div>
<div class="clearfix"></div><br>    
    <div class="thumbnail" style="overflow: auto;padding: 0">
         <br> 
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
        <br>
        <div class="col-md-6">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="clearfix"></div><br>
       
       <div class="col-md-1"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset; display: none"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div style="height: 650px;">
            <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">           
              <?php  if($service_stock){ ?>            
                <thead class="fixedelementtop">                    
                    <th>Case ID</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th>To Local</th>
                    <!--<th><button data-target="#ho_mandate" data-toggle="collapse" class="btn btn-primary btn-sm gradient2 export" ><span class="fa fa-send-o"></span> Send to HO</button></th>-->
                    
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>                        
                        <td><?php echo $stock->id_service; ?>
                            <input type="hidden" name="idservice" class="idservice" id="idservice" value="<?php echo $stock->id_service ?>">
                        </td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>     
                        <td>
                            <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 btn_care" ><span class="fa fa-send-o"></span> Send to Care</button>
                            <div class="details" style="display:none;">
                                <div class="col-md-2">Care Center Name</div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control care" id="care" name="care" placeholder="Care center name"/>
                                </div> 
                                <div class="col-md-2">
                                    <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 sent_to_local" ><span class="fa fa-send-o"></span> Send</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        
                        </td>
<!--                        <td>                           
                            <center><input class="hide_checkbox" type="checkbox" name="checkrow" id="checkrow" value="<?php // echo $stock->id_service; ?>" style=""></center>
                        </td>-->
                        
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
            
        <?php } ?>
            </table>
        </div>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>