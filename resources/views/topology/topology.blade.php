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

		function getNodeColor(n) {
			if (n.lost) return '#d9534f';
			if (n.id === 'nd1') return '#D2691e';
			if (n.black_toner !== undefined) return '#FFD700';
			if (n.online === undefined) return '#0275d8';
			return (n.online == 1) ? '#5cb85c' : '#aaa';
		}

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
                		edges[i].type = "line";
                		break;
                }

            }

            for (i = 0; i < len; i++) {

            	if(nodes[i].x == undefined) {
    				var spread = Math.max(1000, len * 5);
    				nodes[i].x = Math.floor(Math.random() * spread);
    				nodes[i].y = Math.floor(Math.random() * spread);
				}

            	if (nodes[i].size > 24) {
                nodes[i].size = 24;
                }

            	if (nodes[i].size == undefined) {
                nodes[i].size = 15;
                }

            	nodes[i].error = (nodes[i].online == 1) ? 0 : 1;
            	nodes[i].color = getNodeColor(nodes[i]);

            }

            s.refresh();

            var atlasSettings = {
            	gravity: 1,
                scalingRatio: 10,
                strongGravityMode: false,
                linLogMode: true,
                outboundAttractionDistribution: true,
                adjustSizes: true,
                barnesHutOptimize: true,
                barnesHutTheta: 0.6,
                slowDown: 10
            };

            s.startForceAtlas2(atlasSettings);

        @if(auth()->user()->hasPermission('write-topology'))
        var sourceNode = null, targetNode = null, edgeAction = null;
        var contextmenu;
        s.bind('clickNode rightClickNode', function(e) {
        	if (!e.data || !e.data.node) { return; }
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

            		var id = edges.length > 0 ? (edges[edges.length-1].id + 1) : 1;
            		var edgeType = null;
            		if (edgeAction === "addEdgeUTP") { var type = "line"; var label = " "; edgeType = "utp"; }
            		if (edgeAction === "addEdgeMono") { var type = "dotted"; var label = "mono"; edgeType = "mono"; }
            		if (edgeAction === "addEdgeMulti") { var type = "parallel";  var label = "multi"; edgeType = "multi"; }
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

            	sourceNode = null;
            	targetNode = null;
            	edgeAction = null;

            }
       });

        $("body").on("click", ".context-action", function() {
        	edgeAction = $(this).attr("data-action");
        });
        @endif


        $("#play").on("click", function() {
        	s.startForceAtlas2(atlasSettings);
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


		var timer = setInterval(function() {
        	$.getJSON('{{ url("topology/update") }}', function(data) {
            	var statusById = {};
            	data["nodes"].forEach(function(st) { statusById[st.id] = st; });

            	s.graph.nodes().forEach(function(node) {
                	var status = statusById[node.id];
                	if (!status) { return; }
                	node.color = getNodeColor(status);
                });

            	s.refresh();
            });
        }, 30000);

        var filterConfig = {
        	'ws-online':  function(n) { return n.type == 'ws' && n.lost == 0 && n.online == 1; },
        	'ws-offline': function(n) { return n.type == 'ws' && n.lost == 0 && n.online == 0; },
        	'ws-lost':    function(n) { return n.type == 'ws' && n.lost == 1 && n.online == 1; },
        	'pr':         function(n) { return n.type == 'pr'; }
        };

        $.each(filterConfig, function(id, matches) {
        	$('#' + id).on('click', function() {
            	var checked = $(this).prop('checked');
            	s.graph.nodes().forEach(function(node) {
                	if (matches(node)) { node.hidden = !checked; }
                });
            	s.refresh();
            });
        });

        });

        });

	</script>
@endsection
