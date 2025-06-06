const addable = document.getElementById("Addable-Form");
const removable = document.getElementById("Removable-Form");
addable.addEventListener("click", AddTextInput);
removable.addEventListener("click", RemoveTextInput);
let child = [];

for(let i = 0; document.getElementById(`ing-${i}`); i++){
	child.push(document.getElementById(`ing-${i}`).parentElement.parentElement);
}


function AddTextInput(event){
	event.preventDefault();
	entry = document.createElement("tr");
	entry.innerHTML = `<tr><th>Ingredient</th><td><input type='text' name='ing-${child.length}' id='ing-${child.length}'></td></tr>`;
	entry.innerHTML += `<tr><th>Quantity</th><td><input type='text' name='qnt-${child.length}' id='qnt-${child.length}'></td></tr>`;
	
	let table = document.getElementById("Addable-table");
	if(table.querySelector("tbody"))
		table = table.querySelector("tbody");
	child.push(table.appendChild(entry));
}
function RemoveTextInput(event){
	event.preventDefault();
	if(child.length <= 0)
		return;
	child[child.length - 1].remove();
	child.pop();
}