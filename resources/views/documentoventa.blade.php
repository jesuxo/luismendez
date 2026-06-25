@extends('layouts.master')
@section('title')
   @if($tipofac == 'A'  )
       VENTA NRO: {{$numerod}}
   @endif
   @if($tipofac == 'B'  )
        DEVOLUCION NRO: {{$numerod}}
   @endif
@endsection
@section('css')

@endsection
@section('content')
    <x-breadcrumb title="Documento " pagetitle="{{($tipofac == 'A' or $tipofac == 'Z')? 'Venta':'Devolucion'}} NRO: {{$numerod}}" />
    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card pt-4" id="demo">
                @include('layouts.documento')
            </div>
        </div>

    </div>
    <!--end row-->
@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
