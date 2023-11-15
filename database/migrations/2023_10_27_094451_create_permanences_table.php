<?php

use App\Enums\StatesClass;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permanences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json('users')->comment('agents that are included, without repetition and without order')->nullable();
            $table->json('order')->comment('order in which users are stored to generate table')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('departement_id')->constrained();
            $table->string('file')->nullable();
            $table->enum('statut',[
                StatesClass::Inactive()->value,
                StatesClass::Active()->value]
            )->default(StatesClass::Active()->value);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permanences');
    }
};
