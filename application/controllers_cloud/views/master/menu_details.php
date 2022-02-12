<?php include __DIR__.'../../header.php'; ?>
    <center><h3><span class="mdi mdi-menu fa-lg"></span> Menu</h3></center>
    
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;"><br>
        <form id="pay" class="collapse">
            <div class="col-md-6 col-md-offset-3">
                <div class="thumbnail">
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Menu</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Menu</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="menu" placeholder="Enter Menu" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">URL</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="url" placeholder="Enter URL" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Icon</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="font" placeholder="Enter Icon" required="" />
                    </div><div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_menu') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div><hr>
        </form><div class="clearfix"></div>
        <div class="col-md-6">
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
            <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Menu" style="margin-bottom: 2px"></a>
        </div><div class="clearfix"></div>
        <table id="menu_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>Menu</th>
                <th>URL</th>
                <th>Font</th>
                <th>Icon</th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($menu_data as $menu){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $menu->menu; ?></td>
                    <td><?php echo $menu->url; ?></td>
                    <td><?php echo $menu->font; ?></td>
                    <td><i class="<?php echo $menu->font; ?> fa-lg"></i></td>
                    <td><?php if($menu->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                        <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form>
                                <div class="modal-body">
                                    <div class="thumbnail">
                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Menu</h4></center><hr>
                                        <label class="col-md-3 col-md-offset-1">Menu</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $menu->menu; ?>" name="menu" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Font</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $menu->font; ?>" name="font" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">URL</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control" value="<?php echo $menu->url; ?>" name="url" />
                                        </div><div class="clearfix"></div><br>
                                        <label class="col-md-3 col-md-offset-1">Status</label>
                                        <div class="col-md-7">
                                            <select class="select form-control" name="status">
                                                <option value="<?php echo $menu->active ?>"><?php if($menu->active == 1){ echo 'Active'; } elseif($menu->active == 0){ echo 'In Active'; } ?></option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div><div class="clearfix"></div><br>
                                        <div class="clearfix"></div>
                                    </div>
                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                    <button type="submit" value="<?php echo $menu->id_menu ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_menu') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
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
<?php include __DIR__.'../../footer.php'; ?>