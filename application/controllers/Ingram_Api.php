<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ingram_Api extends CI_Controller {

    var $idwarehouse = 141;
    var $stock_idgodown=1;
    public function __construct() {
        parent::__construct(); 
        
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Ingram_Model');
        $this->load->model('General_model');
        $this->load->model('Sale_model');
        $this->load->model('Purchase_model');
        $this->load->model('Api_Model');
        $this->load->model('Inward_model');
        $this->load->model('Stock_model');
        $this->load->model('Outward_model');
        
        
//       sale_token -> ingram_status -> 1-Pendig for approval,2-Order Placed, 3 - Rejected, 4 - Pick and Verify, 5 - Packed and Dispatched, 6 - Received, 7 - Returned, 8 - Refund, 9-Closed'
//       sale_token -> deliver_at -> 0 - branch, 1 - customer 
//       vendor_po -> status -> 0=pending for approval,1=approved & submitted,2= rejected by checker,3 = rejected by vendor
//       vendor_po -> ingram_order_status -> 1 - pending for inward, 2-ready to pick,3-picked,4-packed,5-competed,6-returned,
        
    }
    public function stock_search() {
        $idbranch = $_SESSION['idbranch'];        
        $user_id=$this->session->userdata('id_users');         
        $q['tab_active'] = '';                
        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
        $q['sku_column']=$sku_data->column_name;
        $q['model_data'] = $this->Ingram_Model->ajax_get_ingram_sku(23,$sku_data->column_name);            
        $this->load->view('ingram/stock_search', $q);
    }
    
    
    public function ajax_get_ingram_stock_by_variant() { 
        
        $sku_code = $this->input->post('sku');   
//        die($sku_code);
        $model_name = $this->input->post('model_name');
        ?>             
            <thead class="fixedelement" style="text-align: center;position: none !important;">                        
                <th style="text-align: center;">Vendor</th>
                <th style="text-align: center;">Product Name</th>
                <th style="text-align: center;">Current Stock</th>                 
            </thead>
            <?php
        
            $role_type=$this->session->userdata('role_type');
            $idbranch=$this->session->userdata('idbranch');
            $level=$this->session->userdata('level');        
            $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
            $sku_column=$sku_data->column_name;
            $access_token=$sku_data->access_token;
            
            $ingram_html="";
          
                $data=array();
                $data['servicerequest']=array();
                $servicerequest=array();
                $servicerequest['requestpreamble']=array();
                $servicerequest['requestpreamble']['customernumber']="40-SSSEPV";
                $servicerequest['requestpreamble']['isocountrycode']="IN";
                $servicerequest['priceandstockrequest']=array();
                $servicerequest['priceandstockrequest']['showwarehouseavailability']="True";
                $servicerequest['priceandstockrequest']['extravailabilityflag']="Y";
                $servicerequest['priceandstockrequest']['item']=array();
                $item=array();
                $item['ingrampartnumber']=$sku_code;
                $item['warehouseidlist']=array(31);
                $item['quantity']="1";
                $servicerequest['priceandstockrequest']['item'][]=$item;
                $servicerequest['priceandstockrequest']['includeallsystems']=false;
                $data['servicerequest']= $servicerequest;

                $ingram_data= $this->Ingram_Model->getPriceAndAvailability($data,$access_token);
                
                if(isset($ingram_data['serviceresponse']) && $ingram_data['serviceresponse']['responsepreamble']['responsestatus']=='SUCCESS'){
                    $customerprice=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['customerprice'];
                    $warehousedetails=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['warehousedetails'];
                    $availablequantity=0;
                    foreach ($warehousedetails as $wid){
                        $availablequantity=$availablequantity+$wid['availablequantity'];
                    }
                    $ingram_html.='<tr>                        
                        <td class="textalign">Ingram Micro</td>   
                        <td class="textalign">'.$model_name.'</td>                                    
                        <td class="textalign">'.$availablequantity.'</td>                                                            
                        </tr>';
                }else{
                    
                   if(isset($ingram_data['fault']) || $ingram_data['fault']['faultstring']!=''){
//                        $_SESSION['access_token']=null;
//                        $_SESSION['token_type']=null;
                        $this->Ingram_Model->getToken();
                        $ingram_html.="Please try again";
                   } 
                }
            
        ?>
         
        <tbody class="data_1">           
            <?php echo $ingram_html ?>
       </tbody>  
    <?php     
    }
    
    public function ingram_purchase() {
        $q['tab_active'] = '';
        $idbranch = $_SESSION['idbranch'];
        
        $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $q['payment_head'] = $this->General_model->get_active_payment_head();
        $q['payment_mode'] = $this->General_model->get_active_payment_mode();
        $q['payment_attribute'] = $this->General_model->get_payment_head_has_attributes();        
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
        $q['sku_column']=$sku_data->column_name;
        $q['model_variant'] = $this->Ingram_Model->ajax_get_ingram_sku(23,$sku_data->column_name);        
        $q['var_closer'] = 1;
//        $q['var_closer'] = $this->verify_cash_closure();
        $q['allow_ingram_purchase']=1;
//        if($q['invoice_no']->ingram_purchase==0){
//            $q['allow_ingram_purchase']=0;
//        }
        
        $this->load->view('ingram/create_purchase',$q);
    }
    public function purchase_order() {
        $q['tab_active'] = 'Purchase';
        $datefrom=''; $dateto=''; $status=0;        
        $q['purchase_order'] = $this->Ingram_Model->ajax_get_ingram_purchase_order_data($status, $datefrom, $dateto);        
        $this->load->view('ingram/purchase_order',$q);
    }
    public function ingram_po() {
        $q['tab_active'] = '';
        $result= $this->Ingram_Model->getToken();
        $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($this->idwarehouse);
        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
        $q['sku_column']=$sku_data->column_name;
        $q['model_variant'] = $this->Ingram_Model->ajax_get_ingram_sku(23,$sku_data->column_name);        
        $this->load->view('ingram/create_po',$q);
    }

    public function ajax_get_imei_details() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $idbranch = $this->input->post('idbranch'); 
        $idvariant = $this->input->post('idvariant');                 
        $sale_type = $this->input->post('sale_type');
        $sku_code = $this->input->post('sku');   
        $model_name = $this->input->post('model_name');
        $sale_type = (int)$sale_type;
        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
        $sku_column=$sku_data->column_name;
        $access_token=$sku_data->access_token;
        
                $data=array();
                $data['servicerequest']=array();
                $servicerequest=array();
                $servicerequest['requestpreamble']=array();
                $servicerequest['requestpreamble']['customernumber']="40-SSSEPV";
                $servicerequest['requestpreamble']['isocountrycode']="IN";
                $servicerequest['priceandstockrequest']=array();
                $servicerequest['priceandstockrequest']['showwarehouseavailability']="True";
                $servicerequest['priceandstockrequest']['extravailabilityflag']="Y";
                $servicerequest['priceandstockrequest']['item']=array();
                $item=array();
                $item['ingrampartnumber']=$sku_code;
                $item['warehouseidlist']=array(31);
                $item['quantity']="1";
                $servicerequest['priceandstockrequest']['item'][]=$item;
                $servicerequest['priceandstockrequest']['includeallsystems']=false;
                $data['servicerequest']= $servicerequest;
                $availablequantity=0;
                $error_flag=0;
                $ingram_data= $this->Ingram_Model->getPriceAndAvailability($data,$access_token);
//                die('<pre>' . print_r($ingram_data, 1) . '</pre>');
                $customerprice=0;
                $retailprice=0;
                if(isset($ingram_data['serviceresponse']) && $ingram_data['serviceresponse']['responsepreamble']['responsestatus']=='SUCCESS'){
                    $customerprice=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['customerprice'];
                    $warehousedetails=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['warehousedetails'];
                    $retailprice=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['retailprice'];
                    foreach ($warehousedetails as $wid){
                        $availablequantity=$availablequantity+$wid['availablequantity'];
                    }                    
                }else if(isset($ingram_data['serviceresponse']) && $ingram_data['serviceresponse']['responsepreamble']['responsestatus']=='FAILED'){
                   echo '0';  // Product not found
                   $error_flag=1;
                }
                else{                    
                   if(isset($ingram_data['fault']) || $ingram_data['fault']['faultstring']!=''){
                        $this->Ingram_Model->getToken();      
                        echo '1';  // Token expired -> Re-try
                        $error_flag=1;
                   } 
                }        
        // Quantity
               
        if($availablequantity>=0 && $error_flag==0){     
            
            $models = $this->Ingram_Model->ajax_get_variant_byid_branch_godown($idvariant, $this->idwarehouse,$this->stock_idgodown);
             
            $avail_qty=0;
           if($models->avail_qty!=NULL){
               $avail_qty=$models->avail_qty;
           }
            $model = $this->General_model->get_active_variants_id($idvariant);            
                            $amount_diff = $model->mop - $model->landing; ?>
                            <tr id="m<?php echo $model->id_stock ?>">
                                <td>
                                    <?php echo $model->full_name; ?>
                                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                    <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                    <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->id_variant ?>" />
                                    <input type="hidden" id="sku" class="form-control sku" name="sku[]" value="<?php echo $sku_code ?>" />
                                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />                                    
                                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idsku_type ?>" />
                                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->full_name; ?>" />
                                    <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="1" />
                                    <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />                                                                                                            
                                    <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="1" />                                    
                                    <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                </td>      
                                <td><?php echo $sku_code; ?></td>
                                
                                <td>
                                    <input type="hidden" id="availqty" name="availqty[]" class="form-control input-sm availqty"  value="<?php echo $availablequantity; ?>" style="width: 70px"/>
                                    <?php echo $availablequantity;?></td>
                                <td><?php echo $avail_qty; ?></td>
                                <td><?php echo $retailprice; ?></td>
                                <td><?php echo $customerprice; ?></td>
                                           
                                    <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                    <input type="hidden" id="online_price" name="online_price[]" class="online_price" value="<?php echo $model->online_price ?>" />
                                    <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                     <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                                    <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />                                                                        
                                    <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                <td>
                                    <input type="text" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>                                    
                                </td>
                                
                                <td>
                                    <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>                                   
                                </td>
                            </tr>
                <?php 
        }else{
           if($error_flag!=1){
               echo '2'; //Quantity not available
           } 
        }
    }
    
    public function ajax_get_product_details() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $idbranch = $this->input->post('idbranch'); 
        $idvariant = $this->input->post('idvariant');                 
        $sale_type = $this->input->post('sale_type');
        $idskutype = $this->input->post('idskutype');
        $sku_code = $this->input->post('sku');   
        $model_name = $this->input->post('model_name');
        $sale_type = (int)$sale_type;
        $idgodown= $this->stock_idgodown;
        $models = $this->Ingram_Model->ajax_get_variant_byid_branch_godown($idvariant, $this->idwarehouse,$idgodown);
        $bqty = $this->Ingram_Model->ajax_get_booked_qty($idvariant, $this->idwarehouse);        
        $avail_qty = 0;
        if ($models->avail_qty != NULL) {   
            $avail_qty = $models->avail_qty;
            if($bqty->booked_qty!=NULL){
                $avail_qty = ($models->avail_qty)-($bqty->booked_qty);
            }
        }
//            die(print_r($models));
//        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
//        $sku_column=$sku_data->column_name;
//        $access_token=$sku_data->access_token;
        
//                $data=array();
//                $data['servicerequest']=array();
//                $servicerequest=array();
//                $servicerequest['requestpreamble']=array();
//                $servicerequest['requestpreamble']['customernumber']="40-SSSEPV";
//                $servicerequest['requestpreamble']['isocountrycode']="IN";
//                $servicerequest['priceandstockrequest']=array();
//                $servicerequest['priceandstockrequest']['showwarehouseavailability']="True";
//                $servicerequest['priceandstockrequest']['extravailabilityflag']="Y";
//                $servicerequest['priceandstockrequest']['item']=array();
//                $item=array();
//                $item['ingrampartnumber']=$sku_code;
//                $item['warehouseidlist']=array(31);
//                $item['quantity']="1";
//                $servicerequest['priceandstockrequest']['item'][]=$item;
//                $servicerequest['priceandstockrequest']['includeallsystems']=false;
//                $data['servicerequest']= $servicerequest;
//                $availablequantity=0;
//                $error_flag=0;
//                $ingram_data= $this->Ingram_Model->getPriceAndAvailability($data,$access_token);
////                die('<pre>' . print_r($ingram_data, 1) . '</pre>');
//                $customerprice=0;
//                $retailprice=0;
//                if(isset($ingram_data['serviceresponse']) && $ingram_data['serviceresponse']['responsepreamble']['responsestatus']=='SUCCESS'){
//                    $customerprice=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['customerprice'];
//                    $warehousedetails=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['warehousedetails'];
//                    $retailprice=$ingram_data['serviceresponse']['priceandstockresponse']['details'][0]['retailprice'];
//                    foreach ($warehousedetails as $wid){
//                        $availablequantity=$availablequantity+$wid['availablequantity'];
//                    }                    
//                }else if(isset($ingram_data['serviceresponse']) && $ingram_data['serviceresponse']['responsepreamble']['responsestatus']=='FAILED'){
//                   echo '0';  // Product not found
//                   $error_flag=1;
//                }
//                else{                    
//                   if(isset($ingram_data['fault']) || $ingram_data['fault']['faultstring']!=''){
//                        $this->Ingram_Model->getToken();      
//                        echo '1';  // Token expired -> Re-try
//                        $error_flag=1;
//                   } 
//                }        
        // Quantity
        if($avail_qty > 0){                 
            $model = $this->General_model->get_active_variants_id($idvariant);            
                            $amount_diff = $model->mop - $model->landing; ?>
                            <tr id="m<?php echo $model->id_variant ?>" class="skuqty_row">
                    <td>
                        <?php echo $model->full_name; ?>
                        <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                        <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                        <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                        <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                        <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->id_variant ?>" />
                        <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                        <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idsku_type ?>" />
                        <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->full_name; ?>" />
                        <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                        <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                        <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="1" />                        
                        <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                       
                    </td>
                    
                    <td><?php echo $avail_qty; ?>
                    <input type="hidden" id="availqty" name="availqty[]" class="form-control input-sm availqty"  value="<?php echo $avail_qty; ?>" style="width: 70px"/>
                    </td>
                    <td><?php echo $model->mrp; ?></td>
                    <td><?php echo $model->mop; ?></td>
                    <td>
                        <input type="hidden" id="read_skuprice<?php echo $idvariant ?>" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                        <input type="hidden" id="online_price" name="online_price[]" class="online_price" value="<?php echo $model->online_price ?>" />
                        <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $model->nlc_price ?>" />
                        <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />                        
                        <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                        <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $sale_type ?>" />                        
                        <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" step="0.001" style="width: 90px" />
                        
                    </td>
                    <td>                       
                        <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" style="width: 90px" max="<?php echo $avail_qty; ?>"/>                        
                    </td>
                    <td>
                        <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                        <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                        <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                    </td>
                    <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px" <?php if($model->is_mop == 0){ ?> readonly="" <?php } ?> /></td>
                    <td>
                        <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                        <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="13"/>
                        <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                        <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                        <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                        <?php echo $model->igst ?>%
                    </td>
                    <td>
                        <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                        <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                    </td>
                    <td>
                        <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>                        
                    </td>
                </tr>
                <?php 
        }else{           
               echo '2'; //Quantity not available           
        }
    }
   
    public function pending_orders() {
        $q['tab_active'] = 'Purchase';
        $datefrom=''; $dateto=''; $status=1;$idbranch=0;        
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($status, $idbranch,$datefrom, $dateto);                        
        $this->load->view('ingram/pending_orders',$q);
    }
    
    public function pending_inwards() {        
        $q['tab_active'] = '';  
        $datefrom=''; $dateto=''; $ingram_status=1;           
        $q['purchase_order'] = $this->Ingram_Model->ajax_get_ingram_purchase_order_data($ingram_status, $datefrom, $dateto);        
        $this->load->view('ingram/pending_inwards',$q);
    }
    public function ready_to_pick() {        
        $q['tab_active'] = 'Pick and Verify';  
        $datefrom=''; $dateto=''; $ingram_status=2;$idbranch=0;        
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto);                       
        $this->load->view('ingram/ingram_order',$q);
    }
    public function dispatch_orders() {        
        $q['tab_active'] = 'Update Shipment and Dispatch';  
        $datefrom=''; $dateto=''; $ingram_status=4;$idbranch=0;     
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto); 
        
        
        $this->load->view('ingram/ingram_order',$q);
    }
    public function delivery_confirmation() {        
        $q['tab_active'] = 'Delivery confirmation';  
        $datefrom=''; $dateto=''; $ingram_status=5;$idbranch=0;     
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto);                      
        $this->load->view('ingram/ingram_order',$q);
    }
    public function completed_orders() {        
        $q['tab_active'] = 'Successfull Orders';  
        $datefrom=''; $dateto=''; $ingram_status=6;$idbranch=0;     
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto);                         
        $this->load->view('ingram/ingram_order',$q);
    }
    
    public function returned_orders() {        
        $q['tab_active'] = 'Returned Orders';  
        $datefrom=''; $dateto=''; $ingram_status=7; $idbranch=0;     
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto);                    
        $this->load->view('ingram/ingram_order',$q);
    }
    public function my_ingram_order() {
        $q['tab_active'] = 'Sale';
        $status=0;  
        $datefrom='';  
        $dateto='';          
        $idbranch = $this->session->userdata('idbranch');
        $q['sale_token_data'] = $this->Ingram_Model->get_pending_sale_token($status,$idbranch, $datefrom, $dateto); 
//        die('<pre>'.print_r($q['sale_token_data'],1).'</pre>');
        $this->load->view('ingram/branch_order', $q);
    }
    
    public function order_report() {
        $q['tab_active'] = 'Sale';
        $status=0;  
        $datefrom='';  
        $dateto='';          
        $idbranch = $this->session->userdata('idbranch');
        $q['branch_data']=$this->General_model->get_active_branch_data();
        $q['sale_token_data'] = $this->Ingram_Model->get_pending_sale_token($status,$idbranch, $datefrom, $dateto);         

        $this->load->view('ingram/order_report', $q);
    }

    public function ingram_inward($idpo) {
        $q['tab_active'] = '';        
        $q['purchase_order'] = $this->Ingram_Model->get_purchase_order_byid($idpo);             
        if($q['purchase_order']->status==1 && $q['purchase_order']->ingram_order_status==1){
            $q['purchase_order_product'] = $this->Ingram_Model->get_purchase_order_product_byid($idpo);
            $this->load->view('ingram/purchase_inward',$q);
        }else{
            $this->session->set_flashdata('save_data', 'Error, Already inwared');
             redirect('Ingram_Api/pending_inwards');     
        }        
    }
    
    public function pick_order($id_sale_token){
        $q['tab_active'] = ''; 
        $q['idwarehouse'] = $this->idwarehouse;         
        $q['purchase_order'] = $this->Ingram_Model->ajax_get_branch_order_databy_idsaletoken($id_sale_token);                
        if($q['purchase_order'][0]->ingram_status==2){                 
            $this->load->view('ingram/pick_order', $q);        
        }else{
            redirect('Ingram_Api/ready_to_pick');    
        }
    }
    
    public function pack_stock(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $datetime = date('Y-m-d H:i:s');        
        $date = date('Y-m-d');                
        $id_sale_token = $this->input->post('id_sale_token');
        $po_number = $this->input->post('po_number');
        $iduser=$this->session->userdata('id_users'); 
        $this->db->trans_begin();
        $sale_data = $this->Ingram_Model->get_saletoken_byid($id_sale_token);        
        $sale_product = $this->Api_Model->get_saletoken_product_byid($id_sale_token);       
        $sale_payment = $this->Api_Model->get_saletoken_payment_byid($id_sale_token);     
        
        $saledata = $this->Ingram_Model->get_sale_by_tokenid($id_sale_token);
       $pay_recon=array();
        foreach ($sale_payment as $payments)
        {
                    $attr_value=array();
                    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($payments->idpayment_head);
                    foreach ($payment_attribute as $attr){
                        $tmp=$attr->column_name;                        
                        $attr_value[$tmp] = $payments->$a;
                    }                                        
                    $payment = array(
                        'date' => $date,
                        'idcustomer' => $saledata[0]->idcustomer,
                        'idsale' => $saledata[0]->id_sale,
                        'idbranch' => $saledata[0]->idbranch,                                             
                        'entry_time' => $datetime,
                        'transaction_id' =>$payments->transaction_id,
                        'inv_no' => $saledata[0]->inv_no,
                        'idpayment_head' => $payments->idpayment_head,
                        'idpayment_mode' => $payments->idpayment_mode,
                        'amount' => $payments->amount,
                        'created_by' => $payments->created_by,
                        'received_entry_time'=>$datetime,                                          
                    );                    
                    if(isset($attr_value)>0){
                        $payment = array_merge($payment, $attr_value); 
                    }
                    $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
                    $pay_recon[] = array(       
                        'id_saletokenpayment' => $payments->id_saletokenpayment,
                        '$po_number' => $po_number,
                        'inv_no' => $saledata[0]->inv_no, 
                        'idsale' => $saledata[0]->id_sale,
                        'idsale_payment' => $id_sale_payment
                      );                    
//                    $this->Ingram_Model->update_sale_payment_reconciliation($payments->id_saletokenpayment,$po_number,$pay_recon);                    
        }
        $update_stock=array();
        $imei_history=array();
        foreach ($sale_product as $product) {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = 0;
                    if ($saledata[0]->gst_type == 1) {
                        $igst = $product->igst_per;
                    } else {
                        $cgst = $product->cgst_per;
                        $sgst = $product->sgst_per;
                    }
                    $saleproduct = array(
                        'date' => $date,
                        'idsale' => $saledata[0]->id_sale,
                        'idmodel' => $product->idmodel,
                        'idvariant' => $product->idvariant,
                        'imei_no' => $product->imei_no,
                        'hsn' => $product->hsn,
                        'idskutype' => $product->idskutype,
                        'idgodown' => $product->idgodown,
                        'idproductcategory' => $product->idproductcategory,
                        'idcategory' => $product->idcategory,
                        'idbrand' => $product->idbrand,
                        'product_name' => $product->product_name,
                        'price' => $product->price,
                        'landing' => $product->landing,
                        'online_price' => $product->online_price,
                        'mrp' => $product->mrp,
                        'mop' => $product->mop,
                        'nlc_price' => $product->nlc_price,
                        'ageing' => $product->ageing,
                        'focus' => $product->focus,
                        'focus_incentive' => $product->focus_incentive,
                        'salesman_price' => $product->salesman_price,
                        'qty' => $product->qty,
                        'inv_no' => $saledata[0]->inv_no,
                        'idbranch' => $product->idbranch,
                        'discount_amt' => $product->discount_amt,
                        'is_gst' => $product->is_gst,
                        'is_mop' => $product->is_mop,
                        'basic' => $product->basic,
                        'idvendor' => $product->idvendor,
                        'igst_per' => $product->igst_per,
                        'sgst_per' => $product->sgst_per,
                        'cgst_per' => $product->cgst_per,
                        'total_amount' => $product->total_amount,
                        'entry_time' => $datetime,       
                        'insurance_imei_no' => $product->insurance_imei_no,
                        'activation_code' => $product->activation_code,
                        'ssale_type' => $product->sale_type,
                        'insurance_idbrand' => $product->insurance_idbrand
                    );
                    $idsaleproduct = $this->Sale_model->save_sale_product($saleproduct);
                    
                    if($product->idskutype == 4){ //qunatity
                        $update_stock[]="UPDATE stock SET qty = qty - ".$product->qty." WHERE id_stock = ".$product->id_stock.";";
                
                    }else{
                        $this->Ingram_Model->delete_stock_by_imei($product->imei_no);
                        // IMEI History
                        $imei_history[]=array(
                            'imei_no' => $product->imei_no,
                            'entry_type' => 'Sale - at Ingram',
                            'entry_time' => $datetime,
                            'date' => $date,
                            'idbranch' => $product->idbranch,
                            'idgodown' => $product->idgodown,
                            'idvariant' => $product->idvariant,
                            'idimei_details_link' => 4, 
                            'idlink' => $saledata[0]->id_sale,
                            'iduser' => $iduser,
                        );
                        $imei_history[]=array(
                            'imei_no' => $product->imei_no,
                            'entry_type' => 'Dispatch from ingram',
                            'entry_time' => $datetime,
                            'date' => $date,
                            'idbranch' => $product->idbranch,
                            'idgodown' => $product->idgodown,
                            'idvariant' => $product->idvariant,
                            'idimei_details_link' => 4,
                            'idlink' => $saledata[0]->id_sale,
                            'iduser' => $iduser,
                        );
                    }                    
        }        
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }        
        $update_sale_data = array(
                    'date' => $date,                    
                    'idcustomer' => $sale_data[0]->idcustomer,
                    'basic_total' => $sale_data[0]->basic_total,
                    'discount_total' => $sale_data[0]->discount_total,
                    'final_total' => $sale_data[0]->final_total,
                    'entry_time' => $datetime,
                    'token_uid' => $po_number,
                    'corporate_sale' => 1,
                );
        $this->Ingram_Model->update_sale_idsaletoken($id_sale_token,$update_sale_data);
        $podata = array(
          'packed_date'  => $datetime,
          'awb_no'  => $this->input->post('awb_no'),
          'courier_name'  => $this->input->post('courier_name'),
          'internal_comments'  => $this->input->post('internal_comments'),
          'customer_comments'  => $this->input->post('customer_comments'), 
        );    
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$podata);
        
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 5, //packed          
        );    
        $this->Ingram_Model->update_sale_token($sodata);
        if(count($pay_recon)>0){
        foreach ($pay_recon as $pay){
                $payrecon = array(                               
                            'inv_no' => $saledata[0]->inv_no, 
                            'idsale' => $saledata[0]->id_sale,
                            'idsale_payment' => $id_sale_payment
                          ); 
                $this->Ingram_Model->update_sale_payment_reconciliation($pay->id_saletokenpayment,$pay->po_number,$payrecon);             
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fali Pack the Order! Please try again!');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Order Packed Successfuly');
        }
        
        return redirect('Ingram_Api/delivery_confirmation');
           
    }
    
    public function returned_stock(){
        $datetime = date('Y-m-d H:i:s');        
        $date = date('Y-m-d');        
        $id_vendor_po = $this->input->post('id');
        $return_reason = $this->input->post('return_reason');
        $id_sale_token = $this->input->post('id_sale_token');
        $po_number = $this->input->post('po_number');
        $iduser=$this->session->userdata('id_users'); 
        $this->db->trans_begin();
        $saletoken_product = $this->Api_Model->get_saletoken_product_byid($id_sale_token);
                  
//        $purchase_order = $this->Ingram_Model->ajax_get_purchase_order_databy_idpo($idpo);   
        $stock_array=array();
        $imei_history=array();
        foreach ($saletoken_product as $product){         
            
            $stock_array[] = array(
                            'imei_no' => $product->imei_no,
                            'outward' => 0,
                            'outward_dc' => NULL,
                            'outward_time' => $datetime,
                            'outward_by' => $iduser,
                            'idbranch' => $this->idwarehouse,
                            'temp_idbranch' => 0,
                            'transfer_from' => 0,
                            'outward_remark' => $return_reason,
                        );   
                   $imei_history[]=array(
                        'imei_no' =>$product->imei_no,
                        'entry_type' => 'Rejected by Customer',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $this->idwarehouse,
                        'idgodown' => $product->idgodown,
                        'idvariant' => $product->idvariant,
                        'idimei_details_link' => 0,
                        'iduser' => $iduser,
                        'idlink' => $po_number,
                       'transfer_from' => $this->idwarehouse
                    );           
            
        }
        $this->Ingram_Model->delete_sale_by_tokenid($id_sale_token);
        
        $podata = array(            
          'ingram_order_status' => 6,      //returned          
          'remark' => 'Returned by Customer',                        
        );        
        $this->Ingram_Model->update_purchase_order($id_vendor_po,$podata);
       
        $sodata[] = array(
          'id_sale_token' => $id_sale_token,           
          'ingram_status' => 7, //returned   
        );    
        $this->Ingram_Model->update_sale_token($sodata);
        
        $p_odata = array( 'return_reason'  => $return_reason,'return_date'  => $datetime );   
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);
        
        if(count($stock_array)>0){
            $this->Outward_model->update_batch_stock_byimei($stock_array);        
        }  
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to return.. Try again');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Product returned successfully');
        }
        
         return redirect('Ingram_Api/delivery_confirmation');   
    }
    
    public function cancel_pick_order(){
        $datetime = date('Y-m-d H:i:s');        
        $date = date('Y-m-d');        
        $id_vendor_po = $this->input->post('id_vendor_po');
        $return_reason = $this->input->post('return_reason');
        $id_sale_token = $this->input->post('id_sale_token');
        $po_number = $this->input->post('po_number');
        $iduser=$this->session->userdata('id_users'); 
        $this->db->trans_begin();
        $saletoken_product = $this->Api_Model->get_saletoken_product_byid($id_sale_token);
                  
//        $purchase_order = $this->Ingram_Model->ajax_get_purchase_order_databy_idpo($idpo);   
        $stock_array=array();
        $imei_history=array();
        $vo_poduct=array();
        foreach ($saletoken_product as $product){         
            
            $stock_array[] = array(
                            'imei_no' => $product->imei_no,
                            'outward' => 0,
                            'outward_dc' => NULL,
                            'outward_time' => $datetime,
                            'outward_by' => $iduser,
                            'idbranch' => $this->idwarehouse,
                            'temp_idbranch' => 0,
                            'transfer_from' => 0,
                            'outward_remark' => $return_reason,
                        );   
                   $imei_history[]=array(
                        'imei_no' =>$product->imei_no,
                        'entry_type' => 'Dispatch cnacelled',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $this->idwarehouse,
                        'idgodown' => $product->idgodown,
                        'idvariant' => $product->idvariant,
                        'idimei_details_link' => 0,
                        'iduser' => $iduser,
                        'idlink' => $po_number,
                       'transfer_from' => $this->idwarehouse
                    ); 
                   
        }
        
        $vo_poduct[]=array(
                        'idvendor_po'=>$id_vendor_po,
                        'imei_nos' => NULL);
        
         if(count($vo_poduct)>0){
                            $this->Ingram_Model->update_vendor_products_byid_vendorpo($vo_poduct);                
                        }
        $this->Ingram_Model->delete_sale_by_tokenid($id_sale_token);
        
        
        $podata = array(            
          'ingram_order_status' => 2,      
        );        
        $this->Ingram_Model->update_purchase_order($id_vendor_po,$podata);
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 2,         
        );    
        $this->Ingram_Model->update_sale_token($sodata);
       
        if(count($stock_array)>0){
            $this->Outward_model->update_batch_stock_byimei($stock_array);        
        }  
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        
         $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
            die($output);
        }else{
            $this->db->trans_commit();
            $output = json_encode(array("result" => "true", "data" => "success", "message" => ""));
            die($output); 
        }
        
         return redirect('Ingram_Api/delivery_confirmation');   
    }
    
    
    
    public function save_purchase_inward() {
 
        $this->db->trans_begin();
        $date = $this->input->post('date');
        $entry_time = date('Y-m-d H:i:s');
        $created_by = $this->input->post('created_by');
        $idvendor = 13; 
        $vendor_state='MAHARASHTRA';
        $id_purchase_order = $this->input->post('id_purchase_order');        
        $idbranch = $this->input->post('idwarehouse');
        $y = date(date('Y', strtotime($date)), mktime(0, 0, 0, 3 + date('m', strtotime($date))));
        $y1 = $y + 1;
        $y2 = substr($y1,-2);
        $financial_year = 'IN/'.$y.'-'.$y2.'/'.$this->input->post('branch_code');
        
        $data = array(
            'date' => $date,
            'idvendor' => $idvendor,
            'idvendor_po' => $id_purchase_order,
            'vendor_state' => $vendor_state,
            'supplier_invoice_no' => $this->input->post('supplier_inv'),
            'vendor_invoice_date' => $this->input->post('inv_date'),
            'financial_year' => $financial_year,
            'idbranch' => $idbranch,
            'direct_inward' => $this->input->post('direct_inward'),
            'remark' => $this->input->post('remark'),
            'total_basic_amt' => $this->input->post('total_basic_amt'),
            'total_taxable_amt' => $this->input->post('total_taxable_amt'),
            'total_cgst_amt' => $this->input->post('total_cgst_amt'),
            'total_sgst_amt' => $this->input->post('total_sgst_amt'),
            'total_igst_amt' => $this->input->post('total_igst_amt'),
            'total_tax' => $this->input->post('total_tax'),
            'total_charges_amt' => $this->input->post('total_charges'),
            'total_discount_amt' => $this->input->post('total_discount'),
            'gross_amount' => $this->input->post('gross_total'),
            'overall_discount' => $this->input->post('overall_discount'),
            'final_amount' => $this->input->post('final_total'),
            'tcs_amount' => $this->input->post('tcs_amount'),
            'overall_amount' => $this->input->post('overall_amount'),
            'created_by' => $created_by,
            'entry_time' => $entry_time,
            'status' => 3,
        );
        
        $idinward = $this->Inward_model->save_inward($data);
        $product_id = explode(",",$this->input->post('modelid'));
        $upnestarray[] = array('nested'=>array());
        $imei_history[] = array('nest'=>array());
        for($i = 0; $i < count($product_id); $i++){
            $qty = $this->input->post('qty['.$i.']');
            $scanned_csv = '';
            if($this->input->post('scanned['.$i.']') || $this->input->post('scanned['.$i.']') != ''){
                $scanned_csv = $this->input->post('scanned['.$i.']');
            }            
            $inward_data[$i] = array(                
                'idinward' => $idinward,
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
            $idinward_data = $this->Inward_model->save_inward_data($inward_data[$i]);
                        
                $scanned = explode(",",$scanned_csv);
                for($j = 0; $j < count($scanned); $j++){
                    if($scanned[$j] != ''){
                        $inward_product[$j] = array(
                            'date' => $date,
                            'imei_no' => rtrim($scanned[$j],','),
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                            'idinward_data' => $idinward_data,
                            'idinward' => $idinward,
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
                        $this->Inward_model->save_inward_product($inward_product[$j]);
                        $this->Inward_model->update_model_variant_mrp($product_id[$i], $this->input->post('mrp['.$i.']'));
                        $inward_stock[$j] = array(
                            'date' => $date,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'imei_no' => $scanned[$j],
                            'idbranch' => $idbranch,
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'is_gst'   => $this->input->post('gstradio'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                        );
                        $this->Inward_model->save_stock($inward_stock[$j]);
                        
                        // update_variants_last_purchase_price
                        $last_purchase_price = array(
                            'last_purchase_price' => $this->input->post('total['.$i.']') / $qty,
                        );
                        $this->Inward_model->update_variants_last_purchase_price($product_id[$i], $last_purchase_price);
                        
                        $imei_history['nest'][]=array(
                            'imei_no' => $scanned[$j],
                            'entry_type' => 'Purchase Inward',
                            'entry_time' => $entry_time,
                            'date' => $date,
                            'idbranch' => $idbranch,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'idvariant' => $product_id[$i],
//                            'model_variant_full_name' => $this->input->post('product_name['.$i.']'),
                            'idimei_details_link' => 1, // Purchase return from imei_details_link table
                            'idlink' => $idinward,
                            'iduser' => $created_by,
                            
                        );
                    }
                }

        }
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        $podata = array(
          'status' => 4,
          'ingram_order_status' => 2, //inward
          'inward_date' => $entry_time
        );
        $this->Ingram_Model->update_purchase_order($id_purchase_order,$podata);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Product inward is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Product Inwarded');
        }
//        return redirect('Purchase/inward_details/'.$idinward);
        return redirect('Ingram_Api/pending_inwards');
    }
    
    
     public function ajax_get_purchase_order_data() {
        $status = $this->input->post('status');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $purchase_order = $this->Ingram_Model->ajax_get_purchase_order_data($status, $datefrom, $dateto);
        $i=1; 
        foreach ($purchase_order as $po){ ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $po->financial_year.'-'.$po->id_vendor_po ?></td>
                <td><?php echo $po->date ?></td>
                <td><?php echo $po->branch_name ?></td>
                <td><?php echo $po->vendor_name ?></td>                
                <td><?php if($po->status==0){ echo 'Pending'; }else{ echo 'Approved'; } ?></td>
                <td><center><a target="_blank" href="<?php echo base_url('Ingram_Api/process_order/'.$po->id_sale_token) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
            </tr>
        <?php $i++; }
    }
    
    public function ajax_get_branch_order_data() {
        $status = $this->input->post('status');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');         
        $idbranch = $this->session->userdata('idbranch');             
        
        
        $sale_token_data = $this->Ingram_Model->get_pending_sale_token_data($status,$idbranch, $datefrom, $dateto);
//        die(print_r($sale_token_data));
        $i=1; foreach($sale_token_data as $sale_token){ ?>
        <tr class="recon">
            <td><?php echo $i; ?></td>
            <td>
                <b>ODR-<?php echo $sale_token->id_sale_token ?> </b>
            </td>
            <td><?php echo date('d-m-Y', strtotime($sale_token->date)) ?></td>
            <td><?php echo $sale_token->user_name ?></td>
            <td><?php echo $sale_token->customer_fname.' '.$sale_token->customer_lname ?></td>
            <td><?php echo $sale_token->customer_contact ?></td>
            <td><?php echo $sale_token->basic_total ?></td>
            <td><?php echo $sale_token->discount_total ?></td>
            <td><?php echo $sale_token->final_total ?></td>            
            <td><?php 
                            if($sale_token->ingram_status==1){
                                echo "Pending for Approval";
                            }elseif($sale_token->ingram_status==2){
                               echo "Order Placed - In Process";
                            }else if($sale_token->ingram_status==3){
                                 echo "Rejected";
                            }elseif($sale_token->ingram_status==4){
                                echo "Picked for Dispatch at Ingram";
                            }elseif($sale_token->ingram_status==5){
                                echo "Packed and Dispatched";
                            }elseif($sale_token->ingram_status==6){
                                if($sale_token->deliver_at==1){
                                    echo "Received by Customer";
                                }else{
                                    echo "Received at Branch";
                                }
                            }elseif($sale_token->ingram_status==7){
                                echo "Returned/Declined by Customer";
                            }elseif($sale_token->ingram_status==8){
                                echo "Refund";
                            }elseif($sale_token->ingram_status==9){
                                echo "Deliverd to customer";
                            }
                            
                        ?></td>
                        <td><center><?php 
                            if($sale_token->ingram_status==3){   ?>                             
                                <button type="submit" id="receive_submit" class="btn btn-sm btn-danger waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"><i class="fa fa-sign-out"></i> Refund</button>                                
                            <?php }elseif($sale_token->ingram_status==7){   ?>                             
                                <button type="submit" id="receive_submit" class="btn btn-sm btn-danger waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"><i class="fa fa-sign-out"></i> Refund</button>                                
                            <?php }elseif($sale_token->ingram_status==6){                                                              
                                 if($sale_token->deliver_at==1){ 
                                    echo "-";
                                 }else{ ?>
                                    <button type="submit" id="receive_submit" class="btn btn-sm btn-danger waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"><i class="fa fa-sign-out"></i> Receive</button>                                
                                 <?php }
                               }else{
                                echo "-";
                            }
                        ?></center></td>
                
                <div class="cancel_block"></div>
            </td>
            <td><a href="<?php echo base_url()?>Ingram_Api/order_deatils/<?php echo $sale_token->id_sale_token ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
       <?php $i++; } 
    }
    
    public function ajax_get_branch_order_report() {
        $status = $this->input->post('status');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $idbranch = $this->input->post('idbranch');
//         die('<pre>'.print_r($_POST,1).'</pre>');
        
        $sale_token_data = $this->Ingram_Model->get_pending_sale_token_data($status,$idbranch, $datefrom, $dateto);
//        die(print_r($sale_token_data));
        $i=1; foreach($sale_token_data as $sale_token){ ?>
        <tr class="recon">
            <td><?php echo $i; ?></td>
            <td>
                <b>ODR-<?php echo $sale_token->id_sale_token ?> </b>
            </td>
           <td><?php echo date('d-m-Y', strtotime($sale_token->date)) ?></td>
            <td><?php echo $sale_token->branch_name ?></td>
            <td><?php echo $sale_token->user_name ?></td>
            <td><?php echo $sale_token->customer_fname.' '.$sale_token->customer_lname ?></td>
            <td><?php echo $sale_token->customer_contact ?></td>
             <td><?php echo $sale_token->sku ?></td>
             <td><?php echo $sale_token->part_number ?></td>
            <td><?php echo $sale_token->full_name ?></td>
            <td><?php echo $sale_token->qty?></td>
            <td><?php echo $sale_token->basic_total ?></td>
            <td><?php echo $sale_token->discount_total ?></td>
            <td><?php echo $sale_token->final_total ?></td>         
            <td><?php 
                            if($sale_token->ingram_status==1){
                                echo "Pending for Approval";
                            }elseif($sale_token->ingram_status==2){
                               echo "Order Placed - In Process";
                            }else if($sale_token->ingram_status==3){
                                 echo "Rejected";
                            }elseif($sale_token->ingram_status==4){
                                echo "Picked for Dispatch at Ingram";
                            }elseif($sale_token->ingram_status==5){
                                echo "Packed and Dispatched";
                            }elseif($sale_token->ingram_status==6){
                                if($sale_token->deliver_at==1){
                                    echo "Received by Customer";
                                }else{
                                    echo "Received at Branch";
                                }
                            }elseif($sale_token->ingram_status==7){
                                echo "Returned/Declined by Customer";
                            }elseif($sale_token->ingram_status==8){
                                echo "Refund";
                            }elseif($sale_token->ingram_status==9){
                                echo "Closed";
                            }
                            
                        ?></td>
          
                
                <div class="cancel_block"></div>
            </td>
            <td><a href="<?php echo base_url()?>Ingram_Api/order_deatils/<?php echo $sale_token->id_sale_token ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr>
       <?php $i++; } 
    }
    
    public function ajax_get_purchase_order_report() {
        $status = $this->input->post('status');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');        
        
        $purchase_order = $this->Ingram_Model->ajax_get_ingram_purchase_order_data($status, $datefrom, $dateto);
                 if(count($purchase_order)==0){?>
                <tr>
                    <td colspan="9" style="background: #ffffff;">                 
                        <center><img src="<?php echo base_url('assets/images/no-data-found.png') ?>" style="width: 50%" /></center>                    
                    </td>   
                        </tr>
                    <?php }else{ ?>
                    
                    <?php $i=1; foreach ($purchase_order as $po){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $po->date ?></td>     
                        <td><?php echo $po->financial_year.'-'.$po->id_vendor_po ?></td>
                        <td><?php echo $po->ingram_order_number ?></td>                        
                        <td><?php echo $po->sku ?></td>
                        <td><?php echo $po->oqty?></td>
                        <td><?php echo $po->qty?></td>                                                             
                        <td><?php 
                            if($po->status==1 && $po->ingram_order_status==1){
                                echo "Pending For Inward";
                            }elseif($po->status==1 && $po->ingram_order_status==2){
                                echo "Inwared";
                            }elseif($po->status==2 || $po->status==3){
                                echo "Rejected";
                            }
                        ?></td>
                        <td><a href="<?php echo base_url()?>Ingram_Api/po_details/<?php echo $po->id_vendor_po ?>" class="btn btn-info btn-floating waves-effect"><i class="fa fa-info-circle"></i></a></td>
                    </tr>
                    <?php $i++; } ?>                   
                
    <?php }  
    }
    public function submit_ingram_po() {
//          die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
                $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
                $access_token=$sku_data->access_token;
                $purchase_order = $this->Ingram_Model->ajax_get_purchaseorder_databy_idpo($this->input->post('id_vendor_po'));                     
//                die('<pre>'.print_r($purchase_order,1).'</pre>');
                $data=array();
                $data['customerOrderNumber']=$this->input->post('po_number');
                $data['billToAddressId']="000";        
                $data['notes']="";                        
                $data['shipToInfo']["addressId"]="202"; 
                
                $data["lines"]=array();
                $cnt=0;
                foreach ($purchase_order as $p_data){
                    $lines=array();                
                    $lines["customerLineNumber"]=$cnt+1;
                    $lines["ingramPartNumber"]=$p_data->vendor_sku;
                    $lines["quantity"]=$p_data->ordered_qty;                    
                    array_push($data["lines"], $lines);
                    $cnt++;
                }
                $data["additionalAttributes"]=array();
                
                $data["additionalAttributes"][0]["attributeName"]="allowDuplicateCustomerOrderNumber";
                $data["additionalAttributes"][0]["attributeValue"]="true";
                $data["additionalAttributes"][1]["attributeName"]="shipFromWarehouseId";
                $data["additionalAttributes"][1]["attributeValue"]="31";            
                $data['shipmentDetails']["carrierCode"]="ZO";
                
                die(print_r(json_encode($data)));
//                $result_data='{"customerOrderNumber":"IM/21-22/SHAH/1","billToAddressId":"000","orderSplit":false,"processedPartially":true,"purchaseOrderTotal":205478.0,"resellerInfo":{},"shipToInfo":{"addressId":"300","companyName":"S.S.COMMUNICATION &SERVICES PVT LTD","addressLine1":"C/O BRIGHTPOINT INDIA PVT.LTD","addressLine2":"NO.D-5,SHREE RAJLXMI LOGISTICS PARK","city":"VADAPE,BHIWANDI,THANE","state":"42","postalCode":"302","countryCode":"IN"},"orders":[{"numberOfLinesWithSuccess":1,"numberOfLinesWithError":1,"numberOfLinesWithWarning":0,"ingramOrderNumber":"40-66455","ingramOrderDate":"2021-09-15","notes":"","orderType":"S","orderTotal":205478.0,"freightCharges":0.0,"totalTax":31344.1,"currencyCode":"INR","lines":[{"subOrderNumber":"40-66455-11","ingramLineNumber":"001","customerLineNumber":"2","lineStatus":"In Progress","ingramPartNumber":"GD103X637M1","unitPrice":87066.95,"extendedUnitPrice":174133.9,"quantityOrdered":2,"quantityConfirmed":2,"quantityBackOrdered":0,"notes":"12.9\" IPAD PRO WI-FI 512GB SV  WRLS || -","shipmentDetails":{"carrierCode":"ZO","carrierName":"APPLE ON SUR","shipFromWarehouseId":"31"}}],"miscellaneousCharges":[{"subOrderNumber":"40-66455-11","chargeLineReference":"895","chargeDescription":"FREIGHT-OUT CHARGE","chargeAmount":0.0}],"links":[{"topic":"orders","href":"/resellers/v6/orders/40-66455","type":"GET"}],"rejectedLineItems":[{"customerLinenumber":"1","ingramPartNumber":"GD103AO59M1","customerPartNumber":"1","quantityOrdered":1,"rejectCode":"EW","rejectReason":"BR-CANT-SHIP    GD103AO59M1"}]}]}';
                $ingram_po_data= $this->Ingram_Model->OrderCreate_v6($data,$access_token);    
//                $ingram_po_data= json_decode($result_data,true);                
                
                $ingramOrderNumber="";
                $purchase_product=array();
                $purchase_po=array();
                $sodata=array();                
                if(isset($ingram_po_data['orders']) && $ingram_po_data['orders'][0]['numberOfLinesWithSuccess'] > 0){
                    $in_lines=$ingram_po_data['orders'][0]['lines'];                    
                    
                    foreach ($in_lines as $ln){                                 
                        $ingramPartNumber=$ln['ingramPartNumber'];
                        $key=multi_array_search($purchase_order, array("vendor_sku" => $ingramPartNumber));
                        $product_data=$purchase_order[$key[0]];
                        $purchase_product[] = array(
                        'id_vendor_po_product' => $product_data->id_vendor_po_product,
                        'remark' => $ln['lineStatus'],
                        'confirmed_qty' => $ln['quantityConfirmed'],
                        'sub_order_number' => $ln['subOrderNumber'],
                        'shipment_details' => json_encode($ln['shipmentDetails'])  
                    );
                    }     
                    
                 //status =  0=pending for approval,1=approved & submitted,2= rejected by checker,3 = rejected by vendor 
                    $purchase_po[] = array(
                        'id_vendor_po' => $purchase_order[0]->idvendor_po,
                        'ingram_order_number' => $ingram_po_data['orders'][0]['ingramOrderNumber'],
                        'status' => 1,
                        'ingram_order_status' => 1,
                        'remark' => 'Order Placed'
                    );
                    $sodata[] = array(
                        'id_sale_token' => $purchase_order[0]->id_sale_token,
                        'ingram_status' => 2,           
                      );    
                      
                    $this->session->set_flashdata('save_data', 'Purchase Order is created');
                }else{
                    $purchase_po[] = array(
                        'id_vendor_po' => $purchase_order[0]->idvendor_po,
                        'ingram_order_number' => NULL,
                        'status' => 3,
                        'ingram_order_status' => 6,
                        'remark' => 'Order Rejected'
                    );
                    $sodata[] = array(
                        'id_sale_token' => $purchase_order[0]->id_sale_token,
                        'ingram_status' => 3,           
                      );
                   $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');  
                } 
                if(isset($ingram_po_data['orders']) && isset($ingram_po_data['orders'][0]['rejectedLineItems'])){
                    $in_lines=$ingram_po_data['orders'][0]['rejectedLineItems'];
                    foreach ($in_lines as $ln){
                       $ingramPartNumber=$ln['ingramPartNumber'];
                        $key=multi_array_search($purchase_order, array("vendor_sku" => $ingramPartNumber));
                        $product_data=$purchase_order[$key[0]];
                        $purchase_product[] = array(
                        'id_vendor_po_product' => $product_data->id_vendor_po_product,
                        'remark' => $ln['rejectReason'],
                        'confirmed_qty' => 0,      
                        'sub_order_number' => NULL,
                        'shipment_details' => NULL,
                    );
                    }       
                    $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');
                }
//                die(print_r($purchase_product));                
                if(count($purchase_po)>0){
                    $this->Ingram_Model->update_vendor_po($purchase_po);
                }
                if(count($purchase_product)>0){
                            $this->Ingram_Model->update_vendor_products($purchase_product);                
                        }
                $this->Ingram_Model->update_sale_token($sodata);
                
                $this->db->trans_complete();
                 if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
//                    $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');
                    
                }else{
                    $this->db->trans_commit();
//                    $this->session->set_flashdata('save_data', 'Purchase Order is created');
                   
                }
                
                return redirect('Ingram_Api/order_report');
                
    }
    
    public function save_ingram_po() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
           
                $this->db->trans_begin();                
                $idvendor = 13; // Ingram Mumbai
                $idgodown= $this->stock_idgodown;
                $date = date('Y-m-d');
                $datetime = date('Y-m-d H:i:s');
                
               $remark = $this->input->post('remark');
               $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
               $access_token=$sku_data->access_token;
                $branch_data = $this->General_model->get_branch_byid($this->idwarehouse);
                $y = date('y', mktime(0, 0, 0, 3 + date('m')));
                $y1 = $y + 1;
                $financial_year = "IM/".$y."-".$y1."/".$branch_data->branch_code."/";
                $po = array(
                    'date' => $date,
                    'idbranch' => $this->idwarehouse,
                    'idwarehouse' => $this->idwarehouse,
                    'idvendor' => $idvendor ,
                    'created_by' => $this->input->post('created_by'),
                    'financial_year' => $financial_year,
                    'remark' => $this->input->post('remark'),                    
                    'status' => 0,
                    'entry_time' => date('Y-m-d H:i:s'),
                );
                $idpo = $this->Ingram_Model->save_vendor_po($po);                
                $data=array();
                $data['customerOrderNumber']=$financial_year.$idpo;
                $data['billToAddressId']="000";        
                $data['notes']="";                        
                $data['shipToInfo']["addressId"]="202";
                
                $data["lines"]=array();
                $skuz = $this->input->post('sku'); 
                $qtyz = $this->input->post('qty'); 
                $skutypez = $this->input->post('skutype'); 
                $idvariantz = $this->input->post('idvariant'); 
                $cnt=0;
                $purchaseproduct=array();
                foreach ($skuz as $sku){
                    $lines=array();                
                    $lines["customerLineNumber"]=$cnt+1;
                    $lines["ingramPartNumber"]=$sku;
                    $lines["quantity"]=$qtyz[$cnt];
                    
                    array_push($data["lines"], $lines);
                    $purchaseproduct[] = array(                        
                        'idvariant' => $idvariantz[$cnt],
                        'ordered_qty' => $qtyz[$cnt],
                        'idsku_type' => $skutypez[$cnt],
                        'vendor_sku' => $sku,
                        'idgodown' => $idgodown,
                        'idvendor_po' => $idpo,
                    );
                    $cnt++;
                }                      
                $data["additionalAttributes"]=array();
                
                $data["additionalAttributes"][0]["attributeName"]="allowDuplicateCustomerOrderNumber";
                $data["additionalAttributes"][0]["attributeValue"]="true";
                $data["additionalAttributes"][1]["attributeName"]="shipFromWarehouseId";
                $data["additionalAttributes"][1]["attributeValue"]="31";            
                $data['shipmentDetails']["carrierCode"]="ZO";
                
//                die(print_r(json_encode($data)));
//                $result_data='{
//    "customerOrderNumber": "IM/22-23/IMSS/3",
//    "billToAddressId": "000",
//    "orderSplit": false,
//    "processedPartially": false,
//    "purchaseOrderTotal": 102739.01,
//    "resellerInfo": {},
//    "shipToInfo": {
//        "addressId": "300",
//        "companyName": "S.S.COMMUNICATION &SERVICES PVT LTD",
//        "addressLine1": "C/O BRIGHTPOINT INDIA PVT.LTD",
//        "addressLine2": "NO.D-5,SHREE RAJLXMI LOGISTICS PARK",
//        "city": "VADAPE,BHIWANDI,THANE",
//        "state": "42",
//        "postalCode": "302",
//        "countryCode": "IN"
//    },
//    "orders": [
//        {
//            "numberOfLinesWithSuccess": 1,
//            "numberOfLinesWithError": 0,
//            "numberOfLinesWithWarning": 0,
//            "ingramOrderNumber": "40-66499",
//            "ingramOrderDate": "2021-10-09",
//            "notes": "",
//            "orderType": "S",
//            "orderTotal": 102739.01,
//            "freightCharges": 0.0,
//            "totalTax": 15672.06,
//            "currencyCode": "INR",
//            "lines": [
//                {
//                    "subOrderNumber": "40-66499-11",
//                    "ingramLineNumber": "001",
//                    "customerLineNumber": "1",
//                    "lineStatus": "In Progress",
//                    "ingramPartNumber": "GD103X637M1",
//                    "unitPrice": 87066.95,
//                    "extendedUnitPrice": 87066.95,
//                    "quantityOrdered": 1,
//                    "quantityConfirmed": 1,
//                    "quantityBackOrdered": 0,
//                    "notes": "12.9\" IPAD PRO WI-FI 512GB SV  WRLS || -",
//                    "shipmentDetails": {
//                        "carrierCode": "ZO",
//                        "carrierName": "APPLE ON SUR",
//                        "shipFromWarehouseId": "31"
//                    }
//                }
//            ],
//            "miscellaneousCharges": [
//                {
//                    "subOrderNumber": "40-66499-11",
//                    "chargeLineReference": "895",
//                    "chargeDescription": "FREIGHT-OUT CHARGE",
//                    "chargeAmount": 0.0
//                }
//            ],
//            "links": [
//                {
//                    "topic": "orders",
//                    "href": "/resellers/v6/orders/40-66499",
//                    "type": "GET"
//                }
//            ]
//        }
//    ]
//}';
                $ingram_po_data= $this->Ingram_Model->OrderCreate_v6($data,$access_token); 
//                $ingram_po_data= json_decode($result_data,true);                
                
                if(isset($ingram_po_data['fault']) || $ingram_po_data['fault']['faultstring']!=''){
                        $this->Ingram_Model->getToken();    
                        $this->session->set_flashdata('save_data', 'Access token has been expired... Please try again.');
                        $this->db->trans_complete();
                        $this->db->trans_rollback();
                        return redirect('Ingram_Api/ingram_po');
                   } else{
                
                $ingramOrderNumber="";
                $purchase_product=array();
                $purchase_po=array();                
                if(isset($ingram_po_data['orders']) && $ingram_po_data['orders'][0]['numberOfLinesWithSuccess'] > 0){
                    $in_lines=$ingram_po_data['orders'][0]['lines'];                    
                    
                    foreach ($in_lines as $ln){                                 
                        $ingramPartNumber=$ln['ingramPartNumber'];
                        $key=multi_arraysearch($purchaseproduct, array("vendor_sku" => $ingramPartNumber));
                        $product_data=$purchaseproduct[$key[0]];
                        $purchase_product[] = array(
                        'idvendor_po' => $idpo,
                        'idvariant' => $product_data['idvariant'],
                        'ordered_qty' => $product_data['ordered_qty'],
                        'idsku_type' => $product_data['idsku_type'],
                        'idgodown' => $product_data['idgodown'],                            
                        'vendor_sku' => $ingramPartNumber,
                        'remark' => $ln['lineStatus'],
                        'confirmed_qty' => $ln['quantityConfirmed'],
                        'sub_order_number' => $ln['subOrderNumber'],
                        'shipment_details' => json_encode($ln['shipmentDetails'])  
                    );
                    }                         
                    $purchase_po[] = array(
                        'id_vendor_po' => $idpo,
                        'ingram_order_number' => $ingram_po_data['orders'][0]['ingramOrderNumber'],
                        'status' => 1,
                        'ingram_order_status' => 1,
                        'remark' => 'Order Placed'
                    );                    
                    $this->session->set_flashdata('save_data', 'Purchase Order is created');
                }else{
                    $purchase_po[] = array(
                        'id_vendor_po' => $idpo,
                        'ingram_order_number' => NULL,
                        'status' => 3,
                        'ingram_order_status' => 6,
                        'remark' => 'Order Rejected'
                    );
                    
                   $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');  
                } 
                
                if(isset($ingram_po_data['orders']) && isset($ingram_po_data['orders'][0]['rejectedLineItems'])){
                    $in_lines=$ingram_po_data['orders'][0]['rejectedLineItems'];
                    foreach ($in_lines as $ln){
                       $ingramPartNumber=$ln['ingramPartNumber'];
                       $key=multi_arraysearch($purchaseproduct, array("vendor_sku" => $ingramPartNumber));
                        $product_data=$purchaseproduct[$key[0]];
                        $purchase_product[] = array(
                        'idvendor_po' => $idpo,
                        'idvariant' => $product_data['idvariant'],
                        'ordered_qty' => $product_data['ordered_qty'],
                        'idsku_type' => $product_data['idsku_type'],
                        'idgodown' => $product_data['idgodown'],   
                        'vendor_sku' => $ingramPartNumber,
                        'remark' => $ln['rejectReason'],
                        'confirmed_qty' => 0,      
                        'sub_order_number' => NULL,
                        'shipment_details' => NULL,
                    );
                    }       
                    $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');
                }
                if(count($purchase_po)>0){
                    $this->Ingram_Model->update_vendor_po($purchase_po);
                }
                if(count($purchase_product)>0){
                   $this->Ingram_Model->save_vendor_po_products($purchase_product);                
                }elseif(count($purchaseproduct)>0){
                   $this->Ingram_Model->save_vendor_po_products( $purchaseproduct);                
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
//                    $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');
                }else{
                    $this->db->trans_commit();
//                    $this->session->set_flashdata('save_data', 'Purchase Order is created');
                }        
                
                return redirect('Ingram_Api/po_details/'.$idpo);
    }
        
        }
    
    public function po_details($idpo) {
        $q['tab_active'] = '';
        $q['purchase_order'] = $this->Ingram_Model->get_purchase_order_byid($idpo);
        $q['purchase_order_product'] = $this->Ingram_Model->get_purchase_order_product_byid($idpo);
        $this->load->view('ingram/po_details',$q);
    }
    
    public function create_ingram_po() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
           
                $this->db->trans_begin();
                $idbranch = $this->input->post('idbranch');
                $idvendor = 13; // Ingram Mumbai
                $idgodown=$this->stock_idgodown;
                $date = date('Y-m-d');
                $datetime = date('Y-m-d H:i:s');
                $idstate = $this->input->post('idstate');
                $idcustomer = $this->input->post('idcustomer');
                $cust_fname = $this->input->post('cust_fname');
                $cust_lname = $this->input->post('cust_lname');
                $cust_idstate = $this->input->post('cust_idstate');
                $cust_pincode = $this->input->post('cust_pincode');
               
                $gst_type = 0; //cgst
                if($idstate != $cust_idstate){
                    $gst_type = 1; //igst
                }
               $remark = $this->input->post('remark');
               $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
               $access_token=$sku_data->access_token;
//                $branch_data = $this->General_model->get_branch_byid($idbranch);
                $y = date('y', mktime(0, 0, 0, 3 + date('m')));
                $y1 = $y + 1;
                $financial_year = "IM/".$y."-".$y1."/ODR/";                
                
//                $po = array(
//                    'date' => $date,
//                    'idbranch' => $idbranch,
//                    'idwarehouse' => $this->idwarehouse,
//                    'idvendor' => $idvendor ,
//                    'created_by' => $this->input->post('created_by'),
//                    'financial_year' => $financial_year,
//                    'remark' => $this->input->post('remark'),                    
//                    'status' => 0,
//                    'order_type' => 1,
//                    'entry_time' => date('Y-m-d H:i:s'),
//                );
//                $idpo = $this->Ingram_Model->save_vendor_po($po);
                
//                $data=array();
//                $data['customerOrderNumber']=$financial_year.$idpo;
//                $data['billToAddressId']="000";        
//                $data['notes']="";                        
//                $data['shipToInfo']["addressId"]="300";
//                
//                $data["lines"]=array();
//                $skuz = $this->input->post('sku'); 
//                $qtyz = $this->input->post('qty'); 
//                $skutypez = $this->input->post('skutype'); 
//                $idvariantz = $this->input->post('idvariant'); 
//                $cnt=0;
//                $purchase_product=array();
//                foreach ($skuz as $sku){
//                    $lines=array();                
//                    $lines["customerLineNumber"]=$cnt+1;
//                    $lines["ingramPartNumber"]=$sku;
//                    $lines["quantity"]=$qtyz[$cnt];
//                    
//                    array_push($data["lines"], $lines);
//                    $purchase_product[] = array(
//                        'idvendor_po' => $idpo,
//                        'idvariant' => $idvariantz[$cnt],
//                        'ordered_qty' => $qtyz[$cnt],
//                        'idsku_type' => $skutypez[$cnt],
//                        'vendor_sku' => $sku,
//                        'idgodown' => $idgodown
//                    );
//                    $cnt++;
//                }
//                $data["additionalAttributes"]=array();
//                
//                $data["additionalAttributes"][0]["attributeName"]="allowDuplicateCustomerOrderNumber";
//                $data["additionalAttributes"][0]["attributeValue"]="true";
//                $data["additionalAttributes"][1]["attributeName"]="shipFromWarehouseId";
//                $data["additionalAttributes"][1]["attributeValue"]="31";            
//                $data['shipmentDetails']["carrierCode"]="ZO";
                
//                $ingram_po_data= $this->Ingram_Model->OrderCreate_v6($data,$access_token);      
                
//                $ingramOrderNumber="";
//                if(isset($ingram_po_data['orders']) && $ingram_po_data['orders'][0]['numberOfLinesWithSuccess'] > 0){
//                    $ingramOrderNumber=$ingram_po_data['orders'][0]['ingramOrderNumber'];
//                    $in_lines=$ingram_po_data['orders'][0]['lines'];
//                    
//                    
//                    
//                    foreach ($skuz as $sku){
//                        $key=multi_array_search($in_lines, array("ingramPartNumber" => $sku));
//                        die(print_r($in_lines[$key[0]]));
//                        
//                        $cnt++;
//                }
//                    
//                }
                
                $dataa = array(
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'idsalesperson' => $this->input->post('idsalesperson'),
                    'basic_total' => $this->input->post('gross_total'),
                    'discount_total' => $this->input->post('final_discount'),
                    'final_total' => $this->input->post('final_total'),
                    'gst_type' => $gst_type,
                    'created_by' => $this->input->post('created_by'),
                    'entry_time' => $datetime,
//                    'token_uid' => $financial_year.$idpo,
                    'corporate_sale' => 0,
                    'ingram_status' => 2, // 1 - Need Approval, 2 - Direct Approved
                    'status' => 3,
                    'deliver_at' => $this->input->post('deliver_at'),
                );
                $idsaletoken = $this->Api_Model->save_sale_token($dataa);
                $sodata[] = array(
                    'id_sale_token' => $idsaletoken,
                     'token_uid' => $financial_year.$idsaletoken, //packed          
                );    
                $this->Ingram_Model->update_sale_token($sodata);
                $podata = array(
                    'idsaletoken'  => $idsaletoken,
                    'date'  => $date,
                    'approved_date' => $datetime, // if ingram_status = 1  then NULL else -  ingram_status = 2 then $datetime
                  );    
                  $this->Ingram_Model->save_ingram_order_history($podata);
//                $this->Ingram_Model->save_vendor_po_products($purchase_product);
                
                // Payment
                $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
                $credittype = $this->input->post('credit_type');
                $amount = $this->input->post('amount');
                $payment_type = $this->input->post('payment_type');
                $tranxid = $this->input->post('tranxid');
                $headattr = $this->input->post('headattr');
                $vin=array();
                foreach ($headattr as $idpayment_head => $attributedata){
                    $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
                    $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
                    $attr_value=array();
                    for($m=0;$m<$mode_count;$m++){
                        foreach ($payment_attribute as $attr){
                            $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
                        }
                    }
                    for($j=0; $j < count($idpaymenthead); $j++){
                        if($idpaymenthead[$j] == $idpayment_head){
                            $vin[$j]=$attr_value;
                        }
                    }
                }
                $parr=array();
                
                for($j=0; $j < count($idpaymenthead); $j++){
                    $received_amount=0;$pending_amt=$amount[$j];$received_entry_time=NULL;$payment_receive=0;
                    if($idpaymenthead[$j] == 1){
                        $received_amount = $amount[$j];
                        $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                        $srpayment = array(
                            'date' => $date,
                            'inv_no' => $financial_year.$idsaletoken,
                            'entry_type' => 1,
                            'idbranch' => $idbranch,
                            'idtable' => $idsaletoken,
                            'table_name' => 'ingram_po_booking',
                            'amount' => $received_amount,
                        );
                        $this->Sale_model->save_daybook_cash_payment($srpayment);
                    }
                    
                    $payment = array(
                        'date' => $date,
                        'idsaletoken' => $idsaletoken,
                        'amount' => $amount[$j],        
                        'idpayment_head' => $idpaymenthead[$j],
                        'idpayment_mode' => $payment_type[$j],
                        'transaction_id' => $tranxid[$j],
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'created_by' => $this->input->post('created_by'),
                        'entry_time' => $datetime,
                        
                    );
                    if(isset($vin[$j])>0){
                        $payment = array_merge($payment, $vin[$j]); 
                    }
        
                    $id_sale_payment = $this->Api_Model->save_sale_token_payment($payment);
                    
                    if($credittype[$j] == 0){
                        $npayment = array(                            
                            'inv_no' => $financial_year.$idsaletoken,                            
                            'date' => $date,
                            'idcustomer' => $idcustomer,
                            'idbranch' => $idbranch,
                            'amount' => $amount[$j],
                            'idpayment_head' => $idpaymenthead[$j],
                            'idpayment_mode' => $payment_type[$j],
                            'transaction_id' => $tranxid[$j],
                            'created_by' => $this->input->post('created_by'),
                            'entry_time' => $datetime,
                            'received_amount' => $received_amount,
                            'pending_amt' => $pending_amt,
                            'received_entry_time'=>$received_entry_time,
                            'payment_receive' => $payment_receive,
                            'idsale_payment' =>$id_sale_payment
                        );
                        if(isset($vin[$j])>0){
                            $npayment = array_merge($npayment, $vin[$j]); 
                        }
                        $this->Sale_model->save_payment_reconciliation($npayment);
                    }
                    
                }
                
                //Sale_product
                $idtype = $this->input->post('idtype');
                $idcategory = $this->input->post('idcategory');
                $idbrand = $this->input->post('idbrand');
                $idmodel = $this->input->post('idmodel');
                $idvariant = $this->input->post('idvariant');                
                $skutype = $this->input->post('skutype');
                $product_name = $this->input->post('product_name');                
                $price = $this->input->post('price');
                $basic = $this->input->post('basic');
                $discount_amt = $this->input->post('discount_amt');
                $total_amt = $this->input->post('total_amt');
                $landing = $this->input->post('landing');
                $online_price = $this->input->post('online_price');
                $mrp = $this->input->post('mrp');
                $mop = $this->input->post('mop');
                $nlc_price = $this->input->post('nlc_price');
                
                $salesman_price = $this->input->post('salesman_price');
                $qty = $this->input->post('qty');
                $rowid = $this->input->post('rowid');
                $is_gst = $this->input->post('is_gst');
               
                $hsn = $this->input->post('hsn'); 
                $is_mop = $this->input->post('is_mop'); // price on invoice
                $sale_type = $this->input->post('sale_type'); // 0=Normal,1=PurchaseFirst,2=SaleFirst
                
                for($i = 0; $i < count($idvariant); $i++){
                    
                    $sqty = $this->Ingram_Model->ajax_get_variant_byid_branch_godown($idvariant[$i], $this->idwarehouse,$idgodown);
                    $bqty = $this->Ingram_Model->ajax_get_booked_qty($idvariant[$i], $this->idwarehouse);        
                    $avail_qty = 0;
                    if ($sqty->avail_qty != NULL) {   
                        $avail_qty = $sqty->avail_qty;
                        if($bqty->booked_qty!=NULL){
                            $avail_qty = ($sqty->avail_qty)-($bqty->booked_qty);
                        }
                    }
                    if($avail_qty>=$qty[$i]){
                    
                    for($ii = 0; $ii < $qty[$i]; $ii++){
                        $cgst = 0; $sgst = 0; $igst = 0;
                        if($gst_type == 1){
                            $igst = $this->input->post('igst['.$i.']');
                        }else{
                            $cgst = $this->input->post('cgst['.$i.']');
                            $sgst = $this->input->post('sgst['.$i.']');
                        }
                        $sale_product= array(
                            'date' => $date,
                            'idsaletoken' => $idsaletoken,
                            'idmodel' => $idmodel[$i],
                            'idvariant' => $idvariant[$i],                        
                            'hsn' => $hsn[$i],
                            'idskutype' => $skutype[$i],
                            'idgodown' => $idgodown,
                            'idproductcategory' => $idtype[$i],
                            'idcategory' => $idcategory[$i],
                            'idbrand' => $idbrand[$i],
                            'product_name' => $product_name[$i],
                            'price' => $price[$i],
                            'landing' => $landing[$i],
                            'mrp' => $mrp[$i],
                            'online_price' => $online_price[$i],
                            'mop' => $mop[$i],
                            'nlc_price' => $nlc_price[$i],                        
                            'salesman_price' => $salesman_price[$i],                        
                            'qty' => 1,
                            'idbranch' => $idbranch,
                            'discount_amt' => $discount_amt[$i],
                            'is_gst' => $is_gst[$i],
                            'is_mop' => $is_mop[$i],
                            'basic' => ($basic[$i]/$qty[$i]),
                            'idvendor' => $idvendor,
                            'sale_type' => $sale_type[$i],
                            'cgst_per' => $cgst,
                            'sgst_per' => $sgst,
                            'igst_per' => $igst,
                            'total_amount' => ($total_amt[$i]/$qty[$i]),
                            'entry_time' => $datetime,                        
                        );                     
                        $idsaleproduct = $this->Api_Model->save_sale_token_product($sale_product);
                    }
                    }else{
                        $this->db->trans_complete();                
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('save_data', 'Order is aborted. Out of Stock'); 
                        return redirect('Ingram_Api/ingram_purchase');
                        break;                        
                    }
                
                }


                // BFL integration
                if($this->input->post('bfl_do_id')){
                    $bfl_data = array(
                        'do_id' => $this->input->post('bfl_do_id'),
                        'idsale' => $idsaletoken,
                        'idsale_product' => $idsaleproduct,
                        'bfl_brand' => $this->input->post('bfl_brand'),
                        'bfl_model' => $this->input->post('bfl_model'),
                        'bfl_srno' => $this->input->post('bfl_srno'),
                        'idcustomer' => $idcustomer,
                        'mobile' => $this->input->post('mobile'),
                        'customer_name' => $this->input->post('bfl_customer'),
                        'customer_gst' => $this->input->post('gst_no'),
                        'scheme_code' => $this->input->post('scheme_code'),
                        'scheme' => $this->input->post('scheme'),
                        'mop' => $this->input->post('bfl_mop'),
                        'downpayment' => $this->input->post('bfl_downpayment'),
                        'loan' => $this->input->post('bfl_loan'),
                        'emi_amount' => $this->input->post('bfl_emi_amount'),
                        'tenure' => $this->input->post('bfl_tenure'),
                        'bfl_remark' => $this->input->post('bfl_remark'),
                        'entry_time' => $datetime,
                    );
                    $this->Sale_model->save_bfl($bfl_data);			
                }
                
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('save_data', 'Order is aborted. Try again with same details');
                }else{
                    $this->db->trans_commit();
                    $this->session->set_flashdata('save_data', 'Purchase Order is created');
                }
                    $this->session->set_userdata('idsale_url','order_print/'.$idsaletoken);
                    return redirect('Ingram_Api/order_print/'.$idsaletoken);
        
        }
    public function order_print($idsaletoken) {
        $q['tab_active'] = '';
        $q['sale_data'] = $this->Ingram_Model->get_saletoken_byid($idsaletoken);
        $q['sale_product'] = $this->Api_Model->get_saletoken_product_byid_group($idsaletoken);       
        $q['sale_payment'] = $this->Api_Model->get_saletoken_payment_byid($idsaletoken);
        $this->load->view('ingram/order_print', $q);        
    }
    public function process_order($idsaletoken) {
        $q['tab_active'] = '';
        $q['sale_data'] = $this->Ingram_Model->get_saletoken_byid($idsaletoken);
                         
            $q['sale_product'] = $this->Api_Model->get_saletoken_product_byid_group($idsaletoken);       
            $q['sale_payment'] = $this->Api_Model->get_saletoken_payment_byid($idsaletoken);    
            $q['sale_reconciliation'] = $this->Api_Model->get_sale_reconciliation_byinv($q['sale_data'][0]->token_uid);
            $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();        
            $this->load->view('ingram/process_order', $q);
         
    }
    
    public function order_deatils($idsaletoken) {
        $q['tab_active'] = '';
        $q['sale_data'] = $this->Ingram_Model->get_saletoken_byid($idsaletoken);
        $q['sale_product'] = $this->Api_Model->get_saletoken_product_byid_group($idsaletoken);       
        $q['sale_payment'] = $this->Api_Model->get_saletoken_payment_byid($idsaletoken);    
        $q['sale_reconciliation'] = $this->Api_Model->get_sale_reconciliation_byinv($q['sale_data'][0]->token_uid);
        $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();
        
        $this->load->view('ingram/order_details', $q);
    }
    
    public function ajax_check_valid_barcode() {         
        
        $imei = $this->input->post('val');
        $idvariant = $this->input->post('idvariant');
        $branch = $this->input->post('idbranch');
        $idgodown = $this->input->post('idgodown');              
        if($this->Stock_model->ajax_check_valid_barcode($imei, $idvariant, $branch,$idgodown)){
            $output = json_encode(array("error" => false, "data" => "Success", "message" => ""));
            
        }else{
            $output = json_encode(array("error" => true, "data" => "Fail", "message" => ""));
        }        
        die($output);
    }
    
    public function ingram_apb_stock_report() {             
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');                           
        $q['brand_data'] = $this->General_model->get_brand_byid(23);   
        $q['idgodown'] = $this->stock_idgodown;
       // $idwarehouse=$this->session->userdata('idbranch');            
        $q['branch_data'] = $this->General_model->get_branch_array_byid($this->idwarehouse);   
        $q['product_category'] = $this->General_model->get_product_category_data(); 
            
        $this->load->view('ingram/w_stock_report', $q);
    }
    public function save_picked(){
//        die('<pre>'.print_r($_POST,1).'</pre>');        
        $idbranch=$this->input->post('idbranch');
        $idwarehouse=$this->input->post('idwarehouse');                        
        $idvariants=$this->input->post('idvariant');            
        $idgodown=$this->input->post('id_godown');         
        $scanned=$this->input->post('scanned');
        $count= count($idvariants);              
        $id_sale_token=$this->input->post('id_sale_token');           
        $id_saletokenproduct=$this->input->post('id_saletokenproduct');           
        
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');  
        $remark=$this->input->post('remark'); 
        $this->db->trans_begin();     
        
        $sale_data = $this->Ingram_Model->get_saletoken_byid($id_sale_token);
        
       
        $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);                
        $invid = $invoice_no->invoice_no + 1; 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);  
        $financial_year = "IM/".$y."-".$y1."/ODR/";  
        $customer_data = $this->Sale_model->get_customer_byid($sale_data[0]->idcustomer)[0];
        $idstate = $invoice_no->idstate;        
        $idcustomer = $customer_data->id_customer;
        $cust_idstate = $customer_data->idstate;
        $cust_fname = $customer_data->customer_fname;
        $cust_lname = $customer_data->customer_lname;                
        $cust_pincode = $customer_data->customer_pincode;
        $gst_type = 0; //cgst
        if($idstate != $cust_idstate){
            $gst_type = 1; //igst
        }        
        $data = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_idstate' => $cust_idstate,
                    'customer_pincode' => $cust_pincode,
                    'customer_contact' => $customer_data->customer_contact,
                    'customer_address' => $customer_data->customer_address,
                    'customer_gst' => $customer_data->customer_gst,
                    'idsalesperson' => $iduser,
                    'basic_total' => 0,
                    'discount_total' => 0,
                    'final_total' => 0,                    
                    'gst_type' => $gst_type,
                    'created_by' => $iduser,
                    'remark' => $this->input->post('remark'),
                    'entry_time' => $datetime,
                    'dcprint' => 0,
                    'token_uid' => $financial_year.$id_sale_token,
                    'idsaletoken' => $id_sale_token,
                    'corporate_sale' => 1,
                );
                $idsale = $this->Sale_model->save_sale($data);                                
              /*  foreach ($sale_payment as $pay){
                    $other_attr=array();
                    $other_attr['inv_no'] = $inv_no;
                    $other_attr['idsale'] = $idsale;
                    $other_attr['entry_time'] = $datetime;
                    $other_attr['corporate_sale'] = 1;                    
                    foreach ($pay as $key=>$value){
                        if($key=='id_saletokenpayment' || $key=='idsaletoken' || $key=='entry_time'){
                            
                        }else{
                            $other_attr[$key] = $value;
                        }
                    }                                
                    $id_sale_payment = $this->Sale_model->save_sale_payment($other_attr);
                    
                    $payment_re[] = array(
                        'idsale_payment' => $pay->id_saletokenpayment,
                        'idsale_payment' => $id_sale_payment,
                        'inv_no' => $inv_no,      
                        'idsale' => $idsale,
                    );                    
                    }
                    $this->Ingram_Model->update_sale_payment($payment_re,$po_number);*/                        
        $stock_array=array();
        $imei_history=array();
        $token_product=array(); 
        $vendor_product=array();    
        $update_stock=array();
        for($i=0;$i<$count;$i++){           
               $imeis = explode(',', $scanned[$i]);
//               $idsaletokenproducts = explode(',', $stkids[$i]);
               
//               $vendor_product[] = array(
//                        'id_vendor_po_product'=> $id_vendor_po_product[$i],
//                        'imei_nos' => $scanned[$i],
//                    );
               
               for ($j=0;$j < count($imeis)-1;$j++){                
                  $token_product[] = array(
                        'id_saletokenproduct'=> $id_saletokenproduct[$i],
                        'imei_no' => $imeis[$j],
                        'inv_no' => $inv_no,                        
                    );                   
                   $stock_array[] = array(
                            'imei_no' => $imeis[$j],
                            'outward' => 1,
                            'outward_dc' => $id_saletokenproduct[$i],
                            'outward_time' => $datetime,
                            'outward_by' => $iduser,
                            'idbranch' => 0,
                            'idgodown' => $idgodown[$i],
                            'temp_idbranch' => $idwarehouse,
                            'transfer_from' => $idbranch,
                            'outward_remark' => $remark,
                        );   
                   $imei_history[]=array(
                        'imei_no' =>$imeis[$j],
                        'entry_type' => 'Pick at Ingram',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $idwarehouse,
                        'idgodown' => $idgodown[$i],
                        'idvariant' => $idvariants[$i],
                        'idimei_details_link' => 5, // Outward from imei_details_link table
                        'iduser' => $iduser,
                        'idlink' => $financial_year.$id_sale_token,
                       'transfer_from' => $idwarehouse
                    );                   
                }            
        } 
        
            if (count($imei_history) > 0) {
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $invoice_data = array('invoice_no' => $invid);
        $this->General_model->edit_db_branch($idbranch, $invoice_data);
//        if(count($vendor_product)>0){ 
//            $this->Ingram_Model->update_vendor_products($vendor_product);                 
//        }
        $podata = array('picked_date' => $datetime);
        $this->Ingram_Model->update_ingram_order_history($id_sale_token, $podata);
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 4, //4 - Pick and Verif                
        );
        $this->Ingram_Model->update_sale_token($sodata);
        $p_odata = array('picked_date' => $datetime);
        $this->Ingram_Model->update_ingram_order_history($id_sale_token, $p_odata);
        if (count($token_product) > 0) {
            $this->Ingram_Model->update_sale_token_product($token_product);
        }
        if (count($stock_array) > 0) {
            $this->Outward_model->update_batch_stock_byimei($stock_array);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
            die($output);
        } else { 
            $this->db->trans_commit();
            $output = json_encode(array("result" => "false", "data" => "success", "message" => "$id_sale_token"));
            die($output);
        }
         
        
    }
    
    public function po_invoice_print($idsaletoken) {
        $q['tab_active'] = '';
        $q['branch'] = $this->General_model->get_branch_byid($this->idwarehouse);
        
        $q['sale_data'] = $this->Ingram_Model->get_saletoken_byid($idsaletoken);
        
        $q['saledata'] = $this->Ingram_Model->get_sale_by_tokenid($idsaletoken);
//        die(print_r($q['saledata']));
        $q['sale_product'] = $this->Api_Model->get_saletoken_product_byid($idsaletoken);       
        $q['sale_payment'] = $this->Api_Model->get_saletoken_payment_byid($idsaletoken);        
        $this->load->view('ingram/po_token_print', $q);        
    }
    
    public function reject_branch_order(){
       
         
        $reason = $this->input->post('reject_reason');  
        $id_sale_token = $this->input->post('id_sale_token');  
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 3,
            'cancel_remark' => $reason
        );    
        $this->Ingram_Model->update_sale_token($sodata);        
        $datetime = date('Y-m-d H:i:s');
        $p_odata = array( 'reject_reason'  => $reason,'reject_date' => $datetime );   
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);
       
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to Reject! Please try again.');
            return redirect('Ingram_Api/process_order/'.$id_sale_token);
        } else { 
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Order rejcted successfully!');
            return redirect('Ingram_Api/order_deatils/'.$id_sale_token);
        }
            
            
        
    }
    
    public function submit_ingram_order(){
       
        $this->db->trans_begin();
        $id_sale_token = $this->input->post('id_sale_token');  
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 2,
        );    
        $this->Ingram_Model->update_sale_token($sodata);
       $datetime = date('Y-m-d H:i:s');
        $p_odata = array( 'approved_date'  => $datetime );   
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to Reject! Please try again.');
            return redirect('Ingram_Api/process_order/'.$id_sale_token);
        } else { 
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Order rejcted successfully!');
            return redirect('Ingram_Api/order_deatils/'.$id_sale_token);
        }
    }
    
    public function receive_order(){
        $this->db->trans_begin();        
        $receiveddate = $this->input->post('receiveddate');  
        $id_sale_token = $this->input->post('id_sale_token');                  
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 6            
        );    
        $this->Ingram_Model->update_sale_token($sodata);
        $p_odata = array( 'received_date'  => $receiveddate );   
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fali to receive the order.. Try again!');
            return redirect('Ingram_Api/delivery_confirmation');
        } else { 
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Order maked as comleted!');
            return redirect('Ingram_Api/completed_orders');
        }         
        
    }
    
    public function receive_order_atbranch(){
        $this->db->trans_begin();        
        $receiveddate = $this->input->post('receiveddate');  
        $receive_branch_remark = $this->input->post('receive_branch_remark');  
        $id_sale_token = $this->input->post('id_sale_token');                  
        $sodata[] = array(
            'id_sale_token' => $id_sale_token,
            'ingram_status' => 9            
        );    
        $this->Ingram_Model->update_sale_token($sodata);
        $p_odata = array( 'received_date_branch'  => $receiveddate, 'receive_branch_remark' => $receive_branch_remark );   
        $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fali to receive the order.. Try again!');            
        } else { 
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Order maked as Received!');            
        }         
        return redirect('Ingram_Api/my_ingram_order');
        
    }
    
    public function update_ewaybill() {        
        $q['tab_active'] = '';  
        $datefrom=''; $dateto=''; $ingram_status=4;$idbranch=0;     
        $q['purchase_order'] = $this->Ingram_Model->get_pending_sale_token($ingram_status, $idbranch,$datefrom, $dateto); 
        $this->load->view('ingram/update_ewaybill',$q);
    }
    
    public function save_eway_bill() {        
            $id_sale_token=$this->input->post('id_sale_token');
            $path = NULL;            
            if (!file_exists('assets/ewaybills')) {
                mkdir('assets/ewaybills', 0777, true);
            }            
            if($_FILES['uploadfile'] != ''){                
                $prodlink = 'assets/ewaybills';                
                $filename=$_FILES['uploadfile']['name'];
                $newName = time().".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(                
                'upload_path' => $prodlink,
                'allowed_types' => 'pdf',
                'file_name' => $newName,                
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('uploadfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;                         
                }                 
        }
        if($path!=NULL){
            $p_odata = array( 'eway_billpath'  => $path );   
            $this->Ingram_Model->update_ingram_order_history($id_sale_token,$p_odata);                
            $this->session->set_flashdata('save_data', 'Eway-bill uploadted successfully.');
        }else{
            $this->session->set_flashdata('save_data', 'Fail to upload Eway-bill.');
        }
        
        return redirect('Ingram_Api/update_ewaybill');
    }
    
    
    function sendsms($mobileno, $path){        
                $longurl=base_url().$path;                
                $url=$this->Api_Model->short_url($longurl);                
                $message = "Dear Customer,%0aThank you for shopping with us. Download you invoice from below link.%0a".$url['shortLink'].'%0a- SS MOBILE';
                $message = str_replace(' ', '%20', $message); // replace all spaces with %20 from message                
                
                $baseurl_http='http://login.smsozone.com/api/mt/SendSMS?user=sscommunications&password=sscommunications@7654321&senderid=SSMOBS&channel=Trans&DCS=0&flashsms=0&number='.$mobileno.'&text='.$message.'&route=2069';

                $ch=curl_init($baseurl_http);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response=curl_exec($ch);
                  
                curl_close($ch);  
               
    }
    
  
    function echoResponse($response) {
        echo '{"status":"' . $response['status'] . '", "message":"' . $response['message'] . '", "data": ' . rtrim(json_encode($response['data'])) . '}';
    }
    function json_output($statusHeader,$response)
	{
		$ci =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_status_header($statusHeader);
		$ci->output->set_output(json_encode($response));
	}
        
        
         /* 
    CREATE TABLE `vendors_sku`;
    ALTER TABLE `vendor` ADD `idvendors_sku` INT NULL AFTER `vendor_created_by`;
    * Add image - assets/images/sku.jpg
          * Controller - Master
          * vendors_sku_details
          * save_vendors_sku
          * model_vendor_sku_update
          * ajax_get_model_bycategory_sku
          * save_bulk_sku_update
          * save_sku_update
          * 
          * View - master
          * vendors_sku_details
          * model_vendor_sku_update
          * 
          * Model - General_model
          * get_vendor_sku_data
          * save_vendor_sku
          * update_model_variants_byidvariant_bulk
          * 
          * Controller - Ingram_Api          * 
          * Model -  Ingram_Model
          * 
          * Model - Purchase_model
          * get_variant_by_id
          * 
          * View - Purchase
          * create_purchase_order
          * 
          
  ///// Billing Configuration
          * CREATE TABLE `billing_modes`;
          * Add image - assets/images/bill.jpg
          * 
          * Controller - Master
          * billing_mode_details
          * save_billing_mode
          * branch_billing_mode_configuration
          * save_billing_mode_configuration
          * 
          * View - master
          * billing_mode_details
          * branch_billing_mode_configuration
          * 
          * Model - General_model
          * get_billing_mode_data
          * get_billing_mode_data_byid
          * save_billing_modes
          * update_branch_byid_branch_bulk
          * 
          * Controller - Sale
          * index
          * online_sale
          * 
          * View - sale
          * create_invoice     
          * online_sale
          * 
          * ALTER TABLE `bfl_file_customer` ADD `idsaletoken` INT NOT NULL AFTER `entry_time`;
          * ALTER TABLE `sale_token_product` ADD `online_price` INT NULL AFTER `landing`;
          * ALTER TABLE `sale_product` ADD `online_price` INT NOT NULL AFTER `landing`;
          * ALTER TABLE `sale_token_product` ADD `insurance_idbrand` INT NULL AFTER `activation_code`;
          * ALTER TABLE `sale_product` ADD `insurance_idbrand` INT NULL AFTER `activation_code`;

           
    30-08-2021
          * 
          * 
          * ALTER TABLE `vendors_sku` ADD `access_token` VARCHAR(50) NULL AFTER `column_name`;
    
    
    
    
    */
        
    public function replace_wrong_imei() {
    
    $wng=array('SC4H1266153YP70RBK',
                'SC4H12661C7HP70RBF',
                'SC4H12661CDPP70RBR',
                'SC4H12661CSSP70RBH',
                'SC4H12661CTAP70RBW',
                'SC4H12661CTJP70RBN',
                'SC4H12661FHUP70RB5',
                'SC4H12661GYNP70RBZ',
                'SC4H12661HA5P70RBD',
                'SC4H12661HA8P70RBA',
                'SC4H117401G9P70RBA',
                'SC4H12410N9XP70RBZ',
                'SC4H12410BR7P70RBL',
                'SC4H12410992P70RB7',
                'SC4H124108DFP70RBH',
                'SC4H124108D7P70RBR',
                'SC4H1241075BP70RBC',
                'SC4H124106ALP70RBP',
                'SC4H1237135YP70RBP',
                'SC4H123710MWP70RBE',
                'SC4H123710DHP70RBH',
                'SC4H12370HGSP70RBK',
                'SC4H12410M43P70RB9',
                'SC4H12410U63P70RBW',
                'SC4H12410WTQP70RBC',
                'SC4H1241171XP70RB1',
                'SC4H124118GCP70RB8',
                'SC4H124201UVP70RBY',
                'SC4H124201WQP70RBX'
        );  
    
    $corr=array('SC4H12661HA9P70RB9',
                'SC4H117401G9P70RBA',
                'SC4H12661GQRP70RBL',
                'SC4H12661FATP70RBT',
                'SC4H12661F8XP70RBV',
                'SC4H12661E6YP70RB1',
                'SC4H12661DTMP70RBJ',
                'SC4H12661CP7P70RBB',
                'SC4H12660H4QP70RBF',
                'SC4H1267007MP70RBR',
                'SC4H12661ENMP70RBY',
                'SC4H1241040HP70RBQ',
                'SC4H123711QSP70RB8',
                'SC4H12410CLFP70RBS',
                'SC4H12410DDMP70RB6',
                'SC4H12370ZB8P70RB2',
                'SC4H1241084UP70RBX',
                'SC4H123710VKP70RB1',
                'SC4H12410CAVP70RB8',
                'SC4H123710NCP70RBV',
                'SC4H12410BR8P70RBK',
                'SC4H124119L7P70RB0',
                'SC4H11940AQFP70RBZ',
                'SC4H12410Z16P70RB3',
                'SC4H11930DF9P70RBW',
                'SC4H11940952P70RB2',
                'SC4H12410Z98P70RBB',
                'SC4H1242022ZP70RB3',
                'SC4H12370QACP70RBA'
        );
            $cnt=0;
        foreach ($wng as $wimei){
             echo "UPDATE `inward_product` SET `imei_no`='$corr[$cnt]' WHERE `imei_no`='$wimei';<br>";
             echo "UPDATE `outward_product` SET `imei_no`='$corr[$cnt]' WHERE `imei_no`='$wimei';<br>";
             echo "UPDATE `stock` SET `imei_no`='$corr[$cnt]' WHERE `imei_no`='$wimei';<br>";
             echo "UPDATE `imei_history` SET `imei_no`='$corr[$cnt]' WHERE `imei_no`='$wimei';<br>";    
            $cnt++;

        }

//die('<pre>'.print_r($inward,1).'</pre>');

    
        
    }
    
    public function ingram_live_stock() {
        
        $this->load->library("pagination");
        $data['tab_active'] = '';
        $result= $this->Ingram_Model->getToken();
        $config = array();
        $config["base_url"] = base_url() . "index.php/Ingram_Api/ingram_live_stock";
        $sku_data= $this->General_model->get_vendor_sku_data_byid(1);
        
        $model_variants = $this->Ingram_Model->ajax_get_ingram_sku(23,$sku_data->column_name,0,0);  
        $config["total_rows"] = count($model_variants);
        $config["per_page"] = 500;
        $config["uri_segment"] = 3;
        $config['full_tag_open'] = '<ul class="pagination pull-right">';     
        $config['full_tag_close'] = '</ul>';      
        $config['first_link'] = 'First';  
        $config['last_link'] = 'Last';   
        $config['first_tag_open'] = '<li>'; 
        $config['first_tag_close'] = '</li>';  
        $config['prev_link'] = '&laquo';   
        $config['prev_tag_open'] = '<li class="prev">'; 
        $config['prev_tag_close'] = '</li>';    
        $config['next_link'] = '&raquo';     
        $config['next_tag_open'] = '<li>';   
        $config['next_tag_close'] = '</li>'; 
        $config['last_tag_open'] = '<li>';   
        $config['last_tag_close'] = '</li>';   
        $config['cur_tag_open'] = '<li class="active"><a href="#">';  
        $config['cur_tag_close'] = '</a></li>';  
        $config['num_tag_open'] = '<li>';    
        $config['num_tag_close'] = '</li>';
        
        
        $this->pagination->initialize($config);        
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;                
        $data["links"] = $this->pagination->create_links();
         
        $access_token=$sku_data->access_token;
        $model_variant = $this->Ingram_Model->ajax_get_ingram_sku(23,$sku_data->column_name,$config["per_page"],$page);          
        $dataa=array();
        $dataa['servicerequest']=array();
        $servicerequest=array();
        $servicerequest['requestpreamble']=array();
        $servicerequest['requestpreamble']['customernumber']="40-SSSEPV";
        $servicerequest['requestpreamble']['isocountrycode']="IN";
        $servicerequest['priceandstockrequest']=array();
        $servicerequest['priceandstockrequest']['showwarehouseavailability']="True";
        $servicerequest['priceandstockrequest']['extravailabilityflag']="Y";
        $servicerequest['priceandstockrequest']['item']=array();
        $data['ingram_data']=array();
        foreach ($model_variant as $vdata){
            $item=array();
            $item['ingrampartnumber']=$vdata->ingram;
            $item['warehouseidlist']=array(31);
            $item['quantity']="1";
            $servicerequest['priceandstockrequest']['item'][]=$item;
            $data['ingram_data']['sku'][]=$vdata->ingram;
            $data['ingram_data']['model'][]=$vdata->full_name;
            $data['ingram_data']['part_number'][]=$vdata->part_number;
        }
        $servicerequest['priceandstockrequest']['includeallsystems']=false;
        $dataa['servicerequest']= $servicerequest;               
        $ingram_data= $this->Ingram_Model->getPriceAndAvailability($dataa,$access_token);        
        if(isset($ingram_data['serviceresponse']['priceandstockresponse'])){
             $s_data=$ingram_data['serviceresponse']['priceandstockresponse']['details'];
             foreach ($s_data as $da){
                if($da['itemstatus']=='SUCCESS'){
                    
                    $warehousedetails=$da['warehousedetails']; 
                    $availablequantity=0;
                    foreach ($warehousedetails as $wid){
                        $availablequantity=$availablequantity+$wid['availablequantity'];
                    }   
                    $data['ingram_data']['qty'][]=$availablequantity;                  
                }else{
                    $data['ingram_data']['qty'][]=0;
                }                  
             }            
        }else{
            $data['ingram_data']=array();
        }
//        die('<pre>' . print_r($data['ingram_data'], 1) . '</pre>');
        $this->load->view('ingram/ingram_live_stock', $data); 
    }
    

}
