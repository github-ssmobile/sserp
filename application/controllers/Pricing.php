<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pricing extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
    }

    public function offline_price_control()
    {   
        $q['tab_active'] = '';     
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $this->load->view('pricing/model_price',$q);        
    }
     public function price_control_report()
    {   
        $q['tab_active'] = '';     
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $this->load->view('pricing/model_price_report',$q);        
    }
    public function online_price_control()
    {   
        $q['tab_active'] = '';     
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $this->load->view('pricing/online_model_price',$q);        
    }
    
    public function price_report()
    {   
         $q['tab_active'] = '';            
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $q['price_data'] = $this->General_model->get_recent_price_data();
        $this->load->view('pricing/price_report',$q);
    }
    
    public function update_online_price(){
            $res=$this->General_model->update_db_on_price();  
            echo $res;  
    }
    public function update_price(){
            $res=$this->General_model->update_db_price();  
            echo $res;  
    }
    public function update_price_to_allvariants(){
//        die(print_r($_POST));
            $res=$this->General_model->update_db_all_variant_price();  
            echo $res;  
    }
    
    public function ajax_get_price_change_report(){
       
            $price_data=$this->General_model->get_price_data($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'),$this->input->post('from'),$this->input->post('to'));  
            ?>
               <thead>
                    <th>Sr</th>
                    <th>Product Category</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>MRP</th>
                    <th>MOP/Customer</th>
                    <th>Salesman</th>
                    <th>Landing</th>
                    <th>Online Price</th>
                    <th>Updated Time</th>                    
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($price_data as $price){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $price->product_category_name; ?></td>
                        <td><?php echo $price->category_name; ?></td>
                        <td><?php echo $price->brand_name; ?></td>
                        <td><?php echo $price->full_name; ?></td>
                        <td><?php echo $price->mrp; ?></td>
                        <td><?php echo $price->mop; ?></td>
                        <td><?php echo $price->salesman_price; ?></td>
                        <td><?php echo $price->landing; ?></td>
                        <td><?php echo $price->ponline_price; ?></td>
                        <td><?php echo $price->entry_time; ?></td>
                        
                    </tr>
                    <?php } ?>
                </tbody> 
            <?php
              
    }
    
     public function ajax_get_model_bycategory($price_type) {
       
        $model_data = $this->General_model->ajax_get_active_model_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        if($price_type==1){
        ?>
        <thead>
            <th>Sr</th>
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th>            
            <th>MRP</th>
            <th>MOP/Customer</th>
            <th>Salesman</th>
            <th>Landing</th>
            <th>isMOP</th>
            <th>isOnline</th>
            <th>Online Price</th>
            <th>Wholesale Price</th>
            <th>Best EMI</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>IGST</th>
            <th>Action</th>
            
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                
                <form class="model_price_submit_form">
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="mrp" class="mrp form-control input-sm" value="<?php echo $model->mrp; ?>" /></div>
                        <div class="mrp myDiv2"><?php echo $model->mrp; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="mop" class="form-control input-sm" value="<?php echo $model->mop; ?>" /></div>
                        <div class="mop myDiv2"><?php echo $model->mop; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="salesman" class="form-control input-sm" value="<?php echo $model->salesman_price; ?>" /></div>
                        <div class="salesman myDiv2"><?php echo $model->salesman_price; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none">
                            <input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" />                                                     
                            <input type="text" name="landing" class="form-control input-sm" value="<?php echo $model->landing; ?>" />
                        </div>
                        <div class="landing myDiv2"><?php echo $model->landing; ?></div>
                    </td>                    
                    <td>
                        <div class="myDiv1" style="display: none">
                            <div class="material-switch">
                                <?php $checked="";                            
                                if($model->is_mop==1){
                                    $checked="checked";
                                }?>
                                <!--<input type='hidden' value='0' name='is_mop'>-->
                                <input id="active_is_mop<?php echo $model->id_variant ?>" name="is_mop"  type="checkbox" <?php echo $checked ?> />
                                <label for="active_is_mop<?php echo $model->id_variant ?>" class="label-primary"></label>
                            </div>
                        </div>
                        <div class="is_mop myDiv2"><?php echo (($model->is_mop==1)?'Yes':'No'); ?></div>
                    </td>                    
                    <td>
                        <div class="myDiv1" style="display: none">
                            <div class="material-switch">
                                <?php $checked="";                            
                                if($model->is_online==1){
                                    $checked="checked";
                                }?>
                                <!--<input type='hidden' value='0' name='is_online'>-->
                                <input id="active_is_online<?php echo $model->id_variant ?>" name="is_online"  type="checkbox" <?php echo $checked ?> />
                                <label for="active_is_online<?php echo $model->id_variant ?>" class="label-primary"></label>
                            </div>
                        </div>
                        <div class="is_online myDiv2"><?php echo (($model->is_online==1)?'Yes':'No'); ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="online_price" class="form-control input-sm" value="<?php echo $model->online_price; ?>" /></div>
                        <div class="online_price myDiv2"><?php echo $model->online_price; ?></div>
                    </td> 
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="wholesale_price" class="form-control input-sm" value="<?php echo $model->corporate_sale_price; ?>" /></div>
                        <div class="wholesale_price myDiv2"><?php echo $model->corporate_sale_price; ?></div>
                    </td> 
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="emi" class="form-control input-sm" value="<?php echo $model->best_emi_price; ?>" /></div>
                        <div class="emi myDiv2"><?php echo $model->best_emi_price; ?></div>
                    </td> 
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="cgst" id="cgst" class="form-control input-sm" value="<?php echo $model->cgst; ?>" /></div>
                        <div class="cgst myDiv2"><?php echo $model->cgst; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="sgst" id="sgst" class="form-control input-sm" value="<?php echo $model->sgst; ?>" readonly=""/></div>
                        <div class="sgst myDiv2"><?php echo $model->sgst; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="igst" id="igst" class="form-control input-sm" value="<?php echo $model->igst; ?>" readonly=""/></div>
                        <div class="igst myDiv2"><?php echo $model->igst; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div>
                        <div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div>
                    </td>
                </form>
                
            </tr>
            
            <?php $i++; } ?>
        </tbody>


    <?php }else{ ?>
    
          <thead>
            <th>Sr</th>
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th> 
             <th>MRP</th>
            <th>MOP</th>
            <th>isOnline</th>
            <th>Online Price</th>
            <th>Best EMI</th>
            <th>Action</th>
            
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->mrp; ?></td>
                <td><?php echo $model->mop; ?></td>
                
                <form class="model_price_submit_form">                    
                                  
                    <td>
                        <div class="myDiv1" style="display: none">
                            <div class="material-switch">
                                <?php $checked="";                            
                                if($model->is_online==1){
                                    $checked="checked";
                                }?>
                                <!--<input type='hidden' value='0' name='is_online'>-->
                                <input id="active_is_online<?php echo $model->id_variant ?>" name="is_online"  type="checkbox" <?php echo $checked ?> />
                                <label for="active_is_online<?php echo $model->id_variant ?>" class="label-primary"></label>
                            </div>
                        </div>
                        <div class="is_online myDiv2"><?php echo (($model->is_online==1)?'Yes':'No'); ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none">
                            <input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" />                                                     
                            <input type="text" name="online_price" class="form-control input-sm" value="<?php echo $model->online_price; ?>" /></div>
                        <div class="online_price myDiv2"><?php echo $model->online_price; ?></div>
                    </td>                   
                   
                    <td>
                        <div class="myDiv1" style="display: none"><input type="text" name="emi" class="form-control input-sm" value="<?php echo $model->best_emi_price; ?>" /></div>
                        <div class="emi myDiv2"><?php echo $model->best_emi_price; ?></div>
                    </td>
                    <td>
                        <div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div>
                        <div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div>
                    </td>
                </form>
                
            </tr>
            
            <?php $i++; } ?>
        </tbody>
        
    <?php
    
        }
        
     }
    
     public function ajax_get_model_price_bycategory($price_type) {
       
        $model_data = $this->General_model->ajax_get_active_model_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        if($price_type==1){
        ?>
        <thead>
            <th>Sr</th>
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th>            
            <th>MRP</th>
            <th>MOP/Customer</th>
            <th>Salesman</th>
            <th>Landing</th>
            <th>Online Price</th>
            
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->mrp; ?></td>
                <td><?php echo $model->mop; ?></td>
                <td><?php echo $model->salesman_price; ?></td>
                <td><?php echo $model->landing; ?></td>                    
                <td><?php echo $model->online_price; ?></td> 
            </tr>
            <?php $i++; } ?>
        </tbody>
    <?php }else{ ?>
          <thead>
            <th>Sr</th>
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th> 
             <th>MRP</th>
            <th>MOP</th>
            <th>Online Price</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <td><?php echo $model->mrp; ?></td>
                <td><?php echo $model->mop; ?></td>
                <td><?php echo $model->online_price; ?></td>                   
            </tr>
            <?php $i++; } ?>
        </tbody>
    <?php
    
        }
        
     }
     
      public function price_update()
    {   
        $q['tab_active'] = '';     
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $this->load->view('pricing/price_update',$q);        
    }
    
    public function ajax_get_model_bycategory_nlc($price_type) {
       
        $model_data = $this->General_model->ajax_get_active_model_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        if($price_type==1){
        ?>
        <thead>
            <th>Sr</th>
            <th style="display: none">Idcategory</th>            
            <th style="display: none">Idproductcategory</th>            
            <th style="display: none">Idmoel</th>     
            <th style="display: none">Idbrand</th>     
            <th style="display: none">Idvariant</th>  
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th>            
            <th>MRP</th>
            <th>MOP/Customer</th>
            <th>Dp Price</th>
            <th>Scheme Amount</th>
            <th>Sale Kitty</th>
            <th>NLC Price</th>
            <th>Landing</th>
            <th>Action</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>
                <td style="display: none"><?php echo $model->idcategory; ?></td>
                <td style="display: none"><?php echo $model->idproductcategory; ?></td>
                <td style="display: none"><?php echo $model->idmodel; ?></td>
                <td style="display: none"><?php echo $model->idbrand ; ?></td>
                <td style="display: none"><?php echo $model->id_variant; ?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                <form class="model_price_submit_form">
                    <td><div class="myDiv1" style="display: none"><input type="text" name="mrp" class="mrp form-control input-sm" value="<?php echo $model->mrp; ?>" /></div><div class="mrp myDiv2"><?php echo $model->mrp; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="mop" class="form-control input-sm" value="<?php echo $model->mop; ?>" /></div><div class="mop myDiv2"><?php echo $model->mop; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="dp_price" class="form-control input-sm dpprice" value="<?php echo $model->dp_price; ?>" /></div><div class="dp_price myDiv2"><?php echo $model->dp_price; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="scheme_price" class="form-control input-sm schemeprice"  value="<?php echo $model->scheme_amount; ?>"></div><div class="scheme_price myDiv2"><?php echo $model->scheme_amount; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="text" name="sale_kitty" class="form-control input-sm salekitty"  value="<?php echo $model->sale_kitty; ?>"></div><div class="sale_kitty myDiv2"><?php echo $model->sale_kitty; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" /><input type="text" name="nlc_price" class="form-control input-sm nlcprice" readonly="" value="<?php echo $model->nlc_price; ?>" /></div><div class="nlc_price myDiv2"><?php echo $model->nlc_price; ?></div></td>
                      <td><?php echo $model->landing; ?></td>
                    <td><div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div><div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div></td>
                </form>
            </tr>
            <?php $i++; } ?>
        </tbody>
    <?php } ?>
    <script>
        $(document).ready(function (){ 
            
            $('.salekitty').focusout(function (){
                var parentdiv  = $(this).closest('tr')
                var kitty = +$(this).val();
                var dp = +$(parentdiv).find('.dpprice').val();
                var sch = +$(parentdiv).find('.schemeprice').val();
                var nlc = 0;
                if(dp > 0){
                   var nlc1 = dp - sch;
                   nlc = nlc1 + kitty;
                   $(parentdiv).find('.nlcprice').val(nlc);
                }
                else{
                    alert("DP Price is 0 ");
                    return false;
                }

            });
        });
    </script>
    <?php 
    }
     
    public function update_nlc_price(){
//        die(print_r($_POST));
        $res = $this->General_model->update_nlc_db_price();  
        echo $res;  
    }
    public function update_nlc_price_toall(){
//        die(print_r($_POST));
        $res = $this->General_model->update_nlc_db_price_allvariant();  
        echo $res;  
    }
    
    public function save_price_bulk_price(){
         $q['tab_active'] = '';         
        $this->db->trans_begin();
       $timestamp = time();
        $i =0;
        $filename=$_FILES["uploadfile"]["tmp_name"];
        if($_FILES["uploadfile"]["size"] > 0){
            $file = fopen($filename, "r");
            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){ 
                    //Model_variant table
                    $data = array(
//                        'mrp' => $openingdata[9],
//                        'mop' => $openingdata[10],
                        'dp_price' => $openingdata[11],
                        'scheme_amount' => $openingdata[12],
                        'sale_kitty' => $openingdata[13],
                        'nlc_price' => $openingdata[14],
                        'm_variant_lmt' => $timestamp,
                        'm_variant_lmb' => $_SESSION['id_users'],
                    );
                    $this->General_model->update_model_variants_byidvariant($data, $openingdata[5]);
                    //Price Table
                    $prince_data = array(
                        'idvariant' => $openingdata[5],
                        'idcategory' => $openingdata[1],
                        'idmodel' => $openingdata[3],
                        'idproductcategory'  => $openingdata[2],
                        'idbrand' => $openingdata[4],
                        'pmrp' => $openingdata[9],           
                        'pmop' => $openingdata[10],
                        'dp_price' => $openingdata[11],
                        'scheme_amount' => $openingdata[12],
                        'sale_kitty' => $openingdata[13],
                        'nlc_price' => $openingdata[14],
                        'created_by' => $_SESSION['id_users'],
                        'timestamp' => $timestamp,
                    );
                    $this->General_model->save_price($prince_data);
                }
                $i++;
            }
            fclose($file);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
             redirect('Pricing/price_update');
        }
        
    }
    
    
      /*********** Update GST *************/
    public function gst_price_control()
    {   
        $q['tab_active'] = '';     
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        $this->load->view('pricing/gst_price_update',$q);        
    }
    
    public function ajax_get_model_bycategory_gst($price_type) {
        $model_data = $this->General_model->ajax_get_active_model_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        if($price_type == 1){ ?>
            <thead style="background-color: #99ccff">
                <th>Sr</th>
                <th>Product Type</th>            
                <th>Brand</th>
                <th>Model</th>            
                <th>MRP</th>
                <th>MOP/Customer</th>
                <th>Salesman</th>
                <th>Landing</th>
                <th>isMOP</th>
                <th>isOnline</th>
                <th>Online Price</th>
                <th>Wholesale Price</th>
                <th>Best EMI</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Action</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach($model_data as $model){ ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $model->product_category_name; ?></td>                
                    <td><?php echo $model->brand_name; ?></td>
                    <td><?php echo $model->full_name; ?></td>
                    <form class="model_price_submit_form">
                        <td><?php echo $model->mrp; ?>
                            <div style="display: none"><input type="text" name="mrp" class="mrp form-control input-sm" value="<?php echo $model->mrp; ?>" /></div>
                        </td>
                        <td><?php echo $model->mop; ?>
                            <div  style="display: none"><input type="text" name="mop" class="form-control input-sm" value="<?php echo $model->mop; ?>" /></div>
                        </td>
                        <td><?php echo $model->salesman_price; ?>
                            <div  style="display: none"><input type="text" name="salesman" class="form-control input-sm" value="<?php echo $model->salesman_price; ?>" /></div>
                        </td>
                        <td><?php echo $model->landing; ?>
                            <div style="display: none">
                                <input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" />                                                     
                                <input type="text" name="landing" class="form-control input-sm" value="<?php echo $model->landing; ?>" />
                            </div>
                        </td>                    
                        <td><?php echo (($model->is_mop==1)?'Yes':'No'); ?>
                            <div style="display: none">
                                <div class="material-switch">
                                    <?php $checked="";                            
                                    if($model->is_mop==1){
                                        $checked="checked";
                                    }?>
                                    <input id="active_is_mop<?php echo $model->id_variant ?>" name="is_mop"  type="checkbox" <?php echo $checked ?> />
                                    <label for="active_is_mop<?php echo $model->id_variant ?>" class="label-primary"></label>
                                </div>
                            </div>
                        </td>                    
                        <td><?php echo (($model->is_online==1)?'Yes':'No'); ?>
                            <div style="display: none">
                                <div class="material-switch">
                                    <?php $checked="";                            
                                    if($model->is_online==1){
                                        $checked="checked";
                                    }?>
                                    <input id="active_is_online<?php echo $model->id_variant ?>" name="is_online"  type="checkbox" <?php echo $checked ?> />
                                    <label for="active_is_online<?php echo $model->id_variant ?>" class="label-primary"></label>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $model->online_price; ?>
                            <div style="display: none"><input type="text" name="online_price" class="form-control input-sm" value="<?php echo $model->online_price; ?>" /></div>
                        </td> 
                        <td><?php echo $model->corporate_sale_price; ?>
                            <div style="display: none"><input type="text" name="wholesale_price" class="form-control input-sm" value="<?php echo $model->corporate_sale_price; ?>" /></div>
                        </td> 
                        <td>
                            <?php echo $model->best_emi_price; ?>
                            <div style="display: none"><input type="text" name="emi" class="form-control input-sm" value="<?php echo $model->best_emi_price; ?>" /></div>
                        </td> 
                        <td>
                            <div class="myDiv1" style="display: none"><input type="text" name="cgst" id="cgst" class="form-control input-sm" value="<?php echo $model->cgst; ?>" /></div>
                            <div class="cgst myDiv2"><?php echo $model->cgst; ?></div>
                        </td>
                        <td>
                            <div class="myDiv1" style="display: none"><input type="text" name="sgst" id="sgst" class="form-control input-sm" value="<?php echo $model->sgst; ?>" readonly=""/></div>
                            <div class="sgst myDiv2"><?php echo $model->sgst; ?></div>
                        </td>
                        <td>
                            <div class="myDiv1" style="display: none"><input type="text" name="igst" id="igst" class="form-control input-sm" value="<?php echo $model->igst; ?>" readonly=""/></div>
                            <div class="igst myDiv2"><?php echo $model->igst; ?></div>
                        </td>
                        <td>
                            <div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div>
                            <div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div>
                        </td>
                    </form>
                </tr>
                <?php $i++; } ?>
            </tbody>
        <?php } 
    }
    public function update_gst_price(){
            $res=$this->General_model->update_gst_db_price();  
            echo $res;  
    }
    public function update_gst_price_to_allvariants(){
            $res=$this->General_model->update_gst_db_all_variant_price();  
            echo $res;  
    }
}