<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function(){        
        $(document).on("click", "#receive_shipment", function(event) {
            if(!confirm('Do you want to receive Shipment')){
                return false;
            }
        });
    });
</script>
<style>
.blink_me {
  animation: blinker 2s linear infinite;
}
@keyframes blinker {
  40% {
    opacity: 0;
  }
}
</style>
<div class="col-md-10"><center><h3><span class="fa fa-sign-in fa-lg"></span> Service Inward in HO</h3></center></div>
<div class="clearfix"></div><hr>
<form style="font-size: 14px;">
    <div class="col-md-10 col-md-offset-1">
        <!--<h4><center>Form - Service Inward in HO</center></h4>-->
        <div class="thumbnail" style="padding: 0"><br>
            <div class="col-md-4">Service DC: &nbsp;SW-<?php echo $service_data[0]->idservice_transfer_send_to_ho ?></div>
            <div class="col-md-5 pull-right">Inward Date: &nbsp; &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($service_data[0]->entry_time)) ?></div>
            <div class="clearfix"></div><hr>
            <div class="col-md-7">
                Branch: &nbsp;&nbsp; <?php echo $service_data[0]->branch_name ?>, <?php echo $service_data[0]->branch_contact; ?>
            </div>
            <div class="col-md-5 pull-right">Total Products: &nbsp; &nbsp;<?php echo count($service_data); ?></div>
            <div class="clearfix"></div><br>
            <table id="model_data" class="table table-hover" style="margin-bottom: 0">
                <?php foreach ($service_data as $service){ ?>
                <thead class="bg-info">
                    <th style="">Case: <?php echo $service->id_service ?></th>
                    <th><?php echo $service->full_name.' - '.$service->imei;
                        if($service->counter_faulty){ echo '<span class="pull-right red-text blink_me">Counter Faulty</span>'; 
                        }else{ echo '<span class="pull-right red-text blink_me">Sold Product</span>'; } ?>
                        <input type="hidden" name="imei_no[]" value="<?php echo $service->imei ?>"/>
                        <input type="hidden" name="idservice[]" value="<?php echo $service->id_service ?>"/>
                        <input type="hidden" name="idvariant[]" value="<?php echo $service->idvariant ?>"/>
                    </th>
                </thead>
                <tbody>
                    <?php if(!$service->counter_faulty){ ?>
                    <tr>
                        <td>Invoice No:</td>
                        <td>
                            <div class="col-md-3" style="padding: 0">
                                <?php // echo $service->inv_no; ?>
                                <a href="<?php echo base_url('Reports/sale_details/'.$service->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $service->inv_no ?></a>
                            </div>
                            <div class="col-md-3">Invoice Date: <?php echo date('d/m/Y', strtotime($service->inv_date)); ?></div>
                            <div class="col-md-3">Sold Amount: <?php echo $service->sold_amount; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Customer:</td>
                        <td>
                            <div class="col-md-6" style="padding: 0"><?php echo $service->customer_name; ?></div>
                            <div class="col-md-3">Contact: <?php echo $service->mob_number; ?></div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Issue:</td>
                        <td><?php echo $service->problem; ?></td>
                    </tr>
                    <tr>
                        <td>Remark:</td>
                        <td><?php echo $service->remark; ?></td>                    
                    </tr>
                    <?php if($service_data[0]->warranty_status != ''){ ?>
                    <tr>
                        <?php if($service_data[0]->warranty_status == 1){ ?>
                        <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-flip-to-front fa-lg"></i> Repaired</h4></td>
                        <?php }elseif($service_data[0]->warranty_status == 2){ ?>
                        <td>Status</td><td>Rejected</td>
                        <?php }elseif($service_data[0]->warranty_status == 3){ ?>
                        <td><h4 style="color: #cc0099;">DOA Letter</h4></td>
                        <td>
                            <h4 class="col-md-9" style="color: #28538d;">
                                <span class="col-md-5">DOA ID: <?php echo $service_data[0]->doa_id; ?></span>
                                <span class="col-md-7">Date: <?php echo $service_data[0]->doa_date; ?></span>
                            </h4>
                            <a class="col-md-3 waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$service_data[0]->doa_letter_path) ?>" style="color: #1b6caa"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                        </td>
                        <?php }elseif($service_data[0]->warranty_status == 4){ ?>
                        <td><h4 style="color: #cc0099;">DOA Handset</h4></td><td><h4 style="color: #28538d;">IMEI - <?php echo $service_data[0]->new_imei_against_doa ?></h4></td>
                        <?php } ?>
                    </tr>
                    <?php }if($service_data[0]->executive_remark != NULL){ ?>
                        <tr>
                            <td>Executive Remark</td>
                            <td><?php echo $service_data[0]->executive_remark; ?></td>                    
                        </tr>
                    <?php } ?>
                </tbody>
                <?php } ?>
            </table>
        </div>
    </div><div class="clearfix"></div>
    <div class="thumbnail" style="padding-top: 0px; padding-left: 0; padding-right: 0">
        <center><h4>Shipment Details</h4></center>
        <table class="table table-bordered">
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
        <input type="hidden" name="idservice_transfer" value="<?php echo $service_data[0]->idservice_transfer_send_to_branch ?>"/>
        <input type="hidden" name="counter_faulty" value="<?php echo $service->counter_faulty ?>"/>
        <input type="hidden" name="idbranch" value="<?php echo $service_data[0]->received_at ?>"/>
        <input type="hidden" name="shipment_received_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
        <div class="clearfix"></div><hr>
        <?php if($this->session->userdata('idrole') == 16 && $service_data[0]->process_status == 9){ ?>
        <div class="col-md-2">Received Remark:</div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="shipment_received_remark" placeholder="Enter received remark" required="" />
        </div>
        <div class="col-md-2"><button class="btn btn-primary waves-effect" id="receive_shipment" formmethod="POST" formaction="<?php echo base_url('Service/save_receive_servcice_at_branch_from_ho') ?>">Receive</button></div>
        <?php } ?>
        <div class="clearfix"></div>
    </div>
</form>
<div style="position: fixed; right: 35px; bottom: 70px;"><a  href="<?php echo base_url('Service/dc_receive_service_shipment_at_branch/'.$service_data[0]->idservice_transfer_send_to_branch) ?>" class="btn btn-floating btn-large waves-effect waves-light"><i class="pe pe-7s-print" style="font-size: 30px"></i></a></div>
<?php   include __DIR__ . '../../footer.php'; ?>
