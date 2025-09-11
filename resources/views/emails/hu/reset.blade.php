Kedves {{ $username }}!<br><br>

Elfelejtett jelszó cseréjéhez kérelem érkezett a BigLan rendszerben.<br>
Ha valóban szeretnéd cserélni a jelszavad, akkor kattints az alábbi hivatkozásra, vagy nyisd meg a böngésződben:<br>
<a href="{{ url('/resetpassword/' . $token) }}" target='blank'>{{ url('/resetpassword/' . $token) }}</a><br><br>
	
BigLan
