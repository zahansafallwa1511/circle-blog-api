<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->unique();
            $table->timestamps();
        });

        $sampleData = ['admin', 'author'];
        $data = [];
        foreach ($sampleData as $sample) {
            $data[] = [
                'role' => $sample,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        DB::table('roles')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
