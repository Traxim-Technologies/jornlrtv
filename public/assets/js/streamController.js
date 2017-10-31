liveAppCtrl

.factory('commonHelper', function($location) {
	return {
		stringRepeat: function(num, replace) {
			return new Array(num + 1).join(replace);
		},
		externalLinks:function(text){
		return String(text).replace(/href=/gm, "class=\"ex-link\" href=");
		
		},
		localStorageIsEnabled: function() {
			var uid = new Date(),
							result;

			try {
				localStorage.setItem("uid", uid);
				result = localStorage.getItem("uid") === uid;
				localStorage.removeItem("uid");
				return result && localStorage;
			} catch (e) {
			}
		},
		readJsonFromController: function(file) {
			var request = new XMLHttpRequest();
			request.open('GET', file, false);
			request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			request.send(null);
			try {
				return JSON.parse(request.responseText);
			} catch (e) {
				return '';
			}
		},
		getBadWords: function(input) {
			if (input) {
				var badwords = [];
				for (var i = 0; i < swearwords.length; i++) {
					var swear = new RegExp(swearwords[i], 'g');
					if (input.match(swear)) {
						badwords.push(swearwords[i]);
					}
				}
				return badwords;
			}
		},
		replaceBadWords: function(input) {
			if (this.localStorageIsEnabled()) {
				if (localStorage.getItem('localSwears') === null) {
					// stringify the array so that it can be stored in local storage
					localStorage.setItem('localSwears', JSON.stringify(readJsonFromController(swearWordPath)));
				}
				swearwords = JSON.parse(localStorage.getItem('localSwears'));
			} else {
				swearwords = this.readJsonFromController(swearWordPath);
			}
			if (swearwords === null) {
				return input;
			}
			if (input) {
				for (var i = 0; i < swearwords.length; i++) {
					var swear =  new RegExp('\\b' + swearwords[i] + '\\b', 'gi');
					if (input.match(swear)) {
						var replacement = this.stringRepeat(swearwords[i].length, "*");
						input = input.replace(swear, replacement);
					}
				}
				return input;
			} else {
				return input;
			}
		},
		obToquery: function(obj, prefix) {
			var str = [];
			for (var p in obj) {
				var k = prefix ? prefix + "[" + p + "]" : p,
								v = obj[k];
				str.push(angular.isObject(v) ? this.obToquery(v, k) : (k) + "=" + encodeURIComponent(v));
			}
			return str.join("&");
		},
		isExpired: function(object) {
			if (!object.expiresOn) {
				return false;
			}
			if (new Date(object.expiresOn).getTime() < new Date().getTime() && object.expiresOn) {
				return true;
			}
			return false;
		},
		scrollTo: function(element, to, duration) {
			if (duration < 0)
				return;
			var difference = to - element.scrollTop;
			var perTick = difference / duration * 10;

			setTimeout(function() {
				element.scrollTop = element.scrollTop + perTick;
				if (element.scrollTop == to)
					return;
				scrollTo(element, to, duration - 10);
			}, 10);
		},
		removeLastSpace: function(str) {
			return str.replace(/\s+$/, '');
		},
		numberToAlpha: function(data) {
			var string = '';
			switch (data) {
				case '0':
					string = 'A';
					break;
				case '1':
					string = 'B';
					break;
				case '2':
					string = 'C';
					break;
				case '3':
					string = 'D';
					break;
				case '4':
					string = 'F';
					break;
			}
			return string;
		},
		secondsToDateTime: function(second, type) {
			var string = '';

			var date = this.coverMilisecondToTime(second * 1000, 'minute');
			string = date.seconds + ' second' + date.secondsS;
			if (date.minutes > 0) {
				string = date.minutes + ' min' + date.minutesS + ' ' + string;
			}
			return string;
			// return;
		},
		coverMilisecondToTime: function(millis, type, options) {
			var seconds = 0;
			var minutes = 0;
			var hours = 0;
			var days = 0;
			var months = 0;
			var years = 0;
			if (type === 'day') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24));
				months = 0;
				years = 0;
			} else if (type === 'second') {
				seconds = Math.floor(millis / 1000);
				minutes = 0;
				hours = 0;
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'minute') {
				if (options && options.fixed) {
					seconds = (millis / 1000).toFixed(options.fixed);
				} else {
					seconds = Math.round((millis / 1000) % 60);
				}
				minutes = Math.floor(millis / 60000);
				hours = 0;
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'hour') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(millis / 3600000);
				days = 0;
				months = 0;
				years = 0;
			} else if (type === 'month') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24) % 30);
				months = Math.floor(((millis / (3600000)) / 24) / 30);
				years = 0;
			} else if (type === 'year') {
				seconds = Math.round((millis / 1000) % 60);
				minutes = Math.floor(((millis / (60000)) % 60));
				hours = Math.floor(((millis / (3600000)) % 24));
				days = Math.floor(((millis / (3600000)) / 24) % 30);
				months = Math.floor(((millis / (3600000)) / 24 / 30) % 12);
				years = Math.floor((millis / (3600000)) / 24 / 365);
			}
			var secondsS = (seconds < 2) ? '' : 's';
			var minutesS = (minutes < 2) ? '' : 's';
			var hoursS = (hours < 2) ? '' : 's';
			var daysS = (days < 2) ? '' : 's';
			var monthsS = (months < 2) ? '' : 's';
			var yearsS = (years < 2) ? '' : 's';
			return {
				seconds: seconds,
				secondsS: secondsS,
				minutes: minutes,
				minutesS: minutesS,
				hours: hours,
				hoursS: hoursS,
				days: days,
				daysS: daysS,
				months: months,
				monthsS: monthsS,
				years: years,
				yearsS: yearsS
			};


		}
	};
}
)
.controller('streamCtrl', ['$rootScope', '$window', 'socketFactory','commonHelper', '$sce',
	function ($rootScope, $window, socketFactory, commonHelper, $sce) {

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
            var isChrome = !!window.chrome && !!window.chrome.webstore;

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

        var mobile_ios_type = 0;

        var rtsp_mobile_type = 0;

        if (wowza_ip_address != '' && wowza_ip_address != undefined && socket_url != '' && socket_url != undefined) {

	        if ((browser == 'Safari' || browser == 'IE') || m_type == 'ios') {

	        		if (m_type == 'ios') {

	        			browser = 'Safari';
	        		}

	        		mobile_ios_type = 1;

					jwplayer.key="M2NCefPoiiKsaVB8nTttvMBxfb1J3Xl7PDXSaw==";

					$("#videos-container").html("");


					var playerInstance = jwplayer("videos-container");

					$scope.url = "";

					var data = new FormData;
					data.append('video_id', $scope.videoDetails.id);
					data.append('device_type', 'web');
					data.append('browser', browser);

					$.ajax({
						type : 'post',
						url : url+'/userApi/get_live_url',
						contentType : false,
						processData: false,
						
						async : false,
						data : data,
						success : function(result) {

							if (result.success) {

								$scope.url = result.url;

							} else {

								console.log(result.message);

							}
							
						}, 
				    	error : function(result) {

				    	}
					});

					console.log("Url "+$scope.url);


					playerInstance.setup({

					   file : $sce.trustAsResourceUrl($scope.url),

					    width: "100%",
					    aspectratio: "16:9",
					    primary: "flash",
					    controls : true,
					    "controlbar.idlehide" : false,
					    controlBarMode:'floating',
					    "controls": {
					      "enableFullscreen": false,
					      "enablePlay": false,
					      "enablePause": false,
					      "enableMute": true,
					      "enableVolume": true
					    },
					    autostart : true,
					   /* "sharing": {
					        "sites": ["reddit","facebook","twitter"]
					      }*/
					});


					playerInstance.on('error', function() {

						console.log("setupError");

	                    $("#videos-container").hide();

	                    var hasFlash = false;
	                    
	                   try {
	                        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
	                        if (fo) {
	                            hasFlash = true;
	                        }
	                    } catch (e) {
	                        if (navigator.mimeTypes
	                                && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
	                                && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
	                            hasFlash = true;
	                        }
	                    }

	                    console.log(hasFlash == false);

	                    $('#main_video_setup_error').css('display', 'block');

	                    if (hasFlash == false) {
	                        $('#flash_error_display').show();

	                        confirm('Download Flash Player. Flash Player Fail to Load.');

	                        return false;
	                    }

	                   
	                   // confirm('The video format is not supported in this browser. Please option some other browser.');

					});


					playerInstance.on('setupError', function() {

					 	console.log("setupError");

	                    $("#videos-container").hide();

	                    var hasFlash = false;
	                   try {
	                        var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
	                        if (fo) {
	                            hasFlash = true;
	                        }
	                    } catch (e) {
	                        if (navigator.mimeTypes
	                                && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
	                                && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
	                            hasFlash = true;
	                        }
	                    }

	                    $('#main_video_setup_error').show();

	                    if (hasFlash == false) {
	                        $('#flash_error_display').show();

	                        confirm('Download Flash Player. Flash Player Fail to Load.');

	                        return false;
	                    }

	                    

	                   // confirm('The video format is not supported in this browser. Please option some other browser.');
	                
	                });


					 $("#loader_btn").hide();


	        }


			var ws = new WebSocket('wss://'+socket_url+'/rtprelay');

			console.log(ws);

			var videoInput;
			var videoOutput;
			var webRtcPeer;
			var state = null;
			var destinationIp;
			var destinationPort;
			var rtpSdp;

			console.log('Page loaded ...');
			videoInput = document.getElementById('videoInput');
			videoOutput = document.getElementById('videoOutput');
			rtpSdp = document.getElementById('rtpSdp');

			ws.onmessage = function(message) {


				var parsedMessage = JSON.parse(message.data);
				console.info('Received message: ' + message.data);

				switch (parsedMessage.id) {
				case 'startResponse':
					startResponse(parsedMessage);
					break;
				case 'error':
					onError('Error message from server: ' + parsedMessage.message);
					break;
				case 'iceCandidate':
					webRtcPeer.addIceCandidate(parsedMessage.candidate)
					break;
				default:
					onError('Unrecognized message', parsedMessage);
				}
			}

			$scope.start = function() {
				console.log('Starting video call ...')

				// showSpinner(videoInput);

				console.log('Creating WebRtcPeer and generating local sdp offer ...');

			    var options = {
			      localVideo: videoInput,
			      onicecandidate : onIceCandidate
			    }

			    webRtcPeer = kurentoUtils.WebRtcPeer.WebRtcPeerSendrecv(options, function(error) {
			        if(error) return onError(error);
			        this.generateOffer(onOffer);
			    });
			}

			function onIceCandidate(candidate) {
				   console.log('Local candidate' + JSON.stringify(candidate));

				   var message = {
				      id : 'onIceCandidate',
				      candidate : candidate
				   };
				   sendMessage(message);
			}

			function onOffer(error, offerSdp) {
				if(error) return onError(error);

				console.info('Invoking SDP offer callback function ' + location.host);
				var message = {
					id : 'start',
					sdpOffer : offerSdp,
					rtpSdp : rtpSdp.value
				}
				console.log("This is the offer sdp:");
				console.log(offerSdp);
				sendMessage(message);
			}

			function onError(error) {
				console.error(error);
			}

			function startResponse(message) {
				console.log('SDP answer received from server. Processing ...');
				webRtcPeer.processAnswer(message.sdpAnswer);
			}

			$scope.stop = function() {
				console.log('Stopping video call ...');
				if (webRtcPeer) {
					webRtcPeer.dispose();
					webRtcPeer = null;

					var message = {
						id : 'stop'
					}
					sendMessage(message);
				}
				// hideSpinner(videoInput, videoOutput);
			}

			function sendMessage(message) {
				var jsonMessage = JSON.stringify(message);
				console.log('Senging message: ' + jsonMessage);
				ws.send(jsonMessage);
			}

			/*function showSpinner() {
				for (var i = 0; i < arguments.length; i++) {
					arguments[i].poster = './img/transparent-1px.png';
					arguments[i].style.background = 'center transparent url("./img/spinner.gif") no-repeat';
				}
			}

			function hideSpinner() {
				for (var i = 0; i < arguments.length; i++) {
					arguments[i].src = '';
					arguments[i].poster = './img/webrtc.png';
					arguments[i].style.background = '';
				}
			}*/

			function forceEvenRtpPort(rtpPort) {
				if ((rtpPort > 0) && (rtpPort % 2 != 0))
					return rtpPort - 1;
				else return rtpPort;
			}

			function updateRtpSdp() {
				var destination_ip;
				var destination_port;

				if (!destinationIp.value)
					destination_ip= wowza_ip_address;
				else
					destination_ip = destinationIp.value.trim();

				if (!destinationPort.value)
					destination_port="33124";
				else
					destination_port = forceEvenRtpPort(destinationPort.value.trim());


				destination_ip = wowza_ip_address;

					rtpSdp.value = 'v=0\n'
					+ 'o=- 0 0 IN IP4 ' + destination_ip + '\n'
					+ 's=Kurento\n'
					+ 'c=IN IP4 ' + destination_ip + '\n'
					+ 't=0 0\n'
					+ 'm=video ' + destination_port + ' RTP/AVP 100\n'
					+ 'a=rtpmap:100 H264/90000\n';

					console.log(rtpSdp.value);
			}

		}

		var socket = {};

		// console.log("Before "+socket);

		$scope.socketrun  = function() {

				/*if (appSettings == undefined) {

					$.ajax({
						type : 'post',
						url : apiUrl+'appSettings/'+$stateParams.id,
						contentType : false,
						processData: false,
						async : false,
						data : {},
						success : function(result) {

							// console.log("result "+result);

							memoryStorage.appSettings = result;

						},
						
				    	error : function(result) {

				    	}
					});

				}*/

				// console.log(memoryStorage.appSettings);
			
				//var appSettings = JSON.parse(appSettings);

					 console.log($scope.appSettings);

					  // appSettings = JSON.parse($rootScope.appSettings);

					  // socket.io now auto-configures its connection when we ommit a connection url
					  var ioSocket = io($scope.appSettings.SOCKET_URL, {
						    // Send auth token on connection, you will need to DI the Auth service above
					   	 	'query': commonHelper.obToquery({ token: $scope.appSettings.TOKEN }),
					    	path: '/socket.io-client'
					  });

					  var socket = socketFactory({ ioSocket: ioSocket });

					  socket.on('another-model-connected', function () {

					    //       var cookies = document.cookie.split(";");
					    //       console.log(cookies);
					    //       for(var i=0; i < cookies.length; i++) {
					    //         var equals = cookies[i].indexOf("=");
					    //         var name = equals > -1 ? cookies[i].substr(0, equals) : cookies[i];
					    //         document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
					    //       }
					    //call logout to force remove http flag
					    alert('You are connecting in another session. exit now!');
					    // $window.location.href = appSettings.BASE_URL + 'models/dashboard/profile';
					  });

				  return {
				    socket: socket,

				    /**
				    * send send-tip event to server
				    */
				    sendTip: function sendTip(data) {
				      socket.emit('send-tip', data);
				    },


				    /**
				     * Event for send tip callback
				     */
				    onReceiveTip: function onReceiveTip(cb) {
				      cb = cb || angular.noop;
				      socket.on('send-tip', cb);
				    },


				    /**
				     * new member join to room
				     */

				    joinRoom: function joinRoom(data) {
				      socket.emit('join-room', data);
				    },

				    onLeaveRoom: function onLeaveRoom(cb) {
				      cb = cb || angular.noop;

				      socket.on('leave-room', cb);
				    },
				    onMemberJoin: function onMemberJoin(cb) {
				      cb = cb || angular.noop;
				      //who
				      //total members...
				      //{ member: 2134, .... }
				      socket.on('join-room', cb);
				    },

				    //event get list models online
				    onModelOnline: function onModelOnline(cb) {
				      cb = cb || angular.noop;
				      socket.on('model-online', cb);
				    },

				    //event check current model online
				    getCurrentModelOnline: function getCurrentModelOnline(roomId) {
				      socket.emit('current-model-online', roomId);
				    },

				    //event get current model of room online
				    onCurrentModelOnline: function onCurrentModelOnline(cb) {
				      cb = cb || angular.noop;
				      socket.on('current-model-online', cb);
				    },

				    getModelStreaming: function getModelStreaming(roomId, modelId) {
				      socket.emit('model-streaming', { room: roomId, model: modelId });
				    },

				    /**
				     * notify with model when they receive new tokens
				     */
				    sendModelReceiveInfo: function sendModelReceiveInfo(tokens) {
				      socket.emit('model-receive-info', tokens);
				    },

				    /**
				     * model receive message
				     */
				    onModelReceiveInfo: function onModelReceiveInfo(cb) {
				      cb = cb || angular.noop();
				      socket.on('model-receive-info', cb);
				    },
				    onModelStreaming: function onModelStreaming(cb) {
				      cb = cb || angular.noop;
				      //who
				      //total members...
				      //{ member: 2134, .... }
				      socket.on('model-streaming', cb);
				    },
				    on: function on(event, cb) {
				      socket.on(event, cb);
				    },
				    emit: function emit(event, data, cb) {
				      socket.emit(event, data, cb);
				    }
				  };
		}

		socket = $scope.socketrun();
		// socket.resetData();

		// console.log("After "+socket);
/*
		$scope.sureStreaming = function() {
			$window.sessionStorage.reload = 0;
			window.btn_clicked = false;
			if (confirm('Are you want to stream your video ? ')) {
				if ($window.sessionStorage.reload == undefined || $window.sessionStorage.reload == '' || $window.sessionStorage.reload == 0) {
					$window.sessionStorage.reload = 1;
				    window.location.reload();
				} 
			} else {
			   // alert('Why did you press cancel? You should have confirmed');
			}
		}*/


		/*var data = new FormData;
		data.append('id', memoryStorage.user_id);

		$.ajax({
			url : apiUrl+'userDetails',
			type : 'post',
			contentType : false,
			processData: false,
			async : false,
			data : data,
			success :  function(data) {
				memoryStorage.access_token = data.token;
				memoryStorage.user_type = data.user_type;
				memoryStorage.one_time_subscription = data.one_time_subscription;
				localStorage.setItem('sessionStorage', JSON.stringify(memoryStorage));
			},
	    	error : function(result) {

	    	}
		});

		var appSettings = JSON.parse(memoryStorage.appSettings);*/
		

		//$scope.user_id = memoryStorage.user_id;


		$('#myModal').modal('hide');		

		$(".home div").removeClass('modal-backdrop fade in');

		// using single socket for RTCMultiConnection signaling
		var onMessageCallbacks = {};

		$scope.isOffline = false;
		$scope.roomId = null;
		$scope.virtualRoom = null;

		$scope.streamingInfo = {
			spendTokens: 0,
			time: 0,
			tokensReceive: 0,
			type: 'public',
			hasRoom: true
		};

		socket.on('broadcast-message', function (data) {
		if (data.sender == connection.userid) {
		  return;
		}
		if (onMessageCallbacks[data.channel]) {
		  onMessageCallbacks[data.channel](data.message);
		}
		});


		socket.on('public-room-status', function (status) {
		if (!status) {
		  $('#videos-container').removeClass('loader');
		  $('#offline-image').show();
		  $scope.isOffline = true;
		} else {
		  $('#videos-container').addClass('loader');
		  $('#offline-image').hide();
		  $scope.isPrivateChat = false;
		  $scope.isGroupLive = false;
		  $scope.isOffline = false;
		}
		});

		$scope.isShowPrivateMessage = false;

		$scope.connectionNow = null;

		socket.on('disconnect', function (data) {
			// console.log("disconect");

			// alert(data);


			/*alert($scope.connectionNow.session);

			return false;*/

			/*connection.streams[e.streamid].stopRecording(function (blob) { 
	            // var mediaElement = document.createElement('video'); 
	           //  mediaElement.src = URL.createObjectURL(blob.video); 
	           


	        }); */



		});

		socket.on('disconnectAll', function (data) {
			console.log("disconectAll");
		if (appSettings.CHAT_ROOM_ID != data.id && data.ownerId == appSettings.USER.id) {
			// console.log("disconect");
		  
		}
		});

		
		// initializing RTCMultiConnection constructor.
		$scope.isStreaming = null;

		function initRTCMultiConnection(userid) {

			var connection = new RTCMultiConnection();

			$scope.connectionNow = connection;

			// memoryStorage.connectionNow = $scope.connectionNow;

			//localStorage.setItem('sessionStorage', JSON.stringify(memoryStorage));	


			connection.body = document.getElementById('videos-container');
			connection.channel = connection.sessionid = connection.userid = userid || connection.userid;

			connection.sdpConstraints.mandatory = {
			  OfferToReceiveAudio: true,
			  OfferToReceiveVideo: true
			};

			// using socket.io for signaling
			connection.openSignalingChannel = function (config) {
			  var channel = config.channel || this.channel;
			  onMessageCallbacks[channel] = config.onmessage;
			  if (config.onopen) {
			    setTimeout(config.onopen, 1000);
			  }

			  return {
			    send: function send(message) {
			      socket.emit('broadcast-message', {
			        sender: connection.userid,
			        channel: channel,
			        message: message
			      });
			    },
			    channel: channel
			  };
			};
			connection.onMediaError = function (error) {
			  //              JSON.stringify(error)
			  alertify.alert('Warning', error.message);
			};

			//fix echo
			connection.onstream = function (event) {
			  if (event.mediaElement) {
			    event.mediaElement.muted = true;
			    delete event.mediaElement;
			  }

			  var video = document.createElement('video');
			  if (event.type === 'local') {
			    video.muted = true;
			  }

			  video.src = URL.createObjectURL(event.stream);

			  console.log("Video Src "+video.src);

			  connection.videosContainer.appendChild(video);

			  console.log("StreamId "+event.streamid);

			  console.log(event);

			      // e.type == 'remote' || 'local' 
			    /*connection.streams[e.streamid].startRecording({ 
			        video: true 
			    }); */

			   /* // record 10 sec audio/video 
			    var recordingInterval = 10 * 10000; 

			    setTimeout(function () { 
			        connection.streams[e.streamid].stopRecording(function (blob) { 
			            var mediaElement = document.createElement('video'); 
			            mediaElement.src = URL.createObjectURL(blob.video); 
			           //  document.documentElement.appendChild(h2); 
			        }); 
			    }, recordingInterval)*/



			};

			//disable log
			connection.enableLogs = false;

			return connection;
		}

		var timeout = null;

		// this RTCMultiConnection object is used to connect with existing users
		var connection = initRTCMultiConnection();

		//get other TURN server
		//TODO - config our turn server
		var setupConnection = function setupConnection() {

		connection.getExternalIceServers = true;

		connection.onstream = function (event) {

			console.log("Stream Id "+event.streamid);

			console.log(URL.createObjectURL(event.stream));

			console.log(event);




		  if (event.type == 'local') {

		  	if(is_vod == 1) {

		  		alert(is_vod);

		  		connection.streams[event.streamid].startRecording({ 
			        video: true ,
			        audio:true,
			    });

		  	}

		    var initNumber = 1;


		    console.log("capture image");

		    var capture = function capture() {


		    	console.log("Inside capture image");

		    	console.log(event.userid);

		      connection.takeSnapshot(event.userid, function (snapshot) {

		      	console.log("url "+snapshot);
		      	console.log("url "+url);

		      	$.ajax({

		      		type : 'post',
		      		url : url+'/take_snapshot/'+$scope.videoDetails.id,
		      		data : {base64: snapshot,shotNumber: initNumber},
		      		success : function(data) {
		      			
		      		}

		      	});

		      	$scope.viewerscnt = 0;

		      	$scope.minutes = 0;

		      	/*var data = new FormData;
				data.append('id', memoryStorage.user_id);
				data.append('token', memoryStorage.access_token);
				data.append('mid', $stateParams.id);

		      	$.ajax({
		      		type : 'post',
		      		url : apiUrl+'getVideoDetails',
		      		contentType : false,
					processData: false,
					async : false,
					data : data,
		      		success : function(data) {
		      			$scope.minutes = data.no_of_minutes;
		      			$scope.viewerscnt = data.viewer_cnt;

		      			$("#viewers_cnt").html(data.viewer_cnt);
		      			$("#minutes").html(data.no_of_minutes);
		      		}

		      	});*/
		   	 });

		     initNumber = initNumber < 6 ? initNumber + 1 : 1;

		     timeout = setTimeout(capture, 30000);

	
		    };

		    capture();

		    $scope.$on('destroy', function () {
		      clearTimeout(timeout);
		    });
		  }
		  //      event.mediaElement.controls = false;
		 // console.log("Media Element "+event.mediaElement);


		  $("#default_image").hide();
		  $("#loader_btn").hide();
		  
		  $scope.open = true;

		  connection.body.appendChild(event.mediaElement);

		 /* $.ajax({
				type : 'post',
				url : apiUrl+"live_streaming/"+$stateParams.id,
				data : {id : memoryStorage.user_id, token : memoryStorage.access_token},
				success : function(result) {*/

					$scope.open = true;

					$scope.displayStop = true;

		/*		}

		 });*/

		  if (connection.isInitiator == false && !connection.broadcastingConnection) {
		    $scope.isStreaming = true;
		    // "connection.broadcastingConnection" global-level object is used
		    // instead of using a closure object, i.e. "privateConnection"
		    // because sometimes out of browser-specific bugs, browser
		    // can emit "onaddstream" event even if remote user didn't attach any stream.
		    // such bugs happen often in chrome.
		    // "connection.broadcastingConnection" prevents multiple initializations.

		    // if current user is broadcast viewer
		    // he should create a separate RTCMultiConnection object as well.
		    // because node.js server can allot him other viewers for
		    // remote-stream-broadcasting.
		    // connection.userid = 1;
		    connection.broadcastingConnection = initRTCMultiConnection(connection.userid);

		    // to fix unexpected chrome/firefox bugs out of sendrecv/sendonly/etc. issues.
		    connection.broadcastingConnection.onstream = function () {};

		    connection.broadcastingConnection.session = connection.session;
		    connection.broadcastingConnection.attachStreams.push(event.stream); // broadcast remote stream
		    connection.broadcastingConnection.dontCaptureUserMedia = true;

		    // forwarder should always use this!
		    connection.broadcastingConnection.sdpConstraints.mandatory = {
		      OfferToReceiveVideo: false,
		      OfferToReceiveAudio: false
		    };

		    connection.broadcastingConnection.open({
		      dontTransmit: true
		    });
		    $('#offline-image').hide();
		    $('#videos-container').removeClass('loader');
		  }
		};
		};
		setupConnection();


		$scope.initRoom = function (roomId, virtualRoom) {
			$scope.roomId = roomId;
			$scope.virtualRoom = virtualRoom;

			//get model streaming
			socket.emit('join-broadcast', {
			  broadcastid: $scope.virtualRoom,
			  room: $scope.roomId,
			  userid: connection.userid,
			  openBroadcast: false,
			  typeOfStreams: {
			    video: false,
			    screen: false,
			    audio: false,
			    oneway: true
			  }
			});
		};

		// $("#loader_btn").show();

				// ask node.js server to look for a broadcast
		// if broadcast is available, simply join it. i.e. "join-broadcaster" event should be emitted.
		// if broadcast is absent, simply create it. i.e. "start-broadcasting" event should be fired.
		// TODO - model side should start broadcasting and member/client side should join only
		$scope.openBroadcast = function (room, virtualRoom) {

			console.log("Open Broadcast");

			$scope.roomId = room;
			$scope.virtualRoom = virtualRoom;


				connection.session = {
			    video: true,
	            screen: false,
	            audio: true,
	            oneway: true
	          };

	          socket.emit('join-broadcast', {
	            broadcastid: $scope.virtualRoom,
	            room: $scope.roomId,
	            userid: connection.userid,
	            typeOfStreams: connection.session,
	            openBroadcast: true
	          });

	          $scope.isStreaming = true;

	          $scope.open = false;

	          $("#loader_btn").show();

	          // window.btn_clicked = true;

	       	if (wowza_ip_address != '' && wowza_ip_address != undefined && socket_url != '' && socket_url != undefined) {
	           	$scope.start();
	       	}
	          // $window.sessionStorage.reload = 0;
			
		}


		/*var data = new FormData;
		data.append('id', memoryStorage.user_id);
		data.append('token', memoryStorage.access_token);
		data.append('mid', $stateParams.id);
		data.append('device_type', 'web');*/

		/*$.ajax({
				url : apiUrl+"video/"+$stateParams.id,
				type : 'post',
				contentType : false,
				processData: false,
				beforeSend: function(xhr){
					$(".fond").show();
				},
				async : false,
				data : data,
				success : function(data) {

					if (data.success == false && data.error_code == 104) {

						$scope.loadToken();

					}

					if(data == '') {

							UIkit.notify({message : 'This video no more available, Please Start New Video Stream By clicking "Start BroadCasting" Button', status : 'warning', timeout:5000, pos : 'top-center'});

							$state.go('restricted.video-form', {}, {reload : true});

					} else {
*/
							$('#myModal').modal('hide');

							/*$scope.videoDetails = data;

							$scope.viewerscnt = data.viewer_cnt;

		      				$scope.minutes = data.no_of_minutes;*/



							$("#videos-container").show();

							$scope.user_id = live_user_id;

							console.log($scope.user_id );

							console.log($scope.videoDetails.user_id)

							if ($scope.user_id != $scope.videoDetails.user_id) {
								// $("#default_image").hide();
								$("#loader_btn").hide();

							} else {

								$scope.openBroadcast($scope.videoDetails.id, $scope.videoDetails.virtual_id);

							}

							/*if($scope.videoDetails.video_url != null && $scope.videoDetails.video_url != '' && !mobile_ios_type) {

							} else {
								console.log($scope.videoDetails.video_url);

								$scope.initRoom($scope.videoDetails.id, $scope.videoDetails.virtual_id);

							}*/


							// $scope.start();

							if($scope.videoDetails.video_url != null && $scope.videoDetails.video_url != '' && !mobile_ios_type) {

								jwplayer.key="M2NCefPoiiKsaVB8nTttvMBxfb1J3Xl7PDXSaw==";

								var playerInstance = jwplayer("rtsp_container");

								console.log("data Url "+$scope.videoDetails.video_url);

								$("#videos-container").hide();

								$("#loader_btn").hide();

								$("#rtsp_container").show();

								rtsp_mobile_type = 1;

								playerInstance.setup({

								   file : $sce.trustAsResourceUrl($scope.videoDetails.video_url),
									width: "100%",
									aspectratio: "16:9",
									primary: "flash",
									controls : true,
									"controlbar.idlehide" : false,
									controlBarMode:'floating',
									"controls": {
									  "enableFullscreen": false,
									  "enablePlay": false,
									  "enablePause": false,
									  "enableMute": true,
									  "enableVolume": true
									},
									autostart : true,

								});

							} else {

								$scope.initRoom($scope.videoDetails.id, $scope.videoDetails.virtual_id);

							}


							// if ($window.sessionStorage.reload == 1) {
								// $scope.start();
								


								/*memoryStorage.room_id = $scope.videoDetails.id;

								localStorage.setItem('sessionStorage', JSON.stringify(memoryStorage));								

							// }

								console.log(data.video_url != null && data.video_url != '');

								console.log(data.video_url);

								console.log("mobile_ios_type "+mobile_ios_type)


								if(data.video_url != null && data.video_url != '' && !mobile_ios_type) {

									jwplayer.key="M2NCefPoiiKsaVB8nTttvMBxfb1J3Xl7PDXSaw==";

									var playerInstance = jwplayer("rtsp_container");

									console.log("data Url "+data.video_url);

									$("#videos-container").hide();

									$("#rtsp_container").show();

									rtsp_mobile_type = 1;

									playerInstance.setup({

									   file : $sce.trustAsResourceUrl(data.video_url),
										width: "100%",
										aspectratio: "16:9",
										primary: "flash",
										controls : true,
										"controlbar.idlehide" : false,
										controlBarMode:'floating',
										"controls": {
										  "enableFullscreen": false,
										  "enablePlay": false,
										  "enablePause": false,
										  "enableMute": true,
										  "enableVolume": true
										},
										autostart : true,

									});

								} else {

									$scope.initRoom($scope.videoDetails.id, $scope.videoDetails.virtual_id);

								}*/
/*
					}
				},
				complete : function() {
		    		$(".fond").hide();
		    	},
		    	error : function(result) {

		    	}
		});*/

		/**
		* join broadcast directly, use for member side
		*/

		$scope.joinBroadcast = function (room, virtualRoom) {
			//check model is online / streaming then open broadcast.
			socket.emit('has-broadcast', virtualRoom, function (has) {

			  if (!has) {
			    //TODO - should show nice alert message
			    $('#offline-image').show();
			    //       $scope.isOffline = true;
			    $('#videos-container').removeClass('loader');
			    return;
			  }
			  $scope.isPrivateChat = false;
			  $scope.isGroupLive = false;
			  $scope.isOffline = false;

			  $scope.roomId = room;
			  $scope.virtualRoom = virtualRoom;
			  //TODO - check model room is open or not first?
			  connection.session = {
			    video: true,
			    screen: false,
			    audio: true,
			    oneway: true
			  };
			  socket.emit('join-broadcast', {
			    broadcastid: $scope.virtualRoom,
			    room: $scope.roomId,
			    userid: connection.userid,
			    typeOfStreams: connection.session
			  });
			});
		};

		// this event is emitted when a broadcast is already created.
		socket.on('join-broadcaster', function (broadcaster, typeOfStreams) {

			connection.session = typeOfStreams;
			connection.channel = connection.sessionid = broadcaster.userid;


			connection.sdpConstraints.mandatory = {
			  OfferToReceiveVideo: !!connection.session.video,
			  OfferToReceiveAudio: !!connection.session.audio
			};

			connection.join({
			  sessionid: broadcaster.userid,
			  userid: broadcaster.userid,
			  extra: {},
			  session: connection.session
			});
		});

		// this event is emitted when a broadcast is absent.
		socket.on('start-broadcasting', function (typeOfStreams) {
			 // console.log('model start broadcast');
			// host i.e. sender should always use this!
			connection.sdpConstraints.mandatory = {
			  OfferToReceiveVideo: false,
			  OfferToReceiveAudio: false
			};
			connection.session = typeOfStreams;
			connection.open({
			  dontTransmit: true
			});

			if (connection.broadcastingConnection) {
			  // if new person is given the initiation/host/moderation control
			  connection.close();
			  connection.broadcastingConnection = null;
			}
		});

		socket.on('model-left', function () {
			//close connect if model live
			connection.close();
			connection.broadcastingConnection = null;

			console.log("connection close");

			// alert("diconn model");
		});

		socket.on('broadcast-error', function (data) {

			// console.log(data);
			if (!appSettings.USER || appSettings.USER.role != 'model') {
			  // alert('Warning', data.msg);
			}
			console.log("Broadcast Error");

			if (!mobile_ios_type && !rtsp_mobile_type) {

				window.location.href = stop_streaming_url;

			}


			/*var data = new FormData;
			data.append('id', memoryStorage.user_id);
			data.append('token', memoryStorage.access_token);
			data.append('model', 1);

			$("#videos-container").hide();

			if (!mobile_ios_type && !rtsp_mobile_type) {
			
				$.ajax({

		      		type : 'post',
		      		url : apiUrl+"delete_streaming/"+appSettings.CHAT_ROOM_ID,
		      		contentType : false,
					processData: false,
					async : false,
					data : data,
		      		success : function(data) {
		      			UIkit.notify({message : 'This video no more available', status : 'warning', timeout:5000, pos : 'top-center'});

		      			$state.go('static.home', {}, {reload : true});
		      		}

	      		});
			}

			$scope.isStreaming = false;
			$window.sessionStorage.reload = 0;*/
		});

		//rejoin event
		socket.on('rejoin-broadcast', function (data) {

			connection = initRTCMultiConnection();
			setupConnection();

			socket.emit('join-broadcast', {
			  broadcastid: data.id,
			  room: data.room,
			  userid: connection.userid,
			  typeOfStreams: connection.typeOfStreams
			});
		});


		var getViewerCnt = function getViewerCnt() {

	      	$.ajax({
	      		type : 'post',
	      		url : url+'/get_viewer_cnt?id='+$scope.videoDetails.id,
	      		success : function(data) {

	      			console.log(data.model.status);

	      			if (data.model.status == 1) {

	      				console.log("stop_streaming_url");

	      				window.location.href = stop_streaming_url;

	      			}

	      			$("#viewers_cnt").html(data.viewer_cnt);
	      		}

	      	});

		    timeout = setTimeout(getViewerCnt, 30000);
	
		};

		getViewerCnt();

	    $scope.$on('destroy', function () {

	    alert("diconn destroy");

	      clearTimeout(viewerCount);

	    });

	    connection.onstreamended = function (e) {

	    	// alert("streamid"+e.streamid);

	    	if (is_vod == 1) {

		    	connection.streams[e.streamid].stopRecording(function (blob) {
				   // var mediaElement = document.createElement('audio'); 

				   	var blob_url = URL.createObjectURL(blob.video);

				   // alert(URL.createObjectURL(blob.video)); 

				    console.log(blob.video);

					/*var myFile = new File(blob.video);

					console.log(myFile);*/

					var xhr = new XMLHttpRequest;
					xhr.responseType = 'blob';

					xhr.onload = function() {
					   var recoveredBlob = xhr.response;

					   var reader = new FileReader;

					   reader.onload = function() {

					     	var blobAsDataUrl = reader.result;
					     // window.location = blobAsDataUrl;

					     	// console.log(blobAsDataUrl);

						    var data = new FormData();
							//data.append('blob_url', myFile);
							data.append('id', live_user_id);
							data.append('video_blob', blobAsDataUrl);
							data.append('token', user_token);
							data.append('video_id', $scope.videoDetails.id);

							$.ajax({
								type : 'post',
								url : url+'/userApi/save_vod',
								contentType : false,
								processData: false,
								
								async : false,
								data : data,
								success : function(result) {

									console.log(result);

									window.location.href = stop_streaming_url;
									
								}, 
						    	error : function(result) {

						    	}
							});
						};

					   reader.readAsDataURL(recoveredBlob);
					};
					xhr.open('GET', blob_url);
					xhr.send();
				    
				});

	    	} else {

	    		window.location.href = stop_streaming_url;
	    		
	    	}

	    }

	    $scope.stopStreaming = function() {

	    	$rootScope.$emit('model_leave_room');

	    	// stop all local media streams
			connection.streams.stop('local');

			// stop all remote media streams
			connection.streams.stop('remote');

			// stop all media streams
			connection.streams.stop();

	    	
	    };

	    /*$scope.$on('stop_streaming', function () {

	    	
	      
	    });*/
		

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

	        var liveVideoViewerID = (appSettings.USER == null) ? $scope.user_id : 0;

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
	                message.profile_id = (appSettings.USER == null) ? $scope.user_id : appSettings.USER.id;
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
		            messageTemplate += '<a target="_blank" href="'+url+'/profile?id='+data.profile_id+'"><img class="chat_img" src="'+data.userpicture+'" alt="'+data.username+'"></a>';
		            messageTemplate += '</div>';
		            messageTemplate += '<div class="message col-lg-10 col-md-10 col-xs-10 col-sm-10">';
		            messageTemplate += '<a target="_blank" href="'+url+'/profile?id='+data.profile_id+'" class="clearfix"><small class="text-muted pull-left">'+data.username+'</small></a>';
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
