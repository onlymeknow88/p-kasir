 <!-- sidebar -->
 <section class="sidebar" id="sidebar">
     <div class="sidebar-space"></div>
     <div class="content">
         <div class="sidebar-content-items">
             <div class="section-sidebar">
                 <div class="section-sidebar-items">
                     <div class="section-items">
                         <div class="menus">
                             <div class="items">
                                 <div class="d-flex flex-column py-2">
                                     <span class="form-text-13 fw-bold text-gray">MASTER APLIKASI</span>
                                     <span class="form-text-12 fw-light text-gray">Pengaturan aplikasi</span>
                                 </div>
                                 <div class="horizontal-line my-2"></div>
                                 <a href="#toko" data-bs-toggle="collapse" class="item-link" aria-expanded="false">
                                     <div class="item-icon">
                                         <img src="{{ asset('assets/icon/black/setting.svg') }}" alt="">
                                     </div>
                                     <div class="item-title">
                                         Aplikasi
                                     </div>
                                 </a>
                                 <div class="collapse" id="toko">
                                     <div class="sidebar-dropdown-collapse">
                                         <div class="sidebar-dropdown-content">
                                             <a href="{{ route('menu.index') }}" class="sub-item-links">
                                                 <div class="item-icon">
                                                     <i class="fas fa-clone text-black"></i>
                                                 </div>
                                                 <div class="item-title">
                                                     Menu
                                                 </div>
                                             </a>
                                             <a href="{{ route('menu.index') }}" class="sub-item-links">
                                                 <div class="item-icon">
                                                     <i class="fas fa-network-wired text-black"></i>
                                                 </div>
                                                 <div class="item-title">
                                                     Module
                                                 </div>
                                             </a>
                                             <a href="#setting" data-bs-toggle="collapse" class="sub-item-links"
                                                 aria-expanded="false">
                                                 <div class="item-icon">
                                                     <i class="fas fa-cogs text-black"></i>
                                                 </div>
                                                 <div class="item-title">
                                                     Setting
                                                 </div>
                                             </a>
                                             <div class="collapse" id="setting">
                                                 <div class="sidebar-dropdown-collapse">
                                                     <div class="sidebar-dropdown-content">
                                                         <a href="#" class="sub-item-link">
                                                             <div class="item-title">
                                                                 Setting Aplikasi
                                                             </div>
                                                         </a>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="d-flex flex-column py-2">
                                     <span class="form-text-13 fw-bold text-gray">APLIKASI KOPERASI</span>
                                     <span class="form-text-12 fw-light text-gray">Menu utama aplikasi</span>
                                 </div>
                                 <div class="horizontal-line my-2"></div>
                                 <a href="{{ route('dashboard') }}"
                                     class="item-link {{ Request::is('dashboard') ? 'active' : '' }}">
                                     <div class="item-icon">
                                         <img src="{{ Request::is('dashboard') ? asset('assets/icon/white/dashboard.svg') : asset('assets/icon/black/dashboard.svg') }}"
                                             alt="">
                                     </div>
                                     <div class="item-title">
                                         Dashboard
                                     </div>
                                 </a>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
