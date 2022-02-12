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
});
</script>
<div class="col-md-10"><center><h3><span class="fa fa-sign-in fa-lg"></span> Service Case Details </h3></center></div><div class="clearfix"></div><hr>
<h4><center>Service - Sold Product</center></h4>
<!--<div class="col-md-10">-->
    <?php // foreach ($service_process_status as $process_status){ $datecolumns = $process_status->date_columns;
//        if($datecolumns != NULL){ ?>
        <?php // echo $process_status->delivery_status.' - '.$datecolumns ?>
    <?php // }} ?>
<!--</div><div class="clearfix"></div>-->

    <div style="font-family: K2D; font-size: 15px;" class="col-md-10 col-md-offset-1">
        <div class="thumbnail" style="padding: 0"><br>
            <div class="col-md-8 col-xs-8">
                <div>CaseID  :- <b style="color: #0e10aa !important;"><?php echo $service_data[0]->id_service ?></b></div>
                <div>Date  :- <b><?php echo date('d-M-Y', strtotime($service_data[0]->entry_time)) ?></b></div><br>                                
            </div>
            <div class="col-md-4 col-xs-4">
                <div>Invoice Date :- <?php echo date('d-M-Y', strtotime($service_data[0]->inv_date)) ?></div>
                <div>Invoice No :- <a href="<?php echo base_url('Sale/sale_details/'.$service_data[0]->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $service_data[0]->inv_no ?></a></div>
            </div>
            <div class="clearfix"></div><hr>            
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">                
                <b>Branch: &nbsp; <?php echo $service_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b>Customer , </b><br>
                <b>Name: &nbsp; <?php echo $service_data[0]->customer_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->mob_number; ?><br>
            </div>  
            <div class="clearfix"></div>
            <table id="model_data" class="table table-hover" style="margin-bottom: 0">
                <thead class="bg-info">
                    <th colspan="2"><?php echo $service_data[0]->full_name.' - ['.$service_data[0]->imei.']'; ?></th>
                </thead>
                <tbody>
                    <tr>            
                        <td>Service Issue</td>
                        <td><?php echo $service_data[0]->problem; ?></td>                         
                    </tr>
                    <tr>
                        <td>Remark</td>                    
                        <td><?php echo $service_data[0]->remark; ?></td>                    
                    </tr>
                    <tr>
                        <td>Delivery Status</td>
                        <td><?php echo $service_data[0]->delivery_status; ?></td>
                    </tr>
                    <?php if($service_data[0]->warranty_status != ''){ ?>
                    <tr>
                        <?php if($service_data[0]->warranty_status == 1){ ?>
                        <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-flip-to-front fa-lg"></i> Repaired</h4></td>
                        <?php }elseif($service_data[0]->warranty_status == 2){ ?>
                        <td>Status</td><td><h4 style="color: #cc0099;"><i class="mdi mdi-close-box-outline fa-lg"></i> Rejected</h4></td>
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
                        <td><h4 style="color: #cc0099;">DOA Handset</h4></td><td><h4 style="color: #28538d;">NEW IMEI - <?php echo $service_data[0]->new_imei_against_doa ?></h4></td>
                        <?php } ?>
                    </tr>
                    <?php }if($service_data[0]->executive_remark != NULL){ ?>
                        <tr>
                            <td>Executive Remark</td>
                            <td><?php echo $service_data[0]->executive_remark; ?></td>                    
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <form>
            <input type="hidden" name="imei_no" value="<?php echo $service_data[0]->imei ?>" />
            <input type="hidden" name="idwarehouse" value="<?php echo $service_data[0]->idwarehouse ?>" />
            <input type="hidden" name="idvariant" value="<?php echo $service_data[0]->idvariant ?>" />
            <input type="hidden" name="idservice" value="<?php echo $service_data[0]->id_service ?>" />
            <input type="hidden" name="warranty_status" value="<?php echo $service_data[0]->warranty_status ?>" />
            <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
            <input type="hidden" name="counter_faulty" value="0" />
            <input type="hidden" name="idbranch" value="<?php echo $service_data[0]->idbranch ?>" />
            <input type="hidden" name="new_imei_against_doa" value="<?php echo $service_data[0]->new_imei_against_doa ?>" />
            <input type="hidden" name="idsalesperson" value="<?php echo $service_data[0]->idsalesperson ?>" />
            <?php if($service_data[0]->process_status == 14 && $this->session->userdata('idrole') == 36){ ?>
                <?php if($service_data[0]->warranty_status == 1 || $service_data[0]->warranty_status == 2){ ?>
                    <button class="btn btn-info doa_letter_btn pull-right" formmethod="POST" formaction="<?php echo base_url('Service/add_in_send_to_branch_list') ?>" value="16" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Add in Send to Branch List</button>
                <?php }elseif($service_data[0]->warranty_status == 3){ ?>
                    <button class="btn btn-info doa_letter_btn pull-right" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_process_sold_prouct') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Proceed</button>
                    <span class="red-text">Update DOA & Allow Replace/Upgrade Option to Branch</span>
                <?php }elseif($service_data[0]->warranty_status == 4){ ?>
                    <button class="btn btn-info doa_letter_btn pull-right" formmethod="POST" formaction="<?php echo base_url('Service/verify_inward_in_ho_and_process_sold_prouct') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Proceed</button>
                    <span class="red-text">Inward new Handset & Allow Replace/Upgrade Option to Branch</span>
                <?php } ?></h4><div class="clearfix"></div><br>
            <?php } elseif($service_data[0]->process_status == 16){ ?>
                <h4 style="color: #28538d;">
                    <center>Product Prepare For Send to Branch</center>
                </h4></h4><div class="clearfix"></div><br>
            <?php }elseif($service_data[0]->process_status == 12 && $service_data[0]->branch_process_enable==0 && $this->session->userdata('idbranch') == $service_data[0]->idbranch){ ?>
                <!--received at branch-->
                <button class="btn btn-info pull-right" formmethod="POST" formaction="<?php echo base_url('Service/close_service_at_branch') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Verify and Close</button>
                </h4><div class="clearfix"></div><br>
            <?php } elseif($service_data[0]->process_status == 11){ ?>
                <h4 style="color: #28538d;">
                    <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Service Case Closed</center>
                </h4><div class="clearfix"></div><br>
            <?php } elseif($service_data[0]->process_status == 15){ ?>
                <h4 style="color: #28538d;">
                    <center><i class="mdi mdi-checkbox-marked-circle-outline fa-lg"></i> Verified by Coordiantor & Open Product Replace/Upgrade Form to Branch
                        <?php if($service_data[0]->branch_process_enable == 1 && $service_data[0]->idbranch == $this->session->userdata('idbranch')){ ?>
                        <a href="<?php echo base_url('Service/process_service_details/'.$service_data[0]->id_service) ?>" style="color: #3333ff"><i class="fa fa-send fa-lg"></i> Click to open Form</a>
                        <?php }?>
                    </center>
                </h4><div class="clearfix"></div><br>
            <?php } elseif($service_data[0]->process_status == 3 && $this->session->userdata('idrole') == 36){ ?>
                <h4 style="color: #28538d;">
                    Generate invoice with <b>Name of Branch Manager</b> due to delay(<?php $now = time(); // or your date as well
                                $your_date = strtotime($service_data[0]->force_doa_date);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?> Day's) in force DOA closure
                    <button type="submit" class="btn btn-warning confirmation_btn pull-right" formmethod="POST" formaction="<?php echo base_url('Service/generate_invoice_force_doa_sold') ?>" style="">Generate invoice of Branch Manager</button>
                </h4><div class="clearfix"></div><br>
            <?php } ?>
        </form>
    </div>
<div class="clearfix"></div>
<!--<div class="col-md-10 col-md-offset-2">-->
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
                        <span class="date"><?php echo date('d-m-Y', strtotime($service_data[0]->$dtcolumns)) ?><span>
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
