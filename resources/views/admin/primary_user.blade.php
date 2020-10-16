@extends('admin.admin-layout')

@section('content')
@if(Auth::check())

@else 
<script>window.location = "{{ route('login') }}";</script>
<?php exit; ?>
@endif
<div class="wrapper">
  @include('admin/header')
  @include('admin/left-sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Primary User List
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
                  <div class="col-sm-6">
                   
                  </div>
              <div class="col-sm-6">
            
              </div>
              </div>
              <div class="row">
              <div class="col-sm-12">
              <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                  <tr role="row">
                      <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">User ID</th>
                      <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">User Full Name</th>
                      <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 199px;">Email</th>
                      <th aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px;">Mobile</th>
                      <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                      {{-- <th style="text-align:center;">Action</th> --}}
                  </tr>
                </thead>
                <tbody>
               <?php if( $primary_accounts ):?>
                @foreach ( $primary_accounts  as $row )
                <tr role="row" class="odd">
                    <td>{{ $row['id']}}</td>
                    <td>{{ Crypt::decryptString($row['name'])}}</td>
                    <td>{{ $row['email']}}</td>
                    <td>{{ $row['mobile']}}</td>
                    {{-- <td>
                      <button type="button" class="btn btn-block btn-info editBtn" user-data="{{$row['id'] .'='. $row['name'] .'='. $row['email'] .'='. $row['mobile']}} "><span><i class="fa fa-edit"></i></span> Edit</button>
                    </td>  --}}
                    <td class="text-center"  >
                      <input class="activeSts checkbox" user-id="{{$row['id']}}"  
                      type="checkbox" {{$row["active"] ? "checked" : ""}} 
                      data-toggle="toggle" data-onstyle="success" 
                      data-offstyle="danger" 
                      data-on="Active" data-off="Deactive"/> 
                    </td>
                    {{-- <td class="text-center">
                      <a href="/delete_primary_user/{{$row['id']}}" class="btn btn-danger" user-id="{{$row['id']}}" type="button" data-on="Active" data-off="InActive" onclick="return confirm('Do you want to delete user data')">Delete</a>
                    </td> --}}
                </tr>
                @endforeach
              <?php endif;?>
                </tbody>
                <tfoot>
                  
                </tfoot>
              </table>
              </div>
              </div>
              <div class="row"><div class="col-sm-5">
              </div>
              <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
              </div>
              </div>
              </div>
              </div>
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
            <form role="form" id="editForm" name="edit">
              {{csrf_field()}}
              <input type="hidden" name="editBtn" id="editUserId" value="" />

              <div class="form-group">
                <label for="uname">User Name</label>
                <input type="text" class="form-control" id="username" value="" placeholder="User Name">
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" id="email" value="" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="text" class="form-control" id="mobile" value="" placeholder="Mobile">
              </div>
            </form>
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
      console.log("Active item on Id :::", userid);
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
          $.toast({
            heading: 'Information',
            text: 'Successfully, primary user status changed!',
            icon: 'info',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#B0BF1A' // To change the background
          })
        },
        error: function(error) {
          $.toast({
            heading: 'Information',
            text: 'Sorry, user status not changed!',
            icon: 'error',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#FF6A4D' // To change the background
          })
          console.log("error from server :", error.response.message);
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

  $(document).on('click', '.editBtn', function() {
    var userdata = $(this).attr('user-data');
    console.log("Edit item on Id :::", userdata);
    var user_edit = userdata.split('=');
    $('#editUserId').val(user_edit[0]);
    $('#username').val(user_edit[1]);
    $('#email').val(user_edit[2]);
    $('#mobile').val(user_edit[3]);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {
    var user_id = $('#editUserId').val();
    console.log("user id edit:>>>>", user_id);
    var data = {
      user_id: $('#editUserId').val(),
      user_name: $('#username').val(),
      email: $('#email').val(),
      mobile: $('#mobile').val()
    }

    console.log("Item edit data on:::", data);

    $.ajax({
      url: "http://127.0.0.1:8000/primary_user_edit",
      dataType: "json",
      data: data,
      method: "post",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn').text('Updating User Item....');
        }
      },
      success: function() {
        setTimeout(function() {
          console.log(user_id);
          $('#modal-edit').modal('hide');
          $('#example1').dataTable();

          location.reload();
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
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      
    });
  });
</script>
<!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection