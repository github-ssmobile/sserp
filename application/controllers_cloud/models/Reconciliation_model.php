<?php
class Reconciliation_model extends CI_Model{
    
    public function get_reconciliation_byid($id) {
        return $this->db->where('id_payment_reconciliation',$id)
                        ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('payment_reconciliation.idcustomer = customer.id_customer')->from('customer')
                        ->where('payment_reconciliation.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('payment_reconciliation.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                        ->get('payment_reconciliation')->row();
    }
    public function get_payment_head_by_iduser($iduser) {
        return $this->db->where('user_has_payment_mode.iduser', $iduser)
                ->where('user_has_payment_mode.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                ->group_by('user_has_payment_mode.idpaymenthead')
                ->get('user_has_payment_mode')->result();
    }
    public function ajax_get_payment_mode_byhead_user($idhead, $iduser) {
        return $this->db->where('user_has_payment_mode.iduser', $iduser)
                ->where('user_has_payment_mode.idpaymenthead', $idhead)
                ->where('user_has_payment_mode.idpaymentmode = payment_mode.id_paymentmode')->from('payment_mode')
                ->get('user_has_payment_mode')->result();
    }
    public function receive_payment_reconciliation($id,$data) {
        return $this->db->where('id_payment_reconciliation',$id)->update('payment_reconciliation',$data);
    }
    
    public function ajax_get_received_payment_reconciliation_report($idpayment_head, $idpayment_mode, $idbranch){
        $str1='';
        if($idpayment_mode != ''){
            if($idbranch == ''){
                $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, bank.bank_name "
                        . "FROM payment_reconciliation, payment_mode, branch, customer, bank "
                        . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." and payment_receive = 1 and payment_reconciliation.idcustomer=customer.id_customer and bank.id_bank = payment_reconciliation.idbank order by id_payment_reconciliation desc";
            }else{
                $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, bank.bank_name "
                        . "FROM payment_reconciliation, payment_mode, branch, customer, bank "
                        . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and idbranch = ".$idbranch." and idpayment_mode = ".$idpayment_mode." and payment_receive = 1 and payment_reconciliation.idcustomer=customer.id_customer and bank.id_bank = payment_reconciliation.idbank order by id_payment_reconciliation desc";
            }
        }elseif($idpayment_head != ''){
            if($idbranch == ''){
                $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, bank.bank_name "
                        . "FROM payment_reconciliation, payment_mode, branch, customer, bank "
                        . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and idpayment_head = ".$idpayment_head." and payment_receive = 1 and payment_reconciliation.idcustomer=customer.id_customer and bank.id_bank = payment_reconciliation.idbank order by id_payment_reconciliation desc";
            }else{
                $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, bank.bank_name "
                        . "FROM payment_reconciliation, payment_mode, branch, customer, bank "
                        . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and idbranch = ".$idbranch." and idpayment_head = ".$idpayment_head." and payment_receive = 1 and payment_reconciliation.idcustomer=customer.id_customer and bank.id_bank = payment_reconciliation.idbank order by id_payment_reconciliation desc";
            }
        }
        return $this->db->query($str1)->result();
    }
    
    public function save_cash_closure($data) {
        $this->db->insert('cash_closure', $data);
        return $this->db->insert_id();
    }
    public function delete_cash_closure_byid($id){
        return $this->db->where('id_cash_closure', $id)->delete('cash_closure');
    }
    public function delete_cash_closure_denomination_byid($id){
        return $this->db->where('idcash_closure', $id)->delete('closure_denomination');
    }

    public function update_cash_closure_byid($id_cash_closure, $data) {
        return $this->db->where('id_cash_closure', $id_cash_closure)->update('cash_closure', $data);
    }
    public function save_closure_denomination($data) {
        return $this->db->insert_batch('closure_denomination', $data);
    }
     public function get_cash_closure_denomination_byid($id){
        return $this->db->where('idcash_closure', $id)->get('closure_denomination')->result();
    }
    public function get_cash_closure_data_byidbranch($idbranch) {
        return $this->db->where('idbranch',$idbranch)->order_by('id_cash_closure', 'desc')->get('cash_closure')->result();
    }
    public function get_all_cash_closure_data() {
        return $this->db->order_by('id_cash_closure','desc')->get('cash_closure')->result();
    }
    public function get_cash_closure_byidcash($id) {
        return $this->db->where('id_cash_closure',$id) 
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('cash_closure.idbranch = branch.id_branch')->from('branch')
                        ->get('cash_closure')->row();
    }
    public function save_cash_closure_delete_histroy($data){
        return $this->db->insert('cash_closure_delete_histroy', $data);
    }

    public function get_todays_cash_closure_byidbranch($idbranch) {
        return $this->db->where('idbranch',$idbranch)->where('date', date('Y-m-d'))->get('cash_closure')->result();
    }
    public function get_cash_closure_bystatus_idbranch($idbranch, $status) {
        return $this->db->where('idbranch',$idbranch)->where('status', $status)->get('cash_closure', 1)->result();
    }
    public function update_cash_closure_status_byidbranch($idbranch) {
        $dtata = array(
            'status'=>1
        );
        return $this->db->where('idbranch',$idbranch)->update('cash_closure', $dtata);
    }
    public function get_sum_cash_closure_bystatus_idbranch($idbranch, $status) {
        return $this->db->select('closure_cash as pending_closure_cash, idcombine, id_cash_closure')->where('idbranch',$idbranch)->where('status', $status)->get('cash_closure')->result();
    }
//    public function get_todays_cash_deposit_byidbranch($idbranch) {
//        return $this->db->select('SUM(deposit_cash) as sum_deposit_cash')->where('idbranch',$idbranch)->get('cash_deposite_to_bank')->row();
//    }
    public function save_daybook_cash_payment($data) {
        return $this->db->insert('daybook_cash', $data);
    }
    public function save_deposit_to_bank($data){
        $this->db->insert('cash_deposite_to_bank', $data);
        return $this->db->insert_id();
    }
    public function delete_cash_deposite_by_id($id){
        return $this->db->where('id_cash_deposite_to_bank', $id)->delete('cash_deposite_to_bank');
    }
    public function update_cash_deposite_to_bank_delete_histroy($deposite){
        return $this->db->insert('cash_deposite_to_bank_delete_histroy', $deposite);
    }
    public function update_cash_closure_byiddeposite($data,$id){
        return $this->db->where('idcash_deposit_to_bank', $id)->update('cash_closure', $data);
    }
    public function delete_daybook_data($id){
        return $this->db->where('idtable', $id)
                        ->where('entry_type = 6')
                        ->delete('daybook_cash');
    }

    public function get_deposit_to_bank_byidbranch($idbranch) {
        return $this->db->where('idbranch', $idbranch)->order_by('id_cash_deposite_to_bank')
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
                        ->get('cash_deposite_to_bank')->result();
    }
    public function get_cash_deposite_byid($id){
        return $this->db->where('id_cash_deposite_to_bank', $id)->get('cash_deposite_to_bank')->row();
    }

    public function ajax_get_credit_for_reconciliation($idpayment_mode,$idbranch,$datefrom,$dateto){
        $str1='';
        $iduser = $_SESSION['id_users'];
        if($datefrom != '' && $dateto != ''){
            if($idpayment_mode != ''){
                if($idbranch == ''){
                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                    
                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where('payment_reconciliation.idpayment_mode', $idpayment_mode)
                                    ->where_in('payment_reconciliation.idbranch', $branches)
                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                    ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch "
//                            . "and payment_mode.id_paymentmode = idpayment_mode "
//                            . "and payment_reconciliation.date between '". $datefrom."' and '". $dateto ."' "
//                            . "and idpayment_mode = ".$idpayment_mode." and payment_receive = 0 "
//                            . "and payment_reconciliation.idcustomer=customer.id_customer "
//                            . "order by id_payment_reconciliation desc";
                }else{
                    
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                ->where('payment_reconciliation.idpayment_mode', $idpayment_mode)
                                ->where('payment_reconciliation.idbranch', $idbranch)
                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch "
//                            . "and payment_reconciliation.date between '". $datefrom."' and '". $dateto ."' "
//                            . "and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." "
//                            . "and payment_reconciliation.idbranch = ".$idbranch." "
//                            . "and payment_receive = 0 and payment_reconciliation.idcustomer=customer.id_customer "
//                            . "order by id_payment_reconciliation desc";
                }
            }else{
                // All payment modes
                $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
                foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
                
                if($idbranch == ''){
                    $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
                    foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                    
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
                                ->where_in('payment_reconciliation.idbranch', $branches)
                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch "
//                            . "and payment_reconciliation.date between '". $datefrom."' and '". $dateto ."' "
//                            . "and payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0 "
//                            . "and payment_reconciliation.idcustomer=customer.id_customer "
//                            . "order by id_payment_reconciliation desc";
                }else{
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
                                ->where('payment_reconciliation.idbranch', $idbranch)
                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch "
//                            . "and payment_reconciliation.date between '". $datefrom."' and '". $dateto ."' "
//                            . "and payment_mode.id_paymentmode = idpayment_mode "
//                            . "and payment_reconciliation.idbranch = ".$idbranch." "
//                            . "and payment_receive = 0 and payment_reconciliation.idcustomer=customer.id_customer "
//                            . "order by id_payment_reconciliation desc";
                }
            }
//        }elseif($datefrom == '' && $dateto == ''){
        }elseif($datefrom == '' && $dateto == ''){
            if($idpayment_mode != ''){
                if($idbranch == ''){
                    $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
                    foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                    ->where('payment_reconciliation.idpayment_mode', $idpayment_mode)
                                    ->where_in('payment_reconciliation.idbranch', $branches)
                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                    ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch "
//                            . "and payment_mode.id_paymentmode = idpayment_mode "
//                            . "and idpayment_mode = ".$idpayment_mode." "
//                            . "and payment_receive = 0 "
//                            . "and payment_reconciliation.idcustomer=customer.id_customer "
//                            . "order by id_payment_reconciliation desc";
                }else{
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                    ->where('payment_reconciliation.idpayment_mode', $idpayment_mode)
                                    ->where('payment_reconciliation.idbranch', $idbranch)
                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                    ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." and payment_reconciliation.idbranch = ".$idbranch." and payment_receive = 0 and payment_reconciliation.idcustomer=customer.id_customer order by id_payment_reconciliation desc";
                }
            }else{
                // All payment modes
                $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
                foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
                if($idbranch == ''){ 
                    $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
                    foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
                                ->where_in('payment_reconciliation.idbranch', $branches)
                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0 and payment_reconciliation.idcustomer=customer.id_customer order by id_payment_reconciliation desc";
                    
                }else{
                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, branch.id_branch, customer.*')
                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
                                ->where('payment_reconciliation.idbranch', $idbranch)
                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                ->get('payment_reconciliation')->result();
                    
//                    $str1 = "SELECT payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.* "
//                            . "FROM payment_reconciliation, payment_mode, branch, customer "
//                            . "where branch.id_branch = payment_reconciliation.idbranch and payment_mode.id_paymentmode = idpayment_mode and payment_reconciliation.idbranch = ".$idbranch." and payment_receive = 0 and payment_reconciliation.idcustomer=customer.id_customer order by id_payment_reconciliation desc";
                }
            }    
        }
//        return $this->db->query($str1)->result();
    }
    
    
    
    public function ajax_get_credit_for_reconciliation_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$viewbranches){
//        die(print_r($_POST));
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($datefrom != '' && $dateto != ''){
//            if($idpayment_mode != ''){
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                            ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                            ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                            ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                                    ->where_in('payment_reconciliation.idbranch', $branches)
                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                    ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                ->where('payment_reconciliation.idbranch', $idbranch)
//                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                ->get('payment_reconciliation')->result();
//                }
//            }
//            else{
//                // All payment modes
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                            return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                        ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                        ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                        ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                        ->get('payment_reconciliation')->result();
//                        
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
//                            return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                        ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                        ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                        ->where_in('payment_reconciliation.idbranch', $branches)
//                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                        ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                        ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }
//                }
//            }
        }elseif($datefrom == '' && $dateto == ''){
//            if($idpayment_mode != ''){
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
                                        ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                                        ->where_in('payment_reconciliation.idbranch', $branches)
                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                        ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                        ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                }
//            }
//            else{
//                // All payment modes
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where_in('payment_reconciliation.idbranch', $branches)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                    foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                ->where('payment_reconciliation.idbranch', $idbranch)
//                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                ->get('payment_reconciliation')->result();
//                }
//            }
        }
    }
    
    public function ajax_get_receivables_received_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$viewbranches){
//        die(print_r($_POST));
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
        if($idbranch == '' && $_SESSION['level'] == 1){
            if($datefrom != '' && $dateto != ''){
                return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, payment_head.payment_head, bank.bank_name')
                            ->where('payment_reconciliation.transfer_date between "'. $datefrom.'" and "'. $dateto .'"')
                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                            ->where('bank.id_bank = payment_reconciliation.idbank')->from('bank')
                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                            ->where('payment_head.id_paymenthead = payment_mode.idpaymenthead')->from('payment_head')
                            ->where('payment_mode.id_paymentmode = payment_reconciliation.idpayment_mode and payment_reconciliation.payment_receive = 1')->from('payment_mode')
                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                            ->get('payment_reconciliation')->result();
            }elseif($datefrom == '' && $dateto == ''){
                return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, payment_head.payment_head, bank.bank_name')
                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                            ->where('bank.id_bank = payment_reconciliation.idbank')->from('bank')
                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                            ->where('payment_head.id_paymenthead = payment_mode.idpaymenthead')->from('payment_head')
                            ->where('payment_mode.id_paymentmode = payment_reconciliation.idpayment_mode and payment_reconciliation.payment_receive = 1')->from('payment_mode')
                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                            ->get('payment_reconciliation')->result();
            }
        }else{
            if($idbranch == ''){
                $branches = explode(',',$viewbranches);
            }else{
                $branches[] = $idbranch;
            }
            if($datefrom != '' && $dateto != ''){
                return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, payment_head.payment_head, bank.bank_name')
                            ->where('payment_reconciliation.transfer_date between "'. $datefrom.'" and "'. $dateto .'"')
                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                            ->where_in('payment_reconciliation.idbranch', $branches)
                            ->where('bank.id_bank = payment_reconciliation.idbank')->from('bank')
                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                            ->where('payment_head.id_paymenthead = payment_mode.idpaymenthead')->from('payment_head')
                            ->where('payment_mode.id_paymentmode = payment_reconciliation.idpayment_mode and payment_reconciliation.payment_receive = 1')->from('payment_mode')
                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                            ->get('payment_reconciliation')->result();
            }elseif($datefrom == '' && $dateto == ''){
                return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*, payment_head.payment_head, bank.bank_name')
                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                            ->where_in('payment_reconciliation.idbranch', $branches)
                            ->where('bank.id_bank = payment_reconciliation.idbank')->from('bank')
                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                            ->where('payment_head.id_paymenthead = payment_mode.idpaymenthead')->from('payment_head')
                            ->where('payment_mode.id_paymentmode = payment_reconciliation.idpayment_mode and payment_reconciliation.payment_receive = 1')->from('payment_mode')
                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                            ->get('payment_reconciliation')->result();
            }
        }
    }
    
    public function ajax_get_bank_reconciliation_pending_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$viewbranches){
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($datefrom != '' && $dateto != ''){
//            if($idpayment_mode != ''){
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                            ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                            ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                            ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                            ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                            ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                            ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                            ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                                    ->where_in('payment_reconciliation.idbranch', $branches)
                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                    ->where('payment_mode.id_paymentmode = idpayment_mode and bank_reconciliation = 0')->from('payment_mode')
                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                    ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                ->where('payment_reconciliation.idbranch', $idbranch)
//                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                ->get('payment_reconciliation')->result();
//                }
//            }
//            else{
//                // All payment modes
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                            return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                        ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                        ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                        ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                        ->get('payment_reconciliation')->result();
//                        
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
//                            return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                        ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                        ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                        ->where_in('payment_reconciliation.idbranch', $branches)
//                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                        ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                        ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where('payment_reconciliation.date between "'. $datefrom.'" and "'. $dateto .'"')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }
//                }
//            }
        }elseif($datefrom == '' && $dateto == ''){
//            if($idpayment_mode != ''){
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
                                        ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                                        ->where_in('payment_reconciliation.idbranch', $branches)
                                        ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
                                        ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
                                        ->where('payment_mode.id_paymentmode = idpayment_mode and bank_reconciliation = 0')->from('payment_mode')
                                        ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
                                        ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
//                                    ->where('payment_reconciliation.idbranch', $idbranch)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                }
//            }
//            else{
//                // All payment modes
//                if($idbranch == ''){
//                    if($_SESSION['level'] == 1){ // Admin
//                        $mapped_payment_modes = $this->General_model->get_payment_modes_for_reconciliation();
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->id_paymentmode; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }elseif($_SESSION['level'] == 3){
//                        $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                        foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                        $mapped_branches = $this->General_model->get_user_has_idbranch_mode($iduser);
//                        foreach ($mapped_branches as $br){ $branches[] = $br->idbranch; }
//                        return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                    ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                    ->where_in('payment_reconciliation.idbranch', $branches)
//                                    ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                    ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                    ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                    ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                    ->get('payment_reconciliation')->result();
//                    }
//                }else{
//                    $mapped_payment_modes = $this->General_model->get_user_has_idpayment_mode($iduser);
//                    foreach ($mapped_payment_modes as $pay){ $modes[] = $pay->idpaymentmode; }
//                    return $this->db->select('payment_reconciliation.*, payment_mode.payment_mode, branch.branch_name, customer.*')
//                                ->where_in('payment_reconciliation.idpayment_mode', $modes)
//                                ->where('payment_reconciliation.idbranch', $idbranch)
//                                ->where('branch.id_branch = payment_reconciliation.idbranch')->from('branch')
//                                ->where('payment_mode.id_paymentmode = idpayment_mode and payment_receive = 0')->from('payment_mode')
//                                ->where('payment_reconciliation.idcustomer=customer.id_customer')->from('customer')
//                                ->order_by('payment_reconciliation.id_payment_reconciliation','asc')
//                                ->get('payment_reconciliation')->result();
//                }
//            }
        }
    }
    public function ajax_get_bank_reconciled_report_new_1($idpayment_mode,$idbranch,$datefrom,$dateto,$viewbranches,$idbank){
//        die(print_r($_POST));
        if($idbranch == ''){
            $branches = $viewbranches;
        }else{
            $branches = $idbranch;
        }
        if($idpayment_mode == 3){
            $str = 'select sum(pr.received_amount) as sum_received_amount, pr.utr_no, pr.transfer_date, payment_mode.payment_mode, branch.branch_name, bank_recon.sum_bank_amount, bank.bank_name, pr.idpayment_mode, pr.idbank, bank_recon.sum_bank_amount - sum(pr.received_amount) as diff '
                    . 'from branch,payment_mode,bank,payment_reconciliation pr '
                    . 'LEFT JOIN (select sum(br.amount) as sum_bank_amount, br.idpayment_mode,br.idbank, br.date, br.transaction_id '
                    . 'from bank_reconciliation br '
                    . 'where br.idbank = '.$idbank.' and br.date between "'. $datefrom.'" and "'. $dateto .'" and br.idpayment_mode in ('.$idpayment_mode.') group by br.date, br.idpayment_mode,br.transaction_id,br.idbank) bank_recon '
                    . 'ON bank_recon.idpayment_mode = pr.idpayment_mode and bank_recon.idbank = pr.idbank and bank_recon.date = (pr.transfer_date + interval 1 day) and bank_recon.transaction_id = pr.utr_no '
                    . 'where pr.idbank = '.$idbank.' and pr.transfer_date between "'. $datefrom.'" and "'. $dateto .'" and pr.payment_receive = 1 and pr.idbranch in ('.$branches.') and pr.idpayment_mode in ('.$idpayment_mode.') and branch.id_branch = pr.idbranch and payment_mode.id_paymentmode=pr.idpayment_mode and bank.id_bank=pr.idbank '
                    . 'group by pr.utr_no,pr.transfer_date,pr.idbank order by pr.transfer_date';
        }else{
            $str = 'select sum(pr.received_amount) as sum_received_amount, pr.utr_no, pr.transfer_date, payment_mode.payment_mode, branch.branch_name, bank_recon.sum_bank_amount, bank.bank_name, pr.idpayment_mode, pr.idbank, bank_recon.sum_bank_amount - sum(pr.received_amount) as diff '
                    . 'from branch,payment_mode,bank,payment_reconciliation pr '
                    . 'LEFT JOIN (select sum(br.amount) as sum_bank_amount, br.idpayment_mode,br.idbank, br.date, br.transaction_id '
                    . 'from bank_reconciliation br '
                    . 'where br.idbank = '.$idbank.' and br.date between "'. $datefrom.'" and "'. $dateto .'" and br.idpayment_mode in ('.$idpayment_mode.') group by br.date, br.idpayment_mode,br.transaction_id,br.idbank) bank_recon '
                    . 'ON bank_recon.idpayment_mode = pr.idpayment_mode and bank_recon.idbank = pr.idbank and bank_recon.date = pr.transfer_date and bank_recon.transaction_id = pr.utr_no '
                    . 'where pr.idbank = '.$idbank.' and pr.transfer_date between "'. $datefrom.'" and "'. $dateto .'" and pr.payment_receive = 1 and pr.idbranch in ('.$branches.') and pr.idpayment_mode in ('.$idpayment_mode.') and branch.id_branch = pr.idbranch and payment_mode.id_paymentmode=pr.idpayment_mode and bank.id_bank=pr.idbank '
                    . 'group by pr.utr_no,pr.transfer_date,pr.idbank order by pr.transfer_date';
        }
        return $this->db->query($str)->result();
    }
    public function ajax_get_bank_reconciled_report_new($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$viewbranches,$idbank){
//        die('hi');
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idbank == ''){
            return $this->db->select('sum(pr.received_amount) as sum_received_amount, pr.utr_no, pr.transfer_date, payment_mode.payment_mode, branch.branch_name, sum(br.amount) as sum_bank_amount, bank.bank_name')
                    ->where('pr.transfer_date between "'. $datefrom.'" and "'. $dateto .'" and pr.payment_receive = 1')
                    ->where('br.date between "'. $datefrom.'" and "'. $dateto .'"')
                    ->where_in('pr.idpayment_mode', $modes_arr)
                    ->where_in('pr.idbranch', $branches)
                    ->where('pr.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                    ->where('pr.idbranch = branch.id_branch')->from('branch') // get branch
                    ->where('pr.idbank = bank.id_bank')->from('bank') // get bank
                    ->where('br.idpayment_mode = pr.idpayment_mode')
                    ->where('br.idbank = pr.idbank')
                    ->where('br.date = pr.transfer_date')
                    ->where('br.transaction_id = pr.utr_no')
                    ->from('bank_reconciliation br')
                    ->group_by('pr.utr_no,pr.transfer_date,pr.idbank')
                    ->group_by('br.transaction_id,br.date,br.idbank')
                    ->get('payment_reconciliation pr')->result();
        }else{
            return $this->db->select('sum(pr.received_amount) as sum_received_amount, pr.utr_no, pr.transfer_date, payment_mode.payment_mode, branch.branch_name, sum(br.amount) as sum_bank_amount, bank.bank_name')
                        ->join('(select br.idpayment_mode,br.idbank, br.date,br.transaction_id from bank_reconciliation br) bank_recon','bank_recon.idpayment_mode = pr.idpayment_mode and bank_recon.idbank = pr.idbank and bank_recon.date = pr.transfer_date and bank_recon.transaction_id = pr.utr_no','outer')
                    
                    
                    
                    ->where('pr.transfer_date between "'. $datefrom.'" and "'. $dateto .'" and pr.payment_receive = 1')
                    ->where('br.date between "'. $datefrom.'" and "'. $dateto .'"')
                    ->where_in('pr.idpayment_mode', $modes_arr)
                    ->where_in('pr.idbranch', $branches)
                    ->where('br.idbank', $idbank)
                    ->where('pr.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                    ->where('pr.idbranch = branch.id_branch')->from('branch')
                    ->where('pr.idbank = bank.id_bank')->from('bank')
                    ->where('br.idpayment_mode = pr.idpayment_mode and br.idbank = pr.idbank and br.date = pr.transfer_date and br.transaction_id = pr.utr_no')
                    ->from('bank_reconciliation br')
                    ->group_by('pr.utr_no,pr.transfer_date,br.idbank')
                    ->group_by('br.transaction_id,br.date,br.idbank')
                    ->get('payment_reconciliation pr')->result();
            
            
//            return $this->db->select('sum(pr.received_amount) as sum_received_amount, pr.utr_no, pr.transfer_date, payment_mode.payment_mode, branch.branch_name, sum(br.amount) as sum_bank_amount, bank.bank_name')
//                    ->where('pr.transfer_date between "'. $datefrom.'" and "'. $dateto .'" and pr.payment_receive = 1')
//                    ->where('br.date between "'. $datefrom.'" and "'. $dateto .'"')
//                    ->where_in('pr.idpayment_mode', $modes_arr)
//                    ->where_in('pr.idbranch', $branches)
//                    ->where('br.idbank', $idbank)
//                    ->where('pr.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
//                    ->where('pr.idbranch = branch.id_branch')->from('branch')
//                    ->where('pr.idbank = bank.id_bank')->from('bank')
//                    ->where('br.idpayment_mode = pr.idpayment_mode and br.idbank = pr.idbank and br.date = pr.transfer_date and br.transaction_id = pr.utr_no')
//                    ->from('bank_reconciliation br')
//                    ->group_by('pr.utr_no,pr.transfer_date,br.idbank')
//                    ->group_by('br.transaction_id,br.date,br.idbank')
//                    ->get('payment_reconciliation pr')->result();
        }
    }
    
    public function ajax_get_bank_received_list($idpayment_mode,$datefrom,$dateto,$modes){
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
//        die(print_r($modes_arr));
        return $this->db->where('br.date between "'. $datefrom.'" and "'. $dateto .'"')
                        ->where_in('br.idpayment_mode', $modes_arr)
                        ->where('payment_mode.idpaymenthead = payment_head.id_paymenthead')->from('payment_head')
                        ->where('br.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('br.created_by = users.id_users')->from('users')
                        ->where('br.idbank = bank.id_bank')->from('bank')
                        ->get('bank_reconciliation br')->result();
    }
    
    public function ajax_get_bank_reconciled_report($idpayment_mode,$idbranch,$datefrom,$dateto,$modes,$viewbranches){
        if($idpayment_mode == ''){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpayment_mode;
        }
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        return $this->db->select('sum(br.amount) as bank_amount, pr.received_amount as settlement_amount')
                    ->where('br.date between "'. $datefrom.'" and "'. $dateto .'"')
                    ->where('pr.payment_receive = 1 and pr.payment_receive = 1 and pr.utr_no = br.transaction_id and pr.idbank = br.idbank')
                    ->where('pr.transfer_date = br.date')->from('payment_reconciliation pr')
                    ->group_by('br.idbank')->group_by('br.transaction_id')->group_by('br.date')
                    ->order_by('br.id_bank_reconciliation','asc')
                    ->get('bank_reconciliation br')->result();
    }
    
    public function get_devices_byidpayment_mode($idpayment_mode) {
        if($idpayment_mode == ''){
            return $this->db->where('idbranch = branch.id_branch')->from('branch')
                            ->get('payment_mode_has_devices')->result();
        }else{
            return $this->db->where('idpayment_mode', $idpayment_mode)
                            ->where('idbranch = branch.id_branch')->from('branch')
                            ->get('payment_mode_has_devices')->result();
        }
    }
    public function update_sale_payment($id, $data) {
        return $this->db->where('id_salepayment', $id)->update('sale_payment', $data);
    }
    public function save_cheque_bounce($data) {
        return $this->db->insert('cheque_bounce_history', $data);
    }
    public function submit_bank_reconciliation($data) {
        return $this->db->insert('bank_reconciliation', $data);
    }
    public function submit_cash_reconciliation($data, $id) {
        return $this->db->where('id_cash_deposite_to_bank', $id)->update('cash_deposite_to_bank', $data);
    }
    public function delete_payment_reconciliation($id) {
        return $this->db->where('id_payment_reconciliation', $id)->delete('payment_reconciliation');
    }
    public function get_bank_recon_10days_data() {
        return $this->db->where('bank_reconciliation.idbank = bank.id_bank')->from('bank')
                        ->where('bank_reconciliation.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('payment_mode.idpaymenthead = payment_head.id_paymenthead')
                        ->from('payment_head')->from('payment_mode')
                        ->get('bank_reconciliation', 100)->result();
    }
    public function ajax_get_cash_for_reconciliation($idbranch, $idbranches, $datefrom, $dateto) {
        if($idbranch == ''){
            $branch_arr = explode(',',$idbranches);
        }else{
            $branch_arr[] = $idbranch;
        }
        if($datefrom == '' && $dateto == ''){
            return $this->db->select('bank.bank_name,branch.branch_name,cash_deposite_to_bank.*')
                        ->where_in('cash_deposite_to_bank.idbranch', $branch_arr)
                        ->where('cash_deposite_to_bank.reconciliation_status = 0')
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')
                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')
                        ->from('branch')->from('bank')
                        ->get('cash_deposite_to_bank')->result();
        }else{
            return $this->db->select('bank.bank_name,branch.branch_name,cash_deposite_to_bank.*')
                        ->where_in('cash_deposite_to_bank.idbranch', $branch_arr)
                        ->where('cash_deposite_to_bank.date >=', $datefrom)
                        ->where('cash_deposite_to_bank.date <=', $dateto)
                        ->where('cash_deposite_to_bank.reconciliation_status = 0')
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')
                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')
                        ->from('branch')->from('bank')
                        ->get('cash_deposite_to_bank')->result();
        }
    }
    public function ajax_get_cash_reconciled_report($idbranch, $idbranches, $datefrom, $dateto) {
        if($idbranch == ''){
            $branch_arr = explode(',',$idbranches);
        }else{
            $branch_arr[] = $idbranch;
        }
        if($datefrom == '' && $dateto == ''){
            return $this->db->where_in('cash_deposite_to_bank.idbranch', $branch_arr)
                        ->where('cash_deposite_to_bank.reconciliation_status = 1')
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')->from('bank')
                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')->from('branch')
                        ->get('cash_deposite_to_bank')->result();
        }else{
            return $this->db->where_in('cash_deposite_to_bank.idbranch', $branch_arr)
                        ->where('cash_deposite_to_bank.date >=', $datefrom)
                        ->where('cash_deposite_to_bank.date <=', $dateto)
                        ->where('cash_deposite_to_bank.reconciliation_status = 1')
                        ->where('cash_deposite_to_bank.idbank = bank.id_bank')
                        ->where('cash_deposite_to_bank.idbranch = branch.id_branch')
                        ->from('branch')->from('bank')
                        ->get('cash_deposite_to_bank')->result();
        }
    }
    public function ajax_get_cheque_reconciled_report($idbranch, $idbranches, $datefrom, $dateto, $idmode) {
        if($idbranch == ''){
            $branch_arr = explode(',',$idbranches);
        }else{
            $branch_arr[] = $idbranch;
        }
        if($datefrom == '' && $dateto == ''){
            return $this->db->where_in('payment_reconciliation.idbranch', $branch_arr)
                        ->where('payment_reconciliation.bank_reconciliation = 1')
                        ->where('payment_reconciliation.idpayment_mode', $idmode)
                        ->where('payment_reconciliation.idbank = bank.id_bank')->from('bank')
                        ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                        ->get('payment_reconciliation')->result();
        }else{
            return $this->db->where_in('payment_reconciliation.idbranch', $branch_arr)
                        ->where('payment_reconciliation.date >=', $datefrom)
                        ->where('payment_reconciliation.date <=', $dateto)
                        ->where('payment_reconciliation.idpayment_mode', $idmode)
                        ->where('payment_reconciliation.bank_reconciliation = 1')
                        ->where('payment_reconciliation.idbank = bank.id_bank')->from('bank')
                        ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                        ->get('payment_reconciliation')->result();
        }
    }
    
    public function ajax_get_credit_received_report_byfilter($idpaymentmode, $idbranch, $datefrom, $dateto, $modes, $allbranches){
        if($idpaymentmode == 0){
            $modes_arr = explode(',',$modes);
        }else{
            $modes_arr[] = $idpaymentmode;
        }
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
//        die(print_r($_POST));
        return $this->db->select('sale.final_total,sale.customer_fname,sale.date as invoice_date,sale.customer_lname,sale.customer_contact,payment_reconciliation.*,branch.branch_name,payment_mode.payment_mode')
                        ->where('payment_reconciliation.date >=', $datefrom)
                        ->where('payment_reconciliation.date <=', $dateto)
                        ->where_in('payment_reconciliation.idpayment_mode', $modes_arr)
                        ->where_in('payment_reconciliation.idbranch', $branches)
                        ->where('payment_reconciliation.idsale = sale.id_sale')->from('sale')
                        ->where('payment_reconciliation.from_credit_buyback_received = 1')
                        ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
                        ->where('payment_reconciliation.idbranch = branch.id_branch')->from('branch')
                        ->where('payment_reconciliation.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->get('payment_reconciliation')->result();
    }
    public function get_cash_payment_received_byidbranch($idbranch) {
        return $this->db->where('cash_payment_receive.idbranch',$idbranch)
                        ->where('cash_payment_receive.created_by = users.id_users')->from('users')
                        ->get('cash_payment_receive')->result();
    }
    public function save_cash_payment_receive($data) {
        $this->db->insert('cash_payment_receive', $data);
        return $this->db->insert_id();
    }
    public function get_cash_payment_receive_byid($id) {
        return $this->db->where('id_cash_payment_receive',$id)
                        ->where('cpr.idbranch = branch.id_branch')->from('branch')
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('cpr.created_by = users.id_users')->from('users')
                        ->get('cash_payment_receive cpr')->row();
    }
    public function save_advanced_payment_receive($data) {
        $this->db->insert('advance_payment_receive', $data);
        return $this->db->insert_id();
    }
    public function get_advanced_payment_receive_byid($id) {
        return $this->db->select('payment_head.payment_head,payment_mode.payment_mode,apr.*,users.user_name,model_variants.full_name as mfull_name,branch.*,print_head.*,payment_head.tranxid_type,users.user_name as sales_person')
                        ->where('id_advance_payment_receive',$id)
                        ->where('apr.idbranch = branch.id_branch')->from('branch')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.idsalesperson = users.id_users')
                        ->from('payment_head')->from('payment_mode')->from('users')
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
//                        ->where('apr.created_by = users.id_users')->from('users')
                        ->get('advance_payment_receive apr')->row();
    }
    public function get_advance_payment_received_byidbranch($idbranch) {
        return $this->db->select('payment_head.payment_head,payment_mode.payment_mode,apr.*,users.user_name,model_variants.full_name,payment_reconciliation.payment_receive,urs.user_name as sales_person')
                        ->where('apr.idbranch',$idbranch)
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.created_by = users.id_users')->from('users')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->where('apr.claim = 0')->where('apr.idsalesperson = urs.id_users')->from('users urs')
                        ->from('payment_head')->from('payment_mode')
                        ->where('apr.id_advance_payment_receive = payment_reconciliation.idadvance_payment_receive')->from('payment_reconciliation')
                        ->get('advance_payment_receive apr')->result();
    }
    public function get_advance_payment_received_report() {
        return $this->db->select('payment_head.payment_head,payment_mode.payment_mode,apr.*,users.user_name,model_variants.full_name,payment_reconciliation.payment_receive,urs.user_name as sales_person')
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.created_by = users.id_users')->from('users')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->where('apr.claim = 0')->where('apr.idsalesperson = urs.id_users')->from('users urs')
                        ->from('payment_head')->from('payment_mode')
                        ->where('apr.id_advance_payment_receive = payment_reconciliation.idadvance_payment_receive')->from('payment_reconciliation')
                        ->get('advance_payment_receive apr')->result();
    }
    public function ajax_get_advance_payment_received_report($datefrom,$dateto,$idbranch,$viewbranches) {
        $this->db->select('payment_head.payment_head,payment_mode.payment_mode,apr.*,users.user_name,model_variants.full_name,payment_reconciliation.payment_receive,urs.user_name as sales_person');
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($datefrom && $dateto){
            $this->db->where('apr.date between "'. $datefrom.'" and "'. $dateto .'"');
        }
        return $this->db->where_in('apr.idbranch',$branches)
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.created_by = users.id_users')->from('users')
                        ->where('apr.idsalesperson = urs.id_users')->from('users urs')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->where('apr.id_advance_payment_receive = payment_reconciliation.idadvance_payment_receive')
                        ->from('payment_head')->from('payment_mode')->from('payment_reconciliation')
                        ->order_by('apr.claim')
                        ->get('advance_payment_receive apr')->result();
    }
    public function get_advance_payment_received_report_filter() {
        return $this->db->select('payment_head.payment_head,payment_mode.payment_mode,apr.*,users.user_name,model_variants.full_name,payment_reconciliation.payment_receive')
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.created_by = users.id_users')->from('users')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->from('payment_head')->from('payment_mode')
                        ->where('apr.id_advance_payment_receive = payment_reconciliation.idadvance_payment_receive')->from('payment_reconciliation')
                        ->get('advance_payment_receive apr',100)->result();
    }
    public function get_active_payment_head_allow_for_advance_payment(){
        return $this->db->where('active = 1')->where('allow_for_advance_payment = 1')->get('payment_head')->result();
    }
    public function update_advanced_payment_byid($id, $data){
        return $this->db->where('id_advance_payment_receive', $id)->update('advance_payment_receive', $data);
    }
    public function get_insurance_pending_recon_bystatus($status) {
        return $this->db->select('ir.*, users.user_name')
                        ->join('users','ir.sale_recon_by = users.id_users','left')
                        ->where('ir.sale_recon', $status)->get('insurance_reconciliation ir')->result();
    }
    public function sale_insurance_recon($idvariant, $trans_id, $imei, $data) {
        return $this->db->where('idvariant', $idvariant)
                        ->where('activation_code', $trans_id)
                        ->where('insurance_imei_no', $imei)
                        ->update('insurance_reconciliation', $data);
    }
    public function sale_insurance_recon_byid($idrecon, $data){
        return $this->db->where('id_insurance_reconciliation', $idrecon)->update('insurance_reconciliation', $data);
    }
    public function get_insurance_pending_recon_byvariant_status($idvariant,$status) {
        return $this->db->select('ir.*, users.user_name')
                        ->where('ir.sale_recon', $status)->where('ir.idvariant', $idvariant)
                        ->join('users','ir.sale_recon_by = users.id_users','left')
                        ->get('insurance_reconciliation ir')->result();
    }
    public function get_insurance_recon_bystatus_date($idvariant, $status, $datefrom, $dateto, $sale_recon_by) {
            $this->db->select('ir.*, users.user_name, branch.branch_name');
        if($idvariant){
            $this->db->where('ir.idvariant', $idvariant);
        }
        return  $this->db->where('ir.sale_recon', $status)
                        ->where('ir.date >=', $datefrom)
                        ->where('ir.date <=', $dateto)
                        ->where($sale_recon_by.' = users.id_users')->from('users')
                        ->where('ir.idbranch = branch.id_branch')->from('branch')
                        ->get('insurance_reconciliation ir')->result();
    }
    public function get_verify_insurance_pending_entry($idvariant, $trans_id, $imei, $sale_recon) {
        return $this->db->where('idvariant', $idvariant)
                        ->where('activation_code', $trans_id)
                        ->where('insurance_imei_no', $imei)
                        ->where('sale_recon', $sale_recon)
                        ->get('insurance_reconciliation', 1)->result();
    }
    /********DOA Recon**********/
    public function ajax_get_doa_stock_bybrand_status($brand,$idwarehouse){
        $this->db->select('dr.*,mv.full_name,dr.imei_no as doa_imei,ss.counter_faulty,ss.idvariant,mv.last_purchase_price');
        if($brand){
            $this->db->where('dr.idbrand',$brand);
        }
        return $this->db->where('dr.doa_return_type = 1')
                        ->where('dr.status = 0')
                        ->where('dr.idbranch', $idwarehouse)
                        ->join('service_stock ss','dr.idservice = ss.id_service','left') 
                        ->where('dr.imei_no = stk.imei_no')->from('stock stk')
                        ->where('dr.idvariant = mv.id_variant')->from('model_variants mv')
                        ->get('doa_reconciliation dr')->result();

    }
    public function save_doa_to_vendor($data) {
        $this->db->insert('doa_stock_shipment', $data);
        return $this->db->insert_id();
    }
    public function update_batch_doa_reconciliation_byid($data) {
        return $this->db->update_batch('doa_reconciliation', $data, 'id_doa_stock');
    }
    public function doa_stock_dc($iddoa_transfer){
        return $this->db->select('ss.id_service,brw.branch_name as warbranch_name,brw.branch_contact as warbranch_contact,brw.branch_address as warbranch_address,brw.branch_gstno as warbranch_gstno,mv.full_name,dr.last_purchaseprice,dss.*,ss.imei,vdr.vendor_name,vdr.vendor_contact,vdr.vendor_address,vdr.vendor_gst')
                    ->where('dr.iddoa_stock_shipment', $iddoa_transfer)
                    ->where('dss.transfer_from=brw.id_branch')->from('branch brw')
                    ->where('dss.idvendor=vdr.id_vendor')->from('vendor vdr')
                    ->where('dss.id_doa_stock_shipment', $iddoa_transfer)
                    ->where('dr.idservice = ss.id_service')->from('service_stock ss')
                    ->where('ss.idvariant = mv.id_variant')->from('model_variants mv')
                    ->from('doa_stock_shipment dss')
                    ->get('doa_reconciliation dr')->result();
    }
    public function ajax_get_doa_stock_for_recon($idvendor,$idbrand){
        $this->db->select('dr.*,model_variants.full_name,ss.imei as doa_imei,ss.counter_faulty,ss.idvariant,dss.dispatch_date,vdr.vendor_name,vdr.vendor_contact,vdr.vendor_address,vdr.vendor_gst');
        if($idvendor){
            $this->db->where('dr.idvendor',$idvendor);
        }
        if($idbrand){
            $this->db->where('ss.idbrand',$idbrand);
        }
        return $this->db->where('dr.status = 2')
//                        ->where('dr.doa_return_type = 1')
                        ->where('dr.iddoa_stock_shipment = dss.id_doa_stock_shipment')->from('doa_stock_shipment dss')
                        ->where('dr.idservice = ss.id_service')->from('service_stock ss')
                        ->where('dss.idvendor=vdr.id_vendor')->from('vendor vdr')
                        ->where('ss.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('doa_reconciliation dr')->result();
    }
    public function ajax_get_doa_closure_report($idvendor,$idbrand,$datefrom,$dateto){
        $this->db->select('dr.*,model_variants.full_name,ss.imei as doa_imei,ss.counter_faulty,ss.idvariant,dss.dispatch_date,vdr.vendor_name,vdr.vendor_contact,vdr.vendor_address,vdr.vendor_gst');
        if($datefrom && $dateto){
            $this->db->where('dr.closure_date >=', $datefrom);
            $this->db->where('dr.closure_date <=', $dateto);
        }
        if($idvendor){
            $this->db->where('dr.idvendor',$idvendor);
        }
        if($idbrand){
            $this->db->where('ss.idbrand',$idbrand);
        }
        return $this->db->where('dr.status = 1')
                        ->where('dr.doa_return_type = 1')
                        ->where('dr.iddoa_stock_shipment = dss.id_doa_stock_shipment')->from('doa_stock_shipment dss')
                        ->where('dr.idservice = ss.id_service')->from('service_stock ss')
                        ->where('dss.idvendor=vdr.id_vendor')->from('vendor vdr')
                        ->where('ss.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('doa_reconciliation dr')->result();
    }
    public function ajax_get_doa_inward_report($idvendor,$idbrand){
        $this->db->select('dr.*,model_variants.full_name,di.doa_imei,ss.counter_faulty,ss.idvariant,vdr.vendor_name,mvn.full_name as new_product,mvn.idmodel,mvn.idbrand as new_idbrand,mvn.idproductcategory as nidproductcategory,mvn.idcategory as nidcategory,mvn.idsku_type as nidsku_type');
        if($idvendor){
            $this->db->where('dr.idvendor',$idvendor);
        }
        if($idbrand){
            $this->db->where('di.idbrand',$idbrand);
        }
        return $this->db->where('dr.status = 1')
                        ->where('dr.doa_return_type = 1')
                        ->where('di.inward_against_letter = 1')
                        ->where('dr.iddoainward = di.id_doa_inward')->from('doa_inward di')
                        ->where('dr.idservice = ss.id_service')->from('service_stock ss')
                        ->where('dr.idvendor = vdr.id_vendor')->from('vendor vdr')
                        ->where('ss.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('di.idvariant = mvn.id_variant')->from('model_variants mvn')
                        ->get('doa_reconciliation dr')->result();
    }
    public function check_doa_reconc_ornot($id_doa_stock) {
        return $this->db->where('dr.status = 1')
                        ->where('dr.doa_return_type = 1')
                        ->where('dr.id_doa_stock', $id_doa_stock)
                        ->get('doa_reconciliation dr')->result();
    }
    public function update_doa_inward($iddoainward, $doa_in) {
        return $this->db->where('id_doa_inward', $iddoainward)->update('doa_inward', $doa_in);
    }
    
}
?>