@extends('admin.admin-layout')

@section('content')
@if(Auth::check())

@else 
<script>window.location = "{{ route('login') }}";</script>
<?php exit; ?>
@endif

<div class="wrapper">
  @include('admin/header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('admin/left-sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
          <div class="col-md-4">
            <span class="h1">Billing Details</span class="h1">
          </div>
          <div class="col-md-2">
            
          </div>
          <div class="col-md-3">
            <span class="form-group">
              {{-- <select class="select select2 form-control" id="select_subscribed_status">
                <option value="">::Select Status::</option>
                <option value="0">Unsubscribed</option>
                <option value="1">Subscribed</option>
                
              </select> --}}
            </span>
          </div>
          <div class="col-md-3">
            <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Primary</a></li>
              <li class="active">User list</li>
            </ol>
          </div>
        </div>
  
      </section>

    <!-- Main content -->
    <div class="box-body">

      @if ($message = Session::get('warning'))
      <div class="alert alert-warning alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
      </div>
      @endif
      @if ($message = Session::get('success'))
      <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
      </div>
      @endif
      <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">


        </div>
        <div class="row">
          <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row">
                
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 182px;">Bill Id</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Email</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Package</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Billing Cost</th>
                  
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 199px;">Billing Month</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Payment Type</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Billing Date</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Next Billing Date</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Make Payment</th>
                  
                
                </tr>
              </thead>
              <tbody>
                <?php if ($billing_list) : ?>
                  @foreach ( $billing_list as $row )
                  <tr role="row" class="odd">
                    <td title="{{$row->id}}">{{$row->id}}</td>
                    <td title="{{$row->user_id}}">{{$row->user->email}}</td>
                    <td title="{{$row->user_id}}" class="text-center">
                        {{$row->user->user_package->package_info->package}}</td>
                    <td class="text-center">$ {{ $row['package_cost']}}</td>
                    <td>{{ $row['billing_month']}}</td>
                    <td title="{{ $row['recurring_type']}}">{{ $row['payment_type']}}</td>
                    <td>{{ $row['billing_date']}}</td>
                    <td>{{ $row['next_billing_date']}}</td>
                    <td class="text-center">
                        <span title="{{'Bill start : '.$row['billing_start_date'].', '}}
{{'Bill end : '.$row['billing_end_date'].', '}}
{{'Process times : '.$row['payment_process_times'].', '}}
{{'Status : '.($row['paid_status']?"Paid":"Unpaid").', '}}
{{'Process : '.$row['process_stauts'].', '}}
{{'Updated : '.$row['updated_at'].', '}}
{{'Job id : '.$row['cron_payment_charging_id']}}">More...</span>
                        
                    </td>
                    <td class="text-center">
                    <button type="button" class="btn btn-success btnCharging" billing-details-id="{{$row->id}}" {{ $row['paid_status']?'disabled':'Charging'}}>
                         <i class="fa fa-spinner fa-spin hidden"></i>
                        {{ $row['paid_status']?'Charging':'Charging'}}</button>
                    </td>
                    
                 
                  </tr>
                  @endforeach
                <?php endif; ?>
              </tbody>
            
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-----------------------------------------  Edit Modal start ---------------------------------------------->

    <div class="modal modal-info fade" id="modal-edit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Unsubscribe User</h4>
          </div>
          <div class="modal-body">

            {{csrf_field()}}


            <div class="form-group">
              <label for="user_id">User ID</label>
              <input type="hidden" class="form-control" id="user_package_id" value="" placeholder="User ID" readonly required>
              <input type="text" class="form-control" id="user_id" value="" placeholder="User ID" readonly required>
            </div>
            <div class="form-group">
              <label for="package_id">Package</label>
              <select class="form-control" id="package_id" readonly required>
               
                @foreach($package_list as $package)
                    <option {{strtolower($package->package)===strtolower("FREE ACCOUNT")?'selected':'disabled'}} value="{{$package->id}}">{{$package->package}}</option>
                @endforeach
              </select>

         
            
              <input type="hidden" class="form-control" id="subscription_date" value="" placeholder="Subscription Date" required>
            
              <input type="hidden" class="form-control" id="subscription_expire_date" value="" placeholder="Expire Subscription" required>
        
            <div class="form-group">
              <label for="subscription_status">Subscription Status</label>
              <select class="form-control" id="subscription_status" readonly required>
                <option selected value="2">Pending</option>
                <option value="1" disabled>Active</option>
                <option value="0" disabled>In-active</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_ok" id="edit_btn" class="btn btn-danger">Confirm Requested</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

    <!--------------------------------  Edit Modal End --------------------------------------------->


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 

  <!-- Control Sidebar -->

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- -----------------------------  Active/Deactive Item Ajax Request Start ------------------------------- ---->
<script>
  $(document).ready(function() {
    $('.btnCharging').click(function() {
      var billing_details_id = $(this).attr('billing-details-id');
      //console.log("Active item on Id :::", billing_details_id);
      //console.log("Active Status :::", status); 
      var faspinner = $(this).find('i.fa').removeClass('hidden');
      faspinner.removeClass('hidden');
      $.ajax({
        type: "post",
        dataType: "json",
        url: "./admin_payment_charging",
        data: {
          billing_details_id: billing_details_id
        },
        beforeSend: function(xhr, type) {
          if (!type.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          }
        },
        success: function(data) {
          console.log('success:',data)
          $.toast({
          heading: 'Information',
          text: 'Successfully, payment is updated!',
          icon: 'info',
          position: 'bottom-right',
          loader: true, // Change it to false to disable loader
          bgColor: '#088' // To change the background
        })
         faspinner.addClass('hidden');
          location.reload(true);
        },
        error: function(error) {
          console.log("error :", error);
         faspinner.addClass('hidden');
          $.toast({
            heading: 'Information',
            text: 'Sorry, '+error.responseText,
            icon: 'error',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#FF6A4D' // To change the background
          })
          
        }
      });
    });


    $("#select_subscribed_status").on('change', function() {
      var subscribed_status = $(this).val();
      if (subscribed_status > 0) {
        window.location = '/unsubscribed_user/' + subscribed_status;
      } else {
        window.location = '/unsubscribed_user/0';
      }
    })

  });
</script>

<!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $(document).ready(function() {
    //console.log("Event triggered");
  });
  $('#package_id, #subscription_status').css('pointer-events','none');
  $(document).on('click', '.editBtn', function() {
    var user_package = JSON.parse($(this).attr('user-data'));

    $('#user_package_id').val(user_package.id);
    $('#user_id').val(user_package.user_id);
    // $('#package_id').val(user_package.package_id);
    $('#subscription_date').val(user_package.subscription_date);
    $('#subscription_expire_date').val(user_package.subscription_expire_date);
    // $('#subscription_status').val(user_package.subscription_status);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {

    var data = {
      user_package_id: $('#user_package_id').val(),
      user_id: $('#user_id').val(),
      package_id: $('#package_id').val(),
      subscription_date: $('#subscription_date').val(),
      subscription_expire_date: $('#subscription_expire_date').val(),
      subscription_status: $('#subscription_status').val()
    }


    $.ajax({
      url: "/free_user_package_edit",
      dataType: "json",
      data: data,
      method: "POST",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn').text('Confirmed Free Account....');
        }
      },
      success: function() {
        $.toast({
          heading: 'Information',
          text: 'Successfully, free account request updated!',
          icon: 'info',
          position: 'bottom-right',
          loader: true, // Change it to false to disable loader
          bgColor: '#088' // To change the background
        })
        setTimeout(function() {

          $('#modal-edit').modal('hide');
          $('#example1').dataTable();

          location.reload(true);
        }, 2000)
      },
      error: function(error) {
        console.log("error :", error);
        $('#modal-edit').modal('hide');
      }
    });
  });
</script>
<!-- page script -->
<script>
  $(function() {
    $('#example1').dataTable({
      'paging': true,
      "lengthMenu": [ 25, 50, 100, 150 ],
      "pageLength":50,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'info': true,
      'autoWidth': false
    });
  });
</script>
<!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection