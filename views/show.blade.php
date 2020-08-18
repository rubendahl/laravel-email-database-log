<!DOCTYPE html>
<html>
    <head>
        <title>Emails Log</title>
    </head>

    <body style="background: white;">
        <h1>Email:</h1>

        <ul>
            <li>{{ $email->date }}</li>
            <li>From: {{ $email->from }}</li>
            <li>To: {{ $email->to }}</li>
            <li>Subject: {{ $email->subject }}</li>
            <li>Body: <br>
                <div>{!! $email->body !!}</div>
            </li>
            <li>Attachments:
                @if(count($attachmentsArray = array_filter(explode(',',$email->attachments))) > 0)
                    <ul>
                        @foreach($attachmentsArray as $key => $attachment)
                            <li>
                            @if(config('email_log.save_attachments') == 'folder')
                                @if(Illuminate\Support\Facades\Storage::exists($attachment))
                                    <a href="{{ route('email-log.fetch-attachment', [
                                        'id' => $email->id,
                                        'attachment' => $attachment,
                                    ]) }}">{{ basename($attachment) }}</a>
                                @else
                                    <a href="#!" style="cursor: not-allowed;">{{ basename($attachment) }} - File Not Found</a>
                                @endif
                            @else
                                <span>{{ basename($attachment) }}</span>
                            @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    NONE
                @endif
            </li>
            <li>Headers: {{ $email->headers }}</li>
            <li>Message ID: {{ $email->messageId }}</li>
            <li>Mail Driver: {{ $email->mail_driver }}</li>
            <li>Events:
                <ul>
                    @foreach($email->events as $event)
                        <li><strong>{{ $event->event }}</strong> {{ $event->created_at }}</li>
                    @endforeach
                </ul>
            </li>
        </ul>

        <a href="{{ route('email-log') }}">Back to All</a>
    </body>
</html>