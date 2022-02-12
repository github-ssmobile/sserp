<?php include('header.php'); ?>
<?php include 'charts/clock.php'; ?>
    <div align="center" class="text-darken-4 col-md-8 col-md-offset-1 waves-effect waves-teal text-center">
        <span class="mdi mdi-home fa-2x"> Home</span>
    </div>
    <div class="col-md-1 pull-right">
        <a href="<?php echo base_url('Master/getbackup') ?>" title="Download Backup" class="simple-tooltip btn-floating btn-large waves-effect waves-purple cyan" style="margin: 0"><i class="fa fa-download fa-2x"></i></a>
    </div><div class="clearfix"></div><hr>
<!--    <div style="font-family: Kurale; font-size: 18px;">
        <div class="col-md-3" style="padding: 5px 10px">
            <a href="<?php echo base_url('Master/product_category_details') ?>" class="box waves-effect waves-purple waves-block" style="border-left: 5px solid #8e44cc !important;">
                <center>
                    <div class="col-md-9">
                        <p style="padding-top: 5px">Product Category<br>
                            <span class="" style="padding-top: 5px;"><?php echo count($type_data) ?></span></p>
                    </div>
                    <div class="col-md-3" style="font-size: 44px">
                        <i class="greytext fa fa-xing"></i>
                    </div>
                </center>
            </a>
        </div>

        <div class="col-md-3" style="padding: 5px 10px">
            <a href="<?php echo base_url('Master/category_details') ?>" class="box waves-effect waves-orange waves-block" style="border-left: 5px solid #f29527 !important;">
                <center>
                    <div class="col-md-9">
                        <p style="padding-top: 5px">Category<br>
                        <span class="" style="padding-top: 5px;"><?php echo count($category_data) ?></span></p>
                    </div>
                    <div class="col-md-3" style="font-size: 44px">
                        <span class="greytext mdi mdi-steam"></span> 
                    </div>
                </center>
            </a>
        </div>

        <div class="col-md-3" style="padding: 5px 10px">
            <a href="<?php echo base_url('Master/brand_details') ?>" class="box waves-effect waves-green waves-block" style="border-left: 5px solid #1cc88a !important;">
                <center>
                    <div class="col-md-9">
                        <p style="padding-top: 5px">Product Brand<br>
                        <span class="" style="padding-top: 5px;"><?php echo count($brand_data) ?></span></p>
                    </div>
                    <div class="col-md-3" style="font-size: 44px">
                        <i class="greytext mdi mdi-script"></i>
                    </div>
                </center>
            </a>
        </div>

        <div class="col-md-3" style="padding: 5px 10px">
            <a href="<?php echo base_url('Master/model_details') ?>" class="box waves-effect waves-red waves-block" style="border-left: 5px solid #e74a3b !important;">
                <center>
                    <div class="col-md-9">
                        <p style=" padding-top: 5px">Product Model<br>
                        <span class="" style="padding-top: 5px;"><?php echo count($model_data) ?></span></p>
                    </div>
                    <div class="col-md-3" style="font-size: 44px">
                        <span class="greytext mdi mdi-cellphone-iphone"></span>
                    </div>
                </center>
            </a>
        </div>    <div class="clearfix"></div>
                
    </div>-->
    <div class="clearfix"></div>
    <div class="col-md-4">
        <?php // include 'charts/new_pie.php'; ?>
    </div>
<?php include('footer.php'); ?>