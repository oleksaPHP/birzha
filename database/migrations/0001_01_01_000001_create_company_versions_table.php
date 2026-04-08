<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('name', 256);
            $table->string('edrpou', 10);
            $table->text('address');
            $table->json('old_data')->nullable();
            $table->json('new_data');
            $table->timestamps();

            $table->unique(['company_id', 'version']);
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_versions');
    }
};
