@extends('layouts.master')
@section('title')
    Transferencias
@endsection
@section('css')
    <!-- extra css -->
    <!-- Sweet Alert css-->
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <x-breadcrumb title="Listado de transferencias" pagetitle="Transferencias" />

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-info-subtle border-0 overflow-hidden" onclick="window.location.href='/transferencias/status/0'"
            style="cursor:pointer;"
            >
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1551&quot;)" fill="none">
                            <path d="M306 65L446 -75" stroke-width="8" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M399 2L315 86" stroke-width="10" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M83 77L256 -96" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M281 212L460 33" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M257 62L76 243" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M305 123L214 214" stroke-width="6" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M327 222L440 109" stroke-width="6" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M287 109L362 34" stroke-width="10" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M259 194L332 121" stroke-width="8" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M376 186L240 322" stroke-width="8" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M308 153L123 338" stroke-width="6" stroke="url(#SvgjsLinearGradient1553)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M218 62L285 -5" stroke-width="8" stroke="url(#SvgjsLinearGradient1552)"
                                stroke-linecap="round" class="BottomLeft"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1551">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1552">
                                <stop stop-color="rgba(var(--tb-info-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-info-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1553">
                                <stop stop-color="rgba(var(--tb-info-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-info-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body position-relative">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fs-14 fw-medium text-muted mb-0">Pendiente aprovaci&oacute;n</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <span class="badge bg-info me-1">{{$pendientes}}</span> <span class="text-muted align-bottom">Pendientes</span>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-white text-primary rounded fs-3">
                                <i class="ph-file-text"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>

        <div class="col-xl-3 col-md-6"  >
            <div class="card card-animate bg-success-subtle border-0 overflow-hidden" style="cursor: pointer"
                 onclick="window.location.href='/transferencias/status/1'">
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1608&quot;)" fill="none">
                            <path d="M390 87L269 208" stroke-width="10" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M358 175L273 260" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M319 84L189 214" stroke-width="10" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M327 218L216 329" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M126 188L8 306" stroke-width="8" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M220 241L155 306" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M361 92L427 26" stroke-width="6" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M391 188L275 304" stroke-width="8" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M178 74L248 4" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M84 52L-56 192" stroke-width="6" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M183 111L247 47" stroke-width="10" stroke="url(#SvgjsLinearGradient1610)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M46 8L209 -155" stroke-width="6" stroke="url(#SvgjsLinearGradient1609)"
                                stroke-linecap="round" class="TopRight"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1608">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1609">
                                <stop stop-color="rgba(var(--tb-success-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-success-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1610">
                                <stop stop-color="rgba(var(--tb-success-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-success-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body position-relative">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fs-14 fw-medium text-muted mb-0">Aprobadas</p>
                        </div>

                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>

                            <span class="badge bg-info me-1">{{$aprobadas}}</span> <span class="text-muted">Receptor Aprob&oacute;</span>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-white text-success rounded fs-3">
                                <i class="ph-check-square-offset"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate bg-danger-subtle border-0 overflow-hidden"
                 onclick="window.location.href='/transferencias/status/2'" style="cursor: pointer">
                <div class="position-absolute end-0 start-0 top-0 z-0">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        width="400" height="250" preserveAspectRatio="none" viewBox="0 0 400 250">
                        <g mask="url(&quot;#SvgjsMask1560&quot;)" fill="none">
                            <path d="M306 65L446 -75" stroke-width="8" stroke="url(#SvgjsLinearGradient1558)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M399 2L315 86" stroke-width="10" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M83 77L256 -96" stroke-width="6" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M281 212L460 33" stroke-width="6" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M257 62L76 243" stroke-width="6" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M305 123L214 214" stroke-width="6" stroke="url(#SvgjsLinearGradient1558)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M327 222L440 109" stroke-width="6" stroke="url(#SvgjsLinearGradient1558)"
                                stroke-linecap="round" class="BottomLeft"></path>
                            <path d="M287 109L362 34" stroke-width="10" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M259 194L332 121" stroke-width="8" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M376 186L240 322" stroke-width="8" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M308 153L123 338" stroke-width="6" stroke="url(#SvgjsLinearGradient1559)"
                                stroke-linecap="round" class="TopRight"></path>
                            <path d="M218 62L285 -5" stroke-width="8" stroke="url(#SvgjsLinearGradient1558)"
                                stroke-linecap="round" class="BottomLeft"></path>
                        </g>
                        <defs>
                            <mask id="SvgjsMask1560">
                                <rect width="400" height="250" fill="#ffffff"></rect>
                            </mask>
                            <linearGradient x1="100%" y1="0%" x2="0%" y2="100%"
                                id="SvgjsLinearGradient1558">
                                <stop stop-color="rgba(var(--tb-danger-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-danger-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                            <linearGradient x1="0%" y1="100%" x2="100%" y2="0%"
                                id="SvgjsLinearGradient1559">
                                <stop stop-color="rgba(var(--tb-danger-rgb), 0)" offset="0"></stop>
                                <stop stop-color="rgba(var(--tb-danger-rgb), 0.1)" offset="1"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="card-body position-relative">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-uppercase fs-14 fw-medium text-muted mb-0">Rechazadas</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>

                            <span class="badge bg-info me-1">{{$rechazadas}}</span> <span class="text-muted">No aceptadas</span>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-white text-danger rounded fs-3">
                                <i class="ph-trash"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="invoiceList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1" style="font-weight: 100 !important;">Listado de
                            @if(!$busquedatransf and !$fechas and !$status)
                                las &uacute;ltimas 100
                            @endif

                            @if( $fechas)
                                las
                            @endif
                            Transferencias

                            @if( $busquedatransf)
                               buscando por:   <b> {{$busquedatransf}} </b>
                            @endif

                            @if( $fechas)
                             del   <b>  {{str_replace('to','al',$fechas)}}</b>
                            @endif

                            @if( $status == 0)   <b>  Pendientes por aprobar</b> @endif
                            @if( $status == 1)   <b>que fueron Aprobadas  </b> @endif
                            @if( $status == 2)   <b>que fueron Rechazadas </b> @endif
                        </h5>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-danger btn-icon" id="remove-actions" onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                                <a href="/transferencias/create" class="btn btn-primary"><i
                                        class="ri-add-line align-bottom me-1"></i>1 Transferencia</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-soft-light border border-dashed border-start-0 border-end-0">

                    <form name="form1" id="form1" action="/transferencias" >
                        @method('GET')
                        @csrf

                        <div class="row g-3">
                            <div class="col-xxl-5 col-sm-12">
                                <div class="search-box">
                                    <input type="text" id="busquedatransf" name="busquedatransf"  value="{{$busquedatransf}}"
                                           class="form-control search bg-light border-light"
                                        placeholder="Puede buscar: Titular, Nro Transferencia ...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-sm-4">
                                <input type="text" class="form-control bg-light border-light" id="fechas"
                                       data-range-date="true" data-date-format="d/m/Y" name="fechas"
                                       data-deafult-date="" data-provider="flatpickr" value="{{$fechas}}"
                                       placeholder="Fechas">
                            </div>

                            <div class="col-xxl-3 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false
                                        name="status" id="status">
                                        <option value="2222" @if($status == '2222') selected @endif>Todas</option>
                                        <option value="1" @if($status == 1)  selected @endif>Aprobadas</option>
                                        <option value="2" @if($status == 2)  selected @endif>Rechazadas</option>
                                        <option value="0" @if($status == 0)  selected @endif>Pendientes</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-xxl-1 col-sm-4">
                                <button type="submit" class="btn btn-info w-100"  >
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                </button>
                            </div>

                        </div>

                    </form>

                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card">
                            <table class="table align-middle table-nowrap" id="transferenciaTable">
                                <thead class="text-muted">
                                    <tr>
                                       <!-- <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll"
                                                    value="option">
                                            </div>
                                        </th>-->
                                        <th class="sort" data-sort="numero">Nro.Transf</th>
                                        <th class="sort" data-sort="titular">Titular</th>
                                        <th class="sort" data-sort="banco">Banco</th>
                                        <th class="sort" data-sort="date">Fecha</th>
                                        <th class="sort" data-sort="monto">Monto</th>
                                        <th class="sort" data-sort="status">Status</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all" id="transferencias-list-data">

                                </tbody>
                            </table>

                        </div>
                        <!-- <div class="d-flex justify-content-end mt-3"  style="display: none">
                               <div class="pagination-wrap hstack gap-2 flex-wrap">
                                   <a class="page-item pagination-prev disabled" href="#">
                                       Previous
                                   </a>
                                   <ul class="pagination listjs-pagination mb-0"></ul>
                                   <a class="page-item pagination-next" href="#">
                                       Next
                                   </a>
                               </div>
                           </div>l -->
                   </div>

                   <!-- Modal -->
                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                                    </lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4>Desea eliminar esta transferencia?</h4>
                                        <p class="text-muted fs-15 mb-4">
                                            Borrando este registro ud eliminar&aacute; la informaci&oacute;n de la base de datos </p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button class="btn btn-link link-success fw-medium text-decoration-none"
                                                id="deleteRecord-close" data-bs-dismiss="modal"><i
                                                    class="ri-close-line me-1 align-middle"></i> Cancelar</button>
                                            @if( auth()->user()->can('menu_transferencias_eliminar') )
                                                <button class="btn btn-danger" id="deleterecord" data-id="">Si, Eliminar</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->
                </div>
            </div>

        </div>

    </div>

@endsection
@section('scripts')
    <script>
        $('#deleterecord').unbind('click').bind('click',function () {
            var id = $(this).data('id');

            $.ajax({
                type: 'DELETE',
                url: '/transferencias/'+id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    var deleted = data.deleted;

                    if(deleted == 1) {
                        $('#tr'+id).hide();
                        $("#deleteRecord-close").click();
                        //window.location.href='/transferencias';
                    }else{
                        console.log('no se pudo eliminar');
                    }
                }
            });
        });
    </script>
    <!-- list.js min js -->
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>

    <!--list pagination js-->
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>


    <script src="{{ URL::asset('build/js/backend/transferencias.init.js') }}?version={{rand(0,500)}}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
