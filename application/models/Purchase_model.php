<?php
class Purchase_model extends CI_Model {
    public function get_variant_by_id($id) {
        return $this->db->select('mv.id_variant, mv.modelname, mv.full_name, mv.idsku_type, product_category.product_category_name, mv.idproductcategory,mv.idcategory,mv.idmodel,mv.idbrand,mv.sale_type,mv.last_purchase_price')
                        ->where('mv.id_variant',$id)
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->get('model_variants mv')->row();
    }
    public function get_model_variant_by_id($id) {
        return $this->db->where('mv.id_variant',$id)
                        ->where('model.id_model  = mv.idmodel')->from('model')
                        ->where('sku_type.id_sku_type  = mv.idsku_type')->from('sku_type')
                        ->where('id_product_category  = mv.idproductcategory')->from('product_category')
                        ->where('category.id_category  = mv.idcategory')->from('category')
                        ->where('brand.id_brand = mv.idbrand')->from('brand')
                        ->get('model_variants mv')->result();
    }
    public function save_purchase_order($data) {
        $this->db->insert('purchase_order',$data);
        return $this->db->insert_id();
    }
    public function save_purchase_order_products($data) {
        return $this->db->insert_batch('purchase_order_product',$data);
    }
    public function save_purchase_direct_inward($data) {
        $this->db->insert('purchase_direct_inward',$data);
        return $this->db->insert_id();
    }
    public function save_purchase_direct_inward_products($data) {
        return $this->db->insert_batch('purchase_direct_inward_product',$data);
    }
    public function ajax_get_purchase_order_data($status, $from, $to) {
        if($from == '' && $to == '' && $status != ''){
            return $this->db->where('purchase_order.status', $status)
                        ->where('purchase_order.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_order.idvendor = vendor.id_vendor')->from('vendor')
                        ->order_by('purchase_order.id_purchase_order')
                        ->get('purchase_order')->result();
        }elseif($from != '' && $to != '' && $status == ''){
            return $this->db->where('purchase_order.date >=', $from)
                        ->where('purchase_order.date <=', $to)
                        ->where('purchase_order.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_order.idvendor = vendor.id_vendor')->from('vendor')
                        ->order_by('purchase_order.id_purchase_order')
                        ->get('purchase_order')->result();
        }elseif($from != '' && $to != '' && $status != ''){
            return $this->db->where('purchase_order.status', $status)
                        ->where('purchase_order.date >=', $from)
                        ->where('purchase_order.date <=', $to)
                        ->where('purchase_order.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_order.idvendor = vendor.id_vendor')->from('vendor')
                        ->order_by('purchase_order.id_purchase_order')
                        ->get('purchase_order')->result();
        }else{
            return $this->db->where('purchase_order.status', $status)
                        ->where('purchase_order.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_order.idvendor = vendor.id_vendor')->from('vendor')
                        ->order_by('purchase_order.id_purchase_order')
                        ->get('purchase_order')->result();
        }
    }
    public function get_purchase_direct_inward_data() {
        return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_direct_inward.status = 0')
//                        ->order_by('purchase_direct_inward.status')
                        ->get('purchase_direct_inward')->result();
    }
    public function get_purchase_direct_inward_data_byidbranch($idbranch) {
        return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_direct_inward.idwarehouse', $idbranch)
                        ->order_by('purchase_direct_inward.status')
                        ->get('purchase_direct_inward', 200)->result();
    }
    public function ajax_get_purchase_direct_inward_data_byidbranch($status,$idbranch,$datefrom,$dateto) {
        if($datefrom == ''){
            if($idbranch != ''){
                if($status == ''){
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.idwarehouse', $idbranch)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();
                }else{
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.idwarehouse', $idbranch)
                                    ->where('purchase_direct_inward.status', $status)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();

                }
            }else{
                if($status == ''){
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();
                }else{
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.status', $status)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();

                }
            }
        }else{
            if($idbranch != ''){
                if($status == ''){
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where('purchase_direct_inward.idwarehouse', $idbranch)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();
                }else{
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where('purchase_direct_inward.idwarehouse', $idbranch)
                                    ->where('purchase_direct_inward.status', $status)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();

                }
            }else{
                if($status == ''){
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();
                }else{
                    return $this->db->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                                    ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                                    ->where('purchase_direct_inward.date between "'. $datefrom.'" and "'. $dateto .'"')
                                    ->where('purchase_direct_inward.status', $status)
                                    ->order_by('purchase_direct_inward.status')
                                    ->get('purchase_direct_inward')->result();

                }
            }
        }
    }
    public function get_purchase_order_product() {
        return $this->db->where('purchase_order_product.idmodelvariant = model_variants.id_variant')->from('model_variants')
                        ->get('purchase_order_product')->result();
    }
    public function get_purchase_order_byid($idpo) {
        return $this->db->where('purchase_order.id_purchase_order',$idpo)
                        ->where('purchase_order.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_order.idvendor = vendor.id_vendor')->from('vendor')
                        ->get('purchase_order')->row();
    }
    public function get_purchase_order_product_byid($idpo) {
        return $this->db->where('purchase_order_product.idpurchase_order',$idpo)
                        ->where('model_variants.idsku_type = sku_type.id_sku_type')->from('sku_type')
                        ->where('purchase_order_product.idmodelvariant = model_variants.id_variant')->from('model_variants')
                        ->get('purchase_order_product')->result();
    }
    public function get_purchase_direct_inward_byid($idpo) {
        return $this->db->where('purchase_direct_inward.id_purchase_direct_inward',$idpo)
                        ->where('purchase_direct_inward.idwarehouse = branch.id_branch')->from('branch')
                        ->where('purchase_direct_inward.idvendor = vendor.id_vendor')->from('vendor')
                        ->get('purchase_direct_inward')->row();
    }
    public function get_purchase_direct_inward_product_byid($idpo) {
        return $this->db->where('purchase_direct_inward_product.idpurchase_direct_inward',$idpo)
                        ->where('model_variants.idsku_type = sku_type.id_sku_type')->from('sku_type')
//                        ->where('purchase_direct_inward_product.idgodown = godown.id_godown')->from('godown')
                        ->where('purchase_direct_inward_product.idmodelvariant = model_variants.id_variant')->from('model_variants')
                        ->get('purchase_direct_inward_product')->result();
    }
    public function update_purchase_order($id, $data) {
        return $this->db->where('id_purchase_order',$id)->update('purchase_order',$data);
    }
    public function update_purchase_direct_inward($id, $data) {
        return $this->db->where('id_purchase_direct_inward',$id)->update('purchase_direct_inward',$data);
    }
    public function ready_to_intake_po_list($status, $idbranch) {
        $str = 'select * from purchase_order,vendor,branch where purchase_order.idwarehouse = branch.id_branch and purchase_order.idvendor = vendor.id_vendor and purchase_order.idwarehouse = '.$idbranch.' and (purchase_order.required_approval = 0 or purchase_order.status='.$status.')';
        return $this->db->query($str)->result();
    }
    public function po_list_by_status($status) {
        $str = 'select * from purchase_order,vendor,branch where purchase_order.idwarehouse = branch.id_branch and purchase_order.idvendor = vendor.id_vendor and purchase_order.status='.$status;
        return $this->db->query($str)->result();
    }
    public function edit_batch_po_product_data($data) {
        return $this->db->update_batch('purchase_order_product', $data, 'id_purchase_order_product'); 
    }
    
    //  Purchase return
    public function get_purchase_return() {
        return $this->db->where('purchase_return.purchase_return_by = id_users')->from('users')
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_return.idbranch = branch.id_branch')->from('branch')
                        ->order_by('purchase_return.id_purchasereturn','desc')
                        ->limit(100)
                        ->get('purchase_return')->result();
    }
    public function get_purchase_return_byfilter($from, $to, $idvendor) {
      
        if($idvendor == 0){
             
            return $this->db->where('purchase_return.date >=', $from)
                        ->where('purchase_return.date <=', $to)
                        ->where('purchase_return.purchase_return_by = id_users')->from('users')
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_return.idbranch = branch.id_branch')->from('branch')
                        ->order_by('purchase_return.id_purchasereturn','desc')
                        ->get('purchase_return')->result();
        }else{
        return $this->db->where('purchase_return.date >=', $from)
                        ->where('purchase_return.date <=', $to)
                        ->where('purchase_return.idvendor', $idvendor)
                        ->where('purchase_return.purchase_return_by = id_users')->from('users')
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_return.idbranch = branch.id_branch')->from('branch')
                        ->order_by('purchase_return.id_purchasereturn','desc')
                        ->get('purchase_return')->result();
        }
    }
    public function get_purchase_return_byid($id) {
        return $this->db->where('purchase_return.id_purchasereturn', $id)
                        ->where('purchase_return.purchase_return_by = id_users')->from('users')
                        ->where('purchase_return.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_return.idbranch = branch.id_branch')->from('branch')
                        ->get('purchase_return')->row();
    }
    public function get_purchase_return_product_byid($id) {
        return $this->db->where('purchase_return_product.idpurchase_return', $id)
                        ->where('purchase_return_product.idgodown = godown.id_godown')->from('godown')
                        ->get('purchase_return_product')->result();
    }
    public function ajax_purchase_return_data_byimei($imei, $idbranch, $idvendor) {
        return $this->db->where('imei_no', $imei)
                        ->where('idbranch', $idbranch)
                        ->where('idvendor', $idvendor)
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
    public function ajax_purchase_return_data_byimei_without_vendor($imei, $idbranch) {
        return $this->db->where('imei_no', $imei)
                        ->where('idbranch', $idbranch)
//                        ->where('idvendor', $idvendor)
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock')->result();
    }
    public function ajax_get_variant_byid_branch_godown($variant, $idbranch, $idgodown) {
         return $this->db->where('idvariant', $variant)
                        ->where('idbranch', $idbranch)
//                        ->where('idvendor', $idvendor)
                        ->where('idgodown', $idgodown)
                        ->get('stock')->result();
    }
    public function save_purchase_return($data) {
        $this->db->insert('purchase_return',$data);
        return $this->db->insert_id();
    }
    public function get_purchase_product_byimei($imei) {
        return $this->db->where('imei_no',$imei)
                        ->order_by('date', 'desc')
                        ->get('inward_product')->row();
    }
    public function get_purchase_product_byidvraiant_vendor($idvariant,$idvendor) {
        return $this->db->where('idvariant',$idvariant)->where('idvendor',$idvendor)
                        ->order_by('id_inward_product', 'desc')->get('inward_product')->row();
    }
    public function delete_stock_byidstock($idstock) {
        return $this->db->where('id_stock',$idstock)->delete('stock');
    }
    public function update_skustock_byidstock($idstock, $qty) {
        return $this->db->where('id_stock',$idstock)->set('qty', $qty)->update('stock');
    }
    public function save_purchase_return_product($data) {
        return $this->db->insert_batch('purchase_return_product',$data);
//        return $this->db->insert_id();
    }
    public function save_purchase_return_payment($data){
        return $this->db->insert('purchase_return_payment',$data);
    }
     public function ajax_get_purchase_data($from, $to, $idcompany){
    
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
                
         return $this->db->select('inward_product.*,category.category_name,category.hsn,branch.branch_name, brand.brand_name, godown.godown_name, vendor.*, inward.supplier_invoice_no, inward.vendor_invoice_date,inward.financial_year,inward.final_amount,inward.tcs_amount,inward.overall_discount,inward.gross_amount,inward.idbranch')
                        ->where('inward.date >=', $from)
                        ->where('inward.date <=', $to)
                        ->where_in('inward.idbranch', $branchid)
                        ->where('((`inward`.`total_cgst_amt` != 0 AND `inward`.`total_sgst_amt` != 0) OR (`inward`.`total_igst_amt` != 0 ))')
                        ->where('inward_product.idinward = inward.id_inward')->from('inward')
                        ->where('inward_product.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('inward_product.idgodown = godown.id_godown')->from('godown')
                        ->where('inward_product.idcategory = category.id_category')->from('category')
                        ->where('inward_product.idbrand = brand.id_brand')->from('brand')
                        ->where('inward.idbranch = branch.id_branch')->from('branch')
                        ->get('inward_product')->result();
        die($this->db->last_query());
    }
    public function ajax_get_purchase_return_data($from, $to, $idcompany){
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        return $this->db->where_in('purchase_return_product.idbranch',$branchid)
                        ->where('purchase_return_product.date >=', $from)
                        ->where('purchase_return_product.date <=', $to)
                        ->where('purchase_return_product.idgodown = godown.id_godown')->from('godown')
                        ->where('purchase_return_product.idvendor = vendor.id_vendor')->from('vendor')
                        ->where('purchase_return_product.idcategory = category.id_category')->from('category')
                        ->where('purchase_return_product.idbrand = brand.id_brand')->from('brand')
                        ->where('purchase_return_product.idbranch = branch.id_branch')->from('branch')
                        ->get('purchase_return_product')->result();
    }
    public function test_price() {
        $result = $this->db->get('model_variants')->result();
        foreach ($result as $res){
            $prices = $this->db->select('total_amount, date, idvariant')->where('idvariant', $res->id_variant)->order_by('id_inward_product','desc')->get('inward_product',1)->result();
            if(count($prices)){
                foreach($prices as $pr){
                    $this->db->where('id_variant', $pr->idvariant)->set('last_purchase_price', round($pr->total_amount,3))->update('model_variants');
                }
            }
        }
    }
}
