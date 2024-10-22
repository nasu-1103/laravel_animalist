<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-x1 text-gray-800 leading-tight">
            編集フォーム
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="w-full">

                        {{-- バリデーションエラーがあれば、エラーメッセージを表示 --}}
                        @if ($errors->any())
                            <ul class="ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form action="{{ route('watch_list.update', $watch_list) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3 mt-2">
                                <label for="anime"
                                    class="ml-4 block text-lg font-medium text-gray-700 dark:text-white">アニメを選択してください。</label>
                                <div class="flex">
                                    <select name="anime_id" id="anime"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                        {{-- 全てのアニメのリストを表示 --}}
                                        @foreach ($animes as $anime)
                                            <option value="{{ $anime->id }}" @selected($anime->id == $watch_list->anime_id)>
                                                {{-- アニメのタイトルとサブタイトルを表示 --}}
                                                {{ $anime->title . ' ： ' . $anime->sub_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="{{ route('watch_list.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                <button type="submit" class="btn btn-outline btn-info ml-2">更新</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>