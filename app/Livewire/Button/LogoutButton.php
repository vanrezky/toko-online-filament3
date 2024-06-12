<?php

namespace App\Livewire\Button;

use App\Livewire\Auth\LoginPage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogoutButton extends Component
{
    public function logout(): void
    {
        Auth::guard('customer')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(LoginPage::class, navigate: true);
    }

    public function render()
    {
        return view('livewire.button.logout-button');
    }
}
