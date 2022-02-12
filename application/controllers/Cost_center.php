<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_center extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Costing_model');
        $this->load->model('General_model');
        $this->load->model('common_model');
    }
    public function branch_basic_details(){
        if($this->uri->segment(2)){
            if($this->uri->segment(2)=='NEW'){
                $q['branch_details']=1;
            }else{
                $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)),array('branch_id', 'desc'));
            }
        }
      
        if($this->session->userdata('idrole')==26){
            
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('created_by'=>$this->session->userdata('id_users')),array('branch_id', 'desc'));
        }else if($this->session->userdata('idrole')==25){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('branch_contact_person'=>$this->session->userdata('id_users')),array('branch_id', 'desc'));
        } else if($this->session->userdata('idrole')!=26 || $this->session->userdata('idrole')!=25 ){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*','',array('branch_id', 'desc'));
        }
       

        if($q['branch_data']){
            $q['rent_exist']= $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$this->uri->segment(2)));
        }
        $q['vendor_data'] = $this->common_model->getRecords('vendor','*',array('vendor_type'=>2));
        $q['abm_data'] = $this->common_model->getRecords('users','*',array('iduserrole'=>'25','active'=>1));
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['tab_active'] = '';
        $this->load->view('cost_center/branch_basic_details',$q);
    }
    public function branch_basic_details_store(){
        $postData=$this->input->post();
        $postData['created_by'] = $this->session->userdata('id_users');
        $original_branch_id=$postData['original_branch_id'];
        unset($postData['original_branch_id']);
        if(isset($postData['branch_id']) && $postData['branch_id'] != '')
        {
            $id = $postData['branch_id'];

            $ins= $this->common_model->updateRow('cost_center_branch', $postData, array('branch_id'=>$postData['branch_id']));
        }
        else
        {
            $ins=$this->common_model->insertRow($postData, 'cost_center_branch');
        }
        if($ins){
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
        echo json_encode($response);
    }

    public function branchShopactGst(){
     $q['tab_active'] = '';
     if($this->uri->segment(2)){
        $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_rent_details']  = $this->common_model->getRecords('branch_rent_details','*',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_ele_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));
    }
    $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','','');
    $q['branch_category_data'] = $this->General_model->get_active_branch_category();
    $q['partner_type_data'] = $this->General_model->get_partner_type_data();
    $this->load->view('cost_center/branch-shopact-gst',$q);
}

public function branchShopactGstStore(){
 $postData=$this->input->post();
 $uploadpath='uploads/branch_info/'.$postData['branch_id'];
 if(!file_exists($uploadpath)){
    mkdir($uploadpath,0777,true);
}


if($_FILES && $_FILES['shopact_doc']['name'] != ''){    
    $filename=$_FILES['shopact_doc']['name'];
    $newName = 'rentdoc_'.$postData['branch_id'].'.'.pathinfo($filename, PATHINFO_EXTENSION); 
    $config = array(
        'upload_path' => $uploadpath,
        'allowed_types' => '*',
        'file_name' => $newName,
    );
    $this->load->library('upload', $config);
    $this->upload->initialize($config);
    if($this->upload->do_upload('shopact_doc')){
        $uploadData = $this->upload->data();
        $img = $uploadData['file_name'];
        $path=$uploadpath."/".$img;  
        $file_path['shopact_doc']=$path;
        $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));

        if(!empty($exist_rendoc['shopact_doc'])){
            unlink($exist_rendoc['shopact_doc']);
        }
        $upd_fname = $this->common_model->updateRow('cost_center_branch',$file_path,array('branch_id'=>$postData['branch_id']));
        $response['status']=true;
        $response['message']='Record Save Successfully';
    }else{
        $response['status']=false;
        $response['message']='Please try again';
    }
}
if($_FILES && $_FILES['gstcert_doc']['name'] != ''){    
    $filename=$_FILES['gstcert_doc']['name'];
    $newName = 'rentdoc_'.$postData['branch_id'].'.'.pathinfo($filename, PATHINFO_EXTENSION); 
    $config = array(
        'upload_path' => $uploadpath,
        'allowed_types' => '*',
        'file_name' => $newName,
    );
    $this->load->library('upload', $config);
    $this->upload->initialize($config);
    if($this->upload->do_upload('gstcert_doc')){
        $uploadData = $this->upload->data();
        $img = $uploadData['file_name'];
        $path=$uploadpath."/".$img;  
        $file_path['gstcert_doc']=$path;
        $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));

        if(!empty($exist_rendoc['gstcert_doc'])){
            unlink($exist_rendoc['gstcert_doc']);
        }
        $upd_fname = $this->common_model->updateRow('cost_center_branch',$file_path,array('branch_id'=>$postData['branch_id']));
        $response['status']=true;
        $response['message']='Record Save Successfully';
    }else{
        $response['status']=false;
        $response['message']='Please try again';
    }
}

echo json_encode($response);
}
public function branchFinalDocRentCp(){
 $q['tab_active'] = '';
 if($this->uri->segment(2)){
    $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
    $q['branch_rent_details']  = $this->common_model->getRecords('branch_rent_details','*',array('branch_id'=>$this->uri->segment(2)));
    $q['branch_cp_details']  = $this->common_model->getSingleRow('branch_channel_partner_details',array('branch_id'=>$this->uri->segment(2)));
}
if($this->session->userdata('idrole')==26){

    $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('created_by'=>$this->session->userdata('id_users')),array('branch_id', 'desc'));
}else if($this->session->userdata('idrole')==25){
    $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('branch_contact_person'=>$this->session->userdata('id_users')),array('branch_id', 'desc'));
} else if($this->session->userdata('idrole')!=26 || $this->session->userdata('idrole')!=25 ){
    $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*','',array('branch_id', 'desc'));
}
$q['branch_category_data'] = $this->General_model->get_active_branch_category();
$q['partner_type_data'] = $this->General_model->get_partner_type_data();
$this->load->view('cost_center/branch-final-doc-rent-cp',$q);
}

public function branchFinalDocRentCpStore(){
 $postData=$this->input->post();
 $uploadpath='uploads/branch_info/'.$postData['branch_id'];
 if(!file_exists($uploadpath)){
    mkdir($uploadpath,0777,true);
}


if(isset($_FILES['rent_doc'])){
    if($_FILES && !empty($_FILES['rent_doc']['name'])){ 
        $filename=$_FILES['rent_doc']['name'];
        $newName = 'rentdoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
        $config = array(
            'upload_path' => $uploadpath,
            'allowed_types' => '*',
            'file_name' => $newName,
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if($this->upload->do_upload('rent_doc')){
            $uploadData = $this->upload->data();
            $img = $uploadData['file_name'];
            $path=$uploadpath."/".$img;  
            $file_path['rent_doc']=$path;
            $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));

            if(!empty($exist_rendoc['rent_doc'])){
                unlink($exist_rendoc['rent_doc']);
            }
            $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('branch_id'=>$postData['branch_id']));
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
    }
}
if(isset($_FILES['agreement_doc'])){   
 if($_FILES['agreement_doc']['name'] != ''){    
    $filename=$_FILES['agreement_doc']['name'];
    $newName = 'cpagreement_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
    $config = array(
        'upload_path' => $uploadpath,
        'allowed_types' => '*',
        'file_name' => $newName,
    );
    $this->load->library('upload', $config);
    $this->upload->initialize($config);
    if($this->upload->do_upload('agreement_doc')){
        $uploadData = $this->upload->data();
        $img = $uploadData['file_name'];
        $path=$uploadpath."/".$img;  
        $file_path['agreement_doc']=$path;
        $exist_rendoc = $this->common_model->getSingleRow('branch_channel_partner_details',array('branch_id'=>$postData['branch_id']));

        if(!empty($exist_rendoc['agreement_doc'])){
            unlink($exist_rendoc['agreement_doc']);
        }
        $upd_fname = $this->common_model->updateRow('branch_channel_partner_details',$file_path,array('branch_id'=>$postData['branch_id']));
        $response['status']=true;
        $response['message']='Record Save Successfully';
    }else{
        $response['status']=false;
        $response['message']='Please try again';
    }
}
}

echo json_encode($response);
}
    
    public function branch_rent_details(){
        $q['tab_active'] = '';
        if($this->uri->segment(2)){
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_rent_details']  = $this->common_model->getRecords('branch_rent_details','*',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_ele_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));
        }
      if($this->uri->segment(3)){

        $q['rentow_details']  = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$this->uri->segment(2),'id'=>$this->uri->segment(3)));
        if(empty($q['rentow_details'])){
            $q['rentow_details']=1;
        }
    }
      if($this->session->userdata('idrole')==26){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('created_by'=>$this->session->userdata('id_users')));
        }else if($this->session->userdata('idrole')==25){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('branch_contact_person'=>$this->session->userdata('id_users')));
        } else if($this->session->userdata('idrole')!=26 || $this->session->userdata('idrole')!=25 ){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*');
        }
        
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_rent_details',$q);
    }

    public function branch_rent_details_legal(){
        $q['tab_active'] = '';

        $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_rent_details']  = $this->common_model->getRecords('branch_rent_details','*','',array('id','desc'));
        $q['branch_ele_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));

        $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','','');
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_rent_details_legal',$q);
    }

    
    function branch_rent_details_store(){
    $postData=$this->input->post();
    $postData['created_by'] = $this->session->userdata('id_users');
    unset($postData['branch_name']);
    unset($postData['branch_category']);
    unset($postData['branch_partener_type']);
    $inc_ratio='';
    if(!empty($postData['rent_incr_ratio'])){
        for($t=0;$t<sizeof($postData['rent_incr_ratio']);$t++){
            if($t==0){
                $inc_ratio=$postData['rent_incr_ratio'][$t];
            }else{
                $inc_ratio=$inc_ratio.','.$postData['rent_incr_ratio'][$t];
            }

        }
    }
    $postData['rent_incr_ratio']=$inc_ratio;
    if(!empty($postData['ele_provider'])){
        $ele_postData['ele_provider']=$postData['ele_provider'];
        $ele_postData['ele_custno']=$postData['ele_custno'];
        $ele_postData['ele_billingunit']=$postData['ele_billingunit'];
        $ele_postData['ele_meterno']=$postData['ele_meterno'];
        $ele_postData['ele_last_billing_unit']=$postData['ele_last_billing_unit'];
        $ele_postData['ele_las_billing_month']=$postData['ele_las_billing_month'];

        unset($postData['ele_provider']);
        unset($postData['ele_custno']);
        unset($postData['ele_billingunit']);
        unset($postData['ele_meterno']);
        unset($postData['ele_last_billing_unit']);
        unset($postData['ele_las_billing_month']);


        $exist = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$postData['branch_id']));
        if(empty($exist)){
            $ele_postData['branch_id']=$postData['branch_id'];
            $ele = $this->common_model->insertRow($ele_postData,'branch_details');
        }else{
            $ele = $this->common_model->updateRow('branch_details',$ele_postData,array('branch_id'=>$postData['branch_id']));  
        }
    }
    $rent_details  = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

    if(!empty($rent_details))
    {
        $id = $postData['branch_id'];
        $ins= $this->common_model->updateRow('branch_rent_details', $postData, array('id'=>$postData['id']));
        if ($this->session->userdata('role_type')==0) {
            $rent_branch  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$postData['branch_id']));
            $exist_branch  = $this->common_model->getSingleRow('branch',array('id_branch'=>$rent_branch['original_branch_id']));
            $ori_branchdata['branch_code']=$rent_branch['branch_name'];
            $ori_branchdata['branch_name']=$rent_branch['branch_name'];
            $ori_branchdata['branch_address']=$rent_branch['branch_address'];
            $ori_branchdata['branch_pincode']=$rent_branch['branch_pincode'];
            $ori_branchdata['branch_state_name']=$rent_branch['branch_state'];
            $ori_branchdata['branch_district']=$rent_branch['branch_district'];
            $ori_branchdata['branch_city']=$rent_branch['branch_city'];
            $ori_branchdata['branch_contact_person']=$rent_branch['branch_contact_person'];
            $ori_branchdata['branch_contact']=$rent_branch['branch_contact'];
            $ori_branchdata['idbranchcategory']=$rent_branch['branch_category'];
            $ori_branchdata['idpartner_type']=$rent_branch['branch_partener_type'];
            if($ori_branchdata['idpartner_type']=='1'){
                if(!empty($exist_branch)){
                    $ins_branch= $this->common_model->updateRow('branch', $ori_branchdata, array('id_branch'=> $original_branch_id));
                }else{
                    $ins_branch=$this->common_model->insertRow($ori_branchdata, 'branch');
                    $ori['original_branch_id']=$ins_branch;
                    $ori['branch_status']='1';
                    $_branch= $this->common_model->updateRow('cost_center_branch', $ori, array('branch_id'=>$postData['branch_id']));
                }
            }
        }

    }
    else
    {
        $ins=$this->common_model->insertRow($postData, 'branch_rent_details');
        $rent_details  = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));
        $postData['id']= $ins;
        
    }
    if($ins){
        $uploadpath='uploads/branch_info/'.$postData['branch_id'];
        if(!file_exists($uploadpath)){
            mkdir($uploadpath,0777,true);
        }


        if(isset($_FILES['pan_doc'])){

            if($_FILES && $_FILES['pan_doc']['name'] != ''){ 
                
                $filename=$_FILES['pan_doc']['name'];
                $newName = 'rentpandoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('pan_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['pan_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['pan_doc'])){
                        unlink($exist_rendoc['pan_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['adhar_doc'])){
            if($_FILES && $_FILES['adhar_doc']['name'] != ''){    
                $filename=$_FILES['adhar_doc']['name'];
                $newName = 'renadhartdoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('adhar_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['adhar_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['adhar_doc'])){
                        unlink($exist_rendoc['adhar_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['property_doc'])){
            if($_FILES && $_FILES['property_doc']['name'] != ''){    
                $filename=$_FILES['property_doc']['name'];
                $newName = 'rentpropdoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('property_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['property_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['property_doc'])){
                        unlink($exist_rendoc['property_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            } 
        }
        if(isset($_FILES['electricity_doc'])){
            if($_FILES && $_FILES['electricity_doc']['name'] != ''){    
                $filename=$_FILES['electricity_doc']['name'];
                $newName = 'renteledoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('electricity_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['electricity_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['electricity_doc'])){
                        unlink($exist_rendoc['electricity_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['rent_doc'])){
            if($_FILES && !empty($_FILES['rent_doc']['name'])){ 
                $filename=$_FILES['rent_doc']['name'];
                $newName = 'rentdoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('rent_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['rent_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));

                    if(!empty($exist_rendoc['rent_doc'])){
                        unlink($exist_rendoc['rent_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('branch_id'=>$postData['branch_id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['aminiti_doc'])){
            if($_FILES && !empty($_FILES['aminiti_doc']['name'])){    
                $filename=$_FILES['aminiti_doc']['name'];
                $newName = 'rentamidoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('aminiti_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['aminiti_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('branch_id'=>$postData['branch_id']));

                    if(!empty($exist_rendoc['aminiti_doc'])){
                        unlink($exist_rendoc['aminiti_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('branch_id'=>$postData['branch_id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['cheque_doc'])){
            if($_FILES && !empty($_FILES['cheque_doc']['name'])){    
                $filename=$_FILES['cheque_doc']['name'];
                $newName = 'rentamidoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('cheque_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['cheque_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['cheque_doc'])){
                        unlink($exist_rendoc['cheque_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }   
        if(isset($_FILES['passbook_doc'])){
            if($_FILES && !empty($_FILES['passbook_doc']['name'])){    
                $filename=$_FILES['passbook_doc']['name'];
                $newName = 'rentamidoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('passbook_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['passbook_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['passbook_doc'])){
                        unlink($exist_rendoc['passbook_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        if(isset($_FILES['other_doc'])){
            if($_FILES && !empty($_FILES['other_doc']['name'])){    
                $filename=$_FILES['other_doc']['name'];
                $newName = 'rentamidoc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('other_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['other_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_rent_details',array('id'=>$postData['id']));

                    if(!empty($exist_rendoc['other_doc'])){
                        unlink($exist_rendoc['other_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_rent_details',$file_path,array('id'=>$postData['id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
        }
        $response['status']=true;
        $response['message']='Record Save Successfully';
    }else{
        $response['status']=false;
        $response['message']='Please try again';
    }
    echo json_encode($response);
}

    public function branch_cp_details(){
        $q['tab_active'] = '';
        if($this->uri->segment(2)){
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_cp_details']  = $this->common_model->getRecords('branch_channel_partner_details','*',array('branch_id'=>$this->uri->segment(2)));
        }
        if($this->uri->segment(3)){

        $q['cp_details']  = $this->common_model->getRecords('branch_channel_partner_details','*',array('branch_id'=>$this->uri->segment(2),'id'=>$this->uri->segment(3)));
        if(empty( $q['cp_details'])){
           $q['cp_details']=1;
       }

   }
        if($this->session->userdata('idrole')==26){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('created_by'=>$this->session->userdata('id_users'),'branch_partener_type'=>2));
        }else if($this->session->userdata('idrole')==25){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('branch_contact_person'=>$this->session->userdata('id_users'),'branch_partener_type'=>2));
        } else if($this->session->userdata('idrole')!=26 || $this->session->userdata('idrole')!=25 ){
            $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','*',array('branch_partener_type'=>2));
        }
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_cp_details',$q);
    }
    function branch_cp_details_store(){
        $postData=$this->input->post();
        $postData['created_by'] = $this->session->userdata('id_users');
        unset($postData['branch_name']);
        unset($postData['branch_category']);
        unset($postData['branch_partener_type']);
        $rent_details  = $this->common_model->getSingleRow('branch_channel_partner_details',array('id'=>$postData['id']));

        if(!empty($rent_details))
        {
            $id = $postData['branch_id'];
            $ins= $this->common_model->updateRow('branch_channel_partner_details', $postData, array('id'=>$postData['id']));
        }
        else
        {
            $ins=$this->common_model->insertRow($postData, 'branch_channel_partner_details');
            $postData['id']=$ins;
        }
        if($ins){
            $uploadpath='uploads/branch_info/'.$postData['branch_id'];
            if(!file_exists($uploadpath)){
                mkdir($uploadpath,0777,true);
            }
if($_FILES && $_FILES['pan_doc']['name'] != ''){    
            $filename=$_FILES['pan_doc']['name'];
            $newName = 'cp_pan_doc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
            $config = array(
                'upload_path' => $uploadpath,
                'allowed_types' => '*',
                'file_name' => $newName,
            );
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if($this->upload->do_upload('pan_doc')){
                $uploadData = $this->upload->data();
                $img = $uploadData['file_name'];
                $path=$uploadpath."/".$img;  
                $file_path['pan_doc']=$path;
                $exist_rendoc = $this->common_model->getSingleRow('branch_channel_partner_details',array('id'=>$postData['id']));

                if(!empty($exist_rendoc['pan_doc'])){
                    unlink($exist_rendoc['pan_doc']);
                }
                $upd_fname = $this->common_model->updateRow('branch_channel_partner_details',$file_path,array('id'=>$postData['id']));
                $response['status']=true;
                $response['message']='Record Save Successfully';
            }else{
                $response['status']=false;
                $response['message']='Please try again';
            }
        }
        if($_FILES && $_FILES['adhar_doc']['name'] != ''){    
            $filename=$_FILES['adhar_doc']['name'];
            $newName = 'cp_adhar_doc_'.rand().'.'.pathinfo($filename, PATHINFO_EXTENSION); 
            $config = array(
                'upload_path' => $uploadpath,
                'allowed_types' => '*',
                'file_name' => $newName,
            );
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if($this->upload->do_upload('adhar_doc')){
                $uploadData = $this->upload->data();
                $img = $uploadData['file_name'];
                $path=$uploadpath."/".$img;  
                $file_path['adhar_doc']=$path;
                $exist_rendoc = $this->common_model->getSingleRow('branch_channel_partner_details',array('id'=>$postData['id']));

                if(!empty($exist_rendoc['adhar_doc'])){
                    unlink($exist_rendoc['adhar_doc']);
                }
                $upd_fname = $this->common_model->updateRow('branch_channel_partner_details',$file_path,array('id'=>$postData['id']));
                $response['status']=true;
                $response['message']='Record Save Successfully';
            }else{
                $response['status']=false;
                $response['message']='Please try again';
            }
        }
           
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
        echo json_encode($response);
    }


    public function branch_insurence_details(){
        $q['tab_active'] = '';
        if($this->uri->segment(2)){
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_insurence_details']  = $this->common_model->getSingleRow('branch_insurence_details',array('branch_id'=>$this->uri->segment(2)));
        }
        $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','','');
        $q['vendor_data'] = $this->common_model->getRecords('vendor','*',array('vendor_type'=>3));
        $q['bank_data'] = $this->common_model->getRecords('bank','*',array('active'=>1));
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_insurence_details',$q);
    }
public function branch_insurence_details_legal(){
        $q['tab_active'] = '';
       
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_insurence_details']  = $this->common_model->getRecords('branch_insurence_details');
     
        $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','','');
        $q['vendor_data'] = $this->common_model->getRecords('vendor','*',array('vendor_type'=>3));
        $q['bank_data'] = $this->common_model->getRecords('bank','*',array('active'=>1));
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_insurence_details_legal',$q);
    }


    function branch_insurence_details_store(){
        $postData=$this->input->post();
        $postData['created_by'] = $this->session->userdata('id_users');
        unset($postData['branch_name']);
        unset($postData['branch_category']);
        unset($postData['branch_partener_type']);
        $rent_details  = $this->common_model->getSingleRow('branch_insurence_details',array('branch_id'=>$postData['branch_id']));

        if(!empty($rent_details))
        {
            $id = $postData['branch_id'];
            $ins= $this->common_model->updateRow('branch_insurence_details', $postData, array(''=>$postData['branch_id']));
        }
        else
        {
            $ins=$this->common_model->insertRow($postData, 'branch_insurence_details');
        }
        if($ins){
            $uploadpath='uploads/branch_info/'.$postData['branch_id'];
            if(!file_exists($uploadpath)){
                mkdir($uploadpath,0777,true);
            }

            if($_FILES['insurence_doc']['name'] != ''){    
                $filename=$_FILES['insurence_doc']['name'];
                $newName = 'insurencedoc_'.$postData['branch_id'].'.'.pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                    'upload_path' => $uploadpath,
                    'allowed_types' => '*',
                    'file_name' => $newName,
                );
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('insurence_doc')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$uploadpath."/".$img;  
                    $file_path['insurence_doc']=$path;
                    $exist_rendoc = $this->common_model->getSingleRow('branch_insurence_details',array('branch_id'=>$postData['branch_id']));

                    if(!empty($exist_rendoc['insurence_doc'])){
                        unlink($exist_rendoc['insurence_doc']);
                    }
                    $upd_fname = $this->common_model->updateRow('branch_insurence_details',$file_path,array('branch_id'=>$postData['branch_id']));
                    $response['status']=true;
                    $response['message']='Record Save Successfully';
                }else{
                    $response['status']=false;
                    $response['message']='Please try again';
                }
            }
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
        echo json_encode($response);
    }

    public function branch_mbb_details(){
        $q['tab_active'] = '';
        if($this->uri->segment(2)){
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_mbb_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));

        }
        $q['branch_data'] = $this->common_model->getRecords('cost_center_branch','','');

        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_mbb_details',$q);
    }
    public function branch_electricity_details(){
        $q['tab_active'] = '';
        if($this->uri->segment(2)){
            $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
            $q['branch_ele_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));

        }

        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('cost_center/branch_electricity_details',$q);
    }


    function branch_mbb_details_store(){

        $postData=$this->input->post();
        $postData['created_by'] = $this->session->userdata('id_users');
        unset($postData['branch_name']);
        unset($postData['branch_category']);
        unset($postData['branch_partener_type']);
        $exist = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$postData['branch_id']));
        if(empty($exist)){

            $ins = $this->common_model->insertRow($postData,'branch_details');
        }else{
            $ins = $this->common_model->updateRow('branch_details',$postData,array('branch_id'=>$postData['branch_id']));        
        }
        if($ins){
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
        echo json_encode($response);
    }

    function branch_information(){
        $q['tab_active'] = '';
        $q['branch_details']  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_mbb_ele_details']  = $this->common_model->getSingleRow('branch_details',array('branch_id'=>$this->uri->segment(2)));
        $q['rent_details']  = $this->common_model->getRecords('branch_rent_details','*',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_insurence_details']  = $this->common_model->getSingleRow('branch_insurence_details',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_cp_details']  = $this->common_model->getRecords('branch_channel_partner_details','*',array('branch_id'=>$this->uri->segment(2)));
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();

        $this->load->view('cost_center/branch_information',$q);
    }

    function branch_rent_cp_deposit(){
        $q['tab_active'] = '';
        $this->load->view('cost_center/branch_rent_cp_deposit', $q);
    }
     public function get_branch_rent_cp_deposit_data() {
        $idcostheader = $this->input->post('idcostheader');
        if($idcostheader=='1'){
            $branch_data=$this->Costing_model->getallbranches_rent();
            $thead='<th style="text-align: center">Branch</th><th style="text-align: center">Deposit Amount</th><th style="text-align: center">Deposited Amount</th><th style="text-align: center">Deposited Bank</th><th style="text-align: center">Account No</th><th style="text-align: center">IFSC Code</th><th style="text-align: center">Deposited Date</th><th style="text-align: center">Deposited Trans Id</th><th style="text-align: center">Deposited Remark</th><th style="text-align: center">Passbook Document</th><th style="text-align: center">Cheque Document</th>';
 $thead=$thead.'<th style="text-align: center">Action</th>';
        }else{
            $branch_data= $this->Costing_model->getallbranches_cp();
            $thead='<th style="text-align: center">Branch</th><th style="text-align: center">Deposit Amount</th><th style="text-align: center">Deposit Received Amount</th><th style="text-align: center">Deposited Bank</th><th style="text-align: center">Account No</th><th style="text-align: center">IFSC Code</th><th style="text-align: center">Deposit Received Date</th><th style="text-align: center">Deposit Received Trans Id</th><th style="text-align: center">Deposit Received Remark</th>';

            $thead=$thead.'<th style="text-align: center">Action</th>';
            if($this->session->userdata('role_name')=='Admin'){

                $thead=$thead.'<th style="text-align: center">View Document</th><th style="text-align: center">Approve Branch</th>';
            }
            
        }
        if($branch_data){ ?>
            <form id="form_cp_rent_data" method="post">
                <div style="">
                    <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                        <thead class="fixheader" style="background-color: #c6e6f5">
                            <?php echo $thead;?>
                        </thead>
                        <tbody class="data_1">
                            <?php foreach ($branch_data as $branch){ ?>
                                <tr>
                                    <td><?php echo $branch->branch_name; ?><input type="hidden" class="form-control input-sm" name="branch_id[]" id="branch_id" value="<?php echo $branch->branch_id; ?>"></td>

                                    <td><?php echo $branch->deposit_amt; ?></td>
                                   
                                    <td><input type="text" class="form-control input-sm deposit_paid_amt" name="deposit_paid_amt[]" id="deposit_paid_amt" value="<?php echo $branch->deposit_paid_amt; ?>" <?php if($branch->deposit_paid_amt==$branch->deposit_amt || $this->session->userdata('role_name')=='Admin'){ echo 'readonly';}?>></td>
                                      <td><?php echo $branch->owner_bank_name; ?></td>
                                      <td><?php echo $branch->owner_bank_accno; ?></td>
                                      <td><?php echo $branch->owner_bank_ifsc; ?></td>
                                    <td><input type="date" class="form-control input-sm deposit_paid_date" name="deposit_paid_date[]" id="deposit_paid_date" value="<?php echo $branch->deposit_paid_date; ?>" <?php if($branch->deposit_paid_amt==$branch->deposit_amt || $this->session->userdata('role_name')=='Admin'){ echo 'readonly';}?>></td>
                                    <td><input type="text" class="form-control input-sm trans_id" name="trans_id[]" id="trans_id" value="<?php echo $branch->trans_id; ?>" <?php if($branch->deposit_paid_amt==$branch->deposit_amt || $this->session->userdata('role_name')=='Admin' ){ echo 'readonly';}?>></td>
                                    <td><input type="text" class="form-control input-sm remark" name="remark[]" id="remark" value="<?php echo $branch->remark; ?>" <?php if($branch->remark!='' || $this->session->userdata('role_name')=='Admin'){ echo 'readonly';}?>></td>
                                       <?php 
                                if($idcostheader=='1'){ ?>
                                   <td> <a href="<?php echo base_url().$branch->passbook_doc; ?>" target="_blank"><button type="button">View</button></a></td>
                                    <td> <a href="<?php echo base_url().$branch->cheque_doc; ?>" target="_blank"><button type="button">View</button></a></td>
                                <?php  } if($this->session->userdata('idrole')=='22' && $branch->receive_status==0){
                                        if($idcostheader=='1'){ ?>
                                            <td><input type="button" class="btn btn-info btn-sm remark pay_deposit" name="pay_deposit" id="pay_deposit" data-id="<?php echo $branch->branch_id; ?>" value="Pay"></td>
                                       <?php }else if($idcostheader=='2'){
                                        ?>
                                        <td><input type="button" class="btn btn-info btn-sm remark receive_deposit" name="receive_deposit" id="receive_deposit" data-id="<?php echo $branch->branch_id; ?>" value="Receive"></td>
                                    <?php } }else{ if($idcostheader=='1'){ echo "<td>Paid</td>";}else{ echo "<td>Received</td>";} ?>
                                        
                                        <?php  } if($this->session->userdata('role_name')=='Admin' && $idcostheader=='2'){?>
                                        <td><a href="<?php echo base_url().$branch->agreement_doc;?>" target="_blank">View Document</a></td>
                                        <?php if(empty($branch->original_branch_id) && $branch->receive_status==1){ ?>
                                            <td><input type="button" class="btn btn-info btn-sm remark approve_branch" name="approve_branch" id="approve_branch" data-id="<?php echo $branch->branch_id; ?>" value="Approve Branch"></td>
                                        <?php }else{ ?>
                                            <td><input type="button" class="btn btn-info btn-sm " value="Approved"></td>
                                        <?php } } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="idcostheader" value="<?php echo $idcostheader ?>">
                    </div>
                    <div class="clearfix"></div><br>
<!--                    <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
                        <button type='button' class="btn btn-info btn-sm pull-right " style="margin: 0" id="save_data">Save</button>
                    </div> -->
                </form>
            <?php }
        }

        public function save_branch_rent_cp_details() {

            $idcostheader = $this->input->post('idcostheader');
            $branch_id = $this->input->post('branch_id');
            $deposit_paid_amt = $this->input->post('deposit_paid_amt');
            $deposit_paid_date = $this->input->post('deposit_paid_date');
            $trans_id = $this->input->post('trans_id');
            $remark = $this->input->post('remark');
            for($i=0; $i< count($branch_id); $i++){

                $postData['trans_id']=$trans_id[$i];
                $postData['remark']=$remark[$i];
                if($idcostheader==1){
                    $postData['deposit_paid_amt']=$deposit_paid_amt[$i];
                    $postData['deposit_paid_date']=$deposit_paid_date[$i];

                    $this->common_model->updateRow('branch_rent_details',$postData,array('branch_id'=>$branch_id[$i]));
                }else if($idcostheader==2){
                    $postData['deposit_rec_amt']=$deposit_paid_amt[$i];
                    $postData['deposit_rec_date']=$deposit_paid_date[$i];
                    $this->common_model->updateRow('branch_channel_partner_details',$postData,array('branch_id'=>$branch_id[$i]));
                }
            }

            $response['status']=true;
            $response['message']='Branch Data Updated Successfully';
            echo json_encode($response);
        }
           function receive_cofo_branch_deposit(){
            $postData=$this->input->post();
         
            $ori['receive_status']='1';
                        $ori['remark']=$postData['remark'];

            $_branch= $this->common_model->updateRow('branch_channel_partner_details', $ori, array('branch_id'=>$postData['approve_branch_id']));
           
            $response['status']=true;
            $response['message']='Deposit Received Successfully';
            echo json_encode($response);
        }
          function pay_branch_deposit(){
            $postData=$this->input->post();
            $branch=$postData['approve_branch_id'];
            unset($postData['approve_branch_id']);
            $postData['deposit_status']='1';
            $_branch= $this->common_model->updateRow('branch_rent_details', $postData, array('branch_id'=>$branch));

            $response['status']=true;
            $response['message']='Deposit Received Successfully';
            echo json_encode($response);
        }
        function approve_cofo_branch(){
            $postData=$this->input->post();
            $rent_branch  = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$postData['approve_branch_id']));
            $exist_branch  = $this->common_model->getSingleRow('branch',array('id_branch'=>$rent_branch['original_branch_id']));
            $ori_branchdata['branch_code']=$rent_branch['branch_name'];
            $ori_branchdata['branch_name']=$rent_branch['branch_name'];
            $ori_branchdata['branch_address']=$rent_branch['branch_address'];
            $ori_branchdata['branch_pincode']=$rent_branch['branch_pincode'];
            $ori_branchdata['branch_state_name']=$rent_branch['branch_state'];
            $ori_branchdata['branch_district']=$rent_branch['branch_district'];
            $ori_branchdata['branch_city']=$rent_branch['branch_city'];
            $ori_branchdata['branch_contact_person']=$rent_branch['branch_contact_person'];
            $ori_branchdata['branch_contact']='123456';
            $ori_branchdata['idbranchcategory']=$rent_branch['branch_category'];
            $ori_branchdata['idpartner_type']=$rent_branch['branch_partener_type'];

            if(!empty($exist_branch)){
                $ins_branch= $this->common_model->updateRow('branch', $ori_branchdata, array('id_branch'=> $rent_branch['original_branch_id']));
            }else{
                $ins_branch=$this->common_model->insertRow($ori_branchdata, 'branch');
                $ori['original_branch_id']=$ins_branch;
                 $ori['branch_status']='1';
                $_branch= $this->common_model->updateRow('cost_center_branch', $ori, array('branch_id'=>$postData['approve_branch_id']));
            }

            $response['status']=true;
            $response['message']='Branch Approved Successfully';
            echo json_encode($response);
        }

        function download_expense_format(){
            $q['tab_active'] = '';
            $q['costing_headers'] = $this->Costing_model->get_user_has_costing_header_by_user($_SESSION['id_users']);
            $this->load->view('cost_center/download_expense_format',$q);
        }
        function cost_header_month_data(){
            $q['tab_active'] = '';

            $this->load->view('cost_center/cost_header_month_data',$q);
        }
        function ajax_get_costing_data_for_month(){
            if($this->input->post('idcostheader')==3){
                $branch_cat_data = $this->common_model->getRecords('partner_type','partner_type branch_category_name');
            }else if($this->input->post('idcostheader')==2){
                $branch_cat_data = $this->common_model->getRecords('branch_category','*',array('active'=>1));
            }else{
                $branch_cat_data[0]['branch_category_name']='Percentage';
            }
// print_r($branch_cat_data);die();
            $cost_header_data = $this->common_model->getRecords('branch_cost_headers','*',array('idtype'=>$this->input->post('idcostheader')));

            if($branch_cat_data){ ?>
                <form id="cost-header-month-data">
                    <table class="table table-bordered table-condensed text-center" id="branch_costing_data">
                        <thead>
                            <th>Cost Header</th>
                            <?php foreach($branch_cat_data as $cat_data){ ?>
                                <th><?php echo $cat_data['branch_category_name']; ?></th>
                            <?php  } ?>
                        </thead>
                        <tbody>
                            <?php $i=0;  foreach($cost_header_data as $cost_data){
                                 $user_cost_data = $this->common_model->getRecords('user_has_costing_headers','*',array('idcosting_header'=>$cost_data['id_cost_header'],'iduser'=>$this->session->userdata('id_users')));
                               if(!empty($user_cost_data)){
                                ?>
                                <tr>
                                    <td style="text-align:left;"><?php echo $cost_data['cost_header_name']; ?><input type="text" class="form-control input-sm" name="costheader[]" value="<?php echo $cost_data['id_cost_header']; ?>" hidden></td>

                                    <?php foreach($branch_cat_data as $cat_data){ ?>
                                        <td><input type="text" class="form-control input-sm" name="<?php echo str_replace(' ', '', $cat_data['branch_category_name']); ?>[]" ></td>
                                        <?php  $i++; } ?>
                                    </tr>
                                <?php  
                               }
                                    } ?>
                            </tbody>
                        </table>
                        <div class="clearfix"></div><br>
                        <div class="col-md-2 col-sm-2 col-xs-2 pull-right ">
                            <button type='button' class="btn btn-info btn-sm pull-right " style="margin: 0" id="save_data">Save</button>
                        </div> 
                    </form>
                <?php }

            }
            function save_cost_header_month_data(){
                $cost_header_data = $this->common_model->getRecords('branch_cost_headers','*',array('idtype'=>$this->input->post('idcostheader')));
                if($this->input->post('idcostheader')==3){
                    $branch_cat_data = $this->common_model->getRecords('partner_type','partner_type branch_category_name,id_partner_type');
                }else if($this->input->post('idcostheader')==2){
                    $branch_cat_data = $this->common_model->getRecords('branch_category','*',array('active'=>1));
                }else{
                    $branch_cat_data[0]['branch_category_name']='Percentage';
                }
                $i=0;
                $exist_cost_data = $this->common_model->getRecords('cost_data_config','*',array('cost_type'=>$this->input->post('idcostheader')));
                if(!empty($exist_cost_data)){
                    $arr_ids[] = $this->input->post('idcostheader');
                    $this->db->where('cost_type',$this->input->post('idcostheader'));
                    $this->db->where('month_year',$this->input->post('monthyear'));
                    $delete_cost_data=$this->db->delete('cost_data_config');

                }
                $postData['cost_type']=$this->input->post('idcostheader');
                $postData['month_year']=$this->input->post('monthyear');
                $postData['created_by']=$this->session->userdata('id_users');
                foreach($cost_header_data as $cost_data){
                    $postData['cost_header']=$cost_data['id_cost_header'];
                    foreach($branch_cat_data as $cat_data){
                        if(!empty($cat_data['id_branch_category'])){
                            $postData['branch_category']=$cat_data['id_branch_category'];
                        }
                        if(!empty($cat_data['id_partner_type'])){
                            $postData['partener_type']=$cat_data['id_partner_type'];
                        }

                        $postData['cost_amount']=$this->input->post(str_replace(' ', '',$cat_data['branch_category_name']))[$i];
                        $ins_branch=$this->common_model->insertRow($postData, 'cost_data_config');
                    }
                    $i++;

                }
                $response['status']=true;
                $response['message']='Branch Data Updated Successfully';
                echo json_encode($response);
            }
        }
    ?>