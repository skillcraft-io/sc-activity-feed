<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('sc_activity_feeds')) {
            Schema::create('sc_activity_feeds', function (Blueprint $table) {
                $table->id();
                $table->nullableMorphs('owner');
                $table->nullableMorphs('module');
                $table->string('title', 50)->nullable();
                $table->text('message')->nullable();
                $table->boolean('is_private')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sc_activity_feeds');
    }
};
