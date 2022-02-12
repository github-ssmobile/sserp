<?php include __DIR__ . '../../header.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.3.4/vue.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
const data = [
//  { dateLabel: 'January 2017', title: 'Gathering Information' },
//  { dateLabel: 'February 2017', title: 'Planning' },
//  { dateLabel: 'March 2017', title: 'Design' },
//  { dateLabel: 'April 2017', title: 'Content Writing and Assembly' },
//  { dateLabel: 'May 2017', title: 'Coding' },
//  { dateLabel: 'June 2017', title: 'Testing, Review & Launch' },
//  { dateLabel: 'July 2017', title: 'Maintenance' }
];

new Vue({
  el: '#app', 
  data: {
    steps: data,
  },
  mounted() {
    var swiper = new Swiper('.swiper-container', {
      //pagination: '.swiper-pagination',
      slidesPerView: 3,
      paginationClickable: true,
      grabCursor: true,
      paginationClickable: true,
      nextButton: '.next-slide',
      prevButton: '.prev-slide',
    });    
  }
});
    $(document).on("click", ".confirmation_btn", function (event) {
        if(!confirm("Do you want to submit entry")){
            return false;
        }
    });
});

</script>
<div class="col-md-10"><center><h3><span class="fa fa-sign-in fa-lg"></span> Service Case Details </h3></center></div><div class="clearfix"></div><hr>
<div style="font-family: K2D; font-size: 15px;" class="col-md-10 col-md-offset-1">
    <h4><center>Service - Counter Faulty</center></h4>
    <!--<h4><center>Form - Service Inward in HO</center></h4>-->
    <div class="thumbnail" style="padding: 0"><br>
        <?php if($service_data[0]->idservice_transfer_send_to_ho){ ?><div class="col-md-4">Service DC: &nbsp;SW-<?php echo $service_data[0]->idservice_transfer_send_to_ho ?></div><?php } ?>
        <div class="col-md-5 pull-right">Inward Date: &nbsp; &nbsp;<?php echo date('d-M-Y h:i:s A', strtotime($service_data[0]->entry_time)) ?></div>
        <div class="clearfix"></div><hr>
        <div class="col-md-12 col-xs-12">
            Branch: &nbsp;&nbsp; <?php echo $service_data[0]->branch_name ?>, <?php echo $service_data[0]->branch_contact; ?>
        </div><div class="clearfix"></div><br>
        <table id="model_data" class="table table-hover" style="margin-bottom: 0">
            <?php foreach ($service_data as $service){ ?>
            <thead class="bg-info">
                <th style="">Case Id: <?php echo $service->id_service ?></th>
                <th><?php echo $service->full_name.' - '.$service->imei; ?>
                    <span class="pull-right red-text">Counter Faulty
                        <?php if($service_data[0]->counter_faulty_approval == 1){ ?>
                        <a href="<?php echo base_url('Service/counter_faulty_receipt_for_care/'.$service->id_service) ?>"><i class="fa fa-newspaper-o fa-lg"></i></a>
                        <?php } ?>
                    </span>
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
                    <td><h4 style="color: #cc0099;">DOA Handset</h4></td><td><h4 style="color: #28538d;">NEW IMEI - <?php echo $service->new_imei_against_doa ?></h4></td>
                    <?php } ?>
                </tr>
                <?php }if($service->executive_remark != NULL){ ?>
                    <tr>
                        <td>Service Remark</td>
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
    <input type="hidden" name="new_imei_against_doa" value="<?php echo $service_data[0]->new_imei_against_doa ?>" />
    <input type="hidden" name="idsalesperson" value="<?php echo $service_data[0]->idsalesperson ?>" />
    <?php //    echo $service_data[0]->process_status;
          //    echo $this->session->userdata('idrole');
        if($service_data[0]->process_status == 1){
            if($this->session->userdata('idrole') == 36){
                if($service_data[0]->counter_faulty_approval == 0){ ?>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning confirmation_btn" value="2" name="counter_faulty_btn" formmethod="POST" formaction="<?php echo base_url('Service/approve_counter_faulty_inward') ?>" style="">Reject</button>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="counter_faulty_approve_remark" placeholder="Enter Approve/ Reject Remark" required="" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info pull-right confirmation_btn" value="1" name="counter_faulty_btn" formmethod="POST" formaction="<?php echo base_url('Service/approve_counter_faulty_inward') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Approve</button>
                    </div>
                    <div class="clearfix"></div><br>
            <?php }elseif($service_data[0]->counter_faulty_approval == 1){ ?>
                <h4 style="color: #28538d;">
                    <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Counter Faulty Approved</center>
                </h4><div class="clearfix"></div><br>
            <?php }elseif($service_data[0]->counter_faulty_approval == 2){ ?>
                <h4 style="color: #c30e14;">
                    <center>Counter Faulty Rejected</center>
                </h4><div class="clearfix"></div><br>
            <?php }
        }else{
            if($service_data[0]->counter_faulty_approval == 0){ ?>
                <h4 style="color: #28538d;">
                    <center>Counter Faulty Pending for Approval</center>
                </h4><div class="clearfix"></div><br>
        <?php }elseif($service_data[0]->counter_faulty_approval == 1){ ?>
            <h4 style="color: #28538d;">
                <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Counter Faulty Approved</center>
            </h4><div class="clearfix"></div><br>
        <?php }elseif($service_data[0]->counter_faulty_approval == 2){ ?>
            <h4 style="color: #c30e14;">
                <center>Counter Faulty Rejected</center>
            </h4><div class="clearfix"></div><br>
        <?php }}}
        
        if($service_data[0]->process_status == 14 && $this->session->userdata('idrole') == 36){ ?>
        <?php if($service_data[0]->warranty_status == 1){ ?>
            <button type="submit" class="btn btn-info pull-right confirmation_btn" value="15,11" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify & Inward in HO</button>
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Service/add_in_send_to_branch_list') ?>" value="16" class="btn btn-warning confirmation_btn">Add in Send to Branch List</button>
            <div class="clearfix"></div><br>
        <?php }elseif($service_data[0]->warranty_status == 2){ ?>
            <button type="submit" class="btn btn-warning confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="">Generate invoice and Add in Send to Branch List</button>
            <button type="submit" class="btn btn-info pull-right confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/add_in_send_to_branch_list') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">ADD IN SEND TO BRANCH LIST</button>
            <!--<button type="submit" class="btn btn-info pull-right confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/add_in_send_to_branch_list') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify & Inward in HO</button>-->
            <span class="red-text">If you click here <i class="fa fa-arrow-up"></i> Invoice will be generated of above product in Credit of <b><?php echo $service_data[0]->branch_name ?> Branch</b></span>
            <div class="clearfix"></div><br>
        <?php }elseif($service_data[0]->warranty_status == 3){ ?>
            <button type="submit" class="btn btn-info pull-right confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
            <span class="red-text">Update Godown from Service to DOA and close case in HO</span><div class="clearfix"></div><br>
        <?php }elseif($service_data[0]->warranty_status == 4){ ?>
            <button type="submit" class="btn btn-info pull-right confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_close') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
            <span class="red-text">
                Inward New(Replaced) product & Close service in HO
            </span><div class="clearfix"></div><br>
            <!--<span class="red-text">Service Godown to DOA against New Handset inwarded by Executive & Close service in HO</span><div class="clearfix"></div><br>-->
        <?php } ?>
    <?php } elseif($service_data[0]->process_status == 16){ ?>
        <h4 style="color: #28538d;">
            <center>Product Prepare For Send to Branch</center>
        </h4><div class="clearfix"></div><br>
    <?php } elseif($service_data[0]->process_status == 11){ ?>
        <h4 style="color: #28538d;">
            <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Service Process Closed</center>
            <?php if($service_data[0]->counter_faulty_approval == 0){ ?>
            <center> Counter Faulty Reject </center>
            <?php } ?>
        </h4>
        <div class="clearfix"></div><br>
    <?php } elseif($service_data[0]->process_status == 12 && $service_data[0]->branch_process_enable==0 && $this->session->userdata('idbranch') == $service_data[0]->idbranch){ ?>
            <!--received at branch-->
        <button class="btn btn-info pull-right confirmation_btn" formmethod="POST" formaction="<?php echo base_url('Service/close_service_at_branch') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
        <div class="clearfix"></div><br>
    <?php } ?>
    </form>
</div><div class="clearfix"></div>
<div id="app" class="thumbnail">
    <div class="swiper-container">
        <p class="swiper-control">
            <button type="button" class="btn btn-default btn-sm prev-slide">Prev</button>
            <button type="button" class="btn btn-default btn-sm next-slide">Next</button>
        </p>
        <div class="swiper-wrapper timeline">
            <?php $seq=1; foreach ($service_process_status as $process_status) {
                $dtcolumns = $process_status->date_columns;
                if ($dtcolumns != NULL && $service_data[0]->$dtcolumns != '0000-00-00' && $service_data[0]->$dtcolumns != NULL) { $dates[] = $service_data[0]->$dtcolumns; ?>
                <div class="swiper-slide">
                    <div class="timestamp">
                        <span class="date"><?php echo $service_data[0]->$dtcolumns ?><span>
                    </div>
                    <div class="status">
                        <span>
                            <?php echo $seq.'] '.$process_status->delivery_status ?>
                            <?php if($service_data[0]->idservice_transfer_send_to_ho != NULL && $process_status->id == 4){ ?>
                            <hr><a class="waves-effect waves-block waves-purple" style="color: #3333ff" href="<?php echo base_url('Service/receive_service_shipment/'.$service_data[0]->idservice_transfer_send_to_ho) ?>">Shipment Link <i class="pe pe pe-7s-paper-plane fa-lg"></i></a>
                            <?php } ?>
                            <?php if($service_data[0]->idservice_transfer_send_to_branch != NULL && $process_status->id == 9){ ?>
                            <hr><a class="waves-effect waves-block waves-purple" style="color: #3333ff" href="<?php echo base_url('Service/receive_service_shipment_at_branch/'.$service_data[0]->idservice_transfer_send_to_branch) ?>">Shipment Link <i class="pe pe pe-7s-paper-plane fa-lg"></i></a>
                            <?php } ?>
                        </span>
                    </div>
                </div>
            <?php $seq++; }} ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
<?php // echo print_r($dates); ?>
<style>
.timeline {
  font-family: 'K2D';
  margin: 20px 0;
  list-style-type: none;
  display: flex;
  padding: 0;
  text-align: center;
}
.timeline li {
  transition: all 200ms ease-in;
}
.timestamp {
  width: 100%;
  margin-bottom: 20px;
  padding: 0px 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  font-weight: 100;
}
.status {
  padding: 0px 5px;
  display: flex;
  justify-content: center;
  border-top: 4px solid #c30e14;
  position: relative;
  transition: all 200ms ease-in;
}
.status span {
  padding-top: 20px;
}
.status span:before {
  content: "";
  width: 25px;
  height: 25px;
  background-color: #9b0c13;
  /*background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5)*/
  border-radius: 25px;
  border: 4px solid #c30e14;
  position: absolute;
  top: -15px;
  left: calc(50% - 12px);
  transition: all 200ms ease-in;
}
.swiper-control {
  text-align: right;
}
.swiper-container {
  width: 100%;
  height: 200px;
  margin: 10px 0;
  overflow: hidden;
  padding: 0 10px;
}
.swiper-slide {
  width: 200px;
  text-align: center;
  font-size: 14px;
}
.swiper-slide:nth-child(2n) {
  width: 40%;
}
.swiper-slide:nth-child(3n) {
  width: 20%;
}
</style>
<?php   include __DIR__ . '../../footer.php'; ?>
