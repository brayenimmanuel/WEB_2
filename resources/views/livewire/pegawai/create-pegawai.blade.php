<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Create Pegawai</h1>
    <form wire:submit.prevent="save" class="space-y-4">
        <flux:input type="text" id="nip" wire:model.defer="nip" label="Nip Pegawai"
            placeholder="Masukkan Nip Pegawai" required />
        <flux:input type="text" id="nama" wire:model.defer="nama" label="Nama Pegawai"
            placeholder="Masukkan Nama Pegawai" required />
        <flux:select id="unit_kerja_id" wire:model.defer="unit_kerja_id" label="Unit Kerja Pegawai" placeholder="Pilih Unit Kerja"
            required>
            @foreach ($unit_kerjas as $unit_kerja)
            <flux:select.option value="{{ $unit_kerja->id }}">{{ $unit_kerja->kode }} - {{ $unit_kerja->nama }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:button type="submit" variant="primary">
            Save
        </flux:button>
    </form>
</div>