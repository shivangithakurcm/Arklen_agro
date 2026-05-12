<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login — Arklen Agro</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

 /* NAYA */
body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f5ea;
}

.wrap {
    display: flex;
    width: 100%;
    height: 100vh;       /* ← ADD KARO */
    overflow: hidden;
}

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(40px) scale(0.96); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  /* ── Left Panel ── */
  .left-panel {
    flex: 1;
    background: #1a3a07;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    position: relative;
    overflow: hidden;
  }

  .leaf {
    position: absolute;
    opacity: 0.12;
    font-size: 2.5rem;
    animation: leafFloat linear infinite;
  }
  .l1 { left: 10%; animation-duration: 8s;  animation-delay: 0s; font-size: 2rem; }
  .l2 { left: 30%; animation-duration: 11s; animation-delay: 2s; font-size: 1.5rem; }
  .l3 { left: 55%; animation-duration: 9s;  animation-delay: 4s; font-size: 3rem; }
  .l4 { left: 75%; animation-duration: 12s; animation-delay: 1s; font-size: 1.8rem; }
  .l5 { left: 85%; animation-duration: 7s;  animation-delay: 3s; font-size: 1.2rem; }

  @keyframes leafFloat {
    0%   { transform: translateY(110%) rotate(0deg);   opacity: 0.12; }
    100% { transform: translateY(-110%) rotate(360deg); opacity: 0; }
  }

  .brand-circle {
    width: 90px; height: 90px;
    border-radius: 50%;
    border: 2.5px solid rgba(255,255,255,0.3);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.2rem;
    position: relative;
    animation: pulseRing 2.5s ease-in-out infinite;
  }
  .brand-circle::after {
    content: '';
    position: absolute; inset: -8px;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,0.1);
    animation: pulseRing 2.5s ease-in-out infinite reverse;
  }
  @keyframes pulseRing {
    0%,100% { transform: scale(1); }
    50%     { transform: scale(1.05); }
  }

  .brand-inner {
    width: 70px; height: 70px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
  }
  .brand-inner img { width: 48px; height: 48px; object-fit: contain; }

  .left-title { color: #fff; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 6px; letter-spacing: -.3px; }
  .left-sub   { color: rgba(255,255,255,0.5); font-size: 11px; letter-spacing: 1.5px; text-align: center; text-transform: uppercase; }

  .stats { display: flex; gap: 1.5rem; margin-top: 2rem; }
  .stat  { text-align: center; }
  .stat-num { color: #7ab648; font-size: 22px; font-weight: 700; }
  .stat-lbl { color: rgba(255,255,255,0.4); font-size: 10px; letter-spacing: .5px; margin-top: 2px; }
  .divline  { width: 1px; background: rgba(255,255,255,0.15); align-self: stretch; }

  /* ── Right Panel ── */
  .right-panel {
    width: 420px;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2.5rem 2rem;
  }

  .welcome     { font-size: 22px; font-weight: 700; color: #1a3a07; margin-bottom: 4px; }
  .welcome-sub { font-size: 12px; color: #888; margin-bottom: 2rem; }

  .field { margin-bottom: 1rem; }
  .field label {
    display: block; font-size: 11px; font-weight: 600;
    color: #4a6a2c; margin-bottom: 5px;
    letter-spacing: .5px; text-transform: uppercase;
  }

  .iw { position: relative; display: flex; align-items: center; }
  .iw > i { position: absolute; left: 12px; font-size: 14px; color: #9aba70; pointer-events: none; transition: color .2s; }
  .iw:focus-within > i { color: #2d5c0e; }

  .inp {
    width: 100%; padding: 11px 40px 11px 38px;
    border: 1.5px solid #e0eecf; border-radius: 10px;
    font-family: 'Sora', sans-serif; font-size: 13px;
    color: #1a3a07; background: #f8fdf2;
    transition: all .2s; outline: none;
  }
  .inp:focus { border-color: #7ab648; background: #fff; box-shadow: 0 0 0 3px rgba(122,182,72,.15); }
  .inp::placeholder { color: #c5d9a5; }

  .eye {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: #b0c98a; font-size: 14px; transition: color .2s; padding: 4px;
  }
  .eye:hover { color: #2d5c0e; }

  .progress { height: 2px; background: #e0eecf; border-radius: 2px; margin-top: 5px; overflow: hidden; }
  .progress-bar { height: 100%; background: #7ab648; width: 0%; transition: width .3s; border-radius: 2px; }
  .strength-lbl { font-size: 10px; color: #9aba70; margin-top: 3px; min-height: 14px; }

  .btn {
    width: 100%; margin-top: 1.2rem; padding: 13px;
    background: #1a3a07; color: #fff; border: none; border-radius: 10px;
    font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    gap: 8px; transition: all .25s; position: relative; overflow: hidden;
  }
  .btn:hover  { background: #2d5c0e; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(26,58,7,.3); }
  .btn:active { transform: scale(0.98); box-shadow: none; }

  .btn .loader {
    display: none; width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff; border-radius: 50%;
    animation: spin .6s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  .alert { padding: 9px 12px; border-radius: 9px; font-size: 12px; margin-bottom: 1rem; display: flex; align-items: center; gap: 7px; }
  .alert-error   { background: #fdf0f0; color: #a32d2d; border: 1.5px solid #f5c2c2; animation: shake .4s ease; }
  .alert-success { background: #f2fae8; color: #2d5c0e; border: 1.5px solid #c6e89a; }
  @keyframes shake {
    0%,100% { transform: translateX(0); }
    20%,60% { transform: translateX(-5px); }
    40%,80% { transform: translateX(5px); }
  }

  .typing-dots { display: inline-flex; gap: 3px; align-items: center; }
  .typing-dots span { width: 5px; height: 5px; background: #7ab648; border-radius: 50%; animation: dot .8s ease-in-out infinite; }
  .typing-dots span:nth-child(2) { animation-delay: .15s; }
  .typing-dots span:nth-child(3) { animation-delay: .3s; }
  @keyframes dot {
    0%,80%,100% { transform: scale(0.6); opacity: .5; }
    40%         { transform: scale(1);   opacity: 1; }
  }

  .footer { margin-top: 1.5rem; text-align: center; font-size: 11px; color: #bbb; }

  @media (max-width: 560px) {
    .left-panel { display: none; }
    .right-panel { width: 100%; border-radius: 20px; }
  }
</style>
</head>
<body>

<div class="wrap">

  {{-- ── Left Panel ── --}}
  <div class="left-panel">
    <div class="leaf l1">🌿</div>
    <div class="leaf l2">🍃</div>
    <div class="leaf l3">🌱</div>
    <div class="leaf l4">🍀</div>
    <div class="leaf l5">🌿</div>

    <div class="brand-circle">
      <div class="brand-inner">
        <img src="{{ asset('images/arklen-logo.png') }}" alt="Arklen Agro Logo"/>
      </div>
    </div>

    <div class="left-title">Arklen Agro</div>
    <div class="left-sub">Pvt. Ltd &bull; Seller Portal</div>

    <div class="stats">
      <div class="stat">
        <div class="stat-num" id="c1">0</div>
        <div class="stat-lbl">Members</div>
      </div>
      <div class="divline"></div>
      <div class="stat">
        <div class="stat-num" id="c2">0</div>
        <div class="stat-lbl">Networks</div>
      </div>
      <div class="divline"></div>
      <div class="stat">
        <div class="stat-num" id="c3">0</div>
        <div class="stat-lbl">Districts</div>
      </div>
    </div>
  </div>

  {{-- ── Right Panel ── --}}
  <div class="right-panel">
    <div class="welcome">Welcome back</div>
    <div class="welcome-sub">Sign in to your seller account</div>

    {{-- Error --}}
    @if($errors->any())
      <div class="alert alert-error">
        <i class="fas fa-times-circle"></i>
        {{ $errors->first() }}
      </div>
    @endif

    {{-- Success --}}
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
        <div class="iw">
          <i class="fas fa-id-badge"></i>
          <input class="inp" type="text" name="user_id"
            placeholder="e.g. SL0001"
            value="{{ old('user_id') }}" required autocomplete="off"/>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="iw">
          <i class="fas fa-lock"></i>
          <input class="inp" type="password" name="password" id="pwdField"
            placeholder="Enter password" required oninput="checkStrength()"/>
          <button type="button" class="eye" onclick="togglePwd()" aria-label="Toggle password">
            <i class="fas fa-eye" id="eyeIcon"></i>
          </button>
        </div>
        <div class="progress"><div class="progress-bar" id="pbar"></div></div>
        <div class="strength-lbl" id="slbl"></div>
      </div>

      <button type="submit" class="btn" id="loginBtn">
        <span id="btnTxt"><i class="fas fa-sign-in-alt"></i> Sign In</span>
        <span class="loader" id="btnLoader"></span>
      </button>

    </form>

    <div class="footer">Growing Together, Prospering Together 🌱</div>
  </div>

</div>

<script>
  function togglePwd() {
    const f = document.getElementById('pwdField');
    const i = document.getElementById('eyeIcon');
    if (f.type === 'password') { f.type = 'text';     i.classList.replace('fa-eye','fa-eye-slash'); }
    else                       { f.type = 'password'; i.classList.replace('fa-eye-slash','fa-eye'); }
  }

  function checkStrength() {
    const v   = document.getElementById('pwdField').value;
    const bar = document.getElementById('pbar');
    const lbl = document.getElementById('slbl');
    if (!v) { bar.style.width = '0%'; lbl.textContent = ''; return; }
    let s = 0;
    if (v.length >= 4) s++;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const pct  = [0,20,40,60,80,100][s];
    const cols = ['#e24b4a','#e24b4a','#ef9f27','#7ab648','#3b6d11'];
    const lbls = ['','Weak','Fair','Good','Strong','Very strong'];
    bar.style.width      = pct + '%';
    bar.style.background = cols[s-1] || '#e24b4a';
    lbl.textContent      = lbls[s];
    lbl.style.color      = cols[s-1] || '#e24b4a';
  }

  document.getElementById('loginBtn').addEventListener('click', function() {
    document.getElementById('btnTxt').style.display    = 'none';
    document.getElementById('btnLoader').style.display = 'inline-block';
  });

  function countUp(id, target, dur) {
    const el = document.getElementById(id);
    let start = 0;
    const interval = setInterval(() => {
      start++;
      el.textContent = start + (id === 'c3' ? '' : '+');
      if (start >= target) clearInterval(interval);
    }, dur / target);
  }

  setTimeout(() => {
    countUp('c1', 500, 1200);
    countUp('c2', 120, 1000);
    countUp('c3', 18,  800);
  }, 400);
</script>

</body>
</html>