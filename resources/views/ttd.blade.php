<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Preview Word</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="flex flex-col w-full min-h-screen position-relative">
        <button x-data @click="$dispatch('open-signature')"
            class="position-absolute top-100 mx-auto py-3 px-4 bg-gray-800 text-white font-semibold rounded-xl shadow-lg hover:bg-gray-700">
            Tanda Tangan
        </button>

        <div x-data="ttd({{ Js::from(md5($doc->link)) }})" x-init="init()" x-on:open-signature.window="open = true">
            <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                @click.self="close()">

                <div class="bg-white rounded-lg p-5 w-full max-w-md mx-auto">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold">Tanda Tangan</h3>
                        <button @click="close()" class="text-gray-500">✕</button>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="kode" :value="__('kode')" />
                        <x-text-input class="block w-48" type="number" x-model="kode" name="kode" :value="old('kode')"
                            required autofocus />
                        <template x-if="errors.kode">
                            <ul class="text-sm text-red-600 space-y-1 mt-2">
                                <template x-for="message in errors.kode" :key="message">
                                    <li x-text="message"></li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <!-- Canvas -->
                    {{-- <div class="border-2 border-dashed border-gray-500 w-48">
                        <canvas x-ref="canvas" class="w-48 h-48 touch-none"></canvas>
                    </div> --}}

                    <div class="border-2 border-dashed border-gray-500 w-full h-56 sm:w-48 sm:h-48 mx-auto">
                        <canvas x-ref="canvas" class="w-full h-full touch-none"></canvas>
                    </div>


                    <!-- Tombol -->
                    <div class="mt-2 flex gap-2">
                        <button @click="clear()" class="px-3 py-2 bg-gray-200 rounded">Bersihkan</button>
                        <button @click="undo()" class="px-3 py-2 bg-gray-200 rounded">Undo</button>
                        <div class="flex-1"></div>
                        <button :class="isEmpty ? 'opacity-50 cursor-not-allowed' : ''" @click="save()"
                            class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}"
            frameborder="0" height="1000px">
        </iframe>

    </div>
</body>

</html>
