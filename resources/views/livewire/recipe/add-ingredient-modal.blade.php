<div class="modal fade" id="kt_modal_add_ingredient" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_ingredient_header">
                <h2 class="fw-bold">Add Ingredient</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_add_ingredient_form" class="form" wire:submit.prevent="submit">
                    <input type="hidden" wire:model.defer="ingredient_id" name="ingredient_id" value="{{ $ingredient_id }}"/>
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_ingredient_scroll">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Title</label>
                            <input type="text" wire:model.defer="title" class="form-control form-control-solid" placeholder="Ingredient title" />
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Unit</label>
                            <input type="text" wire:model.defer="unit" class="form-control form-control-solid" placeholder="Ingredient unit" />
                            @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
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
