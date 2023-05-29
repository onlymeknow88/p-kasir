<div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modal-formLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formKategori">
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
                            Kategori</label>
                        <div class="col-sm-8">
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="deskripsi"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="aktif"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Aktif</label>
                        <div class="col-sm-8">
                            <select name="aktif" id="aktif" class="form-select">
                                <option value="Y">Ya</option>
                                <option value="N">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="show_title"
                            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Show Title</label>
                        <div class="col-sm-8">
                            <select name="show_title" id="show_title" class="form-select">
                                <option value="Y">Ya</option>
                                <option value="N">Tidak</option>
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
