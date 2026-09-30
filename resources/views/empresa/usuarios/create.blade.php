<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-dorado-400 leading-tight">Nuevo usuario</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 text-white shadow-sm sm:rounded-lg p-6 border border-dorado-700">
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Correo</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        @error('email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Rol</label>
                        <select name="rol" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                            <option value="administrador">Administrador</option>
                            <option value="moderador">Moderador</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Contraseña</label>
                        <input type="password" name="password" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                        @error('password') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full bg-gray-800 border-gray-700 text-white rounded-md">
                    </div>

                    <button type="submit" class="px-4 py-2 bg-dorado-500 hover:bg-dorado-600 text-black font-semibold rounded">Crear usuario</button>
                    <a href="{{ route('usuarios.index') }}" class="ml-2 text-gray-400">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>