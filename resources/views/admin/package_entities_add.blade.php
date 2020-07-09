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

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Package Entities Add
        <a href="/package_entities" role='button' class="btn btn-success" style="margin-left:20px"> &nbsp; Package Entity List &nbsp;</a>
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

                        <div class=" col-sm-12 form-group">
                            <label for="email">Entity Value</label>
                            <input type="text" name="entity_value" id="entity_value" required class="form-control" id="entity_value" value="" required placeholder="Entity Value">
                        </div>
                        <div class="form-group col-sm-12 form-group">
                            <input type="submit" class="btn btn-success" id="mobile" value="SAVE" >
                        </div>
                    </form>
                </div>
            
            </div><!-- end row -->
            
        
    </div>
<!-----------------------------------------  Edit Modal start ---------------------------------------------->

 


 <!-- -----------------------------  Active/Deactive Item Ajax Request Start ------------------------------- ---->
 <script>
  $(document).ready(function() {
    $("#txtUnit").on('change',function(){
      var txt_unit = this.value;
      if(txt_unit==="Unlimited"){
        $("#entity_value").attr('readonly','readonly');
        $("#entity_value").closest('div').hide('slow');
        $("#entity_value").val('-1');
      }else{
        $("#entity_value").removeAttr('readonly');
        $("#entity_value").closest('div').show('slow');
        $("#entity_value").val('');
      }
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
