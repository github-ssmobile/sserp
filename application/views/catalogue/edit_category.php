<?php include __DIR__.'../../header.php'; ?>
    <center><h3><span class="mdi mdi-steam fa-lg"></span> Edit Category</h3></center>        
            <script>
                $(document).ready(function(){
                    $(document).on("change", "input[type='checkbox']", function (event) {
                        
                        if($(this).hasClass( "attribute") || $(this).hasClass( "attribute_variant")){
                            var type=0;
                            if($(this).hasClass( "attribute")){
                                var type=1
                            }
                            value= $(this).attr('attribute');
                            var checked=0;
                            if($(this).prop("checked") == true){
                                checked=1;
                            }else if($(this).prop("checked") == false){
                                checked=0;
                            }
                             $.ajax({
                                url:"<?php echo base_url() ?>Catalogue/ajax_updatet_category_has_attributes",
                                method:"POST",
                                data:{data : value,checked:checked,type:type},
                                success:function(data)
                                {                   
                                    if(data == 1){
                                        if(type==1){
                                            if(checked==1){
                                                alert("Attribute added to category :-)");
                                            }else{
                                                alert("Attribute removed from category :-)");
                                            }                                            
                                        }else{
                                            if(checked==1){
                                                alert("Attribute set as variant:-)");
                                            }else{
                                                alert("Variant removed :-)");
                                            }  
                                        }
                                        
                                    }else{
                                        if(type==1){
                                            if(checked==1){
                                                alert("Fail to add attribute to category :-(");
                                            }else{
                                                alert("Fail to removed attribute from category :-(");
                                            }                                            
                                        }else{
                                            if(checked==1){
                                                alert("Fail to set attribute as variant:-(");
                                            }else{
                                                alert("Fail to set reset variant :-(");
                                            }  
                                        }
                                        
                                    }
                                }
                            });
                            
                        }
                        
                        if($(this).prop("checked") == true){
                            elmt= $(this).attr('data-target');
                            $(elmt).find("input[type='checkbox']").removeAttr('Checked');
                             if($(this).hasClass( "attribute")){
                                 $(this).parent().parent().find('.variant').show();
                             }
                            
                        }else if($(this).prop("checked") == false){
                            if($(this).hasClass( "attribute")){
                                 $(this).parent().parent().find('.variant').hide();
                             }
                        }
                    });
                    
                    
                });
            </script>    
            
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
            <?php echo form_open_multipart('Catalogue/edit_category') ?>    
                                <div class="modal-body">
                                    <div class="col-md-8 col-md-offset-2">                                        
                                        <div class="col-md-4">
                                            <div class="thumbnail" id="image-preview" style="min-height: 200px">
                                                <label for="image-upload" id="image-label">Upload Image</label>
                                                <input type="file" name="userfile" id="file" onchange="loadFile(event)" >
                                                <img height="200" src="<?php echo base_url().'/'.$category->category_image_path;?>"  id="userfileimage<?php echo $category->id_category ?>" />
                                                <input type="hidden" class="form-control" value="<?php echo $category->category_image_path; ?>" name="image_path" />
                                            </div>
                                            <script>
                                                var loadFile = function (event) {                                                            
                                                    var visitoutput = document.getElementById('userfileimage<?php echo $category->id_category ?>');
                                                    visitoutput.src = URL.createObjectURL(event.target.files[0]);
                                                };
                                            </script>
                                        </div>
                                        <div class="col-md-8">
                                        <label class="col-md-4 col-md-offset-1"> Product Category </label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" disabled="" value="<?php echo $category->product_category_name; ?>"  /> 
                                        </div><div class="clearfix"></div><br>    
                                        <label class="col-md-4 col-md-offset-1">Category</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="<?php echo $category->category_name; ?>" placeholder="Enter Category" name="category" />
                                            <input type="hidden" value="<?php echo $category->category_name; ?>" name="old_category" />
                                            <input type="hidden" value="<?php echo $category->product_category_name; ?>" name="product_category_name" />
                                            <input type="hidden" value="<?php echo $category->id_category; ?>" name="id" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-4 col-md-offset-1">HSN</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" value="<?php echo $category->hsn; ?>" name="hsn" placeholder="Enter HSN" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-4 col-md-offset-1">Add into Model Name</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" class="simple-tooltip" title="Include category into Model" <?php if($category->is_model_name == 1){ echo 'checked'; } ?> name="pattern">
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-4 col-md-offset-1">Status</label>
                                        <div class="col-md-6">
                                            <select class="select form-control" name="status">
                                                <option value="<?php echo $category->active ?>"><?php if($category->active == 1){ echo 'Active'; } elseif($category->active == 0){ echo 'In Active'; } ?></option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-4 col-md-offset-1">Has Sub-Brand</label>
                                        <div class="col-md-6">
                                            <input type="checkbox" <?php if($category->has_sub_brand == 1){ echo 'checked'; } ?> name="has_sub_brand">
                                        </div>
                                        <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="clearfix"></div><hr>
                                     <div class="m_checkbox">
                                        <div class="">
                                             <?php $i=1; foreach ($attribute_data as $attributes){ 
                                                 $array_ids= multi_array_search($category_attribute_data,array('idattributetype' => $attributes['id_attribute_type']));
                                                 $aria_expanded=false;
                                                 $collapsed='class="collapsed"';
                                                 $checked="";
                                                 if(count($array_ids)>0){
                                                        $aria_expanded=true;
                                                        $collapsed='class=""';
                                                        $checked="checked";
                                                 }  
                                                 ?>
                                                <div class="col-md-4" style="padding: 0;">                
                                                    <a class="thumbnail" style="padding: 5px; margin: 5px">                                                        
                                                        <input type="checkbox" id="attributetype<?php echo $attributes['id_attribute_type'] ?>" <?php echo $checked; ?>  <?php echo $collapsed; ?> value="" data-toggle="collapse" data-target="#attribute_type<?php echo $attributes['id_attribute_type'] ?>" aria-expanded="<?php echo $aria_expanded; ?> ">
                                                        <?php echo $attributes['attribute_type'] ?> 
                                                    </a>
                                                </div>
                                             <?php } ?>
                                        </div>
                                    <div class="clearfix"></div><hr>

                                    <?php $i=0; foreach ($attribute_data as $attributes){ 
                                                    $array_ids= multi_array_search($category_attribute_data,array('idattributetype' => $attributes['id_attribute_type'] ));
                                                    $aria_expanded=false;
                                                    $collapsed='class="collapse"';                                                    
                                                    if(count($array_ids)>0){
                                                           $aria_expanded=true;
                                                           $collapsed='class="collapse in"';                                                           
                                                    }  
                                        ?>
                                    <div class="" >
                                        <div id="attribute_type<?php echo $attributes['id_attribute_type'] ?>" <?php echo $collapsed; ?> aria-expanded="<?php echo $aria_expanded; ?>" >
                                            <div class="">
                                                <h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> <?php echo $attributes['attribute_type'] ?></h4>
                                                <?php $variant_att=array(); 
                                                    foreach ($attributes['attributes'] as $attrib){ 
                                                    $arrayids= multi_array_search($category_attribute_data,array('idattributetype' => $attributes['id_attribute_type'],'idattribute' => $attrib->id_attribute ));
                                                    $checked="";
                                                    $isvariant="";
                                                    $style='style="display:none;"';
                                                    if(count($arrayids)>0){
                                                            $checked="checked";                                                           
                                                            $style='style="display:block;"';
                                                            if($category_attribute_data[$arrayids[0]]->is_variant==1){
                                                                    $isvariant="checked";        
                                                                    $variant_att[]=$attrib;
                                                            }
                                                    }  
                                                    
                                                 ?>
                                                    
                                                        <div class="col-md-3" style="padding: 0;"> 
                                                            <a class="thumbnail" style="padding: 5px; margin: 5px">
                                                                <input type="hidden" name="id_attribute_type[]" value="<?php echo $attributes['id_attribute_type']; ?>" >
                                                                <input type="hidden" name="id_attribute[]" value="<?php echo $attrib->id_attribute; ?>" >
                                                                <div>
                                                                    <input type="checkbox" class="attribute" attribute="<?php echo $attrib->id_attribute; ?>_<?php echo $attributes['id_attribute_type']; ?>_<?php echo $category->id_category; ?>" name="id_attribute<?php echo $i; ?>" <?php echo $checked; ?> id="attribute<?php echo $attrib->id_attribute; ?>" >                                                
                                                                <?php echo $attrib->attribute_name ?>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="variant" <?php echo $style; ?> >   
                                                                    <?php $na_me = preg_replace('/\s+/', '', strtolower($attrib->attribute_name)); ?>
                                                                    <hr style="margin-top: 2px !important;margin-bottom: 2px !important;">
                                                                    <input type="checkbox" class="attribute_variant" name="is_variant<?php echo $i; ?>" <?php echo $isvariant; ?> attribute="<?php echo $attrib->id_attribute; ?>_<?php echo $attributes['id_attribute_type']; ?>_<?php echo $category->id_category; ?>_<?php echo $na_me;?>" id="attribute<?php echo $attrib->id_attribute; ?>" >       
                                                                Is Variant
                                                                </div><div class="clearfix"></div>
                                                                </a>
                                                        </div>
                                                <?php $i++; } ?>
                                                <div class="clearfix"></div>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <?php } ?>
                                    <div class="clearfix"></div>
                                    
                                    
                                    
                                    
                    </div>
                                    <a href="<?php echo base_url('Catalogue/category_details') ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" formmethod="POST"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button>
                                    <div class="clearfix"></div>
                                </div>
                                </form>
                    <div class="clearfix"></div>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>