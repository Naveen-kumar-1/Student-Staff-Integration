function showLogin() {
    document.querySelector(".sst-index-box").style.display = "none";
    document.querySelector("#signup-form").style.display = "none";
    document.querySelector("#login-form").style.display = "block";
}

function showSignUp() {
    document.querySelector(".sst-index-box").style.display = "none";
    document.querySelector("#login-form").style.display = "none";
    document.querySelector("#signup-form").style.display = "block";
}

function showLoginForm() {
    const role = document.getElementById('login-role').value;
    const loginCredentials = document.getElementById('login-credentials');

    if (role === 'student') {
        loginCredentials.innerHTML = `
            <label for="student-id">Student ID:</label>
            <input type="text" id="student-id" placeholder="Enter Student ID" class="input-field">
            <div class="error-message" id="student-id-error" style="display: none;">Please enter your Student ID</div>
    
            <label for="student-pass">Password:</label>
            <div class="input-group">
                <input type="password" id="student-pass" placeholder="Enter Password" class="input-field">
                <span class="toggle-password" onclick="togglePassword('student-pass', 'toggle-student-pass')">
                    <i id="toggle-student-pass" class="fa fa-eye"></i>
                </span>
            </div>
            <div class="error-message" id="student-pass-error" style="display: none;">Please enter your password</div>
        `;
    } else if (role === 'staff') {
        loginCredentials.innerHTML = `
            <label for="staff-id">Staff ID:</label>
            <input type="text" id="staff-id" placeholder="Enter Staff ID" class="input-field">
            <div class="error-message" id="staff-id-error" style="display: none;">Please enter your Staff ID</div>
    
            <label for="staff-pass">Password:</label>
            <div class="input-group">
                <input type="password" id="staff-pass" placeholder="Enter Password" class="input-field">
                <span class="toggle-password" onclick="togglePassword('staff-pass', 'toggle-staff-pass')">
                    <i id="toggle-staff-pass" class="fa fa-eye"></i>
                </span>
            </div>
            <div class="error-message" id="staff-pass-error" style="display: none;">Please enter your password</div>
        `;
    } else {
        loginCredentials.innerHTML = '';
    }
}

function showSignupForm() {
    const role = document.getElementById('signup-role').value;
    const signupCredentials = document.getElementById('signup-credentials');

    if (role === 'student') {
        signupCredentials.innerHTML = `
       <form id="student-signup-form">
    <label for="student-first-name">Student First Name:</label>
    <input type="text" id="student-first-name" name="student-first-name" placeholder="Enter Student First Name" class="input-field">
    <div class="error-message" id="student-first-name-error" style="display: none;">Please enter the student first name</div>

    <label for="student-last-name">Student Last Name:</label>
    <input type="text" id="student-last-name" name="student-last-name" placeholder="Enter Student Last Name" class="input-field">
    <div class="error-message" id="student-last-name-error" style="display: none;">Please enter the student last name</div>

    <label for="student-class">Student Class:</label>
    <select id="student-class" name="student-class" class="input-field">
        <option value="">Select Class</option>
        <optgroup label="Undergraduate">
            <option value="first-ug">UG - First Year</option>
            <option value="second-ug">UG - Second Year</option>
            <option value="third-ug">UG - Third Year</option>
        </optgroup>
        <optgroup label="Postgraduate">
            <option value="first-pg">PG - First Year</option>
            <option value="second-pg">PG - Second Year</option>
        </optgroup>
    </select>
    <div class="error-message" id="student-class-error" style="display: none;">Please select a class</div>

    <label for="student-id">Student ID:</label>
    <input type="text" id="student-id" name="student-id" placeholder="Enter Student ID" class="input-field">
    <div class="error-message" id="student-id-error" style="display: none;">Please enter a valid student ID</div>

    <label for="student-adhaar">Student Aadhar:</label>
    <input type="text" id="student-adhaar" name="student-adhaar" placeholder="Enter Aadhar Number" class="input-field">
    <div class="error-message" id="student-adhaar-error" style="display: none;">Please enter a valid Aadhar number</div>

    <label for="student-phone">Student Phone Number:</label>
    <input type="text" id="student-phone" name="student-phone" placeholder="Enter Phone Number" class="input-field">
    <div class="error-message" id="student-phone-error" style="display: none;">Please enter a valid phone number</div>

    <label for="student-email">Student Email ID:</label>
    <input type="email" id="student-email" name="student-email" placeholder="Enter Email ID" class="input-field">
    <div class="error-message" id="student-email-error" style="display: none;">Please enter a valid email ID</div>

    <label for="student-address">Student Address:</label><br>
    <textarea id="student-address" name="student-address" placeholder="Enter Address" class="input-field" ></textarea>
    <div class="error-message" id="student-address-error" style="display: none;">Please enter your address</div><br>

    <label for="student-blood-group">Student Blood Group:</label>
    <select id="student-blood-group" name="student-blood-group" class="input-field">
        <option value="">Select Blood Group</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
    </select>
    <div class="error-message" id="student-blood-group-error" style="display: none;">Please select your blood group</div>

    <label for="student-gender">Student Gender:</label>
    <select id="student-gender" name="student-gender" class="input-field">
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select>
    <div class="error-message" id="student-gender-error" style="display: none;">Please select your gender</div>
<label for="student-image">Upload Profile Image:</label>
<input type="file" name="student-image_url" id="student-image_url" accept="image/*" class="input-field">
<div class="error-message" id="image-error" style="display: none;">Please upload a valid image</div>
    <label for="student-pass">Password:</label>
    <div class="input-group">
        <input type="password" id="student-pass" name="student-pass" placeholder="Enter Password" class="input-field">
        <span class="toggle-password" onclick="togglePassword('student-pass', 'toggle-student-pass')">
            <i id="toggle-student-pass" class="fa fa-eye"></i>
        </span>
    </div>
    <div class="error-message" id="student-pass-error" style="display: none;">Please enter a valid password</div>

   

        `;
    } else if (role === 'staff') {
        signupCredentials.innerHTML = `
<form id="staff-form" method="POST" enctype="multipart/form-data">
            <label for="first-name">First Name:</label>
            <input type="text" id="first-name" placeholder="Enter First Name" class="input-field">
            <div class="error-message" id="first-name-error" style="display: none;">Please enter your first name</div>

            <label for="last-name">Last Name:</label>
            <input type="text" id="last-name" placeholder="Enter Last Name" class="input-field">
            <div class="error-message" id="last-name-error" style="display: none;">Please enter your last name</div>
            <label for="staff-id">Staff ID:</label>
            <input type="text" id="staff-id" placeholder="Enter Staff ID" class="input-field">
            <div class="error-message" id="staff-id-error" style="display: none;">Please enter your Staff ID</div>

            <label for="adhaar">Aadhaar:</label>
            <input type="text" id="adhaar" placeholder="Enter Aadhaar Number" class="input-field">
            <div class="error-message" id="adhaar-error" style="display: none;">Please enter a valid Aadhaar number</div>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" placeholder="Enter Phone Number" class="input-field">
            <div class="error-message" id="phone-error" style="display: none;">Please enter a valid phone number</div>
 <label for="staff-class"> Class Adviser of:</label>
    <select id="staff-class" name="staff-class" class="input-field">
        <option value="not-applicable">Not any class</option>
        <optgroup label="Undergraduate">
            <option value="first-ug">UG - First Year</option>
            <option value="second-ug">UG - Second Year</option>
            <option value="third-ug">UG - Third Year</option>
        </optgroup>
        <optgroup label="Postgraduate">
            <option value="first-pg">PG - First Year</option>
            <option value="second-pg">PG - Second Year</option>
        </optgroup>
    </select>
    <p style="font-size: 14px; color: #4A607A">Select if you are any class's adviser</p>
     <div class="error-message" id="class-error" style="display: none;">Please enter a valid email address</div>
            <label for="email">Email:</label>
            <input type="email" id="email" placeholder="Enter Email Address" class="input-field">
            <div class="error-message" id="email-error" style="display: none;">Please enter a valid email address</div>
  <label for="address">Address:</label><br>
    <textarea id="address" placeholder="Enter Address" class="input-field" rows="4"></textarea><br>
    <div class="error-message" id="address-error" style="display: none;">Please enter a valid address</div>

            <label for="gender">Gender:</label>
            <select id="gender" class="input-field">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <label for="position">Position:</label>
            <select id="position" class="input-field">
                <option value="Head of the Department">Head of the Department</option>
                <option value="Assistant HOD">Assistant HOD</option>
                <option value="Assistant Proffessor">Assistant Proffessor</option>
            </select>

            <label for="blood-group">Blood Group:</label>
            <select id="blood-group" class="input-field">
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
            <div class="error-message" id="blood-group-error" style="display: none;">Please select a valid blood group</div>

           <label for="staff-image">Upload Profile Image:</label>
<input type="file" name="image_url" id="staff-image" accept="image/*" class="input-field">
<div class="error-message" id="image-error" style="display: none;">Please upload a valid image</div>


            <label for="staff-pass">Password:</label>
            <div class="input-group">
                <input type="password" id="staff-pass" placeholder="Enter Password" class="input-field">
                <span class="toggle-password" onclick="togglePassword('staff-pass', 'toggle-staff-pass')">
                    <i id="toggle-staff-pass" class="fa fa-eye"></i>
                </span>
            </div>
            <div class="error-message" id="staff-pass-error" style="display: none;">Please enter a valid password</div>
            
        `;
    } else {
        signupCredentials.innerHTML = '';
    }
}

function togglePassword(inputId, toggleIconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(toggleIconId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}

// Regex patterns for validation
const studentIdPattern = /^[0-9]{2}[A-Za-z]{3}[0-9]{4}$/;
const staffIdPattern = /^[A-Za-z0-9]{9}$/;
const passwordPattern = /^[A-Za-z0-9]{8,}$/;

function validateForm(fields) {
    let isValid = true;

    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const error = document.getElementById(field.errorId);

        // Ensure the error element exists before manipulating it
        if (error) {
            // Check for value
            if (!input.value) {
                input.classList.add("error");
                error.style.display = "block";
                error.textContent = "This field is required.";
                isValid = false;
            } else if (field.pattern && !field.pattern.test(input.value)) {
                // Check for pattern match
                input.classList.add("error");
                error.style.display = "block";
                error.textContent = field.errorMessage;
                isValid = false;
            } else {
                input.classList.remove("error");
                error.style.display = "none";
            }
        }
    });

    return isValid;
}


// Toast notification function
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    setTimeout(() => {
        toast.classList.remove('show');
        document.body.removeChild(toast);
    }, 3000);
}

function submitLogin() {
    const role = document.getElementById('login-role').value;

    if (role === 'student') {
        const isValid = validateForm([
            {
                id: 'student-id',
                errorId: 'student-id-error',
                pattern: studentIdPattern,
                errorMessage: 'Invalid user ID.'
            },
            {
                id: 'student-pass',
                errorId: 'student-pass-error',
                pattern: passwordPattern,
                errorMessage: 'Password must be exactly 8 alphanumeric characters.'
            }
        ]);

        if (isValid) {
            // AJAX for student login
            const studentId = document.getElementById('student-id').value;
            const studentPass = document.getElementById('student-pass').value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "Roles/Student/actions/login/login.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast("Login successful!");
                        window.location.href = response.redirect;
                        // Redirect or handle login success
                    } else {
                        showToast(response.message); // Show error message from PHP
                    }
                }
            };
            xhr.send("role=student&user_id=" + encodeURIComponent(studentId) + "&password=" + encodeURIComponent(studentPass));
        }
    } else if (role === 'staff') {
        const isValid = validateForm([
            {
                id: 'staff-id',
                errorId: 'staff-id-error',
                pattern: staffIdPattern,
                errorMessage: 'Invalid user ID.'
            },
            {
                id: 'staff-pass',
                errorId: 'staff-pass-error',
                pattern: passwordPattern,
                errorMessage: 'Password must be exactly 8 alphanumeric characters.'
            }
        ]);

        if (isValid) {
            // AJAX for staff login
            const staffId = document.getElementById('staff-id').value;
            const staffPass = document.getElementById('staff-pass').value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "Roles/Staff/actions/login/login.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast("Login successful!");
                        window.location.href = response.redirect;
                        // Redirect or handle login success
                    } else {
                        showToast(response.message); // Show error message from PHP
                    }
                }
            };
            xhr.send("role=staff&user_id=" + encodeURIComponent(staffId) + "&password=" + encodeURIComponent(staffPass));
        }
    } else {
        showToast("Please select a role.");
    }
}

function submitSignUp() {
    const role = document.getElementById('signup-role').value;
    let imageUrl = "";

    // Capture image URL based on the role
    let imageFile = null;
    if (role === 'student') {
        imageFile = document.getElementById('student-image_url').files[0]; // Student Image URL
    } else if (role === 'staff') {
        imageFile = document.getElementById('staff-image').files[0]; // Get staff image file
    }

    if (role === 'student') {
        const isValid = validateForm([
            {
                id: 'student-first-name',
                errorId: 'student-first-name-error',
                errorMessage: 'Student First Name is required.'
            },
            {
                id: 'student-last-name',
                errorId: 'student-last-name-error',
                errorMessage: 'Student Last Name is required.'
            },
            {id: 'student-class', errorId: 'student-class-error', errorMessage: 'Please select a class.'},
            {id: 'student-id', errorId: 'student-id-error', errorMessage: 'Please enter a valid Student ID.'},
            {id: 'student-adhaar', errorId: 'student-adhaar-error', errorMessage: 'Invalid Aadhar number.'},
            {id: 'student-phone', errorId: 'student-phone-error', errorMessage: 'Invalid phone number.'},
            {id: 'student-email', errorId: 'student-email-error', errorMessage: 'Invalid email address.'},
            {id: 'student-address', errorId: 'student-address-error', errorMessage: 'Please enter your address.'},
            {
                id: 'student-blood-group',
                errorId: 'student-blood-group-error',
                errorMessage: 'Please select your blood group.'
            },
            {id: 'student-gender', errorId: 'student-gender-error', errorMessage: 'Please select your gender.'},
            {
                id: 'student-pass',
                errorId: 'student-pass-error',
                errorMessage: 'Password must be at least 8 alphanumeric characters.'
            }
        ]);

        if (isValid) {
            const firstName = document.getElementById('student-first-name').value;
            const lastName = document.getElementById('student-last-name').value;
            const classSelected = document.getElementById('student-class').value;
            const studentId = document.getElementById('student-id').value;
            const adhaar = document.getElementById('student-adhaar').value;
            const phone = document.getElementById('student-phone').value;
            const email = document.getElementById('student-email').value;
            const address = document.getElementById('student-address').value;
            const bloodGroup = document.getElementById('student-blood-group').value;
            const gender = document.getElementById('student-gender').value;
            const password = document.getElementById('student-pass').value;

            const xhr = new XMLHttpRequest();
            const formData = new FormData();

// Append form data for the student role
            formData.append("role", "student");
            formData.append("first_name", firstName);
            formData.append("last_name", lastName);
            formData.append("class", classSelected);
            formData.append("student_id", studentId);
            formData.append("adhaar", adhaar);
            formData.append("phone", phone);
            formData.append("email", email);
            formData.append("address", address);
            formData.append("blood_group", bloodGroup);
            formData.append("gender", gender);
            formData.append("password", password);
            formData.append("image", imageFile);  // Attach image file

// Send the form data using XMLHttpRequest
            xhr.open("POST", "Roles/Student/actions/signup/signup.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast("Sign-up successful!");

                        if (response.redirect) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000); // 3000 milliseconds = 3 seconds
                        }

                    } else {
                        showToast(response.message);
                    }
                }
            };
            xhr.send(formData);  // Send form data

        }
    } else if (role === 'staff') {
        const isValid = validateForm([
            {id: 'first-name', errorId: 'first-name-error', errorMessage: 'First Name is required.'},
            {id: 'last-name', errorId: 'last-name-error', errorMessage: 'Last Name is required.'},
            {id: 'adhaar', errorId: 'adhaar-error', errorMessage: 'Invalid Aadhaar number.'},
            {id: 'phone', errorId: 'phone-error', errorMessage: 'Invalid phone number.'},
            {id: 'email', errorId: 'email-error', errorMessage: 'Invalid email address.'},
            {id: 'staff-class', errorId: 'class-error', errorMessage: 'Please select any class or N/A'},
            {
                id: 'staff-pass',
                errorId: 'staff-pass-error',
                errorMessage: 'Password must be exactly 8 alphanumeric characters.'
            },
            {id: 'blood-group', errorId: 'blood-group-error', errorMessage: 'Blood group is required.'}
        ]);

        if (isValid) {
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const gender = document.getElementById('gender').value;
            const bloodGroup = document.getElementById('blood-group').value;
            const adhaar = document.getElementById('adhaar').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            const staffPass = document.getElementById('staff-pass').value;
            const staffId = document.getElementById('staff-id').value;
            const position = document.getElementById('position').value;
            const address = document.getElementById('address').value;
            const classAdviser = document.getElementById('staff-class').value;
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            formData.append("role", "staff");
            formData.append("first_name", firstName);
            formData.append("last_name", lastName);
            formData.append("gender", gender);
            formData.append("blood_group", bloodGroup);
            formData.append("adhaar", adhaar);
            formData.append("address", address);
            formData.append("phone", phone);
            formData.append("email", email);
            formData.append("position", position);
            formData.append("password", staffPass);
            formData.append("staff_id", staffId);
            formData.append("staff_class", classAdviser);
            formData.append("image_url", imageFile);  // Send image file

            xhr.open("POST", "Roles/Staff/actions/signup/signup.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showToast("Sign-up successful!");


                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);


                    } else {
                        showToast(response.message);
                    }
                }
            };
            xhr.send(formData);  // Send the form data with image
        }
    } else {
        showToast("Please select a role.");
    }
}