<?php
class Inward_model extends CI_Model{
    public function update_model_variant_mrp($idvariant, $mrp) {
        $this->db->where('id_variant', $idvariant)->set('mrp', $mrp, false)->update('model_variants');
    }
    public function get_inward_byid($id){
        return $this->db->where('id_inward', $id)
                        ->where('idvendor = id_vendor')->from('vendor')
                        ->where('created_by = id_users')->from('users')
                        ->get('inward')->result();
    }
    public function get_inward_product_byid($id){
        return $this->db->where('idinward', $id)
                        ->where('inward_data.idgodown = id_godown')->from('godown')
                        ->where('id_sku_type = idskutype')->from('sku_type')
                        ->get('inward_data')->result();
    }
    public function get_inward_data(){
        return $this->db->where('inward.idbranch', $_SESSION['idbranch'])
                        ->where('idvendor = id_vendor')->from('vendor')
                        ->where('created_by = id_users')->from('users')
                        ->order_by('id_inward', 'desc')
                        ->limit(100)
                        ->get('inward')->result();
    }
    public function get_inward_product_data(){
        return $this->db->where('inward_product.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('inward_product.created_by = users.id_users')->from('users')
                        ->where('inward_product.idgodown = godown.id_godown')->from('godown')
//                        ->where('inward_product.idskutype = idskutype')->from('sku_type')
                        ->where('idinward = inward.id_inward')->from('inward')
                        ->where('inward.idbranch', $_SESSION['idbranch'])
                        ->order_by('id_inward_product', 'desc')
                        ->limit(100)
                        ->get('inward_product')->result();
    }

    public function ajax_get_inward_data_byfilter($from, $to, $idvendor, $vendors){
         if($idvendor == 0){
            $branch_arr = explode(',',$vendors);
        }else{
            $branch_arr[] = $idvendor;
        }
        return $this->db->where('inward.date <=', $to)
                        ->where('inward.date >=', $from)
//                        ->where('inward.idbranch >=', $_SESSION['idbranch'])
                        ->where_in('inward.idvendor', $branch_arr)
                        ->where('idvendor = id_vendor')->from('vendor')
                        ->where('created_by = id_users')->from('users')
                        ->order_by('id_inward', 'desc')
                        ->get('inward')->result();
    }
    public function ajax_get_inward_product_data($from, $to, $idvendor, $idpcat, $allpcats){
        if($idpcat == 0){
            $pcat = explode(',',$allpcats);
        
        }else{
            $pcat[] = $idpcat;
        }
        
         if($idvendor == 0){
            return $this->db->where('inward.date <=', $to)
                        ->where('inward.date >=', $from)
                        ->where_in('inward_product.idproductcategory', $pcat)
                        ->where('inward_product.idvendor = id_vendor')->from('vendor')
                        ->where('inward_product.created_by = id_users')->from('users')
                        ->where('inward_product.idgodown = id_godown')->from('godown')
                        ->where('inward_product.idskutype = sk.id_sku_type')->from('sku_type sk')
                        ->where('idinward = inward.id_inward')->from('inward')
                        ->order_by('id_inward_product', 'desc')
                        ->get('inward_product')->result();         
        }else{
            return $this->db->where('inward.date <=', $to)
                        ->where('inward.date >=', $from)
                        ->where('inward_product.idvendor', $idvendor)
                        ->where_in('inward_product.idproductcategory', $pcat)
                        ->where('inward_product.idvendor = id_vendor')->from('vendor')
                        ->where('inward_product.created_by = id_users')->from('users')
                        ->where('inward_product.idgodown = id_godown')->from('godown')
                        ->where('inward_product.idskutype = sk.id_sku_type')->from('sku_type sk')
                        ->where('idinward = inward.id_inward')->from('inward')
//                        ->where('inward.idbranch', $_SESSION['idbranch'])
                        ->order_by('id_inward_product', 'desc')
                        ->get('inward_product')->result();         
//        die(print_r($this->db->last_query()));
        }
    }
    
    /////////////// Inward Stock  Start///////////////////////////
    public function save_inward($data) {
        $this->db->insert('inward', $data);
        return $this->db->insert_id();
    }
    public function save_inward_data($data) {
        $this->db->insert('inward_data', $data);
        return $this->db->insert_id();
    }
    public function save_inward_product($data) {
        return $this->db->insert('inward_product', $data);
    }
    public function save_stock($data) {
        return $this->db->insert('stock', $data);
    }
    public function save_stock_batch($data) {
        return $this->db->insert_batch('stock', $data);
    }
    public function update_stock_byid($idstock, $qty) {
        return $this->db->where('id_stock', $idstock)->set('qty', $qty)->update('stock');
    }
    public function update_variants_last_purchase_price($product_id, $last_purchase_price) {
        return $this->db->where('id_variant', $product_id)->update('model_variants', $last_purchase_price);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function get_opening_data(){
        return $this->db->where('opening_stock.idvendor = id_vendor')->from('vendor')
                        ->where('opening_stock.idgodown = id_godown')->from('godown')
                        ->where('opening_stock.created_by = id_users')->from('users')
                        ->order_by('id_inward', 'desc')
                        ->get('opening_stock')->result();
    }
    public function get_opening_byid($id){
        return $this->db->where('id_inward', $id)
                        ->where('idvendor = id_vendor')->from('vendor')
                        ->where('opening_stock.idbranch = id_branch')->from('branch')
                        ->where('idgodown = id_godown')->from('godown')
                        ->where('created_by = id_users')->from('users')
                        ->get('opening_stock')->result();
    }
    public function get_opening_product_byid($id){
        return $this->db->where('idinward', $id)
                        ->where('id_sku_type = idskutype')->from('sku_type')
                        ->get('opening_stock_data')->result();
    }
    public function get_inward_product_data_byid($id){
        return $this->db->where('idinward', $id)->get('inward_product')->result();
    }
    public function get_inward_product_stock_data_byid($id, $idbranch){
        $str = 'select inp.*, st.qty as stock_imei_qty, stq.qty as stock_acc_qty from inward_product inp '
                            . 'left join (select imei_no, qty, idgodown from stock where idskutype != 4) st on inp.imei_no = st.imei_no and inp.idgodown = st.idgodown '
                            . 'left join (select idmodel, qty, idbranch, idgodown from stock where idskutype = 4 and idbranch = '.$idbranch.') stq on inp.idgodown = stq.idgodown and inp.idmodel = stq.idmodel '
                            . 'where inp.idinward ='.$id;
//        die($str);
        return $this->db->query($str)->result();
    }
    public function ajax_check_duplicate_inward_barcode($imei, $idmodel) {
        return $this->db->where('imei_no', $imei)
                        ->where('idmodel', $idmodel)
                        ->get('inward_product')->result();
    }
    public function get_inward_verification_stock_count($idmodel, $imei, $idgodown, $idbranch, $idskutype) {
        // check stock for purchase invoice return
        if($idskutype == 4){
            return $this->db->select('sum(qty) as sum_qty')->where('idbranch', $idbranch)
                            ->where('idmodel', $idmodel)->where('idgodown', $idgodown)
                            ->get('stock')->row();
        }else{
            return $this->db->select('sum(qty) as sum_qty')->where('idbranch', $idbranch)
                            ->where('imei_no', $imei)->where('idmodel', $idmodel)
                            ->where('idgodown', $idgodown)->get('stock')->row();
        }
    }
    public function get_purchase_return_byid($id){
        return $this->db->where('id_purchasereturn', $id)
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->get('purchase_return')->result();
    }
    public function get_purchase_return_product_byid($id){
        return $this->db->where('idpurchase_return', $id)->where('purchase_return_product.idskutype = sku_type.id_sku_type')->from('sku_type')
                        ->get('purchase_return_product')->result();
    }
    public function get_purchase_return_payment_byid($id){
        return $this->db->where('idpurchase_return', $id)->get('purchase_return_payment')->row();
    }


    
    public function save_inwardbatch_product($data) {
        return $this->db->insert_batch('inward_product', $data);
    }
    public function save_purchase_return($data) {
        $this->db->insert('purchase_return', $data);
        return $this->db->insert_id();
    }
    public function save_purchase_return_product($data) {
        return $this->db->insert('purchase_return_product', $data);
    }
    public function save_purchase_return_payment($data) {
        return $this->db->insert('purchase_return_payment', $data);
    }
    
    /////////////// END Inward Stock///////////////////////////
    
    /////////////////////Opening STock Start /////////////////
    public function save_opening($data) {
        $this->db->insert('opening_stock', $data);
        return $this->db->insert_id();
    }
    public function save_opening_data($data) {
        $this->db->insert('opening_stock_data', $data);
        return $this->db->insert_id();
    }
    public function save_opening_product($data) {
        return $this->db->insert('opening_stock_product', $data);
    }
    /////////////////////END Opening Stock /////////////////
    
    public function get_hostock_byidmodel_skutype($idmodel, $sku_type, $branch) {
        return $this->db->where('idmodel', $idmodel)
                        ->where('idskutype', $sku_type)
                        ->where('idbranch', $branch)
                        ->get('stock',1)->result();
    }
    public function minus_stock_byidmodel_idbranch_idgodown($data){ 
        //($idvariant, $idbranch, $idgodown, $qty) {    
        return $this->db->query($data);        
//        return $this->db->where('idvariant', $idvariant)->where('idgodown', $idgodown)->where('idbranch', $idbranch)
//                        ->set('qty', 'qty - ' . $qty, false)->update('stock');        
    }
    
    public function update_stock_qty($idmodel, $sku_type, $branch, $qty) {
        return $this->db->where('idmodel', $idmodel)->where('idskutype', $sku_type)->where('idbranch', $branch)
                        ->set('qty', $qty)->update('stock');
    }
    public function update_stockqty_bymodel_skutype_branch_godown($idmodel, $sku_type, $branch, $qty, $idgodown) {
        return $this->db->where('idmodel', $idmodel)->where('idskutype', $sku_type)->where('idbranch', $branch)->where('idgodown', $idgodown)
                        ->set('qty', $qty)->update('stock');
    }
    public function update_inward_byid($idinward, $data) {
        return $this->db->where('id_inward', $idinward)->update('inward', $data);
    }
    public function update_inward_product_byid($idinward_product, $data) {
        return $this->db->where('id_inward_product', $idinward_product)->update('inward_product', $data);
    }
}
?>