<h1>Email Verification Mail</h1>
<a class="nav-brand" href="{{ url('/') }}">
	<img src="https://africacdc.org/wp-content/uploads/2020/02/Africa-CDC-logo.png" class="logo" alt="" style="width:280px;" />
</a>
<br>
  
Please verify your email with bellow link:
<br> 
<a href="{{ route('account_verify')}}?t={{$token}}">Verify Email</a>