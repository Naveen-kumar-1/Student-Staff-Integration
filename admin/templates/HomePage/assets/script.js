function showContent(section) {
    // Hide all content divs
    document.getElementById('welcome').style.display = 'none';
    document.getElementById('admin').style.display = 'none';
    document.getElementById('students').style.display = 'none';
    document.getElementById('staffs').style.display = 'none';

    // Show the clicked section's content
    document.getElementById(section).style.display = 'block';
}function handleSubmit(event) {
    event.preventDefault(); // Prevent form from submitting the traditional way

    // Get form data
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Validate email and password
    if (!email || !password) {

        createToast('error', 'bx bxs-error', 'Error', 'Both fields are required.');
        return;
    }

    // Validate the password using regular expressions
    const passwordRegex = /^[A-Z][A-Za-z0-9]{7,}$/; // First letter uppercase, min 8 chars, only letters and numbers
    if (!passwordRegex.test(password)) {
        createToast('error', 'bx bxs-error', 'Error', 'Password must start with an uppercase letter, contain at least 8 characters, and only include letters and numbers.');
        return;
    }

    // Prepare data to send to PHP
    const formData = new FormData();
    formData.append("email", email);
    formData.append("password", password);

    // Send data to PHP using Fetch API
    fetch("../../actions/updateAdminDetails.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createToast('success', 'bx bxs-check-circle', "Success", 'Admin details updated successfully!');

            } else {
                createToast('error', 'bx bxs-error', 'Error', 'Failed to update details. Try again later');

            }
        })
        .catch(error => {
            console.log(error.message)
            createToast('error', 'bx bxs-error', 'Error', 'Error: ' + error.message);
        });
}

// Function to show message alerts
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
});

// Fetch current admin details and display them
window.onload = function() {
    fetch("../../actions/getAdminDetails.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('email').value = data.email; // Set the email field
                document.getElementById('password').value = data.password;
            } else {
                console.log("Error fetching admin details:", data.message);
            }
        })
        .catch(error => {
            console.error("Error fetching admin details:", error);
        });
};


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
