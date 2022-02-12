<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Audit_model');
        $this->load->model('Expense_model');
        $this->load->model('Expense_wallet_model');
        $this->load->model('General_model');
        $this->load->model('Report_model');
        $this->load->model('Reconciliation_model');
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
        $q['tab_active'] = 'Wallet Balance';
        $q['branch_data'] = $this->Audit_model->get_active_branch_data_with_zone();
        $q['petty_cash_data'] = $this->Expense_model->get_petty_cash_data();
        $q['user_has_wallet_type'] = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); 
//        die(print_r($q['user_has_wallet_type']));
        $this->load->view('expense/petty_cash', $q);
    }
    
    public function save_petty_cash(){
        
        $branch_arr = $this->input->post('idbranch');
        $branch_amount = $this->input->post('amount'); 
        $branch_remark = $this->input->post('premark'); 
       
        for($i=0; $i<count($branch_arr); $i++){
            if($branch_amount[$i] != 0){
                $idbranch = $branch_arr[$i];
                $data = array(
                    'date' => $this->input->post('date'),
                    'idbranch' => $branch_arr[$i],
                    'amount' => $branch_amount[$i],
                    'petti_remark' => $branch_remark[$i],
                    'idwallet_type' => $this->input->post('idwallet'),          
                    'created_by' => $_SESSION['id_users'],
                    'month' => date('M', strtotime($this->input->post('date'))),
                    'year' => date('Y', strtotime($this->input->post('date'))),
                    'month_year' => date('Y-m', strtotime($this->input->post('date'))),
                );
                $this->Expense_model->save_petty_cash($data);
            }
        }
        $this->session->set_flashdata('save_data', 'Petti Cash Save Successfully');
        redirect('Expense/petty_cash');
    }
    public function save_salarypetty_cash(){
       
        $entrytime = date('Y-m-d H:i:s');
        $idwallet = $this->input->post('idwallet');
        $date = $this->input->post('date');
        $premark = $this->input->post('pettremark');
        $month = date('M', strtotime($this->input->post('date')));
        $year = date('Y', strtotime($this->input->post('date')));
         $i =0;
        if($_FILES['uploadfile']['name'] != ''){
           
//            die(print_r($_FILES));
            $filename=$_FILES["uploadfile"]["tmp_name"];
            
            if($_FILES["uploadfile"]["size"] > 0){
                $file = fopen($filename, "r");
                while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                    if($i > 0){ 
                        $exparr[] = array(
                            'idbranch' => $openingdata[0],
                            'branch_name' => $openingdata[1],
                            'empid' => $openingdata[2],
                            'emp_name' => $openingdata[3],
                            'amount' => $openingdata[4],
                            'date' => $date,
                            'entry_time' => $entrytime,
                            'status' => 0,
                            'created_by' => $_SESSION['id_users'],
                            'idwallet' => $idwallet,
                        );
                    }
//                    die(print_r($exparr));
                    $i++;
                }
                fclose($file);
//                die(print_r($exparr));
//                die("hi");
                if($this->Expense_model->save_employee_salary($exparr)){
                    $empsalary_data = $this->Expense_model->get_sum_employe_salary_byidbranch($idwallet,$entrytime,$_SESSION['id_users']);
                    for($i=0; $i< count($empsalary_data); $i++){
                        $pettydata = array(
                            'date' => $date,
                            'idbranch' => $empsalary_data[$i]->idbranch,
                            'amount' => $empsalary_data[$i]->amount,
                            'petti_remark' => $premark,
                            'idwallet_type' => $idwallet,          
                            'created_by' => $_SESSION['id_users'],
                            'month' => $month,
                            'year' => $year,
                            'month_year' => date('Y-m', strtotime($this->input->post('date'))),
                        );
                        $this->Expense_model->save_petty_cash($pettydata);
                    }
                    $this->session->set_flashdata('save_data', 'Wallet Balance Save Successfully');
                    redirect('Expense/petty_cash');
                }
            }
        }else{ ?>
            <script>
                alert("Excel File Cannot be empty");
                redirect('Expense/petty_cash');
            </script>
        <?php }
        
        
    }


//    public function save_petty_cash(){
//        $idbranch = $this->input->post('idbranch');
//        $branch_aval_bal = $this->Expense_model->get_branch_available_petti_cash($this->input->post('idbranch'));
//        
//        $config = array(
//            'image_library' => 'gd2',
//            'upload_path' => 'assets/expense',
//            'allowed_types' =>'jpg|jpeg|gif|png|jfif|pdf|doc|docx|xlsx|csv|xls',
//            'file_name' => $_FILES['userfile']['name'],
//        );
//       
//        $this->load->library('upload',$config);
//        $this->upload->initialize($config);
//        if($this->upload->do_upload('userfile')){
//            $uploadData = $this->upload->data();
//            $imgfile = 'assets/expense/'.$uploadData['file_name'] ;
//        }else{
//            $imgfile = NULL;
//        }
//        
//        $data = array(
//            'date' => $this->input->post('date'),
//            'idbranch' => $this->input->post('idbranch'),
//            'amount' => $this->input->post('amount'),
//            'idwallet_type' => $this->input->post('idwallet'),
//            'created_by' => $_SESSION['id_users'],
//            'month' => date('M', strtotime($this->input->post('date'))),
//            'year' => date('Y', strtotime($this->input->post('date'))),
//            'fileupload' => $imgfile,
//        );
//        if($this->Expense_model->save_petty_cash($data)){
//            
//            $branch_aval_cash = array(
//                'petti_cash_balance' => $branch_aval_bal->aval_balance + $this->input->post('amount'),
//            );
//            $this->Expense_model->update_branch_petti_cash($branch_aval_cash, $idbranch);
//        }
//        
//        $this->session->set_flashdata('save_data', 'Petti Cash Save Successfully');
//        redirect('Expense/petty_cash');
//    }
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
        $q['allexpense_data'] = $this->Expense_model->get_branch_allexpense_data($idbranch);
        
        //Wallet Data
        $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data();
        
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
//        $q['last_date_entry'] = $this->Sale_model ->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
//        die('<pre>'.print_r($q['branch_aval_bal'],1).'</pre>');
        
        
        $this->load->view('expense/expense', $q);
    }
    
    public function add_branch_expense($idwallet){
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
        $q['allexpense_data'] = $this->Expense_model->get_branch_allexpense_data($idbranch);
        
        //Wallet Data
        $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data();
        
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
//        $q['last_date_entry'] = $this->Sale_model ->get_sale_last_date_entry_byidbranch($idbranch); // cash closure data
//        die('<pre>'.print_r($q['branch_aval_bal'],1).'</pre>');
        
        
        $this->load->view('expense/add_branch_expense', $q);
    }
    
    public function ajax_load_expense_header_byidwallet(){
        $idwallet = $this->input->post('idwallet');
        $heade_data = $this->Expense_wallet_model->ajax_get_expense_head_byidwallet($idwallet);
        $avlpetti_bal = $this->Expense_model->ajax_get_branch_petticash_data_byid($_SESSION['idbranch'], $idwallet);
        $expense_data = $this->Expense_model->ajax_get_branch_expense_data_byid($_SESSION['idbranch'], $idwallet);
        $avl_bal=0; $total_exp =0;$aval_petti = 0;
        
        if($expense_data){
            $total_exp = floatval($expense_data->exp_amount);
        }else{
            $total_exp =0;
        }
        
        if($avlpetti_bal){
            $aval_petti = floatval($avlpetti_bal->aval_balance);
        }else{
            $aval_petti = 0;
        }
        
        if($aval_petti > 0){
            $avl_bal = $aval_petti - $total_exp;
        }else{
            $avl_bal = 0;
        }
        
        if(count($heade_data) > 0){ ?>
            <select class="form-control" name="idexpensehead" id="idexpensehead" required="">
                <option value="">Select Expense Header</option>
                <?php foreach ($heade_data as $head){ ?>
                    <option value="<?php echo $head->id_expense_head?>"><?php echo $head->expense_type;?></option>
                <?php } ?>
            </select>
            <input type="hidden" name="avalbal" id="avalbal" value="<?php echo $avl_bal?>">
            <script>
                $(document).ready(function (){
                    $('#idexpensehead').change(function (){
                        var idhead = $('#idexpensehead').val();
                        if(idhead != ''){
                            $.ajax({
                               type: "POST",
                               url: "<?php echo base_url('Expense/ajax_load_expense_subheader_byidhead'); ?>",
                               data: {idhead: idhead},
                               success: function(data){
                                   $('#idexpenssubehead').html(data);
                               }
                            }); 
                        }else{
                            alert("Select Wallet Type");
                            return false;
                        }

                    });
                });
        </script>
        <?php } else{ ?>
            <select class="form-control" name="idexpensehead" id="idexpensehead" >
                <option value="">Select Expense Header</option>
            </select>
        <input type="hidden" name="avalbal" id="avalbal" value="<?php echo $avl_bal?>">
        <?php }
    }
    public function ajax_load_expense_subheader_byidhead(){
        $idhead = $this->input->post('idhead');
        $subheade_data = $this->Expense_wallet_model->ajax_get_expense_subhead_byidhead($idhead);
        $need_approval = $this->ajax_get_expensehead_byid($idhead);?>
        <?php if(count($subheade_data) > 0){ ?>
            <select class="form-control" name="idexpenssubehead" id="idexpenssubehead" required="">
                <option value="">Select Expense Subhead</option>
                <?php foreach ($subheade_data as $subhead){ ?>
                    <option value="<?php echo $subhead->id_expense_subheader?>"><?php echo $subhead->expense_subheader;?></option>
                <?php } ?>
            </select>
            
        <?php } else{ ?>
            <select class="form-control" name="idexpenssubehead" id="idexpenssubehead" >
                <option value="">Select Expense Subhead</option>
            </select>
        <?php }?>
        <input type="hidden" value="<?php echo $need_approval; ?>" id="status" name="status" >
     <?php    
    }

    public function ajax_get_expensehead_byid($idexpense){
//        $idexpense = $this->input->post('idexpense');
        $expense_head = $this->Expense_model->ajax_get_expensehead_need_approval_byid($idexpense);
        return $expense_head->need_approval;
    }

    public function save_branch_expense(){
        $approval_status = $this->input->post('status');
       /* if($approval_status == 0){ */
            if($this->input->post('idexpenssubehead') == ''){
                $idexp_subhead = NULL;
            }else{
                $idexp_subhead = $this->input->post('idexpenssubehead');
            }
        
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
                $imgfile = 'assets/expense/'.$uploadData['file_name'] ;
             //   die(print_r($uploadData['file_name']));
            }else{
                $imgfile = NULL;
            }

            $branch_aval_cash = $this->input->post('avalbal') -  $this->input->post('amount');
            $data = array(
                'idbranch' => $_SESSION['idbranch'],
                'id_wallet' => $this->input->post('idwallettype'),
                'idexpense_head' => $this->input->post('idexpensehead'),
                'id_expensesubhead' => $idexp_subhead,
                'expense_amount' => $this->input->post('amount'),
                'expense_remark' => $this->input->post('remark'),
                'status' => $this->input->post('status'),
                'approve_expense_amount ' => $this->input->post('amount'),
                'approved_status' => 3,
                'created_by' => $_SESSION['id_users'],
                'entry_date' => $this->input->post('date'),
                'expense_image' => $imgfile,
                'month_year' => $this->input->post('monthyear'),
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

//                $branch_aval_cash_data = array(
//                    'petti_cash_balance' => $branch_aval_cash,
//                );
//                $this->Expense_model->update_branch_petti_cash($branch_aval_cash_data, $_SESSION['idbranch']);
            }

            $this->session->set_flashdata('save_data', 'Expense Saved Successfully');
            $idre = 1;
            redirect('Expense/print_expense/'.$lastid.'/'.$idre);
      /*  }else{
            //need Approval
             if($this->input->post('idexpenssubehead') == ''){
                $idexp_subhead = NULL;
            }else{
                $idexp_subhead = $this->input->post('idexpenssubehead');
            }
            $data = array(
                'idbranch' => $_SESSION['idbranch'],
                'id_wallet' => $this->input->post('idwallettype'),
                'idexpense_head' => $this->input->post('idexpensehead'),
                'id_expensesubhead' => $idexp_subhead,
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
        }*/
       
    }
    /*public function save_branch_expense_proceed_for_approval(){
        
        if($this->input->post('idexpenssubehead') == ''){
            $idexp_subhead = NULL;
        }else{
            $idexp_subhead = $this->input->post('idexpenssubehead');
        }
       
        $data = array(
            'idbranch' => $_SESSION['idbranch'],
            'id_wallet' => $this->input->post('idwallettype'),
            'idexpense_head' => $this->input->post('idexpensehead'),
            'id_expensesubhead' => $idexp_subhead,
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
     
     */
    
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
        
        $q['expense_data'] = $this->Expense_model->get_branch_expense_data_for_approval();
        $this->load->view('expense/expense_approve', $q);
    }
    
    public function ajax_approve_branch_expense(){
        
        $idexpense = $this->input->post('idexpense');
        $idbranch = $this->input->post('idbranch');
        $b_data = $this->General_model->get_branch_byid($idbranch);
        $approved_amount = $this->input->post('approved_amount');
        $remark = $this->input->post('remark');
        //Proceed to approval
//        $data = array(
//            'approved_status' => 1,
//            'approve_expense_amount' => $approved_amount,
//            'approved_remark' => $remark,
//            'approved_date' => date('Y-m-d'),
//            'approved_by' => $_SESSION['id_users'],
//        );
        //Temporary Direct Approve 
        $data = array(
            'status' => 0,
            'approved_status' => 3,
            'approve_expense_amount' => $approved_amount,
            'approved_remark' => $remark,
            'approved_date' => date('Y-m-d'),
            'approved_by' => $_SESSION['id_users'],
        );
        $this->Expense_model->update_expense_data($data, $idexpense);
        
        $data_daybook = array(
            'date' => date('y-m-d'),
            'idbranch' => $idbranch,
            'inv_no' => 'EXP-'.$b_data->branch_code.'-'.$idexpense,
            'amount' => '-'.$approved_amount,
            'entry_type' => 5,
            'idtable' => $idexpense,
            'table_name' => 'expense',
        );
        $this->Expense_model->save_daybook_expense_cash($data_daybook);

//        $branch_aval_cash_data = array(
//            'petti_cash_balance' => $branch_aval_cash,
//        );
//        $this->Expense_model->update_branch_petti_cash($branch_aval_cash_data, $idbranch);
        
        
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
        $idwallet = $this->input->post('idwallet');
        
        $branch_bal = $this->Expense_model->get_branch_available_petti_cash($idbranch);
        
        $data = array(
            'approved_status' => 2,
        );
        if($this->Expense_model->update_expense_data($data, $idexpense)){
            $petti = array(
                'date' => date('Y-m-d'),
                'idbranch' => $idbranch,
                'idwallet_type' => $idwallet,
                'amount' => -$reject_amount,
                'status' => 0,
                'month' => date('M'),
                'year' => date('Y'),
                'created_by' => $_SESSION['id_users'],
                'month_year' => date('Y-m'),
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
        if($_SESSION['idrole'] == 24 || $_SESSION['idrole'] == 38){
            $user_has_wallet_type = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); 
            if(count($expense_data) > 0){ ?> 
                <table class="table table-bordered table-condensed" id="expense_report">
                    <thead style="background-color: #a2cfff" class="fixheader">
                        <th>Sr.</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Wallet Type</th>
                        <th>Expense Head</th>
                        <th>Expense Subhead</th>
                        <th>Expense Amount</th>
                        <th>Expense Remark</th>
                        <th>Expense Month</th>
                        <th>Generated By </th>
                        <th>Status</th>
                        <th>Approved Amount</th>
                        <th>Approved Date</th>
                        <th>Approved Remark</th>
                        <th>Approved By</th>
                        <th>Print</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $total=0; $i = 1; foreach($expense_data as $expense){
                            foreach ($user_has_wallet_type as $haswall){
                                if($expense->id_wallet == $haswall->idwallet) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $expense->entry_date; ?></td>
                                        <td><?php echo $expense->branch_name; ?></td>
                                        <td><?php echo $expense->wallet_type; ?></td>
                                        <td><?php echo $expense->expense_type; ?></td>
                                        <td><?php echo $expense->expense_subheader; ?></td>
                                        <td><?php echo $expense->expense_amount ; $total = $total + $expense->expense_amount; ?></td>
                                        <td><?php echo $expense->expense_remark; ?></td>
                                        <td><?php echo $expense->month_year; ?></td>
                                        <td><?php echo $expense->created_by_name; ?></td>
                                        <td><?php if($expense->approved_status == 0){ echo 'Pending For Approval'; }elseif($expense->approved_status == 3 || $expense->approved_status == 1){ echo 'Approved'; }elseif ($expense->approved_status == 2) { echo 'Rejected'; }elseif ($expense->approved_status == 4) { echo 'Cancelled'; } ?></td>
                                       <td><?php echo $expense->approve_expense_amount; ?></td>
                                        <td><?php echo $expense->approved_date; ?></td>
                                        <td><?php echo $expense->approved_remark; ?></td>
                                        <td><?php echo $expense->approved_by_name; ?></td>
                                        <td> <?php if($expense->idcancell == NULL){ ?><a class="btn btn-floating btn-small btn-warning" target="_blank" href="<?php echo base_url()?>Expense/print_expense/<?php echo $expense->id_expense?>/2"><span class="fa fa-print"></span></a><?php }?></td>
                                    </tr>
                        <?php } } } ?>
                        <tr>
                            <td></td>
                            <td></td>
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
            <?php } ?>
        <?php }else{
            if(count($expense_data) > 0){ ?> 
            <table class="table table-bordered table-condensed" id="expense_report">
                <thead style="background-color: #a2cfff" class="fixheader">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Wallet Type</th>
                    <th>Expense Head</th>
                    <th>Expense Subhead</th>
                    <th>Expense Amount</th>
                    <th>Expense Remark</th>
                    <th>Expense Month</th>
                    <th>Generated By </th>
                    <th>Status</th>
                    <th>Approved Amount</th>
                    <th>Approved Date</th>
                    <th>Approved Remark</th>
                    <th>Approved By</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php $total=0; $i = 1; foreach($expense_data as $expense){?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $expense->entry_date; ?></td>
                        <td><?php echo $expense->branch_name; ?></td>
                        <td><?php echo $expense->wallet_type; ?></td>
                        <td><?php echo $expense->expense_type; ?></td>
                        <td><?php echo $expense->expense_subheader; ?></td>
                        <td><?php echo $expense->expense_amount ; $total = $total + $expense->expense_amount; ?></td>
                        <td><?php echo $expense->expense_remark; ?></td>
                        <td><?php echo $expense->month_year; ?></td>
                        <td><?php echo $expense->created_by_name; ?></td>
                        <td><?php if($expense->approved_status == 0){ echo 'Pending For Approval'; }elseif($expense->approved_status == 3 || $expense->approved_status == 1){ echo 'Approved'; }elseif ($expense->approved_status == 2) { echo 'Rejected'; }elseif ($expense->approved_status == 4) { echo 'Cancelled'; } ?></td>
                       <td><?php // echo $expense->approve_expense_amount; ?></td>
                        <td><?php echo $expense->approved_date; ?></td>
                        <td><?php echo $expense->approved_remark; ?></td>
                        <td><?php echo $expense->approved_by_name; ?></td>
                        <td> <?php if($expense->idcancell == NULL){ ?><a class="btn btn-floating btn-small btn-warning" target="_blank" href="<?php echo base_url()?>Expense/print_expense/<?php echo $expense->id_expense?>/2"><span class="fa fa-print"></span></a><?php }?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
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
        
    }
    
    public function print_expense($idexpense, $idredirect){
        $q['expense_data'] = $this->Expense_model->get_expense_data_byidexpense($idexpense); 
        $q['idredirect'] = $idredirect;
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
        $user_has_wallet_type = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); 
            if(count($expense_data) > 0){ ?> 
                <table class="table table-bordered table-condensed" >
                    <thead style="background-color: #a2cfff">
                        <th>Sr.</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Wallet Type</th>
                        <th>Expense Head</th>
                        <th>Expense subhead</th>
                        <th>Expense Amount</th>
                        <th>Expense Image</th>
                        <th>Expense Remark</th>
                        <th>Generated By </th>
                        <th>Action</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $i = 1; foreach($expense_data as $expense){ 
                            foreach ($user_has_wallet_type as $haswallet){ 
                                if($expense->id_wallet == $haswallet->idwallet){ ?>
                                    <tr>
                                        <!--<form>-->
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $expense->entry_date; ?></td>
                                            <td><?php echo $expense->branch_name; ?></td>
                                            <td><?php echo $expense->wallet_type; ?></td>
                                            <td><?php echo $expense->expense_type; ?></td>
                                            <td><?php echo $expense->expense_subheader; ?></td>
                                            <td><?php echo $expense->expense_amount ; ?></td>
                                            <td><?php if($expense->expense_image){ ?> <a href="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" target="_blank"><img style="height: 50px;" src="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" ></a><?php }?></td>
                                            <td><?php echo $expense->expense_remark; ?></td>
                                            <td><?php echo $expense->created_by_name; ?></td>
                                            <td>
                                                <input type="hidden" id="idexpense<?php echo $expense->id_expense; ?>" name="idexpense" value="<?php echo $expense->id_expense;?>">
                                                <input type="hidden" id="idwallet<?php echo $expense->id_expense; ?>" name="idwallet" value="<?php echo $expense->id_wallet;?>">
                                                <input type="hidden" id="idbranch<?php echo $expense->id_expense; ?>" name="idbranch" value="<?php echo $expense->idbranch;?>">
                                                <input type="hidden"  name="rejectapproved_amount" id="rejectapproved_amount<?php echo $expense->id_expense; ?>" value="<?php echo $expense->expense_amount?>">
                                                <?php if($expense->id_wallet == 3) { ?><a class="btn btn-warning btn-sm rejectexpense<?php echo $expense->id_expense; ?>" id="rejectexpense<?php echo $expense->id_expense; ?>" >Reject</a> <?php } ?>
                                                <script>
                                                    $(document).ready(function (){
                                                        $('.rejectexpense<?php echo $expense->id_expense; ?>').click(function (){
                                                            var idexpense = $('#idexpense<?php echo $expense->id_expense; ?>').val();
                                                            var reject_amount = $('#rejectapproved_amount<?php echo $expense->id_expense; ?>').val();
                                                            var idbranch = $('#idbranch<?php echo $expense->id_expense; ?>').val();
                                                            var idwallet = $('#idwallet<?php echo $expense->id_expense; ?>').val();
                                                            if(confirm("Do You Want To Reject Expense")){
                                                                $.ajax({
                                                                   type: "POST",
                                                                   url: "<?php echo base_url('Expense/ajax_reject_branch_expense'); ?>",
                                                                   data: {idexpense: idexpense, reject_amount: reject_amount, idbranch: idbranch, idwallet: idwallet},
                                                                   success: function(data){
                                                                       alert("Expense Rejected Suuceessfully!..")
                                                                       window.location.reload();
                                                                   }
                                                               }); 
                                                            }else{
                                                               return false;
                                                            }
                                                       });
                                                  }); 
                                               </script>
                                            </td>
                                        <!--</form>-->
                                    </tr>
                        <?php } } } ?>
                    </tbody>
                </table>
            
            <table class="table table-bordered table-condensed" id="user_petty_cash" style="display: none">
                    <thead style="background-color: #a2cfff">
                        <th>Sr.</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Wallet Type</th>
                        <th>Expense Head</th>
                        <th>Expense subhead</th>
                        <th>Expense Amount</th>
                        <th>Expense Image</th>
                        <th>Expense Remark</th>
                        <th>Generated By </th>
                        <th>Action</th>
                    </thead>
                    <tbody >
                        <?php $i = 1; foreach($expense_data as $expense){ 
                            foreach ($user_has_wallet_type as $haswallet){ 
                                if($expense->id_wallet == $haswallet->idwallet){ ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $expense->entry_date; ?></td>
                                        <td><?php echo $expense->branch_name; ?></td>
                                        <td><?php echo $expense->wallet_type; ?></td>
                                        <td><?php echo $expense->expense_type; ?></td>
                                        <td><?php echo $expense->expense_subheader; ?></td>
                                        <td><?php echo $expense->expense_amount ; ?></td>
                                        <td><?php if($expense->expense_image){ ?> <a href="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" target="_blank"><img style="height: 50px;" src="<?php echo base_url()?>/<?php echo $expense->expense_image ?>" ></a><?php }?></td>
                                        <td><?php echo $expense->expense_remark; ?></td>
                                        <td><?php echo $expense->created_by_name; ?></td>
                                           
                                    </tr>
                        <?php } } } ?>
                    </tbody>
                </table>
            
            <?php } else { ?>
        <script>
            $(document).ready(function (){
               alert("Data Not Found"); 
            });
        </script>
        <?php  } ?> 
<!--        <script>
             $(document).on('click', '.rejectexpense', function() {
                var ce = $(this);
                var parentdiv = $(ce).closest('td').parent('tr');
                var idexpense = parentdiv.find('#idexpense').val();
                var reject_amount = parentdiv.find('#rejectapproved_amount').val();
                var idbranch = parentdiv.find('#idbranch').val();
                if(confirm("Do You Want To Reject Expense")){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url('Expense/ajax_reject_branch_expense'); ?>",
                        data: {idexpense: idexpense, reject_amount: reject_amount, idbranch: idbranch},
                        success: function(data){
                            alert("Expense Rejected Suuceessfully!..")
                            window.location.reload();
                        }
                    }); 
                }else{
                    return false;
                }
           }); 
        </script>-->
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
        
        if($_SESSION['idrole'] == 24 || $_SESSION['idrole'] == 38){
            $q['wallet_type'] = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); 
        }else{
            $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data();
        }
        
        $this->load->view('expense/expense_summary_report', $q);
    } 
    
    public function ajax_get_expense_summary_report_data(){
        $idbranch = $this->input->post('idbranch');
        $idwallet = $this->input->post('idwallet');
        $from = $this->input->post('from');
//        $to = $this->input->post('to');
        $total_petti = 0;
        $total_used = 0;
        $exp_dat = $this->Expense_model->get_all_expense_summary_report($idbranch, $from, $idwallet);
        ?>
        <div class="thumbnail">
            <table class="table table-bordered table-condensed" id="expense_summary_report">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th>Branch</th>
                    <th>Wallet Type</th>
                    <th>Month</th>
                    <th>Allocated Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $talloc =0; $tused =0; $tremain =0; $remain=0; 
                    $totpetti = 0;$expamt=0;
                    foreach ($exp_dat as $petti){
                            if($petti->exp_amt){ $expamt  = $petti->exp_amt;}else{ $expamt = 0;}
                            if($petti->total_cash){ $totpetti  = $petti->total_cash;}else{ $totpetti = 0;}
                             ?>
                        <tr>
                            <td><?php echo $petti->branch_name; ?></td>
                            <td><?php echo $petti->wallet_type; ?></td>
                            <td><?php echo date('M', strtotime($from)); ?></td>
                            <td><?php echo $totpetti; $talloc = $talloc + $totpetti; ?></td>
                            <td><?php echo $expamt; $tused = $tused + $expamt; ?></td>
                            <td><?php $remain = $totpetti - $expamt; echo $remain; $tremain = $tremain + $remain;?></td>
                            <td><a href="<?php echo base_url()?>Expense/expense_summary_details/<?php echo $petti->id_branch?>/<?php echo $from ?>/<?php echo $petti->id_wallet_type ?>" class="btn btn-floating btn-primary" target="_blank"><span class="fa fa-info"></span></a></td>
                        </tr>
                    <?php } ?>
                        <tr>
                            <td></td>
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
       
    <?php //} else { ?>
<!--        <div class="thumbnail">
            <table class="table table-bordered table-condensed" id="expense_summary_report">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th>Branch</th>
                    <th>Wallet Type</th>
                    <th>Month</th>
                    <th>Allocated Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $talloc =0; $tused =0; $tremain =0; $remain=0; foreach ($petti_cash_data as $petti){
                        $expamt =0;
//                        foreach($expense_summary_data as $exp){
//                            if($exp->idbranch == $petti->idbranch && $exp->month_year ==  date('Y-m', strtotime($petti->date)) && $petti->idwallet_type == $exp->id_wallet){ 
//                                $expamt = $exp->exp_amt;
//                            }
//                        }
                        ?>
                            <tr>
                                <td><?php echo $petti->branch_name; ?></td>
                                <td><?php echo $petti->wallet_type; ?></td>
                                <td><?php echo date('M', strtotime($petti->date)); ?></td>
                                <td><?php echo $petti->total_cash; $talloc = $talloc + $petti->total_cash; ?></td>
                                <td><?php echo $expamt; $tused = $tused + $expamt; ?></td>
                                <td><?php $remain = $petti->total_cash - $expamt; echo $remain; $tremain = $tremain + $remain;?></td>
                                <td><a href="<?php echo base_url()?>Expense/expense_summary_details/<?php echo $petti->idbranch?>/<?php echo date('Y-m', strtotime($petti->date)) ?>/<?php echo $petti->idwallet_type ?>" class="btn btn-floating btn-primary" target="_blank"><span class="fa fa-info"></span></a></td>
                            </tr>
                    <?php } //} }  ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td><b>Total</b></td>
                                <td><b><?php echo $talloc; ?></b></td>
                                <td><b><?php echo $tused; ?></b></td>
                                <td><b><?php echo $tremain; ?></b></td>
                                <td></td>
                            </tr>
                </tbody>
            </table>
        </div>-->
    <?php 
    }
    
    /*
    public function ajax_get_expense_summary_report_data(){
        $idbranch = $this->input->post('idbranch');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $total_petti = 0;
        $total_used = 0;
        
        $petti_cash_data = $this->Expense_model->ajax_get_total_petti_cash_summary_data($idbranch, $from, $to);
//        die(print_r($petti_cash_data));
        $expense_summary_data = $this->Expense_model->ajax_get_total_expense_summary_data($idbranch, $from, $to);
         if($_SESSION['idrole'] == 24 || $_SESSION['idrole'] == 38){
            $user_has_wallet_type = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); 
        ?>
        <div class="thumbnail">
            <table class="table table-bordered table-condensed" id="expense_summary_report">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th>Branch</th>
                    <th>Wallet Type</th>
                    <th>Month</th>
                    <th>Allocated Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $talloc =0; $tused =0; $tremain =0; $remain=0; foreach ($petti_cash_data as $petti){
                        $expamt =0;
                        foreach($expense_summary_data as $exp){
                            if($exp->idbranch == $petti->idbranch && $exp->month_year ==  date('Y-m', strtotime($petti->date)) && $petti->idwallet_type == $exp->id_wallet){ 
                                $expamt = $exp->exp_amt;
                            }
                        }
                        foreach($user_has_wallet_type as $haswallet){
                           if($petti->idwallet_type == $haswallet->idwallet){ 
                        ?>
                            <tr>
                                <td><?php echo $petti->branch_name; ?></td>
                                <td><?php echo $petti->wallet_type; ?></td>
                                <td><?php echo date('M', strtotime($petti->date)); ?></td>
                                <td><?php echo $petti->total_cash; $talloc = $talloc + $petti->total_cash; ?></td>
                                <td><?php echo $expamt; $tused = $tused + $expamt; ?></td>
                                <td><?php $remain = $petti->total_cash - $expamt; echo $remain; $tremain = $tremain + $remain;?></td>
                                <td><a href="<?php echo base_url()?>Expense/expense_summary_details/<?php echo $petti->idbranch?>/<?php echo date('Y-m', strtotime($petti->date)) ?>/<?php echo $petti->idwallet_type ?>" class="btn btn-floating btn-primary" target="_blank"><span class="fa fa-info"></span></a></td>
                            </tr>
                    <?php } } } //} }  ?>
                            <tr>
                                <td></td>
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
       
    <?php } else { ?>
        <div class="thumbnail">
            <table class="table table-bordered table-condensed" id="expense_summary_report">
                <thead style="background-color: #ffcccc" class="fixheader">
                    <th>Branch</th>
                    <th>Wallet Type</th>
                    <th>Month</th>
                    <th>Allocated Amount</th>
                    <th>Used Amount</th>
                    <th>Remaining Amount</th>
                    <th>Details</th>
                </thead>
                <tbody class="data_1">
                    <?php $talloc =0; $tused =0; $tremain =0; $remain=0; foreach ($petti_cash_data as $petti){
                        $expamt =0;
                        foreach($expense_summary_data as $exp){
                            if($exp->idbranch == $petti->idbranch && $exp->month_year ==  date('Y-m', strtotime($petti->date)) && $petti->idwallet_type == $exp->id_wallet){ 
                                $expamt = $exp->exp_amt;
                            }
                        }?>
                            <tr>
                                <td><?php echo $petti->branch_name; ?></td>
                                <td><?php echo $petti->wallet_type; ?></td>
                                <td><?php echo date('M', strtotime($petti->date)); ?></td>
                                <td><?php echo $petti->total_cash; $talloc = $talloc + $petti->total_cash; ?></td>
                                <td><?php echo $expamt; $tused = $tused + $expamt; ?></td>
                                <td><?php $remain = $petti->total_cash - $expamt; echo $remain; $tremain = $tremain + $remain;?></td>
                                <td><a href="<?php echo base_url()?>Expense/expense_summary_details/<?php echo $petti->idbranch?>/<?php echo date('Y-m', strtotime($petti->date)) ?>/<?php echo $petti->idwallet_type ?>" class="btn btn-floating btn-primary" target="_blank"><span class="fa fa-info"></span></a></td>
                            </tr>
                    <?php } //} }  ?>
                            <tr>
                                <td></td>
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
    <?php }
    }
    
    */
    public function expense_summary_details($idbranch, $month_year, $idwallet){
        $q['tab_active'] = 'Expense';
        $q['expense_summary_data'] = $this->Expense_model->ajax_get_expense_summary_data($idbranch, $month_year, $idwallet);
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
    
    public function wallet_balance_report(){
        $q['tab_active'] = 'Wallet Report';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        $this->load->view('expense/wallet_balace_report', $q);
    }
    public function ajax_get_wallet_balance_report(){
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $wallet_data = $this->Expense_model->ajax_get_wallet_data_byfilter($idbranch, $from, $to, $branches);
        if(count($wallet_data) > 0){ 
            if($_SESSION['idrole'] == 24 || $_SESSION['idrole'] == 38){
                $user_has_wallet_type = $this->General_model->get_wallet_type_by_user($_SESSION['id_users']); ?>
                <table class="table table-bordered table-condensed" id="wallet_balance_report">
                    <thead style="background-color: #99ccff">
                        <th>Sr.</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Wallet Type</th>
                        <th>Wallet Amount</th>
                        <th>Remark</th>
                        <th>Document</th>
                        <th>Action</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $total = 0; foreach ($wallet_data as $wd){ 
                            foreach($user_has_wallet_type as $haswallet){
                                if($wd->idwallet_type == $haswallet->idwallet){ ?>
                                    <tr>
                                        <td><?php echo $sr++; ?></td>
                                        <td><?php echo $wd->date; ?></td>
                                        <td><?php echo $wd->branch_name; ?></td>
                                        <td><?php echo $wd->wallet_type ?></td>
                                        <td><?php echo $wd->amount; $total = $total + $wd->amount; ?></td>
                                        <td><?php echo $wd->petti_remark ?></td>
                                        <td><b><?php  if($wd->fileupload){?> <a style="color: #6666ff" target="_blank" href="<?php echo base_url()?><?php echo $wd->fileupload?>">Documet Uploaded</a><?php } ?></b></td>
                                        <td><?php  if($wd->idwallet_type != 3 ){?><a target="_blank" href="<?php echo base_url()?>Expense/emp_salary_details/<?php echo $wd->date?>/<?php echo $wd->idbranch?>/<?php echo $wd->idwallet_type?>" class="btn btn-floating btn-primary"><span class="mdi mdi-share"></span></a> <?php } ?></td>
                                    </tr>
                        <?php } } } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $total; ?></b></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            <?php  }else{ ?>
                <table class="table table-bordered table-condensed" id="wallet_balance_report">
                    <thead style="background-color: #99ccff">
                        <th>Sr.</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Wallet Type</th>
                        <th>Wallet Amount</th>
                        <th>Document</th>
                    </thead>
                    <tbody class="data_1">
                        <?php $sr=1; $total = 0; foreach ($wallet_data as $wd){ ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo $wd->date; ?></td>
                            <td><?php echo $wd->branch_name; ?></td>
                            <td><?php echo $wd->wallet_type ?></td>
                            <td><?php echo $wd->amount; $total = $total + $wd->amount; ?></td>
                            <td><b><?php  if($wd->fileupload){?> <a style="color: #6666ff" target="_blank" href="<?php echo base_url()?><?php echo $wd->fileupload?>">Documet Uploaded</a><?php } ?></b></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b><?php echo $total; ?></b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            <?php }
        }
    }
    
    public function emp_salary_details($date, $idbranch, $idwallet){
        $q['tab_active'] = 'Wallet Report';
        $q['empslary_data'] = $this->Expense_model->get_emp_salary_details($date, $idbranch, $idwallet);
        $this->load->view('expense/wallet_balace_report_details', $q);
    }
    public function ajax_delete_emp_salary(){
        $res = 1;
        $idempsalary = $this->input->post('idempsalary');
        $saldata = $this->Expense_model->get_emp_salary_data_byid($idempsalary);
        $petty_data = $this->Expense_model->get_petti_cash_data_byempsalary_data($saldata->idbranch, $saldata->date, $saldata->idwallet);
        if($saldata){
            $del_history = array(
                'idbranch' => $saldata->idbranch,
                'branch_name' => $saldata->branch_name,
                'empid' => $saldata->empid,
                'emp_name' => $saldata->emp_name,
                'amount' => $saldata->amount,
                'date' => $saldata->date,
                'entry_time' => $saldata->entry_time,
                'status' => $saldata->status,
                'created_by' => $saldata->created_by,
                'idwallet' => $saldata->idwallet,
                'updated_by' => $_SESSION['id_users'],
                'updated_datetime' => date('Y-m-d H:i:s'),
            );
            if( $this->Expense_model->save_emp_salary_delete_history($del_history)){
                $pettydata = array(
                    'amount' => $petty_data->amount - $saldata->amount,
                );
                if($this->Expense_model->update_petti_cash_data($pettydata, $petty_data->id_petti_cash)){
                    $this->Expense_model->delete_emp_salary_data($idempsalary);
                    $res = 0;
                }
            }
        }else{
            $res = 1;
        }
        echo $res;
    }
    public function ajax_get_branch_data(){
        $branch_data = $this->Audit_model->get_active_branch_data();
        ?>
        <select class="form-control input-sm chosen-select " style="width: auto;" name="branchid" id="branchid">
            <option value="">Select Branch</option>
            <?php foreach ($branch_data as $branch){
                if($emps->idbranch != $branch->id_branch){?>
                    <option value="<?php echo $branch->id_branch;?>"><?php echo $branch->branch_name;?></option>
            <?php  } } ?>
        </select>
        <?php 
    }

    public function ajax_update_emp_salary(){
        $res = 1;
        $idempsalary = $this->input->post('idempsalary');
        $idbranch = $this->input->post('idbranch');
        $branch_name = $this->input->post('branch_name');
        $oldbranch = $this->input->post('oldbranch');
        $idstatus = $this->input->post('idstatus');
        $date = $this->input->post('date');
        $amount = $this->input->post('amount');
        $idwallet = $this->input->post('idwallet');
        
        if($idstatus == 3){
            //check oldbranch petticash
            $branch_bal = $this->Expense_model->get_petti_cash_data_byempsalary_data($oldbranch, $date, $idwallet);
            
            //check transfer branch petti cash data
            $transfer_branch_bal = $this->Expense_model->get_petti_cash_data_byempsalary_data($idbranch, $date, $idwallet);
            
            $data = array(
                'idbranch' => $idbranch,
                'branch_name' => $branch_name,
                'status' => $idstatus,
                'oldbranch' => $oldbranch,
            );
            if($this->Expense_model->update_emp_salary($idempsalary, $data)){
                if($transfer_branch_bal){
                    //Transfer branch petti cash balance add
                    $transfer_branch_data = array(
                        'amount' => $transfer_branch_bal->amount + $amount,
                    );
                    if( $this->Expense_model->update_petti_cash_data($transfer_branch_data, $transfer_branch_bal->id_petti_cash)){
                        //minus amount from old branch
                        $update_bala = array(
                            'amount' => $branch_bal->amount - $amount,
                        );
                        $this->Expense_model->update_petti_cash_data($update_bala, $branch_bal->id_petti_cash);
                            $res = 0;
                    }else{
                        $res = 1;
                    }
                }else{
                    $pettydata = array(
                        'date' => date('Y-m-d'),
                        'idbranch' => $idbranch,
                        'amount' => $amount,
                        'idwallet_type' => $idwallet,          
                        'created_by' => $_SESSION['id_users'],
                        'month' => date('M', strtotime($date)),
                        'year' => date('Y', strtotime($date)),
                        'month_year' => date('Y-m'),
                    );
                    if($this->Expense_model->save_petty_cash($pettydata)){
                        $update_bala = array(
                            'amount' => $branch_bal->amount - $amount,
                        );
                        $this->Expense_model->update_petti_cash_data($update_bala, $branch_bal->id_petti_cash);
                         $res = 0;
                    }else{
                        $res = 1;
                    }
                }
            }else{
                 $res = 1;
            }
       }else{
            $data = array(
                'status' => $idstatus,
            );
            if($this->Expense_model->update_emp_salary($idempsalary, $data)){
                $res = 0;
            }
       }
       echo $res;
        
    }
    
    public function salary_incentive_expense($idwallet){
        $q['tab_active'] = 'Expense';
        $idbranch = $_SESSION['idbranch'];
        if($this->session->userdata('level') == 2){   // Branch Accountant
            $q['petty_cash_data'] = $this->Expense_model->get_branch_petty_cash_data_byidbranch($idbranch);
        }elseif($this->session->userdata('level') == 3){ 
            $q['petty_cash_data'] = $this->Expense_model->get_user_petty_cash_data_byiduser($_SESSION['id_users']);
        }

        $q['branch_aval_bal'] = $this->Expense_model->ajax_get_branch_petticash_data_byid($idbranch, $idwallet);
        $q['expense_data'] = $this->Expense_model->ajax_get_branch_expense_data_byid($idbranch, $idwallet);
        $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data_byid($idwallet);
        
        //Todays Available Cash
        $date = date('Y-m-d');
        $q['total_daybook_cash'] = $this->Report_model->get_sum_daybook_cash_byidbranch_lastdate($idbranch, $date);
        $q['todays_cash'] = $this->Report_model->get_todays_daybooksum_byidbranch_groupby_entrytype($idbranch, $date); 
        $q['todays_cash_closure'] = $this->Reconciliation_model->get_todays_cash_closure_byidbranch($idbranch); // cash closure data
        
        $this->load->model('Sale_model');
        $q['sale_last_entry_byidbranch'] = $this->Sale_model->get_sale_last_day_entry_byidbranch($idbranch); // cash closure data
        $q['cash_closure_last_entry'] = $this->Sale_model->get_cash_closure_last_entry_byidbranch($idbranch); // cash closure data

        $q['idwallet'] = $idwallet;
        
        $q['emp_salary_data'] = $this->Expense_model->get_employee_salary_byid($idbranch, $idwallet);
        $q['heade_data'] = $this->Expense_wallet_model->ajax_get_expense_head_byidwallet($idwallet);
        $this->load->view('expense/salary_incentive_expense', $q);
    }
    public function ajax_save_expense(){
        $res = 1;
        $approval_status = $this->input->post('status');
        $idempsalary = $this->input->post('idempsalary');
        $empsadata = $this->Expense_model->get_emp_salary_data_byid($idempsalary);
        if($empsadata->status == 0 || $empsadata->status == 3){
       /* if($approval_status == 0){ */
            if($this->input->post('idexpenssubehead') == ''){
                $idexp_subhead = NULL;
            }else{
                $idexp_subhead = $this->input->post('idexpenssubehead');
            }

            $branch_aval_cash = $this->input->post('avalbal') -  $this->input->post('amount');

            $data = array(
                'idbranch' => $_SESSION['idbranch'],
                'id_wallet' => $this->input->post('idwallettype'),
                'idexpense_head' => $this->input->post('idexpensehead'),
                'id_expensesubhead' => $idexp_subhead,
                'expense_amount' => $this->input->post('amount'),
                'status' => $this->input->post('status'),
                'approve_expense_amount ' => $this->input->post('amount'),
                'approved_status' => 3,
                'created_by' => $_SESSION['id_users'],
                'entry_date' => $this->input->post('date'),
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
                if($this->Expense_model->save_daybook_expense_cash($data_daybook)){
                    
                    $empsal = array(
                        'status' => 1,
                        'updated_by' => $_SESSION['id_users'],
                        'updated_datetime' => date('Y-m-d H:i:s'),
                    );
                    if($this->Expense_model->update_emp_salary($idempsalary, $empsal)){
                        $res =0;
                    }
                }else{
                    $res =1;
                }
            }
        } else{
            $res = 2;
        }
        echo $res;

            
      /*  }else{
            //need Approval
             if($this->input->post('idexpenssubehead') == ''){
                $idexp_subhead = NULL;
            }else{
                $idexp_subhead = $this->input->post('idexpenssubehead');
            }
            $data = array(
                'idbranch' => $_SESSION['idbranch'],
                'id_wallet' => $this->input->post('idwallettype'),
                'idexpense_head' => $this->input->post('idexpensehead'),
                'id_expensesubhead' => $idexp_subhead,
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
        }*/
       
    }
    public function salary_incentive_report(){
        $q['tab_active'] = 'Expense';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['wallet_type'] = $this->Expense_wallet_model->get_wallet_type_data();
        
        $this->load->view('expense/salary_incentive_report', $q);
    }
    public function ajax_get_salary_incentive_report(){
        $idbranch = $this->input->post('idbranch');
        $branches = $this->input->post('branches');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idwallet = $this->input->post('idwallet');
        $allwallet = $this->input->post('allwallet');
        
        $emp_salarydata = $this->Expense_model->ajax_get_employee_salsry_data($idbranch, $branches, $from, $to, $idwallet, $allwallet);
        if(count($emp_salarydata) > 0){ ?>
            <table class="table table-bordered table-condensed" id="salary_incentive_report">
                <thead style="background-color: #95bdfb" class="fixheader">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Wallet Type</th>
                    <th>Branch</th>
                    <th>Emp Id</th>
                    <th>Emp Name</th>
                    <th>Amount</th>
                    <th>Paid Date</th>
                    <th>Status</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; $total =0; foreach ($emp_salarydata as $emps){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $emps->date; ?></td>
                        <td><?php echo $emps->wallet_type?></td>
                        <td><?php echo $emps->branch_name?></td>
                        <td><?php echo $emps->empid;?></td>
                        <td><?php echo $emps->emp_name;?></td>
                        <td><?php echo $emps->amount; $total = $total+$emps->amount; ?></td>
                        <td><?php echo date($emps->updated_datetime); ?></td>
                        <td><?php if($emps->status == 0){ echo 'Pending'; } elseif ($emps->status == 1){ echo 'Paid'; }elseif($emps->status == 2){ echo 'On Hold'; }elseif($emps->status == 3){ echo 'Pending'; } ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b><?php echo $total; ?></b></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
    
}