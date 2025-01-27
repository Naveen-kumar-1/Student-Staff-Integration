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


function updateLeaveNotification(count) {
    const notificationBadge = document.getElementById('leave-notification');
    notificationBadge.textContent = count;
}

updateLeaveNotification(20);



function updateAssignmentNotification(count) {
    const notificationBadge = document.getElementById('assignment-notification');

    if (count > 0) {
        notificationBadge.textContent = count;
        notificationBadge.style.display = 'inline-block'; // Make sure it's visible if count > 0
    } else {
        notificationBadge.textContent = '0';  // Optional: Set to 0 to show a badge with zero
        notificationBadge.style.display = 'inline-block'; // Show the badge even if count is zero
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
            console.log(popupFormContainer)
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
            assignmentForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(this); // Collect form data

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

