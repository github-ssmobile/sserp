<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Costing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Costing_model');
        $this->load->model('General_model');
    }

     public function branch_cost_header(){
        $q['tab_active'] = '';
        $q['costing_headers'] = $this->Costing_model->get_branch_cost_header();
        $this->load->view('branch_costing/branch_cost_header', $q);
    }
    public function save_branch_cost_header(){
        $data = array(
            'cost_header_name' => $this->input->post('cost_name'),
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
}