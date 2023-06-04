        <form class="modal-form" id="add-form" action="{{route('aplikasi.menuKategori.store')}}">
            @method('post')
            @csrf
            <div class="mb-3 row">
                <label for="name"
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama
                    Kategori</label>
                <div class="col-sm-8">
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="{{ $menuKategori->nama_kategori }}">
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
                    class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Show
                    Title</label>
                <div class="col-sm-8">
                    <select name="show_title" id="show_title" class="form-select">
                        <option value="Y">Ya</option>
                        <option value="N">Tidak</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="{{ $menuKategori->id }}"/>
        </form>
