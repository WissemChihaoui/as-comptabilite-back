<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('file_path', 500); // Increased length for file paths
            $table->string('original_name')->nullable(); // Store original file name
            $table->string('mime_type')->nullable(); // Store file MIME type
            $table->unsignedBigInteger('file_size')->nullable(); // Store file size in bytes
            $table->timestamps();

            // Add indexes for better performance
            $table->index('form_id');
            $table->index('document_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_documents');
    }
};