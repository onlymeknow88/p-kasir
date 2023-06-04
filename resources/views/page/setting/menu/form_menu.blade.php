<div class="modal fade" id="modalMenu" tabindex="-1" aria-labelledby="modal-formLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formMenu">
            @csrf
            @method('post')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-formLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="name"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama
                            Menu</label>
                        <div class="col-sm-8">
                            <input type="text" name="nama_menu" id="nama_menu" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="url"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">URL</label>
                        <div class="col-sm-8">
                            <input type="text" name="url" id="url" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="aktif"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Aktif</label>
                        <div class="col-sm-8">
                            <div class="form-check-sm form-switch">
                                <input class="form-check-input" name="aktif" id="aktif" type="checkbox"
                                    id="switch-aktif">
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
                            <select name="parent_id" id="parent_id" class="form-select"
                                data-placeholder="Choose one thing">
                                <option value="">Tidak Ada Menu Parent</option>
                                @foreach ($children_menu as $key => $val)
                                    <option value="{{ $val->key }}">{{ $val->nama_menu }} ({{ $val->menu_status->nama_status }})</option>
                                @endforeach
                            </select>
                            <span class="form-text-12 fw-light text-muted"><em>Untuk highlight menu dan
                                    parent</em></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="icon"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Use
                            Icon</label>
                        <div class="col-sm-9 row">
                            <div class="col-sm-4">
                                <select name="use_icon" id="use_icon" class="form-select">
                                    <option value="Y">Ya</option>
                                    <option value="N">Tidak</option>
                                </select>
                            </div>
                            <div class="col-sm-7">
                                <select name="icon_class" id="icon_class" class="form-select d-none"
                                    data-placeholder="Choose one thing">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="kategori"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Kategori</label>
                        <div class="col-sm-8">
                            <select name="menu_kategori_id" id="menu_kategori_id" class="form-select">
                                @foreach ($menuKategori as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitForm(this.form)">Simpan</button>
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
