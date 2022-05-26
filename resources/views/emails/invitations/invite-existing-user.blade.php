@component('mail::message')
# Здравствуйте!
Вы были приглашены в команду
**{{ $invitation->team->name }}**
У Вас уже есть аккаунт, вы сможете принять или отклонить приглашение из вашего профиля.
[Настройки профиля]({{ $url }}),

The body of your message.

@component('mail::button', ['url' => $url])
На панель управления
@endcomponent

Спасибо,<br>
{{ config('app.name') }}
@endcomponent
