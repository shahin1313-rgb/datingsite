// Wait for the window to load completely
window.addEventListener('load', function() {
    // Hide the loading spinner
    document.getElementById('loadingSpinner').style.display ='none' ;

    // Show the main content of the page
    document.getElementById('mainContent').style.display ='block' ;
});




document.getElementById('login-tab').addEventListener('click', function() {
    document.getElementById('login-form').classList.add('active');
    document.getElementById('register-form').classList.remove('active');
    document.getElementById('login-tab').classList.add('active');
    document.getElementById('register-tab').classList.remove('active');
});

document.getElementById('register-tab').addEventListener('click', function() {
    document.getElementById('register-form').classList.add('active');
    document.getElementById('login-form').classList.remove('active');
    document.getElementById('register-tab').classList.add('active');
    document.getElementById('login-tab').classList.remove('active');
});
