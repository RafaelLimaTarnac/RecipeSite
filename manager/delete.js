const delObjs = document.getElementsByClassName("delete-btn");
let pressCount = 5;
let lastPressed = null;
for(let i = 0; i <= delObjs.length - 1; i++){
	delObjs[i].addEventListener("click", DeleteDetection);
}

function DeleteDetection(event){
	if(pressCount > 0)
		event.preventDefault();
	else
		return;
	
	if(event.target != lastPressed){
		for(let i = 0; i <= delObjs.length - 1; i++){
			delObjs[i].parentElement.querySelector(".delete-par").innerHTML = '';
		}
		lastPressed = event.target;
		pressCount = 5;
	}
	
	const par = event.target.parentElement.querySelector(".delete-par");
	par.innerHTML = `Click ${pressCount} times to confirm`;
	pressCount--;
}