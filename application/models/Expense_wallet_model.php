<?php
class Expense_wallet_model extends CI_Model{
    
    public function get_wallet_type_data() {
        return $this->db->get('expense_wallet_type')->result();
    }
    public function get_wallet_type_data_byid($idwallet) {
        return $this->db->where('id_wallet_type', $idwallet)->get('expense_wallet_type')->row();
    }
    
    public function get_expense_header_data() {
        return $this->db->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')->get('expense_head')->result();
    }
    
    public function get_expense_subheader_data() {
        return $this->db->where('id_wallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->where('id_header = expense_head.id_expense_head')->from('expense_head')->get('expense_subheader')->result();
    }
    
    public function get_wallet_type_byidheader($idhead) {
        return $this->db->where('id_expense_head', $idhead)->get('expense_head')->row();
    }
    
    public function save_expense_wallet_type($data) {
        return $this->db->insert('expense_wallet_type', $data);
    }
    public function save_expense_header($data) {
        return $this->db->insert('expense_header', $data);
    }
    public function save_expense_subheader($data) {
        return $this->db->insert('expense_subheader', $data);
    }
    public function edit_expense_wallet_type($data, $id) {
        return $this->db->where('id_wallet_type', $id)->update('expense_wallet_type', $data);
    }
    public function edit_expense_header($data, $id) {
        return $this->db->where('id_expense_head', $id)->update('expense_head', $data);
    }
    public function edit_expense_subheader($data, $id) {
        return $this->db->where('id_expense_subheader', $id)->update('expense_subheader', $data);
    }
    public function ajax_get_expense_head_byidwallet($idwallet){
        return $this->db->where('idwallet', $idwallet)
                        ->where('idwallet = expense_wallet_type.id_wallet_type')->from('expense_wallet_type')
                        ->get('expense_head')->result();
    }
    public function ajax_get_expense_subhead_byidhead($idhead){
        return $this->db->where('id_header', $idhead)
                        ->where('id_header = expense_head.id_expense_head')->from('expense_head')
                        ->get('expense_subheader')->result();
    }
    
    








































//    public function get_branch_available_petti_cash($idbranch){
//        return $this->db->select('branch.petti_cash_balance as aval_balance')
//                        ->where('id_branch', $idbranch)
//                        ->get('branch')->row();
//    }
    public function get_branch_available_petti_cash($idbranch){
        return $this->db->select('sum(amount) as aval_balance, petti_cash.*')
                        ->where('idbranch', $idbranch)
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
        return $this->db->where('idbranch = branch.id_branch')->from('branch')->get('petti_cash')->result();
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
        return $this->db->where('idbranch', $idbranch)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('idexpense_head = expense_head.id_expense_head')->from('expense_head')
                        ->get('expense')->result();
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
      
             return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name')
                        ->where('expense.month_year', $monthyear)
                        ->where_in('expense.idbranch', $branchid)
                        ->where('expense.approved_status', 3)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
       
        }else{
             return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name')
                        ->where('expense.idbranch', $idbranch)
                        ->where('expense.month_year', $monthyear)
                        ->where('expense.approved_status', 3)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
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
            return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name')
                        ->where('expense.entry_date >=', $from)
                        ->where('expense.entry_date <=', $to)
                        ->where_in('expense.idbranch', $branchid)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
                        ->join('users c', 'expense.created_by = c.id_users', 'left')
                        ->join('users a', 'expense.approved_by = a.id_users', 'left')
                        ->get('expense')->result();
        }else{
            return $this->db->select('expense.*, branch.branch_name as branch_name, expense_head.expense_type as expense_type, c.user_name as created_by_name, a.user_name as approved_by_name')
                        ->where('expense.entry_date >=', $from)
                        ->where('expense.entry_date <=', $to)
                        ->where_in('expense.idbranch', $branchid)
                        ->where('expense.approved_status', $status)
                        ->join('branch','expense.idbranch = branch.id_branch', 'left')
                        ->join('expense_head','expense.idexpense_head = expense_head.id_expense_head', 'left')
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
            return $this->db->select('sum(amount) as total_cash, petti_cash.date, branch.*')
                        ->where_in('idbranch', $branchid)
                        ->where('date >=', $from)
                        ->where('date <=', $to)
                        ->where('petti_cash.idbranch = branch.id_branch')->from('branch')
                        ->group_by('idbranch')
                        ->group_by('month')
                        ->group_by('year')
                        ->get('petti_cash')->result();
        }else{
            return $this->db->select('sum(amount) as total_cash,petti_cash.date, branch.*')
                            ->where('idbranch', $idbranch)
                            ->where('date >=', $from)
                            ->where('date <=', $to)
                            ->where('petti_cash.idbranch = branch.id_branch')->from('branch')
                            ->group_by('idbranch')
                            ->group_by('month')
                            ->group_by('year')
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
            
            return $this->db->select('sum(expense.approve_expense_amount) as exp_amt, expense.month_year, expense.idbranch, branch.branch_name')
                            ->where_in('expense.idbranch', $branchid)
                            ->where('expense.entry_date >=', $from)
                            ->where('expense.entry_date <=', $to)
                            ->where('expense.idbranch = branch.id_branch')->from('branch')
                            ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                            ->where('expense.created_by = users.id_users')->from('users')
                            ->group_by('expense.idbranch')
                            ->group_by('expense.month_year')
                            ->get('expense')->result();
        }else{
        
            return $this->db->select('sum(expense.approve_expense_amount) as exp_amt, expense.month_year, expense.idbranch, branch.branch_name')
                            ->where('expense.idbranch', $idbranch)
                            ->where('expense.entry_date >=', $from)
                            ->where('expense.entry_date <=', $to)
                            ->where('expense.idbranch = branch.id_branch')->from('branch')
                            ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                            ->where('expense.created_by = users.id_users')->from('users')
                            ->group_by('expense.idbranch')
                            ->group_by('expense.month_year')
                            ->get('expense')->result();
        }
    }
    public function ajax_get_expense_summary_data($idbranch, $month_year){
        return $this->db->where('expense.idbranch', $idbranch)
                        ->where('expense.month_year', $month_year)
                        ->where('expense.idbranch = branch.id_branch')->from('branch')
                        ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                        ->where('expense.created_by = users.id_users')->from('users')
                        ->get('expense')->result();
    }
    
    public function save_expense_head($data){
        return $this->db->insert('expense_head', $data);
    }
    
    public function update_expense_head($data, $id){
        return $this->db->where('id_expense_head', $id)->update('expense_head', $data);
    }
}
?>