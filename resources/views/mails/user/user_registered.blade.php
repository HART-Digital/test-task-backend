@component('mail::message')

#### Ваши данные для входа на сайт <a target="_blank" href="{{ url('/') }}">{{ url('/') }}</a>

---

```
Email: {{ $email }}
Password: {{ $password }}
```

@endcomponent
