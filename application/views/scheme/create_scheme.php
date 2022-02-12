<?php include __DIR__.'../../header.php'; ?>
<style>
.modes_block:hover{
    background-color: #f4f4f4;
}
.blink {
    animation: blinker 1s linear infinite;
}
@keyframes blinker {
    10% {
        opacity: 0;
    }
}
.alert_msg{
    width: 450px;
    background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
    color: #fff;
    border: 1px solid #00cccc;
    font-family: Kurale;
    font-size: 16px;
    text-align: center;
    opacity: 0.9;
    border-radius: 5px;
    position: fixed;
    bottom: 2%;
    left: 2%;
    padding: 10px;
    display: none;
    z-index: 9999999;
    /*animation: blinker 2s linear infinite;*/
}
</style>
<script src="<?php echo site_url('assets/js/autocomplete-jquery-ui.js') ?>"  type="text/javascript"></script>
<?= link_tag("assets/css/autocomplete-jquery-ui.css") ?>
<!--<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="pe pe-7s-note2 fa-lg"></span> Create Scheme</h3></center></div><div class="clearfix"></div><hr>-->
<form id="sale_form_submit">
<!--    <div class="" style="padding: 5px;border-radius: 1rem;background: #fbfbff;border: 4px solid #e3e3e3;">
        <?php foreach($scheme_type as $type){ ?>
            <a href="<?php echo base_url('Scheme/create_scheme/'.$type->id_scheme_type) ?>" class="col-md-3 col-lg-3 p-1">
                <div class="neucard shadow-inset border-light p-2 waves-effect waves-block waves-ripple" 
                    style="background-color: #fff; <?php if(isset($schemetype)){ if($schemetype->id_scheme_type == $type->id_scheme_type){ echo 'color:#000; font-weight: bold;background-color:#d9d9d9'; }} ?>">
                    <center><i class="<?php echo $type->font ?> fa-lg pull-left"></i> <?php echo str_replace('_', ' ', $type->scheme_type) ?></center>
                </div>
            </a>
        <?php } ?>
        <div class="clearfix"></div>
    </div><div class="clearfix"></div>-->
    <?php if(isset($schemetype)){ ?><br>
            <center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?></span></center><hr>
            <?php include $schemetype->scheme_type.'.php'; ?>
    <?php }else{ ?>
    <div class="col-md-10 col-md-offset-1"><br>
        <center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="mdi mdi-arrow-up-bold-circle-outline"></i> Select scheme type for scheme creation</span></center><hr>
        <div class="clearfix"></div>
    </div><div class="clearfix"></div>
    <?php } ?>
</form><div class="alert_msg"></div>

<?php include __DIR__.'../../footer.php'; ?>