<x-guest-layout>
    <h1 class="text-lg font-bold text-gray-900 mb-1">🔑 Recuperar contraseña</h1>
    <p class="mb-4 text-sm text-gray-600">
        Escribe tu <strong>correo</strong> o tu <strong>DNI</strong> y te enviaremos a tu correo un enlace para crear una contraseña nueva.
    </p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-300 text-green-800 text-sm">
            ✅ {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="identificador" value="Correo o DNI" />
            <x-text-input id="identificador" class="block mt-1 w-full" type="text" name="identificador"
                          :value="old('identificador')" required autofocus autocomplete="username"
                          placeholder="ejemplo@gmail.com o 12345678" />
            <x-input-error :messages="$errors->get('identificador')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-3">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">← Volver a ingresar</a>
            <x-primary-button>Enviar enlace</x-primary-button>
        </div>
    </form>

    @if ($whatsappSoporte)
        <div class="mt-6 pt-4 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600 mb-3">¿Ya no usas ese correo o no te llega el mensaje?</p>
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappSoporte) }}?text={{ rawurlencode('Hola, olvidé mi contraseña de El Billetazo y necesito ayuda para recuperar mi cuenta.') }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold">
                💬 Pedir ayuda por WhatsApp
            </a>
            <p class="text-xs text-gray-500 mt-2">Te pediremos tu DNI para confirmar que la cuenta es tuya.</p>
        </div>
    @endif
</x-guest-layout>
