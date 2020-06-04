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
      <div class="row">
        <div class="col-md-4">
          <span class="h1">Package - Entities</span class="h1">
        </div>
        <div class="col-md-2">
          <a href="/package_entities_add" role='button' class="btn btn-success" style="margin-left:20px"> &nbsp; Add Package Entity &nbsp;</a>
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
        <!--end row -->
        <div class="row">
          <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row">
                  <th class="sorting_asc" style="width: 182px;">Package Id</th>
                  <th class="sorting" style="width: 224px;">Entity Title</th>
                  <th class="sorting" style="width: 199px;">Value</th>
                  <th class="sorting" style="width: 199px;">Unit</th>
                  <th style="width: 156px; text-align:center;">Status</th>
                  <th style="width: 156px; text-align:center;">Edit</th>
                  <th style="width: 156px; text-align:center;">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($package_entity)) : ?>
                  @foreach ( $package_entity as $row )
                  <tr role="row" class="odd">

                    <td>{{ !empty($row->package_info)?$row->package_info->package:''}}</td>
                    <td>{{ !empty($row->entity_info)?
                           $row->entity_info->package_entity_title:''}}</td>
                    <td>{{ ($row['entity_value']==="-1")?'Unlimited':$row['entity_value']}}</td>
                    <td>{{ $row['entity_unit']}}</td>
                    <td>{{ $row['entity_status']?'Actived':'Inactived'}}</td>
                    <td class="text-center">
                      <button type="button" class="btn btn-info editBtn" user-data="{{$row}} ">
                        <span><i class="fa fa-edit"></i></span> Edit</button>
                    </td>
                    <td class="text-center">
                      <a href="/package_entities_delete/{{$row['id']}}" class="btn btn-warning editBtn" onclick="return confirm('Do you want to delete entity id: {{$row['id']}}')">
                        <span><i class="fa fa-remove"></i></span> Delete</a>
                    </td>
                  </tr>
                  @endforeach
                <?php endif; ?>
              </tbody>
              <!-- <tfoot>
                <tr role="row">
                  <th class="sorting_asc" style="width: 182px;">Package Id</th>
                  <th class="sorting" style="width: 224px;">Entity Title</th>
                  <th class="sorting" style="width: 199px;">Value</th>
                  <th class="sorting" style="width: 199px;">Unit</th>
                  <th style="width: 156px; text-align:center;">Status</th>
                  <th style="width: 156px; text-align:center;">Edit</th>
                  <th style="width: 156px; text-align:center;">Delete</th>
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
            <h4 class="modal-title">Package Entity</h4>
          </div>
          <div class="modal-body">
            <form role="form" id="editForm" name="edit">
              {{csrf_field()}}
              <input type="hidden" name="package_entities_id" id="package_entities_id" value="" />

              <div class="form-group">
                <label for="package_id">Package</label>
                <select class="form-control" id="package_id">
                  <option>:: Select Package ::</option>
                  @foreach($package_list as $package)
                  <option value="{{$package->id}}">{{$package->package}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="entities_id">Entity</label>
                <select class="form-control" id="entities_id">
                  <option>:: Select Entity ::</option>
                  @foreach($entity_list as $entity)
                  <option value="{{$entity->id}}">{{$entity->package_entity_title}}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label for="txtUnit">Unit</label>
                <select name="txtUnit" id="txtUnit" required class="select select2 form-control">
                  <option value="">:: Unit ::</option>
                  <option value="GB">GB</option>
                  <option value="Qnty">Qnty</option>
                  <option value="Status">Status</option>
                  <option value="Others">Others</option>
                  <option value="Unlimited">Unlimited</option>

                </select>
              </div>

              <div class="form-group">
                <label for="entity_value">Entity Value</label>
                <input type="text" class="form-control" id="entity_value" value="" placeholder="Entity Value">
              </div>
              <div class="form-group">
                <label for="entity_status">Entity Status</label>
                <select class="form-control" id="entity_status">
                  <option value="1">Active</option>
                  <option value="0">In-active</option>

                </select>
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
    $("#select_package").on('change', function() {
      var txt_package = $(this).val();
      if (txt_package > 0) {
        window.location = '/package_entity/' + txt_package;
      } else {
        window.location = '/package_entities';
      }
    })
  });
</script>

<!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $("#txtUnit").on('change', function() {
    var txt_unit = this.value;
    if (txt_unit === "Unlimited") {
      $("#entity_value").attr('readonly', 'readonly');
      $("#entity_value").closest('div').hide('slow');
      $("#entity_value").val('-1');
    } else {
      $("#entity_value").removeAttr('readonly');
      $("#entity_value").closest('div').show();
      $("#entity_value").val('');
    }
  });

  $(document).on('click', '.editBtn', function() {
    var pkg_entity = JSON.parse($(this).attr('user-data'));
    $('#package_entities_id').val(pkg_entity.id);
    $('#package_id').val(pkg_entity.package_id);
    $('#entities_id').val(pkg_entity.package_entities_id);
    $('#txtUnit').val(pkg_entity.entity_unit);
    if (pkg_entity.entity_unit === "Unlimited") {
      $('#entity_value').attr('readonly', 'readonly');
    }
    $('#entity_value').val(pkg_entity.entity_value);
    $('#entity_status').val(pkg_entity.entity_status);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {

    var data = {
      package_entities_id: $('#package_entities_id').val(),
      package_id: $('#package_id').val(),
      entities_id: $('#entities_id').val(),
      entity_unit: $('#txtUnit').val(),
      entity_value: $('#entity_value').val(),
      entity_status: $('#entity_status').val()
    }



    $.ajax({
      url: "/package_entities_edit",
      dataType: "json",
      data: data,
      method: "post",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn').text('Updating Package Entity Item....');
        }
      },
      success: function() {
        $.toast({
          heading: 'Information',
          text: 'Successfully, package entity updated!',
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