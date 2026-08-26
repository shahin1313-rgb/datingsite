@extends('adminlte::page')

@section('title', 'جزئیات کاربر')

@section('content')
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

    <div class="container mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div
                    class="d-flex align-items-center mb-4"
                >
                    <img
                        src="{{ $user->profilePhotoUrl() }}"
                        alt="تصویر {{ $user->name }}"
                        class="rounded-circle border border-primary"
                        width="128"
                        height="128"
                    >

                    <div class="mr-4">
                        <h2 class="h4 font-weight-bold">
                            {{ $user->name }}
                        </h2>

                        <p class="mb-1 text-muted">
                            {{ $user->email }}
                        </p>

                        <span
                            class="badge {{ $user->role === 'admin'
                                ? 'badge-primary'
                                : 'badge-secondary' }}"
                        >
                            {{ $user->role === 'admin'
                                ? 'مدیر'
                                : 'کاربر' }}
                        </span>

                        @if ($isSelf)
                            <span class="badge badge-info">
                                حساب شما
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-primary">
                            <strong>سن:</strong>
                            {{ $user->age ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <strong>جنسیت:</strong>
                            {{ $user->gender ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-secondary">
                            <strong>شهر:</strong>
                            {{ $user->city ?? '—' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div
                            class="alert {{ $user->banned ? 'alert-danger' : 'alert-success' }}"
                        >
                            <strong>وضعیت:</strong>

                            {{ $user->banned
                                ? 'مسدود'
                                : 'فعال' }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="alert alert-light">
                            <strong>عضویت:</strong>

                            {{ $user->created_at->format('Y/m/d') }}
                        </div>
                    </div>
                </div>

                <div
                    class="p-3 border rounded bg-light mb-4"
                >
                    <h5>بیوگرافی</h5>

                    <p class="mb-0">
                        {{ $user->bio ?? 'ندارد' }}
                    </p>
                </div>

                <div class="card card-outline card-warning">
                    <div class="card-body">
                        <label for="admin-current-password">
                            رمز عبور فعلی مدیر
                        </label>

                        <input
                            id="admin-current-password"
                            type="password"
                            class="form-control"
                            autocomplete="current-password"
                            placeholder="برای عملیات حساس وارد کنید"
                        >
                    </div>
                </div>

                <div
                    class="d-flex justify-content-end flex-wrap"
                    style="gap: .5rem;"
                >
                    <form
                        method="POST"
                        action="{{ route('admin.users.ban', $user) }}"
                        class="sensitive-action-form"
                        data-confirm="آیا از تغییر وضعیت این حساب مطمئن هستید؟"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="current_password"
                        >

                        <button
                            type="submit"
                            class="btn {{ $user->banned ? 'btn-success' : 'btn-warning' }}"
                            @disabled($cannotRestrict)
                        >
                            {{ $user->banned
                                ? 'رفع مسدودی'
                                : 'مسدودکردن حساب' }}
                        </button>
                    </form>

                    @if ($user->role !== 'admin')
                        <form
                            method="POST"
                            action="{{ route('admin.makeAdmin', $user) }}"
                            class="sensitive-action-form"
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
                                class="btn btn-primary"
                                @disabled($user->banned)
                            >
                                ارتقا به مدیر
                            </button>
                        </form>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('admin.users.destroy', $user) }}"
                        class="sensitive-action-form"
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
                            class="btn btn-danger"
                            @disabled($cannotRestrict)
                        >
                            حذف کاربر
                        </button>
                    </form>
                </div>

                @if ($cannotRestrict)
                    <p class="text-muted text-right mt-2">
                        حساب خودتان یا آخرین مدیر فعال
                        قابل مسدود یا حذف نیست.
                    </p>
                @endif
            </div>
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
