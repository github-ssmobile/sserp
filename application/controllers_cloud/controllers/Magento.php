<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Magento extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Magento_model");
        date_default_timezone_set('Asia/Kolkata');
    }

    public function index()
    {
        $q['tab_active'] = '';     
        $q['product_category'] = $this->Magento_model->get_categories();
        $this->load->view('magento/products',$q);    
    }
    
    public function ajax_get_category_by_product_category() {
        $category = $this->Magento_model->get_category_by_product_category($this->input->post('product_category'));
        
        echo '<select class="chosen-select form-control" name="category" id="category" required=""><option value="">Select Category</option>';
        foreach ($category as $cat) { 
            echo '<option  value="'.$cat['id'] .'">'.$cat['name'].'</option>';
        }
    }
    
    public function ajax_get_product_by_category(){
       
            $price_data=$this->Magento_model->get_product_by_category($this->input->post('category'));  
            ?>
               <thead>
                    <th>Id</th>                    
                    <th>Name</th>                 
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($price_data as $price){ ?>
                    <tr>
                        
                        <td><?php echo $price['id']; ?></td>
                        <td><?php echo $price['name']; ?></td>
                        
                    </tr>
                    <?php } ?>
                </tbody> 
            <?php
              
    }
    
   
}