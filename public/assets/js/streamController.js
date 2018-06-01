// __________________
// getHTMLMediaElement.js

function getHTMLMediaElement(mediaElement, config) {
    config = config || {};

    if (!mediaElement.nodeName || (mediaElement.nodeName.toLowerCase() != 'audio' && mediaElement.nodeName.toLowerCase() != 'video')) {
        if (!mediaElement.getVideoTracks().length) {
            return getAudioElement(mediaElement, config);
        }

        var mediaStream = mediaElement;
        mediaElement = document.createElement(mediaStream.getVideoTracks().length ? 'video' : 'audio');

        try {
            mediaElement.setAttributeNode(document.createAttribute('autoplay'));
            mediaElement.setAttributeNode(document.createAttribute('playsinline'));
        } catch (e) {
            mediaElement.setAttribute('autoplay', true);
            mediaElement.setAttribute('playsinline', true);
        }

        if ('srcObject' in mediaElement) {
            mediaElement.srcObject = mediaStream;
        } else {
            mediaElement[!!navigator.mozGetUserMedia ? 'mozSrcObject' : 'src'] = !!navigator.mozGetUserMedia ? mediaStream : (window.URL || window.webkitURL).createObjectURL(mediaStream);
        }
    }

    if (mediaElement.nodeName && mediaElement.nodeName.toLowerCase() == 'audio') {
        return getAudioElement(mediaElement, config);
    }

   // var buttons = config.buttons || ['mute-audio', 'mute-video', 'full-screen', 'volume-slider', 'stop'];

   var buttons = config.buttons || ['mute-audio', 'mute-video','volume-slider', 'stop'];

    buttons.has = function(element) {
        return buttons.indexOf(element) !== -1;
    };

    config.toggle = config.toggle || [];
    config.toggle.has = function(element) {
        return config.toggle.indexOf(element) !== -1;
    };

    var mediaElementContainer = document.createElement('div');
    mediaElementContainer.className = 'media-container';

    var mediaControls = document.createElement('div');
    mediaControls.className = 'media-controls';
    mediaElementContainer.appendChild(mediaControls);

    if (buttons.has('mute-audio')) {
        var muteAudio = document.createElement('div');
        muteAudio.className = 'control ' + (config.toggle.has('mute-audio') ? 'unmute-audio selected' : 'mute-audio');
        mediaControls.appendChild(muteAudio);

        muteAudio.onclick = function() {
            if (muteAudio.className.indexOf('unmute-audio') != -1) {
                muteAudio.className = muteAudio.className.replace('unmute-audio selected', 'mute-audio');
                mediaElement.muted = false;
                mediaElement.volume = 1;
                if (config.onUnMuted) config.onUnMuted('audio');
            } else {
                muteAudio.className = muteAudio.className.replace('mute-audio', 'unmute-audio selected');
                mediaElement.muted = true;
                mediaElement.volume = 0;
                if (config.onMuted) config.onMuted('audio');
            }
        };
    }

    if (buttons.has('mute-video')) {
        var muteVideo = document.createElement('div');
        muteVideo.className = 'control ' + (config.toggle.has('mute-video') ? 'unmute-video selected' : 'mute-video');
        mediaControls.appendChild(muteVideo);

        muteVideo.onclick = function() {
            if (muteVideo.className.indexOf('unmute-video') != -1) {
                muteVideo.className = muteVideo.className.replace('unmute-video selected', 'mute-video');
                mediaElement.muted = false;
                mediaElement.volume = 1;
                mediaElement.play();
                if (config.onUnMuted) config.onUnMuted('video');
            } else {
                muteVideo.className = muteVideo.className.replace('mute-video', 'unmute-video selected');
                mediaElement.muted = true;
                mediaElement.volume = 0;
                mediaElement.pause();
                if (config.onMuted) config.onMuted('video');
            }
        };
    }

    if (buttons.has('take-snapshot')) {
        var takeSnapshot = document.createElement('div');
        takeSnapshot.className = 'control take-snapshot';
        mediaControls.appendChild(takeSnapshot);

        takeSnapshot.onclick = function() {
            if (config.onTakeSnapshot) config.onTakeSnapshot();
        };
    }

    if (buttons.has('stop')) {
        var stop = document.createElement('div');
        stop.className = 'control stop';
        mediaControls.appendChild(stop);

        stop.onclick = function() {
            mediaElementContainer.style.opacity = 0;
            setTimeout(function() {
                if (mediaElementContainer.parentNode) {
                    mediaElementContainer.parentNode.removeChild(mediaElementContainer);
                }
            }, 800);
            if (config.onStopped) config.onStopped();
        };
    }

    var volumeControl = document.createElement('div');
    volumeControl.className = 'volume-control';

    if (buttons.has('record-audio')) {
        var recordAudio = document.createElement('div');
        recordAudio.className = 'control ' + (config.toggle.has('record-audio') ? 'stop-recording-audio selected' : 'record-audio');
        volumeControl.appendChild(recordAudio);

        recordAudio.onclick = function() {
            if (recordAudio.className.indexOf('stop-recording-audio') != -1) {
                recordAudio.className = recordAudio.className.replace('stop-recording-audio selected', 'record-audio');
                if (config.onRecordingStopped) config.onRecordingStopped('audio');
            } else {
                recordAudio.className = recordAudio.className.replace('record-audio', 'stop-recording-audio selected');
                if (config.onRecordingStarted) config.onRecordingStarted('audio');
            }
        };
    }

    if (buttons.has('record-video')) {
        var recordVideo = document.createElement('div');
        recordVideo.className = 'control ' + (config.toggle.has('record-video') ? 'stop-recording-video selected' : 'record-video');
        volumeControl.appendChild(recordVideo);

        recordVideo.onclick = function() {
            if (recordVideo.className.indexOf('stop-recording-video') != -1) {
                recordVideo.className = recordVideo.className.replace('stop-recording-video selected', 'record-video');
                if (config.onRecordingStopped) config.onRecordingStopped('video');
            } else {
                recordVideo.className = recordVideo.className.replace('record-video', 'stop-recording-video selected');
                if (config.onRecordingStarted) config.onRecordingStarted('video');
            }
        };
    }

    if (buttons.has('volume-slider')) {
        var volumeSlider = document.createElement('div');
        volumeSlider.className = 'control volume-slider';
        volumeControl.appendChild(volumeSlider);

        var slider = document.createElement('input');
        slider.type = 'range';
        slider.min = 0;
        slider.max = 100;
        slider.value = 100;
        slider.onchange = function() {
            mediaElement.volume = '.' + slider.value.toString().substr(0, 1);
        };
        volumeSlider.appendChild(slider);
    }

   /* if (buttons.has('full-screen')) {
        var zoom = document.createElement('div');
        zoom.className = 'control ' + (config.toggle.has('zoom-in') ? 'zoom-out selected' : 'zoom-in');

        if (!slider && !recordAudio && !recordVideo && zoom) {
            mediaControls.insertBefore(zoom, mediaControls.firstChild);
        } else volumeControl.appendChild(zoom);

        zoom.onclick = function() {
            if (zoom.className.indexOf('zoom-out') != -1) {
                zoom.className = zoom.className.replace('zoom-out selected', 'zoom-in');
                exitFullScreen();
            } else {
                zoom.className = zoom.className.replace('zoom-in', 'zoom-out selected');
                launchFullscreen(mediaElementContainer);
            }
        };

        function launchFullscreen(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        }

        function exitFullScreen() {
            if (document.fullscreen) {
                document.cancelFullScreen();
            }

            if (document.mozFullScreen) {
                document.mozCancelFullScreen();
            }

            if (document.webkitIsFullScreen) {
                document.webkitCancelFullScreen();
            }
        }

        function screenStateChange(e) {
            if (e.srcElement != mediaElementContainer) return;

            var isFullScreeMode = document.webkitIsFullScreen || document.mozFullScreen || document.fullscreen;

            mediaElementContainer.style.width = (isFullScreeMode ? (window.innerWidth - 20) : config.width) + 'px';
            mediaElementContainer.style.display = isFullScreeMode ? 'block' : 'inline-block';

            if (config.height) {
                mediaBox.style.height = (isFullScreeMode ? (window.innerHeight - 20) : config.height) + 'px';
            }

            if (!isFullScreeMode && config.onZoomout) config.onZoomout();
            if (isFullScreeMode && config.onZoomin) config.onZoomin();

            if (!isFullScreeMode && zoom.className.indexOf('zoom-out') != -1) {
                zoom.className = zoom.className.replace('zoom-out selected', 'zoom-in');
                if (config.onZoomout) config.onZoomout();
            }
            setTimeout(adjustControls, 1000);
        }

        document.addEventListener('fullscreenchange', screenStateChange, false);
        document.addEventListener('mozfullscreenchange', screenStateChange, false);
        document.addEventListener('webkitfullscreenchange', screenStateChange, false);
    }*/

    if (buttons.has('volume-slider') || buttons.has('full-screen') || buttons.has('record-audio') || buttons.has('record-video')) {
        mediaElementContainer.appendChild(volumeControl);
    }

    var mediaBox = document.createElement('div');
    mediaBox.className = 'media-box';
    mediaElementContainer.appendChild(mediaBox);

   /* if (config.title) {
        var h2 = document.createElement('h2');
        h2.innerHTML = config.title;
        h2.setAttribute('style', 'position: absolute;color:white;font-size:17px;text-shadow: 1px 1px black;padding:0;margin:0;text-align: left; margin-top: 10px; margin-left: 10px; display: block; border: 0;line-height:1.5;z-index:1;');
        mediaBox.appendChild(h2);
    }*/

    mediaBox.appendChild(mediaElement);

    if (!config.width) config.width = (innerWidth / 2) - 50;

    mediaElementContainer.style.width = config.width + 'px';

    /*if (config.height) {
        mediaBox.style.height = config.height + 'px';
    }

    mediaBox.querySelector('video').style.maxHeight = innerHeight + 'px';
*/
    var times = 0;

    function adjustControls() {
        mediaControls.style.marginLeft = (mediaElementContainer.clientWidth - mediaControls.clientWidth - 2) + 'px';

        if (slider) {
            slider.style.width = (mediaElementContainer.clientWidth / 3) + 'px';
            volumeControl.style.marginLeft = (mediaElementContainer.clientWidth / 3 - 30) + 'px';

            if (zoom) zoom.style['border-top-right-radius'] = '5px';
        } else {
            volumeControl.style.marginLeft = (mediaElementContainer.clientWidth - volumeControl.clientWidth - 2) + 'px';
        }

        volumeControl.style.marginTop = (mediaElementContainer.clientHeight - volumeControl.clientHeight - 2) + 'px';

        if (times < 10) {
            times++;
            setTimeout(adjustControls, 1000);
        } else times = 0;
    }

    if (config.showOnMouseEnter || typeof config.showOnMouseEnter === 'undefined') {
        mediaElementContainer.onmouseenter = mediaElementContainer.onmousedown = function() {
            adjustControls();
            mediaControls.style.opacity = 1;
            volumeControl.style.opacity = 1;
        };

        mediaElementContainer.onmouseleave = function() {
            mediaControls.style.opacity = 0;
            volumeControl.style.opacity = 0;
        };
    } else {
        setTimeout(function() {
            adjustControls();
            setTimeout(function() {
                mediaControls.style.opacity = 1;
                volumeControl.style.opacity = 1;
            }, 300);
        }, 700);
    }

    adjustControls();

    mediaElementContainer.toggle = function(clasName) {
        if (typeof clasName != 'string') {
            for (var i = 0; i < clasName.length; i++) {
                mediaElementContainer.toggle(clasName[i]);
            }
            return;
        }

        if (clasName == 'mute-audio' && muteAudio) muteAudio.onclick();
        if (clasName == 'mute-video' && muteVideo) muteVideo.onclick();

        if (clasName == 'record-audio' && recordAudio) recordAudio.onclick();
        if (clasName == 'record-video' && recordVideo) recordVideo.onclick();

        if (clasName == 'stop' && stop) stop.onclick();

        return this;
    };

    mediaElementContainer.media = mediaElement;

    return mediaElementContainer;
}

// __________________
// getAudioElement.js

function getAudioElement(mediaElement, config) {
    config = config || {};

    if (!mediaElement.nodeName || (mediaElement.nodeName.toLowerCase() != 'audio' && mediaElement.nodeName.toLowerCase() != 'video')) {
        var mediaStream = mediaElement;
        mediaElement = document.createElement('audio');

        try {
            mediaElement.setAttributeNode(document.createAttribute('autoplay'));
            mediaElement.setAttributeNode(document.createAttribute('controls'));
        } catch (e) {
            mediaElement.setAttribute('autoplay', true);
            mediaElement.setAttribute('controls', true);
        }

        if ('srcObject' in mediaElement) {
            mediaElement.mediaElement = mediaStream;
        } else {
            mediaElement[!!navigator.mozGetUserMedia ? 'mozSrcObject' : 'src'] = !!navigator.mozGetUserMedia ? mediaStream : (window.URL || window.webkitURL).createObjectURL(mediaStream);
        }
    }

    config.toggle = config.toggle || [];
    config.toggle.has = function(element) {
        return config.toggle.indexOf(element) !== -1;
    };

    var mediaElementContainer = document.createElement('div');
    mediaElementContainer.className = 'media-container';

    var mediaControls = document.createElement('div');
    mediaControls.className = 'media-controls';
    mediaElementContainer.appendChild(mediaControls);

    var muteAudio = document.createElement('div');
    muteAudio.className = 'control ' + (config.toggle.has('mute-audio') ? 'unmute-audio selected' : 'mute-audio');
    mediaControls.appendChild(muteAudio);

    muteAudio.style['border-top-left-radius'] = '5px';

    muteAudio.onclick = function() {
        if (muteAudio.className.indexOf('unmute-audio') != -1) {
            muteAudio.className = muteAudio.className.replace('unmute-audio selected', 'mute-audio');
            mediaElement.muted = false;
            if (config.onUnMuted) config.onUnMuted('audio');
        } else {
            muteAudio.className = muteAudio.className.replace('mute-audio', 'unmute-audio selected');
            mediaElement.muted = true;
            if (config.onMuted) config.onMuted('audio');
        }
    };

    if (!config.buttons || (config.buttons && config.buttons.indexOf('record-audio') != -1)) {
        var recordAudio = document.createElement('div');
        recordAudio.className = 'control ' + (config.toggle.has('record-audio') ? 'stop-recording-audio selected' : 'record-audio');
        mediaControls.appendChild(recordAudio);

        recordAudio.onclick = function() {
            if (recordAudio.className.indexOf('stop-recording-audio') != -1) {
                recordAudio.className = recordAudio.className.replace('stop-recording-audio selected', 'record-audio');
                if (config.onRecordingStopped) config.onRecordingStopped('audio');
            } else {
                recordAudio.className = recordAudio.className.replace('record-audio', 'stop-recording-audio selected');
                if (config.onRecordingStarted) config.onRecordingStarted('audio');
            }
        };
    }

    var volumeSlider = document.createElement('div');
    volumeSlider.className = 'control volume-slider';
    volumeSlider.style.width = 'auto';
    mediaControls.appendChild(volumeSlider);

    var slider = document.createElement('input');
    slider.style.marginTop = '11px';
    slider.style.width = ' 200px';

    if (config.buttons && config.buttons.indexOf('record-audio') == -1) {
        slider.style.width = ' 241px';
    }

    slider.type = 'range';
    slider.min = 0;
    slider.max = 100;
    slider.value = 100;
    slider.onchange = function() {
        mediaElement.volume = '.' + slider.value.toString().substr(0, 1);
    };
    volumeSlider.appendChild(slider);

    var stop = document.createElement('div');
    stop.className = 'control stop';
    mediaControls.appendChild(stop);

    stop.onclick = function() {
        mediaElementContainer.style.opacity = 0;
        setTimeout(function() {
            if (mediaElementContainer.parentNode) {
                mediaElementContainer.parentNode.removeChild(mediaElementContainer);
            }
        }, 800);
        if (config.onStopped) config.onStopped();
    };

    stop.style['border-top-right-radius'] = '5px';
    stop.style['border-bottom-right-radius'] = '5px';

    var mediaBox = document.createElement('div');
    mediaBox.className = 'media-box';
    mediaElementContainer.appendChild(mediaBox);

    var h2 = document.createElement('h2');
    h2.innerHTML = config.title || 'Audio Element';
    h2.setAttribute('style', 'position: absolute;color: rgb(160, 160, 160);font-size: 20px;text-shadow: 1px 1px rgb(255, 255, 255);padding:0;margin:0;');
    mediaBox.appendChild(h2);

    mediaBox.appendChild(mediaElement);

    mediaElementContainer.style.width = '329px';
    mediaBox.style.height = '90px';

    h2.style.width = mediaElementContainer.style.width;
    h2.style.height = '50px';
    h2.style.overflow = 'hidden';

    var times = 0;

    function adjustControls() {
        mediaControls.style.marginLeft = (mediaElementContainer.clientWidth - mediaControls.clientWidth - 7) + 'px';
        mediaControls.style.marginTop = (mediaElementContainer.clientHeight - mediaControls.clientHeight - 6) + 'px';
        if (times < 10) {
            times++;
            setTimeout(adjustControls, 1000);
        } else times = 0;
    }

    if (config.showOnMouseEnter || typeof config.showOnMouseEnter === 'undefined') {
        mediaElementContainer.onmouseenter = mediaElementContainer.onmousedown = function() {
            adjustControls();
            mediaControls.style.opacity = 1;
        };

        mediaElementContainer.onmouseleave = function() {
            mediaControls.style.opacity = 0;
        };
    } else {
        setTimeout(function() {
            adjustControls();
            setTimeout(function() {
                mediaControls.style.opacity = 1;
            }, 300);
        }, 700);
    }

    adjustControls();

    mediaElementContainer.toggle = function(clasName) {
        if (typeof clasName != 'string') {
            for (var i = 0; i < clasName.length; i++) {
                mediaElementContainer.toggle(clasName[i]);
            }
            return;
        }

        if (clasName == 'mute-audio' && muteAudio) muteAudio.onclick();
        if (clasName == 'record-audio' && recordAudio) recordAudio.onclick();
        if (clasName == 'stop' && stop) stop.onclick();

        return this;
    };

    mediaElementContainer.media = mediaElement;

    return mediaElementContainer;
}



liveAppCtrl
.controller('streamCtrl', ['$rootScope', '$window','$sce',
	function ($rootScope, $window,$sce) {

		$scope = $rootScope;

		function getBrowser() {

            // Opera 8.0+
            var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

            // Firefox 1.0+
            var isFirefox = typeof InstallTrigger !== 'undefined';

            // Safari 3.0+ "[object HTMLElementConstructor]" 
            var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);

            // Internet Explorer 6-11
            var isIE = /*@cc_on!@*/false || !!document.documentMode;

            // Edge 20+
            var isEdge = !isIE && !!window.StyleMedia;

            // Chrome 1+
            var isChrome = (!!window.chrome && !!window.chrome.webstore) || navigator.userAgent.indexOf("Chrome") !== -1;

            // Blink engine detection
            var isBlink = (isChrome || isOpera) && !!window.CSS;

            var b_n = '';

            switch(true) {

                case isFirefox :

                        b_n = "Firefox";

                        break;
                case isChrome :

                        b_n = "Chrome";

                        break;

                case isSafari :

                        b_n = "Safari";

                        break;
                case isOpera :

                        b_n = "Opera";

                        break;

                case isIE :

                        b_n = "IE";

                        break;

                case isEdge : 

                        b_n = "Edge";

                        break;

                case isBlink : 

                        b_n = "Blink";

                        break;

                default :

                        b_n = "Unknown";

                        break;

            }

            return b_n;

        }

        var mobile_type = "";

        function getMobileOperatingSystem() {

		  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

		  if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) )
		  {
		    mobile_type =  'ios';

		  }
		  else if( userAgent.match( /Android/i ) )
		  {

		    mobile_type =  'andriod';
		  }
		  else
		  {
		    mobile_type =  'unknown'; 
		  }

		  return mobile_type;
		
		}

        var browser = getBrowser();

        var m_type = getMobileOperatingSystem();

        $scope.redirectToLive = function(id, amount) {

			$state.go('restricted.join-video', {id : id}, {reload:true});
			
		}


		// This function will call, when the streaming started by user
		$scope.live_status = function() {

			var data = new FormData;
			data.append('id', live_user_id);
			data.append('token', user_token);
			data.append('video_id', $scope.videoDetails.id);
			
			$.ajax({
	      		type : 'post',
	      		url : apiUrl+"userApi/streaming/status",
	      		contentType : false,
				processData: false,
				async : false,
				data : data,
				success : function(response) {

					if (response.success) {

					} else {

						UIkit.notify({message : response.error_messages,timeout:3000, status:'danger', position:'top-center'});

					}

				},
				error : function(response) {


				},
		  	});
		}

		window.enableAdapter = false; // enable adapter.js

		// $("#room-id").val('1auji7mmo5k4916tnu4t');

		// ......................................................
		// .......................UI Code........................
		// ......................................................
		$scope.openRoom = function() {
		    // disableInputButtons();

		    var room_id_value = document.getElementById('room-id').value;

		    console.log(room_id_value);

		    connection.open(document.getElementById('room-id').value, function() {
		       // showRoomURL(connection.sessionid);

		       $("#default_image").hide();

		       $("#loader_btn").hide();

		       $("#open-room").hide();

		       $("#stop-room").show();

		       
		    });
		}


		document.getElementById('join-room').onclick = function() {
		    //disableInputButtons();

		    $("#default_image").hide();

		    connection.sdpConstraints.mandatory = {
		        OfferToReceiveAudio: true,
		        OfferToReceiveVideo: true
		    };
		    connection.join(document.getElementById('room-id').value);
		};

		document.getElementById('open-or-join-room').onclick = function() {
		    // disableInputButtons();
		    connection.openOrJoin(document.getElementById('room-id').value, function(isRoomExist, roomid) {
		        if (!isRoomExist) {
		            showRoomURL(roomid);
		        }
		        else {
		            connection.sdpConstraints.mandatory = {
		                OfferToReceiveAudio: true,
		                OfferToReceiveVideo: true
		            };
		        }
		    });
		};

	    $scope.socket_url = socket_url;

	   // alert($scope.socket_url);

	    $scope.connectionNow= null;

		var connection = new RTCMultiConnection();

		// by default, socket.io server is assumed to be deployed on your own URL
		// connection.socketURL = '/';
		connection.socketURL = $scope.socket_url;

		// comment-out below line if you do not have your own socket.io server
		// connection.socketURL = 'https://rtcmulticonnection.herokuapp.com:443/';

		connection.socketMessageEvent = 'video-broadcast-demo';

		$scope.connectionNow = connection;

		connection.session = {
		    audio: true,
		    video: true,
		    oneway: true
		};

		connection.sdpConstraints.mandatory = {
		    OfferToReceiveAudio: false,
		    OfferToReceiveVideo: false
		};

		connection.videosContainer = document.getElementById('videos-container');
		connection.onstream = function(event) {
		    event.mediaElement.removeAttribute('src');
		    event.mediaElement.removeAttribute('srcObject');

		    var video = document.createElement('video');
		    video.controls = false;
		    if(event.type === 'local') {
		        video.muted = true;
		    }
		    video.srcObject = event.stream;

		    var width = parseInt(connection.videosContainer.clientWidth / 2) - 20;
		    var mediaElement = getHTMLMediaElement(video, {
		        title: event.userid,
		        buttons: ['full-screen'],
		        width: width,
		        showOnMouseEnter: false
		    });

		    connection.videosContainer.appendChild(mediaElement);

		    setTimeout(function() {
		        mediaElement.media.play();
		    }, 5000);

		    mediaElement.id = event.streamid;

		    $scope.live_status();
		};

		connection.onstreamended = function(event) {
		    var mediaElement = document.getElementById(event.streamid);
		    if (mediaElement) {
		        mediaElement.parentNode.removeChild(mediaElement);
		    }
		};

		function disableInputButtons() {
		    document.getElementById('open-or-join-room').disabled = true;
		    document.getElementById('open-room').disabled = true;
		    document.getElementById('join-room').disabled = true;
		    document.getElementById('room-id').disabled = true;
		}

		// ......................................................
		// ......................Handling Room-ID................
		// ......................................................

		/*function showRoomURL(roomid) {
		    var roomHashURL = '#' + roomid;
		    var roomQueryStringURL = '?roomid=' + roomid;

		    var html = '<h2>Unique URL for your room:</h2><br>';

		    html += 'Hash URL: <a href="' + roomHashURL + '" target="_blank">' + roomHashURL + '</a>';
		    html += '<br>';
		    html += 'QueryString URL: <a href="' + roomQueryStringURL + '" target="_blank">' + roomQueryStringURL + '</a>';

		    var roomURLsDiv = document.getElementById('room-urls');
		    roomURLsDiv.innerHTML = html;

		    roomURLsDiv.style.display = 'block';
		}*/

		(function() {
		    var params = {},
		        r = /([^&=]+)=?([^&]*)/g;

		    function d(s) {
		        return decodeURIComponent(s.replace(/\+/g, ' '));
		    }
		    var match, search = window.location.search;
		    while (match = r.exec(search.substring(1)))
		        params[d(match[1])] = d(match[2]);
		    window.params = params;
		})();

		var roomid = '';

		roomid = $scope.videoDetails.virtual_id;

		if (roomid == '') {

			if (localStorage.getItem(connection.socketMessageEvent)) {

			    roomid = localStorage.getItem(connection.socketMessageEvent);

			} else {
			    roomid = connection.token();
			}

		}


		document.getElementById('room-id').value = roomid;

		document.getElementById('room-id').onkeyup = function() {
		    localStorage.setItem(connection.socketMessageEvent, this.value);
		};

		var hashString = location.hash.replace('#', '');
		if (hashString.length && hashString.indexOf('comment-') == 0) {
		    hashString = '';
		}

		/*var roomid = params.roomid;
		if (!roomid && hashString.length) {
		    roomid = hashString;
		}*/

		if (roomid && roomid.length) {
		    document.getElementById('room-id').value = roomid;
		    localStorage.setItem(connection.socketMessageEvent, roomid);

		    // auto-join-room
		    (function reCheckRoomPresence() {
		        connection.checkPresence(roomid, function(isRoomExist) {
		            if (isRoomExist) {
		                connection.sdpConstraints.mandatory = {
		                    OfferToReceiveAudio: true,
		                    OfferToReceiveVideo: true
		                };

		                $("#default_image").hide();

		                connection.join(roomid);
		                return;
		            }

		            setTimeout(reCheckRoomPresence, 5000);
		        });
		    })();

		   // disableInputButtons();
		}

		if (video_details.user_id == live_user_id) {

			$scope.openRoom();

		} 

		$scope.stopStreaming = function(video_id) {

			if (confirm('Do you want to stop streaming ?')) {

				var data = new FormData;
				data.append('id', live_user_id);
				data.append('token',user_token);
				data.append('video_id', video_details.id);
				
				$.ajax({

		      		type : 'post',
		      		url : apiUrl+"userApi/close_streaming",
		      		contentType : false,
					processData: false,
					async : false,
					data : data,
		      		success : function(data) {

				    	connection.attachStreams.forEach(function (stream) {
					        stream.stop();
					    });

					    connection.videosContainer.innerHTML = '';

					    connection.autoCloseEntireSession = true;
					     
					    $scope.connectionNow.close();

		      			UIkit.notify({message : 'Your streaming has been ended successfully.', status : 'success', timeout:5000, pos : 'top-center'});

		      			$state.go('static.home', {}, {reload : true});
		      		}

		      	});

			}

		}

    }

])

.controller('chatBarCtrl', ['$scope', '$http', '$rootScope', '$window',
	function ($scope, $http, $rootScope, $window) {

		if (live_user_id != '' && live_user_id != undefined) {

			console.log('chat');

			console.log(chat_socket_url);


			var appSettings = $scope.appSettings;

	        var defaultImage = "";

	        var chatBox = document.getElementById('chat-box'); // Chat Box container

	        var chatInput = document.getElementById('chat-input'); // User Typed text in input
	 
	        var chatSend = document.getElementById('chat-send'); // Send Box 


	        var liveVideoID = $scope.videoDetails.id;

	        var liveVideoViewerID = (appSettings.USER == null) ? live_user_id : 0;

	        var userID = (appSettings.USER != null) ?  appSettings.USER.id : 0;

	        var userToViewer ="uv";

	        var viewerToUser ="vu";

	        // let's assume that the client page, once rendered, knows what room it wants to join

	        var room = $scope.videoDetails.unique_id; // Room will be video ID

	        // set-up a connection between the client and the server

	        var socket = io(chat_socket_url ,  { secure: true , query: "room="+room});

	        var socketState = false;

	        // The socket state will be enable, once the socket is connected

	        socket.on('connected', function (data) {

	            socketState = true;

	            // Enable chat input box

	            chatInput.enable();

	        });

	        socket.on('message', function(data) {


	           if(data.message){

	                $('#chat-box').append(messageTemplate(data));

	                // $("#chat-box").animate({ scrollTop: $('#chat-box').prop("scrollHeight")}, 300);
	                // $('#chat-box').scrollTop($('#chat-box').height());
	                // $('#chat_box_scroll').scrollTop($('#chat_box_scroll')[0].scrollHeight);
	                $('.chat_box_scroll').scrollTop($('.chat_box_scroll')[0].scrollHeight);
	            }

	        });

	        socket.on('disconnect', function (data) {
	            socketState = false;
	            chatInput.disable();
	           //  console.log('Disconnected from server');
	        });

	        chatInput.enable = function() {
	            this.disabled = false;
	        };

	        chatInput.clear = function() {
	            this.value = "";
	        };

	        chatInput.disable = function() {
	            this.disabled = true;
	        };

	        chatInput.addEventListener("keyup", function (e) {

	            if (e.which == 13) {
	                sendMessage(chatInput);
	                return false;
	            }
	        });

	        // User Click send message , this function will trigger

	        chatSend.addEventListener('click', function() {
	            sendMessage(chatInput);
	        });

	        function sendMessage(input) {

	            chatMessage = input.value.trim();

	            if(socketState && chatMessage != '') {

	                message = {};
	                message.type = userToViewer;
	                message.live_video_viewer_id = liveVideoViewerID;
	                message.live_video_id = liveVideoID;
	                message.user_id = userID;
	                message.profile_id = (appSettings.USER == null) ? live_user_id : appSettings.USER.id;
	                message.room = room;
	                message.message = chatMessage;
	                // message.created_at = appSettings.created_at;
	                // message.username = $scope.videoDetails.name;
	                message.userpicture = appSettings.USER_PICTURE;
	                message.username = appSettings.NAME;
	                message.class = appSettings.CLASS;

	                // The user send message display to other users

	                updateMessageToOthers(message);

	                // socketClient.sendMessage(text);

	                $('#chat-box').append(messageTemplate(message));


	                // $("#chat-box").animate({ scrollTop: $('#chat-box').prop("scrollHeight")}, 300);

	                chatInput.clear();

	                $('.chat_box_scroll').scrollTop($('.chat_box_scroll')[0].scrollHeight);
	                
	                /*$(chatBox).animate({
	                    scrollTop: chatBox.scrollHeight,
	                }, 500);*/
	            
	            }
	        
	        }

	        // The user send message display to other users

	        function updateMessageToOthers(data) {

	            socket.emit('message', data); 
	        }

	        // Message Template

	        // <small class="pull-right text-muted"> <span class="glyphicon glyphicon-time"></span>12 mins ago</small>

	        function messageTemplate(data) {

	        	// <small class="text-muted pull-right">'+data.created_at+'</small>

	            var messageTemplate = '';

	            // if (data.class == 'left') {
		            messageTemplate = '<div class="item">';
		            messageTemplate += '<div class="col-lg-2 col-md-2 col-xs-2 col-sm-2" style="padding: 0">';
		            messageTemplate += '<a target="_blank" href="'+apiUrl+'/profile?id='+data.profile_id+'"><img class="chat_img" src="'+data.userpicture+'" alt="'+data.username+'"></a>';
		            messageTemplate += '</div>';
		            messageTemplate += '<div class="message col-lg-10 col-md-10 col-xs-10 col-sm-10">';
		            messageTemplate += '<a target="_blank" href="'+apiUrl+'/profile?id='+data.profile_id+'" class="clearfix"><small class="text-muted pull-left">'+data.username+'</small></a>';
		            messageTemplate += ' <div>'+data.message+'</div>';
		            messageTemplate += '</div>';
		            messageTemplate += '<div class="clearfix"></div>';
		            messageTemplate += '</div>';
		        // }

		         /*else {
		        	messageTemplate = '<li class="'+data.class+' clearfix">';
		            messageTemplate += '<span class="chat-img pull-right">';
		            messageTemplate += '<img style="width: 60px;height: 60px;" src="'+data.userpicture+'" alt="'+data.username+'" class="img-circle">';
		            messageTemplate += '</span>';
		            messageTemplate += '<div class="chat-body clearfix">';
		            messageTemplate += ' <div class="header"> ';
		            messageTemplate += ' <strong class="pull-right primary-font">'+data.username+'</strong>';
		            messageTemplate += '</div>';
		            messageTemplate += ' <p>'+data.message+'</p>';
		            messageTemplate += '</div>';
		            messageTemplate += '</li>';
		        }
	*/
	            return messageTemplate;

	        }

	    }
    

    }

    ]);
