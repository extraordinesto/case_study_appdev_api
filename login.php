<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Inventory System</title>
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <div class="login-container">
    <h2>Welcome to Inventory System</h2>

    <form id="loginForm">
      <input type="text" id="username" name="username" placeholder="Username" required>
      
      <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Password" required>
      </div>
      
      <button type="submit">Log In</button>
    </form>

    <div class="signup-link">
      Don't have an account?
      <a href="signup.php">Sign Up</a>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();

      const response = await fetch('http://localhost:5000/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      });

      const result = await response.json();

      if (result.success) {
        Swal.fire('Success', 'Logged-In Successfully.', 'success').then(() => {
          window.location.href = 'index.php';
        });
      } else {
        Swal.fire('Failed', 'Incorrect Username or Password.', 'error');
      }
    });
  </script>

</body>
</html>
