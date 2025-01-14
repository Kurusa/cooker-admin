<x-default-layout>

    @section('title')
        Dashboard
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-xxl-6">
            @include('partials/widgets/cards/_widget-18')
        </div>
        <!--end::Col-->
        <!--begin::Col-->
        <div class="col-xl-6">
            @include('partials/widgets/charts/_widget-36')
        </div>
        <!--end::Col-->
    </div>
</x-default-layout>
