<!-- ========================================================================= -->
<!-- LUXURY SYSTEM DIALOGS & ALERT MODAL (Day & Night Mode Supported)          -->
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
                    style="background: rgba(150, 150, 150, 0.15) !important;"
                    class="px-6 py-3 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-white/20 rounded-xl text-xs font-black transition-all duration-300">
                إلغاء
            </button>
            <button type="button" 
                    id="luxuryConfirmOkBtn" 
                    style="background: #dc2626 !important; color: #ffffff !important;"
                    class="px-7 py-3 rounded-xl text-xs font-black shadow-lg hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center gap-2">
                <i id="luxuryConfirmOkIcon" data-lucide="trash-2" class="w-4 h-4 text-white"></i>
                <span id="luxuryConfirmOkText" class="text-white font-black">تأكيد</span>
            </button>
        </div>
    </div>
</div>

<!-- 2. LUXURY SUCCESS & NOTIFICATION POPUP MODAL -->
<div id="luxuryAlertModal" 
     class="hidden fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none" 
     style="z-index: 9999999 !important; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; isolation: isolate !important;"
     dir="rtl"
     tabindex="-1">
    
    <div id="luxuryAlertCard" 
         class="bg-white dark:bg-[#141414] border-2 border-[#D4AF37] dark:border-[#D4AF37] rounded-[32px] shadow-[0_30px_100px_rgba(0,0,0,0.5)] max-w-md w-full p-6 text-center relative overflow-hidden transform scale-90 transition-all duration-300"
         style="z-index: 10000000 !important; position: relative !important;">
        
        <!-- Top Decorative Gold Line -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-transparent via-[#D4AF37] to-transparent"></div>
        
        <!-- Header & Icon Section -->
        <div class="flex flex-col items-center justify-center gap-3 mb-4">
            <div id="luxuryAlertIconBg" class="w-16 h-16 rounded-full bg-emerald-500/15 border-2 border-emerald-500/30 flex items-center justify-center shadow-lg shadow-emerald-500/10">
                <i id="luxuryAlertIcon" data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500"></i>
            </div>
            
            <div class="space-y-1">
                <h3 id="luxuryAlertTitle" class="text-xl font-black text-gray-900 dark:text-white tracking-tight" style="font-family: 'Almarai', sans-serif;">
                    تم الإجراء بنجاح
                </h3>
                <p id="luxuryAlertMessage" class="text-xs font-bold text-gray-600 dark:text-gray-300 leading-relaxed px-2">
                    تم تنفيذ العملية المطلوب بنجاح.
                </p>
            </div>
        </div>

        <!-- Action Button -->
        <div class="pt-4 border-t border-gray-100 dark:border-white/10 flex justify-center">
            <button type="button" 
                    id="luxuryAlertCloseBtn" 
                    style="background: #D4AF37 !important; color: #000000 !important; font-weight: 900;"
                    class="px-10 py-3 rounded-xl text-xs shadow-lg hover:scale-[1.03] active:scale-95 transition-all duration-300 flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4 text-black"></i>
                <span>حسناً</span>
            </button>
        </div>
    </div>
</div>

<!-- 3. SYSTEM SCRIPTS & INTERCEPTORS -->
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
            const cancelBtn = document.getElementById('luxuryConfirmCancelBtn');
            
            const title = options.title || 'تأكيد الإجراء';
            const message = options.message || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
            const type = options.type || (message.includes('حذف') || message.includes('مسح') ? 'danger' : 'warning');
            const confirmText = options.confirmText || (type === 'danger' ? 'نعم، قم بالحذف' : 'نعم، تأكيد');
            const cancelText = options.cancelText || 'إلغاء';

            titleEl.textContent = title;
            msgEl.textContent = message;
            okText.textContent = confirmText;
            cancelBtn.textContent = cancelText;

            // Apply explicit styles for max visibility in both Light & Dark modes
            if (type === 'danger') {
                iconBg.className = 'w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-rose-500/10';
                iconEl.setAttribute('data-lucide', 'alert-triangle');
                iconEl.className = 'w-7 h-7 text-rose-500';
                okBtn.style.cssText = 'background: #dc2626 !important; color: #ffffff !important; font-weight: 900;';
                okIcon.setAttribute('data-lucide', 'trash-2');
                okIcon.className = 'w-4 h-4 text-white';
                okText.className = 'text-white font-black';
            } else {
                iconBg.className = 'w-14 h-14 rounded-2xl bg-[#D4AF37]/15 border border-[#D4AF37]/30 flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#D4AF37]/10';
                iconEl.setAttribute('data-lucide', 'help-circle');
                iconEl.className = 'w-7 h-7 text-[#D4AF37]';
                okBtn.style.cssText = 'background: #D4AF37 !important; color: #000000 !important; font-weight: 900;';
                okIcon.setAttribute('data-lucide', 'check-circle-2');
                okIcon.className = 'w-4 h-4 text-black';
                okText.className = 'text-black font-black';
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

    // ── Global Success / Alert Popup Modal Function ──────────────────────────────
    window.showLuxuryAlert = function(options = {}) {
        const modal = document.getElementById('luxuryAlertModal');
        const card = document.getElementById('luxuryAlertCard');
        const titleEl = document.getElementById('luxuryAlertTitle');
        const msgEl = document.getElementById('luxuryAlertMessage');
        const iconBg = document.getElementById('luxuryAlertIconBg');
        const iconEl = document.getElementById('luxuryAlertIcon');

        const message = options.message || options.title || 'تم تنفيذ العملية بنجاح';
        const type = options.type || (message.includes('حذف') || message.includes('مسح') ? 'success' : 'success');
        const title = options.title || (message.includes('حذف') || message.includes('مسح') ? 'تم الحذف بنجاح' : 'تم الإجراء بنجاح');

        titleEl.textContent = title;
        msgEl.textContent = message;

        if (type === 'error' || type === 'danger') {
            iconBg.className = 'w-16 h-16 rounded-full bg-rose-500/15 border-2 border-rose-500/30 flex items-center justify-center shadow-lg shadow-rose-500/10';
            iconEl.setAttribute('data-lucide', 'shield-alert');
            iconEl.className = 'w-8 h-8 text-rose-500';
        } else {
            iconBg.className = 'w-16 h-16 rounded-full bg-emerald-500/15 border-2 border-emerald-500/30 flex items-center justify-center shadow-lg shadow-emerald-500/10';
            iconEl.setAttribute('data-lucide', 'check-circle-2');
            iconEl.className = 'w-8 h-8 text-emerald-500';
        }

        if (window.lucide) window.lucide.createIcons();

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            card.classList.remove('scale-90');
            card.classList.add('scale-100');
        }, 10);
    };

    function closeAlertModal() {
        const modal = document.getElementById('luxuryAlertModal');
        const card = document.getElementById('luxuryAlertCard');
        if (!modal) return;
        
        card.classList.remove('scale-100');
        card.classList.add('scale-90');
        modal.classList.add('opacity-0', 'pointer-events-none');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    document.getElementById('luxuryAlertCloseBtn')?.addEventListener('click', closeAlertModal);

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
            if (form.dataset.luxuryConfirmed === 'true') {
                delete form.dataset.luxuryConfirmed;
                return true;
            }
            
            e.preventDefault();
            e.stopImmediatePropagation();

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

    // ── Auto trigger Laravel session alerts as Luxury Popup Modal ──────────────
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            showLuxuryAlert({
                title: @json(session('success')).includes('حذف') ? 'تم الحذف بنجاح' : 'تم الإجراء بنجاح',
                message: @json(session('success')),
                type: 'success'
            });
        @endif

        @if(session('error'))
            showLuxuryAlert({
                title: 'تنبيه بالنظام',
                message: @json(session('error')),
                type: 'error'
            });
        @endif

        @if(session('status'))
            showLuxuryAlert({
                title: 'إشعار بالنظام',
                message: @json(session('status')),
                type: 'info'
            });
        @endif
    });
})();
</script>
