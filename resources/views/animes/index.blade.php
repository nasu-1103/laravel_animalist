<x-app-layout>
    <x-slot name="header">
        <form action="{{ route('animes.index') }}" method="GET" class="space-y-4">
            <div class="col-auto flex">
                <label for="keyword" class="font-semibold text-xl my-auto text-gray-800">アニメ</label>
                <input type="text" name="keyword"
                    class="ml-4 mt-1 block w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                    value="{{ request()->keyword }}">
                <button type="submit" class="btn btn-outline btn-info ml-3 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                            d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="mr-1 mt-3 mb-4">
                    <a href="{{ route('animes.create') }}" class="btn btn-link text-lg ml-3 mr-3">アニメ新規登録</a>
                    <a href="{{ route('animes.annict_search') }}" class="btn btn-link text-lg ml-3 mr-3">アニメ新規登録（Annict）</a>

                    {{-- フラッシュメッセージを表示 --}}
                    @session('flash_message')
                        {{ session('flash_message') }}
                    @endsession

                    <div class="p-6 text-gray-900 text-center">
                        {{-- エラーメッセージの表示 --}}
                        @session('error_message')
                            {{ session('error_message') }}
                        @endsession

                        {{-- アニメリストがある場合、テーブルを表示する --}}
                        @if ($animes->isNotEmpty())
                            <div
                                class="relative overflow-x-auto shadow-sm sm:rounded-lg text-gray-300 active:text-gray-200">
                                <table class="w-full text-left text-gray-700 text-nowrap">
                                    <thead class="text-lg text-gray-700 bg-gray-50">
                                        <tr>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center">ID</th>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center min-w-60">タイトル</th>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center">エピソード</th>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center min-w-50">サブタイトル</th>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center min-w-60">エディット</th>
                                            <th scope="col" class="border-slate-300 px-6 py-3 text-center">更新日</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- アニメごとの情報表示するセルを作成 --}}
                                        @foreach ($animes as $anime)
                                            <tr class="bg-white">
                                                <td class="border border-slate-300 px-6 py-4 text-center">{{ $anime->id }}</td>
                                                <td class="border border-slate-300 px-6 py-4 text-center">{{ $anime->title }}</td>
                                                <td class="border border-slate-300 px-6 py-4 text-center">{{ $anime->episode }}</td>
                                                <td class="border border-slate-300 px-6 py-4 text-center">{{ $anime->sub_title }}</td>
                                                <td class="flex border border-slate-300 px-6 py-4 justify-center gap-4">
                                                    <a href="{{ route('animes.show', $anime) }}" class="btn btn-outline btn-primary">詳細</a>
                                                    <a href="{{ route('animes.edit', $anime) }}" class="btn btn-outline btn-primary">編集</a>
                                                    <form action="{{ route('animes.destroy', $anime) }}" method="POST" onsubmit="return confirm('本当に削除してもよろしいですか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline btn-secondary">削除</button>
                                                    </form>
                                                </td>
                                                <td class="border border-slate-300 px-6 py-4 text-center">{{ $anime->updated_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        {{-- データがなければ表示 --}}
                        @else
                            <p>投稿はありません。</p>
                        @endif
                    </div>
                    {{ $animes->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>