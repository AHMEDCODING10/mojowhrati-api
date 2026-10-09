<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');

        .premium-input {
            border: 1.5px solid rgba(197, 160, 89, 0.3) !important;
            border-radius: 14px !important;
            padding: 1rem 1.25rem !important;
            font-weight: 700 !important;
            transition: all 0.3s ease !important;
        }

        .premium-input:focus {
            border-color: #C5A059 !important;
            box-shadow: 0 0 20px rgba(197, 160, 89, 0.25) !important;
            outline: none !important;
        }

        .premium-btn {
            background: linear-gradient(135deg, #C5A059 0%, #E8D095 50%, #8B6914 100%) !important;
            color: #111315 !important;
            border-radius: 40px !important;
            font-weight: 900 !important;
            padding: 1.25rem !important;
            box-shadow: 0 10px 30px rgba(197, 160, 89, 0.3) !important;
            transition: all 0.4s ease !important;
        }

        .premium-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 40px rgba(197, 160, 89, 0.5) !important;
            filter: brightness(1.08);
        }

        .gold-underline {
            display: inline-block;
            border-bottom: 4px solid #C5A059;
            padding-bottom: 6px;
        }

        .logo-ring-pulse {
            animation: ringGlow 3s infinite alternate;
        }

        @keyframes ringGlow {
            0% {
                box-shadow: 0 0 25px rgba(197, 160, 89, 0.3), inset 0 0 15px rgba(197, 160, 89, 0.2);
            }
            100% {
                box-shadow: 0 0 55px rgba(197, 160, 89, 0.7), inset 0 0 30px rgba(197, 160, 89, 0.4);
            }
        }
    </style>

    <div class="w-full max-w-6xl px-4 flex flex-col items-center" dir="rtl">
        <!-- Main Dual-Column Card -->
        <div class="bg-white/95 dark:bg-[#15120E]/95 backdrop-blur-xl border border-[#C5A059]/30 dark:border-[#C5A059]/40 overflow-hidden rounded-[40px] shadow-[0_25px_80px_rgba(197,160,89,0.2)] dark:shadow-[0_25px_80px_rgba(0,0,0,0.8)] flex flex-col lg:flex-row min-h-[550px] w-full transition-colors duration-500">
            
            <!-- RIGHT Column (Logo Section) -->
            <div class="w-full lg:w-1/2 p-12 flex flex-col items-center justify-center text-center relative bg-gradient-to-br from-[#FAF5E8] via-[#FFFDF9] to-[#F3E6C8] dark:from-[#1A1610] dark:via-[#15120E] dark:to-[#0A0907] lg:border-l border-[#C5A059]/20 order-first transition-colors duration-500">
                <div class="relative flex flex-col items-center">
                    
                    <!-- Logo Outer Interactive Container -->
                    <div class="relative mb-10 w-64 h-64 lg:w-80 lg:h-80 flex items-center justify-center group cursor-pointer">
                        <div class="absolute inset-0 border-[6px] border-[#C5A059] rounded-full opacity-30 scale-110 group-hover:scale-125 group-hover:opacity-60 transition-all duration-700"></div>
                        <div class="absolute inset-0 border-2 border-dashed border-[#C5A059] rounded-full scale-105 group-hover:rotate-180 transition-all duration-1000"></div>
                        <div class="absolute inset-0 rounded-full bg-[#C5A059]/10 blur-xl group-hover:bg-[#C5A059]/30 transition-all duration-700"></div>

                        <!-- Inner Circle Logo Container - PERFECT CIRCLE, NO SQUARE EDGES -->
                        <div class="relative w-56 h-56 lg:w-72 lg:h-72 rounded-full overflow-hidden border-4 border-[#C5A059] logo-ring-pulse shadow-[0_20px_50px_rgba(197,160,89,0.4)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.7)] group-hover:scale-105 transition-all duration-700">
                           <img src="/images/logo.jpg" 
                                alt="Logo" 
                                class="w-full h-full object-cover object-center rounded-full transition-transform duration-700 group-hover:scale-110 group-hover:rotate-2">
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <p class="text-gray-900 dark:text-[#E8D095] font-black text-xl lg:text-2xl tracking-tight leading-relaxed" style="font-family: 'Almarai', sans-serif;">
                            نظام الإدارة المتكامل لمتاجر المجوهرات
                        </p>
                        <p class="text-[#8B6914] dark:text-[#C5A059] text-xs font-black uppercase tracking-[0.4em]">
                            ELITE GOLD MANAGEMENT
                        </p>
                    </div>
                </div>
            </div>

            <!-- LEFT Column (Form Section) -->
            <div class="w-full lg:w-1/2 p-10 lg:p-20 flex flex-col justify-center order-last bg-white/40 dark:bg-transparent">
                <div class="max-w-md mx-auto w-full text-right">
                    <div class="mb-10">
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white gold-underline" style="font-family: 'Almarai', sans-serif;">
                            {{ __('استعادة الحساب') }}
                        </h2>
                        <p class="text-gray-500 dark:text-[#E8D095]/70 font-bold text-sm leading-relaxed mt-4">
                            {{ __('أدخل بريدك الإلكتروني المسجل في النظام وسنقوم بإرسال كود تحقق مكون من 6 أرقام لإعادة تعيين كلمة المرور.') }}
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-8" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-8">
                        @csrf

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-black text-gray-600 dark:text-[#E8D095] uppercase tracking-widest mr-1">
                                {{ __('البريد الإلكتروني') }}
                            </label>
                            <div class="relative group">
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       placeholder="admin@example.com"
                                       class="premium-input w-full pr-6 pl-14 text-right bg-white/80 dark:bg-[#1E1913] text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                                       autocomplete="username" />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 text-[#8B6914] dark:text-[#C5A059]">
                                    <i data-lucide="mail" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600 font-bold" />
                        </div>

                        <div class="pt-4 flex flex-col gap-4">
                            <button type="submit" class="premium-btn w-full flex items-center justify-center gap-3 group text-base uppercase tracking-wider">
                                <span>{{ __('إرسال كود التحقق') }}</span>
                                <i data-lucide="send" class="w-5 h-5 transition-transform group-hover:-translate-x-1"></i>
                            </button>

                            <a href="{{ route('login') }}" class="text-center text-xs font-bold text-gray-500 dark:text-[#E8D095]/80 hover:text-[#C5A059] transition-colors py-2">
                                &#8594; {{ __('العودة إلى تسجيل الدخول') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
