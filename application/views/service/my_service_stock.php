<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<style>
.floatingButtonWrap {
    display: block;
    position: fixed;
    bottom: 25px;
    right: 30px;
    z-index: 999;
}
.floatingButtonInner {
    position: relative;
}
.floatingButton {
    display: block;
    width: 55px;
    height: 55px;
    text-align: center;
    border: 1px solid #003399;
    background-color: #fff;
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.3);
    /*background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);*/
    /*background-image: linear-gradient(to right top, #e9d82e, #f2bc00, #f99f00, #fd7e00, #ff5800);*/
    /*background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);*/
    color: #003399;
    line-height: 65px;
    position: absolute;
    border-radius: 50% 50%;
    bottom: 0px;
    right: 0px;
    /*border: 5px solid #176391;*/
    /* opacity: 0.3; */
    opacity: 1;
    transition: all 0.4s;
}
.floatingButton .fas {
    font-size: 25px !important;
}
.floatingButton.open,
.floatingButton:hover,
.floatingButton:focus,
.floatingButton:active {
    /*opacity: 1;*/
    color: #003399;
    box-shadow: 0px 15px 20px rgba(0, 51, 153, 0.4);
    /*transform: translateY(-7px);*/
}
.floatingButton .fas {
    transform: rotate(0deg);
    transition: all 0.4s;
}
.floatingButton.open .fas {
    transform: rotate(270deg);
}
.floatingMenu {
    position: absolute;
    bottom: 30px;
    right: 0px;
    /* width: 200px; */
    display: none;
}
/*.floatingMenu li {
    width: 100%;
    float: right;
    list-style: none;
    text-align: right;
    margin-bottom: 5px;
}*/
</style>
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
<script>
  $(document).on("click", ".sent_to_local", function(event) {
        var ce = $(this);
        var parentDiv=$(ce).closest('td').parent('tr');            
        var idservice=$(parentDiv).find('.idservice').val();
        var imei=$(parentDiv).find('.imei').val();
        var care=$(parentDiv).find('.care').val();
        var idvariant=$(parentDiv).find('.idvariant').val();
        if(care){
            if (confirm('Do you want to send??')) {
                jQuery.ajax({
                    url: "<?php echo base_url('Service/ajax_sent_to_local') ?>",
                    method:"POST",
                    data:{idservice:idservice,care:care,imei:imei,idvariant:idvariant},
                    success:function(data){
                        $(parentDiv).remove();
                        swal('🙂 Case sent to Local Care', 'Transfer Done', 'success');
                    }
                });
            }
        }
    });
    $(document).on("click", ".btn_care", function(event) {            
         var ce = $(this);
        var parentDiv=$(ce).closest('td').parent('tr');            
        var idservice=$(parentDiv).find('.details').show();
        $(this).hide();
    });
    
    var products = [];
    $(document).ready(function(){
        $('.iddispatchtype').change(function (){
            var dispatch_type = $('.iddispatchtype option:selected').text();
            $('#dispatch_type').val(dispatch_type);
        });
   
        $('.idtvendors').change(function (){
             var courier_name = $('.idtvendors option:selected').text();
             $('#courier_name').val(courier_name);
        });
        
        $('#save_service_send_to_ho').on('click',function () {
            if(!confirm('Do you want to submit')){
                return false;
            }
        });
        $('.sel_product').on('click',function () {
            var id = $(this).val();
            if($(this).prop("checked") === true){
                if (products.includes(id) === false){
                    products.push(id);
                }else{
                    alert('duplicate product selected');
                    return false;
                }
            }else if($(this).prop("checked") === false){
                products = jQuery.grep(products, function(value) { return value !== id; });
            }
            $('#idservices').val(products);
        });
    });
        
</script>
<div class="col-md-10">
    <center><h3><span class="mdi mdi-cellphone-iphone fa-lg"></span> My Service Stock</h3></center>
</div><div class="clearfix"></div><br>
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
<div class="col-md-6">
    <div id="count_1" class="text-info"></div>
</div><div class="clearfix"></div><br>
<form>
<div class="thumbnail" style="overflow: auto;padding: 0">
<!--    <div class="col-md-1"></div>
    <div class="col-md-1 col-sm-2">
        <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('stock_data');" style="margin-top: 6px;line-height: unset;"><span class="fa fa-file-excel-o"></span> Export</button>
    </div>
    <div class="clearfix"></div><br>-->
    <div style="height: 650px;">
        <table id="stock_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
          <?php  if($service_stock){ ?>            
            <thead class="fixedelementtop">                    
                <th>Case ID</th>
                <th>Type</th>
                <th>Branch</th>
                <th>Inward Date</th>                    
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Product name</th>
                <th>IMEI/SRNO</th>
                <th>Problem</th>
                <th>Remark</th>                    
                <th>Customer</th>
                <th>Status</th>
                <th>Send to Local</th>
                <th>Send to Ho</th>
                <!--<th><button data-target="#ho_mandate" data-toggle="collapse" class="btn btn-primary btn-sm gradient2 export" ><span class="fa fa-send-o"></span> Send to HO</button></th>-->
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach($service_stock as $stock){
                    if($stock->counter_faulty){ ?>
                <tr>                        
                    <td><?php echo $stock->id_service; ?>
                        <input type="hidden" name="idservice" class="idservice" id="idservice" value="<?php echo $stock->id_service ?>">
                    </td>
                    <td>Counter Faulty</td>
                    <td><?php echo $stock->branch_name; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                    <td></td>
                    <td></td>
                    <td><?php echo $stock->full_name ?></td>
                    <td>
                        <input type="hidden" name="imei" class="imei" id="imei" value="<?php echo $stock->imei ?>">
                        <input type="hidden" name="idvariant" class="idvariant" id="idvariant" value="<?php echo $stock->idvariant ?>">
                        <?php echo $stock->imei ?></td>
                    <td><?php echo $stock->problem ?></td>
                    <td><?php echo $stock->remark ?></td>
                    <td></td>
                    <td><?php echo $stock->delivery_status ?></td>
                    <?php if($stock->counter_faulty_approval){  ?>
                    <td>
                        <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm btn-danger btn_care">Send to Care</button>
                        <div class="details" style="display:none; width: 300px">
                            Care Center Name
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm care" id="care" name="care" placeholder="Care center name"/>
                            </div> 
                            <div class="col-md-2">
                                <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 sent_to_local" ><span class="fa fa-send-o"></span> Send</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </td>
                    <td>
                        <label class="form-check-label btn btn-sm btn-primary" for="checkrow<?php echo $stock->id_service ?>">
                            <input class="hide_checkbox sel_product" type="checkbox" name="checkrow[]" id="checkrow<?php echo $stock->id_service ?>" value="<?php echo $stock->id_service; ?>">
                            Send to HO <i class="fa fa-bank"></i> 
                        </label>
                    </td>
                    <?php }else{ ?>
                    <td>Pending For Approval</td>
                    <td>Pending For Approval</td>
                    <?php } ?>
                </tr>
            <?php }else{ ?>
                <tr>                        
                    <td><?php echo $stock->id_service; ?>
                        <input type="hidden" name="idservice" class="idservice" id="idservice" value="<?php echo $stock->id_service ?>">
                    </td>
                    <td>Sold</td>
                    <td><?php echo $stock->branch_name; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                    <td><?php echo $stock->inv_no ?></td>
                    <td><?php echo $stock->inv_date ?></td>
                    <td><?php echo $stock->full_name ?></td>
                    <td>
                        <input type="hidden" name="imei" class="imei" id="imei" value="<?php echo $stock->imei ?>">
                        <input type="hidden" name="idvariant" class="idvariant" id="idvariant" value="<?php echo $stock->idvariant ?>">
                        <?php echo $stock->imei ?></td>
                    <td><?php echo $stock->problem ?></td>
                    <td><?php echo $stock->remark ?></td>
                    <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                    <td><?php echo $stock->delivery_status ?></td>     
                    <td>
                        <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm btn-danger btn_care">Send to Care</button>
                        <div class="details" style="display:none; width: 300px">
                            Care Center Name
                            <div class="col-md-9">
                                <input type="text" class="form-control input-sm care" id="care" name="care" placeholder="Care center name"/>
                            </div> 
                            <div class="col-md-2">
                                <button idservice="<?php echo $stock->id_service ?>" class="btn btn-primary btn-sm gradient2 sent_to_local" ><span class="fa fa-send-o"></span> Send</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </td>
                    <td>
                        <label class="form-check-label btn btn-sm btn-primary" for="checkrow<?php echo $stock->id_service ?>">
                            <input class="hide_checkbox sel_product" type="checkbox" name="checkrow[]" id="checkrow<?php echo $stock->id_service ?>" value="<?php echo $stock->id_service; ?>">
                            Send to HO <i class="fa fa-bank"></i>
                        </label>
                    </td>
                </tr>
            <?php } $i++; } ?>
            </tbody>
        <?php } ?>
        </table>
    </div>
</div>
<!--<button class="btn btn-primary" id="send_to_ho" style="position: fixed; bottom: 60px; right: 20px; display: none">Send to HO</button>-->
<div class="floatingButtonWrap">
    <div class="floatingButtonInner">
        <a href="#" class="floatingButton">
            <i class="fas pe pe-7s-paper-plane icon-default"></i>
        </a>
        <ul class="floatingMenu">
            <!--<li>-->
                <div class="panel panel-info" style="box-shadow: 4px 4px 10px rgba(0, 51, 153, 0.3);padding-bottom: 0;">
                    <div class="panel-body" style="min-width: 750px">
                        <h4><center style="color: #003399; margin-bottom: 10px"><i class="fa fa-handshake-o"></i> Service - Send to HO</center></h4>
                        <div class="thumbnail">            
                            <div class="col-md-2">Dispatch Type</div>
                            <div class="col-md-4">
                                <select class="select form-control iddispatchtype" required="" name="iddispatchtype" >
                                    <option value="">Select Type</option>
                                    <?php foreach ($dispatch_data as $dispatch){ ?>
                                    <option value="<?php echo $dispatch->id_dispatch_type ?>"><?php echo $dispatch->dispatch_type?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" id="dispatch_type" name="dispatch_type">
                            </div>
                            <div class="col-md-3">Dispatch Date</div>
                            <div class="col-md-3"><input type="text" class="form-control" name="dispatch_date" value="<?php echo $now ?>" readonly=""/></div><div class="clearfix"></div><br>
                            <div class="col-md-2">Courier/ Transport</div>
                            <div class="col-md-4">
                                <select class="select form-control idtvendors" required="" name="idtvendors" >
                                    <option value="">Select Transport Vendor</option>
                                    <?php foreach ($transport_vendor as $tvendors){ ?>
                                    <option value="<?php echo $tvendors->id_transport_vendor ?>"><?php echo $tvendors->transport_vendor_name?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" class="form-control" id="courier_name" name="courier_name" placeholder="Enter Courier/ Transport Name"/>
                            </div>
                                
                            <div class="col-md-3">POP/LR Number</div>
                            <div class="col-md-3"><input type="text" class="form-control" id="po_lr_no" name="po_lr_no" placeholder="Enter POP/LR Number"/></div><div class="clearfix"></div><br>
                            <div class="col-md-2">Send To</div>
                            <div class="col-md-4">
                                <select class="select form-control" required="" name="idwarehouse" >
                                    <?php foreach ($warehouse_data as $warehouse){ ?>
                                    <option value="<?php echo $warehouse->id_branch ?>"><?php echo $warehouse->branch_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">No of Boxes</div>
                            <div class="col-md-3"><input type="text" class="form-control" id="no_of_boxes" name="no_of_boxes" placeholder="No of Boxes" required=""/></div><div class="clearfix"></div><br>
                            <div class="col-md-2">Remark</div>
                            <div class="col-md-10"><input type="text" class="form-control" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                            <div class="col-md-2 pull-right">
                                <input type="hidden" class="hidden" id="idservices" placeholder="Enter Shipment Remark"/>
                                <input type="hidden" name="entry_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                                <input type="hidden" name="idbranch" value="<?php echo $this->session->userdata('idbranch') ?>"/>
                            </div><div class="clearfix"></div>
                        </div>
                        <button class="btn btn-primary pull-right" type="submit" formmethod="POST" id="save_service_send_to_ho" formaction="<?php echo base_url('Service/save_service_send_to_ho')?>">Send <span class="fa fa-send"></span></button>
                    </div>
                </div>
            <!--</li>-->
        </ul>
    </div>
</div>
</form>
<script>
$(document).ready(function(){
    $('.floatingButton').on('click', function(e){
        e.preventDefault();
        $(this).toggleClass('open');
        if($(this).children('.fas').hasClass('pe pe-7s-paper-plane'))
        {
            if(products.length == 0){
                swal('Select Product', 'Select Service Product for - Send to HO', 'warning');
                return false;
            }
            $(this).children('.fas').removeClass('pe pe-7s-paper-plane');
            $(this).children('.fas').addClass('pe pe-7s-close');
        } 
        else if ($(this).children('.fas').hasClass('pe pe-7s-close')) 
        {
            $(this).children('.fas').removeClass('pe pe-7s-close');
            $(this).children('.fas').addClass('pe pe-7s-paper-plane');
        }
        $('.floatingMenu').stop().slideToggle();
    });
//    $(this).on('click', function(e) {
//        var container = $(".floatingButton");
//        // if the target of the click isn't the container nor a descendant of the container
//        if (!container.is(e.target) && $('.floatingButtonWrap').has(e.target).length === 0) 
//        {
//            if(container.hasClass('open'))
//            {
//                container.removeClass('open');
//            }
//            if (container.children('.fas').hasClass('pe pe-7s-close')) 
//            {
//                container.children('.fas').removeClass('pe pe-7s-close');
//                container.children('.fas').addClass('pe pe-7s-paper-plane');
//            }
//            $('.floatingMenu').hide();
//        }
//    });
});
</script>
<?php include __DIR__.'../../footer.php'; ?>