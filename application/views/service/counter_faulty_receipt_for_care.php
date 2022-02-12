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
            <div class="row thumbnail" style="padding: 0;border: 1px solid #999999">
                <center><h4  style="color: #000;font-family: K2D">DELIVERY CHALLAN</h4></center>
                <div class="col-md-7 col-xs-7">Invoice No.: &nbsp;SW/DC/<?php echo $service_data[0]->branch_code.'/'.$service_data[0]->idservice_transfer_send_to_ho ?></div>
                <div class="col-md-5 col-xs-5 pull-right">Date: &nbsp; &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($service_data[0]->entry_time)) ?></div>
                <div class="clearfix"></div><hr>
                <div class="col-md-7 col-xs-7">
                    <b>From,</b><br>
                    Branch: &nbsp;&nbsp; <?php echo $service_data[0]->branch_name ?>, Mob. <?php echo $service_data[0]->branch_contact; ?><br>
                    Address: &nbsp;<?php echo $service_data[0]->branch_address ?><br>
                    GST No.: &nbsp;<?php echo $service_data[0]->branch_gstno ?><br>
                </div>
                <div class="col-md-5 col-xs-5">
                    <b>Buyer,</b><br>
                    &nbsp; Customer: <?php echo $user_data[0]->user_name ?><br>
                    &nbsp; Mobile: &nbsp; &nbsp; <?php echo $user_data[0]->user_contact; ?><br>
                    &nbsp; Address: &nbsp; <?php echo $service_data[0]->branch_city.', '.$service_data[0]->branch_pincode ?><br>
                </div>
                <div class="clearfix"></div><br>
                <table id="model_data" class="table table-bordered table-condensed table-striped" style="margin-bottom: 0">
                    <thead class="bg-info">
                        <th>Sr</th>
                        <th>Case Id</th>
                        <th>Product</th>
                        <th>IMEI</th>
                        <th>Qty</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                        <?php $cnt=1; $tmop=0; foreach ($service_data as $service){ ?>
                        <tr>
                            <td><?php echo $cnt ?></td>
                            <td><?php echo $service->id_service ?></td>
                            <td><?php echo $service->full_name ?></td>
                            <td><?php echo $service->imei ?></td>
                            <td>1</td>
                            <td><?php $tmop += $service->mop; echo $service->mop ?></td>
                        </tr>
                        <?php $cnt++; } ?>
                        <tr>
                            <td style="height: 45px"></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td><?php echo $tmop?></td>
                        </tr>
                        <tr style="border: 1px solid #999999">
                            <td colspan="6" style="border: 1px solid #999999;font-size: 14px;">
                                <b>Declaration:</b> We declare that this invoice shows actual price of the goods described & that all particulars are true & correct.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="border: 1px solid #999999">
                    <div class="col-md-9 col-xs-9 col-xs-9" style="border-right: 1px solid #c3c3c3; padding: auto 5px"> 
                        <div style="line-height: 20px; color: #666666;">
                            <i class="fa fa-circle" style="font-size: 10px; opacity: 0.4;"></i> &nbsp;  <span style="color: #000;font-size: 14px;">Terms & Conditions</span><br>
                            <div style="font-size: 13px">
                            - &nbsp; Goods once sold will not be taken back, until and unless approved by the manufacturer.<br>
                            -  &nbsp; SS Communication & Service Pvt Ltd is not responsible for the performance and the warranty of any device sold. Warranty if any, is provided only by the manufacturer and as per the manufacturer’s policy only.<br>
                            -  &nbsp; In spite of the above, any device which is physically damaged, tampered or water logged, will not qualify for any kind of warranty from the manufacturer. <br>
                            -  &nbsp; All warranty periods if any are mentioned in the warranty card of the manufacturer and is applicable from the date of this invoice.<br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-3 col-xs-3 pull-right">
                        <br><br><br><br><br><br>
                        <center>Authorized Signatory</center>
                    </div>
                </div>
            <!--<center>This DC is generated for the purpose of Warranty Service</center>-->
            </div>
            <center><i>Subject to Kolhapur jurisdiction</i></center>
        </div><div class="clearfix"></div>
    </div>
<script>
    window.print();
</script>
<div class="clearfix"></div>
</body>
</html>
