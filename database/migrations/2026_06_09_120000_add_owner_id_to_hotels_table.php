<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('owner_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('hotels')->orderBy('id')->each(function (object $hotel): void {
            $ownerId = DB::table('workers')
                ->where('hotel_id', $hotel->id)
                ->orderBy('worker_id')
                ->value('worker_id');

            if ($ownerId !== null) {
                DB::table('hotels')
                    ->where('id', $hotel->id)
                    ->update(['owner_id' => $ownerId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });
    }
};
