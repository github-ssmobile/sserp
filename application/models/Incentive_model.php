<?php
class Incentive_model extends CI_Model{
  
    public function product_category_data(){
        return $this->db->where('incentive_allow',0)->get('product_category')->result();
    }
    
    public function get_category_byidpcat($idpcat){
        return $this->db->where('idproductcategory', $idpcat)
                        ->get('category')->result();
    }
    public function get_model_variants_byidbrand($idpcat, $idbrand, $idcat){
        return $this->db->where('idbrand', $idbrand)
                        ->where('idproductcategory', $idpcat)
                        ->where('idcategory', $idcat)
                        ->get('model_variants')->result();
    }
    public function get_idmodel_byvariant($idvariant){
        return $this->db->where('id_variant', $idvariant)
                        ->get('model_variants')->row();
    }
    
    public function save_incentive_policy($inct_policy){
        $this->db->insert('incentive_policy', $inct_policy);
        return $this->db->insert_id();
    }
    public function save_incentive_model_data($policy_data){
        return $this->db->insert_batch('policy_data', $policy_data);
    }
    public function save_incentive_slabs_data($policy_slabs){
        return $this->db->insert_batch('policy_slabs', $policy_slabs);
    }
    public function get_incentive_policy_data(){
        return $this->db->get('incentive_policy')->result();
    }
    public function get_incentive_policy_data_bymonth($month, $idpcat){
        if($idpcat == 0){
            return $this->db->where('month_year', $month)
                        ->where('idproductcat = product_category.id_product_category')->from('product_category')
                        ->order_by('idproductcat')
                        ->get('incentive_policy')->result();
        }else{
            return $this->db->where('month_year', $month)
                        ->where('idproductcat', $idpcat)
                        ->where('idproductcat = product_category.id_product_category')->from('product_category')
                        ->get('incentive_policy')->result();
        }
    }
    public function get_incentive_policy_data_byid($idpolicy){
        return $this->db->where('id_incentive_policy', $idpolicy)
                        ->where('idproductcat = product_category.id_product_category')->from('product_category')
                        ->get('incentive_policy')->result();
    }
    public function get_incentive_policy_details($idpolicy){
//        $this->db->select('p.id_incentive_policy, p.policy_name,pc.product_category_name,pm.idbrand,pm.brand_name, pm.idmodel, pm.full_name,pm.category_name,pm.idcategory'); 
//        $this->db->where('p.id_incentive_policy',$idpolicy);
//        $this->db->join("(select pd.idpolicy, pd.idcategory,pd.idbrand, pd.idmodel, mv.full_name,brand.brand_name,category.category_name from policy_data pd left join model_variants mv ON mv.id_variant = pd.idmodel left join brand ON brand.id_brand = pd.idbrand left join category ON category.id_category = pd.idcategory where pd.idpolicy = $idpolicy) pm",'pm.idpolicy = p.id_incentive_policy');
//        $this->db->where('p.idproductcat = pc.id_product_category')->from('product_category pc');
//        $this->db->from('incentive_policy p');
//        $query = $this->db->get();  
//        return $query->result();
//         die(print_r($this->db->last_query()));
        
        return $this->db->select('p.idpolicy ,pc.product_category_name,c.category_name,c.id_category,brand.brand_name, brand.id_brand,mv.full_name,mv.id_variant')
                        ->where('p.idpolicy', $idpolicy)
                        ->where('p.idproductcat = pc.id_product_category')->from('product_category pc')
                        ->where('p.idcategory = c.id_category')->from('category c')
                        ->join('brand','p.idbrand = brand.id_brand','left')
                        ->join('model_variants mv','p.idmodel = mv.id_variant','left')
                        ->get('policy_data p')->result();
    }
    public function get_incentive_policy_slabs_details($idpolicy){
        return $this->db->where('idpolicy',$idpolicy)->get('policy_slabs')->result();
    }
    public function delete_policy_data($idpolicy){
        return $this->db->where('id_incentive_policy',$idpolicy)->delete('incentive_policy');
    }
    public function delete_policy_model_data($idpolicy){
        return $this->db->where('idpolicy',$idpolicy)->delete('policy_data');
    }
    public function delete_policy_slab_data($idpolicy){
        return $this->db->where('idpolicy',$idpolicy)->delete('policy_slabs');
    }
    public function get_promotor_policy_data($month, $idpcat, $idbranch, $allbranches, $idzone){
        $from = $month.'-01';
        $to = date('Y-m-t', strtotime($from));
        if($idzone != 0){
            $zones = $this->db->where('idzone', $idzone)->get('branch')->result(); 
            $allbran = '';
            foreach ($zones as $z){
                $branches[] = $z->id_branch;
                $allbran .= $z->id_branch.',';
            }
           $allbran = rtrim($allbran,',');
        }else{
            if($idbranch == 0){
                $branches = explode(',',$allbranches);
                $allbran =  $allbranches;
            }else{
                $branches[] = $idbranch;
                $allbran = $idbranch;
            }
        }

        $this->db->select('b.id_branch,b.branch_name,u.id_users, u.user_name, z.id_zone,z.zone_name,bc.id_branch_category,bc.branch_category_name,ip.id_incentive_policy,ip.policy_type,ip.cal_type,ip.policy_name,smartsp.smart_qty,smartsp.smart_amount,pol.saleqty,pol.sale_amt, sret.rsaleqty,sret.rsale_amt');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->where('u.iduserrole', 17);
//        $this->db->where('u.active', 1);
        $this->db->where_in('ip.idproductcat',$idpcat);
        $this->db->where_in('ip.month_year',$month);
        $this->db->where('u.idbranch = b.id_branch');
        $this->db->where('b.idzone= z.id_zone')->from('zone z');
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');
        $this->db->join("(select count(sp.qty) as saleqty, sum(sp.total_amount) as sale_amt,pd.idpolicy, sp.idbranch,sale.idsalesperson from policy_data pd,sale_product sp, sale where sale.id_sale = sp.idsale and pd.idproductcat = $idpcat and sp.idbranch in($allbran) and sp.date between '$from' and '$to' and "
                . "(CASE
                        WHEN pd.idmodel IS NOT NULL THEN sp.idvariant = pd.idmodel and sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        WHEN pd.idbrand IS NOT NULL and pd.idmodel IS NULL THEN sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        ELSE sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        END) GROUP BY pd.idpolicy,sp.idbranch,sale.idsalesperson) pol",'pol.idpolicy = ip.id_incentive_policy and pol.idbranch = b.id_branch and pol.idsalesperson = u.id_users','left');
        $this->db->join("(select count(sp.qty) as rsaleqty, sum(sp.total_amount) as rsale_amt,pd.idpolicy, sp.idbranch, sales_return.idsalesperson from policy_data pd,sales_return_product sp, sales_return where sales_return.id_salesreturn = sp.idsales_return and pd.idproductcat = $idpcat and sp.idbranch in($allbran) and sp.date between '$from' and '$to' and "
                . "(CASE
                        WHEN pd.idmodel IS NOT NULL THEN sp.idvariant = pd.idmodel and sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        WHEN pd.idbrand IS NOT NULL and pd.idmodel IS NULL THEN sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        ELSE sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        END) GROUP BY pd.idpolicy,sp.idbranch, sales_return.idsalesperson) sret",'sret.idpolicy = ip.id_incentive_policy and sret.idbranch = b.id_branch and sret.idsalesperson = u.id_users','left');
        
        $this->db->join("(select sum(ss.qty) as smart_qty,sum(total_amount) as smart_amount, ss.idbranch, sale.idsalesperson from sale_product ss, sale WHERE sale.id_sale = ss.idsale and ss.date between '$from' and '$to' and  ss.idcategory in(1,32) GROUP BY ss.idbranch, sale.idsalesperson) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = u.id_users', 'left');
        $this->db->order_by('z.id_zone,b.id_branch,u.id_users,ip.id_incentive_policy');
        $this->db->from('incentive_policy ip');
        $this->db->from('users u');
        $this->db->from('branch b');
        $query = $this->db->get(); 
        return $query->result();
//         die(print_r($this->db->last_query()));
    }
    public function get_branch_incentive_report_data($month,$idpcat,$idbranch,$allbranches){
        $from = $month.'-01';
        $to = date('Y-m-t', strtotime($from));
//        die(print_r($allbranches));
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
            $allbran =  $allbranches;
        }else{
            $branches[] = $idbranch;
            $allbran = $idbranch;
        }
        
        $this->db->select('z.id_zone,z.zone_name,b.id_branch,b.branch_name,bc.id_branch_category,bc.branch_category_name,smartsp.smart_qty,smartsp.smart_amount,ip.id_incentive_policy,ip.policy_type,ip.cal_type,ip.policy_name,pol.saleqty,pol.sale_amt, sret.rsaleqty,sret.rsale_amt');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->where_in('ip.idproductcat',$idpcat);
        $this->db->where_in('ip.month_year',$month);
        $this->db->join("(select count(sp.qty) as saleqty, sum(sp.total_amount) as sale_amt,pd.idpolicy, sp.idbranch from policy_data pd,sale_product sp where pd.idproductcat = $idpcat and sp.idbranch in($allbran) and sp.date between '$from' and '$to' and "
                . "(CASE
                        WHEN pd.idmodel IS NOT NULL THEN sp.idvariant = pd.idmodel and sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        WHEN pd.idbrand IS NOT NULL and pd.idmodel IS NULL THEN sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        ELSE sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        END) GROUP BY pd.idpolicy,sp.idbranch) pol",'pol.idpolicy = ip.id_incentive_policy and pol.idbranch = b.id_branch','left');
        $this->db->join("(select count(sp.qty) as rsaleqty, sum(sp.total_amount) as rsale_amt,pd.idpolicy, sp.idbranch from policy_data pd,sales_return_product sp where pd.idproductcat = $idpcat and sp.idbranch in($allbran) and sp.date between '$from' and '$to' and "
                . "(CASE
                        WHEN pd.idmodel IS NOT NULL THEN sp.idvariant = pd.idmodel and sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        WHEN pd.idbrand IS NOT NULL and pd.idmodel IS NULL THEN sp.idbrand = pd.idbrand and sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        ELSE sp.idproductcategory = pd.idproductcat and sp.idcategory = pd.idcategory
                        END) GROUP BY pd.idpolicy,sp.idbranch) sret",'sret.idpolicy = ip.id_incentive_policy and sret.idbranch = b.id_branch','left');
        $this->db->join("(select sum(ss.qty) as smart_qty,sum(total_amount) as smart_amount, ss.idbranch from sale_product ss WHERE ss.date between '$from' and '$to' and  ss.idcategory in(1,32) GROUP BY ss.idbranch) smartsp", 'smartsp.idbranch = b.id_branch', 'left');
        $this->db->order_by('z.id_zone,b.id_branch,ip.id_incentive_policy');
        $this->db->where('b.idzone= z.id_zone')->from('zone z');
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');
        $this->db->from('incentive_policy ip');
        $this->db->from('branch b');
        $query = $this->db->get(); 
        return $query->result();
//         die(print_r($this->db->last_query()));
    }
    public function get_policy_slabs_data_byidpolicy($id_incentive_policy){
        return $this->db->where('idpolicy', $id_incentive_policy)->get('policy_slabs')->result();
    }
    
    public function get_policy_report_data_byidpcat($month, $idproduct){
        if($idproduct == 0){
            return $this->db->select('ip.*,pc.product_category_name')
                        ->where('ip.month_year', $month)
                        ->where('ip.idproductcat = pc.id_product_category')->from('product_category pc')
                        ->group_by('ip.id_incentive_policy')
                        ->get('incentive_policy ip')->result();
        }else{
             return $this->db->select('ip.*,pc.product_category_name')
                        ->where('ip.month_year', $month)
                        ->where('ip.idproductcat', $idproduct)
                        ->where('ip.idproductcat = pc.id_product_category')->from('product_category pc')
                        ->group_by('ip.id_incentive_policy')
                        ->get('incentive_policy ip')->result();
        }
    }
}
?>