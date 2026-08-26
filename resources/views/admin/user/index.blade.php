@extends('adminlte::page')

@section('title', 'مدیریت کاربران')

@section('content_header')
    <h1>مدیریت کاربران</h1>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card card-outline card-warning">
        <div class="card-body">
            <label
                for="admin-current-password"
                class="form-label"
            >
                رمز عبور فعلی مدیر برای عملیات حساس
            </label>

            <input
                id="admin-current-password"
                type="password"
                class="form-control"
                autocomplete="current-password"
                placeholder="قبل از مسدودسازی، حذف یا ارتقای نقش وارد کنید"
            >

            <small class="text-muted">
                این رمز فقط همراه همان درخواست ارسال می‌شود
                و در گزارش عملیات ذخیره نمی‌شود.
            </small>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('admin.users') }}"
                class="row mb-4"
            >
                <div class="col-md-3 mb-2">
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="نام"
                        value="{{ request('name') }}"
                    >
                </div>

                <div class="col-md-3 mb-2">
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="ایمیل"
                        value="{{ request('email') }}"
                    >
                </div>

                <div class="col-md-3 mb-2">
                    <select
                        name="banned"
                        class="form-control"
                    >
                        <option value="">
                            همه وضعیت‌ها
                        </option>

                        <option
                            value="1"
                            @selected(request('banned') === '1')
                        >
                            مسدود
                        </option>

                        <option
                            value="0"
                            @selected(request('banned') === '0')
                        >
                            فعال
                        </option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        جستجو
                    </button>

                    <a
                        href="{{ route('admin.users') }}"
                        class="btn btn-secondary"
                    >
                        پاک‌کردن
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table
                    class="table table-bordered align-middle"
                >
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام</th>
                            <th>ایمیل</th>
                            <th>نقش</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $isSelf =
                                    auth()->id() === $user->id;

                                $isLastActiveAdmin =
                                    $user->role === 'admin'
                                    && ! $user->banned
                                    && $activeAdminCount <= 1;

                                $cannotRestrict =
                                    $isSelf
                                    || $isLastActiveAdmin;
                            @endphp

                            <tr
                                @class([
                                    'table-danger' => $user->banned,
                                ])
                            >
                                <td>
                                    {{ $user->id }}
                                </td>

                                <td>
                                    <a
                                        href="{{ route('admin.users.show', $user) }}"
                                    >
                                        {{ $user->name }}
                                    </a>

                                    @if ($isSelf)
                                        <span
                                            class="badge badge-info"
                                        >
                                            حساب شما
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>
                                    {{ $user->role === 'admin'
                                        ? 'مدیر'
                                        : 'کاربر' }}
                                </td>

                                <td>
                                    {{ $user->banned
                                        ? 'مسدود'
                                        : 'فعال' }}
                                </td>

                                <td class="text-nowrap">
                                    <form
                                        action="{{ route('admin.users.ban', $user) }}"
                                        method="POST"
                                        class="d-inline sensitive-action-form"
                                        data-confirm="آیا از تغییر وضعیت این حساب مطمئن هستید؟"
                                    >
                                        @csrf

                                        <input
                                            type="hidden"
                                            name="current_password"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm {{ $user->banned ? 'btn-success' : 'btn-warning' }}"
                                            @disabled($cannotRestrict)
                                        >
                                            {{ $user->banned
                                                ? 'رفع مسدودی'
                                                : 'مسدودکردن' }}
                                        </button>
                                    </form>

                                    @if ($user->role !== 'admin')
                                        <form
                                            action="{{ route('admin.makeAdmin', $user) }}"
                                            method="POST"
                                            class="d-inline sensitive-action-form"
                                            data-confirm="این کاربر به مدیر ارتقا یابد؟"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="current_password"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-primary"
                                                @disabled($user->banned)
                                            >
                                                ارتقا به مدیر
                                            </button>
                                        </form>
                                    @endif

                                    <form
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        method="POST"
                                        class="d-inline sensitive-action-form"
                                        data-confirm="حذف حساب و داده‌های وابسته قابل بازگشت نیست. ادامه می‌دهید؟"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <input
                                            type="hidden"
                                            name="current_password"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            @disabled($cannotRestrict)
                                        >
                                            حذف
                                        </button>
                                    </form>

                                    @if ($cannotRestrict)
                                        <small
                                            class="d-block text-muted mt-1"
                                        >
                                            حساب خودتان یا آخرین مدیر
                                            فعال قابل مسدود یا حذف نیست.
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="text-center text-muted"
                                >
                                    کاربری یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

@section('js')
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        document
            .querySelectorAll('.sensitive-action-form')
            .forEach((form) => {
                form.addEventListener(
                    'submit',
                    (event) => {
                        const password = document
                            .getElementById(
                                'admin-current-password'
                            )
                            .value;

                        if (!password) {
                            event.preventDefault();

                            window.alert(
                                'ابتدا رمز عبور فعلی مدیر را وارد کنید.'
                            );

                            return;
                        }

                        if (
                            !window.confirm(
                                form.dataset.confirm
                            )
                        ) {
                            event.preventDefault();
                            return;
                        }

                        form
                            .querySelector(
                                'input[name="current_password"]'
                            )
                            .value = password;
                    }
                );
            });
    </script>
@endsection