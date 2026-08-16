<h1>Nieuw contactbericht</h1>
<p><strong>Van:</strong> {{ $contact->name }} ({{ $contact->email }})</p>
<p><strong>Type:</strong> {{ $contact->type === 'feedback' ? 'Feedback' : 'Vraag' }}</p>
<p><strong>Onderwerp:</strong> {{ $contact->subject }}</p>
<p><strong>Bericht:</strong><br>{{ $contact->message }}</p>
