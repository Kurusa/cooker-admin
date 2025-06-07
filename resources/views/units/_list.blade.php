<div class="col-md-6">
    <div class="unit-panel">
        <h5>Units (@{{ units.length }})</h5>
        <div class="droppable">
            <div class="unit-list">
                        <span
                            v-for="(unit, index) in units"
                            :key="unit.id"
                            class="badge bg-info-subtle text-dark unit-badge"
                            draggable="true"
                            @dragstart="onDragStart(unit, index)"
                        >
                            @{{ unit.title }} (@{{ unit.ingredient_count }})
                        </span>
            </div>
        </div>
    </div>
</div>
