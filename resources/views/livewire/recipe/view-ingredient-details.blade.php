<div>
    <div class="separator separator-dashed my-5"></div>

    @if (!empty($ingredientUnits))
        <table class="table table-sm">
            <thead>
            <tr>
                <th>Unit</th>
                <th>Recipe Count</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ingredientUnits as $unit)
                <tr>
                    <td contenteditable="true"
                        onblur="Livewire.emit('updateUnitTitle', {{ $unit->id }}, this.innerText)">
                        {{ $unit->unit_title }}
                    </td>
                    <td>{{ $unit->recipe_count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
