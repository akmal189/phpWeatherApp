<div>
    <div style="max-width: 600px; margin: 20px auto;">

        <h2>Погода по адресу</h2>

        <form class="mb-4 flex">
            <div class="flex-auto">
                <input
                    wire:model.live="query"
                    type="text"
                    placeholder="Введите адрес"
                    class="border rounded px-4 py-2 w-full"
                >
                @error('address') <span style="color:red;">{{ $message }}</span> @enderror
            </div>
        </form>

        @if(!empty($suggestions))
            <div class="bg-gray-100 p-4 rounded mb-4">
                <h3 class="text-lg font-semibold">Возможные адреса:</h3>
                <ul>
                    @foreach($suggestions as $suggestion)
                        <li wire:click="selectSuggestion('{{ $suggestion['value'] }}')" class="cursor-pointer hover:bg-blue-200 p-2 rounded">
                            {{ $suggestion['value'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($weatherInfo)
            <div class="bg-white p-4 rounded shadow">
                {!! $weatherInfo !!}
            </div>
        @endif
    </div>

</div>
