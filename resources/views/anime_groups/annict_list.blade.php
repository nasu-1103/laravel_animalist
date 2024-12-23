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
                                    <li class="font-medium text-red-500">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- 総件数を表示 --}}
                        <span class="mb-3 ml-4">{{ $count . '件' }}</span>

                        <form action="{{ route('anime_groups.annict_store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="mb-3 px-4">
                                <select name="idTitle"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                    {{-- アニメのデータを1件ずつ取得 --}}
                                    @foreach ($works as $work)
                                        {{-- アニメIDとサブタイトルを設定 --}}
                                        <option value="{{ $work['id'] . ',' . $work['title'] }}">
                                            {{-- アニメIDとサブタイトルを表示 --}}
                                            {{ $work['id'] . '：' . $work['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="{{ route('anime_groups.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                <button type="submit" class="btn btn-outline btn-info ml-2">登録</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>