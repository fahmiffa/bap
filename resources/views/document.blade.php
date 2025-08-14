<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="col-span-1">
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
                                    <input type="file" name="pdf"  accept="application/pdf"
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
                                                    <textarea type="text" x-model="row.note"
                                                        :name="`note[${index}]`"
                                                        class="mt-1 w-full rounded-2xl border px-3 py-2"
                                                        placeholder="Catatan"></textarea>
                                                </div>

                                                <!-- Email -->
                                                <div class="flex-1">
                                                    <input type="text" x-model="row.name"
                                                        :name="`name[${index}]`"
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
                        <div class="col-span-2"></div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
