<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart fa-lg"></span> <?php echo $tab_active; ?></center></div>
<div class="clearfix"></div><hr>
<script>
$(document).ready(function () {
    $('#status, #from, #to').change(function(){
        var status = $('#status').val();
        var from = $('#from').val();
        var to = $('#to').val();
        $.ajax({
            url: "<?php echo base_url() ?>Ingram_Api/ajax_get_purchase_order_data",
            method: "POST",
            data:{status: status, from: from, to: to},
            success: function (data)
            {
                $('#po_report').html(data);
            }
        });
    });
    
    $(document).on("click", ".pick_order", function (event) {
        
                if (confirm('Do you want to Pick this Order!!')) {
                    var id_vendor_po = $(this).attr("id_vendor_po");
                   
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Ingram_Api/pick_order",
                    method: "POST",                
                    data: {id_vendor_po: id_vendor_po},                
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                           alert("Order Picked Successfully!");
                            $(element).parent().parent().parent().fadeOut();   
                            $(element).parent().parent().parent().remove();
                        }else if(data.data === "fail"){
                            alert("Fail to Pick!! ");
                        }

                    }
                });
            }
        });
        
        $(document).on("click", ".order_received", function (event) {
        
                if (confirm('Do you want to Mark this Order as Received!!')) {
                    var id_vendor_po = $(this).attr("id_vendor_po");
                    var id_sale_token = $(this).attr("id_sale_token");
                   
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Ingram_Api/receive_order",
                    method: "POST",                
                    data: {id_vendor_po: id_vendor_po,id_sale_token:id_sale_token},                
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                           alert("Order Received Successfully!");
                            $(element).parent().parent().parent().fadeOut();   
                            $(element).parent().parent().parent().remove();
                        }else if(data.data === "fail"){
                            alert("Fail to Receive!! ");
                        }

                    }
                });
            }
        });
    
    $(document).on("click", ".cancel_picked", function (event) {
        
                if (confirm('Do you want to Cancel this picked Order!!')) {
                    var parent = $($(this)).closest('td').parent('tr');
                    var id_sale_token = parent.find(".id_sale_token").val();
                    var po_number = parent.find(".po_number").val();                    
                    var id_vendor_po = $(this).attr("id_vendor_po");
                   
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Ingram_Api/cancel_pick_order",
                    method: "POST",                
                    data: {id_vendor_po: id_vendor_po,id_sale_token:id_sale_token,po_number:po_number},                
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                           alert("Order Cancelled Successfully! Please check Ready to Pick Order.");
                            $(element).parent().parent().parent().fadeOut();   
                            $(element).parent().parent().parent().remove();
                        }else if(data.data === "fail"){
                            alert("Fail to Cancel!! ");
                        }

                    }
                });
            }
        });
    
    
});
</script>
<div class="" style="padding: 0; margin: 0;overflow: auto">
    <div id="purchase" style="padding: 10px; margin: 0">
        
        <div class="col-md-2">
            <input type="text" name="search" id="from" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date from">
        </div>
        <div class="col-md-2">
            <input type="text" name="search" id="to" class="form-control input-sm datepick" onfocus="blur()" placeholder="Date to">
        </div>
        <div class="col-md-4">
           
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div><div class="clearfix"></div>
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover " style="margin-bottom: 0; font-size: 13px">
                <thead>
                    <th>Sr</th>
                    <th>Date</th>   
                    <th>OrderNumber</th>                    
                    <th>Branch</th>                    
                    <th>Invoice No</th>                    
                    <th>Customer Name / Number </th>
                    <th>Product</th>
                    <th>PartNumber</th>
                    <th>Model</th>
                    <th>Quantity</th>                     
                     <th>Status</th>
                     <?php if(count($purchase_order)!=0){?>
                    <?php if($purchase_order[0]->ingram_status==6){ ?>
                     <th>Received date</th>
                  <?php   }elseif($purchase_order[0]->ingram_status==7){ ?>
                         <th>Return Reason</th>
                         <th>Return Date</th>
                     <?php  } }?>
                   
                    <th>Action</th>
                </thead>
                <tbody id="po_report" class="data_1">
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
                        <td><?php echo $po->id_sale_token ?></td>    
                        <td><?php echo $po->branch_name ?></td>    
                        <td><?php echo $po->inv_no ?></td>    
                        <td><?php echo $po->customer_fname." ".$po->customer_lname." / ".$po->customer_contact ?></td>
                        <td><?php echo $po->sku ?></td>
                        <td><?php echo $po->part_number ?></td>
                        <td><?php echo $po->full_name ?></td>
                        <td><?php echo $po->qty?></td>
                                                             
                        <td> <center><?php 
                            if($po->ingram_status==1){
                                echo "Pending For Approval";
                            }elseif($po->ingram_status==2){
                                echo "Ready to Pick";
                            }elseif($po->ingram_status==3){
                                echo "Rejected";
                            }elseif($po->ingram_status==4){
                                ?><a target="_blank" href="<?php echo base_url('Ingram_Api/po_invoice_print/'.$po->id_sale_token) ?>" class="btn btn-sm" style="color: blue"><i class="fa fa-download fa-lg"></i> Invoice</a>
                                <?php if($po->eway_billpath!=NULL){ ?>
                                 <a target="_blank" href="<?php echo base_url($po->eway_billpath) ?>" class="btn btn-sm" style="color:blue" ><i class="fa fa-download fa-lg"></i>  Eway-Bill </a>
                                <?php } 
                            }
                            elseif($po->ingram_status==5){  ?>
                                <a target="_blank"  href="<?php echo base_url('Ingram_Api/po_invoice_print/'.$po->id_sale_token) ?>" class="btn btn-sm" style="color: blue"> <i class="fa fa-download fa-lg"></i>  Invoice</a>
                                <?php if($po->eway_billpath!=NULL){ ?>
                                 <a target="_blank" href="<?php echo base_url($po->eway_billpath) ?>" class="btn btn-sm" style="color:blue" ><i class="fa fa-download fa-lg"></i>  Eway-Bill </a>
                            <?php } ?>
                            <?php }elseif($po->ingram_status==6){
                                echo "Completed";
                            }elseif($po->ingram_status==7){
                                echo "Returned";
                            }elseif($sale_token->ingram_status==8){
                                echo "Refund";
                            }elseif($sale_token->ingram_status==9){
                                echo "Received by Branch";
                            }
                            elseif($sale_token->ingram_status==10){
                                echo "Delivered to customer by branch";
                            }
                        ?></center></td>
                        <?php if($po->ingram_status==6){ ?>
                        <td><?php echo date('Y-m-d', strtotime($po->received_date))?></td>
                        <?php   }elseif($po->ingram_status==7){ ?>
                               <td><?php echo $po->return_reason?></td>
                               <td><?php echo date('Y-m-d', strtotime($po->return_date))?></td>
                         <?php  } ?>
                        
                        <td><center>
                            <input type="hidden" class="id_sale_token" name="id_sale_token" id="id_sale_token" value="<?php echo $po->id_sale_token ?>" />                            
                           <?php if($po->ingram_status==1){ ?>
                                -
                            <?php }elseif($po->ingram_status==2){ ?>
                                <a target="_blank" href="<?php echo base_url('Ingram_Api/pick_order/'.$po->id_sale_token) ?>" class="btn btn-sm btn-info  waves-effect waves-light">Pick and Verify</a>   
                                
                            <?php }elseif($po->ingram_status==3){ ?>
                                -
                            <?php }elseif($po->ingram_status==4){ ?>
                                <!--<a href="#" id_vendor_po="<?php echo $po->id_vendor_po ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light pick_order">Pack and Dispatch</a>-->    
                                <a class="btn btn-sm btn-info gradient_info waves-effect waves-light" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                                Pack and Dispatch
                                </a>
                                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form>
                                                    <div class="modal-body">
                                                        <div class="thumbnail">
                                                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Pack Order</h4></center><hr>
                                                            <label class="col-md-3  col-md-offset-1">AWB No</label>
                                                            <div class="col-md-7">
                                                                <input required="" class="form-control input-sm" name="awb_no" id="awb_no" placeholder="AWB NO" />
                                                            </div><div class="clearfix"></div><br>
                                                            <label class="col-md-3 col-md-offset-1">Courier Name</label>
                                                            <div class="col-md-7">
                                                                <select class="select form-control" name="courier_name" id="courier_name" required="">
                                                                    <option value="Blue Dart">Blue Dart</option>                                                                       
                                                                </select>                                                                
                                                            </div><div class="clearfix"></div><br>
                                                            <label class="col-md-3 col-md-offset-1">Internal Comments</label>
                                                            <div class="col-md-7">
                                                                <textarea class="form-control input-sm" name="internal_comments"  rows="2"  placeholder="Internal Comments" ></textarea>
                                                            </div><div class="clearfix"></div><br>
                                                            <label class="col-md-3 col-md-offset-1">Customer Comments</label>
                                                            <div class="col-md-7">
                                                                <textarea class="form-control input-sm" name="customer_comments"  rows="2"  placeholder="Customer Comments" ></textarea>
                                                                <input type="hidden" name="id_sale_token" id="id_sale_token" value="<?php echo $po->id_sale_token ?>" />
                                                                <input type="hidden"  name="po_number" id="po_number" value="<?php echo $po->token_uid ?>" />
                                                            </div><div class="clearfix"></div><br>                                                            
                                                            <div class="clearfix"></div>
                                                        </div>
                                                        <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                        <button type="submit" value="<?php echo $po->id_sale_token ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Ingram_Api/pack_stock') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                
                                <a href="#" class=" cancel_picked btn btn-sm btn-danger  waves-effect waves-light">Cancel</a>                                
                            <?php }elseif($po->ingram_status==5){ ?>
                                <!--<a href="#" id_vendor_po="<?php echo $po->id_vendor_po ?>" id_sale_token="<?php echo $po->id_sale_token ?>"  class="order_received btn btn-sm btn-info  waves-effect waves-light">Received</a>                                \-->
                                <a class="btn btn-sm btn-info gradient_info waves-effect waves-light" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                                Received
                                </a>
                                <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form>
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Order Received </h4></center><hr>
                                                <label class="col-md-3  col-md-offset-1">Received Date</label>
                                                <div class="col-md-7">
                                                    <div class="input-group-btn">
                                                        <input type="text" name="receiveddate" id="receiveddate" class="form-control input-sm datepick" placeholder="Received Date">
                                                        <input type="hidden" name="id_sale_token" id="id_sale_token" value="<?php echo $po->id_sale_token ?>" />
                                                        <input type="hidden"  id="po_number" value="<?php echo $po->token_uid ?>" />
                                                    </div>
                                                </div><div class="clearfix"></div><br>
                                            </div>
                                            <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                            <button type="submit" value="<?php echo $po->id_sale_token ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Ingram_Api/receive_order') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                                <!--<a href="#" id_vendor_po="<?php echo $po->id_vendor_po ?>" id_sale_token="<?php echo $po->id_sale_token ?>"   class=" order_declined btn btn-sm btn-danger  waves-effect waves-light">Returned</a>-->    
                                <a class="btn btn-sm btn-danger  waves-effect waves-light" href="" data-toggle="modal" data-target="#return<?php echo $i ?>" style="margin: 0" >
                                                Returned
                                </a>
                                <div class="modal fade" id="return<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form>
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Order Returned </h4></center><hr>
                                                <label class="col-md-3 col-md-offset-1">Return Reason/Comments</label>
                                                <div class="col-md-7">
                                                    <textarea class="form-control input-sm" name="return_reason"  rows="2"  placeholder="Return Reason/Comments" ></textarea>
                                                    <input type="hidden"  name="id_sale_token" id="id_sale_token" value="<?php echo $po->id_sale_token ?>" />
                                                    <input type="hidden"  id="po_number" value="<?php echo $po->token_uid ?>" />
                                                </div><div class="clearfix"></div><br> 
                                            </div>
                                            <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                            <button type="submit" value="<?php echo $po->id_sale_token ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Ingram_Api/returned_stock') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php }else{ ?>
                                <a target="_blank" href="<?php echo base_url('Ingram_Api/process_order/'.$po->id_sale_token) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a>
                            <?php } ?>
                        
                            </td>
                    </tr>
                    <?php $i++; } ?>                   
                
                 <?php } ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>