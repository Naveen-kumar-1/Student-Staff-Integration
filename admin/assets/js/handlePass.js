document.getElementById("otp-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent form submission

    let otp = "";
    document.querySelectorAll(".otp-box").forEach((box) => {
        otp += box.value; // Collect values from all otp-box elements
    });

    console.log("OTP Entered: ", otp); // Check if the OTP is correctly captured

    if (otp.length === 6) {
        // Make sure we have a full 6-digit OTP
        fetch("../../actions/verifyOtp.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({otp: otp}), // Send the OTP as JSON
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json(); // Ensure the response is JSON
            })
            .then((data) => {
                console.log("Server Response: ", data); // Debug server response
                if (data.type === "success") {
                    createToast(
                        "success",
                        "bx bxs-check-circle",
                        data.title,
                        data.text
                    );
                    setTimeout(() => {
                        window.location.href = data.redirect; // Redirect on success
                    }, 2000); // Delay before redirect
                } else {
                    createToast("error", "bx bxs-error", data.title, data.text);
                }
            })
            .catch((error) => {
                console.error("Fetch Error: ", error); // Catch network or parsing errors
                createToast(
                    "error",
                    "bx bxs-error",
                    "Error",
                    "Unable to process your request. Please try again."
                );
            });
    } else {
        console.log("Incomplete OTP: ", otp); // Log if OTP is not complete
        createToast(
            "warning",
            "bx bxs-error",
            "Invalid OTP",
            "Please enter the full 6-digit OTP."
        );
    }
});

document.getElementById("resend-otp").addEventListener("click", function (e) {
    e.preventDefault(); // Prevent the default anchor link behavior

    const resendLink = document.getElementById("resend-otp");
    resendLink.style.pointerEvents = "none"; // Disable link to prevent multiple clicks
    resendLink.style.color = "gray"; // Optional: change color to indicate it's disabled

    fetch('../../actions/resendOtp.php', {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.type === "success") {
                createToast("success", "bx bxs-check-circle", data.title, data.text);
            } else {
                createToast("error", "bx bxs-error", data.title, data.text);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            createToast("error", "bx bxs-error", "Error", "Unable to resend OTP. Please try again.");
        })
        .finally(() => {
            // Re-enable the link after the request is finished
            resendLink.style.pointerEvents = "auto";
            resendLink.style.color = ""; // Reset the color to default
        });
});


document.querySelectorAll(".otp-box").forEach((input, index, inputs) => {
    input.addEventListener("input", (event) => {
        const value = event.target.value;
        if (value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus(); // Move to the next input
        }
    });

    input.addEventListener("keydown", (event) => {
        if (event.key === "Backspace" && !event.target.value && index > 0) {
            inputs[index - 1].focus(); // Move to the previous input on Backspace
        }
    });
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