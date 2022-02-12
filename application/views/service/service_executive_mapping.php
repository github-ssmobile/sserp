<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>


<div class="col-md-10"><center><h3><span class="mdi mdi-chemical-weapon"></span> Map Service Executive</h3></center></div>
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
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('executive_mapping_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <div class="clearfix"></div><br>
              <table id="executive_mapping_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead style="background-color: #99ccff">
                    <th>Sr</th>
                    <th>Zone Name</th>
                    <th>Branch Name</th>
                    <th>Executive Name</th>                    
                    <th>Action</th>
                </thead>
                <tbody class="data_1">
                    <?php if(count($branch_data)>0){ $i=1; foreach ($branch_data as $odata){ ?>
                    <tr class="<?php echo $odata->id_branch; ?>">
                        <td><?php echo $i++;?></td>
                        <td class="ssku"><?php echo $odata->zone_name; ?></td>
                        <td><?php echo $odata->branch_name; ?></td>                        
                        <td class="ssmodel">
                            <input type="hidden"  name="idbranch" id="idbranch" value="<?php echo $odata->id_branch ?>" />
                            <input type="hidden"  name="branch_name" id="branch_name" value="<?php echo $odata->branch_name ?>" /> 
                            
                        <?php $keys=multi_array_search($branch_data, array('idservice_executive' => $odata->idservice_executive)); 
                        if(count($keys)>0){ ?>
                        <?php echo $branch_data[$keys[0]]->user_name; ?>   
                            <input type="hidden"  name="user_name" id="user_name" value="<?php echo $branch_data[$keys[0]]->user_name ?>" />                                                       
                        <?php }else{ ?>
                            <input type="hidden"  name="user_name" id="user_name" value="" />                         
                        <?php }?>
                        </td>    
                        <td>
                            <a class="btn btn-sm btn-info gradient_info waves-effect waves-light edit_bfl"  style="margin: 0" >
                            Map Executive
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
                            <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> MAP Executive </h4></center><hr>
                            <label class="col-md-4">Branch Name :- </label>
                            <div class="col-md-8 code" style="width: auto">                                                    
                            </div>
                            <div class="clearfix"></div><br>   
                            <label class="col-md-4">Select Executive :- </label>
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
                var branch_name = $(ce).closest('td').parent('tr').find("#branch_name").val();
                var idbranch = $(ce).closest('td').parent('tr').find("#idbranch").val();                
                $(".code").html('<label class="" style="word-wrap: anywhere;">'+branch_name+'</label>'); 
                $.ajax({
                        url: "<?php echo base_url() ?>Service/ajax_service_executive",
                        method: "POST",    
                        data: {idbranch: idbranch},
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
            var idbranch=$('input[name="id_branch"]').val();
            alert(idbranch);
            var parentDiv = $("."+idbranch);
            var $form = $(this);
            var fd = new FormData();
            fd.append("idservice_executive", $('select[name="idservice_executive"]').val());
            fd.append("idbranch", $('input[name="id_branch"]').val());  
            var service_executive=$("select[name='idservice_executive'] option:selected").text();
            fd.append("is_ajax", "yes");
    
               jQuery.ajax({
                    url: "<?php echo base_url('Service/update_service_executive') ?>",
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    method: 'POST',
                    success: function (data, textStatus, jqXHR) {
                        if (data.result == 'yes') {
                             $(parentDiv).find('.ssmodel').html(service_executive);
                            $('#edit_bfl').modal('hide');
                            $(parentDiv).css("background", "#e6ffc0");
                            alert("Service Executive updated successfully!");                       
                            setTimeout(function () {
                                $(parentDiv).css("background", "#fff");
                            }, 500)
                        } else {
                            $(parentDiv).css("background", "#fdb4b4");
                            alert("Fail to update Service Executive!");
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