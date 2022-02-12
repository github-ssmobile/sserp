<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reconciliation extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model('Sale_model');
        $this->load->model('Reconciliation_model');
        date_default_timezone_set('Asia/Kolkata');
    }
    function payment_received_receipt($idrecon) {
        $q['tab_active'] = 'Sale';
        $q['payment_received_data'] = $this->Reconciliation_model->get_reconciliation_byid($idrecon);
        $this->load->view('sale/payment_received_receipt',$q);
    }
    public function save_credit_received() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $id = $this->input->post('id_salepayment');
        $amount = $this->input->post('amount');
        $received_amount1 = $this->input->post('received_amount');
        $sum = $received_amount1 + $amount;
        $update = array(
            'payment_receive' => 1,
            'received_amount' => $sum,
            'received_entry_time' => $datetime,
        );
        if($this->Sale_model->update_credit_sale_payment_byid($id,$update)){
            $received_amount=0;$pending_amt=$sum;$received_entry_time=NULL;$payment_receive=0;
            if($this->input->post('payment_mode') == 1){
                $received_amount = $amount;
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
            }
            $data = array(
                'idsale_payment'=>$id,
                'date' => $date,
                'amount' => $amount,
                'from_credit_buyback_received' => 1,
                'inv_no' => $this->input->post('inv_no'),
                'idcustomer' => $this->input->post('idcustomer'),
                'idpayment_mode' => $this->input->post('payment_mode'),
                'idpayment_head' => $this->input->post('payment_head'),
                'transaction_id' => $this->input->post('tranxid'),
                'idbranch' => $this->input->post('idbranch'),
                'idsale' => $this->input->post('idsale'),
                'created_by' => $this->input->post('created_by'),
                'corporate_sale' => $this->input->post('corporate_sale'),
                'entry_time' => $datetime,
                'received_amount' => $received_amount,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
                'credit_receive_remark' => $this->input->post('credit_receive_remark'),
            );
            $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($this->input->post('payment_head'));
            
            $attr_value = array();
            foreach ($payment_attribute as $attr){
                $attr_value[$attr->column_name] = $this->input->post($attr->column_name);
            }
            $data1 = array_merge($data, $attr_value); 
            if($idrecon = $this->Sale_model->save_payment_reconciliation($data1)){
                if($this->input->post('payment_mode') == 1){
                    $repayment = array(
                        'date' => $date,
                        'inv_no' => $this->input->post('inv_no'),
                        'entry_type' => 4, // credit receive
                        'idbranch' => $this->input->post('idbranch'),
                        'idtable' => $idrecon,
                        'table_name' => 'payment_reconciliation',
                        'idcustomer' => $this->input->post('idcustomer'),
                        'customer_gst' => $this->input->post('gst_no'),
                        'amount' => $amount,
                    );
                    $this->Sale_model->save_daybook_cash_payment($repayment);
                }
                $q['result'] = 'Success';
                $q['idrecon'] = $idrecon;
            }else{
                $q['result'] = 'Failed';
                $q['idrecon'] = '';
            }
            echo json_encode($q);
        }
    }
    
    public function payment_reconciliation(){
        $q['tab_active'] = 'Payment Reconciliation';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['payment_mode'] = $this->General_model->get_payment_modes_by_user($iduser);
        $this->load->view('reconciliation/payment_reconciliation_form', $q);
    }
    public function receivables_received_report(){
        $q['tab_active'] = 'Payment Reconciliation';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['payment_mode'] = $this->General_model->get_payment_mode_for_receivables();
        $this->load->view('reconciliation/receivables_received_report', $q);
    }
    
    public function payment_reconciliation_report(){
        $q['tab_active'] = 'Report';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        if($_SESSION['idrole'] == 23){
            $q['payment_mode'] = $this->General_model->get_payment_modes_by_user($iduser);
        }else{
            $q['payment_mode'] = $this->General_model->get_payment_modes_for_reconciliation();
        }
        $this->load->view('reconciliation/payment_reconciliation_report', $q);
    }
    public function credit_received_report(){
        $q['tab_active'] = 'Report';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['payment_mode'] = $this->General_model->get_payment_modes_for_credit_received_report();
        $this->load->view('report/credit_received_report', $q);
    }
    public function ajax_get_credit_received_report(){
        $idpaymentmode = $this->input->post('payment_mode');
        $idbranch = $this->input->post('idbranch');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $payment_reconcilationdata = $this->Reconciliation_model->ajax_get_credit_received_report_byfilter($idpaymentmode, $idbranch, $datefrom, $dateto, $modes, $branches);
//        die('<pre>'.print_r($payment_reconcilationdata,1).'</pre>');
        if(count($payment_reconcilationdata) > 0){ ?>
            <thead class="fixedelement">
                <th>Sr.</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th>Received Date</th>
                <th>Branch</th>
                <th>Mode</th>
                <th>TxnId</th>
                <th>Customer Name</th>
                <th>Customer Contact</th>
                <th>Invoice Amount</th>
                <th>Received Amount</th>
                <!--<th>Received Amount </th>-->
                <th>Received Time</th>
                <th>Days</th>
                <th>Credit Remark</th>
                <th>Receipt</th>
            </thead>
            <tbody>
                <?php $i=1; foreach ($payment_reconcilationdata as $pr){?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $pr->inv_no ?></td>
                    <td><?php echo $pr->invoice_date ?></td>
                    <td><?php echo $pr->date ?></td>
                    <td><?php echo $pr->branch_name ?></td>
                    <td><?php echo $pr->payment_mode ?></td>
                    <td><?php echo $pr->transaction_id ?></td>
                    <td><?php echo $pr->customer_fname.' '.$pr->customer_lname ?></td>
                    <td><?php echo $pr->customer_contact ?></td>
                    <td><?php echo $pr->final_total ?></td>
                    <td><?php echo $pr->amount ?></td>
                    <!--<td><?php // echo $pr->received_amount ?></td>-->
                    <td><?php if($pr->idpayment_mode == 1){ echo $pr->received_entry_time; $rec = $pr->received_entry_time; }else{ echo $pr->entry_time; $rec = $pr->entry_time; } ?></td>
                    <td><?php 
                        $now = strtotime($rec); // or your date as well
                        $credit_date = strtotime($pr->invoice_date);
                        $datediff = $now - $credit_date;
                        echo round($datediff / (86400)); 
                    ?></td>
                    <td><?php echo $pr->credit_receive_remark ?></td>
                    <td><a href="<?php echo base_url('Reconciliation/payment_received_receipt/'.$pr->id_payment_reconciliation) ?>" class="btn btn-default btn-floating waves-effect" target="_blank"><i class="fa fa-print"></i></a></td>
                </tr>
                <?php } ?>
            </tbody>
        <?php }
    }

    public function bank_reconciliation_form(){
        $q['tab_active'] = 'Payment Reconciliation';
        $iduser = $_SESSION['id_users'];
        $q['bank_data'] = $this->General_model->get_active_bank();
        $q['payment_mode'] = $this->General_model->get_bank_recon_payment_modes($iduser);
        $q['bank_recon'] = $this->Reconciliation_model->get_bank_recon_10days_data($iduser);
        $this->load->view('reconciliation/bank_reconciliation_form', $q);
    }
    
    public function bank_reconciliation_report(){
        $q['tab_active'] = 'Report';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $q['payment_mode'] = $this->General_model->get_bank_recon_payment_modes($iduser);
        $this->load->view('reconciliation/bank_reconciliation_report', $q);
    }
    
    public function bank_received_list(){
        $q['tab_active'] = 'Report';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['payment_mode'] = $this->General_model->get_payment_mode_for_receivables();
        }else{
            $q['payment_mode'] = $this->General_model->get_bank_recon_payment_modes($iduser);
        }
        $this->load->view('reconciliation/bank_received_list', $q);
    }
    
    public function cheque_reconciliation_form(){
        $q['tab_active'] = 'Bank Reconciliation';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
//        $q['payment_mode'] = $this->General_model->get_bank_recon_payment_modes($iduser);
        $this->load->view('reconciliation/cheque_reconciliation_form', $q);
    }
    
    public function cash_reconciliation_form(){
        $q['tab_active'] = 'Payment Reconciliation';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $this->load->view('reconciliation/cash_reconciliation_form', $q);
    }
    
    public function ajax_new_credit_report() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
//        $bank_data = $this->General_model->get_active_bank_data();
        $credit_report = $this->Report_model->ajax_get_credit_for_reconciliation($idpayment_head,$idpayment_mode,$idbranch); ?>
        <thead>
            <th>Sr</th>
            <th>Date time</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>Mode</th>
            <th>Total Amount</th>
            <th>Txn No</th>
            <th>Days</th>
            <!--<th>Receive</th>-->
            <!--<th>Commission</th>-->
            <!--<th>Comm%</th>-->
            <!--<th>Bank</th>-->
            <!--<th>UTR</th>-->
            <!--<th>Received date</th>-->
            <!--<th>Receive</th>-->
        </thead>
        <tbody>
            <?php $i=1; foreach ($credit_report as $credit) { ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                <td><?php echo $credit->branch_name ?> </td>
                <td><a href="<?php echo base_url('Reports/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                <td><?php echo $credit->customer_name ?> </td>
                <td><?php echo $credit->customer_contact ?> </td>
                <td><?php echo $credit->payment_mode ?> </td>
                <td><?php echo $credit->amount ?> </td>
                <td><?php echo $credit->transaction_id ?> </td>
                <td><?php 
                    $now = time(); // or your date as well
                    $credit_date = strtotime($credit->entry_time);
                    $datediff = $now - $credit_date;
                    echo round($datediff / (86400)); ?> 
                </td> 
<!--                <td>
                    <input type="text" class="form-control input-sm received_amt" name="received_amt" placeholder="Amount" style="width: 80px" required=""/>
                    <input type="hidden" name="amount" value="<?php echo $credit->amount ?>" />
                    <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
                </td>
                <td>
                    <input type="text" class="form-control input-sm commission_amt" name="commission_amt" placeholder="Comission" style="width: 80px" required=""/>
                </td>
                <td>
                    <input type="text" class="form-control input-sm" name="commission_per" placeholder="Percentage" style="width: 50px" required="" readonly=""/>
                </td>
                <td>
                    <select class="form-control input-sm" required="" name="idbank" style="width: 100px">
                        <option value="">Bank</option>
                        <?php // foreach ($bank_data as $bank){ ?>
                        <option value="<?php // echo $bank->id_bank ?>"><?php // echo $bank->bank_name.' '.$bank->bank_branch ?></option>
                        <?php // } ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control input-sm" name="utr" placeholder="UTR" style="width: 140px" required=""/>
                </td>
                <td>
                    <input type="date" class="form-control input-sm" name="received_date" placeholder="Received Date" style="width: 110px; padding: 0" required="" />
                </td>
                <td>
                    <button class="btn btn-default btn-sm payment_reconciliation_btn" value="<?php // echo $credit->id_payment_reconciliation ?>" style="margin: 0"><i class="fa fa-sign-out"></i></button>
                </td>-->
            </tr>
            <?php } ?>
        </tbody>
    <?php }
    
    public function ajax_payment_reconciliation_form() {
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
//        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $credit_report = $this->Reconciliation_model->ajax_get_credit_for_reconciliation($idpayment_mode,$idbranch,$datefrom,$dateto);
        if(count($credit_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }else{ 
            $bank_data = $this->General_model->get_active_bank();
            $payment_devices = $this->Reconciliation_model->get_devices_byidpayment_mode($idpayment_mode); ?>
        <thead class="fixedelement">
            <!--<th>Sr</th>-->
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <!--<th>Contact</th>-->
            <!--<th>GSTIN</th>-->
            <th>Mode</th>
            <th>Txn No</th>
            <th>Expected Amount</th>
            <th>Days</th>
            <th>Received Amount</th>
            <th>Commission Amount</th>
            <th>Commission Percentage</th>
            <th>Short Receive</th>
            <th>Bank</th>
            <th>UTR</th>
            <th>Received date</th>
            <th>Receive</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($credit_report as $credit) { ?>
            <form class="ajax_recon_form">
            <tr>
                <!--<td><?php // echo $i++; ?></td>-->
                <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                <td><?php echo $credit->branch_name ?></td>
                <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                <td><?php echo $credit->customer_fname.' '.$credit->customer_lname.' - '.$credit->customer_contact ?></td>
                <!--<td><?php // echo $credit->customer_contact ?> </td>-->
                <!--<td><?php // echo $credit->customer_gst ?> </td>-->
                <td><?php echo $credit->payment_mode ?></td>
                <td><?php echo $credit->transaction_id ?></td>
                <td><?php echo $credit->amount ?></td>
                <td><?php $now = time(); // or your date as well
                    $credit_date = strtotime($credit->entry_time);
                    $datediff = $now - $credit_date;
                    echo round($datediff / (86400)); ?> 
                </td> 
                <td>
                    <input type="number" class="form-control input-sm received_amt" name="received_amt" placeholder="Amount" style="width: 100px" required="" min="1"/>
                    <input type="hidden" class="amount" name="amount" value="<?php echo $credit->amount ?>" />
                    <input type="hidden" class="iduser" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
                </td>
                <td>
                    <input type="number" class="form-control input-sm commission_amt" name="commission_amt" placeholder="Comission" required="" value="0" min="0" style="width: 100px"/>
                </td>
                <td>
                    <input type="hidden" class="commission_per" name="commission_per" value="0"/>
                    <span class="commission_per_lb">0%</span>
                </td>
                <td>
                    <input type="hidden" class="short_receive" name="short_receive" value="<?php echo $credit->amount ?>"/>
                    <span class="short_receive_lb"><?php echo $credit->amount ?></span>
                </td>
                <td>
                    <select class="form-control input-sm idbank" required="" name="idbank" style="width: 150px">
                        <option value="">Select Bank</option>
                        <?php foreach ($bank_data as $bank){ ?>
                        <option value="<?php echo $bank->id_bank ?>"><?php echo $bank->bank_name.' '.$bank->bank_branch ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <input list="utr_devices" class="form-control input-sm utr" name="utr" placeholder="UTR" style="width: 140px" required=""/>
                    <datalist id="utr_devices">
                        <?php foreach ($payment_devices as $devices){ ?>
                        <option value="<?php echo $devices->device_id ?>">
                        <?php } ?>
                    </datalist>
                </td>
                <td>
                    <input type="text" data-provide="datepicker" class="form-control input-sm received_date" name="received_date" placeholder="Received Date" style="width: 110px;" required="" />
                </td>
                <td>
                    <button type="submit" class="btn btn-default grdark btn-sm payment_reconciliation_btn" value="<?php echo $credit->id_payment_reconciliation ?>" style="margin: 0">Receive</button>
                </td>
            </tr>
            </form>
            <?php } ?>
        </tbody>
    <?php }}
    
    public function ajax_cheque_reconciliation_form() {
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $credit_report = $this->Reconciliation_model->ajax_get_credit_for_reconciliation($idpayment_mode,$idbranch,$datefrom,$dateto);
        if(count($credit_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }else{ 
            $bank_data = $this->General_model->get_active_bank(); ?>
        <thead class="bg-info">
            <!--<th>Sr</th>-->
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <!--<th>GSTIN</th>-->
            <!--<th>Mode</th>-->
            <th>Cheque No</th>
            <th>Cheque Amount</th>
            <th>Customer Bank</th>
            <th>Days</th>
            <th>Received Amount</th>
            <!--<th>Commission Amount</th>-->
            <!--<th>Commission Percentage</th>-->
            <th>Short Receive</th>
            <th>Bank</th>
            <th>UTR</th>
            <th>Received date</th>
            <th>Receive</th>
            <th>Bounce Charges</th>
            <th>Bounce</th>
            <th>Customer</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($credit_report as $credit) { ?>
            <form class="ajax_recon_form">
            <tr>
                <!--<td><?php // echo $i++; ?></td>-->
                <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                <td><?php echo $credit->branch_name ?></td>
                <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                <!--<td><?php // echo $credit->customer_contact ?> </td>-->
                <!--<td><?php // echo $credit->customer_gst ?> </td>-->
                <!--<td><?php // echo $credit->payment_mode ?></td>-->
                <td><?php echo $credit->transaction_id ?></td>
                <td><?php echo $credit->amount ?></td>
                <td><?php echo $credit->customer_bank_name ?></td>
                <td><?php $now = time(); // or your date as well
                    $credit_date = strtotime($credit->entry_time);
                    $datediff = $now - $credit_date;
                    echo round($datediff / (86400)); ?>
                </td>
                <td>
                    <input type="number" class="form-control input-sm received_amt" name="received_amt" placeholder="Amount" style="width: 100px" required="" min="1"/>
                    <input type="hidden" class="amount" name="amount" value="<?php echo $credit->amount ?>" />
                    <input type="hidden" class="iduser" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
                </td>
                <td>
                    <input type="hidden" class="inv_no" name="inv_no" value="<?php echo $credit->inv_no ?>"/>
                    <input type="hidden" class="idcustomer" name="idcustomer" value="<?php echo $credit->idcustomer ?>"/>
                    <input type="hidden" class="corporate_sale" name="corporate_sale" value="<?php echo $credit->corporate_sale ?>"/>
                    <input type="hidden" class="date" name="date" value="<?php echo $credit->date ?>"/>
                    <input type="hidden" class="branch" name="branch" value="<?php echo $credit->id_branch ?>"/>
                    <input type="hidden" class="idsale" name="idsale" value="<?php echo $credit->idsale ?>"/>
                    <input type="hidden" class="customer_bank_name" name="customer_bank_name" value="<?php echo $credit->customer_bank_name ?>"/>
                    <input type="hidden" class="transaction_id" name="transaction_id" value="<?php echo $credit->transaction_id ?>"/>
                    <input type="hidden" class="idsale_payment" name="idsale_payment" value="<?php echo $credit->idsale_payment ?>"/>
                    <input type="hidden" class="short_receive" name="short_receive" value="<?php echo $credit->amount ?>"/>
                    <span class="short_receive_lb"><?php echo $credit->amount ?></span>
                </td>
                <td>
                    <select class="form-control input-sm idbank" required="" name="idbank" style="width: 150px">
                        <option value="">Select Bank</option>
                        <?php foreach ($bank_data as $bank){ ?>
                        <option value="<?php echo $bank->id_bank ?>"><?php echo $bank->bank_name.' '.$bank->bank_branch ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <input class="form-control input-sm utr" name="utr" placeholder="UTR" style="width: 140px" required=""/>
                </td>
                <td>
                    <input type="text" data-provide="datepicker" class="form-control input-sm received_date" name="received_date" placeholder="Received Date" style="width: 110px;" required="" />
                </td>
                <td>
                    <button type="submit" class="btn btn-primary btn-sm cheque_reconciliation_btn" value="<?php echo $credit->id_payment_reconciliation ?>" style="margin: 0">Receive</button>
                </td>
                <td>
                    <input type="number" class="form-control input-sm bounce_charges" name="bounce_charges" placeholder="Bounce Charges" style="width: 80px" value="250" />
                </td>
                <td>
                    <button type="submit" class="btn btn-warning btn-sm cheque_bounce_btn" value="<?php echo $credit->id_payment_reconciliation ?>" style="margin: 0">Bounch</button>
                </td>
                <td><?php echo $credit->customer_fname.' '.$credit->customer_lname.' '.$credit->customer_contact ?></td>
            </tr>
            </form>
            <?php } ?>
        </tbody>
    <?php }}
    
    public function ajax_cash_reconciliation_form() {
//        die(print_r($_POST));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
        $idbranches = $this->input->post('branches');
        $cash_deposit= $this->Reconciliation_model->ajax_get_cash_for_reconciliation($idbranch,$idbranches,$datefrom,$dateto);
        if(count($cash_deposit) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }else{ ?>
        <thead class="fixedelement">
            <th>Date</th>
            <th>Branch</th>
            <th>Deposit Bank</th>
            <!--<th>Closure Cash</th>-->
            <!--<th>Difference</th>-->
            <th>Remark</th>
            <th>Days</th>
            <th>Deposit Cash</th>
            <th>Actual Deposit</th>
            <th>Short Receive</th>
            <th>UTR</th>
            <th>Received date</th>
            <th>Receive</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($cash_deposit as $deposit) { ?>
            <form class="ajax_recon_form">
            <tr>
                <td><?php echo date('d-m-Y', strtotime($deposit->date)) ?></td>
                <td><?php echo $deposit->branch_name ?></td>
                <td><?php echo $deposit->bank_name ?></td>
                <!--<td><?php // echo $deposit->total_closure_cash ?></td>-->
                <!--<td><?php // echo $deposit->remaining_after_deposit ?></td>-->
                <td><?php echo $deposit->remark ?></td>
                <td><?php $now = time(); // or your date as well
                    $deposit_date = strtotime($deposit->entry_time);
                    $datediff = $now - $deposit_date;
                    echo round($datediff / (86400)); ?>
                </td>
                <td><?php echo $deposit->deposit_cash ?></td>
                <td>
                    <input type="number" class="form-control input-sm received_amt" name="received_amt" placeholder="Amount" style="width: 120px" required="" min="1"/>
                    <input type="hidden" class="amount" name="amount" value="<?php echo $deposit->deposit_cash ?>" />
                    <input type="hidden" class="iduser" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
                    <!--<input type="hidden" class="received_date" name="received_date" value="<?php echo $deposit->date ?>" />-->
                </td>
                <td>
                    <input type="hidden" class="short_receive" name="short_receive" value="<?php echo $deposit->deposit_cash ?>"/>
                    <span class="short_receive_lb"><?php echo $deposit->deposit_cash ?></span>
                </td>
                <td>
                    <input class="form-control input-sm utr" name="utr" placeholder="UTR" style="width: 140px" required=""/>
                </td>
                <td>
                    <input type="text" data-provide="datepicker" class="form-control input-sm received_date" name="received_date" placeholder="Received Date" value="<?php echo $deposit->date ?>" style="width: 110px;" required="" />
                </td>
                <td>
                    <button type="submit" class="btn btn-primary btn-sm cash_reconciliation_btn" value="<?php echo $deposit->id_cash_deposite_to_bank ?>" style="margin: 0">Receive</button>
                </td>
            </tr>
            </form>
            <?php } ?>
        </tbody>
    <?php }}
        
    public function ajax_payment_reconciliation_report() {
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
//        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $credit_report = $this->Reconciliation_model->ajax_get_credit_for_reconciliation_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$branches);
        if(count($credit_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }else{ 
//            $bank_data = $this->General_model->get_active_bank();
//            $payment_devices = $this->Reconciliation_model->get_devices_byidpayment_mode($idpayment_mode); ?>
            <thead class="fixedelementtop">
                <th>Date</th>
                <th>Branch</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Mode</th>
                <th>Txn No</th>
                <th>Expected Amount</th>
                <!--<th>Days</th>-->
            </thead>
            <tbody>
                <?php $i=1; foreach ($credit_report as $credit) { ?>
                <form class="ajax_recon_form">
                <tr>
                    <!--<td><?php // echo $i++; ?></td>-->
                    <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                    <td><?php echo $credit->branch_name ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                    <td><?php echo $credit->customer_fname.' '.$credit->customer_lname; ?></td>
                    <td><?php echo $credit->customer_contact ?> </td>
                    <td><?php echo $credit->customer_gst ?> </td>
                    <td><?php echo $credit->payment_mode ?></td>
                    <td><?php echo $credit->transaction_id ?></td>
                    <td><?php echo $credit->amount ?></td>
                    <!--<td>-->
                        <?php $now = time(); // or your date as well
                        $credit_date = strtotime($credit->entry_time);
                        $datediff = $now - $credit_date;
//                        echo round($datediff / (86400)); ?> 
                    <!--</td>-->
                </tr>
                </form>
                <?php } ?>
            </tbody>
    <?php }}
    
    public function ajax_receivables_received_report() {
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
//        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $credit_report = $this->Reconciliation_model->ajax_get_receivables_received_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$branches);
        if(count($credit_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }else{ 
//            $bank_data = $this->General_model->get_active_bank();
//            $payment_devices = $this->Reconciliation_model->get_devices_byidpayment_mode($idpayment_mode); ?>
            <thead class="fixedelementtop">
                <th>Invoice Date</th>
                <th>Branch</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Contact</th>
                <!--<th>GSTIN</th>-->
                <th>Head</th>
                <th>Mode</th>
                <th>Txn No</th>
                <th>Expected Amount</th>
                <th>Received Date</th>
                <th>Received Amount</th>
                <th>Commission Amount</th>
                <th>Short Received</th>
                <th>Days</th>
                <th>Bank Name</th>
                <th>UTR No</th>
            </thead>
            <tbody>
                <?php $i=1; foreach ($credit_report as $credit) { ?>
                <form class="ajax_recon_form">
                <tr>
                    <!--<td><?php // echo $i++; ?></td>-->
                    <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                    <td><?php echo $credit->branch_name ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                    <td><?php echo $credit->customer_fname.' '.$credit->customer_lname; ?></td>
                    <td><?php echo $credit->customer_contact ?> </td>
                    <!--<td><?php // echo $credit->customer_gst ?> </td>-->
                    <td><?php echo $credit->payment_head ?></td>
                    <td><?php echo $credit->payment_mode ?></td>
                    <td><?php echo $credit->transaction_id ?></td>
                    <td><?php echo $credit->amount ?></td>
                    <td><?php echo $credit->transfer_date ?></td>
                    <td><?php echo $credit->received_amount ?></td>
                    <td><?php echo $credit->commission_amt ?></td>
                    <td><?php echo $credit->pending_amt ?></td>
                    <td>
                        <?php $now = strtotime($credit->transfer_date); // or your date as well
                        $credit_date = strtotime($credit->date);
                        $datediff = $now - $credit_date;
                        echo round($datediff / (86400)); ?> 
                    </td>
                    <td><?php echo $credit->bank_name ?></td>
                    <td><?php echo $credit->utr_no ?></td>
                </tr>
                </form>
                <?php } ?>
            </tbody>
    <?php }}
    
    public function ajax_bank_reconciliation_report() {
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
//        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $credit_report = $this->Reconciliation_model->ajax_get_bank_reconciliation_pending_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$branches);
        if(count($credit_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }else{ 
//            $bank_data = $this->General_model->get_active_bank();
//            $payment_devices = $this->Reconciliation_model->get_devices_byidpayment_mode($idpayment_mode); ?>
            <thead class="fixedelement">
                <th>Date</th>
                <th>Branch</th>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>GSTIN</th>
                <th>Mode</th>
                <th>Txn No</th>
                <th>Expected Amount</th>
                <th>Days</th>
            </thead>
            <tbody>
                <?php $i=1; foreach ($credit_report as $credit) { ?>
                <form class="ajax_recon_form">
                <tr>
                    <!--<td><?php // echo $i++; ?></td>-->
                    <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                    <td><?php echo $credit->branch_name ?></td>
                    <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                    <td><?php echo $credit->customer_fname.' '.$credit->customer_lname; ?></td>
                    <td><?php echo $credit->customer_contact ?> </td>
                    <td><?php echo $credit->customer_gst ?> </td>
                    <td><?php echo $credit->payment_mode ?></td>
                    <td><?php echo $credit->transaction_id ?></td>
                    <td><?php echo $credit->amount ?></td>
                    <td><?php $now = time(); // or your date as well
                        $credit_date = strtotime($credit->entry_time);
                        $datediff = $now - $credit_date;
                        echo round($datediff / (86400)); ?> 
                    </td>
                </tr>
                </form>
                <?php } ?>
            </tbody>
    <?php }}
    
    public function ajax_bank_reconciled_report() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $idbank = $this->input->post('idbank');
        // $receonciled_report = $this->Reconciliation_model->ajax_get_bank_reconciled_report_new($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$branches,$idbank);
        $receonciled_report = $this->Reconciliation_model->ajax_get_bank_reconciled_report_new_1($idpayment_mode,$idbranch,$datefrom,$dateto,$branches,$idbank);
//        die(print_r($credit_report));
        if(count($receonciled_report) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }else{ ?>
            <thead class="fixedelement">
                <th>Sr</th>
                <th>Date</th>
                <th>Bank</th>
                <th>Payent Mode</th>
                <th>Branch</th>
                <th>UTR No</th>
                <th>Total Bank Received</th>
                <th>Total Reconciliation received</th>
                <th>Difference</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; $sbank=0;$srecon=0; foreach ($receonciled_report as $receonciled) { ?>
                <form class="ajax_recon_form">
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date('d-m-Y', strtotime($receonciled->transfer_date)); ?></td>
                        <td><?php echo $receonciled->bank_name; ?></td>
                        <td><?php echo $receonciled->payment_mode; ?></td>
                        <td><?php echo $receonciled->branch_name; ?></td>
                        <td><?php echo $receonciled->utr_no; ?></td>
                        <td><?php echo $receonciled->sum_bank_amount; $sbank += $receonciled->sum_bank_amount; ?></td>
                        <td><?php echo $receonciled->sum_received_amount; $srecon += $receonciled->sum_received_amount; ?></td>
                        <td><?php $diff = $receonciled->sum_bank_amount - $receonciled->sum_received_amount; echo $diff; ?></td>
                    </tr>
                </form>
                <?php } ?>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><?php echo $sbank; ?></th>
                        <th><?php echo $srecon; ?></th>
                        <th><?php $sdiff = $sbank - $srecon; echo $sdiff; ?></th>
                    </tfoot>
            </tbody>
    <?php }}
    
    public function receive_payment_reconciliation() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $received_amt=$this->input->post('received_amt');
//        $amount=$this->input->post('amount');
//        $pending = $amount - $received_amt;
        $id = $this->input->post('idreconciliation');
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'payment_receive' => 1,
            'received_entry_time' => $datetime,
            'received_by' => $this->input->post('iduser'),
            'utr_no' => $this->input->post('utr'),
            'received_amount' => $received_amt,
            'commission_amt' => $this->input->post('commission_amt'),
            'commission_per' => $this->input->post('commission_per'),
            'idbank' => $this->input->post('idbank'),
            'transfer_date' => $this->input->post('received_date'),
            'pending_amt' => $this->input->post('short_receive'),
        );
        $this->Reconciliation_model->receive_payment_reconciliation($id, $data);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
       echo json_encode($q);
    }
    
    public function receive_cheque_reconciliation() {
        $received_amt=$this->input->post('received_amt');
        $amount=$this->input->post('amount');
        $pending = $amount - $received_amt;
        $id = $this->input->post('idreconciliation');
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'payment_receive' => 1,
            'received_entry_time' => $datetime,
            'received_by' => $this->input->post('iduser'),
            'utr_no' => $this->input->post('utr'),
            'received_amount' => $received_amt,
            'idbank' => $this->input->post('idbank'),
            'transfer_date' => $this->input->post('received_date'),
            'pending_amt' => $pending,
            'bank_reconciliation' => 1
        );
        if($this->Reconciliation_model->receive_payment_reconciliation($id, $data)){
            $pay_data = array(
                'payment_receive' => 1,
                'received_amount' => $received_amt,
                'received_entry_time' => $datetime,
                'short_receive' => $pending,
                'received_by' => $this->input->post('iduser'),
                'bank_reconciliation' => 1
            );
            $this->Reconciliation_model->update_sale_payment($this->input->post('idsale_payment'), $pay_data);
            echo '1';
        }
    }
    
    public function receive_cheque_bounce_reconciliation() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $received_amt=$this->input->post('received_amt');
        $amount=$this->input->post('amount');
        $id = $this->input->post('idreconciliation');
        $this->Reconciliation_model->delete_payment_reconciliation($id);
        $datetime = date('Y-m-d H:i:s');
        $rec_date = date('Y-m-d');
//        $bank = $this->General_model->get_bank_byid($this->input->post('idbank'));
        $chq_return_charges = $this->input->post('bounce_charges'); //$bank->chq_return_charges;
        $total_cheque_amount = $chq_return_charges + $received_amt;
        $bounce_data = array(
            'bounce_date' => $rec_date,
            'inv_no' => $this->input->post('inv_no'),
            'idcustomer' => $this->input->post('idcustomer'),
            'corporate_sale' => $this->input->post('corporate_sale'),
            'date' => $this->input->post('date'),
            'idbranch' => $this->input->post('idbranch'),
            'idsale' => $this->input->post('idsale'),
            'amount' => $received_amt,
            'bounce_charges' => $chq_return_charges,
            'total_cheque_amount' => $total_cheque_amount,
            'bounce_utr' => $this->input->post('utr'),
            'customer_bank_name' => $this->input->post('customer_bank_name'),
            'idbank' => $this->input->post('idbank'),
            'transaction_id' => $this->input->post('transaction_id'),
            'entry_time' => $datetime,
            'entry_by' => $this->input->post('iduser'),
        );
        if($this->Reconciliation_model->save_cheque_bounce($bounce_data)){
            $data = array(
                'idpayment_head' => 6, // credit
                'idpayment_mode' => 17, // cheque_bounce
                'amount' => $total_cheque_amount,
                'bank_reconciliation' => 1,
                'created_by' => $this->input->post('iduser'),
                'bounce_charges' => $chq_return_charges,
                'received_amount' => 0,
                'approved_by' => 'UTR No. - '.$this->input->post('utr'),
            );
            $this->Reconciliation_model->update_sale_payment($this->input->post('idsale_payment'), $data);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function ajax_bank_reconciliation() {
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'idpayment_mode' => $this->input->post('idpayment_mode'),
            'idbank' => $this->input->post('idbank'),
            'date' => $this->input->post('date'),
            'transaction_id' => $this->input->post('trans_id'),
            'amount' => $this->input->post('amount'),
            'created_by' => $this->input->post('iduser'),
            'entry_time' => $datetime,
        );
        if($this->Reconciliation_model->submit_bank_reconciliation($data)){
            $q['result'] = 'Success';
        }else{
            $q['result'] = 'Failed';
        }
        echo json_encode($q);
    }
    
    public function ajax_cash_reconciliation() {
        $datetime = date('Y-m-d H:i:s');
        $idreconciliation = $this->input->post('idreconciliation');
        $received_amt = $this->input->post('received_amt');
        $amount = $this->input->post('amount');
        $short = $amount - $received_amt;
        $data = array(
            'received_utr' => $this->input->post('utr'),
            'received_date' => $this->input->post('received_date'),
            'received_amount' => $received_amt,
            'short_receive' => $short,
            'received_by' => $this->input->post('iduser'),
            'reconciliation_status' => 1,
            'received_datetime' => $datetime,
        );
        if($this->Reconciliation_model->submit_cash_reconciliation($data, $idreconciliation)){
            $q['result'] = 'Success';
        }else{
            $q['result'] = 'Failed';
        }
        echo json_encode($q);
    }
    
    public function ajax_payment_reconciled_report() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_head = $this->input->post('idpayment_head');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $credit_report = $this->Reconciliation_model->ajax_get_received_payment_reconciliation_report($idpayment_head,$idpayment_mode,$idbranch); ?>
        <thead>
            <th>Sr</th>
            <th>Date time</th>
            <th>Reconcilication date</th>
            <th>Branch</th>
            <th>Invoice No</th>
            <th>Mode</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>Total Amount</th>
            <th>Txn No</th>
            <th>Receive</th>
            <th>Pending</th>
            <th>Commission</th>
            <th>Comm%</th>
            <th>Bank</th>
            <th>UTR</th>
            <th>Received date</th>
        </thead>
        <tbody>
            <?php $i=1; foreach ($credit_report as $credit) { ?>
            <tr <?php if($credit->pending_amt){ ?> class="danger" <?php } ?>>
                <td><?php echo $i++; ?></td>
                <td><?php echo date('d-m-Y', strtotime($credit->date)) ?></td>
                <td><?php echo date('d-m-Y', strtotime($credit->received_entry_time)) ?></td>
                <td><?php echo $credit->branch_name ?> </td>
                <td><a href="<?php echo base_url('Sale/sale_details/'.$credit->idsale) ?>" target="_blank" style="color: #3333ff"><?php echo $credit->inv_no ?></a></td>
                <td><?php echo $credit->customer_name ?> </td>
                <td><?php echo $credit->customer_contact ?> </td>
                <td><?php echo $credit->payment_mode ?> </td>
                <td><?php echo $credit->amount ?> </td>
                <td><?php echo $credit->transaction_id ?> </td>
                <td><?php echo $credit->received_amount ?></td>
                <td><?php echo $credit->pending_amt ?></td>
                <td><?php echo $credit->commission_amt ?></td>
                <td><?php echo $credit->commission_per ?></td>
                <td><?php echo $credit->bank_name ?></td>
                <td><?php echo $credit->utr_no ?></td>
                <td><?php echo $credit->transfer_date ?></td>
            </tr>
            <?php } ?>
        </tbody>
    <?php }
    public function ajax_bank_received_list() {
        $idbranch = $this->input->post('idbranch');
        $idpayment_mode = $this->input->post('idpayment_mode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $modes = $this->input->post('modes');
        $branches = $this->input->post('branches');
        $bank_received = $this->Reconciliation_model->ajax_get_bank_received_list($idpayment_mode,$datefrom,$dateto,$modes);
//        $bank_received = $this->Reconciliation_model->ajax_get_bank_received_list($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$branches);
        if(count($bank_received) == 0){
            echo '<center><h3><i class="mdi mdi-alert"></i> Entries Not Found.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }else{ ?>
            <thead class="fixedelementtop">
                <th>Date</th>
                <th>Bank</th>
                <th>Payment Mode</th>
                <th>Txn No</th>
                <th>Amount</th>
                <th>Entry by</th>
                <th>Entry Time</th>
            </thead>
            <tbody class="data_1">
                <?php foreach ($bank_received as $received) { ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($received->date)) ?></td>
                    <td><?php echo $received->bank_name ?></td>
                    <td><?php echo $received->payment_mode.' '.$received->payment_head ?></td>
                    <td><?php echo $received->transaction_id ?></td>
                    <td><?php echo $received->amount ?></td>
                    <td><?php echo $received->user_name ?></td>
                    <td><?php echo $received->entry_time ?></td>
                </tr>
                <?php } ?>
            </tbody>
    <?php }}
    
    public function insurance_recon() {
        $q['tab_active'] = '';
        $q['insurance_pending_recon'] = $this->Reconciliation_model->get_insurance_pending_recon_bystatus(0);
        $sale_type = array(1,2);
        $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
        $this->load->view('reconciliation/insurance_recon', $q);
    }
    public function insurance_reconciliation_report() {
        $q['tab_active'] = '';
        $sale_type = array(1,2);
        $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
        $this->load->view('reconciliation/insurance_reconciliation_report', $q);
    }
    
    public function ajax_insurance_reconciliation() {
//        die(print_r($_POST));
        $date = date('Y-m-d');
        $idvariant = $this->input->post('idvariant');
        $trans_id = $this->input->post('trans_id');
        $imei = $this->input->post('imei');
        $sale_recon = 0;
        $res = $this->Reconciliation_model->get_verify_insurance_pending_entry($idvariant, $trans_id, $imei, $sale_recon);
        if(count($res) > 0){
            $data = array(
                'sale_recon_date' => $date,
                'sale_recon_amount' => $this->input->post('amount'),
                'sale_recon_by' => $this->input->post('iduser'),
                'sale_recon' => 1,
            );
            if($this->Reconciliation_model->sale_insurance_recon_byid($res->id_insurance_reconciliation, $data)){
                $q['result'] = 'Success';
                $q['row_id'] = $idvariant.$trans_id.$imei;
            }else{
                $q['result'] = 'Failed';
                $q['row_id'] = 0;
            }
        }else{
            $q['result'] = 'NotFound';
            $q['row_id'] = 0;
        }
        echo json_encode($q);
    }
    public function ajax_insurance_reconciliation_byid() {
        $date = date('Y-m-d');
        $idrecon = $this->input->post('idrecon');
        $data = array(
            'sale_recon_date' => $date,
            'sale_recon_amount' => $this->input->post('received_amt'),
            'sale_recon_by' => $this->input->post('iduser'),
            'sale_recon' => 1,
        );
        if($this->Reconciliation_model->sale_insurance_recon_byid($idrecon, $data)){
            $q['result'] = 'Success';
        }else{
            $q['result'] = 'Failed';
        }
        echo json_encode($q);
    }
    public function ajax_get_insurance_pending_reconciliation() {
        $idvariant = $this->input->post('idvariant');
        $insurance_pending_recon = $this->Reconciliation_model->get_insurance_pending_recon_byvariant_status($idvariant,0);
        foreach ($insurance_pending_recon as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td>
                    <input type="number" class="form-control input-sm received_amt" placeholder="Enter Amount" step="0.001" min="1" />
                    <input type="hidden" class="actv_code" value="<?php echo $recon->activation_code ?>" />
                </td>
                <td>
                    <button class="btn btn-primary btn-sm insurance_reconciliation_btn" value="<?php echo $recon->id_insurance_reconciliation ?>" style="margin: 0">Receive</button>
                </td>
            </tr>
        <?php }
    }
    public function get_insurance_recon_bystatus_date() {
        $idvariant = $this->input->post('idvariant');
        $status = 1;
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $sale_recon_by = 'ir.sale_recon_by';
        $insurance_recon_data = $this->Reconciliation_model->get_insurance_recon_bystatus_date($idvariant, $status, $datefrom, $dateto, $sale_recon_by); ?>
        <thead class="fixedelement">
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice</th>
            <th>Product</th>
            <th>Activation code</th>
            <th>IMEI</th>
            <th>Amount</th>
            <th>Received Amount</th>
            <th>Short Receive</th>
            <th>Received by</th>
            <th>Received Date</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($insurance_recon_data as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->branch_name ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td><?php echo $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->total_amount - $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->user_name ?></td>
                <td><?php echo $recon->sale_recon_date ?></td>
            </tr>
            <?php } ?>
        </tbody> <?php 
    }
    /******Purchase reconciliation*******/
    public function insurance_purchase_recon() {
        $q['tab_active'] = '';
        $q['insurance_pending_recon'] = $this->Reconciliation_model->get_insurance_pending_recon_bystatus(1);
        $sale_type = array(1,2);
        $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
        $this->load->view('reconciliation/insurance_purchase_recon', $q);
    }
    public function ajax_get_insurance_pending_purchase_recon() {
        $idvariant = $this->input->post('idvariant');
        $insurance_pending_recon = $this->Reconciliation_model->get_insurance_pending_recon_byvariant_status($idvariant,1);
        foreach ($insurance_pending_recon as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td><?php echo $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->total_amount - $recon->sale_recon_amount ?></td>
                <td><?php echo $recon->user_name ?></td>
                <td><?php echo $recon->sale_recon_date ?></td>
            </tr>
        <?php }
    }
    public function submit_insurance_purchase_recon() {
        $filename = $_FILES['fileupload']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'csv') {
            $this->db->trans_begin();
            $idvariant = $this->input->post('idvariant');
            $date = date('Y-m-d');
            $filename=$_FILES['fileupload']["tmp_name"];
            if($_FILES['fileupload']["size"] > 0){
                $file = fopen($filename, "r");
                $cnt=0; $filecnt=0;
                while (($ins_data = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if($ins_data[0] != ''){
                        $sale_recon = 1;
                        $res = $this->Reconciliation_model->get_verify_insurance_pending_entry($idvariant, $ins_data[0], $ins_data[1], $sale_recon);
                        if(count($res) > 0){
                            $data = array(
                                'purchase_recon_date' => $date,
                                'purchase_recon_amount' => $ins_data[2],
                                'purchase_recon_by' => $this->input->post('iduser'),
                                'sale_recon' => 2, // purchase recon
                            );
                            $this->Reconciliation_model->sale_insurance_recon_byid($res[0]->id_insurance_reconciliation, $data);
                            $cnt++;
                        }
                        $filecnt++;
                    }
                }
            }
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                die('Failed to reconcile entry! Please try again');
            }else{
                $this->db->trans_commit();
                $this->session->set_flashdata('save_data', $cnt.' No of rows updated out of '.$filecnt);
                return redirect('Reconciliation/insurance_purchase_recon');
            }
        }else{
            die('Please check file format! Only allowed to upload CSV file');
        }
    }
    public function insurance_purchase_recon_report() {
        $q['tab_active'] = '';
        $sale_type = array(1,2);
        $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
        $this->load->view('reconciliation/ins_purchase_recon_report', $q);
    }
    public function get_insurance_purchase_recon_report() {
        $idvariant = $this->input->post('idvariant');
        $status = 2;
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $sale_recon_by = 'ir.purchase_recon_by';
        $insurance_recon_data = $this->Reconciliation_model->get_insurance_recon_bystatus_date($idvariant, $status, $datefrom, $dateto, $sale_recon_by); ?>
        <thead class="fixedelement">
            <th>Date</th>
            <th>Branch</th>
            <th>Invoice</th>
            <th>Product</th>
            <th>Activation code</th>
            <th>IMEI</th>
            <th>Amount</th>
            <th>Purchase Amount</th>
            <th>Short Receive</th>
            <th>Received by</th>
            <th>Received Date</th>
        </thead>
        <tbody class="data_1">
            <?php foreach ($insurance_recon_data as $recon){ ?>
            <tr class="<?php echo 'row_'.$recon->idvariant.$recon->activation_code.$recon->insurance_imei_no; ?>">
                <td><?php echo $recon->date ?></td>
                <td><?php echo $recon->branch_name ?></td>
                <td><?php echo $recon->inv_no ?></td>
                <td><?php echo $recon->product_name ?></td>
                <td><?php echo $recon->activation_code ?></td>
                <td><?php echo $recon->insurance_imei_no ?></td>
                <td><?php echo $recon->total_amount ?></td>
                <td><?php echo $recon->purchase_recon_amount ?></td>
                <td><?php echo $recon->total_amount - $recon->purchase_recon_amount ?></td>
                <td><?php echo $recon->user_name ?></td>
                <td><?php echo $recon->purchase_recon_date ?></td>
            </tr>
            <?php } ?>
        </tbody> <?php 
    }
    /*******DOA Process*******/
    public function doa_stock() {
        $q['tab_active'] = '';
        $q['brand_data'] = $this->General_model->get_active_brand_data();          
        $this->load->view('reconciliation/doa_stock', $q);
    }
    public function ajax_get_doa_stock() {
        $brand = $this->input->post('brand');
        $idwarehouse = $this->input->post('idwarehouse');
        $service_stock = $this->Reconciliation_model->ajax_get_doa_stock_bybrand_status($brand,$idwarehouse);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>DOA Date</th>
                    <th>DOA IMEI</th>
                    <th>Product name</th>
                    <th>DOA ID</th>
                    <th>DOA Letter</th>
                    <th>Send to Vendor</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php } ?>
                        </td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->doa_date)) ?></td>
                        <td><?php echo $stock->doa_imei; ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->doa_id ?></td>
                        <td>
                            <a class="waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa; padding: 5px;"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                        </td>
                        <td>
                            <?php if($this->session->userdata('idrole') == 36){ ?>
                            <input type="hidden" name="idvariant[<?php echo $stock->id_doa_stock; ?>]" id="idvariant" value="<?php echo $stock->idvariant; ?>">
                            <input type="hidden" name="imei[<?php echo $stock->id_doa_stock; ?>]" id="imei" value="<?php echo $stock->doa_imei; ?>">
                            <input type="hidden" name="last_purchase_price[<?php echo $stock->id_doa_stock; ?>]" id="last_purchase_price" value="<?php echo $stock->last_purchase_price; ?>">
                            <center>
                                <label class="form-check-label btn btn-sm" for="checkrow<?php echo $stock->id_doa_stock ?>">
                                    <input class="hide_checkbox sel_product" type="checkbox" name="checkrow[]" id="checkrow<?php echo $stock->id_doa_stock ?>" value="<?php echo $stock->id_doa_stock; ?>">
                                    Send to Vendor
                                </label>
                            </center>
                            <?php }else{ ?>
                            Pending
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php } ?>
        <?php 
    }
    public function ajax_open_service_send_to_vendor_form() {
        $dispatch_data = $this->General_model->get_dispatch_type();
        $transport_vendor = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
        $vendor_data = $this->General_model->get_active_vendor_data(); ?>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info" style="box-shadow: 4px 4px 10px rgba(0, 51, 153, 0.3);padding-bottom: 0;">
                <div class="panel-body" style="min-width: 750px">
                    <h4><center style="color: #003399; margin-bottom: 10px"><i class="fa fa-send"></i> DOA Stock - Send to Vendor</center></h4>
                    <div class="thumbnail">            
                        <div class="col-md-2">Dispatch Type</div>
                        <div class="col-md-4">
                            <select class="select form-control iddispatchtype" required="" name="iddispatchtype" id="iddispatchtype" onchange="$('#dispatch_type').val($('#iddispatchtype option:selected').text());" >
                                <option value="">Select Type</option>
                                <?php foreach ($dispatch_data as $dispatch){ ?>
                                <option value="<?php echo $dispatch->id_dispatch_type ?>"><?php echo $dispatch->dispatch_type?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" id="dispatch_type" name="dispatch_type">
                        </div>
                        <div class="col-md-3">Dispatch Date</div>
                        <div class="col-md-3"><input type="text" class="form-control" name="dispatch_date" value="<?php echo date('Y-m-d') ?>" readonly=""/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Courier/ Transport</div>
                        <div class="col-md-4">
                            <select class="select form-control idtvendors" required="" name="idtvendors" id="idtvendors" onchange="$('#courier_name').val($('#idtvendors option:selected').text());">
                                <option value="">Select Transport Vendor</option>
                                <?php foreach ($transport_vendor as $tvendors){ ?>
                                <option value="<?php echo $tvendors->id_transport_vendor ?>"><?php echo $tvendors->transport_vendor_name?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" id="courier_name" name="courier_name"/>
                        </div>
                        <div class="col-md-3">POP/LR Number</div>
                        <div class="col-md-3"><input type="text" class="form-control" id="po_lr_no" name="po_lr_no" placeholder="Enter POP/LR Number"/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Send To</div>
                        <div class="col-md-4">
                            <select class="chosen-select form-control input-sm" name="idvendor" required="" >
                                <option value="">Select Vendor</option>
                                <?php foreach ($vendor_data as $vendor) { ?>
                                    <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">No of Boxes</div>
                        <div class="col-md-3"><input type="text" class="form-control" id="no_of_boxes" name="no_of_boxes" placeholder="No of Boxes" required=""/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Remark</div>
                        <div class="col-md-10"><input type="text" class="form-control" required="" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                    </div>
                    <a class="btn btn-warning" onclick="if(confirm('Do you want to cancel')){ $('#doa_send_to_vendor_form').html(''); $('#send_to_vendor_form_open').show(); }">Cancel</a>
                    <button class="btn btn-primary pull-right" type="submit" formmethod="POST" id="save_doa_send_to_vendor" formaction="<?php echo base_url('Reconciliation/save_doa_send_to_vendor')?>">Send <span class="fa fa-send"></span></button>
                </div>
            </div>
        </div><div class="clearfix"></div>
    <?php 
    }
    
    public function save_doa_send_to_vendor() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $checkrow = $this->input->post('checkrow');
        $idvariant = $this->input->post('idvariant');
        $imei = $this->input->post('imei');
        $dispatch_date = $this->input->post('dispatch_date');
        $idwarehouse = $this->input->post('idwarehouse');
        $entry_by = $this->input->post('entry_by');
        $last_purchase_price = $this->input->post('last_purchase_price');
        $count = count($checkrow);
        $datetime = date('Y-m-d H:i:s');
        $doa_trasf = array(
            'transfer_from' => $idwarehouse,
            'total_product' => $count,
            'entry_time' => $datetime,
            'created_by' => $entry_by,
            'status' => 0,
            'dispatch_date' => $dispatch_date,
            'idvendor' => $this->input->post('idvendor'),
            'iddispatch_type' => $this->input->post('iddispatchtype'),
            'dispatch_type' => $this->input->post('dispatch_type'),
            'courier_name' => $this->input->post('courier_name'),
            'idtransport_vendor' => $this->input->post('idtvendors'),
            'po_lr_no' => $this->input->post('po_lr_no'),
            'no_of_boxes' => $this->input->post('no_of_boxes'),
            'shipment_remark' => $this->input->post('shipment_remark'),
        );
        $id_doatrasf = $this->Reconciliation_model->save_doa_to_vendor($doa_trasf);
        $imei_history = [];
        $update_doa = [];
        for($i=0; $i < $count; $i++){
            $row_val = $checkrow[$i];
            $imei_history[] = array(
                'imei_no' => $imei[$row_val],
                'entry_type' => 'Service - DOA Send Vednor',
                'entry_time' => $datetime,
                'date' => $dispatch_date,
                'idbranch' => $idwarehouse,
                'transfer_from' => $idwarehouse,
                'idgodown' => 4,
                'iduser' => $entry_by,
                'idvariant' => $idvariant[$row_val],
                'idimei_details_link' => 16, // Sales Return from imei_details_link table
                'idlink' => $row_val,
            );
            $update_doa[] = array(
                'imei_no' => $imei[$row_val],
                'idvariant' => $idvariant[$row_val],
                'iddoa_stock_shipment' => $id_doatrasf,
                'id_doa_stock' => $row_val,
                'idvendor' => $this->input->post('idvendor'),
                'last_purchaseprice' => $last_purchase_price[$row_val],
                'status' => 2,
            );
        }
//        die(print_r($update_doa));
        if(count($update_doa) > 0){
            $this->Reconciliation_model->update_batch_doa_reconciliation_byid($update_doa);
        }
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to send. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'DOA send to vendor Successfully');
        }
        return redirect('Reconciliation/dc_doa_stock_send_to_vendor/'.$id_doatrasf);
    }
    public function dc_doa_stock_send_to_vendor($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Reconciliation_model->doa_stock_dc($id);
        $this->load->view('Reconciliation/dc_doa_stock_send_to_vendor', $q);
    }
    public function doa_reconciliation() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('reconciliation/doa_reconciliation', $q);
    }
    public function ajax_get_doa_stock_for_recon() {
        $idvendor = $this->input->post('idvendor');
        $idbrand = $this->input->post('idbrand');
        $service_stock = $this->Reconciliation_model->ajax_get_doa_stock_for_recon($idvendor,$idbrand);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>DOA Date</th>
                    <th>DOA IMEI</th>
                    <th>Product name</th>
                    <th>Purcahse Price</th>
                    <th>DOA ID</th>
                    <th>DOA Letter</th>
                    <th>Vendor</th>
                    <th>Dispatch Date</th>
                    <th>Claim</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php } ?>
                        </td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->doa_date)) ?></td>
                        <td><?php echo $stock->doa_imei; ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->last_purchaseprice; ?></td>
                        <td><?php echo $stock->doa_id ?></td>
                        <td>
                            <a class="waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa; padding: 5px;"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                        </td>
                        <td><?php echo $stock->vendor_name ?></td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->dispatch_date)) ?></td>
                        <td>
                            <?php if($this->session->userdata('idrole') == 39){ // excecutive ?> 
                                <center><a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $stock->id_doa_stock ?>" style="margin: 0">
                                    <span class="mdi mdi-login text-info fa-lg"></span> Receive Handset
                                </a></center>
                                <div class="modal fade" id="edit<?php echo $stock->id_doa_stock ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <center><h4>DOA - Replacement Product From Vendor </h4></center><hr>
                                                <?php // echo $stock->id_doa_stock; ?>
                                                <div class="action_form" style="line-height: 25px">
                                                    <div class="thumbnail center-block" style="padding: 25px;text-align: center">
                                                        <h3>Do you have received replacement handset?</h3>
                                                        <a class="thumbnail btn-warning waves-effect btn_handset" style="margin: 0">
                                                            <span class="mdi mdi-login fa-lg"></span> Proceed to Receive Handset
                                                        </a>
                                                    </div>
                                                    <div class="new_handset_block"></div>
                                                    <input type="hidden" class="doa_idvariant" id="doa_idvariant" value="<?php echo $stock->idvariant; ?>">
                                                    <input type="hidden" id="doa_imei" class="doa_imei" value="<?php echo $stock->doa_imei; ?>">
                                                    <input type="hidden" id="id_doa_stock" class="id_doa_stock" value="<?php echo $stock->id_doa_stock; ?>">
                                                    <input type="hidden" id="idservice" class="idservice" value="<?php echo $stock->idservice ?>"/>
                                                    <input type="hidden" id="idwarehouse" value="<?php echo $stock->idbranch ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }elseif($this->session->userdata('idrole') == 10){ // purchase manager // CN ?> 
                                <center><a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#receivecn<?php echo $stock->id_doa_stock ?>" style="margin: 0">
                                    <span class="mdi mdi-login text-info fa-lg"></span> Receive CN
                                </a></center>
                                <div class="modal fade" id="receivecn<?php echo $stock->id_doa_stock ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <center><h4><i class="mdi mdi-note-text"></i> Credit Note From Vendor </h4></center><hr>
                                                <?php // echo $stock->id_doa_stock; ?>
                                                <div class="action_form" style="line-height: 25px">
                                                    <div class="col-md-4" style="padding: 2px">Enter CN NO</div>
                                                    <div class="col-md-8" style="padding: 2px">
                                                        <input type="text" class="form-control cn_no" id="cn_no" name="cn_no" placeholder="Enter CN"/>
                                                    </div><div class="clearfix"></div><hr>
                                                    <div class="col-md-4" style="padding: 2px">Last Purchase Price</div>
                                                    <div class="col-md-8" style="padding: 2px">
                                                        <h4><?php echo $stock->last_purchaseprice; ?></h4>
                                                    </div><div class="clearfix"></div><hr>
                                                    <div class="col-md-4" style="padding: 2px">Enter CN Amount</div>
                                                    <div class="col-md-8" style="padding: 2px">
                                                        <input type="text" class="form-control cn_amount" id="cn_amount" name="cn_amount" placeholder="Enter CN Amount"/>
                                                    </div><div class="clearfix"></div><hr>
                                                    <div class="pull-right col-md-4">
                                                        <button class="btn btn-primary" id="save_receive_cn" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);" value="">Receive CN</button>
                                                    </div><div class="clearfix"></div>
                                                    <input type="hidden" class="doa_idvariant" id="doa_idvariant" value="<?php echo $stock->idvariant; ?>">
                                                    <input type="hidden" id="doa_imei" class="doa_imei" value="<?php echo $stock->doa_imei; ?>">
                                                    <input type="hidden" id="id_doa_stock" class="id_doa_stock" value="<?php echo $stock->id_doa_stock; ?>">
                                                    <input type="hidden" id="idservice" class="idservice" value="<?php echo $stock->idservice ?>"/>
                                                    <input type="hidden" id="idwarehouse" value="<?php echo $stock->idbranch ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                Pending
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
    }
    public function ajax_get_receive_block() {
        $model_variant = $this->General_model->get_model_variant_data();
        if($this->input->post('confirm_type')){ ?>
            <div class="col-md-3" style="padding: 2px">Select Model</div>
            <div class="col-md-9" style="padding: 2px">
                <select class="chosen-select form-control idmodelvariant" name="idmodelvariant" id="idmodelvariant" required="" onchange="$(this).closest('div').find('.new_brand').val($('option:selected', this).attr('idbrand'));">
                    <option value="">Select Model</option><?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>" idbrand="<?php echo $variant->idbrand; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option><?php } ?>
                </select>
                <input type="hidden" class="form-control input-sm new_brand" id="new_brand" />
            </div><div class="clearfix"></div><br>
            <div class="col-md-3" style="padding: 2px">Enter IMEI/SRNO</div>
            <div class="col-md-9" style="padding: 2px">
                <input type="text" class="form-control new_imei" id="new_imei" name="new_imei" placeholder="Enter New imei/srno"/>
                <input type="hidden" class="form-control input-sm verified_imei" id="verified_imei" name="verified_imei" />
            </div><div class="clearfix"></div><hr>
            <div class="pull-right col-md-4">
                <button class="btn btn-primary" id="save_receive_handset" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);" value="">Receive Handset</button>
            </div><div class="clearfix"></div>
        <?php }
    }
    public function submit_receive_handset_against_letter() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Service_model');
        $this->load->model('Purchase_model');
        $this->db->trans_begin();
        $doa_idvariant = $this->input->post('doa_idvariant');
        $doa_imei = $this->input->post('doa_imei');
        $id_doa_stock = $this->input->post('id_doa_stock');
        $new_imei = $this->input->post('new_imei');
        $new_variant = $this->input->post('new_variant');
        $entry_by = $this->input->post('entry_by');
        $idwarehouse = $this->input->post('idwarehouse');
        $idservice = $this->input->post('idservice');
        $idbrand = $this->input->post('new_brand');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $inward_request = array(
            'date' => $date,
            'entry_time' => $datetime,
            'imei_no' => $new_imei,
            'idgodown' => 1,
            'idbranch' => $idwarehouse,
            'created_by' => $entry_by,
            'status' => 1,
            'doa_imei' => $doa_imei,
            'idvariant' => $new_variant,
            'replaced_imei' => $new_imei,
            'idservice' => $idservice,
            'idbrand' => $idbrand,
            'inward_against_letter' => 1,
        );
        $iddoainward=$this->Service_model->save_doa_inward($inward_request);

        $imei_history[] = array(
            'imei_no' => $doa_imei,
            'entry_type' => 'DOA Letter Closed Against Replacement Handset and removed doa product',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idwarehouse,
            'idgodown' => 4,
            'iduser' => $entry_by,
            'idvariant' => $doa_idvariant,
            'idimei_details_link' => 16, // Service
            'idlink' => $idservice,
        );
        $this->General_model->save_batch_imei_history($imei_history);
        $update_doa[] = array(
            'id_doa_stock' => $id_doa_stock,
            'status' => 1,
            'iddoainward' => $iddoainward,
            'cn_imei' => $new_imei,
            'closure_by' => $entry_by,
            'closure_date' => $date,
            'closure_type' => 1,
        );
        $this->Reconciliation_model->update_batch_doa_reconciliation_byid($update_doa);
        $this->Sale_model->delete_stock_byimei($doa_imei);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function submit_receive_cn_against_letter() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Service_model');
        $this->db->trans_begin();
        $doa_idvariant = $this->input->post('doa_idvariant');
        $doa_imei = $this->input->post('doa_imei');
        $id_doa_stock = $this->input->post('id_doa_stock');
        $cn_no = $this->input->post('cn_no');
        $cn_amount = $this->input->post('cn_amount');
        $entry_by = $this->input->post('entry_by');
        $idwarehouse = $this->input->post('idwarehouse');
        $idservice = $this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        
        $imei_history[] = array(
            'imei_no' => $doa_imei,
            'entry_type' => 'DOA Letter Closed Against CN',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idwarehouse,
            'idgodown' => 4,
            'iduser' => $entry_by,
            'idvariant' => $doa_idvariant,
            'idimei_details_link' => 16, // Service
            'idlink' => $idservice,
        );
        $this->General_model->save_batch_imei_history($imei_history);
        $update_doa[] = array(
            'id_doa_stock' => $id_doa_stock,
            'status' => 1,
//            'iddoainward' => $iddoainward,
            'cn_imei' => $cn_no,
            'cn_amount' => $cn_amount,
            'closure_by' => $entry_by,
            'closure_date' => $date,
            'closure_type' => 0,
        );
        $this->Reconciliation_model->update_batch_doa_reconciliation_byid($update_doa);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    public function check_if_entry_receive() {
        $id_doa_stock = $this->input->post('id_doa_stock');
        $res = $this->Reconciliation_model->check_doa_reconc_ornot($id_doa_stock);
        if (count($res)){
            $q['result'] = 'Yes';
        }else{
            $q['result'] = 'No';
        }
        echo json_encode($q);
    }
    public function doa_closure_report() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('reconciliation/doa_closure_report', $q);
    }
    public function ajax_doa_closure_data() {
        $idvendor = $this->input->post('idvendor');
        $idbrand = $this->input->post('idbrand');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $service_stock = $this->Reconciliation_model->ajax_get_doa_closure_report($idvendor,$idbrand,$datefrom,$dateto);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>DOA Date</th>
                    <th>DOA IMEI</th>
                    <th>DOA Product</th>
                    <th>Purcahse Price</th>
                    <th>DOA ID</th>
                    <th>DOA Letter</th>
                    <th>Vendor</th>
                    <th>Dispatch Date</th>
                    <th>Received Date</th>
                    <th>Received</th>
                    <th>IMEI/CN</th>
                    <th>CN Amount</th>
                    <th>Short Amount</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php } ?>
                        </td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->doa_date)) ?></td>
                        <td><?php echo $stock->doa_imei; ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->last_purchaseprice; ?></td>
                        <td><?php echo $stock->doa_id ?></td>
                        <td>
                            <a class="waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa; padding: 5px;"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                        </td>
                        <td><?php echo $stock->vendor_name ?></td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->dispatch_date)) ?></td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->closure_date)) ?></td>
                        <td><?php if($stock->closure_type){ echo 'Handset'; }else{ echo 'CN'; } ?> </td>
                        <td><?php echo $stock->cn_imei ?></td>
                        <td><?php echo $stock->cn_amount ?></td>
                        <td><?php if(!$stock->closure_type){ echo $stock->last_purchaseprice - $stock->cn_amount; }else{ echo '0'; } ?></td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center><h4>Records Not Found</h4></center>
        <?php }
    }
    public function pending_doa_inward() {
        $q['tab_active'] = '';
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $this->load->view('reconciliation/pending_doa_inward', $q);
    }
    public function ajax_pending_doa_inward() {
        $idvendor = $this->input->post('idvendor');
        $idbrand = $this->input->post('idbrand');
//        $closure_type = 1; // handset
        $service_stock = $this->Reconciliation_model->ajax_get_doa_inward_report($idvendor,$idbrand);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>DOA Date</th>
                    <th>DOA Product</th>
                    <th>DOA IMEI</th>
                    <th>DOA ID</th>
                    <th>DOA Letter</th>
                    <th>Vendor</th>
                    <th>Received Date</th>
                    <th>New Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Inward</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->idservice) ?>" class="btn btn-sm btn-block waves-effect">Case/<?php echo $stock->idservice ?> <i class="fa fa-info-circle fa-lg pull-right"></i></a>
                            <?php } ?>
                        </td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->doa_date)) ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->doa_imei; ?></td>
                        <td><?php echo $stock->doa_id ?></td>
                        <td>
                            <a class="waves-effect waves-block thumbnail text-center" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa; padding: 5px;"><i class="pe pe-7s-note2 fa-lg"></i> View Letter</a>
                        </td>
                        <td><?php echo $stock->vendor_name ?></td>
                        <td>'<?php echo date('d-m-Y',strtotime($stock->closure_date)) ?></td>
                        <td><?php echo $stock->new_product ?></td>
                        <td><?php echo $stock->cn_imei ?></td>
                        <td>
                            <?php if($this->session->userdata('idrole') == 12){ // intaker ?> 
                                <button class="btn btn-sm btn-primary waves-effect inward_new_handset" value="<?php echo $stock->iddoainward ?>" style="margin: 0">
                                    <span class="mdi mdi-login fa-lg"></span> Inward
                                </button>
                                <input type="hidden" class="id_doa_stock" value="<?php echo $stock->id_doa_stock; ?>" />
                                <input type="hidden" class="idservice" value="<?php echo $stock->idservice ?>" />
                                <input type="hidden" class="imei_no" value="<?php echo $stock->cn_imei ?>" />
                                <input type="hidden" class="idvariant" value="<?php echo $stock->idvariant ?>" />
                                <input type="hidden" class="idbrand" value="<?php echo $stock->new_idbrand ?>" />
                                <input type="hidden" class="idwarehouse" value="<?php echo $stock->idbranch ?>" />
                                <input type="hidden" class="idmodel" value="<?php echo $stock->idmodel ?>" />
                                <input type="hidden" class="product_name" value="<?php echo $stock->new_product ?>" />
                                <input type="hidden" class="idvendor" value="<?php echo $stock->idvendor ?>" />
                                <input type="hidden" class="idsku_type" value="<?php echo $stock->nidsku_type ?>" />
                                <input type="hidden" class="idcategory" value="<?php echo $stock->nidcategory ?>" />
                                <input type="hidden" class="idproductcategory" value="<?php echo $stock->nidproductcategory ?>" />
                            <?php }else{ ?>Pending<?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center><h4>Records Not Found</h4></center>
        <?php }
    }
    public function save_inward_new_handset() {
        $this->db->trans_begin();
//        die(print_r($_POST));
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iddoainward = $this->input->post('iddoainward');
        $imei_no = $this->input->post('imei_no');
        $idvariant = $this->input->post('idvariant');
        $entry_by = $this->input->post('entry_by');
        $idsku_type = $this->input->post('idsku_type');
        $idwarehouse = $this->input->post('idwarehouse');
        $idmodel = $this->input->post('idmodel');
        $idproductcategory = $this->input->post('idproductcategory');
        $idcategory = $this->input->post('idcategory');
        $product_name = $this->input->post('product_name');
        $idbrand = $this->input->post('idbrand');
        $idservice = $this->input->post('idservice');
        $idvendor = $this->input->post('idvendor');
        // save stock
        $inward_stock = array(
            'date' => $date,
            'imei_no' => $imei_no,
            'idmodel' => $idmodel,
            'created_by' => $entry_by, 
            'idvariant' => $idvariant,
            'product_name'=> $product_name,
            'idskutype' => $idsku_type,
            'idproductcategory' => $idproductcategory,
            'idcategory' => $idcategory,
            'idbrand' => $idbrand,
            'idgodown' => 1,
            'idvendor' => $idvendor,
            'idbranch' => $idwarehouse
        );
        $this->load->model('Inward_model');
        $this->Inward_model->save_stock($inward_stock);
        $imei_history[] = array(
            'imei_no' => $imei_no,
            'entry_type' => 'Inward against DOA Letter',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idwarehouse,
            'idgodown' => 1,
            'iduser' => $entry_by,
            'idvariant' => $idvariant,
            'idimei_details_link' => 16, // Service
            'idlink' => $idservice,
        );
        $this->General_model->save_batch_imei_history($imei_history);
        $doa_in = array(
            'inward_against_letter' => 2,
        );
        $this->Reconciliation_model->update_doa_inward($iddoainward, $doa_in);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
}