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
         $this->db->insert('inward_product', $data);
        return $this->db->insert_id(); 
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
    
     ////////////////////////////// PURCHASE EDIT - BY NIKHIL/////////////////////////
    
    public function get_inward_data_edit(){
         
        $invoice_no_expolde = $_POST['invno'];
        $invoice_expolde = explode("/",$invoice_no_expolde); 
        $inv_no = $invoice_expolde[3];
        $data = $this->db->where('inward.id_inward', $inv_no)
                         ->where('idvendor = id_vendor')->from('vendor')
                         ->where('created_by = id_users')->from('users')
                         ->get('inward')->result();
        //         echo '<pre>'; 
//        print_r($data);die;
        return $data;
    }
    public function get_inward_product_byid_edit(){
        $invoice_no_expolde = $_POST['invno'];
        $invoice_expolde = explode("/",$invoice_no_expolde); 
        $inv_no = $invoice_expolde[3]; 
        $data = $this->db->select('nd.*,mv.*,wn.idbranch')
                         ->where('nd.idinward', $inv_no)
                         ->where('nd.idinward = wn.id_inward')->from('inward wn')
                         ->where('nd.idvariant = mv.id_variant')->from('model_variants mv')
                         ->where('nd.idgodown = gd.id_godown')->from('godown gd')
                         ->where('sku.id_sku_type = nd.idskutype')->from('sku_type sku')
                         ->get('inward_data nd')->result();
//         echo '<pre>'; 
//        print_r($data);die;
        return $data;
       
    }    
    public function get_inward_product_byid_edit_data($idinward,$idvariant){
        $invoice_no_expolde = $_POST['invno'];
        $invoice_expolde = explode("/",$invoice_no_expolde); 
        $inv_no = $invoice_expolde[3]; 
        $data = $this->db->select('pd.*')
                         ->where('pd.idinward', $idinward)
                         ->where('pd.idvariant', $idvariant)
                         ->get('inward_product pd')->result();
//         echo '<pre>'; 
//        print_r($data);die;
        return $data;
       
    }            
    public function ajax_get_branch_vendor_id() {
        //print_r($_POST);die;
        $branchid = $_POST['branch_id'];
        $vendorid = $_POST['vendorid'];
        $data = $this->db->select('br.branch_state_name,')
                         ->where('br.id_branch', $branchid)
                         ->get('branch br')->row();
        
        $data1 = $this->db->select('vr.state,')
                         ->where('vr.id_vendor', $vendorid)
                         ->get('vendor vr')->row();
       
        if($data->branch_state_name == $data1->state){
            return '0_'.$data1->state;
        }else{
            return '1_'.$data1->state;
        }
            
        
     }    
    public function get_branch_vendor_state_data() {
        //print_r($_POST);die;
        $invoice_no_expolde = $_POST['invno'];
        $invoice_expolde = explode("/",$invoice_no_expolde); 
        $inv_no = $invoice_expolde[3]; 
        
        $data = $this->db->select('nd.vendor_state,b.branch_state_name')
                         ->join('branch b','b.id_branch = nd.idbranch','left')
                         ->where('nd.id_inward', $inv_no)
                         ->get('inward nd')->row();
        
        return $data;
        
    }    
    public function get_branch_imei_data($inward_product){
        
        $invoice_no_expolde = $_POST['invno'];
        $invoice_expolde = explode("/",$invoice_no_expolde); 
        $inv_no = $invoice_expolde[3];
        $cart = array();
        foreach ($inward_product as $value) {
            
            if($value->idskutype != 4){
                $data = $this->db->select('nd.idbranch,ip.imei_no,nd.id_inward')
                         ->where('ip.idinward = nd.id_inward')->from('inward_product ip')
                         ->where('nd.id_inward', $value->idinward)
                         ->where('ip.imei_no', $value->imei_srno)
                         ->where('ip.idskutype <> 4')
                         ->get('inward nd')->result();
                
                $data1 = $this->db->select('nd.imei_no,nd.idbranch,nd.idinward')
                         ->where('nd.idinward', $value->idinward)
                         ->where('nd.imei_no', $value->imei_srno)
                         ->where('nd.idskutype <> 4')
                         ->get('stock nd')->result();
//                echo '<pre>';
//                print_r($data1);die;
                for($i=0;$i<count($data);$i++){
                    for($j=$i;$j<=$i;$j++){
                        if($data[$i]->imei_no == $data1[$j]->imei_no){
                            if($data[$i]->idbranch == $data1[$j]->idbranch){
                                array_push($cart, 1);
                            }else{
                                array_push($cart, 0);
                            }
                        }else{
                           array_push($cart, 0);
                        }     
                    }   
                }
                
            }else{
               $inv_qty = $value->qty;
               $inv_variant = $value->idvariant;
               $inv_brach = $value->idbranch;
               //print_r($inv_qty);die;
               $data1 = $this->db->select('nd.qty')
                         ->where('nd.idbranch', $inv_brach)
                         ->where('nd.idvariant', $inv_variant)
                         ->where('nd.idskutype = 4')
                         ->get('stock nd')->row();
               
                if($data1->qty >= $inv_qty){
                    array_push($cart, 1);
                }else{
                    array_push($cart, 0);
                }
            }
            
        }
           
//        echo '<pre>'; 
//        print_r($cart);die;
        if (in_array('0',$cart))
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }    
    public function save_inward_edit() {
//        echo '<pre>';
//        print_r($_POST);die;
        $this->db->trans_begin();
        $product_id = explode(",",$this->input->post('modelid'));
        //die('<pre>'.print_r($product_id,1).'</pre>');
        $idinward = $this->input->post('idinward');
        $data = array(
                'idvendor' => $this->input->post('idvendor'),
                'vendor_state' => $this->input->post('state_ven'),
                'supplier_invoice_no' => $this->input->post('invoice_id'),
                'vendor_invoice_date' => $this->input->post('invoice_date'),
                'total_tax' => $this->input->post('total_tax'),
                'total_basic_amt' => $this->input->post('total_basic_amt'),
                'total_charges_amt' => $this->input->post('total_charges'),
                'total_discount_amt' => $this->input->post('total_discount'),
                'total_taxable_amt' => $this->input->post('total_taxable_amt'),
                'total_cgst_amt' => $this->input->post('total_cgst_amt'),
                'total_sgst_amt' => $this->input->post('total_sgst_amt'),
                'total_igst_amt' => $this->input->post('total_igst_amt'),
                'gross_amount' => $this->input->post('gross_total'),
                'overall_discount' => $this->input->post('overall_discount'),
                'final_amount' => $this->input->post('final_total'),
                'tcs_amount' => $this->input->post('tcs_amount'),
                'overall_amount' => $this->input->post('overall_amount'),
                'remark' => $this->input->post('remark'),
             );
        $idinward_update = $this->Inward_model->update_inward_edit($data,$idinward);
        if($idinward_update > 0){
        for($i = 0; $i < count($product_id); $i++){
            
            $qty = $this->input->post('qty['.$i.']');
            $scanned_csv = '';
            $inwardproduct_csv = '';
            $oldimei_csv = '';
            $z = $i + 1;
            
            if($this->input->post('imei_model_'.$z.'[]') || $this->input->post('imei_model_'.$z.'[]') != ''){
                $scanned_csv = implode(",",$this->input->post('imei_model_'.$z.'[]'));
            }
            
            if($this->input->post('idinward_product_old'.$z.'[]') || $this->input->post('idinward_product_old'.$z.'[]') != ''){
                $inwardproduct_csv = implode(",",$this->input->post('idinward_product_old'.$z.'[]'));
            }
            if($this->input->post('idinwardproduct_emi'.$z.'[]') || $this->input->post('idinwardproduct_emi'.$z.'[]') != ''){
                $oldimei_csv = implode(",",$this->input->post('idinwardproduct_emi'.$z.'[]'));
            }
//            echo '<pre>';
//            print_r($oldimei_csv);die;
            $inward_data[$i] = array(
                'idinward' => $this->input->post('idinward'),
                'idgodown' => $this->input->post('idgodown['.$i.']'),
                'idproductcategory' => $this->input->post('idtype['.$i.']'),
                'idcategory' => $this->input->post('idcategory['.$i.']'),
                'idbrand' => $this->input->post('idbrand['.$i.']'),
                'idvariant' => $product_id[$i],
                'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                'product_name' => $this->input->post('product_name['.$i.']'),
                'qty' => $this->input->post('qty['.$i.']'),
                'price' => $this->input->post('price['.$i.']'),
                'idskutype' => $this->input->post('skutype['.$i.']'),
                'charges_amt' => $this->input->post('chrgs_amt['.$i.']'),
                'discount_per' => $this->input->post('discount_per['.$i.']'),
                'discount_amt' => $this->input->post('discount_amt['.$i.']'),
                'basic' => $this->input->post('basic['.$i.']'),
                'taxable_amt' => $this->input->post('taxable['.$i.']'),
                'cgst_per' => $this->input->post('cgst['.$i.']'),
                'sgst_per' => $this->input->post('sgst['.$i.']'),
                'igst_per' => $this->input->post('igst['.$i.']'),
                'cgst_amt' => $this->input->post('cgst_amt['.$i.']'),
                'sgst_amt' => $this->input->post('sgst_amt['.$i.']'),
                'igst_amt' => $this->input->post('igst_amt['.$i.']'),
                'tax' => $this->input->post('tax['.$i.']'),
                'total_amount' => $this->input->post('total['.$i.']'),
                'imei_srno' => rtrim($scanned_csv,','),
            );
            $productid = $product_id[$i];
            
            
            if($this->input->post('skutype['.$i.']') == 4){
                //$inwardproduct_arr = explode(",",$inwardproduct_csv);
                $idinward_data = $this->Inward_model->update_inward_data_edit_sku($inward_data[$i],$idinward,$productid);
                $inward_product[$i] = array(
                    'date' => $this->input->post('date'),
                    'idgodown' => $this->input->post('idgodown['.$i.']'),
                    'idskutype' => $this->input->post('skutype['.$i.']'),
                    'idproductcategory' => $this->input->post('idtype['.$i.']'),
                    'idcategory' => $this->input->post('idcategory['.$i.']'),
                    'idvariant' => $product_id[$i],
                    'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                    'idbrand' => $this->input->post('idbrand['.$i.']'),
                    'created_by' => $this->input->post('created_by'),
                    'idvendor' => $this->input->post('idvendor'),
                    'qty' => $this->input->post('qty['.$i.']'),
                    'idinward_data' => $idinward_data,
                    'idinward' => $this->input->post('idinward'),
                    'product_name' => $this->input->post('product_name['.$i.']'),
                    'price' => $this->input->post('price['.$i.']'),
                    'mrp' => $this->input->post('mrp['.$i.']'),
                    'charges_amt' => $this->input->post('chrgs_amt['.$i.']'),
                    'discount_per' => $this->input->post('discount_per['.$i.']'),
                    'discount_amt' => $this->input->post('discount_amt['.$i.']'),
                    'basic' => $this->input->post('basic['.$i.']'),
                    'taxable_amt' => $this->input->post('taxable['.$i.']'),
                    'cgst_per' => $this->input->post('cgst['.$i.']'),
                    'cgst_amt' => $this->input->post('cgst_amt['.$i.']'),
                    'sgst_per' => $this->input->post('sgst['.$i.']'),
                    'sgst_amt' => $this->input->post('sgst_amt['.$i.']'),
                    'igst_per' => $this->input->post('igst['.$i.']'),
                    'igst_amt' => $this->input->post('igst_amt['.$i.']'),
                    'tax' => $this->input->post('tax['.$i.']'),
                    'total_amount' => $this->input->post('total['.$i.']'),
                );
                $idinward_product = $this->Inward_model->update_inward_product_edit_sku($inward_product[$i],$productid,$idinward_data,$idinward);
                $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($product_id[$i],4,$this->input->post('idbranch'),$this->input->post('idgodown['.$i.']'));
//                $hostock = $this->Inward_model->get_hostock_byidmodel_skutype($product_id[$i], 4, 1);
                //print_r($hostock);die;
                if(count($hostock) === 0){
                    if($sale_type[$i] == 0){
                        $inward_stock_sku[$i] = array(
                            'date' => $date,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'is_gst' => $this->input->post('gstradio'),
                            'idvariant' => $product_id[$i],
                            'idbranch' => $this->input->post('idbranch'),
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                            'qty' => $this->input->post('qty['.$i.']'),
                            'idinward' => $idinward,
                            'idinward_product' => $idinward_product,
                        );
                        $this->Inward_model->save_stock_edit($inward_stock_sku[$i]);
                    }
                }else{
                    foreach ($hostock as $hstock){
                        $old_qty = $hstock->qty - $this->input->post('old_qty['.$i.']');
                        $qty = $old_qty + $this->input->post('qty['.$i.']');
                        $this->Inward_model->update_stock_byid($hstock->id_stock,$qty);
                    }
                }
                $last_purchase_price = array(
                    'last_purchase_price' => $this->input->post('total['.$i.']') / $this->input->post('qty['.$i.']'),
                );
                $this->Inward_model->update_variants_last_purchase_price($product_id[$i], $last_purchase_price);
               
            }else{
                //die('ddd');
                $scanned = explode(",",$scanned_csv);
                $inwardproduct_arr = explode(",",$inwardproduct_csv);
                $oldimei_arr = explode(",",$oldimei_csv);
                //print_r($scanned);die;
                for($j = 0; $j < count($scanned); $j++){
                    if($scanned[$j] != ''){
                    $idinward_stock_del = $this->Inward_model->delete_stock_edit($idinward,$inwardproduct_arr[$j],$oldimei_arr[$j]);
                    $idinward_product_del = $this->Inward_model->delete_inward_product_edit($idinward,$productid);
                    $idinward_data_del = $this->Inward_model->delete_inward_data_edit($idinward,$productid);
                        
                    if($idinward_stock_del > 0 && $idinward_product_del > 0 && $idinward_data_del > 0){
                    $idinward_data = $this->Inward_model->save_inward_data_edit($inward_data[$i],$idinward,$productid);
                        
                        $inward_product[$j] = array(
                            'date' => $this->input->post('date'),
                            'imei_no' => rtrim($scanned[$j],','),
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $this->input->post('created_by'),
                            'idvendor' => $this->input->post('idvendor'),
                            'idinward_data' => $idinward_data,
                            'idinward' => $this->input->post('idinward'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'price' => $this->input->post('price['.$i.']'),
                            'charges_amt' => $this->input->post('chrgs_amt['.$i.']') / $qty,
                            'mrp' => $this->input->post('mrp['.$i.']'),
                            'price' => $this->input->post('price['.$i.']'),
                            'charges_amt' => $this->input->post('chrgs_amt['.$i.']') / $qty,
                            'discount_per' => $this->input->post('discount_per['.$i.']'),
                            'discount_amt' => $this->input->post('discount_amt['.$i.']') / $qty,
                            'basic' => $this->input->post('basic['.$i.']') / $qty,
                            'taxable_amt' => $this->input->post('taxable['.$i.']') / $qty,
                            'cgst_per' => $this->input->post('cgst['.$i.']'),
                            'cgst_amt' => $this->input->post('cgst_amt['.$i.']') / $qty,
                            'sgst_per' => $this->input->post('sgst['.$i.']'),
                            'sgst_amt' => $this->input->post('sgst_amt['.$i.']') / $qty,
                            'igst_per' => $this->input->post('igst['.$i.']'),
                            'igst_amt' => $this->input->post('igst_amt['.$i.']') / $qty,
                            'tax' => $this->input->post('tax['.$i.']') / $qty,
                            'total_amount' => $this->input->post('total['.$i.']') / $qty,
                        );
                        
                        $idinward_product = $this->Inward_model->save_inward_product_edit($inward_product[$j]);
                        $this->Inward_model->update_model_variant_mrp($product_id[$i], $this->input->post('mrp['.$i.']'));
                        $inward_stock[$j] = array(
                            'date' => $this->input->post('date'),
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'imei_no' => $scanned[$j],
                            'idbranch' => $this->input->post('idbranch'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'is_gst'   => $this->input->post('gstradio'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $this->input->post('created_by'),
                            'idvendor' => $this->input->post('idvendor'),
                            'idinward' => $idinward,
                            'idinward_product' => $idinward_product,
                        );
//                        echo '<pre>';
//                        print_r($inward_stock[$j]);die;
                        $this->Inward_model->save_stock_edit($inward_stock[$j]);
                        
                        // update_variants_last_purchase_price
                        $last_purchase_price = array(
                            'last_purchase_price' => $this->input->post('total['.$i.']') / $qty,
                        );
                        $this->Inward_model->update_variants_last_purchase_price($product_id[$i], $last_purchase_price);
                        
                      }
                    }
                }
//                }
            }
            
        }
        
       }
       $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashsave_import_invoicedata('save_data', 'Purchase Invoice Edit is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Purchase Invoice Edit Successfully');
        }
        return redirect('Purchase/purchase_edit/');
    }    
    public function update_inward_edit($data,$idinward) {
        $this->db->where('id_inward', $idinward)->update('inward', $data);
        $return_val = $this->db->affected_rows();
        return $return_val;
    }
    public function delete_inward_data_edit($idinward,$product_id) {     
        $this->db->where('idinward',$idinward)->where('idvariant',$product_id)->delete('inward_data');
        $return_val = $this->db->affected_rows();
        return $return_val;
    }
    public function save_inward_data_edit($data,$idinward,$product_id) {     
            $this->db->insert('inward_data', $data);
            return $insertid = $this->db->insert_id();
    }
    public function update_inward_data_edit_sku($data,$idinward,$product_id) {
        $this->db->where('idinward', $idinward)->where('idvariant', $product_id)->update('inward_data', $data);
        $return_val = $this->db->affected_rows();       
            $id_inwarddata = $this->db->select('id_inward_data')
                            ->where('idvariant', $product_id)
                            ->where('idinward', $idinward)
                            ->get('inward_data')->row();
            //print_r($id_inwarddata->id_inward_data);die;
            return $id_inwarddata->id_inward_data;
       
    }
    public function delete_inward_product_edit($idinward,$product_id) {        
        $this->db->where('idinward',$idinward)->where('idvariant',$product_id)->delete('inward_product');
        $return_val = $this->db->affected_rows();
        return $return_val;           
    }
    public function save_inward_product_edit($data) { 
            $this->db->insert('inward_product', $data);
            return $insertid = $this->db->insert_id();
    }
    public function update_inward_product_edit_sku($data,$idinward,$productid,$idinward_data) {
        $this->db->where('idinward', $idinward)->where('idvariant', $productid)->where('idinward_data', $idinward_data)->update('inward_product', $data);
        //$return_val = $this->db->affected_rows();
    }
    public function delete_stock_edit($idinward,$idinward_product,$imei_no) {
        //print_r($idinward_product);die;
        $this->db->where('idinward',$idinward)->where('idinward_product',$idinward_product)->where('imei_no',$imei_no)->delete('stock');
        $return_val = $this->db->affected_rows();
        return $return_val;
    }
    public function save_stock_edit($data) {
        //print_r($idinward_product);die;
        $this->db->insert('stock', $data);
        $insertid = $this->db->insert_id(); 
    }
}
?>