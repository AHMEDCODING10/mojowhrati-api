@extends('layouts.admin')

@section('title', __('النسخ الاحتياطية'))

@section('content')
<div class="space-y-10 pb-20" dir="rtl"
     x-data="backupManager()"
     x-init="init()">

    {{-- ─── Header ─────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pt-4 pb-8 border-b border-gold/10 relative">
        <div class="absolute right-0 top-0 w-40 h-40 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative">
            <h2 class="text-4xl lg:text-5xl font-black text-main uppercase tracking-[0.15em] leading-tight flex items-center gap-4">
                {{ __('النسخ الاحتياطية') }}
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="database-backup" class="w-6 h-6 text-gold"></i>
                </div>
            </h2>
            <div class="flex items-center gap-3 mt-4">
                <div class="h-1.5 w-20 bg-gold shadow-[0_0_20px_rgba(212,175,55,0.6)] rounded-full"></div>
                <div class="h-1.5 w-6 bg-gold/30 rounded-full"></div>
                <p class="text-[10px] text-muted/60 font-black uppercase tracking-[0.3em] mr-2">{{ __('حفظ البيانات وأمان النظام') }}</p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Create Backup --}}
            <button @click="startCreateBackup()"
                    id="btn-create-backup"
                    class="relative group flex items-center gap-3 px-6 py-3.5 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#E8D095] text-onyx font-black text-sm shadow-lg shadow-gold/30 hover:shadow-gold/50 transition-all duration-300 active:scale-95 overflow-hidden">
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <i data-lucide="cloud-upload" class="w-5 h-5 relative z-10"></i>
                <span class="relative z-10">{{ __('بدء النسخ الاحتياطي') }}</span>
            </button>

            {{-- Restore Backup --}}
            <label for="restore-file-input"
                   class="relative group flex items-center gap-3 px-6 py-3.5 rounded-2xl border-2 border-amber-600 bg-amber-600/10 hover:bg-amber-600/20 text-amber-700 dark:text-amber-400 font-black text-sm cursor-pointer transition-all duration-300 active:scale-95">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>{{ __('استرجاع نسخة احتياطية') }}</span>
            </label>
            <input type="file" id="restore-file-input" accept=".zip,.sql"
                   class="hidden" @change="onFileSelected($event)">
        </div>
    </div>

    {{-- ─── Info Card ──────────────────────────────── --}}
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-6 shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gold/5 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative flex items-start gap-5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-gold to-[#E8D095] flex items-center justify-center shadow-lg shadow-gold/20 flex-shrink-0">
                <i data-lucide="shield-check" class="w-7 h-7 text-onyx"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-main mb-1">{{ __('إنشاء واسترجاع النسخ الاحتياطية') }}</h3>
                <p class="text-sm text-muted/70 leading-relaxed font-bold">
                    {{ __('يمكنك تصوير قاعدة البيانات بالكامل دون نقص وحفظها على جهازك أو القرص. كما يمكنك استرجاع أي نسخة احتياطية سابقة مع التنبيه والتحقق الأمني.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ─── Stored Backups List ─────────────────────── --}}
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-8 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <i data-lucide="folder-archive" class="w-6 h-6 text-gold"></i>
                <h4 class="text-base font-black text-main">{{ __('ملفات النسخ الاحتياطية المحفوظة بالنظام') }}</h4>
            </div>
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold/10 border border-gold/20 text-gold text-xs font-black">
                {{ count($backups) }} {{ __('ملفات') }}
            </span>
        </div>

        @if(empty($backups))
            <div class="text-center py-20">
                <div class="w-20 h-20 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="database" class="w-10 h-10 text-gold/40"></i>
                </div>
                <p class="text-muted/50 font-black text-sm">{{ __('لا توجد نسخ احتياطية محفوظة بعد') }}</p>
                <p class="text-muted/30 text-xs mt-1">{{ __('اضغط على "بدء النسخ الاحتياطي" لإنشاء أول نسخة') }}</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($backups as $backup)
                <div class="group flex items-center justify-between p-4 rounded-2xl bg-white/80 dark:bg-black/30 border border-gold/10 hover:border-gold/30 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-archive" class="w-5 h-5 text-gold"></i>
                        </div>
                        <div>
                            <p class="font-black text-main text-sm">{{ $backup['name'] }}</p>
                            <p class="text-xs text-muted/50 mt-0.5">{{ $backup['modified'] }} — {{ $backup['size'] }} MB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('backups.download', ['filename' => $backup['name']]) }}"
                           class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-black hover:bg-emerald-500/20 transition-all">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            {{ __('تحميل') }}
                        </a>
                        <form method="POST" action="{{ route('backups.destroy', ['filename' => $backup['name']]) }}"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه النسخة؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-black hover:bg-rose-500/20 transition-all">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                {{ __('حذف') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════
         MODALS
    ════════════════════════════════════════════ --}}

    {{-- ① Creating backup progress modal --}}
    <div x-show="showCreatingModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-lg"
         style="display:none">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-gold rounded-[2rem] shadow-[0_25px_80px_rgba(212,175,55,0.3)] max-w-md w-full p-8 text-center relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="w-20 h-20 rounded-full bg-gold/10 border-2 border-gold flex items-center justify-center mx-auto mb-6">
                <i data-lucide="loader-2" class="w-10 h-10 text-gold animate-spin"></i>
            </div>
            <h3 class="text-2xl font-black text-main mb-2">{{ __('جارٍ إنشاء النسخة الاحتياطية...') }}</h3>
            <p class="text-sm text-muted/60 font-bold">{{ __('يتم الآن تصوير قاعدة البيانات بالكامل. يرجى الانتظار.') }}</p>
        </div>
    </div>

    {{-- ② Restore confirmation modal --}}
    <div x-show="showRestoreConfirm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-lg"
         style="display:none">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-amber-500 rounded-[2rem] shadow-[0_25px_80px_rgba(245,158,11,0.3)] max-w-lg w-full p-8 relative overflow-hidden" dir="rtl">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-7 h-7 text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-main">{{ __('تحذير: استعادة النسخة الاحتياطية') }}</h3>
                    <p class="text-xs text-muted/50 font-bold mt-0.5" x-text="selectedFileName"></p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-rose-500/5 border border-rose-500/20 mb-6">
                <p class="text-sm font-black text-rose-600 dark:text-rose-400">
                    ⚠️ {{ __('سيتم فقدان جميع البيانات الحالية وإعادة الاستعادة من النسخة المحددة. هذا الإجراء لا يمكن التراجع عنه.') }}
                </p>
            </div>

            <div class="space-y-3">
                {{-- Option 1: Create backup before restore --}}
                <button @click="preBackupThenRestore()"
                        :disabled="preBackupDone"
                        :class="preBackupDone
                            ? 'opacity-40 cursor-not-allowed bg-gray-100 dark:bg-gray-800 border-gray-200 text-gray-400'
                            : 'bg-gradient-to-tr from-gold to-[#E8D095] text-onyx hover:shadow-gold/40 hover:shadow-lg cursor-pointer'"
                        class="w-full flex items-center justify-between gap-3 px-6 py-4 rounded-2xl font-black text-sm transition-all duration-300 border border-gold/20">
                    <div class="flex items-center gap-3">
                        <i data-lucide="cloud-upload" class="w-5 h-5"></i>
                        <span>{{ __('إنشاء نسخة احتياطية قبل الاستعادة') }}</span>
                    </div>
                    <template x-if="preBackupDone">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                    </template>
                    <template x-if="!preBackupDone">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </template>
                </button>

                {{-- Option 2: Restore directly --}}
                <button @click="openPasswordModal()"
                        class="w-full flex items-center justify-between gap-3 px-6 py-4 rounded-2xl border-2 border-rose-500 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-black text-sm transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                        <span>{{ __('استعادة النسخة المحددة') }}</span>
                    </div>
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>

                {{-- Cancel --}}
                <button @click="showRestoreConfirm = false"
                        class="w-full py-3 text-center text-xs font-black text-muted/50 hover:text-muted transition-colors">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ③ Pre-backup success modal --}}
    <div x-show="showPreBackupSuccess"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/70 backdrop-blur-lg"
         style="display:none">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-emerald-500 rounded-[2rem] shadow-[0_25px_80px_rgba(16,185,129,0.3)] max-w-md w-full p-8 text-center relative overflow-hidden" dir="rtl">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="w-20 h-20 rounded-full bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center mx-auto mb-6">
                <i data-lucide="check-circle" class="w-10 h-10 text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-main mb-2">{{ __('تم إنشاء النسخة الاحتياطية!') }}</h3>
            <p class="text-sm text-muted/60 font-bold mb-6" x-text="preBackupSavedName"></p>
            <p class="text-xs text-muted/40 font-bold mb-8">{{ __('يمكنك الآن المتابعة لاستعادة النسخة المحددة.') }}</p>
            <button @click="proceedToRestore()"
                    class="w-full py-4 bg-gradient-to-r from-rose-600 to-rose-500 text-white font-black rounded-2xl shadow-lg hover:shadow-rose-500/40 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                {{ __('المتابعة لاستعادة النسخة المحددة') }}
            </button>
        </div>
    </div>

    {{-- ④ Password confirmation modal --}}
    <div x-show="showPasswordModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/70 backdrop-blur-lg"
         style="display:none">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-gold rounded-[2rem] shadow-[0_25px_80px_rgba(212,175,55,0.3)] max-w-md w-full p-8 relative overflow-hidden" dir="rtl">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-gold/10 border-2 border-gold flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="key-round" class="w-8 h-8 text-gold"></i>
                </div>
                <h3 class="text-xl font-black text-main">{{ __('تأكيد كلمة المرور') }}</h3>
                <p class="text-xs text-muted/50 font-bold mt-1">{{ __('أدخل كلمة مرور حسابك للمتابعة') }}</p>
            </div>

            <div x-show="passwordError" class="mb-4 p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 rounded-xl text-rose-600 dark:text-rose-400 text-xs font-bold" x-text="passwordError"></div>

            <div class="space-y-4">
                <div class="relative">
                    <input :type="showPwd ? 'text' : 'password'"
                           x-model="adminPassword"
                           @keydown.enter="submitRestore()"
                           id="admin-restore-password"
                           class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-black/40 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm transition-all duration-200 pr-12"
                           placeholder="{{ __('كلمة المرور') }}"
                           dir="ltr">
                    <button type="button" @click="showPwd = !showPwd"
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-muted/40 hover:text-muted transition-colors">
                        <i :data-lucide="showPwd ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                    </button>
                </div>

                <button @click="submitRestore()"
                        :disabled="restoringLoading"
                        class="w-full py-4 bg-gradient-to-r from-rose-600 to-rose-500 text-white font-black rounded-2xl shadow-lg hover:shadow-rose-500/40 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50">
                    <i x-show="restoringLoading" data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    <i x-show="!restoringLoading" data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    <span x-text="restoringLoading ? '{{ __('جارٍ الاستعادة...') }}' : '{{ __('استعادة النسخة الاحتياطية') }}'"></span>
                </button>
                <button @click="showPasswordModal = false; adminPassword = ''; passwordError = ''"
                        class="w-full py-2.5 text-xs font-black text-muted/50 hover:text-muted transition-colors">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ⑤ Restore success modal --}}
    <div x-show="showRestoreSuccess"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[10001] flex items-center justify-center p-4 bg-black/80 backdrop-blur-lg"
         style="display:none">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-emerald-500 rounded-[2rem] shadow-[0_25px_80px_rgba(16,185,129,0.4)] max-w-md w-full p-8 text-center" dir="rtl">
            <div class="w-24 h-24 rounded-full bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center mx-auto mb-6">
                <i data-lucide="database" class="w-12 h-12 text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-main mb-3">{{ __('تمت الاستعادة بنجاح!') }}</h3>
            <p class="text-sm text-muted/60 font-bold mb-8">{{ __('تم استرجاع النسخة الاحتياطية وإعادة تهيئة قاعدة البيانات بالكامل.') }}</p>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-[#C5A059] to-[#E8D095] text-onyx font-black rounded-2xl shadow-lg hover:shadow-gold/40 transition-all duration-300">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                {{ __('العودة للرئيسية') }}
            </a>
        </div>
    </div>

</div>

<script>
function backupManager() {
    return {
        // state
        showCreatingModal:   false,
        showRestoreConfirm:  false,
        showPreBackupSuccess:false,
        showPasswordModal:   false,
        showRestoreSuccess:  false,
        selectedFileName:    '',
        restoreFileKey:      '',   // server-side filename returned after validate
        preBackupDone:       false,
        preBackupSavedName:  '',
        adminPassword:       '',
        passwordError:       '',
        showPwd:             false,
        restoringLoading:    false,

        init() {
            // Re-init lucide after alpine renders
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        // ── 1. Create Backup ───────────────────────────────────────
        startCreateBackup() {
            this.showCreatingModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });

            // Trigger download (browser opens save dialog)
            const link = document.createElement('a');
            link.href  = '{{ route("backups.create") }}';
            link.click();

            // Hide spinner after 4 s (conservative, actual download may take longer)
            setTimeout(() => {
                this.showCreatingModal = false;
                // Reload to refresh backup list
                window.location.reload();
            }, 5000);
        },

        // ── 2. File selected for restore ──────────────────────────
        onFileSelected(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.selectedFileName = file.name;

            const formData = new FormData();
            formData.append('backup_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("backups.validate") }}', {
                method: 'POST',
                body:   formData,
            })
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    this.preBackupDone   = false;
                    this.restoreFileKey  = data.restore_file || '';  // ← store server filename
                    this.showRestoreConfirm = true;
                    this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(() => alert('حدث خطأ أثناء فحص الملف'));

            // Reset file input so same file can be chosen again
            event.target.value = '';
        },

        // ── 3. Pre-backup then restore ────────────────────────────
        preBackupThenRestore() {
            this.showRestoreConfirm  = false;
            this.showCreatingModal   = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });

            // Trigger backup download
            const link = document.createElement('a');
            link.href  = '{{ route("backups.create") }}?preRestore=1';
            link.click();

            setTimeout(() => {
                this.showCreatingModal   = false;
                this.preBackupDone       = true;
                this.preBackupSavedName  = 'تم حفظ النسخة الاحتياطية بنجاح في مجلد النظام';
                this.showPreBackupSuccess = true;
                this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
            }, 5000);
        },

        proceedToRestore() {
            this.showPreBackupSuccess = false;
            this.showRestoreConfirm   = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        // ── 4. Open password modal ────────────────────────────────
        openPasswordModal() {
            this.showRestoreConfirm = false;
            this.adminPassword      = '';
            this.passwordError      = '';
            this.showPasswordModal  = true;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                document.getElementById('admin-restore-password')?.focus();
            });
        },

        // ── 5. Submit restore ─────────────────────────────────────
        submitRestore() {
            if (!this.adminPassword) {
                this.passwordError = 'يرجى إدخال كلمة المرور';
                return;
            }
            this.restoringLoading = true;
            this.passwordError    = '';

            fetch('{{ route("backups.restore") }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    password:     this.adminPassword,
                    restore_file: this.restoreFileKey,  // ← send filename directly
                }),
            })
            .then(r => r.json())
            .then(data => {
                this.restoringLoading = false;
                if (data.success) {
                    this.showPasswordModal  = false;
                    this.showRestoreSuccess = true;
                    this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                } else {
                    this.passwordError = data.message || 'فشل الاستعادة';
                }
            })
            .catch(() => {
                this.restoringLoading = false;
                this.passwordError    = 'حدث خطأ في الاتصال';
            });
        },
    };
}
</script>
@endsection
