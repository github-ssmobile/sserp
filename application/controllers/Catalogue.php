<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalogue extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
         $this->load->model('Sale_model');
    }

    public function send_mail() {
        sendEmail('vg.gonjari@gmail.com','test','msg');    
    }   
    
    public function config_dashboard() {
            $q['tab_active'] = '';                        
            $q['type_data'] = $this->General_model->get_product_category_data();
            $q['category_data'] = $this->General_model->get_category_data();
            $q['brand_data'] = $this->General_model->get_brand_data();
            $q['model_data'] = $this->General_model->get_model_data();            
            $this->load->view('dashboard_config',$q);
    }
        
    public function user_dashboard() {
           // Admin
            $q['tab_active'] = '';            
            $q['menu_data'] = $this->General_model->get_menu_data();
            $q['branch_data'] = $this->General_model->get_branch_data();            
            $q['model_data'] = $this->General_model->get_model_data();
            $q['vendor_data'] = $this->General_model->get_vendor_data();
            $q['user_data'] = $this->General_model->get_user_data();
            $q['type_data'] = $this->General_model->get_product_category_data();
            $q['brand_data'] = $this->General_model->get_brand_data();
            $q['godown_data'] = $this->General_model->get_godown_data();
            $this->load->view('dashboard_admin',$q);
    }
        
    public function product_category_details() {
        $q['tab_active'] = '';
        $q['menus'] = $this->General_model->get_userrole_has_menu_byrole($this->session->userdata('idrole'));
        $q['type_data'] = $this->General_model->get_all_product_category_data();
        $this->load->view('catalogue/product_category_details',$q);
    }
    public function save_product_category() {
        
            $product_category=$this->input->post('product_category');
            $path = NULL;
            $path_thumb=NULL;
            if (!file_exists('assets/product_images/'.$product_category)) {
                mkdir('assets/product_images/'.$product_category, 0777, true);
            }            
            if($_FILES['userfile'] != ''){                
                $prodlink = 'assets/product_images/'.$product_category;
                $image = preg_replace('/\s+/', '', strtolower($product_category)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;     
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                }                 
        }
        $data = array(
            'product_category_name' => $this->input->post('product_category'),
            'image_path' => $path,
            'product_thumbnail' => $path_thumb,
            'active' => $this->input->post('status'),
            'enable_for_target' => $this->input->post('target'),
            'allow_target' => $this->input->post('allow_target'),
        );
        $this->General_model->save_product_category($data);
        $this->session->set_flashdata('save_data', 'Product Type Created');
        return redirect('Catalogue/product_category_details');
    }
    public function edit_product_category() { 
            $id = $this->input->post('id');  
            $product_category=$this->input->post('product_category');
            $prodlink = 'assets/product_images/'.$product_category;
            $old_product_category=$this->input->post('old_product_category');
            $image_path=$this->input->post('image_path');
            $path = $image_path;            
            if($product_category!=$old_product_category){                 
                $oldpath = FCPATH.'assets/product_images/'.$old_product_category.'/';
                $newpath = FCPATH.$prodlink.'/';                
                rename($oldpath,$newpath);    
            }
            if (!file_exists($prodlink)) {
                mkdir($prodlink, 0777, true);
            } 
            $path_thumb=NULL;
            $path = $prodlink.'/'.basename($image_path);    
            if($_FILES['userfile']['name'] != ''){                
                $image = preg_replace('/\s+/', '', strtolower($product_category)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                unlink(FCPATH.$prodlink.'/'.basename($image_path));     
                unlink(FCPATH.$prodlink.'/thumbnail/'.basename($image_path));     
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;    
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){                        
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }                    
                }                 
        }        
        $data = array(
            'product_category_name' => $this->input->post('product_category'),
            'image_path' => $path,
            'product_thumbnail' => $path_thumb,
            'active' => $this->input->post('status'),
            'enable_for_target' => $this->input->post('target'),
            'allow_target' => $this->input->post('allow_target'),
        );
        $this->General_model->edit_product_category($id, $data);
        $this->session->set_flashdata('save_data', 'Product Type Updated');
        return redirect('Catalogue/product_category_details');
    }
    
    
    public function attribute_type_details() {
        $q['tab_active'] = '';        
        $q['attribute_type'] = $this->General_model->get_attribute_type_data();
        $this->load->view('catalogue/attribute_type_details',$q);
    }
    
    public function attribute_details() {
        $q['tab_active'] = '';
        $q['attribute_type'] = $this->General_model->get_attribute_type_data();
        $q['attributes'] = $this->General_model->get_attribute_data();
        $this->load->view('catalogue/attribute_details',$q);
    }
    
     public function attribute_values($id) {
        $q['tab_active'] = ''; 
        $q['attribute'] =  $this->General_model->get_attribute_by_id($id);        
        $q['attributes_data'] = $this->General_model->get_attribute_value_data($id);
        $this->load->view('catalogue/attribute_values',$q);
    }
    
    public function save_attribute_type() {
        $data = array(
            'attribute_type' => $this->input->post('attribute_type'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_attribute_type($data);
        $this->session->set_flashdata('save_data', 'Attribute Type Created');
        return redirect('Catalogue/attribute_type_details');
    }
    
    public function save_attribute() {
        $data = array(
            'idattributetype ' => $this->input->post('idattributetype'),
            'attribute_name' => $this->input->post('attribute_name'),
            'active' => $this->input->post('status'),
            'has_predefined_values' => (($this->input->post('has_predefined_values')=='on')?1:0),
        );
        $this->General_model->save_db_attribute($data);
        $this->session->set_flashdata('save_data', 'Attribute Created');
        return redirect('Catalogue/attribute_details');
    }
    public function save_attribute_value() {
        $idattribute=$this->input->post('idattribute');
        $data = array(
            'idattribute' => $idattribute,
            'attribute_value' => $this->input->post('attribute_value')
        );
        $this->General_model->save_db_attribute_value($data);
        $this->session->set_flashdata('save_data', 'Attribute Updated');
        return redirect('Catalogue/attribute_values/'.$idattribute);
    }
    
    
    public function edit_attribute_type() { 
            $id = $this->input->post('id');           
            $data = array(
                'attribute_type' => $this->input->post('attribute_type'),
                'active' => $this->input->post('status'),
            );
        $this->General_model->edit_db_attribute_type($id, $data);
        $this->session->set_flashdata('save_data', 'Attribute Type Updated');
        return redirect('Catalogue/attribute_type_details');
    }
    public function edit_attribute() { 
            $id = $this->input->post('id');                    
            $data = array(
                'attribute_name' => $this->input->post('attribute_name'),
                'active' => $this->input->post('status'),                
                'has_predefined_values' => (($this->input->post('has_predefined_values')=='on')?1:0),
            );
        $this->General_model->edit_db_attribute($id, $data);
        $this->session->set_flashdata('save_data', 'Attribute Updated');
        return redirect('Catalogue/attribute_details');
    }
    public function edit_attribute_value() { 
            $id = $this->input->post('id');    
            $idattribute=$this->input->post('idattribute');
            $data = array(
                'attribute_value' => $this->input->post('attribute_value')
            );
        $this->General_model->edit_db_attribute_value($id, $data);
        $this->session->set_flashdata('save_data', 'Attribute Value Updated');
        return redirect('Catalogue/attribute_values/'.$idattribute);
    }
    
     public function detele_attribute_value() { 
            $id = $this->input->post('id');               
            $res = $this->General_model->detele_db_attribute_value($id);
            echo $res; 
    }
    
    
    public function category_details() {
        $q['tab_active'] = '';
        $q['category_data'] = $this->General_model->get_category_all_data();
        $q['attribute_data'] = $this->General_model->get_active_attribute_data();        
        $q['type_data'] = $this->General_model->get_product_category_data();
        $this->load->view('catalogue/category_details',$q);
    }
    
    public function category_edit($id) {
        $q['tab_active'] = '';
        $q['category'] = $this->General_model->get_category_all_data_by_id($id);        
        $q['category_attribute_data'] = $this->General_model->get_all_category_attributes_byid($id);
        $q['attribute_data'] = $this->General_model->get_active_attribute_data();                
        $this->load->view('catalogue/edit_category',$q);
    }
    
    
    public function save_category() {
        $product_category=$this->input->post('product_category');
        $category=$this->input->post('category');
        $prodlink='assets/product_images/'.$product_category.'/'.$this->input->post('category');
        if (!file_exists($prodlink)) {
            mkdir($prodlink, 0777, true);
        }
        $path=null;
        $path_thumb=null;
        if($_FILES['userfile']['name'] != ''){                                
                $image = preg_replace('/\s+/', '', strtolower($category)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;  
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    } 
                }                 
        }
        $data = array(
            'category_name' => $this->input->post('category'),
            'hsn' => $this->input->post('hsn'),
            'idproductcategory ' => $this->input->post('product_category_id'),
            'active' => $this->input->post('status'),
            'has_sub_brand' => (($this->input->post('has_sub_brand')=='on')?1:0),
            'category_image_path' => $path,
            'category_thumbnail' => $path_thumb,
            'is_model_name' =>  (($this->input->post('pattern')=='on')?1:0),
            'norm_sequence' => $this->input->post('idnorm'),
        );
        $this->General_model->save_category($data);        
        $this->session->set_flashdata('save_data', 'Category Created');
        return redirect('Catalogue/category_details');
    }    
    public function edit_category() {
            $id = $this->input->post('id');        
            $product_category_name=$this->input->post('product_category_name');
            $category=$this->input->post('category');
            $old_category=$this->input->post('old_category');
            $prodlink = 'assets/product_images/'.$product_category_name.'/'.$category;
            
            $image_path=$this->input->post('image_path');
            $path = $image_path;            
            if($category!=$old_category){                 
                $oldpath = FCPATH.'assets/product_images/'.$product_category_name.'/'.$old_category.'/';
                $newpath = FCPATH.$prodlink.'/';                
                rename($oldpath,$newpath);    
            }
            if (!file_exists($prodlink)) {
                mkdir($prodlink, 0777, true);
            } 
            
            $path = $prodlink.'/'.basename($image_path);  
            $path_thumb=NULL;
            if($_FILES['userfile']['name'] != ''){                
                $image = preg_replace('/\s+/', '', strtolower($category)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                unlink(FCPATH.$prodlink.'/'.basename($image_path)); 
                unlink(FCPATH.$prodlink.'/thumbnail/'.basename($image_path));
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;      
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                }                 
        }     
        
        $data = array(
            'category_name' => $this->input->post('category'),
            'hsn' => $this->input->post('hsn'),
            'active' => $this->input->post('status'),
            'category_image_path' => $path,
            'category_thumbnail' => $path_thumb,
            'has_sub_brand' => (($this->input->post('has_sub_brand')=='on')?1:0),
            'is_model_name' =>  (($this->input->post('pattern')=='on')?1:0),
            'norm_sequence' => $this->input->post('idnorm'),
        );
        
        $this->General_model->edit_category($id, $data);
        $this->session->set_flashdata('save_data', 'Category Updated');
        return redirect('Catalogue/category_details');
    }
    
    public function ajax_updatet_category_has_attributes(){
         $data = $this->input->post('data'); 
         $status = $this->input->post('checked'); 
         $tra_type = $this->input->post('type'); 
         $res=$this->General_model->edit_db_category($status,$data,$tra_type);
         echo $res;
        
    }
    
    
    public function brand_details() {
        $q['tab_active'] = '';        
        $q['brand_data'] = $this->General_model->get_brand_data();
        $this->load->view('catalogue/brand_details',$q);
    }
    public function save_product_brand() {
            $brand=$this->input->post('brand');
            $path = NULL;
            $path_thumb = NULL;
            if (!file_exists('assets/product_images/'.$brand)) {
                mkdir('assets/product_images/'.$brand, 0777, true);
            }            
            if($_FILES['userfile'] != ''){                
                $prodlink = 'assets/product_images/'.$brand;
                $image = preg_replace('/\s+/', '', strtolower($brand)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;                        
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                }                 
        }
        
        $data = array(
            'brand_name' => $this->input->post('brand'),
            'active' => $this->input->post('status'),
            'brand_image_path' => $path,
            'brand_thumbnail' => $path_thumb,
             'norm_sequence' => $this->input->post('idnorm'),
        );
        $this->General_model->save_product_brand($data);
        $this->session->set_flashdata('save_data', 'Brand Created');
        return redirect('Catalogue/brand_details');
    }
    
    

    public function edit_brand() {
            $id = $this->input->post('id');        
            $brand=$this->input->post('brand');
            $prodlink = 'assets/product_images/'.$brand;
            $old_brand=$this->input->post('old_brand');
            $image_path=$this->input->post('image_path');
            $path = $image_path;            
            if($brand!=$old_brand){                 
                $oldpath = FCPATH.'assets/product_images/'.$old_brand.'/';
                $newpath = FCPATH.$prodlink.'/';                
                rename($oldpath,$newpath);    
            }
            if (!file_exists($prodlink)) {
                mkdir($prodlink, 0777, true);
            } 
            
            $path = $prodlink.'/'.basename($image_path); 
            $path_thumb=NULL;
            if($_FILES['userfile']['name'] != ''){                
                $image = preg_replace('/\s+/', '', strtolower($brand)); 
                $filename=$_FILES['userfile']['name'];
                $newName = $image.".".pathinfo($filename, PATHINFO_EXTENSION); 
                $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                unlink(FCPATH.$prodlink.'/'.basename($image_path));  
                unlink(FCPATH.$prodlink.'/thumbnail/'.basename($image_path));
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('userfile')){
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path=$prodlink."/".$img;                        
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                }                 
        } 
        $data = array(
            'brand_name' => $this->input->post('brand'),
            'active' => $this->input->post('status'),
            'brand_image_path' => $path,
            'brand_thumbnail' => $path_thumb,
             'norm_sequence' => $this->input->post('idnorm'),
        );
        $this->General_model->edit_brand($id, $data);
        $this->session->set_flashdata('save_data', 'Product Brand Updated');
        return redirect('Catalogue/brand_details');
    }

    public function model_details() {
        $q['tab_active'] = '';
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $q['model_data'] = $this->General_model->get_model_all_data();    
//        $this->load->view('catalogue/model_details',$q);
        $this->load->view('catalogue/model_listing',$q);
    }
    public function add_model() {
        $q['tab_active'] = '';           
        $q['type_data'] = $this->General_model->get_product_category_data();
        $q['sku_type_data'] = $this->General_model->get_sku_type_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();   
        
        $this->load->library('CKEditor');
        $this->ckeditor->basePath = base_url().'assets/ckeditor/';
        $this->ckeditor->config['toolbar'] = 'Full';
        $this->ckeditor->config['language'] = 'it';
        
        $this->load->view('catalogue/add_new_mode',$q);
    }
    public function edit_model($id) {
        $q['tab_active'] = '';                   
        $q['model_data'] = $this->General_model->get_model_data_by_id($id);        
        $q['sku_type_data'] = $this->General_model->get_sku_type_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $this->load->library('CKEditor');
        $this->ckeditor->basePath = base_url().'assets/ckeditor/';
        $this->ckeditor->config['toolbar'] = 'Full';
        $this->ckeditor->config['language'] = 'it';
//        die('<pre>' . print_r($q['model_data'], 1) . '</pre>');
        $this->load->view('catalogue/edit_model_details',$q);
    }

   
    public function ajax_get_category_by_product_category() {
        $category = $this->General_model->get_category_by_product_category($this->input->post('product_category'));
        
        echo '<select class="chosen-select form-control" name="category" id="category" required=""><option value="">Select Category</option><option value="0">All</option>';
        foreach ($category as $cat) { 
            echo '<option is_model_name="'.$cat->is_model_name.'" has_sub_brand="'.$cat->has_sub_brand.'" value="'.$cat->id_category .'">'.$cat->category_name.'</option>';
        }
    } 
    
    public function ajax_get_model_by_brand() {
        $category = $this->General_model->get_get_model_by_brand($this->input->post('brand'));
        
        echo '<select class="chosen-select form-control" name="model1" id="model1" required=""><option value="">Select Model</option>';
        foreach ($category as $cat) { 
            echo '<option  value="'.$cat->id_model .'">'.$cat->model_name.'</option>';
        }
    } 
    public function ajax_get_category_variant(){
        $html=array();
        $category_variant = $this->General_model->get_category_variantid($this->input->post('category'));
         foreach ($category_variant as $attri) {             
            $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
            $name = $attri->id_category_attribute.'_'.$attri->idattributetype.'_'.$attri->idattribute.'_'.$na_me;            
            $html[]=$name;
         }
        $output = json_encode($html);
        die($output);
    }
    public function ajax_get_category_variant_edit(){
        $html=array(); 
        $category_variant = $this->General_model->get_category_variantid($this->input->post('category'));
         foreach ($category_variant as $attri) {             
            $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name)); 
            $name = $attri->id_category_attribute.'_'.$attri->idattributetype.'_'.$attri->idattribute.'_'.$na_me; 
            $html[]=$name;
         }
        $output = json_encode($html);
        die($output);
    }
    public function ajax_get_category_attributes_by_id() {
        
        $category_attribute = $this->General_model->get_category_has_attributes_byid($this->input->post('category'));
        $category_variant = $this->General_model->get_category_variantid($this->input->post('category'));
        
        $html=array();
        $data = "";
        if(count($category_attribute)>0){
        $data.='<h4 class="col-md-12 text-center">Specifications</h4><div class="clearfix"></div><hr style="margin-top: 10px !important;margin-bottom: 10px !important;">';
        $old_type=null;
        foreach ($category_attribute as $attri) { 
                if($old_type==null){
                    $data.='<div class="col-md-10 col-md-offset-1">';
                    $data.='<label class="col-md-12">'.$attri->attribute_type.'</label>';
                    $data.='</div><div class="clearfix"></div><br>';
                }else if($old_type==$attri->attribute_type){
                    
                }else{
                    $data.='<hr style="margin-top: 10px !important;margin-bottom: 10px !important;"><div class="col-md-10 col-md-offset-1">';
                    $data.='<label class="col-md-12">'.$attri->attribute_type.'</label>';
                    $data.='</div><div class="clearfix"></div><br>'; 
                }
                    $data.='<div class="col-md-10 col-md-offset-1">';
                    $data.='<span class="col-md-4">'.$attri->attribute_name.'</span>';
                    $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
                    $name = $attri->id_category_attribute.'_'.$attri->idattributetype.'_'.$attri->idattribute.'_'.$na_me;            
                if($attri->has_predefined_values==1){
                        $data.='<div class="col-md-8">';
                        $predefined = $this->General_model->get_attribute_values_by_id($attri->id_attribute);
                        $data.='<select class="chosen-select form-control" name="'.$name.'" id="'.$name.'" >';
                        $data.='<option value="">Select '.ucfirst($attri->attribute_name).' </option>';
                        foreach ($predefined as $pre){                            
                            if($attri->id_attribute==$pre->idattribute){
                                $data.='<option value="'.$pre->attribute_value.'">'.$pre->attribute_value.' </option>';                            
                            }
                        }
                        $data.='</select>';
                        $data.='</div>';
//                        $data.='<div class="col-md-2">';
//                        $data.='<a class="btn btn-outline-info add-val" idattribute="'.$attri->id_attribute.'" >Add New</a>';
//                        $data.='</div>';
                        $data.='</div>';
                    }else{
                        $data.='<div class="col-md-8">';
                        $data.='<input type="text" name="'.$name.'" id="'.$name.'" class="form-control model1"  placeholder="Enter '.strtolower($attri->attribute_name).'" />';    //
                        $data.='</div>';                        
                    }                
                $data.='</div>';
                $data.='<div class="clearfix"></div><br>';
                $old_type=$attri->attribute_type;            
        }
        }
        
        $html['att']=$data;
        $dataa="";
        $names="";
         if(count($category_variant)>0){
        $dataa.='<h4 class="col-md-12 text-center">Variants</h4><div class="clearfix"></div><hr style="margin-top: 10px !important;margin-bottom: 10px !important;">';
        
        $dataa.='<div >';
        
        foreach ($category_variant as $attri) {                 
                    
                    $dataa.='<div class="col-md-4" style="padding: 5px;">';
                    $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
                    $name = $attri->id_category_attribute.'_'.$attri->idattributetype.'_'.$attri->idattribute.'_'.$na_me;            
                    $names.=$attri->attribute_name.",";
                    if($attri->has_predefined_values==1){
                        $dataa.='<div class="col-md-12">';
                        $predefined = $this->General_model->get_attribute_values_by_id($attri->id_attribute);
                        $dataa.='<select class="chosen-select form-control" name="'.$name.'_" id="'.$name.'" >';
                        $dataa.='<option value="">Select '.ucfirst($attri->attribute_name).' </option>';
                        foreach ($predefined as $pre){                            
                            if($attri->id_attribute==$pre->idattribute){
                                $dataa.='<option value="'.$pre->attribute_value.'">'.$pre->attribute_value.' </option>';                            
                            }
                        }
                        $dataa.='</select>';
                        $dataa.='</div>';
//                        $dataa.='<div class="col-md-4">';
//                        $dataa.='<a class="btn btn-outline-info add-val" idattribute="'.$attri->id_attribute.'" >Add New</a>';
//                        $dataa.='</div>';
                       
                    }else{
                        $dataa.='<div class="col-md-12">';
                        $dataa.='<input type="text" name="'.$name.'_" id="'.$name.'" class="form-control model1"  placeholder="Enter '.strtolower($attri->attribute_name).'" />';    //
                        $dataa.='</div>';                        
                    }
                    $dataa.='</div>';                    
                }
                $dataa.='<div class="clearfix"></div><br>';
        }
        $html['names']=rtrim($names, ',');
        $html['vart']=$dataa;        
        $output = json_encode($html);
        die($output);
        
    }    
    public function ajax_get_model_by_category_brand() {
        $models = $this->General_model->ajax_get_model_by_category_brand($this->input->post('category'), $this->input->post('brand'));
        echo '<option value="0">Select Model</option>';
        foreach ($models as $model) { 
            echo '<option value="'.$model->id_model.'">'.$model->model_name.'</option>';
        }
    }
    
    
    public function ajax_get_model_byPCB($view) {
        $model_data = $this->General_model->get_all_models_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        
        if($view==0){
          $i=1; foreach ($model_data as $model){ ?>
            <div class="col-md-2 col-sm-4 btn waves-effect" title="<?php echo $model->full_name; ?>" style="padding: 2px !important;height: 295px !important;">
                    <div class="item-div">
                        <a target="_blank" href="<?php echo base_url('Catalogue/edit_model/'.$model->id_model) ?>">
                        <div class="image-url">
                            <div class="thumbnail" id="iimage-preview" style="max-height: 200px;min-height: 180px;border: 0px solid #fff  !important;margin-bottom: 5px !important;">                                                
                                <?php $path=''; if($model->variant_image_path==null){
                                    $path=base_url() . $model->model_image_path;
                                }else{ 
                                    $path=base_url() . $model->variant_image_path;
                               } ?>
                                <img class="img-view" src="<?php echo $path ?>"  id="userfileimage" />                                                
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="" style="margin: 10px !important; font-size: 14px;line-height: 1.4;white-space: normal;text-transform: none;">
                            <div style="height: 38px;">
                                <a class="link" title="<?php echo $model->full_name; ?>"  ><?php echo $model->full_name; ?></a>
                            </div>
                            <div class="clearfix"></div>
                            <div style="margin-top: 10px;">
                                <span class="price-ol">₹<?php echo $model->online_price; ?></span>
                                <span class="price-mrp">₹<?php echo $model->mrp; ?></span>                                
                                <span class="price-dis">
                                    <?php if ($model->mrp > 0) {
                                        echo round(((($model->mrp - $model->online_price) / $model->mrp) * 100));
                                    } else{ 
                                        echo '0';} ?>% off
                                    </span>        
                            </div>   
                            
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>     
                         </a>
                    </div>                
                </div>
             <?php } 
        }else{ ?>
             <thead>
                <th>Sr</th>
                <th>Product Category</th>
                <th>Category</th>
                <th>Brand</th>                
                <th>Model </th>
                <th>Status</th>
                <th>View</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($model_data as $model){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $model->product_category_name; ?></td>
                    <td><?php echo $model->category_name; ?></td>
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->full_name; ?></td>
                    
                    <td><?php if($model->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="thumbnail btn-link waves-effect" href="<?php echo base_url('Catalogue/edit_model/'.$model->idmodel) ?>"  style="margin: 0" >
                            <span class="mdi mdi-pen text-primary fa-lg"></span>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <?php
        }     
             
    }
    
    
    public function save_model() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');;
        
        $data = array();        
        $product_category=$this->input->post('product_category_name');
        $category=$this->input->post('category_name');
        $brand=$this->input->post('brand_name');
        $model=$this->input->post('model_name');     
        $image_names=$this->input->post('image_names');          
        
        $prodlink='assets/product_images/'.$product_category.'/'.$category.'/'.$brand.'/'.$model;
        if (!file_exists($prodlink)) {
            mkdir($prodlink, 0777, true);
        }
        chmod($prodlink,0777);
        $result=$this->General_model->save_db_model();
        $id_model=$result['model_id'];
        $variant_ids=$result['variant_ids'];
        if($id_model>0){
        $count = count($_FILES['files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES['files']['name'][$i])) {
                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                $path=null;  
                $num=strtotime(date('Y-m-d H:i:s'));
                $image = preg_replace('/\s+/', '-', strtolower($category.'-'.$brand.'-'.$model)); 
                $filename=$_FILES['files']['name'][$i];
                $newName = $image.'-'.$num.".".pathinfo($filename, PATHINFO_EXTENSION);
                 $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
               
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path_thumb=NULL;
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                    $data[]=array(
                        'model_image_path' => $prodlink."/".$img,
                        'idmodel' => $id_model,
                        'model_thumbnail' => $path_thumb
                        );
                }else{
                    //$data['totalFiles'] = $this->upload->display_errors();
                }
            }
        }        
        if(count($data)>0){
            $this->General_model->save_db_model_images($data);            
        }
        $variant_att_names=$this->input->post('variant_att_names');
        
        $data = array();      
        $cnt=0;
        foreach ($image_names as $file_name){
            $count = count($_FILES[$file_name]['name']);
            for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES[$file_name]['name'][$i])) {
                $_FILES['file']['name'] = $_FILES[$file_name]['name'][$i];
                $_FILES['file']['type'] = $_FILES[$file_name]['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES[$file_name]['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES[$file_name]['error'][$i];
                $_FILES['file']['size'] = $_FILES[$file_name]['size'][$i];
                $path=null;      
                $variant_name=str_replace('  ','-',trim($variant_att_names[$cnt]));
                $image = preg_replace('/\s+/', '', strtolower($brand.'-'.$model.'-'.$variant_name)); 
                $filename=$_FILES[$file_name]['name'][$i];
                $num=strtotime(date('Y-m-d H:i:s'));
                $newName = $image.'-'.$num.".".pathinfo($filename, PATHINFO_EXTENSION);
                 $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
               
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path_thumb=NULL;
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                    $data[]=array(
                        'variant_image_path' => $prodlink."/".$img,
                        'idvariant' => $variant_ids[$cnt],
                        'variant_thumbnail' => $path_thumb
                        );
                }else{
                    //$data['totalFiles'][] = $this->upload->display_errors();
                }
            }
        }
        $cnt++;
        }
        
        if(count($data)>0){
            $this->General_model->save_db_variant_images($data);            
        }
        
         $this->session->set_flashdata('save_data', 'Model Created');
         return redirect('Catalogue/model_details');
    }else{
         $this->session->set_flashdata('save_data', 'Fail to create model');
    }
    
    }
    
    public function save_edit_model($id_model) {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $data = array();        
        $product_category=$this->input->post('product_category_name');
        $category=$this->input->post('category_name');
        $brand=$this->input->post('brand_name');
        $model=$this->input->post('model_name');        
        $prodlink='assets/product_images/'.$product_category.'/'.$category.'/'.$brand.'/'.$model;
        
        if (!file_exists($prodlink)) {
            chmod($prodlink,0777);
            mkdir($prodlink, 0777, true);
        }
        $result=$this->General_model->edit_db_model($id_model);
        
        $image_names=$this->input->post('image_names');   
        $id_model=$result['model_id'];
        $variant_ids=$result['variant_ids'];
        if($id_model>0){
        
        if( isset($_FILES['files'])){
            $count = count($_FILES['files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES['files']['name'][$i])) {
                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                $_FILES['file']['size'] = $_FILES['files']['size'][$i];
                $path=null; 
                $num=strtotime(date('Y-m-d H:i:s'));
                $image = preg_replace('/\s+/', '-', strtolower($category.'-'.$brand.'-'.$model)); 
                $filename=$_FILES['files']['name'][$i];
                $newName = $image.'-'.$num.".".pathinfo($filename, PATHINFO_EXTENSION);
                 $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
               
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path_thumb=NULL;
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                    $data[]=array(
                        'model_image_path' => $prodlink."/".$img,
                        'idmodel' => $id_model,
                        'model_thumbnail' => $path_thumb
                        );
                }else{
                    //$data['totalFiles'][] = $this->upload->display_errors();
                }
            }
        }
        if(count($data)>0){
            $this->General_model->save_db_model_images($data);            
        }
       
        }
        
         $data = array();      
        $cnt=0;
        $variant_att_names=$this->input->post('variant_att_names');
        foreach ($image_names as $file_name){
            if(isset($_FILES[$file_name])){
            $count = count($_FILES[$file_name]['name']);
            for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES[$file_name]['name'][$i])) {
                $_FILES['file']['name'] = $_FILES[$file_name]['name'][$i];
                $_FILES['file']['type'] = $_FILES[$file_name]['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES[$file_name]['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES[$file_name]['error'][$i];
                $_FILES['file']['size'] = $_FILES[$file_name]['size'][$i];
                $path=null;         
                $variant_name=str_replace('  ','-',$variant_att_names[$cnt]);
                $image = preg_replace('/\s+/', '', strtolower($brand.'-'.$model.'-'.$variant_name)); 
                $filename=$_FILES[$file_name]['name'][$i];
                $num=strtotime(date('Y-m-d H:i:s'));
                $newName = $image.'-'.$num.".".pathinfo($filename, PATHINFO_EXTENSION);
                 $config = array(
                'image_library' => 'gd2',
                'upload_path' => $prodlink,
                'allowed_types' => 'jpg|jpeg|gif|png|jfif',
                'file_name' => $newName,
                'maintain_ratio' => TRUE,
                'create_thumb' => TRUE,
                'thumb_marker' => '_thumb',
                'width' => 15,
                'height' => 15
                );
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
               
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $img = $uploadData['file_name'];
                    $path_thumb=NULL;
                    if($this->resizeImage($uploadData['file_name'],$prodlink)){
                        $path_thumb=$prodlink."/thumbnail/".$img;
                    }
                    $data[]=array(
                        'variant_image_path' => $prodlink."/".$img,
                        'idvariant' => $variant_ids[$cnt],
                        'variant_thumbnail' => $path_thumb
                        );
                }else{
                    $data[] = $this->upload->display_errors();
                }
            }
        }
        
        $cnt++;
        }
                
        }
       
        if(count($data)>0){
            $this->General_model->save_db_variant_images($data);            
        }
        
        
        $this->session->set_flashdata('save_data', 'Model Updated');
         return redirect('Catalogue/model_details');
         
    }else{
         $this->session->set_flashdata('save_data', 'Fail to update model');
        }
    
    }
    
    
    
    public function ajax_remove_model_image($type) {
        
        $id= $this->input->post('id');
        $image_path = $this->input->post('image_path');
        //die(substr(sprintf('%o', fileperms(FCPATH.$image_path)), -4));
        chmod($image_path, 0777);
        if($this->General_model->remove_model_image($id,$type)){
            
            chmod(pathinfo(FCPATH.$image_path)['dirname'],0777);
            unlink(FCPATH.$image_path);
            unlink(FCPATH.pathinfo(FCPATH.$image_path)['dirname'].'/thumbnail/'.basename($image_path));
            echo "1";    
        }else{
            echo "0";
        }
    } 
    
    
   		public function fileUpload(){
		die(print_r($_FILES));
		if(!empty($_FILES['file']['name'])){
				
			// Set preference
			$config['upload_path'] = 'uploads/';	
			$config['allowed_types'] = 'jpg|jpeg|png|gif';
			$config['max_size']    = '1024'; // max_size in kb
			$config['file_name'] = $_FILES['file']['name'];
					
			//Load upload library
			$this->load->library('upload',$config);			
				
			// File upload
			if($this->upload->do_upload('file')){
				// Get data about the file
				$uploadData = $this->upload->data();
			}
		}
		
	}
    
    public function getbackup(){
        $this->load->dbutil();
        $prefs = array(     
                'format'      => 'zip',             
                'filename'    => 'my_db_backup.sql'
            );
        $backup =& $this->dbutil->backup($prefs); 
        $db_name = 'ipalace backup-on-'. date("d-m-Y") .'.zip';
        $save = site_url().'DB_Backup/'.$db_name;
        $this->load->helper('file');
        write_file($save, $backup); 
        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    
    
    public function resizeImage($filename, $prodlink) {

        $source_path = FCPATH . '/' . $prodlink . '/' . $filename;
        $target_path = FCPATH . '/' . $prodlink . '/thumbnail/';
        if (!file_exists($target_path)) {
            mkdir($target_path, 0777, true);
        }
        $config_manip = array(
            'image_library' => 'gd2',
            'source_image' => $source_path,
            'new_image' => $target_path,
            'maintain_ratio' => TRUE,
            'create_thumb' => TRUE,
            'thumb_marker' => '',
            'width' => 200,
            'height' => 200
        );
        $this->load->library('image_lib', $config_manip);
        $this->image_lib->initialize($config_manip);
        if($this->image_lib->resize()){
            return 1;
        }else{
            return 0;
        }
        $this->image_lib->clear();
    }
    
    public function old_model_mapping(){
        $q['tab_active'] = '';                        
        $q['model_data'] = $this->General_model->get_model_variant_data();            
		
        $q['old_model_data'] = $this->General_model->get_old_model_data();   
        $q['old_data'] = $this->General_model->get_allold_model_data();   		
//        die('<pre>'.print_r($q['old_model_data'],1).'</pre>');
        $this->load->view('catalogue/old_model_mapping',$q);
    }
    public function update_old_model_data(){
        $idoldmodel = $this->input->post('idoldmodel');
        $data = array(
            'idvariant' => $this->input->post('newmodel'),
        );
        $this->General_model->update_old_model($idoldmodel, $data);
//        $this->session->set_flashdata('save_data', 'Model data Updated Successfuly');
//        return redirect('Catalogue/old_model_mapping');
    }
    
    public function change_product_model(){
        $q['tab_active'] = '';       
        $q['type'] = 'normal';       
        $q['title'] = 'Product Model Change';
        $this->load->view('catalogue/product_model_change',$q);
    }
    
    public function convert_to_refurbished(){
        $q['tab_active'] = '';     
        $q['title'] = 'Demo to Refurbished';                        
        $q['type'] = 'refurbished';       
        $this->load->view('catalogue/product_model_change',$q);
    }
    
   /* public function ajax_track_imei_modal_change() {
        $imei = $this->input->post('imei');
        $type = $this->input->post('type');        
        $imei_history = $this->Sale_model->get_imei_history($imei);
        $saleid = 0;
        foreach ($imei_history as $history){ if($history->id_imei_details_link == 4){ $saleid = 1; }}
//        die('<pre>'.print_r($_POST,1).'</pre>');
         if($saleid == 0){ 
            if($type=='normal'){
                $modelvariants = $this->General_model->ajax_get_model_variant_alldata();                 
            }else{
                $modelvariants = $this->General_model->ajax_get_refurbished_model_variant(); 
            }
            $godown = $this->General_model->get_active_godown_data(); 
            
             if(count($imei_history) > 0){ ?>
            
             <div class="clearfix"></div><br><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <form>
                    <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                    <div class="col-md-1"> <b>Godown</b> </div>
                    <div class="col-md-3">
                        <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                            <?php if($type=='normal'){ ?>
                            <option value="">Select Godown</option>
                            <?php foreach($godown as $mvar){   ?>                                                               
                                    <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                            <?php } ?>
                            <?php }else{ ?>
                                <option value="1">New Godown</option> 
                           <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-1"> <b>Model</b> </div>
                    <div class="col-md-3">
                        <select class="chosen-select form-control" name='idmodel' id='idmodel'>
                            <option value="">Select Model</option>
                            <?php foreach($modelvariants as $mvar){ ?>
                            <option value="<?php echo $mvar->id_variant; ?>"><?php echo $mvar->full_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="imei" value="<?php echo $imei ?>">
                        <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/update_model_data">Submit</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
            <div class="clearfix"></div><br>  
            <div>
                <center><h4 style="margin-top: 0;color: red;"><span class="pe pe-7s-note2 fa-lg"></span> Select GODOWN and MODEL to convert</h4></center>
            </div>
            
            <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                <header>
                    <div class="text-center">
                        <h1><?php echo $imei_history[count($imei_history)-1]->full_name ?></h1>
                        <p><?php echo $imei; ?></p>
                    </div>
                </header>
            </div><div class="clearfix"></div><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <section class="timeline">
                    <div class="">
                        <?php $i=1; foreach ($imei_history as $history){ ?>
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content">
                                <h3><?php echo $history->entry_type ?></h3><hr>
                                <p style="font-size: 18px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="pull-right">'.$history->godown_name.'</small>' ?></p>
                                <?php if($history->transfer_from!=NULL){ ?>
                                <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                                <?php } ?>
                                <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android"></i> <?php echo $history->full_name ?></p>
                                <div class="date"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div><div class="clearfix"></div>
                                <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                                <?php if($history->url_link != NULL){ // Purchase,Purchase return ?>
                                <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                    <i class="fa fa-send-o fa-lg"></i>
                                </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
        <?php  ?>
              
        
            <?php }else{
                echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
                . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
            } ?>
          
        <?php } 
       
        
    }
    
    public function update_model_data(){
        $idvariant = $this->input->post('idmodel');
        $imei = $this->input->post('imei');
        $idgodown = $this->input->post('idgodown');
        $type = $this->input->post('type');  
        if($idgodown){            
            $stock_data = $this->General_model->ajax_stock_by_imei($imei);
            $imei_details = $this->General_model->get_model_variant_data_byidvariant($idvariant);
        if($imei_details){
            $stock = array(
                'idproductcategory' => $imei_details->idproductcategory,
                'idcategory' => $imei_details->idcategory,
                'idvariant' => $imei_details->id_variant,
                'idmodel' => $imei_details->idmodel,
                'idbrand' => $imei_details->idbrand,
                'idgodown' => $idgodown,
                'product_name' => $imei_details->full_name,
            );
//            die(print_r($stock));
            
                $this->Sale_model->update_stock_model_variant_data($stock, $imei);
            if($type=='normal'){
                $this->Sale_model->update_opening_model_variant_data($stock, $imei);
                $this->Sale_model->update_inwardproduct_model_variant_data($stock, $imei);

                $imei_histroy = array(
                    'idvariant' =>$imei_details->id_variant,
                    'model_variant_full_name' => $imei_details->full_name,
                    'idgodown' => $idgodown,
                );
                $this->Sale_model->update_imei_histroy_model_variant_data($imei_histroy, $imei);
                
            }else{
                $date = date('Y-m-d');
                $datetime = date('Y-m-d H:i:s');
                $iduser=$this->session->userdata('id_users');  
                 $imei_history[]=array(
                        'imei_no' =>$imei,
                        'entry_type' => 'Demo to Refurbished',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $stock_data->idbranch,
                        'idgodown' => $idgodown,
                        'model_variant_full_name' => $imei_details->full_name,
                        'idvariant' => $imei_details->id_variant,
                        'idimei_details_link' => 13, // Outward from imei_details_link table
                        'iduser' => $iduser
                        
                    ); 
                    $this->General_model->save_batch_imei_history($imei_history);
            }
            $this->session->set_flashdata('save_data', 'Model Converted Successfully');
        }
        }else{
            $this->session->set_flashdata('save_data', 'Select Godown and try again');            
        }
         return redirect('Catalogue/change_product_model');
        
    } */
    
    public function ajax_track_imei_modal_change() {
        $imei = $this->input->post('imei');
        $type = $this->input->post('type');        
        $imei_history = $this->Sale_model->get_imei_history($imei);
//        die(print_r($imei_history));
        $saleid = 0;
        foreach ($imei_history as $history){ if($history->id_imei_details_link == 4){ $saleid = 1; }}
//        die('<pre>'.print_r($imei_history,1).'</pre>');
         if($saleid == 0){ 
            if($type=='normal'){
                $modelvariants = $this->General_model->ajax_get_model_variant_alldata();                 
            }else{
                $modelvariants = $this->General_model->ajax_get_refurbished_model_variant(); 
            }
            $godown = $this->General_model->get_active_godown_data(); 
            
            if(count($imei_history) > 0) {      ?>
            
             <div class="clearfix"></div><br><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <?php if($_SESSION['level'] == 2){
                    $cnt = count($imei_history);
                    $cnt =   $cnt - 1;
                    if( $imei_history[$cnt]->id_godown == 1 && $imei_history[$cnt]->idbranch == $_SESSION['idbranch'] ){ ?>
                        <form>
                            <center><h3>New To Online Godown Change</h3></center><br>
                            <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                            <div class="col-md-1"> <b>Godown</b> </div>
                            <div class="col-md-3">
                                <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                                    <?php if($type=='normal'){ ?>
                                        <option value="">Select Godown</option>
                                        <?php foreach($godown as $mvar){
                                            if($mvar->id_godown == 6) {?>                                                               
                                            <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                                            <?php }  } ?>
                                    <?php }else{ ?>
                                        <option value="1">New Godown</option> 
                                   <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" name="imei" value="<?php echo $imei ?>">
                                <input type="hidden" name="idmodel" value="<?php echo $imei_history[$cnt]->idvariant ?>">
                                <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/update_model_data">Submit</button>
                            </div>
                            <div class="clearfix"></div><br>
                        </form>
                    <?php } else{ ?> 
                        <div style="color: red;text-align: center;font-size: 18px">Only New Godown Imei Change Allow From New Godown To Online Godown</div>
                    <?php }    
                } else { ?>
                <form>
                    <div>
                        <center><h4 style="margin-top: 0;color: red;"><span class="pe pe-7s-note2 fa-lg"></span> Select GODOWN and MODEL to convert</h4></center>
                    </div>
                    <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                    <div class="col-md-1"> <b>Godown</b> </div>
                    <div class="col-md-3">
                        <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                            <?php if($type=='normal'){ ?>
                            <option value="">Select Godown</option>
                            <?php foreach($godown as $mvar){   ?>                                                               
                                    <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                            <?php } ?>
                            <?php }else{ ?>
                                <option value="1">New Godown</option> 
                           <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-1"> <b>Model</b> </div>
                    <div class="col-md-3">
                        <select class="chosen-select form-control" name='idmodel' id='idmodel'>
                            <option value="">Select Model</option>
                            <?php foreach($modelvariants as $mvar){ ?>
                            <option value="<?php echo $mvar->id_variant; ?>"><?php echo $mvar->full_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="imei" value="<?php echo $imei ?>">
                        <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/update_model_data">Submit</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
                <?php } ?>
            </div>
            <div class="clearfix"></div><br>  
            
            <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                <header>
                    <div class="text-center">
                        <h1><?php echo $imei_history[count($imei_history)-1]->full_name ?></h1>
                        <p><?php echo $imei; ?></p>
                    </div>
                </header>
            </div><div class="clearfix"></div><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <section class="timeline">
                    <div class="">
                        <?php $i=1; foreach ($imei_history as $history){ ?>
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content">
                                <h3><?php echo $history->entry_type ?></h3><hr>
                                <p style="font-size: 18px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="pull-right">'.$history->godown_name.'</small>' ?></p>
                                <?php if($history->transfer_from!=NULL){ ?>
                                <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                                <?php } ?>
                                <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android"></i> <?php echo $history->full_name ?></p>
                                <div class="date"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div><div class="clearfix"></div>
                                <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                                <?php if($history->url_link != NULL){ // Purchase,Purchase return ?>
                                <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                    <i class="fa fa-send-o fa-lg"></i>
                                </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
        <?php }else{
                echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
                . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
            }
        } 
    }
    
    public function update_model_data(){
        $idvariant = $this->input->post('idmodel');
        $imei = $this->input->post('imei');
        $idgodown = $this->input->post('idgodown');
        $type = $this->input->post('type');  
//        die(print_r($_POST));
        if($idgodown){            
            $stock_data = $this->General_model->ajax_stock_by_imei($imei);
//            die('<pre>'.print_r($stock_data,1).'</pre>');
            $imei_details = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $imei_history_data = $this->Sale_model->get_imei_history_byimei($imei);
            if($imei_details){
                if($stock_data){
                    $stock = array(
                        'idproductcategory' => $imei_details->idproductcategory,
                        'idcategory' => $imei_details->idcategory,
                        'idvariant' => $imei_details->id_variant,
                        'idmodel' => $imei_details->idmodel,
                        'idbrand' => $imei_details->idbrand,
                        'idgodown' => $idgodown,
                        'product_name' => $imei_details->full_name,
                    );
                    $this->Sale_model->update_stock_model_variant_data($stock, $imei);
                    if($type=='normal'){
                        
                        if($_SESSION['level'] == 2){
                            $date = date('Y-m-d');
                            $datetime = date('Y-m-d H:i:s');
                            $iduser=$this->session->userdata('id_users');
                            if($stock_data->idbranch == 0){
                                $branc = $stock_data->temp_idbranch;
                            }else{
                                $branc = $stock_data->idbranch;
                            }
                            
                            $imei_history[]=array(
                                    'imei_no' =>$imei,
                                    'entry_type' => 'Godown Change',
                                    'entry_time' => $datetime,
                                    'date' => $date,
                                    'idbranch' => $branc,
                                    'idgodown' => $idgodown,
                                    'model_variant_full_name' => $imei_details->full_name,
                                    'idvariant' => $imei_details->id_variant,
                                    'idimei_details_link' => $imei_history_data->idimei_details_link, 
                                    'idlink' => $imei_history_data->idlink, 
                                    'iduser' => $iduser
                                ); 
        //                    die('<pre>'.print_r($imei_history,1).'</pre>');
                            $this->General_model->save_batch_imei_history($imei_history);
                        }else{
                            $this->Sale_model->update_opening_model_variant_data($stock, $imei);
                            $this->Sale_model->update_inwardproduct_model_variant_data($stock, $imei);

                            $imei_histroy = array(
                                'idvariant' =>$imei_details->id_variant,
                                'model_variant_full_name' => $imei_details->full_name,
                                'idgodown' => $idgodown,
                            );
                            $this->Sale_model->update_imei_histroy_model_variant_data($imei_histroy, $imei);
                        }
                    }else{
                        $date = date('Y-m-d');
                        $datetime = date('Y-m-d H:i:s');
                        $iduser=$this->session->userdata('id_users');  
                        $imei_history[]=array(
                                'imei_no' =>$imei,
                                'entry_type' => 'Demo to Refurbished',
                                'entry_time' => $datetime,
                                'date' => $date,
                                'idbranch' => $stock_data->idbranch,
                                'idgodown' => $idgodown,
                                'model_variant_full_name' => $imei_details->full_name,
                                'idvariant' => $imei_details->id_variant,
                                'idimei_details_link' => 13, // Outward from imei_details_link table
                                'iduser' => $iduser

                            ); 
                            $this->General_model->save_batch_imei_history($imei_history);
                    }
                    $this->session->set_flashdata('save_data', 'Model Converted Successfully');
                }else{
                    $this->session->set_flashdata('reject_data', 'Imei Not Found in System');
                }
            }
        }else{
            $this->session->set_flashdata('save_data', 'Select Godown and try again');            
        }
         return redirect('Catalogue/change_product_model');
        
    }
    
     public function change_product_demo_godown(){
        $q['tab_active'] = '';       
        $q['type'] = 'normal';       
        $q['title'] = 'Product Model Change';
        $this->load->view('catalogue/change_demo_to_new_godown',$q);
    }
    
    public function ajax_imei_godown_change() {
        $imei = $this->input->post('imei');
        $type = $this->input->post('type');        
        $imei_history = $this->Sale_model->get_imei_history($imei);
//        die('<pre>'.print_r($imei_history,1).'</pre>');
        $saleid = 0;
        foreach ($imei_history as $history){ 
            if($history->id_imei_details_link == 4){
                $saleid = 1;
                }
            }
//        die('<pre>'.print_r($imei_history,1).'</pre>');
         if($saleid == 0){ 
            if($type=='normal'){
                $modelvariants = $this->General_model->ajax_get_model_variant_alldata();                 
            }else{
                $modelvariants = $this->General_model->ajax_get_refurbished_model_variant(); 
            }
            $godown = $this->General_model->get_active_godown_data(); 
            
            if(count($imei_history) > 0) {      ?>
            
                <div class="clearfix"></div><br><br>
                <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                    <?php $cnt = count($imei_history);
                    $cnt =   $cnt - 1;
                    if( $imei_history[$cnt]->id_godown == 2){ 
                        if($_SESSION['level'] == 2){
                            //branch
                            if($imei_history[$cnt]->idbranch == $_SESSION['idbranch']){ ?>
                                <form>
                                <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                                <div class="col-md-1"> <b>Godown</b> </div>
                                <div class="col-md-3">
                                    <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                                        <?php if($type=='normal'){ ?>
                                            <option value="">Select Godown</option>
                                            <?php foreach($godown as $mvar){
                                                if($mvar->id_godown == 1) {?>                                                               
                                                <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                                                <?php }  } ?>
                                        <?php }else{ ?>
                                            <option value="1">New Godown</option> 
                                       <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" name="imei" value="<?php echo $imei ?>">
                                    <input type="hidden" name="idmodel" value="<?php echo $imei_history[$cnt]->idvariant ?>">
                                    <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/update_imei_godown_data">Submit</button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                            <?php } else { ?>
                                <div style="color: red;text-align: center;font-size: 18px">Imei Not Found in Your Branch</div>
                            <?php  } 
                        }else{ ?>
                           <!--//For Config-->
                            <form>
                                <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                                <div class="col-md-1"> <b>Godown</b> </div>
                                <div class="col-md-3">
                                    <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                                        <?php if($type=='normal'){ ?>
                                            <option value="">Select Godown</option>
                                            <?php foreach($godown as $mvar){
                                                if($mvar->id_godown == 1) {?>                                                               
                                                <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                                                <?php }  } ?>
                                        <?php }else{ ?>
                                            <option value="1">New Godown</option> 
                                       <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" name="imei" value="<?php echo $imei ?>">
                                    <input type="hidden" name="idmodel" value="<?php echo $imei_history[$cnt]->idvariant ?>">
                                    <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/update_imei_godown_data">Submit</button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        <?php }
                    } else {     ?>
                        <div style="color: red;text-align: center;font-size: 18px">Only Demo Godown Imei Change Allow From Demo Godown To New Godown</div>
                    <?php } ?>
                </div>
                <div class="clearfix"></div><br>  
            
            <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                <header>
                    <div class="text-center">
                        <h1><?php echo $imei_history[count($imei_history)-1]->full_name ?></h1>
                        <p><?php echo $imei; ?></p>
                    </div>
                </header>
            </div><div class="clearfix"></div><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <section class="timeline">
                    <div class="">
                        <?php $i=1; foreach ($imei_history as $history){ ?>
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content">
                                <h3><?php echo $history->entry_type ?></h3><hr>
                                <p style="font-size: 18px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="pull-right">'.$history->godown_name.'</small>' ?></p>
                                <?php if($history->transfer_from!=NULL){ ?>
                                <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                                <?php } ?>
                                <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android"></i> <?php echo $history->full_name ?></p>
                                <div class="date"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div><div class="clearfix"></div>
                                <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                                <?php if($history->url_link != NULL){ // Purchase,Purchase return ?>
                                <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                    <i class="fa fa-send-o fa-lg"></i>
                                </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
        <?php }else{
                echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
                . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
            }
        } 
    }
    
    public function update_imei_godown_data(){
        $idvariant = $this->input->post('idmodel');
        $imei = $this->input->post('imei');
        $idgodown = $this->input->post('idgodown');
        $type = $this->input->post('type');  
        if($idgodown){            
            $stock_data = $this->General_model->ajax_stock_by_imei($imei);
            $imei_details = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $imei_history_data = $this->Sale_model->get_imei_history_byimei($imei);
            if($imei_details){
                if($stock_data){
                    $stock = array(
                        'idproductcategory' => $imei_details->idproductcategory,
                        'idcategory' => $imei_details->idcategory,
                        'idvariant' => $imei_details->id_variant,
                        'idmodel' => $imei_details->idmodel,
                        'idbrand' => $imei_details->idbrand,
                        'idgodown' => $idgodown,
                        'product_name' => $imei_details->full_name,
                    );
                    $this->Sale_model->update_stock_model_variant_data($stock, $imei);
                    $date = date('Y-m-d');
                    $datetime = date('Y-m-d H:i:s');
                    $iduser=$this->session->userdata('id_users');  
                    $imei_history[] = array(
                        'imei_no' =>$imei,
                        'entry_type' => 'Godown Change',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $stock_data->idbranch,
                        'idgodown' => $idgodown,
                        'model_variant_full_name' => $imei_details->full_name,
                        'idvariant' => $imei_details->id_variant,
                        'idimei_details_link' => $imei_history_data->idimei_details_link, 
                        'idlink' => $imei_history_data->idlink, 
                        'iduser' => $iduser
                    ); 
                    $this->General_model->save_batch_imei_history($imei_history);
                    $this->session->set_flashdata('save_data', 'Godown Chnage Done Successfully');
                }else{
                    $this->session->set_flashdata('reject_data', 'Imei Not Found in Stock');
                }
            }else{
                $this->session->set_flashdata('reject_data', 'Imei Details not found');
            }
        }else{
            $this->session->set_flashdata('save_data', 'Select Godown and try again');            
        }
        return redirect('Catalogue/change_product_demo_godown');
    }
    
    //********* change imei godown****************
     public function change_product_godown(){
        $q['tab_active'] = '';       
        $q['type'] = 'normal';       
        $q['title'] = 'Product Godown Change';
        $this->load->view('catalogue/product_godown_change',$q);
    }
     public function ajax_track_imei_modal_stock_change() {
        $imei = $this->input->post('imei');
        $type = $this->input->post('type');        
        $imei_history = $this->Sale_model->get_imei_history($imei);
//        die(print_r($imei_history));
        $saleid = 0;
      //  die($saleid);
        foreach ($imei_history as $history){ if($history->id_imei_details_link == 4){ $saleid = 1; }}
        // if($saleid == 0){ 
            $godown = $this->General_model->get_active_godown_data(); 
            if(count($imei_history) > 0) {      ?>
             <div class="clearfix"></div><br><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <form>
                    <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
                    <div class="col-md-1"> <b>Godown</b> </div>
                    <div class="col-md-3">
                        <select class="chosen-select form-control" name='idgodown' id='idgodown'>
                            <option value="">Select Godown</option>
                            <?php foreach($godown as $mvar){ ?>                                                               
                                <option value="<?php echo $mvar->id_godown; ?>"><?php echo $mvar->godown_name; ?></option>                         
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="imei" value="<?php echo $imei ?>">
                        <button class="btn btn-primary" formmethod="post" formaction="<?php echo base_url()?>Catalogue/imei_godown_change">Submit</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
            <div class="clearfix"></div><br>  
            
            <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                <header>
                    <div class="text-center">
                        <h1><?php echo $imei_history[count($imei_history)-1]->full_name ?></h1>
                        <p><?php echo $imei; ?></p>
                    </div>
                </header>
            </div><div class="clearfix"></div><br>
            <div class="col-md-10 col-md-offset-1" style="padding: 0;">
                <section class="timeline">
                    <div class="">
                        <?php $i=1; foreach ($imei_history as $history){ ?>
                        <div class="timeline-item">
                            <div class="timeline-img"></div>
                            <div class="timeline-content">
                                <h3><?php echo $history->entry_type ?></h3><hr>
                                <p style="font-size: 18px"><i class="fa fa-bank"></i> <?php echo $history->branch_name.' <small class="pull-right">'.$history->godown_name.'</small>' ?></p>
                                <?php if($history->transfer_from!=NULL){ ?>
                                <p style="font-size: 14px"><i class="mdi mdi-truck-delivery fa-lg"></i> &nbsp;&nbsp;<?php echo $history->branch_from ?> &nbsp;&nbsp;<i class="mdi mdi-arrow-right-bold"> &nbsp;&nbsp;</i> <?php echo $history->branch_name ?></p>                                    
                                <?php } ?>
                                <p style="font-size: 14px"><i class="fa mdi mdi-cellphone-android"></i> <?php echo $history->full_name ?></p>
                                <div class="date"><?php echo date('d/m/Y h:i:s A', strtotime($history->entry_time)); ?></div><div class="clearfix"></div>
                                <!--<p><i class="mdi mdi-map-marker-radius fa-lg"></i> <?php // echo $history->branch_address ?></p>-->
                                <p><i class="mdi mdi-account-circle fa-lg"></i> <?php echo $history->user_name ?></p>
                                <?php if($history->url_link != NULL){ // Purchase,Purchase return ?>
                                <a class="bnt-more pull-right" target="_blank" href="<?php echo base_url($history->url_link.$history->idlink) ?>">
                                    <i class="fa fa-send-o fa-lg"></i>
                                </a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php $i++; } ?>
                    </div>
                </section>
            </div>
        <?php }else{
                echo '<center><h3><i class="mdi mdi-alert"></i> IMEI/SRNO Not present in system</h3>'
                . '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
            }
       // } 
    }
    
    public function imei_godown_change(){
//        die(print_r($_POST));
        $imei = $this->input->post('imei');
        $idgodown = $this->input->post('idgodown');
        $type = $this->input->post('type');  
        if($idgodown){            
            $stock_data = $this->General_model->ajax_stock_by_imei($imei);
            $idvariant = $stock_data->idvariant;
            $imei_details = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $imei_history_data = $this->Sale_model->get_imei_history_byimei($imei);
//            die(print_r($imei_history_data));
             if($imei_details){
                if($stock_data){
                    $stock = array(
                        'idgodown' => $idgodown,
                    );
                    $this->Sale_model->update_stock_model_variant_data($stock, $imei);
                    
                    $date = date('Y-m-d');
                    $datetime = date('Y-m-d H:i:s');
                    $iduser=$this->session->userdata('id_users');  
                    $imei_history[]=array(
                        'imei_no' =>$imei,
                        'entry_type' => 'Godown Change',
                        'entry_time' => $datetime,
                        'date' => $date,
                        'idbranch' => $stock_data->idbranch,
                        'idgodown' => $idgodown,
                        'model_variant_full_name' => $imei_details->full_name,
                        'idvariant' => $imei_details->id_variant,
                        'idimei_details_link' => 13, // Outward from imei_details_link table
                        'iduser' => $iduser
                    ); 
                    $this->General_model->save_batch_imei_history($imei_history);
                    
                    $this->session->set_flashdata('save_data', 'Imei Godown Change Successfully');
                }else{
                    $this->session->set_flashdata('reject_data', 'Imei Not Found in System');
                }
            }
        }else{
            $this->session->set_flashdata('save_data', 'Select Godown and try again');            
        }
        return redirect('Catalogue/change_product_godown');
        
    }
    
   
}