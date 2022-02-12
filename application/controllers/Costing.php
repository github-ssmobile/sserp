 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Costing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Costing_model');
        $this->load->model('General_model');
        $this->load->model('common_model');
    }

     public function branch_cost_header(){
        $q['tab_active'] = '';
        $q['costing_headers'] = $this->Costing_model->get_branch_cost_header();
        $this->load->view('branch_costing/branch_cost_header', $q);
    }
    public function save_branch_cost_header(){
        $data = array(
            'cost_header_name' => $this->input->post('cost_name'),
             'idtype' => $this->input->post('idtype'),
            'status' => $this->input->post('status'),
            'created_by' => $_SESSION['id_users'],
        );
        $this->Costing_model->Save_costing_header($data);
        $this->session->set_flashdata('save_data', 'Branch Costing Header Created Successfully');
        redirect('Costing/branch_cost_header');
    }
     public function edit_branch_cost_header(){
        $id = $this->input->post('id');
        $data = array(
            'cost_header_name' => $this->input->post('cost_name1'),
            'status' => $this->input->post('status1'),
            'idtype' => $this->input->post('idtype1'),
            'created_by' => $_SESSION['id_users'],
        );
        $this->Costing_model->edit_costing_header($data, $id);
        
        $this->session->set_flashdata('save_data', 'Branch Costing Header Updated Successfully');
        redirect('Costing/branch_cost_header');
    }
    
    public function branch_cost_data(){
        $q['tab_active'] = '';
//        $idrole = $_SESSION['idrole'];
        $q['costing_headers'] = $this->Costing_model->get_user_has_costing_header_by_user($_SESSION['id_users']);
        $this->load->view('branch_costing/add_branch_cost_data', $q);
    }
    public function ajax_get_branch_costing_data() {
        $monthyear = $this->input->post('monthyear');
        $idcostheader = $this->input->post('idcostheader');
        $branch_data = $this->General_model->get_active_branchs();
        $branch_cost_data = $this->Costing_model->get_branch_cost_data_bymonth_idcost($monthyear, $idcostheader);
//        die('<pre>'. print_r($branch_cost_data,1).'</pre>');
        if($branch_cost_data){        ?>
            <form>
                <div  style="">
                    <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                        <thead class="fixheader" style="background-color: #c6e6f5">
                            <th style="text-align: center">Zone</th>
							<th style="text-align: center">Branch</th>
                            <!-- <th style="text-align: center">Volume</th> -->
                            <th style="text-align: center; width:25%">Value</th>
                        </thead>
                        <tbody class="data_1">
                            <?php foreach ($branch_cost_data as $branch){ ?>
                            <tr>
							 <td><?php echo $branch->zone_name; ?>  </td>
                                <td><?php echo $branch->branch_name; ?>
                                    <input type="hidden" class="form-control input-sm" name="id_branch_costing_data[]" id="id_branch_costing_data" value="<?php echo $branch->id_branch_costing_data; ?>">
                                </td>
								<input type="hidden"  name="bvolume[]" id="bvolume" value="<?php echo $branch->volume; ?>"/>
                                <!-- <td><div style="display: none"><?php // echo $branch->volume; ?></div><input type="text" class="form-control input-sm" name="bvolume[]" id="bvolume" value="<?php // echo $branch->volume; ?>"></td> -->
                                <?php if ($branch->value > 0){ ?>
									<td><div style="display: none"><?php echo $branch->value; ?></div><input readonly="readonly" type="text" class="form-control input-sm" name="bvalue[]" id="bvalue" value="<?php echo $branch->value; ?>"></td>
								<?php  }else{ ?>
									<td><div style="display: none"><?php echo $branch->value; ?></div><input type="text" class="form-control input-sm" name="bvalue[]" id="bvalue" value="<?php echo $branch->value; ?>"></td>
								<?php } ?>
								
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="idcostheader" value="<?php echo $idcostheader ?>">
                    <input type="hidden" name="monthyear" value="<?php echo $monthyear ?>">
                </div>
                <div class="clearfix"></div><br>
                <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Costing/update_branch_costing_data">Update</button>
            </form>
        <?php } else {?>
            <form>
                <div  style="">
                    <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                        <thead class="fixheader" style="background-color: #c6e6f5">
						<th style="text-align: center">Zone</th>
                            <th style="text-align: center">Branch</th>
                            <!-- <th style="text-align: center">Volume</th> -->
                            <th style="text-align: center; width:25%">Value</th>
                        </thead>
                        <tbody>
                            <?php foreach ($branch_data as $branch){ ?>
                            <tr>
                                <td><?php echo $branch->zone_name; ?>  </td>
							   <td><?php echo $branch->branch_name; ?>
                                    <input type="hidden" class="form-control input-sm" name="idbranch[]" id="idbranch" value="<?php echo $branch->id_branch; ?>">
                                </td>
                                <!-- <td><input type="text" class="form-control input-sm" name="bvolume[]" id="bvolume" value="0"></td> -->
								<input type="hidden"  name="bvolume[]" id="bvolume" value="0">
                                <td><input type="text" class="form-control input-sm" name="bvalue[]" id="bvalue" value="0"></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="idcostheader" value="<?php echo $idcostheader ?>">
                    <input type="hidden" name="monthyear" value="<?php echo $monthyear ?>">
                </div>
                <div class="clearfix"></div><br>
                <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Costing/save_branch_costing_data">Submit</button>
            </form>
        <?php }
    }
    
    public function ajax_get_branch_costing_data_for_dowenloadxl(){
    $monthyear = $this->input->post('monthyear');
    $idcostheader = $this->input->post('idcostheader');
    $idtype = $this->input->post('idtype');
    $type_value = $this->input->post('type_value');
    $acc_data = $this->input->post('acc_data');
    $acc_new = json_decode($acc_data);
    $cost_data = $this->Costing_model->get_branch_cost_header_byid($idcostheader);
    $branch_data = $this->Costing_model->get_branch_with_sale_data($monthyear, $idcostheader);
    $branch_cost_data = $this->Costing_model->get_branch_cost_data_bymonth_idcost($monthyear, $idcostheader);
    ?>
    <form>
        <div class="thumbnail">
            <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                <thead class="fixheader" style="background-color: #c6e6f5">
                 <th style="text-align: center">Branch Id</th>
                 <th style="text-align: center">Branch Name</th>
                 <th style="text-align: center;">Cost Header</th>
                 <th style="text-align: center;">Cost Header Name</th>
                 <th style="text-align: center;">Month</th>
                 <th style="text-align: center;">Value</th>
             </thead>
             <tbody>
                <?php $erp_sale =0; $sale_total =0; $sale_return_total=0; $tval =0;
                foreach ($branch_data as $branch){ 
                   ?>
                   <tr>
                    <td style="text-align: center;"><?php echo $branch->id_branch; ?>  </td>
                    <td style="text-align: left;"><?php echo $branch->branch_name; ?>
                </td>
                <td><?php echo $idcostheader; ?>  </td>
                <td style="text-align: left;"><?php echo $cost_data->cost_header_name; ?>  </td>
                <td><?php echo $monthyear; ?>  </td>
                <td><?php echo '0'; ?>  </td>


            </tr>
        <?php } ?>
    </tbody>
</table>
<input type="hidden" name="idcostheader" value="<?php echo $idcostheader ?>">
<input type="hidden" name="monthyear" value="<?php echo $monthyear ?>">
<input type="hidden" name="idtypeval" value="<?php echo $type_value ?>">
<div class="clearfix"></div><br>
<!--   <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Costing/save_branch_costing_data">Submit</button> -->
<div class="clearfix"></div><br>
</div>
</form>

<?php 
}

function ajax_get_branch_costing_data_uploadedxl(){
  $monthyear = $this->input->post('monthyear');
  $idcostheader = $this->input->post('idcostheader');
  $idtype = $this->input->post('idtype');
  $type_value = $this->input->post('type_value');
  $acc_data = $this->input->post('acc_data');
  $acc_new = json_decode($acc_data);
  $cost_data = $this->Costing_model->get_branch_cost_header_byid($idcostheader);
  $branch_data = $this->Costing_model->get_branch_with_sale_data($monthyear, $idcostheader);
  $branch_cost_data = $this->Costing_model->get_branch_cost_data_bymonth_idcost($monthyear, $idcostheader);


  $i=0;
  $filename=$_FILES["upload_file"]["tmp_name"];
  if($_FILES["upload_file"]["size"] > 0){
    ?>
    <form>
        <div class="thumbnail">
            <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                <thead class="fixheader" style="background-color: #c6e6f5">

                    <th style="text-align: center">Branch</th>
                    <th style="text-align: center">Volume</th>

                    <th style="text-align: center;">Value</th>
                </thead>
                <tbody>
                    <?php
                    $file = fopen($filename, "r");
                    while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE){

                        if($i > 0){ 
                            if(trim($openingdata[4])==trim($monthyear) && $openingdata[2]==$idcostheader ){
                                ?>
                                <tr>
                                    <td><?php echo $openingdata[1]; ?><input type="hidden" class="form-control input-sm" name="idbranch[]" id="idbranch" value="<?php echo $openingdata[0]; ?>">
                                    </td>
                                    <td><input type="text" class="form-control input-sm" name="bvolume[]" id="bvolume" value="0"></td>
                                    <input type="hidden"  name="bvolume[]" id="bvolume" value="0">

                                    <td>
                                        <input type="text"  class="form-control input-sm bvalue" name="bvalue[]" id="bvalue"  value="<?php echo $openingdata[5]; ?>"></td>
                                    </tr>
                                    <?php 
                                }else{  ?>
                                    <h3>Please select correct file (check selected month and cost header and files month and cost header)</h3>


                                    <?php break; }
                                } ?>

                                <?php 
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="idcostheader" value="<?php echo $idcostheader ?>">
                    <input type="hidden" name="monthyear" value="<?php echo $monthyear ?>">
                    <input type="hidden" name="idtypeval" value="<?php echo $type_value ?>">
                    <div class="clearfix"></div><br>
                    <button type="submit" class="btn btn-primary pull-right" formmethod="POST" formaction="<?php echo base_url()?>Costing/save_branch_costing_data">Submit</button>
                    <div class="clearfix"></div><br>
                </div>
            </form>
            <script>
                $("#same_as_last").change(function() {
                    if(this.checked) {
                        alert("hi");
                        $('tr').each(function () {
                            var last_val = $(this).find('.lastval').val();
                            $(this).find('.bvalue').val(last_val);
                        });
                    }
                });
            </script>
            <?php 

        }
        fclose($file);  

    }
    
    public function save_branch_costing_data() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $monthyear = $this->input->post('monthyear');
        $idcostheader = $this->input->post('idcostheader');
        $idbranch = $this->input->post('idbranch');
        $bvolume = $this->input->post('bvolume');
        $bvalue = $this->input->post('bvalue');
        
        for($i=0; $i< count($idbranch); $i++){
            $data = array(
                'month_year' => $monthyear,
                'idcost_header' => $idcostheader,
                'idbranch' => $idbranch[$i],
                'volume' => $bvolume[$i],
                'value' => $bvalue[$i],
                'created_by' => $_SESSION['id_users'],
            );
            $this->Costing_model->save_branch_costing_data($data);
        }
         $this->session->set_flashdata('save_data', 'Branch Costing Data Created Successfully');
        redirect('Costing/branch_cost_data');
    }
    public function update_branch_costing_data() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $monthyear = $this->input->post('monthyear');
        $idcostheader = $this->input->post('idcostheader');
        $id_branch_costing_data = $this->input->post('id_branch_costing_data');
        $bvolume = $this->input->post('bvolume');
        $bvalue = $this->input->post('bvalue');
        
        for($i=0; $i< count($id_branch_costing_data); $i++){
            $data = array(
                'volume' => $bvolume[$i],
                'value' => $bvalue[$i],
                'created_by' => $_SESSION['id_users'],
            );
            $this->Costing_model->update_branch_costing_data($id_branch_costing_data[$i], $data);
        }
         $this->session->set_flashdata('save_data', 'Branch Costing Data Created Successfully');
        redirect('Costing/branch_cost_data');
    }
    
    public function branch_cost_report(){
        $q['tab_active'] = '';
        $q['costing_headers'] = $this->Costing_model->get_user_has_costing_header_by_user($_SESSION['id_users']);
        $this->load->view('branch_costing/branch_cost_data_report', $q);
    }
    public function ajax_get_branch_costing_data_report() {
        $monthyear = $this->input->post('monthyear');
        $idcostheader = $this->input->post('idcostheader');
        $branch_data = $this->General_model->get_active_branch_data();
        $branch_cost_data = $this->Costing_model->get_branch_cost_data_bymonth_idcost($monthyear, $idcostheader);
        if($branch_cost_data){        ?>
            <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                <thead class="fixheader" style="background-color: #c6e6f5">
                <th style="text-align: center" class="col-md-4">Branch</th>
                    <th style="text-align: center" class="col-md-4">Volume</th>
                    <th style="text-align: center" class="col-md-4">Value</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach ($branch_cost_data as $branch){ ?>
                    <tr style="text-align: center">
                        <td><?php echo $branch->branch_name; ?></td>
                        <td><?php echo $branch->volume; ?></td>
                        <td><?php echo $branch->value; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
    <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
    <?php }
    }
    
    
public function combined_report(){
    $q['tab_active'] = '';
    $q['branch_data'] = $this->General_model->get_active_branch_data();
    $q['zone_data'] = $this->General_model->get_active_zone();
    $this->load->view('branch_costing/branch_combined_report', $q);
}

public function ajax_get_branch_byidzone(){
    $idzone = $this->input->post('idzone');
    $zone = $this->Costing_model->get_acc_branch_byidzone($idzone);
    echo json_encode($zone);
}

public function ajax_get_combined_report() {
//        echo('<pre>'.print_r($_POST,1).'</pre>');
    $idbranch = $this->input->post('idbranch');
    $idzone = $this->input->post('idzone');
    $monthyear = $this->input->post('monthyear');

    $acc_data = $this->input->post('acc_data');
    $acc_new = json_decode($acc_data);

    $branches = array();
    if($idbranch == ''){
        $zone = $this->Costing_model->get_branch_byidzone($idzone);
        foreach($zone as $z){
            $branches[] = $z->id_branch;
        }
    }else{
        $branches[] = $idbranch;
    }

    $branch_cost_data  = $this->Costing_model->get_branch_cost_data_byidbranch($branches, $monthyear);
//        die('<pre>'.print_r($branch_cost_data,1).'</pre>');
    if($branch_cost_data){ ?>
        <table class="table table-bordered table-condensed">
            <thead style="background-color: #99ccff" class="fixheader">
                <th>Sr.</th>
                <th>Branch Name</th>
                <th>Branch Category</th>
                <th>Zone</th>
                <th>Type Of Expenses</th>
                <th><?php echo date('M Y', strtotime($monthyear)).' Expenses'?></th>
                <th><?php echo date('M-Y', strtotime($monthyear));?></th>
                <th><?php echo date('M-Y', strtotime($monthyear)).' Cost in %';?></th>
            </thead>
            <tbody>
                <?php $sr=1;$sale =0; $sale_return=0; $amt=0;
                $b_value=0;$b_total_sale=0;$b_cost_per=0; 

                $old_name = $branch_cost_data[0]->id_branch;
                foreach ($branch_cost_data as $bdata){ 
                  $bb = $bdata->acc_branch_id; 
                  $cost_center_branch  = $this->common_model->getSingleRow('cost_center_branch',array('original_branch_id'=>$bdata->id_branch));
                  if($bdata->value){ $amt = $bdata->value;}else{ $amt=0;}
                  if($bdata->sale_total){ $sale = $bdata->sale_total; }else{ $sale = 0;}
                  if($bdata->sale_return_total){ $sale_return = $bdata->sale_return_total; }else{ $sale_return = 0;}

                  foreach($acc_new as $ac){
                    if($bb == $ac->idbranch){
                        $acc_sale = $ac->achi - $ac->sales_return + $ac->risale; 
                    }
                }

                $total_sale = ($sale - $sale_return) + $acc_sale;
                $head_per='';
                if($bdata->idtype=='1'){
                    $cost_month_data=$this->common_model->getSingleRow('cost_data_config',array('cost_type'=>'1','cost_header'=>$bdata->id_cost_header,'month_year'=>$monthyear));
                    $head_per=' ('.$cost_month_data['cost_amount'].'%)';
                    $amt=($total_sale * $cost_month_data['cost_amount'])/100;

                }else if($bdata->idtype=='2'){
                    $cost_month_data=$this->common_model->getSingleRow('cost_data_config',array('cost_type'=>'2','cost_header'=>$bdata->id_cost_header,'month_year'=>$monthyear,'branch_category'=>$bdata->idbranchcategory));
                    
                    $amt= $cost_month_data['cost_amount'];
                }else if($bdata->idtype=='3'){
                  $cost_month_data=$this->common_model->getSingleRow('cost_data_config',array('cost_type'=>'3','cost_header'=>$bdata->id_cost_header,'month_year'=>$monthyear,'partener_type'=>$bdata->idpartner_type));
                  $head_per=' ('.$cost_month_data['cost_amount'].'%)';
                  $amt=($total_sale * $cost_month_data['cost_amount'])/100;

              }
//              else if($bdata->id_cost_header=='9'){
//                $branch_rent_data  = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$cost_center_branch['branch_id']));
//                $amt=$branch_rent_data['rent_amount'];
//            }
//            else if($bdata->id_cost_header=='13'){
//                $branch_petty_cash  = $this->common_model->getSingleRow('petti_cash',array('idbranch'=>$bdata->id_branch,'idwallet_type'=>3,'month_year'=>$monthyear));
//                $amt=$branch_petty_cash['amount'];
//            }
//            else if($bdata->id_cost_header=='16'){
//                $branch_petty_cash  = $this->common_model->getSingleRow('branch_insurence_details',array('branch_id'=>$cost_center_branch['branch_id']));
//                $amt=round(($branch_petty_cash['total_premium_amt']/12),2);
//            }
            $cost_per = ($amt/$total_sale)*100;

            if($old_name == $bdata->id_branch){
                $b_value = $b_value+$amt;
                $b_total_sale = ($sale - $sale_return) + $acc_sale;
                $b_cost_per = ($b_value/$b_total_sale)*100;

            }else{ ?>
                <tr style="background-color: #ffffcc" >
                    <td style="border-left: 1px solid #cccccc;"></td>
                    <td style="border-left: 1px solid #cccccc;"></td>     
                    <td style="border-left: 1px solid #cccccc;"></td>     
                    <td style="border-left: 1px solid #cccccc;"></td>     
                    <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_value; ?></b></td>                                    
                    <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_total_sale; ?></b></td>
                    <td style="border-left: 1px solid #cccccc;"><b><?php echo round($b_cost_per,2).'%'; ?></b></td>
                </tr>
                <?php   
                $b_value=0;$b_total_sale=0;$b_cost_per=0;
                $b_value = $b_value+$amt;
                $b_total_sale = ($sale - $sale_return) + $acc_sale;
                $b_cost_per = ($b_value/$b_total_sale)*100;
            }?>
            <tr>
                <td><?php echo $sr; ?></td>
                <td><?php echo $bdata->branch_name; ?></td>
                <td><?php echo $bdata->branch_category_name; ?></td>
                <td><?php echo $bdata->zone_name; ?></td>
                <td><?php echo $bdata->cost_header_name.$head_per; ?></td>
                <td><?php echo $amt; ?></td>
                <td><?php echo $total_sale; ?></td>
                <td><?php echo round($cost_per,2).'%'; ?></td>
            </tr>
            <?php $sr++; $old_name=$bdata->id_branch; }?>
            <tr style="background-color: #ffffcc" >
                <td style="border-left: 1px solid #cccccc;"></td>
                <td style="border-left: 1px solid #cccccc;"></td>     
                <td style="border-left: 1px solid #cccccc;"></td>     
                <td style="border-left: 1px solid #cccccc;"></td>     
                <td style="border-left: 1px solid #cccccc;"><b>Total</b></td>            
                <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_value; ?></b></td>                                    
                <td style="border-left: 1px solid #cccccc;"><b><?php echo $b_total_sale; ?></b></td>
                <td style="border-left: 1px solid #cccccc;"><b><?php echo round($b_cost_per,2).'%'; ?></b></td>
            </tr>
        </tbody>
    </table>
<?php }

}

}