@extends('layouts.admin')

@section('title', __('إدارة التواصل'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-14 h-14 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="contact-2" class="w-7 h-7 text-gold"></i>
                </div>
                <div>
                    <h2 class="text-4xl font-black text-main uppercase tracking-widest">{{ __('إدارة التواصل') }}</h2>
                    <div class="h-1 w-20 bg-gold rounded-full mt-2 shadow-[0_0_12px_rgba(212,175,55,0.5)]"></div>
                </div>
            </div>
        </div>
        
        <button onclick="document.getElementById('add-contact-modal').classList.remove('hidden')" 
            class="group relative px-10 py-5 bg-gold text-onyx font-black text-[11px] uppercase tracking-widest rounded-2xl shadow-xl shadow-gold/20 hover:shadow-gold/40 hover:scale-105 transition-all duration-500 flex items-center gap-3 overflow-hidden">
            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 rounded-2xl"></div>
            <i data-lucide="plus-circle" class="w-5 h-5 relative z-10 transition-transform group-hover:rotate-90 duration-500"></i>
            <span class="relative z-10">{{ __('إضافة وسيلة تواصل') }}</span>
        </button>
    </div>

    @if(session('success'))
    <div class="px-8 py-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-black text-sm text-center shadow-lg shadow-emerald-500/5 animate-in fade-in slide-in-from-top-4 duration-700 flex items-center justify-center gap-3">
        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Contacts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($contacts as $contact)
        <div class="luxury-card p-0 border border-gold/10 bg-white/50 dark:bg-[#1A1A1A]/50 backdrop-blur-md rounded-[2.5rem] shadow-xl hover:shadow-gold/10 hover:border-gold/30 transition-all duration-500 overflow-hidden relative group">
            
            <div class="p-8 space-y-8">
                <div class="flex items-start justify-between">
                    <div class="w-16 h-16 rounded-2xl bg-gold/5 flex items-center justify-center text-gold border border-gold/10 shadow-lg group-hover:bg-gold group-hover:text-onyx transition-colors duration-500">
                        <i data-lucide="{{ $contact->icon ?: 'link' }}" class="w-7 h-7"></i>
                    </div>
                    
                    <div class="flex gap-2">
                        <button onclick='openEditModal({!! json_encode($contact) !!})' 
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-black border border-gold/10 text-muted/40 hover:text-gold hover:border-gold/40 transition-all shadow-sm" 
                                title="{{ __('تعديل') }}">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('contacts.toggle', $contact->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-black border border-gold/10 {{ $contact->is_active ? 'text-emerald-500 hover:bg-emerald-500/10 hover:border-emerald-500/30' : 'text-rose-500 hover:bg-rose-500/10 hover:border-rose-500/30' }} transition-all shadow-sm" 
                                    title="{{ $contact->is_active ? __('تعطيل') : __('تفعيل') }}">
                                <i data-lucide="{{ $contact->is_active ? 'shield-check' : 'shield-off' }}" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-500/5 text-rose-500 border border-rose-500/10 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-gold/60 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-gold/40"></span>
                        {{ $contact->label ?? $contact->type }}
                    </p>
                    <p class="text-2xl font-black text-main tracking-tight truncate" dir="ltr">{{ $contact->value }}</p>
                </div>

                <div class="pt-6 border-t border-gold/5 flex justify-between items-center">
                    <div class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted/40">
                        <i data-lucide="sort-asc" class="w-3.5 h-3.5"></i>
                        {{ __('الترتيب:') }} <span class="text-main">{{ $contact->order }}</span>
                    </div>
                    <span class="px-4 py-1.5 bg-gold/10 rounded-full text-[9px] font-black text-gold uppercase tracking-widest">{{ $contact->type }}</span>
                </div>
            </div>
            
            @if(!$contact->is_active)
            <div class="absolute inset-0 bg-white/40 dark:bg-black/40 backdrop-blur-[1px] z-10 pointer-events-none"></div>
            <div class="absolute inset-0 flex items-center justify-center z-20 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <span class="px-6 py-2 bg-rose-500 text-white rounded-full text-xs font-black uppercase tracking-widest shadow-xl">{{ __('معطل') }}</span>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full luxury-card p-24 text-center space-y-8 border border-dashed border-gold/20 bg-gold/5 rounded-[3rem]">
            <div class="w-32 h-32 bg-gold/10 rounded-full flex items-center justify-center mx-auto text-gold border border-gold/10 shadow-lg shadow-gold/5">
                <i data-lucide="contact-2" class="w-16 h-16"></i>
            </div>
            <div class="space-y-3">
                <h3 class="text-3xl font-black text-main tracking-tight italic opacity-40">{{ __('لا توجد وسائل تواصل بعد') }}</h3>
                <p class="text-muted/40 text-[10px] font-black uppercase tracking-widest">{{ __('أضف قنوات التواصل ليتمكن عملاؤك من الوصول إليك بسهولة.') }}</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    /* Styling for the Modals to center flawlessly and look elegant */
    .premium-modal-backdrop {
        background-color: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px);
    }
    .premium-modal-container {
        width: 100%;
        max-width: 32rem; /* max-w-lg */
        margin: auto;
    }
    .icon-btn.selected {
        background-color: rgba(212, 175, 55, 0.1) !important;
        border-color: rgba(212, 175, 55, 0.5) !important;
        color: #D4AF37 !important;
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
        transform: scale(1.05);
    }
</style>

<!-- Add Contact Modal -->
<div id="add-contact-modal" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center hidden premium-modal-backdrop p-4 pt-24">
    <!-- Click outside to close -->
    <div class="absolute inset-0" onclick="document.getElementById('add-contact-modal').classList.add('hidden')"></div>
    
    <div class="premium-modal-container relative bg-gradient-to-br from-white/95 to-white/90 dark:from-[#111111]/95 dark:to-[#1A1A1A]/90 backdrop-blur-xl border border-gold/20 rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all max-h-[85vh] flex flex-col my-auto">
        
        <!-- Modal Header -->
        <div class="px-8 py-6 border-b border-gold/10 bg-gold/5 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gold text-onyx flex items-center justify-center shadow-lg shadow-gold/20">
                    <i data-lucide="plus" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-main tracking-tight">{{ __('إضافة وسيلة تواصل') }}</h3>
                    <p class="text-[9px] text-muted/60 font-black uppercase tracking-widest mt-0.5">New Channel</p>
                </div>
            </div>
            <button onclick="document.getElementById('add-contact-modal').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-main/5 text-muted/40 hover:bg-rose-500/10 hover:text-rose-500 transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body & Form -->
        <form action="{{ route('contacts.store') }}" method="POST" class="px-8 pt-8 pb-12 space-y-6 overflow-y-auto custom-scrollbar">
            @csrf
            
            <!-- Type -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="list-filter" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('نوع الوسيلة') }}
                </label>
                <select name="type" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm font-bold text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all appearance-none" required>
                    <option value="phone">{{ __('هاتف') }}</option>
                    <option value="whatsapp">{{ __('واتساب') }}</option>
                    <option value="instagram">{{ __('انستقرام') }}</option>
                    <option value="facebook">{{ __('فيسبوك') }}</option>
                    <option value="tiktok">{{ __('تيك توك') }}</option>
                    <option value="email">{{ __('بريد إلكتروني') }}</option>
                    <option value="website">{{ __('موقع إلكتروني') }}</option>
                    <option value="location">{{ __('موقع جغرافي') }}</option>
                </select>
            </div>

            <!-- Icon Picker -->
            <div class="space-y-3 border-t border-b border-gold/5 py-4">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('اختر أيقونة مناسبة') }}
                </label>
                <input type="hidden" name="icon" id="add-icon-input" value="phone">
                <div class="grid grid-cols-6 gap-2 p-2 max-h-32 overflow-y-auto custom-scrollbar">
                    @php
                        $availableIcons = ['phone', 'message-circle', 'instagram', 'facebook', 'music', 'mail', 'globe', 'map-pin', 'twitter', 'youtube', 'message-square', 'link', 'smartphone', 'send'];
                    @endphp
                    @foreach($availableIcons as $iconName)
                    <button type="button" onclick="selectIcon('{{ $iconName }}', 'add')" id="add-icon-{{ $iconName }}" 
                            class="icon-btn aspect-square flex items-center justify-center rounded-xl bg-white dark:bg-black/40 border border-gold/10 text-muted/40 hover:text-gold hover:border-gold/30 hover:bg-gold/5 transition-all">
                        <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Label -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="type" class="w-3.5 h-3.5 text-gold/60"></i>
                        {{ __('تسمية اختيارية') }}
                    </label>
                    <input type="text" name="label" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30" placeholder="{{ __('خدمة العملاء') }}">
                </div>
                <!-- Order -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="sort-asc" class="w-3.5 h-3.5 text-gold/60"></i>
                        {{ __('ترتيب العرض') }}
                    </label>
                    <input type="number" name="order" value="0" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all">
                </div>
            </div>

            <!-- Value -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="link-2" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('رقم الهاتف أو الرابط') }}
                </label>
                <input type="text" name="value" required class="w-full bg-white dark:bg-black/40 border border-gold/30 rounded-2xl px-5 py-4 text-base font-black text-gold focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none transition-all placeholder:text-muted/20 placeholder:font-normal text-left" dir="ltr" placeholder="0096... or https://...">
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full h-14 bg-gold text-onyx rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-gold/20 hover:shadow-gold/40 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    {{ __('حفظ القناة') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Contact Modal -->
<div id="edit-contact-modal" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center hidden premium-modal-backdrop p-4 pt-24">
    <!-- Click outside to close -->
    <div class="absolute inset-0" onclick="document.getElementById('edit-contact-modal').classList.add('hidden')"></div>
    
    <div class="premium-modal-container relative bg-gradient-to-br from-white/95 to-white/90 dark:from-[#111111]/95 dark:to-[#1A1A1A]/90 backdrop-blur-xl border border-gold/20 rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all max-h-[85vh] flex flex-col my-auto">
        
        <!-- Modal Header -->
        <div class="px-8 py-6 border-b border-gold/10 bg-gold/5 flex justify-between items-center shrink-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gold text-onyx flex items-center justify-center shadow-lg shadow-gold/20">
                    <i data-lucide="edit-3" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-main tracking-tight">{{ __('تعديل وسيلة تواصل') }}</h3>
                    <p class="text-[9px] text-muted/60 font-black uppercase tracking-widest mt-0.5">Edit Channel</p>
                </div>
            </div>
            <button onclick="document.getElementById('edit-contact-modal').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-main/5 text-muted/40 hover:bg-rose-500/10 hover:text-rose-500 transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body & Form -->
        <form id="edit-contact-form" method="POST" class="px-8 pt-8 pb-12 space-y-6 overflow-y-auto custom-scrollbar">
            @csrf
            
            <!-- Type -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="list-filter" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('نوع الوسيلة') }}
                </label>
                <select name="type" id="edit-type" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm font-bold text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all appearance-none" required>
                    <option value="phone">{{ __('هاتف') }}</option>
                    <option value="whatsapp">{{ __('واتساب') }}</option>
                    <option value="instagram">{{ __('انستقرام') }}</option>
                    <option value="facebook">{{ __('فيسبوك') }}</option>
                    <option value="tiktok">{{ __('تيك توك') }}</option>
                    <option value="email">{{ __('بريد إلكتروني') }}</option>
                    <option value="website">{{ __('موقع إلكتروني') }}</option>
                    <option value="location">{{ __('موقع جغرافي') }}</option>
                </select>
            </div>

            <!-- Icon Picker -->
            <div class="space-y-3 border-t border-b border-gold/5 py-4">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('تحديث الأيقونة') }}
                </label>
                <input type="hidden" name="icon" id="edit-icon-input" value="phone">
                <div class="grid grid-cols-6 gap-2 p-2 max-h-32 overflow-y-auto custom-scrollbar">
                    @foreach($availableIcons as $iconName)
                    <button type="button" onclick="selectIcon('{{ $iconName }}', 'edit')" id="edit-icon-{{ $iconName }}" 
                            class="icon-btn aspect-square flex items-center justify-center rounded-xl bg-white dark:bg-black/40 border border-gold/10 text-muted/40 hover:text-gold hover:border-gold/30 hover:bg-gold/5 transition-all">
                        <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Label -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="type" class="w-3.5 h-3.5 text-gold/60"></i>
                        {{ __('تسمية اختيارية') }}
                    </label>
                    <input type="text" name="label" id="edit-label" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30">
                </div>
                <!-- Order -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="sort-asc" class="w-3.5 h-3.5 text-gold/60"></i>
                        {{ __('ترتيب العرض') }}
                    </label>
                    <input type="number" name="order" id="edit-order" class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-4 py-3.5 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all">
                </div>
            </div>

            <!-- Value -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted/60 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="link-2" class="w-3.5 h-3.5 text-gold/60"></i>
                    {{ __('رقم الهاتف أو الرابط') }}
                </label>
                <input type="text" name="value" id="edit-value" required class="w-full bg-white dark:bg-black/40 border border-gold/30 rounded-2xl px-5 py-4 text-base font-black text-gold focus:ring-2 focus:ring-gold/40 focus:border-gold outline-none transition-all text-left" dir="ltr">
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full h-14 bg-gradient-to-r from-gold to-yellow-600 text-onyx rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-gold/30 hover:shadow-gold/50 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    {{ __('حفظ التعديلات') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectIcon(iconName, modalType) {
        // Clear previous selection
        document.querySelectorAll(`#${modalType}-contact-modal .icon-btn`).forEach(btn => btn.classList.remove('selected'));
        
        // Mark new selection
        const btn = document.getElementById(`${modalType}-icon-${iconName}`);
        if(btn) btn.classList.add('selected');
        
        // Update hidden input
        const input = document.getElementById(`${modalType}-icon-input`);
        if(input) input.value = iconName;
    }

    function openEditModal(contact) {
        const modal = document.getElementById('edit-contact-modal');
        const form = document.getElementById('edit-contact-form');
        
        // Set action URL
        form.action = `/contacts/${contact.id}`;
        
        // Fill fields
        document.getElementById('edit-type').value = contact.type;
        document.getElementById('edit-label').value = contact.label || '';
        document.getElementById('edit-value').value = contact.value;
        document.getElementById('edit-order').value = contact.order;
        
        // Handle icon selection
        selectIcon(contact.icon || 'link', 'edit');
        
        // Show modal
        modal.classList.remove('hidden');
    }

    // Initialize Add Modal icon
    window.addEventListener('load', () => {
        selectIcon('phone', 'add');
    });
</script>
@endsection
