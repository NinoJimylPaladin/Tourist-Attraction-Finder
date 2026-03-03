document.addEventListener("DOMContentLoaded", () => {
    const formButton = document.querySelector("button");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    formButton.addEventListener("click", (event) => {
        event.preventDefault(); 

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email || !password) {
            alert("Please enter both email and password.");
            return;
        }

        
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!email.match(emailPattern)) {
            alert("Please enter a valid email address.");
            return;
        }

        
        alert("Login successful! Welcome back, " + email);
    });
});

 

