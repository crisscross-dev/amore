@extends('layouts.app-public')

@section('title', $__env->yieldContent('title', 'Admission - Amore Academy'))

@push('styles')
@vite(['resources/css/admissions.css'])
@endpush

@section('content')
@yield('content')
@endsection