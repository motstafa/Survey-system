
window.onload=function(){
document.querySelector("button").addEventListener("click",function(){
  
  var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  First_Name=document.getElementById("First_Name").value;
  Last_Name=document.getElementById("Last_Name").value;
  email =document.getElementById("email").value;
  password1=document.getElementById("password1").value;
  password2 =document.getElementById("password2").value;
  if(!email.match(mailformat))
  {
      alert("email is not valid");
      document.getElementById("email").focus();
      return false
  }
  if(password1!=password2)
  {
      alert("please confirm your password");
      document.getElementById("password2").innerHTML="";
      document.getElementById("password2").focus();
      return false;
  }
 var acount={'First_Name':First_Name,'Last_Name':Last_Name,'userName':email,'password':password1,'password_confirmation':password2};
  axios.post('http://127.0.0.1:8000/api/register',acount).then((response)=>
  {
     console.log(response);
     alert('you have created an account');
     window.location.href="login.html";
  })
  .catch((error)=>
  {
    console.log(error);
    alert(error);
  })
})

}