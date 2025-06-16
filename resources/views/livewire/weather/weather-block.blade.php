<div>
    <div style="max-width: 600px; margin: 20px auto;">
        <h2>Погода по адресу</h2>
        <form class="mb-4 flex">
            <div class="flex-auto mr-2">
                <input
                    wire:model.live="query"
                    type="text"
                    placeholder="Введите адрес"
                    class="border rounded px-4 py-2 w-full"
                >
                @error('address') <span style="color:red;">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Узнать погоду</button>
        </form>
    </div>
</div>
