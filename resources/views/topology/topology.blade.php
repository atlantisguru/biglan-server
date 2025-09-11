@extends("layout")
@section("title")
{{ __('all.topology.topology') }} | BigLan
@endsection
@section("content")
	<div class="row mt-2">
		<div class="col-11">
			<i id="play" class="fas fa-play" style="color: #000"></i>
			<i id="pause" class="fas fa-pause" style="color: #000"></i>
			<i class="fas fa-circle" style="color: #D2691E"></i>{{ __('all.topology.internet_connection') }}
			<i class="fas fa-circle text-primary"></i>{{ __('all.topology.network_device') }}
			<div class="form-check form-check-inline">
  				<input class="form-check-input" type="checkbox" id="ws-online" CHECKED>
  				<label class="form-check-label" for="ws-online"><i class="fas fa-circle text-success"></i> {{ __('all.topology.online') }}</label>
			</div>
            <div class="form-check form-check-inline">
  				<input class="form-check-input" type="checkbox" id="ws-offline" CHECKED>
  				<label class="form-check-label" for="ws-offline"><i class="fas fa-circle text-muted"></i> {{ __('all.topology.offline') }}</label>
			</div>
            <div class="form-check form-check-inline">
  				<input class="form-check-input" type="checkbox" id="ws-lost" CHECKED>
  				<label class="form-check-label" for="ws-lost"><i class="fas fa-circle text-danger"></i> {{ __('all.topology.unreachable') }}</label>
			</div>
            <div class="form-check form-check-inline">
  				<input class="form-check-input" type="checkbox" id="pr" CHECKED>
  				<label class="form-check-label" for="pr"><i class="fas fa-circle" style="color:#FFd700"></i> {{ __('all.topology.network_printer') }}</label>
			</div>
           	<a id="export" href="javascript:" class="btn btn-primary btn-sm mr-1">{{ __('all.topology.export_svg') }}</a><a id="export-gexf" href="javascript:" class="btn btn-primary btn-sm">{{ __('all.topology.export_gexf') }}</a>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-11" style="height: 800px" id="container"></div>
	</div>

@endsection

@section("inject-footer")
	
	<script type="text/javascript" src="{{ url('js/sigma.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma/plugins/sigma.renderers.customEdgeShapes.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma/plugins/sigma.renderers.edgeDots.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma/plugins/sigma.renderers.edgeLabels.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma/plugins/sigma.renderers.parallelEdges.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma/plugins/sigma.plugins.dragNodes.min.js') }}"></script>
	
    <script type="text/javascript" src="{{ url('js/sigma.parsers.json.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma.layout.forceAtlas2.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma.plugins.animate.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma.exporters.gexf.js') }}"></script>
	<script type="text/javascript" src="{{ url('js/sigma.exporters.svg.min.js') }}"></script>
	
	
	<script type="text/javascript">
   
		$(function () {
        	sigma.parsers.json("{{ url('topology/update') }}", {
    			settings: {
                	
                	doubleClickEnabled: false,
      				defaultNodeColor: '#ec5148',
      				defaultEdgeColor: '#aaa',
                	defaultLabelColor: '#555',
                	edgeColor: '#aaa',
                	skipErrors: 'true',
                	
                },
            	renderer: {
                	type: 'canvas',
                	container: 'container',
                
    			}            	
            },
        function(s) {
            
            var i,
                nodes = s.graph.nodes(),
                len = nodes.length,
        		edges = s.graph.edges(),
        		elen = edges.length;
        	
        	for (i = 0; i < elen; i++) {
            
            	switch(edges[i].type) {
                	case "mono":
                		edges[i].label = "mono";
                		edges[i].color = "#555";
                		edges[i].type = "line";
                		break;
                	case "multi":
                		edges[i].label = "multi";
                		edges[i].color = "#555";
                		edges[i].type = "parallel";
                		break;
                	case "black":
                		edges[i].label = "Sötét vonal";
                		edges[i].color = "#555";
                		edges[i].type = "dotted";
                		break;
                	default:
                		//edges[i].label = "UTP";
                		edges[i].type = "line";
                		break;
                }
            
            }
       
            for (i = 0; i < len; i++) {
            
            	
            	if(nodes[i].x == undefined) {
                	nodes[i].x = Math.floor(Math.random() * 10);
                	nodes[i].y = Math.floor(Math.random() * 10);
                }
            
            	if (nodes[i].size > 24) {
                nodes[i].size = 24;
                }
            
            	if (nodes[i].size == undefined) {
                nodes[i].size = 15;
                }
            	if(nodes[i].online == 1) {
                nodes[i].color = "#5cb85c";
                nodes[i].error = 0;
                
                } else {
                nodes[i].error = 1;
                nodes[i].color = '#aaa';
                }
            	if (nodes[i].online == undefined) {
                	nodes[i].color = "#0275d8";
                }
                //nyomtató
            	if (nodes[i].black_toner != undefined) {
                	nodes[i].color = "#FFD700";
                }
            	
            	if (nodes[i].lost) {
                	nodes[i].color = "#d9534f";
                }
            	if (nodes[i].id == "nd1") {
                	nodes[i].color = "#D2691e";
                }
            
            }
        
            s.refresh();
        	
            s.startForceAtlas2({
            	gravity: 1
            	
            });
        @if(auth()->user()->hasPermission('write-topology'))
        var sourceNode, targetNode, edgeAction;
        var contextmenu;
        s.bind('clickNode rightClickNode', function(e) {
        	if (e.type === "rightClickNode") {
            	sourceNode = e.data.node.id;
            	
            	$("body").contextmenu(function (event) {
            		var clicked = $(event.target);
                	$(".contextmenu").html("");
                		event.preventDefault();
                    	$(".contextmenu").append("<b class='dropdown-item'>" + e.data.node.label + "</b>");
            			$(".contextmenu").append("<hr>");
            			$(".contextmenu").append("<a href='javascript:' class='dropdown-item context-action' data-action='addEdgeUTP' data-id='"+e.data.node.id+"'>{{ __('all.topology.utp_connection') }}</a>");
            			$(".contextmenu").append("<a href='javascript:' class='dropdown-item context-action' data-action='addEdgeMono' data-id='"+e.data.node.id+"'>{{ __('all.topology.monomode_connection') }}</a>");
            			$(".contextmenu").append("<a href='javascript:' class='dropdown-item context-action' data-action='addEdgeMulti' data-id='"+e.data.node.id+"'>{{ __('all.topology.multimode_connection') }}</a>");
            			$(".contextmenu").append("<hr>");
            			$(".contextmenu").append("<a href='javascript:' class='dropdown-item context-action' data-action='deleteEdge' data-id='"+e.data.node.id+"'>{{ __('all.topology.delete_connection') }}</a>");
            			
                		// Show contextmenu
    					$(".contextmenu").addClass("show").css({
                			position: "absolute",
                    		zIndex: 2000,
        					top: event.pageY + "px",
        					left: event.pageX + "px"
    					});
                });
               
            } else {
            	
            }
        	
        	if (e.type === "clickNode" && sourceNode !== null && edgeAction !== null) {
            	targetNode = e.data.node.id;
            		if (edgeAction === "deleteEdge") {
                    var edgeID;
                    	for (i = 0; i < edges.length; i++) {
                        	if ((edges[i].source === sourceNode && edges[i].target === targetNode) || (edges[i].source === targetNode && edges[i].target === sourceNode)) {
                            	edgeID = edges[i].id;
                            	var payLoad = {};
                           		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            					payLoad['action'] = 'removeEdge';
                				payLoad['id'] = edgeID;
            					var removeEdge = $.post("{{ url('topology/payload') }}", payLoad, "JSONP");
        						removeEdge.done(function(data) {
            						if(data == "OK") {
                						s.graph.dropEdge(edgeID);
                                		edges.splice(i,1);
                            			s.refresh();
                					}
            					});
                            }
                            
                        
                        }
                    } else {
            
            		var id = edges[edges.length-1].id + 1;
            		if (edgeAction === "addEdgeUTP") { var type = "line"; var label = " "; }
            		if (edgeAction === "addEdgeMono") { var type = "dotted"; var label = "mono"; var edgeType = "mono"; }
            		if (edgeAction === "addEdgeMulti") { var type = "parallel";  var label = "multi"; var edgeType = "multi"; }
            		var source = sourceNode;
            		var target = targetNode;
                    
                    var payLoad = {};
                    payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['action'] = 'addEdge';
                	payLoad['target'] = target;
            		payLoad['source'] = source;
                	payLoad['type'] = edgeType;
            		var updateNetworkdevice = $.post("{{ url('topology/payload') }}", payLoad, "JSONP");
        			updateNetworkdevice.done(function(data) {
            			if(data == "OK") {
                			edges.push({"color": "#555", "id" : id, "label" : label, "source" : source, "target" : target, "type": type});
            				s.graph.addEdge({"color": "#555", "id" : id, "label" : label, "source" : source, "target" : target, "type": type});
                		}
            		});
                    
            		
                    
                    }
                    
            	sourceNode, targetNode, edgeAction = null;
            		
            	
            }
       });
        
        $("body").on("click", ".context-action", function() {
        	edgeAction = $(this).attr("data-action");
        });
        @endif
        
        
        $("#play").on("click", function() {
        	s.startForceAtlas2({ 
            	gravity: 20,
                scalingRatio: 30
            });
        });
        
        $("#pause").on("click", function() {
        	s.stopForceAtlas2();
        });
        
        
		$("#export-gexf").on("click", function() {
        	s.toGEXF({
  				download: true,
  				filename: 'szkh-biglan-topology.gexf',
  				nodeAttributes: 'data',
  				renderer: s.renderers[0],
  				creator: 'Sigma.js',
  				description: 'BigLan Network Monitoring System Topology'
			});
        });
  
        $("#export").on("click", function() {
        	s.toSVG({download: true, filename: 'biglan-topology.svg', size: 4800});
        });
        	
        
			var timer = setInterval( function(){ 
            
            	//új állapotok lekérdezése
            	$.getJSON('{{ url("topology/update") }}', function(data) {
                	var nodeStatuses = data["nodes"];
                	var errors = 0;
                	s.graph.nodes().forEach(function(node) {
                    	var status = nodeStatuses.filter(function(el) {
                        	return el.id === node.id;
                        });
                    	
                    	node.color = (status[0].online) ? '#5cb85c' : '#aaa';
               
            	if (status[0].online == undefined) {
                	node.color = "#0275d8";
                }
                //nyomtató
            	if (status[0].black_toner != undefined) {
                	node.color = "#FFD700";
                }
            	if (status[0].type == "server") {
                	node.color = "#9400D3";
                }
            	if (status[0].type == "camera") {
                	node.color = "#FF69b4";
                }
            	if (status[0].lost) {
                	node.color = "#d9534f";
                }
            	if (status[0].id == "nd1") {
                	node.color = "#D2691e";
                }
                    });
               
                });
            	s.refresh();
            
            }, 15000);
        
        $("#ws-online").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#ws-online").prop("checked")) {
            	    	if(node.type == "ws" && node.lost == 0 && node.online == 1) {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "ws" && node.lost == 0 && node.online == 1) {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        $("#ws-offline").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#ws-offline").prop("checked")) {
            	    	if(node.type == "ws" && node.lost == 0 && node.online == 0) {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "ws" && node.lost == 0 && node.online == 0) {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        $("#ws-lost").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#ws-lost").prop("checked")) {
            	    	if(node.type == "ws" && node.lost == 1 && node.online == 1) {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "ws" && node.lost == 1 && node.online == 1) {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        $("#pr").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#pr").prop("checked")) {
            	    	if(node.type == "pr") {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "pr") {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        $("#camera").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#camera").prop("checked")) {
            	    	if(node.type == "camera") {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "camera") {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        $("#server").on("click", function() {
       			s.graph.nodes().forEach(function(node) {
                	if($("#server").prop("checked")) {
            	    	if(node.type == "server") {
                	    	node.hidden = false;
                    	}
                    } else {
                    	if(node.type == "server") {
                	    	node.hidden = true;
                    	}
                    }
                });
        s.refresh();
       });
        
        });
        
        });
        
	</script>
@endsection