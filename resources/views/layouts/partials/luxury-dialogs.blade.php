<!-- ========================================================================= -->
<!-- LUXURY DIALOGS & TOAST SYSTEM (Day & Night Mode Supported)                 -->
<!-- ========================================================================= -->

<!-- 1. LUXURY CONFIRMATION MODAL -->
<div id="luxuryConfirmModal" 
     class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none" 
     style="z-index: 9999999 !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; isolation: isolate !important;"
     dir="rtl"
     tabindex="-1">
    
    <div id="luxuryConfirmCard" 
         class="bg-white dark:bg-[#141414] border-2 border-[#D4AF37] dark:border-[#D4AF37] rounded-[32px] shadow-[0_30px_100px_rgba(0,0,0,0.5)] max-w-md w-full p-6 text-right relative overflow-hidden transform scale-90 transition-all duration-300"
         style="z-index: 10000000 !important; position: relative !important;">
        
        <!-- Top Decorative Gold Line -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-transparent via-[#D4AF37] to-transparent"></div>
        
        <!-- Header & Icon Section -->
        <div class="flex items-start gap-4 mb-6">
            <div id="luxuryConfirmIconBg" class="w-14 h-14 rounded-2xl bg-[#D4AF37]/10 border border-[#D4AF37]/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#D4AF37]/10">
                <i id="luxuryConfirmIcon" data-lucide="help-circle" class="w-7 h-7 text-[#D4AF37]"></i>
            </div>
            
            <div class="space-y-1">
                <h3 id="luxuryConfirmTitle" class="text-xl font-black text-gray-900 dark:text-white tracking-tight" style="font-family: 'Almarai', sans-serif;">
                    تأكيد الإجراء
                </h3>
                <p id="luxuryConfirmMessage" class="text-xs font-bold text-gray-500 dark:text-gray-400 leading-relaxed">
                    هل أنت متأكد من تنفيذ هذا الإجراء؟
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-white/10">
            <button type="button" 
                    id="luxuryConfirmCancelBtn" 
                    class="px-6 py-3 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black transition-all duration-300">
                إلغاء
            </button>
            <button type="button" 
                    id="luxuryConfirmOkBtn" 
                    class="px-7 py-3 bg-gradient-to-r from-[#D4AF37] to-[#B88F3D] hover:from-[#B88F3D] hover:to-[#96722B] text-black font-black text-xs rounded-xl shadow-lg shadow-[#D4AF37]/20 hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2">
                <i id="luxuryConfirmOkIcon" data-lucide="check-circle-2" class="w-4 h-4"></i>
                <span id="luxuryConfirmOkText">تأكيد</span>
            </button>
        </div>
    </div>
</div>

<!-- 2. LUXURY TOAST NOTIFICATION CONTAINER -->
<div id="luxuryToastContainer" 
     class="fixed top-6 left-1/2 -translate-x-1/2 md:left-8 md:translate-x-0 flex flex-col gap-3 max-w-md w-full px-4 pointer-events-none" 
     style="z-index: 9999999 !important; position: fixed !important; isolation: isolate !important;"
     dir="rtl">
</div>

<!-- 3. SYSTEM SCRIPTS & INTERCEPTOR -->
<script>
(function() {
    // ── Global Custom Confirm Promise ──────────────────────────────────────────
    let confirmResolver = null;

    window.showLuxuryConfirm = function(options = {}) {
        return new Promise((resolve) => {
            confirmResolver = resolve;
            
            const modal = document.getElementById('luxuryConfirmModal');
            const card = document.getElementById('luxuryConfirmCard');
            const titleEl = document.getElementById('luxuryConfirmTitle');
            const msgEl = document.getElementById('luxuryConfirmMessage');
            const iconBg = document.getElementById('luxuryConfirmIconBg');
            const iconEl = document.getElementById('luxuryConfirmIcon');
            const okBtn = document.getElementById('luxuryConfirmOkBtn');
            const okText = document.getElementById('luxuryConfirmOkText');
            const okIcon = document.getElementById('luxuryConfirmOkIcon');
            
            const title = options.title || 'تأكيد الإجراء';
            const message = options.message || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
            const type = options.type || (message.includes('حذف') || message.includes('مسح') ? 'danger' : 'warning');
            const confirmText = options.confirmText || (type === 'danger' ? 'نعم، قم بالحذف' : 'نعم، تأكيد');
            const cancelText = options.cancelText || 'إلغاء';

            titleEl.textContent = title;
            msgEl.textContent = message;
            okText.textContent = confirmText;
            document.getElementById('luxuryConfirmCancelBtn').textContent = cancelText;

            // Theme & Type styling
            if (type === 'danger') {
                iconBg.className = 'w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-rose-500/10';
                iconEl.setAttribute('data-lucide', 'alert-triangle');
                iconEl.className = 'w-7 h-7 text-rose-500';
                okBtn.className = 'px-7 py-3 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-black text-xs rounded-xl shadow-lg shadow-rose-600/20 hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2';
                okIcon.setAttribute('data-lucide', 'trash-2');
            } else if (type === 'success') {
                iconBg.className = 'w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-500/10';
                iconEl.setAttribute('data-lucide', 'check-circle-2');
                iconEl.className = 'w-7 h-7 text-emerald-500';
                okBtn.className = 'px-7 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-black text-xs rounded-xl shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2';
                okIcon.setAttribute('data-lucide', 'check-circle-2');
            } else {
                iconBg.className = 'w-14 h-14 rounded-2xl bg-[#D4AF37]/15 border border-[#D4AF37]/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#D4AF37]/10';
                iconEl.setAttribute('data-lucide', 'help-circle');
                iconEl.className = 'w-7 h-7 text-[#D4AF37]';
                okBtn.className = 'px-7 py-3 bg-gradient-to-r from-[#D4AF37] to-[#B88F3D] hover:from-[#B88F3D] hover:to-[#96722B] text-black font-black text-xs rounded-xl shadow-lg shadow-[#D4AF37]/20 hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2';
                okIcon.setAttribute('data-lucide', 'check-circle-2');
            }

            if (window.lucide) window.lucide.createIcons();

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-90');
                card.classList.add('scale-100');
            }, 10);
        });
    };

    function closeConfirmModal(result) {
        const modal = document.getElementById('luxuryConfirmModal');
        const card = document.getElementById('luxuryConfirmCard');
        if (!modal) return;
        
        card.classList.remove('scale-100');
        card.classList.add('scale-90');
        modal.classList.add('opacity-0', 'pointer-events-none');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            if (confirmResolver) {
                confirmResolver(result);
                confirmResolver = null;
            }
        }, 200);
    }

    document.getElementById('luxuryConfirmOkBtn')?.addEventListener('click', () => closeConfirmModal(true));
    document.getElementById('luxuryConfirmCancelBtn')?.addEventListener('click', () => closeConfirmModal(false));
    
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('luxuryConfirmModal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeConfirmModal(false);
            if (e.key === 'Enter') closeConfirmModal(true);
        }
    });

    // ── Form Confirm Helper ──────────────────────────────────────────────────
    window.confirmAction = function(target, message, title) {
        const msg = message || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
        const headTitle = title || (msg.includes('حذف') || msg.includes('مسح') ? 'تأكيد الحذف' : 'تأكيد الإجراء');
        
        showLuxuryConfirm({
            title: headTitle,
            message: msg,
            type: (msg.includes('حذف') || msg.includes('مسح')) ? 'danger' : 'warning'
        }).then((confirmed) => {
            if (confirmed) {
                if (typeof target === 'string') {
                    const form = document.querySelector(target);
                    if (form) form.submit();
                } else if (target && target.tagName === 'FORM') {
                    target.submit();
                } else if (target && target.closest) {
                    const form = target.closest('form');
                    if (form) form.submit();
                }
            }
        });
        return false;
    };

    // ── Global Event Interceptor for native confirm ────────────────────────────
    document.addEventListener('submit', function(e) {
        const form = e.target;
        const onsubmitAttr = form.getAttribute('onsubmit');
        
        if (onsubmitAttr && (onsubmitAttr.includes('confirm(') || onsubmitAttr.includes('confirmAction('))) {
            // Check if already confirmed by us
            if (form.dataset.luxuryConfirmed === 'true') {
                delete form.dataset.luxuryConfirmed;
                return true;
            }
            
            e.preventDefault();
            e.stopImmediatePropagation();

            // Extract prompt message if possible
            let match = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
            let promptMsg = match ? match[1] : 'هل أنت متأكد من تنفيذ هذا الإجراء؟';

            showLuxuryConfirm({
                title: promptMsg.includes('حذف') || promptMsg.includes('مسح') ? 'تأكيد الحذف' : 'تأكيد الإجراء',
                message: promptMsg,
                type: promptMsg.includes('حذف') || promptMsg.includes('مسح') ? 'danger' : 'warning'
            }).then((confirmed) => {
                if (confirmed) {
                    form.dataset.luxuryConfirmed = 'true';
                    form.submit();
                }
            });
            return false;
        }
    }, true);

    // ── Luxury Toast Notification Function ─────────────────────────────────────
    window.showLuxuryToast = function(message, type = 'success', duration = 4500) {
        const container = document.getElementById('luxuryToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white dark:bg-[#1A1A1A] border-2 rounded-2xl p-4 shadow-[0_15px_40px_rgba(0,0,0,0.2)] flex items-center justify-between gap-4 transition-all duration-500 transform -translate-y-4 opacity-0 relative overflow-hidden group';
        
        let borderClass = 'border-[#D4AF37] dark:border-[#D4AF37]';
        let iconBgClass = 'bg-[#D4AF37]/15 text-[#D4AF37]';
        let iconName = 'check-circle-2';
        let barClass = 'bg-[#D4AF37]';

        if (type === 'error' || type === 'danger') {
            borderClass = 'border-rose-500/80 dark:border-rose-500/80';
            iconBgClass = 'bg-rose-500/15 text-rose-500';
            iconName = 'shield-alert';
            barClass = 'bg-rose-500';
        } else if (type === 'warning') {
            borderClass = 'border-amber-500/80 dark:border-amber-500/80';
            iconBgClass = 'bg-amber-500/15 text-amber-500';
            iconName = 'alert-triangle';
            barClass = 'bg-amber-500';
        } else if (type === 'info') {
            borderClass = 'border-sky-500/80 dark:border-sky-500/80';
            iconBgClass = 'bg-sky-500/15 text-sky-500';
            iconName = 'info';
            barClass = 'bg-sky-500';
        } else if (type === 'success') {
            borderClass = 'border-emerald-500/80 dark:border-emerald-500/80';
            iconBgClass = 'bg-emerald-500/15 text-emerald-500';
            iconName = 'check-circle-2';
            barClass = 'bg-emerald-500';
        }

        toast.classList.add(...borderClass.split(' '));

        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl ${iconBgClass} flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i data-lucide="${iconName}" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-900 dark:text-white leading-tight" style="font-family: 'Almarai', sans-serif;">${message}</p>
                </div>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-sm font-bold p-1 rounded-lg transition-colors" onclick="this.parentElement.remove()">
                ✕
            </button>
            <div class="toast-progress absolute bottom-0 left-0 right-0 h-1 ${barClass} transition-all ease-linear" style="width: 100%; transition-duration: ${duration}ms;"></div>
        `;

        container.appendChild(toast);
        if (window.lucide) window.lucide.createIcons();

        setTimeout(() => {
            toast.classList.remove('-translate-y-4', 'opacity-0');
            const progress = toast.querySelector('.toast-progress');
            if (progress) progress.style.width = '0%';
        }, 10);

        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-4');
            setTimeout(() => toast.remove(), 500);
        }, duration);
    };

    // ── Auto trigger Laravel session alerts as Luxury Toasts ──────────────────
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            showLuxuryToast(@json(session('success')), 'success');
        @endif

        @if(session('error'))
            showLuxuryToast(@json(session('error')), 'error');
        @endif

        @if(session('status'))
            showLuxuryToast(@json(session('status')), 'info');
        @endif

        @if(session('warning'))
            showLuxuryToast(@json(session('warning')), 'warning');
        @endif
    });
})();
</script>
