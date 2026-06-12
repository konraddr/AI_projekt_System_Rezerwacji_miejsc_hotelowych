<?php

use App\Enums\HotelWorkerAccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('hotel_id');
        });

        DB::table('workers')->update([
            'permissions' => json_encode(HotelWorkerAccess::values()),
        ]);
    }

    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
