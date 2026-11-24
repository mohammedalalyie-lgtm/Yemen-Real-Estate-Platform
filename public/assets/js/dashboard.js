/**
 * عقار اليمن – Yemen Real Estate Platform
 * Dashboard & Multi-Step Property Form
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Multi-Step Add Property Form
    const wizardForm = document.getElementById('addPropertyForm');
    if (wizardForm) {
        let currentStep = 1;
        const totalSteps = 6;

        const nextBtn = document.getElementById('wizardNextBtn');
        const prevBtn = document.getElementById('wizardPrevBtn');
        const submitBtn = document.getElementById('wizardSubmitBtn');

        function updateStepUI() {
            // Hide all steps
            document.querySelectorAll('.wizard-step-pane').forEach(el => el.classList.add('d-none'));
            // Show current step
            const currentPane = document.getElementById(`stepPane${currentStep}`);
            if (currentPane) currentPane.classList.remove('d-none');

            // Update step indicators
            document.querySelectorAll('.wizard-step').forEach((el, idx) => {
                const stepNum = idx + 1;
                el.classList.remove('active', 'completed');
                if (stepNum === currentStep) {
                    el.classList.add('active');
                } else if (stepNum < currentStep) {
                    el.classList.add('completed');
                }
            });

            // Toggle buttons
            if (prevBtn) {
                prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';
            }
            if (nextBtn) {
                nextBtn.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
            }
            if (submitBtn) {
                submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
            }

            // Scroll to top of form
            wizardForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (validateStep(currentStep)) {
                    currentStep++;
                    updateStepUI();
                }
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepUI();
                }
            });
        }

        function validateStep(step) {
            let valid = true;
            const currentPane = document.getElementById(`stepPane${step}`);
            if (!currentPane) return true;

            const requiredInputs = currentPane.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                showToast('يرجى تعبئة جميع الحقول المطلوبة للمتابعة', 'warning');
            }

            return valid;
        }

        // Initialize UI
        updateStepUI();
    }

    // 2. Chart.js In Dashboard Overview (if Chart.js is loaded)
    if (typeof Chart !== 'undefined') {
        const viewsCanvas = document.getElementById('viewsChart');
        if (viewsCanvas) {
            new Chart(viewsCanvas, {
                type: 'line',
                data: {
                    labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
                    datasets: [{
                        label: 'مشاهدات الإعلانات',
                        data: [42, 65, 89, 120, 145, 178, 210],
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#2563EB'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#E2E8F0' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
});
