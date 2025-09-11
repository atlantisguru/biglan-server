<?php $__env->startSection('title'); ?>
	<?php echo e(__('all.documents.documents')); ?> | BigLan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<?php
		$userPermissions = auth()->user()->permissions();

		$documentStorage = \App\Models\Documents::get()->sum("filesize");
		if (request("page") !== null) {
			$page = request("page");
		} else {
			$page = 1;
		}
		if (request("q") !== null) {
			$keywords = request("q");
		} else {
			$keywords = "";
		}
		$all = \App\Models\Documents::where("deleted", 0)->where(function($query) use ($keywords) {
					$query->where("keywords", "LIKE", "%".$keywords."%")->orWhere("title", "LIKE", "%".$keywords."%" ); 
				})->paginate(20);
	?>
	
	<div class="row mt-2">
		<div class="col-12">
			<form method="GET" class="form-inline">
    			<a href="documents" class="text-decoration-none mr-2 text-dark"><?php echo e(__('all.documents.documents')); ?>

				<small class="text-muted">
    			<?php echo e(($documentStorage <= 1024)
                		? $documentStorage."B"
                		: (($documentStorage/1024 <= 1024)
                    		? round($documentStorage/1024, 2)."kB"
                    		: round($documentStorage/1024/1024, 2)."MB")); ?>

				</small>
                </a>
				<?php if(in_array('write-document', $userPermissions)): ?>
					<a href="javascript:" class="btn btn-primary btn-sm mr-2"  data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#upload"><i class="fas fa-upload"></i> <?php echo e(__('all.documents.upload_document')); ?></a>
            	<?php endif; ?>
    			<a href="<?php echo e(url("documents/trash")); ?>" class="btn btn-sm btn-light mr-2"><i class="far fa-trash-alt"></i> <?php echo e(__('all.documents.archives')); ?></a>
				<div class="form-group">
        			<div class="input-group">
            			<input type="text" name="q" class="form-control form-control-sm"  value="<?php echo e(app('request')->input('q')); ?>">
            			<div class="input-group-append">
    						<button type="submit" class="btn btn-sm btn-primary" type="button"><?php echo e(__('all.documents.search')); ?></button>
  						</div>
            		</div>
    			</div>
			</form>
        </div>
	<?php
		$documents = \App\Models\Documents::where("deleted", 0)->where(function($query) use ($keywords) {
					$query->where("keywords", "LIKE", "%".$keywords."%")->orWhere("title", "LIKE", "%".$keywords."%" ); 
				})->orderBy("created_at", "DESC")->paginate(20);
	?>
	</div>
	<div class="row mt-2">
    <div class="col-12">
		<div class="table-responsive">
			<table class="table table-striped  table-sm table-hover">
    		<thead  class="thead-dark">
				<tr>
    				<th></th>
    				<th><?php echo e(__('all.documents.name')); ?></th>
    				<th><?php echo e(__('all.documents.keywords')); ?></th>
    				<th><?php echo e(__('all.documents.original_date')); ?></th>
    				<th><?php echo e(__('all.documents.size')); ?></th>
    				<th><?php echo e(__('all.documents.username')); ?></th>
    				<th><?php echo e(__('all.documents.created')); ?></th>
    				<th><?php echo e(__('all.documents.actions')); ?></th>
    			</tr>
    		</thead>
    		<tbody>
				<?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    			<tr>
    				<td>
    					<?php
    						$extension = last(explode(".", $document->filename));
    					?>
                        <?php switch($extension):
                        	case ("pdf"): ?>
                        		<i class="far fa-file-pdf text-danger"></i>
                        		<?php break; ?>
                        	<?php case ("png"): ?>
                        		<i class="far fa-file-image text-info"></i>
                        		<?php break; ?>
                        	<?php case ("jpg"): ?>
                        		<i class="far fa-file-image text-info"></i>
                        		<?php break; ?>
                        	<?php case ("doc"): ?>
                        		<i class="far fa-file-alt text-primary"></i>
                        		<?php break; ?>
                        	<?php case ("docx"): ?>
                        		<i class="far fa-file-alt text-primary"></i>
                        		<?php break; ?>
                        	<?php case ("xls"): ?>
                        		<i class="fas fa-table text-success"></i>
                        		<?php break; ?>
                        	<?php case ("xlsx"): ?>
                        		<i class="fas fa-table text-success"></i>
                        		<?php break; ?>
                        	<?php case ("html"): ?>
                        		<i class="far fa-file-code text-info"></i>
                        		<?php break; ?>
                        	<?php default: ?>
                        		<?php break; ?>
                       	<?php endswitch; ?>
    				</td>
    				<td>
    					<a target="_blank" href="documents/<?php echo e($document->filename); ?>"><?php echo e($document->title); ?></a>
    				</td>
                    <td>
                        <span class="text-muted" title="<?php echo e($document->keywords); ?>"><?php echo e(Str::limit($document->keywords, $limit = 30, $end = '...')); ?></span>
    				</td>
                    <td>
                        <?php if($document->signed_at != null): ?>
                        	<?php echo e(\Carbon\Carbon::parse($document->signed_at)->format("Y.m.d.")); ?>    
                        <?php else: ?>
                        	<?php echo e(\Carbon\Carbon::parse($document->created_at)->format("Y.m.d.")); ?>    
                        <?php endif; ?>
                    </td>    
                    <td>
    					<?php echo e(($document->filesize <= 1024)
        ? $document->filesize."B"
        : (($document->filesize/1024 <= 1024)
            ? round($document->filesize/1024, 2)."kB"
            : round($document->filesize/1024/1024, 2)."MB")); ?>

    				</td>
    				<td>
                    	<?php echo $document->uploader ? $document->uploader->username : ''; ?>

    				</td>
    				<td>
                    	<?php echo e(\Carbon\Carbon::parse($document->created_at)->format("Y.m.d. H:i")); ?>

    				</td>
                    <td>
                    	<?php if(in_array('delete-document', $userPermissions)): ?>
				    		<a href="javascript:" data-id="<?php echo e($document->id); ?>" class="btn btn-sm btn-outline-danger trash-document"><?php echo e(__('all.button.archive')); ?></a>
                    	<?php endif; ?>
                    </td>
    			</tr>
              
    			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
			</table>
            
		</div>
        
	</div>
	</div>
    <div class="row">
    	<div class="col">
        	<?php echo e($documents->appends(Request::query())->links()); ?>

        </div>
    </div>
   <?php if(in_array('write-document', $userPermissions)): ?>
	<!-- The Modal -->
	<div class="modal" id="new">
  		<div class="modal-dialog modal-lg">
    		<div class="modal-content">

      			<!-- Modal Header -->
      			<div class="modal-header">
        			<h4 class="modal-title"><?php echo e(__('all.documents.new_document')); ?></h4>
        			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			</div>

      			<!-- Modal body -->
      			<div class="modal-body">
                    	<div class="row">
        					<div class="col-4">
                    			Dokumentum sablon
                    		</div>
                    		<div class="col-8">
                    			<select name="type" id="template-selector" class="form-control">
                    				<option value="">Sablon kiválasztása</option>
                    			</select>
                    			
                    		</div>
                    	</div>
                    	<div id="template-dynamic">
                    	</div>
                    	<button type="button" id="create-document" class="btn btn-primary col-12 mt-2" style="display:none">Létrehozás</button>
                    	<div class="row"  id="printer-dynamic" style="display: none">
                    		<div class="col-12">
                    			<hr>
                        		Hova szeretnéd nyomtatni?
                    			<div class="input-group">
                    				<select name="printer" id="printer" class="form-control"></select>
                    				<div class="input-group-append">
                    					<a href="javascript:" id="print" class="btn btn-primary"><i class="fas fa-print"></i></a>
                    				</div>
                    			</div>
                        	</div>
                        	<div class="col-12 mt-4">
                            	<a id="open-document" href="javascript:" target="_blank" class="btn btn-lg btn-primary btn-block">Megnyitás</a>
                    		</div>
                    	</div>
                
                </div>

      			<!-- Modal footer -->
      			<div class="modal-footer">
                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Bezárás</button>
                </div>

    		</div>
  		</div>
	</div>
    <?php endif; ?>    
                    
    <?php if(in_array('write-document', $userPermissions)): ?>             
    <!-- The Modal -->
	<div class="modal" id="upload">
  		<div class="modal-dialog">
    		<div class="modal-content">

      			<!-- Modal Header -->
      			<div class="modal-header">
        			<h4 class="modal-title"><?php echo e(__('all.documents.upload_document')); ?></h4>
        			<button type="button" class="close" data-dismiss="modal">&times;</button>
      			</div>

      			<!-- Modal body -->
      			<div class="modal-body">
                    	<div class="row">
                    		<div class="col-12">
                    			<?php echo e(__('all.documents.select_file')); ?>:
                    			<input type="file" id="file" name="file" class="form-control">
                    		</div>
                    	</div>
                    	<div class="row">
        					<div class="col-12">
                    			<?php echo e(__('all.documents.document_name')); ?>:
                    			<input type="text" name="title" id="title" class="form-control">
                    		</div>
                    	</div>
                        <div class="row">
        					<div class="col-12">
                    			<?php echo e(__('all.documents.keywords')); ?>:
                    			<div class="input-group mb-3">
                                	<textarea rows="5" name="keywords" class="form-control" id="keywords"></textarea>
                                </div>
                    		</div>
                    	</div>
                        <div class="row">
        					<div class="col-12">
                    			<?php echo e(__('all.documents.original_date')); ?>

                    			<input type="date" name="date" class="form-control" value="<?php echo e(\Carbon\Carbon::now()->format("Y-m-d")); ?>">
                    		</div>
                    	</div>
                </div>

      			<!-- Modal footer -->
      			<div class="modal-footer">
                    <button type="button" id="upload-document" class="btn btn-primary"><?php echo e(__('all.button.upload')); ?></button>
        			<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('all.button.close')); ?></button>
      			</div>

    		</div>
  		</div>
	</div>
	<?php endif; ?>
                                
                                
<?php $__env->stopSection(); ?>

<?php $__env->startSection('inject-footer'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo e(url('css/jquery-ui.min.css')); ?>">
                    <style>
  .ui-autocomplete {
    max-height: 170px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
             z-index: 10000!important;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 170px;
  	
  }
  </style>
    <script type="text/javascript" src="<?php echo e(url('js/jquery-ui.min.js')); ?>"></script>
	                            
	<script type="text/javascript">
   		$(function() {
            	
        	<?php if(in_array('write-document', $userPermissions)): ?>
        	var getDocumentTemplates = $.post("<?php echo e(url('documents')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getDocumentTemplates'}, "JSONP");
        	getDocumentTemplates.done(function(data) {
            	var length = data.length;
            	for(i = 0; i< length; i++) {
                	$("#template-selector").append("<option value='"+data[i]["id"]+"'>"+data[i]["name"]+"</option>");
                }
            });
        
        	$("#template-selector").on("change", function() {
        		var templateId = $(this).val();
            	$("#template-dynamic").html("");
            	$("#printer-dynamic").hide();
            	$("#create-document").show();
            	if (templateId == "") {
                	$("#create-document").hide();
                	return null;
                }
            
            	var getTemplateFields = $.post("<?php echo e(url('documents')); ?>", { '_token': $('meta[name=csrf-token]').attr('content'), action: 'getTemplateFields', templateId: templateId}, "JSONP");
        		getTemplateFields.done(function(data) {
            		var length = data.fields.length;
            		for(i = 0; i< length; i++) {
                    	if (data.fields[i]["field"] == "input") {
                    		$("#template-dynamic").append("<div class='row mt-2'><div class='col-4'>"+data.fields[i]["label"]+"</div><div class='col-8'><input type='"+data.fields[i]["type"]+"' name='"+data.fields[i]["name"]+"' class='"+data.fields[i]["class"]+"'></div>");
                        }
                    	if (data.fields[i]["name"] == "division") {
                        	$("body").find("input[name='division']").autocomplete({
  								source: inventories,
  								minLength: 1,
  								select: function(event, ui) {
      								event.preventDefault();
      								$("input[name='division']").val(ui.item.label);
                                	$("input[name='divisioncode']").val(ui.item.value);
                                }
							});
                        }
                	}
                	$("#template-dynamic").append("<input type='hidden' id='copies' name='copies' value='"+data.copies+"'>");
                });
            
            });
        
        	$("#create-document").on("click", function() {
            	$("#create-document").html('<i class="fas fa-spinner fa-spin"></i> ' + $("#create-document").text()).addClass("disabled");
            	var templateId = $("#template-selector").val();
            	var dynamicFields = $("#template-dynamic input, #template-dynamic select, #template-dynamic textarea");
            	var dynamicLength = dynamicFields.length;
            	var payLoad = {};
            	dynamicFields.each(function() { 
                	payLoad[$(this).attr("name")] = $(this).val();
                });
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'createDocument';
            	payLoad['templateId'] = templateId;
            	var createDocument = $.post("<?php echo e(url('documents')); ?>", payLoad, "JSONP");
        		createDocument.done(function(data) {
            		document = data;
                	$("#create-document").html('<i class="fas fa-check"></i> ' + $("#create-document").text()).removeClass("disabled").removeClass("btn-primary").addClass("btn-success");
                	setTimeout(function(){
    					$("#create-document").html($("#create-document").text()).removeClass("btn-success").addClass("btn-primary");
                	}, 5000);
                	$("#printer-dynamic").show();
                	$("#open-document").attr("href", data);
                });
            
            });
        
        	$("#upload-document").on("click", function() {
            	$("#upload-document").html('<i class="fas fa-spinner fa-spin"></i> ' + $("#upload-document").text());
            	$("#upload-document").addClass("disabled");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'uploadDocument';
            	var file = $('#file').prop('files')[0];
            	const reader = new FileReader();
    			reader.onload = (e) => {
      				const data = e.target.result.split(',')[1];
                	payLoad["file"] = data; 
                	payLoad["filename"] = file.name;
            		payLoad['title'] = $('input[name="title"]').val()
                	payLoad['keywords'] = $('textarea[name="keywords"]').val()
                	payLoad['date'] = $('input[name="date"]').val()
                
            		var uploadDocument = $.post("<?php echo e(url('documents')); ?>", payLoad, "JSONP");
        			uploadDocument.done(function(data) {
                		$("#upload-document").html("<?php echo e(__('all.button.upload')); ?>");
                		$("#upload-document").removeClass("disabled");
            			$('#upload').modal('hide');
                		location.reload();
                	});
                };
            	reader.readAsDataURL(file);
            
            });
        
        	$(".trash-document").on("click", function() {
            	var documentId = $(this).attr("data-id");
            	var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'trashDocument';
            	payLoad['id'] = documentId;
            	var trashDocument = $.post("<?php echo e(url('documents')); ?>", payLoad, "JSONP");
        		trashDocument.done(function(data) {
                	location.reload();
                });
            });
        
        	var printers;
        	var document;
        
        	var payLoad = {};
            payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            payLoad['action'] = 'getAvailableRemotePrinters';
            var getAvailableRemotePrinters = $.post("<?php echo e(url('documents')); ?>", payLoad, "JSONP");
        	getAvailableRemotePrinters.done(function(data) {
               	printers = data;
            	for(var i = 0; i < data.length; i++) {
                	$("#printer").append("<option value='"+i+"'>"+data[i]["alias"]+"</option>");
                }
            });
        
        	$("#print").on("click", function() {
            	 $("#print").html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');
            	var copies = $("#copies").val();
            	var selectedPrinterId = $("#printer").val();
            	var printerName = printers[selectedPrinterId]["name"];
            	var url = document;
            	var wsid = printers[selectedPrinterId]["wsid"];
            	var fileName = url.split("/")[url.split("/").length - 1];
            	var copiesArray = new Array();
            	for(i = 0; i < copies; i++) {
                	copiesArray.push('C:\\temp\\PDFtoPrinter.exe C:\\temp\\'+fileName+' "'+printerName+'"');
                }
            	var command = '$path="C:\\temp";If(!(test-path $path)){New-Item -ItemType Directory -Force -Path $path;}$url="http://192.168.1.227/PDFtoPrinter.exe";$output = "C:\\temp\\PDFtoPrinter.exe";Invoke-WebRequest -Uri $url -OutFile $output;$url="http://192.168.1.227/docs/'+fileName+'";$output = "C:\\temp\\'+fileName+'";Invoke-WebRequest -Uri $url -OutFile $output;'+copiesArray.join(";");
            	//console.log(selectedPrinterId, printerName, url, wsid,fileName, command);
                var payLoad = {};
            	payLoad['_token'] = $('meta[name=csrf-token]').attr('content');
            	payLoad['action'] = 'command';
            	payLoad['id'] = wsid;
            	payLoad['command'] = command;
            	var printDocument = $.post("<?php echo e(url('documents')); ?>", payLoad, "JSONP");
        		printDocument.done(function(data) {
                	$("#print").html('<i class="fas fa-check"></i>').removeClass('disabled btn-primary').addClass("btn-success");
                		setTimeout(function(){
    						$("#print").html('<i class="fas fa-print"></i>').removeClass("btn-success").addClass("btn-primary");
                		}, 5000);
                }).fail(function() {
    				$("#print").html('<i class="fas fa-times"></i>').removeClass('disabled btn-primary').addClass("btn-danger");
                	setTimeout(function(){
    					$("#print").html('<i class="fas fa-print"></i>').removeClass("btn-danger").addClass("btn-primary");
                	}, 5000);
                }).always(function() {
    				$("#print").html('<i class="fas fa-question"></i>').removeClass('disabled btn-primary').addClass("btn-warning");
                	setTimeout(function(){
    					$("#print").html('<i class="fas fa-print"></i>').removeClass("btn-warning").addClass("btn-primary");
                	}, 5000);
                });
            });
        	<?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/documents/main.blade.php ENDPATH**/ ?>