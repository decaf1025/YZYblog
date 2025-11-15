//ThemeToggle
const mode = document.getElementById("themeToggle");
const body = document.querySelector("body");
const icon = mode.querySelector("i");


mode.addEventListener("click", () => {
  if (body.classList.contains("lightMode")) {
    body.classList.replace("lightMode", "darkMode");
      mode.classList.replace("btn-outline-primary", "btn-outline-dark");
      icon.classList.replace("bi-toggle-off", "bi-toggle-on");
  } else {
    body.classList.replace("darkMode", "lightMode");
      mode.classList.replace("btn-outline-dark", "btn-outline-primary");
      icon.classList.replace("bi-toggle-on", "bi-toggle-off");
  }
});

//  Password verification
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("signupForm");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirmPassword");
  const passError = document.getElementById("passError");
  const cpassError = document.getElementById("cpassError");
  const successMsg = document.getElementById("successMsg");

  form.addEventListener("submit", function(event) {
    passError.textContent = "";
    cpassError.textContent = "";
    successMsg.textContent = "";

    // Trim and validate
    const pass = password.value.trim();
    const cpass = confirmPassword.value.trim();

    if (cpass === "" || pass === "") {
      cpassError.textContent = "Please fill in both password fields";
      event.preventDefault();
    } else if (pass !== cpass) {
      cpassError.textContent = "❌ Passwords do not match!";
      event.preventDefault();
    } else {
      successMsg.textContent = "✅ Passwords match! Creating account...";
    }
  });
});

// //PFP right click and preview
// const fileInput = document.getElementById('fileInput');
// const pfpIcon = document.getElementById('profileIcon');
// const pfpImg = document.getElementById('profileImage');

// // Determine which element is visible (icon or image)
// const clickable = pfpIcon || pfpImg;

// // Open file picker when image/icon is clicked
// clickable.addEventListener('click', () => fileInput.click());

// // When file is selected
// fileInput.addEventListener('change', function() {
//   const file = this.files[0];
//   if (!file) return;

//   const reader = new FileReader();
//   reader.onload = function(e) {
//     if (pfpIcon) {
//       // Replace the icon with an <img> element
//       const newImg = document.createElement('img');
//       newImg.src = e.target.result;
//       newImg.className = 'rounded-circle border border-3 border-info shadow mb-3';
//       newImg.style.width = '150px';
//       newImg.style.height = '150px';
//       newImg.style.objectFit = 'cover';
//       newImg.style.cursor = 'pointer';
//       newImg.id = 'profileImage';
//       icon.replaceWith(newImg);
//     } else {
//       // If <img> already exists, just change its src
//       img.src = e.target.result;
//     }
//   };
//   reader.readAsDataURL(file);
// });