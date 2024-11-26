<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメグループ編集フォーム
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

                        <form action="{{ route('anime_groups.update', $animeGroup) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3 mt-2">
                                <label for="name" class="ml-4 block text-lg font-medium text-gray-700">タイトル</label>
                                <div class="flex">
                                    <input type="text" name="name" value="{{ old('name', $animeGroup->name) }}"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base">
                                </div>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="{{ route('anime_groups.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                                <button type="submit" class="btn btn-outline btn-info ml-2">更新</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>