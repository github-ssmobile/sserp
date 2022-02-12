<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Report_model');
        $this->load->model('Sale_model');
        $this->load->model('Reconciliation_model');
    }
    public function index(){
        $q['tab_active'] = 'Payment';
        $this->load->view('payment', $q);
    }
    public function cash_closure(){
        $date = date('Y-m-d');
        $idbranch = $this->session->userdata('idbranch');
        $q['tab_active'] = 'Payment';
        $q['denomination'] = $this->General_model->get_active_denomination();
//        $q['bank_data'] = $this->General_model->get_active_bank();
        $q['total_daybook_cash'] = $this->Report_model->get_sum_daybook_cash_byidbranch_lastdate($idbranch, $date);
        // todays cash group by entry type
        $q['todays_cash'] = $this->Report_model->get_todays_daybooksum_byidbranch_groupby_entrytype($idbranch, $date); 
        $q['todays_cash_closure'] = $this->Reconciliation_model->get_todays_cash_closure_byidbranch($idbranch); // cash closure data
        $this->load->model('Sale_model');
        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_data'] = $this->Reconciliation_model->get_cash_closure_data_byidbranch($idbranch); // cash closure data
//        $q['daybook_cash_sum'] = $this->Sale_model->get_daybook_cash_sum_byid($idbranch); // cash closure data
        $this->load->view('payment/cash_closure', $q);
    }
    
    public function deposit_to_bank(){
        $idbranch = $this->session->userdata('idbranch');
        $q['tab_active'] = 'Payment';
        $q['bank_data'] = $this->General_model->get_active_bank();
        $q['sum_cash_closure'] = $this->Reconciliation_model->get_sum_cash_closure_bystatus_idbranch($idbranch, 0); // branch pending cash closure
        $q['deposit_to_bank_data'] = $this->Reconciliation_model->get_deposit_to_bank_byidbranch($idbranch);
        $this->load->model('Sale_model');
        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
        $this->load->view('payment/deposit_to_bank', $q);
    }
    public function delete_cash_deposite($id){
        $de = $this->Reconciliation_model->get_cash_deposite_byid($id);
        if( $this->Reconciliation_model->delete_daybook_data($id)){
            $data = array(
                'idcash_deposit_to_bank' => NULL,
                'status' => 0,
            );
            $this->Reconciliation_model->update_cash_closure_byiddeposite($data,$id);
            $deposite = array(
                'idbranch' => $de->idbranch,
                'date' => $de->date,
                'idbank' => $de->idbank,
                'total_closure_cash' => $de->total_closure_cash,
                'deposit_cash' => $de->deposit_cash,
                'remaining_after_deposit' => $de->remaining_after_deposit,
                'remark' => $de->remark,
                'received_amount' => $de->received_amount,
                'short_receive' => $de->short_receive,
                'received_date' => $de->received_date,
                'received_by' => $de->received_by,
                'received_utr' => $de->received_utr,
                'received_datetime' => $de->received_datetime,
                'reconciliation_status' => $de->reconciliation_status,
                'created_by' => $de->created_by,
                'entry_time' => $de->entry_time
            );
            $this->Reconciliation_model->update_cash_deposite_to_bank_delete_histroy($deposite);
            $this->Reconciliation_model->delete_cash_deposite_by_id($id);
            $this->session->set_flashdata('save_data', 'Cash Deposite To bank Deleted');
//            return redirect('Payment/deposit_to_bank');
              return redirect('Report/cash_deposit_report');
        }
    }

    public function cash_closure_delete() {
        $q['tab_active'] = 'Payment';
        $q['cash_closure_data'] = $this->Reconciliation_model->get_all_cash_closure_data(); // cash closure data
        $this->load->view('payment/cash_closure_edit', $q);
    }
    public function delete_cash_closure($id){
        $closer = $this->Reconciliation_model->get_cash_closure_byidcash($id);
        if($this->Reconciliation_model->delete_cash_closure_denomination_byid($id)){
            $data = array(
                'date' => $closer->date,
                'idbranch' => $closer->idbranch,
                'closure_cash' => $closer->closure_cash,
                'remark' => $closer->remark,
                'idcombine' => $closer->idcombine,
                'entry_time' => $closer->entry_time,
                'status' => $closer->status,
                'deposit_date' => $closer->deposit_date,
                'idcash_deposit_to_bank' =>  $closer->idcash_deposit_to_bank,
            );
            $this->Reconciliation_model->save_cash_closure_delete_histroy($data);
            $this->Reconciliation_model->delete_cash_closure_byid($id);
            $this->session->set_flashdata('save_data', 'Cash closure Deleted');
//            return redirect('Payment/cash_closure');
             return redirect('Report/cash_closure_report');
        }
    }
    
    public function save_cash_closure() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $idbranch = $this->input->post('idbranch');
        $datetime = date('Y-m-d H:i:s');
//        if(count($closure_pending_entry) > 0){
//            $idcombine = $closure_pending_entry[0]->idcombine;
//        }else{
//        }
        $idcombine = $this->session->userdata('branch_code').time();
        $this->Reconciliation_model->update_cash_closure_status_byidbranch($idbranch); // branch pending cash closure
        $data = array(
            'date' => $this->input->post('date'),
            'idbranch' => $idbranch,
            'closure_cash' => $this->input->post('total_amount'),
            'remark' => $this->input->post('remark'),
            'entry_time' => $this->input->post('date'),
            'idcombine' => $idcombine,
            'actual_entry_time' => $datetime
        );
        $iddeposit = $this->Reconciliation_model->save_cash_closure($data);
        $count = $this->input->post('count');
        $denom = array();
        $qty = $this->input->post('qty');
        for($i=0; $i<$count; $i++){
            if($qty[$i] != '' || $qty[$i] != 0){
                $denom[] = array(
                    'denomination' => $this->input->post('denom['.$i.']'),
                    'qty' => $qty[$i],
                    'cash' => $this->input->post('amount['.$i.']'),
                    'idcash_closure' => $iddeposit,
                    'idbranch' => $idbranch,
                );
            }
        }
        if(count($denom) > 0){
            $this->Reconciliation_model->save_closure_denomination($denom);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Cash closure is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data',  $this->input->post('date').' Cash closure Successfully submited');
        }
//        return redirect('Payment/cash_closure');
         return redirect('Payment/cash_closure_print/'.$iddeposit);
    }
    public function cash_closure_print($iddeposite){
       $q['cash_closure'] = $this->Reconciliation_model->get_cash_closure_byidcash($iddeposite);
       $q['closure_denomination'] = $this->Reconciliation_model->get_cash_closure_denomination_byid($iddeposite);
       $this->load->view('payment/cash_closure_print', $q);
    }
    
    public function save_cash_deposit() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idbranch = $this->input->post('idbranch');
        $date = $this->input->post('date');
        $id_cash_closure = $this->input->post('id_cash_closure');
        $this->db->trans_begin();
        $total_amount = $this->input->post('total_amount');
        $pending_closure_cash = $this->input->post('pending_closure_cash');
        $remain = $pending_closure_cash - $total_amount;
        
        $config = array(
            'image_library' => 'gd2',
            'upload_path' => 'assets/deposite_bank',
            'allowed_types' =>'jpg|jpeg|gif|png|jfif|pdf|doc|docx',
            'file_name' => $_FILES['userfile']['name'],
        );
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        if($this->upload->do_upload('userfile')){
            $uploadData = $this->upload->data();
            $imgfile = 'assets/deposite_bank/'.$uploadData['file_name'];
        }else{
            $imgfile = NULL;
        }
        
        $data = array(
            'idbranch' => $idbranch,
            'date' => $date,
            'idbank' => $this->input->post('idbank'),
            'deposit_cash' => $total_amount,
            'total_closure_cash' => $pending_closure_cash,
            'remaining_after_deposit' => $remain,
            'remark' => $this->input->post('remark'),
            'created_by' => $this->input->post('iduser'),
            'deposite_image' => $imgfile,
            'entry_time' => $date,
        );
        $iddeposit = $this->Reconciliation_model->save_deposit_to_bank($data);
        $refid = $this->input->post('refid');
        $updata = array(
            'idcash_deposit_to_bank' => $iddeposit,
            'deposit_date' => $date,
            'status' => 1
        );
        $daybook_cash = array(
            'date' => $date,
            'inv_no' => $refid,
            'entry_type' => 6, // cash deposit 
            'idbranch' => $idbranch,
            'idtable' => $iddeposit,
            'table_name' => 'cash_deposite_to_bank',
            'amount' => -$this->input->post('total_amount'),
        );
        $this->Reconciliation_model->save_daybook_cash_payment($daybook_cash);
        $this->Reconciliation_model->update_cash_closure_byid($id_cash_closure, $updata);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Cash Deposit is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Cash Deposit Successfull');
        }
        return redirect('Payment/deposit_to_bank');
    }
    function cash_payment_received_receipt($idrecon) {
        $q['payment_received_data'] = $this->Reconciliation_model->get_cash_payment_receive_byid($idrecon);
        $this->load->view('payment/cash_payment_received_receipt',$q);
    }
    function advance_booking_received_receipt($idrecon) {
        $q['payment_received_data'] = $this->Reconciliation_model->get_advanced_payment_receive_byid($idrecon);
        $this->load->view('payment/advance_booking_received_receipt',$q);
    }
    public function cash_payment_recieve() {
        $idbranch = $this->session->userdata('idbranch');
        $q['tab_active'] = 'Payment';
        $q['var_closer'] = $this->verify_cash_closure();
        $q['state_data'] = $this->General_model->get_state_data();
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $q['cash_payment_data'] = $this->Reconciliation_model->get_cash_payment_received_byidbranch($idbranch); // cash closure data
        $this->load->view('payment/cash_payment_recieve', $q);
    }
    public function ajax_get_cash_payment_receive_data(){
        $fromdate = date('Y-m-d', strtotime($this->input->post('fromdate')));
        $todate = date('Y-m-d', strtotime($this->input->post('todate')));
        $idbranch = $this->input->post('idbranch');
        $allbranches = $this->input->post('allbranches');
//        die($allbranches);
        $cash_payment_data = $this->Reconciliation_model->ajax_get_cash_payment_received_byidbranch($fromdate, $todate, $idbranch, $allbranches);
//        die('<pre>'.print_r($cash_payment_data,1).'</pre>');
        if(count($cash_payment_data) > 0){ ?>
            <table class="table table-striped table-condensed table-bordered" id="cash_payment_data">
                <thead>
                    <th>Sr</th>
                    <th>Receipt Id</th>
                    <th>Branch</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Invoice No</th>
                    <th>Cash Amount</th>
                    <th>Entry by</th>
                    <th>Entry Time</th>
                    <th>Remark</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; $total_amt=0; foreach($cash_payment_data as $cash_payment){ ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>CashRec/<?php echo $cash_payment->id_cash_payment_receive ?></td>
                        <td><?php echo $cash_payment->branch_name ?></td>
                        <td><?php echo $cash_payment->date ?></td>
                        <td><?php echo $cash_payment->cust_fname.' '.$cash_payment->cust_lname ?></td>
                        <td><?php echo $cash_payment->cust_contact ?></td>
                        <td><?php echo $cash_payment->inv_no ?></td>
                        <td><?php echo $cash_payment->amount ?></td>
                        <td><?php echo $cash_payment->user_name ?></td>
                        <td><?php echo $cash_payment->entry_time ?></td>
                        <td><?php echo $cash_payment->remark ?></td>
                        <td><a href="<?php echo base_url()?>Payment/cash_payment_received_receipt/<?php echo $cash_payment->id_cash_payment_receive ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        <?php }
    }
    
    public function recieve_advanced_payment() {
        $idbranch = $this->session->userdata('idbranch');
        $q['tab_active'] = 'Sale';
        $q['var_closer'] = $this->verify_cash_closure();
        $q['state_data'] = $this->General_model->get_state_data();
        $q['payment_head'] = $this->Reconciliation_model->get_active_payment_head_allow_for_advance_payment();
        $q['model_variant'] = $this->General_model->get_model_variant_data();
        $q['cash_payment_data'] = $this->Reconciliation_model->get_advance_payment_received_byidbranch($idbranch); // cash closure data
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
        $this->load->view('payment/recieve_advanced_payment', $q);
    }
    public function recieve_advanced_payment_report() {
        $q['tab_active'] = 'Sale';
        $iduser = $_SESSION['id_users'];
        if($_SESSION['level'] == 1){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
        }elseif($_SESSION['level'] == 3){
            $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
        }
        $idbranch = $this->session->userdata('idbranch');
        $q['cash_payment_data'] = $this->Reconciliation_model->get_advance_payment_received_report($idbranch); // cash closure data
        $this->load->view('payment/recieve_advanced_payment_report', $q);
    }
    public function save_cash_payment_receive() {
        $datetime = date('Y-m-d H:i:s');
        $data = array(
            'idbranch' => $this->input->post('idbranch'),
            'amount' => $this->input->post('amount'),
            'date' => $this->input->post('date'),
            'entry_time' => $datetime,
            'created_by' => $this->input->post('created_by'),
            'inv_no' => $this->input->post('inv_no'),
            'cust_contact' => $this->input->post('cust_contact'),
            'cust_address' => $this->input->post('cust_address'),
            'idcustomer' => $this->input->post('idcustomer'),
            'cust_fname' => $this->input->post('cust_fname'),
            'cust_lname' => $this->input->post('cust_lname'),
            'remark' => $this->input->post('remark'),
        );
        $idcash_receive = $this->Reconciliation_model->save_cash_payment_receive($data);
        $srpayment = array(
            'date' => $this->input->post('date'),
            'idbranch' => $this->input->post('idbranch'),
            'amount' => $this->input->post('amount'),
            'entry_type' => 8, // cash receive
            'inv_no' => 'CashRec/'.$idcash_receive,
            'idtable' => $idcash_receive,
            'table_name' => 'Cash Payment Receive',
        );
        $this->Sale_model->save_daybook_cash_payment($srpayment);
        $this->session->set_flashdata('save_data', 'Cash received successfully');
        return redirect('Payment/cash_payment_received_receipt/'.$idcash_receive);
    }
    
    public function save_advanced_payment_receive() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $branch_code = $_SESSION['branch_code'];
//        die($branch_code);
        $payment_type = $this->input->post('payment_type');
        $credit_type = $this->input->post('credit_type');
        $date = $this->input->post('date');
        $datetime = date('Y-m-d H:i:s');
        $amount = $this->input->post('amount');
        $idcustomer = $this->input->post('idcustomer');
        $idbranch = $this->input->post('idbranch');
        $data = array(
            'idbranch' => $idbranch,
            'amount' => $amount,
            'date' => $date,
            'entry_time' => $datetime,
            'created_by' => $this->input->post('created_by'),
            'inv_no' => $this->input->post('inv_no'),
            'idcustomer' => $idcustomer,
            'cust_contact' => $this->input->post('cust_contact'),
            'cust_fname' => $this->input->post('cust_fname'),
            'cust_lname' => $this->input->post('cust_lname'),
            'cust_address' => $this->input->post('cust_address'),
            'idvariant' => $this->input->post('idmodelvariant'),
            'remark' => $this->input->post('remark'),
            'transaction_id' => $this->input->post('tranxid'),
            'idpayment_head' => $this->input->post('paymenthead'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'idpayment_mode' => $payment_type,
        );
        $vin=array();
        $headattr = $this->input->post('headattr');
        if(count($headattr) > 0){
            foreach($headattr as $attr => $attr_value){
                $vin[$attr]=$attr_value;
            }
            $data = array_merge($data, $vin);
        }
        $idcash_receive = $this->Reconciliation_model->save_advanced_payment_receive($data);
        $received_amount=0;$pending_amt=$amount;$received_entry_time=NULL;$payment_receive=0;
        if($payment_type == 1){
            $received_amount = $amount;$pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
            $srpayment = array(
                'date' => $date,
                'idbranch' => $idbranch,
                'amount' => $amount,
                'entry_type' => 8, // cash receive
                'inv_no' => 'AdvPay/'.$branch_code.'/'.$idcash_receive,
                'idtable' => $idcash_receive,
                'table_name' => 'Advance payment receive',
            );
            $this->Sale_model->save_daybook_cash_payment($srpayment);
        }
//        $payment = array(
//            'date' => $date,
////            'idsale' => $idsale,
//            'amount' => $amount,
//    //                'received_amount' => $amount[$j],
//            'idpayment_head' => $this->input->post('paymenthead'),
//            'idpayment_mode' => $payment_type,
//            'transaction_id' => $this->input->post('tranxid'),
//            'inv_no' => 'Receipt/'.$branch_code.'/'.$idcash_receive,
//            'idcustomer' => $idcustomer,
//            'idbranch' => $idbranch,
//            'created_by' => $this->input->post('created_by'),
//            'entry_time' => $datetime,
//            'received_amount' => $received_amount,
//            'received_entry_time'=>$received_entry_time,
//            'payment_receive' => $payment_receive,
//            'idadvance_payment_receive ' => $idcash_receive,
//        );
//        if(count($headattr) > 0){
//            $payment = array_merge($payment, $vin); 
//        }
//        $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
       
         $payment = array(
                        'amount' => $amount,
                     
                        'idpayment_head' => $this->input->post('paymenthead'),
                        'idpayment_mode' => $payment_type,
                        'transaction_id' => $this->input->post('tranxid'),
                        'inv_no' => 'AdvPay/'.$branch_code.'/'.$idcash_receive,
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'created_by' => $this->input->post('created_by'),
                        'entry_time' => $datetime,
                        'received_amount' => $received_amount,
                        'received_entry_time'=>$received_entry_time,
                        'payment_receive' => $payment_receive
                    );
                   $paymentt = array_merge($payment, $vin);
                   // die('<pre>'.print_r($payment,1).'</pre>');
                    $id_sale_payment = $this->Sale_model->save_sale_payment($paymentt);
        
       // if($credit_type == 0){
            $npayment = array(
//                'idsale_payment' => $id_sale_payment,
                'inv_no' => 'AdvPay/'.$branch_code.'/'.$idcash_receive,
//                'idsale' => $idsale,
                'date' => $date,
                'idcustomer' => $idcustomer,
                'idbranch' => $idbranch,
                'amount' => $amount,
                'idpayment_head' => $this->input->post('paymenthead'),
                'idpayment_mode' => $payment_type,
                'transaction_id' => $this->input->post('tranxid'),
                'created_by' => $this->input->post('created_by'),
                'entry_time' => $datetime,
                'received_amount' => $received_amount,
                'pending_amt' => $pending_amt,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
                'idadvance_payment_receive ' => $idcash_receive,
            );
            if(count($headattr) > 0){
                $npayment = array_merge($npayment, $vin); 
            }
            $this->Sale_model->save_payment_reconciliation($npayment);
       // }
        
        $this->session->set_flashdata('save_data', 'Cash received successfully');
        return redirect('Payment/advance_booking_received_receipt/'.$idcash_receive);
    }
    
    
    public function verify_cash_closure() {
        $idbranch = $_SESSION['idbranch'];
        $sale_last_entry_byidbranch = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $cash_closure_last_entry = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
        $var_closer = 1;
        if(count($sale_last_entry_byidbranch)){
            if($sale_last_entry_byidbranch[0]->sum_cash == 0){
                $var_closer = 1;
            }else{
                if(count($cash_closure_last_entry) == 0){
                    $var_closer = 0;
                }else{
                    if($sale_last_entry_byidbranch[0]->date > $cash_closure_last_entry[0]->date){
                        $var_closer = 0;
                    }elseif($sale_last_entry_byidbranch[0]->date == $cash_closure_last_entry[0]->date){
                        if($sale_last_entry_byidbranch[0]->sum_cash <= $cash_closure_last_entry[0]->closure_cash){
                            $var_closer = 1;
                        }else{
                            $var_closer = 0;
                        }
                    }
                }
            }
        }
        if($_SESSION['idbranch'] == 7){
            $var_closer = 1;
        }
        return $var_closer;
    }
    public function ajax_get_payment_mode_data_byidhead() {
//        die(print_r($_POST));
        $head = $this->input->post('paymenthead');
        $headname = $this->input->post('headname');
        $payment_head = $this->General_model->get_payment_head_byid($head); 
        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
        $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); ?>
        <div id="modes_block<?php echo $head ?>" class="modes_block modes_blockc<?php echo $head ?> thumbnail" style="margin-bottom: 5px; padding: 5px;border: 1px solid #288ec3">
            <div class="col-md-4 col-sm-4" style="padding: 2px 5px">
                <span style="font-family: Kurale"><?php echo $headname ?></span>
                <select class="form-control input-sm payment_type" name="payment_type" required="">
                    <?php if($head == 4){ ?>
                    <option value="">Select Finance</option>
                    <?php } ?>
                    <?php foreach ($payment_mode as $mode) { if($mode->id_paymentmode != 17 && $mode->id_paymentmode != 18){ ?>
                    <option value="<?php echo $mode->id_paymentmode ?>"><?php echo $mode->payment_mode ?></option>
                    <?php }} ?>
                </select>
            </div>
            <input type="hidden" class="headname" name="headname" value="<?php echo $headname ?>" />
            <input type="hidden" class="credit_type" name="credit_type" value="<?php echo $payment_head->credit_type ?>" />
            <?php if($payment_head->tranxid_type == NULL){ ?>
                <div class="hidden">
                    <?php echo $payment_head->tranxid_type ?>
                    <input type="text" class="form-control input-sm tranxid" name="tranxid" placeholder="<?php echo $payment_head->tranxid_type ?>" value="<?php echo NULL; ?>" />
                </div>
            <?php }else{ ?>
            <div class="col-md-4 col-sm-4" style="padding: 2px 5px">
                <?php echo $payment_head->tranxid_type ?>
                <input type="text" class="form-control input-sm tranxid" name="tranxid" placeholder="<?php echo $payment_head->tranxid_type ?>" required="" pattern="[a-zA-Z0-9\-]+" />
            </div>
            <?php } ?>
            <?php foreach ($payment_attribute as $attribute){ ?>
            <div class="col-md-4 col-sm-4" style="padding: 2px 5px">
                <?php echo $attribute->attribute_name ?>
                <input type="text" class="form-control input-sm headattr" id="<?php echo $attribute->column_name ?>" name="headattr[<?php echo $attribute->column_name ?>]" placeholder="<?php echo $attribute->attribute_name ?>" required="" />
            </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div><br>
        <script>
            $(document).ready(function(){
                $('#product_model_name').autocomplete({
                    source: '<?php echo base_url('Sale/get_product_names_autocomplete') ?>',
                });
            });
        </script>
    <?php
    }
    public function ajax_refund_booking_payment() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idrow = $this->input->post('idrow');
        $refund_remark = $this->input->post('refund_remark');
        $date = date('Y-m-d');
        $idbranch = $_SESSION['idbranch'];
        
        $daybook_cash_sum_byid = $this->Sale_model->get_daybook_cash_sum_byid($idbranch);
        $daybook_sum_cash = 0;
        if(count($daybook_cash_sum_byid)){
            $daybook_sum_cash = $daybook_cash_sum_byid[0]->sum_cash;
        }
        $amount = $this->input->post('ref_amount');
        if($daybook_sum_cash < $amount){
            $q['result'] = 'Failed';
            $q['daybook_cash'] = $daybook_sum_cash;
        }else{
            $update_booking = array(
                'refund_remark' => $refund_remark,
                'claim' => 2,
                'refund_date' => $date
            );
            $this->Reconciliation_model->update_advanced_payment_byid($idrow, $update_booking);

            $branch_code = $_SESSION['branch_code'];
            $srpayment = array(
                'date' => $date,
                'inv_no' => 'AdvRefund/'.$branch_code.'/'.$idrow,
                'entry_type' => 9, // refund cash payment
                'idbranch' => $idbranch,
                'idtable' => $idrow,
                'table_name' => 'Advanced booking - Refund',
                'amount' => -$amount,
            );
            $this->Sale_model->save_daybook_cash_payment($srpayment);
            $q['result'] = 'Success';
            $q['daybook_cash'] = $daybook_sum_cash;
        }
        echo json_encode($q);
    }
    public function ajax_get_advance_payment_received_report() {
        $idbranch = $this->input->post('idbranch');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $branches = $this->input->post('branches');
        $idstatus = $this->input->post('idstatus');
        $adv_payment = $this->Reconciliation_model->ajax_get_advance_payment_received_report($datefrom,$dateto,$idbranch,$branches,$idstatus);
        ?>
        <thead style="background: #49c5bf;">
            <th>Sr</th>
            <th>Date</th>
            <th>Product</th>
            <th>Sales Promoter</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>Payment head</th>
            <th>Payment type</th>
            <th>Amount</th>
           <!-- <th>Entry by</th>-->
            <th>Branch Name</th>
            <th>Entry Time</th>
            <th>Days Diff</th>
            <th>Remark</th>
            <th>Reconciliation</th>
            <th>Inv No</th>
            <th>Inv Date</th>
            <th>Status</th>
            <th>Refund</th>
            <th>Print</th>
        </thead>
        <tbody class="cash_closure_entries">
            <?php $i=1; $total_amt=0; foreach($adv_payment as $cash_payment){ ?>
            <tr class="recon<?php echo $cash_payment->payment_receive.'_'.$cash_payment->claim ?>">
                <td><?php echo $i; ?></td>
                <td><?php echo date('d-m-Y', strtotime($cash_payment->date)) ?></td>
                <td><?php echo $cash_payment->full_name ?></td>
                <td><?php echo $cash_payment->sales_person ?></td>
                <td><?php echo $cash_payment->cust_fname.' '.$cash_payment->cust_lname ?></td>
                <td><?php echo $cash_payment->cust_contact ?></td>
                <td><?php echo $cash_payment->payment_head; ?></td>
                <td><?php echo $cash_payment->payment_mode; ?></td>
                <td>
                    <?php echo $cash_payment->amount ?>
                    <input type="hidden" class="ref_amount" value="<?php echo $cash_payment->amount ?>" />
                </td>
                <!--<td><?php //echo $cash_payment->user_name ?></td>-->
               <td><?php echo $cash_payment->branch_name ?></td>
                <td><?php echo $cash_payment->entry_time ?></td>
                <td><?php $now = time(); // or your date as well
                    $your_date = strtotime($cash_payment->entry_time);
                    $datediff = $now - $your_date;
                    echo round($datediff / (60 * 60 * 24)); ?></td>
                <td><?php echo $cash_payment->remark ?></td>
                <td><?php if($cash_payment->payment_receive){ echo 'Done'; }else{ echo 'Pending'; }?></td>
                <?php if($cash_payment->claim == 1){ ?>
                <td><a href="<?php echo base_url('Sale/sale_details/'.$cash_payment->idsale) ?>" class="waves-effect" style="color: #005bc0"><?php echo $cash_payment->inv_no ?></a></td>
                <td><?php echo date('d-m-Y', strtotime($cash_payment->inv_date)) ?></td>
                <td>Sale</td>
                <td>-</td>
                <?php }elseif($cash_payment->claim == 0 && $cash_payment->payment_receive){ ?>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <?php }elseif($cash_payment->claim == 2){ ?>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>Refund<br>Remark: <?php echo $cash_payment->refund_remark ?></td>
                <?php }else{ ?>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <?php } ?>
                <td><a href="<?php echo base_url()?>Payment/advance_booking_received_receipt/<?php echo $cash_payment->id_advance_payment_receive ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
            </tr>
            <?php $i++; } ?>
        </tbody>
        <?php 
    }
}