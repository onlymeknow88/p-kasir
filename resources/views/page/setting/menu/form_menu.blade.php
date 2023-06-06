        <form id="add-formMenu" class="modal-form" action="{{ route('aplikasi.menu.store') }}">
            @method('post')
            @csrf
            <div class="mb-3 row">
                <label for="nama_menu"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama
                    Menu</label>
                <div class="col-sm-8">
                    <input type="text" name="nama_menu" id="nama_menu" value="{{ $menu->nama_menu }}"
                        class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label for="url"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">URL</label>
                <div class="col-sm-8">
                    <input type="text" name="url" id="url" value="{{ $menu->url }}"
                        class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label for="aktif"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Aktif</label>
                <div class="col-sm-8">
                    <div class="form-check-sm form-switch">
                        <input class="form-check-input" name="aktif" id="aktif" type="checkbox" value="1"
                            id="switch-aktif" {{ $menu->aktif == 'Y' ? 'checked="checked"' : '' }}>
                        <label class="form-check-label" for="switch-aktif"></label>
                    </div>
                    <span class="form-text-12 fw-light text-muted"><em>Jika tidak aktif, semua children tidak
                            akan dimunculkan</em></span>
                </div>
            </div>
            <div class="row mb-3">
                <label for="parent_id"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Parent</label>
                <div class="col-sm-8">
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">Tidak Ada Menu Parent</option>
                        @foreach ($data['menu'] as $key => $val)
                            <option value="{{ $val->id }}" {{ $val->id === $menu->parent_id ? 'selected' : '' }}>
                                {{ $val->nama_menu }}
                                ({{ $val->menu_status->nama_status }})
                            </option>
                        @endforeach
                    </select>
                    <span class="form-text-12 fw-light text-muted"><em>Untuk highlight menu dan
                            parent</em></span>
                </div>
            </div>
            <div class="row mb-3">
                <label for="menu_status_id"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Status</label>
                <div class="col-sm-8">
                    <select name="menu_status_id" id="menu_status_id" class="form-select"
                        data-placeholder="Choose one thing">
                        @foreach ($data['menu_status'] as $key => $val)
                            <option value="{{ $val->id }}"
                                {{ $val->id === $menu->menu_status_id ? 'selected' : '' }}>{{ $val->nama_status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="icon"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Use
                    Icon</label>
                <div class="col-sm-9 row">
                    <div class="col-sm-4">
                        @php
                            $selected = @$menu['class'] ? 1 : 0;
                            $display = $selected ? '' : 'd-none';
                        @endphp

                        <select name="use_icon" id="use_icon" class="form-select">
                            <option value="1" {{ $selected ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ $selected ? '' : 'selected' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="col-sm-7">
                        <a href="javascript:void(0)" class="icon-preview {{ $display }}"
                             data-action="faPicker"><i
                                class="{{ @$menu->class ? $menu->class : 'far fa-circle' }}"></i></a>
                        <input type="hidden" name="icon_class"
                            value="{{ @$menu->class ? $menu->class : 'far fa-circle' }}" />
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="menu_kategori_id"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Kategori</label>
                <div class="col-sm-8">
                    <select name="menu_kategori_id" id="menu_kategori_id" class="form-select">
                        @foreach ($data['kategori'] as $val)
                            <option value="{{ $val->id }}" {{ $val->id === $menu->menu_kategori_id ? 'selected' : '' }}>{{ $val->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="role_id"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Role</label>
                <div class="col-sm-8">
                    <select name="role_id" id="role_id" class="form-select">
                        @foreach ($data['role'] as $val)
                            <option value="{{ $val->id }}">{{ $val->nama_role }}  </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="{{ $menu->id }}">
        </form>
