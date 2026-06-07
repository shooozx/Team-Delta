/**
 * Toggle between login and signup form views.
 */
function toggleForms() {
    const loginContainer = document.getElementById('loginContainer');
    const signupContainer = document.getElementById('signupContainer');
    loginContainer.classList.toggle('active');
    signupContainer.classList.toggle('active');
}

/**
 * Handle user login request and authenticate against backend.
 */
function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (!username || !password) {
        alert('Please fill out all fields.');
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);

    fetch('../backend/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

/**
 * Handle user registration and create new account in backend.
 */
function handleSignup(event) {
    event.preventDefault();
    const username = document.getElementById('signupUsername').value;
    const firstName = document.getElementById('firstName').value;
    const middleName = document.getElementById('middleName').value;
    const surname = document.getElementById('surname').value;
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (!username || !password || !confirmPassword) {
        alert('Please fill out all required fields.');
        return;
    }

    if (password !== confirmPassword) {
        alert('Passwords do not match.');
        return;
    }

    const formData = new FormData();
    formData.append('username', username);
    formData.append('firstName', firstName);
    formData.append('middleName', middleName);
    formData.append('surname', surname);
    formData.append('password', password);
    formData.append('confirmPassword', confirmPassword);

    fetch('../backend/signup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            toggleForms();
            document.getElementById('signupForm').reset();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
