<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bfl_Api extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata'); 
        $this->load->model('Bfl_Model');                     
    }

    public function update_Inventory() {        
        $this->Bfl_Model->Group_code_to_child_dealer_mapping_API();        
    }
    public function group_code_to_sku_details_api() {
        $q['tab_active'] = '';
        $q['sku_data'] = $this->Bfl_Model->Group_code_to_SKU_details_API();
        $sku_data= $this->General_model->get_vendor_sku_data_byid(2);
        $q['sku_column']=$sku_data->column_name;
        $q['variants'] = $this->Bfl_Model->get_model_variant_data($q['sku_column']);   
        $this->load->view('bajaj_bfl/group_code_to_sku_details_api', $q);
    }
    public function group_code_to_sku_mapping_api() {
        $q['tab_active'] = '';
        $branches = $this->General_model->get_active_branch_data();      
        $sku_data = $this->Bfl_Model->Group_code_to_SKU_details_API();
        $v_data= $this->General_model->get_vendor_sku_data_byid(2);        
        $sku_column=$v_data->column_name;
        $variants = $this->Bfl_Model->get_model_variant_data($sku_column);   
        $pages=$sku_data->page_detail->pages;
        for($i=1;$i<=$pages;$i++){
            if($i==1){
                
            }else{
                $sku_data = $this->Bfl_Model->Group_code_to_SKU_details_API($i);                
            }
            if($sku_data->message=='success'){
                
                $skudata =$sku_data->data;
                    foreach ($skudata as $odata){ 
                        $array = json_decode(json_encode($odata),true);
                        $ky = array_keys($array);
//                        die('<pre>'.print_r($array[$ky[7]],1).'</pre>');
                        $keys=multi_array_search($variants, array($sku_column => $odata->sku));                         
                        if(count($keys)>0){
                            $bfl_idbranch = explode(",",$array[$ky[7]]);
                            $update=array();
                            $this->Bfl_Model->delete_sku_branch_mapping_bulk($variants[$keys[0]]->id_variant);
                            foreach ($bfl_idbranch as $bflidbranch){
                                 $bkeys=multi_array_search($branches, array('bfl_store_id' => $bflidbranch)); 
                                 if(count($bkeys)>0){
                                $update[]=array(
                                    'bfl_sku'=>$odata->sku,
                                    'idvariant'=>$variants[$keys[0]]->id_variant,
                                    'bfl_idbranch'=>$bflidbranch,
                                    'idbranch'=>$branches[$bkeys[0]]->id_branch
                                );
                                 }
                            }                            
                            
                            $this->Bfl_Model->update_sku_branch_mapping_bulk($update);
                        }
                    } 
            }
        }
        
        
//      die('<pre>'.print_r($q['sku_data'],1).'</pre>');
         
    }
    public function upload_inventory_API() {
        
        $branches = $this->General_model->get_active_branch_data(); 
        $inventory=array();
        $inventory['dealer_grpid']="54429";
        foreach ($branches as $branch){
            if($branch->id_branch==82){
            $data = $this->Bfl_Model->get_bfl_stock_by_branch($branch->id_branch); 
//             die('<pre>'.print_r($data,1).'</pre>');
            if(count($data)>0){
                $inventory['seller_id']=$branch->bfl_store_id;
                $inventory['data']=array();
                $i=1;
                $total= count($data);
                foreach ($data as $d){
                    $inv_data['sku']=$d->bfl_sku;
                    $inv_data['price_value']=$d->mop;
                    $inv_data['stock_value']=(int)$d->qty;
                    $inv_data['status']=1;
                    array_push($inventory['data'], $inv_data);
                    if($i==19){
                        $sku_data = $this->Bfl_Model->Update_inventory_price_status($inventory);
                        $inventory['data']=array();
                        $i=1;
                    }elseif($i==$total && $total < 20){
//                        die('<pre>'.print_r(json_encode($inventory),1).'</pre>');
                        $sku_data = $this->Bfl_Model->Update_inventory_price_status($inventory);

                        $i=1;
                        $inventory['data']=array();
                    }else{
                        
                    }                    
                    $i++;
                }
                    die('<pre>'.print_r(json_encode($sku_data),1).'</pre>');
            }
            }  
        }
        
        
//      die('<pre>'.print_r($q['sku_data'],1).'</pre>');
         
    }
    
    public function save_sku_update(){
        $this->db->trans_begin();
        $sku_data= $this->General_model->get_vendor_sku_data_byid(2);
        $sku_column=$sku_data->column_name;
        $sku = $this->input->post('sku');
        $id_variant=  $this->input->post('model');
        $updateArray = array();
        $timestamp = time();        
        $updateArray[] = array(
                        'id_variant' => $id_variant,
                        $sku_column => $sku,                        
                        'm_variant_lmt' => $timestamp,
                        'm_variant_lmb' => $_SESSION['id_users']
                    ); 
        $this->General_model->update_model_variants_byidvariant_bulk($updateArray);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'no';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    public function ajax_variants() {     
        $sku_data= $this->General_model->get_vendor_sku_data_byid(2);        
        $q['sku_column']=$sku_data->column_name;
        $sku = $this->input->post('sku');        
        $model_data = $this->Bfl_Model->get_model_variant_data($q['sku_column']);
        echo '<input type="hidden"  name="skucode" id="skucode" value="'.$sku.'" />';
        echo '<select class="chosen-select form-control" name="model" id="model" required=""><option value="">Select Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
    }
    
    public function group_code_to_child_dealer_mapping_api() {
        $q['tab_active'] = '';
        $q['dealer_data'] = $this->Bfl_Model->Group_code_to_child_dealer_mapping_API()->data;           
        $q['branches'] = $this->General_model->get_active_branch_data();          
        $this->load->view('bajaj_bfl/group_code_to_child_dealer_mapping_api', $q);
    }
    public function update_bfl_store_id(){
        $this->db->trans_begin();       
        $bflcode = $this->input->post('bflcode');
        $idbranch=  $this->input->post('idbranch');
       
        $updateArray = array(                        
                        'bfl_store_id' => $bflcode
                    ); 
         
        $this->General_model->update_branch_data($idbranch,$updateArray);
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'no';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    public function ajax_branches() {     
        $branch_data = $this->General_model->get_active_branch_data();          
        $bfl_store_id = $this->input->post('bfl_store_id');                
        echo '<input type="hidden"  name="bfl_store_idcode" id="bfl_store_idcode" value="'.$bfl_store_id.'" />';
        echo '<select class="chosen-select form-control" name="idbranch" id="idbranch" required=""><option value="">Select Branch</option>';
        foreach ($branch_data as $branch) { 
            echo '<option  value="'.$branch->id_branch .'">'.$branch->branch_name.'</option>';
        }
    }

}