<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-10 py-3.5 bg-gradient-to-r from-gold to-yellow-600 hover:to-yellow-700 text-white font-black text-sm rounded-xl shadow-lg shadow-gold/20 hover:shadow-xl hover:shadow-gold/30 hover:-translate-y-0.5 transition-all duration-300 active:translate-y-0 active:scale-95 uppercase tracking-widest']) }}>
    {{ $slot }}
</button>
