<form class="modal-form" id="add-formKategori" action="{{route('aplikasi.menuKategori.store')}}">
    @method('post')
    @csrf
    <div class="mb-3 row">
        <label
            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama
            Satuan</label>
        <div class="col-sm-8">
            <input type="text" name="nama_satuan" id="nama_satuan" class="form-control" value="{{ $unit->nama_satuan }}">
        </div>
    </div>
    <div class="row mb-3">
        <label
            class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Satuan</label>
        <div class="col-sm-8">
            <input type="text" name="satuan" id="satuan" class="form-control" value="{{ $unit->satuan }}">
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $menuKategori->id }}"/>
</form>
