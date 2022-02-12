<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SS ERP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo site_url(); ?>assets/images/favicon.jpeg"/>
        <?= link_tag("assets/login/vendor/bootstrap/css/bootstrap.min.css") ?>
        <?= link_tag("assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css") ?>
        <?= link_tag("assets/login/vendor/animate/animate.css") ?>
        <?= link_tag("assets/login/vendor/css-hamburgers/hamburgers.min.css") ?>
        <?= link_tag("assets/login/vendor/select2/select2.min.css") ?>
        <?= link_tag("assets/login/css/util.css") ?>
        <?= link_tag("assets/login/css/main.css") ?>
        <script>
            window.setTimeout(function() {
                $("#alert-dismiss").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, 3000);
        </script> 
        <style>
.sponsors-cta {
    /*border-radius: 5px;*/
    box-shadow: 0 6px 13px 0 rgba(0,0,0,.15);
    letter-spacing: 1px;
    background-color: #15C39A;
}
.sponsors-sm01 {
    border-radius: 11px;
    border: 1px solid #ddd;
    /*background: linear-gradient(-30deg, #b0efe0E5, #b0efe0E5 45%, #b0efe0 45%) #fff;*/
}
        </style>
    </head>
    <body>
        <div class="limiter">
            <div class="container-login100 sponsors-sm01">
                <img src="<?php echo site_url(); ?>assets/images/sslogo.jpg" alt="LOGO" style="position: absolute; top: 50px">
                <?php if( $save = $this->session->flashdata('save_data')): ?>
                <div class="alert alert-dismissible alert-success" id="alert-dismiss" style="position: absolute; top: 160px;z-index: 99999">
                    <?= $save ?>
                </div>
                <?php endif; ?>
                <?php if( $save = $this->session->flashdata('logout1')): ?>
                <div class="alert alert-danger" id="alert-dismiss" style="position: absolute; top: 160px;z-index: 99999">
                    <?= $save ?>
                </div>
                <?php endif; ?>
                <div class="wrap-login100">
                    <div class="login100-pic js-tilt" data-tilt>
                        <img src="<?php echo site_url(); ?>assets/login/images/img-01.png" alt="IMG">
                    </div>
                    <form class="login100-form validate-form">
                        <span class="login100-form-title">
                            <i class="fa fa-user-o"></i> &nbsp; Login
                        </span>
                        <div class="wrap-input100 validate-input">
                            <input class="input100" type="text" name="email" placeholder="User ID" required="">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="wrap-input100 validate-input" data-validate = "Password is required">
                            <input class="input100" type="password" name="password" placeholder="Password" required="">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn sponsors-cta" type="submit" formmethod="post" formaction="<?php echo base_url('Login/verify_login')?>">
                                Login
                            </button>
                        </div>
                        <div class="text-center p-t-12">
                            <span class="txt1">
                                Forgot
                            </span>
                            <a class="txt2" href="#">
                                Password?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="<?php echo site_url(); ?>assets/login/vendor/jquery/jquery-3.2.1.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/bootstrap/js/popper.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/select2/select2.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/tilt/tilt.jquery.min.js"></script>
        <script> $('.js-tilt').tilt({ scale: 1.1; });
        </script>
    </body>
</html>