<?php

namespace Dmcbrn\LaravelEmailDatabaseLog;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;

use Illuminate\Support\Facades\Storage;
use Dmcbrn\LaravelEmailDatabaseLog\LaravelEvents\EmailLogged;

class EmailLogger
{
    /**
     * Handle the event.
     *
     * @param MessageSent $event
     */
    public function handle(MessageSent $event)
    {
        try {
            $this->logMessageSentEvent($event);
        } catch (\Exception $e) {
            \Log::error("Email logging failed!");
            \Log::error($e);
        }
    }

    /**
     * Log mail sending event to database.
     *
     * @param MessageSent $event
     */
    function logMessageSentEvent(MessageSent $event)
    {
        $sent = $event->sent;
        $message = $sent->getOriginalMessage();
        $headers = $message->getHeaders();

        $msgIdStr = $sent->getMessageId();
        $messageId = trim(strtok($msgIdStr, '@'), '<');

        $attachments = [];
        foreach ($message->getAttachments() as $child) {
            //FIXME: docs for this below: http://phpdox.de/demo/Symfony2/classes/Swift_Mime_SimpleMimeEntity/getChildren.xhtml
            if(true) { // WAS:  in_array(get_class($child),['Swift_EmbeddedFile','Swift_Attachment'])) { // TODO: Filter out?
                $filename = $child->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename');
                if (config('email_log.save_attachments') == 'folder') {
                    $attachmentPath = config('email_log.disk') . '/' . $messageId . '/' . $filename;
                    Storage::disk(config('email_log.disk'))->put($attachmentPath, $child->bodyToString()); // FIXME: Encoded body...
                    $attachments[] = $attachmentPath;
                } else {
                    $attachments[] = $filename;
                }
            }
        }

        $user_id = null;
        if (config('email_log.store_user_id')) {
            // TODO: This will not work for queued mails.
            $user = \Auth::user();
            $user_id = $user ? $user->id : null;
        }

        $emailLog = EmailLog::create([
            'date' => date('Y-m-d H:i:s'),
            'from' => $this->formatAddressField($message, 'From'),
            'sender' => $this->formatAddressField($message, 'Sender'),
            'to' => $this->formatAddressField($message, 'To'),
            'cc' => $this->formatAddressField($message, 'Cc'),
            'bcc' => $this->formatAddressField($message, 'Bcc'),
            'reply_to' => $this->formatAddressField($message, 'Reply-To'),
            'subject' => $message->getSubject(),
            'body' => $message->getHtmlBody(),
            'headers' => $headers->toString(),
            'attachments' => empty($attachments) ? null : implode(', ', $attachments),
            'messageId' => $messageId,
            'mail_driver' => config('mail.driver'),
            'user_id' => $user_id ?? null,
        ]);

        event(new EmailLogged($emailLog));
    }

    /**
     * Format address strings for sender, to, cc, bcc.
     *
     * @param $message
     * @param $field
     * @return null|string
     */
    function formatAddressField($message, $field)
    {
        $headers = $message->getHeaders();

        if (!$headers->has($field)) {
            return null;
        }

        $mailboxes = $headers->get($field);

        $strings = $mailboxes->getAddressStrings();
        return implode(', ', $strings);
    }
}
