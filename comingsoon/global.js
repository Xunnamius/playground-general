document.addEvent('domready', function()
{
	// Because IE8 (and below) is fucking garbage
	if(document)
	{
		if(document.body) $(document.body);
		else $(document.getElementsByTagName('body')[0]);
	}
	
	/* (1) Initialize */
	
	// Calculating the perceptive luminance; the human eye favors green...
	var color 	= new Color(document.body.getStyle('background-color')),
		perclum = 1 - (0.299 * color.rgb[0] + 0.587 * color.rgb[1] + 0.114 * color.rgb[2])/255;
		
		
	if(perclum < 0.5)
	{
		document.body.addClass('black');
		document.body.removeClass('white');
	}
	
	else
	{
		document.body.addClass('white');
		document.body.removeClass('black');
	}
	
	var loadHandler = (function()
	{
		var num = this.retrieve('dots') || 1,
			str = 'Loading';
		for(var i=num; i--;)
			str+='.';
		this.store('dots', num%LOADER_DOTS+1);
		this.set('text', str);
	}).periodical(LOADER_DOT_DELAY, $('loading'));
	
	/* (3) Initialize UI */
	
	var initUI = function()
	{
		var eloading = $('loading');
		clearInterval(loadHandler);
		
		// Cool flicker effect
		for(var i=0; i<FX_FLICKER_FREQUENCY; ++i)
		{
			(function(){ eloading.set('text', ''); }).delay(i*FX_FLICKER_DELAY, this);
			(function(){ eloading.set('text', 'Loaded!'); }).delay(i*FX_FLICKER_DELAY+FX_FLICKER_PHASE, this);
		}
		
		(function()
		{
			$('inner_wrapper').setStyles({
				'display': 'none',
				'border-color': COLOR_SELECTION[1]
			});
			
			// Background fade-in
			(new Fx.Tween(document.body, { property: 'background-color', duration: 2000 }))
			.start([COLOR_PALLET[0][0], COLOR_SELECTION[0]])
			.chain(function()
			{
				// Step-by-step fade-in
				var propObj = { property: 'opacity', duration: 1000 },
					targets = [$('title'), $$('#title+h2')[0], $('footer')];
				
				if(LAYOUT_MODE) targets.push($('inner_wrapper'));
				
				targets.each(function(item)
				{
					item.setStyles({
						'opacity': 0, // Moo should cover this for IE as well...
						'display': 'block'
					});
				});
				
				(new Fx.Tween(targets[0], propObj)).start([0,1]).chain(function()
				{
					(new Fx.Tween(targets[1], propObj)).start([0,1]).chain(function()
					{
						var complete = function(){ (new Fx.Tween(targets[2], propObj)).start([0,1]).chain(function(){ finalInit }); };
						
						if(LAYOUT_MODE)
							(new Fx.Tween(targets[3], propObj)).start([0,1]).chain(function(){ complete(); });
						else complete();
					});
				});
			});
		}).delay(i*FX_FLICKER_DELAY+FX_FLICKER_PHASE);
		
		var finalInit = function()
		{
			console.log('yeah');
		};
	};
	
	/* (2) Load assets & build UI elements */
	
	initUI.delay(LOAD_ARTIF_DELAY);
});