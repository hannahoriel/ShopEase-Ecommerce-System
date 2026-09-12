<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('buyer')->after('email');
            });
        }

        $columnDefinitions = [
            'first_name' => fn (Blueprint $table) => $table->string('first_name', 255)->nullable(),
            'last_name' => fn (Blueprint $table) => $table->string('last_name', 255)->nullable(),
            'middle_initial' => fn (Blueprint $table) => $table->string('middle_initial', 10)->nullable(),
            'sex' => fn (Blueprint $table) => $table->string('sex', 20)->nullable(),
            'contact_no' => fn (Blueprint $table) => $table->string('contact_no', 20)->nullable(),
            'birthday' => fn (Blueprint $table) => $table->date('birthday')->nullable(),
            'age' => fn (Blueprint $table) => $table->unsignedInteger('age')->nullable(),
            'province' => fn (Blueprint $table) => $table->string('province', 255)->nullable(),
            'municipality' => fn (Blueprint $table) => $table->string('municipality', 255)->nullable(),
            'barangay' => fn (Blueprint $table) => $table->string('barangay', 255)->nullable(),
            'street' => fn (Blueprint $table) => $table->string('street', 255)->nullable(),
            'house_number' => fn (Blueprint $table) => $table->string('house_number', 255)->nullable(),
            'business_name' => fn (Blueprint $table) => $table->string('business_name', 255)->nullable(),
            'line_of_business' => fn (Blueprint $table) => $table->string('line_of_business', 255)->nullable(),
            'vehicle' => fn (Blueprint $table) => $table->string('vehicle', 255)->nullable(),
            'plate_number' => fn (Blueprint $table) => $table->string('plate_number', 50)->nullable(),
            'upload_id' => fn (Blueprint $table) => $table->string('upload_id', 255)->nullable(),
            'upload_business_permit' => fn (Blueprint $table) => $table->string('upload_business_permit', 255)->nullable(),
            'upload_or_cr' => fn (Blueprint $table) => $table->string('upload_or_cr', 255)->nullable(),
            'upload_id_license' => fn (Blueprint $table) => $table->string('upload_id_license', 255)->nullable(),
            'registration_status' => fn (Blueprint $table) => $table->string('registration_status', 20)->default('pending'),
            'approved_at' => fn (Blueprint $table) => $table->timestamp('approved_at')->nullable(),
            'rejected_at' => fn (Blueprint $table) => $table->timestamp('rejected_at')->nullable(),
        ];

        Schema::table('users', function (Blueprint $table) use ($columnDefinitions) {
            foreach ($columnDefinitions as $columnName => $callback) {
                if (! Schema::hasColumn('users', $columnName)) {
                    $callback($table);
                }
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'first_name',
            'last_name',
            'middle_initial',
            'sex',
            'contact_no',
            'birthday',
            'age',
            'province',
            'municipality',
            'barangay',
            'street',
            'house_number',
            'business_name',
            'line_of_business',
            'vehicle',
            'plate_number',
            'upload_id',
            'upload_business_permit',
            'upload_or_cr',
            'upload_id_license',
            'registration_status',
            'approved_at',
            'rejected_at',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
