<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login — 2APL Marketing</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Sora', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f5ea;
    padding: 1rem;
  }
  .card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.10);
    width: 100%;
    max-width: 400px;
    padding: 2.2rem 2rem 1.8rem;
    animation: fadeUp .4s ease both;
  }
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .brand {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.6rem;
  }
  .brand img { width: 80px; height: 80px; object-fit: contain; }
  .brand-name { font-size: 17px; font-weight: 700; color: #1a3a07; letter-spacing: -.3px; }
  .divider { height: 1px; background: #e0eecf; margin-bottom: 1.5rem; }
  .field { margin-bottom: 1rem; }
  .field label { display: block; font-size: 11.5px; font-weight: 600; color: #4a6a2c; margin-bottom: 5px; letter-spacing: .3px; }
  .input-wrap { position: relative; display: flex; align-items: center; }
  .input-wrap > i { position: absolute; left: 12px; color: #9aba70; font-size: 13px; pointer-events: none; z-index: 2; }
  .input-wrap input {
    width: 100%;
    padding: 11px 42px 11px 36px;
    border: 1.5px solid #d6e8c0;
    border-radius: 10px;
    font-family: 'Sora', sans-serif;
    font-size: 13px;
    color: #1a3a07;
    background: #f8fdf2;
    transition: border-color .15s, box-shadow .15s, background .15s;
  }
  .input-wrap input:focus {
    outline: none;
    border-color: #7ab648;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(122,182,72,.13);
  }
  .input-wrap input::placeholder { color: #c0d4a0; }
  .eye-btn {
    position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #b0c98a;
    font-size: 14px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 2;
  }
  .eye-btn:hover { color: #4a8a1e; }
  .btn-login {
    width: 100%; margin-top: 1.2rem; padding: 12px;
    background: #2d5c0e; color: #fff; border: none; border-radius: 10px;
    font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    gap: 8px; box-shadow: 0 4px 14px rgba(45,92,14,.28);
    transition: background .15s, box-shadow .15s, transform .12s;
  }
  .btn-login:hover { background: #3b7012; box-shadow: 0 6px 20px rgba(45,92,14,.35); transform: translateY(-1px); }
  .alert { padding: 10px 13px; border-radius: 9px; font-size: 12.5px; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
  .alert-error { background: #fdf0f0; color: #a32d2d; border: 1.5px solid #f5c2c2; }
  .alert-success { background: #f2fae8; color: #2d5c0e; border: 1.5px solid #c6e89a; }
</style>
</head>
<body>
<div class="card">
  <div class="brand">
    <img src="{{ asset('images/2apl_logo.svg') }}" alt="2APL Marketing Logo"/>
    <div class="brand-name">2APL Marketing</div>
  </div>
  <div class="divider"></div>

  @if($errors->any())
    <div class="alert alert-error">
      <i class="fas fa-times-circle"></i>
      {{ $errors->first() }}
    </div>
  @endif

  @if(session('success'))
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="field">
      <label>Seller ID</label>
      <div class="input-wrap">
        <i class="fas fa-id-badge"></i>
        <input type="text" name="user_id" placeholder="Enter your Seller ID"
          value="{{ old('user_id') }}" required autocomplete="off"/>
      </div>
    </div>
    <div class="field">
      <label>Password</label>
      <div class="input-wrap">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="pwdField" placeholder="Enter password" required/>
        <button type="button" class="eye-btn" onclick="togglePwd()">
          <i class="fas fa-eye" id="eyeIcon"></i>
        </button>
      </div>
    </div>
    <button type="submit" class="btn-login">
      <i class="fas fa-sign-in-alt"></i> Login
    </button>
  </form>
</div>
<script>
  function togglePwd() {
    const f = document.getElementById('pwdField');
    const i = document.getElementById('eyeIcon');
    if (f.type === 'password') { f.type = 'text'; i.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { f.type = 'password'; i.classList.replace('fa-eye-slash', 'fa-eye'); }
  }
</script>
</body>
</html>