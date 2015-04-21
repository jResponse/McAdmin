/***************************************************
Copyright:jReply LLC, 2015. https://jresponse.net
Demo:http://jresponse.co/readmin
Comments & suggestions:contact@jreply.com
Licensed MIT:http://choosealicense.com/licenses/mit/
****************************************************/
Boolean.prototype.intval = function(places)
{
 places = ('undefined' == typeof(places))?0:places; 
 return (~~this) << places;
}

String.prototype.format = function (args)
{
 var newStr = this,key;
	for (key in args) {newStr = newStr.replace('{' + key + '}', args[key]);}
 return newStr;
}


String.prototype.reverse=function(){return this.split("").reverse().join("");};

function fluidDialog()
{
	var $visible = $(".ui-dialog:visible");
	$visible.each(function()
	{
  var $this = $(this);
		var dialog = $this.find(".ui-dialog-content").data("ui-dialog");
		if (dialog.options.fluid)
		{
   var wWidth = $(window).width();
   if (wWidth < dialog.options.maxWidth + 50)
   {this.css("max-width", "90%");} else 
   {$this.css("max-width", dialog.options.maxWidth);}
			
			if (dialog.options.hasOwnProperty('minWidth')) 
			$this.css("min-width", dialog.options.minWidth + 'px');
   
			if (dialog.options.hasOwnProperty('minHeight')) 
			$this.css("min-height", dialog.options.minHeight + 'px');
   
			if (dialog.options.hasOwnProperty('maxHeight')) 
			$this.css("max-height", dialog.options.maxHeight + 'px');

			dialog.option("position", dialog.options.position);
  }
 });
}

$(document).ready(function()
{
 try
	{
  _stats = JSON.parse(_stats);
	} catch(err){_stats = null;}	
 $('.dabtn').button();
	$('#btnFilter').on('click',doFilter);
	$('#btnAdd').on('click',doAdd);
	$('#btnDelete').on('click',doDelete);
	$('#btnFlush').on('click',doFlush);
	$('#selHits').on('click',showKeyData);
	doFilter();
});
//-------------------- Utils ---------------
function escapedValue(v){return $('<div/>').text(v).html();}

function showStats()
{
 var info = '';
 if (null !== _stats)
	{
	 info = "<table><tr><td><b>Version</b>:{s0}</td><td><b>Uptime(s)</b>:{s1}</td><td><b>Entries</b>:{s2}";
		info += "<td><b>Usage</b>:{s3}%</td></tr></table>";
		info = info.format({s0:_stats.v,s1:_stats.ut,s2:_stats.i,s3:_stats.upc});
	}
	$('#divInfo').show().html(info);
}

function showDeleteHint()
{
 var txt = 'To enable the <b>Delete</b> button hold down the CTRL key &amp; click a hit entry';	
 $('#divInfo').show().html(txt).fadeOut(5000,showStats);
}

function showError(err){$('#divInfo').html(err).fadeOut(5000,showDeleteHint);}

function noStringCheck(str,msg)
{if ((undefined == str) || (0 == str.length)) throw(msg);}
//------------------- Commands -------------
function doFilter()
{
	var filter = $('#inpFilter').val();
	filter = (0 === filter.length)?'*':filter;
	filter = encodeURIComponent(filter);
	var url = "find.php?filter={fl}".format({fl:filter});
 $.get(url,afterFilter);
}

function afterFilter(data,rslt)
{
 if ('success' == rslt)
	{
	 try
		{
		 data = JSON.parse(data);
			switch(data.code)
			{
			 case -999:showError('The filter string must blank or a valid PHP regex');break;
				case -1:showError('Unable to connect to Memcached server');break;
				default:_stats = data.stats;
				        fillHits(data.hits);
			}
		} catch(err) {showError(err);}
	}
}

function fillHits(hits)
{
 var hit,options = '',count = hits.length,nhits = (1 === count)?' hit':' hits';
	for(var i=0;i < count;i++)
	{
	 hit = hits[i];
		options += "<option data-v='{vv}'>{kk}</option>".format({vv:hit.value,kk:hit.key});
	}
	
	$('#selHits').html(options);
	showError(count + nhits);
	$('#btnDelete').attr('disabled',true).button('refresh');
}

function doDelete()
{
 var key = $('#selHits option:selected').text(),
	    url,filter = $('#inpFilter').val();
	filter = (0 === filter.length)?'*':filter;
 url = "delete.php?key={kk}&filter={ff}".format({ff:filter,kk:key});
	$.get(url,afterDelete);
}

function afterDelete(data,rslt)
{
 if ('success' == rslt)
	{
	 try
		{
		 data = JSON.parse(data);
		 switch(data.code)
			{
			 case -999:showError('The filter string must blank or a valid PHP regex');break;
			 case -2:showError('Unable to connect to Memcached server');break;
				case -1:showError('The specified key does not exist');break;
				case 0:showError('Operation failed');break;
				case 1:_stats = data.stats;
				       fillHits(data.hits);
											break;
			}
		} catch(err){showError(err);}
	}
}

function doAdd(){showEditor();}

function doFlush()
{
 var really = window.confirm('Do you REALLY want to flush the entire Memcached database?');
	if (really) $.get('flush.php',afterFlush);
}

function afterFlush(data,rslt)
{
 if ('success' == rslt)
	{
	 try
		{
		 data = JSON.parse(data);
			switch(data.code)
			{
			 case -1:showError('Unable to connect to Memcached server');break;
				case 0:showError('Operation failed');break;
				case 1:showError('All keys have been flushed');
											$('#selHits').html('');
											setTimeout(doFilter,5000);
           break;  
			}
		} catch(err){showError(err);}	
	}
}

function showKeyData(e)
{
 var toEdit = !(e.ctrlKey || e.shiftKey);
 $('#btnDelete').attr('disabled',toEdit).button('refresh');
	if (toEdit)
	{
  var opt = $('#selHits option:selected');
	 showEditor(opt.text(),opt.data('v'));
	}	
}
//*************************** Edit/Add **********************
function showEditor(key,value)
{
 var btns = {Update:doStringUpdate,Cancel:closeStringEditor};
	$('#diaStringEd').dialog({title:'Memcached Entry Editor',
	                          position:{my:'center top',at:'center top',of:window}, 
	                          modal:true,resizable:false,resizable:false,
																											minWidth:500,minHeight:400,
	                          open:function(){fillStringData(key,value);},
																											beforeClose:cleanUpStrings,
																											buttons:btns});
}

function doStringUpdate()
{
 var key = $('#inpStrKey').val(),
	    valu = $('#txaStrValue').val(),
					ttl = $('#inpStrTTL').val(),
					filter = $('#inpFilter').val();
	filter = (0 === filter.length)?'*':filter;
	
	try
 {				
	 noStringCheck(key,'Please provide a key!');
		noStringCheck(valu,'The value cannot be a null string');
		valu = escapedValue(valu);
  $.post('update.php',{k:key,v:valu,t:ttl,f:filter},afterUpdates);
	} catch(err){showError(err);}
}

function afterUpdates(data,rslt)
{
 if ('success' == rslt)
	{
  try
		{
		 data = JSON.parse(data);
			switch(data.code)
			{
			 case -999:showError('The filter string must blank or a valid PHP regex');break;
			 case -1:showError('Unable to connect to Memcached server');break;
				case 1:_stats = data.stats;
				       if (false !== data.had)
											{
            console.log("Old Value:{ov}".format({ov:data.had}));
            alert('The old value has been copied to the Javascript console');
											}
											fillHits(data.hits);  
           closeStringEditor();
			}
		} catch(err){showError(err);}	
	} 
}

function fillStringData(key,value)
{
 if (undefined !== key)
	{
  $('#inpStrKey').val(key);
 	$('#txaStrValue').val(value);
	 $('#inpStrTTL').val('');
	}
}

function cleanUpStrings()
{
 $('#inpStrKey').val('');
	$('#txaStrValue').val('');
	$('#inpStrTTL').val('');
}

function closeStringEditor(){$('#diaStringEd').dialog('close');}
