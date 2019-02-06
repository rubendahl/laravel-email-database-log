<?php

namespace Dmcbrn\LaravelEmailDatabaseLog;

use Illuminate\Database\Eloquent\Model;

class EmailLogEvent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_log_events';

    /**
     * Disable timestamps for the model.
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'messageId', 'event', 'data',
        'timestamp_at', 'timestamp_secs',
    ];

    /**
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
        'timestamp_at',
    ];

    /**
     * The Email to which this Event belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function email()
    {
        return $this->belongsTo('Dmcbrn\LaravelEmailDatabaseLog\EmailLog','messageId','id');
    }
}
