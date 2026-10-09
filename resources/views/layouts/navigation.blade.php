@php $haySorteoEnVivo = \App\Models\Sorteo::where('estado', 'pendiente')->whereNotNull('numeros_en_vivo')->exists(); @endphp
<nav x-data="{ open: false }" class="bg-black border-b border-dorado-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>

                    @if (auth()->user()->rol === 'jugador')
                        @php $noLeidasMovil = auth()->user()->cliente?->incidenciasNoLeidas() ?? 0; @endphp
                        <a href="{{ route('incidencias.index') }}" class="sm:hidden relative inline-flex items-center ml-3 p-1" aria-label="Notificaciones{{ $noLeidasMovil > 0 ? " ($noLeidasMovil sin leer)" : '' }}">
                            <span class="text-2xl campana-animada">🔔</span>
                            @if ($noLeidasMovil > 0)
                                <span class="absolute -top-0.5 -right-1 bg-red-600 text-white text-[10px] font-bold rounded-full min-w-[1rem] h-4 px-1 flex items-center justify-center">{{ $noLeidasMovil }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($haySorteoEnVivo && ! request()->routeIs('en-vivo'))
                        <a href="{{ route('en-vivo') }}" class="sm:hidden ml-2 inline-flex items-center gap-1 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-1 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-white"></span>EN VIVO
                        </a>
                    @endif
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('en-vivo')" :active="request()->routeIs('en-vivo')">
                        @if ($haySorteoEnVivo)
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse mr-1.5"></span>
                        @endif
                        {{ __('En vivo') }}
                    </x-nav-link>

                    @if (auth()->user()->esSuperAdmin())
                        <x-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    @endif

                    @if (auth()->user()->esAdministrador())
                        <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                            {{ __('Clientes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('sorteos.index')" :active="request()->routeIs('sorteos.*')">
                            {{ __('Sorteos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.*')">
                            {{ __('Compras / Pagos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reportes.clientes-frecuentes')" :active="request()->routeIs('reportes.clientes-frecuentes')">
                            {{ __('Clientes frecuentes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reportes.numeros-frecuentes')" :active="request()->routeIs('reportes.numeros-frecuentes')">
                            {{ __('Números frecuentes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reportes.financiero')" :active="request()->routeIs('reportes.financiero')">
                            {{ __('Reporte Financiero') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ganadores.index')" :active="request()->routeIs('ganadores.index')">
                            {{ __('Ganadores') }}
                        </x-nav-link>
                    @elseif (auth()->user()->rol === 'moderador')
                        <x-nav-link :href="route('sorteos.index')" :active="request()->routeIs('sorteos.*')">
                            {{ __('Sorteos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ganadores.index')" :active="request()->routeIs('ganadores.index')">
                            {{ __('Ganadores') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('boletos.create')" :active="request()->routeIs('boletos.create')">
                            {{ __('Jugar') }}
                        </x-nav-link>

                        @php $noLeidas = auth()->user()->cliente?->incidenciasNoLeidas() ?? 0; @endphp
                        <a href="{{ route('incidencias.index') }}" class="relative inline-flex items-center px-2">
                            <span class="text-2xl campana-animada">🔔</span>
                            @if ($noLeidas > 0)
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $noLeidas }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 sm:gap-3">
                <x-boton-instalar-app class="px-3 py-1.5 text-sm" />
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-black hover:text-dorado-400 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center gap-2 sm:hidden">
                <x-boton-instalar-app class="px-2.5 py-1.5 text-xs">Instalar</x-boton-instalar-app>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-dorado-400 hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-black border-t border-dorado-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('en-vivo')" :active="request()->routeIs('en-vivo')">
                📺 {{ __('Sorteo en vivo') }}
                @if ($haySorteoEnVivo)
                    <span class="ml-1 bg-red-600 text-white text-xs font-bold rounded-full px-2">EN VIVO</span>
                @endif
            </x-responsive-nav-link>

            @if (auth()->user()->esSuperAdmin())
                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()->esAdministrador())
                <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                    {{ __('Clientes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('sorteos.index')" :active="request()->routeIs('sorteos.*')">
                    {{ __('Sorteos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('compras.index')" :active="request()->routeIs('compras.*')">
                    {{ __('Compras / Pagos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reportes.clientes-frecuentes')" :active="request()->routeIs('reportes.clientes-frecuentes')">
                    {{ __('Clientes frecuentes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reportes.numeros-frecuentes')" :active="request()->routeIs('reportes.numeros-frecuentes')">
                    {{ __('Números frecuentes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reportes.financiero')" :active="request()->routeIs('reportes.financiero')">
                    {{ __('Reporte Financiero') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('ganadores.index')" :active="request()->routeIs('ganadores.index')">
                    {{ __('Ganadores') }}
                </x-responsive-nav-link>
            @elseif (auth()->user()->rol === 'moderador')
                <x-responsive-nav-link :href="route('sorteos.index')" :active="request()->routeIs('sorteos.*')">
                    {{ __('Sorteos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('ganadores.index')" :active="request()->routeIs('ganadores.index')">
                    {{ __('Ganadores') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('boletos.create')" :active="request()->routeIs('boletos.create')">
                    {{ __('Jugar') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
        <style>
        @keyframes mover-campana {
            0%, 100% { transform: rotate(0deg); }
            20% { transform: rotate(-15deg); }
            40% { transform: rotate(12deg); }
            60% { transform: rotate(-8deg); }
            80% { transform: rotate(4deg); }
        }
        .campana-animada {
            display: inline-block;
            animation: mover-campana 2s ease-in-out infinite;
            transform-origin: top center;
        }
    </style>
</nav>