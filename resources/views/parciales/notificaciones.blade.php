<div class="toast-container">
@if(Session::has('bien'))
    <div class="toast-alert toast-bien show">
        <div class="toast show" role="alert" aria-live="assertive">
            <div class="toast-success-header" style="padding-bottom: 5px;">
                <strong><i class="bi bi-check-circle-fill"></i></strong>
                <small></small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="cerrarToast(1)">
                    <span aria-hidden="true">&times</span>
                </button>
            </div>
            <div class="toast-success-body">
                {!! Session::get('bien') !!}
            </div>
        </div>
    </div>
@endif
@if(Session::has('alerta'))
    <div class="toast-alert toast-alerta show" id="alerta2">
        <div class="toast show" role="alert" aria-live="assertive">
            <div class="toast-warning-header">
                <strong><i class="bi bi-exclamation-triangle-fill"></i></strong>
                <small></small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="cerrarToast(2)">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-warning-body">
                {!! Session::get('alerta') !!}
            </div>
        </div>
    </div>
@endif
@if(Session::has('error'))
    <div class="toast-alert toast-error show">
        <div class="toast show" role="alert" aria-live="assertive" id="alerta3">
            <div class="toast-danger-header">
                <strong><i class="bi bi-patch-exclamation-fill"></i></strong>
                <small></small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="cerrarToast(3)">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-danger-body">
                {!! Session::get('error') !!}
            </div>
        </div>
    </div>
@endif
@if(Session::has('mensaje'))
    <div class="toast-alert toast-mensaje show">
        <div class="toast show" role="alert" aria-live="assertive">
            <div class="toast-secondary-header">
                <strong><i class="bi bi-info-circle-fill"></i></strong>
                <small></small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" onclick="cerrarToast(4)">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-secondary-body">
                {!! Session::get('mensaje') !!}
            </div>
        </div>
    </div>
@endif
</div>
