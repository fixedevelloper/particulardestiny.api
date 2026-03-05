<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('src');
            $table->string('alt')->nullable();
            $table->timestamps();
        });
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('capacity'); // nb personnes
            $table->integer('size')->nullable(); // m²
            $table->foreignId('image_id')->nullable()->constrained();

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('image_id')->nullable()->constrained();
            $table->timestamps();
        });
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('image_id')->nullable()->constrained();
            $table->timestamps();
        });
        Schema::create('room_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            // Dates
            $table->date('check_in');
            $table->date('check_out');

            // Occupation (simplifié)
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);

            // Calculé automatiquement côté backend
            $table->unsignedTinyInteger('total_guests');

            // Pricing
            $table->decimal('price_per_night', 10, 2);
            $table->integer('nights');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled'
            ])->default('pending');

            // Paiement
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded'
            ])->default('pending');

            // Infos client (important si pas connecté)
            $table->json('meta')->nullable();

            // Tracking
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            // Index pour performance 🔥
            $table->index(['room_id', 'check_in', 'check_out']);
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('method'); // mobile_money, card
            $table->string('transaction_id')->nullable();

            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');

            $table->timestamps();
        });
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(true);
        });
        // -------------------
        // MEDIA (compatible Spatie/Medialibrary)
        // -------------------
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // model_type + model_id
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations')->nullable();
            $table->json('custom_properties')->nullable();
            $table->json('generated_conversions')->nullable();
            $table->json('responsive_images')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('customers');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('users');
    }
};
