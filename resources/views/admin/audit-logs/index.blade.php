@extends('adminlte::page')

@section('title', 'گزارش عملیات مدیران')

@section('content_header')
    <h1>گزارش عملیات مدیران</h1>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <form
                method="GET"
                action="{{ route('admin.audit-logs.index') }}"
                class="row mb-4"
            >
                <div class="col-md-4 mb-2">
                    <input
                        type="search"
                        name="email"
                        value="{{ request('email') }}"
                        class="form-control"
                        placeholder="ایمیل مدیر یا کاربر هدف"
                    >
                </div>

                <div class="col-md-4 mb-2">
                    <select
                        name="action"
                        class="form-control"
                    >
                        <option value="">
                            همه عملیات‌ها
                        </option>

                        @foreach ($actions as $action)
                            <option
                                value="{{ $action }}"
                                @selected(
                                    request('action') === $action
                                )
                            >
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-2">
                    <button
                        class="btn btn-primary"
                    >
                        جستجو
                    </button>

                    <a
                        href="{{ route('admin.audit-logs.index') }}"
                        class="btn btn-secondary"
                    >
                        پاک‌کردن
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table
                    class="table table-bordered table-striped"
                >
                    <thead>
                    <tr>
                        <th>زمان</th>
                        <th>مدیر</th>
                        <th>عملیات</th>
                        <th>کاربر هدف</th>
                        <th>IP</th>
                        <th>قبل / بعد</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td dir="ltr">
                                {{ $log->created_at
                                    ->format('Y-m-d H:i:s') }}
                            </td>

                            <td>
                                {{ $log->actor_email }}
                            </td>

                            <td>
                                <code>
                                    {{ $log->action }}
                                </code>
                            </td>

                            <td>
                                {{ $log->target_email ?? '—' }}
                            </td>

                            <td dir="ltr">
                                {{ $log->ip_address ?? '—' }}
                            </td>

                            <td dir="ltr">
                                <details>
                                    <summary>
                                        نمایش جزئیات
                                    </summary>

                                    <div class="mt-2">
                                        <strong>
                                            قبل:
                                        </strong>

                                        <pre class="small">{{ json_encode(
                                            $log->before,
                                            JSON_PRETTY_PRINT |
                                            JSON_UNESCAPED_UNICODE
                                        ) }}</pre>

                                        <strong>
                                            بعد:
                                        </strong>

                                        <pre class="small">{{ json_encode(
                                            $log->after,
                                            JSON_PRETTY_PRINT |
                                            JSON_UNESCAPED_UNICODE
                                        ) }}</pre>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
                                class="text-center text-muted"
                            >
                                رکوردی وجود ندارد.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links(
                'pagination::bootstrap-5'
            ) }}

        </div>
    </div>
@endsection