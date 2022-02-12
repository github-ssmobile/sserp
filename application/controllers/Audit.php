<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Sale_model');
        $this->load->model('General_model');
        $this->load->model('Purchase_model');
    }
    public function stock_audit() {
        $q['tab_active'] = 'Audit';
        if($this->session->userdata('level') == 1){
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){ 
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        $q['godown_data'] = $this->General_model->get_godown_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('audit/audit',$q);
    }
    public function ajax_check_godown_allow_for_allbrand_audit(){
        $idgodown = $this->input->post('idgodown');
        $godown_data = $this->Audit_model->get_godown_byid($idgodown);
        $brand_data = $this->General_model->get_active_brand_data();
        $allbrand_audit = $godown_data->all_brand_audit;
      ?>
        <input type="hidden" name="allow_allbrand_audit" id="allow_allbrand_audit" value="<?php echo $allbrand_audit ?>">
         <select class="form-control chosen-select " name="idbrand" id="idbrand">
                <?php  if($allbrand_audit == 1){?> 
                    <option value="all">All</option>
                <?php  }else{ ?>
                    <option value="0">Select Brand</option>
                <?php } ?>
                <?php foreach($brand_data as $brand){ ?>
                <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                <?php } ?>
            </select>
        <?php
    }
    public function ajax_check_idvarian_audit_temp_data(){
        $idbrand = $this->input->post('idbrand');
        $idcat = $this->input->post('idcat');
        $idbranch = $this->input->post('idbranch');
        
        $audit_temp_data = $this->Audit_model->ajax_get_qty_audit_temp_data_byid($idbranch, $idcat, $idbrand, $_SESSION['id_users']);
         
        if(count($audit_temp_data) > 0){
          echo count($audit_temp_data);
        }else{
           echo '0';
        }
    }

    public function get_brand_for_audit(){
        $idbrand = $this->input->get('idbrand');
        $idcat = $this->input->get('idcat');
        $idbranch = $this->input->get('idbranch');
        $idgodown = $this->input->get('idgodown');
        $allow_allbrand_audit = $this->input->get('allow_allbrand_audit');
        $q['audit_start'] = $this->input->get('audit_start');
        $iduser = $_SESSION['id_users'];
        $d = date('Y-m-d');
        
        $q['audit_done_data'] = $this->Audit_model->ajax_check_branch_audit($idbranch, $idcat, $idbrand, $idgodown, $iduser, $d);

        $q['stock_data'] = $this->Audit_model->get_stock_data($idcat, $idbrand, $idbranch, $idgodown);
        $q['intransit_stock'] = $this->Audit_model->get_intransit_stock_data($idcat, $idbrand, $idbranch, $idgodown);
        $q['trnasfer_stock'] = $this->Audit_model->get_transfer_stock_data($idcat, $idbrand, $idbranch, $idgodown);
        $q['cat_data'] = $this->Audit_model->get_product_category_by_id($idcat);

        $q['brand_data'] = $this->Audit_model->get_brand_by_id($idbrand);

        $q['branch_data'] = $this->General_model->get_branch_byid($idbranch);
        $q['godown_data'] = $this->Audit_model->get_godown_byid($idgodown);
        $q['audit_temp_data'] = $this->Audit_model->get_audit_temp_data_byid($idbranch, $idcat, $idbrand, $_SESSION['id_users'], $idgodown);

        $q['qty_stock_data'] = $this->Audit_model->get_qty_modelvariant_data($idcat, $idbrand);
//        die('<pre>'.print_r( $q['audit_done_data'],1).'</pre>');

        $q['idbrand'] = $idbrand;
        $q['idcat'] = $idcat;
        $q['idbranch'] = $idbranch;
        $q['idgodown'] = $idgodown;
//        $_SESSION['barcodes'] = array();
        $_SESSION['temp_barcode'] = array();
        $_SESSION['qty_barcode'] = array();
         $_SESSION['all_variants'] = array();
//        $_SESSION['status'] = array();
        $q['tab_active'] = 'Audit';
        $this->load->view('audit/audit_product_scan',$q);
    }
    
    public function ajax_scan_barcode(){
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $barcode = $this->input->post('scan_barcode');
        $audit_start = $this->input->post('audit_start');
        
        $status = '';
        $remark ='';
        
        $role = $this->session->userdata('role_name');
        
        if(!in_array($barcode, $_SESSION['temp_barcode'])){
            $check_barcode = $this->Audit_model->ajax_check_barcode_byidcat($barcode, $idcat, $idbrand);
            if($check_barcode){
                array_push($_SESSION['temp_barcode'], $barcode);
                if($check_barcode->idbranch == $idbranch ){ ?>
                    <tr>
                        <td><?php echo $check_barcode->imei_no; ?></td>
                        <td><?php echo $check_barcode->product_category_name; ?></td>
                        <td><?php echo $check_barcode->brand_name ?></td>
                        <td><?php echo $check_barcode->product_name; ?></td>
                        <td><?php echo $check_barcode->qty; ?></td>
                        <td>matched
                            <?php $status = "matched"; // array_push( $_SESSION['status'], 'matched');
                             $remark ='';
                            ?>
                            <input type="hidden"  class="mtch_cnt" value="<?php echo $check_barcode->qty?>">
                        </td>
                        <td></td>
                    </tr>
                <?php } elseif($check_barcode->idbranch == 0 ){
                    if($check_barcode->temp_idbranch == $idbranch){ ?>
                        <tr>
                            <td><?php echo $check_barcode->imei_no; ?></td>
                            <td><?php echo $check_barcode->product_category_name; ?></td>
                            <td><?php echo $check_barcode->brand_name ?></td>
                            <td><?php echo $check_barcode->product_name; ?></td>
                            <td><?php echo $check_barcode->qty; ?></td>
                            <td> matched In Transit(Arriving)
                                <?php
                                $status = "match in"; // array_push( $_SESSION['status'], 'matched');
                                $remark = "In Transit(Arriving)";
                                ?>
                                <input type="hidden"  class="mtch_cnt" value="0">
                            </td>
                             <td>In Transit(Arriving)</td>
                        </tr>
                    <?php } elseif($check_barcode->transfer_from == $idbranch){ ?>
                        <tr>
                            <td><?php echo $check_barcode->imei_no; ?></td>
                            <td><?php echo $check_barcode->product_category_name; ?></td>
                            <td><?php echo $check_barcode->brand_name ?></td>
                            <td><?php echo $check_barcode->product_name; ?></td>
                            <td><?php echo $check_barcode->qty; ?></td>
                            <td>Transfer to Other Branch
                                <?php
                                $status = "matched out"; // array_push( $_SESSION['status'], 'matched');
                                $remark = "Transfer to Other Branch";
                                ?>
                                <input type="hidden"  class="mtch_cnt" value="0">
                            </td>
                             <td>Transfer to Other Branch</td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td><?php echo $check_barcode->imei_no; ?></td>
                        <td><?php echo $check_barcode->product_category_name; ?></td>
                        <td><?php echo $check_barcode->brand_name ?></td>
                        <td><?php echo $check_barcode->product_name; ?></td>
                        <td></td>
                        <td>unmatched
                            <?php
                            $status = "unmatched"; // array_push( $_SESSION['status'], 'unmatched');
                             $remark ='';
                            ?>
                            <input type="hidden"  class="unmtch_cnt" value="1">
                        </td>
                         <td></td>
                    </tr>
                <?php }
                $data = array(
                    'imei_no' => $check_barcode->imei_no,
                    'idstock' => $check_barcode->id_stock,
                    'idskutype' => $check_barcode->idskutype,
                    'idgodown' => $check_barcode->idgodown,
                    'idproductcategory' => $check_barcode->idproductcategory,
                    'idcategory' => $check_barcode->idcategory,
                    'idvariant' => $check_barcode->idvariant,
                    'idmodel' => $check_barcode->idmodel,
                    'idbrand' => $check_barcode->idbrand,
                    'product_name' => $check_barcode->product_name,
                    'idbranch' => $idbranch,
                    'date' => date('Y-m-d'),
                    'role' => $role,
                    'qty' => $check_barcode->qty,
                    'created_by' => $_SESSION['id_users'],
                    'status' => $status,
                    'remark' => $remark,
                    'audit_start' => $audit_start,
                );
                $this->Audit_model->save_audit_temp_data($data);
            }else{ ?>
                <script>
                    alert("IMEI Not Found.");
                    location.reload(); 
                </script>
            <?php }
        }else{ ?>
            <script>
                alert("IMEI Already Exsists");
                location.reload(); 
            </script>
        <?php }
    }
   /* 
    public function ajax_qty_scan_barcode() {
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idvariant = $this->input->post('idvariant');
        $idgodown = $this->input->post('idgodown');
        $qty = $this->input->post('qty');
        $audit_start = $this->input->post('audit_start');
//        die(print_r($idvariant));
        
        $total_qty =0;
        $match_qty =0;
        $unmatch_qty =0;
        $missing_qty =0;
        $status = array();
        $qty_array = array();
        
         $role = $this->session->userdata('role_name');

        if(!in_array($idvariant, $_SESSION['qty_barcode'])) {
            
            array_push($_SESSION['qty_barcode'],$idvariant);
          
            $check_stock_data = $this->Audit_model->ajax_get_stockdata_byidvariant($idvariant, $idbranch, $idgodown);
//            die('<pre>'.print_r($check_stock_data,1).'</pre>');
            
            if($check_stock_data ){
                
                $total_qty = $check_stock_data->qty;
                if($qty <= $total_qty){
                   $match_qty = $qty;
                   $missing_qty = $total_qty - $match_qty;
                }
                if($qty > $total_qty){
                   $match_qty = $total_qty;
                   $unmatch_qty = $qty - $match_qty;
                }
                if($match_qty > 0){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $match_qty;  array_push($qty_array, $match_qty); ?></td>
                        <td>matched
                            <?php //$status = "matched";
                            array_push( $status, 'matched');
                            ?>
                            <input type="hidden"  class="mtch_cnt" value="<?php echo $match_qty; ?>">
                        </td>
                        <td></td>
                    </tr>
                <?php }
                if($unmatch_qty > 0){?>
                   <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $unmatch_qty; array_push($qty_array, $unmatch_qty); ?></td>
                        <td>unmatched
                            <?php //$status = "unmatched";
                            array_push( $status, 'unmatched');
                            ?>
                            <input type="hidden"  class="unmtch_cnt" value="<?php echo $unmatch_qty; ?>">
                        </td>
                        <td></td>
                    </tr>
                <?php }
                if($missing_qty > 0){?>
                    <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $missing_qty; array_push($qty_array, $missing_qty); ?></td>
                        <td>missing
                            <?php //$status = "missing";
                            array_push( $status, 'missing');
                            ?>
                        </td>
                        <td></td>
                    </tr>
                <?php }
                for($i=0; $i<count($status); $i++){
                    $data = array(
                        'idstock' => $check_stock_data->id_stock,
                        'imei_no' => $check_stock_data->imei_no,
                        'idskutype' => $check_stock_data->idskutype,
                        'idgodown' => $check_stock_data->idgodown,
                        'idproductcategory' => $check_stock_data->idproductcategory,
                        'idcategory' => $check_stock_data->idcategory,
                        'idvariant' => $check_stock_data->idvariant,
                        'idmodel' => $check_stock_data->idmodel,
                        'idbrand' => $check_stock_data->idbrand,
                        'product_name' => $check_stock_data->product_name,
                        'idbranch' => $check_stock_data->idbranch,
                        'qty' => $qty_array[$i],
                        'date' => date('Y-m-d'),
                        'role' => $role,
                        'created_by' => $_SESSION['id_users'],
                        'status' => $status[$i],
                        'audit_start' => $audit_start,
                    );
                    $this->Audit_model->save_audit_temp_data($data);
                }
             
            }else{ ?>
                <script>
                    alert("Model Not Found");
                </script>
            <?php }
        }else { ?>
            <script>
                alert("Model Already Exists");
            </script>
        <?php }
    }
    
     public function save_scan_barcodes(){
        $this->db->trans_begin();
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $iduser =  $_SESSION['id_users'];
        
        $_SESSION['scan_barcode'] = array();
        $_SESSION['missing_qty_barcode'] = array();
        $_SESSION['missing_qty'] = array();
        
        $all_variants = $_SESSION['all_variants'];
        $datetime = date('Y-m-d H:i:s');
        
        $mising_mop = 0;
        $qty_mop =0;
        $total_sale =0;
        
        $audit_start_datetime  = $this->Audit_model->get_audit_start_date_from_audit_temp($idbranch,$idcat,$idbrand,$iduser, $idgodown);
        
        $audit_start = $audit_start_datetime->audit_start;
        if(count($_SESSION['qty_barcode']) > 0) {
            $not_scann = $this->Audit_model->get_stock_missing_byidvariant($_SESSION['qty_barcode'], $idbranch, $idcat, $idbrand, $idgodown);
        // --------save not sacnned idvariant in audit for missing(idskutype 4 - Qty Barcode)  -------------------- 
            if(count($not_scann) > 0){
               for($n=0; $n<count($not_scann); $n++){
                    array_push($_SESSION['missing_qty_barcode'], $not_scann[$n]->idvariant);
                    array_push($_SESSION['missing_qty'], $not_scann[$n]->qty);
                   $data_idvariant = array(
                       'idstock' => $not_scann[$n]->id_stock,
                       'finish_date' => date('Y-m-d'),
                       'imei_no' => $not_scann[$n]->imei_no,
                       'idskutype' => $not_scann[$n]->idskutype,
                       'idgodown' => $not_scann[$n]->idgodown,
                       'idproductcategory' => $not_scann[$n]->idproductcategory,
                       'idcategory' => $not_scann[$n]->idcategory,
                       'idvariant' => $not_scann[$n]->idvariant,
                       'idmodel' => $not_scann[$n]->idmodel,
                       'idbrand' => $not_scann[$n]->idbrand,
                       'product_name' => $not_scann[$n]->product_name,
                       'idbranch' => $not_scann[$n]->idbranch,
                       'qty' => $not_scann[$n]->qty,
                       'date' => date('Y-m-d'),
                       'role' => $this->session->userdata('role_name'),
                       'status' => 'missing',
                       'created_by' => $_SESSION['id_users'],
                       'audit_start' => $audit_start,
                       'entry_time' => $datetime,
                   );
                   $this->Audit_model->save_audit_data($data_idvariant);
               }
           }
        }
        // ------Audit Temp Data -----------
        $audit_temp_data = $this->Audit_model->get_audit_temp_data_byid($idbranch, $idcat, $idbrand, $iduser, $idgodown);
    
        if(count($audit_temp_data) > 0){
            
            $role = $audit_temp_data[0]->role;
            for($i=0; $i<count($audit_temp_data); $i++){
                
                if($audit_temp_data[$i]->idskutype != 4){
                    array_push($_SESSION['scan_barcode'], $audit_temp_data[$i]->imei_no);
                }
                if($audit_temp_data[$i]->idskutype == 4 && $audit_temp_data[$i]->status == 'missing'){
                    array_push($_SESSION['missing_qty_barcode'], $audit_temp_data[$i]->idvariant);
                    array_push($_SESSION['missing_qty'], $audit_temp_data[$i]->qty);
                }
                //------------- Save Audit_temp data into Audit ----------
                $data = array(
                    'idstock' => $audit_temp_data[$i]->idstock,
                    'finish_date' => date('Y-m-d'),
                    'imei_no' => $audit_temp_data[$i]->imei_no,
                    'idskutype' => $audit_temp_data[$i]->idskutype,
                    'idgodown' => $audit_temp_data[$i]->idgodown,
                    'idproductcategory' => $audit_temp_data[$i]->idproductcategory,
                    'idcategory' => $audit_temp_data[$i]->idcategory,
                    'idvariant' => $audit_temp_data[$i]->idvariant,
                    'idmodel' => $audit_temp_data[$i]->idmodel,
                    'idbrand' => $audit_temp_data[$i]->idbrand,
                    'product_name' => $audit_temp_data[$i]->product_name,
                    'idbranch' => $audit_temp_data[$i]->idbranch,
                    'qty' => $audit_temp_data[$i]->qty,
                    'date' => $audit_temp_data[$i]->date,
                    'role' => $audit_temp_data[$i]->role,
                    'created_by' => $_SESSION['id_users'],
                    'status' => $audit_temp_data[$i]->status,
                    'remark' => $audit_temp_data[$i]->remark,
                    'audit_start' => $audit_temp_data[$i]->audit_start,
                    'entry_time' => $datetime,
                );
                $this->Audit_model->save_audit_data($data);
            }
            
            if(count($_SESSION['scan_barcode']) > 0){
               
                // -------- Missing Imei_no Data From stock --------
                $get_missing_stock = $this->Audit_model->get_stock_missing_byid($_SESSION['scan_barcode'], $idbranch, $idcat, $idbrand, $idgodown);
                
                if(count($get_missing_stock) > 0){
                    
                    for($j=0; $j < count($get_missing_stock); $j++){
                        $missing = array(
                            'idstock' => $get_missing_stock[$j]->id_stock,
                            'finish_date' => date('Y-m-d'),
                            'imei_no' => $get_missing_stock[$j]->imei_no,
                            'idskutype' => $get_missing_stock[$j]->idskutype,
                            'idgodown' => $get_missing_stock[$j]->idgodown,
                            'idproductcategory' => $get_missing_stock[$j]->idproductcategory,
                            'idcategory' => $get_missing_stock[$j]->idcategory,
                            'idvariant' => $get_missing_stock[$j]->idvariant,
                            'idmodel' => $get_missing_stock[$j]->idmodel,
                            'idbrand' => $get_missing_stock[$j]->idbrand,
                            'product_name' => $get_missing_stock[$j]->product_name,
                            'idbranch' => $get_missing_stock[$j]->idbranch,
                            'qty' => $get_missing_stock[$j]->qty,
                            'date' => date('Y-m-d'),
                            'role' => $role,
                            'created_by' => $_SESSION['id_users'],
                            'status' => 'missing',
                            'audit_start' => $audit_start,
                            'entry_time' => $datetime,
                        );
                        if($idaudit = $this->Audit_model->save_audit_data($missing)){
                            $imei_history[]=array(
                                'imei_no' => $get_missing_stock[$j]->imei_no,
                                'entry_type' => 'Missing',
                                'entry_time' => $datetime,
                                'date' => date('Y-m-d'),
                                'idbranch' => $get_missing_stock[$j]->idbranch,
                                'idgodown' => $get_missing_stock[$j]->idgodown,
                                'idvariant' => $get_missing_stock[$j]->idvariant,
                                'model_variant_full_name' => $get_missing_stock[$j]->product_name,
                                'idimei_details_link' => 8, // Sale from imei_details_link table
                                'iduser' => $_SESSION['id_users'],
                                'idlink' => $idaudit,
                            );
                        }
                        $mising_mop = $mising_mop + $get_missing_stock[$j]->mop; //missing imei total amount
                    }
                    if(count($imei_history) > 0){
                        if($this->session->userdata('idrole') == 21){
                            $this->General_model->save_batch_imei_history($imei_history);
                        }
                    }
                }
            }
            //--------Missing Qty barcodes --------------
                
            if(count($_SESSION['missing_qty_barcode']) > 0){
                $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($_SESSION['missing_qty_barcode'], $idgodown, $idbranch);
                $miss_qty = $_SESSION['missing_qty'];
                
                 if(count($get_missing_qty_stock) > 0){
                    for($q=0; $q < count($get_missing_qty_stock); $q++){
                       $qty_mop = $qty_mop +($get_missing_qty_stock[$q]->mop * $miss_qty[$q]); // Missing Qty total amount
                    }
                }
            }

            $customer_data = $this->Audit_model->get_missing_customer_data();

            //--------Auditor Role -----------------
        if($this->session->userdata('idrole') == 21){
                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
                $invid = $invoice_no->invoice_no + 1;
                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
                $y1 = $y - 1;
                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
                
                //-------Total Missing Amount --------------
                $total_sale = $mising_mop + $qty_mop; 
               
                if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
                    //------- Missing Sale Invoice----------
                    $sale_missing = array(
                        'date' => date('Y-m-d'),
                        'inv_no' => $inv_no,
                        'customer_fname' => $customer_data->customer_fname,
                        'customer_contact' => $customer_data->customer_contact,
                        'idcustomer' => $customer_data->id_customer,
                        'customer_gst' => $customer_data->customer_gst,
                        'customer_address' => $customer_data->customer_address,
                        'customer_pincode' => $customer_data->customer_pincode,
                        'customer_idstate' => $customer_data->idstate,
                        'idsalesperson' => $iduser,
                        'basic_total' => $total_sale,
                        'discount_total' => 0,
                        'final_total' => $total_sale,
                        'idbranch' => $idbranch,
                        'corporate_sale' => 0,
                        'gst_type' => 0,
                        'remark' => "Branch Missing",
                        'created_by' => $iduser,
                    );
                  
                    if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){
                        
                        $sale_payment = array(
                            'inv_no' => $inv_no,
                            'idcustomer' => $customer_data->id_customer,
                            'idsale' => $idsale,
                            'date' => date('Y-m-d'),
                            'idbranch' => $idbranch,
                            'corporate_sale' => 0,
                            'idpayment_head ' => 6,
                            'idpayment_mode ' => 18,
                            'amount' => $total_sale,
                            'transaction_id' => 'missing',
                            'created_by' => $iduser,
                            'payment_receive' => 0,
                        );
                        $this->Audit_model->save_sale_payment_data($sale_payment);
                        
                        if(count($get_missing_stock) > 0){
                            //--------sale product imei missing -------------
                            for($im = 0; $im < count($get_missing_stock); $im++){
                                $missing_imei_data = array(
                                    'idbranch' => $get_missing_stock[$im]->idbranch,
                                    'idskutype' => $get_missing_stock[$im]->idskutype,
                                    'idgodown' => $get_missing_stock[$im]->idgodown,
                                    'date' => date('Y-m-d'),
                                    'inv_no' => $inv_no,
                                    'product_name' => $get_missing_stock[$im]->product_name,
                                    'imei_no' => $get_missing_stock[$im]->imei_no,
                                    'hsn' => $get_missing_stock[$im]->hsn,
                                    'is_gst' => $get_missing_stock[$im]->is_gst,
                                    'is_mop' => $get_missing_stock[$im]->is_mop,
                                    'idsale' => $idsale,
                                    'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
                                    'idcategory' => $get_missing_stock[$im]->idcategory,
                                    'idvariant' => $get_missing_stock[$im]->idvariant,
                                    'idmodel' => $get_missing_stock[$im]->idmodel,
                                    'idbrand' => $get_missing_stock[$im]->idbrand,
                                    'qty' => $get_missing_stock[$im]->qty,
                                    'price' => $get_missing_stock[$im]->mop,
                                    'mop' => $get_missing_stock[$im]->mop,
                                    'landing' => $get_missing_stock[$im]->landing,
                                   'mrp' => $get_missing_stock[$im]->mrp,
                                    'salesman_price' => $get_missing_stock[$im]->salesman_price,
                                    'discount_amt' => 0,
                                    'basic' => $get_missing_stock[$im]->mop,
                                    'cgst_per' => $get_missing_stock[$im]->cgst,
                                    'sgst_per' => $get_missing_stock[$im]->sgst,
                                    'igst_per' => 0,
                                    'total_amount' => $get_missing_stock[$im]->mop,
                                    'idvendor' => $get_missing_stock[$im]->idvendor
                               );
                                $this->Audit_model->save_sale_product_data($missing_imei_data);
                            }
                        }
                        if(count($get_missing_qty_stock) > 0){
                        
                            for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
                                //--------sale product Qty missing -------------
                                
                                $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $miss_qty[$qt];
                                
                                $missing_qty_data = array(
                                    'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
                                    'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
                                    'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
                                    'date' => date('Y-m-d'),
                                    'inv_no' => $inv_no,
                                    'product_name' => $get_missing_qty_stock[$qt]->product_name,
                                    'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
                                    'hsn' => $get_missing_qty_stock[$qt]->hsn,
                                   'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
                                    'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
                                    'idsale' => $idsale,
                                    'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
                                    'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
                                    'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
                                    'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
                                    'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
                                    'qty' => $miss_qty[$qt],
                                    'price' => $get_missing_qty_stock[$qt]->mop,
                                    'mop' => $get_missing_qty_stock[$qt]->mop,
                                    'landing' => $get_missing_qty_stock[$qt]->landing,
                                    'mrp' => $get_missing_qty_stock[$qt]->mrp,
                                    'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
                                    'discount_amt' => 0,
                                    'basic' => $miss_qyt_basic,
                                    'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
                                    'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
                                    'igst_per' => 0,
                                    'total_amount' => $miss_qyt_basic,
                                    'idvendor' => $get_missing_qty_stock[$qt]->idvendor
                                );
                                $this->Audit_model->save_sale_product_data($missing_qty_data);
                            }
                        }
                        $invoice_data = array( 'invoice_no' => $invid );
                        $this->General_model->edit_db_branch($idbranch, $invoice_data);
                    }
                }
            }  
        } else {
            if(!$_SESSION['scan_barcode']){
             $get_missing_stock = $this->Audit_model->get_stock_missing($idbranch, $idcat, $idbrand);
                
                if(count($get_missing_stock) > 0){
                    
                    for($j=0; $j < count($get_missing_stock); $j++){
                        $missing = array(
                            'idstock' => $get_missing_stock[$j]->id_stock,
                            'finish_date' => date('Y-m-d'),
                            'imei_no' => $get_missing_stock[$j]->imei_no,
                            'idskutype' => $get_missing_stock[$j]->idskutype,
                            'idgodown' => $get_missing_stock[$j]->idgodown,
                            'idproductcategory' => $get_missing_stock[$j]->idproductcategory,
                            'idcategory' => $get_missing_stock[$j]->idcategory,
                            'idvariant' => $get_missing_stock[$j]->idvariant,
                            'idmodel' => $get_missing_stock[$j]->idmodel,
                            'idbrand' => $get_missing_stock[$j]->idbrand,
                            'product_name' => $get_missing_stock[$j]->product_name,
                            'idbranch' => $get_missing_stock[$j]->idbranch,
                            'qty' => $get_missing_stock[$j]->qty,
                            'date' => date('Y-m-d'),
                            'role' => $this->session->userdata('role_name'),
                            'created_by' => $_SESSION['id_users'],
                            'status' => 'missing',
                            'audit_start' => $datetime,
                            'entry_time' => $datetime,
                        );
                        if($idaudit = $this->Audit_model->save_audit_data($missing)){
                            $imei_history[]=array(
                                'imei_no' => $get_missing_stock[$j]->imei_no,
                                'entry_type' => 'Missing',
                                'entry_time' => $datetime,
                                'date' => date('Y-m-d'),
                                'idbranch' => $get_missing_stock[$j]->idbranch,
                                'idgodown' => $get_missing_stock[$j]->idgodown,
                                'idvariant' => $get_missing_stock[$j]->idvariant,
                                'model_variant_full_name' => $get_missing_stock[$j]->product_name,
                                'idimei_details_link' => 8, // Sale from imei_details_link table
                                'iduser' => $_SESSION['id_users'],
                                'idlink' => $idaudit,
                            );
                        }
                        $mising_mop = $mising_mop + $get_missing_stock[$j]->mop; //missing imei total amount
                    }
                    if(count($imei_history) > 0){
                        if($this->session->userdata('idrole') == 21){
                            $this->General_model->save_batch_imei_history($imei_history);
                        }
                    }
                }
            }
            
            if(count($_SESSION['missing_qty_barcode']) > 0){
                $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($_SESSION['missing_qty_barcode'], $idgodown, $idbranch);
                $miss_qty = $_SESSION['missing_qty'];
                
                 if(count($get_missing_qty_stock) > 0){
                    for($q=0; $q < count($get_missing_qty_stock); $q++){
                        $qty_mop = $qty_mop +($get_missing_qty_stock[$q]->mop * $miss_qty[$q]); // Missing Qty total amount
                    }
                }
            }
            $customer_data = $this->Audit_model->get_missing_customer_data();
            
            if($this->session->userdata('idrole') == 21){
                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
                $invid = $invoice_no->invoice_no + 1;
                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
                $y1 = $y - 1;
                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
                
                //-------Total Missing Amount --------------
                $total_sale = $mising_mop + $qty_mop; 
               
                if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
                    //------- Missing Sale Invoice----------
                    $sale_missing = array(
                        'date' => date('Y-m-d'),
                        'inv_no' => $inv_no,
                        'customer_fname' => $customer_data->customer_fname,
                        'customer_contact' => $customer_data->customer_contact,
                        'idcustomer' => $customer_data->id_customer,
                        'customer_gst' => $customer_data->customer_gst,
                        'customer_address' => $customer_data->customer_address,
                        'customer_pincode' => $customer_data->customer_pincode,
                        'customer_idstate' => $customer_data->idstate,
                        'idsalesperson' => $iduser,
                        'basic_total' => $total_sale,
                        'discount_total' => 0,
                        'final_total' => $total_sale,
                        'idbranch' => $idbranch,
                        'corporate_sale' => 0,
                        'gst_type' => 0,
                        'remark' => "Branch Missing",
                        'created_by' => $iduser,
                    );
                  
                    if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){
                        
                        $sale_payment = array(
                            'inv_no' => $inv_no,
                            'idcustomer' => $customer_data->id_customer,
                           'idsale' => $idsale,
                            'date' => date('Y-m-d'),
                           'idbranch' => $idbranch,
                            'corporate_sale' => 0,
                            'idpayment_head ' => 6,
                            'idpayment_mode ' => 18,
                            'amount' => $total_sale,
                            'transaction_id' => 'missing',
                            'created_by' => $iduser,
                            'payment_receive' => 0,
                        );
                        $this->Audit_model->save_sale_payment_data($sale_payment);
                        
                        if(count($get_missing_stock) > 0){
                            //--------sale product imei missing -------------
                            for($im = 0; $im < count($get_missing_stock); $im++){
                                $missing_imei_data = array(
                                    'idbranch' => $get_missing_stock[$im]->idbranch,
                                    'idskutype' => $get_missing_stock[$im]->idskutype,
                                    'idgodown' => $get_missing_stock[$im]->idgodown,
                                    'date' => date('Y-m-d'),
                                    'inv_no' => $inv_no,
                                    'product_name' => $get_missing_stock[$im]->product_name,
                                    'imei_no' => $get_missing_stock[$im]->imei_no,
                                    'hsn' => $get_missing_stock[$im]->hsn,
                                    'is_gst' => $get_missing_stock[$im]->is_gst,
                                    'is_mop' => $get_missing_stock[$im]->is_mop,
                                    'idsale' => $idsale,
                                    'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
                                    'idcategory' => $get_missing_stock[$im]->idcategory,
                                    'idvariant' => $get_missing_stock[$im]->idvariant,
                                    'idmodel' => $get_missing_stock[$im]->idmodel,
                                    'idbrand' => $get_missing_stock[$im]->idbrand,
                                    'qty' => $get_missing_stock[$im]->qty,
                                    'price' => $get_missing_stock[$im]->mop,
                                    'mop' => $get_missing_stock[$im]->mop,
                                    'landing' => $get_missing_stock[$im]->landing,
                                    'mrp' => $get_missing_stock[$im]->mrp,
                                    'salesman_price' => $get_missing_stock[$im]->salesman_price,
                                   'discount_amt' => 0,
                                    'basic' => $get_missing_stock[$im]->mop,
                                    'cgst_per' => $get_missing_stock[$im]->cgst,
                                    'sgst_per' => $get_missing_stock[$im]->sgst,
                                    'igst_per' => 0,
                                    'total_amount' => $get_missing_stock[$im]->mop,
                                    'idvendor' => $get_missing_stock[$im]->idvendor
                                );
                                $this->Audit_model->save_sale_product_data($missing_imei_data);
                           }
                        }
                        if(count($get_missing_qty_stock) > 0){
                        
                            for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
                                //--------sale product Qty missing -------------
                                
                                $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $miss_qty[$qt];
                                
                                $missing_qty_data = array(
                                    'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
                                    'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
                                    'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
                                    'date' => date('Y-m-d'),
                                    'inv_no' => $inv_no,
                                    'product_name' => $get_missing_qty_stock[$qt]->product_name,
                                    'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
                                    'hsn' => $get_missing_qty_stock[$qt]->hsn,
                                    'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
                                    'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
                                    'idsale' => $idsale,
                                    'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
                                    'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
                                    'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
                                    'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
                                    'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
                                    'qty' => $miss_qty[$qt],
                                    'price' => $get_missing_qty_stock[$qt]->mop,
                                    'mop' => $get_missing_qty_stock[$qt]->mop,
                                    'landing' => $get_missing_qty_stock[$qt]->landing,
                                    'mrp' => $get_missing_qty_stock[$qt]->mrp,
                                    'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
                                    'discount_amt' => 0,
                                    'basic' => $miss_qyt_basic,
                                    'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
                                    'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
                                    'igst_per' => 0,
                                    'total_amount' => $miss_qyt_basic,
                                    'idvendor' => $get_missing_qty_stock[$qt]->idvendor
                                );
                                $this->Audit_model->save_sale_product_data($missing_qty_data);
                            }
                        }
                        $invoice_data = array( 'invoice_no' => $invid );
                        $this->General_model->edit_db_branch($idbranch, $invoice_data);
                    }
                }
            }   
            
        }
        $this->Audit_model->delete_audit_temp_data($idbranch, $idcat, $idbrand, $iduser, $idgodown);
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Audit is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Audit Done generated');
        }
        
        if($this->session->userdata('idrole') == 21){
            redirect('Audit/get_missing_audit_for_sale/'.$idbranch.'/'.$idcat.'/'.$idbrand.'/'.$iduser.'/'.$idgodown.'/'.$datetime);
        }else{
             $this->session->set_flashdata('save_data', 'Audit Done Successfully');
            redirect('Audit/stock_audit');
        }
        
    }
     public function get_missing_audit_for_sale($idbranch, $idcat, $idbrand, $iduser, $idgodown, $datetime){
         $q['tab_active'] = 'Audit';
        $q['missing_data'] = $this->Audit_model->get_audit_missing_data($idbranch, $idcat, $idbrand, $iduser, $idgodown, $datetime);
         $this->load->view('audit/audit_missing_sale_data',$q);
    } 
    
     public function save_missing_audit_credit(){
        $mising_mop = 0;
        $qty_mop =0;
        $total_sale =0;
        $missing_imei_arr = array();
        $missing_qty_arr = array();
        $missing_idvariant_arr = array();
        
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idproductcategory');
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $iduser =  $_SESSION['id_users'];
        
        if(isset($_POST['checkimei'])){
            $checkimei = $_POST['checkimei'];      
            
            if(isset($_POST['missimei'])){
                $missimei = $_POST['missimei'];      
                foreach ($checkimei as $check => $val){
                   $missing_imei_arr[] = $missimei[$val];
                }
            }
            if(isset($_POST['missqty'])){
                $missqty = $_POST['missqty']; 
                $miss_variant = $_POST['idvariant'];
                foreach ($checkimei as $check => $val){
                   $missing_qty_arr[] = $missqty[$val];
                   $missing_idvariant_arr[] = $miss_variant[$val];
                }
            }
        }
        if(count($missing_imei_arr) > 0){
            $get_missing_stock = $this->Audit_model->get_missing_stock_details_byimei($missing_imei_arr);
            if(count($get_missing_stock) > 0){
                for($i=0; $i<count($get_missing_stock); $i++){
                    $mising_mop += $get_missing_stock[$i]->mop;
                   
                }
            }  
        }
        if(count($missing_qty_arr) > 0){
            $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($missing_idvariant_arr, $idgodown, $idbranch);
            if(count($get_missing_qty_stock) > 0){
                for($j=0; $j<count($get_missing_qty_stock); $j++){
                    $qty_mop += $get_missing_qty_stock[$j]->mop * $missing_qty_arr[$j];
                   
                }
            }  
        }
        
        if($this->session->userdata('idrole') == 21){
            $customer_data = $this->Audit_model->get_missing_customer_data();
            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
            $invid = $invoice_no->invoice_no + 1;
            $y = date('y', mktime(0, 0, 0, 9 + date('m')));
            $y1 = $y - 1;
            $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);

            //-------Total Missing Amount --------------
            $total_sale = $mising_mop + $qty_mop; 
            if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
           
//            
                //------- Missing Sale Invoice----------
                $sale_missing = array(
                    'date' => date('Y-m-d'),
                    'inv_no' => $inv_no,
                    'customer_fname' => $customer_data->customer_fname,
                    'customer_contact' => $customer_data->customer_contact,
                    'idcustomer' => $customer_data->id_customer,
                    'customer_gst' => $customer_data->customer_gst,
                    'customer_address' => $customer_data->customer_address,
                    'customer_pincode' => $customer_data->customer_pincode,
                    'customer_idstate' => $customer_data->idstate,
                    'idsalesperson' => $iduser,
                    'basic_total' => $total_sale,
                    'discount_total' => 0,
                    'final_total' => $total_sale,
                    'idbranch' => $idbranch,
                    'corporate_sale' => 0,
                    'gst_type' => 0,
                    'remark' => "Branch Missing",
                    'created_by' => $iduser,
                );
                 

                if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){

                    $sale_payment = array(
                        'inv_no' => $inv_no,
                        'idcustomer' => $customer_data->id_customer,
                        'idsale' => $idsale,
                        'date' => date('Y-m-d'),
                        'idbranch' => $idbranch,
                        'corporate_sale' => 0,
                        'idpayment_head ' => 6,
                        'idpayment_mode ' => 18,
                        'amount' => $total_sale,
                        'transaction_id' => 'missing',
                        'created_by' => $iduser,
                        'payment_receive' => 0,
                    );
                  
                    $this->Audit_model->save_sale_payment_data($sale_payment);

                    if(count($get_missing_stock) > 0){ 
//                        die('<pre>'.print_r($get_missing_stock,1).'</pre>');
                        
                        //--------sale product imei missing -------------
                        for($im = 0; $im < count($get_missing_stock); $im++){
                            $missing_imei_data = array(
                                'idbranch' => $get_missing_stock[$im]->idbranch,
                                'idskutype' => $get_missing_stock[$im]->idskutype,
                                'idgodown' => $get_missing_stock[$im]->idgodown,
                                'date' => date('Y-m-d'),
                                'inv_no' => $inv_no,
                                'product_name' => $get_missing_stock[$im]->product_name,
                                'imei_no' => $get_missing_stock[$im]->imei_no,
                                'hsn' => $get_missing_stock[$im]->hsn,
                                'is_gst' => $get_missing_stock[$im]->is_gst,
                                'is_mop' => $get_missing_stock[$im]->is_mop,
                                'idsale' => $idsale,
                                'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
                                'idcategory' => $get_missing_stock[$im]->idcategory,
                                'idvariant' => $get_missing_stock[$im]->idvariant,
                                'idmodel' => $get_missing_stock[$im]->idmodel,
                                'idbrand' => $get_missing_stock[$im]->idbrand,
                                'qty' => $get_missing_stock[$im]->qty,
                                'price' => $get_missing_stock[$im]->mop,
                                'mop' => $get_missing_stock[$im]->mop,
                                'landing' => $get_missing_stock[$im]->landing,
                                'mrp' => $get_missing_stock[$im]->mrp,
                                'salesman_price' => $get_missing_stock[$im]->salesman_price,
                                'discount_amt' => 0,
                                'basic' => $get_missing_stock[$im]->mop,
                                'cgst_per' => $get_missing_stock[$im]->cgst,
                                'sgst_per' => $get_missing_stock[$im]->sgst,
                                'igst_per' => 0,
                                'total_amount' => $get_missing_stock[$im]->mop,
                                'idvendor' => $get_missing_stock[$im]->idvendor
                            );
                            $this->Audit_model->save_sale_product_data($missing_imei_data);
                        }
                    }
                    if(count($get_missing_qty_stock) > 0){

                        for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
                            //--------sale product Qty missing -------------

                            $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $missing_qty_arr[$qt];

                            $missing_qty_data = array(
                                'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
                                'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
                                'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
                                'date' => date('Y-m-d'),
                                'inv_no' => $inv_no,
                                'product_name' => $get_missing_qty_stock[$qt]->product_name,
                                'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
                                'hsn' => $get_missing_qty_stock[$qt]->hsn,
                                'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
                                'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
                                'idsale' => $idsale,
                                'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
                                'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
                                'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
                                'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
                                'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
                                'qty' => $missing_qty_arr[$qt],
                                'price' => $get_missing_qty_stock[$qt]->mop,
                                'mop' => $get_missing_qty_stock[$qt]->mop,
                                'landing' => $get_missing_qty_stock[$qt]->landing,
                                'mrp' => $get_missing_qty_stock[$qt]->mrp,
                                'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
                                'discount_amt' => 0,
                                'basic' => $miss_qyt_basic,
                                'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
                                'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
                                'igst_per' => 0,
                                'total_amount' => $miss_qyt_basic,
                                'idvendor' => $get_missing_qty_stock[$qt]->idvendor
                            );
                            $this->Audit_model->save_sale_product_data($missing_qty_data);
                        }
                    }

                    $invoice_data = array( 'invoice_no' => $invid );
                    $this->General_model->edit_db_branch($idbranch, $invoice_data);
                }
            }
        }         
        $this->session->set_flashdata('save_data', 'Audit Done Successfully');
        redirect('Audit/stock_audit');
    }
    */
    public function ajax_qty_scan_barcode() {
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idvariant = $this->input->post('idvariant');
        $idgodown = $this->input->post('idgodown');
        $audit_start = $this->input->post('audit_start');
        $qty = $this->input->post('qty');
        $total_qty =0;
        $match_qty =0;
        $unmatch_qty =0;
        $missing_qty =0;
        $status = array();
        $qty_array = array();
        
         $role = $this->session->userdata('role_name');

        if(!in_array($idvariant, $_SESSION['qty_barcode'])) {
            
            array_push($_SESSION['qty_barcode'],$idvariant);
          
            $check_stock_data = $this->Audit_model->ajax_get_stockdata_byidvariant($idvariant, $idbranch, $idgodown);
            $idgo = 5;
            $missing_stock = $this->Audit_model->ajax_get_missing_stockdata_byidvariant($idvariant, $idbranch, $idcat, $idbrand, $idgo);

            if($check_stock_data ){
                
                $total_qty = $check_stock_data->qty;
                if($qty <= $total_qty){
                   $match_qty = $qty;
                   $missing_qty = $total_qty - $match_qty;
                }
                if($qty > $total_qty){
                   $match_qty = $total_qty;
                   $unmatch_qty = $qty - $match_qty;
                }
                if($match_qty > 0){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $match_qty;  array_push($qty_array, $match_qty); ?></td>
                        <td>matched
                            <?php //$status = "matched";
                            array_push( $status, 'matched');
                            ?>
                            <input type="hidden"  class="mtch_cnt" value="<?php echo $match_qty; ?>">
                        </td>
                        <td></td>
                    </tr>
                <?php }
                if($unmatch_qty > 0){?>
                   <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $unmatch_qty; array_push($qty_array, $unmatch_qty); ?></td>
                        <td>unmatched
                            <?php //$status = "unmatched";
                            array_push( $status, 'unmatched');
                            ?>
                            <input type="hidden"  class="unmtch_cnt" value="<?php echo $unmatch_qty; ?>">
                        </td>
                        <td></td>
                    </tr>
                <?php }
                if($missing_qty > 0){  ?>
                    <tr>
                        <td></td>
                        <td><?php echo $check_stock_data->product_category_name; ?></td>
                        <td><?php echo $check_stock_data->brand_name ?></td>
                        <td><?php echo $check_stock_data->product_name; ?></td>
                        <td><?php echo $missing_qty; array_push($qty_array, $missing_qty); ?></td>
                        <td>missing
                            <?php //$status = "missing";
                            array_push( $status, 'missing');
                            ?>
                        </td>
                        <td></td>
                    </tr>
                    <?php 
                   
                    if($this->session->userdata('idrole') == 21){
                        if(isset($missing_stock)){
                            //update missing qty stock data
                            $qt = $missing_stock->qty + $missing_qty;
                            $qtystock_update = array(
                                'qty' => $qt,
                                'audit_date' => date('Y-m-d'),
                            );
                            if($this->Audit_model->update_stock_data($qtystock_update, $missing_stock->id_stock)){
                            //update actual stock  data
                                $qtt = $check_stock_data->qty - $missing_qty;
                                $qtystockupdate = array(
                                    'qty' => $qt,
                                );
                                $this->Audit_model->update_stock_data($qtystockupdate, $check_stock_data->id_stock);
                            }

                        }else{
                            //insert new missing qty stock data
                            $qty_stock_save = array(
                                'date' => $check_stock_data->date,
                                'imei_no' => $check_stock_data->imei_no,
                                'idskutype' => $check_stock_data->idskutype,
                                'idgodown' => 5,
                                'temp_idgodown' => $check_stock_data->idgodown,
                                'idproductcategory' => $check_stock_data->idproductcategory,
                                'idcategory' => $check_stock_data->idcategory,
                                'idvariant' => $check_stock_data->idvariant,
                                'idmodel' => $check_stock_data->idmodel,
                                'idbrand' => $check_stock_data->idbrand,
                                'product_name' => $check_stock_data->product_name,
                                'idbranch' => $check_stock_data->idbranch,
                                'qty' => $missing_qty,
                                'transfer_from' => $check_stock_data->transfer_from,
                                'temp_idbranch' => $check_stock_data->temp_idbranch,
                                'entry_time' => $check_stock_data->entry_time,
                                'created_by' => $check_stock_data->created_by,
                                'outward' => $check_stock_data->outward,
                                'outward_dc' => $check_stock_data->outward_dc,
                                'is_gst' => $check_stock_data->is_gst,
                                'outward_time' => $check_stock_data->outward_time,
                                'outward_remark' => $check_stock_data->outward_remark,
                                'outward_by' => $check_stock_data->outward_by,
                                'transfer' => $check_stock_data->transfer,
                                'transfer_dc' => $check_stock_data->transfer_dc,
                                'transfer_time' => $check_stock_data->transfer_time,
                                'transfer_remark' => $check_stock_data->transfer_remark,
                                'transfer_by' => $check_stock_data->transfer_by,
                                'idvendor' => $check_stock_data->idvendor,
                                'sales_return' => $check_stock_data->sales_return,
                                'sales_return_type' => $check_stock_data->sales_return_type,
                                'sale_date' => $check_stock_data->sale_date,
                                'return_date' => $check_stock_data->return_date,
                                'sales_return_by' => $check_stock_data->sales_return_by,
                                'audit_date' => date('Y-m-d'),
                            );
    //                        die('<pre>'.print_r($qty_stock_save,1).'</pre>');
                            $this->Audit_model->save_stock_missing_data($qty_stock_save);
                            //update actual qty stock data
                            $qt = $check_stock_data->qty - $missing_qty;
                            $qtystock_update = array(
                                'qty' => $qt,
                            );
                            $this->Audit_model->update_stock_data($qtystock_update, $check_stock_data->id_stock);
                        }
                    }
                
                }
                for($i=0; $i<count($status); $i++){
                    $data = array(
                        'idstock' => $check_stock_data->id_stock,
                        'imei_no' => $check_stock_data->imei_no,
                        'idskutype' => $check_stock_data->idskutype,
                        'idgodown' => $check_stock_data->idgodown,
                        'idproductcategory' => $check_stock_data->idproductcategory,
                        'idcategory' => $check_stock_data->idcategory,
                        'idvariant' => $check_stock_data->idvariant,
                        'idmodel' => $check_stock_data->idmodel,
                        'idbrand' => $check_stock_data->idbrand,
                        'product_name' => $check_stock_data->product_name,
                        'idbranch' => $check_stock_data->idbranch,
                        'qty' => $qty_array[$i],
                        'date' => date('Y-m-d'),
                        'role' => $role,
                        'created_by' => $_SESSION['id_users'],
                        'status' => $status[$i],
                        'audit_start' => $audit_start,
                    );
                    $this->Audit_model->save_audit_temp_data($data);
                }
             
            }else{ ?>
                <script>
                    alert("Model Not Found");
                </script>
            <?php }
        }else { ?>
            <script>
                alert("Model Already Exists");
            </script>
        <?php }
    }

   public function save_scan_barcodes(){
       
//      die('<pre>'.print_r($_SESSION['scan_barcode'],1).'</pre>');
        $this->db->trans_begin();
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat'); 
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $iduser =  $_SESSION['id_users'];
        
        $_SESSION['scan_barcode'] = array();
        $_SESSION['missing_qty_barcode'] = array();
        $_SESSION['missing_qty'] = array();
        
        $all_variants = $_SESSION['all_variants'];
        $datetime = date('Y-m-d H:i:s');
        $tdate = date('Y-m-d');
        
        $mising_mop = 0;
        $qty_mop =0;
        $total_sale =0;
        
        $audit_done_data = $this->Audit_model->ajax_check_branch_audit($idbranch, $idcat, $idbrand, $idgodown, $iduser, $tdate);
        if(count($audit_done_data) > 0){ ?>
            <script>
                if(confirm("Audit Alredy Submitted")){
                    window.location = 'stock_audit';
                }else{
                     window.location = 'stock_audit';
                }
            </script>
        <?php }else{
        
            $audit_start_datetime  = $this->Audit_model->get_audit_start_date_from_audit_temp($idbranch,$idcat,$idbrand,$iduser, $idgodown);

            $audit_start = $audit_start_datetime->audit_start;
    //         die('<pre>'.print_r($_SESSION['qty_barcode'],1).'</pre>');

            if(count($_SESSION['qty_barcode']) > 0) {

               $not_scann = $this->Audit_model->get_stock_missing_byidvariant($_SESSION['qty_barcode'], $idbranch, $idcat, $idbrand, $idgodown);

               // --------save not sacnned idvariant in audit for missing(idskutype 4 - Qty Barcode)  -------------------- 
                if(count($not_scann) > 0){
                   for($n=0; $n<count($not_scann); $n++){
                        array_push($_SESSION['missing_qty_barcode'], $not_scann[$n]->idvariant);
                        array_push($_SESSION['missing_qty'], $not_scann[$n]->qty);

                        $data_idvariant = array(
                           'idstock' => $not_scann[$n]->id_stock,
                           'finish_date' => date('Y-m-d'),
                           'imei_no' => $not_scann[$n]->imei_no,
                           'idskutype' => $not_scann[$n]->idskutype,
                           'idgodown' => $not_scann[$n]->idgodown,
                           'idproductcategory' => $not_scann[$n]->idproductcategory,
                           'idcategory' => $not_scann[$n]->idcategory,
                           'idvariant' => $not_scann[$n]->idvariant,
                           'idmodel' => $not_scann[$n]->idmodel,
                           'idbrand' => $not_scann[$n]->idbrand,
                           'product_name' => $not_scann[$n]->product_name,
                           'idbranch' => $not_scann[$n]->idbranch,
                           'qty' => $not_scann[$n]->qty,
                           'date' => date('Y-m-d'),
                           'role' => $this->session->userdata('role_name'),
                           'status' => 'missing',
                           'created_by' => $_SESSION['id_users'],
                           'audit_start' => $audit_start,
                           'entry_time' => $datetime,
                       );
                       $this->Audit_model->save_audit_data($data_idvariant);
                        if($this->session->userdata('idrole') == 21){
                            //save into stock for missing godown (new entry in stock)
                             $qty_stock_save = array(
                                 'date' => $not_scann[$n]->date,
                                 'imei_no' => $not_scann[$n]->imei_no,
                                 'idskutype' => $not_scann[$n]->idskutype,
                                 'idgodown' => 5,
                                 'temp_idgodown' => $not_scann[$n]->idgodown,
                                 'idproductcategory' => $not_scann[$n]->idproductcategory,
                                 'idcategory' => $not_scann[$n]->idcategory,
                                 'idvariant' => $not_scann[$n]->idvariant,
                                 'idmodel' => $not_scann[$n]->idmodel,
                                 'idbrand' => $not_scann[$n]->idbrand,
                                 'product_name' => $not_scann[$n]->product_name,
                                 'idbranch' => $not_scann[$n]->idbranch,
                                 'qty' => $not_scann[$n]->qty,
                                 'transfer_from' => $not_scann[$n]->transfer_from,
                                 'temp_idbranch' => $not_scann[$n]->temp_idbranch,
                                 'entry_time' => $not_scann[$n]->entry_time,
                                 'created_by' => $not_scann[$n]->created_by,
                                 'outward' => $not_scann[$n]->outward,
                                 'outward_dc' => $not_scann[$n]->outward_dc,
                                 'is_gst' => $not_scann[$n]->is_gst,
                                 'outward_time' => $not_scann[$n]->outward_time,
                                 'outward_remark' => $not_scann[$n]->outward_remark,
                                 'outward_by' => $not_scann[$n]->outward_by,
                                 'transfer' => $not_scann[$n]->transfer,
                                 'transfer_dc' => $not_scann[$n]->transfer_dc,
                                 'transfer_time' => $not_scann[$n]->transfer_time,
                                 'transfer_remark' => $not_scann[$n]->transfer_remark,
                                 'transfer_by' => $not_scann[$n]->transfer_by,
                                 'idvendor' => $not_scann[$n]->idvendor,
                                 'sales_return' => $not_scann[$n]->sales_return,
                                 'sales_return_type' => $not_scann[$n]->sales_return_type,
                                 'sale_date' => $not_scann[$n]->sale_date,
                                 'return_date' => $not_scann[$n]->return_date,
                                 'sales_return_by' => $not_scann[$n]->sales_return_by,
                                 'audit_date' => date('Y-m-d'),
                             );
                             $this->Audit_model->save_stock_missing_data($qty_stock_save);
                             //update actual qty stock data 
                             $qtystock_update = array(
                                 'qty' => 0,
                             );
                             $this->Audit_model->update_stock_data($qtystock_update, $not_scann[$n]->id_stock);
                        }
                   }
               }
            }

            // ------Audit Temp Data -----------
            $audit_temp_data = $this->Audit_model->get_audit_temp_data_byid($idbranch, $idcat, $idbrand, $iduser, $idgodown);

            if(count($audit_temp_data) > 0){

                $role = $audit_temp_data[0]->role;
                for($i=0; $i<count($audit_temp_data); $i++){

                    if($audit_temp_data[$i]->idskutype != 4){
                        array_push($_SESSION['scan_barcode'], $audit_temp_data[$i]->imei_no);
                    }
                    if($audit_temp_data[$i]->idskutype == 4 && $audit_temp_data[$i]->status == 'missing'){
                        array_push($_SESSION['missing_qty_barcode'], $audit_temp_data[$i]->idvariant);
                        array_push($_SESSION['missing_qty'], $audit_temp_data[$i]->qty);
                    }
                    //------------- Save Audit_temp data into Audit ----------
                    $data = array(
                        'idstock' => $audit_temp_data[$i]->idstock,
                        'finish_date' => date('Y-m-d'),
                        'imei_no' => $audit_temp_data[$i]->imei_no,
                        'idskutype' => $audit_temp_data[$i]->idskutype,
                        'idgodown' => $audit_temp_data[$i]->idgodown,
                        'idproductcategory' => $audit_temp_data[$i]->idproductcategory,
                        'idcategory' => $audit_temp_data[$i]->idcategory,
                        'idvariant' => $audit_temp_data[$i]->idvariant,
                        'idmodel' => $audit_temp_data[$i]->idmodel,
                        'idbrand' => $audit_temp_data[$i]->idbrand,
                        'product_name' => $audit_temp_data[$i]->product_name,
                        'idbranch' => $audit_temp_data[$i]->idbranch,
                        'qty' => $audit_temp_data[$i]->qty,
                        'date' => $audit_temp_data[$i]->date,
                        'role' => $audit_temp_data[$i]->role,
                        'created_by' => $_SESSION['id_users'],
                        'status' => $audit_temp_data[$i]->status,
                        'remark' => $audit_temp_data[$i]->remark,
                        'audit_start' => $audit_temp_data[$i]->audit_start,
                        'entry_time' => $datetime,
                    );
                    $this->Audit_model->save_audit_data($data);
                }
                if(count($_SESSION['scan_barcode']) > 0){
                    // -------- Missing Imei_no Data From stock --------
                    $get_missing_stock = $this->Audit_model->get_stock_missing_byid($_SESSION['scan_barcode'], $idbranch, $idcat, $idbrand, $idgodown);
                    if(count($get_missing_stock) > 0){
    //                    die('<pre>'.print_r($get_missing_stock,1).'</pre>');
                        for($j=0; $j < count($get_missing_stock); $j++){
                            $missing = array(
                                'idstock' => $get_missing_stock[$j]->id_stock,
                                'finish_date' => date('Y-m-d'),
                                'imei_no' => $get_missing_stock[$j]->imei_no,
                                'idskutype' => $get_missing_stock[$j]->idskutype,
                                'idgodown' => $get_missing_stock[$j]->idgodown,
                                'idproductcategory' => $get_missing_stock[$j]->idproductcategory,
                                'idcategory' => $get_missing_stock[$j]->idcategory,
                                'idvariant' => $get_missing_stock[$j]->idvariant,
                                'idmodel' => $get_missing_stock[$j]->idmodel,
                                'idbrand' => $get_missing_stock[$j]->idbrand,
                                'product_name' => $get_missing_stock[$j]->product_name,
                                'idbranch' => $get_missing_stock[$j]->idbranch,
                                'qty' => $get_missing_stock[$j]->qty,
                                'date' => date('Y-m-d'),
                                'role' => $role,
                                'created_by' => $_SESSION['id_users'],
                                'status' => 'missing',
                                'audit_start' => $audit_start,
                                'entry_time' => $datetime,
                            );
                            if($idaudit = $this->Audit_model->save_audit_data($missing)){
                                if($this->session->userdata('idrole') == 21){
                                    //update missing imei godown as missing godown
                                    $stock_update = array(
                                        'temp_idgodown' => $get_missing_stock[$j]->idgodown,
                                        'idgodown' => 5,
                                        'audit_date' => date('Y-m-d'),
                                    );
                                    $this->Audit_model->update_stock_data($stock_update, $get_missing_stock[$j]->id_stock);
                                }

                                $imei_history[]=array(
                                    'imei_no' => $get_missing_stock[$j]->imei_no,
                                    'entry_type' => 'Missing',
                                    'entry_time' => $datetime,
                                    'date' => date('Y-m-d'),
                                    'idbranch' => $get_missing_stock[$j]->idbranch,
                                    'idgodown' => $get_missing_stock[$j]->idgodown,
                                    'idvariant' => $get_missing_stock[$j]->idvariant,
//                                    'model_variant_full_name' => $get_missing_stock[$j]->product_name,
                                    'idimei_details_link' => 8, // Sale from imei_details_link table
                                    'idlink' => $idaudit,
                                    'iduser' => $_SESSION['id_users']
                                );
                            }
                            $mising_mop = $mising_mop + $get_missing_stock[$j]->mop; //missing imei total amount
                        }
                        if(count($imei_history) > 0){
                            if($this->session->userdata('idrole') == 21){
                                $this->General_model->save_batch_imei_history($imei_history);
                            }
                        }
                    }
                }
                //--------Missing Qty barcodes --------------

           /*     if(count($_SESSION['missing_qty_barcode']) > 0){
                    $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($_SESSION['missing_qty_barcode'], $idgodown, $idbranch);
                    $miss_qty = $_SESSION['missing_qty'];

                     if(count($get_missing_qty_stock) > 0){
                        for($q=0; $q < count($get_missing_qty_stock); $q++){
                            $qty_mop = $qty_mop +($get_missing_qty_stock[$q]->mop * $miss_qty[$q]); // Missing Qty total amount
                        }
                    }
                }

                $customer_data = $this->Audit_model->get_missing_customer_data();
    */
                //--------Auditor Role -----------------
    //           if($this->session->userdata('idrole') == 21){
    //                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
    //                $invid = $invoice_no->invoice_no + 1;
    //                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
    //                $y1 = $y - 1;
    //                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
    //                
    //                //-------Total Missing Amount --------------
    //                $total_sale = $mising_mop + $qty_mop; 
    //               
    //                if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
    //                    //------- Missing Sale Invoice----------
    //                    $sale_missing = array(
    //                        'date' => date('Y-m-d'),
    //                        'inv_no' => $inv_no,
    //                        'customer_fname' => $customer_data->customer_fname,
    //                        'customer_contact' => $customer_data->customer_contact,
    //                        'idcustomer' => $customer_data->id_customer,
    //                        'customer_gst' => $customer_data->customer_gst,
    //                        'customer_address' => $customer_data->customer_address,
    //                        'customer_pincode' => $customer_data->customer_pincode,
    //                        'customer_idstate' => $customer_data->idstate,
    //                        'idsalesperson' => $iduser,
    //                        'basic_total' => $total_sale,
    //                        'discount_total' => 0,
    //                        'final_total' => $total_sale,
    //                        'idbranch' => $idbranch,
    //                        'corporate_sale' => 0,
    //                        'gst_type' => 0,
    //                        'remark' => "Branch Missing",
    //                        'created_by' => $iduser,
    //                    );
    //                  
    //                    if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){
    //                        
    //                        $sale_payment = array(
    //                            'inv_no' => $inv_no,
    //                            'idcustomer' => $customer_data->id_customer,
    //                            'idsale' => $idsale,
    //                            'date' => date('Y-m-d'),
    //                            'idbranch' => $idbranch,
    //                            'corporate_sale' => 0,
    //                            'idpayment_head ' => 6,
    //                            'idpayment_mode ' => 18,
    //                            'amount' => $total_sale,
    //                            'transaction_id' => 'missing',
    //                            'created_by' => $iduser,
    //                            'payment_receive' => 0,
    //                        );
    //                        $this->Audit_model->save_sale_payment_data($sale_payment);
    //                        
    //                        if(count($get_missing_stock) > 0){
    //                            //--------sale product imei missing -------------
    //                            for($im = 0; $im < count($get_missing_stock); $im++){
    //                                $missing_imei_data = array(
    //                                    'idbranch' => $get_missing_stock[$im]->idbranch,
    //                                    'idskutype' => $get_missing_stock[$im]->idskutype,
    //                                    'idgodown' => $get_missing_stock[$im]->idgodown,
    //                                    'date' => date('Y-m-d'),
    //                                    'inv_no' => $inv_no,
    //                                    'product_name' => $get_missing_stock[$im]->product_name,
    //                                    'imei_no' => $get_missing_stock[$im]->imei_no,
    //                                    'hsn' => $get_missing_stock[$im]->hsn,
    //                                    'is_gst' => $get_missing_stock[$im]->is_gst,
    //                                    'is_mop' => $get_missing_stock[$im]->is_mop,
    //                                    'idsale' => $idsale,
    //                                    'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
    //                                    'idcategory' => $get_missing_stock[$im]->idcategory,
    //                                    'idvariant' => $get_missing_stock[$im]->idvariant,
    //                                    'idmodel' => $get_missing_stock[$im]->idmodel,
    //                                    'idbrand' => $get_missing_stock[$im]->idbrand,
    //                                    'qty' => $get_missing_stock[$im]->qty,
    //                                    'price' => $get_missing_stock[$im]->mop,
    //                                    'mop' => $get_missing_stock[$im]->mop,
    //                                    'landing' => $get_missing_stock[$im]->landing,
    //                                    'mrp' => $get_missing_stock[$im]->mrp,
    //                                    'salesman_price' => $get_missing_stock[$im]->salesman_price,
    //                                    'discount_amt' => 0,
    //                                    'basic' => $get_missing_stock[$im]->mop,
    //                                    'cgst_per' => $get_missing_stock[$im]->cgst,
    //                                    'sgst_per' => $get_missing_stock[$im]->sgst,
    //                                    'igst_per' => 0,
    //                                    'total_amount' => $get_missing_stock[$im]->mop,
    //                                    'idvendor' => $get_missing_stock[$im]->idvendor
    //                                );
    //                                $this->Audit_model->save_sale_product_data($missing_imei_data);
    //                            }
    //                        }
    //                        if(count($get_missing_qty_stock) > 0){
    //                        
    //                            for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
    //                                //--------sale product Qty missing -------------
    //                                
    //                                $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $miss_qty[$qt];
    //                                
    //                                $missing_qty_data = array(
    //                                    'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
    //                                    'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
    //                                    'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
    //                                    'date' => date('Y-m-d'),
    //                                    'inv_no' => $inv_no,
    //                                    'product_name' => $get_missing_qty_stock[$qt]->product_name,
    //                                    'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
    //                                    'hsn' => $get_missing_qty_stock[$qt]->hsn,
    //                                    'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
    //                                    'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
    //                                    'idsale' => $idsale,
    //                                    'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
    //                                    'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
    //                                    'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
    //                                    'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
    //                                    'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
    //                                    'qty' => $miss_qty[$qt],
    //                                    'price' => $get_missing_qty_stock[$qt]->mop,
    //                                    'mop' => $get_missing_qty_stock[$qt]->mop,
    //                                    'landing' => $get_missing_qty_stock[$qt]->landing,
    //                                    'mrp' => $get_missing_qty_stock[$qt]->mrp,
    //                                    'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
    //                                    'discount_amt' => 0,
    //                                    'basic' => $miss_qyt_basic,
    //                                    'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
    //                                    'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
    //                                    'igst_per' => 0,
    //                                    'total_amount' => $miss_qyt_basic,
    //                                    'idvendor' => $get_missing_qty_stock[$qt]->idvendor
    //                                );
    //                                $this->Audit_model->save_sale_product_data($missing_qty_data);
    //                            }
    //                        }
    //                        $invoice_data = array( 'invoice_no' => $invid );
    //                        $this->General_model->edit_db_branch($idbranch, $invoice_data);
    //                    }
    //                }
    //            }  
            }else{
                if(!$_SESSION['scan_barcode']){
                 $get_missing_stock = $this->Audit_model->get_stock_missing($idbranch, $idcat, $idbrand, $idgodown);

                    if(count($get_missing_stock) > 0){

                        for($j=0; $j < count($get_missing_stock); $j++){
                            $missing = array(
                                'idstock' => $get_missing_stock[$j]->id_stock,
                                'finish_date' => date('Y-m-d'),
                                'imei_no' => $get_missing_stock[$j]->imei_no,
                                'idskutype' => $get_missing_stock[$j]->idskutype,
                                'idgodown' => $get_missing_stock[$j]->idgodown,
                                'idproductcategory' => $get_missing_stock[$j]->idproductcategory,
                                'idcategory' => $get_missing_stock[$j]->idcategory,
                                'idvariant' => $get_missing_stock[$j]->idvariant,
                                'idmodel' => $get_missing_stock[$j]->idmodel,
                                'idbrand' => $get_missing_stock[$j]->idbrand,
                                'product_name' => $get_missing_stock[$j]->product_name,
                                'idbranch' => $get_missing_stock[$j]->idbranch,
                                'qty' => $get_missing_stock[$j]->qty,
                                'date' => date('Y-m-d'),
                                'role' => $this->session->userdata('role_name'),
                                'created_by' => $_SESSION['id_users'],
                                'status' => 'missing',
                                'audit_start' => $datetime,
                                'entry_time' => $datetime,
                            );
                            if($idaudit = $this->Audit_model->save_audit_data($missing)){
                                if($this->session->userdata('idrole') == 21){
                                    //update missing imei godown as missing godown
                                    $stock_update = array(
                                        'temp_idgodown' => $get_missing_stock[$j]->idgodown,
                                        'idgodown' => 5,
                                        'audit_date' => date('Y-m-d'),
                                    );
                                    $this->Audit_model->update_stock_data($stock_update, $get_missing_stock[$j]->id_stock);
                                }
                                $imei_history[]=array(
                                    'imei_no' => $get_missing_stock[$j]->imei_no,
                                    'entry_type' => 'Missing',
                                    'entry_time' => $datetime,
                                    'date' => date('Y-m-d'),
                                    'idbranch' => $get_missing_stock[$j]->idbranch,
                                    'idgodown' => $get_missing_stock[$j]->idgodown,
                                    'idvariant' => $get_missing_stock[$j]->idvariant,
//                                    'model_variant_full_name' => $get_missing_stock[$j]->product_name,
                                    'idimei_details_link' => 8, // Sale from imei_details_link table
                                    'idlink' => $idaudit,
                                    'iduser' => $_SESSION['id_users']
                                );
                            }
                            $mising_mop = $mising_mop + $get_missing_stock[$j]->mop; //missing imei total amount
                        }
                        if(count($imei_history) > 0){
                            if($this->session->userdata('idrole') == 21){
                                $this->General_model->save_batch_imei_history($imei_history);
                            }
                        }
                    }
                }
        /*        if(count($_SESSION['missing_qty_barcode']) > 0){
                    $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($_SESSION['missing_qty_barcode'], $idgodown, $idbranch);
                    $miss_qty = $_SESSION['missing_qty'];

                     if(count($get_missing_qty_stock) > 0){
                        for($q=0; $q < count($get_missing_qty_stock); $q++){
                            $qty_mop = $qty_mop +($get_missing_qty_stock[$q]->mop * $miss_qty[$q]); // Missing Qty total amount
                        }
                    }
                }
                $customer_data = $this->Audit_model->get_missing_customer_data();

         */

    //             if($this->session->userdata('idrole') == 21){
    //                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
    //                $invid = $invoice_no->invoice_no + 1;
    //                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
    //                $y1 = $y - 1;
    //                $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
    //                
    //                //-------Total Missing Amount --------------
    //                $total_sale = $mising_mop + $qty_mop; 
    //               
    //                if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
    //                    //------- Missing Sale Invoice----------
    //                    $sale_missing = array(
    //                        'date' => date('Y-m-d'),
    //                        'inv_no' => $inv_no,
    //                        'customer_fname' => $customer_data->customer_fname,
    //                        'customer_contact' => $customer_data->customer_contact,
    //                        'idcustomer' => $customer_data->id_customer,
    //                        'customer_gst' => $customer_data->customer_gst,
    //                        'customer_address' => $customer_data->customer_address,
    //                        'customer_pincode' => $customer_data->customer_pincode,
    //                        'customer_idstate' => $customer_data->idstate,
    //                        'idsalesperson' => $iduser,
    //                        'basic_total' => $total_sale,
    //                        'discount_total' => 0,
    //                        'final_total' => $total_sale,
    //                        'idbranch' => $idbranch,
    //                        'corporate_sale' => 0,
    //                        'gst_type' => 0,
    //                        'remark' => "Branch Missing",
    //                        'created_by' => $iduser,
    //                    );
    //                  
    //                    if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){
    //                        
    //                        $sale_payment = array(
    //                            'inv_no' => $inv_no,
    //                            'idcustomer' => $customer_data->id_customer,
    //                            'idsale' => $idsale,
    //                            'date' => date('Y-m-d'),
    //                            'idbranch' => $idbranch,
    //                            'corporate_sale' => 0,
    //                            'idpayment_head ' => 6,
    //                            'idpayment_mode ' => 18,
    //                            'amount' => $total_sale,
    //                            'transaction_id' => 'missing',
    //                            'created_by' => $iduser,
    //                            'payment_receive' => 0,
    //                        );
    //                        $this->Audit_model->save_sale_payment_data($sale_payment);
    //                        
    //                        if(count($get_missing_stock) > 0){
    //                            //--------sale product imei missing -------------
    //                            for($im = 0; $im < count($get_missing_stock); $im++){
    //                                $missing_imei_data = array(
    //                                    'idbranch' => $get_missing_stock[$im]->idbranch,
    //                                    'idskutype' => $get_missing_stock[$im]->idskutype,
    //                                    'idgodown' => $get_missing_stock[$im]->idgodown,
    //                                    'date' => date('Y-m-d'),
    //                                    'inv_no' => $inv_no,
    //                                    'product_name' => $get_missing_stock[$im]->product_name,
    //                                    'imei_no' => $get_missing_stock[$im]->imei_no,
    //                                    'hsn' => $get_missing_stock[$im]->hsn,
    //                                    'is_gst' => $get_missing_stock[$im]->is_gst,
    //                                    'is_mop' => $get_missing_stock[$im]->is_mop,
    //                                    'idsale' => $idsale,
    //                                    'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
    //                                    'idcategory' => $get_missing_stock[$im]->idcategory,
    //                                    'idvariant' => $get_missing_stock[$im]->idvariant,
    //                                    'idmodel' => $get_missing_stock[$im]->idmodel,
    //                                    'idbrand' => $get_missing_stock[$im]->idbrand,
    //                                    'qty' => $get_missing_stock[$im]->qty,
    //                                    'price' => $get_missing_stock[$im]->mop,
    //                                    'mop' => $get_missing_stock[$im]->mop,
    //                                    'landing' => $get_missing_stock[$im]->landing,
    //                                    'mrp' => $get_missing_stock[$im]->mrp,
    //                                    'salesman_price' => $get_missing_stock[$im]->salesman_price,
    //                                    'discount_amt' => 0,
    //                                    'basic' => $get_missing_stock[$im]->mop,
    //                                    'cgst_per' => $get_missing_stock[$im]->cgst,
    //                                    'sgst_per' => $get_missing_stock[$im]->sgst,
    //                                    'igst_per' => 0,
    //                                    'total_amount' => $get_missing_stock[$im]->mop,
    //                                    'idvendor' => $get_missing_stock[$im]->idvendor
    //                                );
    //                                $this->Audit_model->save_sale_product_data($missing_imei_data);
    //                            }
    //                        }
    //                        if(count($get_missing_qty_stock) > 0){
    //                        
    //                            for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
    //                                //--------sale product Qty missing -------------
    //                                
    //                                $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $miss_qty[$qt];
    //                                
    //                                $missing_qty_data = array(
    //                                    'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
    //                                    'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
    //                                    'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
    //                                    'date' => date('Y-m-d'),
    //                                    'inv_no' => $inv_no,
    //                                    'product_name' => $get_missing_qty_stock[$qt]->product_name,
    //                                    'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
    //                                    'hsn' => $get_missing_qty_stock[$qt]->hsn,
    //                                    'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
    //                                    'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
    //                                    'idsale' => $idsale,
    //                                    'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
    //                                    'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
    //                                    'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
    //                                    'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
    //                                    'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
    //                                    'qty' => $miss_qty[$qt],
    //                                    'price' => $get_missing_qty_stock[$qt]->mop,
    //                                    'mop' => $get_missing_qty_stock[$qt]->mop,
    //                                    'landing' => $get_missing_qty_stock[$qt]->landing,
    //                                    'mrp' => $get_missing_qty_stock[$qt]->mrp,
    //                                    'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
    //                                    'discount_amt' => 0,
    //                                    'basic' => $miss_qyt_basic,
    //                                    'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
    //                                    'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
    //                                    'igst_per' => 0,
    //                                    'total_amount' => $miss_qyt_basic,
    //                                    'idvendor' => $get_missing_qty_stock[$qt]->idvendor
    //                                );
    //                                $this->Audit_model->save_sale_product_data($missing_qty_data);
    //                            }
    //                        }
    //                        $invoice_data = array( 'invoice_no' => $invid );
    //                        $this->General_model->edit_db_branch($idbranch, $invoice_data);
    //                    }
    //                }
    //            }   

            }
            $this->Audit_model->delete_audit_temp_data($idbranch, $idcat, $idbrand, $iduser, $idgodown);

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->session->set_flashdata('save_data', 'Audit is aborted. Try again with same details');
            }else{
                $this->db->trans_commit();
                $this->session->set_flashdata('save_data', 'Audit Done Successfully');
            }

            if($this->session->userdata('idrole') == 21){
//                $idgodw = 5;
//                redirect('Audit/get_missing_audit_for_sale/'.$idbranch.'/'.$idcat.'/'.$idbrand.'/'.$idgodw.'/'.$tdate);
                 $this->session->set_flashdata('save_data', 'Audit Done Successfully');
                redirect('Audit/stock_audit');
            }else{
                 $this->session->set_flashdata('save_data', 'Audit Done Successfully');
                redirect('Audit/stock_audit');
            }
        }
    }
    
    public function get_missing_audit_for_sale($idbranch, $idcat, $idbrand, $idgodown, $datetime){
        $q['tab_active'] = 'Audit';
        $q['missing_data'] = $this->Audit_model->get_audit_missing_data_from_stock($idbranch, $idcat, $idbrand, $idgodown, $datetime);
//        die(print_r($q['missing_data']));
        $this->load->view('audit/audit_missing_sale_data',$q);
    }
    
    public function get_missing_stock_for_sale(){
        $q['tab_active'] = 'Audit';
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $q['missing_data'] = $this->Audit_model->get_audit_missing_data_from_stock_byfilter($idbranch, $idcat, $idbrand, $idgodown, $from, $to);
//        die('<pre>'.print_r( $q['missing_data'],1).'</pre>');
        $this->load->view('audit/audit_missing_sale_data',$q);
    }
    
     public function save_missing_audit_credit(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $mising_mop = 0;
        $qty_mop =0;
        $total_sale =0;
        $missing_imei_arr = array();
        $missing_qty_arr = array();
        $missing_idvariant_arr = array();
        
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idproductcategory');
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $iduser =  $_SESSION['id_users'];
        
        if(isset($_POST['checkimei'])){
            $checkimei = $_POST['checkimei'];      
            
            if(isset($_POST['missimei'])){
                $missimei = $_POST['missimei'];      
                foreach ($checkimei as $check => $val){
                   $missing_imei_arr[] = $missimei[$val];
                }
                
            }
            if(isset($_POST['missqty'])){
                $missqty = $_POST['missqty']; 
                $miss_variant = $_POST['idvariant'];
                foreach ($checkimei as $check => $val){
                   $missing_qty_arr[] = $missqty[$val];
                   $missing_idvariant_arr[] = $miss_variant[$val];
                }
            }
        }
        if(count($missing_imei_arr) > 0){
            $get_missing_stock = $this->Audit_model->get_missing_stock_details_byimei($missing_imei_arr);
//            die('<pre>'.print_r($get_missing_stock,1).'</pre>');
            if(count($get_missing_stock) > 0){
                for($i=0; $i<count($get_missing_stock); $i++){
                    $mising_mop += $get_missing_stock[$i]->mop;
                   
                }
            }  
        }
        if(count($missing_qty_arr) > 0){
            $get_missing_qty_stock = $this->Audit_model->ajax_get_stock_data_byidvariant($missing_idvariant_arr, $idgodown, $idbranch);
            if(count($get_missing_qty_stock) > 0){
                for($j=0; $j<count($get_missing_qty_stock); $j++){
                    $qty_mop += $get_missing_qty_stock[$j]->mop * $missing_qty_arr[$j];
                   
                }
            }  
        }
        
        if($this->session->userdata('idrole') == 21){
            $customer_data = $this->Audit_model->get_missing_customer_data();
            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
            $invid = $invoice_no->invoice_no + 1;
            $y = date('y', mktime(0, 0, 0, 9 + date('m')));
            $y1 = $y - 1;
            $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);

            //-------Total Missing Amount --------------
            $total_sale = $mising_mop + $qty_mop; 
            if(count($get_missing_stock) > 0 || count($get_missing_qty_stock) > 0){
           
                //------- Missing Sale Invoice----------
                $sale_missing = array(
                    'date' => date('Y-m-d'),
                    'inv_no' => $inv_no,
                    'customer_fname' => $customer_data->customer_fname,
                    'customer_contact' => $customer_data->customer_contact,
                    'idcustomer' => $customer_data->id_customer,
                    'customer_gst' => $customer_data->customer_gst,
                    'customer_address' => $customer_data->customer_address,
                    'customer_pincode' => $customer_data->customer_pincode,
                    'customer_idstate' => $customer_data->idstate,
                    'idsalesperson' => $iduser,
                    'basic_total' => $total_sale,
                    'discount_total' => 0,
                    'final_total' => $total_sale,
                    'idbranch' => $idbranch,
                    'corporate_sale' => 0,
                    'gst_type' => 0,
                    'remark' => "Branch Missing",
                    'created_by' => $iduser,
                );

                if($idsale = $this->Audit_model->save_missing_sale_data($sale_missing)){

                    $sale_payment = array(
                        'inv_no' => $inv_no,
                        'idcustomer' => $customer_data->id_customer,
                        'idsale' => $idsale,
                        'date' => date('Y-m-d'),
                        'idbranch' => $idbranch,
                        'corporate_sale' => 0,
                        'idpayment_head ' => 6,
                        'idpayment_mode ' => 18,
                        'amount' => $total_sale,
                        'transaction_id' => 'missing',
                        'created_by' => $iduser,
                        'payment_receive' => 0,
                    );
                  
                    $this->Audit_model->save_sale_payment_data($sale_payment);

                    if(count($get_missing_stock) > 0){ 
//                        die('<pre>'.print_r($get_missing_stock,1).'</pre>');
                        //--------sale product imei missing -------------
                        for($im = 0; $im < count($get_missing_stock); $im++){
                            $missing_imei_data = array(
                                'idbranch' => $get_missing_stock[$im]->idbranch,
                                'idskutype' => $get_missing_stock[$im]->idskutype,
                                'idgodown' => $get_missing_stock[$im]->idgodown,
                                'date' => date('Y-m-d'),
                                'inv_no' => $inv_no,
                                'product_name' => $get_missing_stock[$im]->product_name,
                                'imei_no' => $get_missing_stock[$im]->imei_no,
                                'hsn' => $get_missing_stock[$im]->hsn,
                                'is_gst' => $get_missing_stock[$im]->is_gst,
                                'is_mop' => $get_missing_stock[$im]->is_mop,
                                'idsale' => $idsale,
                                'idproductcategory' => $get_missing_stock[$im]->idproductcategory,
                                'idcategory' => $get_missing_stock[$im]->idcategory,
                                'idvariant' => $get_missing_stock[$im]->idvariant,
                                'idmodel' => $get_missing_stock[$im]->idmodel,
                                'idbrand' => $get_missing_stock[$im]->idbrand,
                                'qty' => $get_missing_stock[$im]->qty,
                                'price' => $get_missing_stock[$im]->mop,
                                'mop' => $get_missing_stock[$im]->mop,
                                'landing' => $get_missing_stock[$im]->landing,
                                'mrp' => $get_missing_stock[$im]->mrp,
                                'salesman_price' => $get_missing_stock[$im]->salesman_price,
                                'discount_amt' => 0,
                                'basic' => $get_missing_stock[$im]->mop,
                                'cgst_per' => $get_missing_stock[$im]->cgst,
                                'sgst_per' => $get_missing_stock[$im]->sgst,
                                'igst_per' => 0,
                                'total_amount' => $get_missing_stock[$im]->mop,
                                'idvendor' => $get_missing_stock[$im]->idvendor
                            );
                            $this->Audit_model->save_sale_product_data($missing_imei_data);
                            $this->Audit_model->delete_imei_from_stock($get_missing_stock[$im]->id_stock);
                            
                            $imei_history[]=array(
                                'imei_no' => $get_missing_stock[$im]->imei_no,
                                'entry_type' => 'Sale after missing',
                                'entry_time' => date('Y-m-d H:i:s'),
                                'date' => date('Y-m-d'),
                                'idbranch' => $get_missing_stock[$im]->idbranch,
                                'idgodown' => $get_missing_stock[$im]->idgodown,
                                'idvariant' => $get_missing_stock[$im]->idvariant,
                                'idimei_details_link' => 4, // Sale from imei_details_link table
                                'idlink' => $idsale,
                                'iduser' => $_SESSION['id_users'],
                            );
                        }
                        if(count($imei_history) > 0){
                            $this->General_model->save_batch_imei_history($imei_history);
                        }
                    }
                    if(count($get_missing_qty_stock) > 0){

                        for($qt = 0; $qt < count($get_missing_qty_stock); $qt++){
                            //--------sale product Qty missing -------------

                            $miss_qyt_basic = $get_missing_qty_stock[$qt]->mop * $missing_qty_arr[$qt];

                            $missing_qty_data = array(
                                'idbranch' => $get_missing_qty_stock[$qt]->idbranch,
                                'idskutype' => $get_missing_qty_stock[$qt]->idskutype,
                                'idgodown' => $get_missing_qty_stock[$qt]->idgodown,
                                'date' => date('Y-m-d'),
                                'inv_no' => $inv_no,
                                'product_name' => $get_missing_qty_stock[$qt]->product_name,
                                'imei_no' => $get_missing_qty_stock[$qt]->imei_no,
                                'hsn' => $get_missing_qty_stock[$qt]->hsn,
                                'is_gst' => $get_missing_qty_stock[$qt]->is_gst,
                                'is_mop' => $get_missing_qty_stock[$qt]->is_mop,
                                'idsale' => $idsale,
                                'idproductcategory' => $get_missing_qty_stock[$qt]->idproductcategory,
                                'idcategory' => $get_missing_qty_stock[$qt]->idcategory,
                                'idvariant' => $get_missing_qty_stock[$qt]->idvariant,
                                'idmodel' => $get_missing_qty_stock[$qt]->idmodel,
                                'idbrand' => $get_missing_qty_stock[$qt]->idbrand,
                                'qty' => $missing_qty_arr[$qt],
                                'price' => $get_missing_qty_stock[$qt]->mop,
                                'mop' => $get_missing_qty_stock[$qt]->mop,
                                'landing' => $get_missing_qty_stock[$qt]->landing,
                                'mrp' => $get_missing_qty_stock[$qt]->mrp,
                                'salesman_price' => $get_missing_qty_stock[$qt]->salesman_price,
                                'discount_amt' => 0,
                                'basic' => $miss_qyt_basic,
                                'cgst_per' => $get_missing_qty_stock[$qt]->cgst,
                                'sgst_per' => $get_missing_qty_stock[$qt]->sgst,
                                'igst_per' => 0,
                                'total_amount' => $miss_qyt_basic,
                                'idvendor' => $get_missing_qty_stock[$qt]->idvendor
                            );
                            $this->Audit_model->save_sale_product_data($missing_qty_data);
                            $this->Audit_model->delete_imei_from_stock($get_missing_qty_stock[$qt]->id_stock);
                        }
                    }
                    $invoice_data = array( 'invoice_no' => $invid );
                    $this->General_model->edit_db_branch($idbranch, $invoice_data);
                }
            }
        }         
        $this->session->set_flashdata('save_data', 'Audit Done Successfully');
        redirect('Audit/stock_audit');
    }
    public function audit_missing_stock_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Auditor';
        $q['page_name'] = 'Stock Missing Report';
        if($this->session->userdata('level') == 1){  //admin all branch
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('audit/audit_missing_stock_report',$q);
    }
    public function audit_missing_inward(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Auditor';
        $q['page_name'] = 'Missing Inward';
        if($this->session->userdata('level') == 1){  //admin all branch
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('audit/audit_missing_inward',$q);
    }
    public function ajax_get_missing_stock_data(){
        $idbranch = $this->input->post('idbranch');
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idgodown = $this->input->post('idgodown');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $missing_data = $this->Audit_model->get_audit_missing_data_from_stock_byfilter($idbranch, $idcat, $idbrand, $idgodown, $from, $to);
//        die(print_r($missing_data));
        
        if(count($missing_data) > 0){ ?>
            <table class="table table-bordered table-condensed">
                <thead style="background-color: #9ed5f0" class="fixtop">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <th>Godown</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>Imei</th>
                    <!--<th>Status</th>-->
                    <!--<th>Missing Qty</th>-->
                    <th>Inward Qty</th>
                    <!--<th>Price</th>-->
                    <th>Inward</th>
                </thead>
                <tbody>
                    <?php $sr=1; foreach ($missing_data as $miss){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $miss->audit_date; ?></td>
                        <td><?php echo $miss->branch_name; ?></td>
                        <td><?php echo $miss->product_category_name; ?></td>
                        <td><?php echo $miss->godown_name; ?></td>
                        <td><?php echo $miss->brand_name; ?></td>
                        <td><?php echo $miss->full_name; ?></td>
                        <td><?php echo $miss->imei_no ?></td>
                        <!--<td><?php echo 'Missing'; ?></td>-->
                        <!--<td><?php echo $miss->qty; ?></td>-->
                        <td>
                            <input type="hidden" id="mqty" class="mqty" value="<?php echo $miss->qty; ?>">
                            <?php if($miss->idskutype != 4){ 
                                echo $miss->qty;
                                ?>
                            <input type="hidden" name="qty" class="form-control qty"  id="qty" min="1"  value="<?php echo $miss->qty; ?>">
                          <?php  }else{ ?>
                            <input type="number" name="qty" class="form-control qty"  id="qty" min="1"  value="<?php echo $miss->qty; ?>">
                            <?php } ?>
                        </td>
                        <!--<td><?php // echo $miss->mop ?></td>-->
                        <td>
                            <input type="hidden" name="idbranch" class="idbranch" id="idbranch<?php echo $miss->id_stock?>" value="<?php echo $miss->idbranch?>">
                            <input type="hidden" name="idbrand" class="idbrand" id="idbrand<?php echo $miss->id_stock?>" value="<?php echo $miss->idbrand?>">
                            <input type="hidden" name="idproductcategory" class="idproductcategory" id="idproductcategory<?php echo $miss->id_stock?>" value="<?php echo $miss->idproductcategory?>">
                            <input type="hidden" name="idvariant" class="idvariant" id="idvariant<?php echo $miss->id_stock?>" value="<?php echo $miss->idvariant?>">
                            <input type="hidden" name="idgodown" class="idgodown" id="idgodown<?php echo $miss->id_stock?>" value="<?php echo $miss->idgodown?>"> <!-- Missing idGodown -->
                            <input type="hidden" name="temp_idgodown" class="temp_idgodown" id="temp_idgodown<?php echo $miss->id_stock?>" value="<?php echo $miss->temp_idgodown?>"> <!-- Actual Godown -->
                            <input type="hidden" name="idskutype" class="idskutype" id="idskutype<?php echo $miss->id_stock?>" value="<?php echo $miss->idskutype?>"> 
                            <input type="hidden" name="idstock" class="idstock" id="idstock<?php echo $miss->id_stock?>" value="<?php echo $miss->id_stock?>"> 
                            <button class="btn btn-primary btn-sm btn_inward" id="btn_inward<?php echo $miss->id_stock?>">Inward</button>
                            
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <script>
                $(document).ready(function (){
                    $('.qty').change(function (){
                        var parent_td = $(this).closest('td');
                        var mqty = +parent_td.find($('.mqty')).val();
                        var qty = +parent_td.find($('.qty')).val()
                        if(qty > mqty){
                            $(this).val(mqty);
                            alert("Qty Can't be greater than " + mqty);
                            return false;
                        }
                    });
                });
            </script>
            <script>
                $(document).ready(function (){
                   $('.btn_inward').click(function (){
                       var parenttd = $(this).closest('td');
                       var parenttr = $(this).closest('td').parent('tr');
                        var idstock = parenttd.find('.idstock').val();
                        var idbranch = parenttd.find('.idbranch').val();
                        var idbrand = parenttd.find('.idbrand').val();
                        var idvariant = parenttd.find('.idvariant').val();
                        var idgodown = parenttd.find('.idgodown').val();
                        var temp_idgodown = parenttd.find('.temp_idgodown').val();
                        var idskutype = parenttd.find('.idskutype').val();
                        var idproductcategory = parenttd.find('.idproductcategory').val();
                        var qty = +parenttr.find('.qty').val();
                        var mqty = +parenttr.find('.mqty').val();
                        if(confirm("Do You Want To Inward This Product")){
                            $.ajax({
                               type: "POST",
                               url: "<?php echo base_url('Audit/ajax_missing_inward_stock_data'); ?>",
                               data: {idstock: idstock, idbranch: idbranch, idbrand: idbrand, idvariant: idvariant, idgodown: idgodown, temp_idgodown: temp_idgodown, idskutype: idskutype, idproductcategory: idproductcategory, qty: qty},
                               success: function(data){
//                                   alert(data);
                                   if(data == '1' || data == 1){
                                        parenttr.remove();
                                   }
                                   if(data == '2' || data == 2){
                                       $( "#btnreport" ).trigger( "click" );
                                   }
                                   if(data == '0' || data == 0){
                                       alert("Failed To Inward");
                                       return false;
                                   }
                               }
                           }); 
                        }else{
                           return false;
                        }
                   }); 
                });
            </script>
        <?php }else { ?>
            <script>
                $(document).ready(function (){
                    alert("Data Not Found");
                    return false;    
                });
            </script>
        <?php }
        
    }
    
    public function ajax_missing_inward_stock_data(){
        $this->db->trans_begin();
        $idstock = $this->input->post('idstock');
        $idbranch = $this->input->post('idbranch'); 
        $idbrand = $this->input->post('idbrand');
        $idvariant = $this->input->post('idvariant');
        $idgodown = $this->input->post('idgodown');
        $temp_idgodown = $this->input->post('temp_idgodown');
        $idskutype = $this->input->post('idskutype');
        $idproductcategory = $this->input->post('idproductcategory');
        $qty = $this->input->post('qty');
        $stock_data = $this->Audit_model->ajax_get_stock_data_byid($idstock);
        $qty_stock_data = $this->Audit_model->ajax_get_missing_stockdata_byidvariant($idvariant, $idbranch, $idproductcategory, $idbrand, $temp_idgodown);
        $datetime = date('Y-m-d h:i:s');
        if($idskutype == 4){
            //missing qty minus from missing godown
            $qt = $stock_data->qty - $qty;
            
            if($qt == 0){
                if($this->Audit_model->delete_imei_from_stock($stock_data->id_stock)){
                    //missing qty plus in actual stock
                    $uqty = $qty_stock_data->qty + $qty;
                    $stock_update = array(
                        'qty' => $uqty,
                    );
                    $this->Audit_model->update_stock_data($stock_update, $qty_stock_data->id_stock);
                }
                
            }else{
                $qtystock_update = array(
                    'qty' => $qt,
                    'inward_date' => date('Y-m-d h:i:s'),
                );
                if($this->Audit_model->update_stock_data($qtystock_update, $stock_data->id_stock)){
                    //missing qty plus in actual stock
                    $uqty = $qty_stock_data->qty + $qty;
                    $stock_update = array(
                        'qty' => $uqty,
                    );
                    $this->Audit_model->update_stock_data($stock_update, $qty_stock_data->id_stock);
                }
            } 
        }else{
            $stock_update = array(
                'temp_idgodown' => 0,
                'idgodown' => $temp_idgodown,
                'inward_date' => date('Y-m-d h:i:s'),
            );
            if( $this->Audit_model->update_stock_data($stock_update, $idstock)){
                if($stock_data){
                    $imei_history = array(
                        'imei_no' => $stock_data->imei_no,
                        'entry_type' => 'Missing Inward',
                        'entry_time' => $datetime,
                        'date' => date('Y-m-d'),
                        'idbranch' => $stock_data->idbranch,
                        'idgodown' => $temp_idgodown,
                        'idvariant' => $stock_data->idvariant,
//                        'model_variant_full_name' => $stock_data->product_name,
                        'idimei_details_link' => 17, 
                        'idlink' => $idstock,
                        'iduser' => $_SESSION['id_users'],
                    );
                    $this->Audit_model->save_imei_history($imei_history);
                }
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            echo '0';
        }else{
            $this->db->trans_commit();
            if($idskutype == 4){
                if($qt == 0){
                    echo '1';
                }else{
                    echo '2';
                }
            }else{
                echo '1';
            }
            
        }
    }
   
    //------------- Accountant and auditor report--------------------

    function accountant_audit_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Branch Accountant';
        $q['page_name'] = 'Accountant Audit Report';
        if($this->session->userdata('level') == 1){  //admin all branch
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('audit/auditor_audit_report',$q);
    }
    
    function auditor_audit_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Auditor';
        $q['page_name'] = 'Auditor Audit Report';
        if($this->session->userdata('level') == 1){  //admin all branch
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('audit/auditor_audit_report',$q);
    }
    
    //-------------Warehouse Audit report -------------------
    
    function warehouse_audit_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Warehouse Incharge';
        $q['page_name'] = 'Warehouse Audit Report';
        
        if($this->session->userdata('level') == 1){  //admin all branch
            if($this->session->userdata('role_type') == 1){
                $q['branch_data'] = $_SESSION['idbranch'];
            }else{
                $q['branch_data'] = $this->General_model->get_active_warehouse_data();
            }
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->General_model->get_warehouses_by_user($_SESSION['id_users']);
        }
        
//        die(print_r($q['branch_data']));
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        
        $this->load->view('audit/auditor_audit_report',$q);
    }
    
    function ajax_accountant_audit_report(){
//        die(print_r($_POST));
        $idcat = $this->input->post('idcat');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $role = $this->input->post('role');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        
        $audit_data = $this->Audit_model->get_audit_data_byfilter($from, $to, $idbranch, $role, $idcat, $idbrand);
//        die('<pre>'.print_r($audit_data,1).'</pre>');
        if(count($audit_data) > 0) { ?>
            <table class="table table-bordered table-condensed " id="accountant_audit_report">
                <thead style="background-color: #99ccff" class="fixheader">
                    <th><b>Sr.</b></th>
                    <th><b>Date.</b></th>
                    <th><b>Category</b></th>
                    <th><b>Brand</b></th>
                    <th><b>Branch</b></th>
                    <th><b>Godown</b></th>
                    <th><b>System Count</b></th>
                    <th><b>Matched</b></th>
                    <th><b>Unmatched</b></th>
                    <th><b>Missing</b></th>
                    <th><b>Start</b></th>
                    <th><b>End</b></th>
                    <th><b>Info</b></th>
                </thead>
                <tbody id="myTable">
                    <?php $matched =0; $sys_cnt =0; $i=1; foreach($audit_data as $audit){ 
                        $matched =  $audit->matched_count;
                        $sys_cnt = $matched + $audit->missing_count; ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $audit->finish_date; ?></td>
                        <td><?php echo $audit->product_category_name; ?></td>
                        <td><?php echo $audit->brand_name; ?></td>
                        <td><?php echo $audit->branch_name; ?></td>
                        <td><?php echo $audit->godown_name; ?></td>
                        <td><?php echo $sys_cnt; ?></td>
                        <td><?php echo $audit->matched_count; ?></td>
                        <td><?php echo $audit->unmatched_count ?></td>
                        <td><?php echo $audit->missing_count ?></td>
                        <td><?php echo date('Y-m-d h:i:s a', strtotime($audit->audit_start)) ?></td>
                        <td><?php echo date('Y-m-d h:i:s a', strtotime($audit->entry_time)) ?></td>
                        <td>
                            <form target="_blank">
                                <input type="hidden" name="idproductcat" value="<?php echo $audit->idproductcategory ?>">
                                <input type="hidden" name="idbrand" value="<?php echo $audit->idbrand ?>">
                                <input type="hidden" name="idbranch" value="<?php echo $audit->idbranch ?>">
                                <input type="hidden" name="rolename" value="<?php echo $role ?>">
                                <input type="hidden" name="from" value="<?php echo $audit->finish_date ?>">
                                <input type="hidden" name="entry_time" value="<?php echo $audit->entry_time ?>">

                                <button class="btn btn-floating btn-info"   formmethod="POST" formaction="<?php echo base_url()?>Audit/audit_repot_details"><span class="fa fa-share"></span></button>
                            </form>
                            </td>
                    </tr>
                    <?php } ?>
                </tbody>

            </table>

        <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
    function audit_repot_details(){
        $idcat = $this->input->post('idproductcat');
        $idbrand = $this->input->post('idbrand');
        $idbranch = $this->input->post('idbranch');
        $from = $this->input->post('from');
        $role = $this->input->post('rolename');
        $entry_time = $this->input->post('entry_time');
                
        $q['tab_active'] = 'Audit';
        $q['audit_data'] = $this->Audit_model->get_audit_report_deatils($idcat, $idbrand, $idbranch, $from,  $role, $entry_time);
        $this->load->view('audit/audit_report_details',$q);
    }
    
    function auditor_analysis_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Auditor';
        $q['page_name'] = 'Auditor Analysis Report';
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
         $q['godown_data'] = $this->General_model->get_active_godown();
        $this->load->view('audit/analysis_report',$q);
    }
   
    function accountant_analysis_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Branch Accountant';
        $q['page_name'] = 'Accountant Analysis Report';
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){
            $q['category_data'] = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $q['category_data'] = $this->General_model->get_product_category_data();
        }
        $q['godown_data'] = $this->General_model->get_active_godown();
        
        $this->load->view('audit/analysis_report',$q);
    }
    function warehouse_analysis_report(){
        $q['tab_active'] = 'Audit';
        $q['page_name'] = 'Analysis Report';
        $q['role'] = 'Warehouse Incharge';
        $q['category_data'] = $this->General_model->get_product_category_data();
        $this->load->view('audit/analysis_report',$q);
    }
          
    function ajax_analysis_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $role = $this->input->post('role');
        $idcat = $this->input->post('idcategory');
        $idgodown = $this->input->post('idgodown');
        $allgodown = $this->input->post('allgodown');
        $allcats = $this->input->post('allcats');
        
        $audit_data = $this->Audit_model->ajax_get_audit_analysis_data($from, $to, $role, $idcat, $idgodown, $allgodown, $allcats);
//        die('<pre>'.print_r($audit_data,  1).'</pre>');
        if($this->session->userdata('level') == 1){  //admin all branch
            $branch = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $branch = $this->Audit_model->get_user_session_branch_data();
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $branch = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){ //Warehouse
            $category_data = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $category_data = $this->General_model->get_product_category_data();
        }
        
        $brand_data = $this->General_model->get_active_brand_data();
        
        $godown_data = $this->General_model->get_active_godown_data();
        ?>
        <table class="table table-bordered" id="accountant_analysis_report">
            <thead style="background-color: #a3d1f3" class="fixheader">
                <th>Sr.</th>
                <th>Product Category</th>
                <th>Brand</th>
                <th>Godown</th>
                <?php foreach($branch as $bran){
                    $cn =0;  foreach($audit_data as $audit){ 
                        if( $audit->idbranch == $bran->id_branch){
                            $cn = $cn + $audit->cnt;
//                            echo $audit->cnt;
                        }
                    } 
                    ?>
                <th><?php echo $bran->branch_name ?> <div class="badge badge-secondary pull-right " style="font-size:14px;"><?php echo $cn ?></div></th>
                 <?php }?>
            </thead>
            <tbody id="myTable">
                <?php 
                if($idcat == 0){
                    $sr =1;
                    foreach ($category_data as $cat){
                        foreach($brand_data as $brand){ ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td class="fixleft"><?php echo $cat->product_category_name; ?> </td>
                                <td class="fixleft1"><?php echo $brand->brand_name; ?></td>
                                <td class="fixleft1"><?php foreach ($godown_data as $gdata){ if($gdata->id_godown == $idgodown){ echo $gdata->godown_name ; }}  ?></td>
                                <?php  foreach($branch as $brnch){ ?>
                                 <?php $cn =0;$gdata =''; foreach($audit_data as $audit){ 
                                            if($audit->idproductcategory == $cat->id_product_category && $audit->idbrand == $brand->id_brand && $audit->idbranch == $brnch->id_branch){
                                                $cn = $audit->cnt;
                                            }
                                            ?>

                                                <?php 
                                        } ?>

                                 <td><?php echo $cn;?></td>
                                 <?php }?>
                            </tr>
                       <?php } 
                    } 
                }else{ 
                    $sr =1;
                    foreach ($category_data as $cat){
                        foreach($brand_data as $brand){
                            if($idcat == $cat->id_product_category){ ?>
                                <tr>
                                    <td><?php echo $sr++; ?></td>
                                    <td><?php echo $cat->product_category_name; ?></td>
                                    <td><?php echo $brand->brand_name; ?></td>
                                    <td class="fixleft1"><?php foreach ($godown_data as $gdata){ if($gdata->id_godown == $idgodown){ echo $gdata->godown_name ; }}  ?></td>
                                    <?php  foreach($branch as $brnch){
                                        $cn =0;  foreach($audit_data as $audit){ 
                                            if($audit->idproductcategory == $cat->id_product_category && $audit->idbrand == $brand->id_brand && $audit->idbranch == $brnch->id_branch){
                                                $cn = $audit->cnt;
                                            }
                                        } ?>
                                    <td><?php echo $cn;?></td>
                                    <?php } ?>
                                </tr>
                <?php }} } }  ?>
            </tbody>
        </table> 
    <?php 
    }
    
  /*   19 March Live Backup
   
   function ajax_analysis_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $role = $this->input->post('role');
        $idcat = $this->input->post('idcategory');
        $audit_data = $this->Audit_model->ajax_get_audit_analysis_data($from, $to, $role, $idcat);
//        die('<pre>'.print_r($audit_data,1).'</pre>');
        if($this->session->userdata('level') == 1){  //admin all branch
            $branch = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $branch = $this->Audit_model->get_user_session_branch_data();
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $branch = $this->General_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        if($this->session->userdata('idrole') == 18 || $this->session->userdata('idrole') == 15){ //Warehouse
            $category_data = $this->General_model->get_product_category_by_user($_SESSION['id_users']);
        }else{ 
            $category_data = $this->General_model->get_product_category_data();
        }
        $brand_data = $this->General_model->get_active_brand_data();
        ?>
        <table class="table table-bordered" id="accountant_analysis_report">
            <thead style="background-color: #a3d1f3" class="fixheader">
                <th>Sr.</th>
                <th>Product Category</th>
                <th>Brand</th>
                <?php foreach($branch as $bran){
                    $cn =0;  foreach($audit_data as $audit){ 
                        if( $audit->idbranch == $bran->id_branch){
                            $cn = $cn + $audit->cnt;
//                            echo $audit->cnt;
                        }
                    } 
                    ?>
                <th><?php echo $bran->branch_name ?> <div class="badge badge-secondary pull-right " style="font-size:14px;"><?php echo $cn ?></div></th>
                 <?php }?>
            </thead>
            <tbody id="myTable">
                <?php 
                    if($idcat == 0){
                $sr =1;
                foreach ($category_data as $cat){
                    foreach($brand_data as $brand){ ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td class="fixleft"><?php echo $cat->product_category_name; ?> </td>
                    <td class="fixleft1"><?php echo $brand->brand_name; ?></td>
                    <?php  foreach($branch as $brnch){ ?>
                     <?php $cn =0; foreach($audit_data as $audit){ 
                                if($audit->idproductcategory == $cat->id_product_category && $audit->idbrand == $brand->id_brand && $audit->idbranch == $brnch->id_branch){
                                    $cn = $audit->cnt;
                                }
                            } ?>
                        <td><?php echo $cn;?></td>
                     <?php }?>
                </tr>
                    <?php } }}else{ 
                        $sr =1;
                 foreach ($category_data as $cat){
                    foreach($brand_data as $brand){
                        if($idcat == $cat->id_product_category){
                    ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $cat->product_category_name; ?></td>
                    <td><?php echo $brand->brand_name; ?></td>
                    <?php  foreach($branch as $brnch){ ?>
                     <?php $cn =0; foreach($audit_data as $audit){ 
                                if($audit->idproductcategory == $cat->id_product_category && $audit->idbrand == $brand->id_brand && $audit->idbranch == $brnch->id_branch){
                                    $cn = $audit->cnt;
                                }
                            } ?>
                        <td><?php echo $cn;?></td>
                     <?php }?>
                </tr>
                <?php }} } }  ?>
            </tbody>
        </table>
    <?php 
    }
   
   */
    
    public function brand_audit_report(){
        $q['tab_active'] = 'Audit';
        $q['role'] = 'Auditor';
        $q['page_name'] = 'Brand Wise Audit Report';
        if($this->session->userdata('level') == 1){  //admin all branch
            $q['branch_data'] = $this->Audit_model->get_active_branch_data();
        }elseif($this->session->userdata('level') == 2){   // Branch Accountant
            $q['branch_data'] = $_SESSION['idbranch'];
        }elseif($this->session->userdata('level') == 3){  //Multiple branches
            $q['branch_data'] = $this->Audit_model->get_branches_by_user($_SESSION['id_users']);
        }
        
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->view('audit/brand_audit_report',$q);
    }
    
    public function ajax_get_brand_wise_audit_report(){
        $idbranch = $this->input->post('idbranch');
        $rolename = $this->input->post('role');
        $idbrand = $this->input->post('idbrand');
        $from = $this->input->post('from');
        $status = $this->input->post('status');
        $audit_data = $this->Audit_model->get_audit_data_bybrand($idbranch, $idbrand, $rolename, $from, $status);
        if(count($audit_data) > 0){ ?>
            <table class="table table-bordered table-condensed" id="brand_wise_audit_report">
                <thead class="fixheader">
                    <th>Sr.</th>
                    <th>Date</th>
                    <th>Imei No.</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Branch</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Status</th>
                </thead>
                <tbody id="myTable">
                <?php $i=1; foreach ($audit_data as $audit){ ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $audit->finish_date; ?></td>
                        <td><?php echo $audit->imei_no; ?></td>
                        <td><?php echo $audit->product_category_name; ?></td>
                        <td><?php echo $audit->brand_name; ?></td>
                        <td><?php echo $audit->branch_name; ?></td>
                        <td><?php echo $audit->product_name; ?></td>
                        <td><?php echo $audit->qty; ?></td>
                        <td><?php echo $audit->status; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Audit Not Found");
                });
            </script>
        <?php }
    }
}