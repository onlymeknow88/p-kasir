 <!-- sidebar -->
 <section class="sidebar" id="sidebar">
     <div class="sidebar-space"></div>
     <div class="content">
         <div class="sidebar-content-items">
             <div class="section-sidebar">
                 <div class="section-sidebar-items">
                     <div class="section-items">
                         {{-- <div class="menus">
                             <nav class="items">
                                 <ul>
                                     <li>
                                         <a class="has-children" href="" onclick="javascript:void(0)">
                                             <span class="menu-item">
                                                 <i class="sidebar-menu-icon far fa-sun"></i>
                                                 <span class="text">Aplikasi</span>
                                             </span>
                                             <span class="pull-right-container">
                                                 <i class="fa fa-angle-left arrow"></i>
                                             </span>
                                         </a>
                                         <ul class="submenu">
                                             <li>
                                                 <a class="depth-1" href="http://localhost/kasir/pos-kasir"><span
                                                         class="menu-item"><i
                                                             class="sidebar-menu-icon fas fa-calculator"></i><span
                                                             class="text">Kasir</span></span></a>
                                             </li>
                                             <li>
                                                 <a class="depth-1" href="http://localhost/kasir/penjualan-mobile"><span
                                                         class="menu-item"><i
                                                             class="sidebar-menu-icon fas fa-receipt"></i><span
                                                             class="text">Penjualan</span></span></a>
                                             </li>
                                             <li>
                                                 <a class="depth-1" href="http://localhost/kasir/barang-mobile"><span
                                                         class="menu-item"><i
                                                             class="sidebar-menu-icon fas fa-box-open"></i><span
                                                             class="text">Update Stok</span></span></a>
                                             </li>
                                         </ul>
                                     </li>
                                 </ul>
                             </nav>
                         </div> --}}
                         <div class="menus">
                             <nav class="items">
                                 @foreach (Helper::menuKategori() as $mk)
                                     @if ($mk->show_title == 'Y')
                                         <div class="d-flex flex-column py-1 px-4">
                                             <span
                                                 class="form-text-13 fw-bold text-gray">{{ $mk->nama_kategori }}</span>
                                             <span class="form-text-12 fw-light text-gray">{{ $mk->deskripsi }}</span>
                                         </div>
                                         <div class="horizontal-line my-2 px-4"></div>
                                     @endif
                                     {!! Helper::build_menu(Helper::menu(),url()->current(), $mk->id) !!}
                                 @endforeach

                                 <div class="horizontal-line my-2 px-4"></div>
                                 <div class="form-text-14 text-center text-muted">
                                     @php
                                         $footer = str_replace('{{ YEAR }}', date('Y'), Helper::settingApp()['footer_app']);
                                     @endphp
                                     {!! html_entity_decode($footer) !!}
                                 </div>
                             </nav>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
