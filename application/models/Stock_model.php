<?php

class Stock_model extends CI_Model {
    
    public function get_all_branch_stocknorms($product_category,$days=30) {
        $pids = array();
        foreach ($product_category as $pid) {
            $pids[] = $pid->id_product_category;
        }
        $to='2020-02-10';//date('Y-m-d');
        $day='-'.$days.' days';
        $p_ids=implode(',', $pids);
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        $this->db->select('z.zone_name,`b`.`id_branch`, `b`.`branch_name` ,`bc`.`branch_category_name`,count(mv.id_variant) as all_models, stk.stock_qty,sp.sale_qty,sn.norm_qty,sn.setup_cnt');
        $this->db->from('branch b');                
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE st.idproductcategory in ('.$p_ids.') GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(sn.quantity) as norm_qty,count(sn.idbranch) as setup_cnt,sn.idbranch from stock_norms sn WHERE sn.idproductcategory in ('.$p_ids.') GROUP BY sn.idbranch) sn','`sn`.`idbranch`=`b`.`id_branch`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idproductcategory in (".$p_ids.") and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch=b.id_branch', 'left');                
        $this->db->where('mv.active', 1)->where_in('mv.idproductcategory',$p_ids)->from('model_variants mv');
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');   
        $this->db->where('z.id_zone=b.idzone')->from('zone z'); 
        $this->db->where('b.is_warehouse', 0);
        $this->db->where('b.active', 1);
        $this->db->order_by('z.id_zone,b.id_branch');
        $this->db->group_by('b.id_branch'); 
        $query = $this->db->get();
//        return $this->db->last_query();
        return $query->result();
    }
	
     public function get_branch_stocknorms($branch,$product_category,$days, $allbranches, $idzone) {         
        
        if($idzone == ''){
            if($branch == 0 ){
                $branches = explode(',',$allbranches);
            }else{
                $branches[] = $branch;
            }
        }else{
            $badts = $this->db->where('idzone', $idzone)->get('branch')->result();
            foreach($badts as $bts){
                $branches[] = $bts->id_branch;
            }
        }
          $strbr = implode(',', $branches);
         
        $to=date('Y-m-d');
        $day='-'.$days.' days';          
        $from=date('Y-m-d', strtotime("$day", strtotime($to))); 
//        die($from.'-'.$to);
        
        $this->db->select('b.id_branch, `b`.`branch_name`, z.id_zone,z.zone_name ,`bc`.`branch_category_name`, stk.stock_qty, sp.sale_qty, sn.norm_qty, instk.intra_qty');
        $this->db->where_in('b.id_branch',$branches);
        $this->db->join('(select sum(st.qty) as stock_qty, st.idbranch from stock st WHERE st.idbranch in('.$strbr.') and  st.idproductcategory in('.$product_category.') and st.idgodown in(1,6) GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(st.qty) as intra_qty, st.temp_idbranch from stock st WHERE st.temp_idbranch in('.$strbr.') and  st.idproductcategory in('.$product_category.') and st.idgodown in(1,6) GROUP BY st.temp_idbranch) instk','`instk`.`temp_idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(sn.quantity) as norm_qty,sn.idbranch from stock_norms sn WHERE sn.idbranch in('.$strbr.') and sn.idproductcategory in('.$product_category.') GROUP BY sn.idbranch) sn','`sn`.`idbranch`=`b`.`id_branch`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idbranch in($strbr) and s.idproductcategory in (".$product_category.") and s.date between '$from' and '$to' and s.idgodown in(1,6) GROUP BY s.idbranch) sp", 'sp.idbranch=b.id_branch', 'left');                
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');
        $this->db->where('b.idzone = z.id_zone');
        $this->db->order_by('z.id_zone,b.id_branch');
        $this->db->from('zone z');
        $this->db->from('branch b');
        $query = $this->db->get();
//        return $this->db->last_query();
        return $query->result();
        
        
        
        
    }
    
    
   /* public function get_branch_stocknorms($branch,$product_category,$days=40) {         
        $pids = array();
        foreach ($product_category as $pid) {
            $pids[] = $pid->id_product_category;
        }
        $to='2020-02-10';//date('Y-m-d');
        $day='-'.$days.' days';          
        $p_ids=implode(',', $pids);
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));        
        $this->db->select('brd.id_brand,brd.brand_name,`b`.`id_branch`, `b`.`branch_name` ,`bc`.`branch_category_name`,mv.all_models, stk.stock_qty,sp.sale_qty,sn.norm_qty,sn.setup_cnt');
        $this->db->from('brand brd');    
        $this->db->where('b.id_branch',$branch)->from('branch b');
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbrand from stock st WHERE st.idbranch='.$branch.' and  st.idproductcategory in ('.$p_ids.') GROUP BY st.idbrand) stk','`stk`.`idbrand`=`brd`.`id_brand`','left');         
        $this->db->join('(select sum(sn.quantity) as norm_qty,count(sn.idbranch) as setup_cnt,sn.idbrand from stock_norms sn WHERE sn.idbranch='.$branch.' and sn.idproductcategory in ('.$p_ids.') GROUP BY sn.idbrand) sn','`sn`.`idbrand`=`brd`.`id_brand`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s WHERE s.idbranch='.$branch.' and s.idproductcategory in (".$p_ids.") and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=brd.id_brand', 'left');                
        $this->db->join("(select count(m.id_variant) as all_models,m.idbrand from model_variants m WHERE  m.idproductcategory in (".$p_ids.")  GROUP BY m.idbrand) mv", 'mv.idbrand=brd.id_brand', 'left');                        
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');
        $this->db->where('brd.active', 1);
        $this->db->group_by('brd.id_brand');
        $query = $this->db->get();
//        return $this->db->last_query();
        return $query->result();
    }*/

    public function get_branch_modelstocknorms_by_PCB($category, $brand, $product_category, $branch,$days) {

        $to='2020-02-10';//date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        if($brand){
            $where = array('mv.idproductcategory' => $product_category, 'mv.idbrand' => $brand);
            if ($category > 0) {
                $where = array('mv.idproductcategory' => $product_category, 'mv.idbrand' => $brand, 'mv.idcategory' => $category);
            }            
        }else{
            $where = array('mv.idproductcategory' => $product_category);
            if ($category > 0) {
                $where = array('mv.idproductcategory' => $product_category, 'mv.idcategory' => $category);
            }
        }
        $this->db->select('bc.branch_category_name,sp.sale_qty,sum(stk.qty) as stock_qty,pc.product_category_name,c.category_name,brd.brand_name,mv.id_variant,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,sn.quantity,b.id_branch,b.branch_name');
        $this->db->from('model_variants mv');        
        $wh = 'sn.idvariant=mv.id_variant and sn.idbranch='. $branch;
        $this->db->join('stock_norms sn', $wh, 'left');        
        $whh = 'stk.idvariant=mv.id_variant and (stk.idbranch=' . $branch.' or stk.temp_idbranch='. $branch.')';
        $this->db->join('stock stk', $whh, 'left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idvariant from sale_product s WHERE  s.idproductcategory='.$product_category.' and  s.idbranch=$branch and s.date between '$from' and '$to' GROUP BY s.idvariant) sp", 'sp.idvariant=mv.id_variant', 'left');        
        $this->db->join('product_category pc', 'mv.idproductcategory=pc.id_product_category');
        $this->db->join('category c', 'mv.idcategory=c.id_category');
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where('b.id_branch', $branch)->from('branch b');
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');
        $this->db->where($where);
        $this->db->group_by('mv.id_variant');
        $this->db->where('mv.active', 1);
        $query = $this->db->get();
//        return $this->db->last_query();
        return $query->result();
    }
    public function save_db_branch_stocknorms($idbranch, $id_vatriant, $data_att) {
        $this->db->trans_begin();
        $this->db->where_in('idvariant', $id_vatriant)->where('idbranch', $idbranch)->delete('stock_norms');
        $this->db->insert_batch('stock_norms', $data_att);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
    
     public function get_branch_modelstocknorms($product_category,$branch,$days) {

        $pids = array();
        foreach ($product_category as $pid) {
            $pids[] = $pid->id_product_category;
        }
        $p_ids=implode(',', $pids);
        $to='2020-02-10';//date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));        
        $this->db->select('bc.branch_category_name,sp.sale_qty,sum(stk.qty) as stock_qty,pc.product_category_name,c.category_name,brd.brand_name,mv.id_variant,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,sn.quantity,b.id_branch,b.branch_name');
        $this->db->from('model_variants mv');        
        $wh = 'sn.idvariant=mv.id_variant and sn.idbranch=' . $branch;
        $this->db->join('stock_norms sn', $wh, 'left');        
        $whh = 'stk.idvariant=mv.id_variant and (stk.idbranch=' . $branch.' or stk.temp_idbranch='. $branch.')';
        $this->db->join('stock stk', $whh, 'left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idvariant from sale_product s WHERE s.idproductcategory in (".$p_ids.") and  s.idbranch=$branch and s.date between '$from' and '$to' GROUP BY s.idvariant) sp", 'sp.idvariant=mv.id_variant', 'left');        
        $this->db->join('product_category pc', 'mv.idproductcategory=pc.id_product_category');
        $this->db->join('category c', 'mv.idcategory=c.id_category');
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where('b.id_branch', $branch)->from('branch b');
        $this->db->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc');        
        $this->db->where_in('mv.idproductcategory',$p_ids);
        $this->db->group_by('mv.id_variant');
        $this->db->where('mv.active', 1);
        $query = $this->db->get();
//        return $this->db->last_query();
        return $query->result();
    }
    
    public function get_stock_by_variant_branch($idvariant,$idbranch) {
        return $this->db->select('SUM(qty) as sum_qty')
                        ->where('idvariant', $idvariant)
                        ->where('idbranch', $idbranch)
                        ->get('stock')->row();
    }
    public function get_all_branch_stock_by_variant($idvariant) {
        return $this->db->select('SUM(qty) as sum_qty')
                        ->where('idvariant', $idvariant)
                        ->where('branch.is_warehouse = 0')
                        ->where('stock.idbranch = branch.id_branch')->from('branch')
                        ->get('stock')->row();
    }
    public function get_all_intransit_stock_by_variant($idvariant) {
        return $this->db->select('SUM(qty) as sum_qty')
                        ->where('idvariant', $idvariant)
                        ->where('idbranch = 0') // intrasit
                        ->get('stock')->row();
    }
    public function get_all_branch_sale_qty_by_variant($idvariant) {
        return $this->db->select('SUM(qty) as sum_qty')
                        ->where('idvariant', $idvariant)
                        ->get('sale_product')->row();
    }
    
    public function get_quantity_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs) {
              
        $this->db->select('pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,g.godown_name');
        $this->db->from('model_variants mv');        
        $whr=" ";
        $whr1=" ";
        $whrr=" ";
        $whrr1=" ";        
        if($idproductcategory){
            $whr.=' and st.idproductcategory='.$idproductcategory.' ';
            $whr1.=' and stm.idproductcategory='.$idproductcategory.' ';
            
            $whrr.='  st.idproductcategory='.$idproductcategory.' ';
            $whrr1.=' stm.idproductcategory='.$idproductcategory.' ';
            $this->db->where('mv.idproductcategory', $idproductcategory);
        }
        if($idbrand){
            if($idproductcategory){
                $whrr.=' and st.idbrand='.$idbrand.' ';
                $whrr1.=' and stm.idbrand='.$idbrand.' ';
                
                $whr.=' and st.idbrand='.$idbrand.' ';
                $whr1.=' and stm.idbrand='.$idbrand.' ';
            }else{
                $whrr.=' st.idbrand='.$idbrand.' ';
                $whrr1.=' stm.idbrand='.$idbrand.' ';
                
                $whr.=' st.idbrand='.$idbrand.' ';
                $whr1.=' stm.idbrand='.$idbrand.' ';
            }
             $this->db->where('mv.idbrand', $idbrand);   
        }
        if($idgodown && $idbranch)
        {
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$idbranch.' '.$whr.' GROUP BY st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.' and stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif(!$idgodown && $idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idbranch='.$idbranch.'  '.$whr.'  GROUP BY st.idvariant,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE    stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif($idgodown && !$idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.'  '.$whr.'  GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.'  '.$whr1.'  GROUP BY stm.idvariant,stm.temp_idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }else{
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant ,st.idgodown from stock st WHERE '.$whrr.' GROUP BY st.idvariant,st.idbranch,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE '.$whrr1.' GROUP BY stm.idvariant,stm.temp_idbranch,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }
        if($idgodown){
            $this->db->where('g.id_godown', $idgodown);
        }
        if($idbranch){
              $this->db->where('b.id_branch', $idbranch);
        }else{
            if(count($idbranchs)>0){                
                   $this->db->where_in('b.id_branch',$idbranchs);
            }
        }        
        $this->db->where('mv.active', 1)->from('branch b');         
        $this->db->where('g.active', 1)->from('godown g'); 
        $this->db->join('product_category pc', 'mv.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where('b.is_warehouse', $iswarehouse);                    
        $this->db->where('b.active', 1);
        $this->db->order_by('b.id_branch,pc.product_category_name,brd.brand_name,stk.stock_qty desc');
        //$this->db->order_by('mv.id_variant,b.id_branch');
        $this->db->group_by('mv.id_variant,b.id_branch,g.id_godown');
        $query = $this->db->get(); 
        return $query->result();
//        $query->result();
//        die($this->db->last_query());
    } 
    
    public function get_imei_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs,$type=1) {              
        $this->db->select('c.category_name,doa.status as doa_status,st.idgodown,st.doa_return_type,st.idbranch,st.temp_idbranch,st.imei_no,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, st.qty,g.godown_name,st.outward_time,st.transfer_time,st.date');
        $this->db->from('stock st'); 
        if($type==1){
            $this->db->join('branch b', 'st.idbranch=b.id_branch','left');    /// for current stock
        }else{        
            $this->db->join('branch b', 'st.temp_idbranch=b.id_branch','left'); /// for in transit stock        
        }
        if($idproductcategory){
            $this->db->where('st.idproductcategory', $idproductcategory);
        }
        if($idbrand){
             $this->db->where('st.idbrand', $idbrand);   
        }        
        if($idgodown){
            $this->db->where('st.idgodown', $idgodown); 
        }        
        if($idbranch){
             if($type==1){
                $this->db->where('st.idbranch='.$idbranch);
             }else{
                $this->db->where('st.temp_idbranch='.$idbranch);
             }
        }else{
            if(count($idbranchs)>0){
                if($type==1){
                   $this->db->where_in('st.idbranch',$idbranchs);
                }else{
                   $this->db->where_in('st.temp_idbranch',$idbranchs);
                }
            }
        }
        $this->db->where('g.active', 1); 
        $this->db->join('godown g', 'st.idgodown=g.id_godown');
        $this->db->join('model_variants mv', 'st.idvariant=mv.id_variant');        
        $this->db->join('category c', 'st.idcategory=c.id_category');
        $this->db->join('product_category pc', 'st.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'st.idbrand=brd.id_brand');
        $this->db->join('doa_reconciliation doa', 'doa.imei_no=st.imei_no','left');
        $this->db->where('b.is_warehouse', $iswarehouse);  
        $this->db->where('b.active', 1);
        $this->db->order_by('st.idbrand,b.id_branch');
        $query = $this->db->get(); 
//        die($this->db->last_query());
        return $query->result();
    } 
    
    public function ajax_check_valid_barcode($imei, $idvariant, $idbranch,$idgodown){
         $this->db->where('imei_no', $imei)
                        ->where('idvariant', $idvariant)
                        ->where('idbranch', $idbranch);
                        if($idgodown==3){
                            $this->db->where('doa_return_type != 3');
                        }
        return $this->db->get('stock')->row();   
    }
    
     public function get_branch_stock_by_variant($variantid,$idgodown,$idwarehouse, $level) {                        
        
        $idbranches = array();
        if($level == 2){
             $idbranch = $_SESSION['idbranch'];
             $zones = $this->db->select('idzone')->where('id_branch', $idbranch)->get('branch')->row();
             $idzone = $zones->idzone;
             $branches = $this->db->where('idzone', $idzone)->get('branch')->result();
            
             
        }else{
//            if($level == 3){
//                $branches = $this->db->select('idbranch as id_branch')->where('iduser', $_SESSION['id_users'])->get('user_has_branch')->result();
//            }if($level == 1){
                $branches = $this->db->get('branch')->result();
//            }
        }
        foreach($branches as $bran){
            $idbranches[] = $bran->id_branch;
        }
        
        $this->db->select('z.zone_name,mv.id_variant,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty');
        $this->db->where_in('b.id_branch', $idbranches);
        $this->db->from('branch b');                
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch from stock stm WHERE  stm.idgodown='.$idgodown.' and  stm.idvariant='.$variantid.' GROUP BY stm.temp_idbranch) stkm','`stkm`.`temp_idbranch`=`b`.`id_branch`','left');         
        if($idwarehouse > 0){
            $this->db->where('(b.idwarehouse= '.$idwarehouse.' or b.is_warehouse=1)');
        }elseif($idwarehouse == -1){
            $this->db->where('b.is_warehouse=0');        
        }
        $this->db->where('mv.id_variant',$variantid)->from('model_variants mv');        
        $this->db->where('b.active', 1);
        $this->db->where('z.id_zone=b.idzone')->from('zone z'); 
        $this->db->order_by('b.is_warehouse,z.id_zone,b.id_branch');
        $this->db->group_by('b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
//        die(print_r($this->db->last_query()));
    }
    public function get_store_stocsk_analysis($idgodown,$idbranch,$days,$idbrand,$idproductcategory) {
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));          
        $this->db->select('mv.id_variant,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sp.sale_qty');
        $this->db->from('branch b');                
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch from stock stm WHERE  stm.idgodown='.$idgodown.' and  stm.idvariant='.$variantid.' GROUP BY stm.temp_idbranch) stkm','`stkm`.`temp_idbranch`=`b`.`id_branch`','left');             
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idvariant=$variantid and s.idgodown=$idgodown and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch=b.id_branch', 'left');                        
        $this->db->where('mv.active', 1)->where('mv.id_variant',$variantid)->from('model_variants mv');        
        $this->db->where('b.is_warehouse', 0);        
        $this->db->where('b.id_branch', $idbranch);    
        $this->db->order_by('b.id_branch');
        $this->db->group_by('b.id_branch');
        $query = $this->db->get(); 
//        die(print_r($this->db->last_query()));
        return $query->result();
    }
    
    public function get_warehouse_stock_by_PBG($idproductcategory,$idbrand,$idgodown,$warehouse) {                             
        $this->db->select('mv.*,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk_ho.ho_stock_qty');
        $this->db->from('model_variants mv');        
        $w="";
        if($idbrand){
            $w="and st.idbrand=".$idbrand." ";
            $this->db->where('mv.idbrand', $idbrand);        
        }
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and  st.idproductcategory='.$idproductcategory.' '.$w.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                         
        $this->db->where('mv.idproductcategory', $idproductcategory);
        $this->db->where('mv.active', 1)->from('branch b');        
        $this->db->where('b.id_branch', $warehouse);                
        $this->db->where('b.active', 1);
        $this->db->order_by('b.id_branch');
        $this->db->group_by('b.id_branch,mv.id_variant');
        $query = $this->db->get(); 
//        $query->result();
//        die(print_r($this->db->last_query()));
        return $query->result(); 
    }
    public function get_store_stock_analysis($idgodown,$idbranch,$idbranches,$days,$idbrand,$idproductcategory,$type,$datefrom='',$dateto='') {
        if($datefrom==''){
            $to=date('Y-m-d');
            $day='-'.$days.' days';                 
            $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        }else{
            $to=$dateto;                             
            $from=$datefrom;                   
        }       
        $this->db->from('model_variants mv');    
        $whr=" ";
        $whr1=" ";
        $whrr=" ";          
        $idbranchs='';
        if(count($idbranches)>0){
           $idbranchs= implode(',', $idbranches);           
        }
        if($idproductcategory){
            $whr.=' and st.idproductcategory='.$idproductcategory.' ';
            $whr1.=' and stm.idproductcategory='.$idproductcategory.' ';
            $whrr.='  and s.idproductcategory='.$idproductcategory.' ';            
            $this->db->where('pc.id_product_category', $idproductcategory)->from('product_category pc');            
        }
        if($idbrand){            
                $whr.=' and st.idbrand='.$idbrand.' ';
                $whr1.=' and stm.idbrand='.$idbrand.' ';
                $whrr.=' and s.idbrand='.$idbrand.' ';
                $this->db->where('brd.id_brand', $idbrand);             
        }
        $this->db->where('brd.id_brand = mv.idbrand')->from('brand brd');
        $this->db->where('pc.id_product_category = mv.idproductcategory');
        
        if($idbranch > 0){
            $this->db->select('z.zone_name,bc.branch_category_name,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,g.godown_name,sp.sale_qty,pc.id_product_category,brd.id_brand,mv.idmodel,b.id_branch');
            $this->db->where('b.id_branch', $idbranch);        
            $this->db->join('(select sum(s.qty) as sale_qty,s.idbranch,s.idvariant from sale_product s WHERE s.idbranch='.$idbranch.' and s.idgodown='.$idgodown.' and s.date between "'.$from.'" and "'.$to.'" '.$whrr.' GROUP BY s.idvariant) sp', 'sp.idbranch=b.id_branch and sp.idvariant = mv.id_variant', 'left');
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant from stock st WHERE st.idbranch='.$idbranch.' and st.idgodown='.$idgodown.' '.$whr.' GROUP BY st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant from stock stm WHERE stm.temp_idbranch='.$idbranch.' and stm.idgodown='.$idgodown.' '.$whr1.'  GROUP BY stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->group_by('mv.id_variant,g.id_godown');
            $this->db->order_by('brd.id_brand,mv.id_variant');
        }elseif(count($idbranches)>0 && $type=='warehouse'){
            $this->db->select(' "" as  zone_name,"" as branch_category_name,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, "" as branch_name, sum(stk.stockqty) as stock_qty,sum(stkm.intrastockqty) as intra_stock_qty,g.godown_name,sum(sp.saleqty) as sale_qty,pc.id_product_category,brd.id_brand,mv.idmodel,b.id_branch');
            $this->db->where_in('b.id_branch', $idbranches);        
            $this->db->join('(select sum(s.qty) as saleqty,s.idbranch,s.idvariant from sale_product s WHERE s.idbranch in ('.$idbranchs.') and s.idgodown='.$idgodown.' and s.date between "'.$from.'" and "'.$to.'" '.$whrr.' GROUP BY s.idvariant) sp', 'sp.idbranch=b.id_branch and sp.idvariant = mv.id_variant', 'left');
            $this->db->join('(select sum(st.qty) as stockqty,st.idbranch,st.idvariant from stock st WHERE st.idbranch in ('.$idbranchs.') and st.idgodown='.$idgodown.' '.$whr.' GROUP BY st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->join('(select sum(stm.qty) as intrastockqty,stm.temp_idbranch,stm.idvariant from stock stm WHERE stm.temp_idbranch in ('.$idbranchs.') and stm.idgodown='.$idgodown.' '.$whr1.'  GROUP BY stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->group_by('mv.id_variant,g.id_godown');
            $this->db->order_by('brd.id_brand,mv.id_variant');
        }else{
            $this->db->select('z.zone_name,bc.branch_category_name,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, sum(stk.stockqty) as stock_qty,sum(stkm.intrastockqty) as intra_stock_qty,g.godown_name,sum(sp.saleqty) as sale_qty,pc.id_product_category,brd.id_brand,mv.idmodel,b.id_branch');
            $this->db->where_in('b.id_branch', $idbranches);        
            $this->db->join('(select sum(s.qty) as saleqty,s.idbranch,s.idvariant from sale_product s WHERE s.idbranch in ('.$idbranchs.') and s.idgodown='.$idgodown.' and s.date between "'.$from.'" and "'.$to.'" '.$whrr.' GROUP BY s.idbranch,s.idvariant) sp', 'sp.idbranch=b.id_branch and sp.idvariant = mv.id_variant', 'left');
            $this->db->join('(select sum(st.qty) as stockqty,st.idbranch,st.idvariant from stock st WHERE st.idbranch in ('.$idbranchs.') and st.idgodown='.$idgodown.' '.$whr.' GROUP BY st.idbranch,st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->join('(select sum(stm.qty) as intrastockqty,stm.temp_idbranch,stm.idvariant from stock stm WHERE stm.temp_idbranch in ('.$idbranchs.') and stm.idgodown='.$idgodown.' '.$whr1.'  GROUP BY stm.temp_idbranch,stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');
            $this->db->group_by('b.id_branch,mv.id_variant,g.id_godown');
            $this->db->order_by('z.zone_name,b.branch_name,brd.id_brand,mv.id_variant');
        }
        $this->db->where('g.id_godown', $idgodown);  
        $this->db->where('mv.active', 1);
        $this->db->where('b.active', 1)->where('b.is_warehouse', 0)->from('branch b');         
        $this->db->where('g.active', 1)->from('godown g');         
        $this->db->where('z.id_zone=b.idzone')->where('z.active', 1)->from('zone z'); 
        $this->db->where('bc.id_branch_category=b.idbranchcategory')->where('bc.active', 1)->from('branch_category bc');                 
       
        $query = $this->db->get();         
        return $query->result();
//        $query->result();
//        die($this->db->last_query());
    } 
    
   public function get_sale_stock_analysis($idgodown,$from,$to,$idbrand,$idproductcategory,$idcategory) {        
        $warehouses=$this->db->where('is_warehouse', 1)->where('active', 1)->get('branch')->result();        
//        $to=date('Y-m-d');
//        $day='-'.$days.' days';                 
//        $from=date('Y-m-d', strtotime("$day", strtotime($to)));           
        $this->db->where('mv.active', 1)->from('model_variants mv');    
        $whr=" ";
        $whr1=" ";
        $whrr=" ";    
        if($idproductcategory){
            $whr.=' and st.idproductcategory='.$idproductcategory.' ';
            $whr1.=' and stm.idproductcategory='.$idproductcategory.' ';
            $whrr.='  and s.idproductcategory='.$idproductcategory.' ';            
            $this->db->where('pc.id_product_category', $idproductcategory)->from('product_category pc');            
        }
        if($idbrand){            
                $whr.=' and st.idbrand='.$idbrand.' ';
                $whr1.=' and stm.idbrand='.$idbrand.' ';
                $whrr.=' and s.idbrand='.$idbrand.' ';
                $this->db->where('brd.id_brand', $idbrand);             
        }
        if($idcategory){            
                $whr.=' and st.idcategory='.$idcategory.' ';
                $whr1.=' and stm.idcategory='.$idcategory.' ';
                $whrr.=' and s.idcategory='.$idcategory.' ';
                $this->db->where('c.id_category', $idcategory);             
        }
        $this->db->where('c.id_category = mv.idcategory')->from('category c');
        $this->db->where('brd.id_brand = mv.idbrand')->from('brand brd');
        $this->db->where('pc.id_product_category = mv.idproductcategory');
        
        $i=0;
        $w_ds="";
        foreach ($warehouses as $warehouse){
           $this->db->join('(select sum(st.qty) as warehouse'.$i.',st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and (st.idbranch='.$warehouse->id_branch.' or st.temp_idbranch='.$warehouse->id_branch.') '.$whr.' GROUP BY st.idvariant) stk_how'.$i,'stk_how'.$i.'.idvariant = `mv`.`id_variant`','left');                                         
           $this->db->select('stk_how'.$i.'.warehouse'.$i);
           $i++;  
           $w_ds.=$warehouse->id_branch.',';
        }        
        $w_ds=rtrim($w_ds,',');
        $this->db->select('c.category_name,pc.product_category_name,brd.brand_name,mv.landing,mv.id_variant,mv.full_name, sum(stk.stockqty) as stock_qty,sum(stkm.intrastockqty) as intra_stock_qty,g.godown_name,sum(sp.saleqty) as sale_qty');                  
        $this->db->join('(select sum(s.qty) as saleqty,s.idbranch,s.idvariant from sale_product s WHERE  s.idgodown='.$idgodown.' and s.date between "'.$from.'" and "'.$to.'" '.$whrr.' GROUP BY s.idvariant) sp', 'sp.idbranch=b.id_branch and sp.idvariant = mv.id_variant', 'left');
        $this->db->join('(select sum(st.qty) as stockqty,st.idbranch,st.idvariant from stock st WHERE st.idbranch not in ('.$w_ds.') and  st.idgodown='.$idgodown.' '.$whr.' GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');
        $this->db->join('(select sum(stm.qty) as intrastockqty,stm.temp_idbranch,stm.idvariant from stock stm WHERE stm.temp_idbranch not in ('.$w_ds.') and  stm.idgodown='.$idgodown.' '.$whr1.'  GROUP BY stm.idvariant,stm.idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');
        $this->db->group_by('mv.id_variant,g.id_godown');
        $this->db->order_by('brd.id_brand,mv.id_variant');
        $this->db->where('g.id_godown', $idgodown);          
        $this->db->where('b.active', 1)->from('branch b');         //->where('b.is_warehouse', 0) removed due to warehouse sale not counted into report
        $this->db->where('g.active', 1)->from('godown g');       
        $query = $this->db->get();         
        return $query->result();
//        $query->result();
//        die($this->db->last_query());
    } 
     public function save_ageing_store_stock($data){
        return $this->db->insert('ageing_stock', $data);
    }
    public function remove_ageing_store_stock_byidvariant($idvariant){
//         die(print_r($idvariant));
        return $this->db->where_in('idvariant',$idvariant)->delete('ageing_stock');
    }
    public function remove_ageing_store_stock($idproductcategory, $idbrand, $idmodel,$idvariant, $idbranch){
        return $this->db->where('idproductcategory',$idproductcategory)
                        ->where('idbrand',$idbrand)
                        ->where('idmodel',$idmodel)
                        ->where('idvariant',$idvariant)
                        ->where('idbranch',$idbranch)
                        ->delete('ageing_stock');
    }
    
    public function get_ageing_stock_data($idproductcategory, $idbrand, $idmodel,$idvariant, $idbranch){
        return $this->db->where('idproductcategory',$idproductcategory)
                        ->where('idbrand',$idbrand)
                        ->where('idmodel',$idmodel)
                        ->where('idvariant',$idvariant)
                        ->where('idbranch',$idbranch)
                        ->get('ageing_stock')->row();
    }
     public function get_agening_branch_stock_by_variant($variantid,$idgodown,$idwarehouse,$days) { 
        $to = date('Y-m-d');
        $day = '-'.$days.' days';                 
        $from = date('Y-m-d', strtotime("$day", strtotime($to)));  
        
        $this->db->select('z.zone_name,mv.id_variant,mv.idcategory,mv.full_name,mv.idmodel,mv.idproductcategory,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sp.sale_qty');
        $this->db->from('branch b');    
        $this->db->join('(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idgodown='.$idgodown.' and  s.idvariant='.$variantid.' and s.date between "'.$from.'" and "'.$to.'"  GROUP BY s.idbranch) sp', 'sp.idbranch=b.id_branch', 'left');
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch from stock stm WHERE  stm.idgodown='.$idgodown.' and  stm.idvariant='.$variantid.' GROUP BY stm.temp_idbranch) stkm','`stkm`.`temp_idbranch`=`b`.`id_branch`','left');         
        if($idwarehouse){
            $this->db->where('(b.idwarehouse= '.$idwarehouse.' or b.is_warehouse=1)');
        }
        $this->db->where('mv.id_variant',$variantid)->from('model_variants mv');        
        $this->db->where('b.active', 1);
        $this->db->where('z.id_zone=b.idzone')->from('zone z'); 
        $this->db->order_by('b.is_warehouse,z.id_zone,b.id_branch');
        $this->db->group_by('b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
//        die(print_r($this->db->last_query()));
    }
    public function ajax_get_ageing_stock_data($idbrand, $idproductcategory, $idbranch, $allbranch, $allbrand, $allpcat){
        if($idbranch == 0){
            $branches = explode(',',$allbranch);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idbrand == 0){
            $brands = explode(',',$allbrand);
        }else{
            $brands[] = $idbrand;
        }
        
        if($idproductcategory == 0){
            $pcats = explode(',',$allpcat);
        }else{
            $pcats[] = $idproductcategory;
        }
        return $this->db->where_in('ageing_stock.idproductcategory', $pcats)
                        ->where_in('ageing_stock.idbrand', $brands)
                        ->where_in('ageing_stock.idbranch', $branches)
                        ->where('ageing_stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('ageing_stock.idbrand = brand.id_brand')->from('brand')
                        ->where('ageing_stock.idbranch = branch.id_branch')->from('branch')
                        ->where('ageing_stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('ageing_stock')->result();
                        
    }
    
     public function get_ageing_quantity_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs) {
              
        $this->db->select('pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,g.godown_name');
        $this->db->from('model_variants mv');        
        $whr=" ";
        $whr1=" ";
        $whrr=" ";
        $whrr1=" ";        
        if($idproductcategory){
            $whr.=' and st.idproductcategory='.$idproductcategory.' ';
            $whr1.=' and stm.idproductcategory='.$idproductcategory.' ';
            
            $whrr.='  st.idproductcategory='.$idproductcategory.' ';
            $whrr1.=' stm.idproductcategory='.$idproductcategory.' ';
            $this->db->where('mv.idproductcategory', $idproductcategory);
        }
        if($idbrand){
            if($idproductcategory){
                $whrr.=' and st.idbrand='.$idbrand.' ';
                $whrr1.=' and stm.idbrand='.$idbrand.' ';
                
                $whr.=' and st.idbrand='.$idbrand.' ';
                $whr1.=' and stm.idbrand='.$idbrand.' ';
            }else{
                $whrr.=' st.idbrand='.$idbrand.' ';
                $whrr1.=' stm.idbrand='.$idbrand.' ';
                
                $whr.=' st.idbrand='.$idbrand.' ';
                $whr1.=' stm.idbrand='.$idbrand.' ';
            }
             $this->db->where('mv.idbrand', $idbrand);   
        }
        if($idgodown && $idbranch)
        {
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$idbranch.' '.$whr.' GROUP BY st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.' and stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif(!$idgodown && $idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idbranch='.$idbranch.'  '.$whr.'  GROUP BY st.idvariant,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE    stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif($idgodown && !$idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.'  '.$whr.'  GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.'  '.$whr1.'  GROUP BY stm.idvariant,stm.temp_idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }else{
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant ,st.idgodown from stock st WHERE '.$whrr.' GROUP BY st.idvariant,st.idbranch,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE '.$whrr1.' GROUP BY stm.idvariant,stm.temp_idbranch,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }
        if($idgodown){
            $this->db->where('g.id_godown', $idgodown);
        }
        if($idbranch){
              $this->db->where('b.id_branch', $idbranch);
        }else{
            if(count($idbranchs)>0){                
                   $this->db->where_in('b.id_branch',$idbranchs);
            }
        }        
        $this->db->where('mv.active', 1)->from('branch b');         
        $this->db->where('g.active', 1)->from('godown g'); 
        $this->db->join('product_category pc', 'mv.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where('b.is_warehouse', $iswarehouse);                    
        $this->db->where('b.active', 1);
        $this->db->where('pc.id_product_category = ageing_stock.idproductcategory');
        $this->db->where('mv.id_variant = ageing_stock.idvariant');
        $this->db->where('b.id_branch = ageing_stock.idbranch');
        $this->db->where('brd.id_brand = ageing_stock.idbrand')->from('ageing_stock'); 
        $this->db->order_by('mv.id_variant,b.id_branch');
        $this->db->group_by('mv.id_variant,b.id_branch,g.id_godown');
        $query = $this->db->get(); 
         $query->result();
       return  $query->result();
//        die($this->db->last_query());
    } 
    
    public function get_ageing_imei_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs,$type=1) {              
        $this->db->select('st.idbranch,st.temp_idbranch,st.imei_no,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, st.qty,g.godown_name,st.outward_time,st.transfer_time,st.date');
        $this->db->from('stock st'); 
        if($type==1){
            $this->db->join('branch b', 'st.idbranch=b.id_branch','left');    /// for current stock
        }else{        
            $this->db->join('branch b', 'st.temp_idbranch=b.id_branch','left'); /// for in transit stock        
        }
        if($idproductcategory){
            $this->db->where('st.idproductcategory', $idproductcategory);
        }
        if($idbrand){
             $this->db->where('st.idbrand', $idbrand);   
        }        
        if($idgodown){
            $this->db->where('st.idgodown', $idgodown); 
        }        
        if($idbranch){
             if($type==1){
                $this->db->where('st.idbranch='.$idbranch);
             }else{
                $this->db->where('st.temp_idbranch='.$idbranch);
             }
        }else{
            if(count($idbranchs)>0){
                if($type==1){
                   $this->db->where_in('st.idbranch',$idbranchs);
                }else{
                   $this->db->where_in('st.temp_idbranch',$idbranchs);
                }
            }
        }
        $this->db->where('g.active', 1); 
        $this->db->join('godown g', 'st.idgodown=g.id_godown');
        $this->db->join('model_variants mv', 'st.idvariant=mv.id_variant');        
        $this->db->join('product_category pc', 'st.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'st.idbrand=brd.id_brand');
        $this->db->where('pc.id_product_category = ageing_stock.idproductcategory');
        $this->db->where('mv.id_variant = ageing_stock.idvariant');
        $this->db->where('b.id_branch = ageing_stock.idbranch');
        $this->db->where('brd.id_brand = ageing_stock.idbrand')->from('ageing_stock'); 
        $this->db->where('b.is_warehouse', $iswarehouse);  
        $this->db->where('b.active', 1);
        $this->db->order_by('st.idbrand,b.id_branch');
        $this->db->group_by('mv.id_variant,b.id_branch,g.id_godown');
        $query = $this->db->get(); 
//        die($this->db->last_query());
        return $query->result();
    } 
    
    public function ajax_get_stock_value_report($from, $to){
        return $this->db->select('daily_stock.stock_date,product_category.product_category_name,category.category_name,brand.brand_name, sum(daily_stock.volume) as volume, sum(daily_stock.value_manager) as manager_value, sum(daily_stock.value_purchase) as purchase_value')
                        ->where('daily_stock.stock_date >=', $from)
                        ->where('daily_stock.stock_date <=', $to)
                        ->where('daily_stock.idbranch = branch.id_branch')->from('branch')
                        ->where('daily_stock.idbrand = brand.id_brand')->from('brand')
                        ->where('daily_stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('daily_stock.idcategory = category.id_category')->from('category')
                        ->group_by('daily_stock.stock_date,daily_stock.idproductcategory,daily_stock.idcategory,daily_stock.idbrand')
                        ->get('daily_stock')->result();
    }
    public function ajax_get_monthly_stock_value_report($from, $to){
        return $this->db->select('daily_stock.stock_date,product_category.product_category_name,category.category_name,brand.brand_name, sum(daily_stock.volume) as volume, sum(daily_stock.value_manager) as manager_value, sum(daily_stock.value_purchase) as purchase_value')
                        ->where('daily_stock.stock_date >=', $from)
                        ->where('daily_stock.stock_date <=', $to)
                        ->where('daily_stock.idbranch = branch.id_branch')->from('branch')
                        ->where('daily_stock.idbrand = brand.id_brand')->from('brand')
                        ->where('daily_stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('daily_stock.idcategory = category.id_category')->from('category')
                        ->group_by('daily_stock.idproductcategory,daily_stock.idcategory,daily_stock.idbrand')
                        ->get('daily_stock')->result();
    }
    public function ajax_get_date_count($from, $to){
        return $this->db->select('count(DISTINCT stock_date) as days')
                        ->where('stock_date >=', $from)
                        ->where('stock_date <=', $to)
                        ->get('daily_stock')->row();
    }

    public function ajax_get_all_modelvariants_byid($idvariant,$idcategory){
        $str = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and id_variant IN( SELECT v.idvariant FROM ( SELECT `idvariant`, `idmodel`, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute GROUP BY `idvariant`, `idmodel` ) AS v INNER JOIN( SELECT `idvariant`, idmodel, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute WHERE `idvariant` = $idvariant GROUP BY `idvariant` ) AS b ON b.ram = v.ram AND b.rom = v.rom WHERE v.`idmodel` = b.`idmodel` ) AND active = 1 GROUP BY mv.id_variant DESC ";
        return $this->db->query($str)->result();
    }
    
    public function ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory){
        $str = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and idmodel = $idmodel";
        return $this->db->query($str)->result();
    }
    public function ajax_get_all_modelvariants_byid_af_fc($idvariant,$idcategory){      
        $str = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and id_variant IN( SELECT v.idvariant FROM ( SELECT `idvariant`, `idmodel`, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute GROUP BY `idvariant`, `idmodel` ) AS v INNER JOIN( SELECT `idvariant`, idmodel, MAX( CASE WHEN `idattribute` = 9 THEN `attribute_value` END ) ram, MAX( CASE WHEN `idattribute` = 8 THEN `attribute_value` END ) rom FROM model_variants_attribute WHERE `idvariant` = $idvariant GROUP BY `idvariant` ) AS b ON b.ram = v.ram AND b.rom = v.rom WHERE v.`idmodel` = b.`idmodel` ) AND active = 1 GROUP BY mv.id_variant ASC ";
        return $this->db->query($str)->result();
    }
    public function ajax_get_all_modelvariants_byidmodel_af_fc($idmodel,$idcategory){
        $str = "SELECT mv.id_variant FROM model_variants mv WHERE idcategory = $idcategory and idmodel = $idmodel";
        return $this->db->query($str)->result();
    }
    
    
     //***************Focus Model Data***********************
    
    public function get_get_branch_stock_by_variant($variantid,$idgodown,$idwarehouse,$days) { 
        $to = date('Y-m-d');
        $day = '-'.$days.' days';                 
        $from = date('Y-m-d', strtotime("$day", strtotime($to)));  
        
        $this->db->select('z.zone_name,mv.id_variant,mv.idcategory,mv.full_name,mv.idmodel,mv.idproductcategory,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sp.sale_qty');
        $this->db->from('branch b');    
        $this->db->join('(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idgodown='.$idgodown.' and  s.idvariant='.$variantid.' and s.date between "'.$from.'" and "'.$to.'"  GROUP BY s.idbranch) sp', 'sp.idbranch=b.id_branch', 'left');
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch from stock stm WHERE  stm.idgodown='.$idgodown.' and  stm.idvariant='.$variantid.' GROUP BY stm.temp_idbranch) stkm','`stkm`.`temp_idbranch`=`b`.`id_branch`','left');         
        if($idwarehouse){
            $this->db->where('(b.idwarehouse= '.$idwarehouse.' or b.is_warehouse=1)');
        }
        $this->db->where('mv.id_variant',$variantid)->from('model_variants mv');        
        $this->db->where('b.active', 1);
        $this->db->where('z.id_zone=b.idzone')->from('zone z'); 
        $this->db->order_by('b.is_warehouse,z.id_zone,b.id_branch');
        $this->db->group_by('b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
    }
    public function get_focus_stock_data($idproductcategory, $idbrand, $idmodel,$idvariant, $idbranch){
        return $this->db->where('idproductcategory',$idproductcategory)
                        ->where('idbrand',$idbrand)
                        ->where('idmodel',$idmodel)
                        ->where('idvariant',$idvariant)
                        ->where('idbranch',$idbranch)
                        ->get('focus_model_stock')->row();
    }
    
    public function remove_focus_model_stock_byidvariant($idvariant){
         return $this->db->where_in('idvariant',$idvariant)->delete('focus_model_stock');
    }
    public function save_focus_model_stock_data($data){
        return $this->db->insert('focus_model_stock', $data);
    }
    
    public function ajax_get_focus_incentive_data_byidvariant($idvariant){
        return $this->db->where('idvariant', $idvariant)->get('focus_model_stock')->row();
    }
    
    public function ajax_get_focus_model_data($idbrand, $idproductcategory, $idbranch, $allbranch, $allbrand, $allpcat){
        if($idbranch == 0){
            $branches = explode(',',$allbranch);
        }else{
            $branches[] = $idbranch;
        }
        
        if($idbrand == 0){
            $brands = explode(',',$allbrand);
        }else{
            $brands[] = $idbrand;
        }
        
        if($idproductcategory == 0){
            $pcats = explode(',',$allpcat);
        }else{
            $pcats[] = $idproductcategory;
        }
        return $this->db->where_in('focus_model_stock.idproductcategory', $pcats)
                        ->where_in('focus_model_stock.idbrand', $brands)
                        ->where_in('focus_model_stock.idbranch', $branches)
                        ->where('focus_model_stock.idproductcategory = product_category.id_product_category')->from('product_category')
                        ->where('focus_model_stock.idbrand = brand.id_brand')->from('brand')
                        ->where('focus_model_stock.idbranch = branch.id_branch')->from('branch')
                        ->where('focus_model_stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('focus_model_stock')->result();
    }
    
    public function get_focus_model_quantity_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs) {
              
        $this->db->select('pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,g.godown_name,focus_model_stock.incentive_amount');
        $this->db->from('model_variants mv');        
        $whr=" ";
        $whr1=" ";
        $whrr=" ";
        $whrr1=" ";        
        if($idproductcategory){
            $whr.=' and st.idproductcategory='.$idproductcategory.' ';
            $whr1.=' and stm.idproductcategory='.$idproductcategory.' ';
            
            $whrr.='  st.idproductcategory='.$idproductcategory.' ';
            $whrr1.=' stm.idproductcategory='.$idproductcategory.' ';
            $this->db->where('mv.idproductcategory', $idproductcategory);
        }
        if($idbrand){
            if($idproductcategory){
                $whrr.=' and st.idbrand='.$idbrand.' ';
                $whrr1.=' and stm.idbrand='.$idbrand.' ';
                
                $whr.=' and st.idbrand='.$idbrand.' ';
                $whr1.=' and stm.idbrand='.$idbrand.' ';
            }else{
                $whrr.=' st.idbrand='.$idbrand.' ';
                $whrr1.=' stm.idbrand='.$idbrand.' ';
                
                $whr.=' st.idbrand='.$idbrand.' ';
                $whr1.=' stm.idbrand='.$idbrand.' ';
            }
             $this->db->where('mv.idbrand', $idbrand);   
        }
        if($idgodown && $idbranch)
        {
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$idbranch.' '.$whr.' GROUP BY st.idvariant) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.' and stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif(!$idgodown && $idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idbranch='.$idbranch.'  '.$whr.'  GROUP BY st.idvariant,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE    stm.temp_idbranch='.$idbranch.'  '.$whr1.'  GROUP BY stm.idvariant,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }elseif($idgodown && !$idbranch){
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idgodown='.$idgodown.'  '.$whr.'  GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.idgodown='.$idgodown.'  '.$whr1.'  GROUP BY stm.idvariant,stm.temp_idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }else{
            $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant ,st.idgodown from stock st WHERE '.$whrr.' GROUP BY st.idvariant,st.idbranch,st.idgodown) stk','`stk`.`idgodown` = `g`.`id_godown` and `stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE '.$whrr1.' GROUP BY stm.idvariant,stm.temp_idbranch,stm.idgodown) stkm','`stkm`.`idgodown` = `g`.`id_godown` and `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        }
        if($idgodown){
            $this->db->where('g.id_godown', $idgodown);
        }
        if($idbranch){
              $this->db->where('b.id_branch', $idbranch);
        }else{
            if(count($idbranchs)>0){                
                   $this->db->where_in('b.id_branch',$idbranchs);
            }
        }        
        $this->db->where('mv.active', 1)->from('branch b');         
        $this->db->where('g.active', 1)->from('godown g'); 
        $this->db->join('product_category pc', 'mv.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where('b.is_warehouse', $iswarehouse);                    
        $this->db->where('b.active', 1);
        $this->db->where('pc.id_product_category = focus_model_stock.idproductcategory');
        $this->db->where('mv.id_variant = focus_model_stock.idvariant');
        $this->db->where('b.id_branch = focus_model_stock.idbranch');
        $this->db->where('brd.id_brand = focus_model_stock.idbrand')->from('focus_model_stock'); 
        $this->db->order_by('mv.id_variant,b.id_branch');
        $this->db->group_by('mv.id_variant,b.id_branch,g.id_godown');
        $query = $this->db->get(); 
        return $query->result();
    } 
    
    public function get_focus_model_imei_stock_by_GPBB($idgodown,$idbrand,$idproductcategory,$idbranch,$iswarehouse,$idbranchs,$type=1) {              
        $this->db->select('st.idbranch,st.temp_idbranch,st.imei_no,pc.product_category_name,brd.brand_name,mv.id_variant,mv.full_name, `b`.`branch_name`, st.qty,g.godown_name,focus_model_stock.incentive_amount,st.outward_time,st.transfer_time,st.date');
        $this->db->from('stock st'); 
        if($type==1){
            $this->db->join('branch b', 'st.idbranch=b.id_branch','left');    /// for current stock
        }else{        
            $this->db->join('branch b', 'st.temp_idbranch=b.id_branch','left'); /// for in transit stock        
        }
        if($idproductcategory){
            $this->db->where('st.idproductcategory', $idproductcategory);
        }
        if($idbrand){
             $this->db->where('st.idbrand', $idbrand);   
        }        
        if($idgodown){
            $this->db->where('st.idgodown', $idgodown); 
        }        
        if($idbranch){
             if($type==1){
                $this->db->where('st.idbranch='.$idbranch);
             }else{
                $this->db->where('st.temp_idbranch='.$idbranch);
             }
        }else{
            if(count($idbranchs)>0){
                if($type==1){
                   $this->db->where_in('st.idbranch',$idbranchs);
                }else{
                   $this->db->where_in('st.temp_idbranch',$idbranchs);
                }
            }
        }
        $this->db->where('g.active', 1); 
        $this->db->join('godown g', 'st.idgodown=g.id_godown');
        $this->db->join('model_variants mv', 'st.idvariant=mv.id_variant');        
        $this->db->join('product_category pc', 'st.idproductcategory=pc.id_product_category');
        $this->db->join('brand brd', 'st.idbrand=brd.id_brand');
        $this->db->where('pc.id_product_category = focus_model_stock.idproductcategory');
        $this->db->where('mv.id_variant = focus_model_stock.idvariant');
        $this->db->where('b.id_branch = focus_model_stock.idbranch');
        $this->db->where('brd.id_brand = focus_model_stock.idbrand')->from('focus_model_stock'); 
        $this->db->where('b.is_warehouse', $iswarehouse);  
        $this->db->where('b.active', 1);
        $this->db->order_by('st.idbrand,b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
    } 
    
    public function ajax_get_focus_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand){
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
        
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
        return $this->db->select('sale.date,sale.entry_time,sale_product.idsale,sale_product.inv_no,branch.branch_name,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name, sale_product.landing,partner_type.partner_type,zone.zone_name,sale_product.mop,sale_product.mrp, branch_category.branch_category_name,model_variants.full_name,sale_product.nlc_price,users.id_users')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where_in('sale_product.idproductcategory', $productcatid)
                        ->where_in('sale_product.idbrand', $brandid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where('sale_product.focus ', 1)
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('users','sale.idsalesperson = users.id_users', 'left')
                        ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')
                        ->join('partner_type','branch.idpartner_type = partner_type.id_partner_type', 'left')
                        ->join('zone','branch.idzone = zone.id_zone', 'left')
                        ->join('model_variants','sale_product.idvariant = model_variants.id_variant', 'left')
                        ->get('sale_product')->result();
        
    }
    public function ajax_get_brand_name_byiduser($id_users){
        return $this->db->select('brand.brand_name as user_brand_name')
                        ->where('user_has_brand.iduser', $id_users)
                        ->where('user_has_brand.idbrand = brand.id_brand')->from('brand')
                        ->get('user_has_brand')->row();
    }
    
    //Brand Placement Norms
    public function get_brand_wise_placement_norms($days,$product_category,$branch){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $this->db->select('b.id_brand, b.brand_name, stk.curr_stock,sp.sale_qty,ub.promotor,intra_stk.intra_stock,snn.*');
        $this->db->where('b.norm_sequence !=', 0);
        $this->db->where('b.id_brand !=', 76);
        $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand from stock st, category c WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.idbranch = $branch and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) stk", 'stk.idbrand = b.id_brand', 'left');
        $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand from stock st, category c WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.temp_idbranch = $branch and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) intra_stk", 'intra_stk.idbrand = b.id_brand', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s, category c WHERE s.idgodown in(1,6) and s.idproductcategory = $product_category and s.idbranch = $branch and c.norm_sequence = 0 and  s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=b.id_brand', 'left');                
        $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole from user_has_brand p, users u WHERE p.iduser = u.id_users  and u.active=1  and  u.iduserrole = 17 and u.idbranch = $branch group by p.idbrand) ub", 'ub.idbrand=b.id_brand', 'left');                
        $this->db->join("(select sn.* from stock_norms sn WHERE sn.idbranch = $branch and sn.idproductcategory = $product_category ) snn", 'snn.idbrand=b.id_brand', 'left');                
//        $this->db->order_by('b.norm_sequence');
        $this->db->from('brand b');
        $query = $this->db->get();  
        return $query->result();
        
    }
   
    public function get_allbrand_wise_placement_norms($days,$product_category,$branch){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $this->db->select('sum(stk.curr_stock) as curr_stock,sum(sp.sale_qty) as sale_qty, sum(ub.promotor) as promotor,intra_stk.intra_stock');
        $this->db->where('b.norm_sequence', 0);
        $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand from stock st, category c WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.idbranch = $branch and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) stk", 'stk.idbrand = b.id_brand', 'left');
        $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand from stock st, category c WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.temp_idbranch = $branch and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) intra_stk", 'intra_stk.idbrand = b.id_brand', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s, category c WHERE s.idgodown in(1,6) and s.idproductcategory = $product_category and s.idbranch = $branch and c.norm_sequence  = 0 and s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=b.id_brand', 'left');                
        $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole from user_has_brand p, users u WHERE p.iduser = u.id_users and u.iduserrole = 17  and u.active=1  and u.idbranch = $branch group by p.idbrand) ub", 'ub.idbrand=b.id_brand', 'left');                
        
        $this->db->from('brand b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    public function get_category_wise_placement_norms($days,$product_category,$branch){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $this->db->select('b.id_category, b.category_name, stk.curr_stock,sp.sale_qty,intra_stk.intra_stock,snn.*');
        $this->db->where('b.norm_sequence !=', 0);
        $this->db->join("(select sum(st.qty) as curr_stock, st.idcategory from stock st WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.idbranch = $branch group by st.idcategory) stk", 'stk.idcategory = b.id_category', 'left');
        $this->db->join("(select sum(st.qty) as intra_stock, st.idcategory from stock st WHERE st.idgodown in(1,6) and st.idproductcategory = $product_category and st.temp_idbranch = $branch group by st.idcategory) intra_stk", 'intra_stk.idcategory = b.id_category', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty,s.idcategory from sale_product s WHERE s.idgodown in(1,6) and s.idproductcategory = $product_category and s.idbranch = $branch and s.date between '$from' and '$to' GROUP BY s.idcategory) sp", 'sp.idcategory=b.id_category', 'left');                
        $this->db->join("(select sn.* from stock_norms sn WHERE sn.idbranch = $branch and sn.idproductcategory = $product_category ) snn", 'snn.idcategory=b.id_category', 'left');                
        
        $this->db->order_by('b.norm_sequence');
        $this->db->from('category b');
        $query = $this->db->get();  
        return $query->result();
        
    }
    public function get_mix_brand_data($product_category,$branch){
        return $this->db->where('idbranch', $branch)
                        ->where('idproductcategory', $product_category)
                        ->where('idbrand',76)
                        ->get('stock_norms')->row();
    }
    
    public function get_stock_placement_data_byid($product_category, $branch){
        return $this->db->select('stock_norms.*,brand.brand_name, category.category_name')
                        ->where('stock_norms.idproductcategory', $product_category)
                        ->where('stock_norms.idbranch', $branch)
                        ->join('brand','stock_norms.idbrand = brand.id_brand','left')
                        ->join('category','stock_norms.idcategory = category.id_category','left')
                        ->get('stock_norms')->result();
    }
    public function save_brand_placement_norms($data){
        return $this->db->insert('stock_norms', $data);
    }
    public function update_brand_placement_norms($data, $id_stock_norm){
        return $this->db->where('id_stock_norm', $id_stock_norm)->update('stock_norms', $data);
    }
//    public function get_promotor_count_byidbrand($idbrand, $branch){
//        
//        return $this->db->select('count(ub.id_user_has_brand) as pcnt')
//                        ->where('ub.idbrand', $idbrand)
//                        ->where('ub.iduser = users.id_users')
//                        ->where('users.iduserrole', 17)
//                        ->where('users.idbranch', $branch)->from('users')
//                        ->get('user_has_brand ub')->row();
//    }
     public function get_brand_wise_placement_norms_byidbrand($days,$product_category,$idbrand,$branch, $idcat){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        if($idbrand > 0 && $idbrand != 76){
            $this->db->select('stk.curr_stock,sp.sale_qty,ub.promotor,intra_stk.intra_stock');
            $this->db->where('b.id_brand', $idbrand);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand from stock st, category c WHERE st.idproductcategory = $product_category and st.idbranch = $branch and st.idgodown in(1,6) and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) stk", 'stk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand from stock st, category c WHERE st.idproductcategory = $product_category and st.temp_idbranch = $branch and st.idgodown in(1,6) and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) intra_stk", 'intra_stk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s, category c WHERE s.idproductcategory = $product_category and s.idbranch = $branch  and s.idgodown in(1,6) and c.norm_sequence = 0 and  s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=b.id_brand', 'left');                
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole from user_has_brand p, users u WHERE p.iduser = u.id_users and u.iduserrole = 17  and u.active=1  and u.idbranch = $branch group by p.idbrand) ub", 'ub.idbrand=b.id_brand', 'left');                

    //        $this->db->order_by('b.norm_sequence');
            $this->db->from('brand b');
            $query = $this->db->get();  
            return $query->row();
        }
        if ($idbrand == 76) {
            
            $this->db->select('sum(stk.curr_stock) as curr_stock,sum(sp.sale_qty) as sale_qty, sum(ub.promotor) as promotor,intra_stk.intra_stock');
            $this->db->where('b.norm_sequence', 0);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand from stock st, category c WHERE st.idproductcategory = $product_category and st.idbranch = $branch  and st.idgodown in(1,6) and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) stk", 'stk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand from stock st, category c WHERE st.idproductcategory = $product_category and st.temp_idbranch = $branch  and st.idgodown in(1,6) and c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) intra_stk", 'intra_stk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s, category c WHERE s.idproductcategory = $product_category and s.idbranch = $branch   and s.idgodown in(1,6) and c.norm_sequence = 0 and s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=b.id_brand', 'left');                
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole from user_has_brand p, users u WHERE p.iduser = u.id_users and u.iduserrole = 17  and u.active=1  and u.idbranch = $branch group by p.idbrand) ub", 'ub.idbrand=b.id_brand', 'left');                

            $this->db->from('brand b');
            $query = $this->db->get();  
            return $query->row();
            
        }
        if($idcat > 0){
            $this->db->select('b.id_category, b.category_name, stk.curr_stock,sp.sale_qty,intra_stk.intra_stock');
            $this->db->where('b.id_category', $idcat);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idcategory from stock st WHERE st.idproductcategory = $product_category and st.idbranch = $branch  and st.idgodown in(1,6)  group by st.idcategory) stk", 'stk.idcategory = b.id_category', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idcategory from stock st WHERE st.idproductcategory = $product_category and st.temp_idbranch = $branch  and st.idgodown in(1,6)  group by st.idcategory) intra_stk", 'intra_stk.idcategory = b.id_category', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idcategory from sale_product s WHERE s.idproductcategory = $product_category and s.idbranch = $branch  and s.idgodown in(1,6) and s.date between '$from' and '$to' GROUP BY s.idcategory) sp", 'sp.idcategory=b.id_category', 'left');                
            $this->db->order_by('b.norm_sequence');
            $this->db->from('category b');
            $query = $this->db->get();  
            return $query->row();
        }
    }
    //*********brand norms report************//
     public function get_brand_wise_placement_norms_report($days,$product_category,$branch, $allbranchs, $idzone, $allzones){
//         die(print_r($_POST));
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $branches = array();
        if($idzone == ''){
            if($branch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $branch;
            }
        }else{
            if($idzone == 'all'){
                 $branches = explode(',', $allbranchs);
            }
            elseif ($idzone == 'allzone') {
                $branches = explode(',', $allzones);
            }else{
                $badts = $this->db->where('idzone', $idzone)->get('branch')->result();
                foreach($badts as $bts){
                    $branches[] = $bts->id_branch;
                }
            }
        }
        $strbr = implode(',', $branches);
//        die(print_r($strbr));
        if($idzone == 'all'){ //Overall Zone
            
            $this->db->select('" " as id_branch," " as branch_name," " as id_zone," " as zone_name,b.id_brand,b.brand_name,stk.curr_stock,sp.sale_qty,ub.promotor,sn.qun as quantity,intstk.intra_stock');
            $this->db->where('b.norm_sequence !=', 0);
            $this->db->where('b.id_brand !=', 76);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand from stock st, category c WHERE st.idbranch in($strbr) and st.idgodown in(1,6) and st.idproductcategory = $product_category and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) stk", 'stk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand from stock st, category c WHERE st.temp_idbranch in($strbr) and st.idgodown in(1,6) and st.idproductcategory = $product_category and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand) intstk", 'intstk.idbrand = b.id_brand', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand from sale_product s, category c WHERE s.idbranch in($strbr)and s.idgodown in(1,6) and s.idproductcategory = $product_category and  c.norm_sequence = 0 and  s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand) sp", 'sp.idbrand=b.id_brand', 'left');                
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole from user_has_brand p, users u WHERE u.idbranch in($strbr) and p.iduser = u.id_users and u.iduserrole = 17  and u.active=1    group by p.idbrand) ub", 'ub.idbrand=b.id_brand', 'left');                
            $this->db->join("(select sum(s.quantity) as qun,s.idbrand from stock_norms s WHERE s.idproductcategory = $product_category GROUP BY s.idbrand) sn", 'sn.idbrand = b.id_brand', 'left');                
            $this->db->order_by('b.norm_sequence');
            $this->db->from('brand b');
            $query = $this->db->get();  
            return $query->result();
        }
        elseif ($idzone == 'allzone') {
            $this->db->select('" " as id_branch," " as branch_name,b.id_brand,z.id_zone,z.zone_name, b.brand_name,stk.curr_stock,sp.sale_qty,ub.promotor,sn.qun as quantity,intstk.intra_stock');
            $this->db->where('b.norm_sequence !=', 0);
            $this->db->where_in('z.id_zone', $branches);
            $this->db->where('b.id_brand !=', 76);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand,st.idbranch, brr.idzone from stock st, category c, branch brr WHERE brr.is_warehouse = 0 and st.idgodown in(1,6) and st.idbranch = brr.id_branch and brr.idzone in($strbr) and st.idproductcategory = $product_category and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand,brr.idzone) stk", 'stk.idbrand = b.id_brand and stk.idzone=z.id_zone', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand,st.temp_idbranch, brr.idzone from stock st, category c, branch brr WHERE brr.is_warehouse = 0 and st.idgodown in(1,6) and st.temp_idbranch = brr.id_branch and brr.idzone in($strbr) and st.idproductcategory = $product_category and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand,brr.idzone) intstk", 'intstk.idbrand = b.id_brand and intstk.idzone=z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand,s.idbranch,brr.idzone from sale_product s, category c, branch brr WHERE brr.is_warehouse = 0 and s.idgodown in(1,6) and s.idbranch = brr.id_branch and brr.idzone in($strbr) and s.idproductcategory = $product_category and  c.norm_sequence = 0 and  s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand,brr.idzone) sp", 'sp.idbrand=b.id_brand  and sp.idzone=z.id_zone ', 'left');                
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole,u.idbranch,brr.idzone from user_has_brand p, users u,branch brr WHERE u.idbranch = brr.id_branch and brr.idzone in($strbr) and  p.iduser = u.id_users and u.iduserrole = 17  and u.active=1    group by p.idbrand,brr.idzone) ub", 'ub.idbrand=b.id_brand and ub.idzone=z.id_zone', 'left');                
            $this->db->join("(select sum(s.quantity) as qun,s.idbrand,s.idbranch,brr.idzone from stock_norms s, branch brr WHERE  brr.is_warehouse = 0 and s.idbranch = brr.id_branch and brr.idzone in($strbr) and s.idproductcategory = $product_category GROUP BY s.idbrand,brr.idzone) sn", 'sn.idbrand = b.id_brand and sn.idzone = z.id_zone', 'left');                
            $this->db->order_by('z.id_zone,b.norm_sequence');
            $this->db->from('brand b');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }
        else{
            
            $this->db->select('bnd.id_branch,bnd.branch_name,b.id_brand,zone.id_zone,zone.zone_name, b.brand_name,stk.curr_stock,sp.sale_qty,ub.promotor,sn.quantity,intstk.intra_stock');
    //        $this->db->select('bnd.id_branch,bnd.branch_name,b.id_brand,zone.zone_name, b.brand_name,"0" as id_category ,stk.curr_stock,sp.sale_qty,ub.promotor,sn.quantity');
            $this->db->where('b.norm_sequence !=', 0);
            $this->db->where('bnd.is_warehouse', 0);
            $this->db->where_in('bnd.id_branch', $branches);
            $this->db->where('b.id_brand !=', 76);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand,st.idbranch from stock st, category c WHERE  st.idproductcategory = $product_category and st.idgodown in(1,6) and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand,st.idbranch) stk", 'stk.idbrand = b.id_brand and stk.idbranch=bnd.id_branch', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand,st.temp_idbranch from stock st, category c WHERE  st.idproductcategory = $product_category and st.idgodown in(1,6) and  c.norm_sequence = 0 and st.idcategory = c.id_category group by st.idbrand,st.temp_idbranch) intstk", 'intstk.idbrand = b.id_brand and intstk.temp_idbranch=bnd.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand,s.idbranch from sale_product s, category c WHERE s.idproductcategory = $product_category and s.idgodown in(1,6) and  c.norm_sequence = 0 and  s.idcategory = c.id_category and s.date between '$from' and '$to' GROUP BY s.idbrand,s.idbranch) sp", 'sp.idbrand=b.id_brand  and sp.idbranch=bnd.id_branch ', 'left');                
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole,u.idbranch from user_has_brand p, users u WHERE p.iduser = u.id_users and u.iduserrole = 17 and u.active=1    group by p.idbrand,u.idbranch) ub", 'ub.idbrand=b.id_brand and ub.idbranch=bnd.id_branch', 'left');                
            $this->db->join("(select s.quantity,s.idbrand,s.idbranch from stock_norms s WHERE s.idproductcategory = $product_category) sn", 'sn.idbrand = b.id_brand and sn.idbranch = bnd.id_branch', 'left');                
            $this->db->where('bnd.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('bnd.idzone,bnd.id_branch,b.norm_sequence');
            $this->db->from('brand b');
            $this->db->from('branch bnd');
            $query = $this->db->get();  
            return $query->result();
        }
//         die($this->db->last_query());
    }
     public function get_category_wise_placement_norms_report($days,$product_category,$branch,$allbranchs, $idzone, $allzones){
//         die(print_r($_POST));
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $branches = array();
        if($idzone == ''){
            if($branch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $branch;
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
        if($idzone == 'all'){ 
             $this->db->select('" " as id_branch," " as branch_name," " as id_zone," " as zone_name, b.id_category as id_brand, b.category_name as brand_name,stk.curr_stock,sp.sale_qty,sn.quantity, "0" as promotor, intstk.intra_stock');
            $this->db->where('b.norm_sequence !=', 0);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idcategory from stock st WHERE st.idbranch in($strbr) and st.idgodown in(1,6) and st.idproductcategory = $product_category  group by st.idcategory) stk", 'stk.idcategory = b.id_category', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idcategory from stock st WHERE st.temp_idbranch in($strbr) and st.idgodown in(1,6) and st.idproductcategory = $product_category  group by st.idcategory) intstk", 'intstk.idcategory = b.id_category', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idcategory from sale_product s WHERE s.idbranch in($strbr) and s.idgodown in(1,6) and s.idproductcategory = $product_category and s.date between '$from' and '$to' GROUP BY s.idcategory) sp", 'sp.idcategory=b.id_category', 'left');                
            $this->db->join("(select sum(s.quantity),s.idcategory from stock_norms s WHERE s.idbranch in($strbr) and s.idproductcategory = $product_category GROUP BY s.idcategory) sn", 'sn.idcategory = b.id_category', 'left');  
            $this->db->order_by('b.norm_sequence');
            $this->db->from('category b');
            $query = $this->db->get();  
            return $query->result();
            
        }elseif ($idzone == 'allzone') {
            $this->db->select('" " as id_branch," " as branch_name,z.id_zone,z.zone_name, b.id_category as id_brand, b.category_name as brand_name,stk.curr_stock,sp.sale_qty,"0" as promotor,sn.qun as quantity,intstk.intra_stock');
             $this->db->where('b.norm_sequence !=', 0);
            $this->db->where_in('z.id_zone', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock,st.idbranch, st.idcategory, brr.idzone from stock st, branch brr WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and brr.is_warehouse = 0 and st.idbranch = brr.id_branch and brr.idzone in($strbr) group by st.idcategory,brr.idzone) stk", 'stk.idcategory = b.id_category and stk.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock,st.idbranch, st.idcategory, brr.idzone from stock st, branch brr WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and brr.is_warehouse = 0 and st.temp_idbranch = brr.id_branch and brr.idzone in($strbr) group by st.idcategory,brr.idzone) intstk", 'intstk.idcategory = b.id_category and intstk.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idcategory,s.idbranch, brr.idzone from sale_product s, branch brr WHERE s.idproductcategory = $product_category and s.idgodown in(1,6) and brr.is_warehouse = 0 and s.idbranch = brr.id_branch and brr.idzone in($strbr) and s.date between '$from' and '$to' GROUP BY s.idcategory,brr.idzone) sp", 'sp.idcategory=b.id_category and sp.idzone = z.id_zone', 'left');                
            $this->db->join("(select sum(s.quantity) as qun,s.idcategory,s.idbranch, brr.idzone from stock_norms s, branch brr WHERE s.idproductcategory = $product_category and brr.is_warehouse = 0 and s.idbranch = brr.id_branch and brr.idzone in($strbr) GROUP BY s.idcategory, brr.idzone ) sn", 'sn.idcategory = b.id_category and sn.idzone = z.id_zone', 'left');  
            $this->db->order_by('z.id_zone,b.norm_sequence');
            $this->db->from('category b');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
        }
        else{
            
            $this->db->select('br.id_branch, br.branch_name,zone.id_zone,zone.zone_name, b.id_category as id_brand, b.category_name as brand_name,stk.curr_stock,sp.sale_qty,sn.quantity, "0" as promotor,intstk.intra_stock');
            $this->db->where('b.norm_sequence !=', 0);
            $this->db->where('br.is_warehouse', 0);
            $this->db->where_in('br.id_branch', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock,st.idbranch, st.idcategory from stock st WHERE st.idproductcategory = $product_category and st.idgodown in(1,6)  group by st.idcategory,st.idbranch) stk", 'stk.idcategory = b.id_category and stk.idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock,st.temp_idbranch, st.idcategory from stock st WHERE st.idproductcategory = $product_category and st.idgodown in(1,6)  group by st.idcategory,st.temp_idbranch) intstk", 'intstk.idcategory = b.id_category and intstk.temp_idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idcategory,s.idbranch from sale_product s WHERE s.idproductcategory = $product_category and s.idgodown in(1,6) and s.date between '$from' and '$to' GROUP BY s.idcategory,s.idbranch) sp", 'sp.idcategory=b.id_category and sp.idbranch = br.id_branch', 'left');                
            $this->db->join("(select s.quantity,s.idcategory,s.idbranch from stock_norms s WHERE s.idproductcategory = $product_category) sn", 'sn.idcategory = b.id_category and sn.idbranch = br.id_branch', 'left');  
            $this->db->where('br.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('br.idzone,br.id_branch,b.norm_sequence');
            $this->db->from('category b');
            $this->db->from('branch br');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
    public function get_allbrand_wise_placement_norms_report($days,$product_category,$branch,$allbranchs, $idzone, $allzones){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $branches = array();
        if($idzone == ''){
            if($branch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $branch;
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
//            die("dfg");
            $this->db->select('" " as branch_name," " as id_branch," " as id_zone," " as zone_name,"76" as id_brand, "mixed" as brand_name,sum(stk.curr_stock) as curr_stock,sum(sp.sale_qty) as sale_qty,sum(ub.promotor) as promotor, sum(sn.quantity) as quantity,intstk.intra_stock');
            $this->db->where_in('br.is_warehouse', 0);
            $this->db->where_in('br.id_branch', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand,st.idbranch from stock st, category c, brand b WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by st.idbranch) stk", 'stk.idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand,st.temp_idbranch from stock st, category c, brand b WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by st.temp_idbranch) intstk", 'intstk.temp_idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand,s.idbranch from sale_product s, category c, brand b WHERE s.idproductcategory = $product_category  and s.idgodown in(1,6) and b.norm_sequence = 0 and c.norm_sequence  = 0 and s.idcategory = c.id_category and s.idbrand = b.id_brand and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch = br.id_branch', 'left');                
            $this->db->join("(select s.quantity,s.idbrand,s.idbranch from stock_norms s WHERE s.idbrand = 76 and s.idproductcategory = $product_category) sn", 'sn.idbranch = br.id_branch', 'left');  
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole,u.idbranch from user_has_brand p, users u, brand b WHERE b.norm_sequence =0 and p.iduser = u.id_users and u.iduserrole = 17 and u.active=1  and p.idbrand = b.id_brand GROUP BY u.idbranch) ub", 'ub.idbranch = br.id_branch', 'left');                
            $this->db->where('br.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('br.idzone,br.id_branch');
            $this->db->from('branch br');
            $query = $this->db->get();  
            return $query->result();
            
        }elseif ($idzone == 'allzone'){
//            die("hii");
            $this->db->select('" " as branch_name," " as id_branch,z.id_zone,z.zone_name,"76" as id_brand, "mixed" as brand_name,stk.curr_stock,sp.sale_qty, ub.promotor,sn.qun as quantity,intstk.intra_stock');
            $this->db->where_in('z.id_zone', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand,st.idbranch, brr.idzone from stock st, category c, brand b, branch brr WHERE brr.is_warehouse = 0 and st.idgodown in(1,6) and st.idbranch = brr.id_branch and brr.idzone in($strbr) and st.idproductcategory = $product_category and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by brr.idzone) stk", 'stk.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand,st.temp_idbranch, brr.idzone from stock st, category c, brand b, branch brr WHERE brr.is_warehouse = 0 and st.idgodown in(1,6) and st.temp_idbranch = brr.id_branch and brr.idzone in($strbr) and st.idproductcategory = $product_category and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by brr.idzone) intstk", 'intstk.idzone = z.id_zone', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand,s.idbranch,brr.idzone from sale_product s, category c, brand b, branch brr WHERE brr.is_warehouse = 0  and s.idgodown in(1,6)and s.idbranch = brr.id_branch and brr.idzone in($strbr) and s.idproductcategory = $product_category and b.norm_sequence = 0 and c.norm_sequence  = 0 and s.idcategory = c.id_category and s.idbrand = b.id_brand and s.date between '$from' and '$to' GROUP BY brr.idzone) sp", 'sp.idzone = z.id_zone', 'left');                
            $this->db->join("(select sum(s.quantity) as qun,s.idbrand,s.idbranch,brr.idzone from stock_norms s, branch brr WHERE brr.is_warehouse = 0 and s.idbranch = brr.id_branch and brr.idzone in($strbr) and s.idbrand = 76 and s.idproductcategory = $product_category GROUP BY brr.idzone) sn", 'sn.idzone = z.id_zone', 'left');  
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole,u.idbranch,brr.idzone from user_has_brand p, users u, brand b, branch brr WHERE u.idbranch = brr.id_branch and brr.idzone in($strbr) and b.norm_sequence =0 and p.iduser = u.id_users and u.iduserrole = 17  and u.active=1  and p.idbrand = b.id_brand GROUP BY brr.idzone) ub", 'ub.idzone = z.id_zone', 'left');                
            
            $this->db->order_by('z.id_zone');
            $this->db->from('zone z');
            $query = $this->db->get();  
            return $query->result();
//            die($this->db->last_query());
        }
        else{
            $this->db->select('br.branch_name,br.id_branch,zone.id_zone,zone.zone_name,"76" as id_brand, "mixed" as brand_name,stk.curr_stock,sp.sale_qty, ub.promotor,sn.quantity,intstk.intra_stock');
            $this->db->where_in('br.id_branch', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock, st.idbrand,st.idbranch from stock st, category c, brand b WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by st.idbranch) stk", 'stk.idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(st.qty) as intra_stock, st.idbrand,st.temp_idbranch from stock st, category c, brand b WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and  b.norm_sequence = 0 and c.norm_sequence = 0 and st.idcategory = c.id_category and st.idbrand = b.id_brand group by st.temp_idbranch) intstk", 'intstk.temp_idbranch = br.id_branch', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbrand,s.idbranch from sale_product s, category c, brand b WHERE s.idproductcategory = $product_category  and s.idgodown in(1,6) and b.norm_sequence = 0 and c.norm_sequence  = 0 and s.idcategory = c.id_category and s.idbrand = b.id_brand and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch = br.id_branch', 'left');                
            $this->db->join("(select s.quantity,s.idbrand,s.idbranch from stock_norms s WHERE s.idbrand = 76 and s.idproductcategory = $product_category) sn", 'sn.idbranch = br.id_branch', 'left');  
            $this->db->join("(select count(p.id_user_has_brand) as promotor,p.idbrand,u.iduserrole,u.idbranch from user_has_brand p, users u, brand b WHERE b.norm_sequence =0 and p.iduser = u.id_users and u.iduserrole = 17 and u.active=1  and p.idbrand = b.id_brand GROUP BY u.idbranch) ub", 'ub.idbranch = br.id_branch', 'left');                
            $this->db->where('br.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('br.idzone,br.id_branch');
            $this->db->from('branch br');
            $query = $this->db->get();  
            return $query->result();
        }
    }
    
    
    
    //price category placement norms
     public function get_price_category_wise_stock($days,$product_category,$branch){
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        $this->db->select('b.id_price_category_lab, b.lab_name, stk.curr_stock,sp.sale_qty,intstk.intra_stock');
        $this->db->where('b.active', 0);
        $this->db->join("(select sum(st.qty) as curr_stock, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idbranch = $branch and st.idgodown in(1,6) and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by p.id_price_category_lab) stk","stk.id_price_category_lab = b.id_price_category_lab","left");
        $this->db->join("(select sum(st.qty) as intra_stock, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.temp_idbranch = $branch and st.idgodown in(1,6) and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by p.id_price_category_lab) intstk","intstk.id_price_category_lab = b.id_price_category_lab","left");
        $this->db->join("(select sum(s.qty) as sale_qty,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.idbranch = $branch and s.date between '$from' and '$to' and s.idgodown in(1,6) and s.total_amount between p.min_lab and p.max_lab GROUP BY p.id_price_category_lab) sp","sp.id_price_category_lab = b.id_price_category_lab","left");                
        $this->db->order_by('b.id_price_category_lab');
        $this->db->from('price_category_lab b');
        $query = $this->db->get();  
        return $query->result();
    }
    public function save_price_category_norms($data){
        return $this->db->insert('price_category_norms', $data);
    }
    public function get_price_cat_placement_data($product_category, $branch,$days){
       
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
//        die($to);
        $this->db->select('pc.id_price_category_lab, pc.lab_name,pn.*,stk.curr_stock,sp.sale_qty,intstk.intra_stock');
        $this->db->join("(select sum(st.qty) as curr_stock, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and st.idbranch = $branch and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab","left");
        $this->db->join("(select sum(st.qty) as intra_stock, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idgodown in(1,6) and st.temp_idbranch = $branch and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by p.id_price_category_lab) intstk","intstk.id_price_category_lab = pc.id_price_category_lab","left");
        $this->db->join("(select sum(s.qty) as sale_qty,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.idbranch = $branch and s.idgodown in(1,6) and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab GROUP BY p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab","left");                
        $this->db->join('(select * from price_category_norms p where p. idbranch = "'.$branch.'" and p.idproductcategory = "'.$product_category.'") pn','pn.idpricecategory = pc.id_price_category_lab','left');
        $this->db->order_by('pc.id_price_category_lab');
        $this->db->from('price_category_lab pc');
        $query = $this->db->get();  
        return $query->result();
    }
       public function get_price_cat_placement_data_report($product_category, $branch,$days, $idzone, $allbranchs, $zones){
//        die(print_r($_POST));
        $branches = array();
        if($idzone == ''){
            if($branch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $branch;
            }
        }else{
            if($idzone == 'all'){
                 $branches = explode(',', $allbranchs);
            }
            elseif($idzone == 'allzone'){
                 $branches = explode(',', $zones);
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
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date( 'Y-m-d', strtotime("$day", strtotime($to)));
        if($idzone == 'all'){
            $this->db->select('pc.id_price_category_lab, pc.lab_name,pn.norm_qty,stk.curr_stock,sp.sale_qty,intstk.intra_stock');
            $this->db->join("(select sum(st.qty) as curr_stock,p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idbranch in($strbr) and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab","left");
            $this->db->join("(select sum(st.qty) as intra_stock,p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.temp_idbranch in($strbr) and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by p.id_price_category_lab) intstk","intstk.id_price_category_lab = pc.id_price_category_lab","left");
            $this->db->join("(select sum(s.qty) as sale_qty,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.idbranch in($strbr) and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab and s.idgodown in(1,6) GROUP BY p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab","left");                
            $this->db->join("(select sum(p.norm_qty) as norm_qty,p.idpricecategory from price_category_norms p where p.idproductcategory = $product_category GROUP BY p.idpricecategory) pn","pn.idpricecategory = pc.id_price_category_lab","left");
            $this->db->from('price_category_lab pc');
            $query = $this->db->get();  
            return $query->result();
        }elseif ($idzone == 'allzone') {
            $this->db->select('z.id_zone, z.zone_name,pc.id_price_category_lab, pc.lab_name,pn.*,stk.curr_stock,sp.sale_qty,intstk.intra_stock');
            $this->db->where_in('z.id_zone', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock,st.idbranch,brr.idzone, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv,branch brr WHERE brr.idzone in(".$strbr.") and brr.is_warehouse = 0 and st.idbranch = brr.id_branch and st.idproductcategory = $product_category and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by brr.idzone, p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab and stk.idzone = z.id_zone","left");
            $this->db->join("(select sum(st.qty) as intra_stock,st.temp_idbranch,brr.idzone, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv,branch brr WHERE brr.idzone in(".$strbr.") and brr.is_warehouse = 0 and st.temp_idbranch = brr.id_branch and st.idproductcategory = $product_category and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by brr.idzone, p.id_price_category_lab) intstk","intstk.id_price_category_lab = pc.id_price_category_lab and intstk.idzone = z.id_zone","left");
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,brr.idzone,p.id_price_category_lab from sale_product s, price_category_lab p, branch brr WHERE brr.idzone in(".$strbr.") and brr.is_warehouse = 0 and s.idbranch = brr.id_branch and s.idproductcategory = $product_category and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab and s.idgodown in(1,6) GROUP BY brr.idzone,p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab and sp.idzone = z.id_zone","left");                
            $this->db->join("(select p.*,brr.idzone from price_category_norms p, branch brr where brr.idzone in(".$strbr.") and p.idbranch = brr.id_branch and brr.is_warehouse = 0 and p.idproductcategory = $product_category GROUP BY brr.idzone,p.idpricecategory) pn","pn.idpricecategory = pc.id_price_category_lab and pn.idzone = z.id_zone","left");
            $this->db->order_by('z.id_zone, pc.id_price_category_lab');
            $this->db->from('price_category_lab pc');
            $this->db->from('zone z');
            $query = $this->db->get();  
    //        die(print_r($query));
    //         die($this->db->last_query());
            return $query->result();
        }
        else {
            $this->db->select('br.id_branch, br.branch_name,zone.zone_name,pc.id_price_category_lab, pc.lab_name,pn.*,stk.curr_stock,sp.sale_qty,intstk.intra_stock');
            $this->db->where_in('br.id_branch', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock,st.idbranch, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idbranch in(".$strbr.") and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by st.idbranch,p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab and stk.idbranch = br.id_branch","left");
            $this->db->join("(select sum(st.qty) as intra_stock,st.temp_idbranch, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.temp_idbranch in(".$strbr.") and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab and st.idgodown in(1,6) group by st.temp_idbranch,p.id_price_category_lab) intstk","intstk.id_price_category_lab = pc.id_price_category_lab and intstk.temp_idbranch = br.id_branch","left");
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.idbranch in($strbr) and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab and s.idgodown in(1,6) GROUP BY s.idbranch,p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab and sp.idbranch = br.id_branch","left");                
            $this->db->join("(select * from price_category_norms p where p. idbranch in($strbr) and p.idproductcategory = $product_category GROUP BY p.idbranch,p.idpricecategory) pn","pn.idpricecategory = pc.id_price_category_lab and pn.idbranch = br.id_branch","left");
            $this->db->where('br.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('br.idzone,br.id_branch, pc.id_price_category_lab');
            $this->db->from('price_category_lab pc');
            $this->db->from('branch br');
            $query = $this->db->get();  
    //        die(print_r($query));
    //         die($this->db->last_query());
            return $query->result();
        }
    }
    /*
    public function get_price_cat_placement_data_report($product_category, $branch,$days, $idzone, $allbranchs){
//        die(print_r($_POST));
        $branches = array();
        if($idzone == ''){
            if($branch == 0){
                 $branches = explode(',', $allbranchs);
            }else{
                 $branches[] = $branch;
            }
        }else{
            if($idzone == 'all'){
                 $branches = explode(',', $allbranchs);
            }
            else{
                $badts = $this->db->where('idzone', $idzone)->get('branch')->result();
                foreach($badts as $bts){
                    $branches[] = $bts->id_branch;
                }
            }
        }
       
        $strbr = implode(',', $branches);
        
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));
        
        if($idzone == 'all'){
            $this->db->select('pc.id_price_category_lab, pc.lab_name,pn.*,stk.curr_stock,sp.sale_qty');
            $this->db->join("(select sum(st.qty) as curr_stock,p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab","left");
            $this->db->join("(select sum(s.qty) as sale_qty,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab GROUP BY p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab","left");                
            $this->db->join("(select * from price_category_norms p where p.idproductcategory = $product_category GROUP BY p.idpricecategory) pn","pn.idpricecategory = pc.id_price_category_lab","left");
            $this->db->from('price_category_lab pc');
            $query = $this->db->get();  
            return $query->result();
        }else {
            $this->db->select('br.id_branch, br.branch_name,zone.zone_name,pc.id_price_category_lab, pc.lab_name,pn.*,stk.curr_stock,sp.sale_qty');
            $this->db->where_in('br.id_branch', $branches);
            $this->db->join("(select sum(st.qty) as curr_stock,st.idbranch, p.id_price_category_lab,mv.mop from stock st,price_category_lab p,model_variants mv WHERE st.idproductcategory = $product_category and st.idbranch in(".$strbr.") and mv.id_variant = st.idvariant and mv.mop between p.min_lab and p.max_lab group by st.idbranch,p.id_price_category_lab) stk","stk.id_price_category_lab = pc.id_price_category_lab and stk.idbranch = br.id_branch","left");
            $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,p.id_price_category_lab from sale_product s, price_category_lab p WHERE s.idproductcategory = $product_category and s.idbranch in($strbr) and s.date between '$from' and '$to' and s.total_amount between p.min_lab and p.max_lab GROUP BY s.idbranch,p.id_price_category_lab) sp","sp.id_price_category_lab = pc.id_price_category_lab and sp.idbranch = br.id_branch","left");                
            $this->db->join("(select * from price_category_norms p where p. idbranch in($strbr) and p.idproductcategory = $product_category GROUP BY p.idbranch,p.idpricecategory) pn","pn.idpricecategory = pc.id_price_category_lab and pn.idbranch = br.id_branch","left");
            $this->db->where('br.idzone = zone.id_zone')->from('zone');
            $this->db->order_by('br.idzone,br.id_branch, pc.id_price_category_lab');
            $this->db->from('price_category_lab pc');
            $this->db->from('branch br');
            $query = $this->db->get();  
    //        die(print_r($query));
    //         die($this->db->last_query());
            return $query->result();
        }
    }
    */
    public function update_price_category_norms($data, $id_stock_norm){
        return $this->db->where('id_price_category_norms', $id_stock_norm)->update('price_category_norms', $data);
    }
    public function get_check_price_cat_data($product_category, $branch){
        return $this->db->where('idproductcategory', $product_category)
                        ->where('idbranch', $branch)
                        ->get('price_category_norms')->result();
    }
    public function get_daily_stock_data_manual($report_date,$idpcat,$idbranch) { 
        if($idbranch>0){
            if($idpcat>0){
                $this->db->select('branch.acc_branch_id,branch_category.branch_category_name,zone.zone_name,branch.branch_name,product_category.product_category_name,brand.brand_name,sum(stock.qty) as qty,branch.branch_name,sum(stock.mop) as mop,sum(stock.landing) as landing')
                ->where('stock.idproductcategory ="'. $idpcat.'"')
                ->where('stock.idbrand = brand.id_brand')->from('brand')
                ->group_by('stock.idbrand');
            }else{                
                $this->db->select('branch.acc_branch_id,branch_category.branch_category_name,zone.zone_name,product_category.product_category_name,sum(stock.qty) as qty,branch.branch_name,sum(stock.mop) as mop,sum(stock.landing) as landing ')                
                ->group_by('stock.idproductcategory,');
            }
            $this->db->where('(stock.idbranch="'. $idbranch.'" or stock.temp_idbranch="'. $idbranch.'")')
            ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
            ->join('zone','branch.idzone = zone.id_zone', 'left')
            ->where('stock.date ="'. $report_date.'"')
            ->where('stock.idbranch = branch.id_branch')->from('branch')
            ->where('stock.idgodown in (1,6)')
            ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category');
        }
        return $this->db->get('copy_daily_stock stock')->result(); 
         die($this->db->last_query());
    }
    public function get_monthly_stock_data_manual($monthyear,$idpcat,$idbranch) { 
        $from=$monthyear.'-01';
        $to=$monthyear.'-31';
        if($idbranch>0){
            if($idpcat>0){
                $this->db->select('branch.acc_branch_id,branch_category.branch_category_name,zone.zone_name,branch.branch_name,product_category.product_category_name,brand.brand_name,sum(stock.qty) as qty,branch.branch_name,sum(stock.mop) as mop,sum(stock.landing) as landing')
                ->where('stock.idproductcategory ="'. $idpcat.'"')
                ->where('stock.idbrand = brand.id_brand')->from('brand')
                ->group_by('stock.idbrand');
            }else{                
                $this->db->select('branch.acc_branch_id,branch_category.branch_category_name,zone.zone_name,product_category.product_category_name,sum(stock.qty) as qty,branch.branch_name,sum(stock.mop) as mop,sum(stock.landing) as landing ')                
                ->group_by('stock.idproductcategory');
            }
            
            $this->db->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left');
            $this->db->where('(stock.idbranch="'. $idbranch.'" or stock.temp_idbranch="'. $idbranch.'")')             
            ->join('zone','branch.idzone = zone.id_zone', 'left')
            ->where('stock.date between "'. $from.'" and "'. $to.'"')
            ->where('stock.idbranch = branch.id_branch')->from('branch')
            ->where('stock.idgodown in (1,6)')
            ->where('stock.idproductcategory = product_category.id_product_category')->from('product_category');            
            return $this->db->get('copy_daily_stock stock')->result(); 
            
        }elseif($idbranch=='all'){
            if($idpcat>0){
                $this->db->select('branch.acc_branch_id,stk_temp.intra_days,stk.days,branch.id_branch,product_category.product_category_name,branch_category.branch_category_name, zone.zone_name, branch.branch_name,stk_temp.intra_qty , stk.qty, stk_temp.intra_mop , stk.mop,stk_temp.intra_landing , stk.landing')                
                ->where('product_category.id_product_category='. $idpcat)->from('product_category')
                ->join('( SELECT count(DISTINCT `date`) as intra_days,SUM(stock.qty) AS intra_qty,SUM(stock.mop) AS intra_mop,SUM(stock.landing) AS intra_landing,stock.temp_idbranch AS bid FROM copy_daily_stock stock WHERE stock.idproductcategory="'. $idpcat.'" and stock.date BETWEEN "'.$from.'" AND "'.$to.'" AND stock.idgodown IN(1, 6) GROUP BY stock.temp_idbranch) stk_temp','stk_temp.bid=branch.id_branch', 'left')
                ->join('( SELECT count(DISTINCT `date`) as days,SUM(stock.qty) AS qty,SUM(stock.mop) AS mop,SUM(stock.landing) AS landing,stock.idbranch AS bid FROM copy_daily_stock stock WHERE stock.idproductcategory="'. $idpcat.'" and stock.date BETWEEN "'.$from.'" AND "'.$to.'" AND stock.idgodown IN(1, 6) GROUP BY stock.idbranch) stk','stk.bid=branch.id_branch', 'left');
                
            }else{                
                $this->db->select('branch.acc_branch_id,stk_temp.intra_days,stk.days,branch.id_branch,branch_category.branch_category_name, zone.zone_name, branch.branch_name,stk_temp.intra_qty , stk.qty, stk_temp.intra_mop , stk.mop,stk_temp.intra_landing , stk.landing')                
                ->join('( SELECT count(DISTINCT `date`) as intra_days, SUM(stock.qty) AS intra_qty,SUM(stock.mop) AS intra_mop,SUM(stock.landing) AS intra_landing,stock.temp_idbranch AS bid FROM copy_daily_stock stock WHERE   stock.date BETWEEN "'.$from.'" AND "'.$to.'" AND stock.idgodown IN(1, 6) GROUP BY stock.temp_idbranch) stk_temp','stk_temp.bid=branch.id_branch', 'left')
                ->join('( SELECT count(DISTINCT `date`) as days, SUM(stock.qty) AS qty,SUM(stock.mop) AS mop,SUM(stock.landing) AS landing,stock.idbranch AS bid FROM copy_daily_stock stock  WHERE   stock.date BETWEEN "'.$from.'" AND "'.$to.'" AND stock.idgodown IN(1, 6) GROUP BY stock.idbranch) stk','stk.bid=branch.id_branch', 'left');
                
            }
            $this->db->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
            ->join('zone','branch.idzone = zone.id_zone', 'left')
            ->where('branch.active', 1)
            ->order_by('zone_name');
            return $this->db->get('branch')->result(); 
            
        }
       
         die($this->db->last_query());
    }
    public function get_stock_backup_days($monthyear,$idbranch){
         $from=$monthyear.'-01';
        $to=$monthyear.'-31';
         $this->db->select(' count(DISTINCT `date`) as days')->where('date between "'. $from.'" and "'. $to.'"');
            if($idbranch>0){
            $this->db->where('idbranch', $idbranch);
            }     
            return   $this->db->get('copy_daily_stock')->row();
        die($this->db->last_query());
    }
    function get_stock_summary_accessories_API ($monthyear,$idbranch) {
        $url="http://117.247.86.62:8088/ssweb/index.php/stock/stock_analysis/api_daily_stock_report_byidbranch/".$monthyear.'/'.$idbranch;
        $data=array();
        $authorization=array();
        $result = $this->rest->request($url, "POST", $data, 0, $authorization);           
        return json_decode($result,true);
        
//        return $result;
    } 
   
}
