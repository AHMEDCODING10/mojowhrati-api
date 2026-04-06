<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - MOJAWHARATI PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Almarai', sans-serif; background: #fff; color: #151515; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { margin: 1cm; size: A4; }
            .page-break { page-break-before: always; }
        }
        .gold-border { border-color: #C5A059; }
        .gold-bg { background-color: #C5A059; }
        .gold-text { color: #C5A059; }
    </style>
</head>
<body class="p-8 max-w-5xl mx-auto bg-white min-h-screen relative">
    
    <!-- Watermark Logo -->
    <div class="fixed inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none z-0">
        <img src="/images/logo.jpg" class="w-[600px] h-[600px] object-contain drop-shadow-2xl mix-blend-multiply grayscale">
    </div>

    <!-- Print Action (hidden when printing) -->
    <div class="no-print flex justify-end mb-8 gap-4 relative z-10">
        <button onclick="window.close()" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-bold hover:bg-gray-50">إغلاق</button>
        <button onclick="window.print()" class="px-6 py-2 bg-[#C5A059] text-white rounded-lg text-sm font-bold hover:bg-[#B88F3D] shadow-lg flex items-center gap-2 animate-pulse">
            <i data-lucide="printer" class="w-4 h-4"></i>
            طباعة / حفظ PDF
        </button>
    </div>

    <div class="relative z-10">
        <!-- Official Header -->
        <header class="flex items-start justify-between border-b-[3px] gold-border pb-6 mb-8">
            <div class="flex items-center gap-6">
                <img src="/images/logo.jpg" alt="Logo" class="w-24 h-24 object-cover rounded-full border-4 gold-border shadow-md bg-white">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter" style="font-family: 'Almarai', sans-serif;">
                        MOJAWHARATI <span class="text-[#C5A059]">PRO</span>
                    </h1>
                    <p class="text-[11px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ __('منصة متقدمة لتجارة الذهب والمجوهرات') }}</p>
                    <div class="mt-4 inline-block px-5 py-2 bg-[#C5A059]/10 gold-text font-black text-sm rounded-lg border border-[#C5A059]/30">
                        {{ $title }}
                    </div>
                </div>
            </div>
            <div class="text-left text-xs space-y-2.5 font-bold">
                <div class="flex items-center justify-end gap-2 text-gray-500">
                    <span>{{ now()->format('Y-m-d h:i A') }}</span>
                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-600 font-black tracking-widest text-[9px]">تاريخ الإصدار</span>
                </div>
                <div class="flex items-center justify-end gap-2 text-gray-500">
                    <span class="uppercase">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-600 font-black tracking-widest text-[9px]">مسؤول التصدير</span>
                </div>
                @if($from || $to)
                <div class="flex items-center justify-end gap-2 text-gray-500">
                    <span dir="ltr">
                        {{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : 'البداية' }} 
                        &rarr; 
                        {{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d') : 'الآن' }}
                    </span>
                    <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-600 font-black tracking-widest text-[9px]">نطاق البيانات</span>
                </div>
                @endif
            </div>
        </header>

        <!-- Content -->
        <main class="min-h-[500px]">
            @if(count($data) === 0)
                <div class="p-12 mt-10 text-center text-gray-400 font-black text-lg border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center gap-4">
                    <i data-lucide="folder-search-2" class="w-12 h-12 text-gray-300"></i>
                    لا توجد بيانات متاحة لفترة البحث المحددة في هذا التقرير.
                </div>
            @else
                @if($format === 'summary')
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($data as $stat)
                            <div class="p-6 border border-[#C5A059]/20 rounded-2xl bg-gradient-to-br from-gray-50 to-white shadow-sm relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 gold-bg"></div>
                                <div class="absolute -right-4 -bottom-4 opacity-5 text-[#C5A059]">
                                    <i data-lucide="bar-chart-3" class="w-24 h-24"></i>
                                </div>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">{{ $stat['label'] }}</p>
                                <p class="text-4xl font-black text-[#151515]">{{ $stat['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Detailed Table -->
                    <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white">
                        <table class="w-full text-right text-sm">
                            <thead class="bg-gray-50/80 border-b-2 gold-border">
                                <tr>
                                    @if($source === 'users')
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">الاسم</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">الرقم/البريد</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider text-center">الدور</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">تاريخ الانضمام</th>
                                    @elseif($source === 'merchants')
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">اسم المتجر</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">الهاتف</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider text-center">الحالة</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">تاريخ الانضمام</th>
                                    @elseif($source === 'products')
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">المنتج</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">التاجر</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider" dir="ltr">العيار والوزن</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">السعر</th>
                                    @elseif($source === 'bookings')
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">رقم الطلب</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">التفاصيل</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">القيمة</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider text-center">الحالة</th>
                                    @elseif($source === 'banners')
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">موقع الإعلان</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider">الرابط الموجه</th>
                                        <th class="px-6 py-4 font-black text-gray-700 tracking-wider text-center">الحالة</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($data as $index => $item)
                                    <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/40' }}">
                                        @if($source === 'users')
                                            <td class="px-6 py-3.5 font-bold">{{ $item->name ?? ($item->first_name . ' ' . $item->last_name) }}</td>
                                            <td class="px-6 py-3.5 text-xs text-gray-500 font-bold" dir="ltr">{{ $item->phone ?? $item->email }}</td>
                                            <td class="px-6 py-3.5 text-center"><span class="px-3 py-1 bg-gray-100 rounded-md text-xs font-black uppercase tracking-wider">{{ $item->role }}</span></td>
                                            <td class="px-6 py-3.5 text-gray-500 font-bold text-xs">{{ $item->created_at->format('Y-m-d') }}</td>
                                        @elseif($source === 'merchants')
                                            <td class="px-6 py-3.5 font-bold">{{ $item->store_name }}</td>
                                            <td class="px-6 py-3.5 text-xs text-gray-500 font-bold" dir="ltr">{{ $item->phone }}</td>
                                            <td class="px-6 py-3.5 text-center"><span class="px-3 py-1 bg-gray-100 rounded-md text-xs font-black uppercase tracking-wider">{{ $item->status }}</span></td>
                                            <td class="px-6 py-3.5 text-gray-500 font-bold text-xs">{{ $item->created_at->format('Y-m-d') }}</td>
                                        @elseif($source === 'products')
                                            <td class="px-6 py-3.5 font-bold max-w-xs pr-4">{{ $item->title }}</td>
                                            <td class="px-6 py-3.5 text-gray-500 font-bold text-xs">{{ $item->merchant->store_name ?? 'إدارة المنصة' }}</td>
                                            <td class="px-6 py-3.5 font-black text-gray-600" dir="ltr">{{ $item->gold_karat }}K | {{ $item->weight }}g</td>
                                            <td class="px-6 py-3.5 font-black text-[#C5A059]">${{ number_format($item->price, 2) }}</td>
                                        @elseif($source === 'bookings')
                                            <td class="px-6 py-3.5 font-black text-gray-500">#{{ $item->id }}</td>
                                            <td class="px-6 py-3.5"><div class="text-xs font-bold text-gray-800">{{ $item->merchant->store_name ?? '-' }}</div><div class="text-[10px] text-gray-500 font-black mt-1">العميل: {{ $item->user->name ?? '-' }}</div></td>
                                            <td class="px-6 py-3.5 font-black text-[#151515]">${{ number_format($item->total_price, 2) }}</td>
                                            <td class="px-6 py-3.5 text-center"><span class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-md text-[10px] font-black uppercase tracking-wider">{{ $item->status }}</span></td>
                                        @elseif($source === 'banners')
                                            <td class="px-6 py-3.5 font-black text-xs uppercase tracking-wider text-gray-600">{{ $item->position }}</td>
                                            <td class="px-6 py-3.5 text-[10px] text-blue-500 font-bold truncate max-w-[200px]" dir="ltr">{{ $item->link }}</td>
                                            <td class="px-6 py-3.5 text-center"><span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $item->is_active ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-red-50 text-red-600 border border-red-200' }}">{{ $item->is_active ? 'نشط' : 'متوقف' }}</span></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </main>

        <!-- Official Footer -->
        <footer class="mt-16 pt-6 border-t-[2px] gold-border text-xs text-gray-500 font-black flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#C5A059]"></i>
                <p>MOJAWHARATI PRO - وثيقة رسمية معتمدة ومصدرة آليا من النظام</p>
            </div>
            <p class="px-3 py-1 bg-gray-100 rounded-md border border-gray-200">الصفحة 1</p>
        </footer>
    </div>

    <script>
        lucide.createIcons();
        window.onload = function() {
            // Automatically launch print dialog after images and fonts load
            setTimeout(() => {
                window.print();
            }, 800);
        };
    </script>
</body>
</html>
