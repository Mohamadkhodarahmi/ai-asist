<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('is_active');
            $table->string('invite_code')->unique()->nullable()->after('settings');
            $table->integer('max_members')->nullable()->after('invite_code');
            $table->boolean('is_public')->default(false)->after('max_members');
        });

        Schema::table('group_members', function (Blueprint $table) {
            $table->timestamp('joined_at')->nullable()->after('role');
            $table->json('permissions')->nullable()->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['settings', 'invite_code', 'max_members', 'is_public']);
        });

        Schema::table('group_members', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'permissions']);
        });
    }
};