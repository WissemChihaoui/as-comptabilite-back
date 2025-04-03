<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user receiving the notification
            $table->string('type'); // Type of notification (e.g., 'order', 'message')
            $table->text('title'); // Notification content
            $table->text('serviceLink');
            $table->boolean('isUnRead')->default(1); // Status of the notification
            $table->timestamps(); // Created_at and updated_at timestamps

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
