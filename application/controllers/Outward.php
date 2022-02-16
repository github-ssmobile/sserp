<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outward extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Allocation_model");        
        $this->load->model("General_model");
        $this->load->model("Outward_model");       
        $this->load->model("Inward_model");   
        $this->load->model("Transfer_model");   
        
        date_default_timezone_set('Asia/Kolkata');
    }
    public function ready_to_outward(){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');
        $idwarehouse=$this->session->userdata('idbranch'); 
        $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
        $q['stock_allocation'] = $this->Allocation_model->get_ready_to_outward_stock_allocation($idwarehouse,$user_id);
        $this->load->view('outward/ready_to_scan', $q);
    } 
    public function stock_outward($id){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');
        $q['stock_allocation'] = $this->Allocation_model->get_branch_allocation_by_id_for_outward($id);        
        if($q['stock_allocation'][0]->status==2 && ($q['stock_allocation'][0]->scan_by==$user_id || $q['stock_allocation'][0]->scan_by==0)){
            $allodata = array(
                'scan_by' => $user_id
            );
            $id_array=array($id);
            $branch=array();
            $this->Allocation_model->update_allocation_status($id_array, $allodata,$branch);
            $q['w_idcompany']=$this->General_model->get_branch_byid($q['stock_allocation'][0]->idwarehouse)->idcompany;
            $q['gst_type']=0;
            
            if($q['stock_allocation'][0]->idcompany != $q['w_idcompany']){
                $q['gst_type']=1;
            }            
            $this->load->view('outward/stock_outward', $q);        
        }else{
            redirect('Outward/ready_to_outward');    
        }
    }     
    public function save_outward(){
        
        $idbranch=$this->input->post('idbranch');
        $idwarehouse=$this->input->post('idwarehouse');        
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
        $price=$this->input->post('price');
        $cgst_per=$this->input->post('cgst_per');
        $scanned=$this->input->post('scanned');
        $count= count($idvariants);
        $idallocation=$this->input->post('idallocation');          
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');  
        $remark=$this->input->post('remark'); 
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
                'idbranch_from' => $idwarehouse,
                'idbranch_to' =>  $idbranch,
                'idcompany_from' =>  $idcompany_from,
                'idcompany_to' =>  $idcompany_to,
                'sales_invoice' => $sale_inv,
                'purchase_invoice' => $purc_inv,
                'total_product' => $count,            
                'remark' => $remark,           
                'entry_by' => $iduser,            
                'idoutward_transfer' => $idallocation,
                'transaction_type'  => 'Outward',
                'gst_type' => $this->input->post('gst_type')
            );
            
            $idinterstate = $this->Transfer_model->save_inter_state($data_interstate);            
        }
        ////  Outward Data////        
        $data = array(
            'date' =>  $date,
            'idbranch' => $idbranch,
            'idwarehouse' =>  $idwarehouse,
            'sales_invoice' => $sale_inv,
            'purchase_invoice' => $purc_inv,
            'total_product' => $count,            
            'outward_remark' => $remark,
            'outward_by' => $iduser,
            'scan_time' => $datetime,            
            'idstock_allocation' => $idallocation,
            'gst_type' => $this->input->post('gst_type')
        );
        
        $idoutward = $this->Outward_model->save_outward($data);
        $outward_product=array();
        $inward_stock_sku=array();
        $stock_array=array();
        $imei_history=array();
        $interstate_product=array();
        
        $update_stock=array();
        for($i=0;$i<$count;$i++){
            if($idskutype_s[$i]==4){                
                ////  Outward Product Data For QTY SKU ////                
                $outward_product[] = array(
                    'date' =>  $date,
                    'idbranch' => $idbranch,      
                    'imei_no' => '',
                    'idskutype' => $idskutype_s[$i],
                    'idgodown' => $idgodown[$i],
                    'idproductcategory' => $idproductcategory[$i],
                    'idcategory' => $idcategory_s[$i],
                    'idmodel' => $idmodel_s[$i],
                    'idvariant' => $idvariants[$i],
                    'idbrand' => $idbrand[$i],
                    'qty' => $qty[$i],                    
                    'idoutward' => $idoutward,
                    'price' => $price[$i],
                    'cgst_per' => $cgst_per[$i],
                    'sgst_per' => $cgst_per[$i],
                    'igst_per' => ($cgst_per[$i]*2),                    
                );  
                
                if($this->input->post('gst_type')==1){
                    $interstate_product[] = array(
                       'date' =>  $date,
                       'idbranch' => $idbranch,        
                       'imei_no' => '',
                       'idskutype' => $idskutype_s[$i],
                       'idgodown' => $idgodown[$i],
                       'idproductcategory' => $idproductcategory[$i],
                       'idcategory' => $idcategory_s[$i],
                       'idmodel' => $idmodel_s[$i],
                       'idvariant' => $idvariants[$i],
                       'idbrand' => $idbrand[$i],
                       'qty' => $qty[$i],                         
                       'idinterstate' => $idinterstate,
                       'price' => $price[$i],
                       'cgst_per' => $cgst_per[$i],
                       'sgst_per' => $cgst_per[$i],
                       'igst_per' => ($cgst_per[$i]*2),
                   );
                }
                
                ////  Stock Reflection ////     
                
                //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($idvariants[$i], $idwarehouse, $idgodown[$i], $qty[$i]);
                $update_stock[]="UPDATE stock SET qty = qty - ".$qty[$i]." WHERE idvariant = ".$idvariants[$i]." AND idgodown = ".$idgodown[$i]." AND idbranch = ".$idwarehouse."; ";
                $inward_stock_sku[] = array(
                    'date' =>  $date,
                    'outward_time' => $datetime,
                    'idbranch' => 0,
                    'temp_idbranch' => $idbranch,
                    'transfer_from' => $idwarehouse,
                    'outward_dc' => $idoutward,
                    'outward_remark' => $remark,
                    'outward_by' => $iduser,
                    'product_name' => $product_name[$i],
                    'idskutype' => 4,
                    'idgodown' => $idgodown[$i],
                    'idproductcategory' => $idproductcategory[$i],
                    'idcategory' => $idcategory_s[$i],
                    'idmodel' => $idmodel_s[$i],
                    'idvariant' => $idvariants[$i],
                    'idbrand' => $idbrand[$i],
                    'created_by' => $iduser,
                    'idvendor' => 1,
                    'qty' => $qty[$i],
                    'outward' => 1,
                );                 
                
            }else{
             $imeis = explode(',', $scanned[$i]);
             for ($j=0;$j < count($imeis)-1;$j++){                
                   ////  Outward Product Data For IMEI/SRNO SKU ////                     
                 $outward_product[] = array(
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
                    'idoutward' => $idoutward,
                    'price' => $price[$i],
                    'cgst_per' => $cgst_per[$i],
                    'sgst_per' => $cgst_per[$i],
                    'igst_per' => ($cgst_per[$i]*2),
                );
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
                    'outward' => 1,
                    'outward_dc' => $idoutward,
                    'outward_time' => $datetime,
                    'outward_by' => $iduser,
                    'idbranch' => 0,
                    'idgodown' => $idgodown[$i],
                    'temp_idbranch' => $idbranch,
                    'transfer_from' => $idwarehouse,
                    'outward_remark' => $remark,
                );   
                $imei_history[]=array(
                    'imei_no' =>$imeis[$j],
                    'entry_type' => 'Outward (Scanned)',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $idgodown[$i],
                    'idvariant' => $idvariants[$i],
                        'idimei_details_link' => 5, // Outward from imei_details_link table
                        'iduser' => $iduser,
                        'idlink' => $idallocation,
                        'transfer_from' => $idwarehouse
                    );
                
            } 
        }
    } 
    
    if(count($interstate_product)>0){
        $this->Transfer_model->save_inter_state_product($interstate_product);                  
    }        
    $this->Outward_model->save_outward_product($outward_product);  
    if(count($inward_stock_sku)>0){ 
        $this->Inward_model->save_stock_batch($inward_stock_sku);     
        foreach ($update_stock as $data){
            $this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($data);    
        }
            //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($update_stock);
    }
    if(count($stock_array)>0){
        $this->Outward_model->update_batch_stock_byimei($stock_array);        
    }       
    $allodata = array(
        'status' => 3            
    );
    $id_array=array($idallocation);
    $branch=array();
    $this->Allocation_model->update_allocation_status($id_array, $allodata,$branch);
    
    if(count($imei_history) > 0){
        $this->General_model->save_batch_imei_history($imei_history);
    }
    
    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
        die($output);
    } else {
        $this->db->trans_commit();
        $output = json_encode(array("result" => "false", "data" => "success", "message" => "$idallocation"));
        die($output);
    }
    
}

public function ready_to_shipment(){
    $q['tab_active'] = ''; 
    $q['title'] = 'Ready To Shipment'; 
    $user_id=$this->session->userdata('id_users');       
    $idwarehouse=$this->session->userdata('idbranch'); 
    $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
    $q['stock_allocation'] = $this->Outward_model->get_outward_by_status_idbranch_date('0', '', '', '', $idwarehouse);
    $this->load->view('outward/ready_to_shipment', $q);
} 
public function outward_details($idallocation=null,$idoutward=null){
    if($idallocation==null && $idoutward==null){
        return redirect('Outward/404');
    }else{
        $q['tab_active'] = 'Outward';        
        $q['outward_data'] = $this->Allocation_model->get_branch_allocation_by_id($idallocation,$idoutward); 
        if(count($q['outward_data'])>0){
            $q['dispatch_data'] = $this->General_model->get_dispatch_type();
            $q['transport_vendor'] = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
            $q['branch_data']=$this->General_model->get_branch_byids(array($q['outward_data'][0]->id_warehouse,$q['outward_data'][0]->id_branch));        
            $this->load->view('outward/outward_details', $q);                    
        }else{
            return redirect($this->session->userdata('dashboard'));                
        }
    }
}

public function outward_dc($idallocation=null,$idoutward=null){
    if($idallocation==null && $idoutward==null){
        return redirect('Outward/404');
    }else{
        $q['tab_active'] = 'Outward';        
        $q['outward_data'] = $this->Allocation_model->get_branch_allocation_by_id($idallocation,$idoutward); 
//            die(print_r($q['outward_data']));
        if(count($q['outward_data'])>0){
            $q['dispatch_data'] = $this->General_model->get_dispatch_type();
            $q['transport_vendor'] = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
            $q['branch_data']=$this->General_model->get_branch_byids(array($q['outward_data'][0]->id_warehouse,$q['outward_data'][0]->id_branch));        
            $this->load->view('outward/outward_dc', $q);                    
        }else{
            return redirect($this->session->userdata('dashboard'));                
        }
    }
}
public function save_shipment_details() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        // In transit
    $this->db->trans_begin();
    $datetime = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    $idoutward = $this->input->post('id_outward');
    $idallocation = $this->input->post('id_allocation');    
    $user_id=$this->session->userdata('id_users');
     if($this->input->post('idtvendors')=='9' && ($this->input->post('vehicle_no')=='' || $this->input->post('po_lr_no')=='')){

       $this->session->set_flashdata('save_data', 'Shipment is aborted. If transport is self then enter vehicle no and po lr no');
       return redirect('Outward/outward_details/'.$idallocation.'/0');

   } else{
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
     'status' => 1,
     'vehicle_no' => $this->input->post('vehicle_no'),
 );
    $out_date=$this->Transfer_model->get_outward_product_by_outwardid($idoutward);
    $imei_history=array();
    foreach ($out_date as $dataa){            
        $imei_history[]=array(
            'imei_no' =>$dataa->imei_no,
            'entry_type' => 'Outward (In-transit)',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $dataa->idbranch,
            'idgodown' => $dataa->idgodown,
            'idvariant' => $dataa->idvariant,
                            'idimei_details_link' => 11, // Outward from imei_details_link table
                            'iduser' => $user_id,
                            'idlink' => $idallocation,
                            'transfer_from' => $dataa->transfer_from
                        );
    } 
    if(count($imei_history) > 0){
        $this->General_model->save_batch_imei_history($imei_history);
    }
    $this->Transfer_model->save_outward_shipment_details($idoutward, $data);
    $outward_data = array( 'shipment_status' => 1 );
    $this->Transfer_model->update_outward_data($idoutward, $outward_data);
    $allodata = array(            
        'status' => 4,
    );
    $this->Allocation_model->edit_stock_allocation($idallocation, $allodata);
    if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $this->session->set_flashdata('save_data', 'Shipment is aborted. Try again with same details');
    }else{
        $this->db->trans_commit();
        $this->session->set_flashdata('save_data', 'Shipment added successfully');
    }
    return redirect('Outward/ready_to_shipment');
}
}
public function ready_to_receive(){
    $q['tab_active'] = ''; 
    $q['title'] = 'Warehouse Stock Shipments'; 
    $user_id=$this->session->userdata('id_users');
    $idbranch=$this->session->userdata('idbranch');                   
    $q['stock_allocation'] = $this->Outward_model->get_outward_by_status_idbranch_date('1', $idbranch, '', '', '');
    $this->load->view('outward/ready_to_shipment', $q);
}
public function receive_w_shipment($idallocation){
    $q['tab_active'] = 'Receive Shipment'; 
    $user_id=$this->session->userdata('id_users');
    $idbranch=$this->session->userdata('idbranch');   
    $q['one_click_receive'] = $this->General_model->get_stock_receive_type()->one_click_receive;        
    $q['outward_data'] = $this->Allocation_model->get_branch_allocation_by_id($idallocation,0);                 
    if($q['outward_data'][0]->id_branch==$idbranch && $q['outward_data'][0]->a_status==4){            
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['outward_data'][0]->id_warehouse,$q['outward_data'][0]->id_branch));        
        $this->load->view('outward/stock_receive', $q);
    }else{
        return redirect('outward/ready_to_receive');
    }
}


public function ajx_receive_stock(){
    $allocation_id=$this->input->post('allocation_id');
    $outward_id=$this->input->post('outward_id'); 
    $idbranch=$this->input->post('idbranch');         
    $remark=$this->input->post('remark');                 
    $user_id=$this->session->userdata('id_users');
    $datetime = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    
    $res = $this->Outward_model->receive_stock_shipment($allocation_id,$outward_id,$idbranch,$remark,$user_id);
    
    if ($res) {     
        $out_date=$this->Transfer_model->get_outward_product_by_outwardid($outward_id);
        $imei_history=array();
        foreach ($out_date as $data){
            $imei_history[]=array(
                'imei_no' =>$data->imei_no,
                'entry_type' => 'Received',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $data->idbranch,
                'idgodown' => $data->idgodown,
                'idvariant' => $data->idvariant,
                                'idimei_details_link' => 11, // Outward from imei_details_link table
                                'iduser' => $user_id,
                                'idlink' => $allocation_id,
                                'transfer_from' => $data->transfer_from
                            );
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

public function store_inward_stock_report() {
    $user_id=$this->session->userdata('id_users'); 
    $role_type=$this->session->userdata('role_type');   
    $level=$this->session->userdata('level');  
    $branch = $this->session->userdata('idbranch');
    $q['tab_active'] = '';
    $q['title']='Stock Inward Report';
    $report_type='inward';
    $q['outward_data'] = $this->Outward_model->get_outward_data_by_idbranch_date($branch,'','','',$report_type);        
    $this->load->view('outward/inward_stock_report', $q);
}
public function ajax_store_inward_stock_report() {        
    $idbranch = $this->input->post('idbranch');
    $dateto = $this->input->post('dateto');
    $datefrom = $this->input->post('datefrom');
    $report_type='inward';
    $outward_data = $this->Outward_model->get_outward_data_by_idbranch_date($idbranch,$datefrom,$dateto,'',$report_type);
    ?>
    <thead class="fixedelement" style="text-align: center;position: none !important;">   
        <th>Mandate </th>                
        <th>Received Date</th>                
        <th>Branch From</th>
        <th>Branch  To</th>
        <th>Godown</th>
        <th>Brand</th>
        <th>Model</th>
        <th>IMEI</th>
        <th>Qty</th>
        <th>Allocation Date</th>
        <th>Dispatch Date</th>
        <th>Outward Remark</th>
        <th>Dispatch Remark</th>
        <th>Received Remark</th>
    </thead>
    <tbody>
        <?php foreach ($outward_data as $outward){ ?>
            <tr>
                <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/0/<?php echo $outward->idoutward ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $outward->idoutward ?></b></a></td>
                <td><?php echo $outward->shipment_received_date ?></td>                    
                <td><?php echo $outward->branch_from?></td>
                <td><?php echo $outward->branch_to ?></td>
                <td><?php echo $outward->godown_name ?></td>
                <td><?php echo $outward->brand_name ?></td>
                <td><?php echo $outward->full_name ?></td>
                <td><?php echo $outward->imei_no ?></td>
                <td><?php echo $outward->qty ?></td>
                <td><?php echo $outward->date ?></td>
                <td><?php echo $outward->dispatch_date ?></td>
                <td><?php echo $outward->outward_remark ?></td>
                <td><?php echo $outward->shipment_remark ?></td>
                <td><?php echo $outward->shipment_received_remark ?></td>
            </tr>
        <?php } ?>
    </tbody>    
    <?php
}

public function w_outward_stock_report() {
    $user_id=$this->session->userdata('id_users'); 
    $role_type=$this->session->userdata('role_type');   
    $level=$this->session->userdata('level');  
    $idwarehouse = $this->session->userdata('idbranch');
    $q['tab_active'] = '';
    $q['title']='Stock Outward Report';
    $report_type='outward';
    $q['branch_data'] = $this->General_model->get_active_branchs($idwarehouse);   
    $date_filter='date';
    $q['outward_data'] = $this->Outward_model->get_outward_data_by_idbranch_date('','','',$idwarehouse,$report_type,$date_filter);        
    $this->load->view('outward/outward_stock_report', $q);
}
public function ajax_w_outward_stock_report() {        
    $idbranch_to = $this->input->post('idbranch_to');
    $dateto = $this->input->post('dateto');
    $datefrom = $this->input->post('datefrom');
    $date_filter = $this->input->post('date_filter');
    $idwarehouse = $this->session->userdata('idbranch');    
    
    $report_type='outward';
    $outward_data = $this->Outward_model->get_outward_data_by_idbranch_date($idbranch_to,$datefrom,$dateto,$idwarehouse,$report_type,$date_filter);
    ?>
    <thead class="fixedelement" style="text-align: center;position: none !important;">   
        <th>Sr no</th>  
        <th>Mandate </th>                
        <th>Outward Date</th>
        <th>Dispatch Date</th>
        <th>Branch From</th>
        <th>Branch  To</th>
        <th>Godown</th>
        <th>Brand</th>
        <th>Model</th>
        <th>IMEI</th>
        <th>RATE</th>
        <th>Qty</th>    
        <th>TAXABLE</th>    
        <th>GST RATE</th>    
        <th>CGST</th>    
        <th>SGST</th>    
        <th>IGST</th>    
        <th>TOTAL</th>    
        <th>Received Date</th>  
        <th>Dispatch type</th>                
        <th>Courier name</th>                
        <th>PO/LR no</th>                
        <th>No of Box</th> 
        <th>Status</th> 
    </thead>
    <tbody>
        <?php $i=1; foreach ($outward_data as $outward){ 
            
            $igstamt=0;$cgstamt=0;$sgstamt=0;
            $total_amount = ($outward->price*$outward->qty);
            if($outward->gst_type==1){                                        
                $cal = ($outward->igst_per + 100) / 100;
                $taxable = $total_amount / $cal;
                $igstamt = $total_amount - $taxable;
                $tigst = $igstamt;
                $tqty = $outward->qty;
//                                    $trate += $product->price;
//                                    $tdiscount += $product->discount_amt;
                $ttaxable = $taxable;
                $rate = $taxable / $outward->qty;
                $trate = $rate;
            }else{                                   
                $cal = ($outward->cgst_per + $outward->sgst_per + 100) / 100;
                $taxable = $total_amount / $cal;
                $cgst = $total_amount - $taxable;
                $cgstamt = $cgst / 2;
                $sgstamt=$cgstamt;
                $tcgst = $cgstamt;
                $tqty = $outward->qty;
//                                            $tdiscount += $product->discount_amt;
                $ttaxable = $taxable;
                $rate = $taxable / $outward->qty;
                $trate = $rate;
            }
            
            ?>
            <tr>
               <td><?php echo $i; ?></td>
               <td><a target="_blank" class="thumbnail textalign" href="<?php echo site_url() ?>Outward/outward_details/0/<?php echo $outward->idoutward ?>" style="margin: 0 8px;padding: 5px !important;width: 40%;"><b style="color: #0e10aa !important;"><?php echo $outward->idoutward ?></b></a></td>                  
               <td><?php echo $outward->date ?></td>
               <td><?php echo $outward->dispatch_date ?></td>
               <td><?php echo $outward->branch_from?></td>
               <td><?php echo $outward->branch_to ?></td>
               <td><?php echo $outward->godown_name ?></td>
               <td><?php echo $outward->brand_name ?></td>
               <td><?php echo $outward->full_name ?></td>
               <td><?php echo $outward->imei_no ?></td>                                     
               <td><?php echo round($rate,2) ?></td>                    
               <td><?php echo $outward->qty ?></td>   
               <td><?php echo round($taxable, 2) ?></td>   
               <td><?php echo $outward->igst_per ?></td>   
               
               <td><?php echo round($cgstamt, 2) ?></td>                    
               <td><?php echo round($sgstamt, 2) ?></td> 
               <td><?php echo round($igstamt, 2) ?></td>  
               <td><?php echo $total_amount ?></td>                    
               
               <td><?php echo $outward->shipment_received_date ?></td> 
               <td><?php echo $outward->dispatch_type ?></td>                    
               <td><?php echo $outward->courier_name ?></td>                    
               <td><?php echo $outward->po_lr_no ?></td>                    
               <td><?php echo $outward->no_of_boxes ?></td>  
               <td><?php if($outward->out_status==0){echo 'Scanned';}elseif($outward->out_status==1){ echo 'Dispatched';}elseif($outward->out_status==2){ echo 'Received';} ?></td>  
           </tr>
           <?php $i++; } ?>
       </tbody>    
       <?php
   }
   
   public function transfer_rech_ins_balance()
   {   
    $q['tab_active'] = '';
    $sale_type = array(1);
    $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
    $this->load->view('outward/re_allocation', $q);
}
public function ajax_model_variants_allocation_data1(){
    $variantid = $this->input->post('variant'); 
    $var_data = $this->General_model->get_active_variants_id($variantid);
    $user_id=$this->session->userdata('id_users');
    $warehouse=$this->session->userdata('idbranch');
        $modelid = $var_data->idmodel;//$this->input->post('model'); 
        $days = $this->input->post('days'); 
        $idgodown = $this->input->post('idgodown'); 
        $idskutype = $var_data->idsku_type;
        $idbrand = $var_data->idbrand;//$this->input->post('idbrand'); 
        $idproductcategory = $var_data->idproductcategory;//$this->input->post('idproductcategory');
        $allocation_type=1;
        $idcategory = $var_data->idcategory; 
        $variants = $this->Allocation_model->get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type);
//        die(print_r($variants));
        /// Temporary to show all branches to gandhinager warehouse(7) for model allocation ///
        $id_warehouse=0;
        if($warehouse==7){
            $branch_data = $this->General_model->get_active_branchs();  
            
        }else{
            $id_warehouse=$warehouse;
            $branch_data = $this->General_model->get_branches_by_warehouseid($warehouse);                               
        }
        // *END* //
        $model_data=array();
        $counts= count($variants); 
        ?>
        
        <thead class="fixheader" style="text-align: center;height: 68px;">
            <input type="hidden" name="idmodel" value="<?php echo $modelid; ?>" />            
            <input type="hidden" name="idvariant" value="<?php echo $variantid; ?>" />
            <input type="hidden" name="idcategory" value="<?php echo $idcategory; ?>" />
            <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>" />
            <input type="hidden" name="idskutype" value="<?php echo $idskutype; ?>" />
            <input type="hidden" name="idproductcategory" value="<?php echo $idproductcategory; ?>" />
            <th  colspan='3'><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" /></th>    
            <?php
        $idbranch=0; //All branch
        foreach ($variants as $variant){             
            $stock_qty=$variant->ho_stock_qty;
            $model_data[] = $this->Allocation_model->get_variants_allocation_data($id_warehouse,$idbranch,$days,$variant->id_variant,$idgodown,$allocation_type);
            ?>
            <th colspan='3' style="text-align: center;">
                <input type="hidden" name="variants[]" value="<?php echo $variant->id_variant; ?>" /><?php echo $variant->full_name;?>
                <input type="hidden" name="product_name[]" value="<?php echo $variant->full_name; ?>" />
                <?php   $ho_stock_qty=$variant->ho_stock_qty;
                $allocated_qty=$variant->allocated_qty;
                $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                ?>
                <br><?php echo "Warehouse - ".(($ho_stock_qty==null)?0:$ho_stock_qty); ?>&nbsp;&nbsp;
                <?php echo "Allocated - ".(($allocated_qty==null)?0:$allocated_qty); ?>&nbsp;&nbsp;
                <?php echo "Available - ".(($available==null)?0:$available); ?>&nbsp;&nbsp;
            </th>
        <?php }         
        ?>
    </thead>
    <thead class="fixheader1" style="text-align: center;height: 49px;">
        <th>Zone</th><th>Branch Category</th> <th>Branch</th>   
        <?php
        foreach ($variants as $variant){ 
            $stock_qty=$variant->ho_stock_qty;
            $full_name = clean($variant->full_name);
            ?>
            <!--<th>Placement Norm</th>-->
            <th>Stock</th>
            <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
            <!--<th>To be allocated</th>-->
            <?php   $ho_stock_qty=$variant->ho_stock_qty;
            $allocated_qty=$variant->allocated_qty;
            $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
            ?>
            <th>Quantity<input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /> </th>            
            <script>
                $(document).ready(function(){
                    $("#variant_data").on('input', '.<?php echo $full_name; ?>', function () {
                        var total_sum = 0;
                        var zone_sum = 0;
                        if($(this).val() && $(this).val() > 0){
                            $("#variant_data .<?php echo $full_name; ?>").each(function () {
                                var get_textbox_value = $(this).val();                    
                                if ($.isNumeric(get_textbox_value)) {
                                 total_sum += parseFloat(get_textbox_value);
                             }                  
                         }); 
                            
                            var branch = $(this).attr("branch");
                            var variant = $(this).attr("variant");
                            var w_ty = +$("input[name=<?php echo $full_name; ?>]").val();
                            if(total_sum > w_ty){
                                alert("Sorry, You dont have enough quantity!!");                                          
                                $(this).val("0");
                                $(this).removeAttr('style');
                                $(this).removeAttr('name');                                                                                   
                            }else{
                                $(this).attr('name', "qty["+branch+"]["+variant+"]");
                                $(this).attr('style',"background: #caffca;");   
                            }
                        }else{
                           $(this).removeAttr('style');
                           $(this).removeAttr('name');                         
                       }                     
                       var zone=$(this).attr("zone_name")+"<?php echo $full_name; ?>";    
                       var total_sum = 0;
                       $("#variant_data .<?php echo $full_name; ?>").each(function () {
                        var get_textbox_value = $(this).val();                    
                        if ($.isNumeric(get_textbox_value)) {                            
                         total_sum += parseFloat(get_textbox_value);
                     }else{
                         total_sum += 0;
                     }                  
                 });    
                       
                       $("#variant_data ."+zone).each(function () {
                        var get_textbox_value = $(this).val();                    
                        if ($.isNumeric(get_textbox_value)) {
                            zone_sum += parseFloat(get_textbox_value);
                        }                  
                    }); 
                       var zone=$(this).attr("zone_name")+"<?php echo $full_name; ?>";                                                        
                       $("."+zone).text(zone_sum);
                       var total="total<?php echo $full_name; ?>";                                                        
                       $("."+total).text(total_sum);
                       
                   });
                });            
            </script>
        <?php } ?>
    </thead>   
    <tr class="top_row fixheader2" style="background-color: #fffae1;"></tr>
    <tbody>
        <?php  
        $html="";
        $i=0;
        $zsum_pl=array();$zsum_stl=array();$zsum_sl=array();$zsum_qty=array();
        $zsum_pl1=array();$zsum_stl1=array();$zsum_sl1=array();$zsum_qty1=array();
        $old_name=$branch_data[0]->zone_name;
        foreach ($branch_data as $branch){ 
            if($old_name==$branch->zone_name){
            }else{ 
                $oldname=clean($old_name);
                ?>
                <tr class="fixedelement1" style="position: unset !important;">
                    <td></td>
                    <td></td>                                    
                    <td><b>Total</b></td>   
                    <?php for($j=0;$j<$counts; $j++){ 
                       $data1=$model_data[$j][$i];   
                       $fullname = clean($data1->full_name);    
                       ?>
                       <!--<td class="textalign"><b><?php // echo $zsum_pl[$j]; ?></b></td>-->                                    
                       <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
                       <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
                       <!--<td></td>-->                    
                       <td class="textalign"><b><lable class="<?php echo $oldname.$fullname; ?>" name="<?php echo $oldname.$fullname; ?>" ><?php echo $zsum_qty[$j] ?></lable></d></td>
                           <?php
                           
                       } ?>
                   </tr>
                   <?php   
                   
                   $zsum_pl=array();$zsum_stl=array();$zsum_sl=array();$zsum_qty=array();
                   
               }
               ?>
               <tr>
                <td class="fixleft"><?php echo $branch->zone_name; ?></td>                    
                <td class="fixleft1"><?php echo $branch->branch_category_name; ?></td>
                <td class="fixleft2"><?php echo $branch->branch_name; ?></td>
                <?php    
                
                for($j=0;$j<$counts; $j++){
                 $data=$model_data[$j][$i];   
                 $full_name = clean($data->full_name); 
                 
                 $stock=$data->stock_qty; 
                 if(isset($zsum_pl[$j])){
//                        $zsum_pl[$j]=$zsum_pl[$j]+$data->norm_qty; 
                    $zsum_stl[$j]=$zsum_stl[$j]+$stock;
                    $zsum_sl[$j]=$zsum_sl[$j]+$data->sale_qty; 
                    if($data->allocated_qty!=null){
                        $zsum_qty[$j]=$zsum_qty[$j]+$data->allocated_qty;  
//                            $zsum_qty1[$j] +=$data->allocated_qty;
                    }else{
                       $zsum_qty[$j]=$zsum_qty[$j]+0;  
//                             $zsum_qty1[$j] +=0;
                   }
                   
               }else{
//                        $zsum_pl[$j]=0+$data->norm_qty;   
                $zsum_stl[$j]=0+$stock;
                $zsum_sl[$j]=0+$data->sale_qty; 
                if($data->allocated_qty!=null){
                    $zsum_qty[$j]=0+$data->allocated_qty;    
//                            $zsum_qty1[$j] +=$data->allocated_qty;
                }else{
                   $zsum_qty[$j]=0;  
//                             $zsum_qty1[$j]=0;
               }
           }
           if(isset($zsum_pl1[$j])){
               if($data->allocated_qty!=null){
                $zsum_qty1[$j] +=$data->allocated_qty;                                                     
            }else{
               $zsum_qty1[$j] +=0;
           }
       }else{
        if($data->allocated_qty!=null){
            $zsum_qty1[$j] =$data->allocated_qty;                            
        }else{
            $zsum_qty1[$j] =0;                            
        }
//                        $zsum_pl1[$j] =0; 
        $zsum_stl1[$j] =0; $zsum_sl1[$j] =0;
        
    }
//                    $zsum_pl1[$j] +=$data->norm_qty;
    $zsum_stl1[$j] +=$stock; $zsum_sl1[$j] +=$data->sale_qty;
//                    $zsum_qty1[$j] +=$data->allocated_qty;
//                   if($data->allocated_qty!=null){
//                    $zsum_qty[$j]=$zsum_qty[$j]+$data->allocated_qty;                     
//                   }else{
//                        $zsum_qty[$j]=$zsum_qty[$j]+0;  
//                   }
    $zonename=clean($branch->zone_name);
    ?>
    <!--<td class="textalign"><?php // echo $data->norm_qty; ?></td>-->                    
    <td class="textalign"><?php echo ($stock); ?></td>
    <td class="textalign"><?php echo $data->sale_qty; ?></td>
    <!--<td class="textalign"><?php // echo (($data->norm_qty)-$stock) ?></td>-->
    <td><input type="text" zone_name="<?php echo $zonename;?>" class="<?php echo $zonename.$full_name." " ;?><?php echo " ".$full_name; ?> form-control input-sm" branch="<?php echo $branch->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$branch->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                                   
    <?php 
    
    
    
} ?>                
</tr>                
<?php $i++; $old_name=$branch->zone_name; } $oldname=clean($old_name); ?>
<tr class="" style="background-color: #fffae1;position: unset !important;">
    <td></td>
    <td></td>                                    
    <td><b>Total</b></td>   
    <?php for($j=0;$j<$counts; $j++){ 
       $data1=$model_data[$j][($i-1)];   
       $fullname = clean($data1->full_name);  
       ?>
       <!--<td class="textalign"><b><?php // echo $zsum_pl[$j]; ?></b></td>-->                                    
       <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
       <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
       <!--<td></td>-->
       <td class="textalign"><b><lable class="<?php echo $oldname.$fullname ?>" name="<?php echo $oldname.$fullname ?>" ><?php echo $zsum_qty[$j] ?></lable></b></td>
   <?php } ?>
</tr>
<tr class="" style="background-color: #fffae1;position: unset !important;">
    <?php $html ="";
    $html.='<td></td><td></td><td><b>Over All Total</b></td>';                                                        
    for($j=0;$j<$counts; $j++){ 
       $data1=$model_data[$j][($i-1)];   
       $fullname = clean($data1->full_name);                           
//                    $html.='<td class="textalign"><b>'.$zsum_pl1[$j].'</b></td>';                                    
       $html.='<td class="textalign"><b>'.$zsum_stl1[$j].'</b></td>';
       $html.='<td class="textalign"><b>'.$zsum_sl1[$j].'</b></td>';                    
       $html.='<td class="textalign"><lable class="total'.$fullname.'" name="total'.$fullname.'" >'.$zsum_qty1[$j].'</lable></td>';                     
   }
   echo $html;
//                     $_SESSION['top_row']=$html;
   ?>                
</tr>
</tbody>         
|
<?php echo $html; ?>
<?php }
public function ajax_model_variants_allocation_data(){
    $variantid = $this->input->post('variant'); 
    $var_data = $this->General_model->get_active_variants_id($variantid);
    $user_id=$this->session->userdata('id_users');
    $warehouse=$this->session->userdata('idbranch');
        $modelid = $var_data->idmodel;//$this->input->post('model'); 
        $days = $this->input->post('days'); 
        $idgodown = $this->input->post('idgodown'); 
        $idskutype = $var_data->idsku_type;
        $idbrand = $var_data->idbrand;//$this->input->post('idbrand'); 
        $idproductcategory = $var_data->idproductcategory;//$this->input->post('idproductcategory');
        $allocation_type=1;    
        $idcategory = $var_data->idcategory; 
        $variants = $this->Allocation_model->get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type);
//        die(print_r($variants));
        /// Temporary to show all branches to gandhinager warehouse(7) for model allocation ///
        $id_warehouse=$warehouse;
        if($warehouse==7){             
            $branch_data = $this->General_model->get_active_branchs();              
        }else{            
            $branch_data = $this->General_model->get_branches_by_warehouseid($warehouse);                               
        }
        // *END* //
        $model_data=array();
        $counts= count($variants); 
        ?>
        
        <thead class="fixheader" style="text-align: center;height: 68px;">
            <input type="hidden" name="idmodel" value="<?php echo $modelid; ?>" />            
            <input type="hidden" name="idvariant" value="<?php echo $variantid; ?>" />
            <input type="hidden" name="idcategory" value="<?php echo $idcategory; ?>" />
            <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>" />
            <input type="hidden" name="idskutype" value="<?php echo $idskutype; ?>" />
            <input type="hidden" name="idproductcategory" value="<?php echo $idproductcategory; ?>" />
            <th  colspan='3'><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" />   </th>    
            <?php
        $idbranch=0; //All branch
        foreach ($variants as $variant){             
            $stock_qty=$variant->ho_stock_qty;
            $model_data[] = $this->Allocation_model->get_variants_allocation_data($id_warehouse,$idbranch,$days,$variant->id_variant,$idgodown,$allocation_type);
            ?>
            <th colspan='4' style="text-align: center;">
                <input type="hidden" name="variants[]" value="<?php echo $variant->id_variant; ?>" /><?php echo $variant->full_name;?>
                <input type="hidden" name="product_name[]" value="<?php echo $variant->full_name; ?>" />
                <?php   $ho_stock_qty=$variant->ho_stock_qty;
                $allocated_qty=$variant->allocated_qty;
                $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                ?>
                <br><?php echo "Warehouse - ".(($ho_stock_qty==null)?0:$ho_stock_qty); ?>&nbsp;&nbsp;
                <?php echo "Allocated - ".(($allocated_qty==null)?0:$allocated_qty); ?>&nbsp;&nbsp;
                <?php echo "Available - ".(($available==null)?0:$available); ?>&nbsp;&nbsp;
                
            </th>
        <?php }         
        ?>
    </thead>
    <thead class="fixheader1" style="text-align: center;height: 49px;">
        <th>Zone</th><th>Branch Category</th> <th>Branch</th>   
        <?php
        foreach ($variants as $variant){ 
            $stock_qty=$variant->ho_stock_qty;
            $full_name = clean($variant->full_name);
            ?>
            <!--<th>Placement Norm</th>-->
            <th>Stock</th>
            <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
            <!--<th>To be allocated</th>-->
            <?php   $ho_stock_qty=$variant->ho_stock_qty;
            $allocated_qty=$variant->allocated_qty;
            $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
            ?>
            <th>Quantity<input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /> </th>            
            <script>
                $(document).ready(function(){
                    $("#variant_data").on('input', '.<?php echo $full_name; ?>', function () {
                        var total_sum = 0;
                        var zone_sum = 0;
                        if($(this).val() && $(this).val() > 0){
                            $("#variant_data .<?php echo $full_name; ?>").each(function () {
                                var get_textbox_value = $(this).val();                    
                                if ($.isNumeric(get_textbox_value)) {
                                 total_sum += parseFloat(get_textbox_value);
                             }                  
                         }); 
                            
                            var branch = $(this).attr("branch");
                            var variant = $(this).attr("variant");
                            var w_ty = +$("input[name=<?php echo $full_name; ?>]").val();
                            if(total_sum > w_ty){
                                alert("Sorry, You dont have enough quantity!!");                                          
                                $(this).val("0");
                                $(this).removeAttr('style');
                                $(this).removeAttr('name');                                                                                   
                            }else{
                                $(this).attr('name', "qty["+branch+"]["+variant+"]");
                                $(this).attr('style',"background: #caffca;");   
                            }
                        }else{
                           $(this).removeAttr('style');
                           $(this).removeAttr('name');                         
                       }                     
                       var zone=$(this).attr("zone_name")+"<?php echo $full_name; ?>";    
                       var total_sum = 0;
                       $("#variant_data .<?php echo $full_name; ?>").each(function () {
                        var get_textbox_value = $(this).val();                    
                        if ($.isNumeric(get_textbox_value)) {                            
                         total_sum += parseFloat(get_textbox_value);
                     }else{
                         total_sum += 0;
                     }                  
                 });    
                       
                       $("#variant_data ."+zone).each(function () {
                        var get_textbox_value = $(this).val();                    
                        if ($.isNumeric(get_textbox_value)) {
                            zone_sum += parseFloat(get_textbox_value);
                        }                  
                    }); 
                       var zone=$(this).attr("zone_name")+"<?php echo $full_name; ?>";                                                        
                       $("."+zone).text(zone_sum);
                       var total="total<?php echo $full_name; ?>";                                                        
                       $("."+total).text(total_sum);
                       
                   });
                });            
            </script>
        <?php } ?>
    </tr></thead>   
    <tr class="top_row fixheader2" style="background-color: #fffae1;"></tr>
    <tbody>
        <?php  
        $html="";
        $i=0;
        $zsum_pl=array();$zsum_stl=array();$zsum_sl=array();$zsum_qty=array();
        $zsum_pl1=array();$zsum_stl1=array();$zsum_sl1=array();$zsum_qty1=array();
        $old_name=$branch_data[0]->zone_name;
        foreach ($branch_data as $branch){ 
            if($old_name==$branch->zone_name){
             
            }else{ 
                $oldname=clean($old_name);
                ?>
                <tr class="fixedelement1" style="position: unset !important;">
                    <td></td>
                    <td></td>                                    
                    <td><b>Total</b></td>   
                    <?php for($j=0;$j<$counts; $j++){ 
                       $data1=$model_data[$j][$i];   
                       $fullname = clean($data1->full_name);    
                       ?>
                       <!--<td class="textalign"><b><?php // echo $zsum_pl[$j]; ?></b></td>-->                                    
                       <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
                       <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
                       <!--<td></td>-->                    
                       <td class="textalign"><b><lable class="<?php echo $oldname.$fullname; ?>" name="<?php echo $oldname.$fullname; ?>" ><?php echo $zsum_qty[$j] ?></lable></d></td>
                           <?php
                           
                       } ?>
                   </tr>
                   <?php   
                   
                   $zsum_pl=array();$zsum_stl=array();$zsum_sl=array();$zsum_qty=array();
                   
               }
               ?>
               <tr>
                <td class="fixleft"><?php echo $branch->zone_name; ?></td>                    
                <td class="fixleft1"><?php echo $branch->branch_category_name; ?></td>
                <td class="fixleft2"><?php echo $branch->branch_name; ?></td>
                <?php    
                
                for($j=0;$j<$counts; $j++){
                 $data=$model_data[$j][$i];   
                 $full_name = clean($data->full_name); 
                 
                 $stock=($data->stock_qty + $data->intra_stock_qty); 
                 if(isset($zsum_stl[$j])){
//                        $zsum_pl[$j]=$zsum_pl[$j]+$data->norm_qty; 
                    $zsum_stl[$j]=$zsum_stl[$j]+$stock;
                    $zsum_sl[$j]=$zsum_sl[$j]+$data->sale_qty; 
                    if($data->allocated_qty!=null){
                        $zsum_qty[$j]=$zsum_qty[$j]+$data->allocated_qty;  
//                            $zsum_qty1[$j] +=$data->allocated_qty;
                    }else{
                       $zsum_qty[$j]=$zsum_qty[$j]+0;  
//                             $zsum_qty1[$j] +=0;
                   }
                   
               }else{
//                        $zsum_pl[$j]=0+$data->norm_qty;   
                $zsum_stl[$j]=0+$stock;
                $zsum_sl[$j]=0+$data->sale_qty; 
                if($data->allocated_qty!=null){
                    $zsum_qty[$j]=0+$data->allocated_qty;    
//                            $zsum_qty1[$j] +=$data->allocated_qty;
                }else{
                   $zsum_qty[$j]=0;  
//                             $zsum_qty1[$j]=0;
               }
           }
           if($data->allocated_qty!=null){
            $zsum_qty1[$j] =$data->allocated_qty;                            
        }else{
            $zsum_qty1[$j] =0;                            
        }
//                        $zsum_pl1[$j] =0; $zsum_stl1[$j] =0; $zsum_sl1[$j] =0;
//                    $zsum_pl1[$j] +=$data->norm_qty; 
        $zsum_stl1[$j] +=$stock; $zsum_sl1[$j] +=$data->sale_qty;
//                    $zsum_qty1[$j] +=$data->allocated_qty;
//                   if($data->allocated_qty!=null){
//                    $zsum_qty[$j]=$zsum_qty[$j]+$data->allocated_qty;                     
//                   }else{
//                        $zsum_qty[$j]=$zsum_qty[$j]+0;  
//                   }
        $zonename=clean($branch->zone_name);
        ?>
        <!--<td class="textalign"><?php // echo $data->norm_qty; ?></td>-->                    
        <td class="textalign"><?php echo $stock; ?></td>
        <td class="textalign"><?php echo $data->sale_qty; ?></td>
        <!--<td class="textalign"><?php // echo (($data->norm_qty)-$stock) ?></td>-->
        <td><input type="text" zone_name="<?php echo $zonename;?>" class="<?php echo $zonename.$full_name." " ;?><?php echo " ".$full_name; ?> form-control input-sm" branch="<?php echo $branch->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$branch->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                                   
        <?php 
        
        
        
    } ?>                
</tr>                
<?php $i++; $old_name=$branch->zone_name; } $oldname=clean($old_name); ?>
<tr class="" style="background-color: #fffae1;position: unset !important;">
    <td></td>
    <td></td>                                    
    <td><b>Total</b></td>   
    <?php for($j=0;$j<$counts; $j++){ 
       $data1=$model_data[$j][($i-1)];   
       $fullname = clean($data1->full_name);  
       ?>
       <!--<td class="textalign"><b><?php // echo $zsum_pl[$j]; ?></b></td>-->                                    
       <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
       <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
       <!--<td></td>-->
       <td class="textalign"><b><lable class="<?php echo $oldname.$fullname ?>" name="<?php echo $oldname.$fullname ?>" ><?php echo $zsum_qty[$j] ?></lable></b></td>
   <?php } ?>
</tr>
<tr class="" style="background-color: #fffae1;position: unset !important;">
    <?php $html ="";
    $html.='<td></td><td></td><td><b>Over All Total</b></td>';                                                        
    for($j=0;$j<$counts; $j++){ 
       $data1=$model_data[$j][($i-1)];   
       $fullname = clean($data1->full_name);                           
//                    $html.='<td class="textalign"><b>'.$zsum_pl1[$j].'</b></td>';                                    
       $html.='<td class="textalign"><b>'.$zsum_stl1[$j].'</b></td>';
       $html.='<td class="textalign"><b>'.$zsum_sl1[$j].'</b></td>';                    
       $html.='<td class="textalign"><lable class="total'.$fullname.'" name="total'.$fullname.'" >'.$zsum_qty1[$j].'</lable></td>';                     
   }
   echo $html;
//                     $_SESSION['top_row']=$html;
   ?>                
</tr>
</tbody>         
|
<?php echo $html; ?>
<?php         
}
public function save_transfer_balance() {
//        die('<pre>'.print_r($_POST,1).'</pre>');   
//        $allocation_type=$this->input->post('allocation_type');
//        $timestamp = time();
    $date = date('Y-m-d');
    $datetime = date('Y-m-d H:i:s');
    $iduser=$this->session->userdata('id_users');
    $idwarehouse =$this->session->userdata('idbranch');
    $qty=$this->input->post('qty');
    $variants=$this->input->post('variants');         
    $idvariant = $variants[0];
    $idmodel =$this->input->post('idmodel');                            
    $idgodown=$this->input->post('idgodown');   
    $idcategory =$this->input->post('idcategory');                            
    $idbrand =$this->input->post('idbrand');
    $idskutype =$this->input->post('idskutype');        
    $idproductcategory  =$this->input->post('idproductcategory');
    $product_name=$this->input->post('product_name');
    $count= count($variants);
    
    $this->db->trans_begin();     
    
        ////  Outward Data////        
    $data = array(
        'date' =>  $date,
        'idbranch' => $idwarehouse,
        'idwarehouse' =>  $idwarehouse,
        'sales_invoice' => 0,
        'purchase_invoice' => 0,
        'total_product' => 1,            
        'outward_by' => $iduser,
        'scan_time' => $datetime,
        'status' => 2,
        'total_product' => $count,
        'gst_type' => 0
    );
    $idoutward = $this->Outward_model->save_outward($data);
    
    $outward_product=array();
    $inward_stock_sku=array();
    $update_stock=array();
    foreach ($qty as $idbranch=>$model_array) {
        $en_qty = $model_array[$idvariant];
            ////  Outward Product Data For QTY SKU ////                
        $outward_product[] = array(
            'date' =>  $date,
            'idbranch' => $idbranch,      
            'idskutype' => $idskutype,
            'idgodown' => $idgodown,
            'idproductcategory' => $idproductcategory,
            'idcategory' => $idcategory,
            'idmodel' => $idmodel,
            'idvariant' => $idvariant,
            'idbrand' => $idbrand,
            'qty' => $en_qty,
            'idoutward' => $idoutward,
            'shipment_status' => 2,
        );  
            ////  Stock Reflection ////     
            //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($idvariants[$i], $idwarehouse, $idgodown[$i], $qty[$i]);
        $update_stock="UPDATE stock SET qty = qty - ".$en_qty." WHERE idvariant = ".$idvariant." AND idbranch = ".$idwarehouse."; ";
        $this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($update_stock);
            // check stock entry
//            die(print_r($update_stock));
        $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($idvariant,4,$idbranch,$idgodown);
        if(count($hostock) == 0){
            $inward_stock_sku[] = array(
                'date' =>  $date,
                'outward_time' => $datetime,
                'idbranch' => $idbranch,
                'transfer_from' => $idwarehouse,
                'outward_dc' => $idoutward,
                'outward_by' => $iduser,
                'product_name' => $product_name[0],
                'idskutype' => 4,
                'idgodown' => $idgodown,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idmodel' => $idmodel,
                'idvariant' => $idvariant,
                'idbrand' => $idbrand,
                'created_by' => $iduser,
                'idvendor' => 1,
                'qty' => $en_qty,
                'outward' => 1,
            ); 
        }else{
            $qty_plus = $hostock[0]->qty + $en_qty;
            $this->Inward_model->update_stock_byid($hostock[0]->id_stock,$qty_plus);
        }
    }
//        if(count($qty)){
//            foreach ($update_stock as $data){
//                
//            }
//        }
//        die('hi');
        // outward
    if(count($inward_stock_sku)>0){
        $this->Inward_model->save_stock_batch($inward_stock_sku);
    }
    $this->Outward_model->save_outward_product($outward_product);
    
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
public function transfer_rech_ins_balance_report() {
    $q['tab_active'] = 'Reports';
    $iduser = $_SESSION['id_users'];
    $sale_type = array(1);
    $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
    if($_SESSION['level'] == 1){
        $q['branch_data'] = $this->General_model->get_active_branch_data();
    }elseif($_SESSION['level'] == 3){
        $q['branch_data'] = $this->General_model->get_branches_by_user($iduser);
    }
    $this->load->view('outward/transfer_rech_ins_balance_report', $q);
}
public function ajax_transfer_balance_report() {
    $datefrom = $this->input->post('datefrom');
    $dateto = $this->input->post('dateto');
    $idbranch = $this->input->post('idbranch');
    $branches = $this->input->post('branches');
    $idvariant = $this->input->post('idvariant');
    $transfer_report = $this->Outward_model->get_ajax_transfer_balance_report($idvariant,$datefrom,$dateto,$idbranch,$branches); ?>
    <thead class="fixedelement">
        <th>Sr</th>
        <th>Date</th>
        <th>Branch</th>
        <th>Product</th>
        <th>Quantity</th>
    </thead>
    <tbody id="myTable">
        <?php $i=1; $total_amt=0; foreach($transfer_report as $balance){ ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo date('d-m-Y', strtotime($balance->date)) ?></td>
                <td><?php echo $balance->branch_name ?></td>
                <td><?php echo $balance->full_name ?></td>
                <td><?php $total_amt += $balance->qty; echo $balance->qty ?></td>
            </tr>
            <?php $i++; } ?>
        </tbody>
        <thead class="fixedelement_bottom">
            <th></th>
            <th></th>
            <th><?php echo $datefrom ?> To <?php echo $dateto ?></th>
            <th>Total</th>
            <th><?php echo $total_amt; ?></th>
        </thead>
    <?php }
    
}