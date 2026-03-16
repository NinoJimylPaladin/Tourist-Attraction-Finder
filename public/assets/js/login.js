// Get form elements
const form = document.querySelector("form");
const username = document.querySelector('input[type="text"]');
const password = document.querySelector('input[type="password"]');
const remember = document.querySelector('input[type="checkbox"]');

form.addEventListener("submit", function(event){

event.preventDefault(); // prevent page refresh

// Check if fields are empty
if(username.value === "" || password.value === ""){
    alert("Please enter your username and password.");
    return;
}

// Check Remember Me
if(remember.checked){
    alert("Login successful! (Remember Me enabled)");
}else{
    alert("Login successful!");
    if ("sign up click (Create Account page)"){
        alert("Redirecting to Create Account page...");
    }       
}

});
