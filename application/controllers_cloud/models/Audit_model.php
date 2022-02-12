<?php
class Audit_model extends CI_Model{
     public function get_godown_byid($idgodown){
        return $this->db->where('id_godown', $idgodown)->get('godown')->row();
    }
    public function get_stock_data($idcat, $idbrand, $idbranch, $idgodown){
         $brand_data= $this->db->where('active', 1)->get('brand')->result();
         $brands ='';
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands .= $bdata->id_brand. ',';
            }
        }else{
             $brands = $idbrand;
        }
        $brands = rtrim($brands, ',');
        $str = "Select * from stock where idbranch = $idbranch  and idproductcategory = $idcat and idbrand in($brands) and idgodown = $idgodown ";
//        die($str);
        return $this->db->query($str)->result();
    }
    public function get_intransit_stock_data($idcat, $idbrand, $idbranch, $idgodown){
         $brand_data= $this->db->where('active', 1)->get('brand')->result();
          $brands ='';
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands .= $bdata->id_brand. ',';
            }
        }else{
             $brands = $idbrand;
        }
        $brands = rtrim($brands, ',');
        
        $str = "Select * from stock where  temp_idbranch = $idbranch and idproductcategory = $idcat and idbrand in($brands) and idgodown = $idgodown ";
        return $this->db->query($str)->result();
    }
    public function get_transfer_stock_data($idcat, $idbrand, $idbranch, $idgodown){
         $brand_data= $this->db->where('active', 1)->get('brand')->result();
          $brands ='';
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands .= $bdata->id_brand. ',';
            }
        }else{
             $brands = $idbrand;
        }
        $brands = rtrim($brands, ',');
        $str = "Select * from stock where  transfer_from = $idbranch and idproductcategory = $idcat and idbrand in($brands) and idgodown = $idgodown ";
        return $this->db->query($str)->result();
    }

    public function ajax_check_branch_audit($idbranch, $idcat, $idbrand, $idgodown, $iduser, $d){
          $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('idbranch', $idbranch)
                        ->where('idproductcategory', $idcat)
                        ->where_in('idbrand', $brands)
                        ->where('idgodown', $idgodown)
                        ->where('audit.created_by', $iduser)
                        ->where('finish_date', $d)
                        ->where('idbrand = brand.id_brand')->from('brand')
                        ->get('audit')->result();
    }
    public function get_brand_by_id($idbrand){
        return $this->db->where('id_brand', $idbrand)->get('brand')->row();
    }
    public function get_branches_by_user($id) { //branch and warehouse for auditor
        return  $this->db->select('b.*, ub.id_user_has_branch')->where('b.active', 1)->where('ub.iduser', $id)->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')->get('branch b')->result();
    }   
    public function ajax_check_barcode_byidcat($barcode, $idcat, $idbrand){
        $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('imei_no', $barcode)
                        ->where('idproductcategory', $idcat)
                        ->where_in('idbrand', $brands)
                        ->where('idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('idbrand = brand.id_brand')->from('brand')
                        ->get('stock')->row();
        
        }
//    public function get_qty_stock_data($idbranch, $idcat, $idbrand){
//        return $this->db->where('idbranch', $idbranch)
//                        ->where('idcategory', $idcat)
//                        ->where('idbrand', $idbrand)
//                        ->where('idskutype', 4)
//                        ->where('idcategory = category.id_category')->from('category')
//                        ->where('idbrand = brand.id_brand')->from('brand')
//                        ->get('stock')->result();
//    }
    
    public function get_qty_modelvariant_data($idcat, $idbrand){
        return $this->db->where('idproductcategory', $idcat)
                        ->where('idbrand', $idbrand)
                        ->where('idsku_type', 4)
                        ->get('model_variants')->result();
    }
    
    
    public function ajax_get_stock_data_byid($id){  
        return $this->db->where('id_stock', $id)
                        ->where('idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('idbrand = brand.id_brand')->from('brand')
                        ->get('stock')->row();
    }
   
    public function save_audit_temp_data($data){
        return $this->db->insert('audit_temp',$data);
    }
    public function save_audit_data($data){
         $this->db->insert('audit',$data);
          return $this->db->insert_id();
    }
    public function get_audit_start_date_from_audit_temp($idbranch,$idcat,$idbrand,$iduser, $idgodown){
        $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('audit_temp.idbranch', $idbranch)
                        ->where('audit_temp.idproductcategory', $idcat)
                        ->where_in('audit_temp.idbrand', $brands)
                        ->where('audit_temp.created_by',$iduser )
                        ->where('audit_temp.idgodown',$idgodown )
                        ->get('audit_temp')->row();
    }

    public function get_audit_temp_data_byid($idbranch, $idcat, $idbrand, $iduser, $idgodown){
            $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        
        return $this->db->where('audit_temp.idbranch', $idbranch)
                        ->where('audit_temp.idproductcategory', $idcat)
                        ->where_in('audit_temp.idbrand', $brands)
                        ->where('audit_temp.created_by', $iduser)
                        ->where('audit_temp.idgodown', $idgodown)
                        ->where('audit_temp.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('audit_temp.idgodown = godown.id_godown')->from('godown')
                        ->where('audit_temp.idbrand = brand.id_brand')->from('brand')
                        ->get('audit_temp')->result();
    }
    public function ajax_get_qty_audit_temp_data_byid($idbranch, $idcat, $idbrand, $iduser){
        
          $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        
        return $this->db->where('audit_temp.idbranch', $idbranch)
                        ->where('audit_temp.idproductcategory', $idcat)
                        ->where_in('audit_temp.idbrand', $brands)
                        ->where('audit_temp.created_by',$iduser )
                        ->where('audit_temp.idskutype',4 )
                        ->group_by('idvariant')
                        ->get('audit_temp')->result();
    }
    //imei
    public function get_stock_missing_byid($barcodes, $idbranch, $idcat, $idbrand, $idgodown){
        $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where_in('stock.idbrand', $brands)
                        ->where('stock.idgodown', $idgodown)
                        ->where_not_in('stock.imei_no', $barcodes)
                        ->where('stock.idskutype != 4')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->get('stock')->result();
    }
    public function get_stock_missing($idbranch, $idcat, $idbrand, $idgodown){
        $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where_in('stock.idbrand', $brands)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idskutype != 4')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->get('stock')->result();
    }
    //idvariant missing for scan
//    public function get_stock_missing_byidvariant($barcodes, $idbranch, $idcat, $idbrand, $idgodown){
//        return $this->db->where('stock.idbranch', $idbranch)
//                        ->where('stock.idproductcategory', $idcat)
//                        ->where('stock.idbrand', $idbrand)
//                        ->where_not_in('stock.idvariant', $barcodes)
//                        ->where_not_in('stock.idgodown', $idgodown)
//                        ->where('stock.idskutype', 4)
//                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
//                         ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
//                        ->get('stock')->result();
//    }
     public function get_stock_missing_byidvariant($idvariant, $idbranch, $idcat, $idbrand, $idgodown){
          $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where_in('stock.idbrand', $brands)
                        ->where_in('stock.idgodown', $idgodown)
                        ->where_not_in('stock.idvariant', $idvariant)
                        ->where('stock.idskutype', 4)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                         ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->get('stock')->result();
    }
    
    //ajax scanned  qty model
    public function ajax_get_stockdata_byidvariant($idvariant, $idbranch, $idgodown){
        return $this->db->where_in('stock.idvariant', $idvariant)
                        ->where('stock.idskutype', 4)
                        ->where('stock.idbranch', $idbranch)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->get('stock')->row();
    }
//    public function ajax_get_stock_data_byidvariant($idvariant, $idgodown){
//        return $this->db->where_in('stock.idvariant', $idvariant)
//                        ->where('stock.idskutype', 4)
//                        ->where('stock.idgodown', $idgodown)
//                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
//                        ->where('model_variants.idcategory = category.id_category')->from('category')
//                        ->where('stock.idbrand = brand.id_brand')->from('brand')
//                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
//                        ->get('stock')->result();
//    }
    public function ajax_get_stock_data_byidvariant($idvariant, $idgodown, $idbranch){
        return $this->db->where_in('stock.idvariant', $idvariant)
                        ->where('stock.idskutype', 4)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idbranch', $idbranch)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('model_variants.idcategory = category.id_category')->from('category')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->get('stock')->result();
    }
    
     public function get_audit_missing_data($idbranch, $idcat, $idbrand, $iduser, $idgodown, $datetime){
//        die($idcat);
        $string = $datetime;
        $string = str_replace("%20"," ",$string);
//        die($string);
        return $this->db->where('audit.idbranch', $idbranch)
                        ->where('audit.idproductcategory', $idcat)
                        ->where('audit.idbrand', $idbrand)
                        ->where('audit.created_by', $iduser)
                        ->where('audit.idgodown', $idgodown)
                        ->where('audit.status', 'missing')
                        ->where('audit.entry_time like', $string)
                        ->where('audit.idbranch = branch.id_branch')->from('branch')
                        ->where('audit.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('audit.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('audit.idbrand = brand.id_brand')->from('brand')
                        ->where('audit.idgodown = godown.id_godown')->from('godown')
                        ->get('audit')->result();
    }
    public function get_missing_stock_details_byimei($missing_imei_arr){
        return $this->db->where_in('imei_no', $missing_imei_arr)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->get('stock')->result();
    }
    
//    public function get_model_varient_price_byidstock($idvariant){
//        return $this->db->where_in('stock.idvariant', $idvariant)
//                        ->where('stock.idskutype', 4)
//                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
//                        ->where('idbrand = brand.id_brand')->from('brand')
//                        ->where('stock.idcategory = category.id_category')->from('category')
//                        ->get('stock')->result();
//    }
    
//     public function ajax_get_stock_data_byidvariant($id){
//        return $this->db->where('idvariant', $id)
//                        ->where('idcategory = category.id_category')->from('category')
//                        ->where('idbrand = brand.id_brand')->from('brand')
//                        ->get('stock')->row();
//    }
    
    
    public function get_missing_customer_data(){
        return $this->db->where('id_customer',0)->get('customer')->row();
    }
    
    public function save_missing_sale_data($sale_missing){
         $this->db->insert('sale', $sale_missing);
        return $this->db->insert_id();
    }
    public function save_sale_product_data($sale_product_missing){
         return $this->db->insert('sale_product', $sale_product_missing);
    }
    public function save_sale_payment_data($sale_payment){
         return $this->db->insert('sale_payment', $sale_payment);
    }

   public function delete_audit_temp_data($idbranch, $idcat, $idbrand, $iduser, $idgodown){
         $brand_data= $this->db->where('active', 1)->get('brand')->result();
        if($idbrand == 'all'){
            foreach ($brand_data as $bdata) {
                $brands[] = $bdata->id_brand;
            }
        }else{
             $brands[] = $idbrand;
        }
        return $this->db->where('audit_temp.idbranch', $idbranch)
                        ->where('audit_temp.idproductcategory', $idcat)
                        ->where_in('audit_temp.idbrand', $brands)
                        ->where('audit_temp.created_by',$iduser )
                        ->where('audit_temp.idgodown',$idgodown )
                        ->delete('audit_temp');
    }
    public function get_audit_data_byfilter($from, $to, $idbranch, $role, $idcat, $idbrand){
        $id = $_SESSION['id_users'];
                  
        $branchid = '';
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
            foreach ($branches as $branch) {
                $branchid .= $branch->id_branch. ',';
            }
        }else{
            $branchid = $idbranch;
        }
        
        $branchid = rtrim($branchid, ',');
//        die($branchid);
        
        if($from != '' && $to != '' && $idcat == 0 && $idbrand == 0){

            $str = 'select audit.finish_date, audit.idgodown, audit.entry_time, audit.idproductcategory, audit.idbranch, audit.role, audit.idbrand, audit.entry_time, audit.audit_start,  
                product_category.product_category_name as product_category_name, brand.brand_name as brand_name, godown.godown_name as godown_name, branch.branch_name as branch_name,
                        sum(CASE audit.status WHEN "matched" THEN audit.qty ELSE 0 END) AS matched_count, 
                        sum(CASE audit.status WHEN "unmatched" THEN audit.qty ELSE 0 END) AS unmatched_count, 
                        sum(CASE audit.status WHEN "missing" THEN audit.qty ELSE 0 END) AS missing_count

                        FROM audit, product_category, brand, branch, godown where audit.role ="'.$role.'" and audit.idbranch in('.$branchid.') AND audit.finish_date BETWEEN "'.$from.'" and "'.$to.'" and 
                        audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch and godown.id_godown = audit.idgodown
                        group by audit.idgodown, audit.entry_time, audit.idbranch, audit.idproductcategory, audit.idbrand order by audit.finish_date desc';
             
        }
        elseif($from != '' && $to != '' && $idcat != 0 && $idbrand != 0){
            $str = 'select audit.finish_date, audit.idgodown, audit.entry_time, audit.idproductcategory, audit.idbranch, audit.role, audit.idbrand, audit.entry_time, audit.audit_start, 
                product_category.product_category_name as product_category_name, brand.brand_name as brand_name, godown.godown_name as godown_name, branch.branch_name as branch_name,
                        sum(CASE audit.status WHEN "matched" THEN audit.qty ELSE 0 END) AS matched_count, 
                        sum(CASE audit.status WHEN "unmatched" THEN audit.qty ELSE 0 END) AS unmatched_count, 
                        sum(CASE audit.status WHEN "missing" THEN audit.qty ELSE 0 END) AS missing_count

                        FROM audit, product_category, brand, branch, godown where audit.role ="'.$role.'" and audit.idbranch in('.$branchid.') AND audit.finish_date BETWEEN "'.$from.'" and "'.$to.'" and 
                        audit.idproductcategory ="'.$idcat.'" and audit.idbrand="'.$idbrand.'" and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch and godown.id_godown = audit.idgodown
                        group by audit.idgodown, audit.entry_time, audit.idbranch, audit.idproductcategory, audit.idbrand order by audit.finish_date desc';
        }
        elseif($from != '' && $to != '' && $idcat == 0 && $idbrand != 0){
            $str = 'select audit.finish_date, audit.idgodown, audit.entry_time, audit.idproductcategory, audit.idbranch, audit.role, audit.idbrand, audit.entry_time, audit.audit_start,  
                product_category.product_category_name as product_category_name, brand.brand_name as brand_name, godown.godown_name as godown_name, branch.branch_name as branch_name,
                        sum(CASE audit.status WHEN "matched" THEN audit.qty ELSE 0 END) AS matched_count, 
                        sum(CASE audit.status WHEN "unmatched" THEN audit.qty ELSE 0 END) AS unmatched_count, 
                        sum(CASE audit.status WHEN "missing" THEN audit.qty ELSE 0 END) AS missing_count

                        FROM audit, product_category, brand, branch, godown where audit.role ="'.$role.'" and audit.idbranch in('.$branchid.') AND audit.finish_date BETWEEN "'.$from.'" and "'.$to.'" and 
                        audit.idbrand="'.$idbrand.'" and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch and godown.id_godown = audit.idgodown
                        group by audit.idgodown, audit.entry_time, audit.idbranch, audit.idproductcategory, audit.idbrand order by audit.finish_date desc';
            
        }
        elseif($from != '' && $to != '' && $idcat != 0 && $idbrand == 0){
            $str = 'select audit.finish_date, audit.idgodown, audit.entry_time, audit.idproductcategory, audit.idbranch, audit.role, audit.idbrand, audit.entry_time, audit.audit_start,   
                product_category.product_category_name as product_category_name, brand.brand_name as brand_name, godown.godown_name as godown_name, branch.branch_name as branch_name,
                        sum(CASE audit.status WHEN "matched" THEN audit.qty ELSE 0 END) AS matched_count, 
                        sum(CASE audit.status WHEN "unmatched" THEN audit.qty ELSE 0 END) AS unmatched_count, 
                        sum(CASE audit.status WHEN "missing" THEN audit.qty ELSE 0 END) AS missing_count

                        FROM audit, product_category, brand, branch, godown where audit.role ="'.$role.'" and audit.idbranch in('.$branchid.') AND audit.finish_date BETWEEN "'.$from.'" and "'.$to.'" and 
                        audit.idproductcategory ="'.$idcat.'" and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch and godown.id_godown = audit.idgodown
                        group by audit.idgodown, audit.entry_time, audit.idbranch, audit.idproductcategory, audit.idbrand order by audit.finish_date desc';
            
        }
//        die($str);
        return $this->db->query($str)->result();
    }
    public function get_audit_report_deatils($idcat, $idbrand, $idbranch, $from, $role, $entry_time){
        return $this->db->where('audit.idbranch', $idbranch)
                        ->where('audit.idproductcategory', $idcat)
                        ->where('audit.idbrand', $idbrand)
                        ->where('audit.role', $role)
                        ->where('audit.finish_date', $from)
                        ->where('audit.entry_time', $entry_time)
                        ->where('idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('idbrand = brand.id_brand')->from('brand')
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->get('audit')->result();
    }
    public function get_active_branch_data(){
        return $this->db->where('active', 1)->get('branch')->result();
    }
    public function get_active_branch_data_with_zone(){
        return $this->db->where('branch.active', 1)
                        ->where('branch.idzone = zone.id_zone')->from('zone')
                        ->order_by('branch.idzone,branch.branch_name')
                        ->get('branch')->result();
    }
    public function get_user_session_branch_data(){
        return $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
    }
    public function get_product_category_by_id($id){
        return $this->db->where('active',1)->where('id_product_category',$id)->get('product_category')->result();
    }
    
     public function ajax_get_audit_analysis_data($from, $to, $role, $idcat, $idgodown, $allgodown, $allcats){
        $id = $_SESSION['id_users'];
        $branchid = '';
       
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
            $branchid .= $brn->id_branch. ',';

        } 
        $branchid = rtrim($branchid, ',');
        
        if($idcat == 0){
            $allpcat = $allcats;
        }else{
            $allpcat = $idcat;
        }
        
        if($idgodown == 0){
            $allgodowns = $allgodown;
        }else{
            $allgodowns = $idgodown;
        }
        
        $str1 ='SELECT *, count(distinct audit.entry_time) as cnt, product_category.product_category_name as cat_name, brand.brand_name as brand_name, godown.godown_name as godown_name FROM `audit`, product_category, brand, godown WHERE product_category.id_product_category = audit.idproductcategory AND brand.id_brand = audit.idbrand AND godown.id_godown = audit.idgodown AND `idbranch` IN('.$branchid.') AND audit.idgodown IN('.$allgodowns.') AND `finish_date` between "'.$from.'" AND "'.$to.'" AND role = "'.$role.'" AND audit.idproductcategory IN('.$allpcat.')  group by `idbranch`, `idproductcategory`, `idbrand`, idgodown ';            

        return $this->db->query($str1)->result();
        
    }
    
    //19 mrach Live Backup
//    public function ajax_get_audit_analysis_data($from, $to, $role, $idcat){
//        $id = $_SESSION['id_users'];
//        $branchid = '';
//       
//        if($this->session->userdata('level') == 1){  //admin all branch
//           $branches = $this->db->where('active', 1)->get('branch')->result();
//        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
//            $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
//        }elseif($this->session->userdata('level') == 3){  //Multiple branches
//           $branches = $this->db->select('b.*')
//                                ->where('b.active', 1)
//                                ->where(' ub.iduser', $id)
//                                ->where('b.is_warehouse', 0)
//                                ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
//                                ->get('branch b')->result();
//        }
//        foreach ($branches as $brn){
//            $branchid .= $brn->id_branch. ',';
//
//        } 
//        $branchid = rtrim($branchid, ',');
//        
//        if($idcat == 0){
//            $str1 ='SELECT *, count(distinct audit.entry_time) as cnt, product_category.product_category_name as cat_name, brand.brand_name as brand_name FROM `audit`, product_category, brand WHERE product_category.id_product_category = audit.idproductcategory AND brand.id_brand = audit.idbrand AND `idbranch` IN('.$branchid.') AND `finish_date` between "'.$from.'" AND "'.$to.'" AND role = "'.$role.'"  group by `idbranch`, `idproductcategory`, `idbrand` ';
//        }else{
//            $str1 ='SELECT *, count(distinct audit.entry_time) as cnt, product_category.product_category_name as cat_name, brand.brand_name as brand_name FROM `audit`, product_category, brand WHERE product_category.id_product_category = audit.idproductcategory AND brand.id_brand = audit.idbrand AND `idbranch` IN('.$branchid.') AND `finish_date` between "'.$from.'" AND "'.$to.'" AND role = "'.$role.'" AND audit.idproductcategory = "'.$idcat.'"  group by `idbranch`, `idproductcategory`, `idbrand` ';            
//        }
//        return $this->db->query($str1)->result();
//        
//    }
    
    public function get_audit_data_bybrand($idbranch, $idbrand, $rolename, $from, $status){
        if($idbrand == '0'){
            if($status == '0'){
                $str = "SELECT * FROM `audit`, brand, branch, product_category WHERE `finish_date` = '".$from."' AND `idbranch` = $idbranch AND `role` = '".$rolename."' and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch ";
            }
            else{
                $str = "SELECT * FROM `audit`, brand, branch, product_category WHERE `finish_date` = '".$from."' AND `idbranch` = $idbranch AND `role` = '".$rolename."' and audit.status = '$status' and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch ";
            }
        }else{
            if($status == '0'){
                $str = "SELECT * FROM `audit`, brand, branch, product_category WHERE `finish_date` = '".$from."' AND `idbrand` = $idbrand AND `idbranch` = $idbranch AND `role` = '".$rolename."' and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch ";
            }else{
                $str = "SELECT * FROM `audit`, brand, branch, product_category WHERE `finish_date` = '".$from."' AND `idbrand` = $idbrand AND `idbranch` = $idbranch AND `role` = '".$rolename."' and audit.status = '$status' and audit.idproductcategory = product_category.id_product_category and audit.idbrand = brand.id_brand and audit.idbranch = branch.id_branch ";
            }
        }
        return $this->db->query($str)->result();
       
    }
    
   //New Updates
    
    public function ajax_get_missing_stockdata_byidvariant($idvariant, $idbranch, $idcat, $idbrand, $idgodown){
         return $this->db->where_in('stock.idvariant', $idvariant)
                        ->where('stock.idskutype', 4)
                        ->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where('stock.idbrand', $idbrand)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->get('stock')->row();
    }
    public function get_audit_missing_data_from_stock($idbranch, $idcat, $idbrand, $idgodown, $datetime){
        
//        $str = "SELECT * FROM `stock`,branch,product_category,model_variants,brand,godown WHERE stock.`idgodown` = 5 AND stock.`audit_date` = '2021-01-29' AND stock.`idproductcategory` = 3 AND stock.`idbrand` = 62 AND stock.`idbranch` = 63 and stock.idbranch = branch.id_branch and stock.idproductcategory = product_category.id_product_category and stock.idvariant = model_variants.id_variant and stock.idbrand = brand.id_brand and stock.idgodown = godown.id_godown";
//        return $this->db->query($str)->result();
//        die($datetime);
        return $this->db->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where('stock.idbrand', $idbrand)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.audit_date', $datetime)
                        ->where('stock.idbranch = branch.id_branch')->from('branch')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
     public function get_audit_missing_data_from_stock_byfilter($idbranch, $idcat, $idbrand, $idgodown, $from, $to){
        return $this->db->where('stock.idbranch', $idbranch)
                        ->where('stock.idproductcategory', $idcat)
                        ->where('stock.idbrand', $idbrand)
                        ->where('stock.idgodown', 5)
                        ->where('stock.audit_date >=', $from)
                        ->where('stock.audit_date <=', $to)
                        ->where('stock.idbranch = branch.id_branch')->from('branch')
                        ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idbrand = brand.id_brand')->from('brand')
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
    public function update_stock_data($stock_data, $idstock){
        return $this->db->where('id_stock', $idstock)->update('stock', $stock_data);
    }
    public function save_stock_missing_data($data){
        return $this->db->insert('stock', $data);
    }
    public function save_imei_history($imei_history){
        return $this->db->insert('imei_history', $imei_history);
    }
    public function delete_imei_from_stock($idstock){
        return $this->db->where('id_stock', $idstock)->delete('stock');
    }
    
}
?>