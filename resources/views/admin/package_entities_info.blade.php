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
        Entities Info
        <a href="/package_entities_info_add" role='button' class="btn btn-success" style="margin-left:20px"> &nbsp; Add Entity &nbsp;</a>
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


        </div><!-- end row -->
        <div class="row">
          <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row">
                  <th class="sorting_asc">Package Entity</th>
                  <th class="sorting">Description</th>
                  <th class="sorting" style="width: 7%;">Edit</th>
                  <th class="sorting" style="width: 7%;">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($package_entities_info) : ?>
                  @foreach ( $package_entities_info as $row )
                  <tr role="row" class="odd">
                    <td>

                      {{ $row['package_entity_title']}}

                    </td>
                    <td>{{ $row['package_entity_description']}}</td>


                    <td>
                      <button type="button" class="btn btn-block btn-info editBtn" user-data="{{$row}} "><span><i class="fa fa-edit"></i></span> Edit</button>
                    </td>
                    <td class="text-center">
                      <a href="/package_entities_info_delete/{{$row['id']}}" class="btn btn-block btn-warning editBtn" onclick="return confirm('Do you want to delete entity id: {{$row['id']}}')">
                        <span><i class="fa fa-remove"></i></span> Delete</a>
                    </td>
                  </tr>
                  @endforeach
                <?php endif; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th rowspan="1" colspan="1">Entity Title</th>
                  <th rowspan="1" colspan="1">Description</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
              </tfoot>
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
            <input type="hidden" name="editBtn" id="editUserId" value="" />

            <div class="form-group">
              <label for="entity_title">Entity Title</label>
              <input type="hidden" class="form-control" id="entity_id" value="" placeholder="Entity ID">
              <input type="text" class="form-control" id="entity_title" value="" placeholder="Entity Title">
            </div>
            <div class="form-group">
              <label for="description">Entity Description</label>
              <input type="email" class="form-control" id="description" value="" placeholder="Entity Description">
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
    <strong>Copyright &copy; 2020 <a href="https://thisheart.co"> This Heart Admin</a>.</strong> All rights
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
    var entity_info = JSON.parse($(this).attr('user-data'));
    console.log("Edit item on Id :::", entity_info);

    $('#entity_id').val(entity_info.id);
    $('#entity_title').val(entity_info.package_entity_title);
    $('#description').val(entity_info.package_entity_description);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {

    var data = {
      entity_id: $('#entity_id').val(),
      entity_title: $('#entity_title').val(),
      description: $('#description').val(),

    }

    console.log("Item edit data on:::", data);

    $.ajax({
      url: "./package_entities_info_edit",
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
        $.toast({
          heading: 'Information',
          text: 'Successfully, entity updated!',
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