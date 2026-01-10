<h1>Nieuw contactbericht</h1>

<p><strong>Naam:</strong> {{ $message->name }}</p>
<p><strong>Email:</strong> {{ $message->email }}</p>

@if($message->subject)
    <p><strong>Onderwerp:</strong> {{ $message->subject }}</p>
@endif

<p><strong>Bericht:</strong></p>
<p>{{ $message->message }}</p>
