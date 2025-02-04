function showContent(section) {
    // Hide all content divs
    document.getElementById('admin').style.display = 'none';
    document.getElementById('students').style.display = 'none';
    document.getElementById('staffs').style.display = 'none';

    // Show the clicked section's content
    document.getElementById(section).style.display = 'flex';
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
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const studentCard = this.closest('.student-card'); // Get the parent card element
        const studentId = studentCard.querySelector('.student-id').textContent.split('ID: ')[1]; // Extract the student ID

        // Show the custom confirmation popup
        const modal = document.getElementById('deleteConfirmationPopup');
        modal.classList.add('show');

        // Handle the confirm delete button click
        document.getElementById('confirmDelete').onclick = function() {
            fetch('student/deleteStudent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `student_id=${encodeURIComponent(studentId)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        createToast('success', 'bx bxs-check-circle', "Success", 'Student deleted successfully!');
                        studentCard.remove(); // Remove the card from the DOM
                    } else {
                        createToast('error', 'bx bxs-error', "Error", data.message || 'Failed to delete the student.');
                    }
                    modal.classList.remove('show'); // Hide the modal after deleting
                })
                .catch(error => {
                    console.error('Error:', error);
                    createToast('error', 'bx bxs-error', "Error", 'An error occurred while trying to delete the student.');
                    modal.classList.remove('show'); // Hide the modal even if there's an error
                });
        };

        // Handle the cancel button click
        document.getElementById('cancelDelete').onclick = function() {
            modal.classList.remove('show'); // Just hide the modal
        };
    });
});

const searchInput = document.getElementById('searchInput');
const studentCards = document.querySelectorAll('.student-card');

searchInput.addEventListener('input', function() {
    const searchText = searchInput.value.toLowerCase();

    studentCards.forEach(function(card) {
        const name = card.getAttribute('data-name').toLowerCase();
        const id = card.getAttribute('data-id').toLowerCase();
        const studentClass = card.getAttribute('data-class').toLowerCase();
        const email = card.getAttribute('data-email').toLowerCase();
        const phone = card.getAttribute('data-phone').toLowerCase();

        // Check if search text matches any of the student data fields
        if (name.includes(searchText) || id.includes(searchText) || studentClass.includes(searchText) || email.includes(searchText) || phone.includes(searchText)) {
            card.classList.remove('active');
        } else {
            card.classList.add('active');
        }
    });
});


// Modal Elements
const deleteModal = document.getElementById('deleteModal');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
const deleteAllBtn = document.getElementById('deleteAllBtn');
const successMessage = document.getElementById('successMessage');

// Open the modal on "Delete All" click
deleteAllBtn.addEventListener('click', function() {
    deleteModal.style.display = 'flex';
});

// Handle the delete confirmation
confirmDeleteBtn.addEventListener('click', function() {
    // Send AJAX request to PHP to delete all students
    fetch('student/deleteAll.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=deleteAll'
    })
        .then(response => response.text())
        .then(result => {
            // Show the success message in the modal
            successMessage.style.display = 'block';
            createToast('success', 'bx bxs-check-circle', "Success", result);


            // Close modal after a delay
            setTimeout(() => {
               location.reload()
            }, 2000);
        })
        .catch(error => console.error('Error:', error));
});

// Close modal on cancel
cancelDeleteBtn.addEventListener('click', function() {
    deleteModal.style.display = 'none';
});

// Close modal if clicking outside the content
window.addEventListener('click', function(event) {
    if (event.target === deleteModal) {
        deleteModal.style.display = 'none';
    }
});

// Add event listener to all delete buttons with delegation
document.addEventListener('DOMContentLoaded', function () {
    // Select all delete buttons and add event listener to each of them
    const deleteButtons = document.querySelectorAll('.staff-delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const staffCard = this.closest('.staff-card'); // Get the parent staff card of the clicked button
            const staffId = staffCard.getAttribute('data-id'); // Get the staff ID from the data-id attribute

            // Show the custom delete modal
            const deleteModal = document.getElementById('staffdeleteModal');
            console.log(deleteModal)
            const confirmDeleteBtn = document.getElementById('staffconfirmDeleteBtn');
            const cancelDeleteBtn = document.getElementById('staffcancelDeleteBtn');

            deleteModal.style.display = 'block'; // Show the modal

            // Confirm delete on Yes button
            confirmDeleteBtn.addEventListener('click', function() {
                // Send AJAX request to delete the staff record and image
                fetch('staff/deleteStaff.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=deleteStaff&staff_id=${staffId}`
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            // Remove the staff card from the DOM
                            staffCard.remove();
                            createToast('success', 'bx bxs-check-circle', "Success", 'Staff record deleted successfully!');
                        } else {
                            createToast('error', 'bx bxs-error', "Error", result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        createToast('error', 'bx bxs-error', "Error",'An error occurred while deleting the staff record.');
                    });

                // Close the modal after deletion
                deleteModal.style.display = 'none';
            });

            // Close modal on Cancel button
            cancelDeleteBtn.addEventListener('click', function() {
                deleteModal.style.display = 'none'; // Hide modal
            });

            // Close modal if clicking outside the content
            window.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    deleteModal.style.display = 'none'; // Hide modal
                }
            });
        });
    });
});


// Function to filter staff based on the search query
function filterStaff() {
    const searchQuery = document.getElementById('searchInputStaff').value.toLowerCase();
    const staffCards = document.querySelectorAll('.staff-container .staff-card');  // Selecting all staff cards

    // Loop through all staff cards and hide those that do not match the search query
    staffCards.forEach(card => {
        const staffName = card.getAttribute('data-name').toLowerCase();
        const staffId = card.getAttribute('data-id').toLowerCase();

        if (staffName.includes(searchQuery) || staffId.includes(searchQuery)) {
            card.style.display = ''; // Show the staff card
        } else {
            card.style.display = 'none'; // Hide the staff card
        }
    });
}

// Event listener for input on the staff search field
document.getElementById('searchInputStaff').addEventListener('input', filterStaff);



// Event listener for 'Delete All Staff' button click
document.getElementById('deleteAllStaffBtn').addEventListener('click', function () {
    // Show the custom confirmation modal
    document.getElementById('staffalldeletemodal').style.display = 'block';
});

// Event listener for confirming the deletion
document.getElementById('confirmDeleteAllStaffBtn').addEventListener('click', function () {
    // Send AJAX request to delete all staff and their images
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'staff/deleteAll.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Send the request
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                createToast('success', 'bx bxs-check-circle', "Success", response.message);
                location.reload(); // Reload the page to reflect changes
            } else {
                createToast('error', 'bx bxs-error', "Error", response.message);

            }
        } else {
            createToast('error', 'bx bxs-error', "Error", 'Error in deleting staff records.');
        }
    };
    xhr.send('action=deleteAllStaff'); // Data sent to the server

    // Close the modal after the action
    document.getElementById('staffalldeletemodal').style.display = 'none';
});

// Event listener for canceling the deletion
document.getElementById('cancelDeleteAllStaffBtn').addEventListener('click', function () {
    // Close the modal without performing any action
    document.getElementById('staffalldeletemodal').style.display = 'none';
});
