<?php include __DIR__.'../../header.php';  
if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<script>
$(document).ready(function(){
$(document).on("click", ".remove_allocation_btn", function(event) {
    var ce = $(this);
    var id=$(this).val();
    var parentDiv=$(ce).closest('td').parent('tr');
    var idstock_allocation=parentDiv.find('.idstock_allocation').val();
    var id_stock_allocation_data=parentDiv.find('.idallocation_data').val();
    
    jQuery.ajax({
        url: "<?php echo base_url('Stock_allocation/remove_allocated_stock_data') ?>",
        method:"POST",
        data:{id_stock_allocation_data:id_stock_allocation_data,idstock_allocation:idstock_allocation},
        success:function(data){
            $(parentDiv).remove();
            alert('Allocated stock removed');
        }
    });
});
$(document).on("click", ".remove_allocation", function(event) {
    if (confirm('Do you want to delete this allocation!!')) {
        var id=$(this).val();
        parentDiv=$(this).parent().parent().parent();
        jQuery.ajax({
            url: "<?php echo base_url('Stock_allocation/delete_branch_allocation') ?>",
            method:"POST",
            data:{idstock_allocation:id,allocation_type:0},
             dataType: 'json',
            success:function(data){
                if (data.data === 'success') {
                        $(parentDiv).fadeOut(); 
                        alert("Allocation deleted successfully!!");                         
                        $(parentDiv).remove();
                } else if (data.data === "fail") {
                    alert("Fail to delete allocation!! ")
                }
            }    
        });
    }
});
    $('.stockallocation').click(function(){
        var idstock = $(this).find('.idstock').val();
        var date = $(this).find('.date').val();
        var bname = $(this).find('.bname').val();
        var entry_time = $(this).find('.entry_time').val();
        var idbranch = $(this).find('.idbranch').val();
        var allocation_type = $(this).find('.allocation_type').val();
        $.ajax({
            url:"<?php echo base_url() ?>Stock_allocation/ajax_get_stock_allocation_data_byid",
            method:"POST",
            data:{idstock : idstock, date:date, bname: bname, entry_time: entry_time,idbranch:idbranch,allocation_type:allocation_type,isconfirm:0},
            success:function(data)
            {
                $("#allocated_stock_data").html(data);
                 $("html, body").animate({scrollTop: 0}, 100);
            }
        });
    });
    $(document).on("click", ".allocationform", function (event) {
            event.preventDefault();
            if (confirm('Do you want to Update the allocation!!')) {
            var serialized = $('.allocation_form').serialize();
            $.ajax({
                url: "<?php echo base_url() ?>Stock_allocation/save_branch_allocation",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if (data.data === 'success') {
                        alert("Allocation submitted successfully!!");
                        location.reload();
                    } else if (data.data === "fail") {
                        alert("Fail to save allocation!! ")
                    } else {
                        alert("Select at least one model !! ")
                    }
                }
            });
            }
        });
        
        $(document).on("click", ".delete_allocation_data", function (event) {
        
                if (confirm('Do you want to Delete Allocation Data!!')) {
                    var variant = $(this).attr("variant"); 
                    var idgodown = $(this).attr("godown"); 
                    var allocation_data_id = $(this).attr("allocation_data_id"); 
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/remove_allocation_data",
                    method: "POST",                
                    data: {variant: variant,idgodown:idgodown,allocation_data_id:allocation_data_id},        
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                            $(element).parent().parent().fadeOut();   
                            $(element).parent().parent().remove();
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
<form>
    <div>
        <?php foreach ($stock_allocation as $allocation) { ?>
        <div class="col-md-3" style="padding: 5px;">
            <a class="panel waves-effect waves-ripple waves-block stockallocation" style="padding: 5px; margin: 0">
                <div class="col-md-2 purple-text" style="font-family: Kurale; font-size: 20px;color: #0e10aa !important;"><?php echo $allocation->id_stock_allocation ?></div>                
                <div class="col-md-1"></div>
                <div class="col-md-9" style="font-size: 18px;text-align: center">
                    <?php echo $allocation->branch_name ?>
                </div>                
                <div class="clearfix"></div><hr style="margin: 0px !important;">
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
                <input type="hidden" value="<?php echo $allocation->id_stock_allocation ?>" id="idstock" class="idstock" name="idstock" />
                <input type="hidden" class="date" value="<?php echo $allocation->date ?>" />
                <input type="hidden" class="bname" value="<?php echo $allocation->branch_name ?>" />
                <input type="hidden" class="entry_time" value="<?php echo $allocation->entry_time ?>" />  
                <input type="hidden" class="idbranch" value="<?php echo $allocation->idbranch ?>" /> 
                <input type="hidden" class="allocation_type" value="<?php echo $allocation->allocation_type ?>" /> 
                <?php if($allocation->allocation_type==0){ ?>                
                <div class="col-md-2" style="font-size: 18px;">                    
                        <button type="button" class="remove_allocation" value="<?php echo $allocation->id_stock_allocation ?>" name="idstockallocation" style="margin: 0"><i class="fa fa-trash-o" style="color:red;"></i></button>                    
                </div>
                <div class="clearfix"></div>
                <?php } ?>
            </a>
        </div>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
    
    <!--<input type="hidden" id="qty_changed" name="qty_changed" value="0" />-->
</form>
<?php }} include __DIR__.'../../footer.php'; ?>