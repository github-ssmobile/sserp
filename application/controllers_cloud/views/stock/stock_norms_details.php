<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-9">
    <center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Placement Norms</h3></center>
</div>
<div class="col-md-2 pull-right">
    
</div>
<div class="clearfix"></div><hr>

<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>

<style>
    
    .btn-outline-info {
    color: #17a2b8  !important;
    background-color: transparent  !important;
    background-image: none !important;    
    margin: 0 !important;
    /*box-shadow: none !important;*/
    border: 1px solid #17a2b8 !important;
    
    padding: 5px 10px !important;
    text-transform: initial  !important;
     } 
    
</style>
<script>
    
$(document).ready(function(){

            $('#branch').change(function () {
                var days = $('#days').val();                 
                var branch = $('#branch').val();
                if (branch) {
                    $.ajax({
                        url: "<?php echo base_url() ?>Stock/ajax_get_branch_stocknorms",
                        method: "POST",
                        data: {days: days,branch: branch},
                        success: function (data)
                        {
                            $('#norms_data').html(data);
                            
                        }
                    });
                }
            });
            
            $(document).on("click", ".export", function(event) {   
            event.preventDefault();
            
            var serialized = $(this).closest('.export-form').serialize();
             $.ajax({
                        url: "<?php echo base_url() ?>Stock/ajax_export_branch_stock_norms",
                        method: "POST",
                        data: serialized,
                        
                        success: function (data)
                        {
                            $('#norms_data').html(data);
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
.fixedelement1 {
  background-color: #fbf7c0;
  position: sticky;
  top: 30px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
</style>
<div class="" style="padding: 0; margin: 0;">
    <div id="purchase" style="padding: 20px 10px;">        
        <?php echo form_open('Stock/stock_norms_details') ?>    
            <div class="col-md-2">
                <input type="text" name="days" value="<?php echo $days;?>" id="days" class="form-control" placeholder="Last Sale Days" pattern="[0-9]*" />
            </div>
        <?php echo form_close() ?>        
        <div class="col-md-3">
            <select class="chosen-select form-control" name="branch" id="branch" required="">
                            <option value="">Select Branch</option>
                            <?php foreach ($branch_data as $branch) { ?>
                                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                            <?php } ?>
            </select>
        </div> 
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
        <div class="col-md-2">
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('norms_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <div class="clearfix"></div><br>        
        <!--<div class="thumbnail" style="overflow:auto;">-->
        <table id="norms_data" class="table table-condensed table-full-width table-bordered table-hover">
            <thead class="fixedelementtop">
                <th>Sr</th>            
                <th>Zone</th>  
                <th>Branch</th>  
                <th>Branch Category</th>
                <th>Stock</th>     
                <th>Last <?php echo $days;?> days Sale</th>             
                <th>Placement Norm</th>
                <th>Completion Status</th>  
                <th>Explore</th>  
            </thead>
        <tbody class="data_1">
        
            <?php 
            $zsum_stk=0;$zsum_plc=0;$zsum_sale=0;
            $old_name=$norms_data[0]->zone_name;
            $i=1; foreach ($norms_data as $model){ 
                if($old_name==$model->zone_name){
                $zsum_stk=$zsum_stk+$model->stock_qty;
                $zsum_sale=$zsum_sale+$model->sale_qty;
                $zsum_plc=$zsum_plc+$model->norm_qty;
            }else{ ?>
                <tr>
                    <td></td>
                    <td></td>     
                    <td></td>     
                    <td><b>Total</b></td>            
                    <td class="textalign"><?php echo $zsum_stk; ?></td>                                    
                    <td class="textalign"><?php echo $zsum_sale; ?></td>
                    <td class="textalign"><?php echo $zsum_plc; ?></td>
                </tr>
            <?php   $zsum_sale=0;$zsum_stk=0;$zsum_plc=0;
                    $zsum_stk=$zsum_stk+$model->stock_qty;
                    $zsum_sale=$zsum_sale+$model->sale_qty;
                    $zsum_plc=$zsum_plc+$model->norm_qty;
                }
            ?>
                
            <tr>
                <td><?php echo $i;?></td> 
                <td><?php echo $model->zone_name; ?></td>                                
                <td><?php echo $model->branch_name; ?></td>                                
                <td><?php echo $model->branch_category_name; ?></td>
                <td><?php echo $model->stock_qty; ?></td>
                <td><?php echo $model->sale_qty; ?></td>
                <td><?php echo $model->norm_qty; ?></td>                
                <td>
                <?php  
                    $c=round((($model->setup_cnt/$model->all_models)*100),2);
                    echo $c.'%';
                ?>
                </td>
                <td>
                    <form class="export-form">
                        
                        <input type="hidden" name="days" value="<?php echo $days;?>" />
                        <input type="hidden" name="idbranch" value="<?php echo $model->id_branch; ?>" />
                        <a class=" export thumbnail btn-link waves-effect" href="#"  style="margin: 0" >
                                    <span class="mdi mdi-arrow-right-thick fa-lg"></span>
                        </a>
                    </form>
                   
            </tr>
            
            <?php $i++; $old_name=$model->zone_name; } ?>
             <tr>
                    <td></td>
                    <td></td>     
                    <td></td>     
                    <td><b>Total</b></td>            
                    <td class="textalign"><?php echo $zsum_stk; ?></td>                                    
                    <td class="textalign"><?php echo $zsum_sale; ?></td>
                    <td class="textalign"><?php echo $zsum_plc; ?></td>
                </tr>
        </tbody>
            
        </table>
           <!--</div>-->
        <div class="clearfix"></div>
    </div>
</div>

<?php include __DIR__.'../../footer.php'; ?>