<x-mail::message>
# Hola {{ $user->name }}

Has cambiado tu correo, por favor confírmalo en el siguiente enlace:

<x-mail::button :url="route('verify', $user->verification_token)">
Confirmar Cuenta
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
