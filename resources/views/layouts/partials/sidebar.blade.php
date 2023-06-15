 <!-- sidebar -->
 <section class="sidebar" id="sidebar">
     <div class="menus">
         <nav class="items">
             @foreach (Helper::menuKategori() as $mk)
                 @if ($mk->show_title == 'Y')
                     <div class="d-flex flex-column py-1 px-4 mt-3">
                         <span class="form-text-13 fw-bold text-gray">{{ $mk->nama_kategori }}</span>
                         <span class="form-text-12 fw-light text-gray">{{ $mk->deskripsi }}</span>
                     </div>
                     <div class="horizontal-line my-2 px-4"></div>
                 @endif
                 {!! Helper::build_menu(Helper::menu(), url()->current(), $mk->id) !!}
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
     <div class="sidebar-space"></div>
 </section>
