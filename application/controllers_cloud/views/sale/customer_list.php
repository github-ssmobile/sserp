<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('#filter_btn').click(function (){
           var idbranch = $('#idbranch').val();
//           var branches = $('#branches').val();

            $.ajax({
                url:"<?php echo base_url() ?>Sale/ajax_get_customer_list_byidbranch",
                method:"POST",
                data:{idbranch: idbranch},
                success:function(data)
                {
                    $('#report_data').html(data);
                }
            });
       }); 
    });
</script>
<div class="col-md-10"><center><h3><span class="fa fa-handshake-o fa-lg"></span> Customer</center></div><div class="clearfix"></div><hr>
<div class="" style="padding: 0; overflow: auto">
    <div id="purchase" style="padding: 5px;">
        <?php if($this->session->userdata('level') != 2) { ?>
        <div class="col-md-1"><b>Branch</b></div>
        <div class="col-md-3">
            <select data-placeholder="Select Branches" name="idbranch" id="idbranch" class="chosen-select" required="" style="width: 100%">
                <?php foreach ($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                <?php // $branches[] = $branch->id_branch;
                 } ?>
            </select>
        </div>
        <!--<input type="hidden" name="branches" id="branches" value="<?php // echo implode($branches,',') ?>">-->
        <?php } ?>
        <div class="col-md-1" style="text-align: center;">
            <button type="button"  class="filter_btn btn btn-primary gradient2" id="filter_btn" style="margin-top: 6px;line-height: unset;">Filter</button>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-2">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2 pull-right">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <?php // die('<pre>' . print_r($model_data, 1) . '</pre>');?>
        <!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>-->
        <div class="clearfix"></div><br>
        <div class="thumbnail" style="padding: 0; margin-top: 10px;min-height: 700px">
            <?php if($this->session->userdata('level') != 2){ ?>
            <div id="report_data"></div>
            <?php } else { ?>
            <table id="branch_data" class="table table-condensed table-bordered" style="margin-bottom: 0; font-size: 13px;">
                <thead class="bg-info">
                    <th>Sr</th>
                    <!--<th>Edit</th>-->
                    <th>Customer Name</th>
                    <th>Contact</th>
                    <th>Email Id</th>
                    <th>GSTIN</th>
                    <th>Created At</th>
                    <th>Address</th>
                    <th>Pincode</th>
                    <th>City</th>
                    <th>District</th>
                    <th>State</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($customer_list as $customer){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <!--<td><a href="<?php // echo base_url('Sale/customer_edit_form/'.$customer->id_customer) ?>" class="btn btn-sm btn-primary btn-outline"><i class="fa fa-edit"></i></a></td>-->
                        <td><?php echo $customer->customer_fname.' '.$customer->customer_lname ?></td>
                        <td><?php echo $customer->customer_contact ?></td>
                        <td><?php echo $customer->customer_email ?></td>
                        <td><?php echo $customer->customer_gst ?></td>
                        <td><?php echo $customer->branch_name ?></td>
                        <td><?php echo $customer->customer_address ?></td>
                        <td><?php echo $customer->customer_pincode ?></td>
                        <td><?php echo $customer->customer_city ?></td>
                        <td><?php echo $customer->customer_district ?></td>
                        <td><?php echo $customer->customer_state ?></td>
                        <!--<td><?php // echo date('d-m-Y h:i a', strtotime($customer->entry_time)) ?></td>-->
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>