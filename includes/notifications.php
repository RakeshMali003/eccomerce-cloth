<div id="notification-hub" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<script>
function notify(message, type = 'success') {
    const hub = document.getElementById('notification-hub');
    const icons = {
        success: 'fa-check-circle text-emerald-500',
        error: 'fa-exclamation-triangle text-red-500',
        warning: 'fa-info-circle text-orange-500'
    };

    const id = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${id}" class="flex items-center gap-4 px-6 py-4 rounded-[1.5rem] shadow-2xl border backdrop-blur-md bg-white/90 transform transition-all duration-500 translate-x-full pointer-events-auto border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                <i class="fas ${icons[type]} text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">${type}</p>
                <p class="text-sm font-bold text-slate-800 leading-tight">${message}</p>
            </div>
        </div>`;

    hub.insertAdjacentHTML('beforeend', toastHTML);
    const el = document.getElementById(id);
    setTimeout(() => el.classList.remove('translate-x-full'), 10);
    setTimeout(() => {
        el.classList.add('translate-x-[120%]');
        setTimeout(() => el.remove(), 500);
    }, 4000);
}

// 🚀 AUTO-TRIGGER FROM SESSION
<?php if (isset($_SESSION['toast'])): ?>
    notify("<?php echo $_SESSION['toast']['msg']; ?>", "<?php echo $_SESSION['toast']['type']; ?>");
    <?php unset($_SESSION['toast']); // Clear after showing ?>
<?php endif; ?>
</script>