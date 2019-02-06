<?php

namespace Dmcbrn\LaravelEmailDatabaseLog\Events;

use Illuminate\Http\Request;
use Dmcbrn\LaravelEmailDatabaseLog\EmailLogEvent;

class MailgunEvent extends Event
{
    public function verify(Request $request)
    {
        //get needed data
        $apiKey = config('email_log.mailgun.secret', null);
        $token = $request->signature['token'];
        $timestamp = $request->signature['timestamp'];
        $signature = $request->signature['signature'];

        //check if the timestamp is fresh
        if (abs(time() - $timestamp) > 15)
            return false;

        //returns true if signature is valid
        return hash_hmac('sha256', $timestamp.$token, $apiKey) === $signature;
    }

    public function saveEvent(Request $request)
    {
        //get email
        $email = $this->getEmail(strtok($request->{'event-data'}['message']['headers']['message-id'], '@'));
        if(!$email)
            return response('Error: no E-mail found', 200)->header('Content-Type', 'text/plain');

        $event_data = $request->{'event-data'};

        //save event
        EmailLogEvent::create([
            'messageId' => $email->messageId,
            'event' => $event_data['event'],
            'timestamp_secs' => $event_data['timestamp'],
            'timestamp_at'   => $event_data['timestamp'],
            'data' => json_encode($request->all()),
        ]);

        //return success
        return response('Success', 200)->header('Content-Type', 'text/plain');
    }
}