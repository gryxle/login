document.addEventListener('DOMContentLoaded', () => {
    const loginBox = document.querySelector('.login-box');
    const registerBox = document.querySelector('.register-box');
    const signupBtn = document.getElementById('signupBtn');
    const loginBtn = document.getElementById('loginBtn');

    signupBtn.addEventListener('click', () => {
        loginBox.style.display = 'none';
        registerBox.style.display = 'block';
    });

    loginBtn.addEventListener('click', () => {
        registerBox.style.display = 'none';
        loginBox.style.display = 'block';
    });

    // Handle form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = 'users.php';

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Create and display alert message
                const alertBox = document.createElement('div');
                alertBox.className = 'alert-box';
                alertBox.textContent = data.message;

                // Apply styles based on status
                if (data.status === 'success') {
                    alertBox.style.backgroundColor = '#4CAF50'; // Green for success
                } else {
                    alertBox.style.backgroundColor = '#f44336'; // Red for errors
                }

                document.body.appendChild(alertBox);

                // Automatically remove alert message after 5 seconds
                setTimeout(() => {
                    alertBox.remove();
                }, 5000);

                if (data.status === 'success' && formData.get('action') === 'login') {
                    setTimeout(() => {
                        window.location.href = 'home.html';
                    }, 3000);
                } else if (data.status === 'success' && formData.get('action') === 'register') {
                    setTimeout(() => {
                        loginBox.style.display = 'block';
                        registerBox.style.display = 'none';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});