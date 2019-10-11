var token = '[TOKEN_GOES_HERE]';
var link = 'https://ds360.co/track/blog.php?t='+token
    +'&title='+encodeURIComponent(document.title).substring(0, 200)
    +'&width='+encodeURIComponent(screen.width).substring(0,6)
    +'&height='+encodeURIComponent(screen.height).substring(0,6)
    +'&ref='+encodeURIComponent(document.referrer).substring(0,1100)
    +'&loc='+encodeURIComponent(document.location.href).substring(0,1000)
    +'&app='+encodeURIComponent(navigator.appName).substring(0,100)
    +'&agent='+encodeURIComponent(navigator.userAgent).substring(0,1000)
    +'&col='+encodeURIComponent(screen.colorDepth).substring(0,100)
    +'&dom='+encodeURIComponent(document.domain).substring(0,200)
    +'&lang='+encodeURIComponent(navigator.language).substring(0,200)
    +'&os='+encodeURIComponent(navigator.platform).substring(0,200)
    +'&ck='+encodeURIComponent(navigator.cookieEnabled).substring(0,200);
var preload = new Image();
preload.src = link;

window.onbeforeunload = function(e) {
    if(e.handled !== true)
    {
        var link = 'https://ds360.co/track/blog.php?t='+token+'&r=1';
        var preload = new Image();
        preload.src = link;
    }
}
var popup = false;
if(!popup)
    checkhtml(token);

function checkhtml(t_code)
{
    if(!popup)
    {
        get("https://ds360.co/track/check.php?code="+t_code+"&loc="+encodeURIComponent(document.location.href).substring(0,1000)).then(function(response) {
            if(response != "" && typeof response !== null)
            {
                var json = JSON.parse(response);
                if(json['page'] == "yes")
                {
                    var mobilecheck = checkMobile();
                    if(typeof document.getElementById('digi-iframe') !== 'undefined')
                    {
                        if(json['display'] == "yes")
                        {
                            if((json['mobile'] == 0 && !mobilecheck) || json['mobile'] == 1)
                            {
                                buildPopup(json);
                            }
                        }
                    }
                }
            }
        });
    }
    popup = true;
}

function get(url) {
    return new Promise(function(resolve, reject) {
        var xhttp;
        if (window.XMLHttpRequest) {
            xhttp=new XMLHttpRequest();
        } else {
            xhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xhttp.open('GET', url);
        xhttp.onload = function() {
            if (xhttp.status == 200) {
                resolve(xhttp.response);
            }
            else {
                reject(Error(xhttp.statusText));
            }
        };
        xhttp.onerror = function() {
            reject(Error("Network Error"));
        };
        xhttp.send();
    });
}

var freezeVp = function(e) {
    e.preventDefault();
};

function buildPopup(json)
{
    var visits = readCookie('__zbpv');
    var show = true;
    if(visits == null)
    {
        var d = + new Date();
        setcookie('__zbpv', d, 365);
    }else{
        if(parseInt(json['again']) == 2)
        {
            show = false;
        }else if(parseInt(json['again']) == 3)
        {
            var tp = + new Date();
            var lastShown = parseInt(visits);
            if(tp < (lastShown + parseInt(json['showable'])))
            {
                show = false;
            }
        }
        if(parseInt(json['audience']) == 2)
        {
            show = false;
        }else if(parseInt(json['audience']) == 3)
        {
            var tp = + new Date();
            var lastShown = parseInt(visits);
            if(tp < (lastShown + parseInt(json['aud_showable'])))
            {
                show = false;
            }
        }
    }


    var pages = readCookie('__zbpp');
    if(pages == null)
    {
        setcookie('__zbpp', 1, '');
        pages = 1;
    } else {
        pages = parseInt(pages)+1;
        setcookie('__zbpp', pages, '');
    }
    if((parseInt(json['when'])==4 && pages == parseInt(json['pages'])) || parseInt(json['when']) == 1)
    {
        if((parseInt(json['again']) == 1 && parseInt(json['audience']) == 1) || show == true)
        {
            var after = parseInt(json['after']) * 1000;
            setTimeout(function(){
                var css = document.createElement('link');
                css.href = "https://zymplify.com/client-area/html_builder/css/animate.css";
                css.rel = "stylesheet";
                css.type = "text/css";
                document.head.appendChild(css);

                var iframe = document.createElement('iframe');
                iframe.src = json['url'];
                iframe.id = "digi-iframe";
                iframe.height = "100%";
                iframe.width = "100%";
                iframe.style.border = "none";
                if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    iframe.scrolling = "no";
                }
                iframe.className = "modal-window";
                iframe.style.display = "none";
                iframe.style.top = "0";
                iframe.style.left = "0";
                if(!/iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
                    iframe.style.position = "fixed";
                }else {
                    iframe.style.position = "absolute";
                }
                iframe.style.zIndex = "155001";

                document.body.appendChild(iframe);

                var d = + new Date();
                setcookie('__zbpv', d, '');

                var animationIn = json['in'];
                jQuery(".animate").removeClass('animate');
                jQuery('.modal-window, #digi-iframe-close').addClass(animationIn + ' animated').show().one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
                    jQuery(this).removeClass(animationIn);
                });

                stopBodyScrolling(true);

            }, after);
        }
    }
    var isIE = /*@cc_on!@*/false || !!document.documentMode;
    if(isIE)
    {
        // Internet Explorer
        window.attachEvent('onmessage',closeDigiPopup);
    }else{
        // Opera/Mozilla/Webkit
        window.addEventListener("message", closeDigiPopup, false);
    }
    function closeDigiPopup(event)
    {
        var animationOut = event.data;
        jQuery('.modal-window, #digi-iframe-overlay').addClass(animationOut + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
            stopBodyScrolling(false);
            jQuery(this).hide().removeClass(animationOut);
        });
    }

    jQuery('#digi-iframe-close').click(function(e){
        var animationOut = json['out'];
        if(animationOut == "" || animationOut == "defaultHide")
            animationOut = "fadeOut";

        jQuery('.modal-window, #digi-iframe-overlay, #digi-iframe-close').addClass(animationOut + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
            stopBodyScrolling(false);
            jQuery(this).hide().removeClass(animationOut);
        });
    });

}

function setcookie(name, value, days) {
    if (days)
    {
        var date = new Date();
        date.setTime(date.getTime()+days*24*60*60*1000); // ) removed
        var expires = "; expires=" + date.toGMTString(); // + added
    }
    else
        var expires = "";
    document.cookie = name+"=" + value+expires + ";path=/"; // + and " added
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function checkMobile(){
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
}

function stopBodyScrolling (bool) {
    if (bool === true) {
        document.body.addEventListener("touchmove", freezeVp, false);
    } else {
        document.body.removeEventListener("touchmove", freezeVp, false);
    }
}