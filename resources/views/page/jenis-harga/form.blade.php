<form class="modal-form" id="add-form" action="{{ route('jenis-harga.store') }}">
    @method('post')
    @csrf
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">
            Jenis Harga</label>
        <div class="col-sm-8">
            <input type="text" name="nama_jenis_harga" id="nama_jenis_harga" class="form-control"
                value="{{ $jenisharga->nama_jenis_harga }}">
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Deskripsi</label>
        <div class="col-sm-8">
            <textarea type="text" name="deskripsi" id="deskripsi" class="form-control">{{ $jenisharga->deskripsi }}</textarea>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $jenisharga->id }}" />
</form>
