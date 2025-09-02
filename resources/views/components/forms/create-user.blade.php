{{-- resources/views/components/forms/create-user.blade.php --}}
@props([
    'languages' => [],
    'interests' => [],
    'action' => route('users.store'),
])

<div id="create-user-root">
  <create-user
    :languages='@json($languages)'
    :interests='@json($interests)'
    action="{{ $action }}"
  />
</div>

@vite('resources/js/app.js')