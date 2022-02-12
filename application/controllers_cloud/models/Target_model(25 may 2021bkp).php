<?php
class Target_model extends CI_Model{
    
    public function ajax_get_branch_target_data_byidzone($idzone, $monthyear){
        $dd = $monthyear.'-01';
//        $from = date("Y-m-d", strtotime ( '-1 month' , strtotime ( $dd ) )) ;
//        $to = date("Y-m-t", strtotime ( '-1 month' , strtotime ( $dd ) )) ;
        $from = $dd ;
        $to = date("Y-m-t", strtotime($monthyear)) ;
        
        $allow_target = array(0,2);

        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,pc.product_category_name,pc.id_product_category, sp.sale_qty,sp.total,sp.landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
        $this->db->where('b.idzone',$idzone);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch, s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' GROUP BY s.idbranch, s.idproductcategory) sp", 'sp.idbranch = b.id_branch and sp.idproductcategory = pc.id_product_category', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch, ss.idproductcategory from sale_product ss WHERE ss.date between '$from' and '$to' and ss.idcategory in(1,32) GROUP BY ss.idbranch, ss.idproductcategory) smart_sp", 'smart_sp.idbranch = b.id_branch and smart_sp.idproductcategory = pc.id_product_category', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->where('b.idzone = zone.id_zone')->from('zone')   ;
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->where_in('pc.allow_target',$allow_target);
        $this->db->where('pc.enable_for_target',1)->from('product_category pc');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }   
    public function get_cluster_head_data(){
        return $this->db->select('users.user_name as clust_name,user_has_branch.idbranch as clustbranch')
                        ->where('users.iduserrole',26)
                        ->where('user_has_branch.iduser = users.id_users')->from('users')
                        ->get('user_has_branch')->result();
    }
    public function save_branch_target($data){
        return $this->db->insert('branch_target', $data);
    }
    public function update_branch_target($data, $idbranch_target){
        return $this->db->where('id_branch_target', $idbranch_target)->update('branch_target',$data);
    }

        public function ajax_get_current_month_branch_target_data_byidzone($idzone, $monthyear){
        return $this->db->select('branch_target.*')
                        ->where('branch_target.month_year', $monthyear)
                        ->where('branch.idzone',$idzone)
                        ->where('branch_target.idbranch = branch.id_branch')->from('branch')
                        ->get('branch_target')->result();
    }
    
    public function ajax_get_branch_target_data_byidbranch($idbranch, $monthyear, $allbranches){
        if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        return $this->db->select('branch_target.*, branch.branch_name,product_category.product_category_name')
                        ->where_in('branch_target.idbranch',$branches)
                        ->where('branch_target.month_year',$monthyear)
                        ->where('branch_target.idbranch = branch.id_branch')->from('branch')
                        ->where('branch_target.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->order_by('branch_target.idbranch, branch_target.idproductcategory')
                        ->get('branch_target')->result();
    }
    public function ajax_get_promotor_data_byidbranch($idbranch){
        return $this->db->select('users.id_users, users.user_name')
                        ->where('users.idbranch', $idbranch)
                        ->where('users.iduserrole', 17)
                        ->where('users.active', 1)
                        ->get('users')->result();
//        return $this->db->select('Distinct(users.id_users), users.user_name, brand.id_brand,brand.brand_name')
//                        ->where('users.idbranch', $idbranch)
//                        ->where('users.iduserrole', 17)
//                        ->where('user_has_brand.idbrand =  brand.id_brand')->from('brand')
//                        ->where('users.id_users =  user_has_brand.iduser')->from('user_has_brand')
//                        ->get('users')->result();
    }
    public function get_brand_data_byidpromotor($id_users){
        return $this->db->select('brand.id_brand,brand.brand_name')
                        ->where('user_has_brand.iduser', $id_users)
                        ->where('user_has_brand.idbrand =  brand.id_brand')->from('brand')
                        ->get('user_has_brand')->row();
    }

        public function get_product_category_data(){
            $allow = array(1,2);
        return $this->db->where('enable_for_target',1)
                        ->where_in('allow_target',$allow)
                        ->get('product_category')->result();
    }
    public function save_promotor_target_data($data){
        return $this->db->insert('promotor_target_setup', $data);
    }
    
    public function ajax_get_promotor_target_data_byid($idbranch, $monthyear){
        return $this->db->select('users.user_name,brand.brand_name,promotor_target_setup.*')
                        ->where('promotor_target_setup.month_year',$monthyear)
                        ->where('promotor_target_setup.idbranch',$idbranch)
                        ->where('promotor_target_setup.idpromotor = users.id_users')->from('users')
                        ->where('promotor_target_setup.idbrand = brand.id_brand')->from('brand')
                        ->get('promotor_target_setup')->result();
    }
    
    public function delete_promotor_target_data_byid($idbranch, $monthyear){
        return $this->db->where('promotor_target_setup.month_year',$monthyear)
                        ->where('promotor_target_setup.idbranch',$idbranch)
                        ->delete('promotor_target_setup');
    }
    public function delete_branch_target_data_byid($idbranch, $monthyear){
//        die(print_r($idbranch));
        return $this->db->where('branch_target.month_year',$monthyear)
                        ->where_in('branch_target.idbranch',$idbranch)
                        ->delete('branch_target');
    }
    
    
     // Achivement Report
    public function ajax_get_mtd_achivement_byidbranch($from,$to,$idpcat,$allpcats,$idbranch,$allbranches){
         $c_from = $from; 
         $c_to = $to; 
//        $c_from = date('Y-m-01'); 
//        $c_to = date('Y-m-t'); 
        
        $month_year = date('Y-m', strtotime($from));
        
        if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->order_by('zone.id_zone','ASC');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    
    public function ajax_get_mtd_achivement_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzone){
//        $c_from = date('Y-m-01'); 
//        $c_to = date('Y-m-t'); 
          $c_from = $from; 
         $c_to = $to; 
        $month_year = date('Y-m', strtotime($from));
        
        if($idzone == 0 ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        if($idzone == 'all'){
            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
            $this->db->where_in('z.id_zone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year'  and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
            $this->db->order_by('z.id_zone','ASC');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }else{
            
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year'  and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
     public function ajax_get_drr_achivement_byidbranch($from,$idpcat,$allpcats,$idbranch,$allbranches) {
        
        $c_from = date('Y-m-01',strtotime($from));
        $c_to = $from;
        
        $month_year = date('Y-m',strtotime($from));
//        $month_year = '2021-04';
        
        if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing, csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->order_by('zone.id_zone','ASC');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    
    public function ajax_get_drr_achivement_byidzone($from,$idpcat,$allpcats,$idzone,$allzone){
        $c_from = date('Y-m-01',strtotime($from));
        $c_to = $from;
        
        $month_year = date('Y-m',strtotime($from));
//        $month_year = '2021-04';
        
        if($idzone == 0 ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        if($idzone == 'all'){
            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,  csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing');
            $this->db->where_in('z.id_zone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch,brr.idzone from sale_product cs, branch brr WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) and cs.idbranch = brr.id_branch GROUP BY brr.idzone ) csp", 'csp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date = '$from' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch,brr.idzone from sale_product css,branch brr WHERE css.date >= '$c_from' and css.date < '$from'  and css.idcategory in(1,32) and css.idbranch = brr.id_branch GROUP BY brr.idzone ) csmart_sp", 'csmart_sp.idzone = z.id_zone', 'left');
            $this->db->order_by('z.id_zone','ASC');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }else{
            
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,   csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
    
    //30April backup
    
    
    
//    public function ajax_get_drr_achivement_byidbranch($from,$idpcat,$allpcats,$idbranch,$allbranches) {
//        
//        $c_from = date('Y-m-01',strtotime($from));
//        $c_to = $from;
//        
//        $month_year = date('Y-m',strtotime($from));
////        $month_year = '2021-04';
//        
//        if($idbranch == 0 ){
//            $branches = explode(',',$allbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
//        
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
//        $this->db->where_in('b.id_branch',$branches);
//        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
//        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
//        $this->db->order_by('zone.id_zone','ASC');
//        $this->db->where('b.idzone = zone.id_zone')->from('zone');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
//        $this->db->from('branch b');
//        $query = $this->db->get();  
//        return $query->result();
//        
//    }
//    
//    public function ajax_get_drr_achivement_byidzone($from,$idpcat,$allpcats,$idzone,$allzone){
//        $c_from = date('Y-m-01',strtotime($from));
//        $c_to = $from;
//        
//        $month_year = date('Y-m',strtotime($from));
////        $month_year = '2021-04';
//        
//        if($idzone == 0 ){
//            $zones = explode(',',$allzone);
//        }else{
//            $zones[] = $idzone;
//        }
//        
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        if($idzone == 'all'){
//            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
//            $this->db->where_in('z.id_zone',$zones);
//            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date = '$from' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
//            $this->db->order_by('z.id_zone','ASC');
//            $this->db->from('zone z');
//            $query = $this->db->get();  
//            return $query->result();
//        }else{
//            
//            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
//            $this->db->where_in('b.idzone',$zones);
//            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
//            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
//            $this->db->order_by('zone.id_zone','ASC');
//            $this->db->where('b.idzone = zone.id_zone')->from('zone');
//            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
//            $this->db->from('branch b');
//            $query = $this->db->get();  
//            return $query->result();
//        }
//    }
    
     public function get_promotor_sale_report_byidbranch($from, $to, $idpcat, $allpcats, $idbranch, $allbranches){
        $month_year = date('Y-m',strtotime($from));
        
        if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idpcat == 0 ){
            $pcatss =  $allpcats;
             $pcats = explode(',',$allpcats);
        }else{
            $pcats[] = $idpcat;
            $pcatss = $idpcat;
        }
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
         $query = $this->db->get();  
        return $query->result();

    }
    
   public function get_promotor_sale_report_byidzone($from, $to, $idpcat, $allpcats, $idzone, $allzone){
        $month_year = date('Y-m',strtotime($from));
        
        if($idzone == 0 ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        
        if($idpcat == 0 ){
            $pcatss =  $allpcats;
             $pcats = explode(',',$allpcats);
        }else{
            $pcats[] = $idpcat;
            $pcatss = $idpcat;
        }
 
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing');
        $this->db->where_in('b.idzone', $zones);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
         $query = $this->db->get();  
        return $query->result();
    }
    
    public function get_drr_promotor_sale_report_byidbranch($from, $idpcat, $allpcats, $idbranch, $allbranches){
        $month_year = date('Y-m',strtotime($from));
        $c_from = date('Y-m-01', strtotime($from));
        
        if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idpcat == 0 ){
            $pcatss =  $allpcats;
             $pcats = explode(',',$allpcats);
        }else{
            $pcats[] = $idpcat;
            $pcatss = $idpcat;
        }
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
   
    public function get_drr_promotor_sale_report_byidzone($from, $idpcat, $allpcats, $idzone, $allzone){
        $month_year = date('Y-m',strtotime($from));
        $c_from = date('Y-m-01', strtotime($from));
        if($idzone == 0 ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        
        if($idpcat == 0 ){
            $pcatss =  $allpcats;
             $pcats = explode(',',$allpcats);
        }else{
            $pcats[] = $idpcat;
            $pcatss = $idpcat;
        }
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding');
        $this->db->where_in('b.idzone', $zones);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
    
    
//    public function get_promotor_sale_report_byidbranch($from, $to, $idpcat, $allpcats, $idbranch, $allbranches){
//        $month_year = date('Y-m',strtotime($from));
//        
//        if($idbranch == 0 ){
//            $branches = explode(',',$allbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
//        
//        if($idpcat == 0 ){
//            $pcatss =  $allpcats;
//             $pcats = explode(',',$allpcats);
//        }else{
//            $pcats[] = $idpcat;
//            $pcatss = $idpcat;
//        }
//        
//        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total');
//        $this->db->where_in('b.id_branch', $branches);
//        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
//        $this->db->where('users.iduserrole', 17);
//        $this->db->where('users.active', 1);
//        $this->db->where('b.id_branch = users.idbranch')->from('users');
//        $this->db->where('b.idzone= zone.id_zone')->from('zone');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//        $this->db->from('branch b');
//         $query = $this->db->get();  
//        return $query->result();
//
//    }
//    
//   public function get_promotor_sale_report_byidzone($from, $to, $idpcat, $allpcats, $idzone, $allzone){
//        $month_year = date('Y-m',strtotime($from));
//        
//        if($idzone == 0 ){
//            $zones = explode(',',$allzone);
//        }else{
//            $zones[] = $idzone;
//        }
//        
//        if($idpcat == 0 ){
//            $pcatss =  $allpcats;
//             $pcats = explode(',',$allpcats);
//        }else{
//            $pcats[] = $idpcat;
//            $pcatss = $idpcat;
//        }
// 
//        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total');
//        $this->db->where_in('b.idzone', $zones);
//        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
//        $this->db->where('users.iduserrole', 17);
//        $this->db->where('users.active', 1);
//        $this->db->where('b.id_branch = users.idbranch')->from('users');
//        $this->db->where('b.idzone= zone.id_zone')->from('zone');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//        $this->db->from('branch b');
//         $query = $this->db->get();  
//        return $query->result();
//    }
//    
//    public function get_drr_promotor_sale_report_byidbranch($from, $idpcat, $allpcats, $idbranch, $allbranches){
//        $month_year = date('Y-m',strtotime($from));
//        
//        if($idbranch == 0 ){
//            $branches = explode(',',$allbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
//        
//        if($idpcat == 0 ){
//            $pcatss =  $allpcats;
//             $pcats = explode(',',$allpcats);
//        }else{
//            $pcats[] = $idpcat;
//            $pcatss = $idpcat;
//        }
//        
//        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total');
//        $this->db->where_in('b.id_branch', $branches);
//        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
//        $this->db->where('users.iduserrole', 17);
//        $this->db->where('users.active', 1);
//        $this->db->where('b.id_branch = users.idbranch')->from('users');
//        $this->db->where('b.idzone= zone.id_zone')->from('zone');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//        $this->db->from('branch b');
//        $query = $this->db->get();  
//        return $query->result();
//    }
//   
//    public function get_drr_promotor_sale_report_byidzone($from, $idpcat, $allpcats, $idzone, $allzone){
//        $month_year = date('Y-m',strtotime($from));
//        
//        if($idzone == 0 ){
//            $zones = explode(',',$allzone);
//        }else{
//            $zones[] = $idzone;
//        }
//        
//        if($idpcat == 0 ){
//            $pcatss =  $allpcats;
//             $pcats = explode(',',$allpcats);
//        }else{
//            $pcats[] = $idpcat;
//            $pcatss = $idpcat;
//        }
//        
//        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total');
//        $this->db->where_in('b.idzone', $zones);
//        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
//        $this->db->where('users.iduserrole', 17);
//        $this->db->where('users.active', 1);
//        $this->db->where('b.id_branch = users.idbranch')->from('users');
//        $this->db->where('b.idzone= zone.id_zone')->from('zone');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//        $this->db->from('branch b');
//        $query = $this->db->get();  
//        return $query->result();
//    }
//    
    public function get_lmtd_branch_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches){
        
        $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
         if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) curr_sp", 'curr_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) last_sp", 'last_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->order_by('zone.id_zone','ASC');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
    
    public function get_lmtd_branch_sale_report_byidzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone){
        $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
        if($idzone == 0 || $idzone == 'all'){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        if($idzone == 'all'){
            $this->db->select('zone.zone_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
            $this->db->where_in('zone.id_zone',$zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) curr_sp", 'curr_sp.idzone = zone.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone ) curr_smart_sp", 'curr_smart_sp.idzone = zone.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) last_sp", 'last_sp.idzone = zone.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) last_smart_sp", 'last_smart_sp.idzone = zone.id_zone', 'left');
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->from('zone');
            $query = $this->db->get();  
            return $query->result();
        }else{
        
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) curr_sp", 'curr_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) last_sp", 'last_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch', 'left');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    public function get_lmtd_promotor_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches){
         $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
         if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        
         $this->db->select('users.id_users, users.user_name,b.id_branch,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_landing,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,sum(s.landing) as llanding,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,sum(ss.landing) as lsmart_landing, ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
    
    public function get_lmtd_promotor_sale_report_byidbzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone){
         $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
         if($idzone == 0 || $idzone == 'all' ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        if($idzone == 'all'){
            $this->db->select('zone.zone_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_landing,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_landing');
            $this->db->where_in('zone.id_zone', $zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone) curr_sp", 'curr_sp.idzone = zone.id_zone ', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing,ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) curr_smart_sp", 'curr_smart_sp.idzone = zone.id_zone ', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,sum(s.landing) as llanding,s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) last_sp", 'last_sp.idzone = zone.id_zone ', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,sum(ss.landing) as lsmart_landing,ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) last_smart_sp", 'last_smart_sp.idzone = zone.id_zone', 'left');
            $this->db->from('zone');
            $query = $this->db->get();  
            return $query->result();
        }
        else{
            $this->db->select('users.id_users, users.user_name,b.id_branch,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_landing,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_landing');
            $this->db->where_in('b.idzone', $zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,sum(s.landing) as llanding,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,sum(ss.landing) as lsmart_landing,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
    //LMTD VS MTD Sale Report Old Balup
//    public function get_lmtd_branch_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches){
//        
//        $c_from = $month.'-01';
//        $c_to = date('Y-m-d');
//        $day = date('d');
//        
//        $last_from = $lastmonth.'-01';
//        $last_to = $lastmonth.'-'.$day;
//        
//        
//         if($idbranch == 0 ){
//            $branches = explode(',',$allbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
//        $this->db->where_in('b.id_branch',$branches);
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) curr_sp", 'curr_sp.idbranch = b.id_branch', 'left');
//        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch', 'left');
//        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) last_sp", 'last_sp.idbranch = b.id_branch', 'left');
//        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch', 'left');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
//        $this->db->order_by('zone.id_zone','ASC');
//        $this->db->where('b.idzone = zone.id_zone')->from('zone');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
//        $this->db->from('branch b');
//        $query = $this->db->get();  
//        return $query->result();
//    }
//    
//    public function get_lmtd_branch_sale_report_byidzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone){
//        $c_from = $month.'-01';
//        $c_to = date('Y-m-d');
//        $day = date('d');
//        
//        $last_from = $lastmonth.'-01';
//        $last_to = $lastmonth.'-'.$day;
//        
//        
//        if($idzone == 0 || $idzone == 'all'){
//            $zones = explode(',',$allzone);
//        }else{
//            $zones[] = $idzone;
//        }
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        if($idzone == 'all'){
//            $this->db->select('zone.zone_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
//            $this->db->where_in('zone.id_zone',$zones);
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) curr_sp", 'curr_sp.idzone = zone.id_zone', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone ) curr_smart_sp", 'curr_smart_sp.idzone = zone.id_zone', 'left');
//            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) last_sp", 'last_sp.idzone = zone.id_zone', 'left');
//            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) last_smart_sp", 'last_smart_sp.idzone = zone.id_zone', 'left');
//            $this->db->order_by('zone.id_zone','ASC');
//            $this->db->from('zone');
//            $query = $this->db->get();  
//            return $query->result();
//        }else{
//        
//            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
//            $this->db->where_in('b.idzone',$zones);
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) curr_sp", 'curr_sp.idbranch = b.id_branch', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch', 'left');
//            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) last_sp", 'last_sp.idbranch = b.id_branch', 'left');
//            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch', 'left');
//            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
//            $this->db->order_by('zone.id_zone','ASC');
//            $this->db->where('b.idzone = zone.id_zone')->from('zone');
//            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
//            $this->db->from('branch b');
//            $query = $this->db->get();  
//            return $query->result();
//        }
//    }
//    public function get_lmtd_promotor_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches){
//         $c_from = $month.'-01';
//        $c_to = date('Y-m-d');
//        $day = date('d');
//        
//        $last_from = $lastmonth.'-01';
//        $last_to = $lastmonth.'-'.$day;
//        
//        
//         if($idbranch == 0 ){
//            $branches = explode(',',$allbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        
//         $this->db->select('users.id_users, users.user_name,b.id_branch,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total');
//        $this->db->where_in('b.id_branch', $branches);
//        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idsalesperson = users.id_users', 'left');
//        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idsalesperson = users.id_users', 'left');
//        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idsalesperson = users.id_users', 'left');
//        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idsalesperson = users.id_users', 'left');
//        $this->db->where('users.iduserrole', 17);
//        $this->db->where('users.active', 1);
//        $this->db->where('b.id_branch = users.idbranch')->from('users');
//        $this->db->where('b.idzone= zone.id_zone')->from('zone');
//        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//        $this->db->from('branch b');
//        $query = $this->db->get();  
//        return $query->result();
//    }
//    
//    public function get_lmtd_promotor_sale_report_byidbzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone){
//         $c_from = $month.'-01';
//        $c_to = date('Y-m-d');
//        $day = date('d');
//        
//        $last_from = $lastmonth.'-01';
//        $last_to = $lastmonth.'-'.$day;
//        
//        
//         if($idzone == 0 || $idzone == 'all' ){
//            $zones = explode(',',$allzone);
//        }else{
//            $zones[] = $idzone;
//        }
//        if($idpcat == 0 ){
//            $pcats =  $allpcats;
//        }else{
//            $pcats = $idpcat;
//        }
//        
//        if($idzone == 'all'){
//            $this->db->select('zone.zone_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total');
//            $this->db->where_in('zone.id_zone', $zones);
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone) curr_sp", 'curr_sp.idzone = zone.id_zone ', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) curr_smart_sp", 'curr_smart_sp.idzone = zone.id_zone ', 'left');
//            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,s.idbranch,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone ) last_sp", 'last_sp.idzone = zone.id_zone ', 'left');
//            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY brr.idzone) last_smart_sp", 'last_smart_sp.idzone = zone.id_zone', 'left');
//            $this->db->from('zone');
//            $query = $this->db->get();  
//            return $query->result();
//        }
//        else{
//            $this->db->select('users.id_users, users.user_name,b.id_branch,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total');
//            $this->db->where_in('b.idzone', $zones);
//            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idsalesperson = users.id_users', 'left');
//            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idsalesperson = users.id_users', 'left');
//            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idsalesperson = users.id_users', 'left');
//            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idsalesperson = users.id_users', 'left');
//            $this->db->where('users.iduserrole', 17);
//            $this->db->where('users.active', 1);
//            $this->db->where('b.id_branch = users.idbranch')->from('users');
//            $this->db->where('b.idzone= zone.id_zone')->from('zone');
//            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
//            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
//            $this->db->from('branch b');
//            $query = $this->db->get();  
//            return $query->result();
//        }
//    }
//    
    
    
    
     //lmtd brand sale report
    
     public function get_lmtd_brand_sale_report_byidbranch($month,$lastmonth, $idpcat,$allpcats,$idbranch,$allbranches){
        
        $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
         if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing,brand.id_brand,brand.brand_name');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,s.idbrand ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idbrand = brand.id_brand ', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,ss.idbrand ) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idbrand = brand.id_brand', 'left');
        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch,s.idbrand from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch, s.idbrand ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idbrand = brand.id_brand', 'left');
        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch, ss.idbrand ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idbrand = brand.id_brand', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->order_by('zone.id_zone,b.id_branch,brand.id_brand','ASC');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->from('branch b');
        $this->db->from('brand');
        $query = $this->db->get();  
        return $query->result();
    }
    
    public function get_lmtd_brand_sale_report_byidzone($month,$lastmonth, $idpcat,$allpcats,$idzone,$allzone){
        $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        
        
        if($idzone == 0 || $idzone == 'all'){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        if($idpcat == 0 ){
            $pcats =  $allpcats;
        }else{
            $pcats = $idpcat;
        }
        
        if($idzone == 'all'){
            $this->db->select('brand.brand_name,brand.id_brand,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand from sale_product s WHERE  s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbrand ) curr_sp", 'curr_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbrand) curr_smart_sp", 'curr_smart_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch,s.idbrand from sale_product s WHERE s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbrand) last_sp", 'last_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbrand) last_smart_sp", 'last_smart_sp.idbrand = brand.id_brand', 'left');
            $this->db->order_by('brand.id_brand','ASC');
            $this->db->from('brand');
            $query = $this->db->get();  
            return $query->result();
        }else{
        
            $this->db->select('zone.zone_name,zone.id_zone,brand.id_brand,brand.brand_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,curr_smart_sp.smart_landing,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,last_smart_sp.lsmart_landing');
            $this->db->where_in('zone.id_zone',$zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand, brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone,s.idbrand ) curr_sp", 'curr_sp.idzone = zone.id_zone and curr_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone,ss.idbrand ) curr_smart_sp", 'curr_smart_sp.idzone = zone.id_zone and curr_smart_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal, sum(s.landing) as llanding, s.idbranch,s.idbrand,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone,s.idbrand ) last_sp", 'last_sp.idzone = zone.id_zone and last_sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total, sum(ss.landing) as lsmart_landing,ss.idbrand, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.idbranch = brr.id_branch and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY brr.idzone,ss.idbrand) last_smart_sp", 'last_smart_sp.idzone = zone.id_zone and last_smart_sp.idbrand = brand.id_brand', 'left');
            $this->db->order_by('zone.id_zone','ASC')->from('zone');
            $this->db->from('brand');
            $query = $this->db->get();  
            return $query->result();
        }
         
    }
    
    //MTD BRAND SALE REPORT
        public function get_mtd_brand_sale_report_byidbranch($from,$to,$idpcat,$allpcats,$idbranch,$allbranches){
//            $c_from = date('Y-m-01'); 
//            $c_to = date('Y-m-t'); 
            $c_from = $from; 
            $c_to = $to; 

            $month_year = date('Y-m', strtotime($from));

            if($idbranch == 0 ){
                $branches = explode(',',$allbranches);
            }else{
                $branches[] = $idbranch;
            }
            if($idpcat == 0 ){
                $pcats =  $allpcats;
            }else{
                $pcats = $idpcat;
            }

            $this->db->select('zone.zone_name,brand.id_brand,brand.brand_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
            $this->db->where_in('b.id_branch',$branches);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,s.idbrand ) sp", 'sp.idbranch = b.id_branch and sp.idbrand = brand.id_brand', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,ss.idbrand ) smart_sp", 'smart_sp.idbranch = b.id_branch and smart_sp.idbrand = brand.id_brand', 'left');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
            $this->db->from('branch b')->from('brand');
            $query = $this->db->get();  
            return $query->result();
        }
        public function get_mtd_brand_sale_report_byidzone($from,$to,$idpcat,$allpcats,$idzone,$allzones){
//            $c_from = date('Y-m-01'); 
//            $c_to = date('Y-m-t'); 
            $c_from = $from; 
            $c_to = $to; 

            $month_year = date('Y-m', strtotime($from));

            if($idzone == 0 || $idzone == 'all'){
                $zones = explode(',',$allzones);
            }else{
                $zones[] = $idzone;
            }
            if($idpcat == 0 ){
                $pcats =  $allpcats;
            }else{
                $pcats = $idpcat;
            }

            if($idzone == 'all'){
                $this->db->select('brand.id_brand,brand.brand_name,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbrand ) sp", 'sp.idbrand = brand.id_brand', 'left');
                $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbrand ) smart_sp", 'smart_sp.idbrand = brand.id_brand', 'left');
                $this->db->order_by('brand.id_brand','ASC');
                $this->db->from('brand');
                $query = $this->db->get();  
                return $query->result();
            }else{
                $this->db->select('zone.zone_name,brand.id_brand,brand.brand_name,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing');
                $this->db->where_in('zone.id_zone',$zones);
                $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,s.idbrand,brr.idzone from sale_product s,branch brr WHERE s.idbranch = brr.id_branch and  s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY brr.idzone,s.idbrand ) sp", 'sp.idzone = zone.id_zone and sp.idbrand = brand.id_brand', 'left');
                $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,ss.idbrand,brr.idzone from sale_product ss, branch brr WHERE ss.idbranch = brr.id_branch and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY brr.idzone,ss.idbrand ) smart_sp", 'smart_sp.idzone = zone.id_zone and smart_sp.idbrand = brand.id_brand', 'left');
                $this->db->order_by('zone.id_zone,brand.id_brand','ASC');
                $this->db->from('zone')->from('brand');
                $query = $this->db->get();  
                return $query->result();
            }
        }
        
         //Promotor target vs ach 
        public function get_promotor_target_ach_byidbranch($monthyear, $idpcat, $allpcats, $idbranch, $allbranches){
            $month_year = $monthyear;

            $day = date('d');
            $from = $month_year.'-01';
            $to = $month_year.'-'.$day;

            if($idbranch == 0 ){
                $branches = explode(',',$allbranches);
            }else{
                $branches[] = $idbranch;
            }

            if($idpcat == 0 ){
                $pcatss =  $allpcats;
                 $pcats = explode(',',$allpcats);
            }else{
                $pcats[] = $idpcat;
                $pcatss = $idpcat;
            }

            $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,smartsp.smart_qty,rudram.rudram_qty,finance.finance_qty');
            $this->db->where_in('b.id_branch', $branches);
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and  ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(sss.qty) as rudram_qty,sss.idbranch,sale.idsalesperson from sale_product sss,sale WHERE sss.idsale = sale.id_sale and sss.date between '$from' and '$to' and sss.idbrand = 29  GROUP BY sss.idbranch, sale.idsalesperson ) rudram", 'rudram.idbranch = b.id_branch and rudram.idsalesperson = users.id_users', 'left');
            $this->db->join("(select count(spay.id_salepayment) as finance_qty,spay.idbranch,sale.idsalesperson from sale_payment spay,sale_product, sale WHERE spay.idsale = sale.id_sale and sale_product.idsale = spay.idsale   and spay.idpayment_head = 4 and spay.date between '$from' and '$to' and sale_product.idcategory in(1,32) and sale_product.idproductcategory in($pcatss) GROUP BY spay.idbranch, sale.idsalesperson ) finance", 'finance.idbranch = b.id_branch and finance.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
             $query = $this->db->get();  
            return $query->result();
        }
        public function get_promotor_target_ach_byidbzone($monthyear, $idpcat, $allpcats, $idzone,$allzones){
            $month_year = $monthyear;

            $day = date('d');
            $from = $month_year.'-01';
            $to = $month_year.'-'.$day;

            if($idzone == 0 || $idzone == 'all'){
                $zones = explode(',',$allzones);
            }else{
                $zones[] = $idzone;
            }

            if($idpcat == 0 ){
                $pcatss =  $allpcats;
                 $pcats = explode(',',$allpcats);
            }else{
                $pcats[] = $idpcat;
                $pcatss = $idpcat;
            }

            $this->db->select('zone.zone_name,zone.id_zone,users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,smartsp.smart_qty,rudram.rudram_qty,finance.finance_qty');
            $this->db->where_in('b.idzone', $zones);
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(sss.qty) as rudram_qty,sss.idbranch,sale.idsalesperson from sale_product sss,sale WHERE sss.idsale = sale.id_sale and sss.date between '$from' and '$to' and sss.idbrand = 29  GROUP BY sss.idbranch, sale.idsalesperson ) rudram", 'rudram.idbranch = b.id_branch and rudram.idsalesperson = users.id_users', 'left');
            $this->db->join("(select count(spay.id_salepayment) as finance_qty,spay.idbranch,sale.idsalesperson from sale_payment spay,sale_product, sale WHERE spay.idsale = sale.id_sale and sale_product.idsale = spay.idsale   and spay.idpayment_head = 4 and spay.date between '$from' and '$to' and sale_product.idcategory in(1,32) and sale_product.idproductcategory in($pcatss) GROUP BY spay.idbranch, sale.idsalesperson ) finance", 'finance.idbranch = b.id_branch and finance.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->where('b.is_warehouse',0);
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
             $query = $this->db->get();  
            return $query->result();
        }
        
        //Promotor Discount Report
           public function get_promotor_discount_byidbranch($from, $to, $idpcat,$allpcats, $idbranch, $allbranches){
            
            if($idbranch == 0 ){
                $branches = explode(',',$allbranches);
            }else{
                $branches[] = $idbranch;
            }

            if($idpcat == 0 ){
                $pcatss =  $allpcats;
                 $pcats = explode(',',$allpcats);
            }else{
                $pcats[] = $idpcat;
                $pcatss = $idpcat;
            }

            $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,sp.sale_qty,sp.total,sp.landing,sp.salesman_price, smartsp.smart_qty,smartsp.smart_total,smartsp.smart_landing,smartsp.smart_salesman_price');
            $this->db->where_in('b.id_branch', $branches);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, sum(s.salesman_price) as salesman_price, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing, sum(ss.salesman_price) as smart_salesman_price,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('b.is_warehouse',0);
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
             $query = $this->db->get();  
            return $query->result();
        }
           public function get_promotor_discount_byidzone($from, $to,$idpcat, $allpcats, $idzone, $allzones){
            
            if($idzone == 0 || $idzone == 'all'){
                $zones = explode(',',$allzones);
            }else{
                $zones[] = $idzone;
            }

            if($idpcat == 0 ){
                $pcatss =  $allpcats;
                 $pcats = explode(',',$allpcats);
            }else{
                $pcats[] = $idpcat;
                $pcatss = $idpcat;
            }

            $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,sp.sale_qty,sp.total,sp.landing,sp.salesman_price, smartsp.smart_qty,smartsp.smart_total,smartsp.smart_landing,smartsp.smart_salesman_price');
            $this->db->where_in('b.idzone', $zones);
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, sum(s.salesman_price) as salesman_price, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing, sum(ss.salesman_price) as smart_salesman_price,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('b.is_warehouse',0);
            $this->db->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
             $query = $this->db->get();  
            return $query->result();
        }
        
}
?>