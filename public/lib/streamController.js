liveAppCtrl
.controller('streamCtrl', ['$rootScope', 'socketFactory',  '$sce',
	function ($rootScope, socketFactory, $sce) {

		$scope = $rootScope;

		var socket = {};

		var commonHelper = {};

		console.log("Before "+socket);

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


		$scope.common_helper = function() {

			return {
				obToquery: function(obj, prefix) {
					var str = [];
					for (var p in obj) {
						var k = prefix ? prefix + "[" + p + "]" : p,
										v = obj[k];
						str.push(angular.isObject(v) ? this.obToquery(v, k) : (k) + "=" + encodeURIComponent(v));
					}
					return str.join("&");
				},
			};

		};

		commonHelper = $scope.common_helper();


		$scope.socketrun  = function() {
			
				// var appSettings = $rootScope.appSettings;

					  console.log(appSettings);

					  // appSettings = JSON.parse($rootScope.appSettings);

					  // socket.io now auto-configures its connection when we ommit a connection url
					  var ioSocket = io(appSettings.SOCKET_URL, {
						    // Send auth token on connection, you will need to DI the Auth service above
					   	 	'query': commonHelper.obToquery({ token: appSettings.TOKEN }),
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
					    $window.location.href = appSettings.BASE_URL + 'models/dashboard/profile';
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

		console.log("After "+socket);


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

		socket.on('disconnect', function (data) {
			console.log("disconect");
		});

		socket.on('disconnectAll', function (data) {
			console.log("disconectAll");
		if (appSettings.CHAT_ROOM_ID != data.id && data.ownerId == appSettings.USER.id) {
			console.log("disconect");
		}
		});

		$scope.connectionNow = null;
		// initializing RTCMultiConnection constructor.
		$scope.isStreaming = null;

		function initRTCMultiConnection(userid) {

			var connection = new RTCMultiConnection();
			$scope.connectionNow = connection;

			// memoryStorage.connectionNow = $scope.connectionNow;
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
			  connection.videosContainer.appendChild(video);
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
		 
		  //      event.mediaElement.controls = false;
		  console.log("Media Element "+event.mediaElement);


		  $("#default_image").hide();
		  $("#loader_btn").hide();
		  
		  $scope.open = true;

		  connection.body.appendChild(event.mediaElement);

		 
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
		 console.log('model start broadcast');
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
		});

		socket.on('broadcast-error', function (data) {

			console.log(data);

			if (!appSettings.USER || appSettings.USER.role != 'model') {
			  // alert('Warning', data.msg);
			}
			console.log("Broadcast Error");

			var browser = getBrowser();

	        var m_type = getMobileOperatingSystem();

	        var mobile_ios_type = 0;

	        var rtsp_mobile_type = 0;

	        // if (wowza_ip_address != '' && wowza_ip_address != undefined && socket_url != '' && socket_url != undefined) {

        	if ((browser == 'Safari' || browser == 'IE') || m_type == 'ios' || ($scope.videoDetails.video_url != '' && $scope.videoDetails.video_url != undefined)) {

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

                    alert("There is not live video available, Redirecting into main page");

                    window.location.href = routeUrl;

                   
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

                    alert("There is not live video available, Redirecting into main page");

                    window.location.href = routeUrl;

                   // confirm('The video format is not supported in this browser. Please option some other browser.');
                
                });


				$("#loader_btn").hide();


        	}

			// }

			// window.location.reload(true);
			
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
		

	}
]);

