<x-mail::message>
# Hola {{ $user->name }}

Gracias por crear una cuenta. Por favor, verifícala aquí:

<x-mail::button :url="route('verify', $user->verification_token)">
Confirmar Cuenta
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
