<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-md border border-gray-200 shadow p-8">
        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mt-1">
                Buat Akun ETP
            </h1>
        </div>

        {{-- Form --}}
        <form wire:submit.prevent="register" class="space-y-4" autocomplete="off">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" wire:model.defer="name" class="w-full border border-gray-400 rounded px-3 py-2 bg-white">
                @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" wire:model.defer="email" class="w-full border border-gray-400 rounded px-3 py-2 bg-white">
                @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" wire:model.defer="password" class="w-full border border-gray-400 rounded px-3 py-2 bg-white">
                @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" wire:model.defer="password_confirmation" class="w-full border border-gray-400 rounded px-3 py-2 bg-white">
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition cursor-pointer"
            >
                Daftar
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="/login" class="text-blue-600 hover:underline">
                Masuk
            </a>
        </p>
    </div>
</div>
