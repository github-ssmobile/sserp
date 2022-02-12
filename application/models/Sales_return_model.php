<?php
class Sales_return_model extends CI_Model {
    // Save sales return
    public function save_sales_return($data) {
        $this->db->insert('sales_return', $data);
        return $this->db->insert_id();
    }
    public function save_sales_return_product($data) {
        $this->db->insert('sales_return_product', $data);
        return $this->db->insert_id();
    }
    public function get_sales_return() {
        return $this->db->where('sales_return.idbranch = id_branch')->from('branch')
                        ->where('sales_return_by = id_users')->from('users')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->order_by('sales_return.date','desc')
                        ->get('sales_return')->result();
    }
    public function get_sales_return_byidbranch($idbranch) {
        return $this->db->select('sales_return.*, users.user_name, u.user_name as uname, customer.*, branch.* ')
                        ->where('sales_return.idbranch', $idbranch)
                        ->where('sales_return.idbranch = id_branch')->from('branch')
                        ->where('sales_return_by = users.id_users')->from('users')
                        ->where('idsalesperson = u.id_users')->from('users u')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->order_by('sales_return.date','desc')
                        ->get('sales_return')->result();
    }
    public function ajax_get_sales_return_byidbranch($from, $to, $idbranch, $viewbranches) {
        if($idbranch == 0){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        return $this->db->select('sales_return.*, users.user_name, u.user_name as uname, customer.*, branch.* ')
                        ->where('sales_return.date >=', $from)
                        ->where('sales_return.date <=', $to)
                        ->where_in('sales_return.idbranch', $branches)
                        ->where('sales_return.idbranch = id_branch')->from('branch')
                        ->where('sales_return_by = users.id_users')->from('users')
                        ->where('idsalesperson = u.id_users')->from('users u')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->order_by('sales_return.date','desc')
                        ->get('sales_return')->result();
    }
    public function get_sales_return_byid($id){
        return $this->db->where('id_salesreturn', $id)
                        ->where('branch.id_branch = sales_return.idbranch')->from('branch')
                        ->where('customer.id_customer = sales_return.idcustomer')->from('customer')
                        ->where('idsalesperson = id_users')->from('users')->order_by('sales_return.id_salesreturn', 'desc')
                        ->get('sales_return')->result();
    }
    public function get_sales_return_product_byid($id){
        return $this->db->where('idsales_return', $id)
                        ->where('id_sku_type = idskutype')->from('sku_type')
                        ->get('sales_return_product')->result();
    }
    public function ajax_get_sales_return_product_data($from, $to, $idbranch, $viewbranches){
        if($idbranch == 0){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        return $this->db->select('sales_return_product.*, users.user_name, u.user_name as uname, customer.customer_gst, customer.customer_contact, customer.customer_fname,customer.customer_lname, branch.*, sales_return.sales_return_reason, sales_return.sales_return_approved_by,sales_return.idsale,sales_return.inv_date ')
                        ->where('sales_return_product.date >=', $from)
                        ->where('sales_return_product.date <=', $to)
                        ->where_in('sales_return_product.idbranch', $branches)
                        ->where('customer.customer_gst != ','')
                        ->where('sales_return_product.idbranch = id_branch')->from('branch')
                        ->where('sales_return_product.sales_return_by = users.id_users')->from('users')
                        ->where('sales_return_product.idsales_return = sales_return.id_salesreturn')->from('sales_return')
                        ->where('sales_return.idsalesperson = u.id_users')->from('users u')
                        ->where('sales_return.idcustomer = customer.id_customer')->from('customer')
                        ->order_by('sales_return_product.date','desc')
                        ->get('sales_return_product')->result();
    }
}
