<?php

namespace App\Livewire\UnitKerja;

use App\Models\UnitKerja;
use Livewire\Component;

class ListUnitKerja extends Component
{
    public function render()
    {
        return view('livewire.unit-kerja.list-unit-kerja', ['unitkerjas' => UnitKerja::all()]);
    }

    public function delete($id)
    {
        $unitkerja = UnitKerja::find($id);

        if ($unitkerja) {
            $unitkerja->delete();
            session()->flash('message', 'Unit Kerja berhasil dihapus.');
        }
    }
}
