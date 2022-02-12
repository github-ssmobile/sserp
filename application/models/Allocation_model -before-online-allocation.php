<?php
class Allocation_model extends CI_Model {

    
    public function get_active_variants_by_model($modelid,$days) {
        $to=date('Y-m-d');
        $day='-'.$days.' days';
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));        
        $this->db->select('sp.sale_qty,brd.brand_name,mv.id_variant,mv.idmodel,mv.full_name,mv.idproductcategory,mv.idcategory,mv.idbrand,mv.idsku_type');
        $this->db->from('model_variants mv');                    
        $this->db->join("(select sum(s.qty) as sale_qty,s.idvariant from sale_product s WHERE s.idmodel=$modelid and s.date between '$from' and '$to' GROUP BY s.idvariant) sp", 'sp.idvariant=mv.id_variant', 'left');        
        $this->db->join('brand brd', 'mv.idbrand=brd.id_brand');
        $this->db->where_in('mv.idmodel',$modelid);
        $this->db->group_by('mv.id_variant');
        $this->db->order_by('mv.full_name');
        $this->db->where('mv.active', 1);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_variants_allocation_data($idwarehouse,$idbranch,$days,$variantid,$idgodown,$allocation_type) {                
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));          
        $this->db->select('mv.id_variant,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sp.sale_qty,sn.norm_qty');
        $this->db->from('branch b');                
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch from stock stm WHERE  stm.idgodown='.$idgodown.' and  stm.idvariant='.$variantid.' GROUP BY stm.temp_idbranch) stkm','`stkm`.`temp_idbranch`=`b`.`id_branch`','left');         
        $this->db->join('(select sn.quantity as norm_qty,sn.idbranch from stock_norms sn WHERE sn.idvariant='.$variantid.' GROUP BY sn.idbranch) sn','`sn`.`idbranch`=`b`.`id_branch`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idvariant=$variantid and s.idgodown=$idgodown and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch=b.id_branch', 'left');                
        if($allocation_type==''){}else{
            $this->db->select('sallo.allocated_qty');
            $this->db->join('(select sad.qty as allocated_qty,sad.idbranch from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.idwarehouse='.$idwarehouse.' and  sa.status=0 and sa.allocation_type='.$allocation_type.' and sad.idgodown='.$idgodown.' and  sad.idvariant='.$variantid.') sallo','`sallo`.`idbranch`=`b`.`id_branch`','left');         
        }
        $this->db->where('mv.active', 1)->where('mv.id_variant',$variantid)->from('model_variants mv');        
        $this->db->where('b.is_warehouse', 0);
        if($idbranch){
            $this->db->where('b.id_branch', $idbranch);    
        } 
        $this->db->where('b.active', 1); 
        if ($idwarehouse != 7) {
            $this->db->where('b.idwarehouse', $idwarehouse);
        }
        $this->db->order_by('b.idzone,b.id_branch');
        $this->db->group_by('b.id_branch');
        $query = $this->db->get(); 
        return $query->result();
//        die(print_r($this->db->last_query()));
    } 
    public function get_all_branch_allocation($status,$allocation_type){
        return $this->db->where('status', $status)->where('allocation_type', $allocation_type)->get('stock_allocation')->result();
    }
    public function get_branch_allocation($idwarehouse,$idbranch,$status,$allocation_type){
        return $this->db->where('idwarehouse', $idwarehouse)->where('idbranch', $idbranch)->where('status', $status)->where('allocation_type', $allocation_type)->get('stock_allocation')->row();
    }
    public function save_branch_stock_allocation($data) {
        $this->db->insert('stock_allocation', $data);
        return $this->db->insert_id();
    }    
    public function delete_branch_stock_allocation_by_variant($idwarehouse,$variants,$idgodown,$allocation_type) {
        $qy="delete sad from stock_allocation_data sad inner join stock_allocation sa on sa.id_stock_allocation = sad.idstock_allocation where sa.idwarehouse=$idwarehouse and sa.allocation_type =$allocation_type and sa.status=0 and  sad.idgodown=$idgodown and sad.idvariant in (".implode(',',$variants).");";                                   
        return $this->db->query($qy);                
    }
    public function delete_branch_route_allocation_by_variant($idwarehouse,$variants,$idbranch,$idgodown,$allocation_type) {
        $qy="delete sad from stock_allocation_data sad inner join stock_allocation sa on sa.id_stock_allocation = sad.idstock_allocation where sa.idwarehouse=$idwarehouse and sa.allocation_type =$allocation_type and sa.status=0 and sad.idgodown=$idgodown and sad.idvariant in(".implode(',',$variants).") and sad.idbranch in (".implode(',',$idbranch).");";                                   
        return $this->db->query($qy);                
    }
    public function delete_branch_allocation_by_variant($idwarehouse,$idbranch,$allocation_type){                
        $qy="delete sad from stock_allocation_data sad inner join stock_allocation sa on sa.id_stock_allocation = sad.idstock_allocation where sa.idwarehouse=$idwarehouse and sa.allocation_type =$allocation_type and sa.status = 0 and sad.idbranch=$idbranch";                                   
        return $this->db->query($qy);            
    }
    public function delete_branch_allocation_by_allocationid($idstock_allocation){               
        $qy="delete sad from stock_allocation_data sad,stock_allocation sa  where sa.id_stock_allocation=$idstock_allocation and sad.idstock_allocation=$idstock_allocation";                                   
        return $this->db->query($qy);            
    } 
    public function update_allocation_status($ids,$data,$branch_id) {          
        $id_s=$ids;
       /* if(count($branch_id)>0){            
            $del_ids=array();            
            for($i=0;$i<count($ids);$i++){            
               $alloc=$this->db->where('id_stock_allocation!='.$ids[$i])->where('status', 0)->where('idbranch', $branch_id[$i])->get('stock_allocation')->result();          
               if(count($alloc)>0){
                   foreach ($alloc as $ac){
                    $d=array('idstock_allocation' => $ids[$i]);
                    $this->db->where('idbranch', $branch_id[$i])->where('idstock_allocation', $ac->id_stock_allocation)->update('stock_allocation_data', $d);               
                    $del_ids[]=$ac->id_stock_allocation;
                   }
               $i=$i+(count($alloc)+1);
               }
            }     
        if(count($del_ids)>0){
            $this->db->where_in('id_stock_allocation', $del_ids)->delete('stock_allocation');}
        }*/
         return $this->db->where_in('id_stock_allocation ', $id_s)->update('stock_allocation', $data);
         
    }
    public function save_db_branch_allocation($data_att) {        
        return $this->db->insert_batch('stock_allocation_data', $data_att);
    }
    public function get_stock_allocation_by_status($iduser,$status,$idwarehouse){
        return $this->db->select('stock_allocation.*, branch.branch_name, count(stock_allocation_data.id_stock_allocation_data) as sum_product, sum(stock_allocation_data.qty) as sum_qty')
                        ->where('stock_allocation.status',$status)              
                        ->where('stock_allocation.idwarehouse',$idwarehouse)     
                        ->where('((`stock_allocation`.`allocate_by` = '.$iduser.' AND `stock_allocation`.`allocation_type` = 0) OR `stock_allocation`.`allocation_type` > 0)')
                        ->where('stock_allocation.id_stock_allocation = stock_allocation_data.idstock_allocation')->from('stock_allocation_data')
                        ->where('branch.id_branch = stock_allocation.idbranch')->from('branch')
                        ->group_by('stock_allocation.id_stock_allocation')
                        ->order_by('stock_allocation.id_stock_allocation', 'desc')
                        ->get('stock_allocation')->result(); 
//         die(print_r($this->db->last_query()));
    }    
    public function edit_stock_allocation($idallocation, $data) {
        return $this->db->where('id_stock_allocation', $idallocation)->update('stock_allocation', $data);
    }
    public function get_route_allocation_data($idproductcategory,$idbrand,$days,$idgodown,$allocation_type,$idroute,$warehouse) {              
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));          
        $this->db->select('mv.idmodel,mv.idcategory,mv.idsku_type,mv.id_variant,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sp.sale_qty,snn.norm_qty,sallo.allocated_qty,stk_ho.ho_stock_qty');
        $this->db->from('model_variants mv'); 
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                 
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant from stock stm WHERE  stm.idgodown='.$idgodown.' and stm.idbrand='.$idbrand.' and  stm.idproductcategory='.$idproductcategory.' GROUP BY stm.idvariant,stm.idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');         
        $this->db->join('(select sn.quantity as norm_qty,sn.idbranch,sn.idvariant from stock_norms sn WHERE sn.idbrand='.$idbrand.' and  sn.idproductcategory='.$idproductcategory.' GROUP BY sn.idvariant,sn.idbranch) snn','`snn`.`idbranch` = `b`.`id_branch` and `snn`.`idvariant` = `mv`.`id_variant`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,s.idvariant from sale_product s WHERE s.idgodown=$idgodown and s.date between '$from' and '$to' and s.idbrand=$idbrand and  s.idproductcategory=$idproductcategory GROUP BY s.idvariant,s.idbranch) sp", '`sp`.`idbranch` = `b`.`id_branch` and `sp`.`idvariant` = `mv`.`id_variant`', 'left');                
        $this->db->join('(select sad.qty as allocated_qty,sad.idbranch,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0 and sa.allocation_type='.$allocation_type.' and sad.idgodown='.$idgodown.' and  sad.idbrand='.$idbrand.' and  sad.idproductcategory='.$idproductcategory.') sallo','`sallo`.`idbranch` = `b`.`id_branch` and `sallo`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->where('mv.active', 1)->where_in('b.idroute', $idroute)->from('branch b');        
        $this->db->where('b.is_warehouse', 0);
        $this->db->where('mv.idproductcategory', $idproductcategory);
        $this->db->where('mv.idbrand', $idbrand);        
        $this->db->where('b.active', 1);
        $this->db->order_by('stk_ho.ho_stock_qty,mv.id_variant','desc');
        $this->db->group_by('mv.id_variant,b.id_branch');
        $query = $this->db->get(); 
//        die(print_r($this->db->last_query()));
        return $query->result();
    } 
	public function get_gift_route_allocation_data($idproductcategory,$idbrand,$days,$idgodown,$allocation_type,$idroute,$warehouse) {              
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));          
        $this->db->select('mv.landing,mv.idmodel,mv.idcategory,mv.idsku_type,mv.id_variant,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,stkm.intra_stock_qty,sallo.allocated_qty,stk_ho.ho_stock_qty');
        $this->db->from('model_variants mv'); 
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                 
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant,st.idbranch) stk','`stk`.`idbranch` = `b`.`id_branch` and `stk`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant from stock stm WHERE  stm.idgodown='.$idgodown.' and stm.idbrand='.$idbrand.' and  stm.idproductcategory='.$idproductcategory.' GROUP BY stm.idvariant,stm.idbranch) stkm',' `stkm`.`temp_idbranch` = `b`.`id_branch` and `stkm`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->join('(select sad.qty as allocated_qty,sad.idbranch,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0 and sa.allocation_type='.$allocation_type.' and sad.idgodown='.$idgodown.' and  sad.idbrand='.$idbrand.' and  sad.idproductcategory='.$idproductcategory.') sallo','`sallo`.`idbranch` = `b`.`id_branch` and `sallo`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->where('mv.active', 1)->where_in('b.idroute', $idroute)->from('branch b');        
        $this->db->where('b.is_warehouse', 0);
        $this->db->where('mv.idproductcategory', $idproductcategory);
        $this->db->where('mv.idbrand', $idbrand);        
        $this->db->where('b.active', 1);
        $this->db->order_by('stk_ho.ho_stock_qty,mv.id_variant','desc');
        $this->db->group_by('mv.id_variant,b.id_branch');
        $query = $this->db->get(); 
//        die(print_r($this->db->last_query()));
        return $query->result();
    } 
    public function get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type) {                             
        $this->db->select('mv.*,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk_ho.ho_stock_qty,sallo.allocated_qty,callo.c_allocated_qty');
        $this->db->from('model_variants mv');
        if($variantid){
            $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and st.idvariant='.$variantid.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                 
            $this->db->join('(select sum(sad.qty) as allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status < 3 )  and sa.idwarehouse='.$warehouse.'  and sad.idgodown='.$idgodown.' and  sad.idvariant='.$variantid.'  GROUP BY sad.idvariant) sallo','`sallo`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(sad.qty) as c_allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse='.$warehouse.'  and sa.allocation_type='.$allocation_type.' and sad.idgodown='.$idgodown.' and  sad.idvariant='.$variantid.'  GROUP BY sad.idvariant) callo','`callo`.`idvariant` = `mv`.`id_variant`','left');                     
            $this->db->where('mv.id_variant',$variantid);    
        }else{
            $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                 
            $this->db->join('(select sum(sad.qty) as allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status < 3 ) and sa.idwarehouse='.$warehouse.' and sad.idgodown='.$idgodown.' and  sad.idbrand='.$idbrand.' and  sad.idproductcategory='.$idproductcategory.'  GROUP BY sad.idvariant) sallo','`sallo`.`idvariant` = `mv`.`id_variant`','left');                 
            $this->db->join('(select sum(sad.qty) as c_allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse='.$warehouse.'  and sa.allocation_type='.$allocation_type.' and sad.idgodown='.$idgodown.' and  sad.idbrand='.$idbrand.' and  sad.idproductcategory='.$idproductcategory.'  GROUP BY sad.idvariant) callo','`callo`.`idvariant` = `mv`.`id_variant`','left');                     
            $this->db->where('mv.idproductcategory', $idproductcategory);
        }        
        $this->db->where('mv.active', 1)->from('branch b');        
        $this->db->where('b.id_branch', $warehouse);        
        $this->db->where('mv.idbrand', $idbrand);  
        if($modelid){
            $this->db->where('mv.idmodel',$modelid);    
        }        
        $this->db->where('b.active', 1);
        $this->db->order_by('b.id_branch');
        $this->db->group_by('b.id_branch,mv.id_variant');
        $query = $this->db->get(); 
        return $query->result(); 
//        die(print_r($this->db->last_query()));
    } 
    
    public function get_warehouse_stock_data_fr_analysis($idproductcategory,$idbrand,$idgodown,$warehouse) {                             
        $this->db->select('mv.*,mv.full_name,mv.idmodel,`b`.`id_branch`, `b`.`branch_name`, stk_ho.ho_stock_qty,sallo.allocated_qty');
        $this->db->from('model_variants mv');
        
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant from stock st WHERE  st.idgodown='.$idgodown.' and st.idbranch='.$warehouse.' and st.idbrand='.$idbrand.' and  st.idproductcategory='.$idproductcategory.' GROUP BY st.idvariant) stk_ho','`stk_ho`.`idvariant` = `mv`.`id_variant`','left');                                 
        $this->db->join('(select sum(sad.qty) as allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status=0 or sa.status=1) and sad.idgodown='.$idgodown.' and  sad.idbrand='.$idbrand.' and  sad.idproductcategory='.$idproductcategory.'  GROUP BY sad.idvariant) sallo','`sallo`.`idvariant` = `mv`.`id_variant`','left');                 
        $this->db->where('mv.idproductcategory', $idproductcategory);
        $this->db->where('mv.idbrand', $idbrand);
                
        $this->db->where('mv.active', 1)->from('branch b');        
        $this->db->where('b.id_branch', $warehouse);        
        $this->db->where('mv.idbrand', $idbrand);                
        $this->db->where('b.active', 1);
        $this->db->order_by('b.id_branch');
        $this->db->group_by('b.id_branch,mv.id_variant');
        $query = $this->db->get(); 
//        die(print_r($this->db->last_query()));
        return $query->result(); 
    } 
    
    
    public function get_same_variants_for_allocation($variantid,$warehouse,$idproductcategory,$idbrand,$idgodown,$allocation_type,$modelid,$type) { 
        $qy="";
        if($type==0){
        $qy="select sum(stk.qty) as ho_stock_qty,sallo.allocated_qty,callo.c_allocated_qty,mv.* from model_variants mv left join stock stk on stk.idvariant=mv.id_variant and stk.idgodown=$idgodown and idbranch=$warehouse left join "
                . "(select sum(sad.qty) as allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status < 3) and sa.idwarehouse=$warehouse and sad.idgodown=$idgodown and  sad.idbrand=$idbrand and  sad.idproductcategory=$idproductcategory  GROUP BY sad.idvariant) sallo on `sallo`.`idvariant` = `mv`.`id_variant` left join "
                . "(select sum(sad.qty) as c_allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse=$warehouse  and sa.allocation_type=$allocation_type and sad.idgodown=$idgodown and  sad.idbrand=$idbrand and  sad.idproductcategory=$idproductcategory  GROUP BY sad.idvariant) callo on `callo`.`idvariant` = `mv`.`id_variant` "
                . "where id_variant in (select v.idvariant from (select `idvariant`,`idmodel`, MAX(CASE WHEN `idattribute`=9 THEN `attribute_value` END) ram, MAX(CASE WHEN `idattribute`=8 THEN `attribute_value` END) rom from model_variants_attribute group by `idvariant`,`idmodel`) as v inner join (select `idvariant`,idmodel, MAX(CASE WHEN `idattribute`=9 THEN `attribute_value` END) ram, MAX(CASE WHEN `idattribute`=8 THEN `attribute_value` END) rom from model_variants_attribute where `idvariant`=$variantid group by `idvariant`) as b on b.ram=v.ram and b.rom=v.rom WHERE v.`idmodel`=b.`idmodel`) and active=1  group by mv.id_variant ORDER BY FIELD(mv.id_variant, $variantid) DESC";
        }else{
        $qy="select sum(stk.qty) as ho_stock_qty,sallo.allocated_qty,callo.c_allocated_qty,mv.* from model_variants mv left join stock stk on stk.idvariant=mv.id_variant and stk.idgodown=$idgodown and idbranch=$warehouse left join "
                . "(select sum(sad.qty) as allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status < 3)  and sa.idwarehouse=$warehouse  and sad.idgodown=$idgodown and  sad.idbrand=$idbrand and  sad.idproductcategory=$idproductcategory  GROUP BY sad.idvariant) sallo on `sallo`.`idvariant` = `mv`.`id_variant` left join "
                . "(select sum(sad.qty) as c_allocated_qty,sad.idvariant from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse=$warehouse  and sa.allocation_type=$allocation_type and sad.idgodown=$idgodown and  sad.idbrand=$idbrand and  sad.idproductcategory=$idproductcategory  GROUP BY sad.idvariant) callo on `callo`.`idvariant` = `mv`.`id_variant` "
                . "where mv.idmodel=$modelid  and active=1  group by mv.id_variant ORDER BY FIELD(mv.id_variant, $variantid) DESC";
            
        }
        
        $query = $this->db->query($qy);                
        return $query->result(); 
//        return $query->result_array();    
    }
    
    public function get_variants_by_model($id,$warehouse) {
        $this->db->select('mv.*,sum(st.qty) as stock_qty');
        $this->db->from('model_variants mv');                
        $this->db->join('stock st','`st`.`idvariant`=`mv`.`id_variant` and st.idbranch='.$warehouse,'left');         
        $this->db->where('mv.active', 1);
        $this->db->where('mv.idmodel',$id);
        $this->db->group_by('mv.id_variant');
        $query = $this->db->get();
//      return $this->db->last_query();
        return $query->result_array();
    }     
    
     public function get_branch_allocation_by_id($idallocation,$idoutward) {         
        $this->db->select('b.branch_name,sa.date as all_date,b.idcompany,sd.*,sa.*,sa.idbranch as id_branch,sa.idwarehouse as id_warehouse,sa.status as a_status,od.status as o_status,g.godown_name,mv.full_name,od.*,op.price,op.cgst_per,op.idoutward,GROUP_CONCAT(op.imei_no) as imei');
        $this->db->from('stock_allocation sa');        
        $this->db->join('stock_allocation_data sd','sd.idstock_allocation=sa.id_stock_allocation');                
        $this->db->join('model_variants mv','mv.id_variant=sd.idvariant');
        $this->db->where('g.id_godown=sd.idgodown')->from('godown g');        
        $this->db->where('sa.idbranch=b.id_branch')->from('branch b'); 
        $this->db->join('outward od','`od`.`idstock_allocation` = `sa`.`id_stock_allocation`','left');                                 
        $this->db->join('outward_product op','`op`.`idvariant` = `sd`.`idvariant` and op.idgodown=sd.idgodown and op.idoutward=od.id_outward','left');                                 
        if($idallocation){
            $this->db->where('sa.id_stock_allocation', $idallocation);         
        }elseif($idoutward){
            $this->db->where('od.id_outward', $idoutward);            
        }
        $this->db->group_by('sd.idgodown,sd.idvariant');
        $query = $this->db->get(); 
        return $query->result(); 
//        die(print_r($this->db->last_query()));
    }
    
     public function get_branch_allocation_by_id_for_outward($idallocation) {         
        $this->db->select('b.branch_name,b.idcompany,sd.*,sa.*,u.user_name,u.id_users,g.godown_name,mv.*');
        $this->db->from('stock_allocation sa');        
        $this->db->join('stock_allocation_data sd','sd.idstock_allocation=sa.id_stock_allocation');                
        $this->db->join('model_variants mv','mv.id_variant=sd.idvariant');
        $this->db->where('g.id_godown=sd.idgodown')->from('godown g');        
        $this->db->where('sa.idbranch=b.id_branch')->from('branch b');                
        $this->db->where('u.id_users=sd.created_by')->from('users u');       
        $this->db->where('sa.id_stock_allocation', $idallocation);              
        $this->db->where('mv.active', 1);                      
        $this->db->order_by('mv.id_variant');
        $query = $this->db->get(); 
//        die(print_r($this->db->last_query()));
        return $query->result(); 
    }
    
    public function get_branch_allocation_stock_data($branch,$days,$warehouse,$allocation_type) { 
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));  
        $this->db->select('u.user_name,u.id_users,sd.id_stock_allocation_data,snn.norm_qty,stk_ho.ho_stock_qty,sallo.allocated_qty,callo.c_allocated_qty,ballo.callocated_qty,sp.sale_qty,stkm.intra_stock_qty,bstk.stock_qty,g.*,mv.*');
        $this->db->from('stock_allocation_data sd');        
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE st.idbranch='.$warehouse.' GROUP BY st.idvariant,st.idgodown) stk_ho','`stk_ho`.`idvariant` = `sd`.`idvariant` and stk_ho.idgodown=sd.idgodown','left');                                 
        $this->db->join('(select sum(sad.qty) as allocated_qty,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status < 3) and sa.idwarehouse='.$warehouse.'  GROUP BY sad.idvariant,sad.idgodown) sallo','`sallo`.`idvariant` = `sd`.`idvariant` and sallo.idgodown=sd.idgodown','left');                 
        $this->db->join('(select sum(sad.qty) as c_allocated_qty,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse='.$warehouse.'  and sa.allocation_type='.$allocation_type.' GROUP BY sad.idvariant,sad.idgodown) callo','`callo`.`idvariant` = `sd`.`idvariant` and callo.idgodown=sd.idgodown','left');                     
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idbranch='.$branch.' GROUP BY st.idvariant,st.idgodown) bstk','`bstk`.`idgodown` = `sd`.`idgodown` and `bstk`.`idvariant` = `sd`.`idvariant`','left');                 
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.temp_idbranch='.$branch.' GROUP BY stm.idvariant,stm.idgodown) stkm',' `stkm`.`idgodown` = `sd`.`idgodown` and `stkm`.`idvariant` = `sd`.`idvariant`','left');         
        $this->db->join('(select sn.quantity as norm_qty,sn.idbranch,sn.idvariant from stock_norms sn WHERE sn.idbranch='.$branch.' GROUP BY sn.idvariant,sn.idbranch) snn','`snn`.`idbranch` = `sd`.`idbranch` and `snn`.`idvariant` = `sd`.`idvariant`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,s.idvariant,s.idgodown from sale_product s WHERE s.date between '$from' and '$to' and s.idbranch=$branch GROUP BY s.idvariant,s.idgodown) sp", '`sp`.`idgodown` = `sd`.`idgodown` and `sp`.`idvariant` = `sd`.`idvariant`', 'left');                
        $this->db->join('(select sad.qty as callocated_qty,sad.idbranch,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status=0  and sa.idwarehouse='.$warehouse.'   and sa.allocation_type='.$allocation_type.' and sa.idbranch='.$branch.') ballo','`ballo`.`idgodown` = `sd`.`idgodown` and `ballo`.`idvariant` = `sd`.`idvariant`','left');                 
        $this->db->join('model_variants mv','mv.id_variant=sd.idvariant');                
        $this->db->where('sa.status',0)->where('sa.allocation_type', $allocation_type)->where('sa.idbranch', $branch)->from('stock_allocation sa');
        $this->db->where('g.id_godown=sd.idgodown')->from('godown g');
        $this->db->where('sd.idstock_allocation=sa.id_stock_allocation'); 
        $this->db->where('sa.idwarehouse',$warehouse); 
        
        $this->db->where('u.id_users=sd.created_by')->from('users u');  
        $this->db->where('mv.active', 1);              
        $this->db->order_by('mv.id_variant');
        $this->db->group_by('sd.idgodown,sd.idvariant');
        $query = $this->db->get(); 
        return $query->result(); 
//        die(print_r($this->db->last_query()));
    }
    public function get_branch_allocation_stock_data_manager($branch,$days,$warehouse,$allocation_type) { // all status for manager
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to)));  
        $this->db->select('sa.status,u.user_name,u.id_users,sd.id_stock_allocation_data,snn.norm_qty,stk_ho.ho_stock_qty,sallo.allocated_qty,callo.c_allocated_qty,ballo.callocated_qty,sp.sale_qty,stkm.intra_stock_qty,bstk.stock_qty,g.*,mv.*');
        $this->db->from('stock_allocation_data sd');        
        $this->db->join('(select sum(st.qty) as ho_stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE st.idbranch='.$warehouse.' GROUP BY st.idvariant,st.idgodown) stk_ho','`stk_ho`.`idvariant` = `sd`.`idvariant` and stk_ho.idgodown=sd.idgodown','left');                                 
        $this->db->join('(select sum(sad.qty) as allocated_qty,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE (sa.status=0 or sa.status=1) GROUP BY sad.idvariant,sad.idgodown) sallo','`sallo`.`idvariant` = `sd`.`idvariant` and sallo.idgodown=sd.idgodown','left');                 
        $this->db->join('(select sum(sad.qty) as c_allocated_qty,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status < 3 and sa.allocation_type='.$allocation_type.' GROUP BY sad.idvariant,sad.idgodown) callo','`callo`.`idvariant` = `sd`.`idvariant` and callo.idgodown=sd.idgodown','left');                     
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch,st.idvariant,st.idgodown from stock st WHERE  st.idbranch='.$branch.' GROUP BY st.idvariant,st.idgodown) bstk','`bstk`.`idgodown` = `sd`.`idgodown` and `bstk`.`idvariant` = `sd`.`idvariant`','left');                 
        $this->db->join('(select sum(stm.qty) as intra_stock_qty,stm.temp_idbranch,stm.idvariant,stm.idgodown from stock stm WHERE  stm.temp_idbranch='.$branch.' GROUP BY stm.idvariant,stm.idgodown) stkm',' `stkm`.`idgodown` = `sd`.`idgodown` and `stkm`.`idvariant` = `sd`.`idvariant`','left');         
        $this->db->join('(select sn.quantity as norm_qty,sn.idbranch,sn.idvariant from stock_norms sn WHERE sn.idbranch='.$branch.' GROUP BY sn.idvariant,sn.idbranch) snn','`snn`.`idbranch` = `sd`.`idbranch` and `snn`.`idvariant` = `sd`.`idvariant`','left');        
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch,s.idvariant,s.idgodown from sale_product s WHERE s.date between '$from' and '$to' and s.idbranch=$branch GROUP BY s.idvariant,s.idgodown) sp", '`sp`.`idgodown` = `sd`.`idgodown` and `sp`.`idvariant` = `sd`.`idvariant`', 'left');                
        $this->db->join('(select sad.qty as callocated_qty,sad.idbranch,sad.idvariant,sad.idgodown from stock_allocation_data sad inner join stock_allocation sa on sad.idstock_allocation=sa.id_stock_allocation WHERE sa.status < 3 and sa.allocation_type='.$allocation_type.' and sa.idbranch='.$branch.') ballo','`ballo`.`idgodown` = `sd`.`idgodown` and `ballo`.`idvariant` = `sd`.`idvariant`','left');                 
        $this->db->join('model_variants mv','mv.id_variant=sd.idvariant');                
        $this->db->where('sa.status < 3')->where('sa.allocation_type', $allocation_type)->where('sa.idbranch', $branch)->from('stock_allocation sa');
        $this->db->where('g.id_godown=sd.idgodown')->from('godown g');
        $this->db->where('sd.idstock_allocation=sa.id_stock_allocation');  
        $this->db->where('sa.idwarehouse',$warehouse); 
        $this->db->where('u.id_users=sd.created_by')->from('users u');       
        $this->db->where('mv.active', 1);              
        $this->db->order_by('mv.id_variant');
        $this->db->group_by('sd.idgodown,sd.idvariant');
        $query = $this->db->get(); 
        return $query->result(); 
//        die(print_r($this->db->last_query()));
    }
    
    public function delete_stock_allocation_data($id) {         
        $this->db->trans_begin();
        $this->db->where('id_stock_allocation_data', $id)->delete('stock_allocation_data');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return 0;
        } else {                  
            $this->db->trans_commit();
            return 1;
        }
    }
    public function get_stock_allocation_by_idwarehouse( $idwarehouse) { // status <3
        $this->db->select('stock_allocation.*, branch.branch_name, count(stock_allocation_data.id_stock_allocation_data) as sum_product, sum(stock_allocation_data.qty) as sum_qty');        
        $this->db->where('stock_allocation.status < 3');
        if ($idwarehouse != '') {
            $this->db->where('stock_allocation.idwarehouse', $idwarehouse);
        }
        $this->db->where('stock_allocation.id_stock_allocation = stock_allocation_data.idstock_allocation')->from('stock_allocation_data')
                ->where('branch.id_branch = stock_allocation.idbranch')->from('branch')
                ->group_by('stock_allocation.id_stock_allocation')
                ->order_by('stock_allocation.id_stock_allocation', 'desc');
        return $this->db->get('stock_allocation')->result();
//         die(print_r($this->db->last_query()));
    }
    public function get_stock_allocation_by_status_idbranch_date($status = '', $idbranch = '', $datefrom = '', $dateto = '', $idwarehouse) {

        $this->db->select('od.status as out_status,stock_allocation.status as all_status,od.*,zone.zone_name,route.route_name,stock_allocation.*, branch.branch_name, count(stock_allocation_data.id_stock_allocation_data) as sum_product, sum(stock_allocation_data.qty) as sum_qty');
        if ($status != '') {
            $this->db->where('stock_allocation.status', $status);
        }
        if ($idbranch != '') {
            $this->db->where('stock_allocation.idbranch', $idbranch);
        }
        if ($idwarehouse != '') {
            $this->db->where('stock_allocation.idwarehouse', $idwarehouse);
        }
        if ($idbranch == '' && $datefrom == '' && $dateto == '' && $status == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-30 days", strtotime($dateto)));
        }
        if ($datefrom != '' && $dateto != '') {
            $this->db->where('stock_allocation.date >=', $datefrom)->where('stock_allocation.date <=', $dateto);
        }
        $this->db->join('outward od','`od`.`idstock_allocation` = `stock_allocation`.`id_stock_allocation`','left');  
        $this->db->where('branch.idroute = route.id_route')->from('route'); 
        $this->db->where('branch.idzone = zone.id_zone')->from('zone'); 
        $this->db->where('stock_allocation.id_stock_allocation = stock_allocation_data.idstock_allocation')->from('stock_allocation_data')
                ->where('branch.id_branch = stock_allocation.idbranch')->from('branch')
                ->group_by('stock_allocation.id_stock_allocation')
                ->order_by('route.route_name,branch.branch_name,stock_allocation.id_stock_allocation', 'desc');
        return $this->db->get('stock_allocation')->result();
//         die(print_r($this->db->last_query()));
    }
    
public function get_ready_to_outward_stock_allocation($idwarehouse,$userid) {

        $this->db->select('stock_allocation.*, branch.branch_name, count(stock_allocation_data.id_stock_allocation_data) as sum_product, sum(stock_allocation_data.qty) as sum_qty');
        $this->db->where('stock_allocation.status', 2);        
        $this->db->where('stock_allocation.idwarehouse', $idwarehouse);        
        $this->db->where("(stock_allocation.scan_by=0 OR stock_allocation.scan_by='$userid')", NULL, FALSE);
        $this->db->where('stock_allocation.id_stock_allocation = stock_allocation_data.idstock_allocation')->from('stock_allocation_data')
                ->where('branch.id_branch = stock_allocation.idbranch')->from('branch')
                ->group_by('stock_allocation.id_stock_allocation')
                ->order_by('stock_allocation.id_stock_allocation', 'desc');
        return $this->db->get('stock_allocation')->result();
//         die(print_r($this->db->last_query()));
    }
	
	 public function get_active_branchs_forallocation($idbrand){
         $this->db->select('*')->where('b.is_warehouse', 0)  
                        ->where('b.active', 1)
                        ->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc')
                        ->where('b.idzone = z.id_zone')->from('zone z');
                $this->db->join('(SELECT COUNT(user_has_brand.`iduser`) as brand_promoter,users.idbranch FROM `user_has_brand`,users WHERE user_has_brand.iduser=users.id_users and users.iduserrole=17 and user_has_brand.idbrand='.$idbrand.' and users.active=1 GROUP by users.idbranch) ub','`ub`.`idbranch`=`b`.`id_branch`','left');         
        return $this->db->order_by('b.idzone,b.id_branch')
                        ->get('branch b')->result();
    }
    
    public function get_branches_by_warehouseid_forallocation($warehouse,$idbrand){
         $this->db->select('*')->where('b.is_warehouse', 0)  
                        ->where('b.active', 1)
                        ->where('b.idbranchcategory = bc.id_branch_category')->from('branch_category bc')
                        ->where('b.idwarehouse', $warehouse)
                        ->where('b.idzone = z.id_zone')->from('zone z');
                $this->db->join('(SELECT COUNT(user_has_brand.`iduser`) as brand_promoter,users.idbranch FROM `user_has_brand`,users WHERE user_has_brand.iduser=users.id_users and users.iduserrole=17 and user_has_brand.idbrand='.$idbrand.' and users.active=1 GROUP by users.idbranch) ub','`ub`.`idbranch`=`b`.`id_branch`','left');         
        return  $this->db->order_by('b.idzone,b.id_branch')                 
                        ->get('branch b')->result();
    } 

    
    
}
