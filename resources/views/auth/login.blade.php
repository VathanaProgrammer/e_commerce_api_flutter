<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
</head>

<body class="vh-100 d-flex justify-content-center align-items-center bg-light">

    <form method="POST" action="{{ route('login') }}" class="bg-white p-4 rounded shadow" style="width:360px;">
        @csrf

        <h4 class="text-center mb-4">Sign in</h4>

        <!-- Username -->
        <div class="form-outline mb-4">
            <input type="text" name="username" id="username" class="form-control" required />
            <label class="form-label" for="username">Username</label>
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" name="password" id="password" class="form-control" required
                autocomplete="current-password" />
            <label class="form-label" for="password">Password</label>
        </div>

        <!-- Remember -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" />
            <label class="form-check-label" for="remember"> Remember me </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-3">
            Sign in
        </button>
    </form>

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- MDB JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#loginForm').on('submit', function(e) {
                e.preventDefault(); // 🔒 stop page reload

                $('#loginError').addClass('d-none').text('');

                $.ajax({
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        username: $('#username').val(),
                        password: $('#password').val(),
                        remember: $('#remember').is(':checked')
                    },
                    success: function() {
                        // ✅ login success → redirect
                        window.location.href = "/";
                    },
                    error: function(xhr) {
                        // ❌ login failed
                        let msg = 'Login failed';

                        if (xhr.status === 422 || xhr.status === 401) {
                            msg = xhr.responseJSON?.message ?? 'Invalid credentials';
                        }

                        $('#loginError').removeClass('d-none').text(msg);
                    }
                });
            });

        });
    </script>

</body>

</html>
