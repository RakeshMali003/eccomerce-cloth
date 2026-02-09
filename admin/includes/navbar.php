<nav class="glass-header sticky top-0 z-50 px-6 lg:px-10 py-4 flex justify-between items-center">
    <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="lg:hidden text-slate-500 hover:text-slate-900 transition-colors">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="hidden md:block">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <?php echo date('l, d F Y'); ?>
            </p>
            <p class="text-xs font-bold text-slate-700">
                Welcome back,
                <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-6">
        <div class="flex items-center gap-3">
            <div class="text-right hidden md:block">
                <p class="text-xs font-bold text-slate-900">
                    <?php echo $_SESSION['admin_name'] ?? 'Administrator'; ?>
                </p>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">
                    <?php echo ucfirst($_SESSION['role'] ?? 'Admin'); ?>
                </p>
            </div>
            <div
                class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200">
                <?php echo substr($_SESSION['admin_name'] ?? 'A', 0, 1); ?>
            </div>

            <div class="relative group">
                <button class="text-slate-400 hover:text-slate-900 transition-colors">
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div
                    class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                    <div class="p-2">
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <a href="../profile/change-password.php"
                                class="block px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
                                <i class="fas fa-key mr-2"></i> Change Password
                            </a>
                        <?php endif; ?>
                        <a href="../logout.php"
                            class="block px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                            <i class="fas fa-power-off mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('fixed');
        sidebar.classList.toggle('inset-0');
        sidebar.classList.toggle('z-[100]');
        sidebar.classList.toggle('w-full');
    });
</script>