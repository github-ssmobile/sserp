<?php include __DIR__.'../../header.php'; ?>
        
<center><h3><span class="mdi mdi-cash-multiple fa-lg"></span>  Price Change Report</h3></center>
        
    <?php if( $save = $this->session->flashdata('save_data')): ?>
        <div class="alert alert-dismissible alert-success" id="alert-dismiss">
            <?= $save ?>
        </div>
    <?php endif; ?>

<script>
    $(document).ready(function(){
            $('#product_category').change(function () {
                    var product_category = $('#product_category').val();
                    var type_name = $('#product_category option:selected').text();
                    $("#product_category_name").val(type_name);

                    $.ajax({
                        url: "<?php echo base_url() ?>Catalogue/ajax_get_category_by_product_category",
                        method: "POST",
                        data: {product_category: product_category},
                        success: function (data)
                        {
                            $("#category").html(data);
                            $("#category").trigger("chosen:updated");

                        }
                    });
                });
                
                 $('#category,#idbrand,#product_category,#dateto').change(function () {


                  var product_category = 0;
                if ($('#product_category').val()) {
                    product_category = $('#product_category').val();
                }
                var category = 0;
                if ($('#category').val()) {
                    category = $('#category').val();
                }
                var brand = 0;
                if ($('#idbrand').val()) {
                    brand = $('#idbrand').val();
                }
                var from = '';
                if ($('#datefrom').val()) {
                    from = $('#datefrom').val();
                }
                var to = '';
                if ($('#dateto').val()) {
                    to = $('#dateto').val();
                }
                
                if(to != '' && from == '' || from != '' && to == ''){
                    alert('Both dates need to select');
                    return false;
                }
                
                    $.ajax({
                        url: "<?php echo base_url() ?>Pricing/ajax_get_price_change_report",
                        method: "POST",
                        data: {category: category, brand: brand, product_category: product_category, from: from, to: to},
                        success: function (data)
                        {
                            $('#price_data').html(data);
                        }
                    });
                
            });
                
    });
</script>

    <div class="thumbnail" style="padding: 0; margin: 0; min-height: 700px;">
    <div id="purchase" style="padding: 20px 10px;">        
        
        <div class="col-md-3">
            <select class="chosen-select form-control" name="product_category" id="product_category" required="">
                            <option value="">Select Product Category</option>
                            <?php foreach ($product_category as $type) { ?>
                                <option value="<?php echo $type->id_product_category; ?>"><?php echo $type->product_category_name; ?></option>
                            <?php } ?>
                        </select>
        </div>
        <div class="col-md-2">
            <select class="chosen-select  form-control" id="category" >
                <option id="0">Select Category</option>
               
            </select>
        </div>
        
        <div class="col-md-2">
            <select class="chosen-select form-control" name="idbrand" id="idbrand" required="">
                <option value="">Select Brand</option>
                <?php foreach ($brand_data as $brand) { ?>
                    <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
                <?php } ?>
            </select>
        </div>
        
        <div class="col-md-4">            
                <div class="input-group-btn">
                    <input type="text" name="datefrom" id="datefrom" class="form-control datepick" placeholder="From Date">
                </div>
                <div class="input-group-btn">
                    <input type="text" name="dateto" id="dateto" class="form-control datepick" placeholder="To Date">
                </div>            
        </div>
        
        <div class="clearfix"></div><hr>
        
        
        
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
            <div class="col-md-5">
                <div id="count_1" class="text-info"></div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('price_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
            </div>
            <div class="clearfix"></div>
            <table id="price_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
                <thead>
                    <th>Sr</th>
                    <th>Product Category</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>MRP</th>
                    <th>MOP/Customer</th>
                    <th>Salesman</th>
                    <th>Landing</th>
                    <th>Online Price</th>
                    <th>Updated Time</th>                    
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($price_data as $price){ ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $price->product_category_name; ?></td>
                        <td><?php echo $price->category_name; ?></td>
                        <td><?php echo $price->brand_name; ?></td>
                        <td><?php echo $price->full_name; ?></td>
                        <td><?php echo $price->mrp; ?></td>
                        <td><?php echo $price->mop; ?></td>
                        <td><?php echo $price->salesman_price; ?></td>
                        <td><?php echo $price->landing; ?></td>
                        <td><?php echo $price->ponline_price; ?></td>
                        <td><?php echo $price->entry_time; ?></td>
                        
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>
<?php include __DIR__.'../../footer.php'; ?>