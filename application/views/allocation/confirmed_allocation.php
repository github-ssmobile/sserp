<?php include __DIR__.'../../header.php';  
if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<script>
$(document).ready(function(){

    $('.stockallocation').click(function(){
        var idstock = $(this).find('.idstock').val();
        var date = $(this).find('.date').val();
        var bname = $(this).find('.bname').val();
        var entry_time = $(this).find('.entry_time').val();
        var idbranch = $(this).find('.idbranch').val();
        var allocation_type = $(this).find('.allocation_type').val();
        $.ajax({
            url:"<?php echo base_url() ?>Stock_allocation/ajax_get_stock_allocation",
            method:"POST",
            data:{idstock : idstock},
            success:function(data)
            {
                $("#allocated_stock_data").html(data);
                 $("html, body").animate({scrollTop: 0}, 100);
            }
        });
    });


        $(document).on("click", ".allocation_confirm", function (event) {
        
                var ids = [];
                if (confirm('Do you want to Confirm this Allocation!!')) {                    
                    var idstock = $(this).attr("idstock");  
                    var status=2;
                var  branch_id =[];
                     ids.push(idstock); 
                    $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/confirm_allocation",
                    method: "POST",                
                    data: {allocation_id:ids,status:status},        
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                            location.reload(); 
                        }else if(data.data === "fail"){
                            alert("Fail to remove!! ");
                        }

                    }
                });
            }
        });
});
</script>
<center>
    <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Allocated Stock</h3>
</center><hr>
<div id="allocated_stock_data" style="max-height: 500px;overflow: auto"></div><div class="clearfix"></div><br>
<?php if(count($stock_allocation)){ ?>
<form class="">
    <div>
         <?php  $oldroute=$stock_allocation[0]->zone_name;         ?>
        
        <div class="col-md-1"> </div>
            <h4 style="margin-top: 0"><span class="mdi mdi-road-variant fa-smile-o"></span> <?php echo $oldroute;?></h4>
       <div class="clearfix"></div>
       <hr>
        <?php foreach ($stock_allocation as $allocation) {        
            $br_nm= str_replace(" ","",$allocation->branch_name);
           if($oldroute==$allocation->zone_name){                
            }else{                 
                ?>                
                <div class="clearfix"></div>               <br>
                <div class="col-md-1"></div>
                <h4 style="margin-top: 0"><span class="mdi mdi-road-variant fa-smile-o"></span> <?php echo $allocation->route_name;?></h4>       
                <hr>  
            <?php  } ?>
       
        <div class="col-md-3" style="padding: 5px;">
            <a class="panel waves-effect waves-ripple waves-block " style="padding: 5px; margin: 0">
                <div class="col-md-2 purple-text" style="font-family: Kurale; font-size: 20px;color: #0e10aa !important;"><?php echo $allocation->id_stock_allocation ?></div>                
                <div class="col-md-1"></div>
                <div class="col-md-7 " style="font-size: 18px;text-align: center">
                    <?php echo $allocation->branch_name ?>                    
                </div>     
                <div class="col-md-1">                                                
                    
                </div>
                <div class="clearfix"></div>
                <hr style="margin: 0px !important;">
                <div class="stockallocation">
                    <input type="hidden" value="<?php echo $allocation->id_stock_allocation ?>" id="idstock" class="idstock" name="idstock" />
                    <input type="hidden" class="date" value="<?php echo $allocation->date ?>" />
                    <input type="hidden" class="bname" value="<?php echo $allocation->branch_name ?>" />
                    <input type="hidden" class="entry_time" value="<?php echo $allocation->entry_time ?>" />  
                    <input type="hidden" class="idbranch" value="<?php echo $allocation->idbranch ?>" /> 
                    <input type="hidden" class="allocation_type" value="<?php echo $allocation->allocation_type ?>" /> 
                <div class="" style="padding-top: 10px;">
                    <div class="col-md-5"><span class="text-muted" style="font-size: 13px">Products - <?php echo $allocation->sum_product  ?></span></div>
                    <div class="col-md-5"><span class="text-muted" style="font-size: 13px">Qty - <?php echo $allocation->sum_qty ?></span></div>
                    <div class="col-md-2"></div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-5" style="padding-top: 10px;"><span class="text-muted" style="font-size: 13px">Type - <?php 
                    $allocation_type=''; if($allocation->allocation_type == 0){ $allocation_type='Branch'; }else if($allocation->allocation_type == 1){ $allocation_type='Model'; }else{ $allocation_type='Route'; }
                    echo $allocation_type ?></span>
                </div>
                <div class="col-md-5" style="padding-top: 10px;">
                    <p class="text-muted " style="font-size: 13px;">
                        <i class="fa fa-calendar"></i> <?php echo date('d-M-Y', strtotime($allocation->date)) ?>
                    </p>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-5" style="padding-top: 10px;"><span class="text-muted" style="font-size: 13px">Zone - <?php                     
                    echo $allocation->zone_name ?></span>
                </div>                
                <div class="col-md-6" style="padding-top: 10px;"><span class="text-muted" style="font-size: 13px">Route - <?php                     
                    echo $allocation->route_name ?></span>
                </div>   
                <div class="col-md-1"></div>
                </div>
            </a>
        </div>
        <?php  $oldroute=$allocation->zone_name; } ?>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-2 pull-right">
        <button type="button"  class="confirm_a btn btn-primary gradient2" style="display: none">Confirm</button>
    </div>
    <!--<input type="hidden" id="qty_changed" name="qty_changed" value="0" />-->
</form>
<?php }} include __DIR__.'../../footer.php'; ?>