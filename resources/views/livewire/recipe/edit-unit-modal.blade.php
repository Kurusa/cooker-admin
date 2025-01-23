<div class="modal fade" id="kt_modal_edit_unit" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_edit_unit" class="form" wire:submit.prevent="submit">
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_unit_scroll">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Title</label>
                            <input wire:model.defer="title"
                                   name="title"
                                   class="form-control form-control-solid"
                                   placeholder="Unit title"
                                   rows="15"
                            >
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="fv-row mb-7" wire:ignore>
                            <label class="form-label fs-6 fw-semibold">Merge with:</label>
                            <select
                                    wire:model.defer="mergeUnits"
                                    name="mergeUnits"
                                    aria-label="Select units to merge with"
                                    data-control="select2"
                                    data-placeholder="Select units to merge with"
                                    class="form-select form-select-solid multi-select"
                                    data-dropdown-parent="#kt_modal_edit_unit"
                                    multiple="multiple"
                            >
                                <option></option>
                                @foreach(\App\Models\Unit::orderBy('title')->get() as $unitInfo)
                                    <option value="{{ $unitInfo->id }}">{{ $unitInfo->title }} ({{ $unitInfo->ingredients()->count() }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label" wire:loading.remove>Submit</span>
                            <span class="indicator-progress" wire:loading>Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            $(".multi-select")
                .select2()
                .on('change', function () {
                    const values = $(this).val() || [];

                    @this.set('mergeUnits', values);
                });

            Livewire.on('success', function () {
                $('.multi-select').val(null).trigger('change');
            });
        });
    </script>
@endpush
