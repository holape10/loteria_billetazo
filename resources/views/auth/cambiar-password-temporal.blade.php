<x-guest-layout>
    <h1 class="text-lg font-bold text-gray-900 mb-1">🔒 Crea tu nueva contraseña</h1>
    <p class="mb-4 text-sm text-gray-600">
        Ingresaste con una contraseña temporal. Por tu seguridad, crea ahora una contraseña propia de al menos 8 caracteres.
    </p>

    <form method="POST" action="{{ route('password.temporal.update') }}" x-data="{ mostrar: false }">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="password" value="Nueva contraseña" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" x-bind:type="mostrar ? 'text' : 'password'" name="password" required autofocus autocomplete="new-password" />
                <button type="button" @click="mostrar = ! mostrar" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700" aria-label="Mostrar u ocultar contraseña">
                    <span x-show="! mostrar">👁️</span>
                    <span x-show="mostrar" style="display: none;">🙈</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Repite la nueva contraseña" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" x-bind:type="mostrar ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-between mt-4 gap-3">
            <button type="submit" form="form-salir" class="underline text-sm text-gray-600 hover:text-gray-900">Cerrar sesión</button>
            <x-primary-button>Guardar contraseña</x-primary-button>
        </div>
    </form>

    <form id="form-salir" method="POST" action="{{ route('logout') }}">
        @csrf
    </form>
</x-guest-layout>
