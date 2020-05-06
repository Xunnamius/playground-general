/*
JavaScript Document

Programming on the internet the way it was meant to be done.

By Xunnamius of Dark Gray.

Do not remove this header.
*/

window.onload = initPage;
var globals = {"planetData":{"planets":[],"count":0,"pointer":-1},"disabled":[],"userData":{"choices":{}},"randPointers":{}};
var timers = [];

function initPage()
{
	document.body.style.overflow = "hidden";
	document.getElementsByTagName("body")[0].scroll = "no";
	
	ajaxLoadData(
	{
		"loading":{ "load":function(){}, "unload":function(){} },
		"error":function(status)
		{
			var target = document.getElementById('planet_description');
			target.innerHTML = "Listing Error ("+status+")";
			target.className = target.className + " error";
		},
		"main":function(r)
		{
			var target = document.getElementById('planet_description');
			
			if(r.substr(0, 20) == "<!DOCTYPE html PUBLI")
			{
				target.innerHTML = "Read Error";
				target.className = target.className + " error";
			}
			
			else
			{
				var imgEle = document.getElementById("planet_image").getElementsByTagName("img")[0],
				selEle = document.getElementById("select");
				
				imgEle.onmouseover = selEle.onmouseover = function()
				{ selEle.className = ""; };
				
				imgEle.onmouseout = selEle.onmouseout = function()
				{ selEle.className = "hidden"; };
				
				imgEle.onclick = selEle.onclick = function()
				{
					if(globals['userData']['choices']['planet']) return;
					globals['userData']['choices']['planet'] = globals['planetData']['planets'][globals['planetData']['pointer']];
					var mover = document.getElementById("movement_wrapper"),
					parent = mover.parentNode;
					
					 // The Score is IE -> 1... for now
					if(DG_BROWSER['browser'] == "MSIE" && DG_BROWSER['version'] < 9)
					{
						document.getElementById("login").className = "button hidden";
						parent.removeChild(mover);
						document.getElementById("back").className = document.getElementById("next").className = "button";
						var child = buildPhase2();
						parent.appendChild(child);
					}
					
					else
					{
						var login = document.getElementById("login");
						
						shiftTargetInit({"direction":"left", "targetID":"movement_wrapper", "magnitude":1000,
							"pre":function()
							{ login.className = "button hidden"; },
							"post":function(){},
							"duration":1, "steps":1,
							"options":{"fade":-1, "manipulateScroll":false}
						});
						
						shiftTargetInit({"direction":"left", "targetID":"movement_wrapper2", "magnitude":1000,
							"pre":function()
							{ parent.appendChild(buildPhase2()); },
							"post":function()
							{
								var bBack = document.getElementById("back"), bNext = document.getElementById("next");
								bBack.className = bNext.className = "button";
								
								bBack.onclick = function()
								{
									shiftTargetInit({"direction":"right", "targetID":"movement_wrapper", "magnitude":2,
										"pre":function(){},
										"post":function(){},
										"duration":1, "steps":2,
										"options":{"fade":1, "manipulateScroll":false}
									});
									
									shiftTargetInit({"direction":"right", "targetID":"movement_wrapper2", "magnitude":2,
										"pre":function()
										{
											login.className = "button";
											bBack.className = bNext.className = "button hidden";
										},
										"post":function(){},
										"duration":0.5, "steps":2,
										"options":{"fade":-1, "manipulateScroll":false, "destroy":true}
									});
									
									globals['userData']['choices']['planet'] = null;
								};
							},
							"duration":1, "steps":1,
							"options":{"fade":1, "manipulateScroll":false, "startAt":"-1000px"}
						});
					};
				}
				
				document.getElementById("right").onclick = function()
				{ arrowScroll('right'); };
				
				document.getElementById("left").onclick = function()
				{ arrowScroll('left'); };
				
				globals['planetData']['planets'] = eval("("+r+")");
				globals['planetData']['count'] = globals['planetData']['planets'].length;
				if(globals['planetData']['count']){ arrowScroll('right'); }
				else
				{
					target.innerHTML = "No Data"+(r.length <= 20 ? " ("+r+")" : "");
					target.className = target.className + " error";
				}
			}
		},
		"target":DG_REAL_HOST+"?req=planets"
	});
}

/* Builds the second window and returns the result */
function buildPhase2()
{
	var wrapper = document.createElement("div"), data = globals['userData']['choices']['planet'], id = data['id'], height = 50*globals['userData']['y']/globals['userData']['x'];
	wrapper.id = "movement_wrapper2";
	wrapper.className = "movementDiv";
	wrapper.innerHTML = '<div id="bg2" class="bg"></div><div id="content2"><h2 id="title2" class="title">Naming Phase</h2><div id="planet2"><h3>You chose: <span>'+data['name']+'</span></h3><p id="planet_image2"><img src="'+"assets/images/planets/planet"+id+(id==1?".png":".jpg")+'" alt="'+data['name']+'" title="'+"Planet "+id+": "+data['name']+'" width="50" height="'+height+'" /></p></div><h3>Name Thy Planet</h3><input type="text" id="planet_name2" name="planet_name" maxlength="15" /><h3>Name Thy God</h3><input type="text" id="god_name" name="god_name" maxlength="15" /></div>';
	return wrapper;
}

function buildPhase3()
{
	var wrapper = document.createElement("div"), data = globals['userData']['choices']['name'];
	wrapper.id = "movement_wrapper3";
	wrapper.className = "movementDiv";
	wrapper.innerHTML = '';
	return wrapper;
}

/* Function to fade an element out from opac=1 -> opac=0 */
function setOpacity(/* element */ targetID, /* float */ level)
{
	target = document.getElementById(targetID);
	if(!target) return;
	target.style.filter = "alpha(opacity="+(level*100)+")"; 
	target.style.KhtmlOpacity = level;
	target.style.MozOpacity = level;
	target.style.opacity = level;
}

/* Function to fade an element in from opac=0 -> opac=1 */
function fadeIn(/* string */ targetID, /* integer */ duration, /* integer */ steps)
{
	duration = duration || 2000;
	steps = steps || 120
	for(var i = 0; i <= 1; i += (1 / steps))
		setTimeout("setOpacity('" + targetID + "'," + i + ")", i * duration);
}

/* Function to fade an element out from opac=1 -> opac=0 */
function fadeOut(/* string */ targetID, /* integer */ duration, /* integer */ steps)
{
	duration = duration || 2000;
	steps = steps || 120
  	for(i = 0; i <= 1; i += (1 / steps))
		setTimeout("setOpacity('" + targetID + "'," + Math.abs(1 - i) + ")", i * duration);
}

/* Info UI's grfx controller */
function shiftTargetInit(/* json */ settings)
{
	var
	duration = settings['duration'] || 1,
	duration = Math.abs(duration),
	steps = settings['steps'] || 10,
	actual = (1 / steps),
	
	direction = settings['direction'], dir = '',
	targetID = settings['targetID'],
	magnitude = settings['magnitude'],
	pointer = createPointer(),
	modifier = 1.0;
	
	switch(direction)
	{
		case "top": modifier = -1.0;
		case "bottom": dir = "top"; break;
		case "right": modifier = -1.0;
		default: dir = "right"; break;
	}
	
	globals['randPointers'][pointer]['endMag'] = magnitude - actual;
	globals['randPointers'][pointer]['postFunctionality'] = settings['post'];
	globals['randPointers'][pointer]['options'] = settings['options'];
	
	settings['pre']();
	if(settings['options']['manipulateScroll'])
	{
		document.body.style.overflow = "hidden";
		document.getElementsByTagName("body")[0].scroll = "no";
	}
	
	if((dir == "left" || dir == "right") && settings['options']['ieCompClassNameLR']) document.getElementById(targetID).className += (" "+settings['options']['ieCompClassNameLR']);
	else if(settings['options']['ieCompClassNameTB']) document.getElementById(targetID).className += (" "+settings['options']['ieCompClassNameTB']);
	
	if(settings['options']['fade'] == -1) fadeOut(targetID, (duration*1000)-(250*duration));
	else if(settings['options']['fade'] == 1) fadeIn(targetID, (duration*1000)-(250*duration));
	for(var i=1; i<=magnitude; i += actual)
		setTimeout("shiftTarget('" + targetID + "'," + i*modifier + ",'"+dir+"','"+pointer+"');", i * duration);
}

/* Moves the info UI box up and down */
function shiftTarget(/* element */ targetID, /* float */ magnitude, /* string */ dir, /* string */ pointer)
{
	var pointer = globals['randPointers'][pointer],
	target = document.getElementById(targetID);
	if(!pointer || !target) return;

	if(pointer['options']['startAt']) target.style[dir] = pointer['options']['startAt'];
	
	mag = parseFloat(target.style[dir] || pointer['endMag']);
	target.style[dir] = (mag-(mag-magnitude))+'px';
	
	//Restore handlers and stuff
	if(Math.abs(magnitude) > pointer['endMag'])
	{
		var cnr = pointer['options']['ieCompClassNameLR'] || pointer['options']['ieCompClassNameTB'] || false;
		
		if(pointer['options']['manipulateScroll'])
		{
			document.body.style.overflow = "auto";
			document.getElementsByTagName("body")[0].scroll = "yes";
		}
		
		if(cnr) target.className = target.className.slice(0, -(cnr.length));
		pointer['postFunctionality']();
		if(pointer['options']['destroy']) target.parentNode.removeChild(target);
	}
}

//Creates a random pointer
function createPointer(/* integer [ */ pointer /* ] */)
{
	var pointer = pointer || ("p"+Math.random()).replace('.','');
	globals['randPointers'][pointer] = {};
	return pointer;
}

function arrowScroll(dir)
{
	if(!globals['disabled'][dir])
	{
		document.getElementById("left").className = document.getElementById("right").className = "arrow disabled";
		globals['disabled']['left'] = globals['disabled']['right'] = true;
		
		var c = globals['planetData']['count'],
		p = globals['planetData']['pointer'] = (dir == 'left' ? globals['planetData']['pointer']-1 : globals['planetData']['pointer']+1),
		name = document.getElementById("planet_name"),
		image = document.getElementById("planet_image").getElementsByTagName("img")[0],
		desc = document.getElementById("planet_description");
		
		image.className = "invisible";
		image.src = "assets/images/loading.gif";
		image.width = 84;
		image.height = 64;
		image.className = "";
		image.alt = "Loading...";
		image.title = "Loading...";
		
		name.className = "hidden";
		name.innerHTML = "Loading...";
		desc.innerHTML = "Loading Planet Data...";
		
		var newImage = new Image();
		var id = globals['planetData']['planets'][p]['id'];
		newImage.src = newImage.name = "assets/images/planets/planet"+id+(id==1?".png":".jpg");
		
		//F****ng retard IE doesn't support new image onload event handling... *sigh*
		if(DG_BROWSER['browser'] == "MSIE") imageOnload(image, newImage, name, desc, p, c);
		else newImage.onload = function(){ imageOnload(image, newImage, name, desc, p, c); };
	}
}

function imageOnload(image, newImage, name, desc, p, c)
{
	var data = resize(newImage.width, newImage.height, 600, 350);
			
	name.className = "";
	name.innerHTML = globals['planetData']['planets'][p]['name'];
	desc.innerHTML = globals['planetData']['planets'][p]['desc'];
	
	image.alt = name.innerHTML;
	image.title = "Planet "+(p+1)+": "+image.alt;
	image.className = "invisible";
	image.src = newImage.src;
	image.width = data[0];
	image.height = data[1];
	image.className = "";
	
	globals['userData']['x'] = image.width;
	globals['userData']['y'] = image.height;
	
	if(p+2 <= c) { document.getElementById("right").className = "arrow"; globals['disabled']['right'] = false; }
	if(p-1 >= 0) { document.getElementById("left").className = "arrow"; globals['disabled']['left'] = false; }
}

/* Dark Tools used to load data asyncronously */
function xmlhttp()
{
	//First try the W3C Standard for creating xmlhttpRequest objects
	var request;
	try { request = new XMLHttpRequest(); }
	catch(x)
	{
		//God damn you Microsoft...
		var MSXMLHTTPObjects = ["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"];
		
		for(var i = 0; i < MSXMLHTTPObjects.length && !request; ++i)
		{
			try {request = new ActiveXObject(MSXMLHTTPObjects[i]);}
			catch(x) { request = null; }
		}
	}
	
	//Return the result
	return request;
}

function ajaxLoadData(/* json */ action)
{	
	action.loading.load();
	var XHR = xmlhttp();
	
	XHR.onreadystatechange = function()
	{
		if(XHR.readyState == 4)
		{
			action.loading.unload();
			var response = trim(XHR.responseText);
			if(XHR.status != 200 || !response) action.error(XHR.status);
			else action.main(response);
		}
	};
	
	target = action['target'];
	target = target.split("?", 2);
	
	//Hackers get owned here.
	var page = target[0];
	
	if(target[1])
	{
		target[1] = target[1].replace(/=/g, "*@-_+./").replace(/&/g, "/.+_-@*");
		target[1] = escape(target[1]);
		target[1] = target[1].replace(/\*@\-_\+\.\//g, "=").replace(/\/\.\+_\-@\*/g, "&");
	}
	
	var params = "ajax=1" + (target[1] ? "&" + target[1] : "");
	XHR.open("POST", page, true);
	XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	XHR.send(params);
}

/* From https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Objects/Array/indexOf. Make this into a Dark Tool. */
if(!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(/* mixed */ elt /*[, int from = 0 ]*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

//Trim a string (MAKE THIS A DARKTOOL!)
//Based on http://blog.stevenlevithan.com/archives/faster-trim-javascript
function trim(str) {
	var	str = str.replace(/^\s\s*/, ''),
		ws = /\s/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}

//Array element removal functionality
//NEEDS TO BE A DARKTOOL
if(!Array.prototype.del)
{
	Array.prototype.del = function(from, to) {
	  var rest = this.slice((to || from) + 1 || this.length);
	  this.length = from < 0 ? this.length + from : from;
	  return this.push.apply(this, rest);
	};
}


/* Resizes an image element/properties */
function resize(/* object Element [, int MaxX = 825 [, int MaxY = 310 ]] | int Width , int Height [, int MaxWidth = 825 [, int MaxHeight = 310 ]] */)
{
	//Sub-environment Instance Variables
	var width;
	var height;
	var maxX = maxX ? maxX : 825; //Max Width
	var maxY = maxY ? maxY : 310; //Max Height
	
	//Check the arguments for any inconcistencies
	function argumentCheck(arguments)
	{
		for(var i=0, x=arguments.length; i < x; ++i)
			if(!arguments[i] || typeof arguments[i] != "number") return false;
		return true;
	}
	
	//Computations
	function compute()
	{
		//Landscape
		if(width >= height)
		{
			var tmpY = height * maxX / width;
			if(tmpY <= maxY) maxY = tmpY;
			else maxX = width * maxY / height;
		}
		
		//Portrait
		else
		{
			var tmpX = width * maxY / height;
			if(tmpX <= maxX) maxX = tmpX;
			else maxY = height * maxX / width;
		}
	}
	
	//We have an element we want to resize
	//Returns nothing
	if(arguments.length == 1)
	{
		if(!arguments[0] || (typeof arguments[0] != "object" && typeof arguments[0] != "string")) return;
		
		//Obv an object
		if(typeof arguments[0] != "object")
		{
			width = arguments[0].width;
			height = arguments[0].height;
		}
		
		//An ID
		else
		{
			var el = document.getElementById(arguments[0]);
			if(!el) return;
			
			width = el.width;
			height = el.height;
		}
		
		compute();
		arguments[0].width = maxX;
		arguments[0].height = maxY;
	}
	
	//We have some numbers we want to transform
	//Returns [width, height]
	else if(arguments.length == 2)
	{
		if(!argumentCheck(arguments)) return;
		
		width = arguments[0];
		height = arguments[1];
		
		compute();
		return [maxX, maxY];
	}
	
	//We have an element we want to resize within a specific constraint
	//Returns nothing
	else if(arguments.length == 3)
	{
		if(!arguments[0] || !arguments[1] || !arguments[2] ||
		   (typeof arguments[0] != "object" && typeof arguments[0] != "string") || typeof arguments[1] != "number" || typeof arguments[2] != "number")
		     return;
		
		if(typeof arguments[0] != "object")
		{
			width = arguments[0].width;
			height = arguments[0].height;
		}
		
		else
		{
			var el = document.getElementById(arguments[0]);
			if(!el) return;
			
			width = el.width;
			height = el.height;
		}
		
		maxX = arguments[1];
		maxY = arguments[2];
		
		compute();
		arguments[0].width = maxX;
		arguments[0].height = maxY;
	}
	
	//We have some numbers we want to transform within a specific constraint
	//Returns [width, height]
	else if(arguments.length == 4)
	{
		if(!argumentCheck(arguments)) return;
		
		width = arguments[0];
		height = arguments[1];
		maxX = arguments[2];
		maxY = arguments[3];
		
		compute();
		return [maxX, maxY];
	}
}