<?php include __DIR__.'../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Model</h3></center>
<?php if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
<?= link_tag("assets_ecom/css/choosen.css") ?>   
<?php $v_arr=array(); ?>
 <style>
     .content{
         width: 50%;
         padding: 5px;
         margin: 0 auto;
     }
     .content span{
         width: 250px;
     }
     .dz-message{
         text-align: center;
         font-size: 28px;
     }
     .dz-progress{display:none;}
     .dz-image-preview {
         padding: 4px;
         margin-bottom: 20px;
         line-height: 1.42857143;
         background-color: #fff;
         border: 1px solid #ddd;
         border-radius: 4px;
         transition: all .2s ease-in-out;
     }

     #my-dropzone {
         border: 1px dotted blue !important;border-radius: 4px;margin-left: 15px;margin-right: 15px;
     }
     #iimage-preview {
    
    height: auto;
    position: relative;
    overflow: hidden;
    background-color: #ffffff;
    color: #ecf0f1;
}
     
     .btn-outline-info {
    color: #17a2b8  !important;
    background-color: transparent  !important;
    background-image: none !important;    
    margin: 0 !important;
    /*box-shadow: none !important;*/
    border: 1px solid #17a2b8 !important;
    line-height: 21px !important;
    padding: 5px 5px !important;
    text-transform: initial  !important;
     }     
	</style>
	
<script>            
    $(document).ready(function(){
   
       
        
         $(document).on('click', '.add-url', function() { 
            
            $('#videos').append(''+
                                '<div class="url">'+
                                '<div class="col-md-2">URL</div>'+
                                '<div class="col-md-8">'+
                                        '<input type="text" class="form-control model" name="video[]" id="video" placeholder="http://"/>'+                            
                                '</div>'+
                                '<div class="col-md-2">'+
                                    '<a class="thumbnail btn-link waves-effect delete-url" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a>'+
                                '</div><div class="clearfix"></div><br></div>')
        
         });
         
        $(document).on('click', '.add-image', function() {  
            
                var name=$(this).attr('val');
                el=$("#"+name).find('.images');
                $(el).append('<div class="image-url"><div class="col-md-3" style="margin: 10px !important;">'+
                        '<div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">'+
                            '<label for="image-upload" id="image-label">Browse</label>'+
                            '<input type="file" name="'+name+'[]" id="file" class="imag" />'+
                            '<img height="200" class="img-view" src="" />'+
                            '<input type="hidden" class="form-control" value="" name="image_path" />'+
                        '</div>'+
                        '<center><a class="thumbnail btn-link waves-effect delete-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>'+
                    '</div></div>');
        
            
        }); 
         $(document).on('click', '.delete-url, .delete-img', function() { 
             
             el=$(this).parent().parent().parent();               
             if($(el).hasClass('old')){                
                if (confirm('Do you want to delete this image??')) {
                  
                     $.ajax({
                            url:"<?php echo base_url() ?>Catalogue/ajax_remove_model_image/0",
                            method:"POST",
                            data:{id : $(el).find('input[name=image_path]').attr('id_model_image'),image_path:$(el).find('input[name=image_path]').val() },
                            success:function(data)
                            { 
                                if(data == '1'){
                                    alert("Image removed!");
                                    $(el).fadeOut();
                                }else{
                                    alert("Fail to remove image!");
                                }
                            }
                        });
                }                
             }else{
                 $(this).parent().parent().fadeOut();
                 $(this).parent().parent().html("");
             }
             
         });
         $(document).on('click', '.delete-var-img', function() { 
             
             el=$(this).parent().parent().parent();               
             if($(el).hasClass('old')){                
                if (confirm('Do you want to delete this image??')) {
                  
                     $.ajax({
                            url:"<?php echo base_url() ?>Catalogue/ajax_remove_model_image/1", 
                            method:"POST",
                            data:{id : $(el).find('input[name=image_path]').attr('id_variant_image'),image_path:$(el).find('input[name=image_path]').val() },
                            success:function(data)
                            { 
                                if(data == '1'){
                                    alert("Image removed!");
                                    $(el).fadeOut();
                                }else{
                                    alert("Fail to remove image!");
                                }
                            }
                        });
                }                
             }else{
                 $(this).parent().parent().fadeOut();
                 $(this).parent().parent().html("");
             }
             
         });
         
         
        function readURL(input) {
           if (input.files && input.files[0]) {
               var reader = new FileReader();
               reader.onload = function (e) {                                                                 
                       $(input).closest('.thumbnail').find('img.img-view').attr('src', e.target.result);
               }
               reader.readAsDataURL(input.files[0]);
           }
       }
        $(document).on('change', '.imag', function() {          
           readURL(this);
       });
         
        $('#product_category').change(function(){
            var product_category = $('#product_category').val();
            var type_name = $('#product_category option:selected').text();
            $("#product_category_name").val(type_name);
           
            $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_category_by_product_category",
                method:"POST",
                data:{product_category : product_category},
                success:function(data)
                { 
                    $("#category").html(data);
                    $("#category").trigger("chosen:updated");
                    $("#first_block").hide();
                    $("#second_block").hide();
                }
            });
        });
        
        $('#subbrand').change(function(){
            var subbrand = $('#subbrand').val();
            var type_name = $('#subbrand option:selected').text();         
            $("#model_name").val("");
            $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_model_by_brand",
                method:"POST",
                data:{brand : subbrand},
                success:function(data)
                { 
                    $("#model1").html(data);
                    $("#model1").trigger("chosen:updated");
                }
            });
        });
        
         $(document).on('change', '#idbrand', function() { 
         $("#brand_name").val($('#idbrand option:selected').text());
         });
         
         $(document).on('change', '#model1', function() { 
            var subbrand = $('#subbrand option:selected').text();            
            $("#model_name").val(subbrand+' '+$('#model1 option:selected').text());
         });
         $(document).on('change', '#model', function() { 
            var name = $(this).val();            
            $("#model_name").val(name);
         });       
        
        $(document).on('change', '#category', function() { 
            var category = +$(this).val();
            var has_sub_brand = $('option:selected', this).attr("has_sub_brand");
            $("#has_sub_brand").val(has_sub_brand);
            if(has_sub_brand == 1){
                $("#first_block").hide();
                $("#second_block").show();
                $('#model1').prop('required', true);
                $('#subbrand').prop('required', true);
                $('#model').removeAttr('required', true);
                $('#model').val('');
            }else{
                $("#first_block").show();
                $("#second_block").hide();
                $('#model').prop('required', true);
                $('#model1').removeAttr('required', true);
//                $('#subbrand').removeAttr('required', true);
                $('#model1').val('');
//                $('#subbrand').val('');
            }      
            $("#attribureblock").show();
            $("#category_name").val($('#category option:selected').text());
             $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_category_attributes_by_id",
                method:"POST",
                data:{category : category},
                success:function(data)
                {                   
                    $("#attribure_block").html(data);  
                }
            });
            
            
        });
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="clearfix"></div>
<div class="col-md-8 col-md-offset-2">
    <?php echo form_open_multipart('Catalogue/save_edit_model/'.$model_data['model']->id_model) ?>    
     
        <article  style="padding: 15px">
            <div class="panel">
                <center><h4><span style="font-size: 28px"></span> Basic Information </h4></center><hr>                    
                <div class="col-md-10 col-md-offset-1"> 
                    <label class="col-md-4">Product Type</label>
                    <div class="col-md-8">                        
                        <span><?php echo $model_data['model']->product_category_name; ?></span>
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">Category</label>
                    <div class="col-md-8">
                        <span><?php echo $model_data['model']->category_name; ?></span>
                    </div>
                </div>    
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">                        
                    <label class="col-md-4">Brand</label>
                    <div class="col-md-8">
                        <span><?php echo $model_data['model']->brand_name; ?></span>
                    </div>
                </div>     
                <div class="clearfix"></div><br>     
                
                <div class="col-md-10 col-md-offset-1">                        
                    <label class="col-md-4">Model Name</label>
                    <div class="col-md-8">
                        <span><?php echo $model_data['model']->model_name; ?></span>
                        <?php if($model_data['model']->subidbrand == 0){ ?> 
                        <input type="hidden" class="form-control model" value="<?php echo $model_data['model']->model_name; ?>" name="model" id="model"/>                        
                        <?php }else{ ?>
                        <input type="hidden" class="form-control" value="<?php echo $model_data['model']->subidbrand; ?>" name="subbrand" id="subbrand" />                        
                        <input type="hidden" class="form-control" value="<?php echo $model_data['model']->subidmodel; ?>" name="model1" id="model1"/>                        
                         <?php } ?>
                    </div>
                </div>
                
                <?php if(false){ ?>                
                <div class="col-md-10 col-md-offset-1" id="first_block" >
                    <label class="col-md-4">Model Name</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control model" value="<?php echo $model_data['model']->model_name; ?>" name="model" id="model" placeholder="Enter Model Name"/> 
                    </div><div class="clearfix"></div>
                </div> 
                 <?php }else if(false){ ?>
                <div id="second_block" > 
                <div class="col-md-10 col-md-offset-1">                    
                        <label class="col-md-4">Mobile Brand</label>
                        <div class="col-md-8">                            
                                <select class=" chosen-select form-control" name="subbrand" id="subbrand">
                                    <option value="">Mobile Brand</option>
                                    <?php foreach ($brand_data as $brand) {
                                        if($brand->id_brand==$model_data['model']->subidbrand){ ?>
                                    <option selected="" value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                                    <?php    }else{ ?>                                        
                                        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                                </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-10 col-md-offset-1">                    
                        <label class="col-md-4">Mobile Model</label>
                        <div class="col-md-8">
                                <select class="chosen-select  form-control" name="model1" id="model1">
                                    <option value="">Select Model</option>                                    
                                    <?php foreach ($model_data['subbrand_models'] as $models) {
                                        if($models->id_model==$model_data['model']->subidmodel){ ?>
                                    <option selected="" value="<?php echo $models->id_model; ?>"><?php echo $models->model_name; ?></option>
                                    <?php    }else{ ?>                                        
                                        <option value="<?php echo $models->id_model; ?>"><?php echo $models->model_name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                                </select>               
                        </div><div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>                 
                    
                </div> 
                <?php } ?>
                         
                <div class="clearfix"></div><br>
                
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">SKU Type</label>
                     <div class="col-md-8">
                    <select class="chosen-select form-control" name="sku_type" id="sku_type" required="">
                            <option value="">Select SKU Type</option>
                            <?php
                            $i = 1;
                            foreach ($sku_type_data as $sku) {
                                if($model_data['model']->sku_type==$sku->sku_type){?>
                                    <option selected="" value="<?php echo $sku->id_sku_type; ?>"><?php echo $sku->sku_type; ?></option>   
                                <?php  }else{
                                    ?>
                                    <option value="<?php echo $sku->id_sku_type; ?>"><?php echo $sku->sku_type; ?></option>
                                <?php }
                            }
                             ?>
                    </select>
                   
                    </div>                        
                </div>
                <div class="clearfix"></div><br>
                 <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">Description</label>
                </div>    
                <div class="clearfix"></div> <br>
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-12">
                        <?php echo $this->ckeditor->editor("description",$model_data['model']->description);?>      
                    </div>
                        <!--<textarea type="text" class="form-control" name="description" id="description" placeholder="Enter model description"></textarea>-->
                </div> 
                
                <div class="clearfix"></div>
                <?php if(count($model_data['variant_attribute']) == 0){ ?>
                <br>
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">Status</label>
                     <div class="col-md-8">
                        <div class="material-switch">
                            <?php $checked="";                            
                            if($model_data['model_variants'][0]->active==1){
                                $checked="checked";
                            }?>
                            <input type='hidden' value='0' name='variant[<?php echo $model_data['model_variants'][0]->id_variant ?>]'>
                            <input id="active<?php echo $model_data['model_variants'][0]->id_variant ?>" name="variant[<?php echo $model_data['model_variants'][0]->id_variant ?>]"  type="checkbox" <?php echo $checked ?> />
                            <label for="active<?php echo $model_data['model_variants'][0]->id_variant ?>" class="label-primary"></label>
                        </div>
                    </div>                        
                </div>               
                <div class="clearfix"></div>
                <?php } ?>
            </div>

             <?php 
             $count = count($model_data['variant_attribute']);
             if($count > 0){ ?>
            
            <div class="clearfix"></div>
            
            <div id="variantblock" class="panel" >
                <div>
                    <div id="variant_block">
                        <h4 class="col-md-12 text-center">Variants</h4><div class="clearfix"></div><hr style="margin-top: 10px !important;margin-bottom: 10px !important;">
                    <?php $dataa="";
                    
                    if($count > 0){                    
                        $th="<tr>";
                        $columns=array();
                        foreach ($model_data['variant_attribute'] as $attri) {   
                            $dataa.='<div class="col-md-4" style="padding: 5px;">';
                            $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
                            $dataa.='<input type="hidden" name="variant_names[]" value="'.$na_me.'"/>';                            
                            $name = $attri->id_category_attribute.'_'.$attri->idattributetype.'_'.$attri->idattribute.'_'.$na_me;            
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
                                }else{
                                    $dataa.='<div class="col-md-12">';
                                    $dataa.='<input type="text" name="'.$name.'_" id="'.$name.'" class="form-control model1"  placeholder="Enter '.strtolower($attri->attribute_name).'" />';    //
                                    $dataa.='</div>';                        
                                }
                            $dataa.='</div>';
                            $columns[] = preg_replace('/\s+/', '', strtolower($attri->attribute_name));  
                            $th.="<th>".$attri->attribute_name."</th>";                            
                        }
                        $th.="<th>Status</th>";
                        $th.="</tr>";
                        $dataa.='<div class="clearfix"></div><br>';
                    }
                    
                  echo $dataa;  
                  
                    ?>        
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">                    
                    <a class="btn btn-outline-info waves-effect add-variant pull-right" style="padding: 5px 13px !important;text-transform: inherit;">Create Variant</a>
                    
                </div>
                <div class="clearfix"></div><hr>
                <div id="variant" class="col-md-10 col-md-offset-1">
                    <table  class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                        <thead id="variant_names">
                            <?php
                                 echo $th;
                             ?>
                        </thead> 
                        <tbody id="variant_data">
                            <?php
                            $var_names=array();
                            $variant_array=array();
                                if(count($model_data['model_variants_attribute'])>0){                
                                    $td=""; 
                                    $i=1;
                                    $old_row=null;
                                    foreach ($model_data['model_variants_attribute'] as $attri) { 
                                        if($old_row==null){
                                            $var="";
                                            $td.="<tr>";  
                                            $td.="<td>".$attri->attribute_value."</td>"; 
                                        }else if($old_row==$attri->idvariant){
                                            $td.="<td>".$attri->attribute_value."</td>"; 
                                        }else{                                            
                                            $td.="<td>".$attri->attribute_value."</td>"; 
                                           
                                            
                                        }
                                        $var.=$attri->attribute_value;
                                        if($i==$count){
                                             $td.='<td>';
                                            $td.='<div class="material-switch">';
                                                $checked="";
                                                if($attri->active==1){
                                                    $checked="checked";
                                                }
                                            $td.="<input type='hidden' value='0' name='variant[".$old_row."]'>";
                                            $td.='<input id="active'.$old_row.'" name="variant['.$old_row.']"  type="checkbox" '.$checked.' />'; 
                                            $td.='<label for="active'.$old_row.'" class="label-primary"></label>'; 
                                            $td.='</div>';
                                            $td.='</td>';  
                                            $td.="</tr>";
                                            $td.="<tr>";
                                            $i=0;
                                            $v_arr[]=preg_replace('/\s+/', '', $var);
                                            $var_names[]=$var;
                                            $var="";
                                            $variant_array[]=$attri->idvariant;
                                        }
                                        $old_row=$attri->idvariant;
                                        $i++;
                                    }                                
                                }
                            echo $td;  
                            ?>
                        </tbody>
                    </table>
                </div>
                <div id="variant_images_block">
                    <?php
                    $j=0;
                    //echo $i;
//                    die(print_r($var_names));
                            foreach ($variant_array as $id){  ?>
                                <div id = "<?php echo 'files'.$id; ?>">
                                        <div class = "clearfix"></div>
                                        <hr>
                                        <div class = "col-md-10 col-md-offset-1">
                                            <label class = "col-md-10" style = "padding: 6px 0px 0px 0;">Images : <?php echo $model_data['model']->brand_name . " " . $model_data['model']->model_name ." ".$var_names[$j]; ?></label>
                                            <div class="col-md-2" style="padding-left: 0px !important;">
                                                <a class="btn btn-outline-info add-image"  val="<?php echo 'files'.$attri->idvariant; ?>"  style="padding: 5px 13px !important;">Add Image</a>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div style="border: 1px dotted blue !important;border-radius: 5px;margin-top: 20px;">
                                            <div class="images" > 
                           <?php
                                $ids=multi_array_search($model_data['model_variant_images'], array('idvariant' => $id ));
                                if(count($ids)>0){
                                    foreach ($ids as $pos){ 
                                        $attri=$model_data['model_variant_images'][$pos];
                                        ?>
                                        
                                        <div class="image-url old">
                                                <div class="col-md-3" style="margin: 10px !important;">
                                                    <div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">
                                                        <img height="200" class="img-view"   src="<?php echo base_url() . '/' . $attri->variant_image_path; ?>"  id="userfileimage<?php echo $attri->idvariant; ?>" />
                                                        <input type="hidden" class="form-control" id_variant_image="<?php echo $attri->id_variant_image; ?>" value="<?php echo $attri->variant_image_path; ?>" name="image_path" />
                                                        <input type="hidden" name="image_names[]" value="<?php echo 'files'.$attri->idvariant; ?>" />                                   
                                                    </div>
                                                    <center><a class="thumbnail btn-link waves-effect delete-var-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>
                                                </div>
                                            </div>
                           <?php    }
                                 }else{ ?>
                                    
                                    <div class="image-url">
                                        <div class="col-md-3" style="margin: 10px !important;">
                                            <div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">
                                                <label for="image-upload" id="image-label">Browse</label>
                                                <input type="file" name="<?php echo 'files'.$id ?>[]" id="file" class="imag" />
                                                <img height="200" class="img-view"     id="userfileimage<?php echo $id; ?>" />
                                                <input type="hidden" class="form-control"   name="image_path" />
                                                <input type="hidden" name="image_names[]" value="<?php echo 'files'.$id; ?>" />                                   
                                            </div>
                                            <center><a class="thumbnail btn-link waves-effect delete-var-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>
                                        </div>
                                    </div>
                                                    
                            <?php    } 
                            
                            ?>
                                
                                    </div>  
                                       <div class="clearfix"></div>
                                        </div><br>
                                        <div class="clearfix"></div>
                                        <div class="col-md-10 col-md-offset-1"><span class="col-md-4">Part Number</span><div class="col-md-8"><input type="text" name="partnumber[]" id="partnumber" value="<?php echo $model_data['model_variants'][$j]->part_number ?>" class="form-control" placeholder="Enter part number"></div></div>
                                        </div>  
                                        </div>
                                                
                        <?php  $j++;  }
                    
                    
                                        ?>
                                    
                    
                    
                <div class="clearfix"></div> 
                </div>
                <div class="clearfix"></div>
            </div> 
            
            <?php } ?>
            
            

            <?php if(count($model_data['model_attributes'])>0){ ?>
            <div class="clearfix"></div>
            <div id="attribureblock" class="panel" >
                <div class="clearfix"></div>                            
                <div id="attribure" class="collapse in" aria-expanded="true">
                    <div id="attribure_block">
                        <h4 class="col-md-12 text-center">Specifications</h4><div class="clearfix"></div><hr style="margin-top: 10px !important;margin-bottom: 10px !important;">
                         <?php 
                        $data="";
                        $old_type=null;
                        foreach ($model_data['model_attributes'] as $attri) { 
                            $na_me = preg_replace('/\s+/', '', strtolower($attri->attribute_name));
                            $name = $attri->id_category_attribute.'_'.$attri->id_attribute_type.'_'.$attri->id_attribute.'_'.$na_me;            
                            $data.='<input type="hidden" name="attributes_names[]" value="'.$name.'"/>';       
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
                                    if($attri->has_predefined_values==1){
                                        $data.='<div class="col-md-8">';
                                        $predefined = $this->General_model->get_attribute_values_by_id($attri->id_attribute);
                                        $data.='<select class="chosen-select form-control" name="'.$name.'" id="'.$name.'" >';
                                        $data.='<option value="">Select '.ucfirst($attri->attribute_name).' </option>';
                                        foreach ($predefined as $pre){                            
                                            if($attri->id_attribute==$pre->idattribute){
                                                if($attri->value==$pre->attribute_value){
                                                    $data.='<option selected="" value="'.$pre->attribute_value.'">'.$pre->attribute_value.' </option>';                            
                                                }else{
                                                    $data.='<option value="'.$pre->attribute_value.'">'.$pre->attribute_value.' </option>';                            
                                                }
                                                
                                            }
                                        }
                                        $data.='</select>';
                                        $data.='</div>';
                                        
                                    }else{
                                        $data.='<div class="col-md-8">';
                                        $data.='<input type="text" name="'.$name.'" id="'.$name.'" value="'.$attri->value.'" class="form-control model1"  placeholder="Enter '.strtolower($attri->attribute_name).'" />';    //
                                        $data.='</div>';                        
                                    }                                  

                                $data.='</div>';
                                $data.='<div class="clearfix"></div><br>';
                                $old_type=$attri->attribute_type;

                        }   
                        echo $data;
                        ?>
                        
                        
                    </div>
                </div>
             </div>  
            
            
            <?php } ?>
            
            <div class="clearfix"></div>

            <div class="panel">  
                <div id="files">
                <center><h4><span style="font-size: 28px"></span> Images & Videos </h4></center><hr> 
                <div class="col-md-10 col-md-offset-1">       
                    <label class="col-md-10">Images : </label>
                    <div class="col-md-2">
                            <a class="btn btn-outline-info add-image" val="files" style="padding: 5px 13px !important;">Add Image</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1"  style="border: 1px dotted blue !important;border-radius: 4px;" >                                
                    <div class="images">
                    <?php 
                            foreach ($model_data['model_images'] as $images) { ?>
                                    <div class="image-url old">
                                        <div class="col-md-3" style="margin: 10px !important;">
                                            <div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">                                                
                                                <label for="image-upload" id="image-label">Browse</label>
                                                <input type="file" name="files[]" id="file" class="imag" />
                                                <img height="200" class="img-view" src="<?php echo base_url() . '/' . $images->model_image_path; ?>"  id="userfileimage<?php echo $images->idmodel ?>" />
                                                <input type="hidden" class="form-control" id_model_image="<?php echo $images->id_model_image; ?>" value="<?php echo $images->model_image_path; ?>" name="image_path" />
                                                
                                            </div>
                                            <center><a class="thumbnail btn-link waves-effect delete-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>
                                        </div>
                                    </div>  
                    <?php } ?>
                    </div>

                </div>
                <div class="clearfix"></div><hr>
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-10">Videos  :</label>                    
                    <div class="col-md-2">
                        <a class="btn btn-outline-info add-url" style="padding: 5px 13px !important;">Add Video</a>
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1" id="videos">
                        <?php $i = 1;
                        if(count($model_data['model_videos'])>0){
                        foreach ($model_data['model_videos'] as $video) { ?>
                        
                            <div class="url">
                                <div class="col-md-2">URL</div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control model" value="<?php echo $video->model_video_path; ?>" name="video[]" id="video" placeholder="http://"/>                                                                                           
                                </div>
                                <div class="col-md-2">
                                    <a class="thumbnail btn-link waves-effect delete-url" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a>
                                </div>
                            </div>
                        <div class="clearfix"></div><br>
                        <?php } ?>
                        <?php $i++;
                    } ?>
                </div>        

               <div class="clearfix"></div><br>
            </div>
            </div>  

            <div class="clearfix"></div><br>
            
            <input type="hidden" name="product_category" id="product_category" value="<?php echo $model_data['model']->idproductcategory; ?>" required=""/>
            <input type="hidden" name="category" id="category" value="<?php echo $model_data['model']->idcategory; ?>" required=""/>
            <input type="hidden" name="idbrand" id="idbrand" value="<?php echo $model_data['model']->idbrand; ?>" required=""/>
            
            <input type="hidden" name="product_category_name" id="product_category_name" value="<?php echo $model_data['model']->product_category_name; ?>" required=""/>
            <input type="hidden" name="category_name" id="category_name" value="<?php echo $model_data['model']->category_name; ?>" required=""/>
            <input type="hidden" name="brand_name" id="brand_name" value="<?php echo $model_data['model']->brand_name; ?>" required=""/>
            <input type="hidden" name="model_name" id="model_name" value="<?php echo $model_data['model']->model_name; ?>" required=""/>
            <input type="hidden" name="old_model_name" id="old_model_name" value="<?php echo $model_data['model']->model_name; ?>" required=""/>
            <input type="hidden" name="has_sub_brand" id="has_sub_brand" value="<?php echo $model_data['model']->has_sub_brand; ?>" required=""/>
            <input type="hidden" name="is_model_name" id="is_model_name" value="<?php echo $model_data['model']->is_model_name; ?>" required=""/>

        </article>
        
      <div class="col-md-10 col-md-offset-1">
        <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
        <button type="submit" id="submit-all"  class="pull-right btn btn-info waves-effect"> Save </button>
    </div>
    </form>
</div>

    <div class="clearfix"></div><br>

        
       <script>
            var variant_arr=[];
         $(document).on('click', '.delete-att', function() {              
             var variant=$(this).attr('val');
             variant_arr = jQuery.grep(variant_arr, function(value) { return value !== variant; });
             $(this).parent().parent().fadeOut();
             $(this).parent().parent().remove();
             $("#"+variant).remove();
         });
           
            $(document).on('click', '.add-variant', function() {  
              var category = +$("#category").val();
             $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_category_variant_edit",
                method:"POST",
                data:{category : category},
                success:function(data)
                {   
                    var parsed = $.parseJSON(data);
                    var html="<tr>"
                    var validation=true;
                    var val="";
                    var m_name="";
                    $.each(parsed, function (i, jsondata) {                        
                        if($("#"+jsondata).val()){
                        var type_name = $("#"+jsondata+" option:selected").text(); 
                        html+="<td>"+type_name+"<input type='hidden' name='variant_data["+jsondata+"][]' id='"+jsondata+"' value='"+type_name+"' /></td>";    
                        validation=true;
                        val+=type_name;
                        m_name+=type_name+" ";
                        }else{
                            validation=false;
                            alert("Please select all variants");
                            return false;
                        }
                    });
                    var valu=val.replace(/\s/g,'');
                   
                    html+='<td class="text-center"><a class="thumbnail btn-link waves-effect delete-att" val="'+valu+'" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></td><input type="hidden" name="variant_att_names[]"  value="'+m_name+'" /></tr>';
                    if(validation){
                        
                        var passedArray = <?php echo '["' . implode('", "', $v_arr) . '"]' ?>;                         
                         if (passedArray.includes(valu) === false){                             
                            if (variant_arr.includes(valu) === false){                                
                                variant_arr.push(valu); 
                              $("#variant_data").append(html);
                              var ht='<div id="'+valu+'">';
                                ht+='<div class="clearfix"></div>';
                                ht+='<hr>';
                                ht+='<div class="col-md-10 col-md-offset-1">';
                                ht+='<label class="col-md-10" style="padding: 6px 0px 0px 0;">Images : '+$("#brand_name").val()+' '+$("#model_name").val()+' '+m_name+'</label>';
                                ht+='<div class="col-md-2" style="padding-left: 0px !important;">';
                                    ht+='<a class="btn btn-outline-info add-image"  val="'+valu+'"  style="padding: 5px 13px !important;">Add Image</a>';
                                ht+='</div>';
                                ht+='<div class="clearfix"></div>';
                                ht+='<div style="border: 1px dotted blue !important;border-radius: 5px;margin-top: 20px;">'; 
                                    ht+='<div class="images" >';
                                        ht+='<div class="image-url">';
                                        ht+='<div class="col-md-3" style="margin: 10px !important;">';
                                            ht+='<div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">';
                                                ht+='<label for="image-upload" id="image-label">Browse</label>';
                                                ht+='<input type="file" name="'+valu+'[]" id="file" class="imag" />';
                                                ht+='<img height="200" class="img-view"   id="userfileimage" />';
                                                ht+='<input type="hidden" name="image_names[]" value="'+valu+'" />';
                                                ht+='<input type="hidden" class="form-control" id_model_image=""  name="image_path" />';                                                
                                            ht+='</div>';
                                            ht+='<center><a class="thumbnail btn-link waves-effect delete-var-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>';
                                        ht+='</div>';
                                        ht+='</div>';
                                    ht+='</div>';  
                                    ht+='<div class="clearfix"></div>';
                                ht+='</div>';
                                ht+='<div class="clearfix"></div><br>';
                                ht+='</div>';
                                ht+='<div class="col-md-10 col-md-offset-1"><span class="col-md-4">Part Number</span><div class="col-md-8"><input type="text" name="partnumber[]" id="partnumber" value="" class="form-control" placeholder="Enter part number"></div></div>';
                                ht+='</div>';
                                $("#variant_images_block").append(ht);
                              
                              
                           }else{
                               alert("Variant already created!");
                           }
                       }else{
                          alert("Variant already created!"); 
                       }
                    }
                    
                }
            });
             
             
         });
           
           
        Dropzone.options.myDropzone = {
            url: "<?php echo base_url() ?>Catalogue/save_model",
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 100,
            paramName: "files",
            maxFiles: 100,
            acceptedFiles: "image/*",
            init: function () {

                var submitButton = document.querySelector("#submit-all");
                var wrapperThis = this;

                submitButton.addEventListener("click", function () {
                    wrapperThis.processQueue();
                   
                });
               this.on('success', function(file, json) {    
                   if(json=='1'){
                       alert("Model created successfully");
                        window.location = "<?php echo base_url() ?>Catalogue/model_details";
                    }else{
                        alert("Fail to  create model");
                    }
                });
                
                this.on("addedfile", function (file) {                    
                    var removeButton = Dropzone.createElement('<center><a class="thumbnail btn-link waves-effect" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>');                   
                    removeButton.addEventListener("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        wrapperThis.removeFile(file);
                    });
                    file.previewElement.appendChild(removeButton);
                });

                this.on('sendingmultiple', function (data, xhr, formData) {                    
                    var data = $('form').serializeArray();
                    $.each(data, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                });
            }
        };
    </script>
                            
</div>
<?php include __DIR__.'../../footer.php'; ?>