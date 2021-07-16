@component('mail::message')

#### Ссылка для восстановления входа на сайт <a target="_blank" href="{{ url('/') }}">{{ url('/') }}</a>

@component('mail::button', ['url' => $link])
Восстановить пароль
@endcomponent

@endcomponent
