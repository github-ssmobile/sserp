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
    
}
?> 