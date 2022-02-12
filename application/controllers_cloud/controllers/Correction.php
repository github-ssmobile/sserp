<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Correction extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Audit_model');
        $this->load->model('Correction_model');
        $this->load->model('General_model');
    }
    public function correction_system(){
        $q['tab_active'] = ' ';
        $q['correction_system'] = $this->Correction_model->get_correction_system();
        $q['helpline_type'] = $this->Correction_model->get_helpline_type();
        $q['correction_type'] = $this->Correction_model->get_corretcion_type();
        $q['payment_mode'] = $this->General_model->get_active_payment_mode();
//        $q['customer_data'] = $this->Correction_model->get_customer_data();
        
        $q['correction_request_data'] = $this->Correction_model->get_pending_correction_request();
//        die('<pre>'.print_r($q['correction_request_data'],1).'</pre>');
        $this->load->view('correction/add_correction', $q);
    }
    public function ajax_get_customer_bycontact(){
        $contact = $this->input->post('contact');
        $cust_data = $this->Correction_model->get_customer_bycontact($contact);
        if(count($cust_data)>0){ ?>
            <select class="form-control" name="idcustomer" id="idcustomer" required="">
                <?php foreach($cust_data as $cust){ ?>
                <option value="<?php echo $cust->id_customer?>"><?php echo $cust->customer_fname.' '.$cust->customer_lname?></option>
                <?php } ?>
            </select>
        <?php }else{ ?>
            <script>
                alert("Customer Not Found");
            </script>
        <?php
        }
    }
    public function save_correction_request(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        if($this->input->post('idcustomer') == ''){
            $idcust = NULL;
        }else{
            $idcust = $this->input->post('idcustomer');
        }
        
        if($this->input->post('idpaymentmode') == ''){
            $oldpaymenymode = '';
        }
        else{
            $oldpaymenymode = $this->input->post('idpaymentmode');
        }
        
        $datetime = date('Y-m-d h:i:s');
        $month = date('M');
        $year = date('Y');
        
        $data = array(
            'date' => $this->input->post('date'),
            'month' => $month,
            'year' => $year,
            'idbranch' => $_SESSION['idbranch'],
            'idsystem' => $this->input->post('idsystem'),
            'idhelpline' => $this->input->post('idhelpline'),
            'idcorrectiontype' => $this->input->post('idcorrectiontype'),
            'invoice_no' => $this->input->post('inv_no'),
            'transaction_id' => $this->input->post('transaction_id'),
            'gst_no' => $this->input->post('gst_no'),
            'idcustomer' => $idcust,
            'cust_contact' => $this->input->post('contact'),
            'cust_info' => $this->input->post('cust_info'),
            'idold_paymentmode' => $oldpaymenymode,
            'idnew_paymentmode' => $newpaymode,
            'old_amount' => $this->input->post('oldamount'),
            'new_amount' => $this->input->post('newamount'),
            'product_name' => $this->input->post('product_name'),
            'new_imei' => $this->input->post('newimei'),
            'oldproduct' => $this->input->post('oldproduct'),
            'newproduct' => $this->input->post('newproduct'),
            'remark' => $this->input->post('remark'),
            'status' => 0,
            'created_by' => $_SESSION['id_users'],
            'created_entrytime' => $datetime,
        );
        $this->Correction_model->save_correction_data($data);
        $this->session->set_flashdata('save_data', 'Correction Saved Successfully');
        redirect('Correction/correction_system');
    }

    public function branch_pending_correction(){
        $q['tab_active'] = ' ';
        $q['correction_request_data'] = $this->Correction_model->get_branch_pending_correction_request();
        $this->load->view('correction/branch_pending_correction', $q);
    }
    public function update_correction_request(){
        //die(print_r($_POST));
        $idcorrectionreq = $this->input->post('idcorrectionreq');
        $idstatus = $this->input->post('idstatus');
        $data = array(
            'status' => $idstatus,
            'updated_by' => $_SESSION['id_users'],
            'updated_entrytime' => date('Y-m-d h:i:s'),
            'updated_remark' => $this->input->post('update_remark'),
        );
        if($this->Correction_model->update_correction_request($data, $idcorrectionreq)){
            echo '1';
        }else{
            echo '0';
        }
    }
    public function helpline_report(){
        $q['tab_active'] = ' ';
        
        if($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }else{
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }
        $this->load->view('correction/helpline_report', $q);
    }
    public function ajax_get_helpline_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $idstatus = $this->input->post('idstatus');
        $correction_request_data = $this->Correction_model->get_helpline_report($from, $to, $idbranch, $idstatus, $branches);
        if(count($correction_request_data) > 0){ ?>
            <table class="table table-bordered table-condensed" id="helpline_report">
                <thead style="background-color: #a9c5fc">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>System</th>
                    <th>Helpline</th>
                    <th>Correction Type</th>
                    <th>Invoice No</th>
                    <th>Transaction Id</th>
                    <th>GST No</th>
                    <th>Customer</th>
                    <th>Customer Contact</th>
                    <th>Customer Updates</th>
                    <th>Payment Mode</th>
                    <th>New Payment Mode</th>
                    <th>Amount</th>
                    <th>New Amount</th>
                    <th>Product Name</th>
                    <th>New IMEI</th>
                    <th>Old Product</th>
                    <th>New Product</th>
                    <th>Remark</th>
                    <th>Created By</th>
                    <th>Updated Remark</th>
                    <th>Status</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach($correction_request_data as $cdata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $cdata->date ?></td>
                        <td><?php echo $cdata->branch_name ?></td>
                        <td><?php echo $cdata->system_name ?></td>
                        <td><?php echo $cdata->helpline_type ?></td>
                        <td><?php echo $cdata->correction_type ?></td>
                        <td><?php echo $cdata->invoice_no ?></td>
                        <td><?php echo $cdata->transaction_id ?></td>
                        <td><?php echo $cdata->gst_no ?></td>
                        <td><?php echo $cdata->customer_fname.' '.$cdata->customer_lname ?></td>
                        <td><?php echo $cdata->cust_contact ?></td>
                        <td><?php echo $cdata->cust_info ?></td>
                        <td><?php if($cdata->idold_paymentmode != 0){ echo $cdata->oldpaymentmode; }?></td>
                        <td><?php if($cdata->idnew_paymentmode != 0){ echo $cdata->newpaymentmode; }?></td>
                        <td><?php echo $cdata->old_amount ?></td>
                        <td><?php echo $cdata->new_amount ?></td>
                        <td><?php echo $cdata->product_name ?></td>
                        <td><?php echo $cdata->new_imei ?></td>
                        <td><?php echo $cdata->oldproduct ?></td>
                        <td><?php echo $cdata->newproduct ?></td>
                        <td><?php echo $cdata->remark ?></td>
                        <td><?php echo $cdata->user_name ?></td>
                        <td><?php echo $cdata->updated_remark ?></td>
                        <td><?php if($cdata->status == 0){ echo 'Pending';}elseif($cdata->status == 1){ echo 'On Hold';}elseif ($cdata->status == 2){ echo 'Closed'; } ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php     
        } 
    }
}
    