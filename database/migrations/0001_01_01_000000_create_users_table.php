<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'user_roles' table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create the 'users' table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('national_id_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number', 15);
            $table->string('profile_picture')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('role_id')->constrained('user_roles', 'role_id')->onDelete('cascade');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Create the 'user_settings' table
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('system');
            $table->integer('screen_timeout')->default(30);
            $table->string('font_style')->default('sans-serif');
            $table->integer('font_size')->default(16);
            $table->boolean('notifications_enabled')->default(true);
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->boolean('two_factor_auth')->default(false);
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


        // Create the 'user_role_assignments' table
        Schema::create('user_role_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->string('username');
            $table->unsignedBigInteger('role_id');
            $table->string('national_id_picture')->nullable();
            $table->string('assigned_by')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verified_by')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('username')->references('username')->on('users');
            $table->foreign('role_id')->references('role_id')->on('user_roles');
            $table->foreign('assigned_by')->references('username')->on('users');
            $table->foreign('verified_by')->references('username')->on('users');
        });

        // Create the 'currencies' table
        Schema::create('currencies', function (Blueprint $table) {
            $table->id('currency_id');
            $table->string('currency_code')->unique();
            $table->string('currency_name');
            $table->decimal('exchange_rate', 10, 2);
            $table->boolean('is_base_currency')->default(false);
            $table->timestamps();
        });

        // Create the 'room_types' table
        Schema::create('room_types', function (Blueprint $table) {
            $table->id('room_type_id');
            $table->string('room_type_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create the 'rooms' table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->string('room_number')->unique();
            $table->unsignedBigInteger('room_type_id');
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('room_type_id')->references('room_type_id')->on('room_types');
            $table->foreign('currency_id')->references('currency_id')->on('currencies');
        });

        // Create the 'bookings' table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('guest_id');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->enum('booking_status', ['Pending', 'Confirmed', 'Cancelled'])->default('Pending');
            $table->unsignedBigInteger('booker_id');
            $table->unsignedBigInteger('booked_by');
            $table->string('booked_from');
            $table->timestamps();

            $table->foreign('room_id')->references('room_id')->on('rooms');
            $table->foreign('guest_id')->references('id')->on('users');
            $table->foreign('currency_id')->references('currency_id')->on('currencies');
            $table->foreign('booker_id')->references('id')->on('users');
            $table->foreign('booked_by')->references('id')->on('users');
        });

        // Create the 'payment_methods' table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->string('payment_method_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Create the 'payments' table
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->decimal('exchange_rate', 10, 2);
            $table->decimal('total_in_base_currency', 10, 2);
            $table->enum('payment_status', ['Pending', 'Completed', 'Failed'])->default('Pending');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings');
            $table->foreign('payment_method_id')->references('payment_method_id')->on('payment_methods');
            $table->foreign('currency_id')->references('currency_id')->on('currencies');
        });

        // Create the 'menu_items' table
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('currency_id')->references('currency_id')->on('currencies');
        });

        // Create the 'restaurant_orders' table
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('guest_id'); // Matches the 'username' data type
            $table->date('order_date');
            $table->time('order_time');
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->enum('order_status', ['Pending', 'Delivered', 'Cancelled'])->default('Pending');
            $table->timestamps();

            $table->foreign('room_id')->references('room_id')->on('rooms');
            $table->foreign('guest_id')->references('username')->on('users');
            $table->foreign('currency_id')->references('currency_id')->on('currencies');
        });

        // Create the 'order_items' table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('restaurant_orders')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('menu_items')->onDelete('cascade');
            $table->foreign('currency_id')->references('currency_id')->on('currencies')->onDelete('cascade');
        });

        // Create the 'reports' table
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->string('report_name');
            $table->enum('report_type', ['Financial', 'Booking', 'Restaurant', 'User Activity']);
            $table->string('report_period')->nullable();
            $table->text('report_filters')->nullable();
            $table->longText('report_data')->nullable();
            $table->string('generated_by'); // Matches the 'username' data type
            $table->boolean('is_printed')->default(false);
            $table->dateTime('printed_at')->nullable();
            $table->timestamps();

            $table->foreign('generated_by')->references('username')->on('users');
        });

        // Create the 'user_activity_logs' table
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id('activity_id');
            $table->string('username');
            $table->string('action');
            $table->text('action_details')->nullable();
            $table->timestamps();

            $table->foreign('username')->references('username')->on('users');
        });

        // Create the 'audit_logs' table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('table_name');
            $table->string('column_affected');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('changed_by');
            $table->string('operation')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('record_id');

            $table->foreign('changed_by')->references('username')->on('users');
        });

        // Create the 'otps' table
        Schema::create('otps', function (Blueprint $table) {
            $table->id('otp_id');
            $table->string('email');
            $table->string('otp_code', 6);
            $table->dateTime('generated_at');
            $table->dateTime('expires_at');
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });

        // Create the 'personal_access_tokens' table
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('otps');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('restaurant_orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('user_role_assignments');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
