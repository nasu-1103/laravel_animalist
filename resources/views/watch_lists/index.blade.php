<x-app-layout>
    <x-slot name="header">
        <form action="{{ route('watch_list.index') }}" method="GET">
            <div class="col-auto flex">
                <label for="keyword" class="font-semibold text-xl my-auto text-gray-800">視聴リスト</label>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="mr-1 mt-3 mb-4">
                    <a href="{{ route('watch_list.selectAnimeGroup') }}" class="btn btn-link text-lg">新規登録</a>

                    {{-- フラッシュメッセージを表示 --}}
                    @session('flash_message')
                        {{ session('flash_message') }}
                    @endsession

                    {{-- エラーメッセージの表示 --}}
                    @session('error_message')
                        {{ session('error_message') }}
                    @endsession

                    {{-- アニメグループごとにデータを表示 --}}
                    @forelse ($anime_group_lists as $animeGroup)
                        {{-- ウォッチリストのカウントの初期化 --}}
                        @if ($animeGroup->animes->count() > 0)
                            @php
                                $watch_ListCount = 0;
                            @endphp

                            <div class="text-gray-900">
                                <div class="card bg-base-100 shadow-xl mt-6 text-lg">
                                    <div class="card-body flex">
                                        <div class="flex">
                                            <h2 class="card-title">{{ $animeGroup->name }}</h2>
                                            @foreach ($animeGroup->animes as $anime)
                                                {{-- 各アニメのウォッチリストをチェック --}}
                                                @foreach ($anime->watchlists as $watch_list)
                                                    @php
                                                        // ステータスが1ならカウントを追加する
                                                        if ($watch_list->status == 1) {
                                                            $watch_ListCount++;
                                                        }
                                                    @endphp
                                                @endforeach
                                            @endforeach
                                            {{-- 全てのエピソードが視聴済みの場合、👑を表示 --}}
                                            @if ($animeGroup->watched_count === $animeGroup->total_episodes)
                                                <span class="text-3xl ml-2 mb-2">👑</span>
                                            @endif
                                        </div>

                                        <div class="anime_group">
                                            <div
                                                class="card-actions relative overflow-x-auto shadow-sm sm:rounded-lg text-gray-300 active:text-gray-200">
                                                <table class="w-full text-gray-700 text-nowrap">
                                                    <tr>
                                                        <th class="mt-4 w-24">話数</th>
                                                        <th class="mt-4 w-72">サブタイトル</th>
                                                        <th class="mt-4 w-48">視聴日</th>
                                                        <th class="mt-4 w-16">ステータス</th>
                                                        <th class="mt-4 w-36">エディット</th>
                                                    </tr>

                                                    {{-- アニメグループに含まれる全てのアニメを順番に表示 --}}
                                                    @foreach ($animeGroup->animes as $anime)
                                                        <tr class="text-center">
                                                            <td class="border boder-slate-300 px-6 py-4">
                                                                {{ $anime->episode . '話' }}</td>
                                                            <td class="border boder-slate-300 px-6 py-4 text-center">
                                                                {{ $anime->sub_title }}</td>

                                                            {{-- アニメごとのウォッチリストをチェック --}}
                                                            @forelse ($anime->watchlists as $watch_list)
                                                                {{-- アニメIDとユーザーIDが一致し、かつユーザーが登録したウォッチリストのデータを表示 --}}
                                                                @if ($anime->id == $watch_list->anime_id && Auth::user()->id == $watch_list->user_id)
                                                                    <td class="border boder-slate-300 px-6 py-4">
                                                                        {{ $watch_list->created_at->format('Y-m-d H:i:s') }}
                                                                    </td>
                                                                    <td class="border boder-slate-300 px-6 py-4">
                                                                        {{-- ステータスをもとにアイコンを表示する --}}
                                                                        @if ($watch_list->status == 1)
                                                                            {{-- 視聴済みは✅を表示 --}}
                                                                            {{ '✅' }}
                                                                        @elseif ($watch_list->status == 2)
                                                                            {{-- 視聴中は👀を表示 --}}
                                                                            {{ '👀' }}
                                                                        @endif
                                                                    </td>
                                                                    <td
                                                                        class="flex border border-slate-300 px-6 py-4 justify-center gap-4">
                                                                        <a href="{{ route('watch_list.edit', $watch_list) }}"
                                                                            class="btn btn-outline btn-primary">編集</a>
                                                                        <form
                                                                            action="{{ route('watch_list.destroy', $watch_list) }}"
                                                                            method="POST"
                                                                            onsubmit="return confirm('本当に削除してもよろしいですか？');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-outline btn-secondary">削除</button>
                                                                        </form>
                                                                    </td>
                                                                @endif
                                                                {{-- データがなくても空セルを表示 --}}
                                                            @empty
                                                                <td class="border border-slate-300 px-6 py-4"></td>
                                                                <td class="border border-slate-300 px-6 py-4"></td>
                                                                <td class="border border-slate-300 px-6 py-4"></td>
                                                            @endforelse
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    {{-- データがなければ表示 --}}
                    @empty
                        <p class="text-center">投稿はありません。</p>
                    @endforelse
                    {{ $anime_group_lists->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>