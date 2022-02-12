<?php
class Expense_model extends CI_Model{
    
    public function save_petty_cash($data){
        return $this->db->insert('petti_cash', $data);
    }
//    public function get_branch_available_petti_cash($idbranch){
//        return $this->db->select('branch.petti_cash_balance as aval_balance')
//                        ->where('id_branch', $idbranch)
//                        ->get('branch')->row();
//    }
    public function get_branch_available_petti_cash($idbranch){
        return $this->db->select('sum(amount) as aval_balance, petti_cash.*, expense_wallet_type.wallet_type')
                        ->where('idbranch', $idbranch)
                        ->where('idwallet_type = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->group_by('idwallet_type')
                        ->get('petti_cash')->result();
    }
    
    public function ajax_get_branch_petticash_data_byid($idbranch, $idwallet){
         return $this->db->select('sum(amount) as aval_balance')
                        ->where('idbranch', $idbranch)
                        ->where('idwallet_type', $idwallet)
                        ->get('petti_cash')->row();
    }
    
    public function update_branch_petti_cash($branch_aval_cash, $idbranch){
        return $this->db->where('id_branch', $idbranch)->update('branch', $branch_aval_cash);
    }
//    public function update_branch_petti_cash_byamount($branch_aval_cash, $idbranch){
//        return $this->db->where('id_branch', $idbranch)
//                    ->set('petti_cash_balance', 'petti_cash_balance+'.$branch_aval_cash, FALSE)      
//                    ->update('branch');
//    }
    public function get_petty_cash_data(){
        $month = date('M');
        $year = date('Y');
        return $this->db->where('idbranch = branch.id_branch')->from('branch')
                        ->where('month', $month)
                        ->where('year', $year)
                        ->where('idwallet_type = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('petti_cash')->result();
    }
    public function update_petty_cash($data, $id){
        return $this->db->where('id_petti_cash', $id)->update('petti_cash', $data);
    }
    public function get_user_has_wallet(){
        return $this->db->where('user_role.has_wallet',1)
                        ->where('iduserrole = user_role.id_userrole')->from('user_role')
                        ->get('users')->result();
    }
    public function save_user_petty_cash($data){
        return $this->db->insert('user_petti_cash', $data);
    }
    public function get_user_petty_cash_data(){
        return $this->db->where('iduser = users.id_users')->from('users')->get('user_petti_cash')->result();
    }
    public function update_user_petty_cash($data, $id){
        return $this->db->where('id_user_petti_cash', $id)->update('user_petti_cash', $data);
    }
    
    public function get_branch_petty_cash_data_byidbranch($id){
        return $this->db->where('idbranch', $id)->get('petti_cash')->result();
    }
    
    public function ajax_get_wallet_data_byfilter($idbranch, $from, $to, $branches){
        if($idbranch == 0){
            $viewbranches = explode(',',$branches);
        }else{
            $viewbranches[] = $idbranch;
        }   
            
        return $this->db->select('branch.branch_name, expense_wallet_type.wallet_type, petti_cash.*')
                        ->where_in('idbranch', $viewbranches)
                        ->where('date <=', $to)
                        ->where('date >=', $from)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('idwallet_type = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('petti_cash')->result();
        
    }

//    public function get_branch_active_petti_cash($id){
//        return $this->db->select('sum(amount) as total_amount' )->where('idbranch', $id)->where('status',0)->get('petti_cash')->row();
//    }
//    public function get_branch_approved_expense_cash($id){
//        $status = array(1,3);
//        return $this->db->where('idbranch', $id)->where_in('approved_status',$status)->get('expense')->result();
//    }
    public function get_user_petty_cash_data_byiduser($iduser){
        return $this->db->where('iduser', $iduser)->get('user_petti_cash')->result();
    }
    public function get_expense_head(){
        return $this->db->where('active',1)->get('expense_head')->result();
    }
    public function ajax_get_expensehead_need_approval_byid($idexpense){
        return $this->db->select('need_approval')->where('id_expense_head', $idexpense)->get('expense_head')->row();
    }
    public function save_branch_expense($data){
        $this->db->insert('expense', $data);
        return $this->db->insert_id();
    }
    public function delete_expense_data($idexpense){
        return $this->db->where('id_expense', $idexpense)->delete('expense');
    }
    public function save_branch_expense_histroy($data){
        return $this->db->insert('expense_delete_histroy', $data);
    }
    public function get_branch_expense_data($idbranch){
        return $this->db->select('sum(approve_expense_amount) as exp_amount, expense_wallet_type.*')
                        ->where('idbranch', $idbranch)
                        ->where('id_wallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->group_by('id_wallet')
                        ->get('expense')->result();
    }
    public function get_branch_allexpense_data($idbranch){
        return $this->db->where('idbranch', $idbranch)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->join('expense_head','idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('expense_wallet_type','id_wallet = expense_wallet_type.id_wallet_type', 'left')
                        ->join('expense_subheader','id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->get('expense')->result();
    }
    public function ajax_get_branch_expense_data_byid($idbranch, $idwallet){
        return $this->db->select('sum(approve_expense_amount) as exp_amount')
                        ->where('idbranch', $idbranch)
                        ->where('id_wallet', $idwallet)
                        ->get('expense')->row();
    }
    public function save_daybook_expense_cash($data){
        return $this->db->insert('daybook_cash', $data);
    }
    public function delete_daybook_expense_cash($id){
        return $this->db->where('idtable', $id)->delete('daybook_cash');
    }
    public function update_expense_data($data, $idexpense){
        return $this->db->where('id_expense', $idexpense)->update('expense', $data);
    }
    //approve expense
    public function get_branch_expense_data_for_approval(){
        return $this->db->where('status', 1)
                        ->where('approved_status', 0)
                        ->where('idbranch= branch.id_branch')->from('branch')
                        ->where('idexpense_head = expense_head.id_expense_head')->from('expense_head')
                        ->get('expense')->result();
    }
    
    public function get_expense_data_bymonthyear($idbranch, $monthyear){
        if($idbranch == 0){
           $id = $_SESSION['id_users'];
       
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
      
             return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name, expense_wallet_type.wallet_type as wallet_type,expense_subheader.expense_subheader as expense_subheader')
                        ->where('expense.month_year', $monthyear)
                        ->where_in('expense.idbranch', $branchid)
                        ->where('expense.approved_status', 3)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_wallet_type','expense.id_wallet = expense_wallet_type.id_wallet_type', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('expense_subheader','expense.id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
       
        }else{
             return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name, expense_wallet_type.wallet_type as wallet_type,expense_subheader.expense_subheader as expense_subheader')
                        ->where('expense.idbranch', $idbranch)
                        ->where('expense.month_year', $monthyear)
                        ->where('expense.approved_status', 3)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                         ->join('expense_wallet_type','expense.id_wallet = expense_wallet_type.id_wallet_type', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('expense_subheader','expense.id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
        }
    }
    public function get_expense_data_report($idbranch, $from, $to, $status){
        if($idbranch == 0){
           $id = $_SESSION['id_users'];
       
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
             $branchid[] = $idbranch;
        }
        
        if($status == 0){
            return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name,expense_wallet_type.wallet_type as wallet_type,expense_subheader.expense_subheader as expense_subheader')
                        ->where('expense.entry_date >=', $from)
                        ->where('expense.entry_date <=', $to)
                        ->where_in('expense.idbranch', $branchid)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_wallet_type','expense.id_wallet = expense_wallet_type.id_wallet_type', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('expense_subheader','expense.id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
        }else{
            return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name,expense_wallet_type.wallet_type as wallet_type,expense_subheader.expense_subheader as expense_subheader')
                        ->where('expense.entry_date >=', $from)
                        ->where('expense.entry_date <=', $to)
                        ->where_in('expense.idbranch', $branchid)
                        ->where('expense.approved_status', $status)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_wallet_type','expense.id_wallet = expense_wallet_type.id_wallet_type', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('expense_subheader','expense.id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
        }
            
       
             
    }
    public function get_expense_data_byidexpense($id){
        return $this->db->select('expense.*, branch.branch_name, branch.branch_address, branch.branch_contact, branch.branch_gstno, expense_head.*, users.*, print_head.*')
                        ->where('id_expense', $id)
                        ->where('expense.idbranch = branch.id_branch')->from('branch')
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('users', 'expense.created_by = users.id_users', 'left')
                        ->get('expense')->row();
                        
    }
    
    public function ajax_get_total_petti_cash_summary_data($idbranch, $from, $to){
        if($idbranch == 0){
            $id = $_SESSION['id_users'];
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
            return $this->db->select('sum(amount) as total_cash, petti_cash.date,petti_cash.idbranch, petti_cash.idwallet_type as idwallet_type, branch.branch_name as branch_name, expense_wallet_type.wallet_type as wallet_type')
                        ->where_in('idbranch', $branchid)
                        ->where('date >=', $from)
                        ->where('date <=', $to)
                        ->where('petti_cash.idbranch = branch.id_branch')->from('branch')
                        ->where('petti_cash.idwallet_type = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->group_by('idbranch')
                        ->group_by('month')
                        ->group_by('year')
                        ->group_by('idwallet_type')
                        ->order_by('idbranch,petti_cash.date','ASC')
                        ->get('petti_cash')->result();
        }else{
            return $this->db->select('sum(petti_cash.amount) as total_cash,petti_cash.date, petti_cash.idbranch,petti_cash.idwallet_type as idwallet_type, branch.branch_name as branch_name, expense_wallet_type.wallet_type as wallet_type')
                            ->where('idbranch', $idbranch)
                            ->where('date >=', $from)
                            ->where('date <=', $to)
                            ->where('petti_cash.idbranch = branch.id_branch')->from('branch')
                            ->where('petti_cash.idwallet_type = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                            ->group_by('idbranch')
                            ->group_by('month')
                            ->group_by('year')
                            ->group_by('idwallet_type')
                            ->order_by('idbranch,petti_cash.date','ASC')
                            ->get('petti_cash')->result();
        }
    }
    public function ajax_get_total_expense_summary_data($idbranch, $from, $to){
        if($idbranch == 0){
            
            $id = $_SESSION['id_users'];
       
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
            
            return $this->db->select('sum(expense.approve_expense_amount) as exp_amt, expense.month_year, expense.idbranch, expense.id_wallet, branch.branch_name,expense_wallet_type.wallet_type as wallet_type')
                            ->where_in('expense.idbranch', $branchid)
                            ->where('expense.entry_date >=', $from)
                            ->where('expense.entry_date <=', $to)
                            ->where('expense.idbranch = branch.id_branch')->from('branch')
                            ->where('expense.id_wallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                            ->where('expense.created_by = users.id_users')->from('users')
                            ->group_by('expense.idbranch')
                            ->group_by('expense.month_year')
                            ->group_by('expense.id_wallet')
                            ->get('expense')->result();
        }else{
        
            return $this->db->select('sum(expense.approve_expense_amount) as exp_amt, expense.month_year, expense.idbranch, expense.id_wallet, branch.branch_name,expense_wallet_type.wallet_type as wallet_type')
                            ->where('expense.idbranch', $idbranch)
                            ->where('expense.entry_date >=', $from)
                            ->where('expense.entry_date <=', $to)
                            ->where('expense.idbranch = branch.id_branch')->from('branch')
                            ->where('expense.id_wallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                            ->where('expense.created_by = users.id_users')->from('users')
                            ->group_by('expense.idbranch')
                            ->group_by('expense.month_year')
                            ->group_by('expense.id_wallet')
                            ->get('expense')->result();
        }
    }
    public function ajax_get_expense_summary_data($idbranch, $month_year, $idwallet){
        return $this->db->where('expense.idbranch', $idbranch)
                        ->where('expense.month_year', $month_year)
                        ->where('expense.id_wallet', $idwallet)
                        ->where('expense.idbranch = branch.id_branch')->from('branch')
                        ->where('expense.id_wallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                        ->join('expense_subheader','expense.id_expensesubhead = expense_subheader.id_expense_subheader', 'left')
                        ->where('expense.created_by = users.id_users')->from('users')
                        ->get('expense')->result();
    }
    
    public function save_expense_head($data){
        return $this->db->insert('expense_head', $data);
    }
    
    public function update_expense_head($data, $id){
        return $this->db->where('id_expense_head', $id)->update('expense_head', $data);
    }
    
    public function save_employee_salary($data){
        return $this->db->insert_batch('employee_salary', $data);
    }
    public function get_sum_employe_salary_byidbranch($idwallet,$entrytime,$iduser){
        $str = "select idbranch, sum(amount) as amount from employee_salary where idwallet = $idwallet and created_by = $iduser and entry_time = '".$entrytime."' group by idbranch, idwallet";
        return $this->db->query($str)->result();
    }
    public function get_emp_salary_details($date, $idbranch, $idwallet){
        return $this->db->select('employee_salary.*, branch.branch_name, expense_wallet_type.wallet_type')
                        ->where('date', $date)
                        ->where('idbranch', $idbranch)
                        ->where('idwallet', $idwallet)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('employee_salary')->result();
    }
    public function get_emp_salary_data_byid($idempsalary){
        return $this->db->where('idemployee_salary', $idempsalary)->get('employee_salary')->row();
    }
    public function save_emp_salary_delete_history($del_history){
        return $this->db->insert('employee_salary_delete_history', $del_history);
    }
    public function get_petti_cash_data_byempsalary_data($idbranch, $date, $idwallet){
        return $this->db->where('idbranch', $idbranch)
                        ->where('date', $date)
                        ->where('idwallet_type', $idwallet)
                        ->get('petti_cash')->row(); 
    }
    public function update_petti_cash_data($petti_data, $idpetticash){
        return $this->db->where('id_petti_cash', $idpetticash)->update('petti_cash', $petti_data);
    }
    public function delete_emp_salary_data($idempsalary){
        return $this->db->where('idemployee_salary', $idempsalary)->delete('employee_salary');
    }
    public function update_emp_salary($idempsalary, $data){
        return $this->db->where('idemployee_salary', $idempsalary)->update('employee_salary', $data);
    }
    public function get_employee_salary_byid($idbranch, $idwallet){
        return $this->db->select('employee_salary.*, branch.branch_name, expense_wallet_type.wallet_type')
                        ->where('idbranch', $idbranch)
                        ->where('idwallet', $idwallet)
                        ->where('status !=',1)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('employee_salary')->result();
    }
    public function ajax_get_employee_salsry_data($idbranch, $branches, $from, $to, $idwallet, $allwallet){
        
        if($idbranch == 0){
            $viewbranches = explode(',',$branches);
        }else{
            $viewbranches[] = $idbranch;
        }   
        
        if($idwallet == 0){
            $viewwallet = explode(',',$allwallet);
        }else{
            $viewwallet[] = $idwallet;
        }   
        
        return $this->db->select('employee_salary.*, branch.branch_name, expense_wallet_type.wallet_type')
                        ->where_in('idbranch', $viewbranches)
                        ->where_in('idwallet', $viewwallet)
                        ->where('date >=', $from)
                        ->where('date <=', $to)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('employee_salary')->result();
    }
}
?>