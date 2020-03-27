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
              <img src="AdminLTE/dist/img/userphoto.jpg" class="user-image" alt="User Image">
            <span class="hidden-xs">{{$user->email}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="AdminLTE/dist/img/userphoto.jpg" class="img-circle" alt="User Image">

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
        Package Entities Add
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Primary</a></li>
        <li class="active">User list</li>
      </ol>
    </section>
    <hr>
    <!-- Main content -->
    <div class="box-body">
        
            <div class="row">
                <div class="col-sm-6 col-md-push-3">
                    <form role="form" id="addForm" action="" method="post" name="addPackageEntity">
                        {{csrf_field()}}
                        <div class=" col-sm-12 form-group">
                            <label for="uname">Package</label>
                            <select name="txtPackageID" required class="select select2 form-control">
                              <option value="">:: Package ::</option>
                              @foreach($package_list as $package)
                                  <option value="{{$package->id}}">{{$package->package}}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class=" col-sm-12 form-group">
                            <label for="email">Entity</label>
                            <select name="txtEntityID" required class="select select2 form-control">
                              <option value="">:: Entity ::</option>
                              @foreach($entity_list as $entity)
                              <option value="{{$entity->id}}">{{$entity->package_entity_title}}</option>
                          @endforeach
                            </select>
                        </div>
                        <div class=" col-sm-12 form-group">
                            <label for="email">Entity Value</label>
                            <input type="text" name="entity_value" required class="form-control" id="entity_value" value="" required placeholder="Entity Value">
                        </div>
                        <div class="form-group col-sm-12 form-group">
                            <input type="submit" class="btn btn-success" id="mobile" value="SAVE" >
                        </div>
                    </form>
                </div>
            
            </div><!-- end row -->
            
        
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
            url: "http://127.0.0.1:8000/user_status", 
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
     var userdata = $(this).attr('user-data') ;
     console.log("Edit item on Id :::", userdata);
     var user_edit= userdata.split('=');
     $('#editUserId').val(user_edit[0]);
     $('#username').val(user_edit[1]);
     $('#email').val(user_edit[2]);
     $('#mobile').val(user_edit[3]);
     $('#modal-edit').modal('show');
     
   });

   $('#edit_btn').click(function(data){
     var user_id = $('#editUserId').val();
     console.log("user id edit:>>>>", user_id);
     var data = {
       user_id:$('#editUserId').val(),
       user_name:$('#username').val(),
       email:$('#email').val(),
       mobile:$('#mobile').val()
     }
        
         console.log("Item edit data on:::", data);

     $.ajax({
       url:"http://127.0.0.1:8000/primary_user_edit", 
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
          console.log(user_id);
          $('#modal-edit').modal('hide');
          $('#example1').dataTable();

          location.reload();
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
