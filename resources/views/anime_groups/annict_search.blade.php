<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメグループ登録（Annict）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mx-auto max-w-2x1 text-senter">

                        {{-- バリデーションエラーがあれば、エラーメッセージを表示 --}}
                        @if ($errors->any())
                            <ul class="ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form action="{{ route('anime_groups.annict_list') }}" method="GET" class="space-y-4">
                            <div class="mb-3 mt-2">
                                <label for="search_word"
                                    class="block text-lg font-medium text-gray-700 ml-4">アニメのタイトル名を入力してください。</label>
                                <div class="flex">
                                    <input type="text" name="search_word"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:boder-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        value="{{ old('search_word') }}">
                                    <input type="number" name="page"
                                        class="ml-4 mt-2 block w-12 rounded-md border-gray-300 shadow-sm focus:boder-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        value="{{ old('page', 1) }}">
                                    <button type="submit" class="btn btn-outline btn-info ml-4 mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path fill-rule="evenodd"
                                                d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3 ml-4">
                                <a href="{{ route('anime_groups.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>