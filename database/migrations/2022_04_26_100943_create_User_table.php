<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class User extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        {{schema_up}}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        {{schema_down}}
    }
}
