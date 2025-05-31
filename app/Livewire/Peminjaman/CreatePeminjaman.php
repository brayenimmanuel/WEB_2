<?php

namespace App\Livewire\Peminjaman;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Peminjaman;

class CreatePeminjaman extends Component
{
    #[Validate('required|string|max:10')]
    public string $ruang_id = '';

    #[Validate('required|string|max:10')]
    public string $pegawai_id = '';

    #[Validate('required|string|max:50')]
    public $tanggal = '';

    #[Validate('required|string|max:50')]
    public $jam_mulai = '';

    #[Validate('required|string|max:50')]
    public $jam_akhir = '';

    #[Validate('required|string|max:50')]
    public $keterangan = '';

    public function save()
    {
        $this->validate();

        Peminjaman::create([
            'ruang_id' => $this->ruang_id,
            'pegawai_id' => $this->pegawai_id,
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jam_mulai,
            'jam_akhir' => $this->jam_akhir,
            'keterangan' => $this->keterangan,
        ]);


        session()->flash('message', 'Peminjaman berhasil ditambahkan.');

        // Reset the form fields 
        $this->redirectRoute('peminjaman.index');
    }
    public function render()
    {
        return view('livewire.peminjaman.create-peminjaman');
    }
}