<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->decimal('note', 5, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->integer('note')->nullable()->change();
        });
    }
};
