document.getElementById("forgot-pass").addEventListener("click", function () {
    // Get the elements
    const loginForm = document.getElementById("login-form");
    const forgotPassForm = document.getElementById("forgot-pass-form");

    // Check and toggle visibility
    if (getComputedStyle(loginForm).display !== "none") {

        // Hide Login Form, Show Forgot Password Form
        loginForm.style.display = "none";
        forgotPassForm.style.display = "block";
    } else {
        // Show Login Form, Hide Forgot Password Form
        loginForm.style.display = "block";
        forgotPassForm.style.display = "none";
    }
});
const backToLogin = document.getElementById('back-to-login');
backToLogin.addEventListener('click', () => {
    const loginForm = document.getElementById("login-form");
    const forgotPassForm = document.getElementById("forgot-pass-form");
    if (getComputedStyle(loginForm).display === "none") {
        // Hide Login Form, Show Forgot Password Form
        loginForm.style.display = "block";
        forgotPassForm.style.display = "none";
    } else {
        // Show Login Form, Hide Forgot Password Form
        loginForm.style.display = "none";
        forgotPassForm.style.display = "block";
    }
})
document.getElementById("password-bx").addEventListener("click", function () {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("password-bx");

    if (passwordField.type === "password") {
        // Change input type to text to show the password
        passwordField.type = "text";
        // Update the icon to indicate visibility
        eyeIcon.classList.remove("bx-low-vision");
        eyeIcon.classList.add("bx-show");
        eyeIcon.style.color = "crimson";
    } else {
        // Change input type back to password to hide it
        passwordField.type = "password";
        // Revert the icon to indicate hidden password
        eyeIcon.classList.remove("bx-show");
        eyeIcon.classList.add("bx-low-vision");
        eyeIcon.style.color = "#333";
    }
});

function createToast(type, icon, title, text) {
    let newToast = document.createElement("div");
    newToast.classList.add("toast", type);
    newToast.innerHTML = `
        <i class='${icon}'></i>
        <div class="toast-content">
            <div class="title">${title}</div>
            <span class="toast-msg">${text}</span>
        </div>
        <i class='bx bx-x' style="cursor: pointer" onclick="(this.parentElement).remove()"></i>
    `;
    document.querySelector(".notification").appendChild(newToast);

    newToast.timeOut = setTimeout(function () {
        newToast.remove();
    }, 5000);
}

document.getElementById('login-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission behavior

    const submitButton = document.querySelector('#login-form button[type="submit"]');
    submitButton.disabled = true; // Disable the submit button to prevent multiple clicks

    const formData = new FormData(this); // Gather form data from the form

    fetch('actions/handlelogin.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Parse JSON response
        })
        .then((data) => {
            if (data.type && data.icon && data.title && data.text) {
                // Call the createToast function with server response
                createToast(data.type, data.icon, data.title, data.text);

                // Handle redirection if provided in the response
                if (data.type === 'success' && data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect; // Redirect to the URL
                    }, 2000); // Optional delay for the toast message
                }
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            createToast('error', 'bx bxs-error', 'Error', 'Something went wrong. Please try again.');
        })
        .finally(() => {
            submitButton.disabled = false; // Re-enable the submit button after response is handled
        });
});


document.getElementById('forgot-pass-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the form from reloading the page

    const submitButton = document.querySelector('#forgot-pass-form button[type="submit"]');
    submitButton.disabled = true; // Disable the submit button to prevent multiple clicks

    const formData = new FormData(this); // Gather form data

    fetch('actions/handleForgotPassword.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Parse JSON response
        })
        .then((data) => {
            if (data.type && data.icon && data.title && data.text) {
                // Display toast notification
                createToast(data.type, data.icon, data.title, data.text);

                // Handle redirection if provided in the response
                if (data.type === 'success' && data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect; // Redirect after showing the toast
                    }, 2000); // Optional delay
                }
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            createToast('error', 'bx bxs-error', 'Error', 'Unable to process your request. Please try again.');
        })
        .finally(() => {
            submitButton.disabled = false; // Re-enable the submit button after response is handled
        });
});