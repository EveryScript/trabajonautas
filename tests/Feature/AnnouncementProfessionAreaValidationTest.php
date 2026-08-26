<?php

namespace Tests\Feature;

use App\Livewire\Forms\AnnouncementForm;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Tests\TestCase;

class AnnouncementProfessionAreaValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('area_profesion', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('profesion_id');
            $table->timestamps();
        });
        DB::table('area_profesion')->insert([
            ['area_id' => 1, 'profesion_id' => 10],
            ['area_id' => 1, 'profesion_id' => 11],
            ['area_id' => 2, 'profesion_id' => 20],
        ]);
    }

    public function test_backend_accepts_only_professions_related_to_the_selected_area(): void
    {
        $form = $this->form();
        $form->selected_area_id = 1;
        $form->profesions = [10, 11];

        $form->validateProfessionAreaRelation();

        $this->addToAssertionCount(1);
    }

    public function test_backend_rejects_a_manipulated_profession_from_another_area(): void
    {
        $form = $this->form();
        $form->selected_area_id = 1;
        $form->profesions = [10, 20];

        $this->expectException(ValidationException::class);

        $form->validateProfessionAreaRelation();
    }

    private function form(): AnnouncementForm
    {
        return new AnnouncementForm(new class extends Component {}, 'announcement');
    }
}
