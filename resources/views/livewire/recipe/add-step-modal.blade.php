<div class="modal fade" id="kt_modal_add_step" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_step_header">
                <h2 class="fw-bold">Add Step</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_add_step_form" class="form" wire:submit.prevent="submit">
                    <input type="hidden" wire:model.defer="step_id" name="step_id" />
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_step_scroll">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Description</label>
                            <textarea wire:model.defer="description" name="description" class="form-control form-control-solid" placeholder="Step description"></textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Index</label>
                            <input type="number" wire:model.defer="index" class="form-control form-control-solid" placeholder="Step index" />
                            @error('index') <span class="text-danger">{{ $message }}</span> @enderror
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
