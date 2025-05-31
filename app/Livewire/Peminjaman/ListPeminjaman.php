<?php

namespace App\Livewire\Peminjaman;

use Livewire\Component;
use App\Models\Peminjaman;


class ListPeminjaman extends Component
{
    public function render()
    {
        return view('livewire.peminjaman.list-peminjaman', [
            'peminjamans' => Peminjaman::all(),
        ]);
    }
    public function delete($id)
    {
        $ruang = Peminjaman::find($id);
        if ($ruang) {
            $ruang->delete();
            session()->flash('message', 'Peminjaman berhasil dihapus.');
        }
    }
}