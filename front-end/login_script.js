const sub = document.getElementById("submit");
 

// Short duration JWT token (5-10 min)
 function getJwtToken() {
  return sessionStorage.getItem("jwt")
  }
   
 function setJwtToken(token) {
  sessionStorage.setItem("jwt", token)
}

// Longer duration refresh token (30-60 min)
function getRefreshToken() {
  return sessionStorage.getItem("refreshToken")
}

 function setRefreshToken(token) {
  sessionStorage.setItem("refreshToken", token)
}

{getJwtToken,setJwtToken,getRefreshToken,setRefreshToken};

 function login()
  {
    var userName = document.getElementById("email").value;
    
    var password = document.getElementById("password").value;
    axios.post('http://127.0.0.1:8000/api/login',{
            userName,
            password
          })
          .then(function (response) {
            console.log(response);
            if(response.status==200)
            {
              setJwtToken(response.data.access_token);
              setRefreshToken(response.data.refresh_token);
              if(getJwtToken()){
              window.location.href = "survey.html?id="+response.data.id;
              }
            }
          })
          .catch(function (error) {
            alert(error)
          });        

    }      

sub.addEventListener('click',function(){
        login();
       })