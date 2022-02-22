<?php
class Report_model extends CI_Model{
    // Get All
    public function ajax_get_daybook_sale_report($datefrom, $dateto, $idbranch, $branches){
        $payment_mode_data = $this->db->where('active = 1')->get('payment_mode')->result();
        if($idbranch == ''){
            $str = "SELECT inv_no, sale_payment.date, branch.branch_name, sale_payment.idsale, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." , ";
                    }
                    $str1 = rtrim($str, ', ');
            $str1 .= " FROM sale_payment, branch where idbranch in (".$branches.") and branch.id_branch = sale_payment.idbranch and sale_payment.date between '".$datefrom."' and '".$dateto."' group by idsale";
        }else{
            $str = "SELECT inv_no, sale_payment.date, branch.branch_name, sale_payment.idsale, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." , ";
                    }
            $str1 = rtrim($str, ', ');
            $str1 .= " FROM sale_payment, branch where idbranch = ".$idbranch." and branch.id_branch = sale_payment.idbranch and sale_payment.date between '".$datefrom."' and '".$dateto."' group by idsale";
        }
//        die($str1);
        return $this->db->query($str1)->result();
    }
    public function ajax_get_opening_cash_bydate($datefrom,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->select('SUM(amount) as daybook_cash')
                    ->where_in('idbranch', $branches)
                    ->where('date <', $datefrom)
                    ->get('daybook_cash')->row();
        }else{
            return $this->db->select('SUM(amount) as daybook_cash')
                    ->where('idbranch', $idbranch)
                    ->where('date <', $datefrom)
                    ->get('daybook_cash')->row();
        }
    }
    public function ajax_get_daybook_sales_return_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->where_in('idbranch', $branches)
                    ->where('date >=', $datefrom)
                    ->where('sales_return_type != 3')
                    ->where('date <=', $dateto)
                    ->where('idbranch = branch.id_branch')->from('branch')
                    ->get('sales_return')->result();
        }else{
            return $this->db->where('idbranch', $idbranch)
                    ->where('date >=', $datefrom)
                    ->where('date <=', $dateto)
                    ->where('sales_return_type != 3')
                    ->where('idbranch = branch.id_branch')->from('branch')
                    ->get('sales_return')->result();
        }
    }
    public function ajax_get_daybook_expense_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->select('expense.entry_time, expense_head.expense_type, expense.approve_expense_amount, branch.branch_name')
                            ->where_in('idbranch', $branches)
                            ->where('idbranch = branch.id_branch')->from('branch')
                            ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                            ->where('entry_date >=', $datefrom)
                            ->where('entry_date <=', $dateto)
                            ->get('expense')->result();
        }else{
            return $this->db->select('expense.entry_time, expense_head.expense_type, expense.approve_expense_amount, branch.branch_name')
                        ->where('idbranch',$idbranch)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('expense.idexpense_head = expense_head.id_expense_head')->from('expense_head')
                        ->where('entry_date >=', $datefrom)
                        ->where('entry_date <=', $dateto)
                        ->get('expense')->result();
        }
    }
    public function ajax_get_daybook_accessories_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->select('daybook_cash.amount as access_cash, branch.branch_name, daybook_cash.date')
                            ->where('entry_type',10)
                            ->where_in('idbranch', $branches)
                            ->where('idbranch = branch.id_branch')->from('branch')
                            ->where('date >=', $datefrom)
                            ->where('date <=', $dateto)
                            ->get('daybook_cash')->result();
        }else{
            return  $this->db->select('daybook_cash.amount as access_cash, branch.branch_name, daybook_cash.date')
                        ->where('entry_type',10)
                        ->where('idbranch',$idbranch)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->where('date >=', $datefrom)
                        ->where('date <=', $dateto)
                        ->get('daybook_cash')->result();   
        }
    }
    public function ajax_get_cash_deposite_to_bank_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
                            ->where_in('cash_deposite_to_bank.idbranch', $branches)
                            ->where('cash_deposite_to_bank.date >=', $datefrom)
                            ->where('cash_deposite_to_bank.date <=', $dateto)
                            ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
                            ->get('cash_deposite_to_bank')->result();
        }else{
            return $this->db->where('cash_deposite_to_bank.idbranch', $idbranch)
                            ->where('cash_deposite_to_bank.date >=', $datefrom)
                            ->where('cash_deposite_to_bank.date <=', $dateto)
                            ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
                            ->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
                            ->get('cash_deposite_to_bank')->result();
        }
    }
    public function  ajax_get_daybook_credit_buyback_recieve_report($datefrom, $dateto, $idbranch, $branches){
        $payment_mode_data = $this->db->get('payment_mode')->result();
        if($idbranch == ''){
            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM payment_reconciliation, branch where idbranch in (".$branches.") and from_credit_buyback_received = 1 and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by idsale";
            return $this->db->query($str1)->result();
        }else{
            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM payment_reconciliation, branch where from_credit_buyback_received = 1 and idbranch = ".$idbranch." and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by idsale";
//            die($str1);
            return $this->db->query($str1)->result();
        }
    }
    
    
    public function get_sum_daybook_cash_byidbranch_lastdate($idbranch, $date) {
        return $this->db->select('SUM(amount) as sum_cash')->where('idbranch',$idbranch)
                        ->where('date <', $date)->get('daybook_cash')->row();
    }
    public function get_todays_daybooksum_byidbranch_type($idbranch, $date, $entry_type) {
        return $this->db->select('SUM(amount) as todays_sale_cash')->where('idbranch',$idbranch)
                        ->where('date', $date)->where('entry_type', $entry_type)->get('daybook_cash')->row();
    }
    public function get_todays_daybooksum_byidbranch_groupby_entrytype($idbranch, $date) {
        return $this->db->select('SUM(amount) as todays_cash, entry_type')->where('idbranch',$idbranch)
                        ->where('date', $date)->group_by('entry_type')->get('daybook_cash')->result();
    }
    public function todays_short_deposit_sum($idbranch, $date) {
        return $this->db->select('SUM(remaining_after_deposit) as short_deposit')->where('idbranch',$idbranch)
                        ->where('date', $date)->get('cash_deposite_to_bank')->row();
    }
    public function ajax_cash_closure_report($datefrom,$dateto,$idbranch) {
        $id = $_SESSION['id_users'];
        if($idbranch == 0){
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where('ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }else{
            $branchid[] = $idbranch;
        }
//        die($idbranch);
//        if($idbranch == ''){
//            return $this->db->where('cash_closure.idbranch = branch.id_branch')->from('branch')
//                            ->where('cash_closure.date >=', $datefrom)
//                            ->where('cash_closure.date <=', $dateto)
//                            ->get('cash_closure')->result();
//        }else{
            return $this->db->where_in('cash_closure.idbranch',$branchid)
                            ->where('cash_closure.date >=', $datefrom)
                            ->where('cash_closure.date <=', $dateto)
                            ->where('cash_closure.idbranch = branch.id_branch')->from('branch')
                            ->get('cash_closure')->result();
//        }
    }
    public function closer_details_byid($id){
        return $this->db->where('idcash_closure', $id)
                        ->where('closure_denomination.idbranch = branch.id_branch')->from('branch')
                        ->get('closure_denomination')->result();
    }

    public function ajax_cash_deposit_report($datefrom,$dateto,$idbranch) {
        
        $id = $_SESSION['id_users'];
        if($idbranch == 0){
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where('ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }else{
            $branchid[] = $idbranch;
        }
        return $this->db->where_in('idbranch',$branchid)
                        ->where('cash_deposite_to_bank.date >=', $datefrom)
                        ->where('cash_deposite_to_bank.date <=', $dateto)
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
                        ->get('cash_deposite_to_bank')->result();
       
    }
    
    public function ajax_cash_ledger_report($datefrom,$dateto,$idbranch) {
         $id = $_SESSION['id_users'];
        if($idbranch == 0){
            if($this->session->userdata('level') == 1){  //admin all branch
               $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
               $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where('ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }else{
            $branchid[] = $idbranch;
        }
            return $this->db->where_in('idbranch',$branchid)
                            ->where('daybook_cash.date >=', $datefrom)
                            ->where('daybook_cash.date <=', $dateto)
                            ->where('daybook_cash.idbranch = branch.id_branch')->from('branch')
                            ->where('daybook_cash.entry_type = cash_entry_type.id_cash_entry_type')->from('cash_entry_type')
                            ->order_by('id_daybook_cash')
                            ->get('daybook_cash')->result();
    }
    public function ajax_cheque_bounce_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == 0){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        return $this->db->select('branch.branch_name, users.user_name, cheque_bounce_history.*')
                        ->where_in('cheque_bounce_history.idbranch',$branches)
                        ->where('cheque_bounce_history.bounce_date >=', $datefrom)
                        ->where('cheque_bounce_history.bounce_date <=', $dateto)
                        ->where('cheque_bounce_history.idbranch = branch.id_branch')->from('branch')
                        ->where('cheque_bounce_history.entry_by = users.id_users')->from('users')
                        ->order_by('id_cheque_bounce_history', 'desc')
                        ->get('cheque_bounce_history')->result();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /////////////////// VINAYAK P START/////////////////////
    
    public function get_inter_state_data_by_date($datefrom = '', $dateto = '',$idcomapny) {

        $this->db->select('pc.product_category_name,cat.category_name,cat.hsn,inter_state.*,itp.*,c.company_gstin as gst_no_to,cstk.company_gstin as gst_no_from,c.company_name as company_to,cstk.company_name as company_from,b.branch_name as branch_to,stk.branch_name as branch_from,g.godown_name,mv.full_name,brd.brand_name');                       
        if ($idcomapny != '') {
            $this->db->where('inter_state.idcompany_from', $idcomapny);
        }        
        $this->db->join('inter_state_product itp','inter_state.id_inter_state = itp.idinterstate');
        $this->db->join('model_variants mv','mv.id_variant=itp.idvariant');
        $this->db->where('g.id_godown=itp.idgodown')->from('godown g');   
        $this->db->where('brd.id_brand=itp.idbrand')->from('brand brd');   
        if ($datefrom == '' && $dateto == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-10 days", strtotime($dateto)));
        }
        if ($datefrom != '' && $dateto != '') {
            $this->db->where('inter_state.date >=', $datefrom)->where('inter_state.date <=', $dateto);
        }
        $this->db->where('itp.idcategory= cat.id_category')->from('category cat');
        $this->db->where('itp.idproductcategory= pc.id_product_category')->from('product_category pc');    
        $this->db->where('inter_state.idbranch_to= b.id_branch')->from('branch b')
                ->join('(select bb.branch_name,bb.id_branch from branch bb ) stk','`stk`.`id_branch`=inter_state.idbranch_from','left');        
        $this->db->where('inter_state.idcompany_to= c.company_id')->from('company c')
                ->join('(select cc.company_name,cc.company_id,cc.company_gstin from company cc) cstk','`cstk`.`company_id`=inter_state.idcompany_from','left');
        $this->db->order_by('inter_state.date,inter_state.idoutward_transfer', 'desc');
        return $this->db->get('inter_state')->result();
//        die(print_r($this->db->last_query()));
    }
    
     public function get_inter_state_purchase_by_date($datefrom = '', $dateto = '',$idcomapny) {

        $this->db->select('pc.product_category_name,cat.category_name,cat.hsn,inter_state.*,itp.*,c.company_gstin as gst_no_to,cstk.company_gstin as gst_no_from,c.company_name as company_to,cstk.company_name as company_from,b.branch_name as branch_to,stk.branch_name as branch_from,g.godown_name,mv.full_name,brd.brand_name');                       
        if ($idcomapny != '') {
            $this->db->where('inter_state.idcompany_to', $idcomapny);
        }        
        $this->db->join('inter_state_product itp','inter_state.id_inter_state = itp.idinterstate');
        $this->db->join('model_variants mv','mv.id_variant=itp.idvariant');
        $this->db->where('g.id_godown=itp.idgodown')->from('godown g');   
        $this->db->where('brd.id_brand=itp.idbrand')->from('brand brd');   
        if ($datefrom == '' && $dateto == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-10 days", strtotime($dateto)));
        }
        if ($datefrom != '' && $dateto != '') {
            $this->db->where('inter_state.date >=', $datefrom)->where('inter_state.date <=', $dateto);
        }
        $this->db->where('itp.idcategory= cat.id_category')->from('category cat');
        $this->db->where('itp.idproductcategory= pc.id_product_category')->from('product_category pc');  
        $this->db->where('inter_state.idbranch_to= b.id_branch')->from('branch b')
                ->join('(select bb.branch_name,bb.id_branch from branch bb ) stk','`stk`.`id_branch`=inter_state.idbranch_from','left');        
        $this->db->where('inter_state.idcompany_to= c.company_id')->from('company c')
                ->join('(select cc.company_name,cc.company_id,cc.company_gstin from company cc) cstk','`cstk`.`company_id`=inter_state.idcompany_from','left');
        $this->db->order_by('inter_state.date,inter_state.idoutward_transfer', 'desc');
        return $this->db->get('inter_state')->result();
//        die(print_r($this->db->last_query()));
    }
    /////////////////// VINAYAK P END/////////////////////
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // Opening cash from sale from, to date
    public function ajax_get_sum_sale_opening_fromdate_cash($datefrom, $dateto, $idbranch){
            $str = "SELECT SUM(amount) as available_cash "
                    . "FROM payment_reconciliation where idpayment_mode = 1 and idbranch = ".$idbranch." and entry_time between '".$datefrom."' and '".$dateto."'";
            return $this->db->query($str)->row();
    }
    // Opening cash from sale less than from date
    public function ajax_get_sum_sale_opening_cash($datefrom, $idbranch){
            $str = "SELECT SUM(amount) as available_cash "
                    . "FROM payment_reconciliation where idpayment_mode = 1 and idbranch = ".$idbranch." and entry_time <= '".$datefrom."'";
            return $this->db->query($str)->row();
    }
    // Used cash from sales return from, to date
    public function get_sum_opening_sales_return_fromdate_cash($datefrom, $dateto, $idbranch){
        $str = "SELECT SUM(final_total) as sales_return_cash "
                . "FROM sales_return where idbranch = ".$idbranch." and entry_time between '".$datefrom."' and '".$dateto."'";
        return $this->db->query($str)->row();
    }
    // Used cash from sales return less than from date
    public function get_sum_opening_sales_return_cash($datefrom, $idbranch){
        $str = "SELECT SUM(final_total) as sales_return_cash "
            . "FROM sales_return where idbranch = ".$idbranch." and entry_time <= '".$datefrom."'";
        return $this->db->query($str)->row();
    }
    // Used cash from expense from, to date
    public function get_sum_opening_expense_fromdate_cash($datefrom, $dateto, $idbranch){
        $str = "SELECT SUM(expense_amount) as expense_cash "
            . "FROM expense where idbranch = ".$idbranch." and entry_time between '".$datefrom."' and '".$dateto."'";
        return $this->db->query($str)->row();
    }
    // Used cash from sales return less than from date
    public function get_sum_opening_expense_cash($datefrom, $idbranch){
        $str = "SELECT SUM(expense_amount) as expense_cash "
            . "FROM expense where idbranch = ".$idbranch." and entry_time <= '".$datefrom."'";
        return $this->db->query($str)->row();
    }
    
    // Get All
//    public function  ajax_get_daybook_credit_buyback_recieve_report($datefrom, $dateto, $idbranch){
//        $payment_mode_data = $this->db->get('payment_mode')->result();
//        if($idbranch == ''){
//            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
//                    foreach ($payment_mode_data as $payment_mode){
//            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount END) AS ".$payment_mode->payment_mode." ,";
//                    }
//            $str1 = rtrim($str, ',');
//            $str1 .= " FROM payment_reconciliation, branch where from_credit_buyback_received = 1 and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by inv_no";
//            return $this->db->query($str1)->result();
//        }else{
//            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
//                    foreach ($payment_mode_data as $payment_mode){
//            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount END) AS ".$payment_mode->payment_mode." ,";
//                    }
//            $str1 = rtrim($str, ',');
//            $str1 .= " FROM payment_reconciliation, branch where from_credit_buyback_received = 1 and idbranch = ".$idbranch." and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by inv_no";
////            die($str1);
//            return $this->db->query($str1)->result();
//        }
//    }
//    public function ajax_get_daybook_expense_report($datefrom, $dateto, $idbranch){
//        if($idbranch == ''){
//            return $this->db->select('expense.entry_time, expense_type, expense_amount, branch.branch_name')
//                            ->where('idbranch = branch.id_branch')->from('branch')
//                            ->where('entry_date >=', $datefrom)
//                            ->where('entry_date <=', $dateto)
//                            ->get('expense')->result();
//        }else{
//            return $this->db->select('expense.entry_time, expense_type, expense_amount, branch.branch_name')
//                            ->where('idbranch',$idbranch)
//                            ->where('entry_date >=', $datefrom)
//                            ->where('entry_date <=', $dateto)
//                            ->where('idbranch = branch.id_branch')->from('branch')
//                            ->get('expense')->result();
//        }
//    }
//    public function ajax_get_daybook_sales_return_report($datefrom, $dateto, $idbranch){
//        if($idbranch == ''){
//            return $this->db->where('idbranch = branch.id_branch')->from('branch')
//                            ->where('sales_return.date >=', $datefrom)
//                            ->where('sales_return.date <=', $dateto)
//                            ->get('sales_return')->result();
//        }else{
//            return $this->db->where('idbranch',$idbranch)
//                            ->where('sales_return.date >=', $datefrom)
//                            ->where('sales_return.date <=', $dateto)
//                            ->where('idbranch = branch.id_branch')->from('branch')
//                            ->get('sales_return')->result();
//        }
//    }
//    public function ajax_get_daybook_deposite_to_bank_report($datefrom, $dateto, $idbranch){
//        if($idbranch == ''){
//            return $this->db->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
//                            ->where('cash_deposite_to_bank.date >=', $datefrom)
//                            ->where('cash_deposite_to_bank.date <=', $dateto)
//                            ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
//                            ->get('cash_deposite_to_bank')->result();
//        }else{
//            return $this->db->where('cash_deposite_to_bank.idbranch', $idbranch)
//                            ->where('cash_deposite_to_bank.date >=', $datefrom)
//                            ->where('cash_deposite_to_bank.date <=', $dateto)
//                            ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
//                            ->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
//                            ->get('cash_deposite_to_bank')->result();
//        }
//    }
    public function ajax_get_last_cash_deposite_entry($datefrom,$idbranch){
        return $this->db->where('cash_deposite_to_bank.date <=', $datefrom)
                        ->where('cash_deposite_to_bank.idbranch', $idbranch)
//                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
//                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
                        ->order_by('cash_deposite_to_bank.id_cash_deposite_to_bank', 'desc')
                        ->get('cash_deposite_to_bank',1)->row();
    }
    
    // Get All
    public function new_ajax_get_credit_report($idpayment_head, $idpayment_mode, $idbranch){
        $str1='';
        if($idpayment_mode != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." and amount > received_amount order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and idbranch = ".$idbranch." and idpayment_mode = ".$idpayment_mode." and amount > received_amount order by id_salepayment desc";
            }
        }elseif($idpayment_head != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and idpayment_head = ".$idpayment_head." and amount > received_amount order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and idbranch = ".$idbranch." and idpayment_head = ".$idpayment_head." and amount > received_amount order by id_salepayment desc";
            }
        }
//        else{
//            if($idbranch == ''){
//                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and amount > received_amount order by id_salepayment desc";
//            }else{
//                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, branch.branch_name FROM sale_payment, payment_mode, branch where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and idbranch = ".$idbranch." and amount > received_amount order by id_salepayment desc";
//            }
//        }
//        die($str1);
        return $this->db->query($str1)->result();
    }
    
    public function ajax_get_summary_report($datefrom, $dateto, $idbranch){
        if($idbranch == ''){
            $str = 'select payment_mode, sum(amount) as amount from sale_payment, payment_mode where idpayment_mode = id_paymentmode and date between "'.$datefrom.'" and "'.$dateto.'" group by idpayment_mode';
            return $this->db->query($str)->result();
        }else{
            $str = 'select payment_mode, sum(amount) as amount from sale_payment, payment_mode where idpayment_mode = id_paymentmode and date between "'.$datefrom.'" and "'.$dateto.'" and idbranch = '.$idbranch.' group by idpayment_mode';
            return $this->db->query($str)->result();
        }
    }
    public function get_sales_return() {
        return $this->db->where('sales_return.idbranch = id_branch')->from('branch')
                        ->where('sales_return_by = id_users')->from('users')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->order_by('sales_return.date','desc')
                        ->get('sales_return')->result();
    }
    public function get_purchase_return() {
        return $this->db->where('purchase_return.purchase_return_by = id_users')->from('users')
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->order_by('purchase_return.id_purchasereturn','desc')
                        ->get('purchase_return')->result();
    }
    // Cash Sum///////////////////*****************************************
     public function get_sum_sale_cash($idbranch){
        $str = "SELECT sum(amount) as sum_sale_cash FROM payment_reconciliation where idbranch = ".$idbranch." and idpayment_mode = 1";
        return $this->db->query($str)->row();
    }
    public function get_sum_sales_return_cash($idbranch){
        $str = "SELECT sum(final_total) as sum_sale_return_cash FROM sales_return where idbranch = ".$idbranch;
        return $this->db->query($str)->row();
    }
    public function get_sum_expense_cash($idbranch){
        return $this->db->select('SUM(expense_amount) as sum_expense_cash')->where('idbranch',$idbranch)->get('expense')->row();
    }
    public function get_sum_deposit_cash($idbranch){
        return $this->db->select('SUM(deposit_cash) as sum_deposit_cash')->where('idbranch',$idbranch)->get('cash_deposite_to_bank')->row();
    }
    public function ajax_ageing_stock_report($idcategory, $idbrand, $idmodel, $idbranch, $type,$days,$godown) {
        $to=date('Y-m-d');
        $from=date('Y-m-d',(strtotime ('-'.$days.' day',strtotime($to))));
        $str="";
        //Type
        if($type != 0 && $idcategory == 0 && $idbrand == 0 && $idmodel == 0){
            if($idbranch == 0){
                $str = 'select p.id_model, p.model_name, stockitems, ho_stockitems from model p '
                        . 'left join (select idmodel, SUM(qty) as ho_stockitems from stock WHERE idbranch = 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel '
                        . 'left join (select idmodel, SUM(qty) as stockitems from stock WHERE idbranch > 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 'hos on p.id_model = hos.idmodel '
                        . 'where p.idsku_type != 4 and p.idtype = '.$type;
            }else{
                $str = 'select p.id_model, p.model_name, stockitems from model p left join '
                        . '(select idmodel, SUM(qty) as stockitems from stock WHERE idbranch = '.$idbranch.' and idtype = '.$type.' and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel where p.idsku_type != 4 and p.idtype = '.$type;
            }
        //Type & Category
        }elseif($type != 0 && $idcategory != 0 && $idbrand == 0 && $idmodel == 0){
            if($idbranch == 0){
                $str = 'select p.id_model, p.model_name, stockitems, ho_stockitems from model p left join '
                        . '(select idmodel, SUM(qty) as ho_stockitems from stock WHERE idbranch = 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel '
                        . 'left join (select idmodel, SUM(qty) as stockitems from stock WHERE idbranch > 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 'hos on p.id_model = hos.idmodel where p.idsku_type != 4 and p.idcategory = '.$idcategory;
            }else{
                $str = 'select p.id_model, p.model_name, stockitems from model p left join '
                        . '(select idmodel, SUM(qty) as stockitems from stock WHERE idbranch = '.$idbranch.' and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel where p.idsku_type != 4 and p.idcategory = '.$idcategory;
               }
        //Brand
        }elseif($idbrand != 0 && $idmodel == 0){
            if($idbranch == 0){
                $str = 'select p.id_model, p.model_name, stockitems, ho_stockitems from model p '
                        . 'left join (select idmodel, SUM(qty) as ho_stockitems from stock WHERE idbranch = 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel '
                        . 'left join (select idmodel, SUM(qty) as stockitems from stock WHERE idbranch > 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 'hos on p.id_model = hos.idmodel '
                        . 'where p.idsku_type != 4 and p.idbrand = '.$idbrand.' and p.idcategory = '.$idcategory;
            }else{
                $str = 'select p.id_model, p.model_name, stockitems, from model p left join '
                        . '(select idmodel, SUM(qty) as stockitems from stock WHERE idbranch = '.$idbranch.' and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel '
                        . 'where p.idsku_type != 4 and p.idbrand = '.$idbrand.' and p.idcategory = '.$idcategory;
               }
        // Model
        }elseif($idmodel != 0){
            if($idbranch == 0){
                $str = 'select p.id_model, p.model_name, stockitems, ho_stockitems from model p left join '
                        . '(select idmodel, SUM(qty) as ho_stockitems from stock WHERE idbranch = 1 and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel '
                        . 'left join (select idmodel, SUM(qty) as stockitems from stock WHERE idbranch > 1  and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 'hos on p.id_model = hos.idmodel where p.idsku_type != 4 and p.id_model = '.$idmodel;
            }else{
                $str = 'select p.id_model, p.model_name, stockitems, from model p left join '
                        . '(select idmodel, SUM(qty) as stockitems from stock WHERE idbranch = '.$idbranch.' and idgodown='.$godown.' and transfer_time < "'.$from.'" OR (transfer = 0 AND outward_time < "'.$from.'") OR (outward = 0 AND date < "'.$from.'") group by idmodel) '
                        . 's on p.id_model = s.idmodel where p.idsku_type != 4 and p.id_model = '.$idmodel;
            }
        }
//        die($str);
        return $this->db->query($str)->result();
    }
     public function get_cash_entry_type() {
        return $this->db->where('id_cash_entry_type !=',6)->get('cash_entry_type')->result();
    }

    public function ajax_cash_sumamry_report($datefrom,$dateto,$idbranch,$viewbranches) {
//        $cash_entry_type = $this->db->get('cash_entry_type')->result();
        
        $cash_entry_type = $this->db->where('id_cash_entry_type !=',6)->get('cash_entry_type')->result();
        
//        $cash_entry_type1 = $this->db->where_in('id_cash_entry_type', $identry)->get('cash_entry_type')->result();
            
        if($idbranch == 0){
            $branches = $viewbranches;
        }else{
            $branches = $idbranch;
        }
        
        $str = "SELECT branch.branch_name,daybook_cash.date,daybook_cash.idbranch, ";
                foreach ($cash_entry_type as $entry_type){
        $str .= "SUM(CASE daybook_cash.entry_type WHEN ".$entry_type->id_cash_entry_type." THEN daybook_cash.amount ELSE 0 END) AS ".$entry_type->type_name.", ";
                }
        $str1 = rtrim($str, ', ');
        $str1 .= " FROM daybook_cash, branch where daybook_cash.idbranch in(".$branches.") and branch.id_branch = daybook_cash.idbranch and daybook_cash.date between '".$datefrom."' and '".$dateto."' group by daybook_cash.idbranch, daybook_cash.date";
        
        return $this->db->query($str1)->result();

    }
    public function ajax_max_cash_closure_report($datefrom,$dateto,$idbranch, $viewbranches){
        if($idbranch == 0){
            $branches = $viewbranches;
        }else{
            $branches = $idbranch;
        }
        $str = "Select max(closure_cash) as closure_cash, max(date) as date, idbranch as cidbranch from cash_closure where idbranch in($branches) and date between '$datefrom' and '$dateto' GROUP BY date, idbranch ORDER BY id_cash_closure desc " ;
        return $this->db->query($str)->result();
    }
     public function ajax_get_credit_cust_receipt($from, $to, $idbranch){
        if($idbranch != 0){
            return $this->db->select('payment_reconciliation.inv_no,payment_reconciliation.id_payment_reconciliation,payment_reconciliation.date,payment_reconciliation.amount,payment_mode.payment_mode,branch.branch_code,payment_head.payment_head,branch.branch_name,branch.branch_state_name')
                            ->where('payment_reconciliation.idbranch', $idbranch)
                            ->where('payment_reconciliation.date >=', $from)
                            ->where('payment_reconciliation.date <=', $to)
                            ->where('payment_reconciliation.idpayment_mode',1)
                            ->where('payment_reconciliation.from_credit_buyback_received',1)
                            ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                            ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                            ->where('sale_payment.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                            ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
                            ->get('payment_reconciliation')->result();
        }else{
            return $this->db->select('payment_reconciliation.inv_no,payment_reconciliation.id_payment_reconciliation,payment_reconciliation.date,payment_reconciliation.amount,payment_mode.payment_mode,branch.branch_code,payment_head.payment_head,branch.branch_name,branch.branch_state_name')
                            ->where('payment_reconciliation.date >=', $from)
                            ->where('payment_reconciliation.date <=', $to)
                            ->where('payment_reconciliation.idpayment_mode',1)
                            ->where('payment_reconciliation.from_credit_buyback_received',1)
                            ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                            ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                            ->where('sale_payment.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                            ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
                            ->get('payment_reconciliation')->result();
//            return $this->db->select('payment_reconciliation.inv_no,payment_reconciliation.id_payment_reconciliation,payment_reconciliation.date,payment_reconciliation.amount,payment_mode.payment_mode,branch.branch_code,payment_head.payment_head,branch.branch_name,branch.branch_state_name')
//                            ->where('payment_reconciliation.date >=', $from)
//                            ->where('payment_reconciliation.date <=', $to)
//                            ->where('payment_reconciliation.idpayment_mode',1)
//                            ->where('payment_reconciliation.from_credit_buyback_received',1)
//                            ->where('daybook_cash.idbranch = branch.id_branch')->from('branch')
//                            ->where('daybook_cash.entry_type', 4)
//                            ->where('daybook_cash.idtable = payment_reconciliation.id_payment_reconciliation')->from('payment_reconciliation')
//                            ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
//                            ->where('sale_payment.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
//                            ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
//                            ->get('daybook_cash')->result();
        }
    }
    
     public function ajax_get_credit_note_report($from, $to, $idcompany){
        $id = $_SESSION['id_users'];
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        return $this->db->select('sale.gst_type,customer.customer_fname, customer.customer_lname, customer.customer_contact, customer.customer_gst,state.state_name,sale_product.inv_no,sale_product.date,sale_product.is_mop,sale_product.mop,sale_product.discount_amt,sale_product.basic,sale_product.cgst_per,sale_product.sgst_per,sale_product.igst_per,sale_product.total_amount,sale_product.idsale,sale_product.product_name')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('state','branch.idstate = state.id_state', 'left')
                        ->join('customer','customer.id_customer = sale.idcustomer', 'left')
                        ->get('sale_product')->result();
    }
    
    public function ajax_get_jio_router_sale_data($idbranch, $allidbranch, $from, $to){
        if($idbranch == 0 || $idbranch == '0'){
            $branches = explode(',',$allidbranch);
        }else{
            $branches[] = $idbranch;
        }
        
        return $this->db->select('sale_product.date,sale_product.inv_no,sale_product.product_name,sale_product.qty, sale_product.total_amount,branch.branch_name, customer.customer_fname, customer.customer_lname, customer.customer_contact,sale.id_sale')
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where_in('sale_product.idbranch ', $branches)
                        ->where('sale.dcprint',1)
                        ->where('sale_product.idbranch = branch.id_branch')->from('branch')
                        ->where('sale_product.idsale = sale.id_sale')->from('sale')
                        ->where('sale.idcustomer = customer.id_customer')->from('customer')
                        ->get('sale_product')->result();
    }
    
    //*************Price Category Report****************//    
    
    public function get_product_category_data_byid($idproductcat){
        if($idproductcat != 0){
            return $this->db->where('id_product_category', $idproductcat)->get('product_category')->result();
        }else{
            return $this->db->where('active', 1)->get('product_category')->result();
        }
    }
    
    public function ajax_get_branch_byidzone($idzone){
        if($idzone != 0){
            return $this->db->where('idzone', $idzone)->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
        }else{
            return $this->db->where('branch.active', 1)->where('branch.idzone = zone.id_zone')->from('zone')->order_by('branch_name','ASC')->get('branch')->result();
        }
    }
    public function ajax_get_branch_byid($idbranch){
        if($idbranch != 0){
            return $this->db->where('id_branch',$idbranch)->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
        }else{
            return $this->db->where('branch.idzone = zone.id_zone')->from('zone')->order_by('branch_name','ASC')->get('branch')->result();
        }
    }
    
    public function get_price_category_lab_data(){
        return $this->db->get('price_category_lab')->result();
    }
    
     public function ajax_get_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idzone == 0 || $idzone == '0'){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
        }else{
            $pcats[] = $idproductcat;
        }
        
        
         $price_slots = $this->db->get('price_category_lab')->result();
         
         if($idzone == 'all'){
            $str = 'zone.zone_name,pc.product_category_name,pc.id_product_category,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',';
            }
        
            $this->db->select($str);
            $this->db->where_in('zone.id_zone',$zones);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,s.idproductcategory, brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab GROUP BY brr.idzone, s.idproductcategory) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idzone = zone.id_zone and sp'.$pslots->id_price_category_lab.'.idproductcategory = pc.id_product_category', 'left');
            }
            $this->db->order_by('zone.id_zone,pc.id_product_category'); 
            $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc')->from('zone');
            $query = $this->db->get();  
            return $query->result();
         }else{
             
            $str = 'b.id_branch,b.branch_name,zone.zone_name,pc.product_category_name,pc.id_product_category,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
            }
        
            $this->db->select($str);
             $this->db->where_in('b.idzone',$zones);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab GROUP BY s.idbranch, s.idproductcategory) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idproductcategory = pc.id_product_category', 'left');
            }
            $this->db->where('b.idzone = zone.id_zone');
            $this->db->order_by('zone.id_zone,b.id_branch,pc.id_product_category'); 
            $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc')->from('zone');
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
         }
             
    }
    
    public function ajax_get_sale_product_data_bybranch($idbranch,$allbranch, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idbranch == 0 || $idbranch == '0'){
            $branches = explode(',',$allbranch);
        }else{
            $branches[] = $idbranch;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
        }else{
            $pcats[] = $idproductcat;
        }
        $price_slots = $this->db->get('price_category_lab')->result();
             
        $str = 'b.id_branch,b.branch_name,zone.zone_name,pc.product_category_name,pc.id_product_category,';
        foreach($price_slots as $pslot){
            $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',';
        }

        $this->db->select($str);
         $this->db->where_in('b.id_branch',$branches);
        foreach($price_slots as $pslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab GROUP BY s.idbranch, s.idproductcategory) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idproductcategory = pc.id_product_category', 'left');
        }
        $this->db->where('b.idzone = zone.id_zone');
        $this->db->order_by('b.id_branch,pc.id_product_category'); 
        $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc')->from('zone');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
         
        
    }
    
  
    
/* Price cate backup
     public function ajax_get_sale_product_data_byzone($idzone, $idproductcat, $allproductcat, $from, $to){
        $branches = $this->db->get('branch')->result();
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idzone == 0){
            $branches = $this->db->select('branch.id_branch,branch.branch_name,zone.zone_name,zone.id_zone')->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
        }else{
            $branches = $this->db->select('branch.id_branch,branch.branch_name,zone.zone_name,zone.id_zone')->where('idzone', $idzone)->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
        }
        
        if($idproductcat == 0){
            $allpcat = $allproductcat;
        }else{
            $allpcat = $idproductcat;
        }
        
        $attribute_array = array(); $i=0;
        foreach ($branches as $branch){
            $attribute_array[$i]['id_branch'] = $branch->id_branch;
            $attribute_array[$i]['branch_name'] = $branch->branch_name;
            $attribute_array[$i]['zonename'] = $branch->zone_name;
            $str = "select pc.product_category_name, ";
                foreach($product_cat as $pcat){
            $str .= "p$pcat->id_price_category_lab.$pcat->pname, p$pcat->id_price_category_lab.cnt_$pcat->pname,";
                }
            $str1 = rtrim($str , ', ');

            $str1 .= " from product_category pc ";
            foreach($product_cat as $pcat){
            $str1 .= "left join (select "
                    . "sale_product.idbranch,sale_product.idproductcategory,"
                    . "count(sale_product.id_saleproduct) as cnt_$pcat->pname, "
                    . "sum(sale_product.total_amount) as $pcat->pname from sale_product 
                WHERE sale_product.total_amount between $pcat->min_lab AND $pcat->max_lab and sale_product.idbranch = $branch->id_branch and sale_product.date between '$from' and '$to' GROUP by sale_product.idproductcategory, sale_product.idbranch) p$pcat->id_price_category_lab";
            $str1 .= " on pc.id_product_category = p$pcat->id_price_category_lab.idproductcategory ";
            }
            $str1 .= " WHERE pc.id_product_category IN($allpcat) ";
            $attribute_array[$i][$branch->id_branch] = $this->db->query($str1)->result();
            $i++;
        }
//        die('<pre>'.print_r($attribute_array,1).'</pre>');
        return $attribute_array;
        
    }
    
    public function ajax_get_sale_product_data_bybranch($idbranch, $idproductcat, $allproductcat, $from, $to){
        $branches = $this->db->get('branch')->result();
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idbranch == 0){

            if($this->session->userdata('level') == 3){
                $branches = $this->db->select('b.id_branch,b.branch_name,zone.zone_name,zone.id_zone')
                    ->where('b.active', 1)
                    ->where('ub.iduser', $_SESSION['id_users'])
                    ->where('b.is_warehouse', 0)
                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                    ->where('b.idzone = zone.id_zone')->from('zone')
                    ->get('branch b')->result();
            }else{
                $branches = $this->db->select('branch.id_branch,branch.branch_name,zone.zone_name,zone.id_zone')->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
            }
        }else{
            $branches = $this->db->select('branch.id_branch,branch.branch_name,zone.zone_name,zone.id_zone')->where('id_branch', $idbranch)->where('branch.idzone = zone.id_zone')->from('zone')->get('branch')->result();
        }
//        die('<pre>'.print_r($branches,1).'</pre>');
        
        if($idproductcat == 0){
            $allpcat = $allproductcat;
        }else{
            $allpcat = $idproductcat;
        }
        
        $attribute_array = array(); $i=0;
        foreach ($branches as $branch){
            $attribute_array[$i]['id_branch'] = $branch->id_branch;
            $attribute_array[$i]['branch_name'] = $branch->branch_name;
            $attribute_array[$i]['zonename'] = $branch->zone_name;
            $str = "select pc.product_category_name, ";
                foreach($product_cat as $pcat){
            $str .= "p$pcat->id_price_category_lab.$pcat->pname, p$pcat->id_price_category_lab.cnt_$pcat->pname,";
                }
            $str1 = rtrim($str , ', ');

            $str1 .= " from product_category pc ";
            foreach($product_cat as $pcat){
            $str1 .= "left join (select "
                    . "sale_product.idbranch,sale_product.idproductcategory,"
                    . "count(sale_product.id_saleproduct) as cnt_$pcat->pname, "
                    . "sum(sale_product.total_amount) as $pcat->pname from sale_product 
                WHERE sale_product.total_amount between $pcat->min_lab AND $pcat->max_lab and sale_product.idbranch = $branch->id_branch and sale_product.date between '$from' and '$to'  GROUP by sale_product.idproductcategory, sale_product.idbranch) p$pcat->id_price_category_lab";
            $str1 .= " on pc.id_product_category = p$pcat->id_price_category_lab.idproductcategory ";
            }
            $str1 .= " WHERE pc.id_product_category IN($allpcat) ";
            $attribute_array[$i][$branch->id_branch] = $this->db->query($str1)->result();
            $i++;
        }
        return $attribute_array;
        
    }
    */
//    Other Way To Print Sum of product category price
    
   
//    public function ajax_get_sale_product_data_byzone1($id_branch,$idpcat, $min_lab, $max_lab, $pname){
//        $str = "select idbranch,idproductcategory, sum(total_amount) as pricesum,count(id_saleproduct) as qtycnt from sale_product WHERE idproductcategory =$idpcat and idbranch = $id_branch and total_amount BETWEEN $min_lab and $max_lab";
//        return $this->db->query($str)->row();
//    }
    
     public function ajax_get_sale_time_analysis_data_byidzone($idzone,$allzones,$idproductcat, $allproductcatm,$from,$to){
//        die(print_r($_POST));
        
        if($idzone == 0 || $idzone == '0'){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcatm);
        }else{
            $pcats[] = $idproductcat;
        }
        
        $time_slots = $this->db->get('time_slots')->result();
        
        $str = 'zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,pc.product_category_name,pc.id_product_category,';
        foreach($time_slots as $tslot){
            $str .= 'sp'.$tslot->id_time_slab.'.sale_qty as saleqt'.$tslot->id_time_slab.',sp'.$tslot->id_time_slab.'.total as total'.$tslot->id_time_slab.',sp'.$tslot->id_time_slab.'.landing as land'.$tslot->id_time_slab.',';
        }
        
        
        $this->db->select($str);
        $this->db->where_in('b.idzone',$zones);
        foreach($time_slots as $tslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch, s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' and CAST(s.entry_time AS TIME) >= '$tslots->min_slot' and  CAST(s.entry_time AS TIME) < '$tslots->max_slot' GROUP BY s.idbranch, s.idproductcategory) sp$tslots->id_time_slab", 'sp'.$tslots->id_time_slab.'.idbranch = b.id_branch and sp'.$tslots->id_time_slab.'.idproductcategory = pc.id_product_category', 'left');
        }
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->where('b.idzone = zone.id_zone')->from('zone')   ;
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->order_by('b.idzone, b.id_branch,pc.id_product_category'); 
        $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    public function ajax_get_sale_time_analysis_data_byidbranch($idbranch,$allbranches,$idproductcat, $allproductcat,$from,$to){
//        die(print_r($_POST));
        
        if($idbranch == 0 || $idbranch == '0'){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
         
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
        }else{
            $pcats[] = $idproductcat;
        }
        
        $time_slots = $this->db->get('time_slots')->result();
        
        $str = 'zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,pc.product_category_name,pc.id_product_category,';
        foreach($time_slots as $tslot){
            $str .= 'sp'.$tslot->id_time_slab.'.sale_qty as saleqt'.$tslot->id_time_slab.',sp'.$tslot->id_time_slab.'.total as total'.$tslot->id_time_slab.',sp'.$tslot->id_time_slab.'.landing as land'.$tslot->id_time_slab.',';
        }
        
        
        $this->db->select($str);
        $this->db->where_in('b.id_branch',$branches);
        foreach($time_slots as $tslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch, s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' and CAST(s.entry_time AS TIME) >= '$tslots->min_slot' and  CAST(s.entry_time AS TIME) < '$tslots->max_slot' GROUP BY s.idbranch, s.idproductcategory) sp$tslots->id_time_slab", 'sp'.$tslots->id_time_slab.'.idbranch = b.id_branch and sp'.$tslots->id_time_slab.'.idproductcategory = pc.id_product_category', 'left');
        }
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->where('b.idzone = zone.id_zone')->from('zone')   ;
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->order_by('b.idzone, b.id_branch,pc.id_product_category'); 
        $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
    
    public function ajax_get_zonesale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to){
//        $branches = $this->db->get('branch')->result();
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idzone == 0 || $idzone == '0'){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
        }else{
            $pcats[] = $idproductcat;
        }
        
        
         $price_slots = $this->db->get('price_category_lab')->result();
        
        $str = 'zone.zone_name,pc.product_category_name,pc.id_product_category,';
        foreach($price_slots as $pslot){
            $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',';
        }
        
        
        $this->db->select($str);
        $this->db->where_in('zone.id_zone',$zones);
        foreach($price_slots as $pslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,s.idbranch,s.idproductcategory, brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab GROUP BY brr.idzone, s.idproductcategory) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idzone = zone.id_zone and sp'.$pslots->id_price_category_lab.'.idproductcategory = pc.id_product_category', 'left');
        }
        $this->db->order_by('zone.id_zone,pc.id_product_category'); 
        $this->db->where_in('pc.id_product_category',$pcats)->from('product_category pc')->from('zone');
        $query = $this->db->get();  
        return $query->result();
        
    }
    //Brand & promotor Price Category Report
     public function get_brand_sale_product_data_bybranch($idbranch,$allbranches, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idbranch == 0 || $idbranch == '0'){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
            $pcatss =  $allproductcat;
        }else{
            $pcats[] = $idproductcat;
            $pcatss =  $idproductcat;
        }
        
        
         $price_slots = $this->db->get('price_category_lab')->result();
         
             
        $str = 'b.id_branch,b.branch_name,brand.id_brand,brand.brand_name,zone.zone_name,zone.id_zone,';
        foreach($price_slots as $pslot){
            $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
        }

        $this->db->select($str);
         $this->db->where_in('b.id_branch',$branches);
        foreach($price_slots as $pslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing,s.idbranch,s.idbrand from sale_product s WHERE s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbranch, s.idbrand) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idbrand = brand.id_brand', 'left');
        }
        $this->db->order_by('zone.id_zone,b.id_branch,brand.id_brand');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('brand.active',1)->from('brand');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
   
    }

    public function get_brand_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idzone == 0 || $idzone == '0' || $idzone == 'all'){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
            $pcatss =  $allproductcat;
        }else{
            $pcats[] = $idproductcat;
            $pcatss =  $idproductcat;
        }
        
        
        $price_slots = $this->db->get('price_category_lab')->result();
        
         if($idzone == 'all'){
            
            $str = 'brand.id_brand,brand.brand_name,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
            }

            $this->db->select($str);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,s.idbrand from sale_product s WHERE s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbrand) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbrand = brand.id_brand', 'left');
            }
            $this->db->order_by('brand.id_brand');
            $this->db->where('brand.active',1)->from('brand');
            $query = $this->db->get();  
            return $query->result();
   
        }  
         
         
//        elseif($idzone == '0')
            else{
            
            $str = 'brand.id_brand,brand.brand_name,zone.zone_name,zone.id_zone,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
            }

            $this->db->select($str);
             $this->db->where_in('zone.id_zone',$zones);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,s.idbrand,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and  s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbrand,brr.idzone) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idzone = zone.id_zone and sp'.$pslots->id_price_category_lab.'.idbrand = brand.id_brand', 'left');
            }
            $this->db->order_by('zone.id_zone,brand.id_brand');
            $this->db->where('brand.active',1)->from('brand');
            $this->db->from('zone');
            $query = $this->db->get();  
            return $query->result();
   
        }   
//        else{
//            
//            $str = 'b.id_branch,b.branch_name,brand.id_brand,brand.brand_name,zone.zone_name,zone.id_zone,';
//            foreach($price_slots as $pslot){
//                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',';
//            }
//
//            $this->db->select($str);
//             $this->db->where_in('b.idzone',$zones);
//            foreach($price_slots as $pslots){
//                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,s.idbranch,s.idbrand from sale_product s WHERE s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbranch, s.idbrand) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idbrand = brand.id_brand', 'left');
//            }
//             $this->db->order_by('zone.id_zone,b.id_branch,brand.id_brand');
//            $this->db->where('b.idzone = zone.id_zone')->from('zone');
//            $this->db->where('brand.active',1)->from('brand');
//            $this->db->from('branch b');
//            $query = $this->db->get();  
//            return $query->result();
//   
//        }  
        
    }
    
    //Promotor Price Category 
    public function get_promotor_sale_product_data_bybranch($idbranch,$allbranches, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idbranch == 0 || $idbranch == '0'){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
            $pcatss =  $allproductcat;
        }else{
            $pcats[] = $idproductcat;
            $pcatss =  $idproductcat;
        }
        
        
         $price_slots = $this->db->get('price_category_lab')->result();
         
             
        $str = 'b.id_branch,b.branch_name,users.id_users,users.user_name,zone.zone_name,zone.id_zone,';
        foreach($price_slots as $pslot){
            $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
        }

        $this->db->select($str);
         $this->db->where_in('b.id_branch',$branches);
        foreach($price_slots as $pslots){
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbranch,sale.idsalesperson) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idsalesperson = users.id_users', 'left');
        }
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->order_by('zone.id_zone,b.id_branch');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
   
    }
     public function get_promotor_sale_product_data_byzone($idzone,$allzones, $idproductcat, $allproductcat, $from, $to){
        $product_cat = $this->db->get('price_category_lab')->result();
        
        if($idzone == 0 || $idzone == '0' || $idzone == 'all'){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        if($idproductcat == 0 || $idproductcat == '0'){
            $pcats = explode(',',$allproductcat);
            $pcatss =  $allproductcat;
        }else{
            $pcats[] = $idproductcat;
            $pcatss =  $idproductcat;
        }
        
        
        $price_slots = $this->db->get('price_category_lab')->result();
         
        if($idzone == 'all'){
            
            $str = 'zone.zone_name,zone.id_zone,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
            }

            $this->db->select($str);
             $this->db->where_in('zone.id_zone',$zones);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and  s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY brr.idzone) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idzone = zone.id_zone ', 'left');
            }
            $this->db->order_by('zone.id_zone');
            $this->db->from('zone');
            $query = $this->db->get();  
            return $query->result();
   
        }   else{
            
            $str = 'b.id_branch,b.branch_name,users.id_users,users.user_name,zone.zone_name,zone.id_zone,';
            foreach($price_slots as $pslot){
                $str .= 'sp'.$pslot->id_price_category_lab.'.sale_qty as saleqt'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.total as total'.$pslot->id_price_category_lab.',sp'.$pslot->id_price_category_lab.'.landing as landing'.$pslot->id_price_category_lab.',';
            }

            $this->db->select($str);
             $this->db->where_in('b.idzone',$zones);
            foreach($price_slots as $pslots){
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.total_amount >= $pslots->min_lab and s.total_amount <= $pslots->max_lab and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson) sp$pslots->id_price_category_lab", 'sp'.$pslots->id_price_category_lab.'.idbranch = b.id_branch and sp'.$pslots->id_price_category_lab.'.idsalesperson = users.id_users', 'left');
            }
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
   
        }  
        
    }
    public function ajax_get_cash_payment_receive_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->where('cash_payment_receive.idbranch = branch.id_branch')->from('branch')
                            ->where_in('cash_payment_receive.idbranch', $branches)
                            ->where('cash_payment_receive.date >=', $datefrom)
                            ->where('cash_payment_receive.date <=', $dateto)
                            ->get('cash_payment_receive')->result();
        }else{
            return $this->db->where('cash_payment_receive.idbranch', $idbranch)
                            ->where('cash_payment_receive.date >=', $datefrom)
                            ->where('cash_payment_receive.date <=', $dateto)
                            ->where('cash_payment_receive.idbranch = branch.id_branch')->from('branch')
                            ->get('cash_payment_receive')->result();
        }
    }
    public function  ajax_get_adv_payment_receive_report($datefrom, $dateto, $idbranch, $branches){
        $payment_mode_data = $this->db->get('payment_mode')->result();
        if($idbranch == ''){
            $str = "SELECT adv.id_advance_payment_receive as idsale, adv.date, branch.branch_name,branch.branch_code, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM advance_payment_receive adv, branch where idbranch in (".$branches.") and branch.id_branch = adv.idbranch and adv.date between '".$datefrom."' and '".$dateto."' group by adv.id_advance_payment_receive";
            return $this->db->query($str1)->result();
        }else{
            $str = "SELECT adv.id_advance_payment_receive as idsale, adv.date, branch.branch_name,branch.branch_code, ";
                    foreach ($payment_mode_data as $payment_mode){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount ELSE 0 END) AS ".$payment_mode->payment_mode." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM advance_payment_receive adv, branch where idbranch = ".$idbranch." and branch.id_branch = adv.idbranch and adv.date between '".$datefrom."' and '".$dateto."' group by adv.id_advance_payment_receive";
//            die($str1);
            return $this->db->query($str1)->result();
        }
    }
    public function  ajax_get_adv_payment_refund_report($datefrom, $dateto, $idbranch, $viewbranches){
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->select('adv.*,branch.branch_name,branch.branch_code')
                            ->where('adv.idbranch = branch.id_branch')->from('branch')
                            ->where_in('adv.idbranch', $branches)
                            ->where('adv.refund_date >=', $datefrom)
                            ->where('adv.refund_date <=', $dateto)
                            ->get('advance_payment_receive adv')->result();
        }else{
            return $this->db->select('adv.*,branch.branch_name,branch.branch_code')
                            ->where('adv.idbranch', $idbranch)
                            ->where('adv.refund_date >=', $datefrom)
                            ->where('adv.refund_date <=', $dateto)
                            ->where('adv.idbranch = branch.id_branch')->from('branch')
                            ->get('advance_payment_receive adv')->result();
        }
    }
    
      public function ajax_get_tally_sales_return_product_data($from, $to, $idcompany, $idpcat, $idbrand ){
         $id = $_SESSION['id_users'];
        
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[]  = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
//        die(print_r($branchid));
        
        return $this->db->select('sales_return_product.*, customer.customer_gst,customer.customer_address,customer.customer_district, customer.customer_contact,customer.customer_pincode,customer.customer_state, customer.customer_fname,customer.customer_lname,sales_return.idsale, sales_return.inv_date, product_category.product_category_name,category.hsn ')
                        ->where('sales_return_product.date >=', $from)
                        ->where('sales_return_product.date <=', $to)
                        ->where_in('sales_return_product.idbranch', $branchid)
                        ->where_in('sales_return_product.idproductcategory', $productcatid)
                        ->where_in('sales_return_product.idbrand', $brandid)
                        ->where('sales_return_product.idsales_return = sales_return.id_salesreturn')->from('sales_return')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->where('sales_return_product.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('sales_return_product.idcategory = category.id_category')->from('category')
                        ->order_by('sales_return_product.date','desc')
                        ->get('sales_return_product')->result();
    }
    
    public function get_wharehouse_to_branch_shipment_data($from,$to,$idbranch,$idstatus,$allbranch){
        if($idbranch == 0 ){
            $branches = explode(',',$allbranch);
        }else{
            $branches[] = $idbranch;
        }
        if($from == '' && $to == ''){
            return  $this->db->select('outward.*, branch.branch_name, b.branch_name as branch_from')
                        ->where_in('outward.idbranch', $branches)
                        ->where('outward.status', $idstatus)
                        ->where('outward.idbranch = branch.id_branch')->from('branch')
                        ->where('outward.idwarehouse = b.id_branch')->from('branch b')
                        ->get('outward')->result();
        }else {
            return  $this->db->select('outward.*, branch.branch_name, b.branch_name as branch_from')
                        ->where('outward.date >=', $from)
                        ->where('outward.date <=', $to)
                        ->where_in('outward.idbranch', $branches)
                        ->where('outward.status', $idstatus)
                        ->where('outward.idbranch = branch.id_branch')->from('branch')
                        ->where('outward.idwarehouse = b.id_branch')->from('branch b')
                        ->get('outward')->result();
        }
    }
   public function get_outward_data_byid($id){
        return  $this->db->select('outward.*, branch.branch_name, b.branch_name as branch_from,branch.branch_contact,b.branch_contact as branch_contact_from,st.date as allocation_date')
                        ->where('outward.id_outward', $id)
                        ->where('outward.idbranch = branch.id_branch')->from('branch')
                        ->where('outward.idwarehouse = b.id_branch')->from('branch b')
                        ->where('outward.idstock_allocation = st.id_stock_allocation')->from('stock_allocation st')
                        ->get('outward')->row();
   }
    
    public function get_wh_to_branch_shipment_details($id) {
        return $this->db->select('outward_product.*,model_variants.full_name,branch.branch_name,godown.godown_name ,brand.brand_name')
                        ->where('outward_product.idoutward', $id)
                        ->where('outward_product.idbranch = branch.id_branch')->from('branch')
                        ->where('outward_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('outward_product.idgodown = godown.id_godown')->from('godown')
                        ->where('outward_product.idbrand = brand.id_brand')->from('brand')
                        ->get('outward_product')->result();
    }
    
     public function get_b_to_b_shipment_data($from,$to,$idbranch,$idstatus,$allbranch){
        if($idbranch == 0 ){
            $branches = explode(',',$allbranch);
        }else{
            $branches[] = $idbranch;
        }
        return  $this->db->select('transfer.*, branch.branch_name, b.branch_name as branch_from')
                        ->where('transfer.date >=', $from)
                        ->where('transfer.date <=', $to)
                        ->where_in('transfer.idbranch', $branches)
                        ->where('transfer.status', $idstatus)
                        ->where('transfer.idbranch = branch.id_branch')->from('branch')
                        ->where('transfer.transfer_from  = b.id_branch')->from('branch b')
                        ->get('transfer')->result();
    }
    
    public function get_transfer_product_shipment_details($id){
         return $this->db->select('transfer_product.*,model_variants.full_name,branch.branch_name,godown.godown_name ,brand.brand_name')
                        ->where('transfer_product.idtransfer', $id)
                        ->where('transfer_product.idbranch = branch.id_branch')->from('branch')
                        ->where('transfer_product.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('transfer_product.idgodown = godown.id_godown')->from('godown')
                        ->where('transfer_product.idbrand = brand.id_brand')->from('brand')
                        ->get('transfer_product')->result();
    }
    
    public function get_transfer_data_byid($id){
        return  $this->db->select('transfer.*, branch.branch_name,branch.branch_contact, b.branch_name as branch_from, b.branch_contact as branch_contact_from')
                        ->where('transfer.id_transfer', $id)
                        ->where('transfer.idbranch = branch.id_branch')->from('branch')
                        ->where('transfer.transfer_from  = b.id_branch')->from('branch b')
                        ->get('transfer')->row();
    }
    
    //******* Token Cancellation Report *********//
    public function get_token_cancellation_report_data($from, $to, $idbranch, $branchess, $idstatus){
        if($idbranch == 0 ){
            $branches = explode(',',$branchess);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idstatus == 'all'){
            $status = array(0,1,2);
        }else{
            $status[] = $idstatus;
        }
        return $this->db->select('branch.branch_name,mv.full_name,c.customer_fname, c.customer_lname, c.customer_contact,u.user_name,st.date, st.idsaletoken, st.qty,st.total_amount,sale_token.status,sale.inv_no, sale_token.cancel_remark')
                        ->where('st.date >=', $from)
                        ->where('st.date <=', $to)
                        ->where_in('st.idbranch', $branches)
                        ->where_in('sale_token.status', $status)
                        ->join('sale','sale_token.idsale = sale.id_sale','left')
                        ->where('sale_token.idcustomer = c.id_customer')->from('customer c')
                        ->where('sale_token.idsalesperson = u.id_users')->from('users u')
                        ->where('st.idsaletoken = sale_token.id_sale_token')->from('sale_token')
                        ->where('st.idbranch = branch.id_branch')->from('branch')
                        ->where('st.idvariant = mv.id_variant')->from('model_variants mv')
                        ->get('sale_token_product st')->result();
    }
     public function ajax_get_summary_daybook_sale_report($datefrom, $dateto, $idbranch, $branches){
        $payment_head_data = $this->db->where('active = 1')->get('payment_head')->result();
        if($idbranch == ''){
            $str = "SELECT spy.inv_no, spy.date, branch.branch_name, spy.idsale,(SELECT GROUP_CONCAT(DISTINCT sp.product_name SEPARATOR ',<br>' )FROM sale_product as sp WHERE sp.idsale = spy.idsale)AS product_name, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE spy.idpayment_head WHEN ".$payment_head->id_paymenthead." THEN spy.amount ELSE 0 END) AS ".$payment_head->payment_head." , ";
                    }
            $str1 = rtrim($str, ', ');
            $str1 .= " FROM sale_payment as spy, branch where spy.idbranch in (".$branches.") and branch.id_branch = spy.idbranch and spy.date between '".$datefrom."' and '".$dateto."' group by spy.idsale";
        }else{
            $str = "SELECT spy.inv_no, spy.date, branch.branch_name, spy.idsale,(SELECT GROUP_CONCAT(DISTINCT sp.product_name SEPARATOR ',<br>' )FROM sale_product as sp WHERE sp.idsale = spy.idsale)AS product_name, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE spy.idpayment_head WHEN ".$payment_head->id_paymenthead." THEN spy.amount ELSE 0 END) AS ".$payment_head->payment_head." , ";
                    }
            $str1 = rtrim($str, ', ');
            $str1 .= " FROM sale_payment as spy, branch where spy.idbranch = ".$idbranch." and branch.id_branch = spy.idbranch and spy.date between '".$datefrom."' and '".$dateto."' group by spy.idsale";
        }
       // die($str1);
        return $this->db->query($str1)->result();
    }
     public function  ajax_get_adv_payment_receive_summary_report($datefrom, $dateto, $idbranch, $branches){
        $payment_head_data = $this->db->where('active = 1')->get('payment_head')->result();
        if($idbranch == ''){
            $str = "SELECT adv.id_advance_payment_receive as idsale, adv.date, branch.branch_name,branch.branch_code, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_head->id_paymenthead." THEN amount ELSE 0 END) AS ".$payment_head->payment_head." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM advance_payment_receive adv, branch where idbranch in (".$branches.") and branch.id_branch = adv.idbranch and adv.date between '".$datefrom."' and '".$dateto."' group by adv.id_advance_payment_receive";
            return $this->db->query($str1)->result();
        }else{
            $str = "SELECT adv.id_advance_payment_receive as idsale, adv.date, branch.branch_name,branch.branch_code, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_head->id_paymenthead." THEN amount ELSE 0 END) AS ".$payment_head->payment_head." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM advance_payment_receive adv, branch where idbranch = ".$idbranch." and branch.id_branch = adv.idbranch and adv.date between '".$datefrom."' and '".$dateto."' group by adv.id_advance_payment_receive";
//            die($str1);
            return $this->db->query($str1)->result();
        }
    }
     public function ajax_get_daybook_sales_return_summary_report($datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return  $this->db->select("*,(select GROUP_CONCAT(DISTINCT srp.product_name SEPARATOR ', ') from sales_return_product as srp where sales_return.id_salesreturn=srp.idsales_return) as return_product")
                    ->where_in('idbranch', $branches)
                    ->where('date >=', $datefrom)
                    ->where('sales_return_type != 3')
                    ->where('date <=', $dateto)
                    ->where('idbranch = branch.id_branch')->from('branch')
                    ->get('sales_return')->result();
        }else{
            return  $this->db->select("*,(select GROUP_CONCAT(DISTINCT srp.product_name SEPARATOR ', ') from sales_return_product as srp where sales_return.id_salesreturn=srp.idsales_return) as return_product")
                    ->where('idbranch', $idbranch)
                    ->where('date >=', $datefrom)
                    ->where('date <=', $dateto)
                    ->where('sales_return_type != 3')
                    ->where('idbranch = branch.id_branch')->from('branch')
                    ->get('sales_return')->result();
        }
    }
     public function  ajax_get_daybook_credit_buyback_recieve_summary_report($datefrom, $dateto, $idbranch, $branches){
        $payment_head_data = $this->db->where('active = 1')->get('payment_head')->result();
        if($idbranch == ''){
            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_head->id_paymenthead." THEN amount ELSE 0 END) AS ".$payment_head->payment_head." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM payment_reconciliation, branch where idbranch in (".$branches.") and from_credit_buyback_received = 1 and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by idsale";
            return $this->db->query($str1)->result();
        }else{
            $str = "SELECT inv_no, payment_reconciliation.date, branch.branch_name, payment_reconciliation.idsale, ";
                    foreach ($payment_head_data as $payment_head){
            $str .= "SUM(CASE idpayment_mode WHEN ".$payment_head->id_paymenthead." THEN amount ELSE 0 END) AS ".$payment_head->payment_head." ,";
                    }
            $str1 = rtrim($str, ',');
            $str1 .= " FROM payment_reconciliation, branch where from_credit_buyback_received = 1 and idbranch = ".$idbranch." and branch.id_branch = payment_reconciliation.idbranch and payment_reconciliation.date between '".$datefrom."' and '".$dateto."' group by idsale";
//            die($str1);
            return $this->db->query($str1)->result();
        }
    }
    
}
?>