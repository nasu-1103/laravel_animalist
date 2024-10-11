<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            アニメ登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="ml-40">

                        {{-- 入力フォームにエラーが発生した場合、エラーメッセージを表示 --}}
                        @if ($errors->any())
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form action="{{ route('watch_list.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="flex">
                                <label for="anime"
                                    class="block mb-2 text-lg font-medium text-gray-700 dark:text-white">アニメを選択してください。</label>
                                <label for="note"
                                    class="hidden xl:inline-block xl:ml-2 text-lg font-medium text-red-500 dark:text-white">視聴中の場合、先に時間を入力してから未視聴に変更してください。</label>
                                <div class="tooltip" data-tip="視聴中の場合、先に時間を入力してから未視聴に変更してください。">
                                    <textarea name="memo" id="memo" rows="1" cols="12" placeholder="例： 15:10" class="ml-28 rounded-xl"
                                        onchange="setNotes()" data-user_id="{{ Auth::user()->id }}">{{ Auth::user()->notes }}</textarea>
                                </div>
                            </div>

                            {{-- アニメリストの表示 --}}
                            @foreach ($animes as $anime)
                                <div class="grid grid-cols-2">
                                    <div class="row mt-2">
                                        <label class="mt-4 text-base">
                                            <input type="checkbox" id="{{ 'checkbox' . $anime->id }}"
                                                name="anime_check_lists[]" value="{{ $anime->id }}" class="mr-4"
                                                disabled
                                                @checked(in_array($anime->id, $watch_lists))>{{ $anime->title . '：' . $anime->sub_title }}
                                        </label>
                                    </div>
                                    <div>
                                        <select name="status" id="{{ 'status' . $anime->id }}"
                                            class="align-top rounded-xl ml-6" onchange="setStatus({{ $anime->id }})"
                                            data-user_id="{{ Auth::user()->id }}">
                                            <option value="-1">未視聴</option>
                                            <option value="2" @selected(in_array($anime->id, $watch_lists_in))>視聴中</option>
                                            <option value="1" @selected(in_array($anime->id, $watch_lists_comp))>視聴済み</option>
                                        </select>
                                        <textarea name="note" id="{{ 'notes' . $anime->id }}" rows="1" cols="12" placeholder="メモ"
                                            class="ml-6 rounded-xl hidden xl:inline-block">{{ $watch_lists_notes[$anime->id] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </form>
                    </div>
                    <a href="javascript:history.back();" class="mt-2 btn btn-ghost">&lt; 戻る</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ステータス変更時に非同期処理のリクエストを送信 --}}
    <script>
        async function setStatus(anime_id) {
            const checkbox = document.getElementById('checkbox' + anime_id);
            const status = document.getElementById('status' + anime_id);
            const notes = document.getElementById('notes' + anime_id);

            // 「視聴中」または「視聴済み」はチェックを入れる。それ以外はチェックを外す
            if (status.value === "1" || status.value === "2") {
                checkbox.checked = true; // チェックを入れる
            } else {
                checkbox.checked = false; //　チェックを外す
            }

            // 視聴済みの場合は、メモをクリアにする
            if (status.value === "1") {
                notes.value = '';
            }

            // 非同期処理のリクエストでステータスとメモを更新
            (await fetch("/api/setStatus", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    "anime_id": anime_id,
                    "user_id": status.dataset.user_id,
                    "status": status.value,
                    "notes": notes.value,
                }),
            }))
            .json().then((json) => console.log(json));
        }
        async function setNotes() {
            // メモを取得
            const notes = document.getElementById('memo');

            // 非同期処理リクエストを送信してメモを更新
            (await fetch("/api/setNotes", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    "user_id": notes.dataset.user_id,
                    "notes": notes.value,
                }),
            }))
            .json().then((json) => console.log(json));
        }
    </script>
</x-app-layout>
