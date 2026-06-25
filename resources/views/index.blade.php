@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .linkunderline:hover{
            text-decoration: underline;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class=" col-lg-3  ">
            <div class="row  ">
                <div class="col-12">

                    <div class="card card-animate" >
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <a href="/resumenVentas"  class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Total ventas</p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span > Ver resumen</span></h4>

                                </a>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="ph-wallet"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12">

                    <div class="card card-animate">
                        <a href="/existencias" class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-warning  opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Costo Inventario </p>
                                    <h4 class="fs-22 fw-semibold mb-3">
                                         <span > Ver reporte inventario</span>
                                    </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                        <i class="ph-sketch-logo"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-12">

                    <div class="card card-animate">
                        <div class="card-body">
                            <a href="/cxc" class="d-flex justify-content-between">
                                <div class="vr rounded bg-danger opacity-50" style="width: 4px;"></div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Cuentas x cobrar </p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span >Ver reporte cxc</span> </h4>

                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-danger-subtle text-danger rounded fs-3">
                                        <i class="ph-currency-dollar-bold"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-lg-3  ">
            <div class="row  ">
                <div class="col-12">

                    <div class="card card-animate" >
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="vr rounded bg-primary opacity-50" style="width: 4px;"></div>
                                <a href="/resumenVentas"  class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted fs-14 text-truncate">Ventas de cauchos</p>
                                    <h4 class="fs-22 fw-semibold mb-3"><span > Ver detallado</span></h4>

                                </a>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>


    </div>


@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>


@endsection
