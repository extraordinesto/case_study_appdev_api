<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/styles.css">
  <title>Sign Up - Inventory System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Create an Account</h2>

    <form id="signupForm">
      <input type="text" id="fullName" name="fullName" placeholder="Full Name" required>
      <input type="text" id="username" name="username" placeholder="Username" required>

      <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit">Sign Up</button>
    </form>

    <div class="signup-link">
      Already have an account? <a href="login.php">Log In</a>
    </div>
  </div>

  <script>
    document.getElementById('signupForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const fullName = document.getElementById('fullName').value.trim();
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();

      const response = await fetch('http://localhost:5000/signup', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ fullName, username, password })
      });

      const result = await response.json();

      if (result.success) {
        Swal.fire('Success', 'Account created successfully.', 'success').then(() => {
          window.location.href = 'login.php';
        });
      } else {
        Swal.fire('Error', 'Signup failed. Username may already be taken.', 'error');
      }
    });
  </script>

</body>
</html>
