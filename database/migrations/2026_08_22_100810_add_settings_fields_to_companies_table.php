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
        Schema::table('companies', function (Blueprint $table) {

            $table->string('phone', 30)
                ->nullable()
                ->after('email');

            $table->string('mobile', 30)
                ->nullable()
                ->after('phone');

            $table->string('website')
                ->nullable()
                ->after('address');

            $table->string('logo')
                ->nullable()
                ->after('website');

            $table->string('tax_number', 100)
                ->nullable()
                ->after('logo');

            $table->string('registration_number', 100)
                ->nullable()
                ->after('tax_number');

            $table->string('contact_person', 150)
                ->nullable()
                ->after('registration_number');

            $table->string('currency', 10)
                ->default('BDT')
                ->after('contact_person');

            $table->string('timezone', 100)
                ->default('Asia/Dhaka')
                ->after('currency');

            $table->text('footer_text')
                ->nullable()
                ->after('timezone');

            $table->boolean('status')
                ->default(true)
                ->after('footer_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->dropColumn([
                'phone',
                'mobile',
                'website',
                'logo',
                'tax_number',
                'registration_number',
                'contact_person',
                'currency',
                'timezone',
                'footer_text',
                'status',
            ]);
        });
    }
};