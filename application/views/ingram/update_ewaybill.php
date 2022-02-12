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
        <div class="clearfix"></div>
        <div class="thumbnail" style="padding: 0; margin-top: 10px">
            <table id="branch_data" class="table table-condensed table-bordered table-striped table-hover " style="margin-bottom: 0; font-size: 13px">
                <thead>
                    <th>Sr</th>
                    <th>Date</th>   
                    <th>OrderNumber</th>                    
                    <th>Customer Name / Number </th>
                    <th>Product</th>
                    <th>Quantity</th>                     
                    <th>Status</th>
                    <th>EwayBill</th>
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
                        <td><?php echo $po->customer_fname." ".$po->customer_lname." / ".$po->customer_contact ?></td>
                        <td><?php echo $po->sku ?></td>
                        <td><?php echo $po->qty?></td>
                                                             
                        <td><?php 
                            if($po->ingram_status==4){
                                echo "Picked";
                            }
                        ?></td>
                         <td><?php 
                            if($po->eway_billpath!=NULL){ ?>
                             <a target="_blank" href="<?php echo base_url($po->eway_billpath) ?>" class="" style="color:blue" ><i class="fa fa-download fa-lg"></i> Download </a>
                            <?php }else{
                                echo "-";
                            }
                        ?></td>
                        <td>
                        <?php if($po->ingram_status==4){ ?>
                                <!--<a href="#" id_vendor_po="<?php echo $po->id_vendor_po ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light pick_order">Pack and Dispatch</a>-->    
                                <a class="btn btn-sm btn-info gradient_info waves-effect waves-light" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                                Upload E-way Bill
                                </a>
                                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <?php echo form_open_multipart('', array('id' => 'eway')) ?>  
                                                    <div class="modal-body">
                                                        <div class="thumbnail">
                                                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Upload E-way Bill</h4></center><hr>
                                                            <div class="col-md-12" style="padding: 10px;">                                                                                                                           
                                                                <div class="col-md-3"><b>Upload File </b></div>
                                                                 <div class="col-md-9">
                                                                     <input type="hidden" name="id_sale_token" id="id_sale_token" value="<?php echo $po->id_sale_token ?>" />
                                                                     <input type="file" name="uploadfile" accept="application/pdf" id="uploadfile" required=""><br>
                                                                     <small style="color:red;">*Pdf file only</small>
                                                                </div>
                                                                <div class="clearfix"></div><br>
                                                            <div class="clearfix"></div><hr>                                                                                                                        
                                                        </div><div class="clearfix"></div>
                                                            
                                                        </div><div class="clearfix"></div>
                                                        <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                            <button type="submit" value="<?php echo $po->id_sale_token ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Ingram_Api/save_eway_bill') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button>
                                                            <div class="clearfix"></div>
                                                        </div>
                                                    <?php echo form_close(); ?>
                                                </div>
                                            </div>
                                        </div>
                                
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