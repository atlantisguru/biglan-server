<!doctype html>
<html lang="hu">
    <head>
    	<title>
			@yield('title')
		</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ Session::token() }}">
        <link rel="stylesheet" href="{{ url('css/fontawesome.5.6.3.css') }}">
        <link rel="stylesheet" href="{{ url('css/bootstrap.4.1.3.min.css') }}">
    	<link rel="stylesheet" type="text/css" href="{{ url('css/dark-mode.css') }}">
        
		
        <title>BigLan</title>
    </head>
    @if(Auth::check())
		@if(Auth::user()->theme == "dark")
			<body data-theme="dark">
		@endif
    @else
		<body>
    @endif
        @include('nav')
		
		<div class="container-fluid">
		@php
			$enableNotifications = (int)App\Models\GlobalSettings::where("name", "enable-notifications")->first()->value;
			$masterKey = config('custom.masterkey');
			$isValidFormat = is_string($masterKey) && strlen($masterKey) === 32 && preg_match('/^[A-Za-z0-9]+$/', $masterKey);
		@endphp
        @if($enableNotifications === 0)
		<div class="row bg-danger text-white">
			<div class="col-12 p-1">
        		{{ __('all.global_settings.notifications_off_warning') }} <i>enable-notifications</i> {{ __('all.global_settings.option') }} <a href={{ url("globalsettings") }} class="text-light font-weight-bold" target="_blank">{{ __('all.global_settings.global_settings') }}</a>.
			</div>
		</div>
        @endif
        @if(!$isValidFormat)
		<div class="row bg-warning">
			<div class="col-12 p-1">
        		{{ __('all.layout.env_master_key_warning') }}
			</div>
		</div>
        @endif
        
 @yield('content')
		</div>
    	<div class="modal" tabindex="-1" role="dialog" id="mobile">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __("all.layout.find_workstation") }}</h5>
	  </div>
      <div class="modal-body" id="mobile-content">
		<div class="row">
			<div class="col-12">
				<div class="input-group">
					<input type="text" class="form-control" placeholder='{{ __("all.layout.start_typing") }}' id="id" autocomplete="off">
				</div>
			</div>
		</div>
    	<canvas id="canvas" width="0" height="0" style="display:none;"></canvas>
		<div class="row">
			<div class="col-12 mt-3" id="search-result">
			</div>
		</div>

		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("all.button.close") }}</button>
      </div>
    </div>
  </div>
</div>

@if(auth()->user()->hasPermission('write-intervention'))
<div class="modal" tabindex="-1" role="dialog" id="operator">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __("all.layout.intervention") }}</h5>
	  </div>
      <div class="modal-body" id="operator-content">
		<p><strong>{{ __("all.layout.what_happened") }}</strong></p>
        <div class="row">
			<div class="col-12">
				<input type="hidden" class="form-control" id="operator-wsid" value="">
				<input type="text" class="form-control" placeholder='{{ __("all.layout.event_description") }}' id="operator-event">
        		<a href="javascript:void(0)" id="save-intervention-suggestion" class="d-none"><i class="fas fa-save text-primary" title="{{ __('all.layout.save_this_suggestion') }}" style="position:absolute; top:10px; right:30px"></i></a>
			</div>
		</div>
     	<div class="row mt-2">
			<div class="col-6">
            	<strong>{{ __("all.layout.operators") }}</strong>
        		<br>
        		<div id="operators">
				@php
            		$users = \App\Models\Users::orderBy("username", "ASC")->get();
            		foreach($users as $user) {
            			if ($user->username != "SYSTEM" && $user->confirmed != 0) {
            				echo "<label><input type='checkbox' name='operators' value='" . $user->username . "' " . (($user->id==Auth::user()->id)?"CHECKED":"") . "> $user->username</label><br>";
                        }
            		}
            	@endphp
            	</div>
        	</div>
            <div class="col-6">
            <label><input type="checkbox" name="special" value="keszenlet" id="operator-keszenlet"> {{ __("all.layout.standby_duty") }}</label><br>
            <strong>{{ __("all.layout.interval") }}</strong><br>
            <input type="time" name="time_period" class="form-control" id="operator-time" value="00:00">
            </div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("all.button.close") }}</button>
		<button type="button" class="btn btn-primary" id="create-operator-event" data-dismiss="modal">{{ __("all.button.send") }}</button>
	  </div>
    </div>
  </div>
</div>
@endif
                
	@php
       $operatorEvents = \App\Models\InterventionTemplates::orderBy("priority", "DESC")->get();
       $events = [];
        foreach($operatorEvents as $event) {
        	$events[] = array("label" => $event->message, "value" => $event->message);
        }
        $events = json_encode(array_values($events));
        
	@endphp
    
<div class="contextmenu dropdown-menu"></div>
        <link rel="stylesheet" type="text/css" href="{{ url('css/jquery-ui.min.css') }}">
                    <style>
  .ui-autocomplete {
    max-height: 170px;
  	z-index: 10000000;
    overflow-y: auto;
    overflow-x: hidden;
  }
  * html .ui-autocomplete {
    height: 170px;
  }
  </style>
        <script src="{{ url('js/jquery.3.3.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('js/jquery-ui.min.js') }}"></script>
		<script src="{{ url('js/popper.1.14.3.min.js') }}"></script>
		<script src="{{ url('js/bootstrap.4.1.3.min.js') }}"></script>
    	<script src="{{ url('js/dark-mode-switch.min.js') }}"></script>
		<script type="text/javascript">
        	$(function() {
            
            	@if(auth()->user()->hasPermission('write-intervention'))
                
            	$("#operator-event").autocomplete({
                	source: {!! $events !!},
  					minLength: 3,
                	select: function(event, ui) {
      					event.preventDefault();
                    	$("#operator-event").val(ui.item.label);
  					}
                	
				}).data("ui-autocomplete")._renderItem = function(ul, item) {
    				
                	var listItem = $("<li>")
        				.append("<div>" + item.label + "<i class='fas fa-trash-alt remove-intervention-suggestion text-danger float-right' title=\"{{ __('all.layout.delete_this_suggestion') }}\"></i></div>")
        				.appendTo(ul);

                	listItem.find(".remove-intervention-suggestion").click(function(e) {
        				e.stopPropagation();
						
                    	var payLoad = {};
                    	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                    	payLoad["action"] = "deleteInterventionSuggestion";
                    	payLoad["value"] = item.value
                        var posting = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
        				posting.done(function(data) {
                        	if (data == "OK") {
                        		listItem.remove();
            				}
              			});
        			});

    				return listItem;
				
                };
            
            	$("#operator-event").on("keyup", function() {
                	
                	var length = $(this).val().length;
                	
                	if (length >= 3) {
                    	$("#save-intervention-suggestion").removeClass("d-none");
                	} else {
                    	$("#save-intervention-suggestion").addClass("d-none");
                	}
                
                });
            
            	$("#save-intervention-suggestion").on("click", function() {
                
                	var value = $("#operator-event").val();
                
                	if (value == "" || value.length < 4) {
                    	return;
                    }
                	
                	var payLoad = {};
                    payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                    payLoad["action"] = "addInterventionSuggestion";
                    payLoad["value"] = value;
                    
                    var posting = $.post("{{ url('workstations/payload') }}", payLoad, "JSONP");
                	posting.done(function(data) {
                    	if (data == "OK") {
                        	$("#save-intervention-suggestion i").animate({opacity: 0}, 300, function() {
                            	$(this).removeClass("fa-save text-primary");
                            	$(this).addClass("fa-check text-success");
                            	$(this).animate({opacity: 1}, 300);
                            	setTimeout(() => {
                        			$(this).animate({opacity: 0}, 300, function() {
                            			$(this).removeClass("fa-check text-success");
                            			$(this).addClass("fa-save text-primary");
                            			$(this).animate({opacity: 1}, 300);
                        			});
                    			}, 3000);
                            });
                        } else {
                        	$("#save-intervention-suggestion i").animate({opacity: 0}, 300, function() {
                            	$(this).removeClass("fa-save text-primary");
                            	$(this).addClass("fa-times text-danger");
                            	$(this).animate({opacity: 1}, 300);
                            	setTimeout(() => {
                        			$(this).animate({opacity: 0}, 300, function() {
                            			$(this).removeClass("fa-times text-danger");
                            			$(this).addClass("fa-save text-primary");
                            			$(this).animate({opacity: 1}, 300);
                        			});
                    			}, 3000);
                            });
                        }
              		});
                
                
                	$("#myIcon").animate({opacity: 0}, 300, function() {
                    $(this).removeClass("fas fa-star");
                    $(this).addClass("fas fa-heart");
                    $(this).animate({opacity: 1}, 300);
                });
                
                	
                });
            
            	@endif
            	
            	refreshNotif();
            	var notifTimer = setInterval( function(){ refreshNotif(); }, 30000);
            	
            	function refreshNotif() {
                	payLoad = {};
                	payLoad['_token'] = $('meta[name="csrf-token"]').attr('content');
                	payLoad['action'] = "getNotifications";
                	var getNotifications = $.post("{{ url('dashboard/payload') }}", payLoad, "JSONP");
                	getNotifications.done(function(data, status) {
            			
                    	if(data["notifications"] == 0) {
                        	$("#notifications").addClass("d-none").text("");
                       	} else {
                        	$("#notifications").removeClass("d-none").text(data["notifications"]);
                       	}
                    	if(data["heartbeatlosses"] == 0) {
                        	$(".unreachable-counter").addClass("d-none").text("");
                       	} else {
                        	$(".unreachable-counter").removeClass("d-none").text(data["heartbeatlosses"]);
                       	}
                    }).fail(function(data, status) {
                    	//console.log(data);
                    });
                }
            
            	//dupla ctrl gombra jelenjen meg a kereső mező modal ablaka
            	var dblCtrlKey = 0;
            	var prevKeyCode = 0;
				$(document).on('keyup', function(event) {
                	//console.log(event.keyCode, prevKeyCode);
                	if (dblCtrlKey != 0 && event.keyCode == 17 && prevKeyCode == 17) {
    					$("#mobile").modal("show");
                    	$("#id").val("").focus();
                    } else {
    					dblCtrlKey = setTimeout(function() {dblCtrlKey = 0;}, 300);
                    	
  					}
                	prevKeyCode = event.keyCode;
				});
            
            	$("#id").on('keyup', function (e) {
    				if ($('#id').val().length >= 2) {
                		quickSearch($(this).val());
    				} else {
                		$("#search-result").html("");
                    }
				});
            
            	
            	
            	function quickSearch(phrase) {
                   	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'quickSearch', phrase: phrase}, "JSONP");
        			posting.done(function(data) {
            			if (data.workstations.length == 0) {
                        	$("#search-result").html("Nincs találat.")
                        } else {
                        	$("#search-result").html("<div class='list-group'>");
                        	for(var i = 0; i < data.workstations.length; i++) {
                            	switch(data.workstations[i].status) {
  									case "online":
    									var icon = '<i class="fas fa-circle text-success"></i>'
										break;
  									case "idle":
    									var icon = '<i class="fas fa-circle text-warning"></i>'
										break;
  									case "heartbeatLoss":
    									var icon = '<i class="fas fa-circle text-danger"></i>'
										break;
  									default:
    									var icon = '<i class="fas fa-circle text-muted"></i>'
								}
                            	
                            	$("#search-result").append('<a href="' + '{{ url("/workstations") }}/' +   data.workstations[i].id + '" class="list-group-item list-group-item-action" data-ip="' + data.workstations[i].ip + '" data-id="' + data.workstations[i].id + '"  target="_blank">' + icon + " " + data.workstations[i].alias + ' (' + data.workstations[i].ip + ')</a>');	
                            }
                        	$("#search-result").append("</div>");
                        }
        			});
                }
            
            	$("body").contextmenu(function (event) {
            		var clicked = $(event.target);
            		$(".contextmenu").html("");
                	if (clicked.hasClass("list-group-item")) {
                    	
                    	var wsid = clicked.attr("data-id");
                    	var ip = clicked.attr("data-ip");
                    	event.preventDefault();
                    	
                    	@if(auth()->user()->hasPermission('write-intervention'))
    						$(".contextmenu").append("<a href='javascript:' class='dropdown-item context-action' data-action='service' data-id='"+wsid+"'>{{ __('all.layout.intervention') }}</a>");
						@endif
                        
                        var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getWorkstationConnections', wsid: wsid}, "JSONP");
        				posting.done(function(data) {
                        	
                        	if (data.length > 0) {
                        		$(".contextmenu").append("<hr>");
                        		for(var i = 0; i < data.length; i++) {
                            			$(".contextmenu").append("<a href='"+data[i].url+"' class='dropdown-item'>"+data[i].action+": "+data[i].value+"</a>");
                                }                                    
                        	}
                        });
                    	// Show contextmenu
    					$(".contextmenu").addClass("show").css({
                			position: "absolute",
                    		zIndex: 2000,
        					top: event.pageY + "px",
        					left: event.pageX + "px"
    					});
                	}
                	
                	
                });
            
            	$(document).on('click', '.context-action', function() {
            
            		var contextAction = $(this).attr('data-action');
                	
            		switch(contextAction) {
                		case "service":
                    		//console.log(wsid);,
                    		var wsid = $(this).attr('data-id');
            	    		$("#mobile").modal("hide");
                    		$("#operator").modal("show");
                    		$("#operator-wsid").val(wsid);
                			break;
                    	case "copy-ip":
                    		var ip = $(this).attr('data-ip');
                    		console.log(ip);
    						copyToClipboard(ip);
                            break;
                		default:
                			break;
               	 	}
                	
                	$(".contextmenu").removeClass("show");
                
                });
            
            	$(document).on("click", function() {
                	$(".contextmenu").removeClass("show");
                });
            
            	$("#create-operator-event").on("click", function() {
                	var wsid = $("#operator-wsid").val();
                	var event = $("#operator-event").val();
                	
                	var selected = new Array();
            		$("#operators input[type=checkbox]:checked").each(function () {
		                selected.push(this.value);
				    });
                
                	if ($("#operator-keszenlet:checked").val() == 'keszenlet') {
                    	event = "({{ __('all.layout.standby_duty') }}) " + event;
                    }
					
                	var timeperiod = $("#operator-time").val();
                	if (timeperiod != "") {
                    	event = event + " (" + timeperiod + ")";
                    }
                
                	var operators = "";
                	
            		if (selected.length > 0) {
						var operators = selected.join(", ");
			        }
                
                	var posting = $.post("{{ url('workstations/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'createOperatorEvent', wsid: wsid, event: event, operators: operators}, "JSONP");
        			posting.done(function(data) {
                    	$("#operator-wsid").val("");
                    	$("#operator-event").val("");
                    	$("#operator").modal("hide");
                    });
                });
            
            	const idleDurationSecs = 3600;
    			const redirectUrl = "{{ url('logout') }}";

    			let idleTimeout;

    			const resetIdleTimeout = function() {
        			if (idleTimeout) clearTimeout(idleTimeout);
        			idleTimeout = setTimeout(() => location.href = redirectUrl, idleDurationSecs * 1000);
    			};

    			['click', 'touchstart', 'mousemove'].forEach(evt => document.addEventListener(evt, resetIdleTimeout, false));
            
            	resetIdleTimeout();
            
        });
        </script>
    	
    	@yield('inject-footer')
    <style type="text/css">
        table.dataTable tbody tr {
           	background: none;
        }
		
		#operator-event {
			padding-right: 40px;
		}
	</style>
    </body>
</html>
