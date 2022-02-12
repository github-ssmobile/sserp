<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="fa fa-sign-in fa-lg"></span> Service Case Details </h3></center></div><div class="clearfix"></div><hr>
<div style="font-family: K2D; font-size: 15px;" class="col-md-10 col-md-offset-1">
    <h4><center>Service - Counter Faulty</center></h4>
    <!--<h4><center>Form - Service Inward in HO</center></h4>-->
    <div class="thumbnail" style="padding: 0"><br>
        <div class="col-md-4">Service DC: &nbsp;SW-<?php echo $service_data[0]->idservice_transfer_send_to_ho ?></div>
        <div class="col-md-5 pull-right">Inward Date: &nbsp; &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($service_data[0]->entry_time)) ?></div>
        <div class="clearfix"></div><hr>
        <div class="col-md-12 col-xs-12">
            Branch: &nbsp;&nbsp; <?php echo $service_data[0]->branch_name ?>, <?php echo $service_data[0]->branch_contact; ?>
        </div><div class="clearfix"></div><br>
        <table id="model_data" class="table table-hover" style="margin-bottom: 0">
            <?php foreach ($service_data as $service){ ?>
            <thead class="bg-info">
                <th style="">Case: <?php echo $service->id_service ?></th>
                <th><?php echo $service->full_name.' - '.$service->imei; ?>
                    <span class="pull-right">Counter Faulty</span>
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>Issue:</td>
                    <td><?php echo $service->problem; ?></td>
                </tr>
                <tr>
                    <td>Remark:</td>
                    <td><?php echo $service->remark; ?></td>                    
                </tr>
                <tr>
                    <td>Delivery Status</td>
                    <td><?php echo $service_data[0]->delivery_status; ?></td>
                </tr>
                <?php if($service->warranty_status != ''){ ?>
                <tr>
                    <?php if($service->warranty_status == 1){ ?>
                    <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-flip-to-front fa-lg"></i> Repaired</h4></td>
                    <?php }elseif($service->warranty_status == 2){ ?>
                    <td><h4 style="color: #cc0099;">Status</h4></td><td><h4 style="color: #cc0099;">Rejected</h4></td>
                    <?php }elseif($service->warranty_status == 3){ ?>
                    <td><h4 style="color: #cc0099;">DOA Letter</h4></td>
                    <td>
                        <h4 class="col-md-9" style="color: #28538d;">
                            <span class="col-md-5">DOA ID: <?php echo $service->doa_id; ?></span>
                            <span class="col-md-7">Date: <?php echo $service->doa_date; ?></span>
                        </h4>
                        <a class="col-md-3 waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$service->doa_letter_path) ?>" style="color: #1b6caa"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                    </td>
                    <?php }elseif($service->warranty_status == 4){ ?>
                    <td><h4 style="color: #cc0099;">DOA Handset</h4></td><td><h4 style="color: #28538d;">IMEI - <?php echo $service->new_imei_against_doa ?></h4></td>
                    <?php } ?>
                </tr>
                <?php }if($service->executive_remark != NULL){ ?>
                    <tr>
                        <td>Executive Remark</td>
                        <td><?php echo $service->executive_remark; ?></td>                    
                    </tr>
                <?php } ?>
                
            </tbody>
            <?php } ?>
        </table>
    </div>
    <form>
    <input type="hidden" name="imei_no" value="<?php echo $service_data[0]->imei ?>" />
    <input type="hidden" name="idwarehouse" value="<?php echo $service_data[0]->idwarehouse ?>" />
    <input type="hidden" name="idvariant" value="<?php echo $service_data[0]->idvariant ?>" />
    <input type="hidden" name="idservice" value="<?php echo $service_data[0]->id_service ?>" />
    <input type="hidden" name="warranty_status" value="<?php echo $service_data[0]->warranty_status ?>" />
    <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
    <input type="hidden" name="counter_faulty" value="1" />
    <input type="hidden" name="idbranch" value="<?php echo $service_data[0]->idbranch ?>" />
    <?php //    echo $service_data[0]->process_status;
          //    echo $this->session->userdata('idrole');
    if($service_data[0]->process_status == 14 && $this->session->userdata('idrole') == 36){ ?>
        <?php if($service_data[0]->warranty_status == 1){ ?>
            <button type="submit" class="btn btn-info pull-right" value="15,11" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify & Inward in HO</button>
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Service/add_in_send_to_branch_list') ?>" value="16" class="btn btn-warning">Add in Send to Branch List</button>
        <?php }elseif($service_data[0]->warranty_status == 2){ ?>
            <button type="submit" class="btn btn-info pull-right" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Add in Send to Branch List</button>
            <span class="red-text">Note: Invoice bill will be generated of above product in Credit of <b><?php echo $service_data[0]->branch_name ?> Branch</b></span>
        <?php }elseif($service_data[0]->warranty_status == 3){ ?>
            <button type="submit" class="btn btn-info pull-right" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
            <span class="red-text">Update Godown from Service to DOA and close case in HO</span>
        <?php }elseif($service_data[0]->warranty_status == 4){ ?>
            <button type="submit" class="btn btn-info pull-right" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
            <span class="red-text">
                Service Godown to DOA against New Handset inwarded by Executive & Close service in HO</span>
        <?php } ?>
    <?php } elseif($service_data[0]->process_status == 16){ ?>
        <h4 style="color: #28538d;">
            <center>Product Prepare For Send to Branch</center>
        </h4>
    <?php } elseif($service_data[0]->process_status == 11){ ?>
        <h4 style="color: #28538d;">
            <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Service Process Closed</center>
        </h4>
        <div class="clearfix"></div>
    <?php } elseif($service_data[0]->process_status == 12 && $service_data[0]->branch_process_enable==0 && $this->session->userdata('idbranch') == $service_data[0]->idbranch){ ?>
            <!--received at branch-->
        <button class="btn btn-info pull-right" formmethod="POST" formaction="<?php echo base_url('Service/close_service_at_branch') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
        <div class="clearfix"></div>
    <?php } ?>
    </form>
</div>
<!--<div style="position: fixed; right: 30px; bottom: 70px;"><a  href="<?php // echo base_url('Transfer/transfer_dc/') ?>" class="btn btn-floating btn-large waves-effect waves-light gradient2 print-a"><i class="pe pe-7s-print" style="font-size: 30px"></i></a></div>-->
<?php   include __DIR__ . '../../footer.php'; ?>
