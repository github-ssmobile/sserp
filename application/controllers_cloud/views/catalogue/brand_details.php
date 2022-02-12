<?php include __DIR__.'../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-script fa-lg"></span> Brand</h3></center><div class="clearfix"></div><hr>
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
            <?php echo form_open_multipart('Catalogue/save_product_brand', array('id' => 'pay','class' => 'collapse' )) ?>            
                <div class="col-md-6 thumbnail col-md-offset-3">
                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Product Brand</h4></center><hr>
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
                            </script>
                        </div>
                        <div class="col-md-7">
                            <label class="col-md-3 col-md-offset-1">Product Brand</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="brand" placeholder="Enter Product Brand" required=""/>
                            </div><div class="clearfix"></div><br>
                            <label class="col-md-3 col-md-offset-1">Status</label>
                            <div class="col-md-7">
                                <select class="select form-control" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div><div class="clearfix"></div><br>
                             <label class="col-md-3 col-md-offset-1">Placement Norm</label>
                            <div class="col-md-7">
                                <select class="select form-control" name="idnorm">
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div><div class="clearfix"></div><br>
                            <center class="text-danger">Please keep image type JPG/JPEG and resolution ratio 140x140</center><hr>
                            <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                            <button type="submit" class="pull-right btn btn-info waves-effect">Save</button>
                            <div class="clearfix"></div>
                        </div>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div><hr>
            </form>
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('brand_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
            <div class="clearfix"></div>
            <table id="brand_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Brand</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Edit</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($brand_data as $brand){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $brand->brand_name; ?></td>
                        <td style="width:50px;height: 50px"><img src="<?php echo base_url().'/'.$brand->brand_image_path;?>" data-target="#modal<?php echo $brand->id_brand ?>" data-toggle="modal"  style="width: 100%; "/>
                            <div aria-hidden="true" aria-labelledby="Image" class="modal fade" id="modal<?php echo $brand->id_brand ?>" role="dialog" tabindex="-1">
                                    <div class="modal-dialog modal-small" role="document">
                                            <div class="modal-content">
                                                    <div class="modal-body mb-0 p-0">
                                                            <img src="<?php echo base_url().'/'.$brand->brand_image_path;?>" alt=""  style="height: 70%; width:100%">
                                                    </div>                                                    
                                            </div>
                                    </div>
                            </div>
                        </td>
                        <td><?php if($brand->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                        <td>
                            <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                <span class="mdi mdi-pen text-danger fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <?php echo form_open_multipart('Catalogue/edit_brand') ?>    
                                        <div class="modal-body">
                                            <div class="thumbnail">
                                                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Brand</h4></center><hr>
                                                <div class="col-md-5">
                                                    <div class="thumbnail" id="image-preview" style="min-height: 200px">
                                                        <label for="image-upload" id="image-label">Upload Image</label>
                                                        <input type="file" name="userfile" id="file" onchange="loadFile<?php echo $i ?>(event)" >
                                                        <img height="200" src="<?php echo base_url().'/'.$brand->brand_image_path;?>"  id="userfileimage<?php echo $brand->id_brand ?>" style="width: 100%; "/>
                                                        <input type="hidden" class="form-control" value="<?php echo $brand->brand_image_path; ?>" name="image_path" />
                                                    </div>
                                                    <script>
                                                        var loadFile<?php echo $i ?> = function (event) {                                                            
                                                            var visitoutput = document.getElementById('userfileimage<?php echo $brand->id_brand ?>');
                                                            visitoutput.src = URL.createObjectURL(event.target.files[0]);
                                                        };
                                                    </script>
                                                </div>
                                                <div class="col-md-7">
                                                    <label class="col-md-3 col-md-offset-1">Brand</label>
                                                    <div class="col-md-7">
                                                        <input type="text" class="form-control" value="<?php echo $brand->brand_name; ?>" name="brand" />
                                                        <input type="hidden" class="form-control" value="<?php echo $brand->brand_name; ?>" name="old_brand" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Status</label>
                                                    <div class="col-md-7">
                                                        <select class="select form-control" name="status">
                                                            <option value="<?php echo $brand->active ?>"><?php if($brand->active == 1){ echo 'Active'; } elseif($brand->active == 0){ echo 'In Active'; } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-3 col-md-offset-1">Placement Norm</label>
                                                    <div class="col-md-7">
                                                        <select class="select form-control" name="idnorm">
                                                            <option value="<?php echo $brand->norm_sequence ?>"><?php if($brand->norm_sequence == 0){ echo 'Inactive'; } else{ echo 'Active'; } ?></option>
                                                            <option value="0">Inactive</option>
                                                            <option value="1">Active</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><br>
                                                    <center class="text-danger">Please keep image type JPG/JPEG and resolution ratio 140x140</center>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                            <button type="submit" value="<?php echo $brand->id_brand ?>" name="id"  class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>