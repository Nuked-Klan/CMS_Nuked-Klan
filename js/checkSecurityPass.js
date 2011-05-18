function evalPwd(pw){
	var counter= 0;
	var minlength = 4;

	if (pw.length >= minlength){
		counter = counter + 1;
	}
	
	if (pw.match(/[A-Z]/)){
		counter = counter + 2;
	}
	
	if (pw.match(/[a-z]/)){
		counter = counter + 1;
	}
	
	if (pw.match(/[0-9]/)){
		counter = counter + 2;
	}
	
	if (pw.match(/[^A-Za-z0-9]/)){
		counter = counter + 2;
	}
	
	if (counter <= 2){
		document.getElementById("weak").className = "red";
		document.getElementById("medium").className = "nrm";
		document.getElementById("strong").className = "nrm";
	}
	else if (counter <= 5){
		document.getElementById("weak").className = "yellow";
		document.getElementById("medium").className = "yellow";
		document.getElementById("strong").className = "nrm";
	}
	else if (counter <= 6){
		document.getElementById("weak").className = "green";
		document.getElementById("medium").className = "green";
		document.getElementById("strong").className = "green";
	}
}