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
                                 @foreach (Helper::menuKategori() as $mk)
                                     @if ($mk->show_title == 'Y')
                                         <div class="d-flex flex-column py-2">
                                             <span
                                                 class="form-text-13 fw-bold text-gray">{{ $mk->nama_kategori }}</span>
                                             <span class="form-text-12 fw-light text-gray">{{ $mk->deskripsi }}</span>
                                         </div>
                                         <div class="horizontal-line my-2"></div>
                                     @endif
                                     {!! Helper::build_menu(Route::current()->getName(), $mk->id) !!}
                                 @endforeach
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
