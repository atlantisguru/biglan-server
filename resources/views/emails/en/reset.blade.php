Dear {{ $username }}!<br><br>

A request to change a forgotten password has been received in the BigLan system.<br>
If you really want to change your password, click on the link below or open it in your browser:<br>
<a href="{{ url('/resetpassword/' . $token) }}" target='blank'>{{ url('/resetpassword/' . $token) }}</a><br><br>

BigLan