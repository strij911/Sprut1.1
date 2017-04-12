function absolutePosition(el) {
    var
        found,
        left = 0,
        top = 0,
        width = 0,
        height = 0,
        offsetBase = absolutePosition.offsetBase;
    if (!offsetBase && document.body) {
        offsetBase = absolutePosition.offsetBase = document.createElement('div');
        offsetBase.style.cssText = 'position:absolute;left:0;top:0';
        document.body.appendChild(offsetBase);
    }
    if (el && el.ownerDocument === document && 'getBoundingClientRect' in el && offsetBase) {
        var boundingRect = el.getBoundingClientRect();
        var baseRect = offsetBase.getBoundingClientRect();
        found = true;
        left = boundingRect.left - baseRect.left;
        top = boundingRect.top - baseRect.top;
        width = boundingRect.right - boundingRect.left;
        height = boundingRect.bottom - boundingRect.top;
    }
    return {
        found: found,
        left: left,
        top: top,
        width: width,
        height: height,
        right: left + width,
        bottom: top + height
    };
}

function positionAt(anchor, elem) {

      var anchorCoords = absolutePosition(anchor);

      elem.style.left = (anchorCoords.left-200) + "px";
      elem.style.top = (anchorCoords.bottom-30) + "px";
}
	
function strstr(haystack, needle, bool) {
    var pos = 0;

    haystack += "";
    pos = haystack.indexOf(needle); if (pos == -1) {
        return false;
    } else {
        if (bool) {
            return haystack.substr(0, pos);
        } else {
            return haystack.slice(pos);
        }
    }
}
function XmlHttp()
	{
		var xmlhttp;
		try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
		catch(e)
	{
		try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
		catch (E) {xmlhttp = false;}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
	{
		xmlhttp = new XMLHttpRequest();
	}
		return xmlhttp;
	}
	 
function ajax(param)
	{
		if (window.XMLHttpRequest) req = new XmlHttp();
		method=(!param.method ? "POST" : param.method.toUpperCase());

		if(method=="GET")
		{
		   send=null;
		   param.url=param.url+"&ajax=true";
		}
		else
		{
		   send="";
		   for (var i in param.data) send+= i+"="+param.data[i]+"&";
		   send=send+"ajax=true";
		}

		req.open(method, param.url, true);
		if(param.statbox)document.getElementById(param.statbox).innerHTML = '<img src="images/wait.gif">';
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(send);
		req.onreadystatechange = function()
		{
		   if (req.readyState == 4 && req.status == 200) //если ответ положительный
		   {
			   if(param.success)param.success(req.responseText);
		   }
		}
	}
