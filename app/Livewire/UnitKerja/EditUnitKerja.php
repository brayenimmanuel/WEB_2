<?php

namespace App\Livewire\UnitKerja;

use App\Models\UnitKerja;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditUnitKerja extends Component
{
    #[Validate('required|string|max:10')]
    public string $kode = '';

    #[Validate('required|string|max:100')]
    public $nama = '';

    public UnitKerja $unitkerja;

    public function mount(UnitKerja $unitkerja)
    {
        $this->unitkerja = $unitkerja;
        $this->kode = $unitkerja->kode;
        $this->nama = $unitkerja->nama;
    }

    public function save()
    {
        $this->validate();

        $this->unitkerja->update([
            'kode' => $this->kode,
            'nama' => $this->nama,
        ]);

        session()->flash('message', 'Unit Kerja berhasil diperbarui.');

        return redirect()->route('unit-kerja.index');
    }

    public function render()
    {
        return view('livewire.unit-kerja.edit-unit-kerja');
    }
}
