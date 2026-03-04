document.addEventListener("DOMContentLoaded", () => {
    const signupButton = document.querySelector("button");
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");

    signupButton.addEventListener("click", (event) => {
        event.preventDefault();

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        if (!name || !email || !password || !confirmPassword) {
            alert("Please fill in all fields.");
            return;
        }

        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!email.match(emailPattern)) {
            alert("Please enter a valid email address.");
            return;
        }

    
        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return;
        }

        
        alert("Account created successfully! Welcome, " + name);

       
    });
});
