<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('youtube_videos', function (Blueprint $table) {
            $table->longText('description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('channel_avatar')->nullable();
            $table->bigInteger('channel_subscriber_count')->nullable();
            $table->bigInteger('view_count')->nullable();
            $table->bigInteger('like_count')->nullable();
            $table->bigInteger('comment_count')->nullable();
            $table->string('duration')->nullable();
            $table->json('tags')->nullable();
            $table->string('category_id')->nullable();
            $table->string('category_name')->nullable();
        });

        Schema::create('youtube_video_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('youtube_videos')->cascadeOnDelete();
            $table->string('youtube_comment_id')->unique();
            $table->string('parent_youtube_comment_id')->nullable()->index();
            $table->string('author_name');
            $table->string('author_avatar')->nullable();
            $table->text('text')->nullable();
            $table->bigInteger('like_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_video_comments');

        Schema::table('youtube_videos', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'published_at',
                'channel_id',
                'channel_avatar',
                'channel_subscriber_count',
                'view_count',
                'like_count',
                'comment_count',
                'duration',
                'tags',
                'category_id',
                'category_name',
            ]);
        });
    }
};
