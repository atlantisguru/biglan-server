@extends("layout")
@section("title")
{{ __('all.subnet.ip_table') }} | BigLan
@endsection
@section("content")
	@if(auth()->user()->hasPermission('write-subnetwork'))
	<div class="row mt-2">
		<div class="col-6">
			<a href={{ url('subnets/new') }} class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> {{ __('all.button.new_subnet') }}</a>
		</div>
	</div>
	@endif
	<div class="row mt-2">
				@if(count($subnets)==0)
					<div class="col-lg-6 col-sm-12">
   						<p>{{ __('all.subnet.no_subnet_found') }}</p>
					</div>
				@endif
				@foreach($subnets as $subnet)
				<div class="col-lg-6 col-sm-12">
					<div class="table-responsive">
					<table class="table table-striped table-hover">
					<thead class="thead-dark" data-toggle="collapse" href="#collapse{{ $subnet->id }}" aria-expanded="true" aria-controls="collapse{{ $subnet->id }}">
						<tr>
							<th>
            						{{ $subnet->identifier }}/{{ $subnet->mask }} ({{ $subnet->alias }}) {{ __('all.subnet.gateway') }}: {{ $subnet->gateway }}
  							</th>
                            <th class="align-items-end">
                            
                            </th>
						</tr>
						
					</thead>
					<tbody class="collapse hide" id="collapse{{ $subnet->id }}">
					<tr>
							<th>{{ __('all.subnet.ip_address') }}</th>
							<th>{{ __('all.subnet.device') }}</th>
					</tr>
                    @foreach($subnetIPs[$subnet->id] as $ip)
                    	<tr  >
                            <td>{{ $ip["ip"] }}</td>
                            <td @if(isset($ip["alias"])) 
                            		@if(strpos($ip["alias"], "???") !== false) class="bg-danger text-light" 
            						@else
            							@if(strpos($ip["alias"], "üres") !== false) class="bg-success text-light" @endif
            						@endif
                            		@endif	>
                            	@if(isset($ip["wsid"]))
                            		<i class="fas fa-desktop"></i> <a href={{ url("workstations/".$ip["wsid"]) }} target="_blank">
                            	@endif
                            	@if(isset($ip["prid"]))
                            		<i class="fas fa-print"></i> <a href={{ url("networkprinters") }} target="_blank">
                            	@endif
                            	<span data-ip={{ $ip["ip"] }}>{{ $ip["alias"] ?? "" }}</span> @if(!isset($ip["wsid"]) && !isset($ip["prid"])) <a href="javascript:void(0)" class="edit"><i class="fas fa-edit"></i></a> @endif
								@if(isset($ip["wsid"]) || isset($ip["prid"]))
                            		</a>
                            	@endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
					</table>
					</div>
					</div>
                 @endforeach
	</div>

@endsection
@section('inject-footer')
    <style>
    .table th {
    	padding: 0.3rem;                            
    }
	
	.table thead {
    	cursor: pointer
    }
    </style>                            	
 	<script type="text/javascript">
    $(function() {
    	
    	var originalContent = "";
    
    	//edit static text
    	$(".edit").on("click", function() {
        	var element = $(this).prev("span");
        	var table = "ip_tables";
        	var ip = element.attr("data-ip");
        	var field = "alias";
        	var value = element.text();
        	originalContent = value;
        	element.html("<input type='text' class='form-control' value='"+value+"' name='"+field+"' data-ip='"+ip+"'>");
        	$("input[name="+field+"]").focus();
        	$(this).hide();
        });
    
    	$("body").on("keyup", ".form-control", function(e) {
        	var element = $(this);
        	var table = "ip_tables";
        	var ip = element.attr("data-ip");
        	var field = "alias";
        	var value = element.val();
        	if (e.keyCode == 13) {
            	var posting = $.post("{{ url('subnets/payload') }}", { '_token': $('meta[name=csrf-token]').attr('content'), action: "changeIP", ip: ip, field: field, alias: value }, "JSONP");
        		posting.done(function(data) {
                	if (data == "OK") {
        				element.parent("span").next(".edit").show();
            			element.parent("span").text(value);
                    } else {
                    	element.parent("span").next(".edit").show();
            			element.parent("span").text(originalContent);
                    }
            	});
            }
        	if (e.keyCode == 27) {
            	element.parent("span").next(".edit").show();
            	element.parent("span").text(originalContent);
            	originalContent = "";
            }
        });
    
    	$("body").on("focusout", ".form-control", function() {
        	var element = $(this).prev("span");
        
        		element.parent("span").next(".edit").show();
            	element.parent("span").text(originalContent);
            	//originalContent = "";
        });
        
	});                                
    </script>                               
@endsection