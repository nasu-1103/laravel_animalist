<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            エピソード登録
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

                        <form action="{{ route('animes.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="mb-3 mt-2">
                                <label for="animeGroup"
                                    class="ml-4 block text-lg font-medium text-gray-700">アニメグループ</label>
                                <div class="flex">
                                    <select name="animeGroup"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                        {{-- 全てのアニメのタイトルを表示 --}}
                                        @foreach (\App\Models\animeGroup::all() as $animeGroup)
                                            {{-- アニメのタイトルを表示 --}}
                                            <option value="{{ $animeGroup->id }}">{{ $animeGroup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 mt-2">
                                    <label for="episode"
                                        class="ml-4 block text-lg font-medium text-gray-700">エピソード</label>
                                    <div class="flex">
                                        <input type="number" name="episode" value="{{ old('episode') }}"
                                            class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                    </div>
                                </div>

                                <div class="mb-3 mt-2">
                                    <label for="sub_title"
                                        class="ml-4 block text-lg font-medium text-gray-700">サブタイトル</label>
                                    <div class="flex">
                                        <textarea name="sub_title" rows="4"
                                            class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                            {{ old('sub_title') }}
                                        </textarea>
                                    </div>
                                </div>

                                <div class="mb-3 ml-4">
                                    <a href="{{ route('animes.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                    <button type="submit" class="btn btn-outline btn-info ml-2">登録</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>