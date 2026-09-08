<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->string('peo_code')->nullable();
            $table->string('peo_title')->nullable();
            $table->string('peo_version', 40)->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('task_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_type_id')->constrained()->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label');
            $table->string('field_type', 30);
            $table->string('phase', 20)->default('finish');
            $table->string('unit', 40)->nullable();
            $table->text('help_text')->nullable();
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['task_type_id', 'key']);
        });
        Schema::table('work_logs', function (Blueprint $table) {
            $table->foreignId('task_type_id')->nullable()->after('title')->constrained()->nullOnDelete();
            $table->string('task_type_name')->nullable()->after('task_type_id');
            $table->string('peo_reference')->nullable()->after('task_type_name');
        });
        Schema::create('work_log_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_input_id')->nullable()->constrained()->nullOnDelete();
            $table->string('input_key', 80);
            $table->string('input_label');
            $table->string('field_type', 30);
            $table->string('phase', 20);
            $table->string('unit', 40)->nullable();
            $table->text('value')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->unique(['work_log_id', 'input_key', 'phase']);
        });

        $now = now();
        foreach (['inspection' => 'Inspección', 'preventive' => 'Mantenimiento preventivo', 'corrective' => 'Mantenimiento correctivo', 'cleaning' => 'Limpieza y sanitización', 'cultivation' => 'Operación de cultivo', 'calibration' => 'Calibración', 'other' => 'Otro'] as $code => $name) {
            DB::table('task_types')->insert(['code' => $code, 'name' => $name, 'active' => true, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_log_inputs');
        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_type_id');
            $table->dropColumn(['task_type_name', 'peo_reference']);
        });
        Schema::dropIfExists('task_inputs');
        Schema::dropIfExists('task_types');
    }
};
