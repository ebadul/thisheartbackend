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
        Diagnosis Info
        <a href="/diagnosis_info_add" role='button' class="btn btn-success" style="margin-left:20px"> &nbsp; Add Diagnosis &nbsp;</a>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Diagnosis Add</a></li>
        <li class="active">Diagnosis_info list</li>
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

                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Diagnosis</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending" style="width: 199px;">Description</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Edit</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($diagnosis_info) : ?>
                  @foreach ( $diagnosis_info as $row )
                  <tr role="row" class="odd">

                    <td>
                        {{ $row['diagnosis_name']}}
                    </td>
                    <td title="{{$row['description']}}">{{ substr($row['description'],0,25)}}...</td>
                    
                    <td class="text-center">
                      <button type="button" class="btn btn-info editBtn" user-data="{{$row}}"><span><i class="fa fa-edit"></i></span> Edit</button>
                    </td>
                    <td class="text-center">
                      <a href="/delete_diagnosis_info/{{$row['id']}}" class="btn btn-danger" user-id="{{$row['id']}}" type="button" onclick="return confirm('Do you want to delete diagnosis info data')">
                        Delete</a>
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
            <h4 class="modal-title">Edit Package Details</h4>
          </div>

          <div class="modal-body">
            {{csrf_field()}}
            <input type="hidden" name="diagnosis_id" id="diagnosis_id" value="" />
            <div class="form-group">
              <label for="package">Diagnosis Name</label>
              <input type="text" class="form-control" id="diagnosis_name" name="diagnosis_name" value="" placeholder="Diagnosis Name">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" value="" placeholder="Description"></textarea>
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


<!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $(document).ready(function() {
    //console.log("Event triggered");
  });

  $(document).on('click', '.editBtn', function() {
    var userdata = JSON.parse($(this).attr('user-data'));
    $('#diagnosis_id').val(userdata.id);
    $('#diagnosis_name').val(userdata.diagnosis_name);
    $('#description').val(userdata.description);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {
    var data = {
      id: $('#diagnosis_id').val(),
      diagnosis_name: $('#diagnosis_name').val(),
      description: $('#description').val(),
    }

    $.ajax({
      url: "./diagnosis_info_edit",
      dataType: "json",
      data: data,
      method: "post",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn').text('Updating Package Info....');
        }
      },
      success: function() {
        $.toast({
          heading: 'Information',
          text: 'Successfully, package updated!',
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