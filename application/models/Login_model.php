<?php
class Login_model extends CI_Model {

    public function login($uid, $pass){
        $this->db->select('*');
        $this->db->from('users'); 
        $this->db->join('branch b','users.idbranch = b.id_branch','left');
        $this->db->where('userid',$uid)->where('user_password',$pass);
//        $this->db->where('users.active =1');
        $this->db->where('users.iduserrole = user_role.id_userrole')->from('user_role');
        $query = $this->db->get();
        return $query;
    }
    
    public function forgot_password($uid, $mobile,$data){
        return $this->db->where('userid',$uid)->where('mobile',$mobile)->update('users',$data);
    }
    
}
