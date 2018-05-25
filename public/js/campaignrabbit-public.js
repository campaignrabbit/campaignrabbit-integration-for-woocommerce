jQuery(document).ready(function($) {

    var data = {
        action: 'analytics'

    };

    
    $.post(CRAjax.ajaxurl, data, function(response) {

        window.app_url = "https://app.campaignrabbit.com/";window.app_id = response ;window.ancs_url = "https://hook.campaignrabbit.com/v1/pixel.gif" ;
        !function(e,t,n,p,o,a,i,s,c){e[o]||(i=e[o]=function()
        {i.process?i.process.apply(i,arguments):i.queue.push(arguments)},i.queue=[],i.t=1*new Date,s=t.createElement(n),
            s.async=1,s.src=p+"?t="+Math.ceil(new Date/a)*a,c=t.getElementsByTagName(n)[0] , c.parentNode.insertBefore(s,c))
        }(window,document, "script", "https://cartrabbit.github.io/cdn/campaignrabbit.analytics.js", "rabbit", 1 ),rabbit( "event", "pageload");
    });





});
