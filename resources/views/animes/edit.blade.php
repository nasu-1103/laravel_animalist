<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメ編集フォーム
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mx-auto max-w-2x1 text-senter">

                        {{-- バリデーションエラーがあれば、エラーメッセージを表示 --}}
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form action="{{ route('animes.update', $anime) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="title" class="block text-lg font-medium text-gray-700">タイトル</label>
                                <input type="text" name="title" value="{{ old('title', $anime->title) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                            </div>
                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                <div class="mb-3">
                                    <label for="episode" class="block text-lg font-medium text-gray-700">エピソード</label>
                                    <input type="number" name="episode" value="{{ old('episode', $anime->episode) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                </div>
                                <div class="sm:col-span-2">
                                    <div class="mb-3">
                                        <label for="sub_title" class="block text-lg font-medium text-gray-700">サブタイトル</label>
                                        <textarea name="sub_title" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">{{ old('sub_title', $anime->sub_title) }}</textarea>
                                    </div>
                                    <a href="{{ route('animes.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                    <button type="submit" class="btn btn-outline btn-info">更新</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
