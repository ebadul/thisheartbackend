@extends('admin.admin-layout')

@section('content')
@if(Auth::check())

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
        <div class="row">
          <div class="col-md-4">
            <span class="h1">Billing Details</span class="h1">
          </div>
          <div class="col-md-2">
            
          </div>
          <div class="col-md-3">
            <span class="form-group">
              {{-- <select class="select select2 form-control" id="select_subscribed_status">
                <option value="">::Select Status::</option>
                <option value="0">Unsubscribed</option>
                <option value="1">Subscribed</option>
                
              </select> --}}
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
        <div class="row">
          <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
              <thead>
                <tr role="row">
                
                  <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 182px;">Bill Id</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Email</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Package</th>
                  <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending" style="width: 224px;">Billing Cost</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Payment Type</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Billing Date</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Next Billing Date</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Status</th>
                  <th tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending" style="width: 156px; text-align:center;">Make Payment</th>
                  
                
                </tr>
              </thead>
              <tbody>
                <?php if ($billing_list) : ?>
                  @foreach ( $billing_list as $row )
                  <tr role="row" class="odd">
                    <td title="{{$row->id}}">{{$row->id}}</td>
                    <td title="{{$row->user_id}}" id="lbl-email-{{$row['id']}}">{{$row->user->email}}</td>
                    <td title="{{$row->user_id}}" id="lbl-package-{{$row['id']}}" class="text-center">
                        {{$row->user->user_package->package_info->package}}</td>
                    <td class="text-center">$ {{ $row['package_cost']}}</td>
                    <td title="{{ $row['recurring_type']}}">{{ $row['payment_type']}}</td>
                    <td>{{ $row['billing_date']}}</td>
                    <td>{{ empty($row['next_billing_date'])?'Not yet billed':$row['next_billing_date']}}</td>
                    <td class="text-center">
                        <button class="btn btn-info btnMore" billing-details="{{$row}}">
                          More <i class="fa fa-caret-right" aria-hidden="true"></i>
                        </button>
                  
                    </td>
                    <td class="text-center">
                    <button type="button" class="btn btn-success btnCharging" id="btnCharging-{{$row->id}}" billing-details-id="{{$row->id}}" {{ $row['paid_status']?'disabled':'Charging'}}>
                         <i class="fa fa-spinner fa-spin hidden"></i>
                        {{ $row['paid_status']?'Charging':'Charging'}}</button>
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
    

    <!-----------------------------------------  More Modal start ---------------------------------------------->

    <div class="modal modal-info fade" id="modal-more">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Billing details</h4>
          </div>
          <div class="modal-body">

            {{csrf_field()}}
            <table class="table table-bordered">
              <tr>
                <td>Bill ID</td>
                <td id="lbl-srno"></td>
              </tr>
              
              <tr>
                <td>Email ID</td>
                <td id="lbl-email"></td>
              </tr>
              <tr>
                <td>Package</td>
                <td id="lbl-package"></td>
              </tr>
              <tr>
                <td>Billing Cost</td>
                <td id="lbl-package-cost"></td>
              </tr>
              <tr>
                <td>Bill Start</td>
                <td id="lbl-billing-start"></td>
              </tr>
              <tr>
                <td>Bill End</td>
                <td id="lbl-billing-end"></td>
              </tr>
              <tr>
                <td>Billing Date</td>
                <td id="lbl-billing-date"></td>
              </tr>
              <tr>
                <td>Next Billing Date</td>
                <td id="lbl-next-billing-date"></td>
              </tr>
              <tr>
                <td>Payment Type</td>
                <td id="lbl-payment-type"></td>
              </tr>
              <tr>
                <td>Recurring Type</td>
                <td id="lbl-recurring-type"></td>
              </tr>
              <tr>
                <td>Process Times</td>
                <td id="lbl-process-times"></td>
              </tr>
              <tr>
                <td>Paid Status</td>
                <td id="lbl-paid-status"></td>
              </tr>
              <tr>
                <td>Process Status</td>
                <td id="lbl-process-status"></td>
              </tr>
              <tr>
                <td>Updated</td>
                <td id="lbl-updated"></td>
              </tr>
              <tr>
                <td>Cron Job ID</td>
                <td id="lbl-cron-id"></td>
              </tr>
            </table>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-right" data-dismiss="modal">Cancel</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
   

    <!--------------------------------  More Modal End --------------------------------------------->



     <!-----------------------------------------  Payment Charging Modal start ---------------------------------------------->

     <div class="modal modal-info fade" id="modal-payment-charging">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Payment Charging</h4>
          </div>
          <div class="modal-body">

            {{csrf_field()}}

            Do you want to charge the selected billing payment?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success pull-left" data-dismiss="modal">Cancel</button>
            <button type="button" name="edit_ok" id="btn-payment-confirm" class="btn btn-danger">Confirm Charging</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    </div>

    <!--------------------------------  Payment Charging Modal End --------------------------------------------->



    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 

  <!-- Control Sidebar -->

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- -----------------------------  Active/Deactive Item Ajax Request Start ------------------------------- ---->
<script>
  $(document).ready(function() {
    $('.btnMore').click(function() {
      var billing_details = JSON.parse($(this).attr('billing-details'));
      $("#lbl-srno").text(billing_details.id);
      $("#lbl-email").text($("#lbl-email-"+billing_details.id).text());
      $("#lbl-package").text($("#lbl-package-"+billing_details.id).text());
      $("#lbl-package-cost").text('$'+billing_details.package_cost);
      $("#lbl-billing-start").text(billing_details.billing_start_date);
      $("#lbl-billing-end").text(billing_details.billing_end_date);
      $("#lbl-billing-date").text(billing_details.billing_date);
      $("#lbl-next-billing-date").text(billing_details.next_billing_date);
      $("#lbl-payment-type").text(billing_details.payment_type);
      $("#lbl-recurring-type").text(billing_details.recurring_type);
      $("#lbl-process-times").text(billing_details.payment_process_times);
      $("#lbl-paid-status").text(billing_details?'Paid':'Unpaid');
      $("#lbl-process-status").text(billing_details.process_stauts);
      $("#lbl-updated").text(billing_details.updated_at);
      $("#lbl-cron-id").text(billing_details.cron_payment_charging_id);
      $("#modal-more").modal("show");
    });

    $('.btnCharging').click(function(e) {
      e.preventDefault();
      var billing_details_id = $(this).attr('billing-details-id');
      $("#btn-payment-confirm").attr('billing-details-id',billing_details_id);
      $("#modal-payment-charging").modal('show');

    });

    $('#btn-payment-confirm').click(function(e) {
      e.preventDefault();
      $("#modal-payment-charging").modal('hide');
      var billing_details_id = $(this).attr('billing-details-id');
      var faspinner = $("#btnCharging-"+billing_details_id).find('i.fa').removeClass('hidden');
      faspinner.removeClass('hidden');
      $("#btnCharging-"+billing_details_id).attr('disabled',true);
      $.ajax({
        type: "post",
        dataType: "json",
        url: "./admin_payment_charging",
        data: {
          billing_details_id: billing_details_id
        },
        beforeSend: function(xhr, type) {
          if (!type.crossDomain) {
            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          }
        },
        success: function(data) {
          console.log('success:',data)
          $.toast({
          heading: 'Information',
          text: 'Successfully, payment is updated!',
          icon: 'info',
          position: 'bottom-right',
          loader: true, // Change it to false to disable loader
          bgColor: '#088' // To change the background
        })
         faspinner.addClass('hidden');
          location.reload(true);
        },
        error: function(error) {
          console.log("error :", error);
          $("#btnCharging-"+billing_details_id).removeAttr('disabled');
         faspinner.addClass('hidden');
          $.toast({
            heading: 'Information',
            text: 'Sorry, '+error.responseText,
            icon: 'error',
            position: 'bottom-right',
            loader: true, // Change it to false to disable loader
            bgColor: '#FF6A4D' // To change the background
          })
          
        }
      });
    });


    $("#select_subscribed_status").on('change', function() {
      var subscribed_status = $(this).val();
      if (subscribed_status > 0) {
        window.location = '/unsubscribed_user/' + subscribed_status;
      } else {
        window.location = '/unsubscribed_user/0';
      }
    })

  });
</script>

<!------------------------------- Active/Deactive Item Ajax Request End  ------------------------------------>


<!---------------------------------- Edit Item Ajax Request  Start  ------------------------------------------->
<script>
  $(document).ready(function() {
    //console.log("Event triggered");
  });
  $('#package_id, #subscription_status').css('pointer-events','none');
  $(document).on('click', '.editBtn', function() {
    var user_package = JSON.parse($(this).attr('user-data'));

    $('#user_package_id').val(user_package.id);
    $('#user_id').val(user_package.user_id);
    // $('#package_id').val(user_package.package_id);
    $('#subscription_date').val(user_package.subscription_date);
    $('#subscription_expire_date').val(user_package.subscription_expire_date);
    // $('#subscription_status').val(user_package.subscription_status);
    $('#modal-edit').modal('show');

  });

  $('#edit_btn').click(function(data) {

    var data = {
      user_package_id: $('#user_package_id').val(),
      user_id: $('#user_id').val(),
      package_id: $('#package_id').val(),
      subscription_date: $('#subscription_date').val(),
      subscription_expire_date: $('#subscription_expire_date').val(),
      subscription_status: $('#subscription_status').val()
    }


    $.ajax({
      url: "/free_user_package_edit",
      dataType: "json",
      data: data,
      method: "POST",
      beforeSend: function(xhr, type) {
        if (!type.crossDomain) {
          xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
          $('#edit_btn').text('Confirmed Free Account....');
        }
      },
      success: function() {
        $.toast({
          heading: 'Information',
          text: 'Successfully, free account request updated!',
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
      "lengthMenu": [ 25, 50, 100, 150 ],
      "pageLength":50,
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