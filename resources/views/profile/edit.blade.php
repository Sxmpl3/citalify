<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-bold text-xl text-slate-800 leading-tight">
            Perfil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-bold text-slate-800">
                                Gestión de Suscripción
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Administra tu suscripción actual con Citalify.
                            </p>
                        </header>

                        @if(auth()->user()->plan_id)
                            <div class="mt-6 space-y-4">
                                <p class="text-sm text-slate-600">
                                    Tienes un plan activo. Si cancelas, tu agenda dejará de ser visible para los clientes inmediatamente.
                                </p>
                                <form method="post" action="{{ route('subscription.cancel') }}">
                                    @csrf
                                    <x-danger-button onclick="return confirm('¿Estás seguro de que deseas cancelar tu plan? Tu negocio quedará inactivo.')">
                                        Cancelar Plan Actual
                                    </x-danger-button>
                                </form>
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <p class="text-sm text-slate-600">
                                    No tienes ningún plan activo. Tu negocio no es visible para los clientes.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('checkout.redirect') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Contratar un Plan
                                    </a>
                                </div>
                            </div>
                        @endif
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
