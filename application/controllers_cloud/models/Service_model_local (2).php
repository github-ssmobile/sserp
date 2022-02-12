<?php
class Service_model extends CI_Model{
   
///////// SERVICE - VINAYAK P   /////////////
    
    public function get_old_sale_byinvno($invno){        
            return $this->db->where('os.invoice_no', $invno)                            
                            ->where('os.idbranch=b.id_branch')->from('branch b')
                            ->get('olderp_sale_data os')->result(); 
    }
    public function get_sale_by_invno($invno){
            return $this->db->where('inv_no', $invno)
                            ->where('idsalesperson = id_users')->from('users')
                            ->where('branch.id_branch=sale.idbranch')->from('branch')
                            ->get('sale', 1)->result();        
    }
    public function get_sale_product_by_invno($invno){       
            return $this->db->where('inv_no', $invno)->where('branch.id_branch=sale_product.idbranch')->from('branch')
                            ->where('id_sku_type = idskutype')->from('sku_type')
                            ->get('sale_product')->result();
    }    
    public function get_inv_byimei($imei){
        return $this->db->select("inv_no")->where('imei_no', $imei)->limit(1)->order_by('date', 'desc')->get('sale_product')->row();
    }
    public function get_olderp_inv_byimei($imei){
        return $this->db->select("invoice_no")->where("(imei_1_no='".$imei."' OR serial_no='".$imei."')", NULL, FALSE)->limit(1)->order_by('invoice_date', 'desc')->get('olderp_sale_data')->row();         
    }
    public function get_service_problems(){
         return $this->db->get('service_problems')->result();
    }
    public function save_service_inward($data) {
        $this->db->insert('service_stock', $data);
        return $this->db->insert_id();        
    }
    public function get_service_details_byid($id){
        return $this->db->select("ss.*,mv.full_name,b.*,u.user_name")->where('id_service', $id)     
                        ->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                        ->where('ss.idbranch=b.id_branch')->from('branch b')
                        ->where('u.id_users=ss.idsalesperson')->from('users u')    
                        ->get('service_stock ss')->result();
    }
    public function get_service_stock_report($idbrand, $idproductcategory,$idbranch,$status){
                $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status");
                
                if($status){
                    $this->db->where('process_status', $status);                
                }
                if($idbrand){
                    $this->db->where('ss.idbrand', $idbrand);                    
                }
                if($idbranch){
                    $this->db->where('ss.idbranch', $idbranch);                    
                }
                if($idproductcategory){
                    $this->db->where('ss.idproductcategory', $idproductcategory);                    
                }        
                $this->db->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                        ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                        ->where('ss.idbrand=br.id_brand')->from('brand br')
                        ->where('ss.idbranch=b.id_branch')->from('branch b')
                        ->where('ss.process_status=ps.id')->from('service_process_status ps')
                        ->where('u.id_users=ss.idsalesperson')->from('users u');
                return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
     public function get_inprocess_service_stock_report($idbrand, $idproductcategory,$idbranch,$type){
                $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status");
                if($type==1){ ///LOCAL
                     $this->db->where('process_status', 2);        
                }elseif($type==0){ //HO
                     $this->db->where('process_status in (4,5,6)');  
                }else{
                    $this->db->where('process_status in (2,4,5,6)'); 
                }
                if($idbrand){
                    $this->db->where('ss.idbrand', $idbrand);                    
                }
                if($idbranch){
                    $this->db->where('ss.idbranch', $idbranch);                    
                }
                if($idproductcategory){
                    $this->db->where('ss.idproductcategory', $idproductcategory);                    
                }        
                $this->db->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                        ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                        ->where('ss.idbrand=br.id_brand')->from('brand br')
                        ->where('ss.idbranch=b.id_branch')->from('branch b')
                        ->where('ss.process_status=ps.id')->from('service_process_status ps')
                        ->where('u.id_users=ss.idsalesperson')->from('users u');
                return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
    public function update_service_stock($idservice, $data) {
        return $this->db->where('id_service', $idservice)->update('service_stock', $data);
    }
    
    public function get_sale_product_by_idsaleproduct($id,$inv){
        return $this->db->select("s.gst_type,s.customer_fname,s.customer_lname,s.customer_gst,sp.*")->where('sp.inv_no', $inv)->where('sp.idsale=s.id_sale')->from('sale s')->where('sp.id_saleproduct', $id)->limit(1)->order_by('sp.date', 'desc')->get('sale_product sp')->row();
    }
    public function get_sale_product_by_idproduct($id,$inv){
        return $this->db->select("*")->where('invoice_no', $inv)->where('id_old_sale', $id)->limit(1)->order_by('invoice_date', 'desc')->get('olderp_sale_data')->row();         
    }
    
    public function update_stock($idservice, $data) {
        return $this->db->where('idservice', $idservice)->update('stock', $data);
    }
     public function delete_idservice_from_stock($idservice){
        return $this->db->where('idservice', $idservice)->delete('stock');
    }
    
}
?>