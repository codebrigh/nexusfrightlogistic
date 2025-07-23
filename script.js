// Auto-login after verification redirect
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('verified') === '1' && urlParams.get('email')) {
  showUserArea(urlParams.get('email'));
  window.history.replaceState({}, document.title, window.location.pathname);
}

document.getElementById('tracking-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const trackingNumber = document.getElementById('tracking-number').value.trim();
  const resultDiv = document.getElementById('tracking-result');
  const loadingDiv = document.getElementById('tracking-loading');
  resultDiv.innerHTML = '';
  if (!trackingNumber) {
    resultDiv.innerHTML = '<span style="color:red">Please enter a tracking number.</span>';
    return;
  }
  loadingDiv.style.display = 'block';
  try {
    // NOTE: Change the admin password below to match your backend's ADMIN_PASSWORD
    const adminPassword = 'admin123';
    const res = await fetch('http://localhost:5000/api/admin/tracking/' + encodeURIComponent(trackingNumber), {
      method: 'GET',
      headers: {
        'x-admin-password': adminPassword
      }
    });
    loadingDiv.style.display = 'none';
    if (res.ok) {
      const data = await res.json();
      resultDiv.innerHTML = `
        <div style="background:#e3f2fd;padding:1rem;border-radius:8px;margin-top:1rem;">
          <div style="font-size:1.2em;color:#1976d2;"><strong>Tracking Details:</strong></div>
          <strong>Tracking Number:</strong> ${data.trackingNumber}<br>
          <strong>Status:</strong> ${data.status}<br>
          <strong>Last Update:</strong> ${data.lastUpdate}<br>
          <strong>Estimated Delivery:</strong> ${data.estimatedDelivery}<br>
        </div>
      `;
    } else {
      const err = await res.json();
      resultDiv.innerHTML = `<div style='color:red;margin-top:1rem;'>${err.error || 'Tracking number not found.'}</div>`;
    }
  } catch (err) {
    loadingDiv.style.display = 'none';
    resultDiv.innerHTML = `<div style='color:red;margin-top:1rem;'>Server error. Please try again later.</div>`;
  }
});

// Modal logic for Sign In/Sign Up
const modalSignin = document.getElementById('modal-signin');
const modalSignup = document.getElementById('modal-signup');
const openSignin = document.getElementById('open-signin');
const openSignup = document.getElementById('open-signup');
const closeSignin = document.getElementById('close-signin');
const closeSignup = document.getElementById('close-signup');
const switchToSignup = document.getElementById('switch-to-signup');
const switchToSignin = document.getElementById('switch-to-signin');

if (openSignin && modalSignin) {
  openSignin.onclick = () => { modalSignin.style.display = 'flex'; };
}
if (openSignup && modalSignup) {
  openSignup.onclick = () => { modalSignup.style.display = 'flex'; };
}
if (closeSignin && modalSignin) {
  closeSignin.onclick = () => { modalSignin.style.display = 'none'; };
}
if (closeSignup && modalSignup) {
  closeSignup.onclick = () => { modalSignup.style.display = 'none'; };
}
if (switchToSignup && modalSignin && modalSignup) {
  switchToSignup.onclick = (e) => {
    e.preventDefault();
    modalSignin.style.display = 'none';
    modalSignup.style.display = 'flex';
  };
}
if (switchToSignin && modalSignin && modalSignup) {
  switchToSignin.onclick = (e) => {
    e.preventDefault();
    modalSignup.style.display = 'none';
    modalSignin.style.display = 'flex';
  };
}
window.onclick = function(event) {
  if (event.target === modalSignin) modalSignin.style.display = 'none';
  if (event.target === modalSignup) modalSignup.style.display = 'none';
};

const backendUrl = "http://localhost:5000/api/auth"; // Change if your backend runs elsewhere

// Sign Up Handler
const signupForm = document.getElementById('signup-form');
if (signupForm) {
  signupForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const email = document.getElementById('signup-email').value.trim();
    const pass = document.getElementById('signup-password').value;
    const confirm = document.getElementById('signup-confirm').value;
    if (!email || !pass || !confirm) {
      alert('Please fill in all fields.');
      return;
    }
    if (pass !== confirm) {
      alert('Passwords do not match.');
      return;
    }
    try {
      const res = await fetch(`${backendUrl}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password: pass })
      });
      const data = await res.json();
      if (res.ok) {
        modalSignup.style.display = 'none';
        showEmailPopup();
        alert(data.msg);
      } else {
        alert(data.msg || 'Registration failed');
      }
    } catch (err) {
      alert('Server error');
    }
  });
}

// Sign In Handler
const signinForm = document.getElementById('signin-form');
if (signinForm) {
  signinForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const email = document.getElementById('signin-email').value.trim();
    const pass = document.getElementById('signin-password').value;
    if (!email || !pass) {
      alert('Please fill in all fields.');
      return;
    }
    try {
      const res = await fetch(`${backendUrl}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password: pass })
      });
      const data = await res.json();
      if (res.ok) {
        modalSignin.style.display = 'none';
        showUserArea(email);
        // Optionally store token: localStorage.setItem('token', data.token);
      } else {
        alert(data.msg || 'Login failed');
      }
    } catch (err) {
      alert('Server error');
    }
  });
}

// After successful sign in
function showUserArea(email) {
  document.getElementById('user-area').style.display = 'block';
  document.getElementById('welcome-message').innerHTML = `Welcome, <strong>${email}</strong>! You can now track your goods and products.`;
  document.querySelector('.auth-buttons').style.display = 'none';
}

function showEmailPopup() {
  document.getElementById('email-popup').style.display = 'flex';
}
document.getElementById('close-email-popup')?.addEventListener('click', function() {
  document.getElementById('email-popup').style.display = 'none';
}); 