<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->between(1, 5);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->unique(['article_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_ratings');
    }
};
?>
