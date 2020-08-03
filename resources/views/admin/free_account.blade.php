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
            <span class="h1">Free Account</span class="h1">
          </div>
          <div class="col-md-2">
            
          </div>
          <div class="col-md-3">
            <span class="form-group">
              <select class="select select2 form-control" id="select_package">
                <option value="">:: Select Package ::</option>
                @foreach($package_list as $package)
                <option value="{{$package->id}}">{{$package->package}}</option>
                @endforeach
              </select>
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
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 182px;">User Id</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Email</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Package</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 199px;">Subs. Date</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Edit</th>
                
                </tr>
              </thead>
              <tbody>
                <?php if ($user_package) : ?>
                  @foreach ( $user_package as $row )
                  <tr role="row" class="odd">
                    <td title="{{$row->user_id}}">{{$row->user_id}} </td>
                    <td title="{{$row->user_id}}">{{$row->user['email']}}</td>
                    <td>
                      {{ !empty($row->package_info)?$row->package_info->package:'No package selected'}}
                    </td>
                    <td>{{ $row['subscription_date']}}</td>
                    <td class="text-center">
                        <?php
                            if($row['subscription_status']==="0"){
                                echo "Inactive";
                            }elseif($row['subscription_status']==="1"){
                                echo "Actived";
                            }elseif($row['subscription_status']==="2"){
                                echo "Pending";
                            }
                        ?>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-info editBtn" user-data="{{$row}} ">
                        <span><i class="fa fa-edit"></i></span> Request for free</button>
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
            <h4 class="modal-title">Free Account</h4>
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


    $("#select_package").on('change', function() {
      var txt_package = $(this).val();
      if (txt_package > 0) {
        window.location = '/free_account/' + txt_package;
      } else {
        window.location = '/free_account';
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