@extends('layouts.app')

@section('title', 'Drag & Drop Units')

@section('content')
    <div id="app" class="container py-5">
        <div class="row">
            @include('units._lists')
            <div class="col-md-6">
                @include('units._merge')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const {createApp} = Vue

        createApp({
            data() {
                return {
                    units: @json($units),
                    removed: [],
                    dragged: null,
                    draggedIndex: null,
                    isOver: false,
                    mainUnitId: null,
                    mergedUnitIds: [],
                }
            },
            computed: {
                allUnits() {
                    return [...this.units, ...this.removed]
                }
            },
            mounted() {
                const self = this
                $(this.$el).find('#mainUnit').select2()

                $(this.$el).find('#mainUnit').on('change', function () {
                    self.mainUnitId = parseInt($(this).val())
                })
            },
            watch: {
                allUnits() {
                    this.$nextTick(() => {
                        const select = $(this.$el).find('#mainUnit')
                        select.empty().append('<option disabled>Select unit</option>')
                        this.allUnits.forEach(unit => {
                            select.append(new Option(unit.title, unit.id, false, unit.id === this.mainUnitId))
                        })
                        select.trigger('change.select2')
                    })
                }
            },
            methods: {
                onDragStart(unit, index) {
                    this.dragged = unit
                    this.draggedIndex = index
                },
                onDragOver() {
                    this.isOver = true
                },
                onDragLeave() {
                    this.isOver = false
                },
                onDrop() {
                    if (this.dragged !== null) {
                        this.removed.push(this.dragged)
                        this.units.splice(this.draggedIndex, 1)
                        this.dragged = null
                        this.draggedIndex = null
                        this.isOver = false
                    }
                },
                toggleMerge(id) {
                    if (id === this.mainUnitId) return
                    const index = this.mergedUnitIds.indexOf(id)
                    if (index === -1) {
                        this.mergedUnitIds.push(id)
                    } else {
                        this.mergedUnitIds.splice(index, 1)
                    }
                },
                submitMerge() {
                    fetch('/units/merge', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            main_unit_id: this.mainUnitId,
                            merge_unit_ids: this.mergedUnitIds
                        })
                    }).then(res => res.json()).then(data => {
                        alert('Merged successfully')
                        location.reload()
                    }).catch(err => {
                        console.error(err)
                        alert('Something went wrong')
                    })
                }
            },
        }).mount('#app')
    </script>
@endpush
