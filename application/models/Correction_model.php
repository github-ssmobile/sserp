<?php
class Correction_model extends CI_Model{
    public function get_correction_system(){
        return $this->db->where('ac',0)->get('correction_system')->result();
    }
    public function get_helpline_type(){
        return $this->db->get('helpline_type')->result();
    }
    public function get_corretcion_type(){
        return $this->db->get('correction_type')->result();
    }
    public function get_customer_data(){
        return $this->db->get('customer')->result();
    }
    public function get_customer_bycontact($contact){
        return $this->db->where('customer_contact', $contact)->get('customer')->result();
    }
    public function save_correction_data($data){
        return $this->db->insert('correction_request', $data);
    }
    public function get_pending_correction_request($branch){
        if($_SESSION['idrole'] == 23){
            return $this->db->select('correction_request.*,customer.customer_fname, customer.customer_lname,users.user_name,old.payment_mode as oldpaymentmode,new.payment_mode as newpaymentmode,branch.branch_name,correction_system.system_name,helpline_type.helpline_type,correction_type.correction_type')
                        ->where('status',0)
                        ->where_in('correction_request.created_by', $_SESSION['id_users'])
                        ->where('correction_request.idsystem = correction_system.id_correction_system')->from('correction_system')
                        ->where('correction_request.idhelpline = helpline_type.id_helpline_type')->from('helpline_type')
                        ->where('correction_request.idcorrectiontype = correction_type.id_correction_type')->from('correction_type')
                        ->join('branch','branch.id_branch = correction_request.idbranch','left')
                        ->join('customer','customer.id_customer = correction_request.idcustomer','left')
                        ->join('users','users.id_users = correction_request.created_by','left')
                        ->join('payment_mode old','old.id_paymentmode = correction_request.idold_paymentmode','left')
                        ->join('payment_mode new','new.id_paymentmode = correction_request.idnew_paymentmode','left')
                        ->order_by('correction_request.id_correction_request')
                        ->get('correction_request')->result();
        }else{
            return $this->db->select('correction_request.*,customer.customer_fname, customer.customer_lname,users.user_name,old.payment_mode as oldpaymentmode,new.payment_mode as newpaymentmode,branch.branch_name,correction_system.system_name,helpline_type.helpline_type,correction_type.correction_type')
                            ->where('status',0)
                            ->where_in('correction_request.idbranch', $branch)
                            ->where('correction_request.idsystem = correction_system.id_correction_system')->from('correction_system')
                            ->where('correction_request.idhelpline = helpline_type.id_helpline_type')->from('helpline_type')
                            ->where('correction_request.idcorrectiontype = correction_type.id_correction_type')->from('correction_type')
                            ->join('branch','branch.id_branch = correction_request.idbranch','left')
                            ->join('customer','customer.id_customer = correction_request.idcustomer','left')
                            ->join('users','users.id_users = correction_request.created_by','left')
                            ->join('payment_mode old','old.id_paymentmode = correction_request.idold_paymentmode','left')
                            ->join('payment_mode new','new.id_paymentmode = correction_request.idnew_paymentmode','left')
                            ->order_by('correction_request.id_correction_request')
                            ->get('correction_request')->result();
        }
    }
    public function get_branch_pending_correction_request($branch){
        $st =array(0,1);
        return $this->db->select('correction_request.*,customer.customer_fname, customer.customer_lname,users.user_name,old.payment_mode as oldpaymentmode,new.payment_mode as newpaymentmode,branch.branch_name,correction_system.system_name,helpline_type.helpline_type,correction_type.correction_type')
                        ->where_in('status',$st)
                        ->where_in('correction_request.idbranch',$branch)
                        ->where('correction_request.idsystem = correction_system.id_correction_system')->from('correction_system')
                        ->where('correction_request.idhelpline = helpline_type.id_helpline_type')->from('helpline_type')
                        ->where('correction_request.idcorrectiontype = correction_type.id_correction_type')->from('correction_type')
                        ->join('branch','branch.id_branch = correction_request.idbranch','left')
                        ->join('customer','customer.id_customer = correction_request.idcustomer','left')
                        ->join('users','users.id_users = correction_request.created_by','left')
                        ->join('payment_mode old','old.id_paymentmode = correction_request.idold_paymentmode','left')
                        ->join('payment_mode new','new.id_paymentmode = correction_request.idnew_paymentmode','left')
                        ->order_by('correction_request.id_correction_request', 'ASC')
                        ->get('correction_request')->result();
    }
    public function update_correction_request($data, $idcorrectionreq){
        return $this->db->where('id_correction_request',$idcorrectionreq)->update('correction_request', $data);
    }
    
    public function get_helpline_report($from, $to, $idbranch, $idstatus, $branches){
        if($idstatus == ''){
            $status = array(0,1,2);
        }else{
            $status[] = $idstatus;
        }
        
        if($idbranch == 0){
            $viewbranches = explode(',',$branches);
        }else{
            $viewbranches[] = $idbranch;
        }
        
        return $this->db->select('correction_request.*,customer.customer_fname, customer.customer_lname,users.user_name,old.payment_mode as oldpaymentmode,new.payment_mode as newpaymentmode,branch.branch_name,correction_system.system_name,helpline_type.helpline_type,correction_type.correction_type,u.user_name as updted')
                        ->where('correction_request.date >=',$from)
                        ->where('correction_request.date <=',$to)
                        ->where_in('correction_request.idbranch',$viewbranches)
                        ->where_in('correction_request.status',$status)
                        ->where('correction_request.idsystem = correction_system.id_correction_system')->from('correction_system')
                        ->where('correction_request.idhelpline = helpline_type.id_helpline_type')->from('helpline_type')
                        ->where('correction_request.idcorrectiontype = correction_type.id_correction_type')->from('correction_type')
                        ->join('branch','branch.id_branch = correction_request.idbranch','left')
                        ->join('customer','customer.id_customer = correction_request.idcustomer','left')
                        ->join('users','users.id_users = correction_request.created_by','left')
                        ->join('users u','u.id_users = correction_request.updated_by','left')
                        ->join('payment_mode old','old.id_paymentmode = correction_request.idold_paymentmode','left')
                        ->join('payment_mode new','new.id_paymentmode = correction_request.idnew_paymentmode','left')
                        ->get('correction_request')->result();
    }
    public function delete_duplicate_invoice($idsale){
         $this->db->trans_begin();
        $this->db->where('id_sale',$idsale)->delete('sale');
        $this->db->where('idsale',$idsale)->delete('sale_product');
        $this->db->where('idsale',$idsale)->delete('sale_payment');
        $this->db->where('idsale',$idsale)->delete('payment_reconciliation');
        $this->db->where('idsale',$idsale)->delete('insurance_reconciliation');
        $this->db->where('idtable',$idsale)->delete('daybook_cash');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return 0;
        } else {                  
            $this->db->trans_commit();
            return 1;
        }
    }
    
    public function get_duplicate_invoice(){         
         return $this->db->select('inv_no')->where('idskutype!=4')->where('sales_return_type', 0)
                 ->group_by('imei_no')
                 ->having('COUNT(*)>1')
                 ->get('sale_product')->result();  
    }
            
}
?> 