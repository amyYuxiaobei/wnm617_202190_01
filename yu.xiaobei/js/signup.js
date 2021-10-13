

const checkSignupForm = () => {
    let user = $("#signup-username").val();
    let email = $("#signup-email").val();
    let pass = $('signup-password').val();
    let pass2 = $('#signup-password2').val();
 
    console.log(user,pass);
 
    if(pass == pass2) {
       // logged in
       console.log("sign up success");
       sessionStorage.userId = user;
       sessionStorage.pass = pass;
       $("#signup-form")[0].reset();
    } else {
       // not logged in
       console.log("sign up failure");
       sessionStorage.removeItem('userId');
    }
 }