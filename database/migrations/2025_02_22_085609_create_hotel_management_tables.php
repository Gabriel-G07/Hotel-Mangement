<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('phone_number', 15);
            $table->string('national_id_number')->unique();
            $table->string('profile_picture')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->string('password');

        });

        // User Roles Table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // User Role Assignments Table
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

            // Foreign key constraints
            $table->foreign('username')->references('username')->on('users');
            $table->foreign('role_id')->references('role_id')->on('user_roles');
            $table->foreign('assigned_by')->references('username')->on('users');
            $table->foreign('verified_by')->references('username')->on('users');
        });

        // Currencies Table (must be created first)
        Schema::create('currencies', function (Blueprint $table) {
            $table->id('currency_id');
            $table->string('currency_code')->unique();
            $table->string('currency_name');
            $table->decimal('exchange_rate', 10, 2);
            $table->boolean('is_base_currency')->default(false);
            $table->timestamps();
        });

        // Room Types Table
        Schema::create('room_types', function (Blueprint $table) {
            $table->id('room_type_id');
            $table->string('room_type_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Rooms Table
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

        // Bookings Table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('room_id');
            $table->string('guest_id'); // Matches the 'username' data type
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->decimal('total_price', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->enum('booking_status', ['Pending', 'Confirmed', 'Cancelled'])->default('Pending');
            $table->timestamps();

            $table->foreign('room_id')->references('room_id')->on('rooms');
            $table->foreign('guest_id')->references('username')->on('users');
            $table->foreign('currency_id')->references('currency_id')->on('currencies');
        });

        // Payment Methods Table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->string('payment_method_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Payments Table
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

        // Menu Items Table
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

        // Restaurant Orders Table
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

        // Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('order_id')->on('restaurant_orders')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('menu_items')->onDelete('cascade');
            $table->foreign('currency_id')->references('currency_id')->on('currencies')->onDelete('cascade');
        });

        // Reports Table
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

        // User Activity Logs Table
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id('activity_id');
            $table->string('username'); // Matches the 'username' data type
            $table->string('action');
            $table->text('action_details')->nullable();
            $table->timestamps();

            $table->foreign('username')->references('username')->on('users');
        });

        // Sessions Table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Primary key
            $table->string('user_id')->nullable()->index(); // Use 'string' to match 'username'
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('username')->on('users');
        });

        // Audit Logs Table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->string('table_name');
            $table->unsignedBigInteger('record_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('changed_by'); // Matches the 'username' data type
            $table->timestamps();

            $table->foreign('changed_by')->references('username')->on('users');
        });

        // OTPs Table
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
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('otps');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('sessions');
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
    }
};
