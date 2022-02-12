<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_wallet extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Expense_wallet_model');
        $this->load->model('Expense_model');
    }
    
    public function wallet_type(){
        $q['tab_active'] = 'Expense Wallet';
        $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data();
        $q['expense_headers'] = $this->Expense_wallet_model->get_expense_header_data();
        
        $q['expense_subheaders'] = $this->Expense_wallet_model->get_expense_subheader_data();
        
        
//        die('<pre>'.print_r($q['expense_headers'],1).'</pre>');
        $this->load->view('expense_wallet/expense_wallet_type', $q);
    }
    public function save_expense_wallet() {
        $data = array(
            'wallet_type ' => $this->input->post('wallettype'),
            'created_by' => $_SESSION['id_users'],
        );
        $this->Expense_wallet_model->save_expense_wallet_type($data);
        $this->session->set_flashdata('save_data', 'Wallet Type Created!.. ');
        redirect('Expense_wallet/wallet_type');
    }
    
    public function edit_wallet_type() {
        $id = $this->input->post('idw');
        $data = array(
            'wallet_type ' => $this->input->post('wallet'),
            'created_by' => $_SESSION['id_users'],
        );
        $this->Expense_wallet_model->edit_expense_wallet_type($data, $id);
        $this->session->set_flashdata('save_data', 'Wallet Type Updated!.. ');
        redirect('Expense_wallet/wallet_type');
    }
    
    public function save_expense_header() {
        $data = array(
            'expense_type' => $this->input->post('exheaders'),
            'idwallet' => $this->input->post('idwallet'),
            'head_created_by' => $_SESSION['id_users'],
        );
        $this->Expense_model->save_expense_head($data);
        $this->session->set_flashdata('save_data', 'Expense Header Created!.. ');
        redirect('Expense_wallet/wallet_type');
    }
    public function edit_expense_header() {
        $id = $this->input->post('idh');
        $data = array(
            'expense_type' => $this->input->post('head'),
            'idwallet' => $this->input->post('idwallet'),
            'head_created_by' => $_SESSION['id_users'],
        );
        $this->Expense_wallet_model->edit_expense_header($data, $id);
        $this->session->set_flashdata('save_data', 'Expense Header Created!.. ');
        redirect('Expense_wallet/wallet_type');
    }
    public function save_expense_subheader() {
//        die(print_r($_POST));
        $re = $this->Expense_wallet_model->get_wallet_type_byidheader($this->input->post('idheader'));
        if(count($re)>0){
            $idwallet = $re->idwallet;
        }else{
            $idwallet = NULL;
        }
        
        $data = array(
            'expense_subheader' => $this->input->post('extype'),
            'id_wallet' => $idwallet,
            'id_header' => $this->input->post('idheader'),
            'sub_created_by' => $_SESSION['id_users'],
        );
        $this->Expense_wallet_model->save_expense_subheader($data);
        $this->session->set_flashdata('save_data', 'Expense Header Created!.. ');
        redirect('Expense_wallet/wallet_type');
    }
    public function edit_expense_subheader() {
//        die(print_r($_POST));
        $id = $this->input->post('idsub');
        $re = $this->Expense_wallet_model->get_wallet_type_byidheader($this->input->post('idheader'));
        if(count($re)>0){
            $idwallet = $re->idwallet;
        }else{
            $idwallet = NULL;
        }
        
        $data = array(
            'expense_subheader' => $this->input->post('subhead'),
            'id_wallet' => $idwallet,
            'id_header' => $this->input->post('idheader'),
            'sub_created_by' => $_SESSION['id_users'],
        );
        $this->Expense_wallet_model->edit_expense_subheader($data, $id);
        $this->session->set_flashdata('save_data', 'Expense Header Created!.. ');
        redirect('Expense_wallet/wallet_type');
    }





























    public function expense_branch_configuration(){
        $q['tab_active'] = 'Petty Cash';
        $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        $this->load->view('expense/expense_branch_configuration', $q);
    }
    public function update_branch_expenseallowed_data(){
        $idbranch = $this->input->post('idbranch');
        $data = array(
            'expense_allowed' => $this->input->post('status'),
        );
        $this->General_model->update_branch_data($idbranch, $data);
    }

    public function petty_cash(){
        $q['tab_active'] = 'Petty Cash';
        $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        $q['petty_cash_data'] = $this->Expense_model->get_petty_cash_data();
        $this->load->view('expense/petty_cash', $q);
    }
    public function save_petty_cash(){
        $idbranch = $this->input->post('idbranch');
        $branch_aval_bal = $this->Expense_model->get_branch_available_petti_cash($this->input->post('idbranch'));
        //die(print_r($branch_aval_bal));
        $data = array(
            'date' => $this->input->post('date'),
            'idbranch' => $this->input->post('idbranch'),
            'amount' => $this->input->post('amount'),
            'created_by' => $_SESSION['id_users'],
            'month' => date('M', strtotime($this->input->post('date'))),
            'year' => date('Y', strtotime($this->input->post('date'))),
        );
        if($this->Expense_model->save_petty_cash($data)){
            
            $branch_aval_cash = array(
                'petti_cash_balance' => $branch_aval_bal->aval_balance + $this->input->post('amount'),
            );
            $this->Expense_model->update_branch_petti_cash($branch_aval_cash, $idbranch);
        }
        
        $this->session->set_flashdata('save_data', 'Petti Cash Save Successfully');
        redirect('Expense/petty_cash');
    }
    public function update_petticash_data(){
        
        $amount = $this->input->post('amount');
        $oldamount = $this->input->post('oldamount');
        $status = $this->input->post('status');
        $idpeticash = $this->input->post('idpeticash');
        $idbranch = $this->input->post('idbranch');
        
//        if($amount > $oldamount){
//            $u_amount = $amount - $oldamount;
//        }
//        if($amount < $oldamount){
//            $u_amount = $amount - $oldamount;
//        }
//        
//        if($amount == $oldamount){
//            $u_amount = 0;
//        }
        
        $data = array(
            'amount' => $amount,
            'status' => $status, 
        );
        $this->Expense_model->update_petty_cash($data, $idpeticash);
        $this->session->set_flashdata('save_data', 'Petti Cash Updated Successfully');
        redirect('Expense/petty_cash');
    }
    
     public function user_petty_cash(){
        $q['tab_active'] = 'Petty Cash';
        $q['user_data'] = $this->Expense_model->get_user_has_wallet();
        $q['user_petty_cash_data'] = $this->Expense_model->get_user_petty_cash_data();
        $this->load->view('expense/user_petty_cash', $q);
    }
    public function save_user_petty_cash(){
        $data = array(
            'date' => $this->input->post('date'),
            'iduser' => $this->input->post('iduser'),
            'amount' => $this->input->post('amount'),
            'created_by' => $_SESSION['id_users'],
            'month' => date('M', strtotime($this->input->post('date'))),
            'year' => date('Y', strtotime($this->input->post('date'))),
        );
        $this->Expense_model->save_user_petty_cash($data);
        $this->session->set_flashdata('save_data', 'User Petti Cash Save Successfully');
        redirect('Expense/user_petty_cash');
    }
     public function update_user_petticash_data(){
        $amount = $this->input->post('amount');
        $status = $this->input->post('status');
        $idpeticash = $this->input->post('idpeticash');
        
        $data = array(
            'amount' => $amount,
            'status' => $status, 
        );
        $this->Expense_model->update_user_petty_cash($data, $idpeticash);
        $this->session->set_flashdata('save_data', 'Petti Cash Updated Successfully');
        redirect('Expense/user_petty_cash');
    }
    public function expense(){
        $q['tab_active'] = 'Expense';
        $idbranch = $_SESSION['idbranch'];
        if($this->session->userdata('level') == 2){   // Branch Accountant
            $q['petty_cash_data'] = $this->Expense_model->get_branch_petty_cash_data_byidbranch($idbranch);
        }elseif($this->session->userdata('level') == 3){ 
            $q['petty_cash_data'] = $this->Expense_model->get_user_petty_cash_data_byiduser($_SESSION['id_users']);
        }
        $q['expense_head'] = $this->Expense_model->get_expense_head();
        $q['expense_data'] = $this->Expense_model->get_branch_expense_data($idbranch);
        $q['branch_aval_bal'] = $this->Expense_model->get_branch_available_petti_cash($idbranch);
//        $q['branch_petti_cash'] = $this->Expense_model->get_branch_active_petti_cash($idbranch);
//        $q['branch_expense_data'] = $this->Expense_model->get_branch_approved_expense_cash($idbranch);
//        die('<pre>'.print_r($q['expense_data'],1).'</pre>');
        
        //Todays Available Cash
        $date = date('Y-m-d');
        $q['total_daybook_cash'] = $this->Report_model->get_sum_daybook_cash_byidbranch_lastdate($idbranch, $date);
        $q['todays_cash'] = $this->Report_model->get_todays_daybooksum_byidbranch_groupby_entrytype($idbranch, $date); 
        $q['todays_short_deposit_sum'] = $this->Report_model->todays_short_deposit_sum($idbranch, $date); 
        $q['cash_closure_data'] = $this->Reconciliation_model->get_cash_closure_data_byidbranch($idbranch); // cash closure data
        $q['todays_cash_closure'] = $this->Reconciliation_model->get_todays_cash_closure_byidbranch($idbranch); // cash closure data
        $q['sum_cash_closure'] = $this->Reconciliation_model->get_sum_cash_closure_bystatus_idbranch($idbranch, 0); // branch pending cash closure
        
        $this->load->model('Sale_model');
        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data
        $q['last_date_entry'] = $this->Sale_model->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
//        die(count($q['todays_cash_closure']));
        $this->load->view('expense/expense', $q);
    }
    public function ajax_get_expensehead_byid(){
        $idexpense = $this->input->post('idexpense');
        $expense_head = $this->Expense_model->ajax_get_expensehead_need_approval_byid($idexpense);
        echo $expense_head->need_approval;
    }

    public function save_branch_expense(){
//        die(print_r($_FILES));
//        $imgfile = $this->input->post('userfile');
        
          $config = array(
            'image_library' => 'gd2',
            'upload_path' => 'assets/expense',
            'allowed_types' =>'jpg|jpeg|gif|png|jfif|pdf|doc|docx',
            'file_name' => $_FILES['userfile']['name'],
            );
            $this->load->library('upload',$config);
            $this->upload->initialize($config);
            if($this->upload->do_upload('userfile')){
                $uploadData = $this->upload->data();
                $imgfile = $uploadData['file_name'] ;
             //   die(print_r($uploadData['file_name']));
            }else{
                $imgfile = NULL;
            }
        
        $branch_aval_cash = $this->input->post('branch_bal') -  $this->input->post('amount');
        $data = array(
            'idbranch' => $_SESSION['idbranch'],
            'idexpense_head' => $this->input->post('idexpensehead'),
            'expense_amount' => $this->input->post('amount'),
            'expense_remark' => $this->input->post('remark'),
            'status' => $this->input->post('status'),
            'approve_expense_amount ' => $this->input->post('amount'),
            'approved_status' => 3,
            'created_by' => $_SESSION['id_users'],
            'entry_date' => $this->input->post('date'),
            'expense_image' => 'assets/expense/'.$imgfile,
            'month_year' => date('Y-m', strtotime($this->input->post('date'))),
        );
        if($lastid = $this->Expense_model->save_branch_expense($data)){
              $data_daybook = array(
                'date' => date('y-m-d'),
                'idbranch' => $_SESSION['idbranch'],
                'inv_no' => 'EXP-'.$_SESSION['branch_code'].'-'.$lastid,
                'amount' => '-'.$this->input->post('amount'),
                'entry_type' => 5,
                'idtable' => $lastid,
                'table_name' => 'expense',
            );
            $this->Expense_model->save_daybook_expense_cash($data_daybook);
            
            $branch_aval_cash = array(
                'petti_cash_balance' => $branch_aval_cash,
            );
            $this->Expense_model->update_branch_petti_cash($branch_aval_cash, $_SESSION['idbranch']);
        }
        
        $this->session->set_flashdata('save_data', 'Expense Saved Successfully');
        redirect('Expense/print_expense/'.$lastid);
    }
    public function save_branch_expense_proceed_for_approval(){
       
        $data = array(
            'idbranch' => $_SESSION['idbranch'],
            'idexpense_head' => $this->input->post('idexpensehead'),
            'expense_amount' => $this->input->post('amount'),
            'expense_remark' => $this->input->post('remark'),
            'status' => $this->input->post('status'),
            'created_by' => $_SESSION['id_users'],
            'entry_date' => $this->input->post('date'),
            'approved_status' => 0,
            'month_year' => date('Y-m', strtotime($this->input->post('date'))),
        );
        $lastid =  $this->Expense_model->save_branch_expense($data);
        $this->session->set_flashdata('save_data', 'Expense Saved Successfully');
        redirect('Expense/expense');
    }
    public function save_expense_daybook_data(){
        $idexpense = $this->input->post('idexpense');
        $amount = $this->input->post('amount');
        $idbranch = $this->input->post('idbranch');
        
        $data = array(
            'date' => date('y-m-d'),
            'idbranch' => $idbranch,
            'inv_no' => 'EXP-'.$_SESSION['branch_code'].'-'.$idexpense,
            'amount' => $amount,
            'entry_type' => 5,
            'idtable' => $idexpense,
            'table_name' => 'expense',
        );
        
        if($this->Expense_model->save_daybook_expense_cash($data)){
            $data = array(
                'approved_status' => 3,
            );
            $this->Expense_model->update_expense_data($data, $idexpense);
        }
        $this->session->set_flashdata('save_data', 'Expense Updated Successfully');
        redirect('Expense/expense');
    }
    
    public function expense_approve(){
        $q['tab_active'] = 'Expense';
        
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        
//        $q['expense_data'] = $this->Expense_model->get_branch_expense_data_for_approval();
        $this->load->view('expense/expense_approve', $q);
    }
    
    public function ajax_approve_branch_expense(){
        
        $idexpense = $this->input->post('idexpense');
        $approved_amount = $this->input->post('approved_amount');
        $remark = $this->input->post('remark');
        $data = array(
            'approved_status' => 1,
            'approve_expense_amount' => $approved_amount,
            'approved_remark' => $remark,
            'approved_date' => date('Y-m-d'),
            'approved_by' => $_SESSION['id_users'],
        );
        $this->Expense_model->update_expense_data($data, $idexpense);
    }
//    public function ajax_reject_branch_expense(){
//        $idexpense = $this->input->post('idexpense');
//        $approved_amount = $this->input->post('approved_amount');
//        $remark = $this->input->post('remark');
//        
//        $data = array(
//            'approved_status' => 2,
//            'approve_expense_amount' => $approved_amount,
//            'approved_remark' => $remark,
//            'approved_date' => date('Y-m-d'),
//            'approved_by' => $_SESSION['id_users'],
//        );
//        $this->Expense_model->update_expense_data($data, $idexpense);
//    }
    public function ajax_reject_branch_expense(){
        $idexpense = $this->input->post('idexpense');
        $reject_amount = $this->input->post('reject_amount');
        $idbranch = $this->input->post('idbranch');
        
        $branch_bal = $this->Expense_model->get_branch_available_petti_cash($idbranch);
        
        $data = array(
            'approved_status' => 2,
        );
        if($this->Expense_model->update_expense_data($data, $idexpense)){
            $petti = array(
                'date' => date('Y-m-d'),
                'idbranch' => $idbranch,
                'amount' => -$reject_amount,
                'status' => 0,
                'month' => date('M'),
                'year' => date('Y'),
                'created_by' => $_SESSION['id_users'],
            );
            $this->Expense_model->save_petty_cash($petti);
            
            $branch_update = array(
                'petti_cash_balance' => $branch_bal->aval_balance - $reject_amount,
            );
            $this->Expense_model->update_branch_petti_cash($branch_update, $idbranch);
        }
    }
    
    public function expense_report(){
        $q['tab_active'] = 'Expense';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('expense/expense_report', $q);
    }
    public function ajax_get_expense_report_data(){
        $idbranch = $this->input->post('idbranch');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $status = $this->input->post('status');
//        $monthyear = $this->input->post('monthyear');
        
        $expense_data = $this->Expense_model->get_expense_data_report($idbranch, $from, $to, $status); 
//        die(print_r($expense_data));
        if(count($expense_data) > 0){ ?> 
            <table class="table table-bordered table-condensed" id="expense_report">
                <thead style="background-color: #a2cfff" class="fixheader">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Expense Type</th>
                    <th>Expense Amount</th>
                    <th>Expense Remark</th>
                    <th>Generated By </th>
                    <th>Status</th>
<!--                    <th>Approved Amount</th>
                    <th>Approved Date</th>
                    <th>Approved Remark</th>
                    <th>Approved By</th>-->
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; $i = 1; foreach($expense_data as $expense){?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $expense->entry_date; ?></td>
                        <td><?php echo $expense->branch_name; ?></td>
                        <td><?php echo $expense->expense_type; ?></td>
                        <td><?php echo $expense->expense_amount ; $total = $total + $expense->expense_amount; ?></td>
                        <td><?php echo $expense->expense_remark; ?></td>
                        <td><?php echo $expense->created_by_name; ?></td>
                        <td><?php if($expense->approved_status == 0){ echo 'Pending For Approval'; }elseif($expense->approved_status == 3 || $expense->approved_status == 1){ echo 'Approved'; }elseif ($expense->approved_status == 2) { echo 'Rejected'; }elseif ($expense->approved_status == 4) { echo 'Cancelled'; } ?></td>
<!--                        <td><?php // echo $expense->approve_expense_amount; ?></td>
                        <td><?php echo $expense->approved_date; ?></td>
                        <td><?php echo $expense->approved_remark; ?></td>
                        <td><?php echo $expense->approved_by_name; ?></td>-->
                        <td> <?php if($expense->idcancell == NULL){ ?><a class="btn btn-floating btn-small btn-warning" target="_blank" href="<?php echo base_url()?>Expense/print_expense/<?php echo $expense->id_expense?>"><span class="fa fa-print"></span></a><?php }?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                         <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php } else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
    
    public function print_expense($idexpense){
        $q['expense_data'] = $this->Expense_model->get_expense_data_byidexpense($idexpense); 
        $this->load->view('expense/expense_print', $q);
    }
    
    public function delete_expense(){
        $idexpense = $this->input->post('idexpense');
        $expense = $this->Expense_model->get_expense_data_byidexpense($idexpense); 
        if($expense){
            $data = array(
                'idbranch' =>$expense->idbranch,
                'idexpense_head' => $expense->idexpense_head,
                'expense_amount' => '-'.$expense->expense_amount,
                'expense_image' => $expense-> expense_image,
                'approve_expense_amount' => '-'.$expense->approve_expense_amount,
                'expense_remark' => $expense->expense_remark,
                'status' => $expense->status,
                'approved_status' => 4,
                'created_by' => $_SESSION['id_users'],
                'entry_date' => date('Y-m-d'),
                'month_year' => $expense->month_year,
                'approved_remark' => $expense->approved_remark,
                'approved_date' => $expense->approved_date,
                'approved_by' => $expense->approved_by,
                'idcancell' => $expense->id_expense,
            );
            if($this->Expense_model->save_branch_expense_histroy($data)){
                $this->Expense_model->delete_expense_data($idexpense);
                $this->Expense_model->delete_daybook_expense_cash($idexpense);
            }
            
        }
    }

        public function ajax_get_expense_data_bybranch_month(){
        $idbranch = $this->input->post('idbranch');
        $monthyear = $this->input->post('monthyear');
      
        
        $expense_data = $this->Expense_model->get_expense_data_bymonthyear($idbranch, $monthyear); 
        if(count($expense_data) > 0){ ?> 
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #a2cfff">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Expense Type</th>
                    <th>Expense Amount</th>
                    <th>Expense Image</th>
                    <th>Expense Remark</th>
                    <th>Generated By </th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($expense_data as $expense){?>
                    <tr>
                        <form>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $expense->entry_date; ?></td>
                            <td><?php echo $expense->branch_name; ?></td>
                            <td><?php echo $expense->expense_type; ?></td>
                            <td><?php echo $expense->expense_amount ; ?></td>
                            <td><?php if($expense->expense_image){ ?> <a href="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" target="_blank"><img style="height: 50px;" src="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" ></a><?php }?></td>
                            <td><?php echo $expense->expense_remark; ?></td>
                            <td><?php echo $expense->created_by_name; ?></td>
                            <td>
                                <input type="hidden" id="idexpense" name="idexpense" value="<?php echo $expense->id_expense; ?> ">
                                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $expense->idbranch; ?> ">
                                <input type="hidden"  name="rejectapproved_amount" id="rejectapproved_amount" value="<?php echo $expense->expense_amount?>">
                                <a class="btn btn-warning btn-sm" id="rejectexpense" >Reject</a>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                   
                </tbody>
            </table>
        <?php } else { ?>
        <script>
            $(document).ready(function (){
               alert("Data Not Found"); 
            });
        </script>
        <?php  } ?>
        <script>
             $(document).on('click', 'a[id=rejectexpense]', function() {
                var ce = $(this);
                var parentdiv = $(ce).closest('td').parent('tr');
                var idexpense = parentdiv.find('#idexpense').val();
                var reject_amount = parentdiv.find('#rejectapproved_amount').val();
                var idbranch = parentdiv.find('#idbranch').val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('Expense/ajax_reject_branch_expense'); ?>",
                    data: {idexpense: idexpense, reject_amount: reject_amount, idbranch: idbranch},
                    success: function(data){
                        alert("Expense Rejected Suuceessfully!..")
                        window.location.reload();
                    }
                }); 
           }); 
        </script>
    <?php }
    
    public function expense_summary_report(){
        $q['tab_active'] = 'Expense';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('expense/expense_summary_report', $q);
    }   
    
    public function ajax_get_expense_summary_report_data(){
        $idbranch = $this->input->post('idbranch');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $total_petti = 0;
        $total_used = 0;
        
        $petti_cash_data = $this->Expense_model->ajax_get_total_petti_cash_summary_data($idbranch, $from, $to);
        
//        die('<pre>'.print_r($petti_cash_data,1).'</pre>');
        $expense_summary_data = $this->Expense_model->ajax_get_total_expense_summary_data($idbranch, $from, $to);
//        die('<pre>'.print_r($expense_summary_data,1).'</pre>');
        ?>
         
        <div class="thumbnail">
            <table class="table table-bordered table-condensed" id="expense_summary_report">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th>Branch</th>
                    <th>Month</th>
                    <th>Allocated Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $talloc =0; $tused =0; $tremain =0; $remain=0; foreach ($petti_cash_data as $petti){
                        foreach($expense_summary_data as $exp){
                            if($exp->idbranch == $petti->id_branch && $exp->month_year ==  date('Y-m', strtotime($petti->date))){ ?>
                            <tr>
                                <td><?php echo $petti->branch_name; ?></td>
                                <td><?php echo date('M', strtotime($exp->month_year)); ?></td>
                                <td><?php echo $petti->total_cash; $talloc = $talloc + $petti->total_cash; ?></td>
                                <td><?php echo $exp->exp_amt; $tused = $tused + $exp->exp_amt; ?></td>
                                <td><?php $remain = $petti->total_cash - $exp->exp_amt; echo $remain; $tremain = $tremain + $remain;?></td>
                                <td><a href="<?php echo base_url()?>Expense/expense_summary_details/<?php echo $petti->id_branch?>/<?php echo $exp->month_year?>" class="btn btn-floating btn-primary" target="_blank"><span class="fa fa-info"></span></a></td>
                            </tr>
                    <?php } } }  ?>
                            <tr>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $talloc; ?></b></td>
                                <td><b><?php echo $tused; ?></b></td>
                                <td><b><?php echo $tremain; ?></b></td>
                                <td></td>
                            </tr>
                </tbody>
            </table>
        </div>
       
    <?php 
    }
    
    public function expense_summary_details($idbranch, $month_year){
        $q['tab_active'] = 'Expense';
        $q['expense_summary_data'] = $this->Expense_model->ajax_get_expense_summary_data($idbranch, $month_year);
        $this->load->view('Expense/expense_summary_details', $q);
    }
    public function expense_head(){
        $q['tab_active'] = 'Expense';
        $q['expense_head'] = $this->Expense_model->get_expense_head();
        $this->load->view('Expense/expense_head', $q);
    }
    public function save_expense_head(){
        $data = array(
            'expense_type' => $this->input->post('type'),
            'need_approval' => $this->input->post('need_approval'),
            'active' => $this->input->post('status'),
        );
        $this->Expense_model->save_expense_head($data);
        $this->session->set_flashdata('save_data', 'Expense Type Created Successfully');
        redirect('Expense/expense_head');
    }
    
    public function update_expense_head(){
        $id = $this->input->post('idexpensetype');
         $data = array(
            'expense_type' => $this->input->post('type'),
            'need_approval' => $this->input->post('approval'),
            'active' => $this->input->post('status'),
        );
        $this->Expense_model->update_expense_head($data, $id);
    }
}