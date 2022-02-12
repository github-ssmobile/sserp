<?php

class Old_erp_model extends CI_Model {
    
    public function save_old_credit_data($data) {
        return $this->db->insert_batch('old_credit_custody', $data);
    }
    public function save_bulk_sale_payment($data) {
        return $this->db->insert_batch('sale_payment', $data);
    }
    public function save_sale_payment($data) {
        $this->db->insert('sale_payment', $data);
        return $this->db->insert_id();
    }
    public function save_payment_reconciliation($data) {
        $this->db->insert('payment_reconciliation', $data);
        return $this->db->insert_id();
    }
    public function get_daily_stock_data() {
        return $this->db->select('stock.idvariant,stock.product_name,stock.imei_no,stock.qty,branch.branch_name,product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp')
                        ->where('stock.idbranch = branch.id_branch')->from('branch')
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->get('stock')->result();
    }
    public function get_daily_transit_stock_data() {
        return $this->db->select('stock.idvariant,stock.product_name,stock.imei_no,stock.qty, brs.branch_name as sender, brr.branch_name as receiver, product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp')
                        ->where('stock.idbranch = 0')
                        ->where('stock.transfer_from = brs.id_branch')->from('branch brs')
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.temp_idbranch = brr.id_branch')->from('branch brr')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->get('stock')->result();
    }
    
    public function ajax_get_sale_data_byfilter($from, $to, $idbranch){
        $id = $_SESSION['id_users'];
        if($idbranch == 0){ 
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
         
        return $this->db->select('sale.manager_price,sale.customer_price,sale.invoice_date,sale.invoice_no,sale.id_old_sale,sale.customer_name,sale.customer_mobile,sale.customer_gst_no,sale.imei_1_no,sale.serial_no,sale.category,sale.brand,sale.product_name,sale.settlement_amount,sale.promoter_name,branch.branch_name')                      
                        ->where('sale.invoice_date >=', $from)
                        ->where('sale.invoice_date <=', $to)
                        ->where_in('sale.idbranch', $branchid)
                        ->join('branch','sale.idbranch = branch.id_branch', 'left')
                        ->get('olderp_sale_data sale')->result();        
//       die(print_r($str));
    }    
    public function get_sale_by_inv($inv){
        return $this->db->select("*")->where('invoice_no', $inv)->get('olderp_sale_data')->result();           
    }    
    public function get_sale_byid($idsale) {
        return $this->db->select('sale.*,users.user_name,print_head.*,customer.*,branch.*')->where('id_sale',$idsale)
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idcustomer=customer.id_customer')->from('customer')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale')->result();
    }
    public function get_printhead_bid($idbranch) {
        return $this->db->select('print_head.*,branch.*') ->where('branch.idprinthead = print_head.id_print_head')  
                        ->where('branch.id_branch',$idbranch)->from('branch')
                        ->get('print_head')->row();
    }
    public function ajax_get_sale_search_invoice($invoice, $imei, $mobile){
         if($invoice || $imei || $mobile){
            $this->db->select("olderp_sale_data.*,branch.branch_name");
            if($invoice){                            
                $this->db->like('olderp_sale_data.invoice_no', $invoice, 'both');   
            }
            if($mobile){                
                $this->db->like('olderp_sale_data.customer_mobile', $mobile, 'both');                                
            }
            if($imei){
                $this->db->where('olderp_sale_data.imei_1_no='.$imei.' or olderp_sale_data.serial_no='.$imei);                
            }
            $this->db->join('branch','idbranch = branch.id_branch', 'left');
            return $this->db->get('olderp_sale_data')->result();           
         
         }else{
             $a=array();
             return $a;
         }
    }
}
