// Confirm before destructive actions (delete/remove/block)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.confirm-action').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide flash messages after 4 seconds
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 4000);
    });

    // Basic quiz submit confirmation
    const quizForm = document.getElementById('quizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', function (e) {
            if (!confirm('Submit your answers? You cannot change them after submitting.')) {
                e.preventDefault();
            }
        });
    }
});
