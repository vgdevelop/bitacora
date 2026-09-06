<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 160);
            $table->string('method', 10);
            $table->string('path', 500);
            $table->unsignedSmallInteger('status_code');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->index(['created_at', 'action']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
