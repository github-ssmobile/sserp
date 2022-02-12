<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-sitemap fa-lg"></span> SubMenu</h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;"><br>
    <form id="pay" class="collapse">
        <div class="col-md-6 col-md-offset-3">
            <div class="thumbnail">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add SubMenu</h4></center><hr>
                <label class="col-md-3  col-md-offset-1">Menu</label>
                <div class="col-md-7">
                        <select class="select form-control" name="menu" id="menu" required="">
                            <option value="">Select Menu</option>
                            <?php foreach ($menu_data as $menu){ ?>
                            <option value="<?php echo $menu->id_menu; ?>"><?php echo $menu->menu; ?></option>
                            <?php } ?>
                        </select>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">SubMenu</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="submenu" placeholder="Enter SubMenu" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">URL</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="url" placeholder="Enter URL" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Icon</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="font" placeholder="Enter Icon" required="" />
                </div><div class="clearfix"></div><hr>
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_submenu') ?>" class="pull-right btn btn-info waves-effect">Save</button>
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
            <th>SubMenu</th>
            <th>URL</th>
            <th>Font</th>
            <th>Icon</th>
            <th>Status</th>
            <th>Edit</th>
        </thead>
        <tbody class="data_1">
            <?php 
            if(count($submenu_data)>0){
            $i=1; foreach ($submenu_data as $submenu){ ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $submenu->menu; ?></td>
                <td><?php echo $submenu->submenu; ?></td>
                <td><?php echo $submenu->url; ?></td>
                <td><?php echo $submenu->font; ?></td>
                <td><i class="<?php echo $submenu->font; ?> fa-lg"></i></td>
                <td><?php if($submenu->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
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
                                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit SubMenu</h4></center><hr>
                                    <label class="col-md-3  col-md-offset-1">Menu</label>
                                    <div class="col-md-7">
                                        <select class="select form-control" name="menu" id="menu" required="">
                                            <option value="<?php echo $submenu->id_menu; ?>"><?php echo $submenu->menu; ?></option>
                                            <?php foreach ($menu_data as $menu){ ?>
                                            <option value="<?php echo $menu->id_menu; ?>"><?php echo $menu->menu; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div><div class="clearfix"></div><br>
                                    <label class="col-md-3 col-md-offset-1">SubMenu</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" value="<?php echo $submenu->submenu; ?>" name="submenu" />
                                    </div><div class="clearfix"></div><br>
                                    <label class="col-md-3 col-md-offset-1">Font</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" value="<?php echo $submenu->font; ?>" name="font" />
                                    </div><div class="clearfix"></div><br>
                                    <label class="col-md-3 col-md-offset-1">URL</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" value="<?php echo $submenu->url; ?>" name="url" />
                                    </div><div class="clearfix"></div><br>
                                    <label class="col-md-3 col-md-offset-1">Status</label>
                                    <div class="col-md-7">
                                        <select class="select form-control" name="status">
                                            <option value="<?php echo $submenu->active ?>"><?php if($submenu->active == 1){ echo 'Active'; } elseif($submenu->active == 0){ echo 'In Active'; } ?></option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div><div class="clearfix"></div><br>
                                    <div class="clearfix"></div>
                                </div>
                                <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                <button type="submit" value="<?php echo $submenu->id_submenu ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_submenu') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                </td>
            </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include __DIR__.'../../footer.php'; ?>