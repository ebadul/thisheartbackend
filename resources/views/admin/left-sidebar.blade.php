

  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{asset('AdminLTE/dist/img/userphoto.jpg')}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{$user->email}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <!-- <li class="active"><a href="dashboard"><i class="fa fa-circle-o"></i> Dashboard</a></li> -->
            <li ><a href="primary_user"><i class="fa fa-circle-o"></i> Primary Users</a></li>
            <li><a href="beneficiary_user"><i class="fa fa-circle-o"></i> Benefiicary Users</a></li>
            <li><a href="/package_info"><i class="fa fa-circle-o"></i> Package Info</a></li>
            <li><a href="/user_package"><i class="fa fa-circle-o"></i> User Package</a></li>
            <li><a href="/user_activities"><i class="fa fa-circle-o"></i> User Activities</a></li>
          </ul>
        </li>

        <li class="header">Settings</li>
        {{-- <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Forgot Password</span></a></li> --}}
        <li><a href="{{route('logout')}}"><i class="fa fa-circle-o text-red"></i> <span>Log Out</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>


