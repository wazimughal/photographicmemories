  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('admin/dashboard')}}" class="brand-link">
    <img src="{{url('adminpanel/dist/img/logo_photographic.png')}}" alt="Thephotographic Memories" width="100%">
    </a>
    <a href="{{url('admin/dashboard')}}" class="brand-link">
      {{-- {{config('constants.app_name')}} --}}
     
      <span class="brand-text font-weight-light">{{config('constants.app_name')}}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{url('adminpanel/dist/img/avatar.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
      
        <div class="info">
          <a href="#" class="d-block">{{ $user->name}}</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      {{-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> --}}

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item {{ (request()->segment(2) == 'dashboard') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'dashboard') ? 'active' : ''}}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/dashboard')}}" class="nav-link">
                  <i class="far fa-building nav-icon"></i>
                  <p>Dashboard </p>
                </a>
              </li>
              {{-- <li class="nav-item">
                <a href="{{url('admin/dashboard/2')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/dashboard/3')}}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dashboard v3</p>
                </a>
              </li> --}}
            </ul>
          </li>
          @if($user->group_id!=config('constants.groups.photographer'))
          <li class="nav-item {{ (request()->segment(2) == 'pencils' || request()->segment(2) == 'pencil') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'pencils' || request()->segment(2) == 'pencil') ? 'active' : ''}}">
              <i class="nav-icon far fa-building"></i>
              <p>
                Pencils 
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/pencils')}}" class="nav-link {{ (request()->segment(2) == 'pencils') ? 'active' : ''}}">
                  <i class="fa fa-hospital"></i>
                  <p>Pencils</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/pencil/office')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Pencils by Office</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/pencil/web')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Penciles by Web</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/pencil/venue_group')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Penciles by Hall</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/pencil/trashed')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Trashed Pencile</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/pencils/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Pencil</p>
                </a>
              </li>
            </ul>
          </li>
          
          @endif
          @if($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.venue_group_hod'))
          <li class="nav-item {{ (request()->segment(2) == 'venuegroups') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'venuegroups') ? 'active' : ''}}">
              <i class="nav-icon far fa-hospital"></i>
              <p>
                Venue Group
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/venuegroups')}}" class="nav-link">
                  <i class="far fa-hospital"></i>
                  <p> Venue Group List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/venuegroups/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Venue Group</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          @if($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.customer'))
          {{-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Customers
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/customers')}}" class="nav-link">
                  <i class="fa fa-user"></i>
                  <p> Customer List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/customers/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Customer</p>
                </a>
              </li>
            </ul>
          </li> --}}
          @endif
        
          @if($user->group_id==config('constants.groups.photographer'))
          <li class="nav-item {{ (request()->segment(2) == 'photographer') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'photographer') ? 'active' : ''}}">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Photographer Bookings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('photographer/bookings')}}" class="nav-link">
                  <i class="fa fa-user"></i>
                  <p> Booking List</p>
                </a>
              </li>
              
            </ul>
          </li>
          @else
          <li class="nav-item {{ (request()->segment(2) == 'bookings') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'bookings') ? 'active' : ''}}">
              <i class="nav-icon fa fa-users"></i>
              <p>
                Bookings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/bookings')}}" class="nav-link">
                  <i class="fa fa-user"></i>
                  <p> Booking List</p>
                </a>
              </li>
              @if($user->group_id==config('constants.groups.admin'))
              <li class="nav-item">
                <a href="{{url('admin/booking/trashed')}}" class="nav-link">
                  <i class="fa fa-hospital"></i>
                  <p>Trashed Bookings</p>
                </a>
              </li>
              @endif
              
            </ul>
          </li>
          @endif
          @if($user->group_id==config('constants.groups.admin') || $user->group_id==config('constants.groups.photograper'))
          <li class="nav-item {{ (request()->segment(2) == 'photographers') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'photographers') ? 'active' : ''}}">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Photographer
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/photographers')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Photographer List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/photographers/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Photographer</p>
                </a>
              </li>
            </ul>
          </li>
          @endif

          @if($user->group_id==config('constants.groups.admin'))
          <li class="nav-item {{ (request()->segment(2) == 'packages') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'packages') ? 'active' : ''}}">
              <i class="nav-icon fa fa-images"></i>
              <p>
                Packages
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/packages')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Packages List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/packages/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Add Packages</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/packages/categories')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                  <p>Categories</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item {{ (request()->segment(2) == 'users') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'users') ? 'active' : ''}}">
              <i class="nav-icon fa fa-users"></i>
              <p>
                USERS
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{url('admin/users')}}" class="nav-link">
                  <i class="far fa-user nav-icon"></i>
                  <p>User</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/users/add')}}" class="nav-link">
                  <i class="fa fa-plus"></i>
                
                     <p>Add User</p>
                </a>
              </li>
              
            </ul>
          </li>
          <li class="nav-item {{ (request()->segment(2) == 'activity-log') ? 'menu-open' : ''}}">
            <a href="#" class="nav-link {{ (request()->segment(2) == 'activity-log') ? 'active' : ''}}">
              <i class="nav-icon fa fa-images"></i>
              <p>Activities
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              
              <li class="nav-item">
                <a href="{{url('admin/activity-log')}}" class="nav-link">
                  <i class="fa fa-images"></i>
                  <p> Activity Log</p>
                </a>
              </li>
              <li class="nav-item ">
                <a href="{{url('/admin/colors')}}" class="nav-link {{ (request()->segment(2) == 'colors') ? 'active' : ''}}">
                  <i class="nav-icon far fa-calendar-alt"></i>
                  <p>
                    Color Management
                  </p>
                </a>
              </li>
             
            </ul>
          </li>
          @endif
          <li class="nav-item {{ (request()->segment(2) == 'calender') ? 'menu-open' : ''}}">
            <a href="{{url('admin/calender')}}" class="nav-link {{ (request()->segment(2) == 'calender') ? 'active' : ''}}">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                Calendar
                <span class="badge badge-info right">2</span>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{url('/admin/logout')}}" class="nav-link">
              <i class="nav-icon fa fa-user"></i>
              <p>
               Logout
              </p>
            </a>
            
          </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>