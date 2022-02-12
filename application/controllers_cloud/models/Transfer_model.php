<?php
class Transfer_model extends CI_Model{
    public function get_branchstock_byidmodel_skutype_godown($idvariant, $sku_type, $branch, $godown) {
        return $this->db->where('idvariant', $idvariant)->where('idskutype', $sku_type)
                        ->where('idbranch', $branch)->where('idgodown', $godown)
                        ->get('stock',1)->result();
    }
    
    public function get_branch_stocksale_byvariants($idbranchs,$days,$variantid,$idgodown) {                
        $to=date('Y-m-d');
        $day='-'.$days.' days';                 
        $from=date('Y-m-d', strtotime("$day", strtotime($to))); 
        $this->db->select('mv.id_variant,mv.full_name,mv.idmodel,mv.idcategory,mv.idproductcategory,mv.idcategory,mv.idbrand,mv.idsku_type,`b`.`id_branch`, `b`.`branch_name`, stk.stock_qty,sp.sale_qty');
        $this->db->from('branch b');                
        $this->db->join('(select sum(st.qty) as stock_qty,st.idbranch from stock st WHERE  st.idgodown='.$idgodown.' and  st.idvariant='.$variantid.' GROUP BY st.idbranch) stk','`stk`.`idbranch`=`b`.`id_branch`','left');         
        $this->db->join("(select sum(s.qty) as sale_qty,s.idbranch from sale_product s WHERE s.idvariant=$variantid and s.idgodown=$idgodown and s.date between '$from' and '$to' GROUP BY s.idbranch) sp", 'sp.idbranch=b.id_branch', 'left');                
        $this->db->where('mv.active', 1)->where('mv.id_variant',$variantid)->from('model_variants mv');                       
        if(count($idbranchs)>0){
            $this->db->where_in('b.id_branch', $idbranchs);    
        }        
        $branches = implode(",", $idbranchs);
        $order = sprintf('FIELD(b.id_branch, %s)', $branches);
        $this->db->order_by($order);
        $this->db->group_by('b.id_branch,mv.id_variant');
        $query = $this->db->get();        
        return $query->result();
//      die(print_r($this->db->last_query()));
    } 
    public function get_transfer_data_bystatus_idbranch_to($status, $idbranch,$idbranch_to,$from,$to,$type){ /*Requester branch */                    
            $this->db->select('t.*,b.branch_name as branch_from,stk.branch_name as branch_to')
                    ->where('t.status', $status)
                    ->where('t.idbranch', $idbranch);            
            if($idbranch_to){
                $this->db->where('t.transfer_from', $idbranch_to);
            }
            $this->db->where('t.request_type', $type);
            if($from && $to){
                $this->db->where( "t.date BETWEEN '$from' AND '$to'", NULL, FALSE );
            }
            $this->db->where('t.transfer_from = b.id_branch')->from('branch b')
                 ->join('(select bb.branch_name,bb.id_branch from branch bb WHERE  bb.id_branch='.$idbranch.') stk','`stk`.`id_branch`=`t`.`idbranch`','left');
            return $this->db->get('transfer t')->result();     
            
    }
    public function get_transfer_data_bystatus_idbranch_from($status, $idbranch,$idbranch_from,$from,$to){ /*Provider branch */                        
                        $this->db->select('t.*,b.branch_name as branch_to,stk.branch_name as branch_from');                        
                        $this->db->where('t.status', $status);                        
                        $this->db->where('t.transfer_from', $idbranch);
                        if($idbranch_from){
                            $this->db->where('t.idbranch', $idbranch_from);
                        }
                        if($from && $to){
                            $this->db->where( "t.date BETWEEN '$from' AND '$to'", NULL, FALSE );
                        }
                        $this->db->where('`t`.`idbranch`= b.id_branch')->from('branch b')
                             ->join('(select bb.branch_name,bb.id_branch from branch bb where bb.id_branch = '.$idbranch.') stk','`stk`.`id_branch`=t.transfer_from','left');
                        return $this->db->get('transfer t')->result();      
//                        die(print_r($this->db->last_query()));
                    
    }    
     // saveTransfer
    public function save_transfer($data) { 
        $this->db->insert('transfer', $data);
        return $this->db->insert_id();
    }
    public function save_transfer_product($data) {
        return $this->db->insert_batch('transfer_product', $data);
    }
    public function update_transfer($idtransfer, $data) {
        return $this->db->where('id_transfer', $idtransfer)->update('transfer', $data);
    }
    public function update_transfer_product($data) {
        return $this->db->update_batch('transfer_product',$data, 'id_transfer_product'); 
    }
    public function save_inter_state($data) { 
        $this->db->insert('inter_state', $data);
        return $this->db->insert_id();
    }
    public function save_inter_state_product($data) { 
        $this->db->insert_batch('inter_state_product', $data);
        return $this->db->insert_id();
    }
    
    
    
    public function get_transfer_byid($idtransfer){
        return $this->db->select('t.*,b.branch_name as branch_from,stk.branch_name as branch_to')
                            ->where('t.id_transfer',$idtransfer)
                            ->where('t.transfer_from = b.id_branch')->from('branch b')
                            ->join('(select bb.branch_name,bb.id_branch from branch bb) stk','`stk`.`id_branch`=`t`.`idbranch`','left')
                            ->get('transfer t')->result();
    }    
    public function get_transfer_products_byid($idtransfer,$mystock){
        $this->db->select('t.*,mv.full_name,mv.landing,mv.cgst,mv.sgst,mv.igst,g.godown_name')
                        ->where('t.idtransfer',$idtransfer);
                        if($mystock == 1){
                            $this->db->select('stk.stock_qty')
                                     ->join('(select sum(st.qty) as stock_qty,st.idgodown,st.idvariant,st.idbranch from stock st,transfer_product tt where st.idgodown=tt.idgodown and st.idvariant=tt.idvariant and st.idbranch=tt.transfer_from and  tt.idtransfer='.$idtransfer.' group by st.idgodown,st.idvariant,st.idbranch) stk','stk.idgodown=t.idgodown and stk.idvariant=t.idvariant and stk.idbranch=t.transfer_from','left');
                        }
                $this->db->join('godown g','t.idgodown = g.id_godown')
                        ->join('model_variants mv','t.idvariant = mv.id_variant');
                return $this->db->get('transfer_product t')->result();                
        }
        
    public function save_outward_shipment_details($idoutward, $data) {
        return $this->db->where('id_outward',$idoutward)->update('outward', $data);
    }
    public function save_transfer_shipment_details($idtransfer, $data) {
        return $this->db->where('id_transfer',$idtransfer)->update('transfer', $data);
    }
    public function receive_b2b_shipment($id_transfer,$idbranch,$remark,$user_id) {
        $this->db->trans_begin();    
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        
        $sku4_data=$this->db->where('idskutype', 4)->where('temp_idbranch', $idbranch)->where('transfer_dc', $id_transfer)->get('stock')->result();
            
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
            } if(count($delete_stock) > 0){
                foreach ($delete_stock as $data){   
                    $this->db->query($data);                                    
                }
            }
        
            $this->db->where('transfer_dc', $id_transfer)->where('temp_idbranch ', $idbranch)->set('idbranch', $idbranch, false)->set('temp_idbranch', 0, false)->update('stock');     
            $array = array(
                    'status' => 5,
                    'shipment_received_date' => $date,
                    'shipment_received_by' => $user_id,
                    'shipment_received_remark' => $remark,
                    'shipment_received_entry_time' =>$datetime           
                );
             $this->db->where('id_transfer ', $id_transfer)->update('transfer', $array);
             $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {                  
            $this->db->trans_rollback();
            return 0;
        } else {                  
            $this->db->trans_commit();
            return 1;
        }        
    }
	
	public function update_doa_reconcilliation_details($imei_no, $idbranch) {
        return $this->db->where_in('imei_no',$imei_no)->set('idbranch',$idbranch)->update('doa_reconciliation');
    }
	
    public function get_transfer_data_by_idbranch_date($idbranch = '', $datefrom = '', $dateto = '',$idbranch_other='') {

        $this->db->select('transfer.dispatch_date,transfer.shipment_received_date,op.*,b.branch_name as branch_to,stk.branch_name as branch_from,g.godown_name,mv.full_name,brd.brand_name');        
        $this->db->where('transfer.status', 5);        
        if ($idbranch != '' && $idbranch_other !='') {
            $this->db->where('((transfer.idbranch = '.$idbranch.' and transfer.transfer_from ='.$idbranch_other.') or (transfer.idbranch = '.$idbranch_other.' and transfer.transfer_from ='.$idbranch.'))');
        }elseif($idbranch != '' && $idbranch_other ==''){
            $this->db->where('(transfer.idbranch = '.$idbranch.' or transfer.transfer_from ='.$idbranch.')');
        } elseif($idbranch == '' && $idbranch_other !=''){
            $this->db->where('(transfer.idbranch = '.$idbranch_other.' or transfer.transfer_from ='.$idbranch_other.')');
        }        
        $this->db->join('transfer_product op','transfer.id_transfer = op.idtransfer');
        $this->db->join('model_variants mv','mv.id_variant=op.idvariant');
        $this->db->where('g.id_godown=op.idgodown')->from('godown g');   
        $this->db->where('brd.id_brand=op.idbrand')->from('brand brd');   
        if ($datefrom == '' && $dateto == '') {
            $dateto = date('Y-m-d');
            $datefrom = date('Y-m-d', strtotime("-10 days", strtotime($dateto)));
        }
        if ($datefrom != '' && $dateto != '') {
            $this->db->where('transfer.shipment_received_date >=', $datefrom)->where('transfer.shipment_received_date <=', $dateto);
        }
        $this->db->where('transfer.idbranch= b.id_branch')->from('branch b')
                ->join('(select bb.branch_name,bb.id_branch from branch bb ) stk','`stk`.`id_branch`=transfer.transfer_from','left');
        
        $this->db->order_by('transfer.id_transfer', 'desc');
        return $this->db->get('transfer')->result();
//        die(print_r($this->db->last_query()));
    }
    
    public function wipe_out_pending_b2b_requests($date, $idbranch) {
        $datetime = date('Y-m-d H:i:s');
        $array = array(
                'status' => 2,
                'approved_time' => $datetime,                
                'approved_remark' => 'Auto Reject'
                );
         return $this->db->where('date', $date)->where('status', 0)->where('idbranch not in (18,7)')->where('transfer_from', $idbranch)->update('transfer', $array);
        
    }
    public function get_outward_product_by_outwardid($id) {
        $this->db->select('op.idbranch,op.imei_no,op.idvariant,op.idgodown,o.idwarehouse as transfer_from');
        $this->db->from('outward_product op')->where('op.idoutward= o.id_outward')->from('outward o');        
        $this->db->where('idoutward', $id);
        return $this->db->get()->result();
    }  
    public function get_transfer_product_by_transferid($id) {
        $this->db->select('idbranch,imei_no,idvariant,idgodown,transfer_from');
        $this->db->from('transfer_product');          
        $this->db->where('idtransfer', $id);
        return $this->db->get()->result();
    } 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // getOutward
    public function get_outward_data(){
        return $this->db->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('id_outward', 'desc')
                        ->get('outward')->result();
    }
    public function get_outward_data_by_status($status){
        $idbranch = $_SESSION['idbranch'];
        if($_SESSION['level'] != 1){
            return $this->db->where('outward.status',$status)->where('outward.idbranch',$idbranch)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('id_outward', 'desc')
                        ->get('outward')->result();
        }else{
            return $this->db->where('outward.status',$status)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('id_outward', 'desc')
                        ->get('outward')->result();
        }
    }
    public function ajax_get_shipment_bystatus($status,$idbranch,$datefrom,$dateto){
        if($idbranch != '' && $datefrom == '' && $dateto == ''){
            // branch
            return $this->db->where('outward.status',$status)->where('outward.idbranch',$idbranch)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('outward.id_outward', 'desc')
                        ->get('outward')->result();
        }elseif($idbranch == '' && $datefrom != '' && $dateto != ''){
            // date
            return $this->db->where('outward.status',$status)
                        ->where('outward.date >=', $datefrom)->where('outward.date <=', $dateto)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('outward.id_outward', 'desc')
                        ->get('outward')->result();
        }elseif($idbranch != '' && $datefrom != '' && $dateto != ''){
            // date, status
            return $this->db->where('outward.status',$status)->where('outward.idbranch',$idbranch)
                        ->where('outward.date >=', $datefrom)->where('outward.date <=', $dateto)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('outward.id_outward', 'desc')
                        ->get('outward')->result();
        }elseif($idbranch == '' && $datefrom == '' && $dateto == ''){
            // status only
            return $this->db->where('outward.status',$status)
                        ->where('outward.idbranch = id_branch')->from('branch')
                        ->order_by('outward.id_outward', 'desc')
                        ->get('outward')->result();
        }
    }
    public function ajax_get_my_stock_request_bystatus($status,$idbranch,$datefrom,$dateto){
        if($idbranch != '' && $datefrom == '' && $dateto == ''){
            // branch
            return $this->db->where('transfer.status',$status)->where('transfer.idbranch',$idbranch)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch == '' && $datefrom != '' && $dateto != ''){
            // date
            return $this->db->where('transfer.status',$status)
                        ->where('transfer.date >=', $datefrom)->where('transfer.date <=', $dateto)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch != '' && $datefrom != '' && $dateto != ''){
            // date, status
            return $this->db->where('transfer.status',$status)->where('transfer.idbranch',$idbranch)
                        ->where('transfer.date >=', $datefrom)->where('transfer.date <=', $dateto)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch == '' && $datefrom == '' && $dateto == ''){
            // status only
            return $this->db->where('transfer.status',$status)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }
    }
    public function ajax_get_branch_stock_transfer_bystatus($status,$idbranch,$datefrom,$dateto){
        if($idbranch != '' && $datefrom == '' && $dateto == ''){
            // branch
            return $this->db->where('transfer.status',$status)->where('transfer.transfer_from',$idbranch)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch == '' && $datefrom != '' && $dateto != ''){
            // date
            return $this->db->where('transfer.status',$status)
                        ->where('transfer.date >=', $datefrom)->where('transfer.date <=', $dateto)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch != '' && $datefrom != '' && $dateto != ''){
            // date, status
            return $this->db->where('transfer.status',$status)->where('transfer.transfer_from',$idbranch)
                        ->where('transfer.date >=', $datefrom)->where('transfer.date <=', $dateto)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }elseif($idbranch == '' && $datefrom == '' && $dateto == ''){
            // status only
            return $this->db->where('transfer.status',$status)
                        ->where('transfer.idbranch = id_branch')->from('branch')
                        ->order_by('transfer.id_transfer', 'desc')
                        ->get('transfer')->result();
        }
    }
    public function get_outward_byid($idoutward){
        return $this->db->where('outward.id_outward',$idoutward)
                        ->where('outward.idbranch = b.id_branch')->from('branch b')
//                        ->where('outward.idgodown = id_godown')->from('godown')
                        ->get('outward')->result();
    }
    public function get_outward_product_byid($idoutward){
        return $this->db->where('idoutward',$idoutward)->get('outward_product')->result();
    }
    public function get_outward_product_price_byid($idoutward){
        return $this->db->where('outward_data.idoutward',$idoutward)
                        ->where('outward_data.idmodel = model.id_model')->from('model')
                        ->where('outward_data.idgodown = id_godown')->from('godown')
                        ->get('outward_data')->result();
    }
    public function get_outward_byidstock_allocation($idstock_allocation) {
        return $this->db->where('idstock_allocation', $idstock_allocation)->get('outward')->row();
    }
    public function get_outward_product_byidstock_allocation($idstock_allocation) {
         $this->db->where('outward_product.idoutward = outward.id_outward')->from('outward');
         return $this->db->where('outward.idstock_allocation', $idstock_allocation)->get('outward_data')->result();
    }
    // getTransfer
    public function get_transfer_data(){
        return $this->db->where('transfer.idbranch = id_branch')->from('branch')
                        ->get('transfer')->result();
    }
    
    public function get_transfer_data_bystatus_transfer_from($status, $idbranch){
        if($_SESSION['level'] == 1){
            return $this->db->where('transfer.status', $status)
                            ->where('transfer.idbranch = id_branch')->from('branch')
                            ->get('transfer')->result();
        }else{
            return $this->db->where('transfer.status', $status)
                            ->where('transfer.transfer_from', $idbranch)
                            ->where('transfer.idbranch = id_branch')->from('branch')
                            ->get('transfer')->result();
        }
    }
    
    public function get_transfer_price_product_byid($idtransfer){
        return $this->db->where('transfer_data.idtransfer',$idtransfer)
                        ->where('transfer_data.idgodown = godown.id_godown')->from('godown')
                        ->where('transfer_data.idmodel = model.id_model')->from('model')
                        ->get('transfer_data')->result();
    }
    // saveOutward
    public function save_outward($data) {
        $this->db->insert('outward', $data);
        return $this->db->insert_id();
    }
    public function save_outward_data($data) {
        $this->db->insert('outward_data', $data);
        return $this->db->insert_id();
    }
    public function save_outward_product($data) {
        return $this->db->insert('outward_product', $data);
    }
   
    public function save_transfer_data($data) {
        $this->db->insert('transfer_data', $data);
        return $this->db->insert_id();
    }
    public function save_batch_transfer_data($data) {
        return $this->db->insert_batch('transfer_data', $data);
    }
    
    
    // getStock
    public function get_branchstock_byidmodel_skutype($idmodel, $sku_type, $branch) {
        return $this->db->where('idmodel', $idmodel)->where('idskutype', $sku_type)
                        ->where('idbranch', $branch)
                        ->get('stock',1)->result();
    }
    public function delete_update_branchstock_byidbranch_idmodel_idgodown_outwarddc($idbranch, $idmodel, $idgodown, $dc, $qty, $idstock) {
        $this->db->where('idmodel', $idmodel)->where('idgodown', $idgodown)->where('temp_idbranch', $idbranch)->where('outward_dc', $dc)->delete('stock');
        return $this->db->where('id_stock', $idstock)->set('qty', 'qty+ ' . $qty, false)->update('stock');
    }
    public function delete_update_branchstock_byidbranch_idmodel_idgodown_transferdc($idbranch, $idmodel, $idgodown, $dc, $qty, $idstock) {
        $this->db->where('idmodel', $idmodel)->where('idgodown', $idgodown)->where('temp_idbranch', $idbranch)->where('outward_dc', $dc)->delete('stock');
        return $this->db->where('id_stock', $idstock)->set('qty', 'qty+ ' . $qty, false)->update('stock');
    }
    public function update_branchstock_byidbranch_idmodel_idgodown($idbranch, $idmodel, $idgodown) {
        return $this->db->where('temp_idbranch', $idbranch)->where('idmodel', $idmodel)->where('idgodown', $idgodown)
                        ->set('idbranch', $idbranch, false)->set('temp_idbranch', 0)->update('stock');
    }
    // update
    public function update_branchstock_qty_byidmodel_skutype($idmodel, $sku_type, $branch, $qty, $transfer_from, $remark) {
        return $this->db->where('idmodel', $idmodel)->where('idskutype', $sku_type)
                        ->where('idbranch', $branch)->set('qty', $qty)->set('transfer_from', $transfer_from)->set('outward_remark', $remark)
                        ->update('stock');
    }
    public function update_stock_byimei($data, $imei) {
        return $this->db->where('imei_no', $imei)->update('stock', $data);
    }
    
    public function update_outward_data($idoutward, $data) {
        return $this->db->where('idoutward', $idoutward)->update('outward_product', $data);
    }
    public function update_outward_product($idoutward, $data) {
        return $this->db->where('idoutward', $idoutward)->update('outward_data', $data);
    }
    
    public function update_transfer_data($idtransfer_data, $imei) {
        return $this->db->where('id_transfer_data', $idtransfer_data)->set('imei_srno', $imei)->update('transfer_data');
    }
//    public function update_outward_byidstock_allocation($idstock_allocation,$data) {
//        return $this->db->where('idstock_allocation', $idstock_allocation)->update('outward', $data);
//    }
}
?>