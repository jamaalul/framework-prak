<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  @vite('resources/css/app.css')
  @fluxAppearance
</head>
<body class="bg-white dark:bg-zinc-800">
  <div class="flex min-h-screen">
    <div class="flex-1 flex flex-col justify-center items-center p-12">
    <flux:sidebar.brand
      href="#"
      logo="https://upload.wikimedia.org/wikipedia/en/9/96/Meme_Man_on_transparent_background.webp"
      name="FrameWOK"
      class="absolute left-4 top-4"
    />
    <div class="w-full max-w-96">
      <flux:heading size="3xl" class="mb-6 text-center">Sign in to your account</flux:heading>
      <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <flux:field>
          <flux:label>Email</flux:label>
          <flux:input type="email" name="email" placeholder="you@example.com" required />
        </flux:field>
        <flux:field>
          <flux:label>Password</flux:label>
          <flux:input type="password" name="password" placeholder="••••••••" required />
        </flux:field>
        @if ($errors)
          <div class="mb-4">
            @foreach ($errors->all() as $error)
              <flux:error>{{ $error }}</flux:error>
            @endforeach
          </div>
        @endif
        <flux:button type="submit" variant="primary" class="w-full mt-2">Login</flux:button>
      </form>
      <p class="text-center mt-4 text-sm text-gray-500">Tidak punya akun? <a href="#" class="text-black dark:text-white hover:underline">Daftar</a></p>
    </div>
  </div>

    <div class="flex-1 h-48 md:h-auto flex items-center justify-center bg-cover bg-center text-white relative overflow-hidden" style="background-image: url(https://images.unsplash.com/photo-1556997685-309989c1aa82?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1473);">
      <div class="relative z-10 text-center">
        <flux:heading size="4xl" class="mb-2">Praktikum Framework</flux:heading>
        <flux:subheading class="opacity-85">Suwe suwe gendeng kabeh.</flux:subheading>
      </div>
    </div>
  </div>
  @fluxScripts
</body>
</html>
