<?php

use Illuminate\Database\Migrations\Migration;

class AddSpecificMailHeadersEmailLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_log', function ($table) {
            $table->string('sender')->after('from')->nullable();
            $table->string('reply_to')->after('bcc')->nullable();
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
            $table->dropColumn(['sender', 'reply_to' ]);
        });
    }
}
