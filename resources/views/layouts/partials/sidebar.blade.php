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
                                         @foreach (Helper::main_menu() as $mm)
                                             @if ($mm->link == '#' && $mm->menu_kategori_id == $mk->id)
                                                 <a href="{{ $mm->link . '' . $mm->url }}"
                                                     data-bs-toggle="collapse" class="item-link {{ Request::is($mm->url.'/*')  ? 'active' : '' }}" aria-expanded="{{ Request::is($mm->url.'/*') ? 'true' : 'false' }}">
                                                     <div class="item-icon">
                                                         <i class="far {{ $mm->class }} text-black form-text-22"></i>
                                                     </div>
                                                     <div class="item-title">
                                                         {{ $mm->nama_menu }}
                                                     </div>
                                                 </a>
                                                 <div class="collapse {{ Request::is($mm->url.'/*') ? 'show' : '' }}" id="{{ $mm->url }}">
                                                     <div class="sidebar-dropdown-collapse">
                                                         <div class="sidebar-dropdown-content">
                                                             @foreach (Helper::sub_menu() as $sm)
                                                                 @if ($sm->parent_id == $mm->id)
                                                                     @if ($sm->link == '#')
                                                                         <a href="{{ $sm->link.''.$sm->nama_menu }}" data-bs-toggle="collapse"
                                                                             class="sub-item-links"
                                                                             aria-expanded="false">
                                                                             <div class="item-icon">
                                                                                 <i class="fas {{ $sm->class }} text-black"></i>
                                                                             </div>
                                                                             <div class="item-title">
                                                                                 {{ $sm->nama_menu}}
                                                                             </div>
                                                                         </a>

                                                                         <div class="collapse" id="{{$sm->nama_menu}}">
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
                                                                     @else
                                                                         <a href="{{ route($mm->url.'.'.$sm->url.'.index') }}"
                                                                             class="sub-item-links {{ Request::is($mm->url.'/'.$sm->url) || Request::is($mm->url.'/'.$sm->url.'/*') ? 'active' : '' }}">
                                                                             <div class="item-icon">
                                                                                 <i
                                                                                     class="fas {{ $sm->class }} text-black"></i>
                                                                             </div>
                                                                             <div class="item-title">
                                                                                 {{ $sm->nama_menu }}
                                                                             </div>
                                                                         </a>
                                                                     @endif
                                                                 @endif
                                                             @endforeach
                                                         </div>
                                                     </div>
                                                 </div>
                                             @elseif ($mm->menu_kategori_id == $mk->id)
                                                 <a href="/{{ $mm->url }}"
                                                     class="item-link {{ Request::is($mm->url) ? 'active' : '' }}">
                                                     <div class="item-icon">
                                                         <i class="fas {{ $mm->class }} text-black"></i>
                                                     </div>
                                                     <div class="item-title">
                                                         {{ $mm->nama_menu }}
                                                     </div>
                                                 </a>
                                             @endif
                                         @endforeach
                                     @endif
                                 @endforeach
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>
