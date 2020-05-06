(function($)
{
	var gBindings = {}, gIBindings = [];
	
	function addHighlight(rows, fn)
	{
		var lastIndex = 0;
		for(var i=0, j=rows.length, k=j-1; i<j; ++i)
		{
			var item = rows[i],
				salient = 0, destroyed = 0, total = 0, contra = 0, AH = 0;
					
			if(item.hasClass('CI') || item.hasClass('illogical-highlight'))
			{
				k--;
				continue;
			}
			
			(function(rows, i, item)
			{
				var hl = item.getElement('td.highlights').get('text').toInt();
				
				if(!item.get('rel') && hl)
				{
					item.set('rel', String.uniqueID());
					item.set('data-bound', (item.hasClass('local')?'L':'R')+hl);
					
					if(total < hl) total = hl;
					if(item.hasClass('SP')) salient++;
					if(item.hasClass('CP')) destroyed++;
					if(item.hasClass('CD')) contra++;
					if(item.hasClass('AH')) AH++;
					
					rows.getElements('td.highlights').slice(lastIndex).some(function(itm, index)
					{
						var itmhl = itm[0].get('text').toInt();
						
						if(itmhl)
						{
							if(itmhl == hl)
							{
								itm.getParent().set('rel', item.get('rel'));
								itm.getParent().set('data-bound', item.get('data-bound'));
							}
							
							else
							{
								lastIndex += index;
								return true;
							}
						}
					});
				}
				
				if(i == k) (function(){ fn(total, salient, destroyed, contra, AH); }).delay(1000);
			}).delay(50+(25*i), this, [rows, i, item]);
		}
	}
	
	function init(loadHandler, modalXMLData)
	{
		addHighlight($$('div#convobox tbody tr.remote'), function(remoteTotal, remoteSalient, remoteDestroyed, remoteContra, remoteAH)
		{
			addHighlight($$('div#convobox tbody tr.local'), function(localTotal, localSalient, localDestroyed, localContra, localAH)
			{
				// Add labels
				var css = {}; // garbage firefox -_-
				if(Browser.firefox)
					css = { position: 'relative', float: 'right', right: 890 };
				
				$('point-beginning').getElement('td.msg').grab(new Element('span.label.label-info', { text: 'Beginning', styles: css }));
				$('point-firstAH').getElement('td.msg').grab(new Element('span.label.label-info', { text: 'First Ad Hominem', styles: css }));
				$('point-degradation').getElement('td.msg').grab(new Element('span.label.label-info', { text: 'Initial Degradation', styles: css }));
				$('point-hypocrisy').getElement('td.msg').grab(new Element('span.label.label-info', { text: 'Hypocrisy', styles: css }));
				$('point-illogicalEOA').getElement('td.msg').grab(new Element('span.label.label-info', { text: 'Jibberish Time!', styles: css }));
				$$('.important').getElement('td.msg').grab(new Element('span.label.label-important', { text: 'IMPORTANT', styles: css }));
				
				// Tally points
				var local = $$('div#contribox tbody tr:first-child')[0],
					remote = $$('div#contribox tbody tr:last-child')[0],
					
					tally = function(domain, str, data)
					{
						domain.getElement('.total-adhominem').set('text', data.ah);
						domain.getElement('.total-contradictions').set('text', data.cd);
						domain.getElement('.total-destroyed').set('text', data.cp);
						domain.getElement('.total-salient').set('text', data.sp);
						domain.getElement('.total-points').set('text', data.tp);
					};
				
				tally(local, 'local', { tp: localTotal, sp: localSalient, cp: localDestroyed, cd: localContra, ah: localAH });
				tally(remote, 'remote', { tp: remoteTotal, sp: remoteSalient, cp: remoteDestroyed, cd: remoteContra, ah: remoteAH });
				
				/* Parse XML modal data */
				
				var expandReference = function(item)
				{
					var a = document.createElement('a'),
						b = item.getAttribute('bind');
					
					a.setAttribute('class', 'reference');
					a.setAttribute('data-bind', b);
					
					if(b == 'GOT')
					{
						a.setAttribute('href', 'http://tinyurl.com/88lookd');
						a.appendChild(document.createTextNode('Game of Thrones 19'));
					}
					
					else
					{
						a.setAttribute('href', '#');
						a.appendChild(document.createTextNode('##'+b));
					}
					
					return a;
				},
				
				processElement = function(node, newEl)
				{
					var inode = node.firstChild;
					while(inode)
					{
						if(inode.nodeType == 1 && inode.tagName == 'ref')
							newEl[1].grab(expandReference(inode));
						
						else
							newEl[1].grab(document.importNode(inode, true));
						
						inode = inode.nextSibling;
					}
					
					return newEl;
				};
				
				Array.each(modalXMLData.getElementsByTagName('data'), function(item)
				{
					var bind = item.getAttribute('bind');
					
					if(bind && bind.length >= 2)
					{
						var node 	= item.firstChild,
							parent 	= new Element('div.window-accordion');
						
						while(node)
						{
							if(node.tagName == 'row' && node.nodeType == 1)
							{
								var type = node.getAttribute('type'),
									subb = node.getAttribute('sub-bind'),
									fer = node.getAttribute('for'),
									newEl = [new Element('h4', { text: type, 'class': 'type-'+type.toLowerCase() }), new Element('div')];
								
								if(subb) newEl[0].set('data-sub-bind', subb);
								if(fer)  newEl[0].set('data-for', fer);
								newEl = processElement(node, newEl);
								
								switch(type)
								{
									case 'Supports':
									case 'Supporters':
									case 'Counters':
									case 'Counterers':
										newEl[1].getChildren().each(function(item, index, arr)
										{
											if(index == arr.length-1) return;
											
											var text = ' and ';
											if(arr.length > 2)
											{
												text = ', ';
												if(index == arr.length-2)
													text = ', and ';
											}
											
											(new Element('span', { text: text })).inject(item, 'after');
										});
										break;
										
									case 'Clarafication':
									case 'Comment':
									case 'RFC':
									case 'DCP':
									case 'Contradiction':
									case 'Concession':
									case 'Acknowledgement':
									case 'Reaffirm':
										break;
										
									default:
										throw 'Encountered invalid row type "'+type+'" in modal_data.xml!';
										break;
								}
								
								newEl[1] = (new Element('div.accordion-content')).grab(newEl[1]);
								
								if(newEl[0].get('data-for'))
									newEl[0].grab(new Element('span', { html: 'For: <span>" '+newEl[0].get('data-for')+' "</span>' }));
								
								if(newEl[0].get('data-sub-bind'))
								{
									var line = $$('div#convobox tr[data-bound="'+bind+'"] td.msg')[subb-1],
										h5 	 = parent.getElements('h5[data-rel="'+bind+'.'+subb+'"]')[0];
									
									if(!line) throw 'Invalid data sub-bind "'+subb+'" @ '+bind;
									
									if(h5)
									{
										var sib = h5.getNext('h5');
										if(sib)
											newEl.each(function(item){ item.inject(sib, 'before'); });
										else
											parent.adopt.apply(parent, newEl);
									}
									
									else
									{
										newEl.unshift(new Element('h5', { html: 'Line: <span>'+line.get('text')+'</span>', 'data-rel': bind+'.'+subb }));
										parent.adopt.apply(parent, newEl);
									}
								}
								
								else newEl.reverse().each(function(item){ parent.grab(item, 'top'); });
							}
							
							else if(node.tagName)
								throw 'Encountered invalid element "'+node.tagName+'" in modal_data.xml!';
							node = node.nextSibling;
						}
						
						gBindings[bind] = parent;
					}
					
					else if(bind && bind.length == 1 && bind.toInt())
						gIBindings[bind.toInt()] = document.importNode(item, true).textContent.trim();
				});
				
				var makeConnections = function(item, hl, type, oppositeType, oppositeText)
				{
					item.getElements(type+'+div.accordion-content div .reference').each(function(itm)
					{
						var db = itm.get('data-bind'),
							target = gBindings[db],
							reference = expandReference(new Element('ref', { 'bind': hl }));
						
						if(itm.get('data-bind') == hl)
							return console.log('Skipped:', hl);
						
						if(!target)
							target = gBindings[db] = new Element('div.window-accordion');
						
						if(target.getElement(oppositeType))
						{
							target = target.getElement(oppositeType+'+div.accordion-content div');
							if(!target.getElement('a[data-bind="'+reference.get('data-bind')+'"]'))
							{
								if(!target.getElement('.along-with'))
									target.grab(new Element('span.along-with', { text: ' along with ' }));
								else
									target.grab(new Element('span', { text: ', ' }));
								target.grab(reference);
							}
						}

						else
						{
							newEl = (new Element('div.accordion-content')).grab((new Element('div')).grab(reference));
							target.grab(newEl, 'top');
							target.grab(new Element('h4'+oppositeType, { text: oppositeText }), 'top');
						}
					});
				};
				
				// Connect supporters to support, counterers to counters
				Object.each(gBindings, function(item, hl)
				{
					makeConnections(item, hl, '.type-supporters', '.type-supports', 'Supports');
					makeConnections(item, hl, '.type-supports', '.type-supporters', 'Supporters');
					makeConnections(item, hl, '.type-counterers', '.type-counters', 'Counters');
					makeConnections(item, hl, '.type-counters', '.type-counterers', 'Counterers');
				});
				
				// Fade in
				$('wrapper').set('tween', { duration: 2000 }).setStyle('display', 'block').tween('opacity', [0, 1]);
				clearInterval(loadHandler);
				$('loading').fade('out').dispose();
			});
		});
	}
	
	window.addEvent('domready', function()
	{
		if(Browser.ie)
			return alert('Internet Explorer is not supported. Try a different browser.');
		
		var loadHandler = dotLoader.periodical(LOADER_DOT_DELAY),
			tbody  		= $$('div#convobox table.table tbody')[0],
			openList	= {}, // Keeps a list of all open windowz
			y = -100;
		
		// Slow scroll!
		$$('div#interestbox, #back2top').addEvent('click:relay(a)', function(e, target)
		{
			e.stop();
			if(this == $('back2top')) y = 0;
			(new Fx.Scroll(window, {offset:{x:0,y:y}})).toElement($(target.get('href').split('#')[1]), 'y');
		});
		
		$$('body').addEvent('click:relay(a.reference)', function(e, target)
		{
			e.stop();
			(new Fx.Scroll(window)).toElement($$('div#convobox table.table tbody tr[data-bound="'+target.get('data-bind')+'"]')[0], 'y');
		});
		
		tbody.addEvent('click:relay(tr)', function(e, target)
		{
			var scrl = window.getScroll(), cd = null,
				scrlleftamt = (target.hasClass('local') ? 190 : (target.hasClass('remote') ? -190 : 0)),
				rel = target.get('rel') || 'bad',
				hl = target.getElement('td.highlights').get('text').toInt(),
				hlKey = (this.hasClass('local')?'L':'R')+hl,
				title = new Element('span', { text: 'The Breakdown', 'class': 'side-'+(this.hasClass('local')?'local':'remote') });
			
			if(hl)
			{
				cd = gBindings[hlKey] ? gBindings[hlKey].outerHTML : 'Nothing to see here :P';
				title.appendText(' : Point '+hlKey);
			}
			
			else if(rel != 'bad' && target.get('rel').length == 1 && target.get('rel').toInt())
			{
				cd = gIBindings[target.get('rel').toInt()];
				title.appendText(' of Illogic');
			}
			
			if(openList[rel])
				openList[rel]._display(true);
			
			else
			{
				var sm = new SimpleModal({
					overlayOpacity: 0,
					offsetTop: scrl.y.toInt()+140+Number.random(-30, 60),
					donotfollow: true,
					offsetLeft:	scrlleftamt ? scrlleftamt+Number.random(-40, 40) : 0,
					btn_ok: 'Alert button',
					width: 500,
					rel: rel,
					
					onAppend: function()
					{
						new Fx.Accordion($$('#'+sm.id+' .window-accordion')[0], '#'+sm.id+' .window-accordion h4', '#'+sm.id+' .window-accordion .accordion-content');
					},
					
					onHide: function(){ delete openList[this.options.rel]; }
				});
				
				sm.addButton('Close', 'btn primary', function(){ this.hide(); });
				
				sm.show({
					model: 'modal',
					title: title.outerHTML,
					contents: cd ? cd : 'You just clicked on something that is useless and/or completely irrelevant. Cool!'
				});
				
				openList[rel] = sm;
			}
		});
		
		tbody.addEvent('mouseenter:relay(tr:not(.CI))', function(e, target)
		{
			if(!target.retrieve('nodeList'))
				target.store('nodeList', target.getParent().getElements('tr[rel="'+target.get('rel')+'"]'));
			
			target.retrieve('nodeList').addClass('mouseover');
		});
		
		tbody.addEvent('mouseleave:relay(tr:not(.CI))', function(e, target)
		{
			if(!target.retrieve('nodeList'))
				target.store('nodeList', target.getParent().getElements('tr[rel="'+target.get('rel')+'"]'));
			
			target.retrieve('nodeList').removeClass('mouseover');
		});
		
		new Request({
			url: 'modal_data.xml',
			method: 'get',
			timeout: 60000,
			
			onFailure: function(xhr)
			{
				console.error('XHR Failure:', xhr);
				$('loading').set('html', '(XHR error. Please refresh!)<br />'+$('loading').get('html'));
			},
			
			onTimeout: function(){ $('loading').set('html', '(XHR timeout. Please refresh!)<br />'+$('loading').get('html')); },
			
			onSuccess: function(data, modalXMLData)
			{
				if(!modalXMLData)
					throw 'Invalid modal_data.xml file';
				
				new Request({
					url: 'chat_log.xml',
					method: 'get',
					timeout: 60000,
					
					onFailure: function(xhr)
					{
						console.error('XHR Failure:', xhr);
						$('loading').set('html', '(XHR error. Please refresh!)<br />'+$('loading').get('html'));
					},
					
					onTimeout: function(){ $('loading').set('html', '(XHR timeout. Please refresh!)<br />'+$('loading').get('html')); },
					
					onSuccess: function(data)
					{
						var xml = document.implementation.createHTMLDocument('');
						xml.body.innerHTML = data;
						xml.body.getElements('tr').each(function(item){ tbody.grab(document.importNode(item, true)); });
						init(loadHandler, modalXMLData);
					}
				}).send();
			}
		}).send();
	});
	
	var LOADER_DOTS 	 = 4,
		LOADER_DOT_DELAY = 500;
	
	var dotLoader = function()
	{
		var num = this.retrieve('dots') || 1,
			str = 'Loading';
		for(var i=num; i--;)
			str+='.';
		this.store('dots', num%LOADER_DOTS+1);
		this.set('text', str);
	}.bind($('loading'));
})(document.id);