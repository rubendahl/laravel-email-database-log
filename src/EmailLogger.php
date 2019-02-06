<?php

namespace Dmcbrn\LaravelEmailDatabaseLog;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Storage;

class EmailLogger
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
        try {
            $this->logMessageSendingEvent($event);
        } catch (\Exception $e) {
            \Log::error("Email logging failed!");
            \Log::error($e);
        }
    }

    /**
     * Log mail sending event to database.
     *
     * @param MessageSending $event
     */
    function logMessageSendingEvent(MessageSending $event)
    {
        $message = $event->message;

        $messageId = strtok($message->getId(), '@');

        $attachments = [];
        foreach ($message->getChildren() as $child) {
            //docs for this below: http://phpdox.de/demo/Symfony2/classes/Swift_Mime_SimpleMimeEntity/getChildren.xhtml
            if(in_array(get_class($child),['Swift_EmbeddedFile','Swift_Attachment'])) {
                if (config('email_log.save_attachments') == 'folder') {
                    $attachmentPath = config('email_log.folder') . '/' . $messageId . '/' . $child->getFilename();
                    Storage::put($attachmentPath, $child->getBody());
                    $attachments[] = $attachmentPath;
                } else {
                    $attachments[] = $child->getFilename();
                }
            }
        }

        $user_id = null;
        if (config('email_log.store_user_id')) {
            // TODO: This will not work for queued mails.
            $user = \Auth::user();
            $user_id = $user ? $user->id : null;
        }

        EmailLog::create([
            'date' => date('Y-m-d H:i:s'),
            'from' => $this->formatAddressField($message, 'From'),
            'sender' => $this->formatAddressField($message, 'Sender'),
            'to' => $this->formatAddressField($message, 'To'),
            'cc' => $this->formatAddressField($message, 'Cc'),
            'bcc' => $this->formatAddressField($message, 'Bcc'),
            'reply_to' => $this->formatAddressField($message, 'Reply-To'),
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'headers' => (string)$message->getHeaders(),
            'attachments' => empty($attachments) ? null : implode(', ', $attachments),
            'messageId' => $messageId,
            'mail_driver' => config('mail.driver'),
            'user_id' => $user_id ?? null,
        ]);
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

        $mailboxes = $headers->get($field)->getFieldBodyModel();

        $strings = [];
        foreach ($mailboxes as $email => $name) {
            $mailboxStr = $email;
            if (null !== $name) {
                $mailboxStr = $name . ' <' . $mailboxStr . '>';
            }
            $strings[] = $mailboxStr;
        }
        return implode(', ', $strings);
    }
}
