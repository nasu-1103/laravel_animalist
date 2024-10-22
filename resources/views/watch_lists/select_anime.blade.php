<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメ選択
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="w-full">

                        <a href="{{ route('anime_groups.setting') }}" class="ml-4 underline text-blue-500">非表示設定</a>

                        <form action="{{ route('watch_list.create') }}" method="GET" class="space-y-4">
                            @csrf
                            <div class="mb-3 mt-2">
                                <label for="anime"
                                    class="ml-4 block text-lg font-medium text-gray-700 dark:text-white">アニメを選択してください。</label>
                                <div class="flex">
                                    <select name="animeGroupId" id="anime"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                        {{-- 各アニメグループをチェック --}}
                                        @foreach ($animeGroups as $animeGroup)
                                            {{-- 非表示リストにない場合、アニメグループをリストに追加 --}}
                                            @unless (in_array($animeGroup->id, $userHiddenLists))
                                                <option value="{{ $animeGroup->id }}" @selected($animeGroup->id == old('animeGroupId'))>
                                                    {{-- アニメのタイトルを表示 --}}
                                                    {{ $animeGroup->name }}
                                                </option>
                                            @endunless
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="{{ route('watch_list.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                <button type="submit" class="btn btn-outline btn-info ml-2">選択</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>