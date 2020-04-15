@extends('admin.admin-layout')

@section('content')

<div class="wrapper">
<header class="main-header">
    <!-- Logo -->
    <a href="dashboard" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>LT</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Admin</b>This Heart</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <!-- Notifications: style can be found in dropdown.less -->

          <!-- Tasks: style can be found in dropdown.less -->

          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{asset('AdminLTE/dist/img/userphoto.jpg')}}" class="user-image" alt="User Image">
            <span class="hidden-xs">{{$user->email}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{asset('AdminLTE/dist/img/userphoto.jpg')}}" class="img-circle" alt="User Image">

                <p>
                  Admin This Heart - Web Developer
                  <small>Member since Nov. 2012</small>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>
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
            <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="dataTables_length" id="example1_length">
                      <label>Show 
                      <select name="example1_length" aria-controls="example1" class="form-control input-sm">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      </select> entries
                      </label>
                    </div>
                  </div>
              <div class="col-sm-6">
              <div id="example1_filter" class="dataTables_filter">
                <label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="example1"></label>
              </div>
              </div>
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
                <th  tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                <th  tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Edit</th>
                <th  tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Delete</th>
                </tr>
                </thead>
                <tbody>
               <?php if( $user_package ):?>
                @foreach ( $user_package  as $row )
                <tr role="row" class="odd">
                  
 
                   <td>{{ $row['user_id']}}</td>
                   <td>
                        
                       {{ $row->package_info->package}}
                       
                    </td>
                   <td>{{ $row['subscription_date']}}</td>
                   <td>{{ $row['subscription_expire_date']}}</td>
                   <td>{{ $row['subscription_status']}}</td>
                   <td class="text-center">
                    <button type="button" class="btn btn-info editBtn" user-data="{{$row}} ">
                      <span><i class="fa fa-edit"></i></span> Edit</button>
                  </td>
                  <td class="text-center"  >
                    <a href="/user_package_delete/{{$row['id']}}" 
                      class="btn btn-warning editBtn" 
                      onclick="return confirm('Do you want to delete use package id: {{$row['id']}}')">
                      <span><i class="fa fa-remove"></i></span> Delete</a>
                  </td>
                </tr>
                @endforeach
              <?php endif;?>
                </tbody>
                <tfoot>
                <tr><th rowspan="1" colspan="1">User Id</th><th rowspan="1" colspan="1">User Name</th><th rowspan="1" colspan="1">Email</th><th rowspan="1" colspan="1">Mobile</th> <th>Action</th> <th>Status</th></tr>
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
                  <input type="text" class="form-control" id="package_id" value="" placeholder="Enter Package ID" required>
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


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2019 <a href="https://thisheart.com"> This Heart Admin</a>.</strong> All rights
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
      var userid = $(this).attr('user-id') ;
     //console.log("Active item on Id :::", userid);
      var status = $(this).prop('checked') == true ? 1 : 0; 
      //console.log("Active Status :::", status); 
        $.ajax({
            type: "post",
            dataType: "json",
            url: "./user_status", 
            data: {'active': status, 'user_id': userid},
            beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        }
        },
            success: function(data){
              console.log(data.success)
            },
            error:function(error){
            console.log("error :", error);
      }  
        });
    });
  });
</script>

  <!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


 <!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
 
 $(document).ready( function(){
     //console.log("Event triggered");
   });

   $(document).on('click', '.editBtn', function(){
     var user_package = JSON.parse($(this).attr('user-data')) ;
 
     $('#user_package_id').val(user_package.id);
     $('#user_id').val(user_package.user_id);
     $('#package_id').val(user_package.package_id);
     $('#subscription_date').val(user_package.subscription_date);
     $('#subscription_expire_date').val(user_package.subscription_expire_date);
     $('#subscription_status').val(user_package.subscription_status);
     $('#modal-edit').modal('show');
     
   });

   $('#edit_btn').click(function(data){
 
     var data = {
      user_package_id:$('#user_package_id').val(),
      user_id:$('#user_id').val(),
      package_id:$('#package_id').val(),
      subscription_date:$('#subscription_date').val(),
      subscription_expire_date:$('#subscription_expire_date').val(),
      subscription_status:$('#subscription_status').val()
     }


     $.ajax({
       url:"./user_package_edit", 
       dataType: "json",
       data:data,
       method:"post",
       beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
            $('#edit_btn').text('Updating User Item....');  
        }
    },
       success:function(){
         setTimeout(function(){
         
          $('#modal-edit').modal('hide');
          $('#example1').dataTable();

          location.reload(true);
         }, 2000)
       },
      error:function(error){
        console.log("error :", error);
        $('#modal-edit').modal('hide');
      }  
     });
   });
 </script>
  <!-- page script -->
  <script>
  $(function () {
    $('#example1').dataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    });
  });
</script>
  <!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection
