<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Target_model');
        $this->load->model('General_model');
    }
    
    //***************Branch Target Setup*****************
    public function branch_target_setup() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $this->load->view('target/branch_target_setup',$q);
    }
    public function ajax_get_branch_target_data(){
        $idzone = $this->input->post('idzone');
        $monthyear = $this->input->post('monthyear');
        $lastmonthyear = $this->input->post('lastmonthyear');
        
        $current_monthyear = date('Y-m');
        
        $target_data = $this->Target_model->ajax_get_branch_target_data_byidzone($idzone, $lastmonthyear, $monthyear);
        $current_target_data = $this->Target_model->ajax_get_current_month_branch_target_data_byidzone($idzone, $monthyear);
        $cluster_head = $this->Target_model->get_cluster_head_data();
        
        $all_taret= $this->Target_model->ajax_check_branch_target_data_byidproductcat($idzone, $monthyear);
        
//        die('<pre>'.print_r($all_taret,1).'</pre>');
        $cnttarget = 0;
        if($current_target_data){
            $cnttarget = 1;
        }else{
            $cnttarget = 0;
        }?>
        
       <?php  if($target_data){
            if($cnttarget == 0){ ?>

           <!--***************Set Current Month Target******************-->
            <form id="myform">
                <div  style="overflow-x: auto;height: 700px">
                    <table class="table  table-condensed text-center"  style="margin-bottom: 0;" id="branch_target_data">
                        <thead class="fixheader textcenter" style="background-color: #c6e6f5;">
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="border-left: 1px solid #cccccc;"></th>
                            <th style="text-align: right;" ><center>Last Month</center></th>
                            <th class=""><center>Acheivment</center></th>
                            <th style="border-right: 1px solid #cccccc;"></th>
                            <th></th>
                            <th style="text-align: right"><?php echo date('F',strtotime($monthyear));?> Month</th>
                            <th style="text-align: left">Target</th>
                            <th style="border-right: 1px solid #cccccc;"></th>
                        </thead>
                        <thead class="fixheader1 textcenter" style="background-color: #c6e6f5">
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">SR.</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">ZONE</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">CLUSTER HEAD</th>
                            <th class="fixleft" style="border-left: 1px solid #cccccc;">BRANCH</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">PARTNER TYPE</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;"> BRANCH CATEGORY</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">PRODUCT CATEGORY</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">VOLUME ACH</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">VALUE ACH</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">SMART PHONE ASP ACH</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">REVENUE ACH(%)</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">VOLUME TARGET</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">VALUE TARGET</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">ASP TARGET</th>
                            <th class="textcenter" style="border-left: 1px solid #cccccc;">REVENUE TARGET(%)</th>
                        </thead>
                        <tbody class="data_1">
                            <?php
                            
                            $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                            $zb_volum=0;$zb_value=0;$zb_asp=0;$zb_rev=0;    
                            
                            $old_name=$target_data[0]->id_branch;
                            
                            $sr=1;$sale=0;$stotal=0;$land=0;$rev=0; $revper=0; $aspach = 0; $asp_acheivement =0;
                            foreach ($target_data as $target){  
                            if($target->sale_qty){ $sale = $target->sale_qty; }else{ $sale = 0; }
                            if($target->total){ $stotal = $target->total; }else{ $stotal = 0; }
                            if($target->landing){ $land = $target->landing; }else{ $land=0;}
                            if($sale > 0){ 
                                $aspach = $stotal / $sale;
                            }else{
                                $aspach =0;
                            }
                            $rev = $stotal - $land;
                            if($land != 0){ 
                                $revper = ($rev * 100) /$land;
                            }else{
                                $revper = 0;
                            }
                            
                            //Smart Phone and Tablet sale,landing
                            $smart_sale=0;$smart_total=0;$smart_landing=0;$smart_asp=0;
                            if($target->smart_sale_qty){ $smart_sale = $target->smart_sale_qty; }else{ $smart_sale = 0; }
                            if($target->smart_total){ $smart_total = $target->smart_total; }else{ $smart_total = 0; }
                            if($target->smart_landing){ $smart_landing = $target->smart_landing; }else{ $smart_landing=0;}
                            
                             if($smart_sale > 0){ 
                                $smart_asp = $smart_total / $smart_sale;
                            }else{
                                $smart_asp =0;
                            }
                            
                            if($target->id_product_category ==1 || $target->id_product_category ==32){
                                $asp_acheivement = $smart_asp;
                            }else{
                                $asp_acheivement = $aspach;
                            }
                            
                            //***********Branch Wise Total SUM *************
                            if($old_name == $target->id_branch){
                                $b_volum = $b_volum+$sale;
                                $b_value = $b_value+$stotal;
                                
                                if($target->id_product_category ==1 || $target->id_product_category ==32){
                                    $b_asp = $b_asp+$smart_asp;
                                }else{
                                    $b_asp = $b_asp+$aspach;
                                }
                                
                                $b_rev = $b_rev+$revper;
                            }else{ ?>
                                <tr style="background-color: #ffffcc" >
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"></td>     
                                    <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                    <td style="border-left: 1px solid #cccccc;"<div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                    <td style="border-left: 1px solid #cccccc;"><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                    <td style="border-left: 1px solid #cccccc;"><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                    <td style="border-left: 1px solid #cccccc;"><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                </tr>
                                <?php   
                                $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                $b_volum = $b_volum+$sale;
                                $b_value = $b_value+$stotal;
                                if($target->id_product_category ==1 || $target->id_product_category ==32){
                                    $b_asp = $b_asp+$smart_asp;
                                }else{
                                    $b_asp = $b_asp+$aspach;
                                }
                                $b_rev = $b_rev+$revper;
                                
                            }?>
                            <tr class="br<?php echo $target->id_branch?>">
                                <td style="border-left: 1px solid #cccccc;"><?php echo $sr; ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo $target->zone_name; ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $target->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                                <td class="fixleft" style="border-left: 1px solid #cccccc;"><?php echo $target->branch_name; ?><input type="hidden" name="idbranch[]" class="form-control idbranch" value="<?php echo $target->id_branch; ?>"></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo $target->partner_type; ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo $target->branch_category_name; ?></td>
                                <td style="border-left: 1px solid #cccccc;"> <?php echo $target->product_category_name; ?><input type="hidden" name="idproductcat[]" class="form-control" value="<?php echo $target->id_product_category; ?>"></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo $sale; ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo round($stotal); ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo round($asp_acheivement); // round($aspach); ?></td>
                                <td style="border-left: 1px solid #cccccc;"><?php echo number_format($revper,2).'%';?></td>
<!--                                <td><?php echo round($rev);?></td>-->
                                <?php if($current_monthyear == $monthyear) { ?>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px;text-align: center"  name="volume_target[]" class="form-control volumetarget<?php echo $target->id_branch?>" id="voltarget"></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px;text-align: center"  name="value_target[]" class="form-control valuetarget<?php echo $target->id_branch?>" id="valtarget" ></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px;text-align: center"  name="asp_target[]" class="form-control asptarget<?php echo $target->id_branch?>" id="asptarget" ></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px;text-align: center"  name="revenue_target[]" class="form-control revenuetarget<?php echo $target->id_branch?>" id="revtarget" ></td>
                                <?php } else{ ?>
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                    <td style="border-left: 1px solid #cccccc;"></td>
                                <?php } ?>
                            </tr>
                            <?php $sr++; $old_name=$target->id_branch;  } ?>
                            <tr style="background-color: #ffffcc">
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                <td style="border-left: 1px solid #cccccc;"> <b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                <td style="border-left: 1px solid #cccccc;"><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                <td style="border-left: 1px solid #cccccc;"><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                                <td style="border-left: 1px solid #cccccc;"><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"></div></td>
                            </tr>
                             <tr style="background-color: #c6e6f5">
                                <td style="border-left: 1px solid #cccccc;" ></td>
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"></td>     
                                <td style="border-left: 1px solid #cccccc;"><b>Zone Total</b></td>            
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo $zb_volum; ?></b></td>                                    
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($zb_value); ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($zb_asp); ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"><b><?php echo number_format($zb_rev,2).'%'; ?></b></td>
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>
                                <td style="border-left: 1px solid #cccccc;"></td>
                            </tr>
                        </tbody>
                        
                    </table>
                <div class="clearfix"></div><br>
                </div>
                <?php if($current_monthyear == $monthyear) { ?>
                    <div>
                        <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_branch_target_data" style="margin-right: 20px;">Submit</button>
                    </div>
                <?php } ?>
                <div class="clearfix"></div><br>
            </form>
        <?php }  else{ ?>
        <!--*******Display Target Data****************-->
            <form id="myform">
                <div  style="overflow-x: auto;height: 700px">
                <table class="table table-bordered table-condensed text-center " style="margin-bottom: 0" id="branch_target_data">
                    <thead class="fixheader" style="background-color: #c6e6f5">
                       <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="4"><center><?php //echo date('F',strtotime('-1 month' , strtotime($monthyear)));?> Last Month Acheivment</center></th>
                        <th colspan="4"><center><?php echo date('F',strtotime($monthyear));?> Target</center></th>
                    </thead>
                    <thead class="fixheader1" style="background-color: #c6e6f5">
                        <th>SR.</th>
                        <th>ZONE</th>
                        <th>CLUSTER HEAD</th>
                        <th>BRANCH</th>
                        <th>PARTNER TYPE</th>
                        <th>BRANCH CATEGORY</th>
                        <th>PRODUCT CATEGORY</th>
                        <th>VOLUME ACH</th>
                        <th>VALUE ACH</th>
                        <th>Smart Phone ASP Ach</th>
                        <th>REVENUE ACH(%)</th>
                        <th>VOLUME TARGET</th>
                        <th>VALUE TARGET</th>
                        <th>ASP TARGET</th>
                        <th>REVENUE TARGET(%)</th>
                    </thead>
                    <tbody class="data_1">
                            <?php
                            
                            $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                            $zb_volum=0;$zb_value=0;$zb_asp=0;$zb_rev=0;
                            $vol=0;$valu=0;$aspa=0;$reva=0;
                            $tvol=0;$tvalu=0;$taspa=0;$treva=0;
                            
                            $old_name=$target_data[0]->id_branch;
                            
                            $sr=1;$sale=0;$stotal=0;$land=0;$rev=0; $revper=0; $aspach = 0;
                            
                            $cu_volume = 0;
                            $cu_value = 0;
                            $cu_asp = 0;
                            $cu_rev = 0;
                            
                            foreach ($target_data as $target){  
                            if($target->sale_qty){ $sale = $target->sale_qty; }else{ $sale = 0; }
                            if($target->total){ $stotal = $target->total; }else{ $stotal = 0; }
                            if($target->landing){ $land = $target->landing; }else{ $land=0;}
                            if($sale > 0){ 
                                $aspach = $stotal / $sale;
                            }else{
                                $aspach =0;
                            }
                            $rev = $stotal - $land;
                            if($land != 0){ 
                                $revper = ($rev * 100) /$land;
                            }else{
                                $revper = 0;
                            }
                            
                            //Smart Phone and Tablet sale,landing
                            $smart_sale=0;$smart_total=0;$smart_landing=0;$smart_asp=0;
                            if($target->smart_sale_qty){ $smart_sale = $target->smart_sale_qty; }else{ $smart_sale = 0; }
                            if($target->smart_total){ $smart_total = $target->smart_total; }else{ $smart_total = 0; }
                            if($target->smart_landing){ $smart_landing = $target->smart_landing; }else{ $smart_landing=0;}
                            
                             if($smart_sale > 0){ 
                                $smart_asp = $smart_total / $smart_sale;
                            }else{
                                $smart_asp =0;
                            }
                            
                            if($target->id_product_category ==1 || $target->id_product_category ==32){
                                $asp_acheivement = $smart_asp;
                            }else{
                                $asp_acheivement = $aspach;
                            }
                            $cu_volume = $target->volume;
                            $cu_value = $target->value;
                            $cu_asp = $target->asp;
                            $cu_rev = $target->revenue;
                            $idbranch_tareget = $target->id_branch_target;
                            
//                            foreach ($current_target_data as $cur_target){
//                                if($cur_target->idbranch == $target->id_branch && $cur_target->idproductcategory == $target->id_product_category){
//                                    $cu_volume = $cur_target->volume;
//                                    $cu_value = $cur_target->value;
//                                    $cu_asp = $cur_target->asp;
//                                    $cu_rev = $cur_target->revenue;
//                                    
//                                    $idbranch_tareget = $cur_target->id_branch_target;
//                                }
//                            }
                            
                            //***********Branch Wise Total SUM *************
                            if($old_name == $target->id_branch){
                                $b_volum = $b_volum+$sale;
                                $b_value = $b_value+$stotal;
                                if($target->id_product_category ==1 || $target->id_product_category ==32){
                                    $b_asp = $b_asp+$smart_asp;    
                                }else{
                                    $b_asp = $b_asp+$aspach;
                                }
                                $b_rev = $b_rev+$revper;
                                
                                $vol = $vol+$cu_volume;
                                $valu = $valu+$cu_value;
                                $aspa = $aspa+$cu_asp;
                                $reva = $reva+$cu_rev;
                                
                            }else{ ?>
                                <tr style="background-color: #ffffcc">
                                    <td></td>
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td><b>Total</b></td>            
                                    <td><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                    <td><b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                    <td><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                    <td><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                    <td><div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo $vol; $tvol = $tvol + $vol; ?></div></td>
                                    <td><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($valu); $tvalu = $tvalu + $valu; ?></div></td>
                                    <td><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($aspa); $taspa = $taspa + $aspa;?></div></td>
                                    <td><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"><?php echo number_format($reva,2).'%'; $treva = $treva + $reva; ?></div></div></td>
                                    
                                </tr>
                                <?php   
                                $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                $vol=0;$valu=0;$aspa=0;$reva=0;
                                
                                $b_volum = $b_volum+$sale;
                                $b_value = $b_value+$stotal;
                                if($target->id_product_category ==1 || $target->id_product_category ==32){
                                    $b_asp = $b_asp+$smart_asp;    
                                }else{
                                    $b_asp = $b_asp+$aspach;
                                }
                                $b_rev = $b_rev+$revper;
                                
                                $vol = $vol+$cu_volume;
                                $valu = $valu+$cu_value;
                                $aspa = $aspa+$cu_asp;
                                $reva = $reva+$cu_rev;
                            }?>
                            <tr class="br<?php echo $target->id_branch?>">
                                <td><?php echo $sr; ?></td>
                                <td><?php echo $target->zone_name; ?></td>
                                <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $target->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                                <td class="fixleft"><?php echo $target->branch_name; ?><input type="hidden" name="idbranch[]" class="form-control idbranch" value="<?php echo $target->id_branch; ?>"></td>
                                <td><?php echo $target->partner_type; ?></td>
                                <td><?php echo $target->branch_category_name; ?></td>
                                <td> <?php echo $target->product_category_name; ?><input type="hidden" name="idproductcat[]" class="form-control" value="<?php echo $target->id_product_category; ?>"></td>
                                <td><?php echo $sale; ?></td>
                                <td><?php echo round($stotal); ?></td>
                                <td><?php echo round($asp_acheivement); //round($aspach); ?></td>
                                <td><?php echo number_format($revper,2).'%';?></td>
<!--                                <td><?php echo round($rev);?></td>-->
                                <?php if($current_monthyear == $monthyear) { ?>
                                <td><input type="text" style="width:120px;text-align: center"  name="volume_target[]" class="form-control volumetarget<?php echo $target->id_branch?>" id="voltarget" <?php if($cu_volume > 0){?> readonly <?php } ?> value="<?php echo round($cu_volume); ?>"></td>
                                    <td><input type="text" style="width:120px;text-align: center"  name="value_target[]" class="form-control valuetarget<?php echo $target->id_branch?>" id="valtarget" <?php if($cu_value > 0){?> readonly <?php } ?>  value="<?php echo round($cu_value); ?>"></td>
                                    <td><input type="text" style="width:120px;text-align: center"   name="asp_target[]" class="form-control asptarget<?php echo $target->id_branch?>" id="asptarget" <?php if($cu_asp > 0){?> readonly <?php } ?> value="<?php echo round($cu_asp); ?>"></td>
                                    <td><input type="text" style="width:120px;text-align: center"  name="revenue_target[]" class="form-control revenuetarget<?php echo $target->id_branch?>" id="revtarget" <?php if($cu_rev > 0){?> readonly <?php } ?> value="<?php echo number_format($cu_rev,2); ?>">
                                        <input type="hidden" style="width:120px;text-align: center"  name="idbranch_target[]"  value="<?php echo $idbranch_tareget?>">
                                    </td>
                                <?php } else{ ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                <?php } ?>
                            </tr>
                            <?php $sr++; $old_name=$target->id_branch;  } ?>
                             <tr style="background-color: #ffffcc">
                                    <td></td>
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td></td>     
                                    <td><b>Total</b></td>            
                                    <td><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                    <td><b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                    <td><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                    <td><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                     <td><div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo $vol; $tvol = $tvol + $vol; ?></div></td>
                                    <td><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($valu); $tvalu = $tvalu + $valu; ?></div></td>
                                    <td><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($aspa); $taspa = $taspa + $aspa;?></div></td>
                                    <td><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"><?php echo number_format($reva,2).'%'; $treva = $treva + $reva; ?></div></div></td>
                                </tr>
                                 <tr style="background-color: #c6e6f5">
                            <td></td>
                            <td></td>     
                            <td></td>     
                            <td></td>     
                            <td></td>     
                            <td></td>     
                            <td><b>Zone Total</b></td>            
                            <td><b><?php echo $zb_volum; ?></b></td>                                    
                            <td><b><?php echo round($zb_value); ?></b></td>
                            <td><b><?php echo round($zb_asp); ?></b></td>
                            <td><b><?php echo round($zb_rev).'%'; ?></b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                </table>
                </div>
                 <div class="clearfix"></div><br>
                <div>
                    <input type="hidden" name="edit_target" value="0">
                    <?php if($all_taret){ ?>
                   <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_branch_target_data" style="margin-right: 20px;">Update</button>
                    <?php } else{ ?>
                         <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_branch_target_data" style="margin-right: 20px;">Submit</button>
                    <?php }?>
                </div>
            </form>
            <div class="clearfix"></div><br>
           
        <?php } }?>
         <script>
            $(document).ready(function (){
                $("#myform").on("submit", function(){
                    $(".img").fadeIn();
                });
                  
                $(document).on('change','#voltarget,#valtarget', function(e){
                   var parenttr = $(this).closest('td').parent('tr');
                   var volume_target = +$(parenttr).find('#voltarget').val();
                   var value_target = +$(parenttr).find('#valtarget').val();
                   var asp = 0
                   var valtar =0;
                   if(volume_target > 0 && volume_target != ''){
                        asp = Math.round(value_target/volume_target);
                       // $(parenttr).find('#asptarget').val(asp);
                   }else{
                       asp =0 ;
                       valtar = 0;
                       $(parenttr).find('#asptarget').val(asp);
                       $(parenttr).find('#valtarget').val(valtar);
                   }
                 
                    var total_asp_sum =0;
                    var total_volume_sum =0;
                    var total_value_sum =0;
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                   
                   $('.br'+idbranch).each(function () {
                        $(this).find('.asptarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_asp_sum += parseFloat(total_basic);
                            }
                        });
                        $(this).find('.volumetarget'+idbranch).each(function () {
                            var total_basic_volum = $(this).val();
                            if (!isNaN(total_basic_volum) && total_basic_volum.length !== 0) {
                                total_volume_sum += parseFloat(total_basic_volum);
                            }
                        });

                        $(this).find('.valuetarget'+idbranch).each(function () {
                            var total_basic_val = $(this).val();
                            if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                total_value_sum += parseFloat(total_basic_val);
                            }
                        });
                     });
                    $('.volumetotal'+idbranch).html(total_volume_sum);
                    $('.asptotal'+idbranch).html(total_asp_sum);
                    $('.valuetotal'+idbranch).html(total_value_sum);
                   
                });
                
                $(document).on('change','#revtarget', function(e){
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                    var total_rev_sum =0;
                    $('.br'+idbranch).each(function () {
                        // basic cal
                        $(this).find('.revenuetarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_rev_sum += parseFloat(total_basic);
                            }
                        });

                     });
                    $('.revtotal'+idbranch).html(total_rev_sum);
                });
                $(document).on('change','#asptarget', function(e){
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                    var total_rev_sum =0;
                    $('.br'+idbranch).each(function () {
                        // basic cal
                        $(this).find('.asptarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_rev_sum += parseFloat(total_basic);
                            }
                        });

                     });
                    $('.asptotal'+idbranch).html(total_rev_sum);
                });
               
            });
        </script>
        <div>
    <?php }
    public function save_branch_target_data(){
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $volume_target = $this->input->post('volume_target');
        $value_target = $this->input->post('value_target');
        $asp_target = $this->input->post('asp_target');
        $revenue_target = $this->input->post('revenue_target');
        $entrytime = date('Y-m-d H:i:s');
        $month_year = date('Y-m');
        
        for($i=0; $i<count($idbranch); $i++){
            $branch_target_data = $this->Target_model->ajax_check_branch_target_data($idbranch[$i], $month_year);
            if($branch_target_data){
                if($idproductcat[$i] == 1){
                    $data = array(
                        'idbranch' => $idbranch[$i],
                        'date' => date('Y-m-d'),
                        'month_year' => $month_year,
                        'idproductcategory' => $idproductcat[$i],
                        'volume' => $volume_target[$i],
                        'value' => $value_target[$i],
                        'asp' => $asp_target[$i],
                        'revenue' => $revenue_target[$i],
                        'entrytime' => $entrytime,
                        'iduser' => $_SESSION['id_users'],
                    );
                    $this->Target_model->update_branch_target($data, $branch_target_data->id_branch_target);
                }else{
                    $data = array(
                        'idbranch' => $idbranch[$i],
                        'date' => date('Y-m-d'),
                        'month_year' => $month_year,
                        'idproductcategory' => $idproductcat[$i],
                        'volume' => $volume_target[$i],
                        'value' => $value_target[$i],
                        'asp' => $asp_target[$i],
                        'revenue' => $revenue_target[$i],
                        'entrytime' => $entrytime,
                        'iduser' => $_SESSION['id_users'],
                    );
                    $this->Target_model->save_branch_target($data);
                }
            }else{
                $data = array(
                    'idbranch' => $idbranch[$i],
                    'date' => date('Y-m-d'),
                    'month_year' => $month_year,
                    'idproductcategory' => $idproductcat[$i],
                    'volume' => $volume_target[$i],
                    'value' => $value_target[$i],
                    'asp' => $asp_target[$i],
                    'revenue' => $revenue_target[$i],
                    'entrytime' => $entrytime,
                    'iduser' => $_SESSION['id_users'],
                );
                $this->Target_model->save_branch_target($data);
            }
        }
        
        $this->session->set_flashdata('save_data', 'Branch Target Saved Successfully !');
        redirect('Target/branch_target_setup');
        
    }
    
    public function update_branch_target_data(){
        $edit_target = $this->input->post('edit_target');
        
        $idbranch = $this->input->post('idbranch');
        $idproductcat = $this->input->post('idproductcat');
        $volume_target = $this->input->post('volume_target');
        $value_target = $this->input->post('value_target');
        $asp_target = $this->input->post('asp_target');
        $revenue_target = $this->input->post('revenue_target');
        $idbranch_target = $this->input->post('idbranch_target');
        $entrytime = date('Y-m-d H:i:s');
        $month_year = date('Y-m');
        for($i=0; $i<count($idbranch); $i++){
            if($idbranch_target[$i] != ''){
                if($volume_target[$i] > 0){
                    $data = array(
                        'idbranch' => $idbranch[$i],
                        'date' => date('Y-m-d'),
                        'month_year' => date('Y-m'),
                        'idproductcategory' => $idproductcat[$i],
                        'volume' => $volume_target[$i],
                        'value' => $value_target[$i],
                        'asp' => $asp_target[$i],
                        'revenue' => $revenue_target[$i],
                        'entrytime' => $entrytime,
                        'iduser' => $_SESSION['id_users'],
                    );
                    $this->Target_model->update_branch_target($data, $idbranch_target[$i]);
                }
            }else{
                 $data = array(
                    'idbranch' => $idbranch[$i],
                    'date' => date('Y-m-d'),
                    'month_year' => $month_year,
                    'idproductcategory' => $idproductcat[$i],
                    'volume' => $volume_target[$i],
                    'value' => $value_target[$i],
                    'asp' => $asp_target[$i],
                    'revenue' => $revenue_target[$i],
                    'entrytime' => $entrytime,
                    'iduser' => $_SESSION['id_users'],
                );
                $this->Target_model->save_branch_target($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Branch Target Saved Successfully !');
         if($edit_target == 1){
            redirect('Target/branch_target_setup_edit');
        }else{
            redirect('Target/branch_target_setup');
        }
    }

    //************* Branch Target Report ******************
    
    public function branch_target_report(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/branch_target_report',$q);
    }
    
    public function ajax_get_branch_target_byidbranch(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $allbranches = $this->input->post('branches');
        
        $branch_target_data = $this->Target_model->ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches);
//        die('<pre>'.print_r($branch_target_data,1).'</pre>');
        if($branch_target_data) { ?>
            <table class="table table-bordered table-condensed" id="branch_target_report">
                <thead style="background-color: #c6e6f5" class="fixheader">
                    <th><b>BRANCH</b></th>
                    <th><b>PRODUCT CATEGORY</b></th>
                    <th><b>VOLUME TARGET</b></th>
                    <th><b>VALUE TARGET</b></th>
                    <th><b>ASP TARGET</b></th>
                    <th><b>REVENUE TARGET(%)</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                    <tr>
                        <td><?php echo $bdata->branch_name ?></td>
                        <td><?php echo $bdata->product_category_name ?></td>
                        <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?></td>
                        <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?></td>
                        <td><?php echo $bdata->asp; $tasp = $tasp + $bdata->asp; ?></td>
                        <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tvol; ?></b></td>
                        <td><b><?php echo $tval; ?></b></td>
                        <td><b><?php echo $tasp; ?></b></td>
                        <td><b><?php echo $trev; ?></b></td>
                    </tr>
                </tbody>
            </table>
        
        
        <?php } else{
            echo 'Data Not Found';
        }
    }
    
    //*************** Promotor Target Setup **********************
    
    public function promotor_target_setup(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/promotor_target_setup',$q);
    }
    
     public function ajax_get_branch_target_promotor(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $allbranches = $this->input->post('branches');
        
        $current_monthyear = date('Y-m');
        
        $branch_target_data = $this->Target_model->ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches);
        $promotor_data = $this->Target_model->ajax_get_promotor_data_byidbranch($idbranch);
        $product_cat_data = $this->Target_model->get_product_category_data();
        
//        die('<pre>'.print_r($branch_target_data,1).'</pre>');
        
        //get_promotor _target_setup_data
        $promotor_target_data = $this->Target_model->ajax_get_promotor_target_data_byid($idbranch, $monthyear);
      
//        //Current Month Target Setup and EDIT 
        
        if($current_monthyear == $monthyear){
            
            //Promotor Target Edit Form
            if($promotor_target_data){  ?>
                <div class="col-md-10 col-md-offset-1">
                    <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                        <thead style="background-color: #ffcccc;" >
                        <th style="text-align: center"><b>BRANCH</b></th>
                        <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                        <th style="text-align: center"><b>VOLUME TARGET</b></th>
                        <th style="text-align: center"><b>VALUE TARGET</b></th>
                        <th style="text-align: center"><b>ASP TARGET</b></th>
                        <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                        </thead>
                        <tbody class="data_1">
                            <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                            <tr>
                                <td><?php echo $bdata->branch_name ?></td>
                                <td><?php echo $bdata->product_category_name ?></td>
                                <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                            </tr>
                            <?php } ?>
                            <tr style="background-color: #fbe0e0">
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $tvol; ?></b></td>
                                <td><b><?php echo $tval; ?></b></td>
                                <td><b><?php echo round($tasp); ?></b></td>
                                <td><b><?php echo $trev; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div><br>
                <form id="myform">
                    <div style="height: 700px;overflow-x: auto;padding: 0">
                        <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                            <thead class="fixheader" style="background-color: #c6e6f5">
                                <th class="fixleft"></th>
                                <th class="fixleft1"></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="border: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                        <th style="border-left: 1px solid #c6e6f5"></th>
                                    <?php } else{ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                    <?php }?>
                                <?php } ?>
                            </thead>
                            <thead class="fixheader1" style="background-color: #c6e6f5"> 
                                <th class="fixleft"><b>Promotor Name</b></th>
                                <th class="fixleft1"><b>Promotor Brand</b></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <td><b>Volume</b></td>
                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <td><b>Value</b></td>
                                        <td><b>Asp</b></td>
                                        <td><b>REVENUE</b></td>
                                    <?php } else{  ?>
                                        <td><b>Connect</b></td>                            
                                    <?php } 
                                } ?>
                            </thead>
                            <tbody >
                                <?php $sr=1; foreach ($promotor_data as $pdata){
                                    $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);
                                    ?>
                                    <tr class="promotordataedit">
                                        <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                            <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                        </td>
                                        <td class="fixleft1"><?php echo $brand_data->brand_name; ?> 
                                            <input type="hidden" name="idbrand[]" value="<?php echo $brand_data->id_brand;?>">
                                        </td>
                                        <?php foreach ($promotor_target_data as $target_data){
                                            foreach ($product_cat_data as $pcat){
                                                if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category && $target_data->idbrand == $brand_data->id_brand){ ?>
                                                    <td><input type="text"  style="width: 120px;text-align: center" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->volume > 0){?> readonly <?php } ?> value="<?php echo $target_data->volume; ?>"><div style="display: none"><?php echo $target_data->volume; ?></div></td>
                                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                        <td><input type="text" style="width: 120px;text-align: center" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->value > 0){?> readonly <?php } ?> value="<?php echo $target_data->value; ?>"><div style="display: none"><?php echo $target_data->value; ?></div></td>
                                                        <td><input type="text" style="width: 120px;text-align: center" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->asp > 0){?> readonly <?php } ?> value="<?php echo $target_data->asp; ?>"><div style="display: none"><?php echo $target_data->asp; ?></div></td>
                                                        <td><input type="text" style="width: 120px;text-align: center" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->revenue > 0){?> readonly <?php } ?> value="<?php echo $target_data->revenue; ?>"><div style="display: none"><?php echo $target_data->revenue; ?></div></td>
                                                    <?php } else{ ?>
                                                        <td><input type="text" style="width: 120px;text-align: center" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->connect > 0){?> readonly <?php } ?> value="<?php echo $target_data->connect; ?>"><div style="display: none"><?php echo $target_data->connect; ?></div></td>
                                                    <?php } ?>
                                                    <script>
                                                        $(document).ready(function (){
                                                            $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                                var parenttr = $(this).closest('td').parent('tr');
                                                                var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                                var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                                var asp = 0
                                                            
                                                                if(volume_target > 0 ){
                                                                     asp = Math.round(value_target/volume_target);
                                                                     $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                                }else{
                                                                    asp = 0;
                                                                    $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                                }
                                                               
                                                                var total_volume_sum =0;
                                                                var total_value_sum =0;
                                                                $('.promotordataedit').each(function (){

                                                                   $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_basic_val = $(this).val();
                                                                        if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                            total_volume_sum += parseFloat(total_basic_val);
                                                                        }

                                                                    });

                                                                   $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_vbasic_val = $(this).val();
                                                                        if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                            total_value_sum += parseFloat(total_vbasic_val);
                                                                        }

                                                                    });
                                                                });

                                                                var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                                var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();

                                                                if(total_volume_sum > branch_volume ){
                                                                    alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                                    var volamount =0;
                                                                    +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                                    return false;
                                                                }
                                                                if(total_value_sum > branch_value ){
                                                                    alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                                    var valamount =0;
                                                                    +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                                    return false;
                                                                }

                                                            });
                                                        }); 
                                                    </script>
                                        <?php } }  } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                        <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                    </div>
                    <div class="clearfix"></div><br>
                    <input type="hidden" name="edit_promotor" value="0">
                    <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_promotor_target_data">Update</button>
                    <div class="clearfix"></div><br>
                </form>
            <?php } else{

                if($branch_target_data) { ?>
                <!--************** Branch Target Data Display **************-->

                    <div class="col-md-10 col-md-offset-1">
                        <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                            <thead style="background-color: #ffcccc;" >
                            <th style="text-align: center"><b>BRANCH</b></th>
                            <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                            <th style="text-align: center"><b>VOLUME TARGET</b></th>
                            <th style="text-align: center"><b>VALUE TARGET</b></th>
                            <th style="text-align: center"><b>ASP TARGET</b></th>
                            <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                            </thead>
                            <tbody class="data_1">
                                <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                                <tr>
                                    <td><?php echo $bdata->branch_name ?></td>
                                    <td><?php echo $bdata->product_category_name ?></td>
                                    <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                    <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                    <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                    <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="background-color: #fbe0e0">
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $tvol; ?></b></td>
                                    <td><b><?php echo $tval; ?></b></td>
                                    <td><b><?php echo round($tasp); ?></b></td>
                                    <td><b><?php echo $trev; ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div><br>

                <!--************ Promotor Target Setup *******************-->
                
                    <?php if($promotor_data){ ?>
                        <form id="myform">
                            <div style="height: 700px;overflow-x: auto;padding: 0">
                                <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                                    <thead class="fixheader" style="background-color: #c6e6f5">
                                        <th class="fixleft"></th>
                                        <th class="fixleft1"></th>
                                        <?php foreach ($product_cat_data as $pcat){ ?>
                                           <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                <th style="border-right: 1px solid #c6e6f5"></th>
                                                <th style="border: 1px solid #c6e6f5"></th>
                                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                                <th style="border-left: 1px solid #c6e6f5"></th>
                                            <?php } else{ ?>
                                                <th style="border-right: 1px solid #c6e6f5"></th>
                                                <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                            <?php }?>
                                        
                                        <?php } ?>
                                    </thead>
                                    <thead class="fixheader1" style="background-color: #c6e6f5;"> 
                                        <th class="fixleft"><b>Promotor Name</b></th>
                                        <th class="fixleft1"><b>Promotor Brand</b></th>
                                        <?php foreach ($product_cat_data as $pcat){ ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Volume</b></td>
                                            <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Value</b></td>
                                            <td style="border-top: 1px solid #cccccc"><b>Asp</b></td>
                                            <td style="border-top: 1px solid #cccccc"><b>REVENUE</b></td>
                                            <?php } else{  ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Connect</b></td>                            
                                            <?php } 
                                        } ?>

                                    </thead>
                                    <tbody>
                                        <?php $sr=1; foreach ($promotor_data as $pdata){ 
                                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                                        <tr class="promotordata">
                                            <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                                <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                            </td>
                                            <td class="fixleft1"><?php echo $brand_data->brand_name; ?> 
                                                <input type="hidden" name="idbrand[]" value="<?php echo $brand_data->id_brand;?>"></td>
                                            <?php foreach ($product_cat_data as $pcat){ ?>
                                                <td><input type="text"  style="width: 120px;text-align: center" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                               <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                    <td><input type="text" style="width: 120px;text-align: center" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                    <td><input type="text" style="width: 120px;text-align: center" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                    <td><input type="text" style="width: 120px;text-align: center" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                <?php } else{ ?>
                                                    <td><input type="text" style="width: 120px;text-align: center" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                <?php } ?>

                                                <script>
                                                    $(document).ready(function (){
                                                        $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                           var parenttr = $(this).closest('td').parent('tr');
                                                           var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                           var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                           var asp = 0
                                                           var idpcat = <?php echo $pcat->id_product_category?>;
                                                           if(volume_target != ''){
                                                                asp = Math.round(value_target/volume_target);
                                                                $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                           }else{
                                                               asp = 0;
                                                               $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                           }
                                                           
                                                            var total_volume_sum =0;
                                                            var total_value_sum =0;
                                                           $('.promotordata').each(function (){
                                                                       
                                                               $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                    var total_basic_val = $(this).val();
                                                                    if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                        total_volume_sum += parseFloat(total_basic_val);
                                                                    }
                                                                    
                                                                });
                                                                
                                                               $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                    var total_vbasic_val = $(this).val();
                                                                    if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                        total_value_sum += parseFloat(total_vbasic_val);
                                                                    }
                                                                    
                                                                });
                                                           })
                                                           
                                                           var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                           var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();
                                                           
                                                           if(total_volume_sum > branch_volume ){
                                                               alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                               var volamount =0;
                                                               +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                               return false;
                                                           }
                                                           if(total_value_sum > branch_value ){
                                                               alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                               var valamount =0;
                                                               +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                               return false;
                                                           }
                                                        });
                                                    }); 
                                            </script>
                                            <?php } ?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <script>
                                    $(document).ready(function (){
                                        $(document).on('click','.btnsavepro', function(e){
                                            var flag = 0;
                                            <?php foreach ($product_cat_data as $pcat){ ?>
                                                var total_volume_sum =0;
                                                var total_value_sum =0;
                                                $('.promotordata').each(function (){
                                                    $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                        var total_basic_val = $(this).val();
                                                        if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                            total_volume_sum += parseFloat(total_basic_val);
                                                        }

                                                    });

                                                    $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                        var total_vbasic_val = $(this).val();
                                                        if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                            total_value_sum += parseFloat(total_vbasic_val);
                                                        }
                                                    });       
                                                                       
                                                }) ;      
                                                                
                                                var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();
                                                
                                                if(total_volume_sum != branch_volume ){
                                                    alert("Total Volume Amount Shoud Be " + branch_volume);
                                                    flag = 1;
                                                }
                                                if(total_value_sum != branch_value ){
                                                    alert("Total Value Amound Shoud Be " + branch_value);
                                                     flag = 1;
                                                }
                                            <?php } ?>
                                            if(flag == 1){
                                               return false;
                                            }
                                            
                                        });
                                    }); 
                                </script>
                                
                                <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                                <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                           </div>
                            <div class="clearfix"></div><br>
                            <button class="btn btn-primary pull-right btnsavepro" formmethod="POST" formaction="<?php echo base_url()?>Target/save_promotor_target_data">Submit</button>
                            <div class="clearfix"></div><br>
                        </form>
                    <?php } else{
                        echo 'Promotor Data Not Found For This Branch';
                    }
                } else {  
                    echo 'Branch Target Not Set For This Month';
                }
            }
        }else{ ?>
            <div style="height: 700px;overflow-x: auto;padding: 0">
                 <?php if($promotor_target_data){ ?> 
                <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                    <thead class="fixheader" style="background-color: #c6e6f5">
                        <th class="fixleft"></th>
                        <th class="fixleft1"></th>
                        <?php foreach ($product_cat_data as $pcat){ ?>
                           <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                <th style="border-right: 1px solid #c6e6f5"></th>
                                <th style="border: 1px solid #c6e6f5"></th>
                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                <th style="border-left: 1px solid #c6e6f5"></th>
                            <?php } else{ ?>
                                <th style="border: 1px solid #c6e6f5"></th>
                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                            <?php }?>
                        <?php } ?>
                    </thead>
                    <thead class="fixheader1" style="background-color: #c6e6f5"> 
                        <th class="fixleft"><b>Promotor Name</b></th>
                        <th class="fixleft1"><b>Promotor Brand</b></th>
                        <?php foreach ($product_cat_data as $pcat){ ?>
                            <td><b>Volume</b></td>
                            <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                <td><b>Value</b></td>
                                <td><b>Asp</b></td>
                                <td><b>REVENUE</b></td>
                            <?php } else{  ?>
                                <td><b>Connect</b></td>                            
                            <?php } 
                        } ?>

                    </thead>
                    <tbody>
                        <?php $sr=1; foreach ($promotor_data as $pdata){
                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                            <tr>
                                <td class="fixleft"><?php echo $pdata->user_name; ?></td>
                                <td class="fixleft1"><?php echo $brand_data->brand_name; ?></td>
                                <?php foreach ($promotor_target_data as $target_data){
                                    foreach ($product_cat_data as $pcat){
                                        if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category && $target_data->idbrand == $brand_data->id_brand){ ?>
                                            <td><?php echo $target_data->volume; ?></td>
                                          <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                <td><?php echo $target_data->value; ?></td>
                                                <td><?php echo $target_data->asp; ?></td>
                                                <td><?php echo $target_data->revenue; ?></td>
                                            <?php } else { ?>
                                                <td><?php echo $target_data->connect; ?></td>
                                <?php }  }  } } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else{ 
                    echo 'Data Not Found For Selected Branch And Month';
                } ?>
            </div>
        <?php } ?>
                <script>
                    $(document).ready(function (){
                       $("#myform").on("submit", function(){
                            $(".img").fadeIn();
                        }); 
                    });
                </script>
        <?php 
    }
    
    
    
    public function save_promotor_target_data(){
        $product_cat_data = $this->Target_model->get_product_category_data();
        $entry_time = date('Y-m-d H:i:s');
        
        $iduser = $this->input->post('iduser');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('month_year');
        
        $pvol = $this->input->post('p_volume');
        $pval = $this->input->post('p_value');
        $pasp = $this->input->post('p_asp');
        $preve = $this->input->post('p_revenue');
        $pconnect = $this->input->post('p_connect');
        
        for($i=0; $i<count($iduser); $i++){
            foreach ($product_cat_data as $pcat){
                if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ 
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'value' => $pval[$pcat->id_product_category][$i],
                        'asp' => $pasp[$pcat->id_product_category][$i],
                        'revenue' => $preve[$pcat->id_product_category][$i],
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }else{
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'connect' => $pconnect[$pcat->id_product_category][$i],
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }
                $this->Target_model->save_promotor_target_data($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Promotor Target Saved Successfully !');
        redirect('Target/promotor_target_setup');
        
    }
    public function update_promotor_target_data(){
               
        $product_cat_data = $this->Target_model->get_product_category_data();
        $entry_time = date('Y-m-d H:i:s');
        
        $iduser = $this->input->post('iduser');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('month_year');
        
        $pvol = $this->input->post('p_volume');
        $pval = $this->input->post('p_value');
        $pasp = $this->input->post('p_asp');
        $preve = $this->input->post('p_revenue');
        $pconnect = $this->input->post('p_connect');
        $edit_promotor = $this->input->post('edit_promotor');
        
        $this->Target_model->delete_promotor_target_data_byid($idbranch, $monthyear);
        
        for($i=0; $i<count($iduser); $i++){
            foreach ($product_cat_data as $pcat){
                if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'value' => $pval[$pcat->id_product_category][$i],
                        'asp' => $pasp[$pcat->id_product_category][$i],
                        'revenue' => $preve[$pcat->id_product_category][$i],
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }else{
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'connect' => $pconnect[$pcat->id_product_category][$i],
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }
                $this->Target_model->save_promotor_target_data($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Promotor Target Saved Successfully !');
         if($edit_promotor == 1){
            redirect('Target/promotor_target_setup_edit');
        }else{
            redirect('Target/promotor_target_setup');
        }
    }
    
       
     //**************** MTD Acheivement Report*****************
    
    public function mtd_acheivement_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('target/mtd_acheivement_report',$q);
    }
    
    public function ajax_get_mtd_achivement_byidbranch(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
//        die('<pre>'.print_r($_POST,1).'</pre>');
        if($idbranch != ''){
            $target_data = $this->Target_model->ajax_get_mtd_achivement_byidbranch($from,$to,$idpcat,$allpcats,$idbranch,$allbranches);
        }else{
            $target_data = $this->Target_model->ajax_get_mtd_achivement_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzone);
        }
        $cluster_head = $this->Target_model->get_cluster_head_data();
        
        if($target_data){ 
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="mtd_achivement_report">
                   <thead style="background-color: #9dbfed" class="fixheader">
                       <th  style="text-align: center"><b>SR</b></th>
                       <!--<th><b>ZONE</b></th>-->
                       <th  style="text-align: center"><b>ZONE</b></th>
                       <th  style="text-align: center"><b>VOLUME TARGET</b></th>
                       <th  style="text-align: center"><b>VOLUME ACH</b></th>
                       <th  style="text-align: center"><b>ACH(%)</b></th>
                       <th  style="text-align: center"><b>VALUE TARGET</b></th>
                       <th  style="text-align: center"><b>VALUE ACH</b></th>
                       <th  style="text-align: center"><b>ACH(%)</b></th>
                       <th  style="text-align: center"><b>ASP TARGET</b></th>
                       <th  style="text-align: center"><b>ASP ACH</b></th>
                       <th  style="text-align: center"><b>ACH(%)</b></th>
                       <th  style="text-align: center"><b>REVENUE</b></th>
                       <th  style="text-align: center"><b>REVENUE ACH</b></th>
                   </thead>
                   <tbody class="data_1">
                       <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                       $sale_qty =0;$sale_total=0;$sale_landing=0;
                       $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                       $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                       $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                       $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                       $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                       $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                        $num_cnt = 0;

                        $sr_qty = 0;$sr_total=0;$sr_landing=0;

                       foreach ($target_data as $target){ 

                           $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone);
//                               die(print_r($branch_cnt));
                           if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                           if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                           if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                           if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                            if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                            if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                           if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                           if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                           if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                           if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                           if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                           if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                           $sale_qty = $sale_qty - $sr_qty;
                            $sale_total = $sale_total - $sr_total;
                            $sale_landing = $sale_landing - $sr_landing;

                           //Volume Achivement Per
                           if($tar_volume > 0){
                               $vol_ach_per = ($sale_qty/$tar_volume)*100;
                           }else{
                               $vol_ach_per = 0;
                           }

                           //Value Achivement Per
                           if($tar_value > 0){
                               $val_ach_per = ($sale_total/$tar_value)*100;
                           }else{
                               $val_ach_per = 0;
                           }

                           //Asp Achivement
                           if($idpcat == 1|| $idpcat == 32){
                               if($smart_sale_qty > 0){ 
                                   $tar_asp_achiv = $smart_total/$smart_sale_qty;
                               }else{
                                   $tar_asp_achiv = 0;
                               }
                           }else{
                               if($sale_qty > 0){ 
                                   $tar_asp_achiv = $sale_total/$sale_qty;
                               }else{
                                   $tar_asp_achiv = 0;
                               }
                           }

                           //Target Achivement Per
                           if($tar_asp > 0){
                               $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                           }else{
                               $tar_asp_per = 0;
                           }

                         //Revenue Percentage  
                           if($sale_landing > 0){
                               $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                           }else{
                               $rev_per = 0;
                           }

                           ?>
                       <tr style="text-align: center">
                           <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                           <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                               if($clust->idzone == $target->id_zone){
                                   echo $clust->clust_name.', ';
                               } } ?>
                           </td>-->
                           <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                           <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                           <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                           <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                           <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                           <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                           <td><?php echo round($tar_asp); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                           <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                           <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                           <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                           <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                       </tr>
                       <?php } ?>
                       <tr style="text-align: center">
                           <td></td>
                           <!--<td></td>-->
                           <td><b>Total</b></td>
                            <td><b><?php echo round($total_volume_target,1);?></b></td>
                            <td><b><?php echo round($total_volume_ach,1);?></b></td>
                            <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                            <td><b><?php echo round($total_value_target,1);?></b></td>
                            <td><b><?php echo round($total_value_ach,1);?></b></td>
                            <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                            <td><b><?php echo round($total_asp_target/$num_cnt,1);?></b></td>
                            <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                            <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                            <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                            <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                       </tr>
                   </tbody>
               </table>
            <?php } else{ ?>
                <table class="table table-bordered table-condensed text-center" id="mtd_achivement_report" >
                    <thead  style="background-color: #9dbfed"  class="fixheader">
                        <th style="text-align: center"> <b>SR</b></th>
                        <th style="text-align: center"><b>ZONE</b></th>
                        <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                        <th style="text-align: center"><b>BRANCH</b></th>
                        <th style="text-align: center"><b>PARTNER TYPE</b></th>
                        <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                        <th style="text-align: center"><b>VOLUME TARGET</b></th>
                        <th style="text-align: center"><b>VOLUME ACH</b></th>
                        <th style="text-align: center"><b>ACH(%)</b></th>
                        <th style="text-align: center"><b>VALUE TARGET</b></th>
                        <th style="text-align: center"><b>VALUE ACH</b></th>
                        <th style="text-align: center"><b>ACH(%)</b></th>
                        <th style="text-align: center"><b>ASP TARGET</b></th>
                        <th style="text-align: center"><b>ASP ACH</b></th>
                        <th style="text-align: center"><b>ACH(%)</b></th>
                        <th style="text-align: center"><b>REVENUE</b></th>
                        <th style="text-align: center"><b>REVENUE ACH</b></th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                        $sale_qty =0;$sale_total=0;$sale_landing=0;
                        $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                        $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                        $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                        $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                        $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                        $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                        $num_cnt = 0;             

                        $sr_qty = 0;$sr_total=0;$sr_landing=0;

                        foreach ($target_data as $target){ 
                            if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                            if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                            if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   

                            if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                            if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                            if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                            if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                            if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                            if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                            if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                            if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                            $sale_qty = $sale_qty - $sr_qty;
                            $sale_total = $sale_total - $sr_total;
                            $sale_landing = $sale_landing - $sr_landing;

                            //Volume Achivement Per
                            if($tar_volume > 0){
                                $vol_ach_per = ($sale_qty/$tar_volume)*100;
                            }else{
                                $vol_ach_per = 0;
                            }

                            //Value Achivement Per
                            if($tar_value > 0){
                                $val_ach_per = ($sale_total/$tar_value)*100;
                            }else{
                                $val_ach_per = 0;
                            }

                            //Asp Achivement
                            if($idpcat == 1|| $idpcat == 32){
                                if($smart_sale_qty > 0){ 
                                    $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                }else{
                                    $tar_asp_achiv = 0;
                                }
                            }else{
                                if($sale_qty > 0){ 
                                    $tar_asp_achiv = $sale_total/$sale_qty;
                                }else{
                                    $tar_asp_achiv = 0;
                                }
                            }

                            //Target Achivement Per
                            if($tar_asp > 0){
                                $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                            }else{
                                $tar_asp_per = 0;
                            }

                          //Revenue Percentage  
                            if($sale_landing > 0){
                                $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                            }else{
                                $rev_per = 0;
                            }

                            ?>
                        <tr style="text-align: center">
                            <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                            <td><?php echo $target->zone_name; ?></td>
                            <td><?php foreach ($cluster_head as $clust){
                                if($clust->clustbranch == $target->id_branch){
                                    echo $clust->clust_name.', ';
                                } } ?>
                            </td>
                            <td><?php echo $target->branch_name ?></td>
                            <td><?php echo $target->partner_type ?></td>
                            <td><?php echo $target->branch_category_name ?></td>
                            <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                            <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                            <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                            <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                            <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                            <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                            <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                            <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                            <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                            <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                            <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                        </tr>
                        <?php } ?>
                        <tr style="text-align: center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo round($total_volume_target,1);?></b></td>
                            <td><b><?php echo round($total_volume_ach,1);?></b></td>
                            <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                            <td><b><?php echo round($total_value_target,1);?></b></td>
                            <td><b><?php echo round($total_value_ach,1);?></b></td>
                            <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                            <td><b><?php echo round($total_asp_target/$num_cnt,1);?></b></td>
                            <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                            <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                            <td><b><?php echo  round($total_revenue/$num_cnt,1).'%';?></b></td>
                            <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                        </tr>
                    </tbody>
                </table>
       <?php } }
    }
    /*
    public function ajax_get_mtd_achivement_byidzone(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $target_data = $this->Target_model->ajax_get_mtd_achivement_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($cluster_head,1).'</pre>');
        
        if($target_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="mtd_achivement_report">
                       <thead style="background-color: #9dbfed" class="fixheader">
                           <th  style="text-align: center"><b>SR</b></th>
                           <!--<th><b>ZONE</b></th>-->
                           <th  style="text-align: center"><b>ZONE</b></th>
                           <th  style="text-align: center"><b>VOLUME TARGET</b></th>
                           <th  style="text-align: center"><b>VOLUME ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>VALUE TARGET</b></th>
                           <th  style="text-align: center"><b>VALUE ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>ASP TARGET</b></th>
                           <th  style="text-align: center"><b>ASP ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>REVENUE</b></th>
                           <th  style="text-align: center"><b>REVENUE ACH</b></th>
                       </thead>
                       <tbody class="data_1">
                           <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                           $sale_qty =0;$sale_total=0;$sale_landing=0;
                           $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                           $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                           $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                           $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                           $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                           $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                            $num_cnt = 0;
                            
                            $sr_qty = 0;$sr_total=0;$sr_landing=0;
                             
                           foreach ($target_data as $target){ 
                               
                               $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone);
//                               die(print_r($branch_cnt));
                               if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                               if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                               if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                               if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                                if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                                if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                                if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}
                         
                               if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                               if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                               if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                               if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                               if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                               if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                               $sale_qty = $sale_qty - $sr_qty;
                                $sale_total = $sale_total - $sr_total;
                                $sale_landing = $sale_landing - $sr_landing;
                        
                               //Volume Achivement Per
                               if($tar_volume > 0){
                                   $vol_ach_per = ($sale_qty/$tar_volume)*100;
                               }else{
                                   $vol_ach_per = 0;
                               }

                               //Value Achivement Per
                               if($tar_value > 0){
                                   $val_ach_per = ($sale_total/$tar_value)*100;
                               }else{
                                   $val_ach_per = 0;
                               }

                               //Asp Achivement
                               if($idpcat == 1|| $idpcat == 32){
                                   if($smart_sale_qty > 0){ 
                                       $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }else{
                                   if($sale_qty > 0){ 
                                       $tar_asp_achiv = $sale_total/$sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }

                               //Target Achivement Per
                               if($tar_asp > 0){
                                   $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                               }else{
                                   $tar_asp_per = 0;
                               }

                             //Revenue Percentage  
                               if($sale_landing > 0){
                                   $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                               }else{
                                   $rev_per = 0;
                               }

                               ?>
                           <tr style="text-align: center">
                               <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                               <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                                   if($clust->idzone == $target->id_zone){
                                       echo $clust->clust_name.', ';
                                   } } ?>
                               </td>-->
                               <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                               <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                               <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                               <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                               <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                               <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                               <td><?php echo round($tar_asp); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                               <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                               <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                               <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                               <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                           </tr>
                           <?php } ?>
                           <tr style="text-align: center">
                               <td></td>
                               <!--<td></td>-->
                               <td><b>Total</b></td>
                                <td><b><?php echo round($total_volume_target,1);?></b></td>
                                <td><b><?php echo round($total_volume_ach,1);?></b></td>
                                <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                                <td><b><?php echo round($total_value_target,1);?></b></td>
                                <td><b><?php echo round($total_value_ach,1);?></b></td>
                                <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                                <td><b><?php echo round($total_asp_target/$num_cnt,1);?></b></td>
                                <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                                <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                                <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                                <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                           </tr>
                       </tbody>
                   </table>
            <?php } else{ ?>
               <table class="table table-bordered table-condensed" id="mtd_achivement_report">
                <thead style="background-color: #9dbfed" class="fixheader">
                    <th  style="text-align: center"><b>SR</b></th>
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>VOLUME TARGET</b></th>
                    <th style="text-align: center"><b>VOLUME ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>VALUE TARGET</b></th>
                    <th style="text-align: center"><b>VALUE ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>ASP TARGET</b></th>
                    <th style="text-align: center"><b>ASP ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>REVENUE</b></th>
                    <th style="text-align: center"><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0; 
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    $num_cnt=0;
                    
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                     
                    foreach ($target_data as $target){ 
                        if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                        if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                             
                        if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                        if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}
                                
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                        $sale_qty = $sale_qty - $sr_qty;
                        $sale_total = $sale_total - $sr_total;
                        $sale_landing = $sale_landing - $sr_landing;
                        
                                
                        //Volume Achivement Per
                        if($tar_volume > 0){
                            $vol_ach_per = ($sale_qty/$tar_volume)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($tar_value > 0){
                            $val_ach_per = ($sale_total/$tar_value)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                        <td><?php echo $sale_total; 
                        $total_value_ach = $total_value_ach + $sale_total;
                        $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php  echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                        
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,2);?></b></td>
                        <td><b><?php echo round($total_volume_ach,2);?></b></td>
                        <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,2);?></b></td>
                        <td><b><?php echo round($total_value_ach,2);?></b></td>
                        <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,2);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php  if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,2).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
            <?php } ?>
        <?php }
    }
    */
    //Drr Achivement Report
    
    public function drr_acheivement_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('target/drr_acheivement_report',$q);
    }
    public function ajax_get_drr_achivement_byidbranch(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $selected_month = date('Y-m', strtotime($from));
        
        $first_date = $selected_month.'01';
        
//        $end_date = date('d', strtotime($from));
        $last_date = date('t', strtotime($from));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));
        
        if(date('d', strtotime($from)) == 01){
            $remaining_days = date('t', strtotime($from));
        }else{
            $remaining_days =  $last_date - $end_date;
        }
        if($idbranch != ''){
            $target_data = $this->Target_model->ajax_get_drr_achivement_byidbranch($from,$idpcat,$allpcats,$idbranch,$allbranches);
        }else{
            $target_data = $this->Target_model->ajax_get_drr_achivement_byidzone($from,$idpcat,$allpcats,$idzone,$allzone);
        }
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
        if($target_data){ 
                if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed center" id="mtd_achivement_report">
                       <thead  style="background-color: #9dbfed; text-align: center"  class="fixheader">
                           <th style="text-align: center"><b>SR</b></th>
                           <th style="text-align: center"><b>ZONE</b></th>
                           <th style="text-align: center"><b>VOLUME TARGET</b></th>
                           <th style="text-align: center"><b>VOLUME ACH</b></th>
                           <th style="text-align: center"<b>ACH(%)</b></th>
                           <th style="text-align: center"><b>VALUE TARGET</b></th>
                           <th style="text-align: center"><b>VALUE ACH</b></th>
                           <th style="text-align: center"><b>ACH(%)</b></th>
                           <th style="text-align: center"><b>ASP TARGET</b></th>
                           <th style="text-align: center"><b>ASP ACH</b></th>
                           <th style="text-align: center"><b>ACH(%)</b></th>
                           <th style="text-align: center"><b>REVENUE</b></th>
                           <th style="text-align: center"><b>REVENUE ACH</b></th>
                       </thead>
                       <tbody class="data_1">
                           <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                           $sale_qty =0;$sale_total=0;$sale_landing=0;
                           $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                           $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                           $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                           $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                           $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                           $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                           
                           $volume_target = 0; $value_target=0;
                           
                            $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                            $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                            $num_cnt = 0;
                           foreach ($target_data as $target){ 
                               $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone); 
                               if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                               if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                               if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                               if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                               if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                               if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                               if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                               if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                               if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                               if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                                if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                                if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                                if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                                if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                                if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                                if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                                
                                if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                                if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                                if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                                $sale_qty = $sale_qty - $sr_qty;
                                $sale_total = $sale_total - $sr_total;
                                $sale_landing = $sale_landing - $sr_landing;
                               
                               //Volume Target
                                if($remaining_days > 0){ 
//                                    $volume_target = ($tar_volume - $sale_qty)/$remaining_days;
//                                    $value_target = ($tar_value - $sale_total)/$remaining_days;
                                    $volume_target = ($tar_volume - $c_saleqty)/$remaining_days;
                                    $value_target = ($tar_value - $c_saletotoal)/$remaining_days;
                                }else{
                                    $volume_target = 0;
                                    $value_target =0;
                                }

                                //Volume Achivement Per
                                if($volume_target != 0){
                                    $vol_ach_per = ($sale_qty/$volume_target)*100;
                                }else{
                                    $vol_ach_per = 0;
                                }

                               //Value Achivement Per
                               if($value_target != 0){
                                   $val_ach_per = ($sale_total/$value_target)*100;
                               }else{
                                   $val_ach_per = 0;
                               }

                               //Asp Achivement
                               if($idpcat == 1|| $idpcat == 32){
                                   if($smart_sale_qty > 0){ 
                                       $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }else{
                                   if($sale_qty > 0){ 
                                       $tar_asp_achiv = $sale_total/$sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }

                               //Target Achivement Per
                               if($tar_asp > 0){
                                   $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                               }else{
                                   $tar_asp_per = 0;
                               }

                             //Revenue Percentage  
                               if($sale_landing > 0){
                                   $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                               }else{
                                   $rev_per = 0;
                               }

                               ?>
                           <tr style="text-align: center">
                               <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                               <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                                   if($clust->idzone == $target->id_zone){
                                       echo $clust->clust_name.', ';
                                   } } ?>
                               </td>-->
                               <td><?php echo round($volume_target,0); $total_volume_target = $total_volume_target + $volume_target; ?></td>
                               <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                               <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                               <td><?php echo round($value_target,1); $total_value_target = $total_value_target +$value_target;  ?></td>
                               <td><?php echo round($sale_total,1); $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                               <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                               <td><?php echo round($tar_asp,1); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                               <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                               <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                               <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                               <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                           </tr>
                           <?php } ?>
                           <tr style="text-align: center">
                               <td></td>
                               <!--<td></td>-->
                               <td><b>Total</b></td>
                                <td><b><?php echo round($total_volume_target,0);?></b></td>
                                <td><b><?php echo round($total_volume_ach,0);?></b></td>
                                <td><b><?php  if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                                <td><b><?php echo round($total_value_target,0);?></b></td>
                                <td><b><?php echo round($total_value_ach,0);?></b></td>
                                <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                                <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                                <td><b><?php $tt=0; if( $total_volume_ach != 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                                <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,2).'%';}else{ echo '0%';} ?></b></td>
                                <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                                <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                           </tr>
                       </tbody>
                   </table>
        <?php } else {?>
            <table class="table table-bordered table-condensed center" id="mtd_achivement_report">
                <thead  style="background-color: #9dbfed"  class="fixheader">
                <th style="text-align: center"><b>SR </b></th>
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>VOLUME TARGET</b></th>
                    <th style="text-align: center"><b>VOLUME ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>VALUE TARGET</b></th>
                    <th style="text-align: center"><b>VALUE ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>ASP TARGET</b></th>
                    <th style="text-align: center"><b>ASP ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>REVENUE</b></th>
                    <th style="text-align: center"><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    
                    $volume_target = 0; $value_target=0;
                    
                    $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                    $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                    
                    $num_cnt = 0;
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($target_data as $target){ 
                        if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                        if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                                               
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                        if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                        if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                        if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}
                        
                        if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                        if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                        if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                       
                        if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                        if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                        $sale_qty = $sale_qty - $sr_qty;
                        $sale_total = $sale_total - $sr_total;
                        $sale_landing = $sale_landing - $sr_landing;
                        
                        //Volume Target
                        if($remaining_days > 0){ 
//                            $volume_target = ($tar_volume - $sale_qty)/$remaining_days;
//                            $value_target = ($tar_value - $sale_total)/$remaining_days;
                            
                            $volume_target = ($tar_volume - $c_saleqty)/$remaining_days;
                            $value_target = ($tar_value - $c_saletotoal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target =0;
                        }
                                                
                        //Volume Achivement Per
                        if($volume_target != 0){
                            $vol_ach_per = ($sale_qty/$volume_target)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($value_target != 0){
                            $val_ach_per = ($sale_total/$value_target)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php  echo round($volume_target,0) ; //.'='.$tar_volume.' -'. $c_saleqty.'/'.$remaining_days;
                        $total_volume_target = $total_volume_target + $volume_target; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo round($value_target,1); $total_value_target = $total_value_target +$value_target;  ?></td>
                        <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,0);?></b></td>
                        <td><b><?php echo round($total_volume_ach,0);?></b></td>
                        <td><b><?php if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,0);?></b></td>
                        <td><b><?php echo round($total_value_ach,0);?></b></td>
                        <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
        <?php } 
        }
    }
  /*  public function ajax_get_drr_achivement_byidzone(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $selected_month = date('Y-m', strtotime($from));
        
        $first_date = $selected_month.'01';
        
//        $end_date = date('d', strtotime($from));
        $last_date = date('t', strtotime($from));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));
        if(date('d', strtotime($from)) == 01){
            $remaining_days = date('t', strtotime($from));
        }else{
            $remaining_days =  $last_date - $end_date;
        }        
        
        $target_data = $this->Target_model->ajax_get_drr_achivement_byidzone($from,$idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
        
        if($target_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed center" id="mtd_achivement_report">
                       <thead  style="background-color: #9dbfed; text-align: center"  class="fixheader">
                           <th style="text-align: center"><b>SR</b></th>
                           <!--<th><b>ZONE</b></th>-->
                           <th style="text-align: center"><b>ZONE</b></th>
                           <th style="text-align: center"><b>VOLUME TARGET</b></th>
                           <th style="text-align: center"><b>VOLUME ACH</b></th>
                           <th style="text-align: center"<b>ACH(%)</b></th>
                           <th style="text-align: center"><b>VALUE TARGET</b></th>
                           <th style="text-align: center"><b>VALUE ACH</b></th>
                           <th style="text-align: center"><b>ACH(%)</b></th>
                           <th style="text-align: center"><b>ASP TARGET</b></th>
                           <th style="text-align: center"><b>ASP ACH</b></th>
                           <th style="text-align: center"><b>ACH(%)</b></th>
                           <th style="text-align: center"><b>REVENUE</b></th>
                           <th style="text-align: center"><b>REVENUE ACH</b></th>
                       </thead>
                       <tbody class="data_1">
                           <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                           $sale_qty =0;$sale_total=0;$sale_landing=0;
                           $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                           $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                           $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                           $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                           $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                           $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                           
                           $volume_target = 0; $value_target=0;
                           
                             $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                            $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                            $num_cnt = 0;
                           foreach ($target_data as $target){ 
                               
                               $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone); 
                               
                               if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                               if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                               if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                               if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                               if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                               if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                               if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                               if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                               if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                               if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                                if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                                if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                                if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                                if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                                if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                                if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                                
                                if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                                if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                                if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                                $sale_qty = $sale_qty - $sr_qty;
                                $sale_total = $sale_total - $sr_total;
                                $sale_landing = $sale_landing - $sr_landing;
                               
                               //Volume Target
                                if($remaining_days > 0){ 
//                                    $volume_target = ($tar_volume - $sale_qty)/$remaining_days;
//                                    $value_target = ($tar_value - $sale_total)/$remaining_days;
                                    $volume_target = ($tar_volume - $c_saleqty)/$remaining_days;
                                    $value_target = ($tar_value - $c_saletotoal)/$remaining_days;
                                }else{
                                    $volume_target = 0;
                                    $value_target =0;
                                }

                                //Volume Achivement Per
                                if($volume_target != 0){
                                    $vol_ach_per = ($sale_qty/$volume_target)*100;
                                }else{
                                    $vol_ach_per = 0;
                                }

                      
                               //Value Achivement Per
                               if($value_target != 0){
                                   $val_ach_per = ($sale_total/$value_target)*100;
                               }else{
                                   $val_ach_per = 0;
                               }

                               //Asp Achivement
                               if($idpcat == 1|| $idpcat == 32){
                                   if($smart_sale_qty > 0){ 
                                       $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }else{
                                   if($sale_qty > 0){ 
                                       $tar_asp_achiv = $sale_total/$sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }

                               //Target Achivement Per
                               if($tar_asp > 0){
                                   $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                               }else{
                                   $tar_asp_per = 0;
                               }

                             //Revenue Percentage  
                               if($sale_landing > 0){
                                   $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                               }else{
                                   $rev_per = 0;
                               }

                               ?>
                           <tr style="text-align: center">
                               <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                               <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                                   if($clust->idzone == $target->id_zone){
                                       echo $clust->clust_name.', ';
                                   } } ?>
                               </td>-->
                               <td><?php echo round($volume_target,0); $total_volume_target = $total_volume_target + $volume_target; ?></td>
                               <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                               <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                               <td><?php echo round($value_target,1); $total_value_target = $total_value_target +$value_target;  ?></td>
                               <td><?php echo round($sale_total,1); $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                               <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                               <td><?php echo round($tar_asp,1); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                               <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                               <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                               <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                               <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                           </tr>
                           <?php } ?>
                           <tr style="text-align: center">
                               <td></td>
                               <!--<td></td>-->
                               <td><b>Total</b></td>
                                <td><b><?php echo round($total_volume_target,0);?></b></td>
                                <td><b><?php echo round($total_volume_ach,0);?></b></td>
                                <td><b><?php  if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                                <td><b><?php echo round($total_value_target,0);?></b></td>
                                <td><b><?php echo round($total_value_ach,0);?></b></td>
                                <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                                <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                                <td><b><?php $tt=0; if( $total_volume_ach != 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                                <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,2).'%';}else{ echo '0%';} ?></b></td>
                                <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                                <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                           </tr>
                       </tbody>
                   </table>
            <?php } else{ ?>
               <table class="table table-bordered table-condensed center" id="mtd_achivement_report">
                <thead  style="background-color: #9dbfed"  class="fixheader">
                    <th style="text-align: center"><b>SR</b></th>
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>VOLUME TARGET</b></th>
                    <th style="text-align: center"><b>VOLUME ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>VALUE TARGET</b></th>
                    <th style="text-align: center"><b>VALUE ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>ASP TARGET</b></th>
                    <th style="text-align: center"><b>ASP ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>REVENUE</b></th>
                    <th style="text-align: center"><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0; 
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    
                     $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                    $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                    $volume_target = 0; $value_target=0;
                    $num_cnt =0 ;
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($target_data as $target){ 
                        if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                        if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                                               
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                         if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                        if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                        if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                        if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                        if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                        if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                       
                       //Volume Target
                        if($remaining_days > 0){ 
                            $volume_target = ($tar_volume - $c_saleqty)/$remaining_days;
                            $value_target = ($tar_value - $c_saletotoal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target =0;
                        }
                                                
                        //Volume Achivement Per
                        if($volume_target != 0){
                            $vol_ach_per = ($sale_qty/$volume_target)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($value_target != 0){
                            $val_ach_per = ($sale_total/$value_target)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; $num_cnt = $num_cnt+1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo round($volume_target,0); $total_volume_target = $total_volume_target + $volume_target; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo round($value_target,0); $total_value_target = $total_value_target +$value_target;  ?></td>
                        <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,0);?></b></td>
                        <td><b><?php echo round($total_volume_ach,0);?></b></td>
                        <td><b><?php if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,0);?></b></td>
                        <td><b><?php echo round($total_value_ach,0);?></b></td>
                        <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
            <?php } ?>
        <?php }
    }
    */
    
    //Promotor Target Sale Report
    
    public function mtd_promotor_sale_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('target/mtd_promotor_sale_report',$q);
    }
    
    public function ajax_get_mtd_promotor_sale_report_byidbranch() {
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_promotor_sale_report_byidbranch($from, $to, $idpcat, $allpcats, $idbranch, $allbranches);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="MTD_Promotor_Sale_Report">
                <thead style="background-color: #9dbfed" class="fixheader">
                <th style="text-align: center">ZONE</th>
                <th style="text-align: center">BRANCH</th>
                <th style="text-align: center">PARTNER TYPE</th>
                <th style="text-align: center">BRANCH CATEGORY</th>
                <th style="text-align: center">PROMOTER BRAND</th>
                <th style="text-align: center">PROMOTER NAME</th>
                <th style="text-align: center">VOLUME TARGET</th>
                <th style="text-align: center">VOLUME ACH</th>
                <th style="text-align: center">ACH(%)</th>
                <th style="text-align: center">VALUE TARGET</th>
                <th style="text-align: center">VALUE ACH</th>
                <th style="text-align: center">ACH(%)</th>
                <th style="text-align: center">ASP TARGET</th>
                <th style="text-align: center">ASP ACH</th>
                <th style="text-align: center">ACH(%)</th>
                <th style="text-align: center">REVENUE TARGET</th>
                <th style="text-align: center">REVENUE ACH</th>

            </thead>
            <tbody class="data_1">
                <?php 
                $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;

                $num_cnt = 0;
                $sr_qty = 0;$sr_total=0;$sr_landing=0;
                foreach ($sale_data as $sale){ 
                    if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                    if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                    if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                    if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                    if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                    if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                    if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}

                    if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                    if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                    if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                    $salqt = $salqt - $sr_qty;
                    $saletot = $saletot - $sr_total;
                    $slanding = $slanding - $sr_landing;

                    if($vol > 0){
                        $vol_ach = ($salqt / $vol)*100;
                    }else{
                        $vol_ach =0;
                    }

                    if($vall > 0){
                        $val_ach = ($saletot / $vall)*100;
                    }else{
                        $val_ach =0;
                    }
                    if($salqt > 0){
                        $asp_ach = ($saletot/ $salqt);
                    }else{
                        $asp_ach =0;
                    }

                    //Target Achivement Per
                    if($asp > 0){
                        $asp_ach_per = ($asp_ach/$asp)*100;
                    }else{
                        $asp_ach_per = 0;
                    }

                  //Revenue Percentage  
                    if($slanding > 0){
                        $rev_per = (($saletot - $slanding)*100)/$slanding;
                    }else{
                        $rev_per = 0;
                    }


                    $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                    ?>
                <tr style="text-align: center">
                    <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1; ?></td>
                    <td><?php echo $sale->branch_name; ?></td>
                    <td><?php echo $sale->partner_type; ?></td>
                    <td><?php echo $sale->branch_category_name; ?></td>
                    <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                    <td><?php echo $sale->user_name; ?></td>
                    <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                    <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                    <td><?php echo round($vol_ach,1).'%'; ?></td>
                    <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                    <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                    <td><?php echo round($val_ach,1).'%'; ?></td>
                    <td><?php echo round($asp,1); $t_asp = $t_asp +$asp; ?></td>
                    <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                    <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                    <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                    <td><?php echo round($rev_per,2).'%'; ?></td>
                </tr>
                <?php } ?>
                <tr style="text-align: center">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total</b></td>
                    <td><b><?php echo round($tvol,0); ?></b></td>
                    <td><b><?php echo round($tsal,0); ?></b></td>
                    <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                    <td><b><?php echo round($tval,0); ?></b></td>
                    <td><b><?php echo round($tsa_total,0); ?></b></td>
                    <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                    <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                    <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                    <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                    <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                    <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>
                </tr>
            </tbody>
        </table>   
        <?php }else{
            echo 'Data Not found';
        }
    }
    
     public function ajax_get_mtd_promotor_sale_report_byidzone() {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        $sale_data = $this->Target_model->get_promotor_sale_report_byidzone($from, $to, $idpcat, $allpcats, $idzone, $allzone);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="MTD_Promotor_Sale_Report">
                <thead style="background-color: #9dbfed" class="fixheader">
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">PROMOTER BRAND</th>
                    <th style="text-align: center">PROMOTER NAME</th>
                    <th style="text-align: center">VOLUME TARGET</th>
                    <th style="text-align: center">VOLUME ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">VALUE TARGET</th>
                    <th style="text-align: center">VALUE ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">ASP TARGET</th>
                    <th style="text-align: center">ASP ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">REVENUE TARGET</th>
                    <th style="text-align: center">REVENUE ACH</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    $num_cnt=0;
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($sale_data as $sale){ 
                       if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                         if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}

                        if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                        if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                        $salqt = $salqt - $sr_qty;
                        $saletot = $saletot - $sr_total;
                        $slanding = $slanding - $sr_landing;

                        if($vol > 0){
                            $vol_ach = ($salqt / $vol)*100;
                        }else{
                            $vol_ach =0;
                        }

                        if($vall > 0){
                            $val_ach = ($saletot / $vall)*100;
                        }else{
                            $val_ach =0;
                        }

                         if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }

                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }

                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }

                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot;$t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                         <td><?php echo round($asp,1); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                        <td><?php echo round($recvenue,1).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($tvol,0); ?></b></td>
                        <td><b><?php echo round($tsal,0); ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($tval,0); ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                        <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>
                    </tr>
                </tbody>
            </table>   
        <?php }else{
            echo 'Data Not found';
        }
    }
    
    public function drr_promotor_sale_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('target/drr_promotor_sale_report',$q);
    }
   
    public function ajax_get_drr_promotor_sale_report_byidbranch(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $selected_month = date('Y-m', strtotime($from));
        $first_date = $selected_month.'01';
//        $end_date = date('d', strtotime($from));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));

        $last_date = date('t', strtotime($from));
          if(date('d', strtotime($from)) == 01){
            $remaining_days = date('t', strtotime($from));
        }else{
            $remaining_days =  $last_date - $end_date;
        }
        
        $sale_data = $this->Target_model->get_drr_promotor_sale_report_byidbranch($from, $idpcat, $allpcats, $idbranch, $allbranches);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="DRR_Promotor_Sale_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">PROMOTER BRAND</th>
                    <th style="text-align: center">PROMOTER NAME</th>
                    <th style="text-align: center">VOLUME TARGET</th>
                    <th style="text-align: center">VOLUME ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">VALUE TARGET</th>
                    <th style="text-align: center">VALUE ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">ASP TARGET</th>
                    <th style="text-align: center">ASP ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">REVENUE TARGET</th>
                    <th style="text-align: center">REVENUE ACH</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                    $volume_target = 0;$value_target = 0;
                    $c_saleqty=0; $c_saletotal=0; $c_landing=0;
                    
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    
                    $num_cnt=0;
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($sale_data as $sale){ 
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        if($sale->csale_qty){ $c_saleqty = $sale->csale_qty;}else{ $c_saleqty = 0; }
                        if($sale->ctotal){ $c_saletotal = $sale->ctotal;}else{ $c_saletotal = 0; }
                        if($sale->clanding){ $c_landing = $sale->clanding;}else{ $c_landing = 0; }
                        
                        if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                        if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                        $salqt = $salqt - $sr_qty;
                        $saletot = $saletot - $sr_total;
                        $slanding = $slanding - $sr_landing;
                        
                        if($remaining_days != 0){
                            $volume_target = ($vol - $c_saleqty)/$remaining_days;
                            $value_target = ($vall - $c_saletotal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target = 0;
                        }
                                                
                        if($volume_target != 0){
                            $vol_ach = ($salqt / $volume_target)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($value_target != 0){
                            $val_ach = ($saletot / $value_target)*100;
                        }else{
                            $val_ach =0;
                        }
                        
                        if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users); ?>
                        <tr style="text-align: center">
                            <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1; ?></td>
                            <td><?php echo $sale->branch_name; ?></td>
                            <td><?php echo $sale->partner_type; ?></td>
                            <td><?php echo $sale->branch_category_name; ?></td>
                            <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><?php echo round($volume_target,2); $tvol = $tvol + $volume_target; ?></td>
                            <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                            <td><?php echo round($vol_ach,1).'%'; ?></td>
                            <td><?php echo round($value_target,1); $tval = $tval + $value_target; ?></td>
                            <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot ; $t_sale_landing = $t_sale_landing + $slanding;?></td>
                            <td><?php echo round($val_ach,1).'%'; ?></td>
                            <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                            <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                            <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                            <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                            <td><?php echo round($rev_per,2).'%'; ?></td>
                        </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($tvol,0); ?></b></td>
                        <td><b><?php echo round($tsal,0); ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($tval,0); ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                        <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>
                    </tr>
                </tbody>
            </table>   
        <?php  }
    }
     public function ajax_get_drr_promotor_sale_report_byidzone(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        $selected_month = date('Y-m', strtotime($from));
        $first_date = $selected_month.'01';
//        $end_date = date('d', strtotime($from));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));
        $last_date = date('t', strtotime($from));

        if(date('d', strtotime($from)) == 01){
            $remaining_days = date('t', strtotime($from));
        }else{
            $remaining_days =  $last_date - $end_date;
        }        
        
        $sale_data = $this->Target_model->get_drr_promotor_sale_report_byidzone($from, $idpcat, $allpcats, $idzone, $allzone);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="DRR_Promotor_Sale_Report">
                <thead style="background-color: #9dbfed" class="fixheader">
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">PROMOTER BRAND</th>
                    <th style="text-align: center">PROMOTER NAME</th>
                    <th style="text-align: center">VOLUME TARGET</th>
                    <th style="text-align: center">VOLUME ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">VALUE TARGET</th>
                    <th style="text-align: center">VALUE ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">ASP TARGET</th>
                    <th style="text-align: center">ASP ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">REVENUE TARGET</th>
                    <th style="text-align: center">REVENUE ACH</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                    $volume_target = 0;$value_target = 0;
                    $c_saleqty=0; $c_saletotal=0; $c_landing=0;

                     $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;

                    $num_cnt = 0;
                     $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($sale_data as $sale){ 
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                         if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                         if($sale->csale_qty){ $c_saleqty = $sale->csale_qty;}else{ $c_saleqty = 0; }
                        if($sale->ctotal){ $c_saletotal = $sale->ctotal;}else{ $c_saletotal = 0; }
                        if($sale->clanding){ $c_landing = $sale->clanding;}else{ $c_landing = 0; }

                        if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                        if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                        $salqt = $salqt - $sr_qty;
                        $saletot = $saletot - $sr_total;
                        $slanding = $slanding - $sr_landing;

                        if($remaining_days != 0){
                            $volume_target = ($vol - $c_saleqty)/$remaining_days;
                            $value_target = ($vall - $c_saletotal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target = 0;
                        }

                        if($volume_target != 0){
                            $vol_ach = ($salqt / $volume_target)*100;
                        }else{
                            $vol_ach =0;
                        }

                        if($value_target != 0){
                            $val_ach = ($saletot / $value_target)*100;
                        }else{
                            $val_ach =0;
                        }

                        //Asp 
                         if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }

                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }

                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; $num_cnt = $num_cnt+1; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo round($volume_target,2); $tvol = $tvol + $volume_target; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo round($value_target,1); $tval = $tval + $value_target; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot;$t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                        <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                        <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b><?php echo round($tvol,0); ?></b></td>
                        <td><b><?php echo round($tsal,0); ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($tval,0); ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                        <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>

                    </tr>
                </tbody>
            </table>   
        <?php  }
    }

    //LMTD VS MTD Sale Report
    public function lmtd_branch_sale_report(){
         $q['tab_active'] = '';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/lmtd_branch_sale_report',$q);
    }
    
    public function ajax_get_lmtd_branch_sale_report_byidbranch(){
//        die($_POST);
        $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_lmtd_branch_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="LMTD_branch_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    $t_lmtd_landing =0;
                    $t_mtd_landing = 0;
                    
                    $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    foreach ($sale_data as $sale){
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sale->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,0).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%' ; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                   
                   
                   
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,0).'%'; ?></b></td>
                        <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,0).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'% '; ?></b></td>
                    </tr>
                </tbody>
            </table>
                
        <?php }
                
    }
    
    public function ajax_get_lmtd_branch_sale_report_byidzone(){
        $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $sale_data = $this->Target_model->get_lmtd_branch_sale_report_byidzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="LMTD_branch_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                      $t_lmtd_landing =0;
                    $t_mtd_landing = 0;
                    
                     $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    foreach ($sale_data as $sale){
                        
                      if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                     
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                        <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
            <?php }else {?>
                
            <table class="table table-bordered table-condensed text-center" id="LMTD_branch_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                     <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    $t_lmtd_landing =0;$t_mtd_landing=0;
                     $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    foreach ($sale_data as $sale){
                        
                      if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sale->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,0); ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,0); ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,0); ?></td>
                        <td><?php echo round($lmtd_rev_per,2);$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2); $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,0); ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                   
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,0); ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,0); ?></b></td>
                       <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,0); ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,0); ?></b></td>
                    </tr>
                </tbody>
            </table>
                
        <?php }
        
       }
        
    }
     public function lmtd_promotor_sale_report(){
         $q['tab_active'] = '';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/lmtd_promotor_sale_report',$q);
    }
    
    public function ajax_get_lmtd_promotor_sale_report_byidbranch() {
           $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_lmtd_promotor_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="LMTS_promotor_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">Sales Promotor Brand</th>
                    <th style="text-align: center">Sales Promotor</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue </th>
                    <th style="text-align: center">MTD Revenue </th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                    $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    
                    $t_lmtd_landing =0; $t_mtd_landing=0;
                    foreach ($sale_data as $sale){
                         $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sale->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php if($brand_data) { echo $brand_data->brand_name ;} ?></td>
                        <td><?php echo $sale->user_name ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                  
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume; }else{ $lmtdasp = 0; }
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume; }else{ $mtdasp = 0; }
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                    
                      if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                    
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                        <td><b><?php echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
                
        <?php }
     
    }
    
    public function ajax_get_lmtd_prmotor_sale_report_byidzone() {
        $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
       $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $sale_data = $this->Target_model->get_lmtd_promotor_sale_report_byidbzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ 
            if($idzone == 'all'){ ?>
               <table class="table table-bordered table-condensed text-center" id="LMTS_promotor_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue </th>
                    <th style="text-align: center">MTD Revenue </th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                    $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    $t_lmtd_landing =0; $t_mtd_landing=0;
                    foreach ($sale_data as $sale){
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                       
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                  
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume; }else{ $lmtdasp = 0; }
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume; }else{ $mtdasp = 0; }
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                    
                     if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                    
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                         <td><b><?php echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
          <?php   }else { ?>
            <table class="table table-bordered table-condensed text-center" id="LMTS_promotor_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <th style="text-align: center">SR.</th>
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">Sales Promotor Brand</th>
                    <th style="text-align: center">Sales Promotor</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue </th>
                    <th style="text-align: center">MTD Revenue </th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                     $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    $t_lmtd_landing = 0; $t_mtd_landing =0;
                    foreach ($sale_data as $sale){
                         $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                       if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sale->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php if($brand_data) { echo $brand_data->brand_name ;} ?></td>
                        <td><?php echo $sale->user_name ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing = $t_lmtd_landing + $lmtd_landing ;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value;$t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                  
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume; }else{ $lmtdasp = 0; }
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume; }else{ $mtdasp = 0; }
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                    
                    
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                   
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                         <td><b><?php echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
                
        <?php }
        }   
     
    }
    
    //Edit Branch Target Setup
    
     public function branch_target_setup_edit() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $this->load->view('target/branch_target_setup_edit',$q);
    }
    public function ajax_edit_get_branch_target_setup_data(){
        $idzone = $this->input->post('idzone');
        $monthyear = $this->input->post('monthyear');
        $lastmonthyear = $this->input->post('lastmonthyear');
        
        $current_monthyear = date('Y-m');
        
        $target_data = $this->Target_model->ajax_get_branch_target_data_byidzone($idzone, $lastmonthyear, $monthyear);
        $current_target_data = $this->Target_model->ajax_get_current_month_branch_target_data_byidzone($idzone, $monthyear);
        $cluster_head = $this->Target_model->get_cluster_head_data();
        
         $all_taret= $this->Target_model->ajax_check_branch_target_data_byidproductcat($idzone, $monthyear);
        $cnttarget = 0;
        if($current_target_data){
            $cnttarget = 1;
        }else{
            $cnttarget = 0;
        }
       
        if($target_data){
            if($cnttarget == 0){ ?>
                <div style="text-align: center;font-size: 20px;color: red;">Branch Target Setup Not Done For Selected Zone</div>
            <?php   } else{   ?>
        <!--******* EDIT Display Target Data****************-->
                <form id="myform">
                    <div  style="overflow-x: auto;height: 700px">
                    <table class="table table-bordered table-condensed text-center " style="margin-bottom: 0" id="branch_target_data">
                        <thead class="fixheader" style="background-color: #c6e6f5">
                           <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="4"><center><?php //echo date('F',strtotime('-1 month' , strtotime($monthyear)));?> Last Month Acheivment</center></th>
                            <th colspan="4"><center><?php echo date('F',strtotime($monthyear));?> Target</center></th>
                        </thead>
                        <thead class="fixheader1" style="background-color: #c6e6f5">
                            <th>SR.</th>
                            <th>ZONE</th>
                            <th>CLUSTER HEAD</th>
                            <th>BRANCH</th>
                            <th>PARTNER TYPE</th>
                            <th>BRANCH CATEGORY</th>
                            <th>PRODUCT CATEGORY</th>
                            <th>VOLUME ACH</th>
                            <th>VALUE ACH</th>
                            <th>Smart Phone ASP Ach</th>
                            <th>REVENUE ACH(%)</th>
                            <th>VOLUME TARGET</th>
                            <th>VALUE TARGET</th>
                            <th>ASP TARGET</th>
                            <th>REVENUE TARGET(%)</th>
                        </thead>
                        <tbody class="data_1">
                                <?php

                                $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                $zb_volum=0;$zb_value=0;$zb_asp=0;$zb_rev=0;
                                $vol=0;$valu=0;$aspa=0;$reva=0;
                                $tvol=0;$tvalu=0;$taspa=0;$treva=0;

                                $old_name=$target_data[0]->id_branch;

                                $sr=1;$sale=0;$stotal=0;$land=0;$rev=0; $revper=0; $aspach = 0;
                                $cu_volume = 0;
                                $cu_value = 0;
                                $cu_asp = 0;
                                $cu_rev = 0;
                                $idbranch_tareget = '';
                                foreach ($target_data as $target){  
                                    if($target->sale_qty){ $sale = $target->sale_qty; }else{ $sale = 0; }
                                    if($target->total){ $stotal = $target->total; }else{ $stotal = 0; }
                                    if($target->landing){ $land = $target->landing; }else{ $land=0;}
                                    if($sale > 0){ 
                                        $aspach = $stotal / $sale;
                                    }else{
                                        $aspach =0;
                                    }
                                    $rev = $stotal - $land;
                                    if($land != 0){ 
                                        $revper = ($rev * 100) /$land;
                                    }else{
                                        $revper = 0;
                                    }

                                    //Smart Phone and Tablet sale,landing
                                    $smart_sale=0;$smart_total=0;$smart_landing=0;$smart_asp=0;
                                    if($target->smart_sale_qty){ $smart_sale = $target->smart_sale_qty; }else{ $smart_sale = 0; }
                                    if($target->smart_total){ $smart_total = $target->smart_total; }else{ $smart_total = 0; }
                                    if($target->smart_landing){ $smart_landing = $target->smart_landing; }else{ $smart_landing=0;}

                                     if($smart_sale > 0){ 
                                        $smart_asp = $smart_total / $smart_sale;
                                    }else{
                                        $smart_asp =0;
                                    }

                                    if($target->id_product_category ==1 || $target->id_product_category ==32){
                                        $asp_acheivement = $smart_asp;
                                    }else{
                                        $asp_acheivement = $aspach;
                                    }

//                                    foreach ($current_target_data as $cur_target){
//                                        if($cur_target->idbranch == $target->id_branch && $cur_target->idproductcategory == $target->id_product_category){
                                            $cu_volume = $target->volume;
                                            $cu_value = $target->value;
                                            $cu_asp = $target->asp;
                                            $cu_rev = $target->revenue;

                                            $idbranch_tareget = $target->id_branch_target;
//                                        }
//                                    }


                                    //***********Branch Wise Total SUM *************
                                    if($old_name == $target->id_branch){
                                        $b_volum = $b_volum+$sale;
                                        $b_value = $b_value+$stotal;
                                        if($target->id_product_category ==1 || $target->id_product_category ==32){
                                            $b_asp = $b_asp+$smart_asp;    
                                        }else{
                                            $b_asp = $b_asp+$aspach;
                                        }
                                        $b_rev = $b_rev+$revper;

                                        $vol = $vol+$cu_volume;
                                        $valu = $valu+$cu_value;
                                        $aspa = $aspa+$cu_asp;
                                        $reva = $reva+$cu_rev;

                                    }else{ ?>
                                        <tr style="background-color: #ffffcc">
                                            <td></td>
                                            <td></td>     
                                            <td></td>     
                                            <td></td>     
                                            <td></td>     
                                            <td></td>     
                                            <td><b>Total</b></td>            
                                            <td><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                            <td><b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                            <td><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                            <td><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                            <td><div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo $vol; $tvol = $tvol + $vol; ?></div></td>
                                            <td><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($valu); $tvalu = $tvalu + $valu; ?></div></td>
                                            <td><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($aspa); $taspa = $taspa + $aspa;?></div></td>
                                            <td><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"><?php echo number_format($reva,2).'%'; $treva = $treva + $reva; ?></div></div></td>

                                        </tr>
                                        <?php   
                                        $b_volum=0;$b_value=0;$b_asp=0;$b_rev=0;
                                        $vol=0;$valu=0;$aspa=0;$reva=0;

                                        $b_volum = $b_volum+$sale;
                                        $b_value = $b_value+$stotal;
                                        if($target->id_product_category ==1 || $target->id_product_category ==32){
                                            $b_asp = $b_asp+$smart_asp;    
                                        }else{
                                            $b_asp = $b_asp+$aspach;
                                        }
                                        $b_rev = $b_rev+$revper;

                                        $vol = $vol+$cu_volume;
                                        $valu = $valu+$cu_value;
                                        $aspa = $aspa+$cu_asp;
                                        $reva = $reva+$cu_rev;
                                    }?>
                                    <tr class="br<?php echo $target->id_branch?>">
                                        <td><?php echo $sr; ?></td>
                                        <td><?php echo $target->zone_name; ?></td>
                                        <td><?php foreach ($cluster_head as $clust){
                                            if($clust->clustbranch == $target->id_branch){
                                                echo $clust->clust_name.', ';
                                            }
                                        } ?></td>
                                        <td class="fixleft"><?php echo $target->branch_name; ?><input type="hidden" name="idbranch[]" class="form-control idbranch" value="<?php echo $target->id_branch; ?>"></td>
                                        <td><?php echo $target->partner_type; ?></td>
                                        <td><?php echo $target->branch_category_name; ?></td>
                                        <td> <?php echo $target->product_category_name; ?><input type="hidden" name="idproductcat[]" class="form-control" value="<?php echo $target->id_product_category; ?>"></td>
                                        <td><?php echo $sale; ?></td>
                                        <td><?php echo round($stotal); ?></td>
                                        <td><?php echo round($asp_acheivement); //round($aspach); ?></td>
                                        <td><?php echo number_format($revper,2).'%';?></td>
        <!--                            <td><?php echo round($rev);?></td>-->
                                        <td><input type="text" style="width:120px"  name="volume_target[]" class="form-control volumetarget<?php echo $target->id_branch?>" id="voltarget" value="<?php echo round($cu_volume); ?>"></td>
                                        <td><input type="text" style="width:120px"  name="value_target[]" class="form-control valuetarget<?php echo $target->id_branch?>" id="valtarget" value="<?php echo round($cu_value); ?>"></td>
                                        <td><input type="text" style="width:120px"   name="asp_target[]" class="form-control asptarget<?php echo $target->id_branch?>" id="asptarget" value="<?php echo round($cu_asp); ?>"></td>
                                        <td><input type="text" style="width:120px"  name="revenue_target[]" class="form-control revenuetarget<?php echo $target->id_branch?>" id="revtarget" value="<?php echo number_format($cu_rev,2); ?>">
                                            <input type="hidden" style="width:120px"  name="idbranch_target[]"  value="<?php echo $idbranch_tareget?>">
                                        </td>
                                    </tr>
                                    <?php $sr++; $old_name=$target->id_branch;  
                                } ?>
                                 <tr style="background-color: #ffffcc">
                                        <td></td>
                                        <td></td>     
                                        <td></td>     
                                        <td></td>     
                                        <td></td>     
                                        <td></td>     
                                        <td><b>Total</b></td>            
                                        <td><b><?php echo $b_volum; $zb_volum = $zb_volum + $b_volum; ?></b></td>                                    
                                        <td><b><?php echo round($b_value); $zb_value = $zb_value + $b_value;?></b></td>
                                        <td><b><?php echo round($b_asp); $zb_asp = $zb_asp + $b_asp;?></b></td>
                                        <td><b><?php echo number_format($b_rev,2).'%'; $zb_rev = $zb_rev + $b_rev ?></b></td>
                                         <td><div class="volumetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo $vol; $tvol = $tvol + $vol; ?></div></td>
                                        <td><div class="valuetotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($valu); $tvalu = $tvalu + $valu; ?></div></td>
                                        <td><div class="asptotal<?php echo $old_name?>" id="volumetarget_total"><?php echo round($aspa); $taspa = $taspa + $aspa;?></div></td>
                                        <td><div class="revtotal<?php echo $old_name?>" id="volumetarget_total"><?php echo number_format($reva,2).'%'; $treva = $treva + $reva; ?></div></div></td>
                                    </tr>
                                     <tr style="background-color: #c6e6f5">
                                <td></td>
                                <td></td>     
                                <td></td>     
                                <td></td>     
                                <td></td>     
                                <td></td>     
                                <td><b>Zone Total</b></td>            
                                <td><b><?php echo $zb_volum; ?></b></td>                                    
                                <td><b><?php echo round($zb_value); ?></b></td>
                                <td><b><?php echo round($zb_asp); ?></b></td>
                                <td><b><?php echo round($zb_rev).'%'; ?></b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                    </table>
                    </div>
                     <div class="clearfix"></div><br>
                     <div>
                         <input type="hidden" name="edit_target" value="1">
                         <?php if(count($all_taret) > 0){?>
                         <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_branch_target_data" style="margin-right: 20px;">Update</button>
                         <?php }else{ ?>
                       <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_branch_target_data" style="margin-right: 20px;">Update</button>
                         <?php } ?>
                    </div>
                </form>
                <div class="clearfix"></div><br>
        <?php } } ?>
         <script>
            $(document).ready(function (){
                $("#myform").on("submit", function(){
                    $(".img").fadeIn();
                });
                  
                $(document).on('change','#voltarget,#valtarget', function(e){
                   var parenttr = $(this).closest('td').parent('tr');
                   var volume_target = +$(parenttr).find('#voltarget').val();
                   var value_target = +$(parenttr).find('#valtarget').val();
                   var asp = 0
                   var valtar =0;
                   if(volume_target > 0 && volume_target != ''){
                        asp = Math.round(value_target/volume_target);
                       // $(parenttr).find('#asptarget').val(asp);
                   }else{
                       asp =0 ;
                       valtar = 0;
                       $(parenttr).find('#asptarget').val(asp);
                       $(parenttr).find('#valtarget').val(valtar);
                   }
                 
                    var total_asp_sum =0;
                    var total_volume_sum =0;
                    var total_value_sum =0;
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                   
                   $('.br'+idbranch).each(function () {
                        $(this).find('.asptarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_asp_sum += parseFloat(total_basic);
                            }
                        });
                        $(this).find('.volumetarget'+idbranch).each(function () {
                            var total_basic_volum = $(this).val();
                            if (!isNaN(total_basic_volum) && total_basic_volum.length !== 0) {
                                total_volume_sum += parseFloat(total_basic_volum);
                            }
                        });

                        $(this).find('.valuetarget'+idbranch).each(function () {
                            var total_basic_val = $(this).val();
                            if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                total_value_sum += parseFloat(total_basic_val);
                            }
                        });
                     });
                    $('.volumetotal'+idbranch).html(total_volume_sum);
                    $('.asptotal'+idbranch).html(total_asp_sum);
                    $('.valuetotal'+idbranch).html(total_value_sum);
                   
                });
                
                $(document).on('change','#revtarget', function(e){
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                    var total_rev_sum =0;
                    $('.br'+idbranch).each(function () {
                        // basic cal
                        $(this).find('.revenuetarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_rev_sum += parseFloat(total_basic);
                            }
                        });

                     });
                    $('.revtotal'+idbranch).html(total_rev_sum);
                });
                $(document).on('change','#asptarget', function(e){
                    var idbranch = $(this).closest('td').parent('tr').find('.idbranch').val();
                    var total_rev_sum =0;
                    $('.br'+idbranch).each(function () {
                        // basic cal
                        $(this).find('.asptarget'+idbranch).each(function () {
                            var total_basic = $(this).val();
                            if (!isNaN(total_basic) && total_basic.length !== 0) {
                                total_rev_sum += parseFloat(total_basic);
                            }
                        });

                     });
                    $('.asptotal'+idbranch).html(total_rev_sum);
                });
               
            });
        </script>
        <div>
    <?php }
  
    //Promotor Target Edit
    public function promotor_target_setup_edit(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/promotor_target_setup_edit',$q);
    }
    public function ajax_edit_promotor_promotor_data(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $allbranches = $this->input->post('branches');
        
        $current_monthyear = date('Y-m');
        
        $branch_target_data = $this->Target_model->ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches);
        $promotor_data = $this->Target_model->ajax_get_promotor_data_byidbranch($idbranch);
        $product_cat_data = $this->Target_model->get_product_category_data();
        
//        die('<pre>'.print_r($branch_target_data,1).'</pre>');
        
        //get_promotor _target_setup_data
        $promotor_target_data = $this->Target_model->ajax_get_promotor_target_data_byid($idbranch, $monthyear);
            
       //Promotor Target Edit Form
            if($promotor_target_data){  ?>
                <div class="col-md-10 col-md-offset-1">
                    <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                        <thead style="background-color: #ffcccc;" >
                        <th style="text-align: center"><b>BRANCH</b></th>
                        <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                        <th style="text-align: center"><b>VOLUME TARGET</b></th>
                        <th style="text-align: center"><b>VALUE TARGET</b></th>
                        <th style="text-align: center"><b>ASP TARGET</b></th>
                        <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                        </thead>
                        <tbody class="data_1">
                            <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                            <tr>
                                <td><?php echo $bdata->branch_name ?></td>
                                <td><?php echo $bdata->product_category_name ?></td>
                                <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                            </tr>
                            <?php } ?>
                            <tr style="background-color: #fbe0e0">
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $tvol; ?></b></td>
                                <td><b><?php echo $tval; ?></b></td>
                                <td><b><?php echo round($tasp); ?></b></td>
                                <td><b><?php echo $trev; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div><br>
                <form id="myform">
                    <div style="height: 700px;overflow-x: auto;padding: 0">
                        <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                            <thead class="fixheader" style="background-color: #c6e6f5">
                                <th class="fixleft"></th>
                                <th class="fixleft1"></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="border: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                        <th style="border-left: 1px solid #c6e6f5"></th>
                                    <?php } else{ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                    <?php }?>
                                <?php } ?>
                            </thead>
                            <thead class="fixheader1" style="background-color: #c6e6f5"> 
                                <th class="fixleft"><b>Promotor Name</b></th>
                                <th class="fixleft1"><b>Promotor Brand</b></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <td><b>Volume</b></td>
                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <td><b>Value</b></td>
                                        <td><b>Asp</b></td>
                                        <td><b>REVENUE</b></td>
                                    <?php } else{  ?>
                                        <td><b>Connect</b></td>                            
                                    <?php } 
                                } ?>

                            </thead>
                            <tbody >
                                <?php $sr=1; foreach ($promotor_data as $pdata){
                                    $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                                    <tr class="promotordataedit">
                                        <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                            <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                        </td>
                                        <td class="fixleft1"><?php echo $brand_data->brand_name; ?> 
                                            <input type="hidden" name="idbrand[]" value="<?php echo $brand_data->id_brand;?>">
                                        </td>
                                        <?php foreach ($promotor_target_data as $target_data){
                                            foreach ($product_cat_data as $pcat){
                                                if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category && $target_data->idbrand == $brand_data->id_brand){ ?>
                                                    <td><input type="text"  style="width: 120px;" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->volume; ?>"><div style="display: none"><?php echo $target_data->volume; ?></div></td>
                                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->value; ?>"><div style="display: none"><?php echo $target_data->value; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->asp; ?>"><div style="display: none"><?php echo $target_data->asp; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control" value="<?php echo $target_data->revenue; ?>"><div style="display: none"><?php echo $target_data->revenue; ?></div></td>
                                                    <?php } else{ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control" value="<?php echo $target_data->connect; ?>"><div style="display: none"><?php echo $target_data->connect; ?></div></td>
                                                    <?php } ?>
                                                    <script>
                                                        $(document).ready(function (){
                                                            $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                               var parenttr = $(this).closest('td').parent('tr');
                                                               var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                               var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                               var asp = 0
                                                            
                                                               if(volume_target > 0 ){
                                                                    asp = Math.round(value_target/volume_target);
                                                                    $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }else{
                                                                   asp = 0;
                                                                   $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }
                                                               
                                                                var total_volume_sum =0;
                                                                var total_value_sum =0;
                                                                $('.promotordataedit').each(function (){

                                                                   $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_basic_val = $(this).val();
                                                                        if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                            total_volume_sum += parseFloat(total_basic_val);
                                                                        }

                                                                    });

                                                                   $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_vbasic_val = $(this).val();
                                                                        if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                            total_value_sum += parseFloat(total_vbasic_val);
                                                                        }
                                                                    });
                                                               })

                                                               var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                               var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();

                                                               if(total_volume_sum > branch_volume ){
                                                                   alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                                   var volamount =0;
                                                                   +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                                   return false;
                                                               }
                                                               if(total_value_sum > branch_value ){
                                                                   alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                                   var valamount =0;
                                                                   +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                                   return false;
                                                               }

                                                                });
                                                        }); 
                                                    </script>
                                        <?php } }  } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                        <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                    </div>
                    <div class="clearfix"></div><br>
                    <input type="hidden" name="edit_promotor" value="1">
                    <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_promotor_target_data">Update</button>
                    <div class="clearfix"></div><br>
                </form>
            <?php } else{ ?>
                <div style="text-align: center;font-size: 20px; color:red;">Promotor Target Data Not Found</div>
            <?php }?>
      
        <script>
            $(document).ready(function (){
               $("#myform").on("submit", function(){
                    $(".img").fadeIn();
                }); 
            });
        </script>
        <?php 
    }
    
    
    //LMTD Brand Report
    
     public function lmtd_brand_sale_report(){
         $q['tab_active'] = '';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/lmtd_brand_sale_report',$q);
    }
    
    public function ajax_get_lmtd_brand_sale_report_byidbranch(){
//        die($_POST);
        $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_lmtd_brand_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="LMTD_brand_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <!--<th style="text-align: center">SR.</th>-->
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">BRAND</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                    $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    
                    $t_lmtd_landing =0;$t_mtd_landing=0;
                    foreach ($sale_data as $sale){
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <!--<td><?php echo $sr++; ?></td>-->
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                                    if($clust->clustbranch == $sale->id_branch){
                                        echo $clust->clust_name.', ';
                                    }
                                } ?></td>
                        <td><?php echo $sale->partner_type ?></td>
                        <td><?php echo $sale->branch_category_name ?></td>
                        <td><?php echo $sale->branch_name ?></td>
                        <td><?php echo $sale->brand_name ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td style="display: none"><?php echo $lmtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing= $t_lmtd_landing+ $lmtd_landing;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value; $t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%' ; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                   
                      
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                   
                    ?>
                </tbody>
                <tfoot style="text-align: center">
                        <!--<td></td>-->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                        <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                </tfoot>
            </table>
                
        <?php }
                
    }
    
    public function ajax_get_lmtd_brand_sale_report_byidzone(){
        $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $sale_data = $this->Target_model->get_lmtd_brand_sale_report_byidzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="LMTD_brand_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <!--<th style="text-align: center">SR.</th>-->
                    <th style="text-align: center">BRAND</th>
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                     $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    
                    $t_lmtd_landing=0; $t_mtd_landing=0;
                    foreach ($sale_data as $sale){
                        
                      if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                        ?>
                    <tr style="text-align: center">
                        <!--<td><?php echo $sr++; ?></td>-->
                        <td><?php echo $sale->brand_name; ?></td>
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td style="display: none"><?php echo $lmtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing= $t_lmtd_landing+ $lmtd_landing;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value; $t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                      
                    if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                   
                    ?>
                </tbody>
                    <tfoot style="text-align: center">
                        <!--<td></td>-->
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                        <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tfoot>
            </table>
            <?php }else {?>
                
            <table class="table table-bordered table-condensed text-center" id="LMTD_brand_sale_report">
                <thead  style="background-color: #99ccff"  class="fixheader">
                    <!--<th style="text-align: center">SR.</th>-->
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRAND</th>
<!--                    <th style="text-align: center">CLUSTER HEAD</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">BRANCH</th>-->
                    <th style="text-align: center">LMTD VOLUME</th>
                    <th style="text-align: center">MTD VOLUME</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD VALUE</th>
                    <th style="text-align: center">MTD VALUE</th>
                    <th style="text-align: center">GAP (%)</th>
                    <th style="text-align: center">LMTD ASP</th>
                    <th style="text-align: center">MTD ASP</th>
                    <th style="text-align: center">GAP (%)</th>
                     <th style="text-align: center">LMTD Revenue (%)</th>
                    <th style="text-align: center">MTD Revenue (%)</th>
                    <th style="text-align: center">GAP (%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                     $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    
                    $t_lmtd_landing =0; $t_mtd_landing=0;
                    
                    $old_name = $sale_data[0]->id_zone;
                    
                    foreach ($sale_data as $sale){
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }
                        
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                     ?>
                    <tr style="text-align: center">
                        <!--<td><?php echo $sr++; ?></td>-->
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php echo $sale->brand_name; ?></td>        
                        <td><?php echo round($lmtd_volume,0); $t_lmtd_volume = $t_lmtd_volume + $lmtd_volume; ?></td>
                        <td><?php echo round($mtd_volume,0); $t_mtd_volume = $t_mtd_volume + $mtd_volume; ?></td>
                        <td style="display: none"><?php echo $lmtd_volume + $mtd_volume; ?></td>
                        <td><?php echo round($volume_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_value,0); $t_lmtd_value =  $t_lmtd_value + $lmtd_value ;$t_lmtd_landing= $t_lmtd_landing+ $lmtd_landing;?></td>
                        <td><?php echo round($mtd_value,0); $t_mtd_value = $t_mtd_value + $mtd_value; $t_mtd_landing = $t_mtd_landing + $mtd_landing; ?></td>
                        <td><?php echo round($value_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_asp,0); $t_lmtd_asp = $t_lmtd_asp + $lmtd_asp;  ?></td>
                        <td><?php echo round($mtd_asp,0); $t_mtd_asp = $t_mtd_asp + $mtd_asp; ?></td>
                        <td><?php echo round($asp_gap,1).'%'; ?></td>
                        <td><?php echo round($lmtd_rev_per,2).'%';$tlmtd_rev = $tlmtd_rev + $lmtd_rev_per; ?></td>
                        <td><?php echo round($mtd_rev_per,2).'%'; $tmtd_rev = $tmtd_rev + $mtd_rev_per; ?></td>
                        <td><?php echo round($rev_gap,2).'%'; ?></td>
                    </tr>
                    <?php  $old_name=$sale->id_zone;   }
                    if($t_lmtd_volume > 0){ $t_gap_volume = ((($t_mtd_volume - $t_lmtd_volume)/$t_lmtd_volume)*100); }else{ $t_gap_volume =0; }
                    if($t_lmtd_value > 0){ $t_gap_value = ((($t_mtd_value - $t_lmtd_value)/$t_lmtd_value)*100); }else{ $t_gap_value =0; }
                    
                    if($t_lmtd_volume > 0){ $lmtdasp = $t_lmtd_value/$t_lmtd_volume;}else { $lmtdasp = 0;}
                    if($t_mtd_volume > 0){ $mtdasp = $t_mtd_value/$t_mtd_volume;}else { $mtdasp = 0; } 
                    
                    if($lmtdasp > 0){ $t_asp_gap = ((($mtdasp - $lmtdasp)/$lmtdasp)*100); }else{ $t_asp_gap =0; }
                   
                       if($t_lmtd_landing > 0){
                           $t_lmtd_rev_per = (($t_lmtd_value - $t_lmtd_landing)*100)/$t_lmtd_landing;
                    }else{
                        $t_lmtd_rev_per = 0;
                    }
                    if($t_mtd_landing > 0){
                           $t_mtd_rev_per = (($t_mtd_value - $t_mtd_landing)*100)/$t_mtd_landing;
                    }else{
                        $t_mtd_rev_per = 0;
                    }
                    
                     if($t_lmtd_rev_per > 0){ $t_rev_gap = ((($t_mtd_rev_per - $t_lmtd_rev_per)/$t_lmtd_rev_per)*100); }else{ $t_rev_gap =0; }
                   
                   
                    ?>
                    
                </tbody>
                    <tfoot style="text-align: center">
                        <!--<td></td>-->
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($t_lmtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_volume,0); ?></b></td>
                        <td><b><?php echo round($t_gap_volume,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_mtd_value,0); ?></b></td>
                        <td><b><?php echo round($t_gap_value,1).'%'; ?></b></td>
                        <td><b><?php  echo round($lmtdasp,0); ?></b></td>
                        <td><b><?php echo round($mtdasp,0); ?></b></td>
                        <td><b><?php echo round($t_asp_gap,1).'%'; ?></b></td>
                        <td><b><?php echo round($t_lmtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_mtd_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($t_rev_gap,2).'%'; ?></b></td>
                    </tfoot>
            </table>
                
        <?php }
        
       }
        
    }
    
    
    //MTD Brand Sale Report
    
     public function mtd_brand_sale_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $this->load->view('target/mtd_brand_sale_report',$q);
    }
    
    public function ajax_get_mtd_brand_sale_report_byidbranch(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $target_data = $this->Target_model->get_mtd_brand_sale_report_byidbranch($from,$to,$idpcat,$allpcats,$idbranch,$allbranches);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
        if($target_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="MTD_Brand_Sale_Report" >
                <thead  style="background-color: #9dbfed"  class="fixheader">
                <!--<th style="text-align: center"> <b>SR</b></th>-->
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>BRAND</b></th>
                    <th style="text-align: center"><b>MTD VOLUME</b></th>
                    <th style="text-align: center"><b>Contr. Volume(%)</b></th>
                    <th style="text-align: center"><b>MTD Value</b></th>
                    <th style="text-align: center"><b>Contr. Value(%)</b></th>
                    <th style="text-align: center"><b>Revenue</b></th>
                    <th style="text-align: center"><b>Revenue(%)</b></th>
                    <th style="text-align: center"><b>contr. to Gross Revenue(%)</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; 
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $tsale_qty =0;$tsale_total=0;$tsale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $rev_amount=0; $rev_per= 0;$vol_contri=0; $val_contri=0;$rev_contri=0;
                    
                    $mtd_volume_total =0;$mtd_value_total=0; $landing_total =0 ;$revenue_total=0;
                    $t_vol_con=0;$t_val_con=0;$t_rev_per=0;$t_gp=0;
                    
                    $num_cnt=0;
                    
                    foreach ($target_data as $target){ 
                        $mtd_volume_total = $mtd_volume_total + $target->sale_qty;
                        $mtd_value_total = $mtd_value_total + $target->sale_total;
                        $landing_total = $landing_total + $target->sale_landing;
                    }
                    
                    $revenue_total = $mtd_value_total - $landing_total;
                                       
                    
                    foreach ($target_data as $target){ 
                                               
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                        //Revenue Amount 
                        $rev_amount = $sale_total-$sale_landing;
                        
                        // Contibution
                        if($mtd_volume_total > 0){ $vol_contri = ($sale_qty /$mtd_volume_total)*100;}else{ $vol_contri =0;}
                        if($mtd_value_total > 0) {$val_contri = ($sale_total/$mtd_value_total)*100;}else{ $mtd_value_total =0; }
                        if($revenue_total > 0) { $rev_contri = ($rev_amount/$revenue_total)*100; }else{ $revenue_total = 0;} 
                                
                        //revenue percentage
                        
                        if($sale_total > 0){ $rev_per = ($rev_amount/$sale_total)*100;}else{ $rev_per =0; }
                          
                        ?>
                    <tr style="text-align: center">
                        <!--<td><?php // echo $sr++; ?></td>-->
                        <td><?php echo $target->zone_name; $num_cnt = $num_cnt +1; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->brand_name ?></td>
                        <td><?php echo $sale_qty;  ?></td>
                        <td><?php echo round($vol_contri,1).'%'; $t_vol_con = $t_vol_con + $vol_contri; ?></td>
                        <td><?php echo $sale_total;   ?></td>
                        <td><?php echo round($val_contri,1); $t_val_con = $t_val_con + $val_contri; ?></td>
                        <td><?php echo round($rev_amount,0);  ?></td>
                        <td><?php echo round($rev_per,2).'%'; $t_rev_per = $t_rev_per + $rev_per; ?></td>
                        <td><?php echo round($rev_contri,2).'%'; $t_gp =  $t_gp + $rev_contri; ?></td>
                        
                    </tr>
                    <?php } ?>
                </tbody>
                    <tfoot style="text-align: center">
                        <!--<td></td>-->
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($mtd_volume_total,0);?></b></td>
                        <td><b><?php echo round($t_vol_con,1).'%';?></b></td>
                        <td><b><?php echo round($mtd_value_total,0) ?></b></td>
                        <td><b><?php echo round($t_val_con,1).'%';?></b></td>
                        <td><b><?php  echo round($revenue_total,0);?></b></td>
                        <td><b><?php echo round($t_rev_per/$num_cnt,2).'%'?></b></td>
                        <td><b><?php echo round($t_gp,1).'%';?></b></td>
                    </tfoot>
                    
            </table>
        <?php }
    }
    
    public function ajax_get_mtd_brand_sale_report_byidzone(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzones = $this->input->post('allzone');
        
        $target_data = $this->Target_model->get_mtd_brand_sale_report_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzones);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
        if($target_data){ 
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="MTD_Brand_Sale_Report" >
                    <thead  style="background-color: #9dbfed"  class="fixheader">
                    <!--<th style="text-align: center"> <b>SR</b></th>-->
                        <th style="text-align: center"><b>BRAND</b></th>
                        <th style="text-align: center"><b>MTD VOLUME</b></th>
                        <th style="text-align: center"><b>Contribution(%)</b></th>
                        <th style="text-align: center"><b>MTD Value</b></th>
                        <th style="text-align: center"><b>Contr.Value(%)</b></th>
                        <th style="text-align: center"><b>Revenue</b></th>
                        <th style="text-align: center"><b>Revenue(%)</b></th>
                        <th style="text-align: center"><b>contr. to Gross Revenue(%) </b></th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; 
                        $sale_qty =0;$sale_total=0;$sale_landing=0;
                        $tsale_qty =0;$tsale_total=0;$tsale_landing=0;
                        $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                        $rev_amount=0; $rev_per= 0;$vol_contri=0; $val_contri=0;$rev_contri=0;

                        $mtd_volume_total =0;$mtd_value_total=0; $landing_total =0 ;$revenue_total=0;
                        $t_vol_con=0;$t_val_con=0;$t_rev_per=0;$t_gp=0;

                        foreach ($target_data as $target){ 
                            $mtd_volume_total = $mtd_volume_total + $target->sale_qty;
                            $mtd_value_total = $mtd_value_total + $target->sale_total;
                            $landing_total = $landing_total + $target->sale_landing;
                        }

                        $revenue_total = $mtd_value_total - $landing_total;
                        
                        $num_cnt = 0;

                        foreach ($target_data as $target){ 

                            if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                            if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                            if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                            if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                            if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                            if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                            //Revenue Amount 
                            $rev_amount = $sale_total-$sale_landing;

                            // Contibution
                            if($mtd_volume_total > 0){ $vol_contri = ($sale_qty /$mtd_volume_total)*100;}else{ $vol_contri =0;}
                            if($mtd_value_total > 0) {$val_contri = ($sale_total/$mtd_value_total)*100;}else{ $mtd_value_total =0; }
                            if($revenue_total > 0) { $rev_contri = ($rev_amount/$revenue_total)*100; }else{ $revenue_total = 0;} 

                            //revenue percentage

                            if($sale_total > 0){ $rev_per = ($rev_amount/$sale_total)*100;}else{ $rev_per =0; }

                            ?>
                        <tr style="text-align: center">
                            <!--<td><?php echo $sr++; ?></td>-->
                            <td><?php echo $target->brand_name ;$num_cnt = $num_cnt + 1;?></td>
                            <td><?php echo $sale_qty;  ?></td>
                            <td><?php echo round($vol_contri,1).'%'; $t_vol_con = $t_vol_con + $vol_contri; ?></td>
                            <td><?php echo round($sale_total);   ?></td>
                            <td><?php echo round($val_contri,1); $t_val_con = $t_val_con + $val_contri; ?></td>
                            <td><?php echo round($rev_amount,0);  ?></td>
                            <td><?php echo round($rev_per,2).'%'; $t_rev_per = $t_rev_per + $rev_per; ?></td>
                            <td><?php echo round($rev_contri,2).'%'; $t_gp =  $t_gp + $rev_contri; ?></td>

                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot style="text-align: center">
                        <!--<td></td>-->
                        <td><b>Total</b></td>
                        <td><b><?php echo round($mtd_volume_total,0);?></b></td>
                        <td><b><?php echo round($t_vol_con,1);?></b></td>
                        <td><b><?php echo round($mtd_value_total,0) ?></b></td>
                        <td><b><?php echo round($t_val_con,1);?></b></td>
                        <td><b><?php  echo round($revenue_total,0);?></b></td>
                        <td><b><?php echo round($t_rev_per/$num_cnt,2)?></b></td>
                        <td><b><?php echo round($t_gp,2).'%';?></b></td>
                    </tfoot>
                </table>
            <?php }else {?>
                <table class="table table-bordered table-condensed text-center" id="MTD_Brand_Sale_Report" >
                    <thead  style="background-color: #9dbfed"  class="fixheader">
                    <!--<th style="text-align: center"> <b>SR</b></th>-->
                        <th style="text-align: center"><b>ZONE</b></th>
                        <th style="text-align: center"><b>BRAND</b></th>
                        <th style="text-align: center"><b>MTD VOLUME</b></th>
                         <th style="text-align: center"><b>Contr. Volume(%)</b></th>
                        <th style="text-align: center"><b>MTD Value</b></th>
                        <th style="text-align: center"><b>Contr. Value(%)</b></th>
                        <th style="text-align: center"><b>Revenue</b></th>
                        <th style="text-align: center"><b>Revenue(%)</b></th>
                        <th style="text-align: center"><b>contr. to Gross Revenue(%)</b></th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; 
                        $sale_qty =0;$sale_total=0;$sale_landing=0;
                        $tsale_qty =0;$tsale_total=0;$tsale_landing=0;
                        $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                        $rev_amount=0; $rev_per= 0;$vol_contri=0; $val_contri=0;$rev_contri=0;

                        $mtd_volume_total =0;$mtd_value_total=0; $landing_total =0 ;$revenue_total=0;
                        $t_vol_con=0;$t_val_con=0;$t_rev_per=0;$t_gp=0;

                        foreach ($target_data as $target){ 
                            $mtd_volume_total = $mtd_volume_total + $target->sale_qty;
                            $mtd_value_total = $mtd_value_total + $target->sale_total;
                            $landing_total = $landing_total + $target->sale_landing;
                        }

                        $revenue_total = $mtd_value_total - $landing_total;
                        $num_cnt=0;

                        foreach ($target_data as $target){ 

                            if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                            if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                            if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                            if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                            if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                            if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                            //Revenue Amount 
                            $rev_amount = $sale_total-$sale_landing;

                            // Contibution
                            if($mtd_volume_total > 0){ $vol_contri = ($sale_qty /$mtd_volume_total)*100;}else{ $vol_contri =0;}
                            if($mtd_value_total > 0) {$val_contri = ($sale_total/$mtd_value_total)*100;}else{ $mtd_value_total =0; }
                            if($revenue_total > 0) { $rev_contri = ($rev_amount/$revenue_total)*100; }else{ $revenue_total = 0;} 

                            //revenue percentage

                            if($sale_total > 0){ $rev_per = ($rev_amount/$sale_total)*100;}else{ $rev_per =0; }

                            ?>
                        <tr style="text-align: center">
                            <!--<td><?php echo $sr++;$num_cnt = $num_cnt + 1;?></td>-->
                            <td><?php echo $target->zone_name;$num_cnt = $num_cnt + 1; ?></td>
                            <td><?php echo $target->brand_name ?></td>
                            <td><?php echo $sale_qty;  ?></td>
                            <td><?php echo round($vol_contri,1).'%'; $t_vol_con = $t_vol_con + $vol_contri; ?></td>
                            <td><?php echo round($sale_total);   ?></td>
                            <td><?php echo round($val_contri,1).'%'; $t_val_con = $t_val_con + $val_contri; ?></td>
                            <td><?php echo round($rev_amount,0);  ?></td>
                            <td><?php echo round($rev_per,2).'%'; $t_rev_per = $t_rev_per + $rev_per; ?></td>
                            <td><?php echo round($rev_contri,0).'%'; $t_gp =  $t_gp + $rev_contri; ?></td>

                        </tr>
                        <?php } ?>
                       
                    </tbody>
                    <tfoot style="text-align: center">
                            <!--<td></td>-->
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo round($mtd_volume_total,0);?></b></td>
                            <td><b><?php echo round($t_vol_con,1).'%';?></b></td>
                            <td><b><?php echo round($mtd_value_total,0) ?></b></td>
                            <td><b><?php echo round($t_val_con,1).'%';?></b></td>
                            <td><b><?php  echo round($revenue_total,0);?></b></td>
                            <td><b><?php echo round($t_rev_per/$num_cnt,2).'%'?></b></td>
                            <td><b><?php echo round($t_gp,2).'%';?></b></td>
                    </tfoot>
                </table>
        <?php }
        }
    }
    
    //Promotor target vs ach report
     public function promotor_target_vs_ach_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/promotor_target_vs_achevement_report',$q);
        
    }
    
     public function ajax_get_promotor_target_vs_ach_byidbranch() {
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_promotor_target_ach_byidbranch($monthyear, $idpcat, $allpcats, $idbranch, $allbranches);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="Promoter_Target_vs_Achievement_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">SALE PROMOTER</th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">VOLUME</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">VALUE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">ASP</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">REVENUE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">FINANCE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">RUDRAM</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">AGING</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">Protection Plan</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                </thead>
                  <thead style="background-color: #9dbfed" class="fixheader1">
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">FINANCE ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0; $t_smart_phone=0;$t_finance=0;$t_rudram=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;$rev_ach_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    $t_smart_phone_amt=0;
                    $smart_phone=0;$finance_qty=0;$rud_qty;$fin_conn=0;$rud_conn=0; $num_cnt=0;$pro_qty=0;$pro_conn=0;$t_pro=0;
                    $smart_phone_amount=0; 
                    foreach ($sale_data as $sale){ 
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
                        if($sale->smart_qty){$smart_phone = $sale->smart_qty; } else{ $smart_phone = 0;}
                        if($sale->finance_qty){$finance_qty = $sale->finance_qty; } else{ $finance_qty = 0;}
                        if($sale->rudram_qty){$rud_qty = $sale->rudram_qty; } else{ $rud_qty = 0;}
                        if($sale->pro_qty){$pro_qty = $sale->pro_qty; } else{ $pro_qty = 0;}
                        if($sale->smart_amount){$smart_phone_amount = $sale->smart_amount; } else{ $smart_phone_amount = 0;}
                        
                        if($vol > 0){
                            $vol_ach = ($salqt / $vol)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($vall > 0){
                            $val_ach = ($saletot / $vall)*100;
                        }else{
                            $val_ach =0;
                        }
                        if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        //revenue ach per 
                        if($recvenue > 0){
                            $rev_ach_per = ($rev_per/$recvenue)*100;
                        }else{
                            $rev_ach_per = 0;
                        }
                        //finance conn
                        if($smart_phone > 0){
                            $fin_conn = ($finance_qty/$smart_phone)*100;
                            $rud_conn = ($rud_qty/$smart_phone)*100;
//                            $pro_conn = ($pro_qty/$smart_phone)*100;
                             $pro_conn = ($pro_qty/$smart_phone_amount)*100;
                        }else{
                            $fin_conn = 0;
                            $rud_conn =0;
                            $pro_conn = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name;$num_cnt= $num_cnt +1; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                        <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,0); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,0).'%'; ?></td>
                        <td><?php echo round($recvenue,2); $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                        <td><?php echo round($rev_ach_per,2).'%'; ?></td>
                        <td><?php echo $smart_phone; $t_smart_phone = $t_smart_phone + $smart_phone; ?></td>
                        <td><?php echo $finance_qty; $t_finance = $t_finance + $finance_qty; ?></td>
                        <td><?php echo round($fin_conn,0).'%'?></td>
                        <td><?php echo $smart_phone; ?></td>
                        <td><?php echo $rud_qty; $t_rudram = $t_rudram + $rud_qty;   ?></td>
                        <td><?php echo round($rud_conn,0).'%'?></td>
                        <td>0</td>
                        <td>0</td>
                        <td>0%</td>
                        <td><?php echo $smart_phone_amount; $t_smart_phone_amt = $t_smart_phone_amt + $smart_phone_amount;  ?></td>
                        <td><?php echo $pro_qty; $t_pro = $t_pro + $pro_qty;    ?></td>
                        <td><?php echo round($pro_conn,2).'%'?></td>
                    </tr>
                    <?php } 
                    $tt_asp_ac=0;
                     if($tsal > 0){
                            $tt_asp_ac = ($tsa_total/ $tsal);
                        }else{
                            $tt_asp_ac =0;
                        }?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tvol; ?></b></td>
                        <td><b><?php echo $tsal; ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),2).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo $tval; ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php echo round($tt_asp_ac,1); ?></b></td>
                        <td><b><?php if($t_asp > 0){ echo round((($tt_asp_ac/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2);?></b></td>
                        <td><b><?php $trev = 0; if($t_sale_landing > 0){ $trev = ((($tsa_total -$t_sale_landing)*100)/$t_sale_landing); }else{ $trev = 0;} echo round($trev ,2).'%';?></b></td>
                        <td><b><?php if(($t_rev/$num_cnt) > 0){ echo round(($trev/($t_rev/$num_cnt))*100,2).'%'; }else{ echo '0%'; } ?></b></td>
                        <td><b><?php echo $t_smart_phone; ?></b></td>
                        <td><b><?php echo $t_finance;  ?></b></td>
                        <td><b><?php if($t_smart_phone > 0){ echo round((($t_finance /$t_smart_phone)*100),0).'%'; }else{ echo '0%';}?></b></td>
                        <td><b><?php echo $t_smart_phone;  ?></b></td>
                        <td><b><?php echo $t_rudram;  ?></b></td>
                        <td><b><?php if($t_smart_phone > 0){ echo round((($t_rudram /$t_smart_phone)*100),0).'%'; }else{ echo '0%';}?></b></td>
                        <td><b>0</b></td>
                        <td><b>0</b></td>
                        <td><b>0%</b></td>
                        <td><b><?php echo $t_smart_phone_amt;  ?></b></td>
                        <td><b><?php echo $t_pro;  ?></b></td>
                        <td><b><?php if($t_smart_phone_amt > 0){ echo round((($t_pro /$t_smart_phone_amt)*100),0).'%'; }else{ echo '0%';}?></b></td>
                    </tr>
                </tbody>
            </table>   
            
        <?php }else{
            echo 'Data Not found';
        }
        
    }
     public function ajax_get_promotor_target_vs_ach_byidzone() {
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzones = $this->input->post('allzone');
        
        $sale_data = $this->Target_model->get_promotor_target_ach_byidbzone($monthyear, $idpcat, $allpcats, $idzone,$allzones);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="Promoter_Target_vs_Achievement_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">SALE PROMOTER</th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">VOLUME</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">VALUE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">ASP</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">REVENUE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">FINANCE</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">RUDRAM</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">AGING</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                        <th style="text-align: center;border-right-color:  #9dbfed"></th>
                        <th style="text-align: center;border-left-color:  #9dbfed;border-right-color: #9dbfed">PROTECTION PLAN</th>
                        <th style="text-align: center;border-left-color:  #9dbfed"></th>
                </thead>
                  <thead style="background-color: #9dbfed" class="fixheader1">
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center"></th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">FINANCE ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                    <th style="text-align: center">TARGET</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">SMART PHONE</th>
                    <th style="text-align: center">ACH</th>
                    <th style="text-align: center">CONN(%)</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0; $t_smart_phone=0;$t_finance=0;$t_rudram=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;$rev_ach_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    $t_smart_phone_amt=0;
                    $smart_phone=0;$finance_qty=0;$rud_qty;$fin_conn=0;$rud_conn=0;$num_cnt=0;$smart_phone_amount=0;$pro_conn=0;$t_pro=0;
                    foreach ($sale_data as $sale){ 
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
                        if($sale->smart_qty){$smart_phone = $sale->smart_qty; } else{ $smart_phone = 0;}
                        if($sale->finance_qty){$finance_qty = $sale->finance_qty; } else{ $finance_qty = 0;}
                        if($sale->rudram_qty){$rud_qty = $sale->rudram_qty; } else{ $rud_qty = 0;}
                       if($sale->smart_amount){$smart_phone_amount = $sale->smart_amount; } else{ $smart_phone_amount = 0;}
                       if($sale->pro_qty){$pro_qty = $sale->pro_qty; } else{ $pro_qty = 0;}
                        
                        if($vol > 0){
                            $vol_ach = ($salqt / $vol)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($vall > 0){
                            $val_ach = ($saletot / $vall)*100;
                        }else{
                            $val_ach =0;
                        }
                        if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        //revenue ach per 
                        if($recvenue > 0){
                            $rev_ach_per = ($rev_per/$recvenue)*100;
                        }else{
                            $rev_ach_per = 0;
                        }
                        //finance conn
                        if($smart_phone > 0){
                            $fin_conn = ($finance_qty/$smart_phone)*100;
                            $rud_conn = ($rud_qty/$smart_phone)*100;
                            $pro_conn = ($pro_qty/$smart_phone_amount)*100;
                        }else{
                            $fin_conn = 0;
                            $rud_conn =0;
                            $pro_conn=0;
                        }
                        
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name;$num_cnt=$num_cnt+1; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                        <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,0); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,0).'%'; ?></td>
                        <td><?php echo round($recvenue,2); $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                        <td><?php echo round($rev_ach_per,2).'%'; ?></td>
                        <td><?php echo $smart_phone; $t_smart_phone = $t_smart_phone + $smart_phone; ?></td>
                        <td><?php echo $finance_qty; $t_finance = $t_finance + $finance_qty; ?></td>
                        <td><?php echo round($fin_conn,0).'%'?></td>
                        <td><?php echo $smart_phone; ?></td>
                        <td><?php echo $rud_qty; $t_rudram = $t_rudram + $rud_qty;   ?></td>
                        <td><?php echo round($rud_conn,0).'%'?></td>
                        <td>0</td>
                        <td>0</td>
                        <td>0%</td>
                        <td><?php echo round($smart_phone_amount); $t_smart_phone_amt = $t_smart_phone_amt + $smart_phone_amount; ?></td>
                        <td><?php echo round($pro_qty); $t_pro = $t_pro + $pro_qty;   ?></td>
                        <td><?php echo round($pro_conn,2).'%'?></td>
                    </tr>
                    <?php }
                    
                    $tt_asp_ac=0;
                     if($tsal > 0){
                            $tt_asp_ac = ($tsa_total/ $tsal);
                        }else{
                            $tt_asp_ac =0;
                        }
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tvol; ?></b></td>
                        <td><b><?php echo $tsal; ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),2).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo $tval; ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php echo round($tt_asp_ac,1); ?></b></td>
                        <td><b><?php if($t_asp > 0){ echo round((($tt_asp_ac/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2);?></b></td>
                        <td><b><?php $trev = 0; if($t_sale_landing > 0){ $trev = ((($tsa_total -$t_sale_landing)*100)/$t_sale_landing); }else{ $trev = 0;} echo round($trev ,2).'%';?></b></td>
                        <td><b><?php if(($t_rev/$num_cnt) > 0){ echo round(($trev/($t_rev/$num_cnt))*100,2).'%'; }else{ echo '0%'; } ?></b></td>
                        <td><b><?php echo $t_smart_phone; ?></b></td>
                        <td><b><?php echo $t_finance;  ?></b></td>
                        <td><b><?php if($t_smart_phone > 0){ echo round((($t_finance /$t_smart_phone)*100),0).'%'; }else{ echo '0%';}?></b></td>
                        <td><b><?php echo $t_smart_phone;  ?></b></td>
                        <td><b><?php echo $t_rudram;  ?></b></td>
                        <td><b><?php if($t_smart_phone > 0){ echo round((($t_rudram /$t_smart_phone)*100),0).'%'; }else{ echo '0%';}?></b></td>
                        <td><b>0</b></td>
                        <td><b>0</b></td>
                        <td><b>0%</b></td>
                        <td><b><?php echo $t_smart_phone_amt;  ?></b></td>
                        <td><b><?php echo $t_pro;  ?></b></td>
                        <td><b><?php if($t_smart_phone_amt > 0){ echo round((($t_pro /$t_smart_phone_amt)*100),0).'%'; }else{ echo '0%';}?></b></td>
                    </tr>
                </tbody>
            </table>   
            
        <?php }else{
            echo 'Data Not found';
        }
        
    }
    
    //    /Promotor Discount Report
    public function promotor_discount_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $this->load->view('target/promotor_discount_report',$q);
        
    }
    public function ajax_get_promotor_discount_byidbranch() {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $sale_data = $this->Target_model->get_promotor_discount_byidbranch($from, $to,$idpcat, $allpcats, $idbranch, $allbranches);
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="Promoter_Discount_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">SALE PROMOTER</th>
                        <th style="text-align: center">VOLUME ACH </th>
                        <th style="text-align: center">VALUE ACH</th>
                        <th style="text-align: center">ACTUAL REVENUE</th>
                        <th style="text-align: center">REVENUE(%)</th>
                        <th style="text-align: center">DISCOUNT USED</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    
                    $salqt=0;$saletot=0;$slanding=0;$salesman_price=0;
                    $smart_salqt=0;$smart_saletot=0;$smart_slanding=0;$smart_salesman_price=0;
                    $revenue = 0; $rev_per=0;$tsal=0;$tsa_total=0; $discount_amt=0;
                    $trev=0;$tdis=0;$trev_per=0;$t_sale_landing=0; $all_rev= 0;
                    
                    foreach ($sale_data as $sale){ 
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->salesman_price){ $salesman_price = $sale->salesman_price;}else{ $salesman_price = 0; }
                        
                        
                        if($sale->smart_qty){ $smart_salqt = $sale->smart_qty;}else{ $smart_salqt = 0; }
                        if($sale->smart_total){ $smart_saletot = $sale->smart_total;}else{ $smart_saletot = 0; }
                        if($sale->smart_landing){ $smart_slanding = $sale->smart_landing;}else{ $smart_slanding = 0; }
                        if($sale->smart_salesman_price){ $smart_salesman_price = $sale->smart_salesman_price;}else{ $smart_salesman_price = 0; }
                        
//                        if($idpcat == 1){
//                            $revenue = $smart_saletot - $smart_slanding;
//                             if($smart_saletot > 0){ $rev_per = ($revenue/$smart_saletot)*100; } else{ $rev_per =0; }
//                             $discount_amt = $smart_salesman_price - $smart_saletot;
//                        }else{
                            $revenue = $saletot - $slanding;
                            if($saletot > 0){ $rev_per = ($revenue/$saletot)*100;} else{ $rev_per =0; }
                            $discount_amt = $salesman_price - $saletot;
//                        }
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($saletot,0); $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($revenue,0); $trev = $trev+  $revenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                        <td><?php echo round($discount_amt,0); $tdis = $tdis +  $discount_amt; ?></td>
                    </tr>
                    <?php } 
                    $all_rev = $tsa_total - $t_sale_landing;
                    if($tsa_total > 0){ $all_rev_per = ($all_rev/$tsa_total)*100;} else{ $all_rev_per =0; }
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tsal; ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php echo round($all_rev,0); ?></b></td>
                        <td><b><?php echo round($all_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($tdis,0); ?></b></td>
                    </tr>
                </tbody>
            </table>   
        <?php }else{
            echo 'Data Not found';
        }
    }
    public function ajax_get_promotor_discount_byidbzone() {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
       $idzone = $this->input->post('idzone');
        $allzones = $this->input->post('allzone');
        
        $sale_data = $this->Target_model->get_promotor_discount_byidzone($from, $to,$idpcat, $allpcats, $idzone, $allzones);
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="Promoter_Discount_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">SALE PROMOTER</th>
                        <th style="text-align: center">VOLUME ACH </th>
                        <th style="text-align: center">VALUE ACH</th>
                        <th style="text-align: center">ACTUAL REVENUE</th>
                        <th style="text-align: center">REVENUE(%)</th>
                        <th style="text-align: center">DISCOUNT USED</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    
                    $salqt=0;$saletot=0;$slanding=0;$salesman_price=0;
                    $smart_salqt=0;$smart_saletot=0;$smart_slanding=0;$smart_salesman_price=0;
                    $revenue = 0; $rev_per=0;$tsal=0;$tsa_total=0; $discount_amt=0;
                    $trev=0;$tdis=0;$trev_per=0;$t_sale_landing=0; $all_rev= 0;
                    
                    foreach ($sale_data as $sale){ 
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->salesman_price){ $salesman_price = $sale->salesman_price;}else{ $salesman_price = 0; }
                        
                        
                        if($sale->smart_qty){ $smart_salqt = $sale->smart_qty;}else{ $smart_salqt = 0; }
                        if($sale->smart_total){ $smart_saletot = $sale->smart_total;}else{ $smart_saletot = 0; }
                        if($sale->smart_landing){ $smart_slanding = $sale->smart_landing;}else{ $smart_slanding = 0; }
                        if($sale->smart_salesman_price){ $smart_salesman_price = $sale->smart_salesman_price;}else{ $smart_salesman_price = 0; }
                        
//                        if($idpcat == 1){
//                            $revenue = $smart_saletot - $smart_slanding;
//                             if($smart_saletot > 0){ $rev_per = ($revenue/$smart_saletot)*100; } else{ $rev_per =0; }
//                             $discount_amt = $smart_salesman_price - $smart_saletot;
//                        }else{
                            $revenue = $saletot - $slanding;
                            if($saletot > 0){ $rev_per = ($revenue/$saletot)*100;} else{ $rev_per =0; }
                            $discount_amt = $salesman_price - $saletot;
//                        }
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($saletot,0); $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($revenue,0); $trev = $trev+  $revenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                        <td><?php echo round($discount_amt,0); $tdis = $tdis +  $discount_amt; ?></td>
                    </tr>
                    <?php } 
                    $all_rev = $tsa_total - $t_sale_landing;
                    if($tsa_total > 0){ $all_rev_per = ($all_rev/$tsa_total)*100;} else{ $all_rev_per =0; }
                    ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $tsal; ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php echo round($all_rev,0); ?></b></td>
                        <td><b><?php echo round($all_rev_per,2).'%'; ?></b></td>
                        <td><b><?php echo round($tdis,0); ?></b></td>
                    </tr>
                </tbody>
            </table>   
        <?php }else{
            echo 'Data Not found';
        }
    }
    
    
    public function ageing_target_setup() {
        $q['tab_active'] = 'Target';
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $this->load->view('target/ageing_target_setup',$q);
    }
    public function ajax_get_ageing_data_byidbranch() {
        $month_year = $this->input->post('monthyear');
        $last_month = $this->input->post('lastmonthyear');
        $cnttarg=0;
        $ageing_target_data = $this->Target_model->get_ageing_data_bymonth($month_year);
        $ageing_stock_data = $this->Target_model->get_ageing_stock_data_byidbranch($last_month);
//        die('<pre>'.print_r($ageing_stock_data,1).'</pre>');
        if($ageing_target_data){
            $cnttarg = 1;
        }else{
             $cnttarg = 0; 
        }
//        die('<pre>'.print_r($ageing_target_data,1).'</pre>');
        
        if($ageing_stock_data){ ?>
        <form>
            <!--<div  style="overflow-x: auto;height: 550px">-->
            <table class="table table-bordered table-condensed" id="ageing_setup">
                <thead class="fixheader" style="background-color: #9dbfed">
                    <th>Branch</th>
                    <th>Ageing Stock Qty</th>
                    <th>Ageing Sale Qty</th>
                    <th>Ageing Target(%)</th>
                </thead>
                <tbody class="data_1">
                    <?php $age_qty = 0; $sale_qty=0; $per=0; foreach($ageing_stock_data as $age){
                        if($age->age_qty){ $age_qty = $age->age_qty;}else{ $age_qty =0; } 
                        if($age->sale_qty){$sale_qty = $age->sale_qty;}else{ $sale_qty = 0; }
                        
                        if($cnttarg == 1){ 
                            foreach($ageing_target_data as $tar){
                                if($tar->idbranch == $age->id_branch){
                                    $per = $tar->ageing_target;  
                                }
                            }
                        }else{ $per = 0; }
                        ?>
                        <tr>
                            <td><?php echo $age->branch_name; ?></td>
                            <td><?php echo $age_qty; ?></td>
                            <td><?php echo $sale_qty; ?></td>
                            <td>
                                <input type="text" name="age_per[]" id="age_per" class="form-control input-sm" value="<?php echo $per; ?>" >
                                <input type="hidden" name="idbranch[]" id="idbranch" value="<?php echo $age->id_branch; ?>">
                                <input type="hidden" name="age_qty[]" id="age_qty" value="<?php echo $age_qty ?>">
                                <input type="hidden" name="sale_qty[]" id="sale_qty" value="<?php echo $sale_qty ?>">
                                <input type="hidden" name="month_year" id="month_year" value="<?php echo $month_year ?>">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!--</div>-->
            <div class="clearfix"></div><br>
            <button class="btn btn-primary pull-right" name="btnsub" formmethod="POST" formaction="<?php echo base_url()?>Target/save_ageing_target_setup">Submit</button>
            <div class="clearfix"></div><br>
        </form>
        <?php }else{
             echo 'Data Not Found';
        }
    }
    
    public function save_ageing_target_setup(){
        $idbranch = $this->input->post('idbranch');
        $month_year = $this->input->post('month_year');
        $age_qty = $this->input->post('age_qty');
        $sale_qty = $this->input->post('sale_qty');
        $age_per = $this->input->post('age_per');
        $cdate = date('Y-m-d');
        $entrytime = date('Y-m-d h:i:s');
        
        for($i=0; $i< count($idbranch); $i++){
            $branch_target_data = $this->Target_model->ajax_check_branch_target_data($idbranch[$i], $month_year);
            if($branch_target_data){
                 $data = array(
                    'ageing_target'=> $age_per[$i],
                );
               $this->Target_model->update_branch_target($data, $branch_target_data->id_branch_target);
            }else{
                $data = array(
                    'idbranch' => $idbranch[$i],
                    'date' => $cdate,
                    'month_year' => $month_year,
                    'idproductcategory' => 1,
                    'volume' => 0,
                    'value'=> 0,
                    'asp' => 0,
                    'revenue'=>0,
                    'ageing_target'=> $age_per[$i],
                    'entrytime' => $entrytime,
                    'iduser' => $_SESSION['id_users'],
                );
                $this->Target_model->save_branch_target($data);
            }
            
        }
        $this->session->set_flashdata('save_data', 'Ageing Target Saved Successfully !');
        redirect('Target/ageing_target_setup');
    }
  
    
    //************Target Slabs*******************
    
     public function target_slabs_setup(){
        $q['tab_active'] = '';
        $q['slab_data'] = $this->Target_model->get_target_slab_data();
        $this->load->view('target/target_slabs', $q);
    }
    public function save_target_slabs_per(){
        
        $data = array(
           'month_year' => $this->input->post('monthyear'),
           'from_date' => $this->input->post('from'),
           'to_date' => $this->input->post('to'),
           'target_per' => $this->input->post('tar_per'),
           'slab_name' => $this->input->post('slab_name'),
           'created_by' => $_SESSION['id_users'],
           'entrytime' => date('Y-m-d h:i:s'),
        );
        $this->Target_model->save_target_slab_data($data);
        $this->session->set_flashdata('save_data', 'Target Slabs Saved Successfully !');
        redirect('Target/target_slabs_setup');
        
    }
     public function edit_target_slab_per(){
        $id = $this->input->post('id');
        $data = array(
          'month_year' => $this->input->post('monthyear1'),
           'from_date' => $this->input->post('from1'),
           'to_date' => $this->input->post('to1'),
           'target_per' => $this->input->post('tar_per1'),
           'slab_name' => $this->input->post('slab_name1'),
           'created_by' => $_SESSION['id_users'],
           'entrytime' => date('Y-m-d h:i:s'),
        );
        $this->Target_model->edit_target_slab_data($data, $id);
        
          $this->session->set_flashdata('save_data', 'Target Slabs Saved Successfully !');
        redirect('Target/target_slabs_setup');
    }
    
    //Promotor Target Setup
     public function promotor_target_setup_slab(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        $this->load->view('target/slab_promotor_target_setup',$q);
    }
    
     public function ajax_get_branch_target_promotor_slab(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $allbranches = $this->input->post('branches');
        $target_slabs = $this->input->post('target_slabs');
        
        $current_monthyear = date('Y-m');
        
        $branch_target_data = $this->Target_model->ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches);
        $promotor_data = $this->Target_model->ajax_get_promotor_data_byidbranch($idbranch);
        $product_cat_data = $this->Target_model->get_product_category_data();
        
        $slabs_data = $this->Target_model->get_target_slab_data_byid($target_slabs);
        
//        die('<pre>'.print_r($promotor_data,1).'</pre>');
        
        //get_promotor _target_setup_data
        $promotor_target_data = $this->Target_model->ajax_get_promotor_target_slab_data_byid($idbranch, $monthyear, $slabs_data->id_target_slab);
      
//        //Current Month Target Setup and EDIT 
        
        if($current_monthyear == $monthyear){
            
            //Promotor Target Edit Form
            if($promotor_target_data){  ?>
                <div class="col-md-10 col-md-offset-1">
                    <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                        <thead style="background-color: #ffcccc;" >
                        <th style="text-align: center"><b>BRANCH</b></th>
                        <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                        <th style="text-align: center"><b>VOLUME TARGET</b></th>
                        <th style="text-align: center"><b>VALUE TARGET</b></th>
                        <th style="text-align: center"><b>ASP TARGET</b></th>
                        <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                        </thead>
                        <tbody class="data_1">
                            <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                            <tr>
                                <td><?php echo $bdata->branch_name ?></td>
                                <td><?php echo $bdata->product_category_name ?></td>
                                <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                            </tr>
                            <?php } ?>
                            <tr style="background-color: #fbe0e0">
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $tvol; ?></b></td>
                                <td><b><?php echo $tval; ?></b></td>
                                <td><b><?php echo round($tasp); ?></b></td>
                                <td><b><?php echo $trev; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div><br>
                <div style="text-align: center;font-size: 18px;"><b><?php echo $slabs_data->slab_name.' ( '.$slabs_data->target_per.'% )'; ?></b></div>
                    <?php if($slabs_data){ ?>
                    <div class="col-md-10 col-md-offset-1">
                        <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                            <thead style="background-color: #99ccff;" >
                            <th style="text-align: center"><b>BRANCH</b></th>
                            <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                            <th style="text-align: center"><b>VOLUME TARGET</b></th>
                            <th style="text-align: center"><b>VALUE TARGET</b></th>
                            <th style="text-align: center"><b>ASP TARGET</b></th>
                            <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                            </thead>
                            <tbody class="data_1">
                                <?php $num_cnt=0; $volume= 0; $value=0; $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){
                                        $volume = round(($bdata->volume * $slabs_data->target_per)/100,0);
                                        $value = round(($bdata->value * $slabs_data->target_per)/100,0);
                                    ?>
                                <tr>
                                    <td><?php echo $bdata->branch_name; $num_cnt = $num_cnt + 1; ?></td>
                                    <td><?php echo $bdata->product_category_name ?></td>
                                    <td><?php echo $volume; $tvol = $tvol + $volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $volume ?>"></td>
                                    <td><?php echo $value; $tval = $tval + $value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $value ?>"></td>
                                    <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                    <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="background-color: #99ccff">
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $tvol; ?></b></td>
                                    <td><b><?php echo $tval; ?></b></td>
                                    <td><b><?php echo round($tasp/$num_cnt); ?></b></td>
                                    <td><b><?php echo $trev/$num_cnt.'%'; ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                <form id="myform">
                    <div style="height: 700px;overflow-x: auto;padding: 0">
                        <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                            <thead class="fixheader" style="background-color: #c6e6f5">
                                <th class="fixleft"></th>
                                <th class="fixleft1"></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="border: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                        <th style="border-left: 1px solid #c6e6f5"></th>
                                    <?php } else{ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                    <?php }?>
                                <?php } ?>
                            </thead>
                            <thead class="fixheader1" style="background-color: #c6e6f5"> 
                                <th class="fixleft"><b>Promotor Name</b></th>
                                <th class="fixleft1"><b>Promotor Brand</b></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <td><b>Volume</b></td>
                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <td><b>Value</b></td>
                                        <td><b>Asp</b></td>
                                        <td><b>REVENUE</b></td>
                                    <?php } else{  ?>
                                        <td><b>Connect</b></td>                            
                                    <?php } 
                                } ?>

                            </thead>
                            <tbody >
                                <?php $sr=1; foreach ($promotor_data as $pdata){
                                    $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);
                                    ?>
                                    <tr class="promotordataedit">
                                        <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                            <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                        </td>
                                        <td class="fixleft1"><?php  if($brand_data){ echo $brand_data->brand_name; } ?> 
                                            <input type="hidden" name="idbrand[]" value="<?php  if($brand_data){ echo $brand_data->id_brand; }?>">
                                        </td>
                                        <?php foreach ($promotor_target_data as $target_data){
                                            foreach ($product_cat_data as $pcat){
                                                if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category && $target_data->idbrand == $brand_data->id_brand){ ?>
                                        <td><input type="text"  style="width: 120px;" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->volume > 0){?> readonly <?php } ?> value="<?php echo $target_data->volume; ?>"><div style="display: none"><?php echo $target_data->volume; ?></div></td>
                                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->value > 0){?> readonly <?php } ?> value="<?php echo $target_data->value; ?>"><div style="display: none"><?php echo $target_data->value; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->asp > 0){?> readonly <?php } ?> value="<?php echo $target_data->asp; ?>"><div style="display: none"><?php echo $target_data->asp; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->revenue > 0){?> readonly <?php } ?> value="<?php echo $target_data->revenue; ?>"><div style="display: none"><?php echo $target_data->revenue; ?></div></td>
                                                    <?php } else{ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control" <?php if($target_data->connect > 0){?> readonly <?php } ?> value="<?php echo $target_data->connect; ?>"><div style="display: none"><?php echo $target_data->connect; ?></div></td>
                                                    <?php } ?>
                                                    <script>
                                                        $(document).ready(function (){
                                                            $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                               var parenttr = $(this).closest('td').parent('tr');
                                                               var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                               var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                               var asp = 0
                                                            
                                                               if(volume_target > 0 ){
                                                                    asp = Math.round(value_target/volume_target);
                                                                    $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }else{
                                                                   asp = 0;
                                                                   $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }
                                                               
                                                                var total_volume_sum =0;
                                                                var total_value_sum =0;
                                                                $('.promotordataedit').each(function (){

                                                                   $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_basic_val = $(this).val();
                                                                        if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                            total_volume_sum += parseFloat(total_basic_val);
                                                                        }

                                                                    });

                                                                   $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_vbasic_val = $(this).val();
                                                                        if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                            total_value_sum += parseFloat(total_vbasic_val);
                                                                        }

                                                                    });
                                                               })

                                                               var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                               var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();

                                                               if(total_volume_sum > branch_volume ){
                                                                   alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                                   var volamount =0;
                                                                   +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                                   return false;
                                                               }
                                                               if(total_value_sum > branch_value ){
                                                                   alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                                   var valamount =0;
                                                                   +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                                   return false;
                                                               }

                                                                });
                                                        }); 
                                                    </script>
                                        <?php } }  } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                        <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                        <input type="hidden" name="idslab" value="<?php echo $slabs_data->id_target_slab;?>">
                        <input type="hidden" name="form_slab" value="<?php echo $slabs_data->from_date;?>">
                        <input type="hidden" name="to_slab" value="<?php echo $slabs_data->to_date;?>">
                    </div>
                    <div class="clearfix"></div><br>
                    <input type="hidden" name="edit_promotor" value="0">
                    <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_promotor_target_slab_data">Update</button>
                    <div class="clearfix"></div><br>
                </form>
            <?php } else{

                if($branch_target_data) { ?>
                <!--************** Branch Target Data Display **************-->

                    <div class="col-md-10 col-md-offset-1">
                        <div style="text-align: center; font-size: 18px"><b>Branch Target</b></div>
                        <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                            <thead style="background-color: #ffcccc;" >
                            <th style="text-align: center"><b>BRANCH</b></th>
                            <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                            <th style="text-align: center"><b>VOLUME TARGET</b></th>
                            <th style="text-align: center"><b>VALUE TARGET</b></th>
                            <th style="text-align: center"><b>ASP TARGET</b></th>
                            <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                            </thead>
                            <tbody class="data_1">
                                <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                                <tr>
                                    <td><?php echo $bdata->branch_name ?></td>
                                    <td><?php echo $bdata->product_category_name ?></td>
                                    <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                    <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                    <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                    <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="background-color: #fbe0e0">
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $tvol; ?></b></td>
                                    <td><b><?php echo $tval; ?></b></td>
                                    <td><b><?php echo round($tasp); ?></b></td>
                                    <td><b><?php echo $trev; ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                    <div style="text-align: center;font-size: 18px;"><b><?php echo $slabs_data->slab_name.' ( '.$slabs_data->target_per.'% )'; ?></b></div>
                    <?php if($slabs_data){ ?>
                    <div class="col-md-10 col-md-offset-1">
                        <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                            <thead style="background-color: #99ccff;" >
                            <th style="text-align: center"><b>BRANCH</b></th>
                            <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                            <th style="text-align: center"><b>VOLUME TARGET</b></th>
                            <th style="text-align: center"><b>VALUE TARGET</b></th>
                            <th style="text-align: center"><b>ASP TARGET</b></th>
                            <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                            </thead>
                            <tbody class="data_1">
                                <?php $volume= 0; $value=0; $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){
                                        $volume = round(($bdata->volume * $slabs_data->target_per)/100,0);
                                        $value = round(($bdata->value * $slabs_data->target_per)/100,0);
                                    ?>
                                    
                                <tr>
                                    <td><?php echo $bdata->branch_name ?></td>
                                    <td><?php echo $bdata->product_category_name ?></td>
                                    <td><?php echo $volume; $tvol = $tvol + $volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $volume ?>"></td>
                                    <td><?php echo $value; $tval = $tval + $value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $value ?>"></td>
                                    <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                    <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="background-color: #99ccff">
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $tvol; ?></b></td>
                                    <td><b><?php echo $tval; ?></b></td>
                                    <td><b><?php echo round($tasp); ?></b></td>
                                    <td><b><?php echo $trev; ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                <!--************ Promotor Target Setup *******************-->
                    <?php if($promotor_data){ ?>
                        <form id="myform">
                            <div style="height: 700px;overflow-x: auto;padding: 0">
                                <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                                    <thead class="fixheader" style="background-color: #c6e6f5">
                                        <th class="fixleft"></th>
                                        <th class="fixleft1"></th>
                                        <?php foreach ($product_cat_data as $pcat){ ?>
                                           <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                <th style="border-right: 1px solid #c6e6f5"></th>
                                                <th style="border: 1px solid #c6e6f5"></th>
                                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                                <th style="border-left: 1px solid #c6e6f5"></th>
                                            <?php } else{ ?>
                                                <th style="border-right: 1px solid #c6e6f5"></th>
                                                <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                            <?php }?>
                                        
                                        <?php } ?>
                                    </thead>
                                    <thead class="fixheader1" style="background-color: #c6e6f5;"> 
                                        <th class="fixleft"><b>Promotor Name</b></th>
                                        <th class="fixleft1"><b>Promotor Brand</b></th>
                                        <?php foreach ($product_cat_data as $pcat){ ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Volume</b></td>
                                            <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Value</b></td>
                                            <td style="border-top: 1px solid #cccccc"><b>Asp</b></td>
                                            <td style="border-top: 1px solid #cccccc"><b>REVENUE</b></td>
                                            <?php } else{  ?>
                                            <td style="border-top: 1px solid #cccccc"><b>Connect</b></td>                            
                                            <?php } 
                                        } ?>

                                    </thead>
                                    <tbody>
                                        <?php $sr=1; foreach ($promotor_data as $pdata){ 
                                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                                        <tr class="promotordata">
                                            <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                                <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                            </td>
                                            <td class="fixleft1"><?php echo $brand_data->brand_name; ?> 
                                                <input type="hidden" name="idbrand[]" value="<?php echo $brand_data->id_brand;?>"></td>
                                            <?php foreach ($product_cat_data as $pcat){ ?>
                                                <td><input type="text"  style="width: 120px;" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                               <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                    <td><input type="text" style="width: 120px;" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                    <td><input type="text" style="width: 120px;" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                    <td><input type="text" style="width: 120px;" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                <?php } else{ ?>
                                                    <td><input type="text" style="width: 120px;" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control"></td>
                                                <?php } ?>

                                                <script>
                                                    $(document).ready(function (){
                                                        $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                           var parenttr = $(this).closest('td').parent('tr');
                                                           var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                           var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                           var asp = 0
                                                           var idpcat = <?php echo $pcat->id_product_category?>;
                                                           if(volume_target != ''){
                                                                asp = Math.round(value_target/volume_target);
                                                                $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                           }else{
                                                               asp = 0;
                                                               $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                           }
                                                           
                                                            var total_volume_sum =0;
                                                            var total_value_sum =0;
                                                           $('.promotordata').each(function (){
                                                                       
                                                               $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                    var total_basic_val = $(this).val();
                                                                    if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                        total_volume_sum += parseFloat(total_basic_val);
                                                                    }
                                                                    
                                                                });
                                                                
                                                               $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                    var total_vbasic_val = $(this).val();
                                                                    if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                        total_value_sum += parseFloat(total_vbasic_val);
                                                                    }
                                                                    
                                                                });
                                                           })
                                                           
                                                           var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                           var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();
                                                           
                                                           if(total_volume_sum > branch_volume ){
                                                               alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                               var volamount =0;
                                                               +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                               return false;
                                                           }
                                                           if(total_value_sum > branch_value ){
                                                               alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                               var valamount =0;
                                                               +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                               return false;
                                                           }
                                                        });
                                                    }); 
                                            </script>
                                            <?php } ?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                                <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                                <input type="hidden" name="idslab" value="<?php echo $slabs_data->id_target_slab;?>">
                                <input type="hidden" name="form_slab" value="<?php echo $slabs_data->from_date;?>">
                                <input type="hidden" name="to_slab" value="<?php echo $slabs_data->to_date;?>">
                           </div>
                            <div class="clearfix"></div><br>
                            <script>
                                $(document).ready(function (){
                                    $(document).on('click','.btnsavepro', function(e){
                                        var flag = 0;
                                        <?php foreach ($product_cat_data as $pcat){ ?>
                                            var total_volume_sum =0;
                                            var total_value_sum =0;
                                            $('.promotordata').each(function (){
                                                $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                    var total_basic_val = $(this).val();
                                                    if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                        total_volume_sum += parseFloat(total_basic_val);
                                                    }
                                                });

                                                $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                    var total_vbasic_val = $(this).val();
                                                    if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                        total_value_sum += parseFloat(total_vbasic_val);
                                                    }
                                                });       
                                            });      

                                            var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                            var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();

                                            if(total_volume_sum != branch_volume ){
                                                alert("Total Volume Amount Shoud Be " + branch_volume);
                                                flag = 1;
                                            }
                                            if(total_value_sum != branch_value ){
                                                alert("Total Value Amound Shoud Be " + branch_value);
                                                 flag = 1;
                                            }
                                        <?php } ?>
                                        if(flag == 1){
                                           return false;
                                        }
                                    });
                                }); 
                            </script>
                            <button class="btn btn-primary pull-right btnsavepro" formmethod="POST" formaction="<?php echo base_url()?>Target/save_promotor_target_slab_data">Submit</button>
                            <div class="clearfix"></div><br>
                        </form>
                    <?php } else{
                        echo 'Promotor Data Not Found For This Branch';
                    }
                } else {  
                    echo 'Branch Target Not Set For This Month';
                }
            }
        }else{ ?>
            <div style="height: 700px;overflow-x: auto;padding: 0">
                 <?php if($promotor_target_data){ ?> 
                <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                    <thead class="fixheader" style="background-color: #c6e6f5">
                        <th class="fixleft"></th>
                        <th class="fixleft1"></th>
                        <?php foreach ($product_cat_data as $pcat){ ?>
                           <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                <th style="border-right: 1px solid #c6e6f5"></th>
                                <th style="border: 1px solid #c6e6f5"></th>
                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                <th style="border-left: 1px solid #c6e6f5"></th>
                            <?php } else{ ?>
                                <th style="border: 1px solid #c6e6f5"></th>
                                <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                            <?php }?>
                        <?php } ?>
                    </thead>
                    <thead class="fixheader1" style="background-color: #c6e6f5"> 
                        <th class="fixleft"><b>Promotor Name</b></th>
                        <th class="fixleft1"><b>Promotor Brand</b></th>
                        <?php foreach ($product_cat_data as $pcat){ ?>
                            <td><b>Volume</b></td>
                            <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                <td><b>Value</b></td>
                                <td><b>Asp</b></td>
                                <td><b>REVENUE</b></td>
                            <?php } else{  ?>
                                <td><b>Connect</b></td>                            
                            <?php } 
                        } ?>

                    </thead>
                    <tbody>
                        <?php $sr=1; foreach ($promotor_data as $pdata){
                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                            <tr>
                                <td class="fixleft"><?php echo $pdata->user_name; ?></td>
                                <td class="fixleft1"><?php echo $brand_data->brand_name; ?></td>
                                <?php foreach ($promotor_target_data as $target_data){
                                    foreach ($product_cat_data as $pcat){
                                        if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category && $target_data->idbrand == $brand_data->id_brand){ ?>
                                            <td><?php echo $target_data->volume; ?></td>
                                          <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                <td><?php echo $target_data->value; ?></td>
                                                <td><?php echo $target_data->asp; ?></td>
                                                <td><?php echo $target_data->revenue; ?></td>
                                            <?php } else { ?>
                                                <td><?php echo $target_data->connect; ?></td>
                                <?php }  }  } } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php } else{ 
                    echo 'Data Not Found For Selected Branch And Month';
                } ?>
            </div>
        <?php } ?>
                <script>
                    $(document).ready(function (){
                       $("#myform").on("submit", function(){
                            $(".img").fadeIn();
                        }); 
                    });
                </script>
        <?php 
    }
    
     public function save_promotor_target_slab_data(){
//        die(print_r($_POST));
        $product_cat_data = $this->Target_model->get_product_category_data();
        $entry_time = date('Y-m-d H:i:s');
        
        $iduser = $this->input->post('iduser');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('month_year');
        $idslab = $this->input->post('idslab');
        $from_slab = $this->input->post('form_slab');
        $to_slab = $this->input->post('to_slab');
        
        $pvol = $this->input->post('p_volume');
        $pval = $this->input->post('p_value');
        $pasp = $this->input->post('p_asp');
        $preve = $this->input->post('p_revenue');
        $pconnect = $this->input->post('p_connect');
        
        for($i=0; $i<count($iduser); $i++){
            foreach ($product_cat_data as $pcat){
                if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ 
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'value' => $pval[$pcat->id_product_category][$i],
                        'asp' => $pasp[$pcat->id_product_category][$i],
                        'revenue' => $preve[$pcat->id_product_category][$i],
                        'id_targetslab' => $idslab,
                        'from_slab' => $from_slab,
                        'to_slab' => $to_slab,
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }else{
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'connect' => $pconnect[$pcat->id_product_category][$i],
                        'id_targetslab' => $idslab,
                        'from_slab' => $from_slab,
                        'to_slab' => $to_slab,
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }
                $this->Target_model->save_promotor_target_data($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Promotor Target Saved Successfully !');
        redirect('Target/promotor_target_setup_slab');
        
    }
    public function update_promotor_target_slab_data(){
               
        $product_cat_data = $this->Target_model->get_product_category_data();
        $entry_time = date('Y-m-d H:i:s');
        
        $iduser = $this->input->post('iduser');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('month_year');
        $idslab = $this->input->post('idslab');
        $from_slab = $this->input->post('form_slab');
        $to_slab = $this->input->post('to_slab');
        
        $pvol = $this->input->post('p_volume');
        $pval = $this->input->post('p_value');
        $pasp = $this->input->post('p_asp');
        $preve = $this->input->post('p_revenue');
        $pconnect = $this->input->post('p_connect');
        $edit_promotor = $this->input->post('edit_promotor');
        
        $this->Target_model->delete_promotor_target_slab_data_byid($idbranch, $monthyear, $idslab);
        
        for($i=0; $i<count($iduser); $i++){
            foreach ($product_cat_data as $pcat){
                if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'value' => $pval[$pcat->id_product_category][$i],
                        'asp' => $pasp[$pcat->id_product_category][$i],
                        'revenue' => $preve[$pcat->id_product_category][$i],
                         'id_targetslab' => $idslab,
                        'from_slab' => $from_slab,
                        'to_slab' => $to_slab,
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }else{
                    $data = array(
                        'month_year' => $monthyear,
                        'idbranch' => $idbranch,
                        'idpromotor' => $iduser[$i],
                        'idproductcategory' => $pcat->id_product_category,
                        'idbrand' => $idbrand[$i],
                        'volume' => $pvol[$pcat->id_product_category][$i],
                        'connect' => $pconnect[$pcat->id_product_category][$i],
                        'id_targetslab' => $idslab,
                        'from_slab' => $from_slab,
                        'to_slab' => $to_slab,
                        'date' => date('Y-m-d'),
                        'entry_time' => $entry_time,
                        'created_by' => $_SESSION['id_users'],
                    );
                }
                $this->Target_model->save_promotor_target_data($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Promotor Target Saved Successfully !');
         if($edit_promotor == 1){
            redirect('Target/promotor_target_slab_setup_edit');
        }else{
            redirect('Target/promotor_target_setup_slab');
        }
    }
    
    public function promotor_target_slab_setup_edit(){
        $q['tab_active'] = 'Target';
          $current_monthyear = date('Y-m');
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['slabs_data'] = $this->Target_model->get_slab_by_month($current_monthyear);
        
        $this->load->view('target/promotor_target_setup_slab_edit',$q);
    }
    
    public function ajax_edit_promotor_target_slab_data(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $allbranches = $this->input->post('branches');
        $target_slabs = $this->input->post('target_slabs');
        
        $current_monthyear = date('Y-m');
        
        $branch_target_data = $this->Target_model->ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches);
        $promotor_data = $this->Target_model->ajax_get_promotor_data_byidbranch($idbranch);
        $product_cat_data = $this->Target_model->get_product_category_data();
        $slabs_data = $this->Target_model->get_target_slab_data_byid($target_slabs);
        
        
        //get_promotor _target_setup_data
        $promotor_target_data = $this->Target_model->ajax_get_promotor_target_slab_data_byid($idbranch, $monthyear, $slabs_data->id_target_slab);
            
//        die('<pre>'.print_r($promotor_target_data,1).'</pre>');
       //Promotor Target Edit Form
            if($promotor_target_data){  ?>
                <div class="col-md-10 col-md-offset-1">
                    <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                        <thead style="background-color: #ffcccc;" >
                        <th style="text-align: center"><b>BRANCH</b></th>
                        <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                        <th style="text-align: center"><b>VOLUME TARGET</b></th>
                        <th style="text-align: center"><b>VALUE TARGET</b></th>
                        <th style="text-align: center"><b>ASP TARGET</b></th>
                        <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                        </thead>
                        <tbody class="data_1">
                            <?php $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){ ?>
                            <tr>
                                <td><?php echo $bdata->branch_name ?></td>
                                <td><?php echo $bdata->product_category_name ?></td>
                                <td><?php echo $bdata->volume; $tvol = $tvol + $bdata->volume; ?> <input type="hidden" class="b_volume1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->volume ?>"></td>
                                <td><?php echo $bdata->value; $tval = $tval + $bdata->value; ?><input type="hidden" class="b_value1<?php echo $bdata->idproductcategory;?>" value="<?php echo $bdata->value ?>"></td>
                                <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                            </tr>
                            <?php } ?>
                            <tr style="background-color: #fbe0e0">
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $tvol; ?></b></td>
                                <td><b><?php echo $tval; ?></b></td>
                                <td><b><?php echo round($tasp); ?></b></td>
                                <td><b><?php echo $trev; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div><br>
                <div style="text-align: center;font-size: 18px;"><b><?php echo $slabs_data->slab_name; ?></b></div>
                    <?php if($slabs_data){ ?>
                    <div class="col-md-10 col-md-offset-1">
                        <table class="table table-bordered table-condensed text-center" id="branch_target_report">
                            <thead style="background-color: #99ccff;" >
                            <th style="text-align: center"><b>BRANCH</b></th>
                            <th style="text-align: center"><b>PRODUCT CATEGORY</b></th>
                            <th style="text-align: center"><b>VOLUME TARGET</b></th>
                            <th style="text-align: center"><b>VALUE TARGET</b></th>
                            <th style="text-align: center"><b>ASP TARGET</b></th>
                            <th style="text-align: center"><b>REVENUE TARGET(%)</b></th>
                            </thead>
                            <tbody class="data_1">
                                <?php $volume= 0; $value=0; $tvol=0; $tval=0; $tasp=0; $trev=0; foreach ($branch_target_data as $bdata){
                                        $volume = round(($bdata->volume * $slabs_data->target_per)/100,1);
                                        $value = round(($bdata->value * $slabs_data->target_per)/100,1);
                                    ?>
                                <tr>
                                    <td><?php echo $bdata->branch_name ?></td>
                                    <td><?php echo $bdata->product_category_name ?></td>
                                    <td><?php echo $volume; $tvol = $tvol + $volume; ?> <input type="hidden" class="b_volume<?php echo $bdata->idproductcategory;?>" value="<?php echo $volume ?>"></td>
                                    <td><?php echo $value; $tval = $tval + $value; ?><input type="hidden" class="b_value<?php echo $bdata->idproductcategory;?>" value="<?php echo $value ?>"></td>
                                    <td><?php echo round($bdata->asp); $tasp = $tasp + $bdata->asp; ?></td>
                                    <td><?php echo $bdata->revenue.'%'; $trev = $trev + $bdata->revenue; ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="background-color: #99ccff">
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $tvol; ?></b></td>
                                    <td><b><?php echo $tval; ?></b></td>
                                    <td><b><?php echo round($tasp); ?></b></td>
                                    <td><b><?php echo $trev; ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                <form id="myform">
                    <div style="height: 700px;overflow-x: auto;padding: 0">
                        <table class="table table-bordered table-condensed text-center" id="promotor_target_report">
                            <thead class="fixheader" style="background-color: #c6e6f5">
                                <th class="fixleft"></th>
                                <th class="fixleft1"></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="border: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                        <th style="border-left: 1px solid #c6e6f5"></th>
                                    <?php } else{ ?>
                                        <th style="border-right: 1px solid #c6e6f5"></th>
                                        <th style="text-align: left;border-left: 1px solid #c6e6f5"><?php echo $pcat->product_category_name; ?></th>
                                    <?php }?>
                                <?php } ?>
                            </thead>
                            <thead class="fixheader1" style="background-color: #c6e6f5"> 
                                <th class="fixleft"><b>Promotor Name</b></th>
                                <th class="fixleft1"><b>Promotor Brand</b></th>
                                <?php foreach ($product_cat_data as $pcat){ ?>
                                    <td><b>Volume</b></td>
                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                        <td><b>Value</b></td>
                                        <td><b>Asp</b></td>
                                        <td><b>REVENUE</b></td>
                                    <?php } else{  ?>
                                        <td><b>Connect</b></td>                            
                                    <?php } 
                                } ?>

                            </thead>
                            <tbody >
                                <?php $sr=1; foreach ($promotor_data as $pdata){
                                    $brand_data = $this->Target_model->get_brand_data_byidpromotor($pdata->id_users);?>
                                    <tr class="promotordataedit">
                                        <td class="fixleft"><?php echo $pdata->user_name; ?> 
                                            <input type="hidden" name="iduser[]" value="<?php echo $pdata->id_users;?>">
                                        </td>
                                        <td class="fixleft1"><?php if($brand_data){ echo $brand_data->brand_name; }else{ echo "Brand Not Assigned To This Promotor"; }?> 
                                            <input type="hidden" name="idbrand[]" value="<?php if($brand_data){ echo $brand_data->id_brand; } ?>">
                                        </td>
                                        <?php foreach ($promotor_target_data as $target_data){
                                            foreach ($product_cat_data as $pcat){
                                                if($target_data->idpromotor == $pdata->id_users && $target_data->idproductcategory == $pcat->id_product_category ){ ?>
                                                    <td><input type="text"  style="width: 120px;" name="p_volume[<?php echo $pcat->id_product_category?>][]" id="p_volume<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->volume; ?>"><div style="display: none"><?php echo $target_data->volume; ?></div></td>
                                                   <?php if($pcat->id_product_category != 5 && $pcat->id_product_category != 6){ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_value[<?php echo $pcat->id_product_category?>][]" id="p_value<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->value; ?>"><div style="display: none"><?php echo $target_data->value; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_asp[<?php echo $pcat->id_product_category?>][]" id="p_asp<?php echo $pcat->id_product_category?>" class="form-control"  value="<?php echo $target_data->asp; ?>"><div style="display: none"><?php echo $target_data->asp; ?></div></td>
                                                        <td><input type="text" style="width: 120px;" name="p_revenue[<?php echo $pcat->id_product_category?>][]" id="p_revenue<?php echo $pcat->id_product_category?>" class="form-control" value="<?php echo $target_data->revenue; ?>"><div style="display: none"><?php echo $target_data->revenue; ?></div></td>
                                                    <?php } else{ ?>
                                                        <td><input type="text" style="width: 120px;" name="p_connect[<?php echo $pcat->id_product_category?>][]" id="p_connect<?php echo $pcat->id_product_category?>" class="form-control" value="<?php echo $target_data->connect; ?>"><div style="display: none"><?php echo $target_data->connect; ?></div></td>
                                                    <?php } ?>
                                                    <script>
                                                        $(document).ready(function (){
                                                            $(document).on('change','#p_volume<?php echo $pcat->id_product_category?>,#p_value<?php echo $pcat->id_product_category?>', function(e){
                                                               var parenttr = $(this).closest('td').parent('tr');
                                                               var volume_target = +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val();
                                                               var value_target = +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val();
                                                               var asp = 0
                                                            
                                                               if(volume_target > 0 ){
                                                                    asp = Math.round(value_target/volume_target);
                                                                    $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }else{
                                                                   asp = 0;
                                                                   $(parenttr).find('#p_asp<?php echo $pcat->id_product_category?>').val(asp);
                                                               }
                                                               
                                                                var total_volume_sum =0;
                                                                var total_value_sum =0;
                                                                $('.promotordataedit').each(function (){

                                                                   $(this).find('#p_volume<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_basic_val = $(this).val();
                                                                        if (!isNaN(total_basic_val) && total_basic_val.length !== 0) {
                                                                            total_volume_sum += parseFloat(total_basic_val);
                                                                        }

                                                                    });

                                                                   $(this).find('#p_value<?php echo $pcat->id_product_category?>').each(function () {
                                                                        var total_vbasic_val = $(this).val();
                                                                        if (!isNaN(total_vbasic_val) && total_vbasic_val.length !== 0) {
                                                                            total_value_sum += parseFloat(total_vbasic_val);
                                                                        }
                                                                    });
                                                               })

                                                               var branch_volume = $('.b_volume<?php echo $pcat->id_product_category?>').val();
                                                               var branch_value = $('.b_value<?php echo $pcat->id_product_category?>').val();

                                                               if(total_volume_sum > branch_volume ){
                                                                   alert("Total Volume Amount Shoud Not Greater Than " + branch_volume);
                                                                   var volamount =0;
                                                                   +$(parenttr).find('#p_volume<?php echo $pcat->id_product_category?>').val(volamount);
                                                                   return false;
                                                               }
                                                               if(total_value_sum > branch_value ){
                                                                   alert("Total Value Amound Shoud Not Greater Than " + branch_value);
                                                                   var valamount =0;
                                                                   +$(parenttr).find('#p_value<?php echo $pcat->id_product_category?>').val(valamount);
                                                                   return false;
                                                               }

                                                                });
                                                        }); 
                                                    </script>
                                        <?php } }  } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="month_year" value="<?php echo $monthyear;?>">
                        <input type="hidden" name="idbranch" value="<?php echo $idbranch;?>">
                         <input type="hidden" name="idslab" value="<?php echo $slabs_data->id_target_slab;?>">
                        <input type="hidden" name="form_slab" value="<?php echo $slabs_data->from_date;?>">
                        <input type="hidden" name="to_slab" value="<?php echo $slabs_data->to_date;?>">
                    </div>
                    <div class="clearfix"></div><br>
                    <input type="hidden" name="edit_promotor" value="1">
                    <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_promotor_target_slab_data">Update</button>
                    <div class="clearfix"></div><br>
                </form>
            <?php } else{ ?>
                <div style="text-align: center;font-size: 20px; color:red;">Promotor Target Data Not Found</div>
            <?php }?>
      
        <script>
            $(document).ready(function (){
               $("#myform").on("submit", function(){
                    $(".img").fadeIn();
                }); 
            });
        </script>
        <?php 
    }
     
    
    //******Achevement Reports Slabs wise******
     public function mtd_acheivement_slab_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        
        $this->load->view('target/mtd_acheivement_slab_report',$q);
    }
    
    public function ajax_get_mtd_achivement_slab_byidbranch(){
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idslab = $this->input->post('idslab');
        $slabmonth = $this->input->post('slabmonth');

        // check all slab promotor target setup done or not
        $target_slab_data = $this->Target_model->get_slab_by_month($slabmonth);
        $promotor_target_slab_data = $this->Target_model->get_promotor_target_slab_data_byid($slabmonth, $idbranch, $allbranches);
        $slabcnt = count($target_slab_data);
              
        $pflag= 0;
        
//       die('<pre>'.print_r($promotor_target_slab_data,1).'</pre>');
        if($promotor_target_slab_data){
            if($idbranch == 0 ){
                $allb = explode(',',$allbranches);
            }else{
                $allb[] = $idbranch;
            }
//            die(print_r($allb));
            for($i=0; $i< count($allb); $i++){
                $ptslab_cnt = 0;
                foreach($promotor_target_slab_data as $ps){
                    if($ps->idbranch == $allb[$i]){
                        $ptslab_cnt = $ptslab_cnt + 1;
//                     echo $ps->idbranch . '-'. $ptslab_cnt.'<br>';
                    }
                }
                if($ptslab_cnt  != $slabcnt){
                    if($this->session->userdata('idrole') == 26){
                        $pflag = 1;
                    }else{
                        $pflag = 0; 
                    }
                }
            }
        }
        else{
            if($this->session->userdata('idrole') == 26){
                $pflag = 1;
            }else{
                $pflag = 0; 
            }
        }
        if($slabmonth  != date('Y-m')){
            $pflag = 0;
        }
//      echo $ptslab_cnt;
        if($slabmonth  != date('Y-m')){
            $pflag = 0;
        }
        if($pflag == 0){
        
            if($idslab == '0' ||  $idslab == 0){ 
                $from = $slabmonth.'-01';
                $to = date('Y-m-t', strtotime($from));  
            }else{
                $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
                $from = $slab_data->from_date;
                $to = $slab_data->to_date;
            }

            $target_data = $this->Target_model->ajax_get_mtd_achivement_byidbranch($from,$to,$idpcat,$allpcats,$idbranch,$allbranches);
            $cluster_head = $this->Target_model->get_cluster_head_data();
            if($target_data){ ?>
            <table class="table table-bordered table-condensed text-center" id="mtd_achivement_report" >
                <thead  style="background-color: #9dbfed"  class="fixheader">
                <th style="text-align: center"> <b>SR</b></th>
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>VOLUME TARGET</b></th>
                    <th style="text-align: center"><b>VOLUME ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>VALUE TARGET</b></th>
                    <th style="text-align: center"><b>VALUE ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>ASP TARGET</b></th>
                    <th style="text-align: center"><b>ASP ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>REVENUE</b></th>
                    <th style="text-align: center"><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    $num_cnt = 0;   
                    $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($target_data as $target){ 
                        if($idslab != 0){
                            if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume  * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                        }else{
                            if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        }
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                                               
                        if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                        if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}
                            
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                        $sale_qty = $sale_qty - $sr_qty;
                        $sale_total = $sale_total - $sr_total;
                        $sale_landing = $sale_landing - $sr_landing;
                        
                        //Volume Achivement Per
                        if($tar_volume > 0){
                            $vol_ach_per = ($sale_qty/$tar_volume)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($tar_value > 0){
                            $val_ach_per = ($sale_total/$tar_value)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                        <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                        
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,1);?></b></td>
                        <td><b><?php echo round($total_volume_ach,1);?></b></td>
                        <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,1);?></b></td>
                        <td><b><?php echo round($total_value_ach,1);?></b></td>
                        <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,1);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo  round($total_revenue/$num_cnt,1).'%';?></b></td>
                        <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
        <?php }
        
        }else{ ?>
            <script>
                alert('Promotor Target Setup for All Slabs Pending ');
                
            </script>
        <?php }
    }
    
    public function ajax_get_mtd_achivement_slab_byidzone(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        $idslab = $this->input->post('idslab');
        $slabmonth = $this->input->post('slabmonth');
        
        if($idslab == '0' ||  $idslab == 0){ 
            $from = $slabmonth.'-01';
            $to = date('Y-m-t', strtotime($from)); 
        }else{
            $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
            $from = $slab_data->from_date;
            $to = $slab_data->to_date;
        }
        
        $target_data = $this->Target_model->ajax_get_mtd_achivement_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($cluster_head,1).'</pre>');
        
        if($target_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="mtd_achivement_report">
                       <thead style="background-color: #9dbfed" class="fixheader">
                           <th  style="text-align: center"><b>SR</b></th>
                           <!--<th><b>ZONE</b></th>-->
                           <th  style="text-align: center"><b>ZONE</b></th>
                           <th  style="text-align: center"><b>VOLUME TARGET</b></th>
                           <th  style="text-align: center"><b>VOLUME ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>VALUE TARGET</b></th>
                           <th  style="text-align: center"><b>VALUE ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>ASP TARGET</b></th>
                           <th  style="text-align: center"><b>ASP ACH</b></th>
                           <th  style="text-align: center"><b>ACH(%)</b></th>
                           <th  style="text-align: center"><b>REVENUE</b></th>
                           <th  style="text-align: center"><b>REVENUE ACH</b></th>
                       </thead>
                       <tbody class="data_1">
                           <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                           $sale_qty =0;$sale_total=0;$sale_landing=0;
                           $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                           $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                           $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                           $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                           $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                           $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                            $num_cnt = 0;
                            $sr_qty = 0;$sr_total=0;$sr_landing=0;
                           foreach ($target_data as $target){ 
                                $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone);
                               
                                if($idslab != 0){
                                    if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume  * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                                    if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                                }else{
                                    if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                                    if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                                }
                               if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                               if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                                if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                                if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                                if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}
                        
                               if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                               if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                               if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                               if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                               if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                               if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                                $sale_qty = $sale_qty - $sr_qty;
                                $sale_total = $sale_total - $sr_total;
                                $sale_landing = $sale_landing - $sr_landing;
                        
                               //Volume Achivement Per
                               if($tar_volume > 0){
                                   $vol_ach_per = ($sale_qty/$tar_volume)*100;
                               }else{
                                   $vol_ach_per = 0;
                               }

                               //Value Achivement Per
                               if($tar_value > 0){
                                   $val_ach_per = ($sale_total/$tar_value)*100;
                               }else{
                                   $val_ach_per = 0;
                               }

                               //Asp Achivement
                               if($idpcat == 1|| $idpcat == 32){
                                   if($smart_sale_qty > 0){ 
                                       $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }else{
                                   if($sale_qty > 0){ 
                                       $tar_asp_achiv = $sale_total/$sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }

                               //Target Achivement Per
                               if($tar_asp > 0){
                                   $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                               }else{
                                   $tar_asp_per = 0;
                               }

                             //Revenue Percentage  
                               if($sale_landing > 0){
                                   $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                               }else{
                                   $rev_per = 0;
                               }

                               ?>
                           <tr style="text-align: center">
                               <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                               <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                                   if($clust->idzone == $target->id_zone){
                                       echo $clust->clust_name.', ';
                                   } } ?>
                               </td>-->
                               <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                               <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                               <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                               <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                               <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                               <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                               <td><?php echo round($tar_asp); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                               <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                               <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                               <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                               <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                           </tr>
                           <?php } ?>
                           <tr style="text-align: center">
                               <td></td>
                               <!--<td></td>-->
                               <td><b>Total</b></td>
                                <td><b><?php echo round($total_volume_target,1);?></b></td>
                                <td><b><?php echo round($total_volume_ach,1);?></b></td>
                                <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                                <td><b><?php echo round($total_value_target,1);?></b></td>
                                <td><b><?php echo round($total_value_ach,1);?></b></td>
                                <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                                <td><b><?php echo round($total_asp_target/$num_cnt,1);?></b></td>
                                <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                                <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                                <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                                <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                           </tr>
                       </tbody>
                   </table>
            <?php } else{ ?>
               <table class="table table-bordered table-condensed" id="mtd_achivement_report">
                <thead style="background-color: #9dbfed" class="fixheader">
                    <th  style="text-align: center"><b>SR</b></th>
                    <th style="text-align: center"><b>ZONE</b></th>
                    <th style="text-align: center"><b>CLUSTER HEAD</b></th>
                    <th style="text-align: center"><b>BRANCH</b></th>
                    <th style="text-align: center"><b>PARTNER TYPE</b></th>
                    <th style="text-align: center"><b>BRANCH CATEGORY</b></th>
                    <th style="text-align: center"><b>VOLUME TARGET</b></th>
                    <th style="text-align: center"><b>VOLUME ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>VALUE TARGET</b></th>
                    <th style="text-align: center"><b>VALUE ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>ASP TARGET</b></th>
                    <th style="text-align: center"><b>ASP ACH</b></th>
                    <th style="text-align: center"><b>ACH(%)</b></th>
                    <th style="text-align: center"><b>REVENUE</b></th>
                    <th style="text-align: center"><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0; 
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    $num_cnt=0;
                     $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($target_data as $target){ 
                        if($idslab != 0){
                            if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume  * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                        }else{
                            if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume; } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        }
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                           
                        if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                        if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}
                                
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                        $sale_qty = $sale_qty - $sr_qty;
                        $sale_total = $sale_total - $sr_total;
                        $sale_landing = $sale_landing - $sr_landing;
                        
                        //Volume Achivement Per
                        if($tar_volume > 0){
                            $vol_ach_per = ($sale_qty/$tar_volume)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($tar_value > 0){
                            $val_ach_per = ($sale_total/$tar_value)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo $tar_volume; $total_volume_target = $total_volume_target + $tar_volume; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo $tar_value; $total_value_target = $total_value_target +$tar_value;  ?></td>
                        <td><?php echo $sale_total; 
                        $total_value_ach = $total_value_ach + $sale_total;
                        $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php  echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                        
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,2);?></b></td>
                        <td><b><?php echo round($total_volume_ach,2);?></b></td>
                        <td><b><?php  if($total_volume_target > 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,2);?></b></td>
                        <td><b><?php echo round($total_value_ach,2);?></b></td>
                        <td><b><?php if($total_value_target > 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,2);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php if(($total_asp_target/$num_cnt) > 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,2).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($total_landing > 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
            <?php } ?>
        <?php }
    }
    
     public function drr_acheivement_slab_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        
        $this->load->view('target/drr_acheivement_slab_report',$q);
    }
    public function ajax_get_drr_achivement_slab_byidbranch(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idslab = $this->input->post('idslab');
        $slabmonth = $this->input->post('slabmonth');
        
          // check all slab promotor target setup done or not
        $target_slab_data = $this->Target_model->get_slab_by_month($slabmonth);
        
        $promotor_target_slab_data = $this->Target_model->get_promotor_target_slab_data_byid($slabmonth, $idbranch, $allbranches);
        
        $slabcnt = count($target_slab_data);
              
        $pflag= 0;
        
        if($promotor_target_slab_data){
            if($idbranch == 0 ){
                $allb = explode(',',$allbranches);
            }else{
                $allb[] = $idbranch;
            }
            for($i=0; $i< count($allb); $i++){
                $ptslab_cnt = 0;
                foreach($promotor_target_slab_data as $ps){
                    if($ps->idbranch == $allb[$i]){
                        $ptslab_cnt = $ptslab_cnt + 1;
                    }
                }
                if($ptslab_cnt  != $slabcnt){
                    if($this->session->userdata('idrole') == 26){
                        $pflag = 1;
                    }else{
                        $pflag = 0; 
                    }
                    
                }
            }
        }else{
            if($this->session->userdata('idrole') == 26){
                $pflag = 1;
            }else{
                $pflag = 0; 
            }
        }
        
          if($slabmonth  != date('Y-m')){
            $pflag = 0;
        }
          
        
        
        if($pflag == 0){
            if($idslab == '0' ||  $idslab == 0){ 
                $from_slab = $slabmonth.'-01';
                $to_slab = date('Y-m-t', strtotime($from)); 
            }else{
                $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
                $from_slab = $slab_data->from_date;
                $to_slab = $slab_data->to_date;
            }

            $first_date = $from;
            $last_date = date('d', strtotime($to_slab));
//            $end_date = date('d', strtotime($from));
            $end_date = date('d', strtotime('-1 day', strtotime($from)));
            if(date('d', strtotime($from)) == 01){
                $remaining_days = date('d', strtotime($to_slab));
            }else{
                $remaining_days =  $last_date - $end_date;
            }
            
             //******Last Target Slab Value Start ************** 
            $last_slab_data = $this->Target_model->get_target_slab_per_data_byid($slabmonth, $from_slab);
            //******Last Target Slab Value End ************** 
            
            $target_data = $this->Target_model->ajax_get_drr_achivement_slab_byidbranch($from,$from_slab,$idpcat,$allpcats,$idbranch,$allbranches);
            
            $cluster_head = $this->Target_model->get_cluster_head_data();
//            die('<pre>'.print_r($target_data,1).'</pre>');
            if($target_data){ ?>
                <table class="table table-bordered table-condensed" id="mtd_achivement_report">
                    <thead  style="background-color: #9dbfed"  class="fixheader">
                        <th><b>SR </b></th>
                        <th><b>ZONE</b></th>
                        <th><b>CLUSTER HEAD</b></th>
                        <th><b>BRANCH</b></th>
                        <th><b>PARTNER TYPE</b></th>
                        <th><b>BRANCH CATEGORY</b></th>
                        <th><b>VOLUME TARGET</b></th>
                        <th><b>VOLUME ACH</b></th>
                        <th><b>ACH(%)</b></th>
                        <th><b>VALUE TARGET</b></th>
                        <th><b>VALUE ACH</b></th>
                        <th><b>ACH(%)</b></th>
                        <th><b>ASP TARGET</b></th>
                        <th><b>ASP ACH</b></th>
                        <th><b>ACH(%)</b></th>
                        <th><b>REVENUE</b></th>
                        <th><b>REVENUE ACH</b></th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                        $sale_qty =0;$sale_total=0;$sale_landing=0;
                        $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                        $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                        $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                        $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                        $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                        $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;

                        $volume_target = 0; $value_target=0;

                        $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                        $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;

                        $num_cnt = 0;
                        
                        $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                        $last_tar_vol_gap=0;$tar_vol_gap=0;
                        $last_tar_val_gap=0;$tar_val_gap=0;
                        
                         $sr_qty = 0;$sr_total=0;$sr_landing=0;
                        
                        foreach ($target_data as $target){ 
                            
                            //******Last Target Slab Value Start **************
                            
                            if($last_slab_data){
                                $last_tar_per = $last_slab_data->tar_per;
                                    
                            }else{
                                $last_tar_per = 0;
                            }
                            $last_tar_vol = round(($target->tar_volume * $last_tar_per)/100);
                            $last_tar_val = round(($target->tar_value * $last_tar_per)/100);
                            
                            if( $target->last_csale_qty > 0){ $last_sale_qty = $target->last_csale_qty;  } else{ $last_sale_qty = 0;};
                            if( $target->last_csale_total > 0){ $last_sale_total = round($target->last_csale_total); } else{ $last_sale_total = 0;};
                            
                            $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                            $tar_val_gap = $last_tar_val - $last_sale_total;
                            
                            if($tar_vol_gap > 0){
                                $last_tar_vol_gap = $tar_vol_gap;
                                $last_tar_val_gap = $tar_val_gap;
                            }else{
                                $last_tar_vol_gap = 0;
                                $last_tar_val_gap = 0;
                            }
                            
                              
                            //******Last Target Slab Value END **************
                            
                            if($idslab != 0){
                                if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                                if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                            }else{ 
                                if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume ; } else{ $tar_volume = 0;};
                                if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                            }
                            if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                            if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   

                            if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                            if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                            if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                            if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                            if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                            if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                            if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                            if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                            if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                            if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                            if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                            if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                            
                            if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                            if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                            $sale_qty = $sale_qty - $sr_qty;
                            $sale_total = $sale_total - $sr_total;
                            $sale_landing = $sale_landing - $sr_landing;
                            
                            //Volume Target
                            if($remaining_days > 0){ 
    //                            $volume_target = ($tar_volume - $sale_qty)/$remaining_days;
    //                            $value_target = ($tar_value - $sale_total)/$remaining_days;
                                $volume_target = (($tar_volume + $last_tar_vol_gap) - $c_saleqty)/$remaining_days;
                                $value_target = (($tar_value + $last_tar_val_gap) - $c_saletotoal)/$remaining_days;
                            }else{
                                $volume_target = 0;
                                $value_target =0;
                            }

                            //Volume Achivement Per
                            if($volume_target != 0){
                                $vol_ach_per = ($sale_qty/$volume_target)*100;
                            }else{
                                $vol_ach_per = 0;
                            }

                            //Value Achivement Per
                            if($value_target != 0){
                                $val_ach_per = ($sale_total/$value_target)*100;
                            }else{
                                $val_ach_per = 0;
                            }

                            //Asp Achivement
                            if($idpcat == 1|| $idpcat == 32){
                                if($smart_sale_qty > 0){ 
                                    $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                }else{
                                    $tar_asp_achiv = 0;
                                }
                            }else{
                                if($sale_qty > 0){ 
                                    $tar_asp_achiv = $sale_total/$sale_qty;
                                }else{
                                    $tar_asp_achiv = 0;
                                }
                            }

                            //Target Achivement Per
                            if($tar_asp > 0){
                                $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                            }else{
                                $tar_asp_per = 0;
                            }

                          //Revenue Percentage  
                            if($sale_landing > 0){
                                $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                            }else{
                                $rev_per = 0;
                            }

                            ?>
                        <tr>
                            <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                            <td><?php echo $target->zone_name; ?></td>
                            <td><?php foreach ($cluster_head as $clust){
                                if($clust->clustbranch == $target->id_branch){
                                    echo $clust->clust_name.', ';
                                } } ?>
                            </td>
                            <td><?php echo $target->branch_name ?></td>
                            <td><?php echo $target->partner_type ?></td>
                            <td><?php echo $target->branch_category_name ?></td>
                            <td><?php echo  round($volume_target,0) ; //.'='.$tar_volume.' -'. $c_saleqty.'/'.$remaining_days;
                            $total_volume_target = $total_volume_target + $volume_target; ?></td>
                            <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                            <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                            <td><?php echo round($value_target,1); $total_value_target = $total_value_target +$value_target;  ?></td>
                            <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                            <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                            <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                            <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                            <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                            <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                            <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                        </tr>
                        <?php } ?>
                        <tr style="text-align: center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo round($total_volume_target,0);?></b></td>
                            <td><b><?php echo round($total_volume_ach,0);?></b></td>
                            <td><b><?php if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                            <td><b><?php echo round($total_value_target,0);?></b></td>
                            <td><b><?php echo round($total_value_ach,0);?></b></td>
                            <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                            <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                            <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                            <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                            <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                            <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                        </tr>
                    </tbody>

                </table>
            <?php }
        }else{ ?>
            <script>
                alert('Promotor Target Setup for All Slabs Pending ');
                
            </script>
        <?php }
    }
    public function ajax_get_drr_achivement_slab_byidzone(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        $idslab = $this->input->post('idslab');
        $slabmonth = $this->input->post('slabmonth');
        if($idslab == '0' ||  $idslab == 0){ 
            $from_slab = $slabmonth.'-01';
            $to_slab = date('Y-m-t', strtotime($from)); 
        }else{
            $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
            $from_slab = $slab_data->from_date;
            $to_slab = $slab_data->to_date;
        }
      
        $first_date = $from;
        $last_date = date('d', strtotime($to_slab));
//        $end_date = date('d', strtotime($from));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));
        if(date('d', strtotime($from)) == 01){
            $remaining_days = date('d', strtotime($to_slab));
        }else{
            $remaining_days =  $last_date - $end_date;
        }
//        echo $remaining_days;
           
        
        $target_data = $this->Target_model->ajax_get_drr_achivement_slab_byidzone($from,$from_slab,$idpcat,$allpcats,$idzone,$allzone);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
        
        //******Last Target Slab Value Start ************** 
            $last_slab_data = $this->Target_model->get_target_slab_per_data_byid($slabmonth, $from_slab);
         //******Last Target Slab Value End **************
        
        if($target_data){
            if($idzone == 'all'){ ?>
                <table class="table table-bordered table-condensed" id="mtd_achivement_report">
                       <thead  style="background-color: #9dbfed"  class="fixheader">
                           <th><b>SR</b></th>
                           <!--<th><b>ZONE</b></th>-->
                           <th><b>ZONE</b></th>
                           <th><b>VOLUME TARGET</b></th>
                           <th><b>VOLUME ACH</b></th>
                           <th><b>ACH(%)</b></th>
                           <th><b>VALUE TARGET</b></th>
                           <th><b>VALUE ACH</b></th>
                           <th><b>ACH(%)</b></th>
                           <th><b>ASP TARGET</b></th>
                           <th><b>ASP ACH</b></th>
                           <th><b>ACH(%)</b></th>
                           <th><b>REVENUE</b></th>
                           <th><b>REVENUE ACH</b></th>
                       </thead>
                       <tbody class="data_1">
                           <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                           $sale_qty =0;$sale_total=0;$sale_landing=0;
                           $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                           $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;

                           $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                           $total_value_target=0;$total_value_ach=0;$total_value_per=0;
                           $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                           $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                           
                           $volume_target = 0; $value_target=0;
                           
                           $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                           $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                           $num_cnt = 0;
                            
                            $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                            $last_tar_vol_gap=0;$tar_vol_gap=0;
                            $last_tar_val_gap=0;$tar_val_gap=0;
                             $sr_qty = 0;$sr_total=0;$sr_landing=0;
                           foreach ($target_data as $target){ 
                               
                               $branch_cnt = $this->Target_model->get_branch_cnt_byidzone($target->id_zone);
                               
                                //******Last Target Slab Value Start **************
                               
                            if($last_slab_data){
                                $last_tar_per = $last_slab_data->tar_per;
                                    
                            }else{
                                $last_tar_per = 0;
                            }
                            $last_tar_vol = round(($target->tar_volume * $last_tar_per)/100);
                            $last_tar_val = round(($target->tar_value * $last_tar_per)/100);
                            
                            if( $target->last_csale_qty > 0){ $last_sale_qty = $target->last_csale_qty;  } else{ $last_sale_qty = 0;};
                            if( $target->last_csale_total > 0){ $last_sale_total = round($target->last_csale_total); } else{ $last_sale_total = 0;};
                            
                            $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                            $tar_val_gap = $last_tar_val - $last_sale_total;
                            
                            if($tar_vol_gap > 0){
                                $last_tar_vol_gap = $tar_vol_gap;
                                $last_tar_val_gap = $tar_val_gap;
                            }else{
                                $last_tar_vol_gap = 0;
                                $last_tar_val_gap = 0;
                            }
                            
                         
                            //******Last Target Slab Value END **************
                               
                               if($idslab != 0){
                                    if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                                    if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                                }else{ 
                                    if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume ; } else{ $tar_volume = 0;};
                                    if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                                }
                        
                               if($target->tar_asp > 0){ $tar_asp = $target->tar_asp/$branch_cnt->branch_cnt;}else{ $tar_asp = 0;}
                               if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue/$branch_cnt->branch_cnt; } else{ $tar_rev = 0; }   

                               if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                               if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                               if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}

                               if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                               if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                               if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }

                                if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                                if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                                if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                                if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                                if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                                if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                                
                                if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                                if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                                if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                                $sale_qty = $sale_qty - $sr_qty;
                                $sale_total = $sale_total - $sr_total;
                                $sale_landing = $sale_landing - $sr_landing;
                            
                               
                               //Volume Target
                                if($remaining_days > 0){ 
//                                    $volume_target = ($tar_volume - $sale_qty)/$remaining_days;
//                                    $value_target = ($tar_value - $sale_total)/$remaining_days;
                                    $volume_target = (($tar_volume + $last_tar_vol_gap) - $c_saleqty)/$remaining_days;
                                    $value_target = (($tar_value + $last_tar_val_gap) - $c_saletotoal)/$remaining_days;
                                }else{
                                    $volume_target = 0;
                                    $value_target =0;
                                }

                                //Volume Achivement Per
                                if($volume_target != 0){
                                    $vol_ach_per = ($sale_qty/$volume_target)*100;
                                }else{
                                    $vol_ach_per = 0;
                                }

                      
                               //Value Achivement Per
                               if($value_target != 0){
                                   $val_ach_per = ($sale_total/$value_target)*100;
                               }else{
                                   $val_ach_per = 0;
                               }

                               //Asp Achivement
                               if($idpcat == 1|| $idpcat == 32){
                                   if($smart_sale_qty > 0){ 
                                       $tar_asp_achiv = $smart_total/$smart_sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }else{
                                   if($sale_qty > 0){ 
                                       $tar_asp_achiv = $sale_total/$sale_qty;
                                   }else{
                                       $tar_asp_achiv = 0;
                                   }
                               }

                               //Target Achivement Per
                               if($tar_asp > 0){
                                   $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                               }else{
                                   $tar_asp_per = 0;
                               }

                             //Revenue Percentage  
                               if($sale_landing > 0){
                                   $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                               }else{
                                   $rev_per = 0;
                               }

                               ?>
                           <tr style="text-align: center">
                               <td><?php echo $sr++; $num_cnt = $num_cnt + 1;?></td>
                               <td><?php echo $target->zone_name; ?></td>
<!--                               <td><?php foreach ($cluster_head as $clust){
                                   if($clust->idzone == $target->id_zone){
                                       echo $clust->clust_name.', ';
                                   } } ?>
                               </td>-->
                               <td><?php echo round($volume_target,0); $total_volume_target = $total_volume_target + $volume_target; ?></td>
                               <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                               <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                               <td><?php echo round($value_target,1); $total_value_target = $total_value_target +$value_target;  ?></td>
                               <td><?php echo round($sale_total,1); $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                               <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                               <td><?php echo round($tar_asp,1); $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                               <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                               <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                               <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                               <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>

                           </tr>
                           <?php } ?>
                           <tr style="text-align: center">
                               <td></td>
                               <!--<td></td>-->
                               <td><b>Total</b></td>
                                <td><b><?php echo round($total_volume_target,0);?></b></td>
                                <td><b><?php echo round($total_volume_ach,0);?></b></td>
                                <td><b><?php  if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                                <td><b><?php echo round($total_value_target,0);?></b></td>
                                <td><b><?php echo round($total_value_ach,0);?></b></td>
                                <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                                <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                                <td><b><?php $tt=0; if( $total_volume_ach != 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                                <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,2).'%';}else{ echo '0%';} ?></b></td>
                                <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                                <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                           </tr>
                       </tbody>
                   </table>
            <?php } else{ ?>
               <table class="table table-bordered table-condensed" id="mtd_achivement_report">
                <thead  style="background-color: #9dbfed"  class="fixheader">
                    <th><b>SR</b></th>
                    <th><b>ZONE</b></th>
                    <th><b>CLUSTER HEAD</b></th>
                    <th><b>BRANCH</b></th>
                    <th><b>PARTNER TYPE</b></th>
                    <th><b>BRANCH CATEGORY</b></th>
                    <th><b>VOLUME TARGET</b></th>
                    <th><b>VOLUME ACH</b></th>
                    <th><b>ACH(%)</b></th>
                    <th><b>VALUE TARGET</b></th>
                    <th><b>VALUE ACH</b></th>
                    <th><b>ACH(%)</b></th>
                    <th><b>ASP TARGET</b></th>
                    <th><b>ASP ACH</b></th>
                    <th><b>ACH(%)</b></th>
                    <th><b>REVENUE</b></th>
                    <th><b>REVENUE ACH</b></th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $tar_volume =0; $tar_value=0;$tar_asp=0;$tar_rev=0;
                    $sale_qty =0;$sale_total=0;$sale_landing=0;
                    $smart_sale_qty=0;$smart_total=0;$smart_landing=0;
                    $vol_ach_per=0;$val_ach_per=0;$tar_asp_achiv=0;$tar_asp_per=0;$rev_per=0;
                    
                    $total_volume_target =0; $total_volume_ach=0;$total_volume_per=0;
                    $total_value_target=0;$total_value_ach=0;$total_value_per=0; 
                    $total_asp_target=0;$total_asp_ach=0;$total_asp_per=0;
                    $total_revenue=0;$total_revenue_ach=0;$t_land=0;$total_landing=0;
                    
                     $c_saleqty =0; $c_saletotoal=0;$c_salelnding=0;
                    $csmart_sale_qty=0;$csmart_total=0;$csmart_landing=0;
                    $volume_target = 0; $value_target=0;
                    $num_cnt =0 ;
                    
                    $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                    $last_tar_vol_gap=0;$tar_vol_gap=0;
                    $last_tar_val_gap=0;$tar_val_gap=0;
                     $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($target_data as $target){ 
                        //*****LaST Target salb data*********
                        if($last_slab_data){
                            $last_tar_per = $last_slab_data->tar_per;

                        }else{
                            $last_tar_per = 0;
                        }
                        $last_tar_vol = round(($target->tar_volume * $last_tar_per)/100);
                        $last_tar_val = round(($target->tar_value * $last_tar_per)/100);

                        if( $target->last_csale_qty > 0){ $last_sale_qty = $target->last_csale_qty;  } else{ $last_sale_qty = 0;};
                        if( $target->last_csale_total > 0){ $last_sale_total = round($target->last_csale_total); } else{ $last_sale_total = 0;};

                        $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                        $tar_val_gap = $last_tar_val - $last_sale_total;

                        if($tar_vol_gap > 0){
                            $last_tar_vol_gap = $tar_vol_gap;
                            $last_tar_val_gap = $tar_val_gap;
                        }else{
                            $last_tar_vol_gap = 0;
                            $last_tar_val_gap = 0;
                        }
                         //  echo   $last_tar_vol_gap;
                        
                        //*****LaST Target salb data end*********
                        
                        if($idslab != 0){
                            if( $target->tar_volume > 0){ $tar_volume = round(($target->tar_volume * $slab_data->target_per)/100); } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = round(($target->tar_value * $slab_data->target_per)/100);}else{$tar_value = 0;}
                        }else{ 
                            if( $target->tar_volume > 0){ $tar_volume = $target->tar_volume ; } else{ $tar_volume = 0;};
                            if($target->tar_value > 0){ $tar_value = $target->tar_value;}else{$tar_value = 0;}
                        }
                        if($target->tar_asp > 0){ $tar_asp = $target->tar_asp;}else{ $tar_asp = 0;}
                        if($target->tar_revenue > 0){ $tar_rev = $target->tar_revenue; } else{ $tar_rev = 0; }   
                                               
                        if($target->sale_qty > 0) { $sale_qty = $target->sale_qty; } else{ $sale_qty = 0 ;}
                        if($target->sale_total > 0){ $sale_total = $target->sale_total;}else{ $sale_total = 0;}
                        if($target->sale_landing > 0){ $sale_landing = $target->sale_landing; } else{ $sale_landing = 0;}
                        
                        if($target->smart_sale_qty > 0){ $smart_sale_qty = $target->smart_sale_qty;}else{ $smart_sale_qty = 0;}
                        if($target->smart_total > 0){$smart_total = $target->smart_total;}else{ $smart_total =0; }
                        if($target->smart_landing > 0){ $smart_landing = $target->smart_landing; } else{ $smart_landing = 0; }
                       
                         if($target->csale_qty > 0) { $c_saleqty = $target->csale_qty; } else{ $c_saleqty = 0 ;}
                        if($target->csale_total > 0){ $c_saletotoal = $target->csale_total;}else{ $c_saletotoal = 0;}
                        if($target->csale_landing > 0){ $c_salelnding = $target->csale_landing; } else{ $c_salelnding = 0;}

                        if($target->csmart_sale_qty > 0){ $csmart_sale_qty = $target->csmart_sale_qty;}else{ $csmart_sale_qty = 0;}
                        if($target->csmart_total > 0){$csmart_total = $target->csmart_total;}else{ $csmart_total =0; }
                        if($target->csmart_landing > 0){ $csmart_landing = $target->csmart_landing; } else{ $csmart_landing = 0; }
                       
                        if($target->sale_return_qty > 0) { $sr_qty = $target->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($target->sreturn_total > 0){ $sr_total = $target->sreturn_total;}else{ $sr_total = 0;}
                        if($target->sreturn_landing > 0){ $sr_landing = $target->sreturn_landing; } else{ $sr_landing = 0;}

                        $sale_qty = $sale_qty - $sr_qty;
                        $sale_total = $sale_total - $sr_total;
                        $sale_landing = $sale_landing - $sr_landing;
                            
                       //Volume Target
                        
                        if($remaining_days > 0){ 
                            $volume_target = (($tar_volume + $last_tar_vol_gap) - $c_saleqty)/$remaining_days;
                            $value_target = (($tar_value + $last_tar_val_gap) - $c_saletotoal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target =0;
                        }
                                                
                        //Volume Achivement Per
                        if($volume_target != 0){
                            $vol_ach_per = ($sale_qty/$volume_target)*100;
                        }else{
                            $vol_ach_per = 0;
                        }
                        
                        //Value Achivement Per
                        if($value_target != 0){
                            $val_ach_per = ($sale_total/$value_target)*100;
                        }else{
                            $val_ach_per = 0;
                        }
                        
                        //Asp Achivement
                        if($idpcat == 1|| $idpcat == 32){
                            if($smart_sale_qty > 0){ 
                                $tar_asp_achiv = $smart_total/$smart_sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }else{
                            if($sale_qty > 0){ 
                                $tar_asp_achiv = $sale_total/$sale_qty;
                            }else{
                                $tar_asp_achiv = 0;
                            }
                        }
                        
                        //Target Achivement Per
                        if($tar_asp > 0){
                            $tar_asp_per = ($tar_asp_achiv/$tar_asp)*100;
                        }else{
                            $tar_asp_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($sale_landing > 0){
                            $rev_per = (($sale_total - $sale_landing)*100)/$sale_landing;
                        }else{
                            $rev_per = 0;
                        }
                        
                        ?>
                    <tr>
                        <td><?php echo $sr++; $num_cnt = $num_cnt+1;?></td>
                        <td><?php echo $target->zone_name; ?></td>
                        <td><?php foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $target->id_branch){
                                echo $clust->clust_name.', ';
                            } } ?>
                        </td>
                        <td><?php echo $target->branch_name ?></td>
                        <td><?php echo $target->partner_type ?></td>
                        <td><?php echo $target->branch_category_name ?></td>
                        <td><?php echo round($volume_target,0); $total_volume_target = $total_volume_target + $volume_target; ?></td>
                        <td><?php echo $sale_qty; $total_volume_ach = $total_volume_ach + $sale_qty; ?></td>
                        <td><?php echo round($vol_ach_per,1).'%'; $total_volume_per = $total_volume_per + $vol_ach_per; ?></td>
                        <td><?php echo round($value_target,0); $total_value_target = $total_value_target +$value_target;  ?></td>
                        <td><?php echo $sale_total; $total_value_ach = $total_value_ach + $sale_total;  $total_landing = $total_landing + $sale_landing;?></td>
                        <td><?php echo round($val_ach_per,1).'%'; $total_value_per = $total_value_per + $val_ach_per; ?></td>
                        <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
                        <td><?php echo round($tar_asp_achiv); $total_asp_ach = $total_asp_ach +$tar_asp_achiv;  ?></td>
                        <td><?php echo round($tar_asp_per,1).'%';  $total_asp_per = $total_asp_per + $tar_asp_per; ?></td>
                        <td><?php echo round($tar_rev,2).'%'; $total_revenue = $total_revenue + $tar_rev; ?></td>
                        <td><?php echo round($rev_per,2).'%'; $total_revenue_ach = $total_revenue_ach + $rev_per; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($total_volume_target,0);?></b></td>
                        <td><b><?php echo round($total_volume_ach,0);?></b></td>
                        <td><b><?php if($total_volume_target != 0){ echo round(($total_volume_ach/$total_volume_target)*100,1).'%';}else{ echo '0%'; }?></b></td>
                        <td><b><?php echo round($total_value_target,0);?></b></td>
                        <td><b><?php echo round($total_value_ach,0);?></b></td>
                        <td><b><?php if($total_value_target != 0){ echo round(($total_value_ach/$total_value_target)*100,1),'%'; } else{ echo '0%';}?></b></td>
                        <td><b><?php echo round($total_asp_target/$num_cnt,0);?></b></td>
                        <td><b><?php $tt=0; if( $total_volume_ach > 0){$tt = $total_value_ach/$total_volume_ach; } echo round($tt,1);?></b></td>
                        <td><b><?php if(($total_asp_target/$num_cnt) != 0){ echo round(($tt/($total_asp_target/$num_cnt))*100,1).'%';}else{ echo '0%';} ?></b></td>
                        <td><b><?php echo round($total_revenue/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($total_landing != 0){$t_land = (($total_value_ach - $total_landing)*100)/$total_landing;}else{$t_land=0;} echo round($t_land,2).'%';?></b></td>
                    </tr>
                </tbody>
                    
            </table>
            <?php } ?>
        <?php }
    }
   
      public function mtd_promotor_slab_sale_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        
        $this->load->view('target/mtd_promotor_sale_slab_report',$q);
        
    }
    
    public function ajax_get_mtd_promotor_sale_report_slab_byidbranch() {

        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idslab = $this->input->post('idslab');
        
        $slabmonth = $this->input->post('slabmonth');
        
          // check all slab promotor target setup done or not
        $target_slab_data = $this->Target_model->get_slab_by_month($slabmonth);
        
        $promotor_target_slab_data = $this->Target_model->get_promotor_target_slab_data_byid($slabmonth, $idbranch, $allbranches);
        
        $slabcnt = count($target_slab_data);
              
        $pflag= 0;
        
        if($promotor_target_slab_data){
            if($idbranch == 0 ){
                $allb = explode(',',$allbranches);
            }else{
                $allb[] = $idbranch;
            }
            for($i=0; $i< count($allb); $i++){
                $ptslab_cnt = 0;
                foreach($promotor_target_slab_data as $ps){
                    if($ps->idbranch == $allb[$i]){
                        $ptslab_cnt = $ptslab_cnt + 1;
                    }
                }
                if($ptslab_cnt  != $slabcnt){
                    if($this->session->userdata('idrole') == 26){
                        $pflag = 1;
                    }else{
                        $pflag = 0; 
                    }
                    
                }
            }
        }else{
            if($this->session->userdata('idrole') == 26){
                $pflag = 1;
            }else{
                $pflag = 0; 
            }
        }
          if($slabmonth  != date('Y-m')){
            $pflag = 0;
        }
        if($pflag == 0){
         
           
            if($idslab == '0' ||  $idslab == 0){ 
                $from = $slabmonth.'-01';
                $to = date('Y-m-t', strtotime($from));  
            }else{
                $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
                $from = $slab_data->from_date;
                $to = $slab_data->to_date;
            }
        
            $sale_data = $this->Target_model->get_promotor_sale_report_slab_byidbranch($from, $to, $idslab,  $idpcat, $allpcats, $idbranch, $allbranches);
//            die('<pre>'.print_r($sale_data,1).'</pre>');
            if($sale_data){ ?>
                    <table class="table table-bordered table-condensed text-center" id="MTD_Promotor_Sale_Report">
                        <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">PROMOTER NAME</th>
                        <th style="text-align: center">VOLUME TARGET</th>
                        <th style="text-align: center">VOLUME ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">VALUE TARGET</th>
                        <th style="text-align: center">VALUE ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">ASP TARGET</th>
                        <th style="text-align: center">ASP ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">REVENUE TARGET</th>
                        <th style="text-align: center">REVENUE ACH</th>

                    </thead>
                    <tbody class="data_1">
                        <?php 
                        $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                        $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                        $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                        $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;

                        $num_cnt = 0;
                        $sr_qty = 0;$sr_total=0;$sr_landing=0;
                        foreach ($sale_data as $sale){ 
                            if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                            if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                            if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                            if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                            if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                            if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                            if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                          
                            if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                            if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                          
                            $salqt = $salqt - $sr_qty;
                            $saletot = $saletot - $sr_total;
                            $slanding = $slanding - $sr_landing;
                            if($vol > 0){
                                $vol_ach = ($salqt / $vol)*100;
                            }else{
                                $vol_ach =0;
                            }

                            if($vall > 0){
                                $val_ach = ($saletot / $vall)*100;
                            }else{
                                $val_ach =0;
                            }
                            if($salqt > 0){
                                $asp_ach = ($saletot/ $salqt);
                            }else{
                                $asp_ach =0;
                            }

                            //Target Achivement Per
                            if($asp > 0){
                                $asp_ach_per = ($asp_ach/$asp)*100;
                            }else{
                                $asp_ach_per = 0;
                            }

                          //Revenue Percentage  
                            if($slanding > 0){
                                $rev_per = (($saletot - $slanding)*100)/$slanding;
                            }else{
                                $rev_per = 0;
                            }


                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                            ?>
                        <tr style="text-align: center">
                            <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1; ?></td>
                            <td><?php echo $sale->branch_name; ?></td>
                            <td><?php echo $sale->partner_type; ?></td>
                            <td><?php echo $sale->branch_category_name; ?></td>
                            <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                            <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                            <td><?php echo round($vol_ach,1).'%'; ?></td>
                            <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                            <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot; $t_sale_landing = $t_sale_landing + $slanding; ?></td>
                            <td><?php echo round($val_ach,1).'%'; ?></td>
                            <td><?php echo round($asp,1); $t_asp = $t_asp +$asp; ?></td>
                            <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                            <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                            <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                            <td><?php echo round($rev_per,2).'%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr style="text-align: center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo round($tvol,0); ?></b></td>
                            <td><b><?php echo round($tsal,0); ?></b></td>
                            <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                            <td><b><?php echo round($tval,0); ?></b></td>
                            <td><b><?php echo round($tsa_total,0); ?></b></td>
                            <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                            <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                            <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                            <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                            <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                            <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>


                        </tr>
                    </tbody>
                </table>   

            <?php }else{
                echo 'Data Not found';
            }
        
        }else{  ?>
            <script>
                alert('Promotor Target Setup for All Slabs Pending ');
                
            </script>
        <?php }
    }
    
     public function ajax_get_mtd_promotor_sale_report_slab_byidzone() {
//        $from = $this->input->post('from');
//        $to = $this->input->post('to');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $idslab = $this->input->post('idslab');
        
        $slabmonth = $this->input->post('slabmonth');
        if($idslab == '0' ||  $idslab == 0){ 
            $from = $slabmonth.'-01';
            $to = date('Y-m-t', strtotime($from));   
        }else{
            $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
            $from = $slab_data->from_date;
            $to = $slab_data->to_date;
        }
        
        $sale_data = $this->Target_model->get_promotor_sale_report_slab_byidzone($from, $to, $idslab,  $idpcat, $allpcats, $idzone, $allzone);
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="MTD_Promotor_Sale_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">PROMOTER BRAND</th>
                    <th style="text-align: center">PROMOTER NAME</th>
                    <th style="text-align: center">VOLUME TARGET</th>
                    <th style="text-align: center">VOLUME ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">VALUE TARGET</th>
                    <th style="text-align: center">VALUE ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">ASP TARGET</th>
                    <th style="text-align: center">ASP ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">REVENUE TARGET</th>
                    <th style="text-align: center">REVENUE ACH</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    $num_cnt=0;
                     $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($sale_data as $sale){ 
                       if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                         if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
                        if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                        if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                        if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}

                        $salqt = $salqt - $sr_qty;
                        $saletot = $saletot - $sr_total;
                        $slanding = $slanding - $sr_landing;
                        
                        if($vol > 0){
                            $vol_ach = ($salqt / $vol)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($vall > 0){
                            $val_ach = ($saletot / $vall)*100;
                        }else{
                            $val_ach =0;
                        }
                        
                         if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        
                         $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1;?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo $vol; $tvol = $tvol + $vol; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo $vall; $tval = $tval + $vall; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot;$t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                         <td><?php echo round($asp,1); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                        <td><?php echo round($recvenue,1).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo round($tvol,0); ?></b></td>
                        <td><b><?php echo round($tsal,0); ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($tval,0); ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                        <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>
                        
                    </tr>
                </tbody>
            </table>   
            
        <?php }else{
            echo 'Data Not found';
        }
    }
    
    public function drr_promotor_sale_slab_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        
        $this->load->view('target/drr_promotor_sale_slab_report',$q);
    }
   
    public function ajax_get_drr_promotor_sale_report_slab_byidbranch(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $idslab = $this->input->post('idslab');
        
        $slabmonth = $this->input->post('slabmonth');
        
          // check all slab promotor target setup done or not
        $target_slab_data = $this->Target_model->get_slab_by_month($slabmonth);
        
        $promotor_target_slab_data = $this->Target_model->get_promotor_target_slab_data_byid($slabmonth, $idbranch, $allbranches);
        
        $slabcnt = count($target_slab_data);
              
        $pflag= 0;
        
        if($promotor_target_slab_data){
            if($idbranch == 0 ){
                $allb = explode(',',$allbranches);
            }else{
                $allb[] = $idbranch;
            }
            for($i=0; $i< count($allb); $i++){
                $ptslab_cnt = 0;
                foreach($promotor_target_slab_data as $ps){
                    if($ps->idbranch == $allb[$i]){
                        $ptslab_cnt = $ptslab_cnt + 1;
                    }
                }
                if($ptslab_cnt  != $slabcnt){
                    if($this->session->userdata('idrole') == 26){
                        $pflag = 1;
                    }else{
                        $pflag = 0; 
                    }
                    
                }
            }
        }else{
            if($this->session->userdata('idrole') == 26){
                $pflag = 1;
            }else{
                $pflag = 0; 
            }
        }
        
        if($slabmonth  != date('Y-m')){
            $pflag = 0;
        }
        
        if($pflag == 0){
        
            if($idslab == '0' ||  $idslab == 0){ 
                $from_slab = $slabmonth.'-01';
                $to_slab = date('Y-m-t', strtotime($from));    
            }else{
                $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
                $from_slab = $slab_data->from_date;
                $to_slab = $slab_data->to_date;
            }

            $first_date = $from;
            $last_date = date('d', strtotime($to_slab));
            
            //selected Date
//            $end_date = date('d', strtotime($from));
            $end_date = date('d', strtotime('-1 day', strtotime($from)));
            if(date('d', strtotime($from)) == 01){
                $remaining_days = date('d', strtotime($to_slab));
            }else{
                $remaining_days =  $last_date - $end_date;
            }
            
            $sale_data = $this->Target_model->get_drr_promotor_sale_report_slab_byidbranch($from,$from_slab,$idslab, $idpcat, $allpcats, $idbranch, $allbranches);
//            die('<pre>'.print_r($sale_data,1).'</pre>');
            if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="DRR_Promotor_Sale_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                        <th style="text-align: center">ZONE</th>
                        <th style="text-align: center">BRANCH</th>
                        <th style="text-align: center">PARTNER TYPE</th>
                        <th style="text-align: center">BRANCH CATEGORY</th>
                        <th style="text-align: center">PROMOTER BRAND</th>
                        <th style="text-align: center">PROMOTER NAME</th>
                        <th style="text-align: center">VOLUME TARGET</th>
                        <th style="text-align: center">VOLUME ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">VALUE TARGET</th>
                        <th style="text-align: center">VALUE ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">ASP TARGET</th>
                        <th style="text-align: center">ASP ACH</th>
                        <th style="text-align: center">ACH(%)</th>
                        <th style="text-align: center">REVENUE TARGET</th>
                        <th style="text-align: center">REVENUE ACH</th>
                    </thead>
                    <tbody class="data_1">
                        <?php 
                        $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                        $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                        $volume_target = 0;$value_target = 0;
                        $c_saleqty=0; $c_saletotal=0; $c_landing=0;

                        $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                        $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;

                        $num_cnt=0;
                        
                        $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                        $last_tar_vol_gap=0;$tar_vol_gap=0;
                        $last_tar_val_gap=0;$tar_val_gap=0;
                          $sr_qty = 0;$sr_total=0;$sr_landing=0;
                        foreach ($sale_data as $sale){ 
                            
                       //******Last Target Slab Value Start **************
                           
                            $last_tar_vol = $sale->last_pvolume;
                            $last_tar_val = $sale->last_pvalue;
                            
                            if( $sale->last_csale_qty > 0){ $last_sale_qty = $sale->last_csale_qty;  } else{ $last_sale_qty = 0;};
                            if( $sale->last_ctotal > 0){ $last_sale_total = round($sale->last_ctotal); } else{ $last_sale_total = 0;};
                            
                            $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                            $tar_val_gap = $last_tar_val - $last_sale_total;
                            
                            if($tar_vol_gap > 0){
                                $last_tar_vol_gap = $tar_vol_gap;
                                $last_tar_val_gap = $tar_val_gap;
                            }else{
                                $last_tar_vol_gap = 0;
                                $last_tar_val_gap = 0;
                            }
//                            echo $last_tar_vol_gap;
                            
                            if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                            if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                            if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                            if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                            if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                            if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                            if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                            if($sale->csale_qty){ $c_saleqty = $sale->csale_qty;}else{ $c_saleqty = 0; }
                            if($sale->ctotal){ $c_saletotal = $sale->ctotal;}else{ $c_saletotal = 0; }
                            if($sale->clanding){ $c_landing = $sale->clanding;}else{ $c_landing = 0; }
                            if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                            if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                            $salqt = $salqt - $sr_qty;
                            $saletot = $saletot - $sr_total;
                            $slanding = $slanding - $sr_landing;
                        
                            
                            if($remaining_days != 0){
                                $volume_target = round((($vol + $last_tar_vol_gap) - $c_saleqty)/$remaining_days,0);
                                $value_target = round((($vall + $last_tar_val_gap) - $c_saletotal)/$remaining_days,0);
                            }else{ 
                                $volume_target = 0;
                                $value_target = 0;
                            }

                            if($volume_target != 0){
                                $vol_ach = ($salqt / $volume_target)*100;
                            }else{
                                $vol_ach =0;
                            }

                            if($value_target != 0){
                                $val_ach = ($saletot / $value_target)*100;
                            }else{
                                $val_ach =0;
                            }

                            if($salqt > 0){
                                $asp_ach = ($saletot/ $salqt);
                            }else{
                                $asp_ach =0;
                            }

                            //Target Achivement Per
                            if($asp > 0){
                                $asp_ach_per = ($asp_ach/$asp)*100;
                            }else{
                                $asp_ach_per = 0;
                            }

                          //Revenue Percentage  
                            if($slanding > 0){
                                $rev_per = (($saletot - $slanding)*100)/$slanding;
                            }else{
                                $rev_per = 0;
                            }


                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                            ?>
                        <tr style="text-align: center">
                            <td><?php echo $sale->zone_name; $num_cnt = $num_cnt + 1; ?></td>
                            <td><?php echo $sale->branch_name; ?></td>
                            <td><?php echo $sale->partner_type; ?></td>
                            <td><?php echo $sale->branch_category_name; ?></td>
                            <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                            <td><?php echo $sale->user_name; ?></td>
                            <td><?php echo round($volume_target,2); $tvol = $tvol + $volume_target; ?></td>
                            <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                            <td><?php echo round($vol_ach,1).'%'; ?></td>
                            <td><?php echo round($value_target,1); $tval = $tval + $value_target; ?></td>
                            <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot ; $t_sale_landing = $t_sale_landing + $slanding;?></td>
                            <td><?php echo round($val_ach,1).'%'; ?></td>
                            <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                            <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                            <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                            <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                            <td><?php echo round($rev_per,2).'%'; ?></td>
                        </tr>
                        <?php } ?>
                        <tr style="text-align: center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo round($tvol,0); ?></b></td>
                            <td><b><?php echo round($tsal,0); ?></b></td>
                            <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                            <td><b><?php echo round($tval,0); ?></b></td>
                            <td><b><?php echo round($tsa_total,0); ?></b></td>
                            <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                            <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                            <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                            <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                            <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                            <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>

                        </tr>
                    </tbody>
                </table>   
            <?php  }
        }else{ ?>
         <script>
                alert('Promotor Target Setup for All Slabs Pending ');
            </script>
       <?php }
    }
     public function ajax_get_drr_promotor_sale_report_slab_byidzone(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
         
        $idslab = $this->input->post('idslab');
         $slabmonth = $this->input->post('slabmonth');
        if($idslab == '0' ||  $idslab == 0){ 
            $from_slab = $slabmonth.'-01';
            $to_slab = date('Y-m-t', strtotime($from));     
        }else{
            $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
            $from_slab = $slab_data->from_date;
            $to_slab = $slab_data->to_date;
        }
      
        $first_date = $from;
        $last_date = date('d', strtotime($to_slab));
        $end_date = date('d', strtotime('-1 day', strtotime($from)));
        if(date('d', strtotime($from)) == 01){
            $remaining_days = date('d', strtotime($to_slab));
        }else{
            $remaining_days =  $last_date - $end_date;
        }
        
        $sale_data = $this->Target_model->get_drr_promotor_sale_report_slab_byidzone($from,$from_slab,$idslab,$idpcat, $allpcats, $idzone, $allzone);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ ?>
                <table class="table table-bordered table-condensed text-center" id="DRR_Promotor_Sale_Report">
                    <thead style="background-color: #9dbfed" class="fixheader">
                    <th style="text-align: center">ZONE</th>
                    <th style="text-align: center">BRANCH</th>
                    <th style="text-align: center">PARTNER TYPE</th>
                    <th style="text-align: center">BRANCH CATEGORY</th>
                    <th style="text-align: center">PROMOTER BRAND</th>
                    <th style="text-align: center">PROMOTER NAME</th>
                    <th style="text-align: center">VOLUME TARGET</th>
                    <th style="text-align: center">VOLUME ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">VALUE TARGET</th>
                    <th style="text-align: center">VALUE ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">ASP TARGET</th>
                    <th style="text-align: center">ASP ACH</th>
                    <th style="text-align: center">ACH(%)</th>
                    <th style="text-align: center">REVENUE TARGET</th>
                    <th style="text-align: center">REVENUE ACH</th>
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                    $volume_target = 0;$value_target = 0;
                    $c_saleqty=0; $c_saletotal=0; $c_landing=0;
                    
                     $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    
                    $num_cnt = 0;
                    
                    $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                    $last_tar_vol_gap=0;$tar_vol_gap=0;
                    $last_tar_val_gap=0;$tar_val_gap=0;
                          $sr_qty = 0;$sr_total=0;$sr_landing=0;
                    foreach ($sale_data as $sale){ 
                        
                        //******Last Target Slab Value Start **************
                           
                            $last_tar_vol = $sale->last_pvolume;
                            $last_tar_val = $sale->last_pvalue;
                            
                            if( $sale->last_csale_qty > 0){ $last_sale_qty = $sale->last_csale_qty;  } else{ $last_sale_qty = 0;};
                            if( $sale->last_ctotal > 0){ $last_sale_total = round($sale->last_ctotal); } else{ $last_sale_total = 0;};
                            
                            $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                            $tar_val_gap = $last_tar_val - $last_sale_total;
                            
                            if($tar_vol_gap > 0){
                                $last_tar_vol_gap = $tar_vol_gap;
                                $last_tar_val_gap = $tar_val_gap;
                            }else{
                                $last_tar_vol_gap = 0;
                                $last_tar_val_gap = 0;
                            }
                           //******Last Target Slab Value END **************
                        
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                         if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                         if($sale->csale_qty){ $c_saleqty = $sale->csale_qty;}else{ $c_saleqty = 0; }
                        if($sale->ctotal){ $c_saletotal = $sale->ctotal;}else{ $c_saletotal = 0; }
                        if($sale->clanding){ $c_landing = $sale->clanding;}else{ $c_landing = 0; }
                        
                         if($sale->sale_return_qty > 0) { $sr_qty = $sale->sale_return_qty; } else{ $sr_qty = 0 ;}
                            if($sale->sreturn_total > 0){ $sr_total = $sale->sreturn_total;}else{ $sr_total = 0;}
                            if($sale->sreturn_landing > 0){ $sr_landing = $sale->sreturn_landing; } else{ $sr_landing = 0;}
                            $salqt = $salqt - $sr_qty;
                            $saletot = $saletot - $sr_total;
                            $slanding = $slanding - $sr_landing;
                        
                        
                        if($remaining_days != 0){
                            $volume_target = (($vol + $last_tar_vol_gap) - $c_saleqty)/$remaining_days;
                            $value_target = (($vall + $last_tar_val_gap) - $c_saletotal)/$remaining_days;
                        }else{
                            $volume_target = 0;
                            $value_target = 0;
                        }
                                                
                        if($volume_target != 0){
                            $vol_ach = ($salqt / $volume_target)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($value_target != 0){
                            $val_ach = ($saletot / $value_target)*100;
                        }else{
                            $val_ach =0;
                        }
                        
                        //Asp 
                         if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        
                        
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        ?>
                    <tr style="text-align: center">
                        <td><?php echo $sale->zone_name; $num_cnt = $num_cnt+1; ?></td>
                        <td><?php echo $sale->branch_name; ?></td>
                        <td><?php echo $sale->partner_type; ?></td>
                        <td><?php echo $sale->branch_category_name; ?></td>
                        <td><?php if($brand_data){echo $brand_data->brand_name;} ?></td>
                        <td><?php echo $sale->user_name; ?></td>
                        <td><?php echo  round($volume_target,0); $tvol = $tvol + $volume_target; ?></td>
                        <td><?php echo $salqt; $tsal = $tsal + $salqt; ?></td>
                        <td><?php echo round($vol_ach,1).'%'; ?></td>
                        <td><?php echo round($value_target,1); $tval = $tval + $value_target; ?></td>
                        <td><?php echo $saletot; $tsa_total = $tsa_total + $saletot;$t_sale_landing = $t_sale_landing + $slanding; ?></td>
                        <td><?php echo round($val_ach,1).'%'; ?></td>
                        <td><?php echo round($asp,0); $t_asp = $t_asp +$asp; ?></td>
                        <td><?php echo round($asp_ach,1); $t_asp_ach = $t_asp_ach + $asp_ach?></td>
                        <td><?php echo round($asp_ach_per,1).'%'; ?></td>
                        <td><?php echo round($recvenue,2).'%'; $t_rev = $t_rev + $recvenue; ?></td>
                        <td><?php echo round($rev_per,2).'%'; ?></td>
                    </tr>
                    <?php } ?>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b><?php echo round($tvol,0); ?></b></td>
                        <td><b><?php echo round($tsal,0); ?></b></td>
                        <td><b><?php if($tvol > 0){ echo round((($tsal/$tvol)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($tval,0); ?></b></td>
                        <td><b><?php echo round($tsa_total,0); ?></b></td>
                        <td><b><?php if($tval > 0){ echo round((($tsa_total/$tval)*100),1).'%'; }else{ echo '0';} ?></b></td>
                        <td><b><?php echo round($t_asp/$num_cnt,1); ?></b></td>
                        <td><b><?php $tt=0; if($tsal > 0){ $tt = $tsa_total /$tsal; }  echo round($tt,1); ?></b></td>
                        <td><b><?php if(($t_asp/$num_cnt) > 0){ echo round((($tt/($t_asp/$num_cnt))*100),1).'%'; }else{ echo '0';}?></b></td>
                        <td><b><?php echo round($t_rev/$num_cnt,2).'%';?></b></td>
                        <td><b><?php if($t_sale_landing > 0){ echo round(((($tsa_total -$t_sale_landing)*100)/$t_sale_landing),2).'%'; }else{ echo '0';}?></b></td>
                        
                    </tr>
                </tbody>
            </table>   
        <?php  }
    }
    
    public function get_target_slab_by_month(){
        $month = $this->input->post('month');
        $slabs_data = $this->Target_model->get_slab_by_month($month);
        ?>
        <b>Target Slabs</b>
        <select name="idslab" class="form-control chosen-select idslab" id="idslab">
                <option value="0">Overall Slabs</option>
                <?php foreach ($slabs_data as $slabs){  ?>
                   <option value="<?php echo $slabs->id_target_slab; ?>" slab_from="<?php echo $slabs->from_date?>" slab_to="<?php echo $slabs->to_date?>"><?php echo $slabs->slab_name;?></option>
                <?php } ?>
            </select>
        <?php 
        
    }
    public function get_target_slab_for_setup_bymonth(){
        $month = $this->input->post('month');
        $slabs_data = $this->Target_model->get_slab_by_month($month);
        ?>
       <select class="form-control input-sm" name="target_slab" id="target_slabs">
                <option value="">Select Slabs</option>
                <?php foreach ($slabs_data as $slab){ ?>
                    <option value="<?php echo $slab->id_target_slab?>"><?php echo $slab->slab_name ?></option>
                <?php } ?>
            </select>
        <?php 
        
    }
    
    public  function ajax_get_target_slab_data_bymonth(){
        $monthyear = $this->input->post('monthyear');
        $slabs_data = $this->Target_model->get_slab_by_month($monthyear);
        
        $target_per_sum = 0;
        if($slabs_data){
            foreach ($slabs_data as $sdata){
                $target_per_sum = $target_per_sum + $sdata->target_per;
            }
        }
        echo $target_per_sum;
    }
     
    
    public function target_setup_summary() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        
        $this->load->view('target/target_setup_summary',$q);
    }
    
    
    public function ajax_get_target_setup_summary_byidbranch(){
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
        $slabmonth = $this->input->post('slabmonth');
        
        $slabs_data = $this->Target_model->get_slab_by_month($slabmonth);
        
        $target_setup = $this->Target_model->check_target_setup_data($idpcat, $allpcats, $idbranch, $allbranches, $slabmonth);
//        die('<pre>'.print_r($target_setup,1).'</pre>');
        $cluster_head = $this->Target_model->get_cluster_head_data();
        if($target_setup){ ?>
            
        <table class="table table-bordered table-condensed text-center" id="target_setup_summary_report">
            <thead class="fixheader" style="background-color: #9dbfed;">
                <th style="text-align: center">Branch Name</th>
                <th style="text-align: center">Zone Name</th>
                <th style="text-align: center">Cluster Head</th>
                <?php foreach($slabs_data as $slb){ ?>
                    <th style="text-align: center"><?php echo $slb->slab_name; ?></th>
                <?php } ?>
            </thead>
            <tbody class="data_1">
                <?php foreach($target_setup as $tar){ ?>
                <tr>
                    <td><?php echo $tar->branch_name?></td>
                    <td><?php echo $tar->zone_name?></td>
                     <td><?php 
                        foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $tar->id_branch){
                                echo $clust->clust_name.', ';
                            }
                        } ?>
                     </td>
                    <?php foreach($slabs_data as $slb){ 
                    $tarcnt = 'target_cnt'.$slb->id_target_slab;
                    ?>
                    <td><?php if($tar->$tarcnt){ echo 'Yes'; }else{ echo 'No'; }; ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <?php }
        
        
    }
    public function ajax_get_target_setup_summary_byidzone(){
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
         $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        
        $slabmonth = $this->input->post('slabmonth');
        
        $slabs_data = $this->Target_model->get_slab_by_month($slabmonth);
        
        $target_setup = $this->Target_model->check_target_setup_data_byidzone($idpcat, $allpcats, $idzone, $allzone, $slabmonth);
//        die('<pre>'.print_r($target_setup,1).'</pre>');
        $cluster_head = $this->Target_model->get_cluster_head_data();
        if($target_setup){ ?>
            
        <table class="table table-bordered table-condensed text-center" id="target_setup_summary_report">
            <thead class="fixheader" style="background-color: #9dbfed;">
                <th style="text-align: center">Branch Name</th>
                <th style="text-align: center">Zone Name</th>
                <th style="text-align: center">Cluster Head</th>
                <?php foreach($slabs_data as $slb){ ?>
                    <th style="text-align: center"><?php echo $slb->slab_name; ?></th>
                <?php } ?>
            </thead>
            <tbody class="data_1">
                <?php foreach($target_setup as $tar){ ?>
                <tr>
                    <td><?php echo $tar->branch_name?></td>
                    <td><?php echo $tar->zone_name?></td>
                     <td><?php 
                        foreach ($cluster_head as $clust){
                            if($clust->clustbranch == $tar->id_branch){
                                echo $clust->clust_name.', ';
                            }
                        } ?>
                     </td>
                    <?php foreach($slabs_data as $slb){ 
                    $tarcnt = 'target_cnt'.$slb->id_target_slab;
                    ?>
                    <td><?php if($tar->$tarcnt){ echo 'Yes'; }else{ echo 'No'; }; ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <?php }
        
        
    }
    public function get_target_slab_data_bymonth(){
        $month = $this->input->post('month');
        $slabs_data = $this->Target_model->get_slab_by_month($month);
        if($slabs_data){
        ?>
       <select class="form-control input-sm" name="target_slab" id="target_slabs">
                <option value="all">Overall Slabs</option>
                <option value="0">All Slabs</option>
                <?php foreach ($slabs_data as $slab){ ?>
                    <option value="<?php echo $slab->id_target_slab?>"><?php echo $slab->slab_name ?></option>
                <?php $slabss[] = $slab->id_target_slab;  } ?>
            </select>
            <input type="hidden" name="allslabs" id="allslabs" value="<?php echo implode($slabss,',') ?>">
        <?php }else{ ?>
            <select class="form-control input-sm" name="target_slab" id="target_slabs">
                <option value="all">Overall Slabs</option>
                <option value="0">All Slabs</option>
                <?php foreach ($slabs_data as $slab){ ?>
                    <option value="<?php echo $slab->id_target_slab?>"><?php echo $slab->slab_name ?></option>
                <?php  } ?>
            </select>
        <?php }
    }
    
     
      public function promotor_target_setup_slab_report(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        
        $q['slabs_data'] = $this->Target_model->get_target_slab_data();
        $q['zone_data'] = $this->General_model->get_active_zone();
        $this->load->view('target/promotor_target_setup_report',$q);
    }
    
    public function ajax_get_promotor_target_setup_report_byidbranch(){
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $monthyear = $this->input->post('monthyear');
        $target_slabs = $this->input->post('target_slabs');
        $allslabs = $this->input->post('allslabs');
        $idpcat = $this->input->post('idpcat');
//        die(print_r($_POST));
        $promotor_data = $this->Target_model->get_promotor_target_setup_data_byfilter($idbranch, $branches, $monthyear, $target_slabs, $idpcat, $allslabs);
//        die('<pre>'.print_r($promotor_data,1).'</pre>');
        
        if($promotor_data){
            if($target_slabs == 'all'){ ?>
            
            <table class="table table-bordered table-condensed text-center" id="promotor_target_setup_report">
                    <thead style="background-color: #99ccff" class="fixheader">
                       <th style="text-align: center">Sr.</th>
                        <th style="text-align: center">Branch</th>
                        <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Promotor</th>
                        <th style="text-align: center">Brand.</th>
                        <th style="text-align: center">product Category</th>
                        <?php if($idpcat != 6){ ?>
                        <th style="text-align: center">Volume</th>
                        <th style="text-align: center">Value</th>
                        <th style="text-align: center">Asp</th>
                        <th style="text-align: center">Revenue</th>
                        <?php } else{?>
                          <th style="text-align: center">Volume</th>
                          <th style="text-align: center">Connect</th>
                        <?php } ?>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1;$tvol=0;$tval=0;$tasp=0;$tre=0;$tcon=0;
                        foreach($promotor_data as $pdata){ ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pdata->branch_name ?></td>
                            <td><?php echo $pdata->zone_name ?></td>
                            <td><?php echo $pdata->user_name ?></td>
                            <td><?php echo $pdata->brand_name ?></td>
                            <td><?php echo $pdata->product_category_name ?></td>
                             <?php if($idpcat != 6){ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->value ;$tval = $tval + $pdata->value ; ?></td>
                                <td><?php echo $pdata->asp; $tasp = $tasp + $pdata->asp; ?></td>
                                <td><?php echo $pdata->revenue ; $tre = $tre + $pdata->revenue; ?></td>

                             <?php } else{ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->connect ; $tcon = $tcon + $pdata->connect;?></td>
                             <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align: center;">
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><b>Total</b></td>
                               <?php if($idpcat != 6){ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tval; ?></b></td>
                            <td><b><?php echo $tasp; ?></b></td>
                            <td><b><?php echo $tre; ?></b></td>
                               <?php } else{ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tcon; ?></b></td>
                               <?php } ?>
                        </tr>
                    </tfoot>
                </table>
            <?php } else{ ?>
        <table class="table table-bordered table-condensed text-center" id="promotor_target_setup_report">
            <thead style="background-color: #99ccff" class="fixheader">
               <th style="text-align: center">Sr.</th>
                <th style="text-align: center">Branch</th>
                <th style="text-align: center">Zone</th>
                <th style="text-align: center">Promotor</th>
                <th style="text-align: center">Brand.</th>
                <th style="text-align: center">product Category</th>
                   <th style="text-align: center">Slab</th>
                <?php if($idpcat != 6){ ?>
                <th style="text-align: center">Volume</th>
                <th style="text-align: center">Value</th>
                <th style="text-align: center">Asp</th>
                <th style="text-align: center">Revenue</th>
                <?php } else{?>
                  <th style="text-align: center">Volume</th>
                  <th style="text-align: center">Connect</th>
                <?php } ?>
            </thead>
            <tbody class="data_1">
                <?php $sr=1;$tvol=0;$tval=0;$tasp=0;$tre=0;$tcon=0;
                foreach($promotor_data as $pdata){ ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $pdata->branch_name ?></td>
                    <td><?php echo $pdata->zone_name ?></td>
                    <td><?php echo $pdata->user_name ?></td>
                    <td><?php echo $pdata->brand_name ?></td>
                    <td><?php echo $pdata->product_category_name ?></td>
                    <td><?php echo $pdata->slab_name ?></td>
                     <?php if($idpcat != 6){ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->value ;$tval = $tval + $pdata->value ; ?></td>
                                <td><?php echo $pdata->asp; $tasp = $tasp + $pdata->asp; ?></td>
                                <td><?php echo $pdata->revenue ; $tre = $tre + $pdata->revenue; ?></td>

                             <?php } else{ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->connect ; $tcon = $tcon + $pdata->connect;?></td>
                             <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align: center;">
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><b>Total</b></td>
                               <?php if($idpcat != 6){ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tval; ?></b></td>
                            <td><b><?php echo $tasp; ?></b></td>
                            <td><b><?php echo $tre; ?></b></td>
                               <?php } else{ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tcon; ?></b></td>
                               <?php } ?>
                        </tr>
                    </tfoot>
        </table>
        
            <?php } }else{ ?>
             <script>
                 alert("Data Not Found");
             </script>
      <?php }
    }
    public function ajax_get_promotor_target_setup_report_byidzone(){
        $idzone = $this->input->post('idzone');
        $allzone = $this->input->post('allzone');
        $monthyear = $this->input->post('monthyear');
        $target_slabs = $this->input->post('target_slabs');
        $allslabs = $this->input->post('allslabs');
        $idpcat = $this->input->post('idpcat');
//        die(print_r($_POST));
        $promotor_data = $this->Target_model->get_promotor_target_setup_data_byidzone($idzone, $allzone, $monthyear, $target_slabs, $idpcat, $allslabs);
//        die('<pre>'.print_r($promotor_data,1).'</pre>');
        
        if($promotor_data){ 
            if($target_slabs == 'all'){ ?>
                <table class="table table-bordered table-condensed text-center" id="promotor_target_setup_report">
                    <thead style="background-color: #99ccff" class="fixheader">
                       <th style="text-align: center">Sr.</th>
                        <th style="text-align: center">Branch</th>
                        <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Promotor</th>
                        <th style="text-align: center">Brand.</th>
                        <th style="text-align: center">product Category</th>
                        <?php if($idpcat != 6){ ?>
                        <th style="text-align: center">Volume</th>
                        <th style="text-align: center">Value</th>
                        <th style="text-align: center">Asp</th>
                        <th style="text-align: center">Revenue</th>
                        <?php } else{?>
                          <th style="text-align: center">Volume</th>
                          <th style="text-align: center">Connect</th>
                        <?php } ?>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1;$tvol=0;$tval=0;$tasp=0;$tre=0;$tcon=0;
                        foreach($promotor_data as $pdata){ ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pdata->branch_name ?></td>
                            <td><?php echo $pdata->zone_name ?></td>
                            <td><?php echo $pdata->user_name ?></td>
                            <td><?php echo $pdata->brand_name ?></td>
                            <td><?php echo $pdata->product_category_name ?></td>
                             <?php if($idpcat != 6){ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->value ;$tval = $tval + $pdata->value ; ?></td>
                                <td><?php echo $pdata->asp; $tasp = $tasp + $pdata->asp; ?></td>
                                <td><?php echo $pdata->revenue ; $tre = $tre + $pdata->revenue; ?></td>

                             <?php } else{ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->connect ; $tcon = $tcon + $pdata->connect;?></td>
                             <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align: center;">
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><b>Total</b></td>
                               <?php if($idpcat != 6){ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tval; ?></b></td>
                            <td><b><?php echo $tasp; ?></b></td>
                            <td><b><?php echo $tre; ?></b></td>
                               <?php } else{ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tcon; ?></b></td>
                               <?php } ?>
                        </tr>
                    </tfoot>
                </table>
            <?php }else{ ?>
                <table class="table table-bordered table-condensed" id="promotor_target_setup_report">
                    <thead style="background-color: #99ccff" class="fixheader">
                        <th style="text-align: center">Sr.</th>
                        <th style="text-align: center">Branch</th>
                        <th style="text-align: center">Zone</th>
                        <th style="text-align: center">Promotor</th>
                        <th style="text-align: center">Brand.</th>
                        <th style="text-align: center">product Category</th>
                           <th style="text-align: center">Slab</th>
                        <?php if($idpcat != 6){ ?>
                        <th style="text-align: center">Volume</th>
                        <th style="text-align: center">Value</th>
                        <th style="text-align: center">Asp</th>
                        <th style="text-align: center">Revenue</th>
                        <?php } else{?>
                          <th style="text-align: center">Volume</th>
                          <th style="text-align: center">Connect</th>
                        <?php } ?>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $tvol=0;$tval=0;$tasp=0;$tre=0;$tcon=0;
                        foreach($promotor_data as $pdata){ ?>
                          <tr style="text-align: center;">
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pdata->branch_name ?></td>
                            <td><?php echo $pdata->zone_name ?></td>
                            <td><?php echo $pdata->user_name ?></td>
                            <td><?php echo $pdata->brand_name ?></td>
                            <td><?php echo $pdata->product_category_name ?></td>
                            <td><?php echo $pdata->slab_name ?></td>
                             <?php if($idpcat != 6){ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->value ;$tval = $tval + $pdata->value ; ?></td>
                                <td><?php echo $pdata->asp; $tasp = $tasp + $pdata->asp; ?></td>
                                <td><?php echo $pdata->revenue ; $tre = $tre + $pdata->revenue; ?></td>

                             <?php } else{ ?>
                                <td><?php echo $pdata->volume; $tvol = $tvol + $pdata->volume; ?></td>
                                <td><?php echo $pdata->connect ; $tcon = $tcon + $pdata->connect;?></td>
                             <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="text-align: center;">
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><?php ?></td>
                            <td><b>Total</b></td>
                               <?php if($idpcat != 6){ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tval; ?></b></td>
                            <td><b><?php echo $tasp; ?></b></td>
                            <td><b><?php echo $tre; ?></b></td>
                               <?php } else{ ?>
                            <td><b><?php echo $tvol; ?></b></td>
                            <td><b><?php echo $tcon; ?></b></td>
                               <?php } ?>
                        </tr>
                    </tfoot>
                </table>
                <?php }
            }else{ ?>
             
             <script>
                 alert("Data Not Found");
             </script>
      <?php }
    }
    
    //**********price category target setup***********************
    
     public function price_category_target_setup(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['product_category'] = $this->General_model->get_product_category_data();        
        
        $this->load->view('target/price_category_target_setup',$q);
    }
    
    public function ajax_get_price_category_setup(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        
        $price_data = $this->Report_model->get_price_category_lab_data();
        $check_data = $this->Target_model->check_get_price_category_data($idbranch, $monthyear, $idpcat);
        $target_data = $this->Target_model->get_price_category_target_data($idbranch, $monthyear, $idpcat);
//        die('<pre>'.print_r($target_data,1).'</pre>');
        if(count($check_data) > 0){ ?>
            <form>
                <table class="table table-bordered table-condensed text-center" id="price_category_target_setup">
                    <thead style="background-color: #99ccff">
                       <th style="text-align: center">Sr.</th>
                       <th style="text-align: center">Price Category</th>
                       <th style="text-align: center">Volume</th>
                       <th style="text-align: center">Value</th>
                    </thead>
                    <tbody class="data_1">
                      <?php $sr =1; $tvol=0;  $tval =0; foreach ($target_data as $pcat){ ?>
                        <tr class="tr_price">
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $pcat->lab_name; ?>
                                <input type="hidden" name="idpricecat[]" value="<?php echo $pcat->id_price_category_lab; ?>">
                                <input type="hidden" name="idpricecatsetup[]" value="<?php echo $pcat->id_price_category_setup; ?>">
                            </td>
                            <td><input type="text" class="form-control input-sm pvolume" id="pvolume" name="pvolume[]" style="text-align: center" value="<?php echo $pcat->volume; ?>">
                            <?php $tvol = $tvol + $pcat->volume; ?></td>
                            <td><input type="text" class="form-control input-sm pvalue" id="pvalue" name="pvalue[]" style="text-align: center" value="<?php echo $pcat->value; ?>">
                            <?php $tval = $tval + $pcat->value; ?></td>
                        </tr>
                      <?php } ?>
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><div class="voltotal" style="text-align:center"><?php echo $tvol; ?></div></b></td>
                            <td><b><div class="valtotal" style="text-align:center"><?php echo $tval; ?></div></b></td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="idbranch" value="<?php echo $idbranch; ?>">
                <input type="hidden" name="monthyear" value="<?php echo $monthyear; ?>">
                <input type="hidden" name="idpcat" value="<?php echo $idpcat; ?>">
               <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_price_category_setup">Update</button>
            </form>
                <script>
                $(document).ready(function (){
                    $(document).on('change', '.pvolume', function() {
                        var  total_basic_sum = 0;
                         $('.tr_price').each(function () {
                             $(this).find('.pvolume').each(function () {
                                 var total_basic = +$(this).val();
                                 if (!isNaN(total_basic) && total_basic.length !== 0) {
                                     total_basic_sum += parseFloat(total_basic);
                                 }
                             });
                             $('.voltotal').html(total_basic_sum);
                        });
                    });
                    $(document).on('change', '.pvalue', function() {
                        var  total_basic_sum = 0;
                         $('.tr_price').each(function () {
                             $(this).find('.pvalue').each(function () {
                                 var total_basic = +$(this).val();
                                 if (!isNaN(total_basic) && total_basic.length !== 0) {
                                     total_basic_sum += parseFloat(total_basic);
                                 }
                             });
                             $('.valtotal').html(total_basic_sum);
                        });
                    });
                });
            </script>
        <?php }else { 
            if($price_data){ ?>
                <form>
                    <table class="table table-bordered table-condensed text-center" id="price_category_target_setup">
                        <thead style="background-color: #99ccff">
                           <th style="text-align: center">Sr.</th>
                           <th style="text-align: center">Price Category</th>
                           <th style="text-align: center">Volume</th>
                           <th style="text-align: center">Value</th>
                        </thead>
                        <tbody class="data_1">
                          <?php $sr =1; foreach ($price_data as $pcat){ ?>
                            <tr class="tr_price"> 
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo $pcat->lab_name; ?>
                                    <input type="hidden" name="idpricecat[]" value="<?php echo $pcat->id_price_category_lab; ?>">
                                </td>
                                <td><input type="text" class="form-control input-sm pvolume" id="pvolume" name="pvolume[]"></td>
                                <td><input type="text" class="form-control input-sm pvalue" id="pvalue" name="pvalue[]"></td>
                            </tr>
                          <?php } ?>
                            <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><div class="voltotal" style="text-align:center"></div></td>
                                <td><div class="valtotal" style="text-align:center"></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="idbranch" value="<?php echo $idbranch; ?>">
                    <input type="hidden" name="monthyear" value="<?php echo $monthyear; ?>">
                    <input type="hidden" name="idpcat" value="<?php echo $idpcat; ?>">
                   <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_price_category_setup">Submit</button>
                </form>
                <script>
                $(document).ready(function (){
                    $(document).on('change', '.pvolume', function() {
                        var  total_basic_sum = 0;
                         $('.tr_price').each(function () {
                             $(this).find('.pvolume').each(function () {
                                 var total_basic = +$(this).val();
                                 if (!isNaN(total_basic) && total_basic.length !== 0) {
                                     total_basic_sum += parseFloat(total_basic);
                                 }
                             });
                             $('.voltotal').html(total_basic_sum);
                        });
                    });
                    $(document).on('change', '.pvalue', function() {
                        var  total_basic_sum = 0;
                         $('.tr_price').each(function () {
                             $(this).find('.pvalue').each(function () {
                                 var total_basic = +$(this).val();
                                 if (!isNaN(total_basic) && total_basic.length !== 0) {
                                     total_basic_sum += parseFloat(total_basic);
                                 }
                             });
                             $('.valtotal').html(total_basic_sum);
                        });
                    });
                });
            </script>
        <?php }
        }
    }
    
    public function save_price_category_setup(){
        $idpricecat = $this->input->post('idpricecat');
        $pvolume = $this->input->post('pvolume');
        $pvalue = $this->input->post('pvalue');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        
        for($i=0; $i<count($idpricecat); $i++){
            $data = array(
                'date' => date('Y-m-d'),
                'idprice_category' => $idpricecat[$i],
                'volume' => $pvolume[$i],
                'value' => $pvalue[$i],
                'idbranch' => $idbranch,
                'idproductcategory' => $idpcat,
                'monthyear' => $monthyear,
                'created_by' => $_SESSION['id_users'],
            );
            $this->Target_model->save_price_category_target($data);
        }
        $this->session->set_flashdata('save_data', 'Price Category Target Saved Successfully !');
        redirect('Target/price_category_target_setup');
    }
    
    public function update_price_category_setup(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idpricecat = $this->input->post('idpricecat');
        $pvolume = $this->input->post('pvolume');
        $pvalue = $this->input->post('pvalue');
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        $idpricecatsetup = $this->input->post('idpricecatsetup');
        
        for($i=0; $i<count($idpricecat); $i++){
            if($idpricecatsetup[$i] != ''){
                $data = array(
                    'date' => date('Y-m-d'),
                    'volume' => $pvolume[$i],
                    'value' => $pvalue[$i],
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Target_model->update_price_cat_target($data, $idpricecatsetup[$i]);
            }else{
                $data = array(
                    'date' => date('Y-m-d'),
                    'idprice_category' => $idpricecat[$i],
                    'volume' => $pvolume[$i],
                    'value' => $pvalue[$i],
                    'idbranch' => $idbranch,
                    'idproductcategory' => $idpcat,
                    'monthyear' => $monthyear,
                    'created_by' => $_SESSION['id_users'],
                );
                $this->Target_model->save_price_category_target($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Price Category Target Updated Successfully !');
        redirect('Target/price_category_target_setup');
    }
    public function price_category_target_vs_ach(){
        $q['tab_active'] = 'Target';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }
        $q['product_category'] = $this->General_model->get_product_category_data();        
        $q['godown_data'] = $this->General_model->get_active_godown_data();        
        $q['zone_data'] = $this->General_model->get_active_zone();        
        
        $this->load->view('target/price_category_target_vs_ach',$q);
    }
    public function ajax_get_price_category_target_vs_ach(){
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idpcat');
        $idgodown = $this->input->post('idgodown');
        $idzone = $this->input->post('idzone');
        $allzones = $this->input->post('allzones');
        
        $target_data = $this->Target_model->get_price_catgeory_target_vs_ach_data($idbranch, $monthyear, $idpcat, $branches, $idgodown, $idzone, $allzones);
//        die('<pre>'.print_r($target_data,1).'</pre>');
        if($target_data){
            if($idzone == 'all'){ ?>
                 <table class="table table-bordered table-condensed" id="price_category_target_vs_ach">
                <thead style="background-color: #99ccff">
                    <th>Sr.</th>
                    <th>Price Category</th>
                    <th>Volume Target</th>
                    <th>Volume Ach</th>
                    <th>Ach %</th>
                    <th>Share %</th>
                    <th>Value Target</th>
                    <th>Value Ach</th>
                    <th>Ach %</th>
                    <th>Share %</th>
                </thead>
                <tbody>
                    <?php $sr=1;$tar_volume=0; $sale_qty=0; $volach=0; $volshare=0; 
                    $tar_valume=0; $sale_amt=0; $valach=0; $valshare=0;
                    $overall_sqty =0;$overall_samount =0;
                    
                    $tot_tar_volume=0;$tot_sale_qty=0;$tvolach=0;$tvolshare=0;
                    $tot_tar_valume=0;$tot_sale_amt=0;$tvalach=0;$tvalshare=0;
                    
                    $bvol =0; $bsaleqty =0;$bvolach =0; $bvol_shar=0;
                    $bval =0; $bsaleamt =0;$bvalach =0; $bval_shar=0;
                     
                    foreach ($target_data as $target){
                        $overall_sqty = $overall_sqty +  $target->sqty;
                        $overall_samount = $overall_samount +  $target->samount;
                    }
                    
                    foreach ($target_data as $target){
                        if($target->volume){ $tar_volume = $target->volume; }else{ $tar_volume =0;}
                        if($target->value){ $tar_valume = $target->value; }else{ $tar_valume =0;}
                        if($target->sqty){ $sale_qty = $target->sqty; }else{ $sale_qty =0;}
                        if($target->samount){ $sale_amt = $target->samount; }else{ $sale_amt =0;}

                        if($tar_volume > 0){ $volach = ($sale_qty/$tar_volume)*100;}else{ $volach =0; }
                        if($tar_valume > 0){ $valach = ($sale_amt/$tar_valume)*100;}else{ $valach =0; }
                        if($overall_sqty > 0) { $volshare = ($sale_qty /$overall_sqty)*100;}else{ $volshare = 0;}
                        if($overall_samount > 0) { $valshare = ($sale_amt /$overall_samount)*100;}else{ $valshare = 0;}
                        ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $target->lab_name; ?></td>
                            <td><?php echo $tar_volume; $tot_tar_volume = $tot_tar_volume + $tar_volume; ?></td>
                            <td><?php echo $sale_qty; $tot_sale_qty = $tot_sale_qty + $sale_qty; ?></td>
                            <td><?php echo round($volach).'%'; $tvolach = $tvolach + $volach;  ?></td>
                            <td><?php echo round($volshare).'%'; $tvolshare = $tvolshare + $volshare; ?></td>
                            <td><?php echo $tar_valume; $tot_tar_valume = $tot_tar_valume + $tar_valume; ?></td>
                            <td><?php echo $sale_amt; $tot_sale_amt = $tot_sale_amt + $sale_amt; ?></td>
                            <td><?php echo round($valach).'%'; $tvalach = $tvalach + $valach;  ?></td>
                            <td><?php echo round($valshare).'%'; $tvalshare = $tvalshare + $valshare;?></td>
                         </tr>
                    <?php } ?>
                        <tr>
                            <td></td>
                            <td><b>Overall Total</b></td>
                            <td><b><?php echo $tot_tar_volume;?></b></td>
                            <td><b><?php echo $tot_sale_qty; ?></b></td>
                            <td><b><?php echo round($tvolach).'%'; ?></b></td>
                            <td><b><?php echo round($tvolshare).'%'; ?></b></td>
                            <td><b><?php echo $tot_tar_valume; ?></b></td>
                            <td><b><?php echo $tot_sale_amt; ?></b></td>
                            <td><b><?php echo round($tvalach).'%'; ?></b></td>
                            <td><b><?php echo round($tvalshare).'%'; ?></b></td>
                        </tr>
                </tbody>
            </table>
                
            <?php } elseif($idzone == 'allzone'){ ?>
                <table class="table table-bordered table-condensed" id="price_category_target_vs_ach">
                <thead style="background-color: #99ccff">
                    <th>Sr.</th>
                    <th>Zone</th>
                    <th>Price Category</th>
                    <th>Volume Target</th>
                    <th>Volume Ach</th>
                    <th>Ach %</th>
                    <th>Share %</th>
                    <th>Value Target</th>
                    <th>Value Ach</th>
                    <th>Ach %</th>
                    <th>Share %</th>
                </thead>
                <tbody>
                    <?php $sr=1;$tar_volume=0; $sale_qty=0; $volach=0; $volshare=0; 
                    $tar_valume=0; $sale_amt=0; $valach=0; $valshare=0;
                    $overall_sqty =0;$overall_samount =0;
                    
                    $tot_tar_volume=0;$tot_sale_qty=0;$tvolach=0;$tvolshare=0;
                    $tot_tar_valume=0;$tot_sale_amt=0;$tvalach=0;$tvalshare=0;
                    
                    $bvol =0; $bsaleqty =0;$bvolach =0; $bvol_shar=0;
                    $bval =0; $bsaleamt =0;$bvalach =0; $bval_shar=0;
                     
                    $old_name = $target_data[0]->id_zone;
                    foreach ($target_data as $target){
                        if($target->volume){ $tar_volume = $target->volume; }else{ $tar_volume =0;}
                        if($target->value){ $tar_valume = $target->value; }else{ $tar_valume =0;}
                        if($target->sqty){ $sale_qty = $target->sqty; }else{ $sale_qty =0;}
                        if($target->samount){ $sale_amt = $target->samount; }else{ $sale_amt =0;}

                        if($target->tsqty){ $overall_sqty = $target->tsqty; }else{ $overall_sqty =0;}
                        if($target->tsamount){ $overall_samount = $target->tsamount; }else{ $overall_samount =0;}

                        if($tar_volume > 0){ $volach = ($sale_qty/$tar_volume)*100;}else{ $volach =0; }
                        if($tar_valume > 0){ $valach = ($sale_amt/$tar_valume)*100;}else{ $valach =0; }
                        if($overall_sqty > 0) { $volshare = ($sale_qty /$overall_sqty)*100;}else{ $volshare = 0;}
                        if($overall_samount > 0) { $valshare = ($sale_amt /$overall_samount)*100;}else{ $valshare = 0;}
                        
                        //Branch Wise Total
                        if($old_name == $target->id_zone){
                            $bvol = $bvol + $tar_volume;
                            $bsaleqty = $bsaleqty + $sale_qty;
                            $bvolach = $bvolach + $volach;
                            $bvol_shar = $bvol_shar + $volshare;

                            $bval = $bval + $tar_valume;
                            $bsaleamt = $bsaleamt + $sale_amt;
                            $bvalach = $bvalach + $valach;
                            $bval_shar = $bval_shar + $valshare;
                            
                        }else{ ?>
                            <tr style="background-color: #ffffcc" >
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $bvol;?></b></td>
                                <td><b><?php echo $bsaleqty; ?></b></td>
                                <td><b><?php echo round($bvolach).'%'; ?></b></td>
                                <td><b><?php echo round($bvol_shar).'%'; ?></b></td>
                                <td><b><?php echo $bval; ?></b></td>
                                <td><b><?php echo $bsaleamt; ?></b></td>
                                <td><b><?php echo round($bvalach).'%'; ?></b></td>
                                <td><b><?php echo round($bval_shar).'%'; ?></b></td>
                            </tr>
                            <?php   
                            $bvol =0; $bsaleqty =0;$bvolach =0; $bvol_shar=0;
                            $bval =0; $bsaleamt =0;$bvalach =0; $bval_shar=0;
                            
                            $bvol = $bvol + $tar_volume;
                            $bsaleqty = $bsaleqty + $sale_qty;
                            $bvolach = $bvolach + $volach;
                            $bvol_shar = $bvol_shar + $volshare;

                            $bval = $bval + $tar_valume;
                            $bsaleamt = $bsaleamt + $sale_amt;
                            $bvalach = $bvalach + $valach;
                            $bval_shar = $bval_shar + $valshare;  
                            }       ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $target->zone_name ?></td>
                            <td><?php echo $target->lab_name; ?></td>
                            <td><?php echo $tar_volume; $tot_tar_volume = $tot_tar_volume + $tar_volume; ?></td>
                            <td><?php echo $sale_qty; $tot_sale_qty = $tot_sale_qty + $sale_qty; ?></td>
                            <td><?php echo round($volach).'%'; $tvolach = $tvolach + $volach;  ?></td>
                            <td><?php echo round($volshare).'%'; $tvolshare = $tvolshare + $volshare; ?></td>
                            <td><?php echo $tar_valume; $tot_tar_valume = $tot_tar_valume + $tar_valume; ?></td>
                            <td><?php echo $sale_amt; $tot_sale_amt = $tot_sale_amt + $sale_amt; ?></td>
                            <td><?php echo round($valach).'%'; $tvalach = $tvalach + $valach;  ?></td>
                            <td><?php echo round($valshare).'%'; $tvalshare = $tvalshare + $valshare;?></td>
                         </tr>
                    <?php $old_name = $target->id_zone; } ?>
                        <tr style="background-color: #ffffcc" >
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $bvol;?></b></td>
                            <td><b><?php echo $bsaleqty; ?></b></td>
                            <td><b><?php echo round($bvolach).'%'; ?></b></td>
                            <td><b><?php echo round($bvol_shar).'%'; ?></b></td>
                            <td><b><?php echo $bval; ?></b></td>
                            <td><b><?php echo $bsaleamt; ?></b></td>
                            <td><b><?php echo round($bvalach).'%'; ?></b></td>
                            <td><b><?php echo round($bval_shar).'%'; ?></b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Overall Total</b></td>
                            <td><b><?php echo $tot_tar_volume;?></b></td>
                            <td><b><?php echo $tot_sale_qty; ?></b></td>
                            <td><b><?php echo round($tvolach).'%'; ?></b></td>
                            <td><b><?php echo round($tvolshare).'%'; ?></b></td>
                            <td><b><?php echo $tot_tar_valume; ?></b></td>
                            <td><b><?php echo $tot_sale_amt; ?></b></td>
                            <td><b><?php echo round($tvalach).'%'; ?></b></td>
                            <td><b><?php echo round($tvalshare).'%'; ?></b></td>
                        </tr>
                </tbody>
            </table>
            <?php }else {?>
                <table class="table table-bordered table-condensed" id="price_category_target_vs_ach">
                    <thead style="background-color: #99ccff">
                        <th>Sr.</th>
                        <th>Branch</th>
                        <th>Zone</th>
                        <th>Price Category</th>
                        <th>Volume Target</th>
                        <th>Volume Ach</th>
                        <th>Ach %</th>
                        <th>Share %</th>
                        <th>Value Target</th>
                        <th>Value Ach</th>
                        <th>Ach %</th>
                        <th>Share %</th>
                    </thead>
                    <tbody>
                        <?php $sr=1;$tar_volume=0; $sale_qty=0; $volach=0; $volshare=0; 
                        $tar_valume=0; $sale_amt=0; $valach=0; $valshare=0;
                        $overall_sqty =0;$overall_samount =0;

                        $tot_tar_volume=0;$tot_sale_qty=0;$tvolach=0;$tvolshare=0;
                        $tot_tar_valume=0;$tot_sale_amt=0;$tvalach=0;$tvalshare=0;

                        $bvol =0; $bsaleqty =0;$bvolach =0; $bvol_shar=0;
                        $bval =0; $bsaleamt =0;$bvalach =0; $bval_shar=0;

                        $old_name = $target_data[0]->id_branch;
                        foreach ($target_data as $target){
                            if($target->volume){ $tar_volume = $target->volume; }else{ $tar_volume =0;}
                            if($target->value){ $tar_valume = $target->value; }else{ $tar_valume =0;}
                            if($target->sqty){ $sale_qty = $target->sqty; }else{ $sale_qty =0;}
                            if($target->samount){ $sale_amt = $target->samount; }else{ $sale_amt =0;}

                            if($target->tsqty){ $overall_sqty = $target->tsqty; }else{ $overall_sqty =0;}
                            if($target->tsamount){ $overall_samount = $target->tsamount; }else{ $overall_samount =0;}

                            if($tar_volume > 0){ $volach = ($sale_qty/$tar_volume)*100;}else{ $volach =0; }
                            if($tar_valume > 0){ $valach = ($sale_amt/$tar_valume)*100;}else{ $valach =0; }
                            if($overall_sqty > 0) { $volshare = ($sale_qty /$overall_sqty)*100;}else{ $volshare = 0;}
                            if($overall_samount > 0) { $valshare = ($sale_amt /$overall_samount)*100;}else{ $valshare = 0;}
                            //Branch Wise Total
                            if($old_name == $target->id_branch){
                                $bvol = $bvol + $tar_volume;
                                $bsaleqty = $bsaleqty + $sale_qty;
                                $bvolach = $bvolach + $volach;
                                $bvol_shar = $bvol_shar + $volshare;

                                $bval = $bval + $tar_valume;
                                $bsaleamt = $bsaleamt + $sale_amt;
                                $bvalach = $bvalach + $valach;
                                $bval_shar = $bval_shar + $valshare;

                            }else{ ?>
                                <tr style="background-color: #ffffcc" >
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Total</b></td>
                                    <td><b><?php echo $bvol;?></b></td>
                                    <td><b><?php echo $bsaleqty; ?></b></td>
                                    <td><b><?php echo $bvolach.'%'; ?></b></td>
                                    <td><b><?php echo $bvol_shar.'%'; ?></b></td>
                                    <td><b><?php echo $bval; ?></b></td>
                                    <td><b><?php echo $bsaleamt; ?></b></td>
                                    <td><b><?php echo $bvalach.'%'; ?></b></td>
                                    <td><b><?php echo $bval_shar.'%'; ?></b></td>
                                </tr>
                                <?php   
                                $bvol =0; $bsaleqty =0;$bvolach =0; $bvol_shar=0;
                                $bval =0; $bsaleamt =0;$bvalach =0; $bval_shar=0;

                                $bvol = $bvol + $tar_volume;
                                $bsaleqty = $bsaleqty + $sale_qty;
                                $bvolach = $bvolach + $volach;
                                $bvol_shar = $bvol_shar + $volshare;

                                $bval = $bval + $tar_valume;
                                $bsaleamt = $bsaleamt + $sale_amt;
                                $bvalach = $bvalach + $valach;
                                $bval_shar = $bval_shar + $valshare;                        }       ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo $target->branch_name ?></td>
                                <td><?php echo $target->zone_name ?></td>
                                <td><?php echo $target->lab_name; ?></td>
                                <td><?php echo $tar_volume; $tot_tar_volume = $tot_tar_volume + $tar_volume; ?></td>
                                <td><?php echo $sale_qty; $tot_sale_qty = $tot_sale_qty + $sale_qty; ?></td>
                                <td><?php echo round($volach).'%'; $tvolach = $tvolach + $volach;  ?></td>
                                <td><?php echo round($volshare).'%'; $tvolshare = $tvolshare + $volshare; ?></td>
                                <td><?php echo $tar_valume; $tot_tar_valume = $tot_tar_valume + $tar_valume; ?></td>
                                <td><?php echo $sale_amt; $tot_sale_amt = $tot_sale_amt + $sale_amt; ?></td>
                                <td><?php echo round($valach).'%'; $tvalach = $tvalach + $valach;  ?></td>
                                <td><?php echo round($valshare).'%'; $tvalshare = $tvalshare + $valshare;?></td>
                             </tr>
                        <?php $old_name = $target->id_branch; } ?>
                            <tr style="background-color: #ffffcc" >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $bvol;?></b></td>
                                <td><b><?php echo $bsaleqty; ?></b></td>
                                <td><b><?php echo $bvolach.'%'; ?></b></td>
                                <td><b><?php echo $bvol_shar.'%'; ?></b></td>
                                <td><b><?php echo $bval; ?></b></td>
                                <td><b><?php echo $bsaleamt; ?></b></td>
                                <td><b><?php echo $bvalach.'%'; ?></b></td>
                                <td><b><?php echo $bval_shar.'%'; ?></b></td>
                            </tr>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Overall Total</b></td>
                                <td><b><?php echo $tot_tar_volume;?></b></td>
                                <td><b><?php echo $tot_sale_qty; ?></b></td>
                                <td><b><?php echo $tvolach.'%'; ?></b></td>
                                <td><b><?php echo $tvolshare.'%'; ?></b></td>
                                <td><b><?php echo $tot_tar_valume; ?></b></td>
                                <td><b><?php echo $tot_sale_amt; ?></b></td>
                                <td><b><?php echo $tvalach.'%'; ?></b></td>
                                <td><b><?php echo $tvalshare.'%'; ?></b></td>
                            </tr>
                    </tbody>
                </table>
            <?php } }
    }
    
    
         
        //************** Allocators Target Setup ************************//

        public function allocator_target_setup(){
            $q['tab_active'] = 'Target';
            $q['allocator_data'] = $this->Target_model->get_allocators_data();
           
            $this->load->view('target/allocator_target_setup',$q);
        }
        
        public function ajax_get_allocator_branch_data(){
            $iduser = $this->input->post('idusers');
            $monthyear = $this->input->post('monthyear');
            
            $user_data = $this->Target_model->get_allocator_has_branch_byid($iduser);
            $allocation_data = $this->Target_model->get_allocation_target_data($iduser, $monthyear);
            
            $chk_all_data = $this->Target_model->check_allocation_target_data($iduser, $monthyear);
//            die('<pre>'.print_r($allocation_data,1).'</pre>');
            if($chk_all_data){ ?>
                <form>
                    <table class="table table-bordered table-condensed text-center" id="allocator_target">
                        <thead style="background-color: #9dbfed" >
                            <th style="text-align: center">Sr.</th>
                            <th style="text-align: center">Branch</th>
                            <th style="text-align: center">Zone</th>
                            <th style="text-align: center">Volume</th>
                        </thead>
                        <tbody class="data_1">
                            <?php $i=1; $tqty =0; foreach($allocation_data as $udata){?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $udata->branch_name; ?></td>
                                    <td><?php echo $udata->zone_name; ?></td>
                                    <td><input type="text" style="text-align: center" class="form-control qty" id="qty" name="qty[]" value="<?php echo $udata->qty ?>">
                                        <input type="hidden" class="form-control" id="idbranch" name="idbranch[]" value="<?php echo $udata->id_branch?> ">
                                        <input type="hidden" class="form-control" id="idallocator" name="idallocator[]" value="<?php echo $udata->id_allocator_target?> ">
                                        <?php $tqty = $tqty + $udata->qty;  ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot >
                            <th></th>
                            <th></th>
                            <th style="text-align: center">TOTAL</th>
                            <th style="text-align: center"><div id="overalltotal"><?php echo $tqty; ?></div></th>
                        </tfoot>
                    </table>
                    <div>
                        <input type="hidden" name="iduser" value="<?php echo $iduser; ?>">
                        <input type="hidden" name="month" value="<?php echo $monthyear; ?>">
                        <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_allocator_target">Update</button>
                    </div>
                </form>
            <?php }else{  
                if($user_data){ ?>
                    <form>
                        <table class="table table-bordered table-condensed text-center" id="allocator_target">
                            <thead style="background-color: #9dbfed">
                                <th style="text-align: center">Sr.</th>
                                <th style="text-align: center">Branch</th>
                                <th style="text-align: center">Zone</th>
                                <th style="text-align: center">Volume</th>
                            </thead>
                            <tbody class="data_1">
                                <?php $i=1; foreach($user_data as $udata){?>
                                    <tr style="text-align: center">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $udata->branch_name; ?></td>
                                        <td><?php echo $udata->zone_name; ?></td>
                                        <td><input type="text" style="text-align: center" class="form-control qty" id="qty" name="qty[]">
                                            <input type="hidden" class="form-control" id="idbranch" name="idbranch[]" value="<?php echo $udata->id_branch?> ">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot >
                                <th></th>
                                <th></th>
                                <th style="text-align: center">TOTAL</th>
                                <th style="text-align: center"><div id="overalltotal"></div></th>
                            </tfoot>
                        </table>
                                                <div>
                            <input type="hidden" name="iduser" value="<?php echo $iduser; ?>">
                            <input type="hidden" name="month" value="<?php echo $monthyear; ?>">
                            <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_allocator_target">Submit</button>
                        </div>
                    </form>
                <?php }
                }
            ?>
            <script>
                $(document).on('change', 'input[id=qty]', function() {
                    var  total_basic_sum = 0;
                     $('tr').each(function () {
                         $(this).find('.qty').each(function () {
                             var total_basic = $(this).val();
                             if (!isNaN(total_basic) && total_basic.length !== 0) {
                                 total_basic_sum += parseFloat(total_basic);
                             }
                         });
                         $('#overalltotal').html(total_basic_sum);
                    });
                 });
            </script>
            <?php 
        }
        
        public function save_allocator_target(){
//            die('<pre>'.print_r($_POST,1).'</pre>');
            
            $month_year = $this->input->post('month');
            $iduser = $this->input->post('iduser');
            $idbranch = $this->input->post('idbranch');
            $qty = $this->input->post('qty');
            
            for($i=0; $i < count($idbranch); $i++){
                $data[] = array(
                   'date' => date('Y-m-d'),
                   'month_year' => $month_year,
                   'iduser' => $iduser,
                   'idbranch' => $idbranch[$i],
                   'qty' => $qty[$i],
                   'created_by' => $_SESSION['id_users'], 
                );
            }
            if(count($data) > 0){
               $this->Target_model->save_allocatore_target_data($data);
            }
            
            $this->session->set_flashdata('save_data', 'Allocator Target Save Successfully!');
            redirect('Target/allocator_target_setup');
            
        }
    
        public function update_allocator_target(){
//            die('<pre>'.print_r($_POST,1).'</pre>');
            $qty = $this->input->post('qty');
            $idbranch = $this->input->post('idbranch');
            $idallocator = $this->input->post('idallocator');
            $month_year = $this->input->post('month');
            $iduser = $this->input->post('iduser');
            
            
            for($i=0; $i < count($idbranch); $i++){
                if($idallocator[$i] != ''){
                    $data = array(
                        'date' => date('Y-m-d'),
                        'qty' => $qty[$i],
                        'created_by' => $_SESSION['id_users'], 
                    );
                    $this->Target_model->update_allocatior_target_data($data, $idallocator[$i]);
                }else{
                    $all_data[] = array(
                       'date' => date('Y-m-d'),
                       'month_year' => $month_year,
                       'iduser' => $iduser,
                       'idbranch' => $idbranch[$i],
                       'qty' => $qty[$i],
                       'created_by' => $_SESSION['id_users'], 
                    );
                }
            }
            if(count($all_data) > 0){
               $this->Target_model->save_allocatore_target_data($all_data);
            }
            
            $this->session->set_flashdata('save_data', 'Allocator Target Updated Successfully!');
            redirect('Target/allocator_target_setup');
            
        }
        public function allocator_target_setup_report(){
            $q['tab_active'] = 'Target';
            $q['allocator_data'] = $this->Target_model->get_allocators_data();
           
            $this->load->view('target/allocator_target_setup_report',$q);
        }
        public function ajax_get_allocator_target_vs_ach_report(){
            $iduser = $this->input->post('idusers');
            $monthyear = $this->input->post('monthyear');
            
            $allocation_data = $this->Target_model->get_allocation_target_vs_ach_data($iduser, $monthyear);
            if($allocation_data){ ?>
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #99ccff" class="fixheader">
                    <th style="text-align: center">Sr.</th>
                    <th style="text-align: center">Zone</th>
                    <th style="text-align: center">Branch Category</th>
                    <th style="text-align: center"> Branch</th>
                    <th style="text-align: center">Allocator</th>
                    <th style="text-align: center">Allocation Target</th>
                    <th style="text-align: center">Allocation Ach.</th>
                    <th style="text-align: center">Gap</th>
                    <th style="text-align: center">Ach %</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1;$tar_qty=0;$allc_qty=0; $gap =0;$ach=0;
                    $tot_tar =0; $tot_allqty=0;$tot_gap=0;$tot_ach=0;
                    $z_tar =0; $z_allqty=0;$z_gap=0;$z_ach=0;
                     $idzone = $allocation_data[0]->id_zone;
                    foreach($allocation_data as $allc){ 
                        if($allc->qty){ $tar_qty = $allc->qty; }else{ $tar_qty = 0; }
                        if($allc->allocated_qty){ $allc_qty = $allc->allocated_qty; }else{ $allc_qty = 0; }
                        $gap = $tar_qty - $allc_qty;
                        if($tar_qty > 0){ $ach = ($allc_qty / $tar_qty)*100;}else{ $ach=0;}     
                        if($idzone == $allc->id_zone){
                            $z_tar = $z_tar + $tar_qty;
                            $z_allqty = $z_allqty + $allc_qty;
                            $z_gap = $z_tar - $z_allqty;
                            if($z_tar > 0){ $z_ach = ($z_allqty / $z_tar)*100;}else{ $z_ach=0;}     
                            
                        }else{ ?>
                            <tr style="background-color: #ffffcc;text-align: center" >
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>TOTAL<b></td>
                                <td><b><?php echo $z_tar; ?></b></td>
                                <td><b><?php echo $z_allqty; ?></b></td>
                                <td><b><?php echo $z_gap; ?></b></td>
                                <td><b><?php echo round($z_ach,2).'%'; ?></b></td>
                            </tr>
                    
                        <?php
                          $z_tar =0; $z_allqty=0;$z_gap=0;$z_ach=0;
                           $z_tar = $z_tar + $tar_qty;
                            $z_allqty = $z_allqty + $allc_qty;
                            $z_gap = $z_tar - $z_allqty;
                            if($z_tar > 0){ $z_ach = ($z_allqty / $z_tar)*100;}else{ $z_ach=0;}      
                        }
                        ?>
                        <tr style="text-align: center">
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $allc->zone_name ?></td>
                            <td><?php echo $allc->branch_category_name ?></td>
                            <td><?php echo $allc->branch_name ?></td>
                            <td><?php echo $allc->user_name ?></td>
                            <td><?php echo $tar_qty ; $tot_tar = $tot_tar + $tar_qty; ?></td>
                            <td><?php echo $allc_qty; $tot_allqty = $tot_allqty + $allc_qty; ?></td>
                            <td><?php echo $gap ?></td>
                            <td><?php echo round($ach,2).'%' ?></td>
                        </tr>
                    <?php $idzone = $allc->id_zone; } 
                    $tot_gap = $tot_tar - $tot_allqty;
                    if($tot_tar > 0){ $tot_ach = ($tot_allqty / $tot_tar)*100;}else{ $tot_ach = 0;}     
                    
                    ?>
                   <tr style="background-color: #ffffcc;text-align: center" >
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>TOTAL<b></td>
                        <td><b><?php echo $z_tar; ?></b></td>
                        <td><b><?php echo $z_allqty; ?></b></td>
                        <td><b><?php echo $z_gap; ?></b></td>
                        <td><b><?php echo round($z_ach,2).'%'; ?></b></td>
                    </tr>
                    <tr style="text-align: center">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>OVERALL TOTAL<b></td>
                        <td><b><?php echo $tot_tar; ?></b></td>
                        <td><b><?php echo $tot_allqty; ?></b></td>
                        <td><b><?php echo $tot_gap; ?></b></td>
                        <td><b><?php echo round($tot_allqty,2),'%'; ?></b></td>
                    </tr>
                </tbody>
            </table>
            <?php }
        }
}