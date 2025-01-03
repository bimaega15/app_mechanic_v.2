<div class="row mb-3">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-lg-5">
                <x-form-input-vertical label="Dari Tanggal" name="dari_tanggal" placeholder="Dari Tanggal"
                    class="datepicker1" value="{{ $dari_tanggal }}" />
            </div>

            <div class="col-lg-5">
                <x-form-input-vertical label="Sampai Tanggal" name="sampai_tanggal" placeholder="Sampai Tanggal"
                    class="datepicker2" value="{{ $sampai_tanggal }}" />
            </div>

            <div class="col-lg-2">
                <div style="margin-top: 30px;">
                    <button type="button" class="btn btn-primary btn-filter me-2">
                        <i class="fa-solid fa-filter me-2"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
