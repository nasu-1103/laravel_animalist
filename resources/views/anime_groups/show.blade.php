<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメグループ詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mx-auto max-w-2x1 text-senter">

                        {{-- フラッシュメッセージを表示 --}}
                        @session('flash_message')
                            {{ session('flash_message') }}
                        @endsession

                        <form action="{{ route('anime_groups.update', $animeGroup) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3 mt-2">
                                <label for="name" class="ml-4 block text-lg font-medium text-gray-700">タイトル</label>
                                <div class="flex">
                                    <input type="text" name="name" value="{{ old('name', $animeGroup->name) }}"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        disabled>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <div class="flex">
                                    <details class="dropdown">
                                        <summary class="btn btn-outline btn-info ml-4">エピソード</summary>
                                        <ul
                                            class="menu droupdown-content bg-base-100 text-lg text-gray-700 ml-4 p-2 rounded-box z-[1] w-full shadow">
                                            {{-- エピソードデータからエピソード番号とサブタイトルをリストで表示 --}}
                                            @foreach ($episodes as $episode)
                                                <li value="{{ $episode['number'] . ',' . $episode['title'] }}">
                                                    {{ $episode['number'] . '：' . $episode['title'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <div class="mb-3 ml-4">
                                        <a href="{{ route('anime_groups.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>