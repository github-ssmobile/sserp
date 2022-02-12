<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>ERP</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <?= link_tag("assets/css/bootstrap.css") ?>
    <?= link_tag("assets/css/signinstyle.css") ?>
    <?= link_tag("assets/css/font-awesome.min.css") ?>
    <?= link_tag("assets/material_font/css/materialdesignicons.css") ?>
    <?= link_tag("assets/css/kurale-font.css") ?>
    <?= link_tag("assets/css/style.css") ?>
    <?=  link_tag("assets/waves/btnwave.css") ?>
    <?=  link_tag("assets/creative_font/css/pe-icon-7-stroke.css") ?>
    <?=  link_tag("assets/creative_font/css/helper.css") ?>
    <?= link_tag("assets/css/tipped.css") ?>    
    <?= link_tag("assets/css/k2d.css") ?>
    <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    <style>.btn{ margin: 0; }
        .new_box{
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 10px rgba(0, 0, 0, 0.24);
            webkit-transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
            transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .new_box:hover{
            box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);
            webkit-transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
            transition: all 400ms cubic-bezier(0.4, 0, 0.2, 1);
        }</style>
</head>
<body>
    <div class="container-fluid"><br>
        <center><img class="hovereffect" src="<?php echo site_url(); ?>assets/images/sslogo.jpg" alt="LOGO"></center>
        <div class="col-md-6"><br>
            <img class="" src="<?php echo site_url(); ?>assets/images/meeting-large.gif" style="width: 100%" />
        </div>
        <div class="col-md-4 col-md-offset-1" style=""><br><br><br><br><br>
            <form class="hoverable">
            <?php if ($save = $this->session->flashdata('save_data')): ?>
                <div class="alert alert-dismissible alert-success" id="alert-dismiss">
                    <?= $save ?>
                </div>
            <?php endif; ?>
            <?php if ($save = $this->session->flashdata('logout1')): ?>
                <div class="alert alert-danger">
                    <?= $save ?>
                </div>
            <?php endif; ?>
                <div class="hovereffect" style="background-image: linear-gradient(to right top, #ffd8d8, #ffdace, #ffdfc1, #ffe7b5, #fff1ae);">
                    <div class="modal-header" style="border-bottom: 1px solid #ff82a1;font-family: Kurale;">
                        <h2 class="modal-title">
                            <center>
                                <!--<img class="" src="https://i.dlpng.com/static/png/6869183_preview.png" width="50" />-->
                                <!--<span class="mdi mdi-account-circle"></span>-->  
                                Login
                            </center></h2>
                    </div>
                    <div class="modal-body" style="padding: 30px;">
                        <div class="form-group-lg">
                            User ID
                            <input type="text" required="true" class="form-control" name="email" id="email" placeholder="Enter User Id" autocomplete="off">
                        </div><br>
                        <div class="form-group-lg">
                            Password
                            <input type="password" class="form-control" name="password" id="pwd" placeholder="Enter password" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #ff82a1">
                        <button type="submit" class="btn btn-primary waves-effect waves-purple pull-right" formmethod="post" 
                            formaction="<?php echo base_url('Login/verify_login') ?>" >Login</button>
                    </div>
                </div>
            </form>
        </div><div class="clearfix"></div>
    </div>
<script src="<?php echo site_url(); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/waves/waves.js" type="text/javascript"></script>
</body>
</html>



