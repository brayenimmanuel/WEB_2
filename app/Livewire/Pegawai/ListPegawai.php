<?php

namespace App\Livewire\Pegawai;

use App\Models\Pegawai;
use Livewire\Component;

class ListPegawai extends Component
{
    public function render()
    {
        return view('livewire.pegawai.list-pegawai', ['pegawais' => Pegawai::all()]);
    }

    public function delete($id)
    {
        $pegawai = Pegawai::find($id);

        if ($pegawai) {
            $pegawai->delete();
            session()->flash('message', 'Pegawai berhasil dihapus.');
        }
    }
}
