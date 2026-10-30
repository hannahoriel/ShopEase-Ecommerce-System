<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Personal details live in the role-specific profile tables. */
    private array $profileColumns = [
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
    ];

    public function up(): void
    {
        $columns = array_values(array_filter(
            $this->profileColumns,
            fn (string $column): bool => Schema::hasColumn('users', $column),
        ));

        if ($columns !== []) {
            Schema::table('users', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        $definitions = [
            'first_name' => fn (Blueprint $table) => $table->string('first_name')->nullable(),
            'last_name' => fn (Blueprint $table) => $table->string('last_name')->nullable(),
            'middle_initial' => fn (Blueprint $table) => $table->string('middle_initial', 10)->nullable(),
            'sex' => fn (Blueprint $table) => $table->string('sex', 20)->nullable(),
            'contact_no' => fn (Blueprint $table) => $table->string('contact_no', 20)->nullable(),
            'birthday' => fn (Blueprint $table) => $table->date('birthday')->nullable(),
            'age' => fn (Blueprint $table) => $table->unsignedInteger('age')->nullable(),
            'province' => fn (Blueprint $table) => $table->string('province')->nullable(),
            'municipality' => fn (Blueprint $table) => $table->string('municipality')->nullable(),
            'barangay' => fn (Blueprint $table) => $table->string('barangay')->nullable(),
            'street' => fn (Blueprint $table) => $table->string('street')->nullable(),
            'house_number' => fn (Blueprint $table) => $table->string('house_number')->nullable(),
            'business_name' => fn (Blueprint $table) => $table->string('business_name')->nullable(),
            'line_of_business' => fn (Blueprint $table) => $table->string('line_of_business')->nullable(),
            'vehicle' => fn (Blueprint $table) => $table->string('vehicle')->nullable(),
            'plate_number' => fn (Blueprint $table) => $table->string('plate_number', 50)->nullable(),
            'upload_id' => fn (Blueprint $table) => $table->string('upload_id')->nullable(),
            'upload_business_permit' => fn (Blueprint $table) => $table->string('upload_business_permit')->nullable(),
            'upload_or_cr' => fn (Blueprint $table) => $table->string('upload_or_cr')->nullable(),
            'upload_id_license' => fn (Blueprint $table) => $table->string('upload_id_license')->nullable(),
        ];

        foreach ($definitions as $column => $definition) {
            if (! Schema::hasColumn('users', $column)) {
                Schema::table('users', $definition);
            }
        }
    }
};
