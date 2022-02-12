<?php include __DIR__.'../../sale/header_invoice.php';  ?>
<style>
@page {
    size: letter;
    margin: 30px;
    padding: 20px;
}
@media print {
    html, body {
        width: 210mm;
        height: 297mm;
    }
}
</style>
    <div id="printTable" class="print_invoice"><br>
        <div class="container-fluid" style="font-size: 14px; padding: 0px 15px; background-color: #fff; line-height: 23px">
            <div class="row" style="padding: 0; border-bottom: 1px solid #999999; border: 1px solid #999999;">
                <div class="col-md-4 col-xs-4" style="padding: 0;">
                    <img class="hovereffect" height="85" src="<?php echo base_url() ?>assets/images/logo.jpg" alt="SS Mobile"/>
                </div>
                <div class="col-md-8 col-xs-8 text-center" style="padding: 0; font-size: 14px; padding-top: 15px; padding-left: 10px;">
                    <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0">SS COMMUNICATION & SERVICES PVT LTD</h3>
                    RATIKAMAL COMPLEX, 399 E WARD, SHAHUPURI, KOLHAPUR - 416001
                </div>
            </div>
<!--            <div class="row justify-content-end" style="padding: 3px 10px;">
                <div class="col-md-7 col-xs-7">
                    Invoice No.: &nbsp;<b><?php echo $service_data[0]->inv_no ?></b>
                    <input type="hidden" value="<?php echo $service_data[0]->inv_no ?>" id="inv_no">
                    <input type="hidden" value="<?php echo $service_data[0]->id_sale ?>" id="inv_id">
                    <input type="hidden" value="<?php echo date('Y-m-d', strtotime($sale->entry_time)) ?>" id="inv_date">
                </div>
                <div class="col-md-5 col-xs-5">
                    Date: &nbsp;<?php echo date('d-M-Y h:i: A', strtotime($sale->entry_time)) ?>
                </div>
            </div>-->
            <div class="row thumbnail" style="padding: 0">
                <center><h4  style="color: #000;font-family: K2D">DELIVERY CHALLAN</h4></center>
                <div class="col-md-7 col-xs-7">Service DC: &nbsp;SW-<?php echo $service_data[0]->idservice_transfer_send_to_ho ?></div>
                <div class="col-md-5 col-xs-5 pull-right">Date: &nbsp; &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($service_data[0]->entry_time)) ?></div>
                <div class="clearfix"></div><hr>
                <div class="col-md-7 col-xs-7">
                    <b>From,</b><br>
                    Warehouse: <?php echo $service_data[0]->warbranch_name ?>, Mob. <?php echo $service_data[0]->warbranch_contact; ?><br>
                    Address: <?php echo $service_data[0]->warbranch_address ?><br>
                    GST No.: &nbsp;<?php echo $service_data[0]->warbranch_gstno ?><br>
                </div>
                <div class="col-md-5 col-xs-5">
                    <b>To,</b><br>
                    Branch: &nbsp;&nbsp; <?php echo $service_data[0]->branch_name ?>, Mob. <?php echo $service_data[0]->branch_contact; ?><br>
                    Address: &nbsp;<?php echo $service_data[0]->branch_address ?><br>
                    GST No.: &nbsp;<?php echo $service_data[0]->branch_gstno ?><br>
                </div>
                <div class="clearfix"></div><br>
                <table id="model_data" class="table table-bordered table-condensed table-striped" style="margin-bottom: 0">
                    <thead class="bg-info">
                        <th>Sr</th>
                        <th>Case Id</th>
                        <th>Product</th>
                        <th>IMEI</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                        <?php $cnt=1;$tmop=0; foreach ($service_data as $service){ ?>
                        <tr>
                            <td><?php echo $cnt ?></td>
                            <td><?php echo $service->id_service ?></td>
                            <td><?php echo $service->full_name ?></td>
                            <td><?php echo $service->imei ?></td>
                            <td>
                                <?php if($service_data[0]->warranty_status == 1){ ?>
                                Repaired
                                <?php }elseif($service_data[0]->warranty_status == 2){ ?>
                                Rejected
                                <?php }elseif($service_data[0]->warranty_status == 3){ ?>
                                DOA Letter
                                <?php }elseif($service_data[0]->warranty_status == 4){ ?>
                                DOA Handset
                                <?php } ?>
                            </td>
                            <td><?php $tmop += $service->mop; echo $service->mop ?></td>
                        <?php $cnt++; } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td><?php echo $tmop?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row thumbnail" style="padding-top: 0px; padding-left: 0; padding-right: 0">
                <center><h4>Shipment Details</h4></center>
                <table class="table table-bordered table-condensed">
                    <thead>
                        <th>Dispatch date</th>
                        <th>Dispatch Type</th>
                        <th>Courier Name</th>
                        <th>Po/LR No</th>
                        <th>No of Boxes</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $service_data[0]->dispatch_date ?></td>
                            <td><?php echo $service_data[0]->dispatch_type ?></td>
                            <td><?php echo $service_data[0]->courier_name ?></td>
                            <td><?php echo $service_data[0]->po_lr_no ?></td>
                            <td><?php echo $service_data[0]->no_of_boxes ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="col-md-12">Shipment Remark: <?php echo $service_data[0]->shipment_remark ?></div>
                <div class="clearfix"></div>
            </div>
        </div><div class="clearfix"></div>
        <center>This DC is generated for the purpose of Warranty Service</center>
    </div>
<script>
    window.print();
</script>
<div class="clearfix"></div>
</body>
</html>

