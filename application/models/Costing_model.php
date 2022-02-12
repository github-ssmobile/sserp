<?php
class Costing_model extends CI_Model{
    public function get_branch_cost_header(){
       return $this->db->get('branch_cost_headers')->result();
    }
    
    public function Save_costing_header($data) {
        return $this->db->insert('branch_cost_headers', $data);
    }
    
    public function edit_costing_header($data, $id){
        return $this->db->where('id_cost_header', $id)->update('branch_cost_headers', $data);
    }
    
    public function get_active_branch_costing_headers(){
        return $this->db->where('status', 0)->get('branch_cost_headers')->result();
    }
public function get_branch_cost_header_byid($idcostheader){
    return $this->db->where('id_cost_header', $idcostheader)->get('branch_cost_headers')->row();
}    
    public function get_user_has_costing_header_by_user($iduser){
        return $this->db->where('iduser', $iduser)->where('idcosting_header = branch_cost_headers.id_cost_header')->from('branch_cost_headers')->get('user_has_costing_headers')->result();
    }
    public function delete_user_has_costing_header($id){
        return $this->db->where('id_user_has_costing_headers', $id)
                        ->delete('user_has_costing_headers');
    }
    public function delete_user_has_costing_header_byiduser($iduser){
        return $this->db->where('iduser', $iduser)->delete('user_has_costing_headers');
    }
    public function save_branch_costing_data($data) {
        return $this->db->insert('branch_costing_data', $data);
    }
    public function get_branch_cost_data_bymonth_idcost($monthyear, $idcostheader){
        return $this->db->select('branch_costing_data.*,branch.id_branch, branch.branch_name,z.zone_name')
                        ->where('month_year', $monthyear)
                        ->where('idcost_header', $idcostheader)
						 ->where('branch.idzone = z.id_zone')->from('zone z')
                        ->where('idbranch = branch.id_branch')->from('branch')
						->order_by('branch.idzone,branch.branch_name')
                        ->get('branch_costing_data')->result();
    }
    public function update_branch_costing_data($id_branch_costing_data, $data){
        return $this->db->where('id_branch_costing_data', $id_branch_costing_data)
                        ->update('branch_costing_data', $data);
    }
    
    public function get_branch_with_sale_data($monthyear, $idcostheader){
    $from = $monthyear.'-01';
    $to = date('Y-m-t', strtotime($from));
    $lastmonth = date('Y-m', strtotime($monthyear." -1 month"));

    $this->db->select('b.id_branch,b.branch_name,b.acc_branch_id,zone.id_zone,zone.zone_name,sa.sale_total,sret.sale_return_total,bc.last_val');
    $this->db->where('b.is_warehouse',0);
    $this->db->join("(select bcd.value as last_val, bcd.idbranch from branch_costing_data bcd WHERE bcd.month_year ='$lastmonth' and bcd.idcost_header = $idcostheader) bc", 'bc.idbranch = b.id_branch', 'left');
    $this->db->join("(select sum(s.final_total) as sale_total, s.idbranch from sale s WHERE s.date >='$from' and s.date <='$to' group by s.idbranch) sa", 'sa.idbranch = b.id_branch', 'left');
    $this->db->join("(select sum(srt.final_total) as sale_return_total, srt.idbranch from sales_return srt WHERE srt.date >='$from' and srt.date <='$to' group by srt.idbranch) sret", 'sret.idbranch = b.id_branch', 'left');
    $this->db->where('b.idzone = zone.id_zone')->from('zone');
    $this->db->group_by('b.id_branch');
    $this->db->order_by('zone.id_zone');
    $this->db->from('branch b');
    $query = $this->db->get();  
    return $query->result();
}


    //*********Company Cost Data***********//
public function get_company_cost_header(){
   return $this->db->get('company_costing_header')->result();
}
public function save_company_cost_header($data) {
    return $this->db->insert('company_costing_header', $data);
}

public function edit_company_costing_header($data, $id){
    return $this->db->where('id_company_costing', $id)->update('company_costing_header', $data);
}
public function get_active_company_cost_header(){
   return $this->db->where('status', 0)->get('company_costing_header')->result();
}
public function get_company_cost_data_bymonth_idcost($monthyear){
  return $this->db->select('h.id_company_costing, h.company_cost_name,a.*')
  ->where('h.status', 0)
  ->join('company_cost_data a', "a.idcompany_cost_header=h.id_company_costing and a.month_year='$monthyear'",'left')
  ->get('company_costing_header h')->result();
//          die(print_r($this->db->last_query()));
}

public function update_company_costing_data($id_branch_costing_data, $data){
    return $this->db->where('id_company_cost_data', $id_branch_costing_data)
    ->update('company_cost_data', $data);
}

public function save_company_costing_data($data) {
    return $this->db->insert('company_cost_data', $data);
}
public function delete_company_cost_data_bymonyh($monthyear){
    return $this->db->where('month_year', $monthyear)->delete('company_cost_data');
}

    //************Combined Report**********//
public function get_acc_branch_byidzone($idzone){
    return $this->db->select('acc_branch_id')->where('idzone', $idzone)->get('branch')->result();
}
public function get_branch_byidzone($idzone){
    return $this->db->select('id_branch')->where('idzone', $idzone)->get('branch')->result();
}

public function get_branch_cost_data_byidbranch($idbranch, $monthyear){
    $from = $monthyear.'-01';
    $to = date('Y-m-t', strtotime($from));
    $this->db->select('b.id_branch,b.branch_name,b.acc_branch_id,branch_category.branch_category_name,zone.id_zone,zone.zone_name,bc.cost_header_name,bc.id_cost_header,branch_cost.volume,branch_cost.value,sa.sale_total,sret.sale_return_total,b.idpartner_type,bc.idtype,b.idbranchcategory,b.idpartner_type');
    $this->db->where_in('b.id_branch', $idbranch);
    $this->db->where('b.is_warehouse',0);
    $this->db->join("(select tar.volume, tar.value, tar.idcost_header,tar.idbranch from branch_costing_data tar WHERE tar.month_year = '$monthyear') branch_cost", 'branch_cost.idbranch = b.id_branch and branch_cost.idcost_header = bc.id_cost_header', 'left');
    $this->db->join("(select sum(s.final_total) as sale_total, s.idbranch from sale s WHERE s.date >='$from' and s.date <='$to' group by s.idbranch) sa", 'sa.idbranch = b.id_branch', 'left');
    $this->db->join("(select sum(srt.final_total) as sale_return_total, srt.idbranch from sales_return srt WHERE srt.date >='$from' and srt.date <='$to' group by srt.idbranch) sret", 'sret.idbranch = b.id_branch', 'left');
    $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
    $this->db->where('b.idzone = zone.id_zone')->from('zone');
    $this->db->where('bc.status',0)->from('branch_cost_headers bc');
    $this->db->order_by('b.id_branch, bc.id_cost_header');
    $this->db->from('branch b');
    $query = $this->db->get();  
    return $query->result();
}


public function get_sale_data_byid($idbranch, $monthyear){

    $from = $monthyear.'-01';
    $to = date('Y-m-t', strtotime($from));
    return $this->db->select('sum(final_total) as sale_total, idbranch')
    ->where_in('idbranch', $idbranch)
    ->where('date >=', $from)
    ->where('date <=', $to)
    ->group_by('idbranch')
    ->get('sale')->result();
}
public function get_sales_return_data_byid($idbranch, $monthyear){
    $from = $monthyear.'-01';
    $to = date('Y-m-t', strtotime($from));
    return $this->db->select('sum(final_total) as sale_return_total, idbranch')
    ->where_in('idbranch', $idbranch)
    ->where('date >=', $from)
    ->where('date <=', $to)
    ->group_by('idbranch')
    ->get('sales_return')->result();
}
        
    public function getallbranches_rent()
{
    $this->db->select('c.branch_id, c.original_branch_id, c.branch_name, c.branch_category, r.deposit_amt, r.deposit_paid_amt, r.deposit_paid_date, r.trans_id, r.remark,r.rent_doc,r.deposit_status receive_status ,r.owner_bank_name,r.owner_bank_accno,r.owner_bank_ifsc,passbook_doc,cheque_doc')
    ->from('cost_center_branch as c')
    ->join('branch_rent_details as r', 'c.branch_id = r.branch_id')
    ->where('r.legal_approve', '1');
     $this->db->group_by('c.branch_id');
    $this->db->order_by('r.deposit_paid_amt', 'asc');
    $result = $this->db->get();
    return $result->result();

}
public function getallbranches_cp()
{
  $this->db->select('c.branch_id, c.original_branch_id, c.branch_name, c.branch_category, r.deposit_amt, r.deposit_rec_amt deposit_paid_amt, r.deposit_rec_date deposit_paid_date , r.trans_id, r.remark,r.agreement_doc,r.receive_status,r.owner_bank_name,r.owner_bank_accno,r.owner_bank_ifsc')
    ->from('cost_center_branch as c')
    ->join('branch_channel_partner_details as r', 'c.branch_id = r.branch_id');
    $this->db->group_by('c.branch_id');
    $this->db->order_by('r.deposit_rec_amt', 'asc');
    $result = $this->db->get();
    return $result->result();


} 
}
?> 