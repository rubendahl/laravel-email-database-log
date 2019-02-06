<?php

use Illuminate\Database\Migrations\Migration;

class AddUserIdToEmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_log', function ($table) {
            $table->unsignedInteger('user_id')->after('sender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_log', function ($table) {
            $table->dropColumn(['user_id']);
        });
    }
}
