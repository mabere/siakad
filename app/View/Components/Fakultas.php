<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Fakultas extends Component
{
    public $fakultas;
    public $alamat;
    public $nomorTelepon;
    public $website;
    public $email;

    public function __construct($fakultas, $alamat, $nomorTelepon, $website, $email)
    {
        $this->fakultas = $fakultas;
        $this->alamat = $alamat;
        $this->nomorTelepon = $nomorTelepon;
        $this->website = $website;
        $this->email = $email;
    }

    public function render()
    {
        return view('components.kop.fakultas');
    }
}