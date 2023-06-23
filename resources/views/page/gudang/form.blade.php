<form class="modal-form" id="add-form" action="{{ route('list-gudang.store') }}">
    @method('post')
    @csrf
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Nama Gudang</label>
        <div class="col-sm-8">
            <input class="form-control" type="text" name="nama_gudang" value="{{ $gudang->nama_gudang }}" />
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Alamat</label>
        <div class="col-sm-8">
            <textarea class="form-control" name="alamat_gudang" required="required" />{{ $gudang->alamat_gudang }}</textarea>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Deskripsi</label>
        <div class="col-sm-8">
            <textarea type="text" name="deskripsi" id="deskripsi" class="form-control">{{ $gudang->deskripsi }}</textarea>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label form-text-12 text-black text-right mtext-left fw-bold">Default</label>
        <div class="col-sm-8">
            @php
                $options = collect([
                    'N' => 'Tidak',
                    'Y' => 'Ya',
                ]);
            @endphp
            <select class="form-select" type="text" name="default_gudang">
                @foreach ($options as $key => $val)
                    <option value="{{ $key }}" {{ $key == $gudang->default_gudang ? 'selected' : '' }}>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
            <div class="text-muted form-text-12">Default pilihan gudang ketika input form</div>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $gudang->id }}" />
</form>
