<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Create Peminjaman</h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:input
            type="text"
            id="ruang_id"
            wire:model.defer="ruang_id"
            label="Kode Ruang"
            placeholder="Masukkan Kode Ruang"
            required />

        <flux:input
            type="text"
            id="pegawai_id"
            wire:model.defer="pegawai_id"
            label="Nama Pegawai"
            placeholder="Masukkan Nama Pegawai"
            required />

        <flux:input
            type="date"
            id="tanggal"
            wire:model.defer="tanggal"
            label="Pilih Tanggal"
            required />

        <flux:input
            type="time"
            id="jam_mulai"
            wire:model.defer="jam_mulai"
            label="Pilih Jam Mulai"
            required />

        <flux:input
            type="time"
            id="jam_akhir"
            wire:model.defer="jam_akhir"
            label="Pilih Jam Akhir"
            required />

        <flux:select
            id="keterangan"
            wire:model.defer="keterangan"
            label="Keterangan"
            placeholder="Pilih Status Keterangan"
            required>
            <flux:select.option value="Diskusi Proyek">Diskusi Proyek</flux:select.option>
            <flux:select.option value="Koordinasi Tim">Koordinasi Tim</flux:select.option>
            <flux:select.option value="Pelatihan Karyawan">Pelatihan Karyawan</flux:select.option>
            <flux:select.option value="Presentasi Produk">Presentasi Produk</flux:select.option>
            <flux:select.option value="Rapat Anggaran">Rapat Anggaran</flux:select.option>
            <flux:select.option value="Rapat Evaluasi">Rapat Evaluasi</flux:select.option>
            <flux:select.option value="Rapat Strategi">Rapat Strategi</flux:select.option>
            <flux:select.option value="Rapat Tim">Rapat Tim</flux:select.option>
        </flux:select>


        <flux:button
            type="submit"
            variant="primary">
            Save
        </flux:button>
    </form>
</div>