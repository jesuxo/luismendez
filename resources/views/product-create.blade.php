@extends('layouts.master')
@section('title')
    Crear nuevo producto
@endsection
@section('css')
    <style>
        .card-datosprod {
            transition: all 0.3s ease;
        }

        /* Estilos para el indicador de carga */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-loading .spinner-border {
            display: inline-block !important;
            margin-right: 8px;
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        .btn-loading .btn-text {
            opacity: 0.7;
        }

        /* Animación de guardado exitoso */
        @keyframes saveSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .save-success {
            animation: saveSuccess 0.5s ease;
        }

        /* Notificación flotante */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }

        .toast-notification {
            background: white;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid #28a745;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
            transition: all 0.3s ease;
        }

        .toast-notification.error {
            border-left-color: #dc3545;
        }

        .toast-notification .toast-icon {
            margin-right: 12px;
            font-size: 20px;
        }

        .toast-notification .toast-content {
            flex: 1;
        }

        .toast-notification .toast-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .toast-notification .toast-message {
            color: #6c757d;
            font-size: 14px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Overlay de carga */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9998;
            backdrop-filter: blur(3px);
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .loading-content .spinner-border {
            width: 3rem;
            height: 3rem;
            margin-bottom: 16px;
        }

        .loading-content p {
            margin: 0;
            color: #495057;
            font-weight: 500;
        }

        .loading-content .progress {
            width: 300px;
            margin-top: 16px;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Crear nuevo producto" pagetitle="Productos" />

    <!-- Overlay de carga -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Creando producto...</p>
            <p style="font-size: 14px; color: #6c757d; margin-top: 8px;" id="loadingMessage">Por favor espere</p>
            <div class="progress" style="height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar"
                     style="width: 100%"></div>
            </div>
        </div>
    </div>

    <!-- Contenedor para notificaciones toast -->
    <div class="toast-container" id="toastContainer"></div>

    <form id="createproduct-form" autocomplete="off" class="needs-validation" method="post" novalidate action="{{route('productos.store')}}" onsubmit="return handleFormSubmit(event)">
        @method('POST')
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
                                <h5 class="card-title mb-1">Informaci&oacute;n</h5>
                                <p class="text-muted mb-0">Ingrese los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar errores de validación -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <strong>Error:</strong> Por favor corrija los siguientes problemas:
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario <span class="text-danger">*</span></label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline">+1 Instancia</a>
                                </div>
                            </div>
                            <div>
                                <select onchange="$('.error-msg').hide(); $('.card-datosprod').fadeIn(); verUltimoProd(this.value)"
                                        class="form-select @error('codinst') is-invalid @enderror"
                                        data-choices
                                        required
                                        id="datacodinst"
                                        name="codinst">
                                    <option value=""> Seleccionar </option>
                                    @foreach($instancias as $instancia)
                                        <option style="margin-left: {{($instancia->nivel-1) * 14}}px !important;"
                                                value="{{$instancia->codinst}}"
                                            {{ old('codinst') == $instancia->codinst ? 'selected' : '' }}>
                                            {!! $instancia->label !!}
                                        </option>
                                    @endforeach
                                </select>
                                @error('codinst')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="error-msg mt-1 text-danger" style="display: none;">
                                Por favor, seleccione una instancia del inventario para clasificar este producto.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-datosprod" style="{{ old('codinst') ? 'display: block' : 'display: none' }}">
                    <div class="card-body" style="background-color: #f3f4f4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label @error('codprod') text-danger @enderror" id="invalidcodprod" for="codprod">
                                        C&oacute;digo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('codprod') is-invalid @enderror"
                                           id="codprod"
                                           maxlength="15"
                                           name="codprod"
                                           placeholder="Código único del producto"
                                           required
                                           value="{{ old('codprod', $lastCod ?? '') }}"
                                           onkeyup="limpiarCodigo(); validarCodigo();"
                                           onblur="convertirMayusculas(); validarCaracteresCodigo();"
                                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');"
                                           style="text-transform: uppercase;">
                                    @error('codprod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Por favor, ingrese el código del producto</div>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"
                                           placeholder="Ej: C&oacute;digo de barra" value="{{ old('refere') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto <span class="text-danger">*</span></label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="add">
                            <input type="text" class="form-control @error('descrip') is-invalid @enderror" id="descrip"
                                   value="{{ old('descrip') }}"
                                   placeholder="Descripci&oacute;n principal"
                                   name="descrip"
                                   required>
                            @error('descrip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                                @enderror
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2"
                                   value="{{ old('descrip2') }}"
                                   placeholder="Descripci&oacute;n 2">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3"
                                   value="{{ old('descrip3') }}"
                                   placeholder="Descripci&oacute;n 3">
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <input type="text" class="form-control" id="marca" name="marca"
                                           placeholder="Ej: POLAR" value="{{ old('marca') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"
                                           placeholder="Ej: Kg, Unidad, Litro" value="{{ old('unidad') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="preciodpro">Costo</label>
                                    <input type="number" step="0.01" class="form-control" id="preciodpro" name="preciodpro"
                                           placeholder="Costo del producto" value="{{ old('preciodpro') }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="costod3">Precio de venta</label>
                                    <input type="number" step="0.01" class="form-control" id="costod3" name="costod3"
                                           placeholder="Precio de venta" value="{{ old('costod3') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Indicador de creación (visible solo durante la creación) -->
                        <div id="creationIndicator" style="display: none;" class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <span>Creando producto en todos los comerciales...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success w-sm" id="submitBtn">
                        <span class="btn-text">
                            <i class="bi bi-plus-circle me-1"></i>
                            Guardar Producto
                        </span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3 col-lg-4">
                <div class="card card-datosprod" style="{{ old('codinst') ? 'display: block' : 'display: none' }}">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <select class="form-select" name="activo" id="publish-visibility-input">
                                <option value="1" {{ old('activo', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('activo', 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card card-datosprod" style="{{ old('codinst') ? 'display: block' : 'display: none' }}">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Observaciones</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="observaciones"
                                  placeholder="Ej: solo vender en condiciones especificas"
                                  rows="3">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        <input type="hidden" name="comercial_id" value="{{ session('comercialid', 1) }}">
    </form>
@endsection
@section('scripts')
    <script>
        // Mostrar/ocultar campos según instancia seleccionada
        $(document).ready(function() {
            // Si hay un error de validación, mostrar los campos
            @if($errors->any())
            $('.card-datosprod').show();
            @endif

            // Verificar si hay mensaje de éxito en la sesión
            @if(session('success'))
            showToast('success', '¡Éxito!', '{{ session('success') }}');
            @endif
        });

        function verUltimoProd(codinst) {
            $('#invalidcodprod').html('C&oacute;digo ');

            if (!codinst) {
                $('#invalidcodprod').html('C&oacute;digo');
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/sainsta/check/lastprod/' + codinst,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {},
                success: function(data) {
                    lastprod = data.last;
                    if (lastprod)
                        $('#invalidcodprod').html('C&oacute;digo [ ' + lastprod + ' &Uacute;ltimo producto creado ]');
                },
                error: function() {
                    console.log('Error al obtener último producto');
                }
            });
        }

        function validarCodigo() {
            var codigo = $('#codprod').val();

            if (!codigo) return;

            $.ajax({
                type: 'POST',
                url: '/productos/validar-codigo',
                data: {
                    codigo: codigo,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.existe) {
                        $('#invalidcodprod').addClass('text-danger');
                        $('#invalidcodprod').html('C&oacute;digo ya existe en el inventario');
                        $('#codprod').addClass('is-invalid');
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        $('#invalidcodprod').removeClass('text-danger');
                        $('#codprod').removeClass('is-invalid');
                        $('#submitBtn').prop('disabled', false);
                    }
                }
            });
        }

        function validarCaracteresCodigo() {
            var codigo = $('#codprod').val();
            var regex = /^[a-zA-Z0-9\-]*$/;

            if (!regex.test(codigo)) {
                $('#invalidcodprod').addClass('text-danger');
                $('#invalidcodprod').html('C&oacute;digo solo puede contener letras, números y guiones');
                $('#codprod').addClass('is-invalid');
                $('#submitBtn').prop('disabled', true);
                return false;
            } else {
                $('#invalidcodprod').removeClass('text-danger');
                $('#codprod').removeClass('is-invalid');
                $('#submitBtn').prop('disabled', false);
                return true;
            }
        }

        function limpiarCodigo() {
            var codigo = $('#codprod').val();
            var codigoLimpio = codigo.replace(/[^a-zA-Z0-9\-]/g, '');

            if (codigo !== codigoLimpio) {
                $('#codprod').val(codigoLimpio);
                $('#invalidcodprod').addClass('text-danger');
                $('#invalidcodprod').html('C&oacute;digo solo puede contener letras, números y guiones (caracteres no permitidos eliminados)');
                $('#codprod').addClass('is-invalid');
                $('#submitBtn').prop('disabled', true);

                setTimeout(function() {
                    if ($('#invalidcodprod').hasClass('text-danger')) {
                        $('#invalidcodprod').removeClass('text-danger');
                        $('#invalidcodprod').html('C&oacute;digo');
                        $('#codprod').removeClass('is-invalid');
                        $('#submitBtn').prop('disabled', false);
                    }
                }, 3000);
            } else {
                validarCaracteresCodigo();
            }
        }

        function convertirMayusculas() {
            var codigo = $('#codprod').val();
            $('#codprod').val(codigo.toUpperCase());
        }

        // Función para mostrar notificaciones toast
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const icon = type === 'success' ? '✅' : '❌';
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type === 'success' ? '' : 'error'}`;
            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            container.appendChild(toast);

            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        // Manejar el envío del formulario
        function handleFormSubmit(event) {
            const form = document.getElementById('createproduct-form');

            // Validar campos requeridos
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return false;
            }

            // Validar código duplicado (si está en estado inválido)
            if ($('#codprod').hasClass('is-invalid')) {
                showToast('error', 'Error de validación', 'El código del producto no es válido o ya existe');
                return false;
            }

            // Mostrar overlay de carga
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('active');

            // Cambiar estado del botón
            const btn = document.getElementById('submitBtn');
            btn.classList.add('btn-loading');
            btn.querySelector('.btn-text').innerHTML = 'Guardando...';
            btn.querySelector('.spinner-border').classList.remove('d-none');
            btn.disabled = true;

            // Mensajes de progreso
            const messages = [
                'Validando datos del producto...',
                'Creando producto en el sistema...',
                'Asignando a comerciales...',
                'Finalizando proceso...'
            ];
            let msgIndex = 0;
            const loadingMsg = document.getElementById('loadingMessage');

            const interval = setInterval(() => {
                if (msgIndex < messages.length) {
                    loadingMsg.textContent = messages[msgIndex];
                    msgIndex++;
                } else {
                    clearInterval(interval);
                }
            }, 800);

            // El formulario se enviará automáticamente después de este punto
            return true;
        }

        // Validación adicional antes del envío
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createproduct-form');

            form.addEventListener('submit', function(e) {
                // La validación ya se maneja en handleFormSubmit
                // Pero añadimos validación extra para el select de instancia
                const instSelect = document.getElementById('datacodinst');
                if (!instSelect.value) {
                    e.preventDefault();
                    $('.error-msg').show();
                    instSelect.classList.add('is-invalid');
                    showToast('error', 'Error de validación', 'Por favor, seleccione una instancia de inventario');
                    return false;
                }
            });

            // Limpiar error al seleccionar instancia
            document.getElementById('datacodinst').addEventListener('change', function() {
                if (this.value) {
                    this.classList.remove('is-invalid');
                    $('.error-msg').hide();
                }
            });
        });
    </script>
    <script src="{{ URL::asset('build/js/backend/create-product.init.js') }}?version={{rand(0,500)}}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
