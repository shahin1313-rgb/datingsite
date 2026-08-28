import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

function hideGlobalLoader() {
    const loader = document.getElementById('globalLoading');

    if (!loader) {
        return;
    }

    loader.classList.add('loader-hidden');

    window.setTimeout(() => {
        loader.style.display = 'none';
    }, 500);
}

window.addEventListener('load', hideGlobalLoader);
window.setTimeout(hideGlobalLoader, 3000);

document.addEventListener('click', (event) => {
    const historyButton = event.target.closest(
        '[data-history-back]'
    );

    if (historyButton) {
        window.history.back();
        return;
    }

    const sidebarButton = event.target.closest(
        '[data-toggle-sidebar]'
    );

    if (sidebarButton) {
        const sidebar =
            document.getElementById('mySidebar');

        const overlay =
            document.getElementById('myOverlay');

        sidebar?.classList.toggle('sidebar-open');
        overlay?.classList.toggle('hidden');
    }
});

const sweetAlertClasses = {
    popup: 'vlora-swal-popup',
    title: 'vlora-swal-title',
    htmlContainer: 'vlora-swal-text',
    actions: 'vlora-swal-actions',
    confirmButton: 'vlora-swal-confirm',
    cancelButton: 'vlora-swal-cancel',
};

async function submitBlockWithSweetAlert(form, submitButton) {
    const targetName =
        form.dataset.blockName || 'این کاربر';

    const confirmation = await Swal.fire({
        icon: 'warning',
        title: 'مسدود کردن کاربر؟',
        text:
            form.dataset.confirm ||
            `آیا از مسدود کردن ${targetName} مطمئن هستید؟`,
        showCancelButton: true,
        confirmButtonText: 'بله، مسدود شود',
        cancelButtonText: 'انصراف',
        focusCancel: true,
        reverseButtons: true,
        buttonsStyling: false,
        customClass: sweetAlertClasses,
    });

    if (!confirmation.isConfirmed) {
        return;
    }

    if (submitButton) {
        submitButton.disabled = true;
    }

    Swal.fire({
        title: 'در حال مسدودسازی...',
        text: 'لطفاً چند لحظه صبر کنید.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        buttonsStyling: false,
        customClass: sweetAlertClasses,
        didOpen: () => Swal.showLoading(),
    });

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        let data = {};

        try {
            data = await response.json();
        } catch (error) {
            data = {};
        }

        if (!response.ok) {
            throw new Error(
                data.message ||
                'مسدودسازی انجام نشد. دوباره تلاش کنید.'
            );
        }

        if (!data.redirect_url) {
            throw new Error(
                'مسدودسازی ثبت شد اما مسیر ادامه دریافت نشد.'
            );
        }

        await Swal.fire({
            icon: 'success',
            title: 'مسدودسازی انجام شد',
            text:
                data.message ||
                `${targetName} با موفقیت مسدود شد.`,
            confirmButtonText: 'ادامه',
            allowOutsideClick: false,
            allowEscapeKey: false,
            buttonsStyling: false,
            customClass: sweetAlertClasses,
        });

        /*
         * replace() صفحه فعلی پروفایل/چت را در History جایگزین می‌کند؛
         * بنابراین Back کاربر را به همان صفحه مسدودشده برنمی‌گرداند.
         */
        window.location.replace(data.redirect_url);
    } catch (error) {
        if (submitButton) {
            submitButton.disabled = false;
        }

        await Swal.fire({
            icon: 'error',
            title: 'مسدودسازی انجام نشد',
            text:
                error.message ||
                'ارتباط با سرور برقرار نشد. دوباره تلاش کنید.',
            confirmButtonText: 'متوجه شدم',
            buttonsStyling: false,
            customClass: sweetAlertClasses,
        });
    }
}

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.matches('[data-sweet-block]')) {
        event.preventDefault();

        await submitBlockWithSweetAlert(
            form,
            event.submitter
        );

        return;
    }

    const message = form.dataset.confirm;

    if (!message || form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    const confirmation = await Swal.fire({
        icon: 'question',
        title: form.dataset.confirmTitle || 'آیا مطمئن هستید؟',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'تأیید',
        cancelButtonText: 'انصراف',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: sweetAlertClasses,
    });

    if (confirmation.isConfirmed) {
        form.dataset.confirmed = 'true';
        form.requestSubmit(event.submitter || undefined);
    }
});
