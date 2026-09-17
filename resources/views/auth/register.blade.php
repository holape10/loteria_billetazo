<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- DNI -->
        <div>
            <x-input-label for="dni" :value="__('DNI')" />
            <x-text-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" required maxlength="8" autofocus />
            <p id="dni-mensaje" class="text-sm mt-1"></p>
            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
        </div>

        <!-- Nombre -->
        <div class="mt-4">
            <x-input-label for="nombre" :value="__('Nombre completo')" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- Celular / Yape -->
        <div class="mt-4">
            <x-input-label for="celular" :value="__('Celular (Yape/Plin)')" />
            <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" required maxlength="15" />
            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Registrarme') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.getElementById('dni').addEventListener('blur', function () {
            const dni = this.value.trim();
            const mensaje = document.getElementById('dni-mensaje');
            const nombreInput = document.getElementById('nombre');

            mensaje.textContent = '';

            if (dni.length !== 8) {
                return;
            }

            mensaje.textContent = 'Buscando DNI...';

            fetch('{{ route('dni.consultar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ dni: dni }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.encontrado) {
                        nombreInput.value = data.nombre_completo;
                        mensaje.textContent = 'Nombre encontrado ✓';
                        mensaje.classList.add('text-green-600');
                    } else {
                        mensaje.textContent = 'No se encontró el DNI, ingresa tu nombre manualmente.';
                        mensaje.classList.add('text-yellow-600');
                    }
                })
                .catch(() => {
                    mensaje.textContent = 'No se pudo consultar el DNI, ingresa tu nombre manualmente.';
                });
        });
    </script>
</x-guest-layout>