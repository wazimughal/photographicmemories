@php
$home_url='';
$name=get_session_value('name');
$role='Customer';
if($user->group_id==config('constants.groups.admin')){
    $home_url=route('admin.dashboard');
    $role='Admin';
}

elseif($user->group_id==config('constants.groups.venue_group_hod')){
    $home_url=route('bookings.type','all');//route('admin.venuegroups');
    $name=get_session_value('vg_name');
    $role='Venue';
}

elseif($user->group_id==config('constants.groups.photographer')){
    $home_url=route('bookings.scheduled');
    $role='Photographer';
}



// $url=request()->segments();

@endphp
<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ $home_url }}" class="brand-link">
          <img src="{{ url('adminpanel/dist/img/logo_photographic.png') }}" alt="Thephotographic Memories" width="100%">
      </a>
      <a href="{{ $home_url }}" class="brand-link">
          {{-- {{config('constants.app_name')}} --}}

          <span class="brand-text font-weight-light">{{ config('constants.app_name') }}</span>
      </a>
      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex">
              <div class="image">
                  <img src="{{ url('adminpanel/dist/img/avatar.png') }}" class="img-circle elevation-2"
                      alt="User Image">
              </div>

              <div class="info">
                  <a href="{{ $home_url }}" class="d-block">{{ $name }}({{$role}})</a>
              </div>
          </div>

          <!-- Sidebar Menu -->
          @php
          if(!isset($record_count) || empty($record_count))
              $record_count=get_record_count();
          @endphp
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               @if ($user->group_id == config('constants.groups.admin'))
                  <li class="nav-item {{ request()->segment(2) == 'dashboard' ? 'menu-open' : '' }}">
                      <a href="#" class="nav-link {{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
                          <i class="nav-icon fas fa-tachometer-alt"></i>
                          <p>
                              Dashboard
                              <i class="right fas fa-angle-left"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item {{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
                              <a href="{{ url('admin/dashboard') }}" class="nav-link">
                                  <i class="far fa-building nav-icon"></i>
                                  <p>Dashboard </p>
                              </a>
                          </li>
                         
                      </ul>
                  </li>
                  
                  @endif
                  @if ($user->group_id == config('constants.groups.admin') || $user->group_id == config('constants.groups.venue_group_hod'))
                      <li
                          class="nav-item {{ request()->segment(2) == 'pencils' || request()->segment(2) == 'pencil' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'pencils' || request()->segment(2) == 'pencil' ? 'active' : '' }}">
                              <i class="nav-icon far fa-building"></i>
                              <p>
                                  Pencils
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['total_pencils'])}}</span>
                                  

                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('admin.pencil.types','all') }}"
                                      class="nav-link {{ request()->segment(2) == 'pencils' && request()->segment(3)==''? 'active' : '' }}">
                                      <i class="fa fa-hospital"></i>
                                      <p>Pencils <span class="badge badge-info right">{{($record_count['total_pencils'])}}</span></p>
                                  </a>
                              </li>
                              @if ($user->group_id == config('constants.groups.admin'))
                              <li class="nav-item">
                                <a href="{{ route('admin.pencil.types','venue_group') }}" class="nav-link {{ request()->segment(3) == 'venue_group' ? 'active' : '' }}">
                                    <i class="fa fa-hospital"></i>
                                    <p>Penciles by Hall <span class="badge badge-info right">{{($record_count['hall'])}}</span></p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="{{ route('admin.pencil.types','office') }}" class="nav-link {{ request()->segment(3) == 'office' ? 'active' : '' }}">
                                      <i class="fa fa-hospital"></i>
                                      <p>Pencils by Office <span class="badge badge-info right">{{($record_count['office'])}}</span></p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                <a href="{{ route('admin.pencil.types','web') }}" class="nav-link {{ request()->segment(3) == 'web' ? 'active' : '' }}">
                                      <i class="fa fa-hospital"></i>
                                      <p>Penciles by Web <span class="badge badge-info right">{{($record_count['web'])}}</span></p>
                                  </a>
                              </li>
                              {{-- <li class="nav-item">
                                  <a href="{{ route('admin.pencil.types','customer') }}" class="nav-link {{ request()->segment(3) == 'customer' ? 'active' : '' }}">
                                      <i class="fa fa-hospital"></i>
                                      <p>Penciles by Customer <span class="badge badge-info right">{{($record_count['customer_pencil'])}}</span></p>
                                  </a>
                              </li> --}}
                              <li class="nav-item">
                                <a href="{{ route('admin.pencil.types','trashed') }}" class="nav-link {{ request()->segment(3) == 'trashed' ? 'active' : '' }}">
                                    <i class="fa fa-hospital"></i>
                                    <p>Trashed Pencile</p>
                                </a>
                            </li>
                              @endif
                             
                              
                              <li class="nav-item">
                                  <a href="{{ route('pencils.pencils_form') }}" class="nav-link {{ request()->segment(3) == 'add' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>
                                      <p>Add Pencil</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endif
                  @if ($user->group_id == config('constants.groups.admin'))
                      <li class="nav-item {{ request()->segment(2) == 'venuegroups' || request()->segment(2) == 'vgroup' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'venuegroups' ? 'active' : '' }}">
                              <i class="nav-icon far fa-hospital"></i>
                              <p>
                                  Venue Group
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['venue_groups'])}}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">

                              <li class="nav-item">
                                  <a href="{{ route('admin.venuegroups') }}" class="nav-link {{ request()->segment(2) == 'venuegroups' && request()->segment(3) == '' ? 'active' : '' }}">
                                      <i class="far fa-hospital"></i>
                                      <p> Venue Group List <span class="badge badge-info right">{{($record_count['venue_groups'])}}</span></p>
                                  </a>
                              </li>
                              @if ($user->group_id == config('constants.groups.admin'))
                              <li class="nav-item">
                                <a href="{{ route('admin.vg_type', 'trash') }}" class="nav-link {{ request()->segment(2) == 'vgroup' && request()->segment(3) == 'trash' ? 'active' : '' }}">
                                    <i class="far fa-hospital"></i>
                                    <p>Trashed Venue Group </p>
                                </a>
                            </li>
                              <li class="nav-item">
                                  <a href="{{ route('venuegroups.addform') }}" class="nav-link {{ request()->segment(2) == 'venuegroups' && request()->segment(3) == 'add' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>
                                      <p>Add Venue Group</p>
                                  </a>
                              </li>
                              @endif
                          </ul>
                      </li>
                  @endif
                  @if ($user->group_id == config('constants.groups.admin') ||
                      $user->group_id == config('constants.groups.customer'))
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

                 
                     
                      <li class="nav-item {{ request()->segment(2) == 'bookings' || request()->segment(2) == 'booking' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'bookings' ? 'active' : '' }}">
                              <i class="nav-icon fa fa-users"></i>
                              <p>
                                  Bookings
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['total_bookings'])}}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">

                              
                              @if ($user->group_id != config('constants.groups.photographer'))
                              <li class="nav-item">
                                <a href="{{ route('bookings.type','all') }}" class="nav-link {{ request()->segment(2) == 'bookings' && request()->segment(3) == '' ? 'active' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <p> Booking List <span class="badge badge-info right">{{($record_count['total_bookings'])}}</span></p>
                                </a>
                            </li>
                            @else
                            <li class="nav-item">
                                <a href="{{ route('bookings.scheduled') }}" class="nav-link {{ request()->segment(2) == 'bookings' && request()->segment(3) == 'scheduled' ? 'active' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <p> Scheduled Booking <span class="badge badge-info right">{{($record_count['photographer_scheduled'])}}</span> </p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="{{ route('bookings.awaited') }}" class="nav-link {{ request()->segment(2) == 'bookings' && request()->segment(3) == 'awaited' ? 'active' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <p> Awaiting Booking <span class="badge badge-info right">{{($record_count['photographer_awaited'])}}</span></p>
                                </a>
                            </li>

                            
                              @endif

                              @if ($user->group_id == config('constants.groups.admin'))
                                  <li class="nav-item">
                                      <a href="{{ route('bookings.type','trashed') }}" class="nav-link {{ request()->segment(2) == 'booking' && request()->segment(3) == 'trashed' ? 'active' : '' }}">
                                          <i class="fa fa-hospital"></i>
                                          <p>Trashed Bookings</p>
                                      </a>
                                  </li>
                              @endif
                          
                              @if ($user->group_id == config('constants.groups.customer'))
                                  <li class="nav-item">
                                      <a href="{{ route('booking_galleries') }}" class="nav-link {{ request()->segment(2) == 'bookings' && request()->segment(3) == 'galleries' ? 'active' : '' }}">
                                          <i class="fa fa-images"></i>
                                          <p>Gallery</p>
                                      </a>
                                  </li>
                              @endif

                          </ul>
                      </li>

                    
                    
                  @if ($user->group_id == config('constants.groups.admin') ||
                      $user->group_id == config('constants.groups.photograper'))
                      <li class="nav-item {{ request()->segment(2) == 'photographers' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'photographers' ? 'active' : '' }}">
                              <i class="nav-icon fa fa-images"></i>
                              <p>
                                  Photographer
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['photographers'])}}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">

                              <li class="nav-item">
                                  <a href="{{ route('admin.photographers') }}" class="nav-link {{ request()->segment(2) == 'photographers' && request()->segment(3) == '' ? 'active' : '' }}">
                                      <i class="fa fa-images"></i>
                                      <p> Photographer List</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('photographers.addform') }}" class="nav-link {{ request()->segment(2) == 'photographers' && request()->segment(3) == 'add' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>
                                      <p>Add Photographer</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endif

                  @if ($user->group_id == config('constants.groups.admin'))
                      <li class="nav-item {{ request()->segment(2) == 'packages' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'packages' ? 'active' : '' }}">
                              <i class="nav-icon fa fa-images"></i>
                              <p>
                                  Packages
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['total_packages'])}}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">

                              <li class="nav-item">
                                  <a href="{{ route('admin.packages') }}" class="nav-link {{ request()->segment(2) == 'packages' && request()->segment(3) == '' ? 'active' : '' }}">
                                      <i class="fa fa-images"></i>
                                      <p> Packages List <span class="badge badge-info right">{{($record_count['total_packages'])}}</span></p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('packages.openform') }}" class="nav-link {{ request()->segment(2) == 'packages' && request()->segment(3) == 'add' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>
                                      <p>Add Packages</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->segment(2) == 'packages' && request()->segment(3) == 'categories' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>
                                      <p>Categories</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                      <li class="nav-item {{ request()->segment(2) == 'users' ? 'menu-open' : '' }}">
                          <a href="#"
                              class="nav-link {{ request()->segment(2) == 'users' ? 'active' : '' }}">
                              <i class="nav-icon fa fa-users"></i>
                              <p>
                                  USERS
                                  <i class="right fas fa-angle-left"></i>
                                  <span class="badge badge-info right">{{($record_count['admin'])}}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('admin.users') }}" class="nav-link {{ request()->segment(2) == 'users' && request()->segment(3) == '' ? 'active' : '' }}">
                                      <i class="far fa-user nav-icon"></i>
                                      <p>User</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('users.addform') }}" class="nav-link {{ request()->segment(2) == 'users' && request()->segment(3) == 'add' ? 'active' : '' }}">
                                      <i class="fa fa-plus"></i>

                                      <p>Add User</p>
                                  </a>
                              </li>

                          </ul>
                      </li>
                      
                      <li class="nav-item {{ request()->segment(2) == 'reports' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                          <i class="nav-icon far fa-images"></i>
                          <p>Reports<i class="right fas fa-angle-left"></i> </p>
                        </a>
                        <ul class="nav nav-treeview">
                          <li class="nav-item">
                            <a href="{{route('reports.bookings')}}"  class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'bookings' ? 'active' : '' }}">
                              <i class="far fa-copy"></i>
                              <p>Bookings</p>
                            </a>
                          </li>
                          {{-- <li class="nav-item">
                            <a href="{{ route('reports.customer.payments') }}"
                                class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'customer' ? 'active' : '' }}">
                                <i class="fa fa-hospital"></i>
                                <p>Customer Payments</p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('reports.vg.payments') }}"
                                class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'venue-group' ? 'active' : '' }}">
                                <i class="fa fa-hospital"></i>
                                <p>Venue Group Payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.photographer.payments') }}"
                                class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'venue-group' ? 'active' : '' }}">
                                <i class="fa fa-hospital"></i>
                                <p>Photographer Expense</p>
                            </a>
                        </li>
                          
                        </ul>
                      </li>
                      <li class="nav-item {{ request()->segment(2) == 'activity-log' ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->segment(2) == 'activity-log' ? 'active' : '' }}">
                            <i class="nav-icon fa fa-images"></i>
                            <p>Activities
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ url('admin/activity-log') }}" class="nav-link">
                                    <i class="fa fa-images"></i>
                                    <p> Activity Log</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                  @endif

                  @if($role=='Venue')
                <li class="nav-item {{ request()->segment(2) == 'reports' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                          <i class="nav-icon far fa-images"></i>
                          <p>Reports<i class="right fas fa-angle-left"></i> </p>
                        </a>
                        <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.vg.payments') }}"
                                class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'venue-group' ? 'active' : '' }}">
                                <i class="fa fa-hospital"></i>
                                <p>Venue Group Payments</p>
                            </a>
                        </li>
                          
                        </ul>
                      </li>
                  @endif
                  @if($role=='Photographer')
                <li class="nav-item {{ request()->segment(2) == 'reports' ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                          <i class="nav-icon far fa-images"></i>
                          <p>Reports<i class="right fas fa-angle-left"></i> </p>
                        </a>
                        <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.photographer.payments') }}"
                                class="nav-link {{ request()->segment(2) == 'reports' && request()->segment(3) == 'venue-group' ? 'active' : '' }}">
                                <i class="fa fa-hospital"></i>
                                <p>Photographer Expense</p>
                            </a>
                        </li>
                          
                        </ul>
                      </li>
                  @endif
                  <li class="nav-item {{ request()->segment(2) == 'calender' ? 'menu-open' : '' }}">
                      <a href="{{ route('user.calender') }}"
                          class="nav-link {{ request()->segment(2) == 'calender' ? 'active' : '' }}">
                          <i class="nav-icon far fa-calendar-alt"></i>
                          <p>Calendar</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ url('/admin/logout') }}" class="nav-link">
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
