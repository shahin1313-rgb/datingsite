<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تست مودال</title>

    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js',
    ])
</head>

<body class="p-5">

    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#exampleModal"
    >
        باز کردن مودال
    </button>

    <div
        class="modal fade"
        id="exampleModal"
        tabindex="-1"
        aria-labelledby="modalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5
                        class="modal-title"
                        id="modalLabel"
                    >
                        عنوان تستی
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="بستن"
                    ></button>
                </div>

                <div class="modal-body">
                    این یک پیام تستی است.
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        بستن
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>