async function AddTask(){
	var task = document.getElementById("newTask");
	if(!task){ return; }
	if(task.value.trim() == ""){ return ShowMsg(task,"You need to write something to save");}
	var name = task.value.trim();

	var res = await GetTaskByName(name);
	if(res && res.id){ return ShowMsg(task,"Already exists a task with this name"); }

	QueryData({
		uri: "tasks/AddTask",
		type: 'POST',
		data: {
			name: name
		}
	},async function(status,data){
		if(status != 201 && status != 200){
			return ShowMsg(task,"There is a problem creating the task");
		}
		var res = await GetTaskByName(name)
		if(res){
			var checked = (res.status == 'OK')? true:false;
			AddToList(res.name, "task_"+res.id, checked);
			task.value = "";
		}
	});
}

function AddToList(taskName, id = null, checked = false){
	var task = document.getElementById("newTask");
	var list = document.getElementById("taskList");
	if(!list){ return; }
	if(id && id.trim() == ""){
		id = taskName.replace(/[^a-z0-9]+/i,"");
	}
	if(document.querySelector("input#"+id)){
		return ShowMsg(task,"A task with this name already exists");
	}
	var li = document.createElement("li");
	li.classList.add("list-group-item","d-flex","justify-content-between")
	var div = document.createElement("div");
	div.classList.add("d-flex","justify-content-between");

	var input = document.createElement("input");
	input.setAttribute("id",id);
	input.setAttribute("name",id);
	input.classList.add("form-check-input");
	input.setAttribute("type","checkbox");
	input.value = id.replace(/[^0-9]+/i,"");//We only need numbers here...
	input.addEventListener("change",toggleTaskState)
	input.checked = (checked)? true:false;
	var label = document.createElement("label");
	label.classList.add("form-check-label");
	label.setAttribute("for",id);
	var span = document.createElement("span");
	span.textContent = taskName;
	span.classList.add("ms-1");
	label.appendChild(span);
	div.appendChild(input);
	div.appendChild(label);

	var btn = document.createElement("button");
	btn.classList.add("btn","btn-primary","float-end");
	btn.setAttribute("type","button");
	btn.textContent = "Edit";
	btn.addEventListener("click",()=>{ EditTask(btn); });

	li.appendChild(div);
	li.appendChild(btn);
	list.appendChild(li);
}

function EditTask(self){
	var parent = self.parentNode;
	if(!parent){ return; }
	var check = parent.querySelector("div>input");
	var span = parent.querySelector("div span");
	if(!check || !span){ return ShowMsg(self,"You cannot edit this task"); }
	var content = document.getElementById("taskContent");
	var editFrm = document.getElementById("taskEdit");
	if(!content || !editFrm){ return ShowMsg(self,"You cannot edit this task"); }
	var input = editFrm.querySelector("#editOptions>input");
	if(!input){ return ShowMsg(self,"You cannot edit this task"); }
	input.setAttribute("data-id",check.value);
	var title = editFrm.querySelector("#taskToEdit");
	title.innerHTML = span.textContent;
	input.value = span.textContent;

	if(!content.classList.contains("d-none")){
		content.classList.add("d-none");
	}
	editFrm.classList.remove("d-none");
}

function ShowMsg(element,msg){
	var tooltip = new bootstrap.Tooltip(element, {
		title: msg
	});
	tooltip.show();
	setTimeout(()=>{
		tooltip.hide();
		tooltip.disable();
		tooltip = null;
	},3000);
}

function CancelEdit(){
	var content = document.getElementById("taskContent");
	var editFrm = document.getElementById("taskEdit");
	if(!content || !editFrm){ return ShowMsg(self,"You cannot edit this task"); }
	if(!editFrm.classList.contains("d-none")){
		editFrm.classList.add("d-none");
	}
	var title = editFrm.querySelector("#taskToEdit");
	title.innerHTML = "";
	content.classList.remove("d-none");
}

async function SaveEditedTask(self){
	var input = document.getElementById("editInput");
	if(!input){ return; }
	if(input.value.trim() == ""){ return; }
	var target = input.getAttribute("data-id");
	if(isNaN(parseInt(target))){ return; }
	self.disabled = true;
	var taskData = await GetTaskByName(input.value);
	if(taskData && taskData.id != target){
		self.disabled = false;
		return ShowMsg(input,"Already exists a task with this name");
	}else if(taskData && taskData.id == target){
		self.disabled = false;
		return ShowMsg(input,"The name has not been modified");
	}

	var data = {
		task_id: target,
		name: input.value
	}
	var res = await SaveEdit(data);
	self.disabled = false;
	if(!res){
		return ShowMsg(input,"There is a problem editing this task");
	}
	ResetTasks();
	ShowMsg(input,"Task edited succefully");
	input.value = "";
	input.removeAttribute("data-id");
	CancelEdit();
}

function toggleTaskState(){
	var id = parseInt(this.value);
	if(isNaN(id)){ return; }
	var data = {
		status: "PEND"
	}
	if(this.checked){
		data['status'] = "OK";
	}
	data['task_id'] = id;
	SaveEdit(data);
}

window.addEventListener("load",()=>{
	GetTasks();
});

function GetTasks(){
	var h4 = document.getElementById("taskNone");
	QueryData({
		uri: "tasks/list",
		type: "GET"
	},function(status,data){
		var added = false;
		if(data && typeof data == 'object'){
			for(var value of data){
				var id = value.id;
				id = "task_"+id;
				var name = value.name;
				var checked = false;
				if(value.status == 'OK'){
					checked = true;
				}
				AddToList(name,id,checked);
				added = true;
			}
		}
		if(h4){
			if(added){
				if(!h4.classList.contains("d-none")){
					h4.classList.add("d-none");
				}
			}else{
				h4.classList.remove("d-none");
			}
		}
	});
}

function SaveEdit(data){
	return new Promise(res=>{
		if(typeof data != 'object'){ res(false);return; }
		QueryData({
			uri: "tasks/EditTask",
			type: "POST",
			data: data
		}, function(status,data){
			if(status == 200){
				res(true);
				return;
			}
			res(false);
		});
	})
}

function GetTaskByName(name){
	name = name.trim();
	return new Promise(res=>{
		QueryData({
			uri: "tasks/GetByName",
			type: 'GET',
			data: {name: name}
		},function(status,data){
			if(status == 200){
				res(data);
				return;
			}
			res(null);
		});
	})
}

function ResetTasks(){
	var list = document.getElementById("taskList");
	list.innerHTML = "";
	GetTasks();
}