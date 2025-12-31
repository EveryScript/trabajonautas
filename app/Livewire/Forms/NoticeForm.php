<?php

namespace App\Livewire\Forms;

use App\Models\Notice;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NoticeForm extends Form
{
    public $title;
    public $description;
    public $link;
    public $image;
    public $user_id;

    public function delete($id)
    {
        Notice::find($id)->delete();
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:10|max:50|unique:notices,title',
            'description' => 'required|min:10|max:100',
            'link' => 'required|active_url',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5000',
            'user_id' => 'required'
        ]);
        $this->image = $this->image->store('noticias', 'public');
        $notice_saved = Notice::create($this->only('title', 'description', 'link', 'image', 'user_id'));
    }

    public function validationAttributes()
    {
        return [
            'title' => 'título',
            'description' => 'descripción', 
            'link' => 'enlace',
            'image' => 'imagen'
        ];
    }
}
