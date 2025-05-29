<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">List Unit Kerja</h1>
    <div class="flex justify-between mb-4">
        <flux:button :href="route('unit-kerja.create')" variant="primary">New Unit Kerja</flux:button>
    </div>
    @if (session('message'))
    <div class="bg-green-500 text-white p-4 rounded mb-4">
        {{ session('message') }}
    </div>
    @endif
    <table class="min-w-full border-collapse border border-gray-400 mt-4">
        <thead>
            <tr class="text-left bg-gray-600">
                <th class="py-2 px-4 border border-gray-300">ID</th>
                <th class="py-2 px-4 border border-gray-300">Kode</th>
                <th class="py-2 px-4 border border-gray-300">Nama</th>
                <th class="py-2 px-4 border border-gray-300">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unitkerjas as $unitkerja)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $unitkerja->id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $unitkerja->kode }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $unitkerja->nama }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <!-- Add your action buttons here -->
                    <flux:button :href="route('unit-kerja.edit', $unitkerja)">Edit</flux:button>
                    <flux:button variant="danger" wire:click="delete({{ $unitkerja->id }})"
                        wire:confirm="Are you sure?">Delete</flux:button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>