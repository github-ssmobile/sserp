<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
        $(document).on("click", ".btn_refund", function(event) {
            var ce = $(this);
            var idrow = $(this).val();
            var parentDiv=$(ce).closest('td').parent('tr');            
//            var idservice=$(parentDiv).find('.details').show();
            $(parentDiv).find('.cancel_block').html('<div style="padding: 2px;width: 250px">\n\
                                    \n\
                                <select class="form-control input-sm cancel_remark" id="cancel_remark" name="cancel_remark" required><option value="">Select Remark</option><option value="Product Taken Back">Product Taken Back</option><option value="Wrong Token">Wrong Token</option><option value="Wrong Model">Wrong Model</option></select></div>\n\
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
    }); 
</script>
<div class="col-md-10"><center><h3 style="margin: 10px"><span class="mdi mdi-image-filter-tilt-shift fa-lg"></span> Sale Token </h3></center></div>
<div class="clearfix"></div><hr>
<table class="table table-condensed table-bordered" id="cash_closure_data">
    <thead>
        <th>Sr</th>
        <th>Token Id</th>
        <th>Date</th>
        <th>Sales Promoter</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>Basic</th>
        <th>Discount</th>
        <th>Total</th>
        <th>Entry Time</th>
        <th>Days Diff</th>
        <!--<th>Invoice</th>-->
        <th>Cancel</th>
        <!--<th>Print</th>-->
    </thead>
    <tbody class="cash_closure_entries">
        <?php $i=1; foreach($sale_token_data as $sale_token){ ?>
        <tr class="recon">
            <td><?php echo $i; ?></td>
            <td>
                <?php if($sale_token->corporate_sale == 0){ ?>
                    <a href="<?php echo base_url()?>Sale/index?idtoken=<?php echo $sale_token->id_sale_token ?>" class="btn btn-sm btn-block waves-effect black-text claim_btn">Token/<?php echo $sale_token->id_sale_token ?> <i class="mdi mdi-tag-text-outline pull-right"></i></a>
                <?php }else{ ?>
                    <a href="<?php echo base_url()?>Sale/online_sale?idtoken=<?php echo $sale_token->id_sale_token ?>" class="btn btn-sm btn-block waves-effect black-text claim_btn">Token/<?php echo $sale_token->id_sale_token ?> <i class="mdi mdi-tag-text-outline pull-right"></i></a>
                <?php } ?>
            </td>
            <td><?php echo date('d-m-Y', strtotime($sale_token->date)) ?></td>
            <td><?php echo $sale_token->user_name ?></td>
            <td><?php echo $sale_token->customer_fname.' '.$sale_token->customer_lname ?></td>
            <td><?php echo $sale_token->customer_contact ?></td>
            <td><?php echo $sale_token->basic_total ?></td>
            <td><?php echo $sale_token->discount_total ?></td>
            <td><?php echo $sale_token->final_total ?></td>
            <td><?php echo $sale_token->entry_time ?></td>
            <td><?php $now = time(); // or your date as well
                $your_date = strtotime($sale_token->date);
                $datediff = $now - $your_date;
                echo round($datediff / (60 * 60 * 24)); ?></td>
            <td>
                <button value="<?php echo $sale_token->id_sale_token ?>" class="btn btn-danger btn-sm waves-effect btn_refund"><i class="fa fa-reply"></i> Cancel</button>
                <div class="cancel_block"></div>
            </td>
            <!--<td><a href="<?php echo base_url()?>Payment/advance_booking_received_receipt/<?php echo $sale_token->id_sale_token ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
        </tr>
        <?php $i++; } ?>
    </tbody>
</table>
<?php include __DIR__ . '../../footer.php'; ?>