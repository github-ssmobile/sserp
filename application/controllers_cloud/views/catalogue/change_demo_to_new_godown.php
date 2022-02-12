<?php include __DIR__ . '../../header.php'; ?>
<?= link_tag("assets/css/timeline.css") ?>
<?= link_tag("assets_ecom/css/choosen.css") ?>   
<style type="text/css">
  #map {
    height: 600px;  /* The height is 400 pixels */
    width: 100%;  /* The width is the width of the web page */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 10px rgba(0, 0, 0, 0.24);
  }
</style>
<script>
$(document).ready(function(){
    $('#imei').change(function(){
        var imei = $('#imei').val();
        var type= $('#type').val();
        if(imei == ''){
            $("#product_data").hide();
        }else{
            $.ajax({
                url:"<?php echo base_url() ?>Catalogue/ajax_imei_godown_change",
                method:"POST",
                data:{imei : imei,type:type},
                success:function(data)
                {
                    $("#product_data").html(data);
                     $(".chosen-select").chosen({ search_contains: true });
//                        $("#config_block").show();
                }
            });
        }
    });
});
</script>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-checkbox-marked-outline"></span> <?php echo $title ?> </h3></center></div><div class="clearfix"></div><hr>

<div class="" style="background-color: #fbfbff">
    <div class="col-md-2 col-sm-3">IMEI / SRNO</div>
    <div class="col-md-4 col-sm-8">
        <input type="hidden" class="form-control" id="type" name="type" value="<?php echo $type; ?>"/>
        <input type="text" class="form-control" id="imei" name="imei" placeholder="IMEI/SRNO Tracking"/>
    </div><div class="clearfix"></div>
    <div class="" style="font-size: 14px; overflow: auto; padding-bottom: 50px;">
        <div id="product_data"></div>
    </div>
</div>
<style>
h1 {
  font-size: 150%;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: 400;
  padding-top: 10px;
}

header {
  /*background-color: #fff;*/
  color: #fff;
  background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);
  box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);
  border-radius: 5px;
  /*border-radius: 5px;*/
  /*padding: 10px 0;*/
  /*box-shadow: 5px 20px 25px -15px rgba(63, 81, 181, 0.3);*/
  /*box-shadow: 0 10px 20px rgba(63, 81, 181, 0.19), 0 6px 6px rgba(63, 81, 181, 0.23);*/
  /*-webkit-box-shadow: 0 10px 20px rgba(63, 81, 181, 0.19), 0 1px 15px rgba(63, 81, 181, 0.23);*/
  /*box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);*/
  /*-webkit-box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 1px 15px rgba(0, 0, 0, 0.23);*/
}
header p {
  font-family: 'Allura';
  color: #fff;
  margin-bottom: 0;
  font-size: 34px;
  margin-top: -20px;
}

.timeline {
  position: relative;
}
.timeline::before {
  content: '';
  background: #C5CAE9;
  width: 5px;
  height: 95%;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
}

.timeline-item {
  width: 100%;
  margin-bottom: 20px;
}
.timeline-item:nth-child(even) .timeline-content {
  float: right;
  padding: 5px 20px;
}
/*.timeline-item:nth-child(even) .timeline-content .date {
  right: auto;
  left: 0;
}*/
.timeline-item:nth-child(even) .timeline-content::after {
  content: '';
  position: absolute;
  border-style: solid;
  width: 0;
  height: 0;
  top: 30px;
  left: -15px;
  border-width: 10px 15px 10px 0;
  border-color: transparent #C5CAE9 transparent transparent;
}
.timeline-item::after {
  content: '';
  display: block;
  clear: both;
}

.timeline-content {
  position: relative;
  width: 45%;
  padding: 5px 20px;
  border-radius: 4px;
  background: #fff;
  border: 1px solid #C5CAE9;
  box-shadow: 0 20px 25px -15px rgba(0, 0, 0, 0.3);
}
.timeline-content::after {
  content: '';
  position: absolute;
  border-style: solid;
  width: 0;
  height: 0;
  top: 30px;
  right: -15px;
  border-width: 10px 0 10px 15px;
  border-color: transparent transparent transparent #C5CAE9;
}

.timeline-img {
  width: 30px;
  height: 30px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
  -webkit-box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 1px 15px rgba(0, 0, 0, 0.23);
  background: #3F51B5;
  border-radius: 50%;
  position: absolute;
  left: 50%;
  margin-top: 25px;
  margin-left: -15px;
}

.bnt-more {
  /*background: #3F51B5;*/
  color: #3F51B5;
  padding: 5px 15px;
  text-transform: uppercase;
  font-size: 14px;
  margin: 10px auto;
  position: absolute;
  /*display: inline-block;*/
  right: 10px;
  bottom: 0px;
  border-radius: 2px;
  border: 1px solid #3F51B5;
  box-shadow: 0 1px 3px -1px rgba(0, 0, 0, 0.6);
}
.bnt-more:hover, .bnt-more:active, .bnt-more:focus {
  background: #32408f;
  color: #FFFFFF;
  text-decoration: none;
}

.timeline-card {
  padding: 0 !important;
}
.timeline-card p {
  padding: 0 20px;
}
.timeline-card .bnt-more {
  margin-left: 20px;
}

.timeline-item .timeline-img-header {
  background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4)), url("https://picsum.photos/1000/800/?random") center center no-repeat;
  /*background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.4));*/
  background-size: cover;
}

.timeline-img-header {
  height: 100px;
  position: relative;
  margin-bottom: 20px;
}

.timeline-img-header h2 {
  color: #32408f;
  position: absolute;
  bottom: 5px;
  left: 20px;
}

blockquote {
  margin-top: 30px;
  color: #757575;
  border-left-color: #3F51B5;
  padding: 0 20px;
}

.date {
    background: #999999;
    display: inline-block;
    color: #FFFFFF;
    font-size: 16px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
    -webkit-box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 1px 15px rgba(0, 0, 0, 0.23);
    padding: 7px;
    position: absolute;
    top: 0;
    right: 0;
}

@media screen and (max-width: 768px) {
  .timeline::before {
    left: 50px;
  }
  .timeline .timeline-img {
    left: 50px;
  }
  .timeline .timeline-content {
    max-width: 100%;
    width: auto;
    margin-left: 70px;
    padding-top: 20px;
  }
  .timeline .timeline-item:nth-child(even) .timeline-content {
    float: none;
  }
  .timeline .timeline-item:nth-child(odd) .timeline-content::after {
    content: '';
    position: absolute;
    border-style: solid;
    width: 0;
    height: 0;
    top: 30px;
    left: -15px;
    border-width: 10px 15px 10px 0;
    border-color: transparent #C5CAE9 transparent transparent;
  }
}
</style>
<!--<div id="map"></div>-->
<?php include __DIR__ . '../../footer.php'; ?>