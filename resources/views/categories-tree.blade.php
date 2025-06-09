@extends('layouts.app')

@section('title', 'Categories Tree (TreantJS)')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css">
    <link rel="stylesheet" href="{{ asset('css/category-tree.css?v=10') }}">
@endpush

@section('content')
    <div id="tree-container" class="my-5"></div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script>
    <script src="{{ asset('js/category-tree.js?v=5') }}"></script>
@endpush
