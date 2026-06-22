/**
 * CDCMS — Cricket Drafting Ceremony Management System
 * Global JavaScript Utilities
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Auto-dismiss flash alerts ───────────────────────── */
    document.querySelectorAll('.alert-dismissible[data-auto-dismiss]').forEach(el => {
        const delay = parseInt(el.dataset.autoDismiss) || 4000;
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 420);
        }, delay);
    });

    /* ── Confirm-before-submit on data-confirm forms ─────── */
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm(form.dataset.confirm)) e.preventDefault();
        });
    });

    /* ── Live character counter for textareas ────────────── */
    document.querySelectorAll('textarea[data-maxlength]').forEach(ta => {
        const max     = parseInt(ta.dataset.maxlength);
        const counter = document.createElement('small');
        counter.className = 'text-muted d-block text-end mt-1';
        ta.after(counter);
        const update = () => {
            const left = max - ta.value.length;
            counter.textContent = `${ta.value.length} / ${max}`;
            counter.classList.toggle('text-danger', left < 20);
        };
        ta.addEventListener('input', update);
        update();
    });

    /* ── Bulk checkbox: select-all ───────────────────────── */
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
        });
    }

    /* ── Image preview on file input ────────────────────── */
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        const previewId = input.dataset.preview;
        const preview   = document.getElementById(previewId);
        if (!preview) return;
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; };
                reader.readAsDataURL(file);
            }
        });
    });

    /* ── Draft room: highlight my-turn card ─────────────── */
    const teamId = document.body.dataset.myTeamId;
    if (teamId) {
        document.querySelectorAll(`[data-team-id="${teamId}"]`).forEach(el => {
            el.classList.add('my-turn-glow');
        });
    }

    /* ── Tooltip init (Bootstrap) ────────────────────────── */
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el));

    /* ── Active sidebar link highlight ───────────────────── */
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-admin a').forEach(link => {
        if (link.getAttribute('href') && currentPath.startsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });

});

/* ── Global AJAX helper ──────────────────────────────────── */
window.cdcmsPost = async function(url, data = {}) {
    const res = await fetch(url, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.CDCMS?.csrfToken
                ?? document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    });
    return res.json();
};

/* ── Draft countdown factory (reusable across pages) ─────── */
window.createCountdown = function(elementId, initialSeconds, onExpire) {
    let seconds = initialSeconds;
    const el    = document.getElementById(elementId);
    if (!el) return;

    const render = () => {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        el.textContent = `${m}:${s}`;
        el.classList.toggle('warning', seconds <= 30 && seconds > 0);
        el.classList.toggle('text-danger', seconds <= 10);
    };

    render();
    const interval = setInterval(() => {
        if (seconds <= 0) {
            clearInterval(interval);
            if (typeof onExpire === 'function') onExpire();
            return;
        }
        seconds--;
        render();
    }, 1000);

    return {
        reset: (newSeconds) => { seconds = newSeconds; render(); },
        stop:  () => clearInterval(interval),
    };
};
