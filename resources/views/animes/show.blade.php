<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメ詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mx-auto max-w-2x1 text-senter">

                        <form action="{{ route('animes.update', $anime) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3 mt-2">
                                <label for="title" class="ml-4 block text-lg font-medium text-gray-700">タイトル</label>
                                <div class="flex">
                                    <input type="text" name="title" value="{{ old('title', $anime->title) }}"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        disabled>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <label for="episode" class="ml-4 block text-lg font-medium text-gray-700">エピソード</label>
                                <div class="flex">
                                    <input type="number" name="episode" value="{{ old('episode', $anime->episode) }}"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        disabled>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <label for="sub_title"
                                    class="ml-4 block text-lg font-medium text-gray-700">サブタイトル</label>
                                <div class="flex">
                                    <textarea name="sub_title" rows="4"
                                        class="ml-4 mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-base"
                                        disabled>{{ old('sub_title', $anime->sub_title) }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3 ml-4">
                                <a href="{{ route('animes.index') }}" class="btn btn-ghost">&lt; 戻る</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>