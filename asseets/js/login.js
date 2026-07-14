```javascript
/* =====================================================
   SGC v1.0
   login.js
   ===================================================== */

document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // Show / Hide Password
    // ==========================

    const password = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");

    if (toggle && password) {

        toggle.addEventListener("click", function () {

            if (password.type === "password") {

                password.type = "text";

                this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

            } else {

                password.type = "password";

                this.innerHTML = '<i class="fa-solid fa-eye"></i>';

            }

        });

    }

    // ==========================
    // SweetAlert Error
    // ==========================

    if (typeof error !== "undefined" && error !== "") {

        Swal.fire({

            icon: "error",

            title: "Connexion impossible",

            text: error,

            confirmButtonColor: "#16a34a",

            confirmButtonText: "OK"

        });

    }

    // ==========================
    // Loading Animation
    // ==========================

    const form = document.querySelector("form");

    if (form) {

        form.addEventListener("submit", function () {

            const btn = form.querySelector("button[type='submit']");

            btn.disabled = true;

            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Connexion...
            `;

        });

    }

    // ==========================
    // Input Animation
    // ==========================

    const inputs = document.querySelectorAll(".form-control");

    inputs.forEach(input => {

        input.addEventListener("focus", function () {

            this.parentElement.style.transform = "scale(1.02)";

        });

        input.addEventListener("blur", function () {

            this.parentElement.style.transform = "scale(1)";

        });

    });

});
```
