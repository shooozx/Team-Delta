/**
 * Toggle between login and signup form views.
 */
function toggleForms() {
    const loginContainer = document.getElementById("loginContainer");
    const signupContainer = document.getElementById("signupContainer");

    const isLoginActive = loginContainer.classList.contains("active");

    if (isLoginActive) {
        loginContainer.classList.remove("active");
        signupContainer.classList.add("active");
    } else {
        signupContainer.classList.remove("active");
        loginContainer.classList.add("active");
    }
}

/* ================= LOGIN ================= */
function handleLogin(event) {
    event.preventDefault();

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    if (!username || !password) {
        alert("Please fill out all fields.");
        return;
    }

    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    fetch("../backend/login.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error("Login error:", err);
        alert("Something went wrong.");
    });
}

/* ================= SIGNUP ================= */
function handleSignup(event) {
    event.preventDefault();

    const username = document.getElementById("signupUsername").value;
    const firstName = document.getElementById("firstName").value;
    const middleName = document.getElementById("middleName").value;
    const surname = document.getElementById("surname").value;
    const password = document.getElementById("signupPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (!username || !password || !confirmPassword) {
        alert("Please fill out required fields.");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return;
    }

    const formData = new FormData();
    formData.append("username", username);
    formData.append("firstName", firstName);
    formData.append("middleName", middleName);
    formData.append("surname", surname);
    formData.append("password", password);
    formData.append("confirmPassword", confirmPassword);

    fetch("../backend/signup.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Account created successfully!");
            toggleForms();
            document.getElementById("signupForm").reset();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error("Signup error:", err);
        alert("Something went wrong.");
    });
}

/* ================= INIT ================= */
window.onload = function () {
    document.getElementById("loginContainer").classList.add("active");
};