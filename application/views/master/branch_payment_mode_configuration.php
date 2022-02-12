<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-bank fa-lg"></span> Payment Mode Configuration Panel</h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-4">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2">
            <!--<button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_has_payment_mode');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>-->
        </div>
        <div class="clearfix"></div><br>
        <form>
            <table id="branch_has_payment_mode" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead style="background-color:#99ccff">
                    <th>Sr</th>
                    <th>Branch</th>
                    <th>Zone</th>
                    <th>Branch Category</th>
                    <?php foreach ($paymenthead_data as $phead){ ?>
                        <th><?php echo $phead->payment_head?></th>
                    <?php } ?>
                </thead>
                <tbody class="data_1">
                    <?php $i = 1;
                    foreach ($branch_data as $bdata){ ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $bdata->branch_name; ?>
                             <input name="idbranch[]" value="<?php echo $bdata->id_branch; ?>" type="hidden"  > 
                            </td>
                            <td><?php echo $bdata->zone_name; ?></td>
                            <td><?php echo $bdata->branch_category_name; ?></td>
                             <?php foreach ($paymenthead_data as $phead){
                                 $checked="";
                                foreach ($branch_paymenthead_data as $bph){
                                   if($bph->idbranch == $bdata->id_branch && $bph->idhead == $phead->id_paymenthead){ 
                                       $checked="checked"; 
                                   }
                                }
                                 ?>
                            <td>
                                <div class="material-switch col-md-2">                                         
                                    <input name="mode[<?php echo $bdata->id_branch ?>][<?php echo $phead->payment_head ?>]" id="mn<?php echo $bdata->id_branch.$phead->payment_head; ?>"  type="checkbox" <?php echo $checked; ?>  > 
                                    <label for="mn<?php echo $bdata->id_branch.$phead->payment_head; ?>" class="label-primary" ></label> 
                                </div>
                            </td>
                            <script>
                                $(document).ready(function(){                                        
                                    $('#mn<?php echo $bdata->id_branch.$phead->payment_head; ?>').change(function(){                                                                                
                                        if($(this).is(":checked")){                                            
                                            $('#mn<?php echo $bdata->id_branch.$phead->payment_head; ?>').attr('Checked','Checked'); 
                                        }else{                                            
                                            $('#mn<?php echo $bdata->id_branch.$phead->payment_head; ?>').val('0'); 
                                            $('#mn<?php echo $bdata->id_branch.$phead->payment_head; ?>').removeAttr('Checked');
                                        }
                                    });
                                });
                            </script>
                             <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="clearfix"></div><br>
            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Master/update_branch_paymenthead_configuration">Submit</button>
        </form>
        <div class="clearfix"></div>
        
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>