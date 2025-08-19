<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 w-100">
                    <div class="grid md:grid-cols-2 grid-cols-1 gap-2">
                        <div class="col-span-1 p-5">
                            <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input class="block w-full" type="text" name="doc" :value="old('doc')"
                                        required autofocus />
                                    <x-input-error :messages="$errors->get('doc')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="pdf" :value="__('PDF')" />
                                    <input type="file" name="pdf" accept="application/pdf"
                                        class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0
                                        file:text-sm file:font-semibold file:bg-blue-50 file:text-gray-700 
                                        hover:file:bg-blue-100 cursor-pointer ring-2 border-gray-500 focus:outline-none focus:border-gray-500 focus:ring-gray-500 shadow-sm block mt-1 w-full rounded-2xl"
                                        required>
                                </div>
                                <div class="mb-4" x-data="userForm()">
                                    <div class="space-y-4">
                                        <template x-for="(row, index) in rows" :key="index">
                                            <div class="flex gap-3 items-start">
                                                <!-- Name -->
                                                <div class="flex-1">
                                                    <textarea type="text" x-model="row.note" :name="`note[${index}]`" class="mt-1 w-full rounded-2xl border px-3 py-2"
                                                        placeholder="Catatan"></textarea>
                                                </div>

                                                <!-- Email -->
                                                <div class="flex-1">
                                                    <input type="text" x-model="row.name" :name="`name[${index}]`"
                                                        class="mt-1 w-full rounded-2xl border px-3 py-2"
                                                        placeholder="Nama">
                                                </div>

                                                <!-- Remove Button -->
                                                <button type="button" @click="removeRow(index)"
                                                    class="p-2  text-red-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-user-round-x-icon lucide-user-round-x">
                                                        <path d="M2 21a8 8 0 0 1 11.873-7" />
                                                        <circle cx="10" cy="8" r="5" />
                                                        <path d="m17 17 5 5" />
                                                        <path d="m22 17-5 5" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>

                                        <div>
                                            <button type="button" @click="addRow()"
                                                class="p-2 text-sm  text-gray-800 ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-user-round-plus-icon lucide-user-round-plus">
                                                    <path d="M2 21a8 8 0 0 1 13.292-6" />
                                                    <circle cx="10" cy="8" r="5" />
                                                    <path d="M19 16v6" />
                                                    <path d="M22 19h-6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <button type="submit"
                                        class="cursor-pointer  bg-gray-500 text-sm hover:bg-gray-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-span-1 p-5">
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-500 text-left text-white">
                                            <th class="px-4 py-2">No</th>
                                            <th class="px-4 py-2">Name</th>
                                            <th class="px-4 py-2">Users</th>
                                            <th class="px-4 py-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($doc as $row)
                                            <tr class="border-t border-gray-300">
                                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-2">{{ $row->name }}</td>
                                                <td class="px-4 py-2">
                                                    <ul
                                                        class="list-disc list-inside space-y-2 text-gray-700 text-nowrap">
                                                        @foreach ($row->users as $item)
                                                            <li>{{ $item->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <div x-data="{ open: false }">
                                                        <div class="flex items-center gap-2">
                                                            <a href="{{ route('document.preview', ['id' => md5($row->id)]) }}"
                                                                target="_blank">
                                                                <div class="text-red-800 size-5">
                                                                    <!-- icon -->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="lucide lucide-file">
                                                                        <path
                                                                            d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                            <a href="#modal-{{ $row->id }}"
                                                                @click.prevent="open = true"
                                                                class="block text-blue-600 underline hover:text-blue-800">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="lucide lucide-link-icon lucide-link">
                                                                    <path
                                                                        d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                                                    <path
                                                                        d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                                                </svg>
                                                            </a>
                                                        </div>

                                                        <!-- Modal -->
                                                        <div x-cloak x-show="open" id="modal-{{ $row->id }}"
                                                            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                                                            x-transition @click.self="open = false"
                                                            @keydown.escape.window="open = false">
                                                            @if ($row->link)
                                                                <div
                                                                    class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                                                    <h2 class="text-lg font-semibold mb-4">
                                                                        Link
                                                                        Dokumen
                                                                        {{ $row->name }}</h2>
                                                                    <div x-data>
                                                                        <textarea readonly class="w-full rounded-2xl border px-2 py-1 mb-2 cursor-pointer" x-ref="link"
                                                                            @click="
                                                                            $refs.link.select();
                                                                            document.execCommand('copy');
                                                                            $dispatch('notify', { text: 'Link disalin!' });
                                                                        ">{{ route('link.show', ['id' => md5($row->link)]) }}</textarea>
                                                                    </div>

                                                                    <div class="flex justify-start space-x-2">
                                                                        <button type="button" @click="open = false"
                                                                            class="px-4 py-2 bg-gray-300 rounded-xl">Tutup</button>
                                                                        <a href="{{ route('link.show', ['id' => md5($row->link)]) }}"
                                                                            target="_blank"
                                                                            class="px-4 py-2 bg-gray-800 text-white rounded-xl">
                                                                            Buka
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                                                    <h2 class="text-lg font-semibold mb-4">Generate
                                                                        Link
                                                                        Dokumen
                                                                        {{ $row->name }}</h2>
                                                                    <form method="POST"
                                                                        action="{{ route('link.store', ['id' => $row->id]) }}">
                                                                        @csrf

                                                                        <div class="flex justify-start space-x-2">
                                                                            <button type="button"
                                                                                @click="open = false"
                                                                                class="px-4 py-2 bg-gray-300 rounded-xl">Batal</button>
                                                                            <button type="submit"
                                                                                class="px-4 py-2 bg-gray-800 text-white rounded-xl">Generate</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
