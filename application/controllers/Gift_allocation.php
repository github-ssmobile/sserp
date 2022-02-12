<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gift_allocation extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Allocation_model");
        $this->load->model("Outward_model");
        $this->load->model("Inward_model");
        $this->load->model("General_model");
        date_default_timezone_set('Asia/Kolkata');
    }
   
    public function gift_allocation()
    {   $user_id=$this->session->userdata('id_users');
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $warehouse=$this->session->userdata('idbranch');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        $role = $this->General_model->get_role_byid($this->session->userdata('idrole'));  
        if(count($res)>0){    
             if($level==1){  
                /// Temporary to show all routes to gandhinager warehouse(7) ///
                if($warehouse==7){
                    $q['route_data'] = $this->General_model->get_route_data();                    
                }else{
                    $q['route_data'] = $this->General_model->get_route_by_warehouse_user_id($warehouse,'');                                                         
                }
                // *END* //
             }else{
                /// Temporary to show all routes to gandhinager warehouse(7) ///
                if($warehouse==7){
                    $q['route_data'] = $this->General_model->get_route_data();                    
                }else{
                    $q['route_data'] = $this->General_model->get_route_by_warehouse_user_id($warehouse,'');                                                         
                }                
                // *END* //               
             }
             if($role->has_product_category==1){
                $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);                 
            }else{
                $q['product_category'] = $this->General_model->get_product_category_data();                   
            }
            if($role->has_brand==1){
                $q['brand_data'] = $this->General_model->get_brands_by_user($user_id);         
            }else{
                $q['brand_data'] = $this->General_model->get_active_brand_data();             
            }
            $q['active_godown'] = $this->General_model->get_allowed_for_allocation_godowns();
            $this->load->view('allocation/gift_allocation', $q);
        }else{
            redirect('Stock_allocation/404');
        }
    }     
    public function ajax_route_allocation_data(){
        $allocation_type=2;
        $user_id=$this->session->userdata('id_users');
        $idroute = $this->input->post('idroute'); 
        $days = $this->input->post('days'); 
        $idbrand = $this->input->post('brand'); 
        $idgodown = $this->input->post('idgodown'); 
        $idproductcategory = $this->input->post('product_category');                
        $model_data=array();
        $warehouse =$this->session->userdata('idbranch');        
        $branches = $this->General_model->get_branches_by_routeid($idroute);
	$godown_to=$idgodown;
            if($idgodown==1){
                $godown_to=6;
            }
            else if($idgodown==6){
                $godown_to=1;
            }

        $wr = $this->Allocation_model->get_warehouse_stock_data(0,0,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type,$godown_to);
        $model_data = $this->Allocation_model->get_gift_route_allocation_data($idproductcategory,$idbrand,$days,$idgodown,$allocation_type,$idroute,$warehouse);
        ?>            
        <thead class="fixedelementtop" style="text-align: center;position: none !important;">               
            <th colspan='4' style=" position: sticky;top:0"><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" />
                <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>" />
                <input type="hidden" name="idproductcategory" value="<?php echo $idproductcategory; ?>" />
            </th>    
            <?php
            foreach ($branches as $branch){ ?>            
                <th colspan='2' ><input type="hidden" name="idbranch[]" value="<?php echo $branch->id_branch; ?>" /><?php echo $branch->branch_name;?></th>
            <?php }             ?>
        </thead>
        <thead class="fixedelement1" style="text-align: center;position: none !important;">
        <th>Model Name</th><th>Warehouse Qty</th> <th>Allocated Qty</th><th>Available Qty</th>   
            <?php
            foreach ($branches as $branch){ ?>
                
                <th>Stock</th>
               
                <th>Quantity</th>                       
            <?php } ?>
        </thead>   
        <tbody class="data_1">    
        <?php        
        $old=null;
        foreach ($model_data as $data){ 
             $full_name = clean($data->full_name); 
             
            if($old==null){
                $keys=multi_array_search($wr, array('id_variant'=>$data->id_variant));
				$key_s=multi_array_sum($model_data, array('id_variant'=>$data->id_variant));   
                $ho_stock_qty=$wr[$keys[0]]->ho_stock_qty;
                $allocated_qty=$wr[$keys[0]]->allocated_qty;
                $available=($ho_stock_qty-($allocated_qty))+$key_s;                
            ?>
            <tr>    
                <td class="fixleft" style="background: #ffcccc;"><?php echo $data->full_name ?> 
                    <input type="hidden" name="full_name[]" value="<?php echo $data->full_name; ?>" />
                    <input type="hidden" name="landing[]" value="<?php echo $data->landing; ?>" />
                    <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                    <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                    <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                    <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />
                    
                </td>
                <td class="fixleft1" style="background: #ffcccc;"><?php echo $ho_stock_qty ?></td>
                <td class="fixleft2" style="background: #ffcccc;"><?php echo $allocated_qty ?></td>
                <td class="fixleft3" style="background: #caffca;"><?php echo $available ?> <input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available<=0)?0:$available); ?>" /></td>
                
                <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
                
                <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $data->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$data->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                       
            
        <?php }else if($old==$data->id_variant){ ?>               
           
            <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
           
            <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $data->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$data->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                       
              <script>
            $(document).ready(function(){
            $("#variant_data").on('input', '.<?php echo $full_name; ?>', function () {
                    var total_sum = 0;
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
                    });
                });            
            </script>       
         <?php   }else{ 
                $keys=multi_array_search($wr, array('id_variant'=>$data->id_variant));
				$key_s=multi_array_sum($model_data, array('id_variant'=>$data->id_variant));   
                $ho_stock_qty=$wr[$keys[0]]->ho_stock_qty;
                $allocated_qty=$wr[$keys[0]]->allocated_qty;
                $available=($ho_stock_qty-($allocated_qty))+$key_s;                
             ?>                
            </tr>    
             <tr>    
                 <td class="fixleft" style="background: #ffcccc;"><?php echo $data->full_name ?>
                    <input type="hidden" name="full_name[]" value="<?php echo $data->full_name; ?>" />
                    <input type="hidden" name="landing[]" value="<?php echo $data->landing; ?>" />
                    <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                    <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                    <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                    <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />                    
                </td>
                <td class="fixleft1" style="background: #ffcccc;"><?php echo $ho_stock_qty ?></td>
                <td class="fixleft2" style="background: #ffcccc;"><?php echo $allocated_qty ?></td>
                <td class="fixleft3" style="background: #caffca;"><?php echo $available ?>
                    <input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available<=0)?0:$available); ?>" />
                </td>
               
            <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
           
            <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $data->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$data->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                       
              <script>
            $(document).ready(function(){
            $("#variant_data").on('input', '.<?php echo $full_name; ?>', function () {
                    var total_sum = 0;
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
                    });
                });            
            </script>      
       <?php  }
         $old=$data->id_variant;
        
            } ?>
            </tbody>
<?php         
    }   
    public function save_gift_route_allocation() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   '
        $allocation_type=$this->input->post('allocation_type');
        $idbranchs=$this->input->post('idbranch');
        $idgodown=$this->input->post('idgodown');
        $idproductcategory=$this->input->post('idproductcategory');
        $product_name=$this->input->post('full_name');        
        $price=$this->input->post('landing');    
        $idbrand=$this->input->post('idbrand');
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');
        $variants=$this->input->post('variants');    
        $idmodel_s=$this->input->post('idmodel');    
        $idcategory_s=$this->input->post('idcategory');    
        $idskutype_s=$this->input->post('idskutype');  
        $idwarehouse =$this->session->userdata('idbranch');
         $this->db->trans_begin();
    if (isset($_POST['qty']) && count($this->input->post('qty')) > 0) {
            $qty=$this->input->post('qty');  
            
            $i = 0;    
            $outward_product=array();
            $inward_stock_sku=array();
            $data_att = array();
            $update_stock=array();
            foreach ($qty as $branch=>$model_array) {                 
                            $array = array(
                            'allocation_type' => $allocation_type,
                            'idbranch' => $branch,
                            'idwarehouse' => $idwarehouse,
                            'date' => $date,
                            'timestamp' =>$timestamp,
                            'allocate_by' => $iduser,
                            'entry_time' => $datetime,
                            'status' => 3
                        );
                        $id_stock_allocation = $this->Allocation_model->save_branch_stock_allocation($array);
                        
                    $data = array(
                    'date' =>  $date,
                    'idbranch' => $branch,
                    'idwarehouse' =>  $idwarehouse,
                    'total_product' => count($model_array),                                
                    'outward_by' => $iduser,
                    'scan_time' => $datetime,            
                    'idstock_allocation' => $id_stock_allocation,                    
                );

                $idoutward = $this->Outward_model->save_outward($data);            
               
                    foreach ($model_array as $id_vatriant=>$qty) {  
                        $key=array_search($id_vatriant, $variants);
                        $data_att[] = array(
                            'idstock_allocation' => $id_stock_allocation,
                            'idbranch' => $branch,
                            'qty' => $qty,
                            'idvariant' => $id_vatriant,
                            'idmodel' => $idmodel_s[$key],                            
                            'idskutype' => $idskutype_s[$key],
                            'idgodown' => $idgodown,
                            'idproductcategory ' => $idproductcategory,
                            'idcategory' => $idcategory_s[$key],                            
                            'idbrand' => $idbrand,
                            'date' => $date,
                            'entry_time' => $datetime,
                            'created_by' => $iduser
                        );                        
                        ////  Outward Product Data For QTY SKU ////                
                        $outward_product[] = array(
                            'date' =>  $date,
                            'idbranch' => $branch,      
                            'imei_no' => '',
                            'idskutype' => $idskutype_s[$key],
                            'idgodown' => $idgodown,
                            'idproductcategory' => $idproductcategory,
                            'idcategory' => $idcategory_s[$key],
                            'idmodel' => $idmodel_s[$key],
                            'idvariant' => $id_vatriant,
                            'idbrand' => $idbrand,
                            'qty' => $qty,                    
                            'idoutward' => $idoutward,
                            'price' => $price[$key],                                          
                        );  
                
                ////  Stock Reflection ////     
                $update_stock[]="UPDATE stock SET qty = qty - ".$qty." WHERE idvariant = ".$id_vatriant." AND idgodown = ".$idgodown." AND idbranch = ".$idwarehouse."; ";
                $inward_stock_sku[] = array(
                    'date' =>  $date,
                    'outward_time' => $datetime,
                    'idbranch' => 0,
                    'temp_idbranch' => $branch,
                    'transfer_from' => $idwarehouse,
                    'outward_dc' => $idoutward,
                    'outward_by' => $iduser,
                    'product_name' => $product_name[$key],
                    'idskutype' => 4,
                    'idgodown' => $idgodown,
                    'idproductcategory' => $idproductcategory,
                    'idcategory' => $idcategory_s[$key],
                    'idmodel' => $idmodel_s[$key],
                    'idvariant' => $id_vatriant,
                    'idbrand' => $idbrand,
                    'created_by' => $iduser,
                    'idvendor' => 1,
                    'qty' => $qty,
                    'outward' => 1,
                );   
                         
                    }                
            }             
            if (count($data_att) > 0) {
                $result = $this->Allocation_model->save_db_branch_allocation($data_att);               
            }                  
            $this->Outward_model->save_outward_product($outward_product);  
            if(count($inward_stock_sku)>0){ 
                $this->Inward_model->save_stock_batch($inward_stock_sku);           
                foreach ($update_stock as $data){
                    $this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($data);    
                }
                //$this->Inward_model->minus_stock_byidmodel_idbranch_idgodown($update_stock);
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
            } else {
                $this->db->trans_complete();
                $this->db->trans_rollback();
                $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
                die($output);
            }
       
    }
    
   
}
