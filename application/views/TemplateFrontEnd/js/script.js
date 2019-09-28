jQuery(document).ready(function($) {
    
    $(".select2").select2();

    $("#download_btn").on('click', function() {
        $("#step1").slideUp();
        $("#loadbar").show();
        
        var video_id = $("#video_url").val().split('v=')[1];
        if(typeof video_id == 'undefined') 
        {        
            $("#loadbar").fadeOut();
            $("#step1").fadeIn();
            return false;
        }
        
        $("#step1").slideUp();
        $("#loadbar").show();
        
        var ampersandPosition = video_id.indexOf('&');
        if(ampersandPosition != -1) 
        {
            video_id = video_id.substring(0, ampersandPosition);
        }
		
		sendJsonRequest(video_id, 0);
		
        return false;
    });
    
    $("#start_over").on('click', function() {
        $("#formats").find('option').remove();
        $("#step2").hide();
        $("#loadbar").fadeIn();
        setTimeout(function() {
            $("#loadbar").fadeOut('slow', function() {
                $("#step1").fadeIn('slow');
            });
        }, 50);
    });
	
	function sendJsonRequest(video_id, ins)
	{
		ins++;
		
		$.ajax({ 
			url: base_path+"main/retrieveJsonInfo/video_id:"+video_id+"/" 
		}).success(function(response) {
			var json = jQuery.parseJSON(response);
			var urls = [];
			var urlmp3 = '';
			for(var i=0;i<json.length;i++)
			{
				if(ins == 2) 
				{
					$('#formats').append($('<option>', {
						value: base_path+'main/download?url='+window.btoa((json[i].download_url+"&signature="+(Vm(json[i].signature_encoded))))+"&mime="+window.btoa(json[0].mime)+"&title="+window.btoa(encodeURIComponent(json[0].title)),
						text: json[i].format + ' ('+json[i].res + ') '
					}));
					
					if(json[i].res == '480x360') 
					{
						urlmp3 = base_path+'main/download?mp3=1&url='+window.btoa((json[i].download_url+"&signature="+(Vm(json[i].signature_encoded))))+"&mime="+window.btoa(json[0].mime)+"&title="+window.btoa(encodeURIComponent(json[0].title));
					}
					if(urlmp3 == '' && use_mp3 == 1) {
					
						if(json[i].res == '640x360') 
						{
							urlmp3 = base_path+'main/download?mp3=1&url='+window.btoa((json[i].download_url+"&signature="+(Vm(json[i].signature_encoded))))+"&mime="+window.btoa(json[0].mime)+"&title="+window.btoa(encodeURIComponent(json[0].title));
						}
					}
				}
				
				urls[i]  = (json[i].download_url+"&signature="+(Vm(json[i].signature_encoded)));   
			}
			if(ins==2 && use_mp3 ==1) {
				$("#loadbar").fadeIn();
				setTimeout(function() {
					$("#loadbar").fadeOut();
				},5500);
				$('#formats').append($('<option>', {
					value: urlmp3,
					text: 'MP3 Audio '
				}));
			}
			//
			$("#formats").change();
			$("#title").text(json[0].title);
			$("#duration").text(fmtMSS(json[0].duration));
			$("#author").text(json[0].author);
			$("#view_count").text(json[0].view_count);
			$("#is_listed").text(json[0].is_listed == 1 ? "Yes" : "No");
			$("#thumb").attr('src', json[0].thumbnail_url);
			setTimeout(function() {
				if(ins < 2) {
					setTimeout(function() {
						sendJsonRequest(video_id, ins);
					}, 300);
				} else {
					$("#loadbar").fadeOut('slow', function() {
						$("#step2").fadeIn('slow');
					});
				}
			}, 150);
		});
	}
    
    function fmtMSS(s)
    {
        return (s-(s%=60))/60+(9<s?':':':0')+s
    }
    
    $('#formats').on('change', function() {
        $("#dwn_anchor").attr('href', $(this).val());
    });
    
	
	var Um = {
        NB: function(a) {
            a.reverse()
        },
        I3: function(a, b) {
            var c = a[0];
            a[0] = a[b % a.length];
            a[b] = c
        },
        Qn: function(a, b) {
            a.splice(0, b)
        }
    };
	
    Vm = function(a) {
        a = a.split("");
        Um.I3(a, 58);
        Um.I3(a, 66);
        Um.Qn(a, 2);
        Um.I3(a, 70);
        Um.NB(a, 77);
        Um.I3(a, 56);
        return a.join("")
    };
    /* ---- Animations ---- */

    $('#links a').hover(
        function(){ $(this).animate({ left: 3 }, 'fast'); },
        function(){ $(this).animate({ left: 0 }, 'fast'); }
    );

    $('footer a').hover(
        function(){ $(this).animate({ top: 3 }, 'fast'); },
        function(){ $(this).animate({ top: 0 }, 'fast'); }
    );

});
 