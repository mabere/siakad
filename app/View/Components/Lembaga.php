<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Lembaga extends Component
{
    public $lembaga;
    public $alamat;
    public $nomorTelepon;
    public $website;
    public $email;

    public function __construct($lembaga, $alamat, $nomorTelepon, $website, $email)
    {
        $this->lembaga = $lembaga;
        $this->alamat = $alamat;
        $this->nomorTelepon = $nomorTelepon;
        $this->website = $website;
        $this->email = $email;
    }

    public function render()
    {
        return view('components.kop.lembaga');
    }
}