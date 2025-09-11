<?php $__env->startSection("title"); ?>
<?php echo e(__('all.button.new_notification')); ?> | BigLan
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
	
	<div class="row mt-2">
		<div class="col-lg-12 col-sm-12">
			<h4><?php echo e(__('all.button.new_notification')); ?></h4>
			<form method="POST" class="form-horizontal" action="<?php echo e(url('notifications/save')); ?>">
        	<button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> <?php echo e(__('all.button.save')); ?></button>&nbsp;<a href=<?php echo e(url('notifications')); ?> class="btn btn-light btn-sm"><i class="fas fa-arrow-circle-up"></i> <?php echo e(__('all.button.back_without_save')); ?></a>
			<?php echo e(csrf_field()); ?>

			<div class="form-group row mt-2">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.alias')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" id="alias" name="alias" class="form-control"  value="<?php echo e(old('alias')); ?>">
				</div>
            	<?php if($errors->has('alias')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('alias')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row mt-2">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.name')); ?></span><br>
            		<small><?php echo e(__('all.notification_center.name_helper')); ?></small>
				</div>
				<div class="col-4">
					<input type="text" id="name" name="name" class="form-control" value="<?php echo e(old('name')); ?>" <?php echo e(old('name_restriction') ? '' : 'readonly'); ?>>
					<div class="form-check">
            			<input class="form-check-input" type="checkbox" id="name_restriction" name="name_restriction" <?php echo e(old('name_restriction') ? 'checked' : ''); ?>>
            			<label class="form-check-label" for="name_restriction">
                			<?php echo e(__('all.notification_center.name_restriction')); ?>

            			</label>
        			</div>
				</div>
                <?php if($errors->has('name')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('name')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row mt-2">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.description')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="description" class="form-control" value="<?php echo e(old('description')); ?>">
				</div>
			</div>
            <div class="form-group row mt-2">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.type')); ?></span>
				</div>
				<div class="col-4">
					<select name="type" class="form-control">
            			<option value="ping" <?php if(old('type') === 'ping'): ?> selected <?php endif; ?>><?php echo e(__('all.notification_center.ping')); ?></option>
            			<option value="socket-polling" <?php if(old('type') === 'socket-polling'): ?> selected <?php endif; ?>><?php echo e(__('all.notification_center.socket_polling')); ?></option>
            			<option value="sensor-value" <?php if(old('type') === 'sensor-value'): ?> selected <?php endif; ?>><?php echo e(__('all.notification_center.sensor_value')); ?></option>
            			<option value="biglan-command" <?php if(old('type') === 'biglan-command'): ?> selected <?php endif; ?>><?php echo e(__('all.notification_center.biglan_command')); ?></option>
            			<option value="snmp" <?php if(old('type') === 'snmp'): ?> selected <?php endif; ?>>SNMP</option>
                        <option value="http-status-code" <?php if(old('type') === 'http-status-code'): ?> selected <?php endif; ?>><?php echo e(__('all.notification_center.http_status_code')); ?></option>
                        
            		</select>
				</div>
			</div>
            <div class="form-group row website-row d-none">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.website')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="website" class="form-control" value="<?php echo e(old('website')); ?>">
				</div>
                <?php if($errors->has('website')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('website')); ?></div>
    			<?php endif; ?>
			</div>
            
            <div class="form-group row ip-row">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.ip_address')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="ip" class="form-control" value="<?php echo e(old('ip')); ?>">
				</div>
                <?php if($errors->has('ip')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('ip')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row port-row d-none">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.port')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="port" class="form-control" value="<?php echo e(old('port')); ?>">
				</div>
                <?php if($errors->has('port')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('port')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row wsid-row d-none">
				<div class="col-4">
					<span class="control-label">WSID</span>
				</div>
				<div class="col-4">
					<input type="text" name="wsid" class="form-control" value="<?php echo e(old('wsid')); ?>">
				</div>
                <?php if($errors->has('wsid')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('wsid')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row command-row d-none">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.biglan_command')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="command" class="form-control" value="<?php echo e(old('command')); ?>">
				</div>
                <?php if($errors->has('command')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('command')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row oid-row d-none">
				<div class="col-4">
					<span class="control-label">OID</span>
				</div>
				<div class="col-4">
					<input type="text" name="oid" class="form-control" value="<?php echo e(old('oid')); ?>">
				</div>
                <?php if($errors->has('oid')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('oid')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row expression-row d-none">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.expression')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="expression" class="form-control">
				</div>
                <?php if($errors->has('expression')): ?>
                	<div class="text-danger"><i class="fas fa-exclamation-circle"></i> <?php echo e($errors->first('expression')); ?></div>
    			<?php endif; ?>
			</div>
            <div class="form-group row unit-row d-none">
				<div class="col-4">
					<span class="control-label"><?php echo e(__('all.notification_center.unit')); ?></span>
				</div>
				<div class="col-4">
					<input type="text" name="unit" class="form-control" value="<?php echo e(old('unit')); ?>">
				</div>
			</div>
        </form>
		</div>
	</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('inject-footer'); ?>
    <style>
    	.table td {
            padding: 0.2rem;
        }

    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	formHandle();
    
    	$('#alias').on('input', function() {
        	if ($('#name_restriction').is(':checked') === false) {
            	let alias = $(this).val();
            	let name = generateSlug(alias);
            	$('#name').val(name);
            }
        });
    
    	$('#name').on('input', function() {
            var name = $(this).val();
       		name = generateSlug(name);
        	$('#name').val(name);
        });
    
    	function generateSlug(str) {
            str = str.toLowerCase();
			str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
			str = str.replace(/[^a-z0-9\s-]/g, '');
			str = str.replace(/\s+/g, '-');
			str = str.replace(/-+/g, '-');

        	if (str.length > 100) {
            	str = str.substring(0, 100);
        	}
        
            return str;
        }
    
    	 $('#name_restriction').change(function() {
            if ($(this).is(':checked')) {
                $('#name').removeAttr('readonly');
            } else {
                $('#name').attr('readonly', 'readonly').val(generateSlug($('#alias').val()));
            }
        });
    
    	$("body").on("change", "select[name=type]", function() {
        
        	formHandle();
        
        });
        
    	function formHandle() {
    
        	var value = $("select[name=type]").val();
        	 	
        	if(value === "http-status-code") {
            	$(".website-row").removeClass("d-none");
            	$(".expression-row").removeClass("d-none");
            	$(".ip-row").addClass("d-none");
            	$(".port-row").addClass("d-none");
            	$(".wsid-row").addClass("d-none");
            	$(".command-row").addClass("d-none");
            	$(".unit-row").addClass("d-none");
            	$(".oid-row").addClass("d-none");
            }
        
        	if(value === "ping") {
            	$(".ip-row").removeClass("d-none");
            	$(".website-row").addClass("d-none");
            	$(".port-row").addClass("d-none");
            	$(".wsid-row").addClass("d-none");
            	$(".command-row").addClass("d-none");
            	$(".expression-row").addClass("d-none");
            	$(".unit-row").addClass("d-none");
            	$(".oid-row").addClass("d-none");
            }
        	
        	if(value === "socket-polling") {
            	$(".website-row").addClass("d-none");
            	$(".ip-row").removeClass("d-none");
            	$(".port-row").removeClass("d-none");
            	$(".wsid-row").addClass("d-none");
            	$(".command-row").addClass("d-none");
            	$(".expression-row").addClass("d-none");
            	$(".unit-row").addClass("d-none");
            	$(".oid-row").addClass("d-none");
            }
        	
        	if(value === "sensor-value") {
            	$(".website-row").addClass("d-none");
            	$(".ip-row").addClass("d-none");
            	$(".port-row").addClass("d-none");
            	$(".wsid-row").addClass("d-none");
            	$(".command-row").addClass("d-none");
            	$(".expression-row").removeClass("d-none");
            	$(".unit-row").removeClass("d-none");
            	$(".oid-row").addClass("d-none");
            }
        	
        	if(value === "snmp") {
            	$(".website-row").addClass("d-none");
            	$(".ip-row").removeClass("d-none");
            	$(".port-row").addClass("d-none");
            	$(".wsid-row").addClass("d-none");
            	$(".command-row").addClass("d-none");
            	$(".expression-row").removeClass("d-none");
            	$(".unit-row").addClass("d-none");
            	$(".oid-row").removeClass("d-none");
            }
        	
        
        	if(value === "biglan-command") {
            	$(".website-row").addClass("d-none");
            	$(".ip-row").addClass("d-none");
            	$(".port-row").addClass("d-none");
            	$(".wsid-row").removeClass("d-none");
            	$(".command-row").removeClass("d-none");
            	$(".expression-row").removeClass("d-none");
            	$(".unit-row").addClass("d-none");
            	$(".oid-row").addClass("d-none");
            }
        
        }
    
    	$('[data-toggle="popover"]').popover();
    	
	});
</script>                               
<?php $__env->stopSection(); ?>
<?php echo $__env->make("layout", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/biglan/resources/views/notifications/new.blade.php ENDPATH**/ ?>