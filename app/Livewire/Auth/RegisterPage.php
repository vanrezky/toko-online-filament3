<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class RegisterPage extends Component
{
    #[Validate('required|min:5|max:100')]
    public $first_name;

    #[Validate('required|email|unique:customers,email|max:100')]
    public $email;

    #[Validate('required|confirmed|min:5|max:25')]
    public $password;

    #[Validate('required')]
    public $password_confirmation;


    public function save()
    {
        $this->validate();
        Customer::create($this->all());
        Toaster::success('Registered successfully!');
        // $this->redirect(route('login'));
        return $this->redirect(route('login'));
    }

    public function render()
    {

        return view('livewire.auth.register-page');
    }
}
