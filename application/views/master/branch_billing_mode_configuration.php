<?php include __DIR__.'../../header.php'; ?>
<style>
    .greytext{
        color: #dddfeb;
    }
    .greytext:hover{
        transform: scale(1.3);
    }
    .box{
        box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
        border: 1px solid #e3e6f0;
        border-left-color: rgb(227, 230, 240);
        border-left-style: solid;
        border-left-width: 1px;
        border-radius: 8px;
        background: #fff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    
    <div class="col-md-4">
        <center><h3><span class="mdi mdi-account-edit fa-lg"></span> Update Billing Modes </h3></center>
    </div><div class="clearfix"></div>
    
    <div class="" style="padding: 0; margin: 0;">
        <div class="thumbnail" style=" min-height: 550px;">
            
            <div class="">
                <form>
                <?php if(count($branch_data) > 0) { ?>
                <div class="thumbnail"><br>
                    <div class="col-md-5"><input type="text" class="filter_1 form-control" id="filter" placeholder="Search anything from table"/></div>
                    <div class="col-md-5"><span class="green-text" id="count_1" ></span></div>                    
                    <div class="col-md-2"><button type="submit" class="btn btn-sm btn-primary" formmethod="POST" formaction="<?php echo base_url('Master/save_billing_mode_configuration') ?>">Submit</button></div>
                    <div class="clearfix"></div><hr>
                    <table class="table table-hover table-responsive">
                        <thead>
                            <th>Branch</th>                            
                            <th>Zone</th>                            
                            <th>Category</th>                                                        
                            <?php  foreach ($billing_mode_data as $mb) {  ?>
                                <th><?php echo $mb->billing_mode_name ?></th> 
                            <?php } ?>
                            
                            <th></th>
                            
                        </thead>
                        <tbody class="data_1">
                            <?php $count=1; 
                                $old_menu_id=null;
                                foreach ($branch_data as $branch) { 
                                //$barr[] = $mb->id_menu; ?>
                                <tr>
                                    <td>
                                        <label > <?php echo $branch->branch_name; ?></label>
                                        <!--<input name="id_branch[]" value="<?php echo $branch->id_branch; ?>" type="hidden"  >--> 
                                    </td>
                                    <td><label > <?php echo $branch->zone_name; ?></label></td>
                                    <td><label > <?php echo $branch->branch_category_name; ?></label></td>
                                        
                                    <?php  foreach ($billing_mode_data as $mb) {  ?>
                                    <td>
                                        <?php        
                                        $checked="";
                                        $cl=$mb->billing_mode_column_name;
                                        if($branch->$cl ==1 ){
                                            $checked="checked";
                                        }                                        
                                        ?>
                                            <div class="material-switch col-md-2">                                         
                                                <input name="mode[<?php echo $branch->id_branch ?>][<?php echo $mb->billing_mode_column_name ?>]" id="mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>"  type="checkbox" <?php echo $checked; ?>  > 
                                                <label for="mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>" class="label-primary"></label> 
                                            </div>
                                        
                                    </td> 
                                    
                                    <script>
                                    $(document).ready(function(){                                        
                                        $('#mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>').change(function(){                                                                                
                                            if($(this).is(":checked")){                                            
                                                $('#mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>').attr('Checked','Checked'); 
                                            }else{                                            
                                                $('#mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>').val('0'); 
                                                $('#mn<?php echo $branch->id_branch.$mb->billing_mode_column_name; ?>').removeAttr('Checked');
                                            }
                                        });
                                    });
                                </script>
                                     <?php } ?>
                                    
                                    
                                </tr>
                                
                            <?php $count++; } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="count" value="" />
                <?php }?>
               </form>
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>