<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracker d'Habitudes - Accueil</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #74ABE2, #5563DE);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 500px;
      padding: 2rem;
      animation: fadeIn 0.8s ease-in-out;
    }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .tabs { display: flex; justify-content: space-around; margin-bottom: 1.5rem; border-bottom: 2px solid #eee; }
    .tabs button {
      background: none; border: none; font-size: 1.2rem; padding: 0.5rem 1rem; cursor: pointer;
      transition: color 0.3s, border-bottom 0.3s; color: #555;
    }
    .tabs button.active { color: #5563DE; border-bottom: 2px solid #5563DE; }
    .form { display: none; }
    .form.active { display: block; }
    h1 { margin-bottom: 1.5rem; color: #5563DE; font-size: 2rem; text-align: center; }
    .form-group { margin-bottom: 1.2rem; text-align: left; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: #333; }
    .form-group input {
      width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;
      transition: border-color 0.3s;
    }
    .form-group input:focus { border-color: #5563DE; outline: none; }
    .form-actions { text-align: center; margin-top: 1.5rem; }
    .form-actions button {
      padding: 0.8rem 1.5rem; font-size: 1rem; color: #fff; background: #5563DE; border: none;
      border-radius: 5px; cursor: pointer; transition: background 0.3s, transform 0.3s;
    }
    .form-actions button:hover { background: #4453c7; transform: translateY(-2px); }
    .error { color: #e74c3c; margin-top: 0.5rem; font-size: 0.9rem; text-align: center; }
  </style>
</head>
<body>
  <div class="container">
    <div class="tabs">
      <button id="loginTab" class="active">Connexion</button>
      <button id="registerTab">Inscription</button>
    </div>
    <div id="loginForm" class="form active">
      <h1>Connexion</h1>
      <form action="/connexion" method="POST">
        <div class="form-group">
          <label for="identifier">Pseudo ou Email</label>
          <input type="text" id="identifier" name="identifier" placeholder="Votre pseudo ou email" required>
        </div>
        <div class="form-group">
          <label for="loginPassword">Mot de passe</label>
          <input type="password" id="loginPassword" name="password" placeholder="Votre mot de passe" required>
        </div>
        <div class="form-actions">
          <button type="submit">Se connecter</button>
        </div>
        <div class="error" id="loginError"></div>
      </form>
    </div>
    <div id="registerForm" class="form">
      <h1>Inscription</h1>
      <form action="/inscription" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="registerPseudo">Pseudo</label>
          <input type="text" id="registerPseudo" name="pseudo" placeholder="Votre pseudo" required>
        </div>
        <div class="form-group">
          <label for="registerEmail">Email</label>
          <input type="email" id="registerEmail" name="email" placeholder="exemple@mail.com" required>
        </div>
        <div class="form-group">
          <label for="registerPassword">Mot de passe</label>
          <input type="password" id="registerPassword" name="password" placeholder="Votre mot de passe" required>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirmer le mot de passe</label>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre mot de passe" required>
        </div>
        <div class="form-group">
          <label for="profilePicture">Photo de profil (max 1Mo)</label>
          <input type="file" id="profilePicture" name="profilePicture" accept="image/*">
        </div>
        <div class="form-actions">
          <button type="submit">S'inscrire</button>
        </div>
        <div class="error" id="registerError"></div>
      </form>
    </div>
  </div>
  <script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    loginTab.addEventListener('click', () => {
      loginTab.classList.add('active');
      registerTab.classList.remove('active');
      loginForm.classList.add('active');
      registerForm.classList.remove('active');
    });
    registerTab.addEventListener('click', () => {
      registerTab.classList.add('active');
      loginTab.classList.remove('active');
      registerForm.classList.add('active');
      loginForm.classList.remove('active');
    });
    document.querySelector('#loginForm form').addEventListener('submit', function(e) {
      const identifier = document.getElementById('identifier').value.trim();
      const password = document.getElementById('loginPassword').value;
      const errorEl = document.getElementById('loginError');
      errorEl.textContent = "";
      if (!identifier || !password) {
        e.preventDefault();
        errorEl.textContent = "Veuillez renseigner tous les champs.";
      }
    });
    document.querySelector('#registerForm form').addEventListener('submit', function(e) {
      const password = document.getElementById('registerPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      const errorEl = document.getElementById('registerError');
      errorEl.textContent = "";
      if (password !== confirmPassword) {
        e.preventDefault();
        errorEl.textContent = "Les mots de passe ne correspondent pas.";
      }
    });
  </script>
</body>
</html>