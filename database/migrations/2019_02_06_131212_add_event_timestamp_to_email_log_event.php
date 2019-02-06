<?php

use Illuminate\Database\Migrations\Migration;

class AddEventTimestampToEmailLogEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_log_events', function ($table) {
            $table->dateTime('timestamp_at')->after('event')->nullable();
            $table->unsignedInteger('timestamp_secs')->after('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_log_events', function ($table) {
            $table->dropColumn(['timestamp_secs', 'timestamp_at']);
        });
    }
}
