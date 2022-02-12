<?php include __DIR__.'../../header.php';  if(!$this->session->userdata('userid')){ return redirect(base_url()); } else { ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>

<script>
  $(document).ready(function(){      
      
      $(document).on("click", ".receive_service_case", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idservice=$(parentDiv).find('.idservice').val();
             var remark=$(parentDiv).find('.remark').val();
            if(remark){
                 if (confirm('Do you want to Receive??')) {
                    jQuery.ajax({
                        url: "<?php echo base_url('Service/ajax_receive_service_case') ?>",
                        method:"POST",
                        data:{idservice:idservice,remark:remark},
                        success:function(data){
                            $(parentDiv).remove();
                            swal('🙂 Service handset received and closed', 'Case Closed', 'success');
                        }
                    });
                    }
                }
        });
        $(document).on("change", "#type", function(event) {                      
            var type = +$('#type').val();
            var branch = +$('#idbranch').val();
            $.ajax({
                url:"<?php echo base_url() ?>Service/ajax_get_pending_service_stock_report",
                method:"POST",
                data:{ type:type,idbranch: branch},
                success:function(data)
                {
                    $(".export").show();
                    $("#stock_data").html(data);
                }
            });           
        });
         $(document).on("click", ".check_doa", function(event) {  
         alert("dsfg");
            el=$(this).parent();
            $(el).find('.doa_with').show();
            $(el).find('.close_with').hide();
        });
         $(document).on("click", ".check_close", function(event) {                      
            el=$(this).parent();
            $(el).find('.doa_with').hide();
            $(el).find('.close_with').show();
        });
        
        $(document).on("click", ".btn_receive_case", function(event) {            
             var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idservice=$(parentDiv).find('.details').show();
            $(parentDiv).find('.btn_make_doa').hide();
            $(this).hide();
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
</style>
<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Pending Service Stock</h3>
    </center>
</div>
<div class="clearfix"></div><br>  
 <input type="hidden" value="<?php echo $_SESSION['idbranch']; ?>" name="idbranch" id="idbranch">   

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
       
       <div class="col-md-10"></div>
        <div class="col-md-1 col-sm-2">
            <button class="btn btn-primary btn-sm gradient2 export pull-right" onclick="javascript:xport.toCSV('service_data');" style="margin-top: 6px;line-height: unset; "><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><br>
        <div style="height: 650px;" id="stock_data">
            <table id="service_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">           
              <?php  if($force_doa){ ?>            
                <thead class="fixedelementtop">                    
                    <th>Case ID</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Replaced IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th style="width:20%">Action</th>
                    
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($force_doa as $stock){ ?>
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
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>    
<!--                        <td>
                        </td>-->
                        <td>  
                            <a href="<?php echo base_url('Service/force_doa_clerance/'.$stock->id_service) ?>" class="btn btn-primary gradient2 btn-sm btn_make_doa" style="margin-top: 6px;line-height: unset;"><center>DOA</center></a>&nbsp;&nbsp;&nbsp;                            
                        </td>

                        
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
            
        <?php } ?>
            </table>
        </div>
    </div>
<?php } include __DIR__.'../../footer.php'; ?>