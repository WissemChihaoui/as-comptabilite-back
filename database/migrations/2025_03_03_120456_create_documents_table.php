<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id(); // This creates a BIGINT UNSIGNED primary key
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('user_documents');
    }
};
