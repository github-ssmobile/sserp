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
        return $this->db->select('stock.idvariant,stock.product_name,stock.imei_no,stock.qty,branch.branch_name,product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp,godown.godown_name')
                        ->where('stock.idbranch = branch.id_branch')->from('branch')
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
    public function get_daily_transit_stock_data() {
        return $this->db->select('stock.idvariant,stock.product_name,stock.imei_no,stock.qty, brs.branch_name as sender, brr.branch_name as receiver, product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp,godown.godown_name')
                        ->where('stock.idbranch = 0')
                        ->where('stock.transfer_from = brs.id_branch')->from('branch brs')
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.temp_idbranch = brr.id_branch')->from('branch brr')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
    public function get_daily_stock_data_manual($report_date) { 
        return $this->db->select('stock.idvariant,mv.full_name product_name,stock.imei_no,stock.qty,branch.branch_name,product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp,godown.godown_name')
        ->where('stock.date ="'. $report_date.'"')
        ->where('stock.idbranch = branch.id_branch')->from('branch')
        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
        ->where('stock.idbrand = brand.id_brand')->from('brand')
        ->where('stock.idgodown = godown.id_godown')->from('godown') 
        ->get('copy_daily_stock stock')->result(); 
    }
    public function get_daily_transit_stock_data_manual($report_date) {
        return $this->db->select('stock.idvariant,mv.full_name product_name,stock.imei_no,stock.qty, brs.branch_name as sender, brr.branch_name as receiver, product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp,godown.godown_name')
        ->where('stock.idbranch = 0')
        ->where('stock.date ="'. $report_date.'"')
        ->where('stock.transfer_from = brs.id_branch')->from('branch brs')
        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
        ->where('stock.temp_idbranch = brr.id_branch')->from('branch brr')
        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
        ->where('stock.idbrand = brand.id_brand')->from('brand')
        ->where('stock.idgodown = godown.id_godown')->from('godown')
       
        ->get('copy_daily_stock stock')->result();
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
                                    ->where('ub.iduser', $id)
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
   public function save_import_invoice($data) {
        
        // print_r($data);die;
        extract($data);
        $originalDate = $invoice_date;
        $invoicedate = date("d-m-Y", strtotime($originalDate));
        $sqlDate = date("Y-m-d", strtotime($originalDate));
        
       
        
        if($imei_1_no != ""){
            $pr_imei_no1 = $this->db->select('imei_1_no')
               ->where('imei_1_no', $imei_1_no)
               ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no1)){
            $pr_imei_no2 = $this->db->select('imei_2_no')
                          ->where('imei_2_no', $imei_1_no)
                          ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no2)){
                $pr_serial_no3 = $this->db->select('serial_no')
                          ->where('serial_no', $imei_1_no)
                          ->get('olderp_sale_data')->row();
                if(empty($pr_serial_no3)){
                    $imei1_pr = '0';
                }else{
                    $imei1_pr = '1';
                }
            }else{
                $imei1_pr = '1';
            }
            }else{
                $imei1_pr = '1';
            }   
        }elseif($imei_2_no != ""){
            $pr_imei_no1 = $this->db->select('imei_1_no')
               ->where('imei_1_no', $imei_2_no)
               ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no1)){
            $pr_imei_no2 = $this->db->select('imei_2_no')
                          ->where('imei_2_no', $imei_2_no)
                          ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no2)){
                $pr_serial_no3 = $this->db->select('serial_no')
                          ->where('serial_no', $imei_2_no)
                          ->get('olderp_sale_data')->row();
                if(empty($pr_serial_no3)){
                    $imei1_pr = '0';
                }else{
                    $imei1_pr = '1';
                }
            }else{
                $imei1_pr = '1';
            }
            }else{
                $imei1_pr = '1';
            }  
        }elseif($serial_no != ""){
            $pr_imei_no1 = $this->db->select('imei_1_no')
               ->where('imei_1_no', $serial_no)
               ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no1)){
            $pr_imei_no2 = $this->db->select('imei_2_no')
                          ->where('imei_2_no', $serial_no)
                          ->get('olderp_sale_data')->row();
            if(empty($pr_imei_no2)){
                $pr_serial_no3 = $this->db->select('serial_no')
                          ->where('serial_no', $serial_no)
                          ->get('olderp_sale_data')->row();
                if(empty($pr_serial_no3)){
                    $imei1_pr = '0';
                }else{
                    $imei1_pr = '1';
                }
            }else{
                $imei1_pr = '1';
            }
            }else{
                $imei1_pr = '1';
            }  
        }
        //print_r($imei1_pr);die;
        if($imei1_pr == 0){
        $this->db->trans_begin();
        $this->db->select_max('id_old_sale');
        $result = $this->db->get('olderp_sale_data')->row();  
        $id_oldsale = $result->id_old_sale + 1;
        
          $data_invoice = array(
            'id_old_sale'=>$id_oldsale,  
            'invoice_no' =>$invoice_no,
            'invoice_type'=>'Sales',
            'date'=>$invoicedate,
            'invoice_date'=>$sqlDate,
            'billing_type'=>'Manual Billing',
            'zone'=>$zone,
            'promoter_name'=>$promoter_name,
            'customer_name'=>$customer_name,
            'customer_mobile'=>$customer_mobile,
            'customer_gst_no'=>$customer_gst_no,
            'idbranch'=>$branch,
            'store_name'=>$branch_name,
            'city'=>$city,
            'pincode'=>$pincode,
            'route'=>$route,
            'category'=>$category,
            'sub_category'=>$sub_category,
            'brand'=>$brand,
            'product_name'=>$product_name,
            'product_code'=>$product_code,
            'product_id'=>$product_id,
            'hsn_code'=>$hsn_code,  
            'imei_1_no'=>$imei_1_no,
            'imei_2_no'=>$imei_2_no,
            'serial_no'=>$serial_no,  
            'gst_rate'=>$gst_rate,
            'base_price'=>$base_price,
            'igst'=>$igst,
            'sgst'=>$sgst,
            'cgst'=>$cgst,
            'total_amount_per_qty'=>$total_amount_per_qty,
            'hidden_discount'=>$hidden_discount,
            'settlement_amount'=>$settlement_amount,
            'cash_amount'=>$cash_amount,
            'manager_price'=>$manager_price,  
            'salesman_price'=>$salesman_price,
            'customer_price'=>$customer_price, 
        );
          
         $this->db->insert('olderp_sale_data',$data_invoice);
         $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Import Invoice is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Import Invoice Successfully');
        }
         }else{
         $this->session->set_flashdata('save_data', 'IMEI IS ALREADY PRESENT');
         }
                  
    }
     public function save_import_invoice_edit() {
        $edit_import = $_POST;
//        echo ""<pre>"";
//        print_r($edit_import);die;
        $cnt = count($edit_import['invoice_id']);
        $this->db->trans_begin();
        for($i=0;$i<$cnt;$i++){
            $invoice_date = $edit_import['invoice_date'][$i];
           // $originalDate = $invoice_date;
            $invoicedate = date("d-m-Y", strtotime($invoice_date));
            $sqlDate = date("Y-m-d", strtotime($invoice_date));
            
            $invoice_id = $edit_import['invoice_id'][$i];
            
            $imei_1_no = $edit_import['imei_1_no'][$i];
            $imei_2_no = $edit_import['imei_2_no'][$i];
            $serial_no = $edit_import['serial_no'][$i];
            
            $str1 = "UPDATE `olderp_sale_data` SET `invoice_date`='$sqlDate' WHERE `invoice_no`='$invoice_id'";
            if($imei_1_no != ""){
              $str1.= " AND `imei_1_no` = '$imei_1_no'";  
            }
            elseif($imei_2_no != ""){
              $str1.= " AND `imei_2_no` = '$imei_2_no'";  
            }
            elseif($serial_no != ""){
              $str1.= " AND `serial_no` = '$serial_no'";  
            }
            
            $this->db->query($str1);
            //$str = $this->db->last_query();
            //echo $str1;die;


        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashsave_import_invoicedata('save_data', 'Import Invoice is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Import Invoice Update Successfully');
        }
        return redirect('Old_erp/import_invoice_edit');    
    }
    public function get_import_invoice_data() {
       // print_r($_POST);die;
        $inv_no = $_POST['imp_invoice'];
               
        $imp_data = $this->db->where('invoice_no', $inv_no)
                             ->get('olderp_sale_data')->result();
        return $imp_data;
    }
     public function get_daily_transit_stock_data_byidproductcategory($idproductcategory) {
        return $this->db->select('stock.idvariant,stock.product_name,SUM(stock.qty) as qty, brr.branch_name, product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp')
                        ->where('stock.idbranch = 0')
                        ->where('stock.idproductcategory', $idproductcategory)
                        ->where('stock.idskutype != 4')                        
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.temp_idbranch = brr.id_branch')->from('branch brr')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->group_by('stock.temp_idbranch,stock.idvariant')
                        ->get('stock')->result();
    }
    public function get_daily_stock_data_byidproductcategory($idproductcategory) {
        return $this->db->select('stock.idvariant,stock.product_name,SUM(stock.qty) as qty, brr.branch_name, product_category.product_category_name,brand.brand_name,mv.mop,mv.landing,mv.mrp')
                        ->where('stock.temp_idbranch = 0')
                        ->where('stock.idproductcategory', $idproductcategory)
                        ->where('stock.idskutype != 4')                                      
                        ->where('stock.idvariant = mv.id_variant')->from('model_variants mv')
                        ->where('stock.idbranch = brr.id_branch')->from('branch brr')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->group_by('stock.idbranch,stock.idvariant')
                        ->get('stock')->result();
    }


}
