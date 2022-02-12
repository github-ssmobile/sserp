<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Outward_model extends CI_Model{
   
   
    public function save_outward($data) {
        $this->db->insert('outward', $data);
        return $this->db->insert_id();
    }   
    public function save_outward_product($data) {        
        return $this->db->insert_batch('outward_product', $data);         
    }
    public function update_batch_stock_byimei($data) {
        return $this->db->update_batch('stock', $data, 'imei_no'); 
    }
    
    public function get_outward_by_status_idbranch_date($status = '', $idbranch = '', $datefrom = '', $dateto = '', $idwarehouse) {

        $this->db->select('outward.*, branch.branch_name, count(outward_product.idoutward) as sum_product, sum(outward_product.qty) as sum_qty');
        if ($status != '') {
            $this->db->where('outward.status', $status);
        }
        if ($idbranch != '') {
            $this->db->where('outward.idbranch', $idbranch);
        }
        if ($idbranch == '' && $datefrom == '' && $dateto == '' && $status == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-30 days", strtotime($dateto)));
        }
        if ($datefrom != '' && $dateto != '') {
            $this->db->where('outward.date >=', $datefrom)->where('outward.date <=', $dateto);
        }
        $this->db->where('outward.id_outward = outward_product.idoutward')->from('outward_product');        
         if ($idwarehouse != '') {
            $this->db->where('outward.idwarehouse', $idwarehouse);
        }
        $this->db->where('branch.id_branch = outward.idbranch')->from('branch')
        ->group_by('outward.id_outward')
        ->order_by('outward.id_outward', 'desc');
        return $this->db->get('outward')->result();
//         die(print_r($this->db->last_query()));
    }
    public function get_outward_data_by_idbranch_date($idbranch = '', $datefrom = '', $dateto = '', $idwarehouse = '',$report_type,$date_filter='date') {

        $this->db->select('outward.gst_type,outward.shipment_received_remark,outward.outward_remark,outward.shipment_remark,outward.dispatch_type,outward.courier_name,outward.po_lr_no,outward.status as out_status,outward.no_of_boxes,outward.dispatch_date,outward.shipment_received_date,op.*,b.branch_name as branch_to,stk.branch_name as branch_from,g.godown_name,mv.full_name,brd.brand_name');        
//        $this->db->where('outward.status', 2);        
        if ($idbranch != '') {
            $this->db->where('outward.idbranch', $idbranch);
        }
        if ($idwarehouse == '' || $idwarehouse==0) {
            $this->db->where_in('outward.idwarehouse', '7,18');            
        }else{            
            $this->db->where('outward.idwarehouse', $idwarehouse);
        }
        $this->db->join('outward_product op','outward.id_outward = op.idoutward');
        $this->db->join('model_variants mv','mv.id_variant=op.idvariant');
        $this->db->where('g.id_godown=op.idgodown')->from('godown g');   
        $this->db->where('brd.id_brand=op.idbrand')->from('brand brd');   
        if ($datefrom == '' && $dateto == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-2 days", strtotime($dateto)));
        }
         if ($datefrom != '' && $dateto != '') {
            if($report_type=='inward'){
                $this->db->where("outward.shipment_received_date between '$datefrom' and '$dateto'");
//                $this->db->where('outward.shipment_received_date >=', $datefrom)->where('outward.shipment_received_date <=', $dateto);                            
            }else{
//                $this->db->where("outward.scan_time between '$datefrom' and '$dateto'");
                $this->db->where("outward.".$date_filter." between '$datefrom' and '$dateto'");            
            }
        }
        $this->db->where('outward.idbranch= b.id_branch')->from('branch b')
                ->join('(select bb.branch_name,bb.id_branch from branch bb where bb.is_warehouse = 1) stk','`stk`.`id_branch`=outward.idwarehouse','left');
        
        $this->db->order_by('outward.id_outward', 'desc');
        return $this->db->get('outward')->result();
//        die(print_r($this->db->last_query()));
    }
    
    public function receive_stock_shipment($allocation_id,$outward_id,$idbranch,$remark,$user_id) {
        $this->db->trans_begin();    
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
            $sku4_data=$this->db->where('idskutype', 4)->where('temp_idbranch', $idbranch)->where('outward_dc', $outward_id)->get('stock')->result();
//            $update_stock="";
//            $delete_stock="";
            $update_stock=array();
             $delete_stock=array();
            foreach ($sku4_data as $data){
                $sku4=$this->db->where('idskutype', 4)->where('idbranch', $idbranch)->where('idgodown', $data->idgodown)->where('idvariant', $data->idvariant)->get('stock')->result();
                    if(count($sku4) > 0){
                         $update_stock[]="UPDATE stock SET qty = qty + ".$data->qty." WHERE idvariant = ".$data->idvariant." AND idgodown = ".$data->idgodown." AND idbranch = ".$idbranch.";";                                
                        $delete_stock[]="DELETE FROM stock where id_stock = ".$data->id_stock.";";
                    }
            }
            if(count($update_stock) > 0){
                foreach ($update_stock as $data){                
                    $this->db->query($data);                                                             
                }
            } 
            if(count($delete_stock) > 0){
                foreach ($delete_stock as $data){   
                    $this->db->query($data);                                    
                }
            }
            $this->db->where('outward_dc', $outward_id)->where('temp_idbranch', $idbranch)->set('idbranch', $idbranch, false)->set('temp_idbranch', 0, false)->update('stock');     
            $array = array(
                    'status' => 2,
                    'shipment_received_date' => $date,
                    'shipment_received_by' => $user_id,
                    'shipment_received_remark' => $remark,
                    'shipment_received_entry_time' =>$datetime           
                );
            $this->db->where('id_outward ', $outward_id)->where('idstock_allocation ', $allocation_id)->update('outward', $array);
            $this->db->where('id_stock_allocation', $allocation_id)->set('status', 5, false)->update('stock_allocation');
        //die(print_r($this->db->last_query()));
             $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return 0;
        } else {                  
            $this->db->trans_commit();
            return 1;
        }
        
    }
    
    public function get_ajax_transfer_balance_report($idvariant,$datefrom,$dateto,$idbranch,$viewbranches) {
        if($idbranch == ''){
            $branches = explode(',',$viewbranches);
            return $this->db->select('opr.*,branch.branch_name,model_variants.full_name')
                            ->where('opr.idbranch = branch.id_branch')->from('branch')
                            ->where_in('opr.idbranch', $branches)
                            ->where('opr.idvariant', $idvariant)
                            ->where('opr.date >=', $datefrom)
                            ->where('opr.date <=', $dateto)
                            ->where('model_variants.id_variant', $idvariant)->from('model_variants')
                            ->get('outward_product opr')->result();
        }else{
            return $this->db->select('opr.*,branch.branch_name,model_variants.full_name')
                            ->where('opr.idbranch', $idbranch)
                            ->where('opr.date >=', $datefrom)
                            ->where('opr.date <=', $dateto)
                            ->where('opr.idvariant', $idvariant)
                            ->where('opr.idbranch = branch.id_branch')->from('branch')
                            ->where('model_variants.id_variant', $idvariant)->from('model_variants')
                            ->get('outward_product opr')->result();
        }
    }
    
}
?>

