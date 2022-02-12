<?php
class Target_model extends CI_Model{
    
    public function ajax_get_branch_target_data_byidzone($idzone, $lastmonth, $monthyear){
        $dd = $lastmonth.'-01';
//        $from = date("Y-m-d", strtotime ( '-1 month' , strtotime ( $dd ) )) ;
//        $to = date("Y-m-t", strtotime ( '-1 month' , strtotime ( $dd ) )) ;
        $from = $dd ;
        $to = date("Y-m-t", strtotime($lastmonth)) ;
        
        $allow_target = array(0,2);

        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,pc.product_category_name,pc.id_product_category, sp.sale_qty,sp.total,sp.landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,btarget.*');
        $this->db->where('b.idzone',$idzone);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch, s.idproductcategory from sale_product s WHERE s.date between '$from' and '$to' GROUP BY s.idbranch, s.idproductcategory) sp", 'sp.idbranch = b.id_branch and sp.idproductcategory = pc.id_product_category', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch, ss.idproductcategory from sale_product ss WHERE ss.date between '$from' and '$to' and ss.idcategory in(1,32) GROUP BY ss.idbranch, ss.idproductcategory) smart_sp", 'smart_sp.idbranch = b.id_branch and smart_sp.idproductcategory = pc.id_product_category', 'left');
        $this->db->join("(select * from branch_target br WHERE br.month_year = '$monthyear') btarget", 'btarget.idbranch = b.id_branch and btarget.idproductcategory = pc.id_product_category', 'left');
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
                        ->where('users.id_users !=', 0)
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
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
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
            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('z.id_zone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year'  and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch,brr.idzone from sales_return_product s, branch brr WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sreturn", 'sreturn.idzone = z.id_zone', 'left');
            $this->db->order_by('z.id_zone','ASC');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }else{
            
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->where('b.is_warehouse',0);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year'  and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
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
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing, csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
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
            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,  csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('z.id_zone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch,brr.idzone from sale_product cs, branch brr WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) and cs.idbranch = brr.id_branch GROUP BY brr.idzone ) csp", 'csp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date = '$from' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch,brr.idzone from sale_product css,branch brr WHERE css.date >= '$c_from' and css.date < '$from'  and css.idcategory in(1,32) and css.idbranch = brr.id_branch GROUP BY brr.idzone ) csmart_sp", 'csmart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch,brr.idzone from sales_return_product s,branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY s.idbranch ) sreturn", 'sreturn.idzone = z.id_zone', 'left');
            $this->db->order_by('z.id_zone','ASC');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }else{
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,   csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->where('b.is_warehouse',0);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
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
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing, sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing, sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.idzone', $zones);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding, sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.idzone', $zones);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
        $this->db->where('b.is_warehouse',0);
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
            $this->db->where('b.is_warehouse',0);
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
        $this->db->where('b.is_warehouse',0);
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
            $this->db->where('b.is_warehouse',0);
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
        $this->db->where('b.is_warehouse',0);
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
            $this->db->where('b.is_warehouse',0);
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

            $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,smartsp.smart_qty,rudram.rudram_qty,finance.finance_qty,pro_plan.pro_qty');
            $this->db->where_in('b.id_branch', $branches);
            $this->db->where('b.is_warehouse',0);
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,avg(p.asp) as pasp,p.revenue as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and  ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(sss.qty) as rudram_qty,sss.idbranch,sale.idsalesperson from sale_product sss,sale WHERE sss.idsale = sale.id_sale and sss.date between '$from' and '$to' and sss.idbrand = 29  GROUP BY sss.idbranch, sale.idsalesperson ) rudram", 'rudram.idbranch = b.id_branch and rudram.idsalesperson = users.id_users', 'left');
            $this->db->join("(select count(spay.id_salepayment) as finance_qty,spay.idbranch,sale.idsalesperson from sale_payment spay,sale_product, sale WHERE spay.idsale = sale.id_sale and sale_product.idsale = spay.idsale   and spay.idpayment_head = 4 and spay.date between '$from' and '$to' and sale_product.idcategory in(1,32) and sale_product.idproductcategory in($pcatss) GROUP BY spay.idbranch, sale.idsalesperson ) finance", 'finance.idbranch = b.id_branch and finance.idsalesperson = users.id_users', 'left');
            $this->db->join("(select count(s.id_saleproduct) as pro_qty,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory = 8 GROUP BY s.idbranch, sale.idsalesperson ) pro_plan", 'pro_plan.idbranch = b.id_branch and pro_plan.idsalesperson = users.id_users', 'left');
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
            $this->db->where('b.is_warehouse',0);
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
            $this->db->where('b.is_warehouse',0);
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
            $this->db->where('b.is_warehouse',0);
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
        
        //Ageing Target Setup 
        
        public function get_ageing_stock_data_byidbranch($last_month){
            $from= $last_month.'-01';
            $to = date("Y-m-t", strtotime($from)) ;
            
            $this->db->select('b.id_branch,b.branch_name, ageing.age_qty,sp.sale_qty');
            $this->db->where('b.is_warehouse',0);
            $this->db->join('(select  sum(st.qty) as age_qty,st.idbranch,st.idvariant,st.idgodown from stock st, ageing_stock ast WHERE st.idproductcategory = 1 and st.idgodown=1 and ast.idproductcategory = st.idproductcategory and st.idbranch = ast.idbranch and  st.idvariant = ast.idvariant GROUP BY st.idbranch) ageing','ageing.idbranch = b.id_branch', 'left');                 
//            $this->db->join("(select count(agst.id_ageing_stock) as age_qty, agst.idbranch from ageing_stock agst where agst.idproductcategory = 1 GROUP BY agst.idbranch ) ageing", 'ageing.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, s.idbranch from sale_product s WHERE  s.idproductcategory = 1 and s.ageing = 1 and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->group_by('b.id_branch');
            $this->db->from('branch b');
            $query = $this->db->get();  
             return $query->result();
            
//             die($this->db->last_query());
        }
      
       
      
        public function get_ageing_data_bymonth($month_year){
            return $this->db->where('month_year', $month_year)
                            ->where('idproductcategory', 1)
                            ->order_by('idbranch', 'ASC')
                            ->get('branch_target')->result();
        }


        public function ajax_check_branch_target_data($idbranch, $month_year){
            return $this->db->where('month_year', $month_year)
                            ->where('idproductcategory', 1)
                            ->where('idbranch', $idbranch)
                            ->get('branch_target')->row();
        }
        public function ajax_check_branch_target_data_byidproductcat($idzone, $monthyear) {
            return $this->db->where('month_year', $monthyear)
                            ->where('idproductcategory !=', 1)
                            ->where('branch.idzone',$idzone)
                            ->where('branch_target.idbranch = branch.id_branch')->from('branch')
                            ->get('branch_target')->result();
        }
       
        //***********Target Slabs*******************
        
        public function save_target_slab_data($data){
            return $this->db->insert('target_slab', $data);
        }
        public function get_target_slab_data(){
            return $this->db->get('target_slab')->result();
        }
        public function get_target_slab_data_byid($target_slabs){
            return $this->db->where('id_target_slab', $target_slabs)->get('target_slab')->row();
        }
        public function edit_target_slab_data($data, $id){
            return $this->db->where('id_target_slab', $id)->update('target_slab', $data);
        }
        public function get_target_slab_per_data_byid($slabmonth, $from_slab){
            return $this->db->select('sum(target_per) as tar_per')
                            ->where('month_year', $slabmonth)
                            ->where('from_date <',$from_slab)
                            ->get('target_slab')->row();
        }
        public function get_slab_by_month($month) {
			return $this->db->where('month_year', $month)->order_by('id_target_slab')->get('target_slab')->result();            
        }
        public function ajax_get_promotor_target_slab_data_byid($idbranch, $monthyear, $idslab){
        return $this->db->select('users.user_name,brand.brand_name,promotor_target_setup.*')
                        ->where('promotor_target_setup.month_year',$monthyear)
                        ->where('promotor_target_setup.idbranch',$idbranch)
                        ->where('promotor_target_setup.id_targetslab',$idslab)
                        ->where('promotor_target_setup.idpromotor = users.id_users')->from('users')
                        ->join('brand','promotor_target_setup.idbrand = brand.id_brand','left')
                        ->get('promotor_target_setup')->result();
    }
    public function delete_promotor_target_slab_data_byid($idbranch, $monthyear, $idslab){
        return $this->db->where('promotor_target_setup.month_year',$monthyear)
                        ->where('promotor_target_setup.idbranch',$idbranch)
                        ->where('promotor_target_setup.id_targetslab',$idslab)
                        ->delete('promotor_target_setup');
    }
      public function ajax_get_drr_achivement_slab_byidbranch($from,$from_slab,$idpcat,$allpcats,$idbranch,$allbranches) {
        
        $c_from = $from_slab;
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
        
        $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing, csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing,last_csp.last_csale_qty, last_csp.last_ctotal as last_csale_total,last_csp.last_clanding as last_csale_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(last_cs.qty) as last_csale_qty, sum(last_cs.total_amount) as last_ctotal, sum(last_cs.landing) as last_clanding, last_cs.idbranch from sale_product last_cs WHERE last_cs.date >= '$month_year-01' and  last_cs.date < '$c_from' and last_cs.idproductcategory in($pcats) GROUP BY last_cs.idbranch ) last_csp", 'last_csp.idbranch = b.id_branch', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
        $this->db->order_by('zone.id_zone','ASC');
        $this->db->where('b.idzone = zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    
    public function ajax_get_drr_achivement_slab_byidzone($from,$from_slab,$idpcat,$allpcats,$idzone,$allzone){
        $c_from = $from_slab;
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
            $this->db->select('z.zone_name,z.id_zone,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing, last_csp.last_csale_qty, last_csp.last_ctotal as last_csale_total,last_csp.last_clanding as last_csale_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('z.id_zone',$zones);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue,tar.idbranch, brr.idzone from branch_target tar, branch brr WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) and tar.idbranch = brr.id_branch GROUP BY brr.idzone) branch_target", 'branch_target.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch,brr.idzone from sale_product s, branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY brr.idzone ) sp", 'sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal, sum(cs.landing) as clanding, cs.idbranch,brr.idzone from sale_product cs, branch brr WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) and cs.idbranch = brr.id_branch GROUP BY brr.idzone ) csp", 'csp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch,brr.idzone from sale_product ss,branch brr WHERE ss.date = '$from' and ss.idcategory in(1,32) and ss.idbranch = brr.id_branch GROUP BY brr.idzone ) smart_sp", 'smart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch,brr.idzone from sale_product css,branch brr WHERE css.date >= '$c_from' and css.date < '$from'  and css.idcategory in(1,32) and css.idbranch = brr.id_branch GROUP BY brr.idzone ) csmart_sp", 'csmart_sp.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch,brr.idzone from sales_return_product s , branch brr WHERE s.date = '$from' and s.idproductcategory in($pcats) and s.idbranch = brr.id_branch GROUP BY s.idbranch ) sreturn", 'sreturn.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(last_cs.qty) as last_csale_qty, sum(last_cs.total_amount) as last_ctotal, sum(last_cs.landing) as last_clanding, last_cs.idbranch,brr.idzone from sale_product last_cs, branch brr WHERE last_cs.date >= '$month_year-01' and  last_cs.date < '$c_from' and last_cs.idproductcategory in($pcats) and last_cs.idbranch = brr.id_branch GROUP BY last_cs.idbranch ) last_csp", 'last_csp.idzone = z.id_zone', 'left');
            
            $this->db->order_by('z.id_zone','ASC');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }else{
            
            $this->db->select('zone.zone_name,b.branch_name,b.id_branch,partner_type.partner_type,branch_category.branch_category_name,branch_target.tar_volume,branch_target.tar_value,branch_target.tar_asp,branch_target.tar_revenue,sp.sale_qty,sp.total as sale_total,sp.landing as sale_landing,smart_sp.smart_sale_qty,smart_sp.smart_total,smart_sp.smart_landing,   csp.csale_qty, csp.ctotal as csale_total,csp.clanding as csale_landing,csmart_sp.csmart_sale_qty,csmart_sp.csmart_total,csmart_sp.csmart_landing, last_csp.last_csale_qty, last_csp.last_ctotal as last_csale_total,last_csp.last_clanding as last_csale_landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
            $this->db->where_in('b.idzone',$zones);
            $this->db->where('b.is_warehouse',0);
            $this->db->join("(select sum(tar.volume) as tar_volume, sum(tar.value) as tar_value, sum(tar.asp) as tar_asp, sum(tar.revenue) as tar_revenue, tar.idbranch from branch_target tar WHERE tar.month_year = '$month_year' and tar.idproductcategory in($pcats) GROUP BY tar.idbranch) branch_target", 'branch_target.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total, sum(s.landing) as landing, s.idbranch from sale_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sp", 'sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total, sum(s.old_landing) as sreturn_landing, s.idbranch from sales_return_product s WHERE s.date = '$from' and s.idproductcategory in($pcats) GROUP BY s.idbranch ) sreturn", 'sreturn.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total, sum(ss.landing) as smart_landing, ss.idbranch from sale_product ss WHERE ss.date = '$from' and ss.idcategory in(1,32) GROUP BY ss.idbranch ) smart_sp", 'smart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch from sale_product cs WHERE cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcats) GROUP BY cs.idbranch ) csp", 'csp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(css.qty) as csmart_sale_qty, sum(css.total_amount) as csmart_total, sum(css.landing) as csmart_landing, css.idbranch from sale_product css WHERE css.date >= '$c_from' and css.date < '$from' and css.idcategory in(1,32) GROUP BY css.idbranch ) csmart_sp", 'csmart_sp.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(last_cs.qty) as last_csale_qty, sum(last_cs.total_amount) as last_ctotal, sum(last_cs.landing) as last_clanding, last_cs.idbranch from sale_product last_cs WHERE last_cs.date >= '$month_year-01' and  last_cs.date < '$c_from' and last_cs.idproductcategory in($pcats) GROUP BY last_cs.idbranch ) last_csp", 'last_csp.idbranch = b.id_branch', 'left');
            
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category')   ;
            $this->db->order_by('zone.id_zone','ASC');
            $this->db->where('b.idzone = zone.id_zone')->from('zone');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type')   ;
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
    public function get_promotor_sale_report_slab_byidbranch($from, $to, $idslab, $idpcat, $allpcats, $idbranch, $allbranches){
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
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing, sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        if($idslab != 0){ 
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.id_targetslab = $idslab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }else{
             $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
    
   public function get_promotor_sale_report_slab_byidzone($from, $to,$idslab, $idpcat, $allpcats, $idzone, $allzone){
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
 
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,  sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing');
        $this->db->where_in('b.idzone', $zones);
        $this->db->where('b.is_warehouse',0);
        if($idslab != 0){ 
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.id_targetslab = $idslab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }else{
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
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
    
     public function get_drr_promotor_sale_report_slab_byidbranch($from,$from_slab,$idslab,$idpcat, $allpcats, $idbranch, $allbranches){
        $month_year = date('Y-m',strtotime($from));
        $c_from = $from_slab;
        
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
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding,  last_prom.last_pvolume,last_prom.last_pvalue,last_csp.last_csale_qty,last_csp.last_ctotal,sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing ');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        if($idslab != 0){
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.id_targetslab = $idslab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }else{
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        
        $this->db->join("(select sum(last_p.volume) as last_pvolume, sum(last_p.value) as last_pvalue, last_p.idbranch,last_p.idpromotor from promotor_target_setup last_p WHERE last_p.month_year = '$month_year' and last_p.to_slab < '$from_slab' and last_p.idproductcategory in($pcatss) GROUP BY last_p.idbranch, last_p.idpromotor ) last_prom", 'last_prom.idbranch = b.id_branch and last_prom.idpromotor = users.id_users', 'left');
         $this->db->join("(select sum(cs.qty) as last_csale_qty, sum(cs.total_amount) as last_ctotal, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$month_year-01' and cs.date < '$from_slab' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) last_csp", 'last_csp.idbranch = b.id_branch and last_csp.idsalesperson = users.id_users', 'left');
        
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
   
    public function get_drr_promotor_sale_report_slab_byidzone($from,$from_slab,$idslab, $idpcat, $allpcats, $idzone, $allzone){
        $month_year = date('Y-m',strtotime($from));
        $c_from = $from_slab;
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
        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding, last_prom.last_pvolume,last_prom.last_pvalue,last_csp.last_csale_qty,last_csp.last_ctotal, sreturn.sale_return_qty,sreturn.sreturn_total,sreturn.sreturn_landing  ');
        $this->db->where_in('b.idzone', $zones);
        $this->db->where('b.is_warehouse',0);
        if($idslab != 0){
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.id_targetslab = $idslab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }else{
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_return_qty, sum(s.total_amount) as sreturn_total,sum(s.old_landing) as sreturn_landing,s.idbranch,sales_return.idsalesperson from sales_return_product s,sales_return WHERE s.idsales_return = sales_return.id_salesreturn and s.date = '$from' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sales_return.idsalesperson ) sreturn", 'sreturn.idbranch = b.id_branch and sreturn.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        
         $this->db->join("(select sum(last_p.volume) as last_pvolume, sum(last_p.value) as last_pvalue, last_p.idbranch,last_p.idpromotor from promotor_target_setup last_p WHERE last_p.month_year = '$month_year' and last_p.to_slab < '$from_slab' and last_p.idproductcategory in($pcatss) GROUP BY last_p.idbranch, last_p.idpromotor ) last_prom", 'last_prom.idbranch = b.id_branch and last_prom.idpromotor = users.id_users', 'left');
         $this->db->join("(select sum(cs.qty) as last_csale_qty, sum(cs.total_amount) as last_ctotal, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE cs.idsale = sale.id_sale and cs.date >= '$month_year-01' and cs.date < '$from_slab' and cs.idproductcategory in($pcatss) GROUP BY cs.idbranch, sale.idsalesperson ) last_csp", 'last_csp.idbranch = b.id_branch and last_csp.idsalesperson = users.id_users', 'left');
         
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
    
    
    public function get_promotor_target_slab_data_byid($slabmonth, $idbranch, $allbranches){
         if($idbranch == 0 ){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        $this->db->select('*');
        $this->db->where('month_year', $slabmonth);
        $this->db->where_in('idbranch', $branches);
        $this->db->where('id_targetslab !=', NULL);
        $this->db->group_by('idbranch');
        $this->db->group_by('id_targetslab');
        $this->db->from('promotor_target_setup');
        $query = $this->db->get();  
        return $query->result();
        
        
    }
    
    public function check_target_setup_data($idpcat, $allpcats, $idbranch, $allbranches, $slabmonth){
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
        $slabs_data = $this->db->where('month_year', $slabmonth)->get('target_slab')->result();
        
        $str = 'b.branch_name,b.id_branch, zone.zone_name, zone.id_zone,';
        foreach($slabs_data as $slab){ 
               $str .=  'prom'.$slab->id_target_slab.'.target_cnt as target_cnt'.$slab->id_target_slab.',';
        }
         $this->db->select($str);
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        foreach($slabs_data as $slab){ 
            $this->db->join("(select count(p.id_targetslab) as target_cnt, p.idbranch from promotor_target_setup p WHERE p.month_year = '$slabmonth' and p.id_targetslab = $slab->id_target_slab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.id_targetslab ) prom$slab->id_target_slab", 'prom'.$slab->id_target_slab.'.idbranch = b.id_branch ', 'left');
        }
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->order_by('b.id_branch');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
        
    }
    
    public function check_target_setup_data_byidzone($idpcat, $allpcats, $idzone, $allzone, $slabmonth){
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
        $slabs_data = $this->db->where('month_year', $slabmonth)->get('target_slab')->result();
        
        $str = 'b.branch_name,b.id_branch, zone.zone_name, zone.id_zone,';
        foreach($slabs_data as $slab){ 
               $str .=  'prom'.$slab->id_target_slab.'.target_cnt as target_cnt'.$slab->id_target_slab.',';
        }
         $this->db->select($str);
        $this->db->where_in('b.idzone', $zones);
        $this->db->where('b.is_warehouse',0);
        foreach($slabs_data as $slab){ 
            $this->db->join("(select count(p.id_targetslab) as target_cnt, p.idbranch from promotor_target_setup p WHERE p.month_year = '$slabmonth' and p.id_targetslab = $slab->id_target_slab and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.id_targetslab ) prom$slab->id_target_slab", 'prom'.$slab->id_target_slab.'.idbranch = b.id_branch ', 'left');
        }
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->order_by('b.id_branch');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
        
        
    }
    
    public function get_branch_cnt_byidzone($id_zone){
        return $this->db->select('count(id_branch) as branch_cnt')
                        ->where('idzone', $id_zone)
                        ->get('branch')->row();
    }
    
    
    public function get_promotor_target_setup_data_byfilter($idbranch, $branches, $monthyear, $target_slabs, $idpcat, $allslabs){
        if($idbranch == 0 ){
            $allbranch = explode(',',$branches);
        }else{
            $allbranch[] = $idbranch;
        }
        
        if($target_slabs == 0 ||  $target_slabs == 'all'){
            $slabs = explode(',',$allslabs);
        }else{
            $slabs[] = $target_slabs;
        }
        if($target_slabs == 'all'){
        
        return $this->db->select('sum(promotor_target_setup.volume) as volume,sum(promotor_target_setup.value) as value, sum(promotor_target_setup.asp) as asp, sum(promotor_target_setup.revenue) as revenue, sum(promotor_target_setup.connect) as connect , brand.brand_name,branch.branch_name,users.user_name, product_category.product_category_name,zone.zone_name,target_slab.slab_name')
                        ->where('promotor_target_setup.month_year', $monthyear)
                        ->where_in('promotor_target_setup.idbranch', $allbranch)
                        ->where('promotor_target_setup.idproductcategory', $idpcat)
                        ->where_in('promotor_target_setup.id_targetslab', $slabs)
                        ->where('promotor_target_setup. idpromotor = users.id_users')->from('users')
                        ->where('promotor_target_setup.idbrand = brand.id_brand')->from('brand')
                        ->where('promotor_target_setup.idbranch = branch.id_branch')->from('branch')
                        ->where('promotor_target_setup.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('promotor_target_setup.id_targetslab = target_slab.id_target_slab')->from('target_slab')
                        ->where('branch.idzone = zone.id_zone')->from('zone')
                        ->group_by('users.id_users,branch.id_branch ')
                        ->order_by('zone.id_zone,branch.id_branch,users.id_users,target_slab.id_target_slab')
                        ->get('promotor_target_setup')->result();
        }else{
             return $this->db->select('promotor_target_setup.*, brand.brand_name,branch.branch_name,users.user_name, product_category.product_category_name, zone.zone_name, target_slab.slab_name')
                        ->where('promotor_target_setup.month_year', $monthyear)
                        ->where_in('promotor_target_setup.idbranch', $allbranch)
                        ->where('promotor_target_setup.idproductcategory', $idpcat)
                        ->where_in('promotor_target_setup.id_targetslab', $slabs)
                        ->where('promotor_target_setup. idpromotor = users.id_users')->from('users')
                        ->where('promotor_target_setup.idbrand = brand.id_brand')->from('brand')
                        ->where('promotor_target_setup.idbranch = branch.id_branch')->from('branch')
                        ->where('promotor_target_setup.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('promotor_target_setup.id_targetslab = target_slab.id_target_slab')->from('target_slab')
                        ->where('branch.idzone = zone.id_zone')->from('zone')
                        ->order_by('zone.id_zone,branch.id_branch,users.id_users,target_slab.id_target_slab')
                        ->get('promotor_target_setup')->result();
        }
       
    }
    public function get_promotor_target_setup_data_byidzone($idzone, $allzone, $monthyear, $target_slabs, $idpcat, $allslabs){
        if($idzone == 0 ){
            $zones = explode(',',$allzone);
        }else{
            $zones[] = $idzone;
        }
        
        if($target_slabs == 0 || $target_slabs == 'all'){
            $slabs = explode(',',$allslabs);
        }else{
            $slabs[] = $target_slabs;
        }
        
        if($target_slabs == 'all'){
            
            return $this->db->select('sum(promotor_target_setup.volume) as volume,sum(promotor_target_setup.value) as value, sum(promotor_target_setup.asp) as asp, sum(promotor_target_setup.revenue) as revenue, sum(promotor_target_setup.connect) as connect , brand.brand_name,branch.branch_name,users.user_name, product_category.product_category_name,zone.zone_name,target_slab.slab_name')
                        ->where('promotor_target_setup.month_year', $monthyear)
                        ->where('promotor_target_setup.idproductcategory', $idpcat)
                        ->where_in('promotor_target_setup.id_targetslab', $slabs)
                        ->where_in('branch.idzone', $zones)
                        ->where('promotor_target_setup. idpromotor = users.id_users')->from('users')
                        ->where('promotor_target_setup.idbrand = brand.id_brand')->from('brand')
                        ->where('promotor_target_setup.idbranch = branch.id_branch')->from('branch')
                        ->where('promotor_target_setup.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('promotor_target_setup.id_targetslab = target_slab.id_target_slab')->from('target_slab')
                        ->where('branch.idzone = zone.id_zone')->from('zone')
                        ->group_by('users.id_users,branch.id_branch ')
                        ->order_by('zone.id_zone,branch.id_branch,users.id_users,target_slab.id_target_slab')
                        ->get('promotor_target_setup')->result();
            
        }else{
        
            return $this->db->select('promotor_target_setup.*, brand.brand_name,branch.branch_name,users.user_name, product_category.product_category_name,zone.zone_name,target_slab.slab_name')
                        ->where('promotor_target_setup.month_year', $monthyear)
                        ->where('promotor_target_setup.idproductcategory', $idpcat)
                        ->where_in('promotor_target_setup.id_targetslab', $slabs)
                        ->where_in('branch.idzone', $zones)
                        ->where('promotor_target_setup. idpromotor = users.id_users')->from('users')
                        ->where('promotor_target_setup.idbrand = brand.id_brand')->from('brand')
                        ->where('promotor_target_setup.idbranch = branch.id_branch')->from('branch')
                        ->where('promotor_target_setup.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('promotor_target_setup.id_targetslab = target_slab.id_target_slab')->from('target_slab')
                        ->where('branch.idzone = zone.id_zone')->from('zone')
                        ->order_by('zone.id_zone,branch.id_branch,users.id_users,target_slab.id_target_slab')
                        ->get('promotor_target_setup')->result();
        }
    }
    
    
    //***********Price Caategory Target Setup**************
    
    public function save_price_category_target($data){
        return $this->db->insert('price_category_target_setup', $data);
    }
    public function check_get_price_category_data($idbranch, $monthyear, $idpcat){
        return $this->db->where('idbranch', $idbranch)
                        ->where('monthyear', $monthyear)
                        ->where('idproductcategory', $idpcat)
                        ->get('price_category_target_setup')->result();
                        
    }
    public function get_price_category_target_data($idbranch, $monthyear, $idpcat){
        $this->db->select('p.id_price_category_lab, p.lab_name,pset.*');
        $this->db->join("(select psetup.* from price_category_target_setup psetup WHERE psetup.monthyear = '$monthyear' and psetup.idbranch = $idbranch and psetup.idproductcategory = $idpcat) pset", 'pset.idprice_category = p.id_price_category_lab', 'left');
        $this->db->order_by('p.id_price_category_lab');
        $this->db->from('price_category_lab p');
        $query = $this->db->get();  
        return $query->result();    
    }
    public function update_price_cat_target($data, $idpricecatsetup){
        return $this->db->where('id_price_category_setup', $idpricecatsetup)->update('price_category_target_setup', $data);
    }
    public function get_price_catgeory_target_vs_ach_data($idbranch, $monthyear, $idpcat, $allbranchs, $idgodown, $idzone, $allzones){
       
        if($idgodown == 0){
            $godown = array(1,2);
        }else{
            $godown[] = $idgodown;
        }
        $godown = implode(',', $godown);
        
        $branches = array();
        if($idzone == ''){
            if($idbranch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $idbranch;
            }
        }else{
            if($idzone == 'all'){
                 $branches = explode(',', $allbranchs);
            } elseif ($idzone == 'allzone') {
                $branches = explode(',', $allzones);
            }
            else{
                $badts = $this->db->where('idzone', $idzone)->get('branch')->result();
                foreach($badts as $bts){
                    $branches[] = $bts->id_branch;
                }
            }
        }
        $strbr = implode(',', $branches);
//        die(print_r($strbr));
        if($idzone == 'all'){ 
            $from = $monthyear.'-01';
            $to = date('Y-m-t', strtotime($from));

            $this->db->select('p.id_price_category_lab,p.lab_name,pset.vol as volume,pset.val as value, sprod.sqty, sprod.samount');
            $this->db->where('p.active', 0);
            $this->db->join("(select sum(psetup.volume) as vol,sum(psetup.value) as val,psetup.idprice_category from price_category_target_setup psetup WHERE psetup.monthyear = '$monthyear' and psetup.idproductcategory = $idpcat GROUP BY psetup.idprice_category) pset", 'pset.idprice_category = p.id_price_category_lab', 'left');
            $this->db->join("(select sum(s.qty) as sqty, sum(s.total_amount) as samount,s.idbranch,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idbranch in(".$strbr.") and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab GROUP By p.id_price_category_lab) sprod", 'sprod.id_price_category_lab = p.id_price_category_lab', 'left');
//            $this->db->join("(select sum(s.qty) as tsqty, sum(s.total_amount) as tsamount,s.idbranch from sale_product s, price_category_lab p WHERE s.idbranch in(".$strbr.") and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab ) over_tot",'left');
            $this->db->order_by('p.id_price_category_lab');
            $this->db->from('price_category_lab p');
            $query = $this->db->get();  
            return $query->result();
           
        }elseif($idzone == 'allzone'){
            $from = $monthyear.'-01';
            $to = date('Y-m-t', strtotime($from));

            $this->db->select('z.id_zone,z.zone_name,p.id_price_category_lab,p.lab_name,pset.vol as volume, pset.val as value, sprod.sqty, sprod.samount,over_tot.tsqty,over_tot.tsamount');
            $this->db->where_in('z.id_zone', $branches);
            $this->db->where('p.active', 0);
            $this->db->join("(select sum(psetup.volume) as vol,sum(psetup.value) as val,psetup.idprice_category ,brr.idzone from price_category_target_setup psetup, branch brr WHERE brr.idzone in(".$strbr.") and psetup.idbranch = brr.id_branch and psetup.monthyear = '$monthyear' and psetup.idproductcategory = $idpcat GROUP BY psetup.idprice_category) pset", 'pset.idzone = z.id_zone and pset.idprice_category = p.id_price_category_lab ', 'left');
            $this->db->join("(select sum(s.qty) as sqty, sum(s.total_amount) as samount,p.id_price_category_lab, brr.idzone from sale_product s, price_category_lab p,branch brr WHERE brr.idzone in(".$strbr.") and  s.idbranch = brr.id_branch and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab GROUP By brr.idzone,p.id_price_category_lab) sprod", 'sprod.idzone = z.id_zone and sprod.id_price_category_lab = p.id_price_category_lab', 'left');
            $this->db->join("(select sum(s.qty) as tsqty, sum(s.total_amount) as tsamount,s.idbranch,brr.idzone from sale_product s, price_category_lab p, branch brr WHERE brr.idzone in(".$strbr.") and s.idbranch = brr.id_branch and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab GROUP By brr.idzone) over_tot", 'over_tot.idzone = z.id_zone', 'left');
            $this->db->order_by('z.id_zone,p.id_price_category_lab');
            $this->db->from('price_category_lab p');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result(); 
//              die($this->db->last_query());
             
        }else{
            $from = $monthyear.'-01';
            $to = date('Y-m-t', strtotime($from));

            $this->db->select('z.id_zone,z.zone_name,b.id_branch,b.branch_name,p.id_price_category_lab,p.lab_name,pset.*, sprod.sqty, sprod.samount,over_tot.tsqty,over_tot.tsamount');
            $this->db->where_in('b.id_branch', $branches);
            $this->db->where('p.active', 0);
            $this->db->join("(select psetup.* from price_category_target_setup psetup WHERE psetup.monthyear = '$monthyear' and psetup.idproductcategory = $idpcat) pset", 'pset.idprice_category = p.id_price_category_lab and pset.idbranch = b.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sqty, sum(s.total_amount) as samount,s.idbranch,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idbranch in(".$strbr.") and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab GROUP By s.idbranch,p.id_price_category_lab) sprod", 'sprod.idbranch = b.id_branch and sprod.id_price_category_lab = p.id_price_category_lab', 'left');
            $this->db->join("(select sum(s.qty) as tsqty, sum(s.total_amount) as tsamount,s.idbranch from sale_product s, price_category_lab p WHERE s.idbranch in(".$strbr.") and s.idproductcategory = $idpcat and s.idgodown in($godown) and s.date between '".$from."' and '".$to."' and s.total_amount between p.min_lab and p.max_lab GROUP By s.idbranch) over_tot", 'over_tot.idbranch = b.id_branch', 'left');
            $this->db->where('b.idzone = z.id_zone');
            $this->db->order_by('z.id_zone,b.id_branch,p.id_price_category_lab');
            $this->db->from('zone z');
            $this->db->from('price_category_lab p');
            $this->db->from('branch b');
            $query = $this->db->get();  
            return $query->result();
//        die($this->db->last_query());
        }
    }
    
    
}
?>