<div class="modal fade" id="kt_modal_add_recipe" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_recipe_header">
                <h2 class="fw-bold">Add Recipe</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_add_recipe_form" class="form" wire:submit.prevent="submit">
                    <input type="hidden" wire:model.defer="recipe_id" name="recipe_id" />
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_add_recipe_scroll">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Title</label>
                            <input type="text" wire:model.defer="title" class="form-control form-control-solid" placeholder="Recipe title" />
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Complexity</label>
                            <select wire:model.defer="complexity" class="form-select form-control-solid">
                                <option value="">Select Complexity</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                            @error('complexity') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-semibold fs-6 mb-2">Advice</label>
                            <textarea wire:model.defer="advice" class="form-control form-control-solid" placeholder="Recipe advice"></textarea>
                            @error('advice') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Time</label>
                            <input type="number" wire:model.defer="time" class="form-control form-control-solid" placeholder="Cooking time in minutes" />
                            @error('time') <span class="text-danger">{{ $message }}</span> @enderror
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
