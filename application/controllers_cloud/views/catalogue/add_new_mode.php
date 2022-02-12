<?php include __DIR__.'../../header.php'; ?>

<center><h3 style="margin-top: 0"><span class="mdi mdi-cellphone-iphone fa-lg"></span> Create Model</h3></center>
<?php $data=array(); if($save = $this->session->flashdata('save_data')): ?>
    <div class="alert alert-dismissible alert-success" id="alert-dismiss">
        <?= $save ?>
    </div>
<?php endif; ?>
   
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
   
        var variant_arr=[];
         $(document).on('click', '.add-url', function() { 
            
            $('#videos').append(''+
                                '<div class="url">'+
                                '<div class="col-md-4"></div>'+
                                '<div class="col-md-6">'+
                                        '<input type="text" class="form-control model" name="video[]" id="video" placeholder="http://"/>'+                            
                                '</div>'+
                                '<div class="col-md-2">'+
                                    '<a class="thumbnail btn-link waves-effect delete-url" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a>'+
                                '</div><div class="clearfix"></div><br></div>')
        
         });
         
         $(document).on('click', '.delete-url, .delete-img', function() {              
             $(this).parent().parent().fadeOut();
             $(this).parent().parent().remove();
             
         });
         $(document).on('click', '.delete-att', function() {              
             var variant=$(this).attr('val');
             variant_arr = jQuery.grep(variant_arr, function(value) { return value !== variant; });
             $(this).parent().parent().fadeOut();
             $(this).parent().parent().remove();
             $("#"+variant).remove();
         });
         
         $(document).on('click', '.add-image', function() {  
            
                var name=$(this).attr('val');
                el=$("#"+name).find('.images');
                $(el).append('<div class="col-md-3" style="margin: 10px !important;">'+
                        '<div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">'+
                            '<label for="image-upload" id="image-label">Browse</label>'+
                            '<input type="file" name="'+name+'[]" id="file" class="imag" />'+
                            '<img height="200" class="img-view" src="" />'+
                            '<input type="hidden" class="form-control" value="" name="image_path" />'+
                        '</div>'+
                        '<center><a class="thumbnail btn-link waves-effect delete-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>'+
                    '</div>'); 
        
            
        });
         var cnt=0;
         $(document).on('click', '.add-variant', function() {  
              var category = +$("#category").val();
             $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_category_variant",
                method:"POST",
                data:{category : category},
                success:function(data)
                { 
                    
                    var parsed = $.parseJSON(data);
                    var html="<tr>"
                    var val="";
                    var validation=true;
                    var val="";
                    var m_name="";
                    $.each(parsed, function (i, jsondata) {                        
                        if($("#"+jsondata).val()){
                        var type_name = $("#"+jsondata+ " option:selected").text(); 
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
                    html+='<td class="text-center"><a class="thumbnail btn-link waves-effect delete-att" cnt="'+cnt+'" val="'+valu+'" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></td><input type="hidden" name="variant_att_names[]"  value="'+m_name+'" /></tr>';
                    if(validation){
                        
                         
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
                                        ht+='<div class="col-md-3" style="margin: 10px !important;">';
                                            ht+='<div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">';
                                                ht+='<label for="image-upload" id="image-label">Browse</label>';
                                                ht+='<input type="file" name="'+valu+'[]" id="file" class="imag" />';
                                                ht+='<img height="200" class="img-view"   id="userfileimage" />';
                                                ht+='<input type="hidden" name="image_names[]" value="'+valu+'" />';
                                                ht+='<input type="hidden" class="form-control" id_model_image=""  name="image_path" />';                                                
                                            ht+='</div>';
                                            ht+='<center><a class="thumbnail btn-link waves-effect delete-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>';
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
                    }
                    cnt++;
                }
            });
             
             
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
            var is_model_name = $('option:selected', this).attr("is_model_name");
            $("#is_model_name").val(is_model_name);
            
            
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
                     
            $("#category_name").val($('#category option:selected').text());
             $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_get_category_attributes_by_id",                
                data:{category : category},
                method: 'POST',
                dataType: 'json',
                success: function(data, textStatus, jqXHR)
                {                   
                                      
                    if(data.vart === ""){
                        $("#variantblock").hide();
                    }else{
                        $("#variantblock").show();
                       $("#variant_block").html(data.vart); 
                            var array = data.names.split(',');                                        
                            var html="<tr>";
                            $.each(array, function (i, jsondata) {

                                html+="<th>"+jsondata+"</th>";    
                            });
                            html+="<th class='text-center'>Delete</th></tr>";
                            $("#variant_names").append(html);
                    }
                    if(data.att === ""){
                        $("#attribureblock").hide();
                    }else{
                        $("#attribureblock").show();
                        $("#attribure_block").html(data.att);   
                    }
                      $(".chosen-select").chosen({search_contains: true});
                   
                }
            });
            
            
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
    });
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<div class="clearfix"></div>
<div class="col-md-8 col-md-offset-2">
    <!--<form action="/" enctype="multipart/form-data" method="POST">-->
     <?php echo form_open_multipart('Catalogue/save_model') ?> 
        <article  style="padding: 15px">
            <div class="panel">
                <center><h4><span style="font-size: 28px"></span> Basic Information </h4></center><hr>                    
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">Product Type</label>
                    <div class="col-md-8">
                        <select class="chosen-select form-control" name="product_category" id="product_category" required="">
                            <option value="">Select Type</option>
                            <?php foreach ($type_data as $type) { ?>
                                <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">Category</label>
                    <div class="col-md-8">
                        <select class="chosen-select form-control" name="category" id="category" required="">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                </div>    
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">                        
                    <label class="col-md-4">Brand</label>
                    <div class="col-md-8">
                        <select class="chosen-select form-control" name="idbrand" id="idbrand" required="">
                            <option value="">Select Brand</option>
                            <?php foreach ($brand_data as $brand) { ?>
                                <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>     
                <div class="clearfix"></div><br>                
                <div class="col-md-10 col-md-offset-1" id="first_block" style="display: none;">
                    <label class="col-md-4">Model</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control model" name="model" id="model" placeholder="Enter Model Name"/>
                    </div><div class="clearfix"></div>
                </div>              
                <div class="clearfix"></div>  
                <div id="second_block" style="display: none"> 
                <div class="col-md-10 col-md-offset-1">                    
                        <label class="col-md-4">Mobile Brand</label>
                        <div class="col-md-8">
                                <select class="form-control" name="subbrand" id="subbrand">
                                    <option value="">Mobile Brand</option>
                                    <?php foreach ($brand_data as $brand) { ?>
                                        <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                                    <?php } ?>
                                </select>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-10 col-md-offset-1">                    
                        <label class="col-md-4">Mobile Model</label>
                        <div class="col-md-8">
                                <select class="form-control" name="model1" id="model1">
                                    <option value="">Select Model</option>
                                </select>               
                        </div><div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>                 
                    
                </div> 
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">
                    <label class="col-md-4">SKU Type</label>
                    <div class="col-md-8">
                        <select class="chosen-select form-control" name="sku_type" id="sku_type" required="">
                            <option value="">Select SKU Type</option>
                            <?php
                            $i = 1;
                            foreach ($sku_type_data as $sku) {
                                ?>
                                <option value="<?php echo $sku->id_sku_type; ?>"><?php echo $sku->sku_type; ?></option>
                            <?php } ?>
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
                        <?php  echo $this->ckeditor->editor("description");?>      
                    </div>
                        <!-- <textarea type="text" class="form-control" name="description" id="description" placeholder="Enter model description"></textarea> -->
                </div><div class="clearfix"></div>                
              
                
                <div class="clearfix"></div> 
            </div>

            <div class="clearfix"></div>
            
            <div id="variantblock" class="panel" style="display: none;">
                <div>
                    <div id="variant_block">

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
                        </thead>    
                        <tbody id="variant_data">
                        </tbody>
                    </table>
                </div>
                
                <div id="variant_images_block">
                        
                <div class="clearfix"></div> 
            </div>
                <div class="clearfix"></div>
            </div> 
            
            
            <div class="clearfix"></div> 
            <div id="attribureblock" class="panel" style="display: none">
                <!--<div class="" style="display: none">
                    <a class=" pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#attribure" title="Specifications" aria-expanded="false" style="margin-bottom: 2px"></a>
                    <div class="clearfix"></div>
                </div>-->
                <div class="clearfix"></div>                            
                <div id="attribure" class="collapse in" aria-expanded="true">
                    <div id="attribure_block">

                    </div>
                </div><div class="clearfix"></div>
            </div>

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
                                    <div class="image-url">
                                        <div class="col-md-3" style="margin: 10px !important;">
                                            <div class="thumbnail" id="iimage-preview" style="min-height: 150px;margin-bottom: 5px !important;">                                                
                                                <label for="image-upload" id="image-label">Browse</label>
                                                <input type="file" name="files[]" id="file" class="imag" />
                                                <img height="200" class="img-view"   id="userfileimage" />                                                
                                            </div>
                                            <center><a class="thumbnail btn-link waves-effect delete-img" style="margin: 0"><span class="mdi mdi-delete text-danger fa-lg"></span></a></center>
                                        </div>
                                    </div>
                            </div>
                            <div class="clearfix"></div>
                </div>
                
<!--                <div class="col-md-10 col-md-offset-1" >                                
                    <div class="dropzone" id="my-dropzone" name="mainFileUploader">
                        <div class="dz-message" data-dz-message><span>Drag & Drop images here</span></div>
                        <div class="fallback">
                            <input name="file" type="file" multiple />
                        </div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                </div>-->
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1">
                    <div class="url">
                        <label class="col-md-4">Videos URL :</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control model" name="video[]" id="video" placeholder="http://"/>                            
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-outline-info add-url" style="padding: 5px 13px !important;">Add</a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <div class="col-md-10 col-md-offset-1" id="videos">

                </div>
                <div class="clearfix"></div>
                </div>
            </div>  

            <div class="clearfix"></div>
            
                 

            <input type="hidden" name="product_category_name" id="product_category_name" required=""/>
            <input type="hidden" name="category_name" id="category_name" required=""/>
            <input type="hidden" name="brand_name" id="brand_name" required=""/>
            <input type="hidden" name="model_name" id="model_name" required=""/>
            <input type="hidden" name="has_sub_brand" id="has_sub_brand" required=""/>
            <input type="hidden" name="is_model_name" id="is_model_name" required=""/>
            

        </article>
        <div class="clearfix"></div><br>
    
         <div class="col-md-10 col-md-offset-1">
        <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
        <button type="submit" id="submit-all"  class="pull-right btn btn-info waves-effect"> Save </button>
    </div>
        
    </form>
   
    <div class="clearfix"></div><br>
<!--
        
       <script>
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
                         
                        alert("Fail to create model");
                    }
                });
                
                this.on("complete", function(file) { 
               // this.removeAllFiles(true); 
            });
            
//            this.on("error", function(file, errormessage, xhr){
//                alert("Fail to create model");
//                this.removeAllFiles(true); 
//            });

            this.on("error", function(file, errorMessage) {
                $.each(this.files, function(i, file) {
                    file.status = Dropzone.QUEUED
                });

                
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
                    formData.append('description', CKEDITOR.instances['description'].getData());
                });
            }
        };
    </script>
                            -->
</div>
<?php include __DIR__.'../../footer.php'; ?>