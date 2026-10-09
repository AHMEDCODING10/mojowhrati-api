@if(session('require_password_reset_modal') || session('show_master_alert_step'))
<div id="masterPasswordModalOverlay" class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-lg flex items-center justify-center p-4 animate-fade-in" dir="rtl">
    
    <!-- STEP 1: System Alert Modal -->
    <div id="masterAlertStep" class="bg-white dark:bg-[#1A1A1A] border-2 border-[#D4AF37] rounded-[32px] shadow-[0_25px_80px_rgba(212,175,55,0.3)] max-w-md w-full p-8 text-center relative overflow-hidden transition-all duration-300">
        <!-- Gold Glow Blob -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-[#D4AF37]/20 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="w-20 h-20 bg-[#D4AF37]/10 dark:bg-[#D4AF37]/20 rounded-full border-2 border-[#D4AF37] flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i data-lucide="shield-alert" class="w-10 h-10 text-[#D4AF37]"></i>
        </div>

        <span class="inline-block px-4 py-1.5 bg-[#D4AF37]/10 text-[#B88F3D] dark:text-[#E8D095] text-xs font-black rounded-full mb-3 border border-[#D4AF37]/30">
            تنبيه حماية النظام
        </span>

        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3" style="font-family: 'Almarai', sans-serif;">
            تنبيه من النظام
        </h3>

        <p class="text-gray-600 dark:text-gray-300 text-sm font-bold leading-relaxed mb-8" style="font-family: 'Almarai', sans-serif;">
            لقد تم كسر كلمة المرور لهذا الحساب بنجاح. الرجاء إعادة كتابة كلمة مرور جديدة لضمان سلامة وأمان بياناتك.
        </p>

        <button type="button" 
                onclick="showResetPasswordStep()" 
                class="w-full py-4 bg-gradient-to-r from-[#DFB967] to-[#B08535] hover:from-[#E5C378] hover:to-[#C29543] text-black font-black text-base rounded-2xl shadow-[0_10px_25px_rgba(212,175,55,0.4)] transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
            <span>موافق</span>
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- STEP 2: Password Reset Form Modal -->
    <div id="masterResetStep" class="hidden bg-white dark:bg-[#1A1A1A] border-2 border-[#D4AF37] rounded-[32px] shadow-[0_25px_80px_rgba(212,175,55,0.3)] max-w-md w-full p-8 relative overflow-hidden transition-all duration-300">
        
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-[#D4AF37]/10 dark:bg-[#D4AF37]/20 rounded-full border border-[#D4AF37] flex items-center justify-center mx-auto mb-4">
                <i data-lucide="key-round" class="w-8 h-8 text-[#D4AF37]"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-1" style="font-family: 'Almarai', sans-serif;">
                إعادة تعيين كلمة المرور
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-xs font-bold">
                أدخل كلمة المرور الجديدة لحفظها تلقائياً في قاعدة البيانات
            </p>
        </div>

        <!-- Error Notification Container -->
        <div id="resetModalError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-xl text-right"></div>
        <div id="resetModalSuccess" class="hidden mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl text-center"></div>

        <form id="masterPasswordResetForm" onsubmit="submitMasterPasswordReset(event)" class="space-y-5">
            @csrf

            <!-- New Password -->
            <div class="space-y-2 text-right">
                <label for="modal_new_password" class="block text-xs font-black text-gray-700 dark:text-gray-300">
                    كلمة المرور الجديدة
                </label>
                <div class="relative">
                    <input id="modal_new_password" 
                           type="password" 
                           name="password" 
                           required 
                           placeholder="أدخل كلمة المرور الجديدة (6 أحرف على الأقل)" 
                           class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gold/30 rounded-xl pr-4 pl-12 py-3.5 text-sm font-bold text-gray-900 dark:text-white focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] outline-none transition-all" />
                    <button type="button" 
                            onclick="toggleModalPassVisibility('modal_new_password', 'icon_new_pass')" 
                            tabindex="-1"
                            class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 hover:text-[#D4AF37]">
                        <i id="icon_new_pass" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm New Password -->
            <div class="space-y-2 text-right">
                <label for="modal_confirm_password" class="block text-xs font-black text-gray-700 dark:text-gray-300">
                    إعادة كلمة المرور الجديدة
                </label>
                <div class="relative">
                    <input id="modal_confirm_password" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           placeholder="أعد كتابة كلمة المرور جديدة للبيان" 
                           class="w-full bg-gray-50 dark:bg-black/40 border border-gray-300 dark:border-gold/30 rounded-xl pr-4 pl-12 py-3.5 text-sm font-bold text-gray-900 dark:text-white focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] outline-none transition-all" />
                    <button type="button" 
                            onclick="toggleModalPassVisibility('modal_confirm_password', 'icon_confirm_pass')" 
                            tabindex="-1"
                            class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 hover:text-[#D4AF37]">
                        <i id="icon_confirm_pass" data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        id="submitResetBtn"
                        class="w-full py-4 bg-gradient-to-r from-[#DFB967] to-[#B08535] hover:from-[#E5C378] hover:to-[#C29543] text-black font-black text-base rounded-2xl shadow-[0_10px_25px_rgba(212,175,55,0.4)] transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    <span id="submitResetText">حفظ كلمة المرور الجديدة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showResetPasswordStep() {
        document.getElementById('masterAlertStep').classList.add('hidden');
        document.getElementById('masterResetStep').classList.remove('hidden');
        if (window.lucide) window.lucide.createIcons();
    }

    function toggleModalPassVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        if (window.lucide) window.lucide.createIcons();
    }

    function submitMasterPasswordReset(e) {
        e.preventDefault();
        const errDiv = document.getElementById('resetModalError');
        const succDiv = document.getElementById('resetModalSuccess');
        const btnText = document.getElementById('submitResetText');
        const pass = document.getElementById('modal_new_password').value;
        const confirmPass = document.getElementById('modal_confirm_password').value;

        errDiv.classList.add('hidden');
        succDiv.classList.add('hidden');

        if (pass.length < 6) {
            errDiv.textContent = 'كلمة المرور يجب أن لا تقل عن 6 أحرف.';
            errDiv.classList.remove('hidden');
            return;
        }

        if (pass !== confirmPass) {
            errDiv.textContent = 'كلمة المرور وتأكيد كلمة المرور غير متطابقين.';
            errDiv.classList.remove('hidden');
            return;
        }

        btnText.textContent = 'جاري الحفظ والتحديث...';

        fetch('{{ route('profile.update-master-password') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                password: pass,
                password_confirmation: confirmPass
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                succDiv.textContent = data.message;
                succDiv.classList.remove('hidden');
                btnText.textContent = 'تم الحفظ بنجاح!';
                setTimeout(() => {
                    document.getElementById('masterPasswordModalOverlay').remove();
                }, 1500);
            } else {
                btnText.textContent = 'حفظ كلمة المرور الجديدة';
                errDiv.textContent = data.message || 'حدث خطأ أثناء التحديث.';
                errDiv.classList.remove('hidden');
            }
        })
        .catch(err => {
            btnText.textContent = 'حفظ كلمة المرور الجديدة';
            errDiv.textContent = 'فشل الاتصال بالسيرفر. يرجى المحاولة لاحقاً.';
            errDiv.classList.remove('hidden');
        });
    }
</script>
@endif
