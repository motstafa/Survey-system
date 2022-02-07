
const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");
const selectBoxes = document.querySelectorAll("select");
let formStepsNum = 0;
var id
var saved=false;
var submit=document.querySelector("form").action="survey.html?id="+id;
    
window.onload=function(){
  // add access token to header as bearer
  window.axios.defaults.headers.common["Authorization"]=`Bearer ${sessionStorage.getItem("jwt")}`;
    
  console.log(sessionStorage.getItem('jwt'));
  const querryString = window.location.search;
  const urlParm = new URLSearchParams(querryString);
  id=urlParm.get('id');
  
 //console.log(id);

 axios.get('http://127.0.0.1:8000/api/survey/'+id,).then
(function(response){
  if(response.data.questionsAnswerd==3)
  {
    document.querySelector("h1").innerHTML="You have filled the form today,you can't edit it anymore";
    selectBoxes.forEach((box)=>{
      box.setAttribute("disabled","disabled");
    })
  }
  formStepsNum = response.data.questionsAnswerd-1;
  // update css
  updateProgressbar();
  document.getElementById('progress'+response.data.questionsAnswerd).classList.add('progress-step-active');
  document.getElementById('question'+response.data.questionsAnswerd).classList.add('form-step-active');

  // fill the select boxes with api data
  document.querySelector('#sport').value=response.data.Sport;
  document.querySelector('#games').value=response.data.videoGames;
  document.querySelector('#tv').value=response.data.TV;
  console.log(response);
}).catch(function(error){
  
  // update css
  formStepsNum=0;
  document.getElementById('progress1').classList.add('progress-step-active');
  document.getElementById('question1').classList.add('form-step-active');

  console.log(error)
})
}


function saveData()
{
  return new Promise((resolve,reject)=>{
    var sport= document.querySelector('#sport').value;
    var games= document.querySelector('#games').value;
    var tv= document.querySelector('#tv').value;
    var questionsAnswerd=0;
    selectBoxes.forEach((select)=>
    {
      if(select.value!='null'){
        questionsAnswerd++;
      }
    })
         
      setTimeout(()=>{
        var article={'id_user':id,'videoGames':games,'TV':tv,'Sport':sport,'questionsAnswerd':questionsAnswerd};
        axios.post('http://127.0.0.1:8000/api/survey/',article,).then(
        function(response)
        {
          console.log(response);
          saved= true;
          resolve();
        }
        ).catch(function(error)
        {
          saved=false;
          alert(error.status);
        })
      ;} , 800
      );
  });
}
  
// set listner for logout 
document.getElementById('logout').addEventListener("click",async function(){
  
  //setTimeout(()=>{
  
    await saveData()
    if(saved){
      saved=false;
      //logout
    axios.post('http://127.0.0.1:8000/api/logout',
  ).then(function(response){
    console.log(response);
    window.location.href ="login.html";
  }).catch(function(error){
    console.log(error);
  });
  
  } //location.reload();
  //},800);
  //saveData();
  
  
})

nextBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    if(selectBoxes[formStepsNum].value=="null")
      {window.alert("please select an answer")}
      else{
              formStepsNum++;
              updateFormSteps();
              updateProgressbar();
          }
  });
});

prevBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    formStepsNum--;
    updateFormSteps();
    updateProgressbar();
  });
});

function updateFormSteps() {
  formSteps.forEach((formStep) => {
    formStep.classList.contains("form-step-active") &&
      formStep.classList.remove("form-step-active");
  });

  formSteps[formStepsNum].classList.add("form-step-active");
}

function updateProgressbar() {
  progressSteps.forEach((progressStep, idx) => {
    if (idx < formStepsNum + 1) {
      progressStep.classList.add("progress-step-active");
    } else {
      progressStep.classList.remove("progress-step-active");
    }
  });


  // set listner for sumbit button
  document.querySelector("#submit").addEventListener("click",async function()
  {
  await saveData()

  if(saved){
    saved=false;
    location.reload();
  }
  

  });
  
  
  const progressActive = document.querySelectorAll(".progress-step-active");

  progress.style.width =
    ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";  
}