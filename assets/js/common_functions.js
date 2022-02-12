

//For Refresh a Page 
function reload()
{
	window.location.href=window.location.href;
}


//Added By Sachin Common Function for Validation on 22 Dec 2021

function CommonFunction(formname){
	var isEmptyy=NotEmpty(formname);
	if(isEmptyy==true)
	{
		var abc1=NumberOnly(formname);
		if(abc1==true)
		{
			var charonly11=CharOnly(formname);
			if(charonly11==true)
			{
				var IsGst1=IsGst(formname);
				if(IsGst1==true)
				{
					var IsContact1=IsContact(formname);
					if(IsContact1==true)
					{
						var IsPan1=IsPan(formname);
						if(IsPan1==true)
						{
							var IsAdhar1=IsAdhar(formname);
							if(IsAdhar1==true)
							{

								return true;
							}
						}
					}

				}
			}
		}
	}
	return false;
}


function validateAllTextBoxes() { 
	var inputControls = document.getElementsByTagName("input");
	for (i = 0; i < inputControls.length; i++) {
		if (inputControls[i].type == "text" && inputControls[i].value == "") {
			alert("Enter Value in  " + inputControls[i].name);
			inputControls[i].style.background = '#BBD6E8';
			inputControls[i].focus();
			return false;
		}else {
			
		}
	} 
	return true;
}

function NumberOnly(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("NumberOnly");
	
	var numbers =/^\d+$/;

	for (i = 0; i < inputControls.length; i++) 
	{

		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			if (!inputControls[i].value.match(numbers) && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				inputControls[i].focus(); 
				alert(inputControls[i].name+" Should Contain Numbers Only");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}
		}
	}
	return true;
}

//Validation For Character only
function IsGst(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("IsGst");

	for (i = 0; i < inputControls.length; i++) 
	{
		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			let regTest = /[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9A-Za-z]{1}[Z]{1}[0-9a-zA-Z]{1}/.test(inputControls[i].value);
			if (!regTest && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				alert(inputControls[i].name+" Please Enter Proper GST no Format");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}

		}
	}	
	return true;
}
function IsPan(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("IsPan");

	for (i = 0; i < inputControls.length; i++) 
	{
		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			let regTest = /[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}/.test(inputControls[i].value);
			if (!regTest && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				alert(inputControls[i].name+" Please Enter Proper PAN no Format");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}

		}
	}	
	return true;
}
function IsAdhar(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("IsAdhar");

	for (i = 0; i < inputControls.length; i++) 
	{
		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			let regTest = /[0-9]{12}/.test(inputControls[i].value);
			if (!regTest && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				alert(inputControls[i].name+" Please Enter Proper Adhar no Format");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}

		}
	}	
	return true;
}

function IsContact(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("IsContact");

	for (i = 0; i < inputControls.length; i++) 
	{
		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			let regTest = /[0-9]{10}/.test(inputControls[i].value);
			if (!regTest && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				alert(inputControls[i].name+" Please Enter Proper Contact no Format");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}

		}
	}	
	return true;
}


function CharOnly(formname)
{
	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("CharOnly");
	var letters = /^[A-Za-z ]*$/;
	for (i = 0; i < inputControls.length; i++) 
	{
		if (inputControls[i].value != ""){
			var idd=inputControls[i].id;
			if (!inputControls[i].value.match(letters) && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
			{
				alert(inputControls[i].name+" Should Contain Alphabet Character Only");
				inputControls[i].value="";
				inputControls[i].focus();
				return false;
			}

		}
	}	
	return true;
}


function NotEmpty(formname) {

	var thisform=formname.name;
	var inputControls = document.forms[thisform].getElementsByClassName("NotEmpty");
	
	for (i = 0; i < inputControls.length; i++) 
	{
		var idd=inputControls[i].id;
		if (inputControls[i].value == "" && !$('#'+idd+'').is(":hidden") && !$('#'+idd+'').is(":disabled")) 
		{
			alert("Enter Value in  " + inputControls[i].name);
			return false;
		} 
	}
	return true;
}

