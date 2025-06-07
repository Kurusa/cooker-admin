<div class="unit-panel">
    <h5 class="mb-3">Merge Units</h5>

    <div class="mb-3">
        <label for="mainUnit" class="form-label">Main unit:</label>
        <select id="mainUnit" class="form-select">
            <option value="" disabled selected>Select unit</option>
            <option v-for="unit in allUnits" :value="unit.id">
                @{{ unit.title }} (@{{ unit.ingredient_count }})
            </option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Merge these into it:</label>
        <div class="unit-list">
            <span v-for="unit in allUnits"
                  :key="'merge-' + unit.id"
                  class="badge unit-badge"
                  :class="{
                    'bg-secondary': mainUnitId !== unit.id && !mergedUnitIds.includes(unit.id),
                    'bg-success': mergedUnitIds.includes(unit.id),
                    'bg-dark': mainUnitId === unit.id
                  }"
                  @click="toggleMerge(unit.id)">
                @{{ unit.title }} (@{{ unit.ingredient_count }})
            </span>
        </div>
    </div>

    <button class="btn btn-outline-success"
            :disabled="!mainUnitId || mergedUnitIds.length === 0"
            @click="submitMerge">
        Merge Units
    </button>
</div>
