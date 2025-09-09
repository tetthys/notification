<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_notification_prefs', function (Blueprint $t) {
            $t->id();
            $t->string('user_id');
            $t->string('type');
            $t->json('disabled_channels')->default('[]');
            $t->timestamps();
            $t->unique(['user_id', 'type']);
        });
    }
    public function down(): void { Schema::dropIfExists('user_notification_prefs'); }
};