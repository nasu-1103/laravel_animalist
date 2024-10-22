<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメ非表示設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="w-full">

                        <form action="{{ route('anime_groups.setting.add') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="mb-3 mt-2">
                                <label for="anime"
                                    class="ml-4 block text-lg font-medium text-gray-700 dark:text-white">非表示設定</label>
                                <div class="flex">
                                    <select name="anime_group_id" id="anime"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                        {{-- 各アニメグループをチェック --}}
                                        @foreach ($animeGroups as $animeGroup)
                                            {{-- アニメグループIDを設定 --}}
                                            <option value="{{ $animeGroup->id }}" @disabled(in_array($animeGroup->id, $userHiddenLists))>

                                                {{-- ユーザーの非表示jリストにアニメグループIDが含まれている場合、非表示のラベルを表示 --}}
                                                @if (in_array($animeGroup->id, $userHiddenLists))
                                                    {{ '非表示：' }}
                                                @endif
                                                {{-- アニメのタイトルを表示 --}}
                                                {{ $animeGroup->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="javascript:history.back();" class="btn btn-ghost">&lt; 戻る</a>
                                <button type="submit" class="btn btn-outline btn-info ml-2">登録</button>
                            </div>
                        </form>

                        <hr class="mt-4">

                        {{-- ユーザーが非表示にしたアニメグループがある場合のみ表示 --}}
                        @if ($userHiddenLists)
                            <form action="{{ route('anime_groups.setting.delete') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('DELETE')
                                <div class="mb-3 mt-2">
                                    <label for="anime"
                                        class="ml-4 block text-lg font-medium text-gray-700 dark:text-white">削除設定</label>
                                    <div class="flex">
                                        <select name="anime_group_id" id="anime"
                                            class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                            {{-- 各アニメグループをチェック --}}
                                            @foreach ($animeGroups as $animeGroup)
                                                {{-- 非表示リストに含まれるアニメのタイトルを表示 --}}
                                                @if (in_array($animeGroup->id, $userHiddenLists))
                                                    <option value="{{ $animeGroup->id }}">
                                                        {{-- アニメのタイトルを表示 --}}
                                                        {{ $animeGroup->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 ml-4">
                                    <a href="javascript:history.back();" class="btn btn-ghost mt-1">&lt; 戻る</a>
                                    <button type="submit" class="btn btn-outline btn-secondary ml-2 mt-1">削除</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>