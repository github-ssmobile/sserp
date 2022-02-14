<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_allocation extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        $this->load->model("Allocation_model");
        $this->load->model("Stock_model");
        $this->load->model("General_model");
         $this->load->model("common_model");
        date_default_timezone_set('Asia/Kolkata');
    }
    
    public function model_allocation()
    {   $user_id=$this->session->userdata('id_users');        
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
//            $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);
            $q['product_category'] = $this->General_model->get_product_category_data();   
            $q['brand_data'] = $this->General_model->get_active_brand_data();            
            $q['active_godown'] = $this->General_model->get_allowed_for_allocation_godowns();
            $this->load->view('allocation/stock_allocation', $q);
        }else{
            redirect('Stock_allocation/404');
        }
    }
    public function route_allocation()
    {   $user_id=$this->session->userdata('id_users');
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');   
        $warehouse=$this->session->userdata('idbranch');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){    
             if($level==1){                 
                $q['product_category'] = $this->General_model->get_product_category_data();
                $q['brand_data'] = $this->General_model->get_active_brand_data();
                
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
                $q['brand_data'] = $this->General_model->get_active_brand_data();
                 $q['product_category'] = $this->General_model->get_product_category_data();                                   
             }
            $q['active_godown'] = $this->General_model->get_allowed_for_allocation_godowns();
            $this->load->view('allocation/route_allocation', $q);
        }else{
            redirect('Stock_allocation/404');
        }
    }
    public function branch_allocation()
    {   $user_id=$this->session->userdata('id_users');
        $q['idwarehouse']=$this->session->userdata('idbranch');
        $_SESSION['variant']=array();
        $q['tab_active'] = '';  
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){                        
                          
            $q['warehouse'] = $this->General_model->get_active_warehouse_data();        
            
             if($level==1){
                 $q['brand_data'] = $this->General_model->get_active_brand_data();      
                $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($q['idwarehouse']);             
             }else{
                 $q['brand_data'] = $this->General_model->get_brands_by_user($user_id);      
                 $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);             
                 if(count($q['branch_data'])>0){}else{
                     $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($q['idwarehouse']);                          
                 }
             }        
            $q['active_godown'] = $this->General_model->get_allowed_for_allocation_godowns();
            $this->load->view('allocation/branch_allocation', $q);
        }else{
            redirect('Stock_allocation/404');
        }
    }     
    public function ajax_models_by_brands() {
        $product_category = $this->input->post('product_category');
        $brand = $this->input->post('brand');        
        $model_data = $this->General_model->get_active_model_by_brand_product_category($product_category,$brand);
        echo '<select class="chosen-select form-control" name="model" id="model" required=""><option value="">Select Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_model .'">'.$model->model_name.'</option>';
        }
    }
    public function ajax_variants_by_brand() {          
        $product_category = $this->input->post('product_category');        
        $brand = $this->input->post('brand');        
        $model_data = $this->General_model->get_active_variants_by_brand_product_category($product_category,$brand);
        echo '<select class="chosen-select form-control" name="model" id="model" required=""><option value="">Select Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
    }
    public function ajax_branch_by_warehouse() {        
        $warehouse = $this->input->post('warehouse');        
        $branch_data = $this->General_model->get_active_branch_data_warehouse($warehouse);
        echo '<select class="chosen-select form-control input-sm" name="branch" id="branch">
            <option value="0">Select Branch</option>';
            foreach ($branch_data as $branch) { 
                echo '<option value="'.$branch->id_branch.'">'.$branch->branch_name.'</option>';
            } 
        echo '</select>
                <div class="chosen-container chosen-container-single branch_lable" style="display:none">
                </div>';        
    }
    public function ajax_model_variants(){
            $modelid = $this->input->post('model'); 
            $days = $this->input->post('days'); 
            $model_data = $this->Allocation_model->get_active_variants_by_model($modelid,$days);
             ?>        
                <thead class="fixedelement" style="text-align: center;position: none !important;">
                <th>Sr</th>                    
                <th>Brand</th>            
                <th>Model</th>
                <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
                <th style="text-align: center;">Action</th>            
                </thead>
                <tbody class="data_1">
                <?php $i = 1;
                foreach ($model_data as $model) { ?>                
                        <tr>
                            <td><?php echo $i; ?></td>                    
                            <td><?php echo $model->brand_name; ?></td>
                            <td><?php echo $model->full_name; ?></td>
                            <td><?php echo $model->sale_qty; ?></td>
                            <td style="text-align: center;">                                
                                <input type="hidden" name="idmodel" value="<?php echo $model->idmodel; ?>" />            
                                <input type="hidden" name="idvariant" value="<?php echo $model->id_variant; ?>" />
                                <input type="hidden" name="idcategory" value="<?php echo $model->idcategory; ?>" />
                                <input type="hidden" name="idbrand" value="<?php echo $model->idbrand; ?>" />
                                <input type="hidden" name="idskutype" value="<?php echo $model->idsku_type; ?>" />
                                <input type="hidden" name="idproductcategory" value="<?php echo $model->idproductcategory; ?>" />
                                <a class="btn btn-outline-info select-variant" style="padding: 3px 6px !important;">Select</a>
                            </td>  
                        </tr>
                    <?php $i++;
                } ?>
                </tbody>    
            <?php
    }
    
    public function ajax_model_variants_allocation_data(){
//        die(print_r($_POST));
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
        $godown_to = $idgodown;
        if($idgodown==1){
            $godown_to = 6;
        }else if($idgodown==6){
            $godown_to = 1;
        }
        if($idproductcategory==1){            
            if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                $variants = $this->Allocation_model->get_same_variants_for_allocation($variantid,$warehouse,$idproductcategory,$idbrand,$idgodown,$allocation_type,$modelid,1,$godown_to);
            }else{
                $variants = $this->Allocation_model->get_same_variants_for_allocation($variantid,$warehouse,$idproductcategory,$idbrand,$idgodown,$allocation_type,$modelid,0,$godown_to);
            }   
        }else{
            $variants = $this->Allocation_model->get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type,$godown_to);   
        }     
//        die('<pre>'.print_r($variants,1).'</pre>');
        /// Temporary to show all branches to gandhinager warehouse(7) for model allocation ///
        $id_warehouse=$warehouse;
        if($warehouse==7){ // || $warehouse==18
            $branch_data = $this->Allocation_model->get_active_branchs_forallocation($idbrand);  
            
        }else{
            $id_warehouse=$warehouse;
            $branch_data = $this->Allocation_model->get_branches_by_warehouseid_forallocation($warehouse,$idbrand);                               
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
        <th  colspan='4'><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" />   </th>    
        <?php
        $idbranch=0; //All branch
        foreach ($variants as $variant){                    
            $stock_qty=$variant->ho_stock_qty;
		$model_data[] = $this->Allocation_model->get_variants_allocation_data($id_warehouse,$idbranch,$days,$variant->id_variant,$idgodown,$allocation_type,$idbrand);
            ?>
                <th colspan='3' style="text-align: center; height: 150px; "><input type="hidden" name="variants[]" value="<?php echo $variant->id_variant; ?>" /><?php echo $variant->full_name;?>
                    <?php   $ho_stock_qty=$variant->ho_stock_qty;
                    $allocated_qty=$variant->allocated_qty;
                    $o_allocated_qty=$variant->o_allocated_qty;
                    $n_allocated_qty=($allocated_qty-$variant->o_allocated_qty);
                    $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                ?>
                    
                    <br><?php echo "New Allocated - ".(($n_allocated_qty==null)?0:$n_allocated_qty); ?>&nbsp;&nbsp;
                    <?php echo "Online Allocated - ".(($o_allocated_qty==null)?0:$o_allocated_qty); ?>&nbsp;&nbsp;<br>
                    <?php echo "Warehouse - ".(($ho_stock_qty==null)?0:$ho_stock_qty); ?>&nbsp;&nbsp;                    
                    <?php echo "Available - ".(($available==null)?0:$available); ?>&nbsp;&nbsp;
                
                </th>
        <?php }         
        ?>
        </thead>
        <thead class="fixheader1" style="text-align: center;height: 52px;">
        <th style="text-align: center;">Zone</th><th style="text-align: center;">Branch Category</th> <th style="text-align: center;">Branch</th><th style="text-align: center;">Branch Promoter</th>      
        <?php
        foreach ($variants as $variant){ 
            $stock_qty=$variant->ho_stock_qty;
             $full_name = clean($variant->full_name);
            ?>
            <!--<th>Placement Norm</th>-->
            <th style="text-align: center;">Stock</th>
            <th style="text-align: center;">Last <?php echo $this->input->post('days'); ?> days Sale</th>  
            <!--<th>To be allocated</th>-->
            <?php   $ho_stock_qty=$variant->ho_stock_qty;
                    $allocated_qty=$variant->allocated_qty;
                    $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                ?>
            <th style="text-align: center;">Quantity<input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /> </th>            
            
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
                <td class="fixleft" style="background: #ffcccc;"><?php echo $branch->zone_name; ?></td>                    
                <td class="fixleft1" style="background: #ffcccc;"><?php echo $branch->branch_category_name; ?></td>
                <td class="fixleft2" style="background: #ffcccc;"><?php echo $branch->branch_name; ?></td>
                <td class="fixleft2" style="background: #ffcccc;"><?php if($branch->brand_promoter > 0){ echo 'Yes'; }else{ echo 'No'; } ?></td>
                
            <?php    
                
                for($j=0;$j<$counts; $j++){
                   $data=$model_data[$j][$i];   
                   $full_name = clean($data->full_name); 
                   
                   $stock=($data->stock_qty + $data->intra_stock_qty); 
                   if(isset($zsum_pl[$j])){
                        $zsum_pl[$j]=$zsum_pl[$j]+$data->norm_qty; 
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
                        $zsum_pl[$j]=0+$data->norm_qty;   
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
                        $zsum_pl1[$j] =0; $zsum_stl1[$j] =0; $zsum_sl1[$j] =0;
                        
                    }
                    $zsum_pl1[$j] +=$data->norm_qty; $zsum_stl1[$j] +=$stock; $zsum_sl1[$j] +=$data->sale_qty;
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
                <!--<td class="textalign"><?php echo (($data->norm_qty)-$stock) ?></td>-->
                <td><input type="text" zone_name="<?php echo $zonename;?>" class="<?php echo $zonename.$full_name." " ;?><?php echo " ".$full_name; ?> form-control input-sm" branch="<?php echo $branch->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$branch->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                                   
            <?php 
            
                     
            
                } ?>                
            </tr>                
       <?php $i++; $old_name=$branch->zone_name; } $oldname=clean($old_name); ?>
           <tr class="" style="background-color: #fffae1;position: unset !important;">
                    <td></td>
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
                    $html.='<td></td><td></td><td></td><td><b>Over All Total</b></td>';                                                        
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
    public function save_stock_allocation() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   
        $allocation_type=$this->input->post('allocation_type');
        $timestamp = time();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $iduser=$this->session->userdata('id_users');
        $idwarehouse =$this->session->userdata('idbranch');
        $variants=$this->input->post('variants');         
        $idgodown=$this->input->post('idgodown');   
        $idmodel =$this->input->post('idmodel');                            
        $idskutype =$this->input->post('idskutype');        
        $idproductcategory  =$this->input->post('idproductcategory');
        $idcategory =$this->input->post('idcategory');                            
        $idbrand =$this->input->post('idbrand');
        $qty=$this->input->post('qty');         
        if (isset($qty) && count($qty) > 0) {
            $data_att = array();
            $vatriant = array();
            $i = 0;            
            $delete = $this->Allocation_model->delete_branch_stock_allocation_by_variant($idwarehouse,$variants,$idgodown,$allocation_type);
            foreach ($qty as $branch=>$model_array) {  
                $result = $this->Allocation_model->get_branch_allocation($idwarehouse,$branch,0,$allocation_type);                
                    if($result){
                        $id_stock_allocation=$result->id_stock_allocation;
                    }else{
                            $array = array(
                            'allocation_type' => $allocation_type,
                            'idbranch' => $branch,
                            'idwarehouse' => $idwarehouse,
                            'date' => $date,
                            'timestamp' =>$timestamp,
                            'allocate_by' => $iduser,
                            'entry_time' => $datetime                            
                        );
                        $id_stock_allocation = $this->Allocation_model->save_branch_stock_allocation($array);
                    }
                    
                    foreach ($model_array as $id_vatriant=>$qtyy) {  
                        $data_att[] = array(
                            'idstock_allocation' => $id_stock_allocation,
                            'idbranch' => $branch,
                            'qty' => $qtyy,
                            'idvariant' => $id_vatriant,
                            'idmodel' => $idmodel,                            
                            'idskutype' => $idskutype,
                            'idgodown' => $idgodown,
                            'idproductcategory ' => $idproductcategory,
                            'idcategory' => $idcategory,                            
                            'idbrand' => $idbrand,
                            'date' => $date,
                            'entry_time' => $datetime,
                            'created_by' => $iduser
                        );
                    }                
            }             
            if (count($data_att) > 0) {
                $result = $this->Allocation_model->save_db_branch_allocation($data_att);                
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
    public function allocated_stock()     
    {      
        $q['tab_active'] = '';
        $iduser=$this->session->userdata('id_users');  
        $idwarehouse =$this->session->userdata('idbranch');
        $q['stock_allocation'] = $this->Allocation_model->get_stock_allocation_by_status($iduser,0,$idwarehouse); // open mandates
        $this->load->view('allocation/open_allocations', $q);
    }
     
    public function confirm_allocated_stock()     {              
        $q['tab_active'] = '';         
        $user_id=$this->session->userdata('id_users');  
        $idwarehouse =$this->session->userdata('idbranch');
        $q['stock_allocation'] = $this->Allocation_model->get_stock_allocation_by_status_idbranch_date('0','','','',$idwarehouse); 
        $this->load->view('allocation/confirm_allocation', $q);
    }
    
    public function edit_confirmed_allocated_stock()     {              
        $q['tab_active'] = '';        
        $user_id=$this->session->userdata('id_users');
        $idwarehouse =$this->session->userdata('idbranch');
        $q['stock_allocation'] = $this->Allocation_model->get_stock_allocation_by_idwarehouse($idwarehouse); 
        $this->load->view('allocation/edit_confirm_allocation', $q);
    }
    
    public function confirmed_allocated_stock()     {              
        $q['tab_active'] = '';        
        $user_id=$this->session->userdata('id_users');
        $idwarehouse =$this->session->userdata('idbranch');
        $q['stock_allocation'] = $this->Allocation_model->get_stock_allocation_by_status_idbranch_date('1','','','',$idwarehouse);
        $this->load->view('allocation/confirmed_allocation', $q);
    }    
    public function ready_to_outward(){
        $q['tab_active'] = ''; 
        $user_id=$this->session->userdata('id_users');
        $idwarehouse =$this->session->userdata('idbranch');
        $q['branch_data'] = $this->General_model->get_active_branch_data_warehouse($idwarehouse);   
        $q['stock_allocation'] = $this->Allocation_model->get_stock_allocation_by_status_idbranch_date('2','','','',$idwarehouse);
        $this->load->view('allocation/ready_to_scan', $q);
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
        $model_data = $this->Allocation_model->get_route_allocation_data($idproductcategory,$idbrand,$days,$idgodown,$allocation_type,$idroute,$warehouse);
        ?>            
        <thead class="fixedelementtop" style="text-align: center;position: none !important;">               
            <th colspan='4' style=" position: sticky;top:0"><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" />
                <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>" />
                <input type="hidden" name="idproductcategory" value="<?php echo $idproductcategory; ?>" />
            </th>    
            <?php
            foreach ($branches as $branch){ ?>            
                <th colspan='4' ><input type="hidden" name="idbranch[]" value="<?php echo $branch->id_branch; ?>" /><?php echo $branch->branch_name;?></th>
            <?php }             ?>
        </thead>
        <thead class="fixedelement1" style="text-align: center;position: none !important;">
        <th>Model Name</th><th>Warehouse Qty</th> <th>Allocated Qty</th><th>Available Qty</th>   
            <?php
            foreach ($branches as $branch){ ?>
                <th>Placement Norm</th>
                <th>Stock</th>
                <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
                <!--<th>To be allocated</th>-->
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
                    <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                    <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                    <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                    <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />
                    
                </td>
                <td class="fixleft1" style="background: #ffcccc;"><?php echo $ho_stock_qty ?></td>
                <td class="fixleft2" style="background: #ffcccc;"><?php echo $allocated_qty ?></td>
                <td class="fixleft3" style="background: #caffca;"><?php echo $available ?> <input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available<=0)?0:$available); ?>" /></td>
                <td class="textalign"><?php echo $data->norm_qty ?></td>
                <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
                <td class="textalign"><?php echo $data->sale_qty ?></td>  
                <!--<td><?php //echo (($data->norm_qty)-(($data->stock_qty)+($data->intra_stock_qty))) ?></td>-->
                <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $data->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$data->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                       
            
        <?php }else if($old==$data->id_variant){ ?>               
            <td  class="textalign"><?php echo $data->norm_qty ?></td>
            <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
            <td class="textalign"><?php echo $data->sale_qty ?></td>  
            <!--<td><?php //echo (($data->norm_qty)-(($data->stock_qty)+($data->intra_stock_qty))) ?></td>-->
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
                <td class="textalign"><?php echo $data->norm_qty ?></td>
            <td class="textalign"><?php echo (($data->stock_qty)+($data->intra_stock_qty)) ?></td>
            <td class="textalign"><?php echo $data->sale_qty ?></td>  
            <!--<td><?php //echo (($data->norm_qty)-(($data->stock_qty)+($data->intra_stock_qty))) ?></td>-->
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
    public function save_route_allocation() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   
        $allocation_type=$this->input->post('allocation_type');
        $idbranchs=$this->input->post('idbranch');
        $idgodown=$this->input->post('idgodown');
        $idproductcategory=$this->input->post('idproductcategory');
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
            $data_att = array();
            $i = 0;            
            $delete = $this->Allocation_model->delete_branch_route_allocation_by_variant($idwarehouse,$variants,$idbranchs,$idgodown,$allocation_type);
            foreach ($qty as $branch=>$model_array) {  
                $result = $this->Allocation_model->get_branch_allocation($idwarehouse,$branch,0,$allocation_type);                
                    if($result){
                        $id_stock_allocation=$result->id_stock_allocation;
                    }else{
                            $array = array(
                            'allocation_type' => $allocation_type,
                            'idbranch' => $branch,
                            'idwarehouse' => $idwarehouse,
                            'date' => $date,
                            'timestamp' =>$timestamp,
                            'allocate_by' => $iduser,
                            'entry_time' => $datetime                            
                        );
                        $id_stock_allocation = $this->Allocation_model->save_branch_stock_allocation($array);
                    }
                    
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
                    }                
            }             
            if (count($data_att) > 0) {
                $result = $this->Allocation_model->save_db_branch_allocation($data_att);                
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
    public function ajax_get_branch_allocation_header(){
        $user_id=$this->session->userdata('id_users');
        $warehouse =$this->session->userdata('idbranch');
        $warehousedata=$this->General_model->get_branch_byid($warehouse);
        $warehouse_name = $warehousedata->branch_name;         
        $branch = $this->input->post('branch'); 
        $idbranch = $this->input->post('idbranch'); 
        $days = $this->input->post('days'); 
        ?>
        <thead class="fixedelementtop" style="text-align: center;position: none !important;">
            <th colspan="6" class="textalign"><?php echo $warehouse_name;?></th>
            <th colspan="7"  class="textalign"><?php echo $branch;?></th>
        </thead>
        <thead class="fixedelement1" style="text-align: center;position: none !important;">                        
        <th>Model  Name</th>
        <th>Godown</th>
        <th>Warehouse Qty</th> 
        <th>New Allocated Qty</th>
        <th>Online Allocated Qty</th>
        <th>Available Qty</th>                                                   
        <th>Stock</th>
        <th>Sale Days</th>  
        <th>Last Sale</th>               
        <th>Quantity</th>            
        <th>Delete</th>
        </thead>   <?php
            $allocation_type=0;
            $warehouse_data = $this->Allocation_model->get_branch_allocation_stock_data($idbranch,$days,$warehouse,$allocation_type);
            
            ?> 
                <tbody class="data_1">
                <?php $i = 1;
                foreach ($warehouse_data as $data) {                     
                    array_push($_SESSION['variant'], $data->id_variant.'-'.$data->id_godown);
                    
                        ?>                
                        <tr>                  
                            <?php
                            $idgodown=$data->id_godown;
                             if($idgodown==1 || $idgodown==6){
                                $full_name = clean($data->full_name);
                            }else{
                                $full_name = clean($data->full_name.$data->godown_name);
                            }   
                            $n_allocated_qty = 0;
                            $o_allocated_qty = 0;
                            $ho_stock_qty = $data->ho_stock_qty;
                            $allocated_qty = $data->allocated_qty;
                            $stock=(($data->stock_qty)+($data->intra_stock_qty));
                            $idgodown_to=$data->id_godown;
                            if($data->id_godown==1){
                                $idgodown_to=6;
                                $n_allocated_qty = $data->allocated_qty;
                            }else if($data->id_godown==6){
                                $idgodown_to=1;
                                $o_allocated_qty = $data->allocated_qty;
                            }
                        $result=$this->Allocation_model->get_branch_allocation_stock_data_newonline($idbranch,$warehouse,$allocation_type,$data->id_variant,$data->id_godown,$idgodown_to);                        
                        $dataa=$result[0];
                        $total_allocated=$dataa->allocated_qty;    
                        if($data->id_godown==1){
                                $o_allocated_qty=$total_allocated-$n_allocated_qty;
                            }else if($data->id_godown==6){                                
                                $n_allocated_qty=$total_allocated-$o_allocated_qty;
                            }
                            $ho_stock_qty = $dataa->ho_stock_qty;
                        $available = ($ho_stock_qty - (($o_allocated_qty+$n_allocated_qty) - $data->callocated_qty));
                            ?>
                            <td><?php echo $data->full_name; ?>
                                <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                                <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                                <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                                <input type="hidden" name="idproductcategory[]" value="<?php echo $data->idproductcategory; ?>" />
                                <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />  
                                <input type="hidden" name="idgodown[]" value="<?php echo $data->id_godown; ?>" />  
                                <input type="hidden" name="idbrand[]" value="<?php echo $data->idbrand; ?>" />  
                            </td>
                            <td><?php echo $data->godown_name; ?></td>
                           
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
                                         var warehouse_ty = +$("input[name=warehouse<?php echo $full_name; ?>]").val();
                                         var w_ty = +$("input[name=<?php echo $full_name; ?>]").val();   
                                         var t_allocated_ty = +$("input[name=t_allocated<?php echo $full_name; ?>]").val();   
                                         var c_allocated_ty = +$("input[name=c_all<?php echo $full_name; ?>]").val();   
                                         var f=parseInt((warehouse_ty-t_allocated_ty)+c_allocated_ty) ;
                                         if(total_sum > warehouse_ty || total_sum > f){
                                            alert("Sorry, You dont have enough quantity!!"); 
                                            $(this).val("0");
                                            $(this).removeAttr('style');                                            

                                         }else{                                              
                                              $(this).attr('style',"background: #caffca;");
                                         }
                                     }else{
                                         $(this).removeAttr('style');

                                     }
                                    });
                                });            
                            </script>
                            
                            <td><?php echo $ho_stock_qty; ?><input type="hidden" name="warehouse<?php echo $full_name; ?>" value="<?php echo (($ho_stock_qty==null)?0:$ho_stock_qty); ?>" /></td>
                            <td><?php echo $n_allocated_qty; ?><input type="hidden" name="t_allocated<?php echo $full_name; ?>" value="<?php echo (($total_allocated==null)?0:$total_allocated); ?>" /></td>    
                            <td><?php echo $o_allocated_qty; ?><input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /></td>    
                            <td><?php echo $available; ?><input type="hidden" name="c_all<?php echo $full_name; ?>" value="<?php echo (($data->callocated_qty==null)?0:$data->callocated_qty); ?>" /></td>                            
                            <td><?php echo $stock; ?></td>
                            <td><?php echo $days; ?></td>
                            <td><?php echo $data->sale_qty; ?></td>                            
                            <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $idbranch; ?>"  variant="<?php echo $data->id_variant ?>" name="qty[]" <?php if($data->callocated_qty!=null){ echo 'style="background: #caffca;"' ;} ?> value="<?php echo $data->callocated_qty; ?>" /></td>                                                               
                            <td><a href="#" class="thumbnail textalign delete_row" variant="<?php echo $data->id_variant ?>" godown="<?php echo $data->id_godown ?>" style="margin: 0 8px;padding: 5px !important;"><i class="fa fa-trash-o" style="color:red;"></i></a></td>    
                        </tr>
                    <?php $i++;
                } ?>
                </tbody>    
            <?php        
        ?>
        
        
    <?php    
    } 
    public function ajax_variants_by_id(){
            $variantid = $this->input->post('variant_id'); 
            $days = $this->input->post('days'); 
            $idgodown = $this->input->post('idgodown'); 
            $idbrand = $this->input->post('brand');
            $idbranch = $this->input->post('idbranch');
            $user_id=$this->session->userdata('id_users');
            $godown_name = $this->input->post('godown_name');    
            $o_warehouse = $this->input->post('warehouse'); 
            $allocation_type=0;
            $modelid=0;
            $idproductcategory=0;
            $godown_to=$idgodown;
            if($idgodown==1){
                $godown_to=6;
            }
            else if($idgodown==6){
                $godown_to=1;
            }
            if(in_array($variantid.'-'.$idgodown,$_SESSION['variant'])){?>
                <exist>
             <?php }else{
                array_push($_SESSION['variant'], $variantid.'-'.$idgodown);
            $warehouse =$this->session->userdata('idbranch');
            $warehouse_data = $this->Allocation_model->get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type,$godown_to);
            $model_data = $this->Allocation_model->get_variants_allocation_data($o_warehouse,$idbranch,$days,$variantid,$idgodown,$allocation_type);
            
            ?> 
                <tbody class="data_1">
                <?php $i = 1;
                foreach ($warehouse_data as $data) { ?>                
                        <tr>                  
                            <?php
                            if($idgodown==1 || $idgodown==6){
                                $full_name = clean($data->full_name);
                            }else{
                                $full_name = clean($data->full_name.$godown_name);
                            }
                            $ho_stock_qty = $data->ho_stock_qty;
                            $allocated_qty = $data->allocated_qty;
                            $o_allocated_qty = $data->o_allocated_qty;
                            $n_allocated_qty = ($data->allocated_qty-$o_allocated_qty);
                            $available = ($ho_stock_qty - ($allocated_qty));
                            $stock=(($model_data[0]->stock_qty)+($model_data[0]->intra_stock_qty));
                            
                            ?>
                            <td><?php echo $data->full_name; ?>
                                <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                                <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                                <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                                <input type="hidden" name="idproductcategory[]" value="<?php echo $data->idproductcategory; ?>" />
                                <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />  
                                <input type="hidden" name="idgodown[]" value="<?php echo $idgodown; ?>" />  
                                <input type="hidden" name="idbrand[]" value="<?php echo $idbrand; ?>" />  
                            </td>
                            <td><?php echo $godown_name; ?></td>
                           
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
                                         var warehouse_ty = +$("input[name=warehouse<?php echo $full_name; ?>]").val();
                                         var w_ty = +$("input[name=<?php echo $full_name; ?>]").val();   
                                         var t_allocated_ty = +$("input[name=t_allocated<?php echo $full_name; ?>]").val();   
                                         var c_allocated_ty = +$("input[name=c_all<?php echo $full_name; ?>]").val();   
                                         var f=parseInt((warehouse_ty-t_allocated_ty)+c_allocated_ty) ;
                                         if(total_sum > warehouse_ty || total_sum > f){
                                            alert("Sorry, You dont have enough quantity!!"); 
                                            $(this).val("0");
                                            $(this).removeAttr('style');                                            

                                         }else{                                              
                                              $(this).attr('style',"background: #caffca;");
                                         }
                                     }else{
                                         $(this).removeAttr('style');

                                     }
                                    });
                                });            
                            </script>
                            <td><?php echo $ho_stock_qty; ?><input type="hidden" name="warehouse<?php echo $full_name; ?>" value="<?php echo (($ho_stock_qty==null)?0:$ho_stock_qty); ?>" /></td>
                            <td><?php echo $n_allocated_qty; ?><input type="hidden" name="t_allocated<?php echo $full_name; ?>" value="<?php echo (($allocated_qty==null)?0:$allocated_qty); ?>" /></td>                                                        
                            <td><?php echo $o_allocated_qty; ?></td>                                                        
                            <td><?php echo $available; ?><input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /></td>
                            <td><?php echo $stock; ?></td>
                            <td><?php echo $days; ?></td>
                            <td><?php echo $model_data[0]->sale_qty; ?></td>
                            <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $idbranch; ?>"  variant="<?php echo $data->id_variant ?>" name="qty[]" <?php if(isset($model_data[0]->allocated_qty)){ echo 'style="background: #caffca;" value="'.$model_data[0]->allocated_qty; } ?> /></td>                                                               
                            <td><a href="#" class="thumbnail textalign delete_row" variant="<?php echo $data->id_variant ?>" godown="<?php echo $idgodown ?>" style="margin: 0 8px;padding: 5px !important;"><i class="fa fa-trash-o" style="color:red;"></i></a></td>    
                        </tr>
                    <?php $i++;
                } ?>
                </tbody>    
            <?php
        }
    }
    public function remove_variant(){
        $variant = $this->input->post('variant');   
        $idgodown = $this->input->post('idgodown');   
        $val=$variant.'-'.$idgodown;      
        if (($key = array_search($val, $_SESSION['variant'])) !== false) {            
            unset($_SESSION['variant'][$key]);            
            $output = json_encode(array("result" => "true", "data" => "success", "message" => ""));
            die($output);  
        }else{
            $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
            die($output);            
        }        
    }        
    public function save_branch_allocation() {        
//        die('<pre>'.print_r($_POST,1).'</pre>');   
        $allocation_type=$this->input->post('allocation_type');
        $idbranch=$this->input->post('idbranch');        
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
        $idwarehouse =$this->session->userdata('idbranch');
        
    if (isset($_POST['qty']) && count($this->input->post('qty')) > 0) {            
            $data_att = array();
            $i = 0;            
            $delete = $this->Allocation_model->delete_branch_allocation_by_variant($idwarehouse,$idbranch,$allocation_type);
            $result = $this->Allocation_model->get_branch_allocation($idwarehouse,$idbranch,0,$allocation_type);                
                    if($result){
                        $id_stock_allocation=$result->id_stock_allocation;
                    }else{
                            $array = array(
                            'allocation_type' => $allocation_type,
                            'idbranch' => $idbranch,
                            'idwarehouse' => $idwarehouse,
                            'date' => $date,
                            'timestamp' =>$timestamp,
                            'allocate_by' => $iduser,
                            'entry_time' => $datetime                            
                        );
                        $id_stock_allocation = $this->Allocation_model->save_branch_stock_allocation($array);
                    }
                    $key=0;
                    foreach ($variants as $id_vatriant) {  
                        //$key=array_search($id_vatriant, $variants);
                        $data_att[] = array(
                            'idstock_allocation' => $id_stock_allocation,
                            'idbranch' => $idbranch,
                            'qty' => $qty[$key],
                            'idvariant' => $id_vatriant,
                            'idmodel' => $idmodel_s[$key],                            
                            'idskutype' => $idskutype_s[$key],
                            'idgodown' => $idgodown_s[$key],
                            'idproductcategory ' => $idproductcategory_s[$key],
                            'idcategory' => $idcategory_s[$key],                            
                            'idbrand' => $idbrand_s[$key],
                            'date' => $date,
                            'entry_time' => $datetime,
                            'created_by' => $iduser
                        ); 
                        $key++;
                    }             
            if (count($data_att) > 0) {
                $result = $this->Allocation_model->save_db_branch_allocation($data_att);                
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
    public function ajax_get_stock_allocation_data_byid() {
//         die('<pre>'.print_r($_POST,1).'</pre>');   
        $idstock = $this->input->post('idstock');
        $date = $this->input->post('date');
        $entry_time = $this->input->post('entry_time');                
        $user_id=$this->session->userdata('id_users');
        $warehouse =$this->session->userdata('idbranch');
        $warehousedata=$this->General_model->get_branch_byid($warehouse);         
        $warehouse_name = $warehousedata->branch_name;         
        $branch = $this->input->post('bname'); 
        $idbranch = $this->input->post('idbranch'); 
        $allocation_type=$this->input->post('allocation_type');        
        $isconfirm=$this->input->post('isconfirm');        
        $days = 30; 
        $_SESSION['variant']=array();
        ?>
        <form class="allocation_form">
            <input type="hidden" name="allocation_type" value="<?php echo $allocation_type; ?>" />
            <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $idbranch; ?>" />
        <table id="variant_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
        <thead class="fixedelement" style="text-align: center;position: none !important;">
            <th colspan="6" class="textalign"><?php echo $warehouse_name;?></th>
            <th colspan="7"  class="textalign"><?php echo $branch;?></th>
        </thead>
        <thead class="fixedelement" style="text-align: center;position: none !important;">                        
        <th>Model Name</th>
        <th>Godown</th>
        <th>Warehouse Qty</th> 
        <th>New Allocated Qty</th>
        <th>Online Allocated Qty</th>
        <th>Available Qty</th>                                                   
        <th>Stock</th>
        <th>Sale Days</th>  
        <th>Last Sale</th>          
        <th>Quantity</th>
        <th>Allocator</th>
        <th>Delete</th>
        </thead>   <?php    
        if($isconfirm==0){
            $warehouse_data = $this->Allocation_model->get_branch_allocation_stock_data($idbranch,$days,$warehouse,$allocation_type);        
        }else{
            $warehouse_data = $this->Allocation_model->get_branch_allocation_stock_data_manager($idbranch,$days,$warehouse,$allocation_type);
        }            
            ?> 
                <tbody class="data_1">
                <?php $i = 1;
                foreach ($warehouse_data as $data) {                     
                    array_push($_SESSION['variant'], $data->id_variant.'-'.$data->id_godown);                    
                        ?>                
                        <tr>                  
                            <?php
                            $idgodown=$data->id_godown;
                             if($idgodown==1 || $idgodown==6){
                                $full_name = clean($data->full_name);
                            }else{
                                $full_name = clean($data->full_name.$data->godown_name);
                            }                                                    $o_allocated_qty=0;
                            $n_allocated_qty=0;
                            $ho_stock_qty = $data->ho_stock_qty;
                            $allocated_qty = $data->allocated_qty;
                            $stock=(($data->stock_qty)+($data->intra_stock_qty));
                            $idgodown_to=$data->id_godown;
                            if($data->id_godown==1){
                                $idgodown_to=6;
                                $n_allocated_qty = $data->allocated_qty;
                            }else if($data->id_godown==6){
                                $idgodown_to=1;
                                $o_allocated_qty = $data->allocated_qty;
                            }
                        $result=$this->Allocation_model->get_branch_allocation_stock_data_newonline($idbranch,$warehouse,$allocation_type,$data->id_variant,$data->id_godown,$idgodown_to);                        
                        $dataa=$result[0];
                        $total_allocated=$dataa->allocated_qty;    
                        if($data->id_godown==1){
                                $o_allocated_qty=$total_allocated-$n_allocated_qty;
                            }else if($data->id_godown==6){                                
                                $n_allocated_qty=$total_allocated-$o_allocated_qty;
                            }
                            $ho_stock_qty = $dataa->ho_stock_qty;
                        $available = ($ho_stock_qty - (($o_allocated_qty+$n_allocated_qty) - $data->callocated_qty));
                          
                        ?>
                            <td><?php echo $data->full_name; ?>
                                <input type="hidden" name="variants[]" value="<?php echo $data->id_variant; ?>" />
                                <input type="hidden" name="idmodel[]" value="<?php echo $data->idmodel; ?>" />
                                <input type="hidden" name="idcategory[]" value="<?php echo $data->idcategory; ?>" />
                                <input type="hidden" name="idproductcategory[]" value="<?php echo $data->idproductcategory; ?>" />
                                <input type="hidden" name="idskutype[]" value="<?php echo $data->idsku_type; ?>" />  
                                <input type="hidden" name="idgodown[]" value="<?php echo $data->id_godown; ?>" />  
                                <input type="hidden" name="idbrand[]" value="<?php echo $data->idbrand; ?>" />  
                            </td>
                            <td><?php echo $data->godown_name; ?></td>
                           
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
                                         var warehouse_ty = +$("input[name=warehouse<?php echo $full_name; ?>]").val();
                                         var w_ty = +$("input[name=<?php echo $full_name; ?>]").val();   
                                         var t_allocated_ty = +$("input[name=t_allocated<?php echo $full_name; ?>]").val();   
                                         var c_allocated_ty = +$("input[name=c_all<?php echo $full_name; ?>]").val();   
                                         var f=parseInt((warehouse_ty-t_allocated_ty)+c_allocated_ty) ;
                                         if(total_sum > warehouse_ty || total_sum > f){
                                            alert("Sorry, You dont have enough quantity!!"); 
                                            $(this).val("0");
                                            $(this).removeAttr('style');                                            

                                         }else{                                              
                                              $(this).attr('style',"background: #caffca;");
                                         }
                                     }else{
                                         $(this).removeAttr('style');

                                     }
                                    });
                                });            
                            </script>
                            
                            <td><?php echo $ho_stock_qty; ?><input type="hidden" name="warehouse<?php echo $full_name; ?>" value="<?php echo (($ho_stock_qty==null)?0:$ho_stock_qty); ?>" /></td>
                            <td><?php echo $n_allocated_qty; ?><input type="hidden" name="t_allocated<?php echo $full_name; ?>" value="<?php echo (($total_allocated==null)?0:$total_allocated); ?>" /></td>    
                            <td><?php echo $o_allocated_qty; ?><input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /></td>    
                            <td><?php echo $available; ?><input type="hidden" name="c_all<?php echo $full_name; ?>" value="<?php echo (($data->callocated_qty==null)?0:$data->callocated_qty); ?>" /></td>    
                            <td><?php echo $stock; ?></td>
                            <td><?php echo $days; ?></td>
                            <td><?php echo $data->sale_qty; ?></td>                            
                            <?php
//                            if($data->id_godown==6){ ?>
                            <!--<td><input type="text" readonly="" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $idbranch; ?>" <?php // if($allocation_type!=0){ echo "readonly=readonly";}?> variant="<?php echo $data->id_variant ?>" name="qty[]" <?php if($data->callocated_qty!=null){ echo 'style="background: #caffca;"' ;} ?> value="<?php echo $data->callocated_qty; ?>" /></td>-->                                                               
                                                            <?php // }else{ ?>
                                <td><input type="text" class="<?php echo $full_name; ?> form-control input-sm" branch="<?php echo $idbranch; ?>" <?php // if($allocation_type!=0){ echo "readonly=readonly";}?> variant="<?php echo $data->id_variant ?>" name="qty[]" <?php if($data->callocated_qty!=null){ echo 'style="background: #caffca;"' ;} ?> value="<?php echo $data->callocated_qty; ?>" /></td>                                                               
                            <?php // } ?>
                            
                            <td><?php echo $data->user_name; ?></td>
                            <?php if($user_id==$data->id_users){ ?>
                            <td><a href="#" class="thumbnail textalign delete_allocation_data" allocation_data_id="<?php echo $data->id_stock_allocation_data; ?>"  style="margin: 0 8px;padding: 5px !important;"><i class="fa fa-trash-o" style="color:red;"></i></a></td>    
                            <?php }else{?>
                            <td>-</td>    
                            <?php } ?>
                        </tr>
                    <?php $i++;
                } ?>
                </tbody>  
            </table>
            <input type="hidden" name="count" value="<?php echo count($warehouse_data) ?>" />
            <input type="hidden" name="idallocation" value="<?php echo $idstock ?>" />
            <br>
            <div class="col-md-9"></div>
            <?php // if($allocation_type==0){  ?>                        
                <div class="col-md-1">
                    <button type="button" class="allocationform btn btn-primary gradient2">Edit</button>
                </div>
            <?php // }?>  
            <?php if(!$isconfirm){  ?>            
            <div class="col-md-2">
                <button type="button" idstock="<?php echo $idstock ?>"  class="allocation_confirm btn btn-primary gradient2">Confirm</button>
            </div>
            <?php }?>  
            <div class="clearfix"></div>
        </div>
        </form>
        <?php 
    }
    
    public function ajax_get_stock_allocation() {
        $idstock = $this->input->post('idstock');
       $stock_allocation = $this->Allocation_model->get_branch_allocation_by_id($idstock,0); 
       
        ?>
        
       <div class="panel" style="margin: 10px;padding: 10px;">
    <div class="" style="padding: 0; margin: 0"><br>
        <div class="col-md-6">
            <span class="text-muted col-md-3">Mandate No.</span>
            <div class="col-md-9" style="font-family: Kurale; color: #0e10aa !important;"><?php echo $stock_allocation[0]->id_stock_allocation ?></div><div class="clearfix"></div>
            <br>
            <span class="text-muted col-md-3">Date</span>
            <div class="col-md-9"><?php echo $stock_allocation[0]->date ?></div>
        </div>
        <div class="col-md-6">
            <span class="text-muted col-md-3">Branch:</span>
            <div class="col-md-9"><?php echo $stock_allocation[0]->branch_name ?></div><div class="clearfix"></div>
            <br>
            <span class="text-muted col-md-3">Entry Time:</span>
            <div class="col-md-9"><?php echo date('d/m/Y h:i:s a', strtotime($stock_allocation[0]->entry_time)); ?></div><div class="clearfix"></div>
        </div><div class="clearfix"></div><br>
    </div>
    <div class="" style="padding: 0; margin: 0">
        <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin: 0">
            <?php             
            if($stock_allocation[0]->status == 0 || $stock_allocation[0]->status == 1){ ?>
            <thead class="bg-info">
                <th>Id</th>
                <th class="col-md-8">Product</th>
                <th>Qty</th>
            </thead>
            <tbody>
                <?php 
                $i=1;
                foreach ($stock_allocation as $product) {                    
                    ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $product->full_name; ?></td>
                    <td><?php echo $product->qty ?></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
            <?php } ?>
        </table>
    </div>
           <br>
            
            <div class="col-md-2 textalign pull-right">
                <button type="button" style="line-height: unset;padding: 6px 12px;" idstock="<?php echo $idstock ?>"  class="allocation_confirm btn btn-primary gradient2">Ready</button>
            </div> 
           <div class="col-md-2 textalign pull-left">
                <a  style="line-height: unset;padding: 6px 12px;" idstock="<?php echo $idstock ?>" href="<?php echo base_url('Stock_allocation/stock_allocation_details/'.$idstock) ?>" target=""  class="btn btn-primary gradient2">Print</a>
           </div> 
            <div class="clearfix"></div>
    </div>
        <?php 
    }
    
    
    public function confirm_allocation(){        
        $idstock_allocation = $this->input->post('allocation_id');  
        if(isset($_POST['branch_id'])){
            $branch_id = $this->input->post('branch_id');                          
        }else{
            $branch_id = array();            
        }
        $status = $this->input->post('status');  
        $datetime = date('Y-m-d H:i:s');
        if($status==1){
            $allodata = array(
                'status' => $status,
                'confirm_time' => $datetime
             );
        }else{
            $allodata = array(
            'status' => $status
        );
        }                
        $confirm = $this->Allocation_model->update_allocation_status($idstock_allocation,$allodata,$branch_id);        
        if ($confirm) {                        
            $output = json_encode(array("data" => "success"));
            die($output);  
        }else{
            $output = json_encode(array("data" => "fail")); 
            die($output);            
        }        
    }
    
    public function delete_branch_allocation(){
        $idstock_allocation = $this->input->post('idstock_allocation');        
        $delete = $this->Allocation_model->delete_branch_allocation_by_allocationid($idstock_allocation);        
        if ($delete) {                        
            $output = json_encode(array("data" => "success"));
            die($output);  
        }else{
            $output = json_encode(array("data" => "fail")); 
            die($output);            
        }        
    }  
    public function remove_allocation_data(){        
        $allocation_data_id = $this->input->post('allocation_data_id');           
        if ($this->Allocation_model->delete_stock_allocation_data($allocation_data_id)) {                                    
            $output = json_encode(array("data" => "success"));
            die($output);  
        }else{
            $output = json_encode(array("data" => "fail"));             
            die($output);            
        }        
    }    
    public function allocation_report(){
        $user_id=$this->session->userdata('id_users');
        $q['tab_active'] = ''; 
        $idwarehouse=$this->session->userdata('idbranch');
        $q['branch_data'] = $this->General_model->get_active_branch_data();   
        $q['stock_allocation'] = array();//$this->Allocation_model->get_stock_allocation_by_status_idbranch_date('','','','',$idwarehouse);
        $this->load->view('allocation/allocation_report', $q);
    }  
    
    public function stock_allocation_details($idstock_allocation)
    {
        $q['tab_active'] = '';
        $q['stock_allocation'] = $this->Allocation_model->get_branch_allocation_by_id($idstock_allocation,0);          
        $q['branch_data']=$this->General_model->get_branch_byids(array($q['stock_allocation'][0]->id_warehouse,$q['stock_allocation'][0]->id_branch));               
        $this->load->view('allocation/stock_allocation_details', $q);
    }    
    public function ajax_get_stock_allocation_by_status() {
        $status = $this->input->post('status');
        $idbranch= $this->input->post('idbranch');
        $datefrom= $this->input->post('datefrom');                
        $dateto= $this->input->post('dateto'); 
        $user_id=$this->session->userdata('id_users');   
        $idwarehouse=$this->session->userdata('idbranch');
        $stock_allocation = $this->Allocation_model->get_stock_allocation_by_status_idbranch_date($status,$idbranch,$datefrom,$dateto,$idwarehouse);
        ?>        
        <table id="variant_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
        <thead class="fixedelement" style="text-align: center;position: none !important;">                        
        <th>Mandate Number</th>
        <th>Branch Name</th>
        <th>Date</th>        
        <th>Products</th>
        <th>Total Quantity</th>
        <th>Allocation Type</th>             
        <?php if($status > 3){ ?>                
        <th>Dispatch Remark</th>        
        <th>Received Remark</th>
        <?php } ?>
        <th>Info</th>
        <th>Print DC</th>
         <?php if($status== 4){ ?>
                    <th>Generate Eway Bill</th>
                    <th>Print</th>
                <?php } ?>
        <th><?php if($_SESSION['idrole']==15){ ?>
            Action
        <?php } ?></th>
        </thead>
                <tbody class="data_1">
                <?php $i = 1;
                foreach ($stock_allocation as $data) {    
                    $ewayinv_data= $this->common_model->getSingleRow('eway_einvoice_data',array('idoutword_no'=>$data->id_outward));                                   
                        ?>                
                        <tr>  
                            <td><?php echo $data->id_stock_allocation; ?> </td>
                            <td><?php echo $data->branch_name; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($data->date)) ?></td>
                            <td><?php echo $data->sum_product ?></td>
                            <td><?php echo $data->sum_qty ?></td>
                            <?php  $allocation_type=''; if($data->allocation_type == 0){ $allocation_type='Branch'; }else if($data->allocation_type == 1){ $allocation_type='Model'; }else{ $allocation_type='Route'; } ?>
                            <td><?php echo $allocation_type; ?></td>     
                            <?php if($status > 3){ ?>                                            
                            <td><?php echo $data->shipment_remark ?></td>
                            <td><?php echo $data->shipment_received_remark ?></td>
                            <?php } ?>
                            <td>
                                <a target="_blank" class="thumbnail textalign" href="<?php echo base_url('Stock_allocation/stock_allocation_details/'.$data->id_stock_allocation) ?>" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-info " style="color: blue"></i>
                            </td>
                             <td>
                                <a target="" class="thumbnail textalign" href="<?php echo base_url('Outward/outward_dc/'.$data->id_stock_allocation) ?>/0" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-print " style="color: blue"></i>
                            </td>
                            <?php if($status== 4){ ?>
                                    <td>
                                        <?php if(empty($ewayinv_data['ewb_no'])){?>
                                            <button type="button" class="btn btn-sm btn-info textalign gen-eway-bill" 
                                            data-id="<?php echo $data->id_stock_allocation;?>" data-branch="<?php echo $data->idbranch;?>" data-warehouse="<?php echo $idwarehouse;?>">Generate Eway Bill
                                        </button>
                                    <?php }else{ ?>
                                     <button type="button" class="btn btn-sm btn-success textalign">Generated
                                     </button>
                                 <?php } ?>

                             </td>
                             <td> 
                                <?php if(!empty($ewayinv_data['ewb_no'])){?>
                                   <a href="<?php echo base_url().'Print-e-way/'.$ewayinv_data['ewb_no'].'/'.$idwarehouse;?>" target="_blank">
                                    <button type="button" class="btn btn-sm btn-success textalign gen-eway-bill"><i class="fa fa-print " style="color: blue"></i>&nbsp; EWAY</button>
                                </a>
                            <?php } if($ewayinv_data['bill_type']=='1'){ ?>                                 
                                <a href="<?php echo base_url().'Print-e-invoice/'.$ewayinv_data['idoutword_no'].'/'.$idwarehouse;?>" target="_blank">
                                    <button type="button" class="btn btn-sm btn-success textalign gen-eway-bill"><i class="fa fa-print " style="color: blue"></i> &nbsp;E-Invoice</button>
                                </a>
                            <?php } ?>

                        </td>
                    <?php } ?>
                        <td>
                            <?php if ($_SESSION['idrole'] == 15 && $data->status < 3 ) { ?>                        
                               <a  class="thumbnail textalign delete_allo"  value="<?php echo $data->id_stock_allocation ?>" style="margin: 0 8px;padding: 5px !important;width: 50%;"><i class="fa fa-trash-o " style="color: red"></i>  </a>                      
                            <?php } ?>
                        </td>
                        </tr>
                    <?php $i++;
                } ?>
                </tbody>  
            </table>               
        <?php 
    }
    
    //************** ONLINE GODOWN *********************//
    public function online_godown_model_allocation()
    {   $user_id=$this->session->userdata('id_users');        
        $q['tab_active'] = '';
        $level=$this->session->userdata('level');
        $menu=$this->uri->segment(1).'/'.$this->uri->segment(2);
        $res=find_menu($_SESSION['menus'], $menu);
        if(count($res)>0){            
//            $q['product_category'] = $this->General_model->get_product_category_by_user($user_id);
            $q['product_category'] = $this->General_model->get_product_category_data();   
            $q['brand_data'] = $this->General_model->get_active_brand_data();            
            $q['active_godown'] = $this->General_model->get_allowed_for_allocation_godowns();
            $this->load->view('allocation/online_stock_allocation', $q);
        }else{
            redirect('Stock_allocation/404');
        }
    }
    public function ajax_online_godown_model_variants_allocation_data(){
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
        
        
        $godown_to = 1;
        
        if($idproductcategory==1){            
            if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33){
                $variants = $this->Allocation_model->get_same_variants_for_allocation($variantid,$warehouse,$idproductcategory,$idbrand,$idgodown,$allocation_type,$modelid,1,$godown_to);
            }else{
                $variants = $this->Allocation_model->get_same_variants_for_allocation($variantid,$warehouse,$idproductcategory,$idbrand,$idgodown,$allocation_type,$modelid,0,$godown_to);
            }   
        }else{
            $variants = $this->Allocation_model->get_warehouse_stock_data($variantid,$modelid,$idproductcategory,$idbrand,$idgodown,$warehouse,$allocation_type,$godown_to);   
        }      
//         die('<pre>'.print_r($variants,1).'</pre>');   
        /// Temporary to show all branches to gandhinager warehouse(7) for model allocation ///
        $id_warehouse=$warehouse;
        if($warehouse==7){
            $branch_data = $this->Allocation_model->get_active_branchs_forallocation($idbrand);  
            
        }else{
            $id_warehouse=$warehouse;
            $branch_data = $this->Allocation_model->get_branches_by_warehouseid_forallocation($warehouse,$idbrand);                               
        }
        // *END* //
        $model_data=array();
        $counts= count($variants);  ?>
                
        <thead class="fixheader" style="text-align: center;height: 68px;">
            <input type="hidden" name="idmodel" value="<?php echo $modelid; ?>" />            
            <input type="hidden" name="idvariant" value="<?php echo $variantid; ?>" />
            <input type="hidden" name="idcategory" value="<?php echo $idcategory; ?>" />
            <input type="hidden" name="idbrand" value="<?php echo $idbrand; ?>" />
            <input type="hidden" name="idskutype" value="<?php echo $idskutype; ?>" />
            <input type="hidden" name="idproductcategory" value="<?php echo $idproductcategory; ?>" />
            <th  colspan='4'><input type="hidden" name="idgodown" value="<?php echo $idgodown; ?>" /></th>    
            <?php $idbranch=0; //All branch
            foreach ($variants as $variant){             
                $stock_qty=$variant->ho_stock_qty;

                //Online Godown Stock Data
                $model_data[] = $this->Allocation_model->get_variants_allocation_data($id_warehouse,$idbranch,$days,$variant->id_variant,$idgodown,$allocation_type,$idbrand); 
                ?>
                <th colspan='4' style="text-align: center;">
                    <input type="hidden" name="variants[]" value="<?php echo $variant->id_variant; ?>" /><?php echo $variant->full_name;?>
                    <?php 
                        $ho_stock_qty=$variant->ho_stock_qty;
                        $allocated_qty=$variant->allocated_qty;
                        $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                    ?>
                    <br><?php echo "Warehouse - ".(($ho_stock_qty==null)?0:$ho_stock_qty); ?>&nbsp;&nbsp;
                    <?php echo "Allocated - ".(($allocated_qty==null)?0:$allocated_qty); ?>&nbsp;&nbsp;
                    <?php echo "Available - ".(($available==null)?0:$available); ?>&nbsp;&nbsp;
                </th>
            <?php } ?>
        </thead>
        <thead class="fixheader1" style="text-align: center;height: 49px;">
            <th>Zone</th>
            <th>Branch Category</th>
            <th>Branch</th>
            <th>Branch Promoter</th>      
            <?php foreach ($variants as $variant){ 
                $stock_qty=$variant->ho_stock_qty;
                $full_name = clean($variant->full_name); ?>
                <th>Placement Norm</th>
                <th>Stock</th>
                <th>Last <?php echo $this->input->post('days'); ?> days Sale</th>  
                <!--<th>To be allocated</th>-->
                <?php
                    $ho_stock_qty=$variant->ho_stock_qty;
                    $allocated_qty=$variant->allocated_qty;
                    $available=($ho_stock_qty-($allocated_qty - ($variant->c_allocated_qty)));                
                ?>
                <th>Quantity<input type="hidden" name="<?php echo $full_name; ?>" value="<?php echo (($available==null)?0:$available); ?>" /></th>            
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
                    $oldname=clean($old_name);   ?>
                    <tr class="fixedelement1" style="position: unset !important;">
                        <td></td>
                        <td></td>     
                        <td></td>     
                        <td><b>Total</b></td>   
                        <?php for($j=0;$j<$counts; $j++){ 
                            $data1=$model_data[$j][$i];   
                            $fullname = clean($data1->full_name);  ?>
                            <td class="textalign"><b><?php echo $zsum_pl[$j]; ?></b></td>                                    
                            <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
                            <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
                        <!--<td></td>-->                    
                            <td class="textalign"><b><lable class="<?php echo $oldname.$fullname; ?>" name="<?php echo $oldname.$fullname; ?>" ><?php echo $zsum_qty[$j] ?></lable></d></td>
                        <?php } ?>
                    </tr>
                <?php $zsum_pl=array();$zsum_stl=array();$zsum_sl=array();$zsum_qty=array();
                } ?>
                <tr>
                    <td class="fixleft" style="background: #ffcccc;"><?php echo $branch->zone_name; ?></td>                    
                    <td class="fixleft1" style="background: #ffcccc;"><?php echo $branch->branch_category_name; ?></td>
                    <td class="fixleft2" style="background: #ffcccc;"><?php echo $branch->branch_name; ?></td>
                    <td class="fixleft2" style="background: #ffcccc;"><?php if($branch->brand_promoter > 0){ echo 'Yes'; }else{ echo 'No'; } ?></td>
                <?php    
                    for($j=0;$j<$counts; $j++){
                        $data=$model_data[$j][$i];   
                        $full_name = clean($data->full_name); 

                        $stock=($data->stock_qty + $data->intra_stock_qty); 
                        if(isset($zsum_pl[$j])){
                            $zsum_pl[$j]=$zsum_pl[$j]+$data->norm_qty; 
                            $zsum_stl[$j]=$zsum_stl[$j]+$stock;
                            $zsum_sl[$j]=$zsum_sl[$j]+$data->sale_qty; 
                            if($data->allocated_qty!=null){
                                $zsum_qty[$j]=$zsum_qty[$j]+$data->allocated_qty;  
                            }else{
                                $zsum_qty[$j]=$zsum_qty[$j]+0;  
                            }
                        }else{
                            $zsum_pl[$j]=0+$data->norm_qty;   
                            $zsum_stl[$j]=0+$stock;
                            $zsum_sl[$j]=0+$data->sale_qty; 
                            if($data->allocated_qty!=null){
                                $zsum_qty[$j]=0+$data->allocated_qty;    
                            }else{
                                $zsum_qty[$j]=0;  
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
                            $zsum_pl1[$j] =0; $zsum_stl1[$j] =0; $zsum_sl1[$j] =0;

                        }
                        $zsum_pl1[$j] +=$data->norm_qty; $zsum_stl1[$j] +=$stock; $zsum_sl1[$j] +=$data->sale_qty;

                        $zonename=clean($branch->zone_name); ?>
                        <td class="textalign"><?php echo $data->norm_qty; ?></td>                    
                        <td class="textalign"><?php echo ($stock); ?></td>
                        <td class="textalign"><?php echo $data->sale_qty; ?></td>
                        <!--<td class="textalign"><?php echo (($data->norm_qty)-$stock) ?></td>-->
                        <td><input type="text" zone_name="<?php echo $zonename;?>" class="<?php echo $zonename.$full_name." " ;?><?php echo " ".$full_name; ?> form-control input-sm" branch="<?php echo $branch->id_branch; ?>" variant="<?php echo $data->id_variant ?>" <?php if($data->allocated_qty!=null){ echo 'style="background: #caffca;" name=qty['.$branch->id_branch.']['.$data->id_variant.']' ;} ?> value="<?php echo $data->allocated_qty; ?>" /></td>                                   
                    <?php } ?>                
                </tr>                
                <?php $i++; $old_name=$branch->zone_name; } $oldname=clean($old_name); ?>
                <tr class="" style="background-color: #fffae1;position: unset !important;">
                    <td></td>
                    <td></td>
                    <td></td>                                    
                    <td><b>Total</b></td>   
                    <?php for($j=0;$j<$counts; $j++){ 
                        $data1=$model_data[$j][($i-1)];   
                        $fullname = clean($data1->full_name);    ?>
                        <td class="textalign"><b><?php echo $zsum_pl[$j]; ?></b></td>                                    
                        <td class="textalign"><b><?php echo $zsum_stl[$j]; ?></b></td>
                        <td class="textalign"><b><?php echo $zsum_sl[$j]; ?></b></td>
                        <!--<td></td>-->
                        <td class="textalign"><b><lable class="<?php echo $oldname.$fullname ?>" name="<?php echo $oldname.$fullname ?>" ><?php echo $zsum_qty[$j] ?></lable></b></td>
                    <?php } ?>
                </tr>
                <tr class="" style="background-color: #fffae1;position: unset !important;">
                <?php $html ="";
                    $html.='<td></td><td></td><td></td><td><b>Over All Total</b></td>';                                                        
                    for($j=0;$j<$counts; $j++){ 
                        $data1=$model_data[$j][($i-1)];   
                        $fullname = clean($data1->full_name);                           
                        $html.='<td class="textalign"><b>'.$zsum_pl1[$j].'</b></td>';                                    
                        $html.='<td class="textalign"><b>'.$zsum_stl1[$j].'</b></td>';
                        $html.='<td class="textalign"><b>'.$zsum_sl1[$j].'</b></td>';                    
                        $html.='<td class="textalign"><lable class="total'.$fullname.'" name="total'.$fullname.'" >'.$zsum_qty1[$j].'</lable></td>';                     
                    }
                    echo $html;
                ?>                
                </tr>
        </tbody>         
        <?php echo $html;
    }       
    
}
