@extends('layouts.master')
@section('title')
    Actualizaci&oacute&oacute;n de  producto
@endsection
@section('css')
    <style>
        .dropzone-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .dropzone-wrapper:hover {
            border-color: #0d6efd;
            background-color: #e9ecef;
        }

        .dropzone-wrapper.dragover {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .galeria-imagen .imagen-card {
            position: relative;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .galeria-imagen .imagen-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .galeria-imagen .imagen-card .badge-tipo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
        }

        .galeria-imagen .imagen-card .acciones {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 8px;
            display: flex;
            justify-content: center;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover .acciones {
            opacity: 1;
        }

        .galeria-imagen .imagen-card .acciones .btn {
            padding: 2px 8px;
            font-size: 12px;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Modificacion de producto" pagetitle="Productos" />
    <form id="editproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{route('productos.update',$id)}}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1 ">Informaci&oacute;n</h5>
                                <p class="text-muted mb-0">Ingrese/Modifique los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="display:none !important;">
                            <label class="form-label">Product description</label>

                            <div id="ckeditor-classic">
                                <p>Tommy Hilfiger men striped pink sweatshirt. Crafted with cotton. Material composition is
                                    100% organic cotton. This is one of the world’s leading designer lifestyle brands and is
                                    internationally recognized for celebrating the essence of classic American cool style,
                                    featuring preppy with a twist designs.</p>
                                <ul>
                                    <li>Full Sleeve</li>
                                    <li>Cotton</li>
                                    <li>All Sizes available</li>
                                    <li>4 Different Color</li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario</label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline">+1 Instancia</a>
                                </div>
                            </div>
                            <div>
                                <select onchange="$('.error-msg').hide();  " class="form-select" data-choices  required
                                        id="choices-category-input" name="codinst">
                                    @foreach($instancias as $instancia)
                                        <option  {{($instancia->codinst == $producto->codinst)?'selected':''}} style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <div class="card "  >

                    <div class="card-body">
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled value="{{$producto->codprod}}"
                                           placeholder="" required onclick="$('#invalidcodprod').html('C&oacute;digo'); $('#invalidcodprod').removeClass('text-danger');">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"  value="{{$producto->refere}}"
                                           placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="edit">

                            <input type="hidden" class="form-control" id="isadmin" name="isadmin"
                                   value="{{(Auth::user() and auth()->user()->type == 'admin')? 1: 0}}">

                            <input type="text" class="form-control d-none" id="product-id-input">

                            <input type="text" class="form-control" id="descrip" value="{{$producto->descrip}}"
                                   placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2"   value="{{$producto->descrip2}}"
                                   placeholder="Descripci&oacute;n 2" >
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3"  value="{{$producto->descrip3}}"
                                   placeholder="Descripci&oacute;n 3" >
                        </div>
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <input type="text" class="form-control" id="marca"  value="{{$producto->marca}}" name="marca"
                                           placeholder="Ej: POLAR">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"value="{{$producto->unidad}}"
                                           placeholder="Ej: Kg">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6"  style="display: none">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="esexento" name="esexento"  {{($producto->esexento)?'checked':''}} value="1">
                                    <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" {{(isset($producto->exdecimal) and $producto->exdecimal == 1)?'checked':''}}    value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Sección de imágenes -->


                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Modificar</button>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <div>

                            <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="activo">
                                <option value="1" {{( $producto->activo == 1)?'selected':''}}>Activo</option>
                                <option value="0" {{( $producto->activo == 0)?'selected':''}}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                </div>

                @if(Auth::user() and ( auth()->user()->can('products_cots_view')  or auth()->user()->can('products_prices')  )  )
                <div class="card"  >
                    <div class="card-header">
                        @if(Auth::user() and ( auth()->user()->can('products_cots')   )  )
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo anterior</label>
                                <input type="text" class="form-control" id="preciodant" value="{{$producto->preciodant}}"  name="preciodant"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo promedio</label>
                                <input type="text" class="form-control" id="preciodpro" value="{{$producto->preciodpro}}"  name="preciodpro"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="preciod">Costo</label>
                                <input type="text" class="form-control" id="preciod" value="{{$producto->preciod}}"  name="preciod"
                                       placeholder="Ej: 15" required style="width: 70px">
                            </div>
                        @endif
                        @if(Auth::user() and ( auth()->user()->can('products_cots_view')   )  and !( auth()->user()->can('products_cots')   )  )
                                <div class="d-flex justify-content-between">
                                   <label class="form-label" for="">Costo anterior: </label>
                                   <div>{{$producto->preciodant}}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                   <label class="form-label" for="">Costo promedio: </label>
                                   <div> {{$producto->preciodpro}}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="" >Costo actual: </label>
                                    <div>{{$producto->preciod}}</div>
                                </div>
                        @endif

                        @if(Auth::user() and (  auth()->user()->can('products_prices')  )  )
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod">Precio1</label>
                                    <input type="text" class="form-control" id="costod" value="{{$producto->costod}}" name="costod"
                                           placeholder="Ej: 18" style="width: 70px">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod2">Precio2</label>
                                    <input type="text" class="form-control" id="costod2" value="{{$producto->costod2}}"name="costod2"
                                           placeholder="Ej: 20" style="width: 70px">
                                </div>
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="costod3">Precio3</label>
                                    <input type="text" class="form-control" id="costod3" value="{{$producto->costod3}}"name="costod3"
                                           placeholder="Ej: 22.5" required style="width: 70px">
                                </div>
                        @endif
                    </div>

                </div>
                @endif

            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <!-- create-product -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ URL::asset('build/js/backend/edit-product.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
