<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incentive_policy extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Incentive_model');
        $this->load->model('General_model');
        $this->load->model('Stock_model');
    }
    
    //***************Branch Target Setup*****************
    public function incentive_policy_setup() {
        $q['tab_active'] = 'Target';
        $q['product_category_data'] = $this->Incentive_model->product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['policy_data'] = $this->Incentive_model->get_incentive_policy_data();
        $this->load->view('incentive_policy/policy_setup',$q);
    }
    public function ajax_get_category_bypidcat(){
        $idpcat = $this->input->post('idpcat');
        $cat_data = $this->Incentive_model->get_category_byidpcat($idpcat);
        if(count($cat_data) > 0){ 
            echo '<select class="chosen-select form-control idcat" name="idcat" id="idcat" required=""><option value="">Select Model</option>';
            foreach ($cat_data as $cdata) { 
                echo '<option  value="'.$cdata->id_category .'">'.$cdata->category_name.'</option>';
            } 
        }else { 
            echo '<select name="idcat" id="idcat" class="form-control input-sm idcat">
                <option value="">Select Category</option>
            </select>';
        } 
    }
    
    public function ajax_get_brand_bypidcat(){
        $idpcat = $this->input->post('idpcat');
          $brand_data = $this->General_model->get_active_brand_data();
        if($idpcat == '8'){
            echo '<select name="idbrand" id="idbrand" class="form-control input-sm idbrand" >
                    <option value="">Select brand</option>
                </select>';
        }else{
            echo '<select name="idbrand" id="idbrand" class="form-control input-sm idbrand" >
                    <option value="">Select brand</option>';
                foreach ($brand_data as $bdata){
                    echo '<option value="'.$bdata->id_brand.'">'.$bdata->brand_name.'</option>';
                    }
                '</select>';
        } ?>
        <script>
            $(document).ready(function (){
            $('#idbrand').change(function (){
                var idbrand = $('#idbrand') .val();
                var idpcat = $('#idpcat') .val();
                var idcat = $('#idcat') .val();

                if(idbrand != '' && idpcat != ''){
                   $.ajax({
                        url:"<?php echo base_url() ?>Incentive_policy/ajax_get_model_byidbrand",
                        method:"POST",
                        data:{ idbrand: idbrand, idpcat: idpcat, idcat: idcat},
                        success:function(data)
                        {
                            $('.modeldata').html(data);
                            if(idpcat == '1'){
                                 $('.idmodel').removeAttr('multiple');
                               $(".chosen-select").chosen({ search_contains: false });
                            }else{
                                
                                $(".chosen-select").chosen({ search_contains: true });
                            }
                        }
                    });
                }else{
                    alert("Select Data Properly ! ");
                    return false;
                }
            });
        });
        </script>
          <?php   
        
    }
    public function ajax_get_model_bypidcat(){
        echo '<select name="idmodel[]" id="idmodel" class="form-control input-sm chosen-select">
                <option value="">Select Model</option>
            </select>';
    }
    public function ajax_get_model_byidbrand(){
        
        $idbrand = $this->input->post('idbrand');
        $idpcat = $this->input->post('idpcat');
        $idcat = $this->input->post('idcat');
        
        $model_data = $this->Incentive_model->get_model_variants_byidbrand($idpcat, $idbrand, $idcat);

        if(count($model_data) > 0){ 
            echo '<select class="chosen-select form-control idmodel" name="idmodel[]" multiple id="idmodel" ><option value="">Select Model</option>';
            foreach ($model_data as $mdata) { 
                echo '<option  value="'.$mdata->id_variant .'">'.$mdata->full_name.'</option>';
            } 
        }else { 
            echo '<select name="idmodel[]" id="idmodel" class="form-control input-sm chosen-select">
                <option value="">Select Model</option>
            </select>';
        }
        
    }
    
    public function save_incentive_policy(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $monthyear = $this->input->post('monthyear');
        $policy_name = $this->input->post('policy_name');
        $policy_type = $this->input->post('idtype');
        $idproductcat = $this->input->post('idpcat');
        $idcategory = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idvariant = $this->input->post('idmodel');
        $incent_cal_type = $this->input->post('idincnt');
        $slab_name = $this->input->post('slab_name');
        $slab_min = $this->input->post('conn_min');
        $slab_max = $this->input->post('conn_max');
        $slab_per = $this->input->post('conn_per');
        
        if(!$idbrand){
            $idbrand = NULL;
        }
        $date = date('Y-m-d');
        if($idproductcat == 1){ //For Mobile Product Catgeory
            if($idvariant[0] != ''){
                $model_data = $this->Incentive_model->get_idmodel_byvariant($idvariant[0]);
//                die(print_r($model_data->idmodel));
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    //get model variants from selected model
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($model_data->idmodel,$idcategory);
                }else{
                    //Get color variants of selected catgeory & idvariant
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant[0],$idcategory);
                }
                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }
            }else{
                $idva =  $idvariant;
            }
        }else{
           $idva =  $idvariant;
        }
        
        // TV, Protextion Plan, Software productcategory & model is null
        $inct_policy = array(
            'policy_name' => $policy_name,
            'policy_type' => $policy_type,
            'month_year' => $monthyear,
            'date' => $date,
            'created_by' => $_SESSION['id_users'],
            'idproductcat' => $idproductcat,
            'cal_type' => $incent_cal_type,
        );
        if($id_inct_policy = $this->Incentive_model->save_incentive_policy($inct_policy)){
            for($m=0; $m<count($idva); $m++){
                if($idva[$m] == ''){
                    $idva[$m] = NULL;
                }
                $policy_data[] = array(
                    'idpolicy' => $id_inct_policy,
                    'idproductcat' => $idproductcat,
                    'idcategory' => $idcategory,
                    'idbrand' => $idbrand,
                    'idmodel' => $idva[$m],
                    'created_by' => $_SESSION['id_users']
                );
            }
//                die(print_r($policy_data));
            if(count($policy_data) > 0){
                $this->Incentive_model->save_incentive_model_data($policy_data);
            }

            for($i=0; $i<count($slab_name); $i++){
                $policy_slabs[] = array(
                    'idpolicy' => $id_inct_policy,
                    'slab_name' => $slab_name[$i],
                    'min_slab' => $slab_min[$i],
                    'max_slab' => $slab_max[$i],
                    'slab_per' => $slab_per[$i],
                );
            }
            if(count($policy_slabs) > 0){
                $this->Incentive_model->save_incentive_slabs_data($policy_slabs);
            }

            $this->session->set_flashdata('save_data', 'Incentive Policy Save Successfully');
            redirect('Incentive_policy/incentive_policy_setup');
        }
    }
    
    public function ajax_get_policy_data(){
        $month = $this->input->post('month');
        $idpcat = $this->input->post('pdprodcat');
        
        $policy_data = $this->Incentive_model->get_incentive_policy_data_bymonth($month, $idpcat);
//        die('<pre>'.print_r($policy_data,1).'</pre>');
        if($policy_data){ ?>
            <table class="table table-bordered table-condensed" id="incentive_policy_data">
                <thead style="background-color: #99ccff">
                    <th>Sr.</th>
                    <th>Month</th>
                    <th>Policy Name</th>
                    <th>Policy Type</th>
                    <th>Product Category</th>
                    <th>Info</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($policy_data as $pdata){ ?>
                    <tr class="trdata">
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $pdata->month_year; ?></td>
                        <td><?php echo $pdata->policy_name; ?></td>
                        <td><?php if($pdata->policy_type == 0){ echo 'Volume Connect'; }elseif($pdata->policy_type == 1){ echo 'Value Connect'; }elseif($pdata->policy_type == 2){ echo 'Qty Connect'; } ?></td>
                        <td><?php echo $pdata->product_category_name; ?></td>
                        <td><a target="_blank" href="<?php echo base_url()?>Incentive_policy/Incentive_policy_details/<?php echo $pdata->id_incentive_policy?>" class="btn btn-primary btn-floating"><span class="fa fa-info"></span></a></td>
                        <td><input type="hidden" class="idpolicy" name="idpolicy" id="idpolicy" value="<?php echo $pdata->id_incentive_policy; ?>">
                            <button class="btn btn-warning btn-floating btndelete" id="btndelete"><span class="fa fa-trash"></span></button></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <script>
            $(document).ready(function (){
               $('.btndelete').click(function (){
                   var idpolicy = $(this).closest('td').find('.idpolicy').val();
                   var parent = $(this).closest('td').parent('.trdata');
                   if(confirm("Do You Want To Delete Incentive Policy?")){
                        $.ajax({
                            url:"<?php echo base_url() ?>Incentive_policy/delete_incentive_policy",
                            method:"POST",
                            data:{idpolicy: idpolicy},
                            success:function(data)
                            {
                                if(data == '1' || data == 1){
                                    parent.remove();
                                    alert("Incentive Policy Deleted Successfully!...")
                                }else{
                                    alert("Failed to Delete Policy!...");
                                    return false;
                                }
                            }
                        });
                    }else{
                        return false;
                    }
               });
            });
        </script>
        <?php }
    }
    public function Incentive_policy_details($idpolicy){
        $q['tab_active'] = 'Target';
        $q['inc_policy'] = $this->Incentive_model->get_incentive_policy_data_byid($idpolicy);
        $q['policy_details'] = $this->Incentive_model->get_incentive_policy_details($idpolicy);
        $q['slab_details'] = $this->Incentive_model->get_incentive_policy_slabs_details($idpolicy);
//        die('<pre>'.print_r($q,1).'</pre>');
        $this->load->view('incentive_policy/policy_details',$q);
    }
    public function delete_incentive_policy(){
       $idpolicy = $this->input->post('idpolicy');
       $res = 0;
       if($this->Incentive_model->delete_policy_data($idpolicy)){
          $this->Incentive_model->delete_policy_model_data($idpolicy);
          $this->Incentive_model->delete_policy_slab_data($idpolicy);
          $res = 1;
       }else{
           $res = 0;
       }
       echo $res;
    }
    
    public function Incentive_policy_promotor_report(){
        $q['tab_active'] = 'Target';
        $q['product_category_data'] = $this->Incentive_model->product_category_data();
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
            
        }
        $q['zone_data'] = $this->General_model->get_zone_data();
        $this->load->view('incentive_policy/promotor_policy_report',$q);
    }
    public function ajax_get_promotor_policy_report(){
        $month = $this->input->post('month');
        $idpcat = $this->input->post('idpcat');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $idzone = $this->input->post('idzone');
//        die(print_r($_POST));
        $policy_data = $this->Incentive_model->get_promotor_policy_data($month, $idpcat, $idbranch, $allbranches, $idzone);
//        die('<pre>'.print_r($policy_data,1).'</pre>');
        if($policy_data){ ?>
        <table class="table table-bordered text-center" id="promotor_incentive_policy">
            <thead style="background-color: #99ccff" class="fixheader" >
                <th>Sr.</th>
                <th>Branch</th>
                <th>Zone</th>
                <th>Branch Category</th>
                <th>Promotor</th>
                <th>Policy</th>
                <th>Policy type</th>
                <th>Calculation type</th>
                <th style="text-align: center">Smart Phone Volume/Value</th>
                <th style="text-align: center">Volume Ach/Value Ach</th>
                <th style="text-align: center">Conn %</th>
                <th style="text-align: center"> Slab Name </th>
                <th style="text-align: center"> Incentive </th>
                <th style="text-align: center"> Total Incentive </th>
            </thead>
            <tbody class="data_1">
                    <?php $sr=1; $smartvol=0; $smartval=0;$saleamt=0; $volconn=0; $valconn=0; $slbper=0;$slab_name='';$inc_amount=0; $rsaleqty = 0;$rsalemt=0;
                    foreach ($policy_data as $pdata){ 
                        if($pdata->smart_qty){ $smartvol = $pdata->smart_qty; }else{ $smartvol = 0; }
                        if($pdata->smart_amount){ $smartval = $pdata->smart_amount; }else{ $smartval = 0; }
                        //Sales return
                        if($pdata->rsaleqty){ $rsaleqty = $pdata->rsaleqty; }else{ $rsaleqty = 0; }
                        if($pdata->rsale_amt){ $rsalemt = $pdata->rsale_amt; }else{ $rsalemt = 0; }
                        //Sale 
                        if($pdata->saleqty){ $saleqty = $pdata->saleqty - $rsaleqty; }else{ $saleqty = 0; }
                        if($pdata->sale_amt){ $saleamt = $pdata->sale_amt - $rsalemt; }else{ $saleamt = 0; }
                        if($smartvol > 0){ $volconn = ($saleqty/$smartvol)*100; }else{ $volconn =0;}
                        if($smartval > 0){ $valconn = ($saleamt/$smartval)*100; }else{ $valconn =0;}
                        
                        $policy_slabs = $this->Incentive_model->get_policy_slabs_data_byidpolicy($pdata->id_incentive_policy);
                        if($policy_slabs){
                            foreach ($policy_slabs as $pslab){
                                if($pdata->policy_type == 0){
                                    //Volume Connect
                                    if($saleqty >= $pslab->min_slab && $saleqty<= $pslab->max_slab){
                                        $slbper = $pslab->slab_per;
                                        $slab_name = $pslab->slab_name;
                                    }
                                    if($pdata->cal_type  == 0){
                                        //percentage cal
                                        $inc_amount = ($slbper*$saleqty)/100;
                                    }else{
                                        //amount cal
                                        $inc_amount = ($slbper*$saleqty);
                                    }
                                }elseif($pdata->policy_type == 1){
                                    //Value Connect
                                    if($saleamt >= $pslab->min_slab && $saleamt<= $pslab->max_slab){
                                        $slbper = $pslab->slab_per;
                                        $slab_name = $pslab->slab_name;
                                    }
                                    if($pdata->cal_type  == 0){
                                        //percentage cal
                                        $inc_amount = ($slbper*$saleamt)/100;
                                    }else{
                                        //amount cal
                                        $inc_amount = ($slbper);
                                    }
                                }
                                elseif($pdata->policy_type == 2){
                                    //Qty Connect
                                    $slbper = $pslab->slab_per;
                                    $slab_name = $pslab->slab_name;
                                    $inc_amount = ($slbper*$saleqty);
                                } 
                            }
                        }
                        ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $pdata->branch_name; ?></td>
                        <td><?php echo $pdata->zone_name; ?></td>
                        <td><?php echo $pdata->branch_category_name; ?></td>
                        <td><?php echo $pdata->user_name; ?></td>
                        <td><?php echo $pdata->policy_name; ?></td>
                        <td><?php if($pdata->policy_type==0){echo 'Volume Connect';}elseif($pdata->policy_type==1){echo 'Value Connect';}else{ echo 'Qty Connect';} ?></td>
                        <td><?php if($pdata->cal_type==0){echo 'Percentage Calculate';}else{ echo 'Amount Calculation';} ?></td>
                        <td><?php if($pdata->policy_type==0 || $pdata->policy_type==2 ){ echo $smartvol; } else{ echo $smartval; } ?></td>
                        <td><?php if($pdata->policy_type==0 || $pdata->policy_type==2 ){ echo $saleqty; }else{ echo $saleamt; } ?></td>
                        <td><?php if($pdata->policy_type==0 || $pdata->policy_type==2 ){ echo round($volconn,2).'%'; }else{ echo round($valconn,2).'%'; } ?></td>
                        <td><?php echo $slab_name; ?></td>
                        <td><?php  if($pdata->cal_type == 0){ echo $slbper.'%';}else{  echo $slbper.' Rs'; } ?></td>
                        <td><?php echo round($inc_amount); ?></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        <?php }
    }
    
     public function Incentive_policy_report(){
        $q['tab_active'] = 'Target';
        $q['product_category_data'] = $this->Incentive_model->product_category_data();
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('incentive_policy/incentive_policy_report',$q);
    }
    public function ajax_get_incentive_report(){
        $month = $this->input->post('month');
        $idpcat = $this->input->post('idpcat');
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
        $policy_data = $this->Incentive_model->get_branch_incentive_report_data($month,$idpcat,$idbranch,$allbranches);
//        die('<pre>'.print_r($policy_data,1).'</pre>');
        if($policy_data){ ?>
            <table class="table table-bordered text-center" id="branch_incentive_report">
                <thead style="background-color: #99ccff" class="fixheader">  
                    <th style="text-align: center">Zone</th>
                    <th style="text-align: center">Branch Category</th>
                    <th style="text-align: center">Branch</th>
                    <th style="text-align: center">Policy Name</th>
                    <th style="text-align: center">Policy Type</th>
                    <th style="text-align: center">Cal Type</th>
                    <th style="text-align: center">Smart Phone Volume/Value</th>
                    <th style="text-align: center">Volume Ach/Value Ach</th>
                    <th style="text-align: center">Conn %</th>
<!--                    <th style="text-align: center">Smart Phone Value</th>
                    <th style="text-align: center">Value Ach</th>
                    <th style="text-align: center">Val Conn %</th>-->
                    <th style="text-align: center">Slab Name</th>
                    <th style="text-align: center">Incentive </th>
                    <th style="text-align: center">Total Incentive </th>
                </thead>
                <tbody class="data_1">
                    <?php $smartvol=0; $smartval=0; $saleqty=0;$saleamt=0; $volconn=0; $valconn=0; $slbper=0;$slab_name='';$inc_amount=0; $rsaleqty = 0;$rsalemt=0;
                    foreach ($policy_data as $pdata){ 
                        if($pdata->smart_qty){ $smartvol = $pdata->smart_qty; }else{ $smartvol = 0; }
                        if($pdata->smart_amount){ $smartval = $pdata->smart_amount; }else{ $smartval = 0; }
                        //Sales return
                        if($pdata->rsaleqty){ $rsaleqty = $pdata->rsaleqty; }else{ $rsaleqty = 0; }
                        if($pdata->rsale_amt){ $rsalemt = $pdata->rsale_amt; }else{ $rsalemt = 0; }
                        //Sale 
                        if($pdata->saleqty){ $saleqty = $pdata->saleqty - $rsaleqty; }else{ $saleqty = 0; }
                        if($pdata->sale_amt){ $saleamt = $pdata->sale_amt - $rsalemt; }else{ $saleamt = 0; }
                        if($smartvol > 0){ $volconn = ($saleqty/$smartvol)*100; }else{ $volconn =0;}
                        if($smartval > 0){ $valconn = ($saleamt/$smartval)*100; }else{ $valconn =0;}
                        
                        $policy_slabs = $this->Incentive_model->get_policy_slabs_data_byidpolicy($pdata->id_incentive_policy);
                        if($policy_slabs){
                            foreach ($policy_slabs as $pslab){
                                if($pdata->policy_type == 0){
                                    //Volume Connect
                                    if($saleqty >= $pslab->min_slab && $saleqty<= $pslab->max_slab){
                                        $slbper = $pslab->slab_per;
                                        $slab_name = $pslab->slab_name;
                                    }
                                    if($pdata->cal_type  == 0){
                                        //percentage cal
                                        $inc_amount = ($slbper*$saleqty)/100;
                                    }else{
                                        //amount cal
                                        $inc_amount = ($slbper*$saleqty);
                                    }
                                }elseif($pdata->policy_type == 1){
                                    //Value Connect
                                    if($saleamt >= $pslab->min_slab && $saleamt<= $pslab->max_slab){
                                        $slbper = $pslab->slab_per;
                                        $slab_name = $pslab->slab_name;
                                    }
                                    if($pdata->cal_type  == 0){
                                        //percentage cal
                                        $inc_amount = ($slbper*$saleamt)/100;
                                    }else{
                                        //amount cal
                                        $inc_amount = ($slbper);
                                    }
                                }
                                elseif($pdata->policy_type == 2){
                                    //Qty Connect
                                    $slbper = $pslab->slab_per;
                                    $slab_name = $pslab->slab_name;
                                    $inc_amount = ($slbper*$saleqty);
                                } 
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $pdata->zone_name; ?></td>
                            <td><?php echo $pdata->branch_category_name; ?></td>
                            <td><?php echo $pdata->branch_name; ?></td>
                            <td><?php echo $pdata->policy_name  ; ?></td>
                            <td><?php if($pdata->policy_type==0){echo 'Volume Connect';}elseif($pdata->policy_type==1){echo 'Value Connect';}else{ echo 'Qty Connect';} ?></td>
                            <td><?php if($pdata->cal_type==0){echo 'Percentage Calculate';}else{ echo 'Amount Calculation';} ?></td>
                            <?php if($pdata->policy_type==0 || $pdata->policy_type==2 ){ ?>
                            <td><?php echo $smartvol; ?></td>
                            <td><?php echo $saleqty; ?></td>
                            <td><?php echo round($volconn,2).'%'; ?></td>
                            <?php } else{ ?>
                            <td><?php echo $smartval; ?></td>
                            <td><?php echo $saleamt; ?></td>
                            <td><?php echo round($valconn,2).'%'; ?></td>
                            <?php } ?>
                            <td><?php echo $slab_name; ?></td>
                            <td><?php if($pdata->cal_type == 0){ echo $slbper.'%';}else{  echo $slbper.' Rs'; } ?></td>
                            <td><?php echo round($inc_amount); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                    alert('Policy Data Not Found');
                })
            </script>
        <?php }
    }
    
    public function policy_setup_report() {
        $q['tab_active'] = 'Target';
        $q['product_category_data'] = $this->Incentive_model->product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['policy_data'] = $this->Incentive_model->get_incentive_policy_data();
        $this->load->view('incentive_policy/policy_report',$q);
    }
    public function ajax_get_policy_report_data(){
        $month = $this->input->post('month');
        $idproduct = $this->input->post('pdprodcat');
        
        $ince_data = $this->Incentive_model->get_policy_report_data_byidpcat($month, $idproduct);
//        die('<pre>'.print_r($ince_data,1).'</pre>');
        ?>
        <table class="table table-bordered table-condensed" id="incentive_policy_data">
            <thead style="background-color: #99ccff">
                <th>Sr.</th>
                <th>Month</th>
                <th>Policy Name</th>
                <th>Policy Type</th>
                <th>Product Category</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Policy Slabs</th>
            </thead>
            <tbody class="data_1">
                <?php $sr=1; foreach ($ince_data as $indata){
                    $idpolicy = $indata->id_incentive_policy;
                    $policy_details = $this->Incentive_model->get_incentive_policy_details($idpolicy);
                    $slab_details = $this->Incentive_model->get_incentive_policy_slabs_details($idpolicy);
                    if($policy_details){
                        foreach ($policy_details as $pdata){

                            $category = $pdata->category_name;
                            $brand = $pdata->brand_name;
                        }
                    }else{
                        $category = '';
                        $brand = '';
                    }
                ?>
                <tr class="trdata">
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $indata->month_year; ?></td>
                    <td><?php echo $indata->policy_name; ?></td>
                    <td><?php if($indata->policy_type == 0){ echo 'Volume Connect'; }elseif($indata->policy_type == 1){ echo 'Value Connect'; }elseif($indata->policy_type == 2){ echo 'Qty Connect'; } ?></td>
                    <td><?php echo $indata->product_category_name; ?></td>
                    <td><?php echo $category; ?></td>
                    <td><?php if($brand != ''){ echo $brand; }else{ echo 'All Brands'; }?></td>
                    <td>
                        <?php $ss = 1; foreach ($policy_details as $pdata){
                          if($pdata->full_name != ''){ 
                              echo $ss++.') '.$pdata->full_name.'<br>';
                          }else{
                              echo 'All Models'.'<br>';
                          }
                        } ?>
                    </td>
                    <td>
                        <table class="table table-bordered">
                            <thead>
                                <th>Slab Name</th>
                                <th>Min Slab</th>
                                <th>Max Slab</th>
                                <th>Inc per</th>
                            </thead>
                            <tbody>
                                <?php foreach ($slab_details as $sdata){?>
                                <tr>
                                    <td><?php echo $sdata->slab_name ?></td>
                                    <td><?php echo $sdata->min_slab ?></td>
                                    <td><?php echo $sdata->max_slab ?></td>
                                    <td><?php if($indata->cal_type == 0){ echo $sdata->slab_per.'%'; }else{ echo 'Rs '.$sdata->slab_per;} ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php }
    
    
    
   
}