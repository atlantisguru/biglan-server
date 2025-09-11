<?php $__env->startSection("title"); ?>
<?php echo e(__('all.network_printers.network_printers')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>

<?php
	$userPermissions = auth()->user()->permissions();
?>

	<div class="row mt-2">
		<div class="col-6">
			<?php echo e(csrf_field()); ?>

			<?php if(in_array('write-network-printer', $userPermissions)): ?>
   				<a href=<?php echo e(url('networkprinters/new')); ?> class="btn btn-sm btn-primary mr-2"><i class="fas fa-plus"></i> <?php echo e(__('all.button.new_network_printer')); ?></a>
            <?php endif; ?>
            <a href="javascript:void(0)" id="btn-query" class="btn btn-sm btn-primary mr-2"><i class="fas fa-search"></i> <?php echo e(__('all.button.update_data')); ?></a>
            <?php if(in_array('write-network-printer', $userPermissions)): ?>
   				<span class="badge badge-light mr-2"><i class="fas fa-info-circle"></i> <?php echo e(__('all.network_printers.helper')); ?></span>
			<?php endif; ?>
        </div>
	</div>
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<div class="table-responsive">
				<?php if(count($networkPrinters) > 0): ?>
   				<table class="table table-striped table-hover" id="networkprinters">
						<thead class="thead-dark">
							<tr>
            					<th></th>
            					<th><?php echo e(__('all.network_printers.name')); ?></th>
            					<th><?php echo e(__('all.network_printers.brand_model')); ?></th>
								<th><?php echo e(__('all.network_printers.note')); ?></th>
								<th><?php echo e(__('all.network_printers.serial')); ?></th>
								<th></th>
								<th><?php echo e(__('all.network_printers.ip_address')); ?></th>
								<th><?php echo e(__('all.network_printers.mac_address')); ?></th>
								<th><?php echo e(__('all.network_printers.toner_ink_level')); ?></th>
            					<th><?php echo e(__('all.network_printers.counter')); ?></th>
            				</tr>
						</thead>
						<tbody>
					
   				<?php $__currentLoopData = $networkPrinters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $np): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr data-id=<?php echo e($np->id); ?>>
								<td><a href="javascript:void(0)" class="details" data-panel="details" data-toggle="modal" data-target="#printerDetails"><i class="fas fa-chart-bar"></i></a></td>
            					<td class="editable" data-field="alias"><?php echo e($np->alias); ?></td>
								<td class="editable" data-field="brand"><?php echo e($np->brand); ?></td>
								<td class="editable" data-field="notes"><small><?php echo e($np->notes); ?></small></td>
            					<td class="editable" data-field="serial"><?php echo e($np->serial); ?></td>
								<td class="text-center"><a href="http://<?php echo e($np->ip); ?>" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>
								<td class="editable" data-field="ip" ><?php echo e($np->ip); ?></td>
								<td class="editable" data-field="mac"><?php echo e($np->mac); ?></td>
            					<td>
									<?php if(isset($np->cyan_toner_level) && $np->cyan_toner_max > 0): ?>
            						<div data-color="cyan" class="progress border border-secondary">
  										<div class="progress-bar" role="progressbar" style="width: <?php echo e(round(($np->cyan_toner_level/$np->cyan_toner_max)*100)); ?>%;background-color:#008B8B;" aria-valuenow="<?php echo e(round(($np->cyan_toner_level/$np->cyan_toner_max)*100)); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo e(round(($np->cyan_toner_level/$np->cyan_toner_max)*100)); ?>%</div>
									</div>
             						<?php endif; ?>
            						<?php if(isset($np->magenta_toner_level) && $np->magenta_toner_max > 0): ?>
            						<div data-color="magenta" class="progress border border-secondary">
  										<div class="progress-bar" role="progressbar" style="width: <?php echo e(round(($np->magenta_toner_level/$np->magenta_toner_max)*100)); ?>%;background-color:#d6006e;" aria-valuenow="<?php echo e(round(($np->magenta_toner_level/$np->magenta_toner_max)*100)); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo e(round(($np->magenta_toner_level/$np->magenta_toner_max)*100)); ?>%</div>
									</div>
             						<?php endif; ?>
            						<?php if(isset($np->yellow_toner_level) && $np->yellow_toner_max > 0): ?>
            						<div data-color="yellow" class="progress border border-secondary">
  										<div class="progress-bar" role="progressbar" style="width: <?php echo e(round(($np->yellow_toner_level/$np->yellow_toner_max)*100)); ?>%;background-color:#eecf14;" aria-valuenow="<?php echo e(round(($np->yellow_toner_level/$np->yellow_toner_max)*100)); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo e(round(($np->yellow_toner_level/$np->yellow_toner_max)*100)); ?>%</div>
									</div>
             						<?php endif; ?>
            						<?php if(isset($np->black_toner_level) && $np->black_toner_max > 0): ?>
            						<div data-color="black" class="progress border border-secondary">
  										<div class="progress-bar" role="progressbar" style="width: <?php echo e(round(($np->black_toner_level/$np->black_toner_max)*100)); ?>%;background-color:#000;" aria-valuenow="<?php echo e(round(($np->black_toner_level/$np->black_toner_max)*100)); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo e(round(($np->black_toner_level/$np->black_toner_max)*100)); ?>%</div>
									</div>
             						<?php endif; ?>
            						<?php if($np->black_toner_max < 0): ?>
                                    	<?php echo e(__('all.network_printers.incompatible_toner_ink')); ?>

            						<?php endif; ?>
            						
                                </td>
            					<td data-field="print_counter" class="text-right"><?php echo e($np->print_counter); ?></td>
            				</tr>
			    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
            		
            	<?php else: ?>
                	<p><?php echo e(__('all.network_printers.network_printer_not_found')); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

            <div class="modal fade" id="printerDetails" tabindex="-1" role="dialog">
  						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    						<div class="modal-content">
      							<div class="modal-header">
        							<h5 class="modal-title" id="printer-name">...</h5>
        							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          								<span aria-hidden="true">&times;</span>
        							</button>
      							</div>
      							<div class="modal-body">
        							<ul class="nav nav-tabs" id="myTab" role="tablist">
  										<li class="nav-item">
    										<a class="nav-link active" id="home-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true"><?php echo e(__('all.network_printers.data_sheet')); ?></a>
  										</li>
  										<li class="nav-item">
    										<a class="nav-link" id="profile-tab" data-toggle="tab" href="#events" role="tab" aria-controls="events" aria-selected="false"><?php echo e(__('all.network_printers.events')); ?></a>
  										</li>
  										<li class="nav-item">
    										<a class="nav-link" id="contact-tab" data-toggle="tab" href="#statistics" role="tab" aria-controls="statistics" aria-selected="false"><?php echo e(__('all.network_printers.statistics')); ?></a>
  										</li>
									</ul>
									<div class="tab-content" id="myTabContent">
  										<div class="tab-pane pt-4 fade show active" id="details" role="tabpanel" aria-labelledby="home-tab">
                                        	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.black_toner_ink_level')); ?></strong></span>
                                        			<p id="black-toner-level">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.cyan_toner_ink_level')); ?></strong></span>
                                        			<p id="cyan-toner-level">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.magenta_toner_ink_level')); ?></strong></span>
                                        			<p id="magenta-toner-level">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.yellow_toner_ink_level')); ?></strong></span>
                                        			<p id="yellow-toner-level">-</p>
                                        		</div>
                                        	</div>
                                        	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.inventory_id')); ?></strong></span>
                                        			<p id="inventory-id">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.serial')); ?></strong></span>
                                        			<p id="serial-number">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.ip_address')); ?></strong></span>
                                        			<p id="ip-address">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.mac_address')); ?></strong></span>
                                        			<p id="mac-address">-</p>
                                        		</div>
                                        	</div>
                                        	<div class="row">
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.counter')); ?></strong></span>
                                        			<p id="print-counter">-</p>
                                        		</div>
                                        		<div class="col-3">
                                        			<span><strong><?php echo e(__('all.network_printers.action')); ?></strong></span>
                                        			<?php if(in_array('delete-network-printer', $userPermissions)): ?>
                                        				<p><a href="javascript:void(0)" data-id="" id="btn-archive" class="btn btn-danger btn-sm mt-2"><i class="fas fa-trash"></i> <?php echo e(__('all.button.archive')); ?> (x2)</a></p>
                                        			<?php endif; ?>
                                        		</div>
                                        	</div>
                                			<div class="row">
                                        		<div class="col-12">
                                        			<span><strong><?php echo e(__('all.network_printers.note')); ?></strong></span>
                                        			<p id="notes">-</p>
                                        		</div>
                                        	</div>
                                		</div>
  										<div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="profile-tab">
                                        	<table id="events-table" class="table table-striped">
                                        		<tbody>
                                        			
                                        		</tbody>
                                        	</table>
                                        </div>
  										<div class="tab-pane fade pt-2" id="statistics" role="tabpanel" aria-labelledby="contact-tab">
                                        	<div class="row">
                                        		<div class="col-12">
                                        			<p><strong><?php echo e(__('all.network_printers.black_toner_ink_level')); ?></strong> (<small id="toner-loss"></small>)</p>
                                        			<div id="black-toner-graph">
                                        			
                                        			</div>
                                        		</div>
                                        	</div>
                                        	<div class="row mt-4">
                                        		<div class="col-4">
                                        			<span><strong><?php echo e(__('all.network_printers.paper_jam_counter')); ?></strong></span>
                                        			<p id="paperjam">-</p>
                                        		</div>
                                        		<div class="col-4">
                                        			<span><strong><?php echo e(__('all.network_printers.service_call_counter')); ?></strong></span>
                                        			<p id="maintenance">-</p>
                                        		</div>
                                        		<div class="col-4">
                                        			<span><strong><?php echo e(__('all.network_printers.printed_pages_counter')); ?></strong></span>
                                        			<p id="printed">-</p>
                                        		</div>
                                        	</div>
                                        </div>
									</div>
      							</div>
      							<div class="modal-footer">
        							<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      							</div>
    						</div>
  						</div>
					</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
        <link rel="stylesheet" type="text/css" href=<?php echo e(url("css/jquery.dataTables.min.css")); ?>>
        <script type="text/javascript" src=<?php echo e(url("js/jquery.dataTables.min.js")); ?>></script>
        <script type="text/javascript">
        	$(function() {
    				$('#networkprinters').DataTable({
                    	"pageLength": 100
                    });
			});
        </script>
    <style>
        .modal-backdrop {
    z-index: 1040 !important;
}
.modal-content {
   
    z-index: 1100 !important;
}
    	.table td {
            padding: 0.2rem!important;
        }

		.progress-bar {
        	text-shadow : 1px 1px 1px #777;
        }

		@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }

		.tab-pane {
        	height:350px;
        	overflow-y: scroll;
        	overflow-x: hidden;
        }

		#black-toner-graph {
        	height: 150px;
        	border: 1px solid #DDD;
        }
	
		.graph-bar {
        	background-color: #EEE;
        	width: 3%;
        	margin-left: 0.33%;
        	height: 100%;
        	float: left;
        	font-size: 10px;
        	text-align: center;
        }

		.graph-value {
        	background-color: #000;
        	width: 100%;
        	float: left;
        	
        }

    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	var editValue = "";
        var payLoad = {};
    
    	
    	$("body").on("click", "#btn-query", function(e) {
        	var element = $(this);
        
        	element.addClass("disabled");
        	element.children("i").addClass("fa-spinner").addClass("fa-spin").removeClass("fa-search");
        	var posting = $.post("<?php echo e(url('networkprinters/payload')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: "queryNetworkPrinters" }, "JSONP");
        	posting.done(function(data) {
        		$(data).each(function( ) {
                	
            	   	var row = $("table tr[data-id="+ this.id + "]");
            		row.find("td[data-field=brand]").text(this.brand);
                	row.find("td[data-field=serial]").text(this.serial);
                	row.find("td[data-field=mac]").text(this.mac);
                	row.find("[data-color=black] .progress-bar").css("width", Math.round(this.black_toner_level/this.black_toner_max*100) + "%").attr("aria-valuenow", Math.round(this.black_toner_level/this.black_toner_max*100)).text(Math.round(this.black_toner_level/this.black_toner_max*100) + "%");
                	row.find("td[data-field=print_counter]").text(this.print_counter);
                	row.effect( "highlight", {color:"#ffc107"}, 2000 );
                });
            	
            	$("#btn-query").removeClass("disabled");
            	$("#btn-query i").removeClass("fa-spinner").removeClass("fa-spin").addClass("fa-search");
            	
            });
        });
    	
    			
    	<?php if(in_array('write-network-printer', $userPermissions)): ?>
   			
    			$(document).on('mouseover', 'table#networkprinters .editable', function() {
                	$(this).css("cursor","cell");
                });
            
            	$(document).on('mouseleave', 'table#networkprinters .editable', function() {
                	$(this).css("cursor","default");
                });
            	
            	function saveData(npid, npfield, npvalue) { 
            		payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            		payLoad['id'] = npid;
            		payLoad['action'] = 'updateNetworkPrinter';
                	payLoad['field'] = npfield;
            		payLoad['value'] = npvalue;
                	var updateNetworkPrinter = $.post("<?php echo e(url('networkprinters/payload')); ?>", payLoad, "JSONP");
        			updateNetworkPrinter.done(function(data) {
            			if(data == "OK") {
                			var saveValue = $('.editing').val();
                        	$('.editing').parents("td").text(saveValue);
                		} else {
            				$('.editing').parents("td").text(editValue);
                		}
            		});
                }
            
            	$(document).on("dblclick", 'table#networkprinters .editable', function() {
                	$(this).removeClass("editable");
                	editValue = $(this).text();
                   	$(this).html("<input class='form-control editing' type='text' />");
                	$('.editing').val(editValue).focus();
                });
            	
            	$(document).on('blur', '.editing', function() {
                   	$('.editing').parents("td").text(editValue).addClass("editable");
                });
            
            	$(document).on('keydown', '.editing', function(e) {
                	//var saveValue = $('.editing').val();
                	if(e.which === 13 && e.shiftKey) {
                    	$(this).parents("td").addClass("editable");
                    	var id = $('.editing').parents("tr").attr("data-id");
                    	var field = $('.editing').parents("td").attr("data-field");
                    	var value = $('.editing').val();
                    	//console.log(id, field, value);
                    	saveData(id, field, value);
                    	//$('.editing').parents("td").text(saveValue);
    				}
                	
                	if(e.which === 27) {
                	   	$('.editing').parents("td").text(editValue).addClass("editable");
                	}
                });
    
    	<?php endif; ?>
    
        <?php if(in_array('delete-network-printer', $userPermissions)): ?>
   		
    	$(document).on("dblclick", '#btn-archive', function() {
        	console.log("clicked");
        	payLoad = {};
        	var id = $(this).attr("data-id");
        	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            payLoad['action'] = 'deleteNetworkPrinter';
            payLoad['id'] = id;
            var deletePrinter = $.post("<?php echo e(url('networkprinters/payload')); ?>", payLoad, "JSONP");
        	deletePrinter.done(function() {
            	location.reload();
            } );
       	});

    	<?php endif; ?>
    
    	$(document).on("click", '.details', function() {
        
        	$("#events-table tbody, #black-toner-graph").html("");
        	$("#black-toner-level, #cyan-toner-level, #magenta-toner-level, #yellow-toner-level, #inventory-id, #serial-number, #ip-address, #mac-address, #print-counter, #notes, #paperjem, #maintenance, #printed, #toner-loss").text("-");
        	var blackTonerLevel, cyanTonerLevel, magentaTonerLevel, yellowTonerLevel;
        	payLoad = {};
        	var id = $(this).parents("tr").attr("data-id");
        	$("#btn-archive").attr("data-id", id);
        	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
        	payLoad['action'] = 'viewNetworkPrinter';
            payLoad['id'] = id;
            var showPrinter = $.post("<?php echo e(url('networkprinters/payload')); ?>", payLoad, "JSONP");
        	showPrinter.done(function(data) {
            	$("#printer-name").text(data.printer["alias"] + " (" + data.printer["brand"] + ")");
            	
            	if(data.printer["black_toner_level"] >= 0 && data.printer["black_toner_max"] > 0) {
                	blackTonerLevel = Math.round(data.printer["black_toner_level"]/data.printer["black_toner_max"] * 100);
                }
            	$("#black-toner-level").text(blackTonerLevel + "%")
                if (data.printer["cyan_toner_level"] !== null) {
                	if(data.printer["cyan_toner_level"] >= 0 && data.printer["cyan_toner_max"] > 0) {
                		cyanTonerLevel = Math.round(data.printer["cyan_toner_level"]/data.printer["cyan_toner_max"] * 100);
                	}
            		$("#cyan-toner-level").text(cyanTonerLevel + "%")
               	}
            	if (data.printer["magenta_toner_level"] !== null) {
                	if(data.printer["magenta_toner_level"] >= 0 && data.printer["magenta_toner_max"] > 0) {
                		magentaTonerLevel = Math.round(data.printer["magenta_toner_level"]/data.printer["magenta_toner_max"] * 100);
                	}
            		$("#magenta-toner-level").text(magentaTonerLevel + "%")
               	}
                if (data.printer["yellow_toner_level"] !== null) {
                	if(data.printer["yellow_toner_level"] >= 0 && data.printer["yellow_toner_max"] > 0) {
                		yellowTonerLevel = Math.round(data.printer["yellow_toner_level"]/data.printer["yellow_toner_max"] * 100);
                	}
            		$("#yellow-toner-level").text(yellowTonerLevel + "%")
               	}
            	$("#inventory-id").text(data.printer["inventory_id"]);
            	$("#serial-number").text(data.printer["serial"]);
            	$("#ip-address").html("<a href='http://" + data.printer["ip"] + "' target='_blank'>" + data.printer["ip"] + "</a>");
            	$("#mac-address").text(data.printer["mac"]);
            	$("#print-counter").text(data.printer["print_counter"]);
            	$("#notes").text(data.printer["notes"]);
            
            	$.each(data.events, function(i, item) {
    				$("#events-table tbody").append("<tr><td>"+ item.created_at +"</td><td>"+ item.event +"</td></tr>")
  				});
            
            	$("#toner-loss").text(data.tonerRemaining);
            	$("#paperjam").text(data.paperjam);
            	$("#maintenance").text(data.maintenance);
            	$("#printed").text(data.printed);
            
            	for(var i = 1; i <= 30; i++) {
            		$("#black-toner-graph").append("<div class='graph-bar' title='"+ data.tonerarray[i]["date"] +" (" +data.tonerarray[i]["black_toner"]+"%)'><div class='graph-value' style='margin-top: "+ (1.5*(100-data.tonerarray[i]["black_toner"])) +"px;height: "+ data.tonerarray[i]["black_toner"] +"%'></div></div>");    
                }
            
            });
        });
    
    	$('[data-toggle="popover"]').popover();
    	
    
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/networkprinters/list.blade.php ENDPATH**/ ?>