<x-guest-layout>
    <div class="flex flex-col items-center justify-center w-full px-4">
        <button @click="open = true" x-data x-on:click="$dispatch('open-signature')"
            class="px-4 py-2 bg-gray-600 text-white rounded-xl my-3 self-center max-w-5xl mx-auto w-25">
            Buka
        </button>
        <div class="p-8 w-full">
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}"
                class="w-[700px] max-w-5xl h-[600px] mx-auto" frameborder="0"></iframe>
        </div>
    </div>


    <div x-data="ttd({{ Js::from(md5($doc->link)) }})" x-init="init()" x-on:open-signature.window="open = true">
        <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
            @click.self="close()">

            <div class="bg-white rounded-lg p-4 w-[95vw] max-w-2xl">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-semibold">Tanda Tangan</h3>
                    <button @click="close()" class="text-gray-500">✕</button>
                </div>

                <div class="mb-4">
                    <select class="block w-full rounded-xl" name="user" x-model="user">
                        <option>Pilih User</option>
                        @foreach ($doc->users as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Canvas -->
                <div class="border rounded">
                    <canvas x-ref="canvas" class="w-full h-48 touch-none"></canvas>
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
</x-guest-layout>
