// Show / hide password

function togglePassword(){

    let pass = document.getElementById("password");
    let icon = document.getElementById("eye");


    if(pass.type === "password"){

        pass.type="text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }else{

        pass.type="password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}



// Loading button

document.querySelector("form").addEventListener("submit",()=>{

    let btn=document.querySelector(".btn-login");

    btn.innerHTML=
    '<i class="fa-solid fa-spinner fa-spin"></i> Connexion...';

});