<form class="modal-form" id="add-formKategori" action="{{ route('refrensi.kategori.store') }}">
    @method('post')
    @csrf
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama
            Kategori</label>
        <div class="col-sm-8">
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control"
                value="{{ $kategori->nama_kategori }}">
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Deskripsi</label>
        <div class="col-sm-8">
            <input type="text" name="deskripsi" id="deskripsi" class="form-control"
                value="{{ $kategori->deskripsi }}">
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Aktif</label>
        <div class="col-sm-8">
            <div class="form-check-sm form-switch">
                <input class="form-check-input" name="aktif" id="aktif" type="checkbox" value="1"
                    id="switch-aktif" {{ $kategori->aktif == 'Y' ? 'checked="checked"' : '' }}>
                <label class="form-check-label" for="switch-aktif"></label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <label for="icon"
            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Use
            Icon</label>
        <div class="col-sm-9 row">
            <div class="col-sm-4">
                @php
                    $selected = @$kategori['icon'] ? 1 : 0;
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
                        class="{{ @$kategori->icon ? $kategori->icon : 'far fa-circle' }}"></i></a>
                <input type="hidden" name="icon_class"
                    value="{{ @$kategori->icon ? $kategori->icon : 'far fa-circle' }}" />
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $kategori->id }}" />
</form>
