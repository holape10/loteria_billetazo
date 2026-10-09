{{-- Oculto hasta que el navegador permita instalar la app (lo muestra public/js/pwa.js) --}}
<button type="button" data-instalar-app {{ $attributes->merge(['class' => 'hidden inline-flex items-center justify-center gap-2 font-semibold rounded-lg bg-dorado-500 hover:bg-dorado-600 text-black transition']) }}>
    <span aria-hidden="true">📲</span>
    <span>{{ $slot->isEmpty() ? 'Instalar app' : $slot }}</span>
</button>
