<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function () {
        $(document).on("change", "input[type='checkbox']", function (event) {
            if ($(this).prop("checked") == true) {
                $(this).attr('checked', 'checked');
                if ($(this).hasClass("attribute")) {
                    $(this).parent().parent().find('.variant').show();
                }
            } else if ($(this).prop("checked") == false) {

                $(this).removeAttr('checked');
                elmt = $(this).attr('data-target');
                $(elmt).find("input[type='checkbox']").removeAttr('checked');
                if ($(this).hasClass("attribute")) {
                    $(this).parent().parent().find('.variant').hide();
                }
            }
        });
    });
</script>
<div class="col-md-10"><center><h3 style="margin: 10px 0"><span class="mdi mdi-steam fa-lg"></span> Category</h3></center></div>
<div class="col-md-1"><a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a></div><div class="clearfix"></div><hr>
<div class="" style="padding: 0; margin: 0;">
    <div id="purchase" style="padding: 10px; margin: 0">
        <?php echo form_open_multipart('Catalogue/save_category', array('id' => 'pay', 'class' => 'collapse')) ?>            
        <div class="col-md-10 thumbnail col-md-offset-1" style="border-radius: 8px">
            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Create Category</h4></center><hr>
            <div class="col-md-5">
                <div class="thumbnail" id="image-preview" style="min-height: 200px">
                    <label for="image-upload" id="image-label">Upload Image</label>
                    <input type="file" name="userfile" id="file" onchange="loadFilee(event)" >
                    <img height="200" id="userfileimage" style="width: 100%; "/>
                </div>
                <script>
                    var loadFilee = function (event) {
                        var visitoutput = document.getElementById('userfileimage');
                        visitoutput.src = URL.createObjectURL(event.target.files[0]);
                    };

                    $(document).ready(function () {
                        $("#category_name").change(function () {
                            $('#product_category').val($(this).children("option:selected").text());
                        });
                    });
                </script>
                <label class="col-md-8">Features Add into Model Name</label>
                <div class="col-md-4">
                    <input type="checkbox" class="simple-tooltip" title="Include category into Model" name="pattern">
                </div><div class="clearfix"></div><br>
            </div>
            <div class="col-md-7">
                <label class="col-md-4 col-md-offset-1">Product Category</label>                        
                <div class="col-md-7">
                    <select class="select form-control" id="category_name" name="product_category_id">
                        <option value="">Select Product Category</option>
                        <?php foreach ($type_data as $type) { ?>
                            <option value="<?php echo $type->id_product_category ?>"><?php echo $type->product_category_name ?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" name="product_category" id="product_category" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-4 col-md-offset-1">Category</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" placeholder="Enter Category" name="category" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-4 col-md-offset-1">HSN</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="hsn" placeholder="Enter HSN" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-4 col-md-offset-1">Status</label>
                <div class="col-md-7">
                    <select class="select form-control" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div><div class="clearfix"></div><br>
                <label class="col-md-4  col-md-offset-1">Has Sub-Brand</label>
                <div class="col-md-7">
                    <input type="checkbox"  name="has_sub_brand">
                </div><div class="clearfix"></div><br>
                <label class="col-md-4 col-md-offset-1">Placement Norm</label>
                <div class="col-md-7">
                    <select class="select form-control" name="idnorm">
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                    </select>
                </div><div class="clearfix"></div><br>
            </div>

            <div class="clearfix"></div><hr>
            <div class="m_checkbox">
                <div class="">
                    <?php $i = 1;
                    foreach ($attribute_data as $attributes) { ?>
                        <div class="col-md-4" style="padding: 0;">                
                            <a class="thumbnail" style="padding: 5px; margin: 5px">
                                <input type="checkbox" id="attributetype<?php echo $attributes['id_attribute_type'] ?>" class="collapsed" value="" data-toggle="collapse" data-target="#attribute_type<?php echo $attributes['id_attribute_type'] ?>" aria-expanded="false">
                                <?php echo $attributes['attribute_type'] ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
                <div class="clearfix"></div><hr>
                <?php $i = 0;
                    foreach ($attribute_data as $attributes) { ?>
                    <div class="" >
                        <div id="attribute_type<?php echo $attributes['id_attribute_type'] ?>" class="collapse" aria-expanded="true" >
                            <div class="">
                                <h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> <?php echo $attributes['attribute_type'] ?></h4>
                                    <?php foreach ($attributes['attributes'] as $attrib) { ?>
                                    <div class="col-md-3" style="padding: 0;"> 
                                        <a class="thumbnail" style="padding: 5px; margin: 5px">
                                            <input type="hidden" name="id_attribute_type[]" value="<?php echo $attrib->idattributetype; ?>" >
                                            <input type="hidden" name="id_attribute[]" value="<?php echo $attrib->id_attribute; ?>" >
                                            <input type="hidden" name="attribute_name[]" value="<?php echo $attrib->attribute_name; ?>" >
                                            <div>
                                                <input type="checkbox" class="attribute" name="id_attribute<?php echo $i; ?>"  id="attribute<?php echo $attrib->id_attribute; ?>" >                                                
                                            <?php echo $attrib->attribute_name ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="variant" style="display:none;" >  

                                                <hr style="margin-top: 2px !important;margin-bottom: 2px !important;">
                                                <input type="checkbox" name="is_variant<?php echo $i; ?>"  id="attribute<?php echo $attrib->id_attribute; ?>" >       
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
            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Catalogue/save_category') ?>" class="pull-right btn btn-info waves-effect">Save</button>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div><hr>
        <?php echo form_close(); ?>
        <div class="edit"></div>
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-4">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('category_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div><div class="clearfix"></div><br>
        <div class="thumbnail" style="padding: 0; overflow: auto;">
            <table id="category_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="margin-bottom: 0">
                <thead>
                <th>Sr</th>
                <th>Product Category</th>
                <th>Category</th>
                <th>HSN</th>
                <th>Image</th>
                <th>Status</th>
                <th class="col-md-2 text-center">Attribute Setup</th>
                <th>Edit</th>
                </thead>
                <tbody class="data_1">
                    <?php $i = 1;
                    foreach ($category_data as $category) { ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $category->product_category_name; ?></td>
                            <td><?php echo $category->category_name; ?></td>
                            <td><?php echo $category->hsn; ?></td>
                            <td style="width:50px;height: 50px "><img src="<?php echo base_url() . '/' . $category->category_image_path; ?>" data-target="#modal<?php echo $category->id_category ?>" data-toggle="modal"  style="width: 100%; "/>
                                <div aria-hidden="true" aria-labelledby="Image" class="modal fade" id="modal<?php echo $category->id_category ?>" role="dialog" tabindex="-1">
                                    <div class="modal-dialog modal-small" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body mb-0 p-0">
                                                <img src="<?php echo base_url() . '/' . $category->category_image_path; ?>" alt=""  style="height: 70%; width:100%">
                                            </div>                                                    
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?php if ($category->active == 1) {
                                    echo 'Active';
                                } else {
                                    echo 'In Active';
                                } ?>
                            </td>
                            <td class="text-center">
                                <a class="thumbnail btn-link waves-effect" style="margin-bottom: 0px !important;" href="<?php echo base_url('Catalogue/category_edit/' . $category->id_category) ?>" >
                                    <i class="mdi mdi-account-settings-variant text-primary fa-lg"></i>
                                </a>

                            </td>
                            <td>
                                <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                    <span class="mdi mdi-pen text-danger fa-lg"></span>
                                </a>
                            </td>
                        </tr>
                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <?php echo form_open_multipart('Catalogue/edit_category') ?>    
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Category</h4></center><hr>
                                        <div class="col-md-5">
                                            <div class="thumbnail" id="image-preview" style="min-height: 200px">
                                                <label for="image-upload" id="image-label">Upload Image</label>
                                                <input type="file" name="userfile" id="file" onchange="loadFile<?php echo $i ?>(event)" >
                                                <img height="200" src="<?php echo base_url() . '/' . $category->category_image_path; ?>"  id="userfileimage<?php echo $category->id_category ?>" style="width: 100%; "/>
                                                <input type="hidden" class="form-control" value="<?php echo $category->category_image_path; ?>" name="image_path" />
                                            </div>
                                            <script>
                                                var loadFile<?php echo $i ?> = function (event) {
                                                    var visitoutput = document.getElementById('userfileimage<?php echo $category->id_category ?>');
                                                    visitoutput.src = URL.createObjectURL(event.target.files[0]);
                                                };
                                            </script>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="col-md-3 col-md-offset-1">Category</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" value="<?php echo $category->category_name; ?>" placeholder="Enter Category" name="category" />
                                                <input type="hidden" value="<?php echo $category->category_name; ?>" name="old_category" />
                                                <input type="hidden" value="<?php echo $category->product_category_name; ?>" name="product_category_name" />
                                                <input type="hidden" value="<?php echo $category->id_category; ?>" name="id" />
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">HSN</label>
                                            <div class="col-md-7">
                                                <input type="text" class="form-control" value="<?php echo $category->hsn; ?>" name="hsn" placeholder="Enter HSN" />
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">Add into Model Name</label>
                                            <div class="col-md-7">
                                                <input type="checkbox" class="simple-tooltip" title="Include category into Model" <?php if ($category->is_model_name == 1) { echo 'checked'; } ?> name="pattern">
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-3 col-md-offset-1">Status</label>
                                            <div class="col-md-7">
                                                <select class="select form-control" name="status">
                                                    <option value="<?php echo $category->active ?>"><?php if ($category->active == 1) {
                                                            echo 'Active';
                                                        } elseif ($category->active == 0) {
                                                            echo 'In Active';
                                                        } ?>
                                                    </option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div><div class="clearfix"></div><br>
                                            <label class="col-md-4 col-md-offset-1">Has Sub-Brand</label>
                                            <div class="col-md-6">
                                                <input type="checkbox" <?php if ($category->has_sub_brand == 1) {
                                                        echo 'checked'; } ?> name="has_sub_brand">
                                            </div>
                                            <div class="clearfix"></div>
                                            <label class="col-md-4 col-md-offset-1">Placement Norm</label>
                                            <div class="col-md-6">
                                                <select class="select form-control" name="idnorm">
                                                    <option value="<?php echo $category->norm_sequence ?>"><?php if($category->norm_sequence == 0){ echo 'Inactive'; } else{ echo 'Active'; } ?></option>
                                                    <option value="0">Inactive</option>
                                                    <option value="1">Active</option>
                                                </select>
                                            </div><div class="clearfix"></div><br>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" formmethod="POST"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button>
                                    <div class="clearfix"></div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </tbody>
            </table>
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>