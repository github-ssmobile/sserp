<?php include __DIR__ . '../../header.php';
if (!$this->session->userdata('userid')) {
    return redirect(base_url());
} else {
    ?>
    <script>
        $(document).ready(function () {
            $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
            
             $(document).on("click", ".receive", function (event) {                   
                if (confirm('Do you want to receive this Stock Shipment!!')) {  
                    
                    var allocation_id=$("#a_id").val();
                    var outward_id=$("#o_id").val();
                    var idbranch=$("#idbranch").val();
                    var remark=$("#remark").val();
                    $.ajax({
                    url: "<?php echo base_url() ?>Outward/ajx_receive_stock",
                    method: "POST",                
                    data: {allocation_id:allocation_id ,outward_id:outward_id,idbranch:idbranch,remark:remark},        
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                            window.location = "<?php echo base_url() ?>Outward/ready_to_receive";
                        }else if(data.data === "fail"){
                            alert("Fail to receive the shipment!! Try again. ");
                        }

                    }
                });
            }
        });
            
            
            
        });
        var barcodes = [];
        var qty1 = 1;
        $(document).on('keyup', 'input[id=barcode]', function (e) {
            var ce = $(this);
            var val = $(this).val();
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13 && val !== '') {
                var qty = +$(ce).closest('td').parent('tr').find(".qty").val();
                var scannedqty = +$(ce).closest('td').parent('tr').find(".qty1").val();
                if (qty == scannedqty) {
                    alert('Quantity limit exceeded!!');
                    $(this).val('');
                    return false;
                }
                var idvariant = $(ce).closest('td').parent('tr').find(".idvariant").val();
                var idgodown = $(ce).closest('td').parent('tr').find(".id_godown").val();
                var idwarehouse = $(".idwarehouse").val();

                $.ajax({
                    url: "<?php echo base_url() ?>Stock/ajax_check_valid_barcode",
                    method: "POST",
                    data: {val: val, idvariant: idvariant, idbranch: idwarehouse, idgodown: idgodown},
                    dataType: 'json',
                    success: function (data)
                    {
                        if (data.error === false) {
                            if (barcodes.includes(val) === false) {
                                barcodes.push(val);
                                //              var scanned;
                                $('#imeiscanned').val(barcodes);
                                e.preventDefault();
                                $(ce).closest('td').parent('tr').find(".scanned").append('' + val + ',');
    //                            qty1 = $(ce).closest('td').parent('tr').find(".qty1").val();
                                qty1 = +$(ce).closest('td').parent('tr').find(".qty1").val() + +1;
                                $(ce).closest('td').parent('tr').find(".qty1").val(qty1);
                                $(ce).closest('td').parent('tr').find(".scanned1").append('<input type="text" class="btn btn-sm btn-warning scanned_imei" id="scanned_imei" readonly="" style="margin:2px; width: 160px;background-color: #0e10aa;opacity: 0.8;" value="' + val + '" href="javascript:void(0);"></input>');
                            } else {
                                alert('Failed: Duplicate IMEI/SRNO scanned');
                                $(this).val('');
                                return false;
                            }
                        } else {
                            alert('Failed: IMEI/SRNO not verified');
                            $(this).val('');
                            return false;
                        }
                    }
                });
                $(this).val('');
            }
        });
        $(document).on('click', '.scanned_imei', function () {
            var barid = $(this).val();
            if (confirm('Are you sure? You want to remove imei/srno: ' + barid)) {
                var ce = $(this);
                var qty = $(ce).closest('td').parent('tr').find(".qty1").val()
                $(ce).closest('td').parent('tr').find(".qty1").val(parseInt(qty) - 1);
                barcodes = jQuery.grep(barcodes, function (value) {
                    return value !== barid;
                });
                $('#imeiscanned').val(barcodes);

                var TextSearch = $(ce).closest('td').parent('tr').find(".scanned").val();
                scanned = TextSearch.replace(barid + ',', '');
                $(ce).closest('td').parent('tr').find(".scanned").text(scanned);

                $(this).fadeOut();
                var qty = $(ce).closest('td').parent('tr').find(".qty1").val();
                var org_qty = $(ce).closest('td').parent('tr').find(".qty").val();
                if (qty > 0) {
                    $(ce).closest('td').parent('tr').find(".barcode").removeAttr("readonly");
                }
                if (qty == org_qty) {
                    $(ce).closest('td').parent('tr').find(".qty").removeAttr("readonly");
                }
            }
        });
        $(document).on('click', '.btn-sub', function () {
            var qty1 = 0;
            var qty = 0;
            $('tr').each(function () {
                $(this).find('.qty1').each(function () {
                    qty1 += parseFloat($(this).val());
                });
                $(this).find('.qty').each(function () {
                    qty += parseFloat($(this).val());
                });
            });
            if (parseInt(qty1) != parseInt(qty)) {
                alert("Please scan all IMEIs");
                return false;
            }
        });

        
    </script>
    <center><h3 style="margin-top: 0"><span class="mdi mdi-barcode-scan fa-lg"></span> Receive Shipment</h3></center>
    <div class="clearfix"></div><hr>
    <?php if ($one_click_receive == 0) { ?>

        <?php $outward = $outward_data[0]; ?>
        <div class="thumbnail col-md-offset-2" style="padding: 0; background: #fffcf0;margin: 0 20px -1px 20px;"><br>                 
            <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                <b>FROM, </b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[0]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b> To,</b><br>
                <b>Branch: &nbsp; <?php echo $branch_data[1]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $branch_data[1]->branch_contact; ?><br>

            </div>  
            <div class="clearfix"></div><hr>
            <div class="col-md-8 col-xs-8">
                <div class="col-md-2">Mandate Number</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"><?php echo $outward->id_stock_allocation ?></b></div><br>
                <div class="col-md-2">DC Number</div><div class="col-md-6"> :- <b style="color: #0e10aa !important;"><?php echo $outward->id_outward ?></b></div><br>
                <div class="col-md-2">Remark</div><div class="col-md-6"> :- <?php echo $outward->outward_remark ?></div>                
                <br>
            </div>
            <div class="col-md-4 col-xs-4">
                <div class="col-md-4">Allocation Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y', strtotime($outward->confirm_time)) ?></div><br>
                <div class="col-md-4">Outward Date</div><div class="col-md-6"> :- <?php echo date('d-M-Y', strtotime($outward->scan_time)) ?></div><br>
                <div class="col-md-4">Shipment Date</div><div class="col-md-6"> :- <?php if ($outward->shipment_entry_time != "0000-00-00 00:00:00") { echo date('d-M-Y', strtotime($outward->shipment_entry_time)); } ?>
            
        </div><div class="clearfix"></div>                  
            </div>
            <div class="clearfix"></div><br>

            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin: 0;padding: 30px;">
                <thead class="">
                <th>Sr</th>
                <th class="col-md-6">Product</th>
                <th>Qty</th>
                <th class="col-md-5">IMEI/SRNO</th>
                </thead>
                <tbody>
        <?php $i = 1;
        foreach ($outward_data as $product) { 
            $array = explode(',', $product->imei); ?> 
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $product->full_name; ?></td>
                            <td><?php echo $product->qty ?></td>
                            <td><?php $j=0;$im=""; foreach($array as $imei){
                                            if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; }
                                            $j++;                                            
                                        }
                                        echo $im;                                                
                                        //echo $product->imei ?>
                            </td>
                        </tr>
        <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="thumbnail" style="background: #fffcf0;margin: 0 20px -1px 20px;">
            <center><h4 style="margin-bottom: 0"><i class="mdi mdi-truck"></i> Shipment Details</h4></center>
            <div class="clearfix"></div><hr>
            <div class="col-md-2 text-muted">Dispatch Date</div>
            <div class="col-md-2"><?php echo $outward->dispatch_date ?></div>
            <div class="col-md-2 text-muted">Dispatch Type</div>
            <div class="col-md-1"><?php echo $outward->dispatch_type ?></div>
            <div class="col-md-2 text-muted">Courier/ Transport Name</div>
            <div class="col-md-3"><?php echo $outward->courier_name ?></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2 text-muted">POP/LR Number</div>
            <div class="col-md-2"><?php echo $outward->po_lr_no ?></div>
            <div class="col-md-2 text-muted">No of Boxes</div>
            <div class="col-md-1"><?php echo $outward->no_of_boxes ?></div>
            <div class="col-md-2 text-muted">Remark</div>
            <div class="col-md-3"><?php echo $outward->shipment_remark ?></div><div class="clearfix"></div><br>
            <div class="clearfix"></div><hr>
            <div class="col-md-6"></div>
            <input type="hidden" name="o_id" id="o_id" value="<?php echo $outward->id_outward ?>" />
            <input type="hidden" name="a_id" id="a_id" value="<?php echo $outward->id_stock_allocation ?>" />
            <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $branch_data[1]->id_branch ?>" />
            <div class="col-md-4">                
                <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">Remark : </div>
                <div class="col-md-8" style="font-family: Kurale; font-size: 20px;color: #0e10aa !important;text-align: left">
                    <textarea class="form-control input-sm" name="remark"  id="remark"  placeholder="Enter remark" ></textarea>
                </div>
                <div class="clearfix"></div>                              
            </div>
            <div class="col-md-2 pull-right">
                <button type="button"  class="receive btn btn-primary gradient2" >Receive</button>
            </div>
            <div class="clearfix"></div>
        </div>
    
    <?php } else { ?>

        <form class="outward">
            <div class="thumbnail" style="padding: 15px 0; margin: 0;font-size: 13px">
                <div class="col-md-10">
                    <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                        <div class="col-md-6">Mandate No : </div>
                        <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $stock_allocation[0]->id_stock_allocation ?></div>
                        <div class="clearfix"></div><br>                                
                        <div class="col-md-6">Branch : </div>
                        <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo $stock_allocation[0]->branch_name ?></div>
                        <div class="clearfix"></div>                              
                    </div>
                    <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">
                        <div class="col-md-6" >Outward Date : </div>
                        <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo date('d-M-Y', strtotime($stock_allocation[0]->scan_time)) ?></div>
                        <div class="clearfix"></div><br>
                        <div class="col-md-6">Shipment Date : </div>
                        <div class="col-md-6" style="color: #0e10aa !important;text-align: left"><?php echo date('d-M-Y', strtotime($stock_allocation[0]->dispatch_date)); ?></div>
                        <div class="clearfix"></div><br>
                    </div>
                    <div class="col-md-4">                
                        <div class="col-md-4" style="font-family: Kurale; font-size: 16px;padding-top: 3px;">Remark : </div>
                        <div class="col-md-8" style="font-family: Kurale; font-size: 20px;color: #0e10aa !important;text-align: left">
                            <textarea class="form-control input-sm" name="remark"  placeholder="Enter remark" ></textarea>
                        </div>
                        <div class="clearfix"></div>                              
                    </div>
                    <div class="clearfix"></div>                                    
                    <input type="hidden" id="idbranch" class="idbranch" name="idbranch" value="<?php echo $stock_allocation[0]->idbranch ?>" />
                    <input type="hidden" id="idwarehouse" class="idwarehouse" name="idwarehouse" value="<?php echo $stock_allocation[0]->idwarehouse ?>" />
                    <input type="hidden" id="idallocation" class="idallocation" name="idallocation" value="<?php echo $stock_allocation[0]->id_stock_allocation ?>" />
                    <input type="hidden" id="gst_type" class="gst_type" name="gst_type" value="<?php echo $stock_allocation[0]->gst_type; ?>" />
                </div>
                <div class="clearfix"></div>
            </div>
            <input type="hidden" id="imeiscanned" class="form-control" />
            <div class="thumbnail" style="font-family: K2D;">
                <div class="col-md-12" id="product" style="overflow: auto;">
                    <div class="row">

                        <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                            <div style="padding: 5px 0">
                                <input type="text" class="form-control" placeholder="Scan IMEI/SRNO/Barcode" id="enter_imei"/>
                            </div>
                        </div>
                        <div class="clearfix"></div><br>

                        <table class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 13px">
                            <thead class="bg-info">
                            <th>Srno</th>
                            <th class="col-md-3">Product</th>  
                            <th class="col-md-3">Qty</th>                          
                            </thead>
                            <tbody id="product_data" style="border: 1px solid #C8D4D4">
                                            <?php $i = 1;
                                            foreach ($stock_allocation as $allo_data) { ?>
                                    <tr>
                                        <td><?php echo $i ?></td>
                                        <td><?php echo $allo_data->full_name ?></td>
                                        <td>
                                            <div class="col-md-3" style="padding: 0; margin: 0">
            <?php if ($allo_data->idskutype == 4) { ?>
                                                    <input type="text" id="qty1" class="form-control input-sm qty1" if value="<?php echo $allo_data->qty ?>" placeholder="Qty1"  style="margin: 0"/>
            <?php } else { ?>
                                                    Hidden
            <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="1" placeholder="Scanned IMEI" style="display: none"></textarea>
                                            <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto"></div>
                                        </td>
                                        <!-- td>
                                            <input type="hidden" class="form-control input-sm id_stock_allocation_data" value="<?php echo $allo_data->id_stock_allocation_data ?>" />
                                            <a class="btn remove" name="remove[]" id="remove" style="color: #cc0033"><i class="fa fa-trash-o fa-lg"></i></a>
                                        </td>-->
                                    </tr>
            <?php $i++;
        } ?>
                            </tbody>
                        </table>

                        <input type="hidden" name="count" value="<?php echo $i ?>" />
                        <button type="button" class="submit-outward btn btn-primary pull-right btn-sub">Submit</button>
                        <div class="clearfix"></div>
                    </div>
                </div><div class="clearfix"></div><br>
            </div>
        </form>


    <?php } ?>

<?php } include __DIR__ . '../../footer.php'; ?>