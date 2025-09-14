<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\Business;

class CreateBusiness extends Component
{
    public string $name = '';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        
        $business = Business::create($validated);
        
        $this->reset('name');
        
        session()->flash('message', 'Business created successfully!');
        
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.business.create-business');
    }
}
