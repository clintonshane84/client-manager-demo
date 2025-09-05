@php($name = trim(($client->name ?? '').' '.($client->surname ?? '')))
<!doctype html>
<html>
  <body style="font-family: sans-serif;">
    <h2>Welcome{{ $name ? ', '.$name : '' }}!</h2>
    <p>Thanks for joining us. Your profile has been created successfully.</p>
    <p>If you didn’t expect this email, please ignore it.</p>
    <p style="color:#888">— The Team</p>
  </body>
</html>
