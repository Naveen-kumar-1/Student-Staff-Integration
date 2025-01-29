function showContent(section) {
    document.querySelectorAll('.display-content-box').forEach(box => {
        box.style.display = 'none';
    });
    document.getElementById(section).style.display = 'block';
}

// Clear assignment form function
function clearAssignmentForm() {
    document.querySelectorAll('#assignment-class').value = '';
    document.querySelectorAll('#assignment-subject').value = '';
    document.querySelectorAll('#assignment-due-date').value = '';
    document.querySelectorAll('#assignment').value = '';

    // Clear error messages
    document.querySelectorAll('#assignment-class-error').innerText = '';
    document.querySelectorAll('#assignment-subject-error').innerText = '';
    document.querySelectorAll('#assignment-due-date-error').innerText = '';
}

// Show the assignment form and hide the assignment list


// Hide the assignment form and show the assignment list
function hideAssignmentForm() {
    document.querySelector('.assignment-form').style.display = 'none';
    document.querySelector('.assignment-list').style.display = 'block'; // Assuming there's an assignment list
}


// notice
function validateNoticeForm() {
    const titleVal = document.querySelector('#notice-title').value.trim();
    const detailsVal = document.querySelector('#notice-details').value.trim();
    const dateVal = document.querySelector('#notice-date').value.trim();

    let isValid = true;

    // Validate title
    if (titleVal === '') {
        document.querySelector('#notice-title-error').innerText = 'Title is required';
        isValid = false;
    } else {
        document.querySelector('#notice-title-error').innerText = '';
    }

    // Validate details
    if (detailsVal === '') {
        document.querySelector('#notice-details-error').innerText = 'Details are required';
        isValid = false;
    } else {
        document.querySelector('#notice-details-error').innerText = '';
    }

    // Validate date
    if (dateVal === '') {
        document.querySelector('#notice-date-error').innerText = 'Date is required';
        isValid = false;
    } else {
        document.querySelector('#notice-date-error').innerText = '';
    }

    return isValid;
}


function showAssignmentForm(formType, assignmentId = null) {
    clearAssignmentForm();

    // Show all assignment forms
    document.querySelectorAll('.assignment-form').forEach(function (form) {
        form.style.display = 'block';
    });

    // Hide all assignment lists
    document.querySelectorAll('.assignment-list').forEach(function (list) {
        list.style.display = 'none';
    });

    // Update the title for all forms

    // Reset error messages and form
    document.getElementById('assignment-class-error').textContent = '';
    document.getElementById('assignment-subject-error').textContent = '';
    document.getElementById('assignment-due-date-error').textContent = '';
    document.getElementById('assignmentForm').reset(); // Reset form

    // Set form title based on formType
    var formTitle = formType === 'edit' ? 'Edit Assignment' : 'Create Assignment';
    document.getElementById('form-title').textContent = formTitle;

    // If it's an edit, populate the form with existing assignment data
    if (formType === 'edit' && assignmentId !== null) {
        // Fetch existing assignment data
        fetch('actions/assignment/getAssignmentData.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id: assignmentId})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate form fields with data
                    document.getElementById('assignment-id').value = data.assignment.id;
                    document.getElementById('assignment-class').value = data.assignment.class;
                    document.getElementById('assignment-subject').value = data.assignment.subject;
                    document.getElementById('assignment-due-date').value = data.assignment.due_date;
                    document.getElementById('assignment-details').value = data.assignment.details;
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching assignment data:', error);
            });
    }

    // Show the assignment form
    document.querySelector('.assignment-form').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('assignmentForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create a FormData object
        var isValid = true; // Validation flag

        // Input fields
        var classInput = document.getElementById('assignment-class');
        var subjectInput = document.getElementById('assignment-subject');
        var dueDateInput = document.getElementById('assignment-due-date');
        var assignmentDetailsInput = document.getElementById('assignment-details');

        // Error message elements
        var classError = document.getElementById('assignment-class-error');
        var subjectError = document.getElementById('assignment-subject-error');
        var dueDateError = document.getElementById('assignment-due-date-error');
        var assignmentDetailsError = document.getElementById('assignment-details-error');

        // Validation checks
        if (!classInput.value) {
            classError.textContent = 'Class is required';
            isValid = false;
        } else {
            classError.textContent = '';
        }

        if (!subjectInput.value.trim()) {
            subjectError.textContent = 'Subject is required';
            isValid = false;
        } else {
            subjectError.textContent = '';
        }

        if (!dueDateInput.value) {
            dueDateError.textContent = 'Due Date is required';
            isValid = false;
        } else {
            dueDateError.textContent = '';
        }

        if (!assignmentDetailsInput.value.trim()) {
            assignmentDetailsError.textContent = 'Assignment details are required';
            isValid = false;
        } else {
            assignmentDetailsError.textContent = '';
        }

        if (!isValid) {
            return; // Stop submission if validation fails
        }

        // Determine the correct endpoint (save or update)
        var assignmentId = document.getElementById('assignment-id').value;
        var url = assignmentId
            ? 'actions/assignment/updateAssignment.php'
            : 'actions/assignment/saveAssignment.php';

        // Make the AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);

        xhr.onload = function () {
            var data;
            try {
                data = JSON.parse(xhr.responseText);
            } catch (e) {
                data = {success: false, message: 'Invalid response from server'};
            }

            if (data.success) {
                showToast(assignmentId ? 'Assignment updated successfully!' : 'Assignment created successfully!', 'success');
                document.getElementById('assignmentForm').reset(); // Reset the form
                hideAssignmentForm(); // Close the form

                setTimeout(function () {
                    location.reload();
                }, 3000);
                // Reload the page

            } else {
                showToast('Error: ' + data.message, 'error');
            }
        };

        xhr.onerror = function () {
            showToast('Error: An error occurred during the request.', 'error');
        };

        xhr.send(formData); // Send form data
    });
});


// Function to show toast messages
function showToast(message, type) {
    var toast = document.createElement('div');
    toast.classList.add('toast-message');
    toast.classList.add(type); // Add type class for styling (e.g., success, error)

    toast.textContent = message;
    document.body.appendChild(toast);

    // Automatically remove the toast after 3 seconds
    setTimeout(function () {
        toast.remove();
    }, 3000);
}


// Variable to store the assignment ID


// Cancel form

// Clear the form fields
function clearNoticeForm() {
    document.getElementById('notice-title').value = '';
    document.getElementById('notice-details').value = '';
    document.getElementById('notice-date').value = '';
    document.getElementById('notice-title-error').textContent = '';
    document.getElementById('notice-details-error').textContent = '';
    document.getElementById('notice-date-error').textContent = '';
}


// Fetch and Display Notices


// Show notice form (Create/Edit)
function showNoticeForm(formType, noticeId = null) {
    document.getElementById('edit-form-title').innerText = 'Create Notice';

    clearNoticeForm();

    // Show all notice forms
    document.querySelectorAll('.notice-form').forEach(function (form) {
        form.style.display = 'block';
    });

    // Hide all notice lists
    document.querySelectorAll('.notice-list').forEach(function (list) {
        list.style.display = 'none';
    });

    // Reset error messages and form
    document.getElementById('notice-title-error').textContent = '';
    document.getElementById('notice-details-error').textContent = '';
    document.getElementById('notice-date-error').textContent = '';
    document.getElementById('notice-class-error').textContent = '';

    // Set form title based on formType
    var formTitle = formType === 'edit' ? 'Edit Notice' : 'Create Notice';
    document.getElementById('form-title').textContent = formTitle;

    // If it's an edit, populate the form with existing notice data
    if (formType === 'edit' && noticeId !== null) {
        document.getElementById('edit-form-title').innerText = 'Edit Notice';

        fetch('actions/notice/getNotice.php', {
            method: 'POST', // POST request to send data to PHP
            headers: {'Content-Type': 'application/json'}, // Set the content type to JSON
            body: JSON.stringify({id: noticeId}) // Send noticeId as JSON
        })
            .then(response => response.json()) // Parse the JSON response
            .then(data => {
                if (data.success) {
                    // Populate form fields with data
                    document.getElementById('notice-id').value = data.notice.id;
                    document.getElementById('notice-title').value = data.notice.title;
                    document.getElementById('notice-details').value = data.notice.details;
                    document.getElementById('notice-date').value = data.notice.date;
                    document.getElementById('notice-class').value = data.notice.class;
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching notice data:', error);
            });
    }


    // Show the notice form
    document.querySelector('.notice-form').style.display = 'block';
}

// Save or edit notice
function saveNotice() {
    const title = document.getElementById('notice-title').value;
    const details = document.getElementById('notice-details').value;
    const date = document.getElementById('notice-date').value;
    const classVal = document.getElementById('notice-class').value;
    const noticeId = document.getElementById('notice-id').value;

    let isValid = true;
    // Validation checks
    if (!title) {
        document.getElementById('notice-title-error').textContent = "Title is required";
        isValid = false;
    } else {
        document.getElementById('notice-title-error').textContent = ""; // Clear error
    }
    if (!details) {
        document.getElementById('notice-details-error').textContent = "Details are required";
        isValid = false;
    } else {
        document.getElementById('notice-details-error').textContent = ""; // Clear error
    }
    if (!date) {
        document.getElementById('notice-date-error').textContent = "Date is required";
        isValid = false;
    } else {
        document.getElementById('notice-date-error').textContent = ""; // Clear error
    }
    if (!classVal) {
        document.getElementById('notice-class-error').textContent = "Class is required";
        isValid = false;
    } else {
        document.getElementById('notice-class-error').textContent = ""; // Clear error
    }

    if (!isValid) return; // Stop if validation fails

    const noticeData = {
        title: title,
        details: details,
        date: date,
        class: classVal,
        id: noticeId // For editing if noticeId exists
    };

    const url = noticeId ? 'actions/notice/updateNotice.php' : 'actions/notice/saveNotice.php';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json'); // Change to JSON

    xhr.onload = function () {
        const data = JSON.parse(xhr.responseText);
        if (data.success) {
            showToast(noticeId ? 'Notice updated successfully!' : 'Notice created successfully!', 'success');
            cancelNoticeForm();
            setTimeout(function () {
                location.reload(); // Reload to reflect changes
            }, 3000);
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    };

    xhr.send(JSON.stringify(noticeData)); // Send as JSON
}


function cancelNoticeForm() {
    document.querySelector('.notice-form').style.display = 'none';
    document.querySelector('.notice-list').style.display = 'block';
}

// Function to delete a notice (example)
function deleteNotice(noticeId) {
    // Show the custom confirmation modal
    var modal = document.getElementById('confirmationModal');
    var confirmBtn = document.getElementById('confirmNoticeDeleteBtn');
    var cancelBtn = document.getElementById('cancelNoticeDeleteBtn');

    // Display the modal
    modal.style.display = 'block';

    // When the user clicks the "Yes, Delete" button
    confirmBtn.onclick = function () {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'actions/notice/deleteNotice.php', true);
        xhr.onload = function () {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                modal.style.display = 'none';
                showToast('Notice deleted successfully', 'success');

                setTimeout(function () {
                    if (data.redirect) {
                        location.reload(); // Reload the page to reflect the changes
                    }
                }, 3000);
            } else {
                showToast('Error deleting notice', 'error');
            }
            // Close the modal
            modal.style.display = 'none';
        };

        var formData = new FormData();
        formData.append('id', noticeId);
        xhr.send(formData);
    };

    // When the user clicks the "Cancel" button
    cancelBtn.onclick = function () {
        // Close the modal
        modal.style.display = 'none';
    };

    // If the user clicks outside the modal, close it
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// attandance
function openClass(className) {
    // Hide all class sections
    const allClasses = document.querySelectorAll('.classes');
    allClasses.forEach(classSection => {
        classSection.style.display = 'none';
        classSection.classList.remove('active');
    });

    // Show the clicked class section
    const selectedClass = document.getElementById(className);
    if (selectedClass) {
        selectedClass.style.display = 'block';
        selectedClass.classList.add('active');
    }
}

function updateLeaveNotification(count) {
    const notificationBadge = document.getElementById('leave-notification');
    notificationBadge.textContent = count;
}

updateLeaveNotification(20);

// Function to handle tab switching
function openLeaveStatus(status) {
    const links = document.querySelectorAll('.class-link');
    const requestBoxes = document.querySelectorAll('.request-box');

    // Remove active class from all links and hide all request boxes
    links.forEach(link => link.classList.remove('active'));
    requestBoxes.forEach(box => box.style.display = 'none');

    // Add active class to the clicked link and display the relevant request box
    const activeLink = Array.from(links).find(link => link.textContent.trim().toLowerCase().includes(status));
    const activeRequestBox = document.getElementById(status);

    if (activeLink) activeLink.classList.add('active');
    if (activeRequestBox) activeRequestBox.style.display = 'block';
}

function changeLeaveStatus(dropdown) {
    const selectedStatus = dropdown.value;
    const parentBox = dropdown.closest('.approve-leave-box');
    const statusText = parentBox.querySelector('p:nth-of-type(4)');
    const statusIcon = statusText.querySelector('.status-icon');

    // Update the status text and icon
    switch (selectedStatus) {
        case 'approved':
            statusText.innerHTML = `Status: Approved <i class="status-icon bx bxs-check-circle"></i>`;
            break;
        case 'declined':
            statusText.innerHTML = `Status: Declined <i class="status-icon bx bxs-x-circle"></i>`;
            break;
        case 'trash':
            statusText.innerHTML = `Status: Trash <i class="status-icon bx bxs-trash"></i>`;
            break;
    }
}

function saveLeaveStatus(button) {
    const dropdown = button.previousElementSibling; // Get the dropdown element
    const selectedStatus = dropdown.value;
    const parentBox = button.closest('.approve-leave-box');
    const statusText = parentBox.querySelector('p:nth-of-type(4)');
    const statusIcon = statusText.querySelector('.status-icon');

    // Update the status text and icon based on selected value
    switch (selectedStatus) {
        case 'approved':
            statusText.innerHTML = `Status: Approved <i class="status-icon bx bxs-check-circle"></i>`;
            break;
        case 'declined':
            statusText.innerHTML = `Status: Declined <i class="status-icon bx bxs-x-circle"></i>`;
            break;
        case 'trash':
            statusText.innerHTML = `Status: Trash <i class="status-icon bx bxs-trash"></i>`;
            break;
    }

    // Optionally, you can trigger a save action (e.g., sending data to a server)
    alert('Status updated to: ' + selectedStatus);
}

function toggleDropdown(selectElement) {
    const dropdown = selectElement.nextElementSibling;
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    selectElement.classList.toggle('active');
}

function selectOption(optionElement) {
    const selectBox = optionElement.closest('.custom-select').querySelector('.select-box .selected-option');
    const newIcon = optionElement.querySelector('i').classList;
    const newText = optionElement.textContent.trim();

    // Update the displayed option with icon and text
    selectBox.innerHTML = `<i class="${newIcon}"></i> ${newText}`;

    // Hide dropdown after selection
    const dropdown = optionElement.closest('.options-container');
    dropdown.style.display = 'none';
}

function showSubmittedStudentList(assignmentId) {
    // Make an AJAX request to get the submitted student list for the given assignment
    fetch('actions/assignment/submitedStudents.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({assignment_id: assignmentId})
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignmentID = data.assignment_id;
                const studentList = data.students;
                const studentTable = document.getElementById('submittedStudentTable');
                studentTable.innerHTML = ''; // Clear previous content

                // Create table headers
                studentTable.innerHTML = `
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Submitted On</th>
                        <th>View/Delete PDF</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;

                const tableBody = studentTable.querySelector('tbody');
                const origin = window.location.origin;

                // Loop through the object keys (student IDs) and populate the table with student details
                Object.keys(studentList).forEach(studentId => {
                    const student = studentList[studentId]; // Access the student's data using the student ID as the key
                    const pdfUrl = `${origin}/Student-Staff-Integration/uploads/assignment/${student.class}/${student.subject}/${student.due_date}/${student.file_name}`;

                    const tableRow = `
                    <tr>
                        <td>${student.student_id}</td>
                        <td>${student.first_name}</td>
                        <td>${student.last_name}</td>
                        <td>${student.submitted_at}</td>
                        <td>
                            <a href="${pdfUrl}" target="_blank"><i class="fas fa-eye"></i></a>
                            <a href="#" onclick="deleteASingleFile('${student.file_name}','${student.class}','${student.subject}','${student.due_date}','${student.student_id}','${assignmentID}')">
                                <i class='bx bxs-trash'></i>
                            </a>
                        </td>
                    </tr>
                `;
                    tableBody.innerHTML += tableRow;
                });

                // Show the modal
                document.getElementById('submittedStudentModal').style.display = 'block';
            } else {
                alert('No students have submitted this assignment.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch student list.');
        });
}


// Function to close the modal
function closeModal() {
    document.getElementById('submittedStudentModal').style.display = 'none';
}

let fileNameToDelete = '';
let classNameToDelete = '';
let SubjectToDelete = '';
let dueDateToDelete = '';
let studentIDToDelete = '';
let assignmentIDToDelete = '';

function deleteASingleFile(fileName, className, subject, dueDate, studentID, assignmentID) {
    // Save the file details to global variables
    fileNameToDelete = fileName;
    classNameToDelete = className;
    subjectToDelete = subject;
    dueDateToDelete = dueDate;
    studentIDToDelete = studentID;
    assignmentIDToDelete = assignmentID;
    // Show the modal
    document.getElementById('deleteFileModal').style.display = 'block';
}

// Function to confirm deletion
document.getElementById('confirmDelete').onclick = function () {
    // Make an AJAX request to delete the file
    fetch('actions/assignment/deleteSinglePDFFile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            file_name: fileNameToDelete,
            class_name: classNameToDelete,
            subject: subjectToDelete,
            due_date: dueDateToDelete,
            student_id: studentIDToDelete,
            assignment_id: assignmentIDToDelete

        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('File deleted successfully', 'success');
                setTimeout(function () {
                    if (data.redirect) {
                        location.reload(); // Reload the page to reflect the changes
                    }
                }, 3000);
                // Optionally, remove the file entry from the UI
            } else {
                showToast('Failed to delete file: ' + data.message, 'error');
            }

            // Hide the modal
            document.getElementById('deleteFileModal').style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete file.', 'error');
        });
};

// Function to cancel deletion
document.getElementById('cancelDelete').onclick = function () {
    // Hide the modal
    document.getElementById('deleteFileModal').style.display = 'none';
};
var currentAssignmentId = null;

function deleteAssignment(assignmentId, className, subject, dueDate) {
    // Check if any required parameter is missing or empty
    if (!assignmentId || !className || !subject || !dueDate) {
        showToast('Error: Missing required assignment details.', 'error');
        return; // Stop execution
    }

    // Declare variables before assigning values
    let currentAssignmentId = assignmentId;
    let classNameToDelete = className;
    let subjectToDelete = subject;
    let dueDateToDelete = dueDate;

    // Show the custom confirmation popup
    document.getElementById('deleteConfirmationModal').style.display = 'flex';

    // Event listener for confirming the delete action
    document.getElementById('confirmDeleteBtn').onclick = function () {
        fetch('actions/assignment/deleteAssignment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                class_name: classNameToDelete,
                subject: subjectToDelete,
                due_date: dueDateToDelete,
                assignment_id: currentAssignmentId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Assignment deleted successfully!', 'success');

                    let assignmentItem = document.getElementById('assignment-' + currentAssignmentId);
                    if (assignmentItem) {
                        assignmentItem.remove();
                    }

                    setTimeout(function () {

                        location.reload();

                    }, 3000);
                } else {
                    showToast('Error: ' + data.message, 'error');
                }

                document.getElementById('deleteConfirmationModal').style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error: An error occurred during the request.', 'error');
                document.getElementById('deleteConfirmationModal').style.display = 'none';
            });
    };

    // Event listener for canceling the delete action
    document.getElementById('cancelDeleteBtn').onclick = function () {
        document.getElementById('deleteConfirmationModal').style.display = 'none';
    };
}
