<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $(document).on("click", ".btn_refund", function(event) {
            var ce = $(this);
            var idrow = $(this).val();
            var parentDiv=$(ce).closest('td').parent('tr');            
//            var idservice=$(parentDiv).find('.details').show();
            $(parentDiv).find('.cancel_block').html('<div style="padding: 2px;width: 250px">\n\
                                    <input type="text" class="form-control input-sm cancel_remark" id="cancel_remark" name="cancel_remark" placeholder="Enter cancel reason"/>\n\
                                </div>\n\
                                <div class="pull-right" style="padding: 2px">\n\
                                    <button value="'+idrow+'" class="btn btn-danger btn-sm cancel_submit" ><span class="fa fa-repeat"></span> Cancel</button>\n\
                                </div><div class="clearfix"></div>');
            $(this).hide();
        });
        
        $(document).on("click", ".cancel_submit", function(event) {
            var ce = $(this);
            var parentDiv=$(ce).closest('td').parent('tr');            
            var idrow=$(this).val();
            var cancel_remark=$(parentDiv).find('.cancel_remark').val();
//            alert(ref_amount);
            if(cancel_remark != ''){
                swal({
                    title: "Do you want to cancel token?",
                    text: "Click on cancel to proceed",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#E84848',
                    confirmButtonText: 'Yes, Cancel it!',
                    closeOnConfirm: false,
                },
                function(){
                    jQuery.ajax({
                        url: "<?php echo base_url('Sale/ajax_cancel_token') ?>",
                        method:"POST",
                        dataType:"json",
                        data:{idrow:idrow,cancel_remark:cancel_remark},
                        success:function(data){
                            if(data.result == 'Success'){
                                swal('🙂 Token Cancelled', 'Token cancellation done', 'success');
                                $(parentDiv).remove();
                            }else{
                                swal('Failed to cancel token', 'Try again', 'warning');
                                $(parentDiv).find('.btn_refund').show();
                                $(parentDiv).find('.cancel_block').html('');
                            }
                        }
                    });
                    swal("Cancellation done!", "Token removed from list!", "success");
               });
           }else{
                swal('🙂 Cancellation reason is mandatory', 'Enter cancel reason', 'warning');
           }
        });
        
        $('#status, #from, #to').change(function(){
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        var idbranch = $('#idbranch').val();
        $.ajax({
            url: "<?php echo base_url() ?>Ingram_Api/ajax_get_branch_order_report",
            method: "POST",
            data:{status: status, from: from, to: to,idbranch:idbranch},
            success: function (data)
            {
                $('.order_details').html(data);
            }
        });
    });
        
    }); 
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-image-filter-tilt-shift fa-lg"></span> Ingram Orders Report </h3></center></div>
<div class="clearfix"></div><hr>
<div class="col-md-2">
        <select class="chosen-select form-control input-sm" name="idbranch" id="idbranch">
            <option value="">Select Branch</option>
            <option value="0">All Branch</option>
            <?php foreach ($branch_data as $branch){ ?>
            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
    </div>
<div class="col-md-3">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Status
                    </a>
                </div>
                <select class="chosen-select form-control input-sm" id="status">
                    <option value="">Select status</option>
                    <option value="1">Pending for Approval</option>
                    <option value="2">Order Placed - In Process</option>                                                                                
                    <option value="3">Rejected</option>
                    <option value="4">Picked for Dispatch at Ingram</option>
                    <option value="5">Packed and Dispatched</option>
                    <option value="6">Delivered</option>
                    <option value="7">Returned/Declined by Customer</option>
                    <option value="8">Refund</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="from" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date from">
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="to" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date to">
        </div>
        
        <div class="col-md-1">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('ingram_branch_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div><div class="clearfix"></div><br>
<table class="table table-condensed table-bordered" id="ingram_branch_data">
    <thead>
        <th>Sr</th>
        <th>Order Id</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Sales Promoter</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Product</th>
        <th>PartNumber</th>
        <th>Model</th>
        <th>Quantity</th>  
        <th>Basic</th>
        <th>Discount</th>
        <th>Total</th>                
        <th>Status</th>        
        <th>Info</th>
    </thead>
    <tbody class="order_details">
        <?php $i=1; foreach($sale_token_data as $sale_token){ 
//                    die('<pre>'.print_r($sale_token_data,1).'</pre>');                 
                    ?>
        <tr class="recon">
            <td><?php echo $i; ?></td>
            <td>
                <b>ODR-<?php echo $sale_token->id_sale_token ?> </b>
            </td>
          <td><?php echo date('d-m-Y', strtotime($sale_token->date)) ?></td>
            <td><?php echo $sale_token->branch_name ?></td>
            <td><?php echo $sale_token->user_name ?></td>
            <td><?php echo $sale_token->customer_fname.' '.$sale_token->customer_lname ?></td>
            <td><?php echo $sale_token->customer_contact ?></td>
            <td><?php echo $sale_token->sku ?></td>
            <td><?php echo $sale_token->part_number ?></td>
            <td><?php echo $sale_token->full_name ?></td>
            <td><?php echo $sale_token->qty?></td>
            <td><?php echo $sale_token->basic_total ?></td>
            <td><?php echo $sale_token->discount_total ?></td>
            <td><?php echo $sale_token->final_total ?></td>        
            <td><?php 
                            if($sale_token->ingram_status==1){
                                echo "Pending for Approval";
                            }elseif($sale_token->ingram_status==2){
                               echo "Order Placed - In Process";
                            }else if($sale_token->ingram_status==3){
                                 echo "Rejected";
                            }elseif($sale_token->ingram_status==4){
                                echo "Picked for Dispatch at Ingram";
                            }elseif($sale_token->ingram_status==5){
                                echo "Packed and Dispatched";
                            }elseif($sale_token->ingram_status==6){
                                if($sale_token->deliver_at==1){
                                    echo "Received by Customer";
                                }else{
                                    echo "Received at Branch";
                                }
                            }elseif($sale_token->ingram_status==7){
                                echo "Returned/Declined by Customer";
                            }elseif($sale_token->ingram_status==8){
                                echo "Refund";
                            }elseif($sale_token->ingram_status==9){
                                echo "Closed";
                            }
                            
                        ?></td>
           
                
                <div class="cancel_block"></div>
            </td>
            <td><a href="<?php echo base_url()?>Ingram_Api/order_deatils/<?php echo $sale_token->id_sale_token ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
        <?php $i++; } ?>
    </tbody>
</table>
<?php include __DIR__ . '../../footer.php'; ?>