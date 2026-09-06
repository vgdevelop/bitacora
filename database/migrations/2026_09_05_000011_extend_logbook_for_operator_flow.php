<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('technician')->index();
            }
            if (! Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->index();
            }
        });

        Schema::table('work_logs', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            if (! Schema::hasColumn('work_logs', 'closed_by')) {
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        User::where('is_admin', true)->update(['role' => 'supervisor']);
    }

    public function down(): void
    {
        Schema::table('work_logs', function (Blueprint $table) {
            if (Schema::hasColumn('work_logs', 'closed_by')) {
                $table->dropConstrainedForeignId('closed_by');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};
