<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center px-6">
                <div class="my-3 font-semibold text-xl">{{ $action }}</div>
                <a href="{{ route('document.create') }}"
                    class="cursor-pointer my-3 bg-gray-500 text-sm hover:bg-gray-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                    Tambah
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 w-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-500 text-left text-white">
                                    <th class="px-4 py-2">No</th>
                                    <th class="px-4 py-2">Nomor</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Peserta</th>
                                    <th class="px-4 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($doc as $row)
                                    <tr class="border-t border-gray-300">
                                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $row->nomor }}</td>
                                        <td class="px-4 py-2">{{ $row->tanggal }}</td>
                                        <td class="px-4 py-2">
                                            <div class="flex items-start gap-2">
                                                <ul class="list-disc list-inside space-y-2 text-gray-700 text-nowrap">
                                                    @foreach ($row->users as $item)
                                                        <li>{{ $item->name }}</li>
                                                    @endforeach
                                                </ul>

                                                <ul class="list-disc list-inside space-y-2 text-gray-700 text-nowrap">
                                                    @foreach ($row->paraf as $item)
                                                        <li>{{ $item->name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div x-data="{ open: false }">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('document.preview', ['id' => md5($row->id)]) }}"
                                                        target="_blank">
                                                        <div class="text-red-800 size-5">
                                                            <!-- icon -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-file">
                                                                <path
                                                                    d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                                            </svg>
                                                        </div>
                                                    </a>

                                                    <a href="{{ route('document.edit', ['id' => md5($row->id)]) }}">
                                                        <div class="text-blue-800 size-5">
                                                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                                        </div>
                                                    </a>

                                                    <a href="#modal-{{ $row->id }}" @click.prevent="open = true"
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
                                                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
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
                                                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                                                            <h2 class="text-lg font-semibold mb-4">Generate
                                                                Link
                                                                Dokumen
                                                                {{ $row->name }}</h2>
                                                            <form method="POST"
                                                                action="{{ route('link.store', ['id' => $row->id]) }}">
                                                                @csrf

                                                                <div class="flex justify-start space-x-2">
                                                                    <button type="button" @click="open = false"
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
</x-app-layout>
