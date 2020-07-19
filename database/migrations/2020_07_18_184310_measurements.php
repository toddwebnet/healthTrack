<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Measurements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurement_types', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('type_name');
            $table->unsignedInteger('decimals');
            $table->decimal('min');
            $table->decimal('max');
            $table->string('aggr');
            $table->timestamps();
        });
        $sqls = "
        insert into measurement_types (code, type_name, decimals, min, max, aggr) values ('sys', 'BP Systolic', 0, 20, 500, 'avg');
        insert into measurement_types (code, type_name, decimals, min, max, aggr) values ('dia', 'BP Diastolic', 0, 20, 500, 'avg');
        insert into measurement_types (code, type_name, decimals, min, max, aggr) values ('weight', 'Weight', 2, 20, 500, 'avg');
        insert into measurement_types (code, type_name, decimals, min, max, aggr) values ('hamster', 'Hamster Wheel Minutes', 2, 0, 250, 'sum');
        ";
        foreach (explode(';', $sqls) as $sql) {
            if (strlen(trim($sql)) > 0) {
                \Illuminate\Support\Facades\DB::insert($sql);
            }
        }


        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->bigInteger('value');
            $table->dateTime('ts');
            $table->timestamps();
            $table->foreign('type_id')->references('id')->on('measurement_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measurements');

        Schema::dropIfExists('measurement_types');
    }
}
