// Function to show the content based on the clicked nav link
function showContent(contentId) {
    // Hide all content boxes first
    const contentBoxes = document.querySelectorAll('.display-content-box');
    contentBoxes.forEach(box => {
        box.style.display = 'none';
    });

    // Show the clicked content box
    const contentBox = document.getElementById(contentId);
    if (contentBox) {
        contentBox.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const submitBtns = document.querySelectorAll('.submit-btn');

    submitBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const assignmentId = this.dataset.id;
            const firstName = this.dataset.firstName;
            const lastName = this.dataset.lastName;
            const studentId = this.dataset.studentId;

            const popupFormContainer = document.getElementById('popup-form');
            console.log(popupFormContainer);
            popupFormContainer.innerHTML = `
                <div class="popup-form">
                    <span class="close-btn" onclick="closePopupForm()">&times;</span>
                    <h2>Submit Assignment</h2>
                    <p>Student: ${firstName} ${lastName} (ID: ${studentId})</p>
                    <form class="assignment-form" data-id="${assignmentId}" enctype="multipart/form-data">
                        <input type="hidden" name="first_name" value="${firstName}">
                        <input type="hidden" name="last_name" value="${lastName}">
                        <input type="hidden" name="assignment_id" value="${assignmentId}">
                        <input type="hidden" name="student_id" value="${studentId}">
                        <label for="file-upload">Upload PDF:</label>
                        <input type="file" id="file-upload" name="assignment_file" accept="application/pdf" required>
                        <span class="error-message" id="file-error" style="display:none; color:red;"></span>
                        <button type="submit" id="submit-assignment-pdf">Submit</button>
                    </form>
                </div>
            `;
            popupFormContainer.style.display = 'flex';

            // Handle form submission with JS
            const assignmentForm = document.querySelector('.assignment-form');
            const submitButton = assignmentForm.querySelector('#submit-assignment-pdf');

            assignmentForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(this); // Collect form data

                // Disable the submit button to prevent double-clicking
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';

                // AJAX request to PHP for handling submission
                fetch('actions/submitAssignment/submitAssignment.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success toast
                            showToast('Assignment submitted successfully!', 'success');
                            closePopupForm(); // Close the popup on success
                            if (data.redirect) {
                                setTimeout(function () {
                                    if (data.redirect) {
                                        location.reload(); // Reload the page to reflect the changes
                                    }
                                }, 3000);
                            }
                        } else {
                            // Display error message in the form
                            document.getElementById('file-error').textContent = data.error;
                            document.getElementById('file-error').style.display = 'block';

                            // Show error toast
                            showToast(data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Show a general error toast in case of fetch failure
                        showToast('An error occurred, please try again.', 'error');
                    })
                    .finally(() => {
                        // Re-enable the submit button after the request is done
                        submitButton.disabled = false;
                        submitButton.textContent = 'Submit';
                    });
            });
        });
    });
});


function closePopupForm() {
    const popupFormContainer = document.getElementById('popup-form');
    popupFormContainer.style.display = 'none';
    popupFormContainer.innerHTML = '';
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.classList.add('toast', type); // Add success/error class
    toast.innerHTML = `<p>${message}</p>`;

    // Append toast to body
    document.body.appendChild(toast);

    // Trigger the animation by setting visibility
    setTimeout(() => {
        toast.style.visibility = 'visible';
        toast.style.opacity = 1;
    }, 10); // Delay to trigger animation

    // Automatically remove toast after it fades out
    setTimeout(() => {
        toast.remove();
    }, 4000); // Toast duration: 4 seconds
}


document.addEventListener("DOMContentLoaded", () => {
    // Get modal elements
    const modal = document.getElementById("notice-modal");
    const closeModal = document.querySelector(".close");
    const noticeTitle = document.getElementById("notice-title");
    const noticeDetails = document.getElementById("notice-details");
    const noticeDate = document.getElementById("notice-date");

    // Handle View button clicks
    document.querySelectorAll(".view-btn").forEach(button => {
        button.addEventListener("click", () => {
            // Fetch data attributes
            const title = button.getAttribute("data-title");
            const details = button.getAttribute("data-details");
            const date = button.getAttribute("data-date");

            // Populate modal
            noticeTitle.textContent = title;
            noticeDetails.textContent = details;
            noticeDate.textContent = date;

            // Show modal
            modal.style.display = "block";
        });
    });

    // Close modal when 'X' is clicked
    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close modal when clicking outside the content
    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});

// Get elements
const applyLeaveBtn = document.getElementById('applyLeave');
const leavePopup = document.getElementById('leavePopup');
const closeBtn = document.querySelector('.close-btn');

// Show popup when button is clicked
applyLeaveBtn.addEventListener('click', () => {
    leavePopup.style.display = 'flex';
});

// Hide popup when close button is clicked
closeBtn.addEventListener('click', () => {
    leavePopup.style.display = 'none';
});

// Hide popup when clicking outside the form
window.addEventListener('click', (event) => {
    if (event.target === leavePopup) {
        leavePopup.style.display = 'none';
    }
});
document.getElementById('applyLeaveForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    // Get form values
    let studentName = document.getElementById('student-name').value;
    let studentId = document.getElementById('student-id').value;
    let studentClass = document.getElementById('student-class').value;
    let leaveDate = document.getElementById('leave-date').value;
    let leaveReason = document.getElementById('leave-reason').value;
    let submitButton = document.querySelector('#applyLeaveForm button[type="submit"]');

    // Validate form inputs
    if (leaveDate === "" || leaveReason.trim() === "") {
        showToast("Please fill in all required fields.", 'error');
        return;
    }

    // Disable submit button to prevent double-click
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...'; // Optional: Change button text to indicate processing

    // Prepare data for AJAX request
    let formData = new FormData();
    formData.append('student_name', studentName);
    formData.append('student_id', studentId);
    formData.append('student_class', studentClass);
    formData.append('leave_date', leaveDate);
    formData.append('leave_reason', leaveReason);

    // Send data to PHP using Fetch API
    fetch('actions/applyLeave/applyLeave.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("Leave Applied Successfully!", "success");
                document.getElementById('applyLeaveForm').reset(); // Reset the form
                document.getElementById('leavePopup').style.display = 'none'; // Hide popup
                setTimeout(function () {
                    location.reload(); // Reload the page to reflect the changes
                }, 3000);
            } else {
                showToast("Error: " + data.message, 'error');
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            // Re-enable the submit button after the request completes
            submitButton.disabled = false;
            submitButton.textContent = 'Submit'; // Reset button text
        });
});


// Show edit popup with data
function editLeaveRequest(id) {
    fetch(`actions/applyLeave/getLeaveRequest.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("edit-id").value = data.leave.id;
                document.getElementById("edit-student-name").value = data.leave.student_name;
                document.getElementById("edit-student-id").value = data.leave.student_id;
                document.getElementById("edit-student-class").value = data.leave.student_class;
                document.getElementById("edit-leave-date").value = data.leave.leave_date;
                document.getElementById("edit-leave-reason").value = data.leave.leave_reason;
                document.getElementById("editPopup").style.display = "flex";
            } else {
                showToast("Error fetching leave request!", "error");
            }
        });
}

// Close edit popup
document.getElementById("closeEditPopup").addEventListener("click", function () {
    document.getElementById("editPopup").style.display = "none";
});
// Submit edited data
document.getElementById("editLeaveForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let submitButton = document.querySelector('#editLeaveForm button[type="submit"]');

    // Disable the submit button to prevent multiple clicks
    submitButton.disabled = true;
    submitButton.textContent = 'Updating...'; // Optional: Show processing message

    let formData = new FormData();
    formData.append("id", document.getElementById("edit-id").value);
    formData.append("leave_date", document.getElementById("edit-leave-date").value);
    formData.append("leave_reason", document.getElementById("edit-leave-reason").value);
    formData.append('student_class',document.getElementById("edit-student-class").value)
    formData.append('student_name',document.getElementById('edit-student-name').value)
    formData.append('student_id',document.getElementById('edit-student-id').value)

    fetch("actions/applyLeave/applyLeave.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("Leave updated successfully!", 'success');
                location.reload(); // Refresh the page after successful update
            } else {
                showToast("Error: " + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast("Error occurred while updating the leave request.", 'error');
        })
        .finally(() => {
            // Re-enable the submit button once the request completes
            submitButton.disabled = false;
            submitButton.textContent = 'Update'; // Reset button text
        });
});

// Show custom confirmation popup
function confirmDelete(id) {
    document.getElementById("delete-id").value = id; // Store ID in hidden input
    document.getElementById("confirmPopup").style.display = "flex"; // Show popup
}

// Cancel delete
document.getElementById("confirmNo").addEventListener("click", function () {
    document.getElementById("confirmPopup").style.display = "none"; // Hide popup
});

// Confirm delete and make AJAX request
document.getElementById("confirmYes").addEventListener("click", function () {
    let id = document.getElementById("delete-id").value;

    fetch("actions/applyLeave/deleteLeave.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("Leave request deleted successfully!", 'success');
                location.reload(); // Refresh page
            } else {
                showToast("Error: " + data.message, 'error');
            }
            document.getElementById("confirmPopup").style.display = "none"; // Hide popup
        })
        .catch(error => console.error("Error:", error));
});
