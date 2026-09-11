@php($logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout'))
@php($logout_url = config('adminlte.use_route_url', false) ? ($logout_url ? route($logout_url) : '') : ($logout_url ? url($logout_url) : ''))
<li class="nav-item">
    <form action="{{ $logout_url }}" method="POST">
        @if(config('adminlte.logout_method')) {{ method_field(config('adminlte.logout_method')) }} @endif
        {{ csrf_field() }}
        <button type="submit" class="nav-link btn btn-link">
            <i class="fa fa-fw fa-power-off text-red"></i> {{ __('adminlte::adminlte.log_out') }}
        </button>
    </form>
</li>
