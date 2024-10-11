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

                        {{-- エラーメッセージを表示 --}}
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <a href="{{ route('anime_groups.setting') }}" class="underline text-blue-500 mt-1">非表示設定</a>

                        <form action="{{ route('watch_list.create') }}" method="GET" class="space-y-4">
                            @csrf
                            <label for="anime"
                                class="block mb-2 text-lg font-medium text-gray-700 dark:text-white">アニメを選択してください。</label>
                            <div class="relative">
                                <select name="animeGroupId" id="anime"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                    @foreach ($animeGroups as $animeGroup)
                                        @unless (in_array($animeGroup->id, $userHiddenLists))
                                            <option value="{{ $animeGroup->id }}" @selected($animeGroup->id == old('animeGroupId'))>
                                                {{ $animeGroup->name }}
                                            </option>
                                        @endunless
                                    @endforeach
                                </select>
                            </div>
                            <a href="{{ route('watch_list.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                            <button type="submit" class="btn btn-outline btn-info">選択</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
