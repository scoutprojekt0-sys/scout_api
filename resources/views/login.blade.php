<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NextScout | Uye Girisi</title>
  <style>
    :root {
      --bg: #2c2c2c;
      --text: #f5f5f5;
      --muted: #c3c3c3;
      --line: #d6d6d6;
      --card: rgba(44, 44, 44, 0.95);
      --red: #df1f36;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background: radial-gradient(circle at 10% 10%, #2c2c2c 0%, transparent 34%), var(--bg);
      color: var(--text);
      font-family: "Segoe UI", Tahoma, sans-serif;
      padding: 0 24px 24px;
    }

    .page {
      width: min(1100px, 92%);
      margin: 0 auto;
      padding-top: 86px;
      min-height: 160vh;
    }

    .login-shell {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 52vh;
    }

    .card {
      width: min(420px, 96vw);
      max-height: 52vh;
      overflow-y: auto;
      border: 1px solid var(--line);
      border-radius: 14px;
      background: var(--card);
      padding: 18px;
      display: grid;
      gap: 12px;
    }

    h1 {
      margin: 2px 0 0;
      font-size: 1.3rem;
      text-align: center;
    }

    p {
      margin: 0;
      color: var(--muted);
      font-size: 0.9rem;
    }

    .field {
      display: grid;
      gap: 6px;
    }

    label {
      font-size: 0.84rem;
      color: #f1f1f1;
      font-weight: 600;
    }

    input {
      width: 100%;
      border: 1px solid #bfbfbf;
      border-radius: 8px;
      background: #ffffff;
      color: #252525;
      padding: 10px;
      font-size: 0.9rem;
      outline: 0;
    }

    .btn {
      border: 1px solid #d6d6d6;
      border-radius: 8px;
      padding: 9px 12px;
      font-weight: 800;
      font-family: "Arial Black", "Segoe UI", sans-serif;
      letter-spacing: 0.01em;
      text-decoration: none;
      cursor: pointer;
      font-size: 0.78rem;
    }

    .btn-primary {
      background: var(--red);
      color: #ffffff;
      border-color: var(--red);
    }

    .scroll-space {
      height: 80vh;
    }

  </style>
</head>
<body>
  <div class="page">
    <div class="login-shell">
      <main class="card">
        <h1>Uye Girisi</h1>

        <div class="field">
          <label for="email">E-posta</label>
          <input id="email" type="email" placeholder="ornek@mail.com" />
        </div>

        <div class="field">
          <label for="password">Sifre</label>
          <input id="password" type="password" placeholder="********" />
        </div>

        <button class="btn btn-primary" type="button">Giris Yap</button>
      </main>
    </div>
    <div class="scroll-space" aria-hidden="true"></div>
  </div>
</body>
</html>
