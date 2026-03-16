// Get form
const form = document.getElementById("signupForm");

// Get inputs
const fullname = document.getElementById("fullname");
const email = document.getElementById("email");
const password = document.getElementById("password");
const confirm = document.getElementById("confirm");
const agree = document.getElementById("agree");

form.addEventListener("submit", function(e){

e.preventDefault(); // stop page refresh

let name = fullname.value.trim();
let mail = email.value.trim();
let pass = password.value.trim();
let confirmPass = confirm.value.trim();

// Check empty fields
if(name === "" || mail === "" || pass === "" || confirmPass === ""){
alert("Please fill in all fields.");
return;
}

// Email validation
let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
if(!mail.match(emailPattern)){
alert("Please enter a valid email address.");
return;
}

// Password length
if(pass.length < 6){
alert("Password must be at least 6 characters.");
return;
}

// Password match
if(pass !== confirmPass){
alert("Passwords do not match.");
return;
}

// Check terms agreement
if(!agree.checked){
alert("You must agree to the Terms and Privacy Policy.");
return;
}

// Success
alert("Account created successfully!");

// Reset form
form.reset();

});
