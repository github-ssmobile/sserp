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
        $this->db->select("srp.new_imei_against_doa,ss.*,mv.full_name,b.*,u.user_name,service_process_status.*")->where('id_service', $id)     
                        ->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                        ->where('ss.idbranch=b.id_branch')->from('branch b')
                        ->where('ss.process_status=service_process_status.id')->from('service_process_status')
                        ->where('u.id_users=ss.idsalesperson')->from('users u');
        $this->db->join('sales_return_product srp','`srp`.`imei_no` = `ss`.`imei` and `srp`.`sales_return_invid` = `ss`.`sales_return_invid`','left');  
        return  $this->db->get('service_stock ss')->result();
    }
    public function service_counter_faulty_details($id){
        return $this->db->select("ss.*,mv.full_name,mv.mop,b.*,service_process_status.*")->where('id_service', $id)
                ->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                ->where('ss.idbranch=b.id_branch')->from('branch b')
                ->where('ss.process_status=service_process_status.id')->from('service_process_status')
                ->get('service_stock ss')->result();
//                ->where('u.id_users=ss.idsalesperson')->from('users u');
//                $this->db->join('sales_return_product srp','`srp`.`imei_no` = `ss`.`imei` and `srp`.`sales_return_invid` = `ss`.`sales_return_invid`','left');
    }
    
    public function service_shipment_at_ho($idservice_transfer){
//        return $this->db->select("service_stock.*,mv.full_name,b.*")
//                    ->where('service_stock.id_service', $idservice_transfer)
//                    ->where('service_stock.idvariant=mv.id_variant')->from('model_variants mv')
//                    ->where('service_stock.idbranch=b.id_branch')->from('branch b')
//                    ->where('service_transfer_product.idservice_transfer = service_transfer.id_service_transfer')
//                    ->where('service_stock.idservice_transfer_send_to_ho=service_transfer.id_service_transfer')
//                    ->from('service_transfer')->from('service_transfer_product')
//                    ->get('service_stock')->result();
//        
        return $this->db->select('service_stock.*,b.branch_name,b.branch_contact,b.branch_address,b.branch_gstno,brw.branch_name as warbranch_name,brw.branch_contact as warbranch_contact,brw.branch_address as warbranch_address,brw.branch_gstno as warbranch_gstno,mv.full_name,mv.mop, service_transfer.idbranch as received_at,service_transfer.*')
//                    ->where('service_stock.idservice_transfer_send_to_ho', $idservice_transfer)
                    ->where('service_stock.idservice_transfer_send_to_ho', $idservice_transfer)
                    ->where('service_stock.idvariant=mv.id_variant')->from('model_variants mv')
                    ->where('service_stock.idbranch=b.id_branch')->from('branch b')
                    ->where('service_stock.idwarehouse=brw.id_branch')->from('branch brw')
//                    ->where('service_transfer_product.idservice_transfer = service_transfer.id_service_transfer')
                    ->where('service_stock.idservice_transfer_send_to_ho=service_transfer.id_service_transfer')
                    ->from('service_transfer')
//                    ->from('service_transfer_product')
                    ->get('service_stock')->result();
//         die(print_r($this->db->last_query()));
    }
    
    public function service_shipment_at_branch($idservice_transfer){
        return $this->db->select('service_stock.*,b.branch_name,b.branch_contact,b.branch_address,b.branch_gstno,brw.branch_name as warbranch_name,brw.branch_contact as warbranch_contact,brw.branch_address as warbranch_address,brw.branch_gstno as warbranch_gstno,mv.full_name,mv.mop, service_transfer.idbranch as received_at,service_transfer.*')
                    ->where('service_stock.idservice_transfer_send_to_branch', $idservice_transfer)
//                    ->where('service_transfer.transfer_process_status = 9')
                    ->where('service_stock.idvariant=mv.id_variant')->from('model_variants mv')
                    ->where('service_stock.idbranch=b.id_branch')->from('branch b')
                    ->where('service_stock.idwarehouse=brw.id_branch')->from('branch brw')
                    ->where('service_stock.idservice_transfer_send_to_branch=service_transfer.id_service_transfer')
                    ->from('service_transfer')
                    ->get('service_stock')->result();
    }
    
    public function get_service_stock_report($idbrand, $idproductcategory,$idbranch,$status,$warranty){
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
        if($warranty != 0){
            $this->db->where('ss.warranty_status', $warranty);
        }
        $this->db->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                ->where('ss.idbrand=br.id_brand')->from('brand br')
                ->where('ss.idbranch=b.id_branch')->from('branch b')
                ->where('ss.process_status=ps.id')->from('service_process_status ps')
                ->join('users u','u.id_users=ss.idsalesperson', 'left');
        return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
    
    public function get_service_stock_for_process($idbrand, $idproductcategory,$idbranch,$status){
        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status,stp.*");

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
                ->where('stp.idservice_stock=ss.id_service')->from('service_transfer_product stp')
                ->join('users u','u.id_users=ss.idsalesperson', 'left');
        return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
    public function get_ho_shipment_service_stock_for_process($idbranch){
        $this->db->select("st.*,b.branch_name,u.user_name");
        if($idbranch){
            $this->db->where('st.transfer_from', $idbranch);
        }
        $this->db->where('st.transfer_from=b.id_branch')->from('branch b')
                ->where('st.status = 0')->where('st.idbranch = '.$this->session->userdata('idbranch'))
                ->join('users u','u.id_users=st.created_by', 'left');
        return $this->db->get('service_transfer st')->result();
//        die(print_r($this->db->last_query()));
    }
    public function get_service_ho_send_to_branch_process($idbranch){
        $this->db->select("st.*,b.branch_name,u.user_name");
        if($idbranch){
            $this->db->where('st.idbrand', $idbranch);
        }
        $this->db->where('st.transfer_from=b.id_branch')->from('branch b')
                ->where('st.status = 0')->where('st.idbranch = '.$this->session->userdata('idbranch'))
                ->join('users u','u.id_users=st.created_by', 'left');
        return $this->db->get('service_transfer st')->result();
//        die(print_r($this->db->last_query()));
    }
    public function ajax_get_doa_inward_list($brand,$idbranch,$warranty){
        $this->db->select("doa_inward.*,b.branch_name,u.user_name,mvs.full_name");
        if($idbranch){ $this->db->where('doa_inward.idbranch', $idbranch); }
        if($brand){ $this->db->or_where('idbrand', $brand); }
        if($warranty == 3){ // letter
            $this->db->where('idvariant = 0'); 
        }elseif($warranty == 4){ // letter
            $this->db->where('idvariant != 0'); 
        }
        $this->db->where('doa_inward.idbranch=b.id_branch')->from('branch b')
                ->join('users u','u.id_users=doa_inward.created_by', 'left')
                ->join('model_variants mvs','mvs.id_variant=doa_inward.idvariant', 'left');
        return $this->db->get('doa_inward')->result();
    }
    public function get_service_ho_send_to_branch_process1($idbrand, $idproductcategory,$idbranch,$status){
        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,sender.branch_name as sender_name,pc.product_category_name,ps.delivery_status");
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
//                ->where('stp.idservice_stock=ss.id_service')->from('service_transfer_product stp')
                ->join('users u','u.id_users=ss.idsalesperson', 'left')
                ->join('branch sender','sender.id_branch=ss.idwarehouse', 'left');
        return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
    
    public function ajax_get_assigned_cases_to_excecutive($idbrand, $idproductcategory,$idbranch,$status,$iduser){
        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status");
//        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status,stp.*");
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
        if($iduser){
            $this->db->where('ss.idservice_excecutive', $iduser);
        }
        $this->db->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                ->where('ss.idbrand=br.id_brand')->from('brand br')
                ->where('ss.idbranch=b.id_branch')->from('branch b')
                ->where('ss.process_status=ps.id')->from('service_process_status ps')
//                ->where('stp.idservice_stock=ss.id_service')->from('service_transfer_product stp')
                ->join('users u','u.id_users=ss.idsalesperson', 'left');
        return $this->db->get('service_stock ss')->result();
//                die(print_r($this->db->last_query()));
    }
    
    public function ajax_get_assigned_cases_to_excecutive_on_exe($idbrand, $idproductcategory,$idbranch,$iduser){
        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status");
//        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name,ps.delivery_status,stp.*");
//        if($status){
//        }
        if($idbrand){
            $this->db->where('ss.idbrand', $idbrand);
        }
        if($idbranch){
            $this->db->where('ss.idbranch', $idbranch);
        }
        if($idproductcategory){
            $this->db->where('ss.idproductcategory', $idproductcategory);
        }
        if($iduser){
            $this->db->where('ss.idservice_excecutive', $iduser);
        }
//        $pr_status = array(14,15,16,11,9,12);
        $this->db->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                ->where('ss.idbrand=br.id_brand')->from('brand br')
                ->where('ss.idbranch=b.id_branch')->from('branch b')
                ->where('ss.process_status=ps.id')->from('service_process_status ps')
                ->where('ss.process_status != 13')
//                ->where('stp.idservice_stock=ss.id_service')->from('service_transfer_product stp')
                ->join('users u','u.id_users=ss.idsalesperson', 'left');
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
                        ->join('users u','u.id_users=ss.idsalesperson', 'left');
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
    
    public function get_variants_by_olderp_model($product_name){
         return $this->db->select('idvariant')->like('old_model_name', $product_name, 'both')->get('old_product_model_data')->row();
    }
    public function verify_imei_presence($imei){
         return $this->db->select('*')->like('imei_no', $imei, 'both')->get('imei_history')->result();
    }    
    public function update_sale_product_byidsaleproduct($idsale, $data) {
        $this->db->where('id_old_sale', $idsale)->update('olderp_sale_data', $data);
    }
    public function save_doa_inward($data) {
         $this->db->insert('doa_inward', $data);
         return $this->db->insert_id();
    }
    public function save_doa_reconciliation($data) {
         $this->db->insert('doa_reconciliation', $data);
        return $this->db->insert_id();
    }
    
     ///16-02-2021
    
    public function get_force_doa_stock_by_PBB($idbrand,$idproductcategory,$idbranch,$idbranchs) {              
        $this->db->select('srp.new_imei_against_doa,ss.*,ps.delivery_status,st.idbranch,st.temp_idbranch,st.imei_no,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, st.qty,g.godown_name');
        $this->db->from('stock st');         
        $this->db->join('branch b', 'st.idbranch=b.id_branch','left');    /// for current stock        
        if($idproductcategory){
            $this->db->where('st.idproductcategory', $idproductcategory);
        }
        if($idbrand){
             $this->db->where('st.idbrand', $idbrand);   
        }                
        $this->db->where('st.idgodown', 3);         
        if($idbranch){           
                $this->db->where('st.idbranch='.$idbranch);
        }else{
            if(count($idbranchs)>0){               
                   $this->db->where_in('st.idbranch',$idbranchs);
            }
        }
        $this->db->join('service_stock ss','`ss`.`id_service` = `st`.`idservice`','left');  
        $this->db->join('sales_return_product srp','`srp`.`imei_no` = `ss`.`imei` and `srp`.`sales_return_invid` = `ss`.`sales_return_invid`','left');  
        $this->db->where('ss.process_status=ps.id')->from('service_process_status ps');
        $this->db->where_in('st.doa_return_type',3);
        $this->db->where('g.active', 1); 
        $this->db->join('godown g', 'st.idgodown=g.id_godown');
        $this->db->join('model_variants mv', 'st.idvariant=mv.id_variant');        
        $this->db->join('product_category pc', 'st.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'st.idbrand=brd.id_brand');
        $this->db->where('b.active', 1);
        $this->db->order_by('st.idbrand,b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
//        die($this->db->last_query());
    } 
    
    public function get_sale_return_product_data_inv_imei($imeino,$sales_return_invid){
        return $this->db->select("*")->where('imei_no', $imeino)->where('sales_return_invid', $sales_return_invid)->get('sales_return_product')->row();         
    }
    public function update_stock_byimei($imei, $data) {
        $this->db->where('imei_no',$imei)->update('stock',$data);
    }
    public function get_service_row_byid($idservice) {
        return $this->db->where('id_service', $idservice)->get('service_stock')->row();
    }
    public function save_service_transfer($data) {
        $this->db->insert('service_transfer', $data);
        return $this->db->insert_id();
    }
    public function save_service_transfer_product($data) {
        return $this->db->insert('service_transfer_product', $data);
    }
    public function get_service_process_status() {
        return $this->db->order_by('sequence')->get('service_process_status')->result();
    }
    public function update_service_transfer($idservice_transfer, $data) {
        return $this->db->where('id_service_transfer', $idservice_transfer)->update('service_transfer', $data);
    }
    public function update_doa_inward($idservice, $data) {
        return $this->db->where('idservice', $idservice)->update('doa_inward', $data);
    }
    public function update_doa_reconciliation_byid($id_doa_stock, $data) {
        return $this->db->where('id_doa_stock', $id_doa_stock)->update('doa_reconciliation', $data);
    }
    public function get_sale_product_byidsaleproduct($idsaleproduct) {
        return $this->db->where('id_saleproduct', $idsaleproduct)->get('sale_product')->result();
    }
    public function get_sale_old_product_byidsaleproduct($idsaleproduct) {
        return $this->db->where('invoice_no', $idsaleproduct)->get('olderp_sale_data')->result();
    }
    public function doa_inward_reconciliation_data_byidservice($idservice) {
        return $this->db->select('doa_reconciliation.*,model_variants.full_name')
                        ->where('idservice',$idservice)
                        ->where('doa_reconciliation.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('doa_reconciliation')->row();
    }
    public function get_user_data_idbranch_byidrole($idbranch, $idrole) {
        return $this->db->where('iduserrole', $idrole)->where('idbranch', $idbranch)->get('users')->result();
    }
    public function get_service_counter_faulty_pending_stock() {
        $this->db->select("ss.*,mv.full_name,b.*,u.user_name,br.brand_name,pc.product_category_name")
                ->where('ss.counter_faulty_approval = 0')
                ->where('ss.counter_faulty = 1')
                ->where('ss.idvariant=mv.id_variant')->from('model_variants mv')
                ->where('ss.idproductcategory=pc.id_product_category')->from('product_category pc')
                ->where('ss.idbrand=br.id_brand')->from('brand br')
                ->where('ss.idbranch=b.id_branch')->from('branch b')
//                ->where('ss.process_status=ps.id')->from('service_process_status ps')
                ->join('users u','u.id_users=ss.idsalesperson', 'left');
        return $this->db->get('service_stock ss')->result();
    }
}
?>