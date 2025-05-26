document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const email = e.target[0].value;
    const senha = e.target[1].value;
  
    // Simulação de login
    if (email === "admin@escola.com" && senha === "123456") {
      document.getElementById("loginStatus").style.color = "green";
      document.getElementById("loginStatus").innerText = "Login bem-sucedido!";
      // Redirecionar após 1.5 segundos
      setTimeout(() => {
        window.location.href = "index.php";
      }, 1500);
    } else {
      document.getElementById("loginStatus").style.color = "red";
      document.getElementById("loginStatus").innerText = "E-mail ou senha incorretos.";
    }
  });
  