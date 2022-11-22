<div class="mb-4" x-data="{
        destroy(id) {
            if (confirm('Are you sure you want to delete this todo?')) {
                $wire.destroy(id);
            }
        }
    }">
    <div class="flex items-center grid grid-cols-6 gap-2">
        <div class="col-span-5">
            <x-text-input wire:model.defer="title" maxlength="255" class="text-gray-800 w-full bg-gray-200 p-1 border-2 border-gray-400 focus:outline-none focus:border-gray-400" placeholder="Add a new to-do" />
        </div>
        <div class="col-span-1">
            <x-primary-button wire:click="create" class="w-full flex justify-center">Add</x-primary-button>
        </div>
    </div>
    @error('title')
        <x-input-error :messages="$message" />
    @enderror
    <div wire:loading.delay.longest wire:target="create">
        <p class="text-2xl text-red-500">Loading ...</p>
    </div>

    @php /** @var \App\Models\Todo $todo */ @endphp
    <ul class="mt-3">
        @forelse ($todos ?? [] as $todo)
            <li class="text-gray-800">
                <span @class([
                        'inline-flex items-center',
                        'text-gray-400 line-through' => $todo->is_completed,
                    ])>
                    {{ $todo->title }}
                    <x-svg.check-circle wire:click="completed('{{ $todo->id }}')" class="ml-1 w-4 h-4 text-green-500 cursor-pointer" />
                    <x-svg.trash x-on:click="destroy('{{ $todo->id }}')" class="w-4 h-4 text-red-500 cursor-pointer" />
                </span>
            </li>
        @empty
            <li class="text-gray-800">No to-dos yet.</li>
        @endforelse
    </ul>
</div>