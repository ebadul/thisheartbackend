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
      <h1>
        User Package
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Primary</a></li>
        <li class="active">User list</li>
      </ol>
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
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 182px;">User Id</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Package</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 199px;">Subs. Date</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px;">Expire</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Edit</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($user_package) : ?>
                  @foreach ( $user_package as $row )
                  <tr role="row" class="odd">


                    <td title="{{$row->user_id}}">{{$row->user['email']}} </td>
                    <td>
                      {{ !empty($row->package_info)?$row->package_info->package:'No package selected'}}
                    </td>
                    <td>{{ $row['subscription_date']}}</td>
                    <td>{{ $row['subscription_expire_date']}}</td>
                    <td class="text-center">
                      <button type="button"  class="btn btn-success activeBtn" user-data="{{$row}} ">
                        <span><i class="fa fa-edit"></i></span>
                        {{ $row['subscription_status']?'Active':'In-active'}}
                      </button>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-info editBtn" user-data="{{$row}} ">
                        <span><i class="fa fa-edit"></i></span> Edit</button>
                    </td>
                    <td class="text-center">
                      <a href="/user_package_delete/{{$row['id']}}" class="btn btn-warning editBtn" onclick="return confirm('Do you want to delete use package id: {{$row['id']}}')">
                        <span><i class="fa fa-remove"></i></span> Delete</a>
                    </td>
                  </tr>
                  @endforeach
                <?php endif; ?>
              </tbody>
              <!-- <tfoot>
                <tr>
                  <th rowspan="1" colspan="1">User Id</th>
                  <th rowspan="1" colspan="1">User Name</th>
                  <th rowspan="1" colspan="1">Email</th>
                  <th rowspan="1" colspan="1">Mobile</th>
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </tfoot> -->
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
            <h4 class="modal-title">Info Modal</h4>
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
              <select class="form-control" id="package_id" required>
                <option>:: Select Package ::</option>
                @foreach($package_list as $package)
                <option value="{{$package->id}}">{{$package->package}}</option>
                @endforeach
              </select>

            </div>
            <div class="form-group">
              <label for="subscription_date">Subscription Date</label>
              <input type="text" class="form-control" id="subscription_date" value="" placeholder="Subscription Date" required>
            </div>
            <div class="form-group">
              <label for="subscription_expire_date">Subscription Expire</label>
              <input type="text" class="form-control" id="subscription_expire_date" value="" placeholder="Expire Subscription" required>
            </div>
            <div class="form-group">
              <label for="subscription_status">Subscription Status</label>
              <select class="form-control" id="subscription_status" required>
                <option value="1">Active</option>
                <option value="0">In-active</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_ok" id="edit_btn" class="btn btn-danger">Update Changes</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

    <!--------------------------------  Edit Modal End --------------------------------------------->


        <!-----------------------------------------  Active/Inactive Modal start ---------------------------------------------->

        <div class="modal modal-info fade" id="modal-active">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Active/In-active blocked user</h4>
              </div>
              <div class="modal-body">
    
                {{csrf_field()}}
                <p>Are you sure, you want to reactive/unblock  this account?</p>
    
    
                <div class="form-group hidden">
                  <label for="user_id">User ID</label>
                  <input type="hidden" class="form-control" id="active_user_package_id" value="" placeholder="User ID" readonly required>
                  <input type="text" class="form-control" id="active_user_id" value="" placeholder="User ID" readonly required>
                </div>
                <div class="form-group hidden ">
                  <label for="package_id">Package</label>
                  <select class="form-control" id="active_package_id" required>
                    <option>:: Select Package ::</option>
                    @foreach($package_list as $package)
                    <option value="{{$package->id}}">{{$package->package}}</option>
                    @endforeach
                  </select>
    
                </div>
                <div class="form-group hidden">
                  <label for="subscription_date">Subscription Date</label>
                  <input type="text" class="form-control" id="active_subscription_date" value="" placeholder="Subscription Date" required>
                </div>
                <div class="form-group hidden">
                  <label for="subscription_expire_date">Subscription Expire</label>
                  <input type="text" class="form-control" id="active_subscription_expire_date" value="" placeholder="Expire Subscription" required>
                </div>
                <div class="form-group ">
                  <label for="subscription_status"></label>
                  <select class="form-control" id="active_subscription_status" required>
                    <option value="1">Active</option>
                    <option value="0">In-active</option>
                  </select>
                </div>
    
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
                <button type="submit" name="edit_ok" id="active_btn" class="btn btn-danger">Update Changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
    
        <!--------------------------------  Active/Inactive Modal End --------------------------------------------->

        
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2020 <a href="https://thisheart.co"> ThisHeart Admin</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- -----------------------------  Active/Deactive Item Ajax Request Start ------------------------------- ---->
<script>
  $(document).ready(function() {
    $('.activeSts').change(function() {
      var userid = $(this).attr('user-id');
      //console.log("Active item on Id :::", userid);
      var status = $(this).prop('checked') == true ? 1 : 0;
      //console.log("Active Status :::", status); 
      $.ajax({
        type: "post",
        dataType: "json",
        url: "./user_status",
        data: {
          'active': status,
          'user_id': userid
        },
        beforeSend: function(xhr, type) {
          if (!type.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          }
        },
        success: function(data) {
          console.log(data.success)
        },
        error: function(error) {
          console.log("error :", error);
        }
      });
    });
  });
</script>

<!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $(document).ready(function() {
    //console.log("Event triggered");
  });

  $(document).on('click', '.activeBtn', function() {
    var user_package = JSON.parse($(this).attr('user-data'));

    $('#active_user_package_id').val(user_package.id);
    $('#active_user_id').val(user_package.user_id);
    $('#active_package_id').val(user_package.package_id);
    $('#active_subscription_date').val(user_package.subscription_date);
    $('#active_subscription_expire_date').val(user_package.subscription_expire_date);
    $('#active_subscription_status').val(user_package.subscription_status);
    $('#modal-active').modal('show');

  });

  $(document).on('click', '.editBtn', function() {
    var user_package = JSON.parse($(this).attr('user-data'));

    $('#user_package_id').val(user_package.id);
    $('#user_id').val(user_package.user_id);
    $('#package_id').val(user_package.package_id);
    $('#subscription_date').val(user_package.subscription_date);
    $('#subscription_expire_date').val(user_package.subscription_expire_date);
    $('#subscription_status').val(user_package.subscription_status);
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
      url: "./user_package_edit",
      dataType: "json",
      data: data,
      method: "post",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn,#active_btn').text('Updating User Item....');
        }
      },
      success: function() {
        $.toast({
          heading: 'Information',
          text: 'Successfully, user package updated!',
          icon: 'info',
          position: 'bottom-right',
          loader: true, // Change it to false to disable loader
          bgColor: '#088' // To change the background
        })
        setTimeout(function() {

          $('#modal-edit').modal('hide');
          $('#modal-active').modal('hide');
          $('#example1').dataTable();

          location.reload(true);
        }, 2000)
      },
      error: function(error) {
        console.log("error :", error);
        $('#modal-edit, #modal-active').modal('hide');
      }
    });
  });

  $('#active_btn').click(function(data) {

var data = {
  user_package_id: $('#active_user_package_id').val(),
  user_id: $('#active_user_id').val(),
  package_id: $('#active_package_id').val(),
  subscription_date: $('#active_subscription_date').val(),
  subscription_expire_date: $('#active_subscription_expire_date').val(),
  subscription_status: $('#active_subscription_status').val()
}

console.log(data);

$.ajax({
  url: "./user_package_edit",
  dataType: "json",
  data: data,
  method: "post",
  beforeSend: function(xhr, type) {
    if (!type.crossDomain) {
      xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
      $('#edit_btn,#active_btn').text('Updating User Item....');
    }
  },
  success: function() {
    $.toast({
      heading: 'Information',
      text: 'Successfully, user package updated!',
      icon: 'info',
      position: 'bottom-right',
      loader: true, // Change it to false to disable loader
      bgColor: '#088' // To change the background
    })
    setTimeout(function() {

      $('#modal-edit').modal('hide');
      $('#modal-active').modal('hide');
      $('#example1').dataTable();

      location.reload(true);
    }, 2000)
  },
  error: function(error) {
    console.log("error :", error);
    $('#modal-edit, #modal-active').modal('hide');
  }
});
});

</script>
<!-- page script -->
<script>
  $(function() {
    $('#example1').dataTable({
      'paging': true,
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