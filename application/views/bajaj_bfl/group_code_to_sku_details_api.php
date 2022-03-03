<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>


<div class="col-md-10"><center><h3><span class="mdi mdi-chemical-weapon"></span> Bajaj SKU COdes</h3></center></div>
    <div class="clearfix"></div><hr>
    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
        <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
            
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('babaj_mapping_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <div class="clearfix"></div><br>
              <table id="babaj_mapping_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead style="background-color: #99ccff">
                    <th>Sr</th>
                    <th>BFL SKU</th>
                    <th>MOP</th>
                    <th>MRP</th>
                    <th>SS Model</th>
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php if($sku_data->message=='success'){ $i=1;
                    $skudata =$sku_data->data;
                    foreach ($skudata as $odata){ ?>
                    <tr class="<?php echo $odata->sku; ?>">
                        <td><?php echo $i++;?></td>
                        <td class="ssku"><?php echo $odata->sku; ?></td>
                        <td><?php echo $odata->MOP; ?></td>
                        <td><?php echo $odata->MRP; ?></td>  
                        <td class="ssmodel">
                        <?php $keys=multi_array_search($variants, array($sku_column => $odata->sku)); 
                        if(count($keys)>0){ ?>
                        <?php echo $variants[$keys[0]]->full_name; ?>   
                            <input type="hidden"  name="full_name" id="full_name" value="<?php echo $variants[$keys[0]]->full_name ?>" />
                            <input type="hidden"  name="idvariant" id="idvariant" value="<?php echo $variants[$keys[0]]->id_variant ?>" />
                            <input type="hidden"  name="sku" id="sku" value="<?php echo $odata->sku ?>" />                            
                        <?php }else{ ?>
                            <input type="hidden"  name="full_name" id="full_name" value="" />
                            <input type="hidden"  name="idvariant" id="idvariant" value="" />
                            <input type="hidden"  name="sku" id="sku" value="<?php echo $odata->sku ?>" />
                        <?php } ?>
                        </td>    
                        <td>
                            <a class="btn btn-sm btn-info gradient_info waves-effect waves-light edit_bfl"  style="margin: 0" >
                            Map Model
                            </a>
                        </td>
                    </tr>
                    <?php  } 
                    }?>
                </tbody>
            </table>
        </div>
    </div>
        <div class="modal" id="edit_bfl" style="z-index: 999999; display: none;" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form>
                    <div class="modal-body">
                        <div class="thumbnail">
                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> MAP SS Model </h4></center><hr>
                            <label class="col-md-4">Bajaj SKU Code :- </label>
                            <div class="col-md-8 code" style="width: auto">                                                    
                            </div>
                            <div class="clearfix"></div><br>   
                            <label class="col-md-4">Select Model :- </label>
                            <div class="col-md-8 models">                                                    
                            </div>
                            <div class="clearfix"></div><br>                                            
                         </div>
                        <a href="#edit_bfl" class="clo-se pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                       <button type="submit"  name="id"  class=" save btn btn-info pull-right waves-effect"><span class=""></span> Save</button>            <!--   formmethod="POST" formaction="<?php // echo base_url('Bfl_Api/save_sku_update') ?>"                               -->
                        <div class="clearfix"></div><br>
                    </div>
                    </form>
                    
                </div>
            </div>
        </div>
        <script>
    
        $(document).ready(function () {         
            $(document).on("click", ".edit_bfl", function (event) {                   
                var ce = $(this);
                var sku = $(ce).closest('td').parent('tr').find("#sku").val();
                $(".code").html('<label class="" style="word-wrap: anywhere;">'+sku+'</label>'); 
                $.ajax({
                        url: "<?php echo base_url() ?>Bfl_Api/ajax_variants",
                        method: "POST",    
                        data: {sku: sku},
                        success: function (data)
                        {
                            $(".models").html(data);                        
                            $(".chosen-select").chosen({search_contains: true});
                        }
                    });    
                    $('#edit_bfl').modal('show');
                $(".chosen-select").chosen({search_contains: true});
            });
        
        $(document).on("click", ".clo-se", function(event) {  
            $('#edit_bfl').modal('hide');
        });
        
        $(document).on("click", ".save", function(event) {         
            event.preventDefault();
            var sku=$('input[name="skucode"]').val();
            var parentDiv = $("."+sku);
            var $form = $(this);
            var fd = new FormData();
            fd.append("model", $('select[name="model"]').val());
            fd.append("sku", $('input[name="skucode"]').val());  
            var ssmodel=$("select[name='model'] option:selected").text();
            fd.append("is_ajax", "yes");
    
               jQuery.ajax({
                    url: "<?php echo base_url('Bfl_Api/save_sku_update') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    method: 'POST',
                    success: function (data, textStatus, jqXHR) {
                        if (data.result == 'yes') {
                             $(parentDiv).find('.ssmodel').html(ssmodel);
                            $('#edit_bfl').modal('hide');
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("SKU updated successfully!");                       
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 500)
                        } else {
                            $(parentDiv).css("background", "#fdb4b4");
                            alert("Fail to update SKU!");
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 500)
                        }                    
                    }
            });        
        });
        
        });
        </script>
<?php include __DIR__.'../../footer.php'; ?>