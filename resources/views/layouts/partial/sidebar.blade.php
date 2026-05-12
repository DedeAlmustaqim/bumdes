  <!-- ========== Left Sidebar Start ========== -->
  <div class="vertical-menu">

      <div data-simplebar class="h-100">

          {{-- <!-- User details -->
          <div class="user-profile text-center mt-3">
              <div class="">
                  <img src="assets/bartim.png" alt="" class="avatar-md ">
              </div>
              <div class="mt-3">
                  <h4 class="font-size-16 mb-1">Dinas Kesehatan</h4>
                  <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i>
                      Online</span>
              </div>
          </div> --}}

          <!--- Sidemenu -->
          <div id="sidebar-menu">
              <!-- Left Menu Start -->
              <ul class="metismenu list-unstyled" id="side-menu">
                  <li class="menu-title">Menu</li>

                  <li>
                      <a href="{{ url('dashboard') }}" class="waves-effect">
                              <i class="ri-dashboard-line"></i>
                              <span>Dashboard</span>
                          </a>

                  </li>
                  {{-- admin --}}
                  @if (auth()->user()->role == 'administrator-sistem')
                      <li>
                          <a href="javascript: void(0);" class="has-arrow waves-effect">
                              <i class="ri-database-2-line"></i>
                              <span>Master Data</span>
                          </a>
                          <ul class="sub-menu" aria-expanded="false">
                              <li><a href="{{ url('admin/master/kecamatan') }}">Kecamatan</a></li>
                              <li><a href="{{ url('admin/master/desa') }}">Desa</a></li>
                              <li><a href="{{ url('admin/master/bumdes') }}">Bumdes</a></li>
                              <li><a href="{{ url('admin/master/opd') }}">OPD</a></li>
                              
                          </ul>
                      </li>
                      <li>
                          <a href="javascript: void(0);" class="has-arrow waves-effect">
                              <i class="ri-user-2-line"></i>
                              <span>Kelola Pengguna</span>
                          </a>
                          <ul class="sub-menu" aria-expanded="false">
                              <li><a href="{{ url('admin/user/petugas ') }}">Petugas </a></li>
                              <li><a href="{{ url('admin/user/operator-bumdes') }}">Operator Bumdes</a></li>
                              <li><a href="{{ url('admin/user/operator-opd') }}">Operator OPD</a></li>
                             
                              
                          </ul>
                      </li>
                      <li>
                          <a href="{{ url('verifikasi') }}" class=" waves-effect">
                              <i class="mdi mdi-check-circle-outline"></i>
                              <span>Verifikasi Dokumen</span>
                          </a>
                      </li>
                      <li>
                          <a href="{{ url('desa/approve-dokumen') }}" class=" waves-effect">
                              <i class="fas fa-file-signature "></i>
                              <span>Upload Dokumen Final </span>
                          </a>
                      </li>
                  @endif
                  {{-- Operator OPD --}}
                  @if (auth()->user()->role == 'operator-bumdes')
                      <li>
                          <a href="{{ url('verifikasi') }}" class=" waves-effect">
                              <i class="mdi mdi-check-circle-outline"></i>
                              <span>Pengajuan Izin Usaha</span>
                          </a>
                      </li>
                      
                  @endif
                  @if (auth()->user()->role == 'admin_desa')
                      <li>
                          <a href="{{ url('desa/upload-dokumen') }}" class=" waves-effect">
                              <i class="mdi mdi-file-upload-outline"></i>
                              <span>Upload Dokumen</span>
                          </a>
                      </li>
                      <li>
                          <a href="{{ url('desa/dokumen-final') }}" class=" waves-effect">
                              <i class="fas fa-file-signature "></i>
                              <span>Dokumen Final</span>
                          </a>
                      </li>
                  @endif






                  @if (auth()->user()->role == 'super_admin')
                      <li>
                          <a href="{{ url('admin/user-kecamatan') }}" class=" waves-effect">
                              <i class="fas fa-user "></i>
                              <span>Admin Kecamatan</span>
                          </a>
                      </li>
                  @endif

                  @if (auth()->user()->role == 'admin_kecamatan')
                      <li>
                          <a href="{{ url('kecamatan/user-desa') }}" class=" waves-effect">
                              <i class="fas fa-user "></i>
                              <span>Admin Desa</span>
                          </a>
                      </li>
                  @endif
              </ul>
          </div>
          <!-- Sidebar -->
      </div>
  </div>
  <!-- Left Sidebar End -->
