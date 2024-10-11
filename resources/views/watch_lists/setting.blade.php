<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ユーザー非表示設定
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

                        <form action="{{ route('anime_groups.setting.add') }}" method="POST" class="space-y-4">
                            @csrf
                            <label for="anime"
                                class="block mb-2 text-lg font-medium text-gray-700 dark:text-white">非表示設定</label>
                            <div class="relative">
                                <select name="anime_group_id" id="anime"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                    @foreach ($animeGroups as $animeGroup)
                                        <option value="{{ $animeGroup->id }}" @disabled(in_array($animeGroup->id, $userHiddenLists))>

                                            @if (in_array($animeGroup->id, $userHiddenLists))
                                                {{ '非表示：' }}
                                            @endif
                                            {{ $animeGroup->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="javascript:history.back();" class="btn btn-ghost">&lt; 戻る</a>
                            <button type="submit" class="btn btn-outline btn-info">登録</button>
                        </form>
                        
                        <hr class="mt-4">

                        {{-- ユーザーが非表示にしたアニメグループがある場合のみ表示 --}}
                        @if ($userHiddenLists)
                            <form action="{{ route('anime_groups.setting.delete') }}" method="POST" class="space-y-4">
                                @csrf
                                @method('DELETE')
                                <label for="anime"
                                    class="block mb-2 text-lg font-medium text-gray-700 dark:text-white">削除設定</label>
                                <div class="relative">
                                    <select name="anime_group_id" id="anime"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                        @foreach ($animeGroups as $animeGroup)
                                        {{-- 非表示リストに含まれるアニメグループを表示 --}}
                                            @if (in_array($animeGroup->id, $userHiddenLists))
                                                <option value="{{ $animeGroup->id }}">
                                                    {{ $animeGroup->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <a href="javascript:history.back();" class="btn btn-ghost mt-4">&lt; 戻る</a>
                                    <button type="submit" class="btn btn-outline btn-secondary">削除</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
