@extends('admin.admin-layout')

@section('content')

<div class="wrapper">
  @include('admin/header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('admin/left-sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Package Info Add
        <a href="/package_info" role='button' class="btn btn-success" style="margin-left:20px"> &nbsp; Package List &nbsp;</a>
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

            <div class="form-group">
              <label for="package">Package Name</label>
              <input type="text" class="form-control" id="package" name="package" value="" placeholder="Package Name" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" value="" placeholder="Description" required></textarea>
            </div>
            <div class="form-group">
              <label for="price">Price</label>
              <input type="text" class="form-control" id="price" name="price" value="" placeholder="Price" required>
            </div>
            <div class="form-group">
              <label for="days">Days</label>
              <input type="text" class="form-control" id="days" name="days" value="" placeholder="Days" required>
            </div>


            <div class="form-group   form-group text-right">
              <input type="submit" class="btn btn-success" id="mobile" value="SAVE">
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
        url: "http://127.0.0.1:8000/user_status",
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
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': true,
      'autoWidth': false
    });
  });
</script>
<!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection