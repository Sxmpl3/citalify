<x-guest-layout>
    <div class="flex flex-col items-center bg-white px-4">
        <div class="w-full sm:max-w-md text-center px-6 py-4">
            <h2 class="text-xl font-bold text-gray-800 mb-1">
                Negocio no disponible
            </h2>
            
            <p class="text-sm text-gray-500 font-medium mb-6">
                {{ $business->business_name }}
            </p>

            <p class="text-gray-600 text-sm mb-10 leading-relaxed">
                Este negocio se encuentra inactivo actualmente. <br class="hidden sm:block">
                Por favor, inténtalo más tarde.
            </p>

            <div class="flex justify-center">
                <a href="/" class="text-sm font-bold text-indigo-600 hover:text-indigo-500 transition-colors underline decoration-2 underline-offset-4">
                    Volver a Citalify
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
