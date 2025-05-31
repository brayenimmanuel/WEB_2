<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">List Peminjaman</h1>

    @if (session('message'))
    <div class="bg-green-500 text-white p-4 rounded mb-4">
        {{ session('message') }}
    </div>
    @endif

    <div class="flex justify-between mb-4">
        <flux:button :href="route('peminjaman.create')" variant="primary">New Peminjaman</flux:button>
    </div>
    <table class="min-w-full border-collapse border border-gray-400 mt-4">
        <thead>
            <tr class="text-left bg-gray-100">
                <th class="py-2 px-4 border border-gray-300">No.</th>
                <th class="py-2 px-4 border border-gray-300">Ruang</th>
                <th class="py-2 px-4 border border-gray-300">Pegawai</th>
                <th class="py-2 px-4 border border-gray-300">Tanggal</th>
                <th class="py-2 px-4 border border-gray-300">Jam Mulai</th>
                <th class="py-2 px-4 border border-gray-300">Jam Akhir</th>
                <th class="py-2 px-4 border border-gray-300">keterangan</th>
                <th class="py-2 px-4 border border-gray-300">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($peminjamans as $index => $peminjaman)
            <tr>
                <td class="py-2 px-4 border border-gray-300">{{ $index + 1 }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->ruang_id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->pegawai_id }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->tanggal }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->jam_mulai }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->jam_akhir }}</td>
                <td class="py-2 px-4 border border-gray-300">{{ $peminjaman->keterangan }}</td>
                <td class="py-2 px-4 border border-gray-300">
                    <flux:button :href="route('peminjaman.edit', $peminjaman)">Edit</flux:button>
                    <flux:button variant="danger" wire:click="delete({{ $peminjaman->id }})" wire:confirm="Are you sure?">Delete</flux:button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>