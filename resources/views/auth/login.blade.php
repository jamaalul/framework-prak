<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="left">
    <div class="login-box">
      <h1>Sign in to your account</h1>
      <form>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="••••••••" required>
        </div>
        <button type="submit">Login</button>
      </form>
      <p class="footer-text">Tidak punya akun? <a href="#">Daftar</a></p>
    </div>
  </div>

  <div class="right">
    <div class="brand">
      <h2>Praktikum Framework</h2>
      <p>Suwe suwe gendeng kabeh.</p>
    </div>
  </div>
</body>
</html>
