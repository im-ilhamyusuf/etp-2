<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-md border border-gray-200 shadow p-8">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mt-1">
                Masuk ETP
            </h1>
        </div>

        {{-- Form --}}
        <form wire:submit.prevent="login" class="space-y-5" autocomplete="off">

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input
                    type="email"
                    wire:model.defer="email"
                    autocomplete="email"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                >
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    type="password"
                    wire:model.defer="password"
                    autocomplete="current-password"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                >
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition cursor-pointer"
            >
                Masuk
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="/register" class="text-blue-600 hover:underline">
                Daftar
            </a>
        </p>
    </div>
</div>
