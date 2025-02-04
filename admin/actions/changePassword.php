<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Change Password</title>
	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../assets/css/style.css">
	<style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .password-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px 40px 10px 10px;
            margin: 10px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s, background-color 0.3s;
            position: relative;
        }

        input:focus {
            border-color: #007bff;
        }

        input.valid {
            border-color: seagreen;
            background-color: #e0f5e0;
        }

        input.invalid {
            border-color: crimson;
            background-color: #f5e0e0;
        }


        .bx {
            font-size: 32px;
            display: flex;
            align-items: end;
        }

        .validation-message {
            font-size: 13px;
            color: crimson;
            margin-top: -15px;
            padding: 5px 0;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .input-show-pass {
            display: flex;
            align-items: center;

        }
	</style>
</head>
<body>

<div class="notification"></div>
<div class="password-container">
	<h2 style="text-align: center;padding: 20px; margin-bottom: 10px;">Change Password</h2>
	<form id="change-password-form">
		<div class="input-group">
			<span style="color: #333;font-size: 14px;padding: 8px;">Enter the Password : </span>
			<div class="input-show-pass">
				<input type="password" id="new-password" placeholder="Create New Password" required>
				<i class="bx bx-show" id="toggle-new-password"></i>
			</div>

			<span id="new-password-message" class="validation-message"></span>
		</div>

		<div class="input-group">
			<span style="color: #333;font-size: 14px;padding: 8px;">Confirm the Password : </span>
			<div class="input-show-pass">
				<input type="password" id="confirm-password" placeholder="Confirm Password" required>
				<i class="bx bx-show" id="toggle-confirm-password"></i>
			</div>
			<span id="confirm-password-message" class="validation-message"></span>
		</div>

		<button type="submit" id="submit-btn">Submit</button>
	</form>
</div>

<script>
    const passwordInput = document.getElementById("new-password");
    const confirmPasswordInput = document.getElementById("confirm-password");
    const passwordMessage = document.getElementById("new-password-message");
    const confirmPasswordMessage = document.getElementById("confirm-password-message");

    const passwordRegex = /^[A-Za-z0-9]{8,}$/; // Only letters and numbers, minimum 8 characters

    document.getElementById("toggle-new-password").addEventListener("click", function () {
        togglePasswordVisibility("new-password", this);
    });

    document.getElementById("toggle-confirm-password").addEventListener("click", function () {
        togglePasswordVisibility("confirm-password", this);
    });

    function togglePasswordVisibility(inputId, icon) {
        const passwordInput = document.getElementById(inputId);


        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("bx-show");
            icon.classList.add("bx-hide");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("bx-hide");
            icon.classList.add("bx-show");
        }
    }

    // Validate password on input
    passwordInput.addEventListener("input", function () {
        if (passwordRegex.test(passwordInput.value)) {
            passwordInput.classList.add("valid");
            passwordInput.classList.remove("invalid");
            passwordMessage.textContent = "";
        } else {
            passwordInput.classList.add("invalid");
            passwordInput.classList.remove("valid");
            passwordMessage.textContent = "Password must be at least 8 characters and contain only letters and numbers.";
        }
    });

    // Validate confirm password on input
    confirmPasswordInput.addEventListener("input", function () {
        if (confirmPasswordInput.value === passwordInput.value) {
            confirmPasswordInput.classList.add("valid");
            confirmPasswordInput.classList.remove("invalid");
            confirmPasswordMessage.textContent = "";
        } else {
            confirmPasswordInput.classList.add("invalid");
            confirmPasswordInput.classList.remove("valid");
            confirmPasswordMessage.textContent = "Passwords do not match.";
        }
    });


    // Form submission
    document.getElementById("change-password-form").addEventListener("submit", function (e) {
        e.preventDefault();

        // Only proceed if both password inputs are valid
        if (passwordInput.classList.contains("valid") && confirmPasswordInput.classList.contains("valid")) {
            const password = passwordInput.value;

            // Create a FormData object to send to PHP
            const formData = new FormData();
            formData.append("password", password);

            // Send password data to PHP
            fetch('storePassword.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.type === 'success') {
                        createToast("success", "bx bxs-check-circle", "Success", "Password changed successfully..!");
                        if (data.redirect) {
                            window.location.href = data.redirect;  // Perform the redirect
                        }
                    } else {
                        createToast("error", "bx bxs-error", "Error", data.message || "Failed to update password.");
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    createToast("error", "bx bxs-error", "Error", "Unable to process your request. Please try again.");
                });
        } else {
            createToast("error", "bx bxs-error", "Error", "Please correct the errors before submitting.");
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

</script>

</body>
</html>