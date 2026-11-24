/**
 * عقار اليمن – Yemen Real Estate Platform
 * Global Client JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Dark Mode Toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        if (localStorage.getItem('yemen_realestate_theme') === 'dark') {
            document.body.classList.add('dark-mode');
            updateDarkModeIcon(true);
        }

        darkModeToggle.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('yemen_realestate_theme', isDark ? 'dark' : 'light');
            updateDarkModeIcon(isDark);
        });
    }

    function updateDarkModeIcon(isDark) {
        const icon = document.querySelector('#darkModeToggle i');
        if (icon) {
            icon.className = isDark ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
        }
    }

    // 2. Dynamic Governorate -> District -> Area Dependent Dropdowns
    const govSelect = document.getElementById('governorate_id');
    const distSelect = document.getElementById('district_id');
    const areaSelect = document.getElementById('area_id');

    if (govSelect && distSelect) {
        govSelect.addEventListener('change', function () {
            const govId = this.value;
            distSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
            if (areaSelect) areaSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';

            if (!govId) {
                distSelect.innerHTML = '<option value="">اختر المديرية</option>';
                return;
            }

            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
            fetch(`${baseUrl}/api/districts/${govId}`)
                .then(res => res.json())
                .then(data => {
                    distSelect.innerHTML = '<option value="">اختر المديرية</option>';
                    data.forEach(item => {
                        distSelect.innerHTML += `<option value="${item.id}">${item.name_ar}</option>`;
                    });
                })
                .catch(() => {
                    distSelect.innerHTML = '<option value="">تعذر تحميل المديريات</option>';
                });
        });
    }

    if (distSelect && areaSelect) {
        distSelect.addEventListener('change', function () {
            const distId = this.value;
            areaSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';

            if (!distId) {
                areaSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                return;
            }

            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
            fetch(`${baseUrl}/api/areas/${distId}`)
                .then(res => res.json())
                .then(data => {
                    areaSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                    data.forEach(item => {
                        areaSelect.innerHTML += `<option value="${item.id}">${item.name_ar}</option>`;
                    });
                })
                .catch(() => {
                    areaSelect.innerHTML = '<option value="">اختر المنطقة (اختياري)</option>';
                });
        });
    }

    // 3. Favorites AJAX Toggle
    document.querySelectorAll('.favorite-btn-action').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const propId = this.dataset.propertyId;
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch(`${baseUrl}/favorite/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `property_id=${propId}&csrf_token=${csrfToken}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorited) {
                        this.classList.add('active');
                        this.innerHTML = '<i class="bi bi-heart-fill text-danger"></i>';
                    } else {
                        this.classList.remove('active');
                        this.innerHTML = '<i class="bi bi-heart"></i>';
                    }
                    showToast(data.message, 'success');
                } else {
                    showToast(data.error || 'حدث خطأ ما', 'danger');
                    if (data.error && data.error.includes('تسجيل الدخول')) {
                        setTimeout(() => window.location.href = `${baseUrl}/login`, 1500);
                    }
                }
            })
            .catch(() => {
                showToast('تعذر حفظ العقار في المفضلة', 'danger');
            });
        });
    });

    // 4. Quick Share Button
    const shareBtn = document.getElementById('sharePropertyBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                }).catch(() => {});
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('تم نسخ رابط العقار إلى الحافظة بنجاح!', 'success');
                });
            }
        });
    }

    // 5. Inquiry Form Submission via AJAX
    const inquiryForm = document.getElementById('inquiryForm');
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ الإرسال...';

            const formData = new FormData(this);
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

            fetch(`${baseUrl}/inquiry/send`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (data.success) {
                    showToast(data.message, 'success');
                    inquiryForm.reset();
                    const modalEl = document.getElementById('inquiryModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                } else {
                    showToast(data.error || 'تعذر إرسال الاستفسار', 'danger');
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                showToast('حدث خطأ أثناء الإرسال', 'danger');
            });
        });
    }

    // 6. Report Property Form
    const reportForm = document.getElementById('reportForm');
    if (reportForm) {
        reportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';

            fetch(`${baseUrl}/report/submit`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                    reportForm.reset();
                    const modalEl = document.getElementById('reportModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                } else {
                    showToast(data.error || 'تعذر إرسال البلاغ', 'danger');
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                showToast('حدث خطأ أثناء إرسال البلاغ', 'danger');
            });
        });
    }
});

// Toast notification helper
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '10999';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0 shadow-lg`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body fw-bold">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    container.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}
