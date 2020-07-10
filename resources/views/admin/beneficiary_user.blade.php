@extends('admin.admin-layout')

@section('content')
@if(Auth::user())

@else 
<script>window.location = "{{ route('login') }}";</script>
<?php exit; ?>
@endif

<div class="wrapper">
  @include('admin/header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('admin/left-sidebar')
  <!-- <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column descending" style="width: 224px;" aria-sort="ascending">Browser</th> -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Beneficiary User List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Beneficiary</a></li>
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
      <table id="example1" class="table table-bordered table-striped ">
        <thead>
          <tr>

            <th rowspan="1" colspan="1">Beneficiary User ID</th>
            <th rowspan="1" colspan="1">Beneficiary User Name</th>
            <th rowspan="1" colspan="1">Email</th>
            
            <th>Action</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($beneficiary_accounts) : ?>
            @foreach ( $beneficiary_accounts as $row )
            <tr role="row" class="odd">
              <td>{{ $row['id']}}</td>
              <td>{{ Crypt::decryptString($row['name'])}}</td>
              <td>{{ $row['email']}}</td>
              
                  <td class="text-center">
                    <input class="activeSts checkbox" user-id="{{$row['id']}}" 
                      type="checkbox" {{$row["active"] ? "checked" : ""}} 
                      data-toggle="toggle" data-onstyle="success" 
                      data-offstyle="danger" data-on="Active" data-off="Deactive"/> 
                  </td>     
                  <td class="text-center" >
                    <a href="{{url("/delete_beneficiary_user/".$row['id'])}}" 
                      class="btn btn-block btn-danger" 
                      role="button" 
                      onclick="return confirm('Do you want to delete beneficiary user data')" alt="Delete Beneficiary User">
                      Delete</a>  
                    </td>         
                </tr>
                @endforeach
              <?php endif;?>
                </tbody>
                <tfoot>

                </tfoot>
              </table>
              
         
            </div>


    <!-- /.content -->
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
                  <label for="uid">User ID</label>
                  <input type="text" class="form-control" id="userid" value="" placeholder="user Id" readonly>
                </div>
                <div class="form-group">
                  <label for="beneid">Beneficiary by (Primary User ID)</label>
                  <input type="text" class="form-control" id="bnuserid" value="" placeholder="Beneficiary Id" readonly>
                </div>
               
            </form>
              </div>
              <div class="form-group">
                <label for="beneid">Beneficiary ID</label>
                <input type="text" class="form-control" id="bnuserid" value="" placeholder="Beneficiary Id">
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
      console.log("Active Status :::", status);
      $.ajax({
        type: "post",
        dataType: "json",
        url: "/bnuser_status",
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
          console.log(data.success);
          $.toast({
            heading: 'Information',
            text: 'Successfully, beneficiary user status changed!',
            icon: 'info',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#B0BF1A' // To change the background
          })
        },
        error: function(error) {
          console.log("error :", error);
          $.toast({
            heading: 'Information',
            text: 'Sorry, beneficiary user status not changed!',
            icon: 'error',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#FF6A4D' // To change the background
          })
        }
      });
    });
  });
</script>

<!--------------------------------- Active/Deactive Item Ajax Request End ----------------------------------->


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $(document).ready(function() {
    //console.log("Event triggered");
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
  });

  $(document).on('click', '.editBtn', function() {
    var userdata = $(this).attr('user-data');
    console.log("Edit item on Id :::", userdata);
    var user_edit = userdata.split('=');
    $('#userid').val(user_edit[1]);
    $('#bnuserid').val(user_edit[2]);

    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {
    var user_id = $('#editUserId').val();
    console.log("user id edit:>>>>", user_id);
    var data = {
      user_id: $('#userid').val(),
      beneficiary_id: $('#bnuserid').val()
    }

    console.log("Item edit data on:::", data);

    $.ajax({
      url: "./beneficiary_user_edit",
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

</script>
<!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection