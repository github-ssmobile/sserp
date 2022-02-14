<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Allocation_model");        
        $this->load->model("General_model");
        $this->load->model("Outward_model");       
        $this->load->model("Inward_model");   
        $this->load->model("Transfer_model");     
           $this->load->model("common_model");         
        date_default_timezone_set('Asia/Kolkata');        
    }
    public function my_stock_requests(){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');             
        $role_type=$this->session->userdata('role_type');   
        $q['type']=0; 
        if($role_type==1){            
            $idbranch=$this->session->userdata('idbranch');             
            $q['branch_data']=$this->General_model->get_my_branches_n_warehouses($idbranch);            
        }else{     
            $idbranch=$this->session->userdata('idbranch'); 
            $idwarehouse=$this->General_model->get_branch_byid($idbranch)->idwarehouse;
            $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idwarehouse);
        }
        $q['idbranch']=$idbranch;        
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_to(0, $idbranch,"","","",$q['type']);
        $this->load->view('transfer/my_stock_requests', $q);
    }
    public function my_doa_stock_requests(){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');             
        $role_type=$this->session->userdata('role_type');   
        $q['type']=1; 
        $q['branch_data'] = $this->General_model->get_active_branch_data();   
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();   
        $idbranch=$this->session->userdata('idbranch');                    
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_to(0, $idbranch,"","","",$q['type']);
        $q['idbranch']=$idbranch;        
        $this->load->view('transfer/my_doa_stock_requests', $q);
    }
    public function create_stock_request(){
        $q['tab_active'] = ''; 
        $_SESSION['variant']=array();
        $user_id=$this->session->userdata('id_users');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){
            $role_type=$this->session->userdata('role_type');   
            if($role_type==1){                
                $q['request_from']=$this->session->userdata('idbranch');
                $q['branch_data'] = $this->General_model->get_my_branches_n_warehouses($q['request_from']);
            }else{
                $q['request_from']=$this->session->userdata('idbranch');
                $idwarehouse=$this->General_model->get_branch_byid($q['request_from'])->idwarehouse;
                $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idwarehouse);            
            }        
            $q['active_godown'] = $this->General_model->get_b_to_b_godown();
            $q['brand_data'] = $this->General_model->get_active_brand_data(); 
            $q['request_type'] = 0; 
            $this->load->view('transfer/create_stock_request', $q);
        }else{
            redirect('Transfer/404');
        }
    }
    public function create_doa_stock_request(){
        $q['tab_active'] = ''; 
        $_SESSION['variant']=array();
        $user_id=$this->session->userdata('id_users');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
            $q['request_from']=$this->session->userdata('idbranch');
            $q['branch_data'] = $this->General_model->get_active_branch_data();
            $q['active_godown'] = $this->General_model->get_doa_godown();
            $q['brand_data'] = $this->General_model->get_active_brand_data();
            $q['request_type'] = 1;
            $this->load->view('transfer/create_stock_request', $q);
        }else{
            redirect('Transfer/404');
        }
    }
    
    public function ajax_get_for_me_stock_request_bystatus(){
//                die('<pre>'.print_r($_POST,1).'</pre>');   
        $user_id=$this->session->userdata('id_users');
        $idbranch_from=$this->session->userdata('idbranch_from');
        $status = $this->input->post('status');
        $from = $this->input->post('datefrom');
        $to = $this->input->post('dateto');
        $role_type=$this->session->userdata('role_type'); 
        $idbranch=$this->session->userdata('idbranch');
        if($role_type==1){
            $idbranch=$this->session->userdata('idbranch');             
        }   
        $transfer_data = $this->Transfer_model->get_transfer_data_bystatus_idbranch_from($status, $idbranch,$idbranch_from,$from,$to);
        ?>
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>DC</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Request To</th>
                <th>Total Product</th>
                <th>Branch Remark</th>
                <th>My Remark</th>
                <th>Info</th>
            </thead>
            <tbody>
                <?php foreach ($transfer_data as $transfer){ ?>
                <tr>
                    <td><b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></td>
                    <td><?php echo $transfer->date ?></td>
                    <td><?php echo $transfer->branch_to ?></td>
                    <td><?php echo $transfer->branch_from ?></td>
                    <td><?php echo $transfer->total_product ?></td>
                    <td><?php echo $transfer->transfer_remark ?></td>
                    <td><?php echo $transfer->approved_remark ?></td>
                    <?php if($status==0){ ?>
                        <td><a target="_blank" class="thumbnail gradient2 textalign" href="<?php echo site_url() ?>Transfer/update_stock_request/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;color: #fff"><i class="" style="margin-right: 5px;"></i></i> Approve </a></td>
                      <?php }else{ ?>
                        <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><i class="fa fa-info " style="color: blue"></i></a></td>
                    <?php } ?>
                    
                    
                </tr>
                <?php } ?>
            </tbody>
            
            <?php
    }
    
    public function ajax_get_my_stock_request_bystatus(){
//      die('<pre>'.print_r($_POST,1).'</pre>');   
        $idbranch=$this->input->post('idbranch');
        $status = $this->input->post('status');
        $from = $this->input->post('datefrom');
        $to = $this->input->post('dateto');
        $idbranch_to = $this->input->post('idbranch_to');   
         $type = $this->input->post('type');   
        $transfer_data = $this->Transfer_model->get_transfer_data_bystatus_idbranch_to($status, $idbranch,$idbranch_to,$from,$to,$type);
        
        ?>
            <thead class="fixedelement" style="text-align: center;position: none !important;">   
                <th>DC</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Request To</th>
                <th>Total Product</th>
                <th>My Remark</th>
                <th>Branch Remark</th>
                <th>Info</th>
            </thead>
            <tbody>
                <?php foreach ($transfer_data as $transfer){ ?>
                <tr>
                    <td><b style="color: #0e10aa !important;"><?php echo $transfer->id_transfer ?></b></td>
                    <td><?php echo $transfer->date ?></td>
                    <td><?php echo $transfer->branch_to ?></td>
                    <td><?php echo $transfer->branch_from ?></td>
                    <td><?php echo $transfer->total_product ?></td>
                    <td><?php echo $transfer->transfer_remark ?></td>
                     <td><?php echo $transfer->approved_remark ?></td>
                    <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $transfer->id_transfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><i class="fa fa-info " style="color: blue"></i></a></td>
                   
                </tr>
                <?php } ?>
            </tbody>
            
            <?php
    }
    
    public function ajax_variants_by_id(){
//            die('<pre>'.print_r($_POST,1).'</pre>');   
            $variantid = $this->input->post('variant_id'); 
            $idgodown = $this->input->post('idgodown'); 
            $idbrand = $this->input->post('brand');
            $request_to = $this->input->post('request_to');
            $request_from = $this->input->post('request_from');            
            $role_type=$this->session->userdata('role_type');  
            $user_id=$this->session->userdata('id_users');
            
            $godown_name = $this->input->post('godown_name');            
            $allocation_type=0;
            $modelid=0;
            $idproductcategory=0;
            $days=30;
            if(in_array($variantid.'-'.$idgodown,$_SESSION['variant'])){?>
                <exist>
             <?php }else{
                array_push($_SESSION['variant'], $variantid.'-'.$idgodown);
           
            $model_data = $this->Transfer_model->get_branch_stocksale_byvariants(array($request_from,$request_to),$days,$variantid,$idgodown);
            
            $full_name = preg_replace('/\s+/', '', strtolower($model_data[0]->full_name)); 
            ?> 
                <tbody class="data_1">
                <?php $i = 1; ?> 
                        <tr>
                            <td>
                                <?php echo $model_data[0]->full_name; ?>
                                <input type="hidden" name="variants[]" value="<?php echo $model_data[0]->id_variant; ?>" />
                                <input type="hidden" name="idmodel[]" value="<?php echo $model_data[0]->idmodel; ?>" />
                                <input type="hidden" name="idcategory[]" value="<?php echo $model_data[0]->idcategory; ?>" />
                                <input type="hidden" name="idproductcategory[]" value="<?php echo $model_data[0]->idproductcategory; ?>" />
                                <input type="hidden" name="idskutype[]" value="<?php echo $model_data[0]->idsku_type; ?>" />  
                                <input type="hidden" name="idgodown[]" value="<?php echo $idgodown; ?>" />  
                                <input type="hidden" name="idbrand[]" value="<?php echo $idbrand; ?>" />
                            </td>
                            <td><?php echo $godown_name; ?></td>
                                <input type="hidden" class="bstcok" value="<?php echo $model_data[1]->stock_qty; ?>" />  
                            <!--<td><?php //echo $model_data[1]->stock_qty; ?></td>-->
                            <td><?php echo $model_data[1]->sale_qty; ?></td>
                            <td><?php echo $model_data[0]->stock_qty; ?></td>
                            <td><?php echo $model_data[0]->sale_qty; ?></td>                         
                            <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm qtt" name="qty[]" /></td>  
                            <td><a href="#" class="thumbnail textalign delete_row" variant="<?php echo $model_data[0]->id_variant ?>" godown="<?php echo $idgodown ?>" style="margin: 0 8px;padding: 5px !important;"><i class="fa fa-trash-o" style="color:red;"></i></a></td>    
                         </tr>
                    <?php $i++;
                 ?>
                </tbody>    
            <?php
        }
    } 
           
    public function save_stock_request() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   
        $allocation_type=$this->input->post('allocation_type');
        $transfer_from=$this->input->post('request_to');
        $idbranch=$this->input->post('request_from');
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');
        $variants=$this->input->post('variants');    
        $idmodel_s=$this->input->post('idmodel');    
        $idcategory_s=$this->input->post('idcategory');    
        $idskutype_s=$this->input->post('idskutype'); 
        $idproductcategory_s=$this->input->post('idproductcategory'); 
        $idgodown_s=$this->input->post('idgodown'); 
        $idbrand_s=$this->input->post('idbrand'); 
        $qty=$this->input->post('qty');
        $request_type=$this->input->post('request_type');

    if (isset($_POST['qty']) && count($this->input->post('qty')) > 0) {            
            $data_att = array();
            $i = 0;                                    
            $array = array(
                'date' => $date,
                'transfer_from' => $transfer_from,
                'idbranch' => $idbranch,
                'total_product' => count($variants),
                'created_by' =>$iduser,
                 'request_type' => $request_type
                );
                $id_transfer_request = $this->Transfer_model->save_transfer($array);
                    
                    foreach ($variants as $id_vatriant) {  
                        $key=array_search($id_vatriant, $variants);
                        $data_att[] = array(
                            'date' => $date,
                            'transfer_from' => $transfer_from,
                            'idbranch' => $idbranch,        
                            'idtransfer ' => $id_transfer_request,
                            'idvariant' => $id_vatriant,
                            'idmodel' => $idmodel_s[$key],                            
                            'idskutype' => $idskutype_s[$key],
                            'idgodown' => $idgodown_s[$key],
                            'idproductcategory ' => $idproductcategory_s[$key],
                            'idcategory' => $idcategory_s[$key],                            
                            'idbrand' => $idbrand_s[$key],                            
                            'qty' => $qty[$key]
                        );                                   
                    }             
            if (count($data_att) > 0) {
                $result = $this->Transfer_model->save_transfer_product($data_att);                
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                    die($output);
                } else {
                    $this->db->trans_commit();
                    $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
                    die($output);
                }
            } else {
                $this->db->trans_complete();
                $this->db->trans_rollback();
                $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                die($output);
            }
        } else {
            $this->db->trans_complete();
            $output = json_encode(array("result" => "true", "data" => "select_model", "message" => ""));
            die($output);
        }
    }
    public function transfer_details($id){
        $q['tab_active'] = 'B to B Transfer';
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type'); 
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);
           if(count($q['transfer_data'])>0){
                $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
                $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0);   //(transfer_id,need responder branch stock)       
           if($role_type==1){
               $q['idbranch']=$this->session->userdata('idbranch');             
           }else{
               $q['idbranch']=$this->session->userdata('idbranch');
           } 
           $this->load->view('transfer/transfer_details', $q);
        }else{
            return redirect('Transfer/404');
        }
    }
    public function update_shipment($id){
        $q['tab_active'] = 'B to B Transfer';
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type'); 
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);
        $q['dispatch_data'] = $this->General_model->get_dispatch_type();
        $q['transport_vendor'] = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
        $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0);   //(transfer_id,need responder branch stock)       
        if($role_type==1){
            $q['idbranch']=$this->session->userdata('idbranch');     
        }else{
            $q['idbranch']=$this->session->userdata('idbranch');
        } 
        $this->load->view('transfer/update_shipment', $q);
    }
    public function stock_requests_for_me(){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');                              
        $role_type=$this->session->userdata('role_type');   
        if($role_type==1){            
            $q['idbranch']=$this->session->userdata('idbranch');    
            $q['branch_data']=$this->General_model->get_my_branches_n_warehouses($q['idbranch']);
        }else{          
            $q['idbranch']=$this->session->userdata('idbranch'); 
            $idwarehouse=$this->General_model->get_branch_byid($q['idbranch'])->idwarehouse;
            $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idwarehouse);
        }        
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_from(0, $q['idbranch'],"","","");
        $this->load->view('transfer/stock_requests_for_me', $q);
    }
    public function ready_for_transfer(){
        $q['tab_active'] = ''; 
        $q['title'] = 'Stock Transfer'; 
               
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type'); 
        $idbranch=$this->session->userdata('idbranch');    
        if($role_type==1){
            $idbranch=$this->session->userdata('idbranch');    
        }         
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_from(1, $idbranch,"","","");
        $this->load->view('transfer/stock_requests', $q);
    }
    public function ready_for_shipment(){
         $q['tab_active'] = ''; 
         $q['title'] = 'Update Shipment'; 
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type'); 
        $idbranch=$this->session->userdata('idbranch');    
        if($role_type==1){
            $idbranch=$this->session->userdata('idbranch');    
        }         
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_from(3, $idbranch,"","","");
        $this->load->view('transfer/stock_requests', $q);
    }
    public function update_stock_request($id){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
        $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,1);   //(transfer_id,need responder branch stock)
        if($q['transfer_data'][0]->status==0){                        
            $q['gst_type']=0;
            if($q['branch_data'][0]->idcompany != $q['branch_data'][1]->idcompany ){
                $q['gst_type']=1;
            }            
            $this->load->view('transfer/update_stock_request', $q);        
        }else{
            if($q['transfer_data'][0]->status==1){
                redirect('Transfer/stock_trasnfer/'.$id);
            }elseif($q['transfer_data'][0]->status > 1){
                redirect('Transfer/transfer_details/'.$id);
            }else{
                redirect('Transfer/stock_requests_for_me');
            }                
        }
    }
    public function approve_stock_request() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   
        $idbranch=$this->input->post('idbranch');
        $idwarehouse=$this->input->post('idwarehouse');
        $idtransfer=$this->input->post('idtransfer');
        $id_transfer_product=$this->input->post('id_transfer_product');
        $a_qty=$this->input->post('a_qty');
        $remark=$this->input->post('remark');
        $iduser=$this->session->userdata('id_users');
        
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        
    if (isset($_POST['id_transfer_product']) && count($this->input->post('id_transfer_product')) > 0) {            
            $data_att = array();
            $i = 0;                                    
            $array = array(
                'status' => 1,
                'approved_time' => $datetime,                
                'approved_by' =>$iduser,
                'approved_remark' =>$remark
                );
                $this->Transfer_model->update_transfer($idtransfer,$array);
                    
                    foreach ($id_transfer_product as $id_product) {  
                        $key=array_search($id_product, $id_transfer_product);
                        $data_att[] = array(
                            'id_transfer_product' => $id_product,
                            'approved_qty' => $a_qty[$key]
                        );                                   
                    }             
            if (count($data_att) > 0) {
                $result = $this->Transfer_model->update_transfer_product($data_att);                
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                    die($output);
                } else {
                    $this->db->trans_commit();
                    $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
                    die($output);
                }
            } else {
                $this->db->trans_complete();
                $this->db->trans_rollback();
                $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                die($output);
            }
        } else {
            $this->db->trans_complete();
            $output = json_encode(array("result" => "true", "data" => "select_model", "message" => ""));
            die($output);
        }
    }
    public function reject_stock_request() {   
        $idtransfer=$this->input->post('idtransfer');
        $remark=$this->input->post('remark');
        $iduser=$this->session->userdata('id_users');        
        $datetime = date('Y-m-d H:i:s');
            $array = array(
                'status' => 2,
                'approved_time' => $datetime,                
                'approved_by' =>$iduser,
                'approved_remark' =>$remark
                );
                $this->Transfer_model->update_transfer($idtransfer,$array);
                    
                 if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                    die($output);
                } else {
                    $this->db->trans_commit();
                    $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
                    die($output);
                }  
    }
    
    public function stock_trasnfer($id){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);
        $q['dispatch_data'] = $this->General_model->get_dispatch_type();
        $q['transport_vendor'] = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
        $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0); //(transfer_id,need responder branch stock)  
        if($q['transfer_data'][0]->status==1  && ($q['transfer_data'][0]->scanned_by==$user_id || $q['transfer_data'][0]->scanned_by==0)){  
            $tdata = array(
                'scanned_by' => $user_id
             );            
            $this->Transfer_model->update_transfer($id, $tdata);
            $q['gst_type']=0;
            if($q['branch_data'][0]->idcompany != $q['branch_data'][1]->idcompany ){
                $q['gst_type']=1;
            }            
            $this->load->view('transfer/stock_transfer', $q);        
        }else{           
                redirect('Transfer/ready_for_transfer');
            }         
    }
    
    public function save_transfer(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idbranch=$this->input->post('idbranch');
        $transfer_from=$this->input->post('idwarehouse');   
        $idcompany_to=$this->input->post('idcompany_to');
        $idcompany_from=$this->input->post('idcompany_from');   
        $idproductcategory=$this->input->post('idproductcategory');
        $idvariants=$this->input->post('idvariant');    
        $idmodel_s=$this->input->post('modelid');    
        $idcategory_s=$this->input->post('idcategory');    
        $idskutype_s=$this->input->post('skutype');  
        $idgodown=$this->input->post('id_godown');
        $idbrand=$this->input->post('idbrand');
        $product_name=$this->input->post('product_name');
        $qty=$this->input->post('qty');
        $vehicle_no=$this->input->post('vehicle_no');
        $price=$this->input->post('price');
        $cgst_per=$this->input->post('cgst_per');
        $scanned=$this->input->post('scanned');
        $count= count($idvariants);
        $idtransfer=$this->input->post('idtransfer'); 
        $id_transfer_product=$this->input->post('id_transfer_product'); 
        $remark=$this->input->post('remark'); 
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');  
        $this->db->trans_begin();     
        $sale_inv=0;
        $purc_inv=0;
        $idinterstate=0;
        if($this->input->post('gst_type')==1){
            $financial_year=$this->General_model->get_financial_year()->financial_year;
            $comapny_data=$this->General_model->get_company_byids(array($idcompany_from,$idcompany_to));                
            $inter_state_sale=($comapny_data[0]->inter_state_sale+1);
            $purchase_invoice=($comapny_data[1]->purchase_invoice+1);
            $sale_inv="SL".$financial_year.$comapny_data[0]->campany_code.sprintf("%05d", $inter_state_sale);
            $purc_inv="PS".$financial_year.$comapny_data[1]->campany_code.sprintf("%05d", $purchase_invoice);
            $dat = array('inter_state_sale' => $inter_state_sale);       
            $da = array('purchase_invoice' => $purchase_invoice);
            $this->General_model->edit_db_comapny($idcompany_from, $dat);
            $this->General_model->edit_db_comapny($idcompany_to, $da);
            
            $data_interstate = array(
                'date' =>  $date,
                'idbranch_from' => $transfer_from,
                'idbranch_to' =>  $idbranch,
                'idcompany_from' =>  $idcompany_from,
                'idcompany_to' =>  $idcompany_to,
                'sales_invoice' => $sale_inv,
                'purchase_invoice' => $purc_inv,
                'total_product' => $count,            
                'remark' => $remark,
                'entry_by' => $iduser,            
                'idoutward_transfer' => $idtransfer,
                'transaction_type'  => 'Transfer',
                'gst_type' => $this->input->post('gst_type')
            );
            $idinterstate = $this->Transfer_model->save_inter_state($data_interstate);
        }
        ////  Outward Data ////        
        $data = array(  
            'scanned_remark' => $remark,
            'status' => 3,
            'scanned_time' => $datetime,
            'scanned_by' => $iduser,
            'sales_invoice' => $sale_inv,
            'purchase_invoice' => $purc_inv
        );
        
        
        
        $transfer_product=array();
        $inward_stock_sku=array();
        $stock_array=array();
        $imei_history=array();
        $interstate_product=array();
        $update_stock=array();
        for($i=0;$i<$count;$i++){
            if($idskutype_s[$i]==4){                
                ////  Outward Product Data For QTY SKU ////                
                $transfer_product[] = array(
                    'id_transfer_product' =>  $id_transfer_product[$i],                    
                    'price' => $price[$i],
                    'cgst_per' => $cgst_per[$i],
                    'sgst_per' => $cgst_per[$i],
                    'igst_per' => ($cgst_per[$i]*2),                    
                );                
                ////  Stock Reflection ////     
                
                //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($idvariants[$i], $transfer_from, $idgodown[$i], $qty[$i]);
                //$update_stock.="UPDATE stock SET qty = qty - ".$qty[$i]." WHERE idvariant = ".$idvariants[$i]." AND idgodown = ".$idgodown[$i]." AND idbranch = ".$transfer_from.";";
				$update_stock[]="UPDATE stock SET `qty` = `qty` - ".$qty[$i]." WHERE `idvariant`='".$idvariants[$i]."' AND `idgodown`='".$idgodown[$i]."' AND `idbranch`='".$transfer_from."' ; ";
                $inward_stock_sku[] = array(
                    'date' =>  $date,
                    'transfer_time' => $datetime,
                    'idbranch' => 0,
                    'temp_idbranch' => $idbranch,
                    'transfer_from' => $transfer_from,
                    'transfer_dc' => $idtransfer,
                    'transfer_remark' => $remark,
                    'transfer_by' => $iduser,
                    'idskutype' => 4,            
                    'product_name' => $product_name[$i],
                    'idgodown' => $idgodown[$i],
                    'idproductcategory' => $idproductcategory[$i],
                    'idcategory' => $idcategory_s[$i],
                    'idmodel' => $idmodel_s[$i],
                    'idvariant' => $idvariants[$i],
                    'idbrand' => $idbrand[$i],
                    'created_by' => $iduser,
                    'idvendor' => 1,
                    'qty' => $qty[$i],
                    'transfer' => 1,
                );                 
                
            }else{
               $imeis = explode(',', $scanned[$i]);
               $transfer_product[] = array(
                    'id_transfer_product' =>  $id_transfer_product[$i],  
                    'imei_no' => $scanned[$i],                       
                    'qty' => $qty[$i],                                       
                    'price' => $price[$i],
                    'cgst_per' => $cgst_per[$i],
                    'sgst_per' => $cgst_per[$i],
                    'igst_per' => ($cgst_per[$i]*2),
                );
               
               for ($j=0;$j < count($imeis)-1;$j++){                
                   ////  Outward Product Data For IMEI/SRNO SKU ////   
                   if($this->input->post('gst_type')==1){
                    $interstate_product[] = array(
                         'date' =>  $date,
                         'idbranch' => $idbranch,        
                         'imei_no' => $imeis[$j],
                         'idskutype' => $idskutype_s[$i],
                         'idgodown' => $idgodown[$i],
                         'idproductcategory' => $idproductcategory[$i],
                         'idcategory' => $idcategory_s[$i],
                         'idmodel' => $idmodel_s[$i],
                         'idvariant' => $idvariants[$i],
                         'idbrand' => $idbrand[$i],
                         'qty' => 1,                    
                         'idinterstate' => $idinterstate,
                         'price' => $price[$i],
                         'cgst_per' => $cgst_per[$i],
                         'sgst_per' => $cgst_per[$i],
                         'igst_per' => ($cgst_per[$i]*2),
                     );
                   }
                   
                   $stock_array[] = array(
                            'imei_no' => $imeis[$j],
                            'transfer' => 1,
                            'transfer_from' => $transfer_from,
                            'transfer_dc' => $idtransfer,
                            'transfer_remark' => $remark,
                            'transfer_time' => $datetime,
                            'transfer_by' => $iduser,
                            'idbranch' => 0,
                            'temp_idbranch' => $idbranch,
                            'transfer_from' => $transfer_from,
                        );   
                   $imei_history[]=array(
                        'imei_no' =>$imeis[$j],
                        'entry_type' => 'Transfer (scanned)',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $idbranch,
                        'transfer_from' => $transfer_from,
                        'idgodown' => $idgodown[$i],
                        'idvariant' => $idvariants[$i],
                        'idimei_details_link' => 6, // Outward from imei_details_link table
                        'iduser' => $iduser,
                        'idlink' => $idtransfer   
                    );
                } 
            }
        }
        
         /////// Update ////
        $this->Transfer_model->update_transfer($idtransfer,$data);
         $this->Transfer_model->update_transfer_product($transfer_product); 
        if(count($inward_stock_sku)>0){          
			foreach ($update_stock as $data){
                $this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($data);    
            }
            //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($update_stock);
        }
        if(count($stock_array)){
            $this->Outward_model->update_batch_stock_byimei($stock_array);        
        }        
        $role_type=$this->session->userdata('role_type'); 
        if($role_type==2){
        $datetime = date('Y-m-d H:i:s');
        $id_transfer = $this->input->post('id_transfer');               
        $data = array(
           'dispatch_date' => $this->input->post('dispatch_date'),
           'dispatch_type' => $this->input->post('dispatch_type'),
           'iddispatch_type' => $this->input->post('iddispatchtype'),
           'idtransport_vendor' => $this->input->post('idtvendors'),
           'courier_name' => $this->input->post('courier_name'),
           'po_lr_no' => $this->input->post('po_lr_no'),
           'no_of_boxes' => $this->input->post('no_of_boxes'),
           'shipment_remark' => $this->input->post('shipment_remark'),
           'shipment_entry_by' => $this->input->post('shipment_entry_by'),
           'shipment_entry_time' => $datetime,
           'status' => 4,
           'vehicle_no' => $vehicle_no,
        );        
            $this->Transfer_model->save_transfer_shipment_details($id_transfer, $data);
        }
        
         /////INSERT ////     
       if(count($interstate_product)>0){
            $this->Transfer_model->save_inter_state_product($interstate_product);                  
       }        
        if(count($inward_stock_sku)>0){ 
            $this->Inward_model->save_stock_batch($inward_stock_sku);     
        } 
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
            die($output);
        } else {
            $this->db->trans_commit();
            $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
            die($output);
        }
        
    }
    
    public function save_shipment_details() {
//        die('<pre>'.print_r($_POST,1).'</pre>');        
        $this->db->trans_begin();
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
         $user_id=$this->session->userdata('id_users');      
        $id_transfer = $this->input->post('id_transfer');               
        $data = array(
           'dispatch_date' => $this->input->post('dispatch_date'),
           'dispatch_type' => $this->input->post('dispatch_type'),
           'iddispatch_type' => $this->input->post('iddispatchtype'),
           'idtransport_vendor' => $this->input->post('idtvendors'),
           'courier_name' => $this->input->post('courier_name'),
           'po_lr_no' => $this->input->post('po_lr_no'),
           'no_of_boxes' => $this->input->post('no_of_boxes'),
           'shipment_remark' => $this->input->post('shipment_remark'),
           'shipment_entry_by' => $this->input->post('shipment_entry_by'),
           'shipment_entry_time' => $datetime,
           'status' => 4,
        );
        
        $this->Transfer_model->save_transfer_shipment_details($id_transfer, $data);
        
        $out_date=$this->Transfer_model->get_transfer_product_by_transferid($id_transfer);
            $imei_history=array();
            foreach ($out_date as $data){
                 $array = explode(',', $data->imei_no);   
                    foreach($array as $imei){    
                        $imei_history[]=array(
                                'imei_no' =>$imei,
                                'entry_type' => 'Transfer (In-transit)',
                                'entry_time' => $datetime,
                                'date' => $date,
                                'idbranch' => $data->idbranch,
                                'idgodown' => $data->idgodown,
                                'idvariant' => $data->idvariant,
                                'idimei_details_link' => 14, // Outward from imei_details_link table
                                'iduser' => $user_id,
                                'idlink' => $id_transfer,
                                'transfer_from' =>$data->transfer_from
                            );
                    }
            }
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            } 
        
         if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Shipment is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Shipment added successfully');
        }
        return redirect('Transfer/transfer_details/'.$id_transfer);
    }
    public function transfer_dc($id){
        $q['tab_active'] = 'B to B Transfer';
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
        $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0);   //(transfer_id,need responder branch stock)       
        $this->load->view('transfer/transfer_dc', $q);
    }   
    public function branch_stock_shipment(){
        $q['tab_active'] = ''; 
        $q['title'] = 'Branch Stock Shipments'; 
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');
        $type=0;
            if($role_type==1){
            $q['idbranch']=$this->session->userdata('idbranch');    
        }else{
            $q['idbranch']=$this->session->userdata('idbranch');
        } 
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_to('4', $q['idbranch'] ,"","","",$type);        
        $this->load->view('transfer/stock_shipment', $q);
    }
    
    public function receive_b2b_shipment($id){
         $q['tab_active'] = 'Receive Shipment'; 
        $user_id=$this->session->userdata('id_users');        
        $role_type=$this->session->userdata('role_type');         
        if($role_type==1){
            $idbranch=$this->session->userdata('idbranch');    
        }else{
            $idbranch=$this->session->userdata('idbranch');
        } 
        $q['one_click_receive'] = $this->General_model->get_stock_receive_type()->one_click_receive; 
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);                 
        if($q['transfer_data'][0]->idbranch==$idbranch && $q['transfer_data'][0]->status==4){            
            $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
            $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0);        
            $this->load->view('transfer/stock_receive', $q);
        }else{
            return redirect('transfer/branch_stock_shipment');
        }
    }
    
    public function ajx_receive_stock(){        
        $transfer_id=$this->input->post('transfer_id'); 
        $idbranch=$this->input->post('idbranch');         
        $remark=$this->input->post('remark');         
        $user_id=$this->session->userdata('id_users');        
        $res = $this->Transfer_model->receive_b2b_shipment($transfer_id,$idbranch,$remark,$user_id);        
        if ($res) {    
            $out_date=$this->Transfer_model->get_transfer_product_by_transferid($transfer_id);
            $imei_history=array();
            $datetime = date('Y-m-d H:i:s');
            $date = date('Y-m-d');
            foreach ($out_date as $data){
                 $array = explode(',', $data->imei_no);   
                    foreach($array as $imei){                       
                        $imei_history[]=array(
                                    'imei_no' =>$imei,
                                    'entry_type' => 'Received',
                                    'entry_time' => $datetime,
                                    'date' => $date,
                                    'idbranch' => $data->idbranch,
                                    'idgodown' => $data->idgodown,
                                    'idvariant' => $data->idvariant,
                                    'idimei_details_link' => 15, // Outward from imei_details_link table
                                    'iduser' => $user_id,
                                    'idlink' => $transfer_id,
                                    'transfer_from' =>$data->transfer_from
                                );
                }
            }
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }            
           $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
           die($output);
       } else {    
           $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
           die($output);
       }
    }
	
	public function branch_doa_stock_shipment(){
        $q['tab_active'] = ''; 
        $q['title'] = 'Branch Stock Shipments'; 
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');      
        $type=1;         
        $q['idbranch']=$this->session->userdata('idbranch');    
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_bystatus_idbranch_to('4', $q['idbranch'] ,"","","",$type);        
        $this->load->view('transfer/doa_stock_shipment', $q);
    }
    
    public function receive_doa_b2b_shipment($id){
         $q['tab_active'] = 'Receive Shipment'; 
        $user_id=$this->session->userdata('id_users');        
        $role_type=$this->session->userdata('role_type');         
        if($role_type==1){
            $idbranch=$this->session->userdata('idbranch');    
        }else{
            $idbranch=$this->session->userdata('idbranch');
        } 
        $q['one_click_receive'] = $this->General_model->get_stock_receive_type()->one_click_receive; 
        $q['transfer_data'] = $this->Transfer_model->get_transfer_byid($id);                 
        if($q['transfer_data'][0]->idbranch==$idbranch && $q['transfer_data'][0]->status==4){            
            $q['branch_data']=$this->General_model->get_branch_byids(array($q['transfer_data'][0]->transfer_from,$q['transfer_data'][0]->idbranch));        
            $q['transfer_product'] = $this->Transfer_model->get_transfer_products_byid($id,0);        
            $this->load->view('transfer/doa_stock_receive', $q);
        }else{
            return redirect('transfer/branch_doa_stock_shipment');
        }
    }
    
    public function ajx_receive_doa_stock(){        
        $transfer_id=$this->input->post('transfer_id'); 
        $idbranch=$this->input->post('idbranch');         
        $remark=$this->input->post('remark');         
        $user_id=$this->session->userdata('id_users');        
        $res = $this->Transfer_model->receive_b2b_shipment($transfer_id,$idbranch,$remark,$user_id);        
        if ($res) {    
            $out_date=$this->Transfer_model->get_transfer_product_by_transferid($transfer_id);
            $imei_history=array();
            $datetime = date('Y-m-d H:i:s');
            $date = date('Y-m-d');
            $imeis=array();
            foreach ($out_date as $data){
                 $array = explode(',', $data->imei_no);   
                    foreach($array as $imei){                       
                        $imei_history[]=array(
                                    'imei_no' =>$imei,
                                    'entry_type' => 'Received',
                                    'entry_time' => $datetime,
                                    'date' => $date,
                                    'idbranch' => $data->idbranch,
                                    'idgodown' => $data->idgodown,
                                    'idvariant' => $data->idvariant,
                                    'idimei_details_link' => 15, // Outward from imei_details_link table
                                    'iduser' => $user_id,
                                    'idlink' => $transfer_id,
                                    'transfer_from' =>$data->transfer_from
                                );
                    }
                   $imeis = array_merge($imeis,$array);
            }            
            if(count($imeis)>0){                
                $this->Transfer_model->update_doa_reconcilliation_details($imeis,$idbranch);
            }
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }            
           $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
           die($output);
       } else {    
           $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
           die($output);
       }
    }
    public function store_stock_transfer_report() {
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');  
        $branch = $this->session->userdata('idbranch');
        $q['transfer_data'] = array();
        if($role_type==0){
             if($level==3){
                $q['branch_data_to'] = $this->General_model->get_branches_by_user($user_id);                  
                }else{
                    $q['branch_data_to'] = $this->General_model->get_active_branch_data();                     
                }
                $q['branch_data_from'] = $q['branch_data_to'];
        }elseif($role_type==1){
            $q['branch_data_to'] = $this->General_model->get_branches_by_warehouseid($branch);              
            $q['branch_data_from'] = $q['branch_data_to'];
        }elseif($role_type==2){
            $q['branch_data_to'] = $this->General_model->get_branch_array_byid($branch);  
            $q['transfer_data'] = $this->Transfer_model->get_transfer_data_by_idbranch_date($branch,'','','');  
            $idwarehouse=$this->General_model->get_branch_byid($branch)->idwarehouse;
            $q['branch_data_from'] = $this->General_model->get_branches_by_warehouseid($idwarehouse); 
        }
        
        $q['tab_active'] = '';
        $q['title']='Stock Transfer Report';
        $this->load->view('transfer/transfer_stock_report', $q);
    }
    public function store_stock_transfer_report_old() {
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');  
        $branch = $this->session->userdata('idbranch');
        $q['tab_active'] = '';
        $q['title']='Stock Transfer Report';
        $q['transfer_data'] = $this->Transfer_model->get_transfer_data_by_idbranch_date($branch,'','','');        
        $idwarehouse=$this->General_model->get_branch_byid($branch)->idwarehouse;
        $q['branch_data'] = $this->General_model->get_branches_by_warehouseid($idwarehouse);  
        $this->load->view('transfer/transfer_stock_report', $q);
    }
    
    public function ajax_store_stock_transfer_report() {        
//        die(print_r($_POST));
        $idbranch = $this->input->post('idbranch');
        $idbranch_other = $this->input->post('idbranch_other');
        $dateto = $this->input->post('dateto');
        $datefrom = $this->input->post('datefrom');
         
        $transfer_data = $this->Transfer_model->get_transfer_data_by_idbranch_date($idbranch,$datefrom,$dateto,$idbranch_other);
        ?>
        <thead class="fixedelement" style="text-align: center;position: none !important;">   
            <th>Mandate </th>
            <th>Branch From</th>
            <th>Branch  To</th>
            <th>Godown</th>
            <th>Brand</th>
            <th>Model</th>
            <th>IMEI</th>
            <th>Qty</th>
            <th>Request Date</th>
            <th>Dispatch Date</th>
            <th>Received Date</th>                
        </thead>
        <tbody>
            <?php foreach ($transfer_data as $transfer){ 
               $string_imei=rtrim($transfer->imei_no,',');                   
                $imei_array=explode(',', $string_imei);
                foreach ($imei_array as $imei){ 
                ?>
            <tr>
                <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Transfer/transfer_details/<?php echo $transfer->idtransfer ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $transfer->idtransfer ?></b></a></td>
                <td><?php echo $transfer->branch_from?></td>
                <td><?php echo $transfer->branch_to ?></td>
                <td><?php echo $transfer->godown_name ?></td>
                <td><?php echo $transfer->brand_name ?></td>
                <td><?php echo $transfer->full_name ?></td>
                <td><?php echo $imei ?></td>
                <td><?php echo $transfer->qty ?></td>
                <td><?php echo $transfer->date ?></td>
                <td><?php echo $transfer->dispatch_date ?></td>
                <td><?php echo $transfer->shipment_received_date ?></td>                    
            </tr>
            <?php }} ?>
        </tbody>    
        <?php
    }
   
}