@component('mail::message')
# Здравствуйте!

Вы были приглашены в команду
**{{ $invitation->team->name }}**

У Вас ещё нет аккаунта.
[Присоединяйтесь!]({{ $url }}),

Вы сможете принять или отклонить приглашение из вашего профиля.

@component('mail::button', ['url' => $url])
Зарегистрироваться
@endcomponent

Спасибо,<br>
{{ config('app.name') }}
@endcomponent
