<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h1>You've been invited to join {{ $team->name }}</h1>

    <p>Click the link below to accept the invitation:</p>

    <p><a href="{{ $acceptUrl }}">Accept Invitation</a></p>

    <p>This invitation will expire in 7 days.</p>
</body>
</html>
