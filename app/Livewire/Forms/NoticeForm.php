<?php

namespace App\Livewire\Forms;

use App\Models\Notice;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NoticeForm extends Form
{
    public ?Notice $notice = null;

    public $title;
    public $description;
    public $link;
    public $image;
    public $user_id;

    public function setNotice(Notice $notice)
    {
        $this->notice = $notice;
        $this->fill($notice->only([
            'title',
            'description',
            'link',
            'image',
            'user_id'
        ]));
    }

    public function save()
    {
        $this->validate();
        if ($this->image && !is_string($this->image))
            $this->image = $this->image->store('noticias', 'public');

        if ($this->notice)
            $this->notice->update($this->except(['notice']));
        else
            Notice::create($this->except(['notice']));

        $this->reset();
    }

    public function rules()
    {
        return [
            'title' => 'required|min:10|max:50|unique:notices,title',
            'description' => 'required|min:10|max:100',
            'link' => 'required|active_url',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5000',
            'user_id' => 'required'
        ];
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
