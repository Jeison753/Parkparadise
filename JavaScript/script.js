const icon = document.getElementById("icon");
const barraLateral = document.querySelector(".barraLateral");
const spans = document.querySelectorAll("span");
const menu = document.querySelector(".menu");
const main = document.querySelector("main");


menu.addEventListener("click", () => {
    barraLateral.classList.toggle("maxBarraLateral");
    if(barraLateral.classList.contains("maxBarraLateral")){
        menu.children[0].style.display = "none";
        menu.children[1].style.display = "block";
    }
    else{
        menu.children[0].style.display = "block";
        menu.children[1].style.display = "none";
    }
    if(window.innerWidth<=320){
       barraLateral.classList.add("miniBarraLateral");
       main.classList.add("minMain");
       spans.forEach((span)=>{
        span.classList.add("oculto");
       })
    }
});
icon.addEventListener("click", () => {
    icon.classList.toggle("mini");
    main.classList.toggle("minMain");
    barraLateral.classList.toggle("miniBarraLateral");
    spans.forEach((span)=>{
        span.classList.toggle("oculto");
    })
});