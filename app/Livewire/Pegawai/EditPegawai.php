<?php

namespace App\Livewire\Pegawai;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditPegawai extends Component
{
    #[Validate('required|string|max:10')]
    public string $nip = '';

    #[Validate('required|string|max:50')]
    public $nama = '';

    #[Validate('required|int|max:20')]
    public $unit_kerja_id = '';

    public Pegawai $pegawai;

    public function mount(Pegawai $pegawai)
    {
        $this->pegawai = $pegawai;
        $this->nip = $pegawai->nip;
        $this->nama = $pegawai->nama;
        $this->unit_kerja_id = $pegawai->unit_kerja_id;
    }

    public function save()
    {
        $this->validate();

        $this->pegawai->update([
            'nip' => $this->nip,
            'nama' => $this->nama,
            'unit_kerja_id' => $this->unit_kerja_id,
        ]);

        session()->flash('message', 'Pegawai berhasil diperbarui.');

        return redirect()->route('pegawai.index');
    }

    public function render()
    {
        return view('livewire.pegawai.edit-pegawai', ['unit_kerjas' => UnitKerja::all()]);
    }
}
