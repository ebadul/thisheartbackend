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
        Inactive Users
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">User</a></li>
        <li class="active">In Active</li>
      </ol>
    </section>

    <!-- Main content -->
    <div class="box-body">
            <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                  <div class="col-sm-2">
                    
                  </div>

                <div class="col-sm-5">
                  <label> 
                    
                      <select name="selectCheckbox" id="selectCheckbox" aria-controls="" class="form-control input-sm">
                        <option value="">::Select::</option>
                        <option value="select">Select All</option>
                        <option value="deselect">Deselect All</option>
                      </select> 
                      
                      <select name="selectAction" id="selectAction" aria-controls="" class="form-control input-sm">
                        <option value="">:: Action ::</option>
                        <option value="first_send_email">First Send Email</option>
                        <option value="second_send_email">Second Send Email</option>
                        <option value="send_sms">Send SMS</option>
                        <option value="final_make_call">Final Phone Call</option>
                         
                      </select> 
                      
                      <button type="submit" class="btn btn-primary input-sm" 
                          placeholder="" aria-controls="example1" id="btnNotify">
                          Notify
                      </button>
                      <button type="submit" class="btn btn-info input-sm" 
                          placeholder="" aria-controls="example1" id="btnAutomation">
                          Auto Notify
                      </button>
                      <i class="fa fa-refresh fa-spin fa-spin-email" style="visibility:hidden"></i>
                  </label>

                </div>
                <div class="col-sm-3">
                    <div id="example1_filter" class="dataTables_filter">
                        @php
                            $days = isset($_GET['days'])?$_GET['days']:0;

                        @endphp
                        <form action="" method="get" >
                        <label><select class="form-control input-sm" 
                                  name="days">
                                    <option value="0" {{$days==0?'selected':''}}>:: Days ::</option>
                                    <option value="30" {{$days==30?'selected':''}}>30 Days</option>
                                    <option value="60" {{$days==60?'selected':''}}>2 Months</option>
                                    <option value="90" {{$days==90?'selected':''}}>3 Months</option>
                                    <option value="120" {{$days==120?'selected':''}}>4 Months</option>
                                    <option value="150" {{$days==150?'selected':''}}>5 Months</option>
                                    <option value="180" {{$days==180?'selected':''}}>6 Months</option>
                                </select>
                            </label>
                            <label>
                                <button type="submit" class="btn btn-info input-sm" 
                                    placeholder="" aria-controls="example1">
                                    Search
                                </button>
                            </label>
                        </form>
                    </div>
                </div>

                <div class="col-sm-2">
                   
                </div>
              </div>
              <div class="row">
              <div class="col-sm-12">
              <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                <tr role="row">
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" >S/L</th>
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" >User Id</th>
                  <th  tabindex="0" aria-controls="example1" >Last Login</th>
                  <th  tabindex="0" aria-controls="example1" >Last Activity</th>
                  <th  tabindex="0" aria-controls="example1" >First Email</th>
                  <th  tabindex="0" aria-controls="example1" >Second Email</th>
                  <th  tabindex="0" aria-controls="example1" >Send SMS</th>
                  <th  tabindex="0" aria-controls="example1" >Email Beneficiary</th>
                  <th  tabindex="0" aria-controls="example1" >SMS Beneficiary</th>
                  <th  tabindex="0" aria-controls="example1" >Phone Call</th>
                  <th  tabindex="0" aria-controls="example1" >Notes</th>
                  <th  tabindex="0" aria-controls="example1" >Delete</th>
                </tr>
                </thead>
                <tbody>
               <?php if( $user_activities ):?>
                @php
                    $i=0;
                @endphp 
                @foreach ( $user_activities  as $row )
                @php
                    $i++;
                @endphp
                <tr role="row" class="odd">
                   <td>
                   <input type="checkbox" class="selectChk" name="userSelect[{{$i}}]" id="userSelect{{$i}}" value="{{$row['id']}}">
                     {{ $i }}
                    </td>
                   <td>{{ $row['id'] }}</td>
                   <td>{{ !empty($row->inactive_user_notify->last_login)?$row->inactive_user_notify->last_login:''}}</td>
                   
                   <td>
                     @php
                      if(!empty($row->inactive_user_notify->last_login)){
                        $date = Carbon\Carbon::parse(
                        $row->inactive_user_notify->last_login);
                        $now = Carbon\Carbon::now();
                        $diff = $date->diffInDays($now);
                      }else{
                        $diff = 0;
                      }
                     
                     @endphp
                     {{ $diff}} days
                    </td>
                 
                    <td>
                      @php
                        if(!empty($row->inactive_user_notify->first_send_email)){
                          $first_email_date = Carbon\Carbon::parse(
                            $row->inactive_user_notify->first_send_email);
                          $now_first_email = Carbon\Carbon::now();
                          $diff_first_email = ($first_email_date=="-0001-11-30 00:00:00")?'0':
                          $first_email_date->diffInDays($now_first_email);
                        }else{
                          $diff_first_email = 0;
                        }
                        
                        echo $diff_first_email;
                      @endphp
                      days
                    </td>
                    <td>
                      @php
                        if(!empty($row->inactive_user_notify->second_send_email)){
                          $second_email_date = Carbon\Carbon::parse(
                          $row->inactive_user_notify->second_send_email);
                          $now_first_email = Carbon\Carbon::now();
                          $diff_first_email = ($second_email_date=="-0001-11-30 00:00:00")?'0':
                          $second_email_date->diffInDays($now_first_email);
                        }else{
                          $diff_first_email = 0;
                        }
                        
                        echo $diff_first_email;
                      @endphp
                        days
                    </td>
                    <td>
                      @php
                      if(!empty($row->inactive_user_notify->send_sms)){
                        $send_sms = Carbon\Carbon::parse(
                        $row->inactive_user_notify->send_sms);
                        $now_first_email = Carbon\Carbon::now();
                        $diff_first_email = ($send_sms=="-0001-11-30 00:00:00")?'0':
                        $send_sms->diffInDays($now_first_email);
                      }else{
                        $diff_first_email = 0;
                      }
                      
                      echo $diff_first_email;
                    @endphp
                      days
                      
                    </td>
                    {{--  <!--td>
                      @php
                      $make_phone = Carbon\Carbon::parse($row->inactive_user_notify->make_phone_call);
                      $now_first_email = Carbon\Carbon::now();
                      $diff_first_email =($make_phone=="-0001-11-30 00:00:00")? '0':
                      $make_phone->diffInDays($now_first_email);
                      echo $diff_first_email;
                    @endphp

                      days
                    </td-->  --}}
                    <td>
                      @php
                      if(!empty($row->inactive_user_notify->
                      send_email_beneficiary_user)){
                        $email_beneficiary = Carbon\Carbon::parse($row->inactive_user_notify->
                        send_email_beneficiary_user);
                        $now_first_email = Carbon\Carbon::now();
                        $diff_first_email = ($email_beneficiary=="-0001-11-30 00:00:00")?'0':
                        $email_beneficiary->diffInDays($now_first_email);
                      }else{
                        $diff_first_email = 0;
                      }
                      
                      echo $diff_first_email;
                    @endphp
                      days
                      
                    </td>
                    <td>
                      @php
                      if(!empty($row->inactive_user_notify->
                      send_sms_beneficiary_user)){
                        $send_sms_beneficiary = Carbon\Carbon::parse($row->inactive_user_notify->
                        send_sms_beneficiary_user);
                        $now_first_email = Carbon\Carbon::now();
                        $diff_first_email = ($send_sms_beneficiary=="-0001-11-30 00:00:00")?'0':
                        $send_sms_beneficiary->diffInDays($now_first_email);
                      }else{
                        $diff_first_email = 0;
                      }
                      
                      echo $diff_first_email;
                    @endphp

                      days
                    </td>
                    <td>
                      @php
                      if(!empty($row->inactive_user_notify->final_make_call)){
                        $final_make_call = Carbon\Carbon::parse(
                        $row->inactive_user_notify->final_make_call);
                        $now_make_call = Carbon\Carbon::now();
                        $diff_make_call = ($final_make_call=="-0001-11-30 00:00:00")?'0':
                        $final_make_call->diffInDays($now_make_call);
                      }else{
                        $diff_make_call = 0;
                      }
                      
                      echo $diff_make_call ;
                    @endphp
                      days
                    </td>
                    <td>
                      {{!empty($row->inactive_user_notify->notes)?$row->inactive_user_notify->notes:''}}
                    </td>
                    
                     
                   
                    <td class="text-center"  >
                      <button type="button" class="btn btn-success editBtn" user-data="{{$row}} "><span><i class="fa fa-pencil"></i></span> Edit</button>
                    </td>
                </tr>
                @endforeach
              <?php endif;?>
                </tbody>
                <tfoot>
                <tr>
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" >S/L</th>
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" >User Id</th>
                  <th  tabindex="0" aria-controls="example1" >Last Login</th>
                  <th  tabindex="0" aria-controls="example1" >Last Activity</th>
                  <th  tabindex="0" aria-controls="example1" >First Email</th>
                  <th  tabindex="0" aria-controls="example1" >Second Email</th>
                  <th  tabindex="0" aria-controls="example1" >Send SMS</th>
                  <th  tabindex="0" aria-controls="example1" >Email Beneficiary</th>
                  <th  tabindex="0" aria-controls="example1" >SMS Beneficiary</th>
                  <th  tabindex="0" aria-controls="example1" >Phone Call</th>
                  <th  tabindex="0" aria-controls="example1" >Notes</th>
                  <th  tabindex="0" aria-controls="example1" >Delete</th>
                </tr>
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
                <h4 class="modal-title">In Active User</h4>
              </div>
              <div class="modal-body">
              
              {{csrf_field()}}
              <input type="hidden" name="id" id="id" value="" />

              <div class="form-group">
                  <label for="user_id">User ID</label>
                  <input type="text" class="form-control" id="user_id" value="" placeholder="User ID" readonly required>
                </div>
                <div class="form-group">
                  <label for="user_email">Email Address</label>
                  <input type="email" class="form-control" id="user_email" value="" placeholder="Enter email" readonly required>
                </div>
                <div class="form-group">
                  <label for="last_login">Last Login</label>
                  <input type="text" class="form-control" id="last_login" value="" placeholder="Last Login">
                </div>
                <div class="form-group">
                  <label for="notes">Notes</label>
                  <textarea class="form-control" id="notes" value="" placeholder="Notes"></textarea>
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


    $('#selectCheckbox').on('change',function(e){
          var status = e.target.value;
          if(status==="select"){
            $('.selectChk').parent().addClass('checked');
            $('.selectChk').parent().attr('aria-checked','true');
          }else if(status==="deselect"){
            $('.selectChk').parent().removeClass('checked');
            $('.selectChk').parent().attr('aria-checked','false');
          }
         
    });
    
    $('#btnNotify').on('click',function(e){
          var status = $('#selectAction').val();
          $('.fa-spin-email').css('visibility','visible');
          var userList = [];
          $('.selectChk').each((index,value)=>{
            var selectStatus = $(value).parent().attr('aria-checked');
            if(selectStatus==="true"){
              $(value).prop('checked', true);
              userList.push($(value).val());
            }
            
          })

          $.ajax({
            type: "post",
            dataType: "json",
            url: app_path+"inactive_user/send_email", 
            data: {actionType:status,userList:userList},
            beforeSend: function(xhr, type) {
            if (!type.crossDomain) {
                xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
            }
            },
                success: function(data){
                  console.log(data)
                  $('.fa-spin-email').css('visibility','hidden');
                  $.toast({
                            heading: 'Information',
                            text: 'Successfully, notification has sent!',
                            icon: 'info',
                            position: 'bottom-right',
                            loader: true,        // Change it to false to disable loader
                            bgColor: '#088'  // To change the background
                        })
                },
                error:function(error){
                console.log("error :", error);
                $('.fa-spin-email').css('visibility','hidden');
                $.toast({
                            heading: 'Error',
                            text: 'Sorry,'+error.responseText,
                            icon: 'warning',
                            position: 'bottom-right',
                            loader: true,        // Change it to false to disable loader
                            bgColor: 'darkred'  // To change the background
                        })
            }  
        });

           console.log('user list: ', userList);
         
    });

    $('#btnAutomation').on('click',function(e){
          var status = $('#selectAction').val();
          $('.fa-spin-email').css('visibility','visible');
          var userList = [];
          $('.selectChk').each((index,value)=>{
            var selectStatus = $(value).parent().attr('aria-checked');
            if(selectStatus==="true"){
              $(value).prop('checked', true);
              userList.push($(value).val());
            }
            
          })

          $.ajax({
            type: "post",
            dataType: "json",
            url: app_path+"inactive_user/send_email_automation", 
            data: {actionType:status,userList:userList},
            beforeSend: function(xhr, type) {
            if (!type.crossDomain) {
                xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
            }
            },
                success: function(data){
                  console.log(data)
                  $('.fa-spin-email').css('visibility','hidden');
                  $.toast({
                            heading: 'Information',
                            text: 'Successfully, notification has sent!',
                            icon: 'info',
                            position: 'bottom-right',
                            loader: true,        // Change it to false to disable loader
                            bgColor: '#088'  // To change the background
                        })
                },
                error:function(error){
                console.log("error :", error);
                $('.fa-spin-email').css('visibility','hidden');
                $.toast({
                            heading: 'Error',
                            text: 'Sorry,'+error.responseText,
                            icon: 'error',
                            position: 'bottom-right',
                            loader: true,        // Change it to false to disable loader
                            bgColor: 'darkred'  // To change the background
                        })
            }  
        });

           console.log('user list: ', userList);
         
    });

  });
</script>

  <!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


 <!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
 
 

   $(document).on('click', '.editBtn', function(){
     var userdata = JSON.parse($(this).attr('user-data')) ;
     console.log("Edit item on Id :::", userdata);
     $('#id').val(userdata.inactive_user_notify?userdata.inactive_user_notify.id:'');
     $('#user_id').val(userdata.id);
     $('#user_email').val(userdata.email);
     $('#last_login').val(userdata.inactive_user_notify?userdata.inactive_user_notify.last_login:'');
     $('#notes').val(userdata.inactive_user_notify?userdata.inactive_user_notify.notes:'');
     
     $('#modal-edit').modal('show');
     
   });

   $('#edit_btn').click(function(data){
     
     var data = {
       id:$('#id').val(),
       user_id:$('#user_id').val(),
       last_login:$('#last_login').val(),
       notes:$('#notes').val()
     }
        
        

     $.ajax({
       url:"./inactive_user_notify_edit", 
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
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    });
  });
</script>

  <!------------------------------------------ Edit Item Ajax Request End --------------------------------------->

@endsection
