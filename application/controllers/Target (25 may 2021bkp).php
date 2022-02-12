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
        
        $target_data = $this->Target_model->ajax_get_branch_target_data_byidzone($idzone, $lastmonthyear);
        $current_target_data = $this->Target_model->ajax_get_current_month_branch_target_data_byidzone($idzone, $monthyear);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
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
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px"  name="volume_target[]" class="form-control volumetarget<?php echo $target->id_branch?>" id="voltarget"></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px"  name="value_target[]" class="form-control valuetarget<?php echo $target->id_branch?>" id="valtarget" ></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px"  name="asp_target[]" class="form-control asptarget<?php echo $target->id_branch?>" id="asptarget" ></td>
                                    <td style="border-left: 1px solid #cccccc;"><input type="text" style="width:120px"  name="revenue_target[]" class="form-control revenuetarget<?php echo $target->id_branch?>" id="revtarget" ></td>
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
                            
                            foreach ($current_target_data as $cur_target){
                                if($cur_target->idbranch == $target->id_branch && $cur_target->idproductcategory == $target->id_product_category){
                                    $cu_volume = $cur_target->volume;
                                    $cu_value = $cur_target->value;
                                    $cu_asp = $cur_target->asp;
                                    $cu_rev = $cur_target->revenue;
                                    
                                    $idbranch_tareget = $cur_target->id_branch_target;
                                }
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
                                <td><input type="text" style="width:120px"  name="volume_target[]" class="form-control volumetarget<?php echo $target->id_branch?>" id="voltarget" <?php if($cu_volume > 0){?> readonly <?php } ?> value="<?php echo round($cu_volume); ?>"></td>
                                    <td><input type="text" style="width:120px"  name="value_target[]" class="form-control valuetarget<?php echo $target->id_branch?>" id="valtarget" <?php if($cu_value > 0){?> readonly <?php } ?>  value="<?php echo round($cu_value); ?>"></td>
                                    <td><input type="text" style="width:120px"   name="asp_target[]" class="form-control asptarget<?php echo $target->id_branch?>" id="asptarget" <?php if($cu_asp > 0){?> readonly <?php } ?> value="<?php echo round($cu_asp); ?>"></td>
                                    <td><input type="text" style="width:120px"  name="revenue_target[]" class="form-control revenuetarget<?php echo $target->id_branch?>" id="revtarget" <?php if($cu_rev > 0){?> readonly <?php } ?> value="<?php echo number_format($cu_rev,2); ?>">
                                        <input type="hidden" style="width:120px"  name="idbranch_target[]"  value="<?php echo $idbranch_tareget?>">
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
                   <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_branch_target_data" style="margin-right: 20px;">Update</button>
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
        
        for($i=0; $i<count($idbranch); $i++){
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
            $this->Target_model->save_branch_target($data);
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
//        die('<pre>'.print_r($_POST,1).'</pre>');        
        for($i=0; $i<count($idbranch); $i++){
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
//                                                       
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
                           </div>
                            <div class="clearfix"></div><br>
                            <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/save_promotor_target_data">Submit</button>
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
    
     //****************MTD Asceivement Report*****************
    
    public function mtd_acheivement_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
    }
    
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
                               <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
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
    
    //Drr Achivement Report
    
    public function drr_acheivement_report() {
        $q['tab_active'] = 'Target';
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }
        $this->load->view('target/drr_acheivement_report',$q);
    }
    public function ajax_get_drr_achivement_byidbranch(){
        $from = $this->input->post('from');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        
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
        
        
        $target_data = $this->Target_model->ajax_get_drr_achivement_byidbranch($from,$idpcat,$allpcats,$idbranch,$allbranches);
        $cluster_head = $this->Target_model->get_cluster_head_data();
//        die('<pre>'.print_r($target_data,1).'</pre>');
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
    public function ajax_get_drr_achivement_byidzone(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
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
                           <tr>
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
                               <td><?php echo $tar_asp; $total_asp_target = $total_asp_target + $tar_asp;  ?></td>
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
    
    
    //Promotor Target Sale Report
    
      public function mtd_promotor_sale_report() {
         $q['tab_active'] = 'Target';
         $q['zone_data'] = $this->General_model->get_active_zone();
        $q['product_cat_data'] = $this->Target_model->get_product_category_data();
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
                    foreach ($sale_data as $sale){ 
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
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
                    foreach ($sale_data as $sale){ 
                       if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                         if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
        
        $target_data = $this->Target_model->ajax_get_branch_target_data_byidzone($idzone, $lastmonthyear);
        $current_target_data = $this->Target_model->ajax_get_current_month_branch_target_data_byidzone($idzone, $monthyear);
        $cluster_head = $this->Target_model->get_cluster_head_data();
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

                                    foreach ($current_target_data as $cur_target){
                                        if($cur_target->idbranch == $target->id_branch && $cur_target->idproductcategory == $target->id_product_category){
                                            $cu_volume = $cur_target->volume;
                                            $cu_value = $cur_target->value;
                                            $cu_asp = $cur_target->asp;
                                            $cu_rev = $cur_target->revenue;

                                            $idbranch_tareget = $cur_target->id_branch_target;
                                        }
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
                       <button class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Target/update_branch_target_data" style="margin-right: 20px;">Update</button>
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            <table class="table table-bordered table-condensed text-center" id="LMTD_branch_sale_report">
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0; $t_smart_phone=0;$t_finance=0;$t_rudram=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;$rev_ach_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    
                    $smart_phone=0;$finance_qty=0;$rud_qty;$fin_conn=0;$rud_conn=0; $num_cnt=0;
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
                        }else{
                            $fin_conn = 0;
                            $rud_conn =0;
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
                </thead>
                <tbody class="data_1">
                    <?php 
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0; $t_smart_phone=0;$t_finance=0;$t_rudram=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;$rev_ach_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    
                    $smart_phone=0;$finance_qty=0;$rud_qty;$fin_conn=0;$rud_conn=0;$num_cnt=0;
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
                        }else{
                            $fin_conn = 0;
                            $rud_conn =0;
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
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
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
  
}